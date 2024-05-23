<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Programs</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/css/bootstrap.min.css">
    <!-- bootstrap icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
</head>

<?php
// get total number of programs
$db = new SQLite3('db.sqlite3');
$programs = $db->query('SELECT * FROM programs');
// get total number of programs
$totalPrograms = 0;
while ($program = $programs->fetchArray(SQLITE3_ASSOC)) {
    $totalPrograms++;
}
$db->close();

// get the category_of_credentialing distinct values
$db = new SQLite3('db.sqlite3');
$programs = $db->query('SELECT DISTINCT category_of_credentialing FROM programs');
$categories = [];
while ($category = $programs->fetchArray(SQLITE3_ASSOC)) {
    $categories[] = $category['category_of_credentialing'];
}

// get the degree distinct values
$db = new SQLite3('db.sqlite3');
$programs = $db->query('SELECT DISTINCT level_of_degree FROM programs');
$degrees = [];
while ($degree = $programs->fetchArray(SQLITE3_ASSOC)) {
    $degrees[] = $degree['level_of_degree'];
}

// get the program format distinct values
$db = new SQLite3('db.sqlite3');
$programs = $db->query('SELECT DISTINCT format FROM programs');
$formats = [];
while ($format = $programs->fetchArray(SQLITE3_ASSOC)) {
    $formats[] = $format['format'];
}

// get the program type distinct values
$db = new SQLite3('db.sqlite3');
$programs = $db->query('SELECT DISTINCT program_type FROM programs');
$programTypes = [];
while ($programType = $programs->fetchArray(SQLITE3_ASSOC)) {
    $programTypes[] = $programType['program_type'];
}

$db->close();
?>

<body>
    <div class="container">
        <h1 class="my-4">Programs</h1>
        <p>Total Programs: <?= $totalPrograms ?></p>
        <!-- Search and Filter Options -->
        <div class="row mb-4">
            <div class="col">
                <select class="form-select" id="stateFilter">
                    <option value="">Filter by State</option>
                    <!-- Dynamically populate state options -->
                    <?php
                    $db = new SQLite3('db.sqlite3');
                    $programs = $db->query('SELECT DISTINCT state FROM programs');
                    while ($state = $programs->fetchArray(SQLITE3_ASSOC)) {
                        echo "<option value=\"{$state['state']}\">{$state['state']}</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="col">
                <!-- program type (ECE/ECSE/Blended) -->
                <select class="form-select" id="programTypeFilter">
                    <option value="">Filter by Program Type</option>
                    <?php foreach ($programTypes as $programType) : ?>
                        <option value="<?= $programType ?>"><?= $programType ?></option>
                    <?php endforeach; ?>
                </select>

            </div>

            <div class="col">
                <!-- category of credentialing (all distinct values) -->
                <select class="form-select" id="categoryFilter">
                    <option value="">Filter by Category of Credentialing</option>
                    <?php foreach ($categories as $category) : ?>
                        <option value="<?= $category ?>"><?= $category ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col">
                <select class="form-select" id="degreeFilter">
                    <option value="">Filter by Degree</option>
                    <?php foreach ($degrees as $degree) : ?>
                        <option value="<?= $degree ?>"><?= $degree ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col">
                <!-- program format (all, online, in-person, hybrid, online option) -->
                <select class="form-select" id="formatFilter">
                    <option value="">Filter by Program Format</option>
                    <?php foreach ($formats as $format) : ?>
                        <option value="<?= $format ?>"><?= $format ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

        </div>

        <div id="programContainer">
            <?php
            $db = new SQLite3('db.sqlite3');
            $programs = $db->query('SELECT * FROM programs');

            $states = [];
            while ($program = $programs->fetchArray(SQLITE3_ASSOC)) {
                $state = $program['state'];
                if (!isset($states[$state])) {
                    $states[$state] = [];
                }
                $states[$state][] = $program;
            }

            foreach ($states as $state => $programs) : ?>
                <div class='state-group' data-state='<?= $state ?>'>
                    <h2 class='state-header'>State: <?= $state ?> (<?= count($programs) ?> programs)</h2>
                    <div class='row'>
                        <?php foreach ($programs as $program) : ?>
                            <div class='col-md-3 mb-4 program-card' data-state='<?= $program['state'] ?>' data-title='<?= $program['ihe_name'] ?>'>
                                <div class='card h-100'>
                                    <div class="card-header">
                                        <h5 class="card-title"><?= $program['program_title'] ?></h5>
                                    </div>
                                    <div class='card-body'>
                                    <span class="badge bg-dark"><?= $program['ihe_name']; ?></span>
                                    <span class="badge bg-primary"><?= $program['level_of_degree'] ?></span>
                                        <span class="badge bg-secondary"><?= $program['format'] ?></span>
                                        <span class="badge bg-success"><?= $program['program_type'] ?></span>
                                        <span class="badge bg-danger"><?= $program['category_of_credentialing'] ?></span>

                                    </div>
                                    <div class='card-footer'>

                                        <a target="_blank" href='<?= $program['url_for_program'] ?>' class='btn btn-primary'>
                                            Program Website
                                            <!-- bootstarp icons external link -->
                                            <i class='bi bi-box-arrow-up-right'></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div> <!-- Close row -->
                </div> <!-- Close state-group -->
            <?php endforeach;


            $db->close();
            ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function filterPrograms() {
            const searchValue = document.getElementById('searchInput').value.toLowerCase();
            const selectedState = document.getElementById('stateFilter').value;
            let visibleStates = new Set();

            document.querySelectorAll('.program-card').forEach(card => {
                const title = card.getAttribute('data-title').toLowerCase();
                const state = card.getAttribute('data-state');
                const matchesSearch = title.includes(searchValue);
                const matchesState = selectedState === '' || state === selectedState;

                if (matchesSearch && matchesState) {
                    card.style.display = '';
                    visibleStates.add(state);
                } else {
                    card.style.display = 'none';
                }
            });

            document.querySelectorAll('.state-group').forEach(group => {
                const state = group.getAttribute('data-state');
                if (visibleStates.has(state)) {
                    group.style.display = '';
                } else {
                    group.style.display = 'none';
                }
            });
        }

        document.getElementById('searchInput').addEventListener('keyup', filterPrograms);
        document.getElementById('stateFilter').addEventListener('change', filterPrograms);
    </script>
</body>

</html>