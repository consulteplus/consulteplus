<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

echo "Starting debug...<br>";

// Check files existence first
$files = [
    __DIR__ . '/../../../config/config.php',
    __DIR__ . '/../../../config/database.php',
    __DIR__ . '/../../../classes/ApiResponse.php'
];

foreach ($files as $f) {
    echo "File $f: " . (file_exists($f) ? "EXISTS" : "MISSING") . "<br>";
}

try {
    require_once __DIR__ . '/../../../config/config.php';
    echo "Config loaded...<br>";

    require_once __DIR__ . '/../../../config/database.php';
    echo "Database loaded...<br>";

    if (isset($conn)) {
        echo "Connection object exists.<br>";
        if ($conn->connect_error) {
            echo "Connect Error: " . $conn->connect_error . "<br>";
        } else {
            echo "Connection OK.<br>";
        }
    } else {
        echo "Connection object MISSING.<br>";
    }

    $sql = "SELECT id, nome FROM users LIMIT 1";
    $res = $conn->query($sql);
    if ($res) {
        echo "Query Users OK. Rows: " . $res->num_rows . "<br>";
    } else {
        echo "Query Users FAILED: " . $conn->error . "<br>";
    }

    $sqlEmp = "SELECT id, nome FROM empresas LIMIT 1";
    $resEmp = $conn->query($sqlEmp);
    if ($resEmp) {
        echo "Query Empresas OK. Rows: " . $resEmp->num_rows . "<br>";
    } else {
        echo "Query Empresas FAILED: " . $conn->error . "<br>";
    }

    if (class_exists('ApiResponse')) {
        echo "Class ApiResponse exists.<br>";
    } else {
        echo "Class ApiResponse MISSING.<br>";
    }

} catch (Throwable $e) {
    echo "EXCEPTION: " . $e->getMessage() . " in " . $e->getFile() . ":" . $e->getLine();
}
?>