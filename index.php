<?php

// Path: index.php
$db = new SQLite3('db.sqlite3');

$programs = $db->query('SELECT * FROM programs');

// grouping by state, display the number of programs in each state and the ihe_name and program_title of each program in that state

$states = [];
while ($program = $programs->fetchArray(SQLITE3_ASSOC)) {
    $state = $program['state'];
    if (!isset($states[$state])) {
        $states[$state] = [];
    }
    $states[$state][] = $program;
}

foreach ($states as $state => $programs) {
    echo "<h2>State: $state (". count($programs) . " programs)</h2>\n";

    foreach ($programs as $program) {
        echo "<div class='program'>";
        echo "<h3>{$program['ihe_name']}</h3>\n";
        echo "<p>Program Title: {$program['program_title']}</p>\n";
        // url and link to the program target blank
        echo "<p>URL: <a href=\"{$program['url_for_program']}\" target=\"_blank\">{$program['url_for_program']}</a></p>\n";

        // program type

        echo "<p>Program Type: {$program['program_type']}</p>\n";

        // category of credentialing

        echo "<p>Category of Credentialing: {$program['category_of_credentialing']}</p>\n";
        
        echo "</div>";
    }
    echo "\n";
}

$db->close();

