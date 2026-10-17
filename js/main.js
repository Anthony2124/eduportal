/**
 * EduPortal - Main JavaScript
 * Handles the signed-in student, interactive components, navigation, modals, filters, tabs, chat, and toasts.
 */

// Site root, worked out from this script's own URL so it is right for index.php and for pages/*/*.html
const SITE_ROOT = new URL('..', (document.currentScript || document.querySelector('script[src*="js/main.js"]')).src);

document.addEventListener('DOMContentLoaded', () => {
  loadSignedInStudent();
  initLogoutConfirm();
  initSidebar();
  initDropdowns();
  initModals();
  initTabs();
  initFilters();
  initMessaging();
  initCalendarViews();
  initInteractiveActions();
});

// Signed-in Student: fills every [data-user] / [data-user-value] spot with the account from the
// PHP session, and sends visitors who aren't signed in to the sign-in page
async function loadSignedInStudent() {
  let response;
  try {
    response = await fetch(new URL('api/auth.php?action=me', SITE_ROOT), {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: '{}'
    });
  } catch {
    return; // server not reachable (e.g. page opened without XAMPP)
  }

  if (response.status === 401) {
    window.location.replace(new URL('login.html', SITE_ROOT));
    return;
  }
  if (!response.ok) return;

  const { user } = await response.json();

  // Pages this role can't open (ROLE_PAGES in api/bootstrap.php) send it back to its dashboard
  const currentPage = sitePath(location.href);
  if (currentPage.startsWith('pages/') && !user.pages.includes(currentPage)) {
    window.location.replace(new URL(user.dashboard, SITE_ROOT));
    return;
  }
  hideLockedLinks(user.pages);

  const yearNames = ['1st Year', '2nd Year', '3rd Year', '4th Year', '5th Year'];
  const fullName = `${user.firstName} ${user.lastName}`;
  const initials = (user.firstName[0] + user.lastName[0]).toUpperCase();
  const values = {
    fullName,
    firstName: user.firstName,
    initials,
    studentId: user.studentId,              // Student ID, or Employee ID for faculty/admin
    email: user.email,
    program: user.program,                  // program, or department/office for faculty/admin
    yearLevel: user.role === 'student' ? (yearNames[user.yearLevel - 1] || '') : user.roleName,
    programYear: user.subtitle              // e.g. "BS Computer Science IV" or "Faculty · College of Engineering"
  };

  document.querySelectorAll('[data-user]').forEach(el => {
    const key = el.dataset.user;
    if (key === 'avatar') {
      el.referrerPolicy = 'no-referrer'; // needed for Google profile photos
      el.src = user.pictureUrl || initialsAvatar(initials);
      el.alt = fullName;
    } else if (key in values) {
      el.textContent = values[key];
    }
  });

  document.querySelectorAll('[data-user-value]').forEach(input => {
    input.value = values[input.dataset.userValue] || '';
  });
}

// A URL as a path from the site root, e.g. "pages/academics/grades.php"
function sitePath(url) {
  return new URL(url, location.href).pathname.slice(SITE_ROOT.pathname.length);
}

// Hides sidebar and menu links to pages this role can't open, then any sidebar section left empty
function hideLockedLinks(allowedPages) {
  const isHidden = link => (link.closest('li') || link).hidden;

  document.querySelectorAll('.sidebar a[href], .dropdown-menu a[href]').forEach(link => {
    const path = sitePath(link.getAttribute('href'));
    if (path.startsWith('pages/') && !allowedPages.includes(path)) {
      (link.closest('li') || link).hidden = true;
    }
  });

  // Section headings: some pages wrap a section in .nav-section, others put the links right after the heading
  document.querySelectorAll('.sidebar .nav-section-title').forEach(title => {
    const links = [];
    for (let el = title.nextElementSibling; el && !el.matches('.nav-section-title'); el = el.nextElementSibling) {
      links.push(...(el.matches('a[href]') ? [el] : el.querySelectorAll('a[href]')));
    }
    if (links.length && links.every(isHidden)) {
      title.hidden = true;
    }
  });

  document.querySelectorAll('.sidebar .nav-section').forEach(section => {
    const links = [...section.querySelectorAll('a[href]')];
    if (links.length && links.every(isHidden)) {
      section.hidden = true;
    }
  });
}

// Round avatar image with the student's initials, for accounts without a Google photo
function initialsAvatar(initials) {
  const svg = '<svg xmlns="http://www.w3.org/2000/svg" width="96" height="96">'
    + '<rect width="96" height="96" fill="#4F46E5"/>'
    + '<text x="50%" y="50%" dy=".35em" text-anchor="middle" font-family="Inter, Arial, sans-serif" '
    + `font-size="38" font-weight="700" fill="#FFFFFF">${escapeHtml(initials)}</text></svg>`;
  return `data:image/svg+xml,${encodeURIComponent(svg)}`;
}

// Logout Confirmation: every "Logout" / "Sign Out" link points to logout.php, so ask first
function initLogoutConfirm() {
  // Capture phase, so the question appears even inside menus that handle their own clicks
  document.addEventListener('click', (e) => {
    const link = e.target.closest('a[href]');
    if (!link || sitePath(link.href) !== 'logout.php') return;
    e.preventDefault();
    e.stopPropagation();
    confirmLogout(link.href);
  }, true);
}

function confirmLogout(logoutUrl) {
  let dialog = document.getElementById('logout-dialog');
  if (!dialog) {
    const style = document.createElement('style');
    style.textContent = `
      .logout-dialog { margin: auto; border: none; border-radius: 16px; padding: 28px 26px 22px; width: min(380px, calc(100vw - 32px));
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.15), 0 10px 10px -5px rgba(0, 0, 0, 0.06);
        text-align: center; font-family: 'Inter', -apple-system, 'Segoe UI', sans-serif; color: #1E293B; }
      .logout-dialog::backdrop { background: rgba(15, 23, 42, 0.55); backdrop-filter: blur(3px); }
      .logout-dialog-icon { width: 56px; height: 56px; margin: 0 auto 14px; border-radius: 50%; display: flex;
        align-items: center; justify-content: center; font-size: 1.4rem; background: rgba(239, 68, 68, 0.12); color: #DC2626; }
      .logout-dialog h3 { font-size: 1.15rem; font-weight: 700; margin: 0 0 6px; }
      .logout-dialog p { font-size: 0.9rem; color: #64748B; margin: 0 0 22px; line-height: 1.5; }
      .logout-dialog-actions { display: flex; gap: 10px; }
      .logout-dialog-actions button { flex: 1; padding: 11px 16px; border-radius: 10px; font-size: 0.92rem;
        font-weight: 600; cursor: pointer; font-family: inherit; transition: background-color 0.15s ease; }
      .logout-dialog-cancel { background: #FFFFFF; border: 1px solid #CBD5E1; color: #334155; }
      .logout-dialog-cancel:hover { background: #F1F5F9; }
      .logout-dialog-confirm { background: #EF4444; border: 1px solid #EF4444; color: #FFFFFF; }
      .logout-dialog-confirm:hover { background: #DC2626; }
      .logout-dialog-actions button:focus-visible { outline: 3px solid rgba(79, 70, 229, 0.35); outline-offset: 2px; }
    `;
    document.head.appendChild(style);

    dialog = document.createElement('dialog');
    dialog.id = 'logout-dialog';
    dialog.className = 'logout-dialog';
    dialog.setAttribute('aria-labelledby', 'logout-dialog-title');
    dialog.innerHTML = `
      <div class="logout-dialog-icon"><i class="fa-solid fa-right-from-bracket"></i></div>
      <h3 id="logout-dialog-title">Log out of EduPortal?</h3>
      <p>You'll need to sign in again to access your account.</p>
      <div class="logout-dialog-actions">
        <button type="button" class="logout-dialog-cancel">Cancel</button>
        <button type="button" class="logout-dialog-confirm">Log Out</button>
      </div>
    `;
    document.body.appendChild(dialog);

    dialog.querySelector('.logout-dialog-cancel').addEventListener('click', () => dialog.close());
    dialog.querySelector('.logout-dialog-confirm').addEventListener('click', () => {
      window.location.href = dialog.dataset.logoutUrl;
    });
    // Clicking the dark backdrop (outside the box) also cancels
    dialog.addEventListener('click', (e) => {
      const box = dialog.getBoundingClientRect();
      const outside = e.clientX < box.left || e.clientX > box.right || e.clientY < box.top || e.clientY > box.bottom;
      if (e.target === dialog && outside) dialog.close();
    });
  }

  dialog.dataset.logoutUrl = logoutUrl;
  dialog.showModal();
  dialog.querySelector('.logout-dialog-cancel').focus();
}

// Sidebar Toggle for Mobile
function initSidebar() {
  const sidebar = document.getElementById('sidebar');
  // Support both IDs used across pages
  const sidebarToggle = document.getElementById('sidebar-toggle') || document.getElementById('sidebarToggle');
  const sidebarClose = document.getElementById('sidebar-close');
  const sidebarOverlay = document.getElementById('sidebar-overlay') || document.getElementById('sidebarOverlay');

  if (sidebarToggle && sidebar) {
    sidebarToggle.addEventListener('click', () => {
      sidebar.classList.toggle('mobile-open');
      if (sidebarOverlay) sidebarOverlay.classList.toggle('active');
    });
  }

  const closeSidebar = () => {
    if (sidebar) sidebar.classList.remove('mobile-open');
    if (sidebarOverlay) sidebarOverlay.classList.remove('active');
  };

  if (sidebarClose) sidebarClose.addEventListener('click', closeSidebar);
  if (sidebarOverlay) sidebarOverlay.addEventListener('click', closeSidebar);
}

// Topbar Dropdowns (User Menu, Notifications)
function initDropdowns() {
  // Generic dropdown-container pattern (used in sub-pages)
  const dropdownContainers = document.querySelectorAll('.dropdown-container');

  dropdownContainers.forEach(container => {
    const trigger = container.querySelector('.dropdown-trigger');
    if (!trigger) return;

    trigger.addEventListener('click', (e) => {
      e.stopPropagation();
      // Close other open dropdowns
      dropdownContainers.forEach(other => {
        if (other !== container) other.classList.remove('open');
      });
      container.classList.toggle('open');
    });
  });

  if (dropdownContainers.length > 0) {
    document.addEventListener('click', () => {
      dropdownContainers.forEach(container => container.classList.remove('open'));
    });
  }
}

// Modal Dialog Management
function initModals() {
  // Open buttons
  document.querySelectorAll('[data-modal-open]').forEach(btn => {
    btn.addEventListener('click', (e) => {
      e.preventDefault();
      const modalId = btn.getAttribute('data-modal-open');
      const modal = document.getElementById(modalId);
      if (modal) {
        modal.classList.add('active');
        document.body.style.overflow = 'hidden';
      }
    });
  });

  // Close buttons
  document.querySelectorAll('[data-modal-close]').forEach(btn => {
    btn.addEventListener('click', (e) => {
      e.preventDefault();
      const modal = btn.closest('.modal-overlay');
      if (modal) {
        modal.classList.remove('active');
        document.body.style.overflow = '';
      }
    });
  });

  // Click outside modal dialog to dismiss
  document.querySelectorAll('.modal-overlay').forEach(overlay => {
    overlay.addEventListener('click', (e) => {
      if (e.target === overlay) {
        overlay.classList.remove('active');
        document.body.style.overflow = '';
      }
    });
  });
}

// Tab Switching
function initTabs() {
  document.querySelectorAll('[data-tabs]').forEach(tabContainer => {
    const tabButtons = tabContainer.querySelectorAll('[data-tab-target]');
    const tabPanes = document.querySelectorAll(`[data-tab-content="${tabContainer.getAttribute('data-tabs')}"]`);

    tabButtons.forEach(btn => {
      btn.addEventListener('click', () => {
        const targetId = btn.getAttribute('data-tab-target');

        tabButtons.forEach(b => b.classList.remove('active'));
        btn.classList.add('active');

        tabPanes.forEach(pane => {
          if (pane.id === targetId) {
            pane.style.display = 'block';
          } else {
            pane.style.display = 'none';
          }
        });
      });
    });
  });
}

// Category / Pill Filtering
function initFilters() {
  document.querySelectorAll('[data-filter-group]').forEach(group => {
    const buttons = group.querySelectorAll('.filter-tab');
    const targetSelector = group.getAttribute('data-filter-target');
    const items = document.querySelectorAll(targetSelector);

    buttons.forEach(btn => {
      btn.addEventListener('click', () => {
        buttons.forEach(b => b.classList.remove('active'));
        btn.classList.add('active');

        const filterVal = btn.getAttribute('data-filter');
        items.forEach(item => {
          const itemCategory = item.getAttribute('data-category');
          if (filterVal === 'all' || itemCategory === filterVal || (itemCategory && itemCategory.includes(filterVal))) {
            item.style.display = '';
          } else {
            item.style.display = 'none';
          }
        });
      });
    });
  });
}

// Toast Notifications System
function showToast(message, type = 'info') {
  let toastContainer = document.querySelector('.toast-container');
  if (!toastContainer) {
    toastContainer = document.createElement('div');
    toastContainer.className = 'toast-container';
    document.body.appendChild(toastContainer);
  }

  const toast = document.createElement('div');
  toast.className = `toast ${type}`;
  
  let icon = 'fa-info-circle';
  if (type === 'success') icon = 'fa-check-circle';
  if (type === 'warning') icon = 'fa-exclamation-triangle';
  if (type === 'danger') icon = 'fa-times-circle';

  // The message is shown as plain text (it can contain names typed in by users)
  toast.innerHTML = `<i class="fa-solid ${icon}"></i> <span>${escapeHtml(String(message))}</span>`;
  toastContainer.appendChild(toast);

  setTimeout(() => {
    toast.style.opacity = '0';
    toast.style.transform = 'translateX(100%)';
    toast.style.transition = 'all 0.3s ease';
    setTimeout(() => toast.remove(), 300);
  }, 4000);
}

// Messaging Interaction
function initMessaging() {
  const chatForm = document.getElementById('chat-form');
  const chatInput = document.getElementById('chat-input');
  const chatMessages = document.getElementById('chat-messages');
  const convoItems = document.querySelectorAll('.conversation-item');
  const chatTabs = document.querySelectorAll('.chat-tab-btn');

  // Filter conversations
  if (chatTabs.length > 0) {
    chatTabs.forEach(tab => {
      tab.addEventListener('click', () => {
        chatTabs.forEach(t => t.classList.remove('active'));
        tab.classList.add('active');
        const category = tab.getAttribute('data-chat-type');

        convoItems.forEach(item => {
          const itemType = item.getAttribute('data-chat-type');
          if (category === 'all' || itemType === category) {
            item.style.display = 'flex';
          } else {
            item.style.display = 'none';
          }
        });
      });
    });
  }

  // Switch active conversation
  convoItems.forEach(item => {
    item.addEventListener('click', () => {
      convoItems.forEach(c => c.classList.remove('active'));
      item.classList.add('active');

      const name = item.querySelector('.convo-name')?.innerText || 'Chat';
      const headerTitle = document.getElementById('chat-active-name');
      if (headerTitle) headerTitle.innerText = name;

      // Mark unread dot removed
      const unread = item.querySelector('.unread-dot');
      if (unread) unread.remove();

      showToast(`Switched conversation to ${name}`, 'info');
    });
  });

  // Sending a message
  if (chatForm && chatInput && chatMessages) {
    chatForm.addEventListener('submit', (e) => {
      e.preventDefault();
      const text = chatInput.value.trim();
      if (!text) return;

      const timeNow = new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });

      const msgEl = document.createElement('div');
      msgEl.className = 'message-row sent';
      msgEl.innerHTML = `
        <div class="message-bubble">
          <p>${escapeHtml(text)}</p>
          <span class="message-meta">${timeNow} · Sent</span>
        </div>
      `;
      chatMessages.appendChild(msgEl);
      chatInput.value = '';
      chatMessages.scrollTop = chatMessages.scrollHeight;

      // Auto-reply simulation for realism
      setTimeout(() => {
        const replyEl = document.createElement('div');
        replyEl.className = 'message-row received';
        replyEl.innerHTML = `
          <div class="message-bubble">
            <p>Thank you for the update! I will look into this and get back to you shortly.</p>
            <span class="message-meta">Just now</span>
          </div>
        `;
        chatMessages.appendChild(replyEl);
        chatMessages.scrollTop = chatMessages.scrollHeight;
      }, 1500);
    });
  }
}

// Calendar View Switcher
function initCalendarViews() {
  const viewBtns = document.querySelectorAll('.cal-view-btn');
  const calendarView = document.getElementById('calendar-grid-view');
  const listView = document.getElementById('calendar-list-view');
  const cardView = document.getElementById('calendar-cards-view');

  viewBtns.forEach(btn => {
    btn.addEventListener('click', () => {
      viewBtns.forEach(b => b.classList.remove('active'));
      btn.classList.add('active');
      const view = btn.getAttribute('data-view');

      if (calendarView) calendarView.style.display = view === 'calendar' ? 'block' : 'none';
      if (listView) listView.style.display = view === 'list' ? 'block' : 'none';
      if (cardView) cardView.style.display = view === 'card' ? 'block' : 'none';
    });
  });
}

// Interactive demo buttons (RSVP, Join Club, Apply, Book)
function initInteractiveActions() {
  // RSVP Buttons
  document.querySelectorAll('.btn-rsvp').forEach(btn => {
    btn.addEventListener('click', function() {
      if (this.classList.contains('btn-success')) {
        this.classList.remove('btn-success');
        this.classList.add('btn-primary');
        this.innerHTML = '<i class="fa-solid fa-calendar-check"></i> RSVP';
        showToast('RSVP cancelled', 'info');
      } else {
        this.classList.remove('btn-primary');
        this.classList.add('btn-success');
        this.innerHTML = '<i class="fa-solid fa-check"></i> Going';
        showToast('RSVP confirmed! Added to your schedule.', 'success');
      }
    });
  });

  // Join Club Buttons
  document.querySelectorAll('.btn-join-club').forEach(btn => {
    btn.addEventListener('click', function() {
      if (this.innerText.includes('Join')) {
        this.className = 'btn btn-outline btn-sm';
        this.innerHTML = '<i class="fa-solid fa-check text-success"></i> Joined';
        showToast('Successfully joined the club!', 'success');
      } else {
        this.className = 'btn btn-primary btn-sm btn-join-club';
        this.innerText = 'Join';
        showToast('Left the club', 'info');
      }
    });
  });

  // Bookmark / Save Job
  document.querySelectorAll('.btn-save-job').forEach(btn => {
    btn.addEventListener('click', function(e) {
      e.preventDefault();
      const icon = this.querySelector('i');
      if (icon.classList.contains('fa-regular')) {
        icon.classList.remove('fa-regular');
        icon.classList.add('fa-solid');
        icon.style.color = '#EF4444';
        showToast('Opportunity saved to your favorites!', 'success');
      } else {
        icon.classList.remove('fa-solid');
        icon.classList.add('fa-regular');
        icon.style.color = '';
        showToast('Removed from saved opportunities', 'info');
      }
    });
  });

  // Forms submission toast simulation
  document.querySelectorAll('form[data-toast-success]').forEach(form => {
    form.addEventListener('submit', function(e) {
      e.preventDefault();
      const msg = this.getAttribute('data-toast-success') || 'Action completed successfully!';
      const modal = this.closest('.modal-overlay');
      if (modal) {
        modal.classList.remove('active');
        document.body.style.overflow = '';
      }
      this.reset();
      showToast(msg, 'success');
    });
  });
}

function escapeHtml(str) {
  return str.replace(/[&<>'"]/g, 
    tag => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', "'": '&#39;', '"': '&quot;' }[tag] || tag)
  );
}
