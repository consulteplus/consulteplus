<?php
// modules/deploy/save_config.php
// Arquivo isolado para salvar configurações e evitar conflitos de renderização

session_start();

echo "<pre><h1>Debug de Salvamento Ativo</h1>";

// 1. Receber Dados
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die("ERRO: Este arquivo deve ser acessado via POST (Formulário).");
}

echo "Dados recebidos via POST.<br>";
require_once __DIR__ . '/../../../config/config.php';

// 2. Definir arquivo
$ftpConfigFile = __DIR__ . '/../../../config/ftp_deploy.json';
$targetDir = dirname($ftpConfigFile);

echo "Alvo: $ftpConfigFile<br>";

// 3. Montar Payload
$newConfig = [
    'host' => trim($_POST['ftp_host']),
    'user' => trim($_POST['ftp_user']),
    'port' => (int) ($_POST['ftp_port'] ?? 21),
    'path' => trim($_POST['ftp_path']),
    'exclude' => trim($_POST['ftp_exclude'])
];

// 4. Manter Senha Antiga (se vazia)
$oldConfig = file_exists($ftpConfigFile) ? json_decode(file_get_contents($ftpConfigFile), true) : [];
if (!empty($_POST['ftp_pass'])) {
    $newConfig['pass'] = $_POST['ftp_pass'];
} else {
    $newConfig['pass'] = $oldConfig['pass'] ?? '';
}

// 5. Tentar Salvar
$json = json_encode($newConfig, JSON_PRETTY_PRINT);
$bytes = file_put_contents($ftpConfigFile, $json);

if ($bytes === false) {
    echo "<h2 style='color:red'>FALHA AO SALVAR</h2>";
    echo "Permissões da pasta config: " . substr(sprintf('%o', fileperms($targetDir)), -4) . "<br>";
    echo "Erro: ";
    print_r(error_get_last());
    echo "<br><a href='index.php?tab=ftp'>Voltar e tentar novamente</a>";
    die();
}

echo "<h2 style='color:green'>SALVO COM SUCESSO! ($bytes bytes)</h2>";
echo "Redirecionando em 3 segundos...";

// Feedback para Sessão
$_SESSION['success'] = "Credenciais FTP atualizadas via Modo Seguro!";

// 6. Redirect
header("refresh:2;url=index.php?tab=ftp");
?>