<?php
// modules/deploy/execute_db.php
// Executor isolado de migrações de banco de dados

session_start();
require_once __DIR__ . '/../../../config/config.php';
require_once __DIR__ . '/../../../config/database.php';

echo "<pre><h1>Executor de Banco de Dados</h1>";

// 1. Receber Dados
echo "Método da Requisição: " . $_SERVER['REQUEST_METHOD'] . "<br>";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die("ERRO: Acesso via GET detectado. O formulário deve enviar via POST.");
}

// Debug do que chegou
echo "Dados POST recebidos: <pre>" . print_r($_POST, true) . "</pre>";

// Validação simplificada (Apenas checa se tem JSON)
if (!isset($_POST['pending_json'])) {
    die("ERRO: Variável pending_json não encontrada no POST.");
}

// 2. Decode Payload
$pendingList = json_decode($_POST['pending_json'], true);

if (empty($pendingList)) {
    die("ERRO: Lista de migrações veio vazia.");
}

echo "Total de arquivos para rodar: " . count($pendingList) . "<br>";

// 3. Conexão PDO Dedicada (Debug ON)
try {
    $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";
    $pdo = new PDO($dsn, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "Conexão com Banco: OK<br>";
} catch (PDOException $e) {
    die("<h2 style='color:red'>ERRO CONEXÃO: " . $e->getMessage() . "</h2>");
}

// 4. Executar Loop
$count = 0;
$errors = [];

foreach ($pendingList as $v => $m) {
    echo "<hr><strong>Processando v{$v} ({$m['filename']})...</strong><br>";

    if (!file_exists($m['path'])) {
        echo "<span style='color:red'>ERRO: Arquivo não encontrado no disco: {$m['path']}</span><br>";
        $errors[] = "Arquivo sumiu: {$m['filename']}";
        continue;
    }

    $sqlContent = file_get_contents($m['path']);

    try {
        // Executa SQL
        $pdo->exec($sqlContent);
        echo "<span style='color:green'>SQL Executado com Sucesso.</span><br>";

        // Registra na tabela
        $stmt = $pdo->prepare("INSERT INTO sys_migrations (version, filename, status, log) VALUES (?, ?, 'success', 'Deploy via Painel')");
        $stmt->execute([$v, $m['filename']]);
        echo "Tabela de registro atualizada.<br>";

        $count++;

    } catch (PDOException $e) {
        $msg = $e->getMessage();
        echo "<h3 style='color:red'>FALHA NA MIGRAÇÃO v{$v}:</h3>";
        echo "<div style='background:#fdd; padding:10px; border:1px solid red'>$msg</div>";
        echo "<strong>SQL Tentado:</strong><br>" . substr($sqlContent, 0, 200) . "...<br>";

        // Para tudo se der erro pra não quebrar integridade
        die("<br><br><a href='index.php?tab=db' style='font-size:20px'>VOLTAR E CORRIGIR</a>");
    }
}

echo "<hr><h2 style='color:green'>SUCESSO TOTAL! $count migrações aplicadas.</h2>";
echo "Redirecionando...";

$_SESSION['success'] = "Banco de Dados atualizado com sucesso ($count arquivos)!";

header("refresh:2;url=index.php?tab=db");
?>