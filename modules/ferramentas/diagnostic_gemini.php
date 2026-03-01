<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../../includes/header.php';

echo "<div class='container-fluid p-4'>";
echo "<h3>Diagnóstico Ferramentas</h3>";

if (isset($conn)) {
    echo "<div class='alert alert-success'>\$conn está definido.</div>";

    // Check if table exists
    $checkTable = $conn->query("SHOW TABLES LIKE 'ferramentas_tipos'");
    if ($checkTable && $checkTable->num_rows > 0) {
        echo "<div class='alert alert-success'>Tabela 'ferramentas_tipos' existe.</div>";

        // Execute query
        $sql = "SELECT * FROM ferramentas_tipos WHERE ativo = 1 ORDER BY ordem ASC";
        $result = $conn->query($sql);

        if ($result) {
            echo "<div class='alert alert-success'>Query executada com sucesso. Linhas retornadas: " . $result->num_rows . "</div>";
            while ($row = $result->fetch_assoc()) {
                echo "<pre>" . print_r($row, true) . "</pre>";
            }
        } else {
            echo "<div class='alert alert-danger'>Erro na query: " . $conn->error . "</div>";
        }
    } else {
        echo "<div class='alert alert-danger'>Tabela 'ferramentas_tipos' NÃO existe!</div>";
    }

} else {
    echo "<div class='alert alert-danger'>\$conn NÃO está definido!</div>";
}

echo "</div>";
require_once __DIR__ . '/../../includes/footer.php';
?>