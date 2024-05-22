<?php
// SQLite3 database file
$db = new SQLite3('db.sqlite3');

// Open the CSV file
$csv = fopen('programs.csv', 'r');

if ($csv === false) {
    die('Could not open the CSV file.');
}

// Get the first row of the CSV file
$headers = fgetcsv($csv);

if ($headers === false) {
    die('Could not read the headers from the CSV file.');
}

// Create the SQL statement to create the table
$sql = 'CREATE TABLE IF NOT EXISTS programs (';
foreach ($headers as $header) {
    $header = trim($header);
    if ($header === '') {
        continue;
    }
    $sql .= '"' . str_replace(' ', '_', strtolower($header)) . '" TEXT, ';
}
$sql = rtrim($sql, ', ') . ')';

// Execute the SQL statement to create the table
if (!$db->exec($sql)) {
    die('Failed to create the table.');
}

// Insert the data from the CSV file into the table
while (($row = fgetcsv($csv)) !== false) {
    $values = array_map([$db, 'escapeString'], $row);
    $placeholders = implode(', ', array_fill(0, count($values), '?'));
    $stmt = $db->prepare('INSERT INTO programs VALUES (' . $placeholders . ')');
    
    foreach ($values as $index => $value) {
        $stmt->bindValue($index + 1, $value, SQLITE3_TEXT);
    }

    if (!$stmt->execute()) {
        die('Failed to insert data into the table.');
    }
}

// Close the CSV file
fclose($csv);

// Close the database connection
$db->close();
?>
