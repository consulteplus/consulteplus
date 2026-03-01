<?php
// modules/deploy/migrate.php
session_start();
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/database.php';

// Apenas Admin
if (!isset($_SESSION['user_id']) || $_SESSION['tipo'] !== 'admin') {
    die("Acesso Negado.");
}

$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($conn->connect_error)
    die("Erro DB: " . $conn->connect_error);

echo "<h3>Gerenciador de Migrações de Banco de Dados</h3>";

// 1. Garantir tabela de controle de migrations
$conn->query("CREATE TABLE IF NOT EXISTS system_migrations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    filename VARCHAR(255) NOT NULL UNIQUE,
    executed_at DATETIME DEFAULT CURRENT_TIMESTAMP
)");

// 2. Listar arquivos locais
$migrationDir = __DIR__ . '/../../database/migrations/';
$files = glob($migrationDir . '*.sql');
sort($files); // Ordem alfabética é crucial (v1, v2, v3...)

$executedCount = 0;

foreach ($files as $file) {
    $filename = basename($file);

    // Verificar se já rodou
    $check = $conn->query("SELECT id FROM system_migrations WHERE filename = '$filename'");
    if ($check && $check->num_rows > 0) {
        // Já rodou, pular
        continue;
    }

    echo "Executando: <strong>$filename</strong>... ";

    // Ler e Executar
    $sqlContent = file_get_contents($file);

    // O MySQL PHP driver não suporta múltiplos comandos em uma só query() facilmente
    // Vamos usar multi_query
    if ($conn->multi_query($sqlContent)) {
        do {
            // Consumir resultados para liberar buffer
            if ($res = $conn->store_result())
                $res->free();
        } while ($conn->more_results() && $conn->next_result());

        if ($conn->errno) {
            echo "<span style='color:red'>ERRO SQL: " . $conn->error . "</span><br>";
            break; // Para tudo se der erro
        } else {
            // Registrar sucesso
            $conn->query("INSERT INTO system_migrations (filename) VALUES ('$filename')");
            echo "<span style='color:green'>SUCESSO!</span><br>";
            $executedCount++;
        }
    } else {
        echo "<span style='color:red'>ERRO GERAL: " . $conn->error . "</span><br>";
        break;
    }
}

if ($executedCount === 0) {
    echo "<p>Nenhuma migração nova encontrada. O banco está atualizado.</p>";
} else {
    echo "<p><strong>$executedCount</strong> migrações aplicadas com sucesso.</p>";
}

echo '<br><a href="index.php">Voltar</a>';
?>