/**
 * Assignment page (pages/academics/assignment.php). Students attach files, write an answer (kept as a draft while
 * they type), turn their work in or unsubmit it; faculty review a student's work and return it with a score and
 * a private comment. Talks to api/submissions.php. Uses SITE_ROOT, showToast and confirmAction from js/main.js.
 */
document.addEventListener('DOMContentLoaded', () => {
  const work = document.getElementById('your-work');
  if (work) initYourWork(work);
  const review = document.getElementById('review-modal');
  if (review) initReview(review);
});

// POST to api/submissions.php: JSON, or a FormData for file uploads. Throws with the server's message.
async function submissionsApi(action, payload) {
  const upload = payload instanceof FormData;
  const response = await fetch(new URL(`api/submissions.php?action=${action}`, SITE_ROOT), {
    method: 'POST',
    headers: upload ? { 'X-Requested-With': 'fetch' } : { 'Content-Type': 'application/json' },
    body: upload ? payload : JSON.stringify(payload)
  });
  const data = await response.json().catch(() => ({}));
  if (!response.ok) {
    throw Object.assign(new Error(data.error || `Server error (${response.status}).`), { field: data.field });
  }
  return data;
}

const FILE_ICONS = {
  pdf: 'fa-file-pdf', doc: 'fa-file-word', docx: 'fa-file-word', odt: 'fa-file-word',
  xls: 'fa-file-excel', xlsx: 'fa-file-excel', csv: 'fa-file-excel', ppt: 'fa-file-powerpoint', pptx: 'fa-file-powerpoint',
  jpg: 'fa-file-image', jpeg: 'fa-file-image', png: 'fa-file-image', gif: 'fa-file-image', webp: 'fa-file-image',
  zip: 'fa-file-zipper', rar: 'fa-file-zipper', '7z': 'fa-file-zipper', txt: 'fa-file-lines', md: 'fa-file-lines'
};

// One attached file: a link that opens it (once uploaded) and a Remove button
function fileRow(file) {
  const row = document.createElement('li');
  const link = document.createElement(file.id ? 'a' : 'span');
  if (file.id) {
    link.href = new URL(`api/submissions.php?action=file&id=${file.id}`, SITE_ROOT);
    link.target = '_blank';
    link.rel = 'noopener';
  }
  const icon = document.createElement('i');
  icon.className = `fa-solid ${FILE_ICONS[file.name.split('.').pop().toLowerCase()] || 'fa-file-code'}`;
  const name = document.createElement('span');
  name.className = 'work-file-name';
  name.textContent = file.name;
  const size = document.createElement('small');
  size.textContent = file.size;
  link.append(icon, name, size);
  row.appendChild(link);

  if (file.id) {
    const remove = document.createElement('button');
    remove.type = 'button';
    remove.className = 'work-file-remove';
    remove.dataset.fileRemove = file.id;
    remove.setAttribute('aria-label', `Remove ${file.name}`);
    remove.innerHTML = '<i class="fa-solid fa-xmark"></i>';
    row.appendChild(remove);
  }
  return row;
}

function initYourWork(work) {
  const assignmentId = Number(work.dataset.assignment);
  const list = document.getElementById('work-files');
  const fileInput = document.getElementById('work-file-input');
  const body = document.getElementById('work-body');
  const saved = document.getElementById('work-saved');
  const turnIn = document.getElementById('work-turn-in');
  const unsubmit = document.getElementById('work-unsubmit');

  // "Mark as done" when there's nothing to hand in, "Turn in" otherwise (like Google Classroom)
  const updateTurnIn = () => {
    if (turnIn) turnIn.textContent = list.children.length || body.value.trim() ? 'Turn in' : 'Mark as done';
  };

  // The answer is saved as a draft a moment after typing stops, so leaving the page doesn't lose it
  let saveTimer = null;
  const saveDraft = async () => {
    clearTimeout(saveTimer);
    try {
      await submissionsApi('save', { assignment_id: assignmentId, body: body.value });
      saved.textContent = 'Draft saved';
    } catch (err) {
      saved.textContent = err.message;
    }
  };
  body?.addEventListener('input', () => {
    saved.textContent = '';
    updateTurnIn();
    clearTimeout(saveTimer);
    saveTimer = setTimeout(saveDraft, 1000);
  });

  // Files upload one by one as soon as they're picked
  fileInput?.addEventListener('change', async () => {
    const files = [...fileInput.files];
    fileInput.value = '';
    for (const file of files) {
      const row = fileRow({ name: file.name, size: 'Uploading…' });
      row.classList.add('uploading');
      list.appendChild(row);
      try {
        const form = new FormData();
        form.append('assignment_id', assignmentId);
        form.append('file', file);
        const result = await submissionsApi('upload', form);
        row.replaceWith(fileRow(result.file));
      } catch (err) {
        row.remove();
        showToast(err.message, 'danger');
      }
    }
    updateTurnIn();
  });

  list.addEventListener('click', async (e) => {
    const remove = e.target.closest('[data-file-remove]');
    if (!remove) return;
    remove.disabled = true;
    try {
      await submissionsApi('remove_file', { file_id: Number(remove.dataset.fileRemove) });
      remove.closest('li').remove();
      updateTurnIn();
    } catch (err) {
      remove.disabled = false;
      showToast(err.message, 'danger');
    }
  });

  turnIn?.addEventListener('click', async () => {
    if (list.querySelector('.uploading')) {
      showToast('Wait for your files to finish uploading.', 'danger');
      return;
    }
    const count = list.children.length;
    const title = turnIn.dataset.title;
    const message = count
      ? `${count} attachment${count === 1 ? '' : 's'}${body.value.trim() ? ' and your answer' : ''} will be turned in for "${title}".`
      : body.value.trim() ? `Your answer will be turned in for "${title}".` : `"${title}" will be marked as done with nothing attached.`;
    const label = count || body.value.trim() ? 'Turn in' : 'Mark as done';
    if (!await confirmAction(`${label}?`, message, label, 'fa-paper-plane', 'primary')) return;

    clearTimeout(saveTimer);
    turnIn.disabled = true;
    try {
      await submissionsApi('turn_in', { assignment_id: assignmentId, body: body.value });
      showToast('Turned in.', 'success');
      setTimeout(() => window.location.reload(), 600);
    } catch (err) {
      turnIn.disabled = false;
      showToast(err.message, 'danger');
    }
  });

  unsubmit?.addEventListener('click', async () => {
    if (!await confirmAction('Unsubmit?', "Unsubmit to add or change your work. Don't forget to turn it in again.",
      'Unsubmit', 'fa-rotate-left', 'primary')) return;
    unsubmit.disabled = true;
    try {
      await submissionsApi('unsubmit', { assignment_id: assignmentId });
      showToast('Unsubmitted. Turn it in again when you are done.', 'success');
      setTimeout(() => window.location.reload(), 600);
    } catch (err) {
      unsubmit.disabled = false;
      showToast(err.message, 'danger');
    }
  });

  updateTurnIn();
}

// Faculty: "Review" opens the student's work with the score and private comment, "Return" sends them back
function initReview(modal) {
  const form = modal.querySelector('form');
  const errorBox = form.querySelector('.form-error');
  const scoreInput = form.elements.score;

  document.addEventListener('click', (e) => {
    const button = e.target.closest('[data-review]');
    if (!button) return;
    const data = JSON.parse(button.dataset.review);
    form.reset();
    form.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
    errorBox.hidden = true;
    form.elements.student_id.value = data.student;
    if (scoreInput.type !== 'hidden') scoreInput.value = data.score;
    form.elements.feedback.value = data.feedback;
    modal.querySelector('.modal-title').textContent = data.name;
    modal.querySelector('#review-modal-status').textContent = data.status + (data.turnedIn ? ` • ${data.turnedIn}` : '');
    modal.querySelector('#review-work').replaceChildren(document.getElementById(`review-work-${data.student}`).content.cloneNode(true));
    modal.classList.add('active');
    document.body.style.overflow = 'hidden';
    (scoreInput.type !== 'hidden' ? scoreInput : form.elements.feedback).focus();
  });

  form.addEventListener('submit', async (e) => {
    e.preventDefault();
    const submit = form.querySelector('[type=submit]');
    errorBox.hidden = true;
    submit.disabled = true;
    try {
      await submissionsApi('return', Object.fromEntries(new FormData(form)));
      showToast(`Returned to ${modal.querySelector('.modal-title').textContent}.`, 'success');
      setTimeout(() => window.location.reload(), 600);
    } catch (err) {
      errorBox.textContent = err.message;
      errorBox.hidden = false;
      const field = err.field && form.elements[err.field];
      if (field) {
        field.classList.add('is-invalid');
        field.focus();
      }
      submit.disabled = false;
    }
  });
  form.addEventListener('input', (e) => e.target.classList.remove('is-invalid'));
}
