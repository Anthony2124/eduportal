/**
 * Analytics page (pages/smart/analytics.php): students per program (bar chart), by gender (pie chart) and by year
 * level (column chart), plus the same numbers as a table.
 *
 * The charts filter each other: clicking a program bar, a gender slice or a year column (or using the dropdowns)
 * keeps only those students in the OTHER charts, the tiles and the table. A chart always shows its own full
 * breakdown, with the picked part highlighted; click it again to clear. Hovering or focusing a mark shows its numbers.
 */
document.addEventListener('DOMContentLoaded', () => {
  const source = document.getElementById('analytics-data');
  if (!source) return;
  const data = JSON.parse(source.textContent);
  const rows = data.rows.map(([p, g, y, c, n]) => ({ p, g, y, c, n }));
  const genderNames = Object.fromEntries(data.genders);
  const yearNames = Object.fromEntries(data.years);

  // Colors: the bar and column charts are one series each (one blue, set in css/portal.css); gender uses the first
  // two slots of the validated categorical palette, and neutral gray for "Not specified"
  const GENDER_COLORS = { male: '#2a78d6', female: '#eb6834', unspecified: '#94a3b8' };

  const filters = { program: '', gender: '', year: '', inClass: false };
  const selects = {
    program: document.getElementById('filter-program'),
    gender: document.getElementById('filter-gender'),
    year: document.getElementById('filter-year'),
  };
  const inClassBox = document.getElementById('filter-in-class');
  const clearButton = document.getElementById('filter-clear');

  // Students matching the filters; `skip` leaves out the chart's own dimension
  const matches = (row, skip) =>
    (skip === 'program' || !filters.program || row.p === filters.program) &&
    (skip === 'gender' || !filters.gender || row.g === filters.gender) &&
    (skip === 'year' || !filters.year || row.y === Number(filters.year)) &&
    (!filters.inClass || row.c === 1);
  const countBy = (key, skip) => {
    const totals = new Map();
    rows.filter(row => matches(row, skip)).forEach(row => totals.set(row[key], (totals.get(row[key]) || 0) + row.n));
    return totals;
  };
  const sum = (list) => list.reduce((total, row) => total + row.n, 0);
  const percent = (part, whole) => (whole ? Math.round((part / whole) * 100) : 0) + '%';
  const plural = (n) => `${n.toLocaleString()} student${n === 1 ? '' : 's'}`;

  const el = (tag, className, text) => {
    const node = document.createElement(tag);
    if (className) node.setAttribute('class', className);
    if (text !== undefined) node.textContent = text;
    return node;
  };

  /* ---------------------------------------------------------------- hover card */

  const tip = el('div', 'viz-tooltip');
  tip.setAttribute('role', 'tooltip');
  tip.hidden = true;
  document.body.appendChild(tip);
  const showTip = (target, value, label, note) => {
    tip.replaceChildren(el('strong', '', value), el('span', '', label), ...(note ? [el('span', 'muted', note)] : []));
    tip.hidden = false;
    const rect = target.getBoundingClientRect();
    const left = Math.min(Math.max(rect.left + rect.width / 2 - tip.offsetWidth / 2, 8), window.innerWidth - tip.offsetWidth - 8);
    const top = rect.top - tip.offsetHeight - 8;
    tip.style.left = `${left + window.scrollX}px`;
    tip.style.top = `${(top < 8 ? rect.bottom + 8 : top) + window.scrollY}px`;
  };
  const hideTip = () => { tip.hidden = true; };
  // Hover and keyboard focus show the same card
  const withTip = (node, content) => {
    node.addEventListener('pointerenter', () => showTip(node, ...content()));
    node.addEventListener('focus', () => showTip(node, ...content()));
    node.addEventListener('pointerleave', hideTip);
    node.addEventListener('blur', hideTip);
  };

  /* ---------------------------------------------------------------- filters */

  const toggle = (key, value) => {
    filters[key] = filters[key] === String(value) ? '' : String(value);
    render();
  };
  Object.entries(selects).forEach(([key, select]) => select.addEventListener('change', () => { filters[key] = select.value; render(); }));
  inClassBox.addEventListener('change', () => { filters.inClass = inClassBox.checked; render(); });
  clearButton.addEventListener('click', () => {
    Object.assign(filters, { program: '', gender: '', year: '', inClass: false });
    render();
  });

  /* ---------------------------------------------------------------- charts */

  // Students per program: horizontal bars, value at the tip
  function renderPrograms() {
    const counts = countBy('p', 'program');
    const shownTotal = sum(rows.filter(row => matches(row, 'program')));
    const max = Math.max(1, ...counts.values());
    const chart = el('div', 'hbar-chart');
    data.programs.forEach(program => {
      const n = counts.get(program) || 0;
      const picked = filters.program === program;
      const row = el('button', `hbar-row${filters.program && !picked ? ' dimmed' : ''}`);
      row.type = 'button';
      row.setAttribute('aria-pressed', String(picked));
      row.setAttribute('aria-label', `${program}: ${plural(n)}. ${picked ? 'Click to show every program' : 'Click to show only this program'}`);
      const track = el('span', 'hbar-track');
      const bar = el('span', 'hbar');
      bar.style.width = `${(n / max) * 100}%`;
      track.append(bar, el('span', 'hbar-value', n.toLocaleString()));
      row.append(el('span', 'hbar-label', program), track);
      row.addEventListener('click', () => toggle('program', program));
      withTip(row, () => [plural(n), program, `${percent(n, shownTotal)} of the students shown`]);
      chart.appendChild(row);
    });
    document.getElementById('chart-programs').replaceChildren(chart);
  }

  // Gender: pie chart with a legend (the legend rows are buttons too)
  function renderGender() {
    const counts = countBy('g', 'gender');
    const total = [...counts.values()].reduce((a, b) => a + b, 0);
    const wrap = el('div', 'pie-wrap');
    const svgNS = 'http://www.w3.org/2000/svg';
    const svg = document.createElementNS(svgNS, 'svg');
    svg.setAttribute('viewBox', '0 0 200 200');
    svg.setAttribute('class', 'pie');
    svg.setAttribute('role', 'group');
    svg.setAttribute('aria-label', 'Gender of the students shown');

    const legend = el('ul', 'pie-legend');
    let angle = -Math.PI / 2; // start at 12 o'clock
    data.genders.forEach(([key, name]) => {
      const n = counts.get(key) || 0;
      const picked = filters.gender === key;
      const dim = filters.gender && !picked;
      const label = `${name}: ${plural(n)} (${percent(n, total)})`;
      const pick = () => toggle('gender', key);
      const content = () => [plural(n), name, `${percent(n, total)} of the students shown`];

      if (n > 0) {
        const share = n / total;
        let slice;
        if (share >= 0.9999) {
          slice = document.createElementNS(svgNS, 'circle');
          slice.setAttribute('cx', '100');
          slice.setAttribute('cy', '100');
          slice.setAttribute('r', '92');
        } else {
          const end = angle + share * Math.PI * 2;
          const point = (a) => `${(100 + 92 * Math.cos(a)).toFixed(3)} ${(100 + 92 * Math.sin(a)).toFixed(3)}`;
          slice = document.createElementNS(svgNS, 'path');
          slice.setAttribute('d', `M100 100 L${point(angle)} A92 92 0 ${share > 0.5 ? 1 : 0} 1 ${point(end)} Z`);
          angle = end;
        }
        slice.setAttribute('class', `pie-slice${dim ? ' dimmed' : ''}`);
        slice.setAttribute('fill', GENDER_COLORS[key]);
        slice.setAttribute('tabindex', '0');
        slice.setAttribute('role', 'button');
        slice.setAttribute('aria-pressed', String(picked));
        slice.setAttribute('aria-label', label);
        slice.addEventListener('click', pick);
        slice.addEventListener('keydown', (e) => {
          if (e.key === 'Enter' || e.key === ' ') {
            e.preventDefault();
            pick();
          }
        });
        withTip(slice, content);
        svg.appendChild(slice);
      }

      const item = el('li');
      const button = el('button', `pie-legend-item${dim ? ' dimmed' : ''}`);
      button.type = 'button';
      button.setAttribute('aria-pressed', String(picked));
      button.setAttribute('aria-label', label);
      const swatch = el('span', 'pie-swatch');
      swatch.style.background = GENDER_COLORS[key];
      button.append(swatch, el('span', 'pie-legend-name', name), el('strong', '', n.toLocaleString()), el('span', 'muted', percent(n, total)));
      button.addEventListener('click', pick);
      item.appendChild(button);
      legend.appendChild(item);
    });
    if (!total) {
      const empty = document.createElementNS(svgNS, 'circle');
      empty.setAttribute('cx', '100');
      empty.setAttribute('cy', '100');
      empty.setAttribute('r', '92');
      empty.setAttribute('class', 'pie-empty');
      svg.appendChild(empty);
    }
    wrap.append(svg, legend);
    document.getElementById('chart-gender').replaceChildren(wrap);
  }

  // Year level: columns, value on the cap
  function renderYears() {
    const counts = countBy('y', 'year');
    const shownTotal = sum(rows.filter(row => matches(row, 'year')));
    const max = Math.max(1, ...counts.values());
    const chart = el('div', 'vbar-chart');
    data.years.forEach(([year, name]) => {
      const n = counts.get(year) || 0;
      const picked = filters.year === String(year);
      const column = el('button', `vbar-col${filters.year && !picked ? ' dimmed' : ''}`);
      column.type = 'button';
      column.setAttribute('aria-pressed', String(picked));
      column.setAttribute('aria-label', `${name}: ${plural(n)}. ${picked ? 'Click to show every year' : 'Click to show only this year'}`);
      const area = el('span', 'vbar-area');
      const bar = el('span', 'vbar');
      bar.style.height = `${(n / max) * 85}%`; // the rest of the column is room for the value on top
      area.append(el('span', 'vbar-value', n.toLocaleString()), bar);
      column.append(area, el('span', 'vbar-label', name));
      column.addEventListener('click', () => toggle('year', year));
      withTip(column, () => [plural(n), name, `${percent(n, shownTotal)} of the students shown`]);
      chart.appendChild(column);
    });
    document.getElementById('chart-years').replaceChildren(chart);
  }

  // The same numbers as a table: a row per program, with gender and year columns, under every filter
  function renderTable() {
    const shown = rows.filter(row => matches(row));
    const programs = filters.program ? [filters.program] : data.programs;
    const columns = [...data.genders.map(([key, name]) => ['g', key, name]), ...data.years.map(([year, name]) => ['y', year, name])];
    const count = (list, key, value) => sum(list.filter(row => row[key] === value));

    const head = el('thead');
    const headRow = el('tr');
    headRow.append(el('th', '', 'Program'), ...columns.map(([, , name]) => el('th', 'num', name)), el('th', 'num', 'Total'));
    head.appendChild(headRow);

    const body = el('tbody');
    programs.forEach(program => {
      const list = shown.filter(row => row.p === program);
      const tr = el('tr');
      tr.append(el('td', '', program), ...columns.map(([key, value]) => el('td', 'num', count(list, key, value).toLocaleString())),
        el('td', 'num total', sum(list).toLocaleString()));
      body.appendChild(tr);
    });
    const foot = el('tfoot');
    const footRow = el('tr');
    footRow.append(el('td', '', 'Total'), ...columns.map(([key, value]) => el('td', 'num', count(shown, key, value).toLocaleString())),
      el('td', 'num total', sum(shown).toLocaleString()));
    foot.appendChild(footRow);
    document.getElementById('analytics-table').replaceChildren(head, body, foot);
  }

  function render() {
    hideTip();
    Object.entries(selects).forEach(([key, select]) => { select.value = filters[key]; });
    inClassBox.checked = filters.inClass;
    clearButton.hidden = !(filters.program || filters.gender || filters.year || filters.inClass);

    const shown = rows.filter(row => matches(row));
    const total = sum(shown);
    const inClass = sum(shown.filter(row => row.c === 1));
    document.querySelector('[data-stat="students"]').textContent = total.toLocaleString();
    document.querySelector('[data-stat="in-class"]').textContent = `${inClass.toLocaleString()} (${percent(inClass, total)})`;
    document.querySelector('[data-stat="not-in-class"]').textContent = (total - inClass).toLocaleString();

    // keep keyboard focus on the chart the user just clicked in
    const focusedChart = document.activeElement?.closest('.viz')?.id;
    const focusedIndex = focusedChart ? [...document.getElementById(focusedChart).querySelectorAll('[aria-pressed]')].indexOf(document.activeElement) : -1;

    renderPrograms();
    renderGender();
    renderYears();
    renderTable();

    if (focusedChart && focusedIndex >= 0) {
      document.getElementById(focusedChart).querySelectorAll('[aria-pressed]')[focusedIndex]?.focus();
    }
  }

  window.addEventListener('scroll', hideTip, { passive: true });
  render();
});
