/**
 * Class group chats (pages/communication/messaging.php). Messages come from api/chat.php; new ones are
 * checked every few seconds, and the chat list (last message, unread counts) a little less often.
 * Uses SITE_ROOT and showToast from js/main.js.
 */
const CHAT_POLL_MS = 3000;
const CHAT_LIST_POLL_MS = 10000;
const CHAT_GROUP_SECONDS = 5 * 60; // messages from the same person this close together share one name/avatar

document.addEventListener('DOMContentLoaded', () => {
  const app = document.getElementById('chat-app');
  if (!app) return;
  const data = JSON.parse(document.getElementById('chat-data').textContent);
  const box = document.getElementById('chat-messages');
  const list = document.getElementById('chat-list');
  const form = document.getElementById('chat-composer');
  const input = form.elements.body;
  const sendButton = form.querySelector('.chat-send');

  // lastId: newest message shown; last*: what the previous message was, for grouping and day separators
  const chat = { courseId: data.course, lastId: 0, lastSender: null, lastStamp: 0, lastDay: null, closed: false, sending: false, queue: Promise.resolve() };

  renderChatList(list, data.chats, chat.courseId);
  const active = list.querySelector('.chat-list-item.active');
  if (active) active.scrollIntoView({ block: 'nearest' });

  // One request at a time, in order (checking for new messages and sending share this queue)
  const enqueue = (task) => (chat.queue = chat.queue.then(task).catch(() => {}));

  const loadNew = (scrollDown = false) => enqueue(async () => {
    if (chat.closed) return;
    try {
      const { messages } = await chatApi('messages', { course_id: chat.courseId, after: chat.lastId });
      const nearBottom = box.scrollHeight - box.scrollTop - box.clientHeight < 80;
      if (chat.lastId === 0) box.replaceChildren(...(messages.length ? [] : [emptyChat()]));
      if (messages.length) {
        box.querySelector('.chat-empty')?.remove();
        messages.forEach(message => box.appendChild(renderMessage(chat, message)));
        chat.lastId = messages[messages.length - 1].id;
        if (scrollDown || nearBottom) box.scrollTop = box.scrollHeight;
      }
    } catch (err) {
      if (err.status === 403 || err.status === 401) closeChat(chat, box, form, err.message);
    }
  });

  loadNew(true);
  setInterval(() => { if (!document.hidden) loadNew(); }, CHAT_POLL_MS);
  setInterval(async () => {
    if (document.hidden) return;
    try {
      const { chats } = await chatApi('list', {});
      const scroll = list.scrollTop;
      renderChatList(list, chats, chat.courseId);
      list.scrollTop = scroll;
    } catch (err) { /* try again next time */ }
  }, CHAT_LIST_POLL_MS);

  // Composer: Enter sends, Shift+Enter adds a new line; the box grows with the text
  const resize = () => {
    input.style.height = 'auto';
    input.style.height = `${Math.min(input.scrollHeight, 140)}px`;
    sendButton.disabled = chat.closed || chat.sending || input.value.trim() === '';
  };
  input.addEventListener('input', resize);
  input.addEventListener('keydown', (e) => {
    if (e.key === 'Enter' && !e.shiftKey && !e.isComposing) {
      e.preventDefault();
      form.requestSubmit();
    }
  });
  form.addEventListener('submit', (e) => {
    e.preventDefault();
    const body = input.value.trim();
    if (!body || chat.closed || chat.sending) return;
    chat.sending = true;
    sendButton.disabled = true;
    enqueue(async () => {
      try {
        await chatApi('send', { course_id: chat.courseId, body });
        input.value = '';
      } catch (err) {
        showToast(err.message, 'danger');
        if (err.status === 403 || err.status === 401) closeChat(chat, box, form, err.message);
      } finally {
        chat.sending = false;
        resize();
      }
    });
    loadNew(true);
    input.focus();
  });
  if (window.matchMedia('(min-width: 901px)').matches) input.focus();
});

async function chatApi(action, payload) {
  const response = await fetch(new URL(`api/chat.php?action=${action}`, SITE_ROOT), {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify(payload)
  });
  const data = await response.json().catch(() => ({}));
  if (!response.ok) {
    throw Object.assign(new Error(data.error || `Server error (${response.status}).`), { status: response.status });
  }
  return data;
}

// Small helper: element with a class and (optionally) text
function el(tag, className, text) {
  const node = document.createElement(tag);
  if (className) node.className = className;
  if (text !== undefined) node.textContent = text;
  return node;
}

function renderChatList(list, chats, activeId) {
  list.replaceChildren(...chats.map(item => {
    const link = el('a', `chat-list-item${item.id === activeId ? ' active' : ''}`);
    link.href = `?course=${item.id}`;
    if (item.id === activeId) link.setAttribute('aria-current', 'page');

    const icon = el('div', 'chat-icon', item.code.replace(/[^A-Za-z]/g, '').slice(0, 3) || '#');
    const text = el('div', 'chat-list-text');
    const top = el('div', 'chat-list-top');
    top.append(el('strong', '', item.code), el('span', 'chat-list-time', item.time));
    text.append(top, el('div', 'chat-list-title', item.title), el('div', 'chat-list-preview', item.preview));
    link.append(icon, text);

    // the open chat is being read right now
    if (item.unread > 0 && item.id !== activeId) {
      link.classList.add('has-unread');
      link.appendChild(el('span', 'chat-unread', item.unread > 99 ? '99+' : String(item.unread)));
      link.setAttribute('aria-label', `${item.code}, ${item.unread} unread`);
    }
    return link;
  }));
}

function emptyChat() {
  const empty = el('div', 'chat-empty');
  empty.append(el('i', 'fa-regular fa-comments'), el('div', 'empty-title', 'No messages yet'),
    el('div', '', 'Say hello to your class!'));
  return empty;
}

function avatar(message) {
  const node = el('div', 'chat-avatar');
  if (message.picture) {
    const img = el('img');
    img.src = message.picture;
    img.alt = '';
    img.referrerPolicy = 'no-referrer';
    node.appendChild(img);
  } else {
    node.textContent = message.initials;
  }
  return node;
}

// One message (or an "added to the group" notice), plus a day separator when the day changes
function renderMessage(chat, message) {
  const fragment = document.createDocumentFragment();
  if (message.day !== chat.lastDay) {
    const day = el('div', 'chat-day');
    day.appendChild(el('span', '', message.day));
    fragment.appendChild(day);
    chat.lastDay = message.day;
    chat.lastSender = null;
  }

  if (message.kind !== 'message') {
    const notice = el('div', 'chat-notice');
    notice.append(el('i', `fa-solid ${message.kind === 'added' ? 'fa-user-plus' : 'fa-user-minus'}`),
      ` ${message.name} ${message.kind === 'added' ? 'was added to' : 'was removed from'} the group • ${message.time}`);
    fragment.appendChild(notice);
    chat.lastSender = null;
    return fragment;
  }

  const grouped = message.sender !== null && message.sender === chat.lastSender && message.stamp - chat.lastStamp < CHAT_GROUP_SECONDS;
  chat.lastSender = message.sender;
  chat.lastStamp = message.stamp;

  const row = el('div', `chat-msg ${message.mine ? 'mine' : 'theirs'}${grouped ? ' grouped' : ''}`);
  if (!message.mine) row.appendChild(avatar(message));
  const content = el('div', 'chat-msg-content');
  if (!grouped) {
    const meta = el('div', 'chat-meta');
    if (!message.mine) {
      meta.appendChild(el('span', 'chat-name', message.name));
      if (message.role) meta.appendChild(el('span', `chat-role${message.role === 'Admin' ? ' admin' : ''}`, message.role));
    }
    meta.appendChild(el('span', 'chat-time', message.time));
    content.appendChild(meta);
  }
  const bubble = el('div', 'chat-bubble', message.body);
  bubble.title = `${message.day} ${message.time}`;
  content.appendChild(bubble);
  row.appendChild(content);
  fragment.appendChild(row);
  return fragment;
}

// Removed from the course (or signed out) while the chat was open
function closeChat(chat, box, form, message) {
  if (chat.closed) return;
  chat.closed = true;
  form.querySelector('textarea').disabled = true;
  form.querySelector('.chat-send').disabled = true;
  const notice = el('div', 'chat-closed');
  notice.append(el('i', 'fa-solid fa-lock'), ` ${message}`);
  box.appendChild(notice);
  box.scrollTop = box.scrollHeight;
}
