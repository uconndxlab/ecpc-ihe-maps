<?php

//sqlite3 database file
$db = new SQLite3('db.sqlite3');

// Create table "programs" with the fields found in the CSV file (programs.csv). Column headers are the first row of the CSV file and should be converted to lowercase and spaces replaced with underscores.

// Open the CSV file
$csv = fopen('programs.csv', 'r');

// Get the first row of the CSV file
$headers = fgetcsv($csv);

// Create the SQL statement to create the table
$sql = 'CREATE TABLE programs (';
foreach ($headers as $header) {
    if ($header =="" || $header == " ") {
        continue;
    }
    $sql .= str_replace(' ', '_', strtolower($header)) . ' TEXT, ';
}
$sql = rtrim($sql, ', ') . ')';

// Execute the SQL statement to create the table
$db->exec($sql);

// Insert the data from the CSV file into the table
while (($row = fgetcsv($csv)) !== false) {
    $sql = 'INSERT INTO programs VALUES (';
    foreach ($row as $value) {
        $sql .= "'" . $db->escapeString($value) . "', ";
    }
    $sql = rtrim($sql, ', ') . ')';
    $db->exec($sql);
}

// Close the CSV file
fclose($csv);

// Close the database connection
$db->close();