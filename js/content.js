/**
 * Add / Edit / Remove buttons for announcements, events, scholarships and courses (includes/content.php).
 * Changes are saved through api/content.php, then the page reloads to show them.
 * Uses SITE_ROOT, showToast and confirmAction from js/main.js.
 */
document.addEventListener('DOMContentLoaded', () => {
  document.addEventListener('click', (e) => {
    const add = e.target.closest('[data-content-add]');
    const edit = e.target.closest('[data-content-edit]');
    const remove = e.target.closest('[data-content-delete]');
    if (add) openContentForm(add.dataset.contentAdd, null);
    if (edit) openContentForm(edit.dataset.contentEdit, JSON.parse(edit.dataset.item));
    if (remove) removeContent(remove.dataset.contentDelete, remove.dataset.id, remove.dataset.name);
  });

  document.querySelectorAll('[data-content-modal]').forEach(modal => {
    const form = modal.querySelector('form');
    form.addEventListener('submit', (e) => {
      e.preventDefault();
      saveContent(modal.dataset.contentModal, form);
    });
    form.addEventListener('input', (e) => e.target.classList.remove('is-invalid'));
  });
});

// POST to api/content.php; throws with the server's message (and the field to highlight) on failure
async function contentApi(action, type, payload) {
  const response = await fetch(new URL(`api/content.php?action=${action}&type=${encodeURIComponent(type)}`, SITE_ROOT), {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify(payload)
  });
  const data = await response.json().catch(() => ({}));
  if (!response.ok) {
    throw Object.assign(new Error(data.error || `Server error (${response.status}).`), { field: data.field });
  }
  return data;
}

// Opens the Add form (item = null) or the Edit form filled with the item's current values
function openContentForm(type, item) {
  const modal = document.getElementById(`content-modal-${type}`);
  if (!modal) return;
  const form = modal.querySelector('form');
  const title = modal.querySelector('.modal-title');

  form.reset();
  form.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
  form.querySelector('.form-error').hidden = true;
  form.elements.id.value = item ? item.id : '';
  if (item) {
    for (const [name, value] of Object.entries(item)) {
      if (name !== 'id' && form.elements[name]) form.elements[name].value = value ?? '';
    }
  }
  title.textContent = item ? title.dataset.editTitle : title.dataset.addTitle;

  modal.classList.add('active');
  document.body.style.overflow = 'hidden';
  form.querySelector('input:not([type=hidden]), textarea, select').focus();
}

async function saveContent(type, form) {
  const submit = form.querySelector('[type=submit]');
  const errorBox = form.querySelector('.form-error');
  const payload = Object.fromEntries(new FormData(form));
  errorBox.hidden = true;
  submit.disabled = true;

  try {
    await contentApi('save', type, payload);
    showToast(payload.id ? 'Changes saved.' : 'Added.', 'success');
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
}

async function removeContent(type, id, name) {
  const confirmed = await confirmAction(`Remove "${name}"?`, "This can't be undone.", 'Remove', 'fa-trash-can');
  if (!confirmed) return;
  try {
    await contentApi('delete', type, { id: Number(id) });
    showToast('Removed.', 'success');
    setTimeout(() => window.location.reload(), 600);
  } catch (err) {
    showToast(err.message, 'danger');
  }
}
