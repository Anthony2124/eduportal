/**
 * Add / Edit / Remove buttons (includes/content.php), enrollment, grades, and faculty picking classes.
 * Changes are saved through api/content.php and api/academics.php, then the page reloads to show them.
 * Uses SITE_ROOT, showToast and confirmAction from js/main.js.
 */
document.addEventListener('DOMContentLoaded', () => {
  document.addEventListener('click', (e) => {
    const add = e.target.closest('[data-content-add]');
    const edit = e.target.closest('[data-content-edit]');
    const remove = e.target.closest('[data-content-delete]');
    if (add) openContentForm(add.dataset.contentAdd, null);
    if (edit) openContentForm(edit.dataset.contentEdit, JSON.parse(edit.dataset.item));
    if (remove) removeContent(remove.dataset.contentDelete, remove.dataset.id, remove.dataset.name, remove.dataset.note);

    // Enrollment and grades (Enrollment and Grades pages)
    const enroll = e.target.closest('[data-enroll-add]');
    const unenroll = e.target.closest('[data-unenroll]');
    const gradeEdit = e.target.closest('[data-grade-edit]');
    const gradeClear = e.target.closest('[data-grade-clear]');
    if (enroll) openModalForm('enroll-modal', { course_id: enroll.dataset.enrollAdd });
    if (unenroll) unenrollStudent(unenroll.dataset);
    if (gradeEdit) openGradeForm(gradeEdit.dataset);
    if (gradeClear) clearGrade(gradeClear.dataset);

    // Faculty picking classes (Courses page)
    const teach = e.target.closest('[data-class-teach]');
    const drop = e.target.closest('[data-class-drop]');
    if (teach) teachClass(teach.dataset);
    if (drop) dropClass(drop.dataset);
  });

  document.querySelectorAll('[data-content-modal]').forEach(modal => {
    const form = modal.querySelector('form');
    form.addEventListener('submit', (e) => {
      e.preventDefault();
      saveContent(modal.dataset.contentModal, form);
    });
    form.addEventListener('input', (e) => {
      e.target.classList.remove('is-invalid');
      e.target.closest('.day-picker')?.classList.remove('is-invalid');
    });
  });

  const enrollForm = document.querySelector('#enroll-modal form');
  if (enrollForm) {
    enrollForm.addEventListener('submit', (e) => {
      e.preventDefault();
      submitAcademics('enroll', enrollForm, 'Student enrolled and added to the class group chat.');
    });
  }
  const gradeForm = document.querySelector('#grade-modal form');
  if (gradeForm) {
    gradeForm.addEventListener('submit', (e) => {
      e.preventDefault();
      submitAcademics('save_grade', gradeForm, 'Grades saved.');
    });
    gradeForm.addEventListener('input', (e) => e.target.classList.remove('is-invalid'));
  }
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
      if (name === 'id' || !form.elements[name]) continue;
      const boxes = form.querySelectorAll(`input[type=checkbox][name="${name}"]`);
      if (boxes.length) {
        // checkbox group (class days): "Mon,Wed,Fri"
        const chosen = String(value ?? '').split(',');
        boxes.forEach(box => { box.checked = chosen.includes(box.value); });
      } else {
        form.elements[name].value = value ?? '';
      }
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
  const data = new FormData(form);
  const payload = Object.fromEntries(data);
  // checkbox groups (class days) send every ticked value
  form.querySelectorAll('.day-picker').forEach(group => {
    const name = group.querySelector('input').name;
    payload[name] = data.getAll(name);
  });
  errorBox.hidden = true;
  submit.disabled = true;

  try {
    await contentApi('save', type, payload);
    showToast(payload.id ? 'Changes saved.' : 'Added.', 'success');
    setTimeout(() => window.location.reload(), 600);
  } catch (err) {
    errorBox.textContent = err.message;
    errorBox.hidden = false;
    let field = err.field && form.elements[err.field];
    if (field instanceof RadioNodeList) field = field[0]; // checkbox group: highlight the group, focus its first box
    if (field) {
      (field.closest('.day-picker') || field).classList.add('is-invalid');
      field.focus();
    }
    submit.disabled = false;
  }
}

async function removeContent(type, id, name, note) {
  const confirmed = await confirmAction(`Remove "${name}"?`, `${note ? note + ' ' : ''}This can't be undone.`, 'Remove', 'fa-trash-can');
  if (!confirmed) return;
  try {
    await contentApi('delete', type, { id: Number(id) });
    showToast('Removed.', 'success');
    setTimeout(() => window.location.reload(), 600);
  } catch (err) {
    showToast(err.message, 'danger');
  }
}

/* ---------------------------------------------------------------- enrollment and grades (api/academics.php) */

async function academicsApi(action, payload) {
  const response = await fetch(new URL(`api/academics.php?action=${action}`, SITE_ROOT), {
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

// Opens a dialog (#id) with its form reset and filled with values
function openModalForm(id, values) {
  const modal = document.getElementById(id);
  if (!modal) return;
  const form = modal.querySelector('form');
  form.reset();
  form.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
  form.querySelector('.form-error').hidden = true;
  for (const [name, value] of Object.entries(values)) {
    if (form.elements[name]) form.elements[name].value = value ?? '';
  }
  modal.classList.add('active');
  document.body.style.overflow = 'hidden';
  const first = form.querySelector('select, input:not([type=hidden])');
  if (first) first.focus();
  return modal;
}

function openGradeForm(data) {
  const modal = openModalForm('grade-modal', {
    course_id: data.course, student_id: data.student, prelim: data.prelim, midterm: data.midterm, final: data.final
  });
  if (modal) modal.querySelector('.modal-title').textContent = `Grades for ${data.name}`;
}

async function submitAcademics(action, form, successMessage) {
  const submit = form.querySelector('[type=submit]');
  const errorBox = form.querySelector('.form-error');
  errorBox.hidden = true;
  submit.disabled = true;
  try {
    await academicsApi(action, Object.fromEntries(new FormData(form)));
    showToast(successMessage, 'success');
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

async function unenrollStudent(data) {
  const confirmed = await confirmAction(`Remove ${data.name} from ${data.courseName}?`,
    "Their grades for this course are removed too, and they leave the class group chat. This can't be undone.", 'Remove', 'fa-user-minus');
  if (!confirmed) return;
  try {
    await academicsApi('unenroll', { course_id: data.course, student_id: data.student });
    showToast('Student removed from the course.', 'success');
    setTimeout(() => window.location.reload(), 600);
  } catch (err) {
    showToast(err.message, 'danger');
  }
}

async function teachClass(data) {
  const confirmed = await confirmAction(`Teach ${data.name}?`,
    `You become its instructor: ${data.details}. You can drop it later.`, 'Teach this class', 'fa-chalkboard-user');
  if (!confirmed) return;
  try {
    await academicsApi('teach', { course_id: data.classTeach });
    showToast(`You now teach ${data.name}.`, 'success');
    setTimeout(() => window.location.reload(), 600);
  } catch (err) {
    showToast(err.message, 'danger');
  }
}

async function dropClass(data) {
  const confirmed = await confirmAction(`Stop teaching ${data.name}?`,
    'It goes back to the open classes for another instructor. Its students, assignments and grades stay.', 'Drop class', 'fa-right-from-bracket');
  if (!confirmed) return;
  try {
    await academicsApi('drop_class', { course_id: data.classDrop });
    showToast('Class dropped.', 'success');
    setTimeout(() => window.location.reload(), 600);
  } catch (err) {
    showToast(err.message, 'danger');
  }
}

async function clearGrade(data) {
  const confirmed = await confirmAction(`Clear ${data.name}'s grades?`, 'The midterm and final grades for this course are removed.', 'Clear', 'fa-eraser');
  if (!confirmed) return;
  try {
    await academicsApi('clear_grade', { course_id: data.course, student_id: data.student });
    showToast('Grades cleared.', 'success');
    setTimeout(() => window.location.reload(), 600);
  } catch (err) {
    showToast(err.message, 'danger');
  }
}
