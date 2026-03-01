<?php
require_once __DIR__ . '/../config/database.php';

echo "Running Migration V27...\n";

$sql = file_get_contents(__DIR__ . '/migrations/v27_create_leads_table.sql');

if (!$sql) {
    die("Error reading migration file.");
}

// Split by command (basic assumption: commands separated by semicolon at end of line)
// But formatting might be issue. Multi_query is better.

if ($conn->multi_query($sql)) {
    do {
        /* store first result set */
        if ($result = $conn->store_result()) {
            $result->free();
        }
    } while ($conn->next_result());
    echo "Migration V27 executed successfully.\n";
} else {
    echo "Error executing migration: " . $conn->error . "\n";
}
?>