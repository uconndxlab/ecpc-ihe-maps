<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Programs</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/css/bootstrap.min.css">
</head>

<body>
    <div class="container">
        <h1 class="my-4">Programs</h1>
        <!-- Search and Filter Options -->
        <div class="row mb-4">
            <div class="col-md-6">
                <input type="text" class="form-control" id="searchInput" placeholder="Search programs...">
            </div>
            <div class="col-md-6">
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

            foreach ($states as $state => $programs) {
                echo "<div class='state-group' data-state='$state'>";
                echo "<h2 class='state-header'>State: $state (" . count($programs) . " programs)</h2>\n";
                echo "<div class='row'>";

                foreach ($programs as $program) {
                    echo "<div class='col-md-3 mb-4 program-card' data-state='{$program['state']}' data-title='{$program['ihe_name']}'>";
                    echo "<div class='card h-100'>";
                    echo "<div class='card-body'>";
                    echo "<h3 class='card-title'>{$program['ihe_name']}</h3>\n";
                    echo "<p class='card-text'>Program Title: {$program['program_title']}</p>\n";
                    echo "<p class='card-text'>URL: <a href=\"{$program['url_for_program']}\" target=\"_blank\">{$program['url_for_program']}</a></p>\n";
                    echo "<p class='card-text'>Program Type: {$program['program_type']}</p>\n";
                    echo "<p class='card-text'>Category of Credentialing: {$program['category_of_credentialing']}</p>\n";
                    echo "</div>";
                    echo "</div>";
                    echo "</div>";
                }

                echo "</div>"; // Close row
                echo "</div>"; // Close state-group
            }

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
