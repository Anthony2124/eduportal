<?php
// Analytics (Admin): the students - how many are in each program, their gender and their year level - as a bar
// chart, a pie chart and a column chart that filter each other (js/analytics.js), with the same numbers in a table.
require __DIR__ . '/../../includes/app_shell.php';
$page = 'pages/smart/analytics.php';
$user = require_page_access($page);

// How many students there are for each program, gender, year level and "enrolled in a class or not".
// Students who never answered the gender question count as "Not specified", like "Prefer not to say".
$rows = db()->query(
    "SELECT u.program, COALESCE(u.gender, 'unspecified') AS gender, u.year_level,
            EXISTS (SELECT 1 FROM enrollments en WHERE en.student_id = u.id) AS in_class, COUNT(*) AS students
     FROM users u
     WHERE u.role = 'student'
     GROUP BY u.program, COALESCE(u.gender, 'unspecified'), u.year_level, in_class"
)->fetchAll();
foreach ($rows as &$row) {
    $row['program'] ??= 'No program';
}
unset($row);

// Every program is listed (even with no students yet); 1st to 4th year, and 5th year only if someone is in it
$programs = array_values(array_unique([...PROGRAMS, ...array_column($rows, 'program')]));
$years = array_filter([1 => '1st Year', 2 => '2nd Year', 3 => '3rd Year', 4 => '4th Year', 5 => '5th Year'],
    fn($year) => $year <= 4 || in_array($year, array_map('intval', array_column($rows, 'year_level')), true), ARRAY_FILTER_USE_KEY);

app_page_start($user, $page, 'Analytics');
app_page_header('Analytics', 'Students by program, gender and year level. Click a bar or slice to filter the other charts.');

if (!$rows) {
    app_empty_card('fa-chart-pie', 'Students', 'fa-user-graduate', 'No students yet', 'Charts appear here once students create their accounts.');
    app_page_end();
    return;
}
?>
            <section class="card section-gap analytics-filters" aria-label="Filters">
                <div class="analytics-filter">
                    <label for="filter-program">Program</label>
                    <select class="form-select" id="filter-program" data-filter="program">
                        <option value="">All programs</option>
                        <?php foreach ($programs as $program): ?>
                        <option><?= e($program) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="analytics-filter">
                    <label for="filter-gender">Gender</label>
                    <select class="form-select" id="filter-gender" data-filter="gender">
                        <option value="">Everyone</option>
                        <option value="male">Male</option>
                        <option value="female">Female</option>
                        <option value="unspecified">Not specified</option>
                    </select>
                </div>
                <div class="analytics-filter">
                    <label for="filter-year">Year level</label>
                    <select class="form-select" id="filter-year" data-filter="year">
                        <option value="">All years</option>
                        <?php foreach ($years as $year => $label): ?>
                        <option value="<?= $year ?>"><?= e($label) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <label class="analytics-check">
                    <input type="checkbox" id="filter-in-class"> Only students enrolled in a class
                </label>
                <button type="button" class="btn btn-outline btn-sm" id="filter-clear" hidden><i class="fa-solid fa-xmark"></i> Clear filters</button>
            </section>

            <section class="grid-3 section-gap" aria-live="polite">
                <div class="stat-tile">
                    <div class="stat-tile-icon tone-primary"><i class="fa-solid fa-user-graduate"></i></div>
                    <div>
                        <div class="stat-tile-value" data-stat="students">0</div>
                        <div class="stat-tile-label">Students</div>
                    </div>
                </div>
                <div class="stat-tile">
                    <div class="stat-tile-icon tone-success"><i class="fa-solid fa-book-open-reader"></i></div>
                    <div>
                        <div class="stat-tile-value" data-stat="in-class">0</div>
                        <div class="stat-tile-label">Enrolled in a class</div>
                    </div>
                </div>
                <div class="stat-tile">
                    <div class="stat-tile-icon tone-warning"><i class="fa-solid fa-hourglass-half"></i></div>
                    <div>
                        <div class="stat-tile-value" data-stat="not-in-class">0</div>
                        <div class="stat-tile-label">Not enrolled in a class yet</div>
                    </div>
                </div>
            </section>

            <div class="analytics-grid section-gap">
                <section class="card analytics-wide">
                    <?php app_card_header('fa-chart-bar', 'Students per Program', 'Click a program to see only its students in the other charts'); ?>
                    <div class="viz" id="chart-programs"></div>
                </section>

                <section class="card">
                    <?php app_card_header('fa-chart-pie', 'Gender'); ?>
                    <div class="viz" id="chart-gender"></div>
                </section>

                <section class="card analytics-full">
                    <?php app_card_header('fa-layer-group', 'Year Level', 'Click a year to see only those students in the other charts'); ?>
                    <div class="viz" id="chart-years"></div>
                </section>

                <section class="card analytics-full">
                    <?php app_card_header('fa-table', 'Breakdown', 'The same numbers as the charts, with the filters above'); ?>
                    <div class="table-responsive">
                        <table class="data-table analytics-table" id="analytics-table"></table>
                    </div>
                </section>
            </div>

            <script type="application/json" id="analytics-data"><?= json_encode([
                'programs' => $programs,
                'genders' => [['male', 'Male'], ['female', 'Female'], ['unspecified', 'Not specified']],
                'years' => array_map(null, array_keys($years), array_values($years)),
                // [program, gender, year level, in a class (0/1), number of students]
                'rows' => array_map(fn($r) => [$r['program'], $r['gender'], (int) $r['year_level'], (int) $r['in_class'], (int) $r['students']], $rows),
            ], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?></script>
<?php
app_page_end(['js/analytics.js?v=1']);
