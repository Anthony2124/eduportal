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
    if (add) openContentForm(add.dataset.contentAdd, null, add.dataset.preset ? JSON.parse(add.dataset.preset) : null);
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

    // Details dialog: clicking anywhere on an item (or its title button) shows everything about it. Other buttons
    // and links in the item (Edit, Remove, Teach) keep doing their own thing, and selecting text doesn't open it.
    const detailItem = e.target.closest('[data-detail]');
    const control = e.target.closest('a, button, input, select, textarea, label');
    if (detailItem && (!control || control.matches('[data-detail-trigger]')) && !String(window.getSelection())) {
      openDetail(JSON.parse(detailItem.dataset.detail), control || detailItem);
    }
    // Items with their own page (assignments): clicking anywhere on them opens it
    const linkItem = e.target.closest('[data-href]');
    if (linkItem && !control && !String(window.getSelection())) {
      window.location.href = linkItem.dataset.href;
    }
  });

  // The details dialog also closes with Escape; focus goes back to what opened it
  const detailModal = document.getElementById('detail-modal');
  if (detailModal) {
    document.addEventListener('keydown', (e) => {
      if (e.key === 'Escape' && detailModal.classList.contains('active')) {
        detailModal.classList.remove('active');
        document.body.style.overflow = '';
        returnDetailFocus();
      }
    });
    detailModal.addEventListener('click', (e) => {
      if (e.target === detailModal || e.target.closest('[data-modal-close]')) returnDetailFocus();
    });
  }

  initScheduleChart();

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
    // Forms that ask one thing first (course form: which program): picking an answer moves on, "Change" goes back
    form.addEventListener('click', (e) => {
      const choice = e.target.closest('[data-step-choice]');
      if (choice) {
        choice.closest('[data-form-step]').querySelector('input[type=hidden]').value = choice.value;
        showFormStep(form, false);
      }
      if (e.target.closest('[data-step-back]')) showFormStep(form, true);
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

// Opens the Add form (item = null, optionally with preset values) or the Edit form filled with the item's current values
function openContentForm(type, item, preset = null) {
  const modal = document.getElementById(`content-modal-${type}`);
  if (!modal) return;
  const form = modal.querySelector('form');
  const title = modal.querySelector('.modal-title');

  form.reset();
  form.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
  form.querySelector('.form-error').hidden = true;
  // reset() leaves hidden inputs as they were
  form.querySelectorAll('input[type=hidden]').forEach(input => { input.value = ''; });
  form.elements.id.value = item ? item.id : '';
  if (item || preset) {
    for (const [name, value] of Object.entries(item || preset)) {
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
  showFormStep(form);
}

// A form that asks one thing first shows only that question until it has an answer (choosing = undefined: decide
// from the answer), then the other fields under a "Program: ... Change" line. Focuses the first box of what's shown.
function showFormStep(form, choosing) {
  const choose = form.querySelector('[data-form-step="choose"]');
  if (choose) {
    const answer = choose.querySelector('input[type=hidden]').value;
    choosing ??= answer === '';
    choose.hidden = !choosing;
    form.querySelector('[data-form-step="details"]').hidden = choosing;
    form.querySelector('[type=submit]').hidden = choosing;
    form.querySelector('[data-step-chosen]').textContent = answer;
    choose.querySelectorAll('[data-step-choice]').forEach(button => {
      button.setAttribute('aria-pressed', String(button.value === answer));
    });
  }
  const step = form.querySelector('[data-form-step]:not([hidden])');
  const first = step.querySelector('[data-step-choice][aria-pressed="true"]')
    || step.querySelector('input:not([type=hidden]), textarea, select, [data-step-choice]');
  first?.focus();
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
    if (field?.closest('[data-form-step="choose"]')) {
      showFormStep(form, true); // the question asked first (e.g. no program picked): back to it
    } else if (field) {
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

/* ---------------------------------------------------------------- details dialog (includes/content.php detail_attr) */

let detailOpener = null;

function detailEl(tag, className, text) {
  const node = document.createElement(tag);
  if (className) node.className = className;
  if (text !== undefined) node.textContent = text;
  return node;
}

// detail: { kicker, title, color, meta: [[icon, text]], badges: [[class, text]], facts: [[label, value]],
//           sections: [[heading, text]], link: [href, label] | null }. Everything goes in as text, never HTML.
function openDetail(detail, opener) {
  const modal = document.getElementById('detail-modal');
  if (!modal) return;
  detailOpener = opener;

  modal.querySelector('[data-detail-kicker]').textContent = detail.kicker || '';
  modal.querySelector('.modal-title').textContent = detail.title;
  const swatch = modal.querySelector('.detail-swatch');
  swatch.hidden = !detail.color;
  swatch.style.background = detail.color || '';

  const parts = [];
  if (detail.badges?.length) {
    const row = detailEl('div', 'detail-badges');
    detail.badges.forEach(([cls, text]) => row.appendChild(detailEl('span', `badge ${cls}`, text)));
    parts.push(row);
  }
  if (detail.meta?.length) {
    const list = detailEl('ul', 'detail-meta');
    detail.meta.forEach(([icon, text]) => {
      const row = detailEl('li');
      row.append(detailEl('i', icon), detailEl('span', '', text));
      list.appendChild(row);
    });
    parts.push(list);
  }
  if (detail.facts?.length) {
    const grid = detailEl('dl', 'detail-facts');
    detail.facts.forEach(([label, value]) => {
      const fact = detailEl('div', 'detail-fact');
      fact.append(detailEl('dt', '', label), detailEl('dd', '', value));
      grid.appendChild(fact);
    });
    parts.push(grid);
  }
  (detail.sections || []).forEach(([heading, text]) => {
    const section = detailEl('section', 'detail-section');
    if (heading) section.appendChild(detailEl('h4', '', heading));
    section.appendChild(detailEl('p', 'detail-text', text));
    parts.push(section);
  });
  modal.querySelector('[data-detail-body]').replaceChildren(...parts);

  const link = modal.querySelector('[data-detail-link]');
  link.hidden = !detail.link;
  if (detail.link) {
    link.href = detail.link[0];
    link.replaceChildren(document.createTextNode(`${detail.link[1]} `), detailEl('i', 'fa-solid fa-arrow-right'));
  }

  modal.classList.add('active');
  document.body.style.overflow = 'hidden';
  modal.querySelector('.modal-close-btn').focus();
}

function returnDetailFocus() {
  if (detailOpener && document.contains(detailOpener)) {
    (detailOpener.matches('[data-detail-trigger]') ? detailOpener : detailOpener.querySelector('[data-detail-trigger]'))?.focus();
  }
  detailOpener = null;
}

/* ---------------------------------------------------------------- weekly schedule chart (schedule_chart()) */

// A bar's subject code is shown inside it only when it fits; otherwise the legend, the hover card and the
// class table below carry it. Hovering or focusing a bar shows its class, time and room.
function initScheduleChart() {
  const chart = document.querySelector('.sched-chart');
  if (!chart) return;

  const fitLabels = () => chart.querySelectorAll('.sched-bar').forEach(bar => {
    // 6px of padding on each side; a hidden label (visibility) still has its width
    bar.classList.toggle('no-label', bar.querySelector('.sched-bar-label').offsetWidth > bar.clientWidth - 12);
  });
  fitLabels();
  new ResizeObserver(fitLabels).observe(chart);

  const tip = detailEl('div', 'sched-tooltip');
  tip.setAttribute('role', 'tooltip');
  tip.hidden = true;
  chart.appendChild(tip);

  const show = (bar) => {
    const [time, name, room] = JSON.parse(bar.dataset.tip);
    tip.replaceChildren(detailEl('strong', '', time), detailEl('span', '', name), ...(room ? [detailEl('span', 'muted', room)] : []));
    tip.hidden = false;
    const box = chart.getBoundingClientRect();
    const rect = bar.getBoundingClientRect();
    const left = Math.min(Math.max(rect.left - box.left, 0), box.width - tip.offsetWidth);
    tip.style.left = `${left}px`;
    tip.style.top = `${rect.top - box.top - tip.offsetHeight - 6}px`;
  };
  const hide = () => { tip.hidden = true; };
  chart.querySelectorAll('.sched-bar').forEach(bar => {
    bar.addEventListener('pointerenter', () => show(bar));
    bar.addEventListener('focus', () => show(bar));
    bar.addEventListener('pointerleave', hide);
    bar.addEventListener('blur', hide);
  });
  chart.querySelector('.sched-scroll').addEventListener('scroll', hide);
}
