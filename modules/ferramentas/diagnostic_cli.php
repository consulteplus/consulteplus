<?php
// Bypass header, just verify DB
require_once __DIR__ . '/../../config/database.php';

echo "Checking Database Connection...\n";

if (isset($conn)) {
    echo "Connection OK.\n";

    // Check table
    $result = $conn->query("SHOW TABLES LIKE 'ferramentas_tipos'");
    if ($result && $result->num_rows > 0) {
        echo "Table 'ferramentas_tipos' EXISTS.\n";

        $data = $conn->query("SELECT * FROM ferramentas_tipos");
        echo "Table rows: " . $data->num_rows . "\n";
        while ($row = $data->fetch_assoc()) {
            echo " - " . $row['nome'] . " (Slug: " . $row['slug'] . ")\n";
        }
    } else {
        echo "Table 'ferramentas_tipos' DOES NOT EXIST.\n";
    }
} else {
    echo "Connection FAILED.\n";
}
?>