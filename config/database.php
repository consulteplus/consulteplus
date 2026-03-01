<?php
// Carregar variáveis de ambiente
require_once __DIR__ . '/env.php';

// Detecção de Ambiente (Local vs Produção)
$isLocal = (
    $_SERVER['SERVER_NAME'] === 'localhost' ||
    $_SERVER['REMOTE_ADDR'] === '127.0.0.1' ||
    $_SERVER['REMOTE_ADDR'] === '::1' ||
    env('APP_ENV') === 'local'
);

if ($isLocal) {
    // AMBIENTE LOCAL — credenciais via .env
    define('DB_HOST', env('DB_HOST', 'localhost'));
    define('DB_USER', env('DB_USER', 'root'));
    define('DB_PASS', env('DB_PASS', ''));
    define('DB_NAME', env('DB_NAME', 'consulte'));
} else {
    // AMBIENTE PRODUÇÃO — credenciais via .env (PROD_*)
    define('DB_HOST', env('PROD_DB_HOST', 'localhost'));
    define('DB_USER', env('PROD_DB_USER', ''));
    define('DB_PASS', env('PROD_DB_PASS', ''));
    define('DB_NAME', env('PROD_DB_NAME', ''));
}

// Criar conexão
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Verificar conexão
if ($conn->connect_error) {
    $msg = env('APP_ENV') === 'local'
        ? "Erro de conexão: " . $conn->connect_error
        : "Erro de conexão com o banco de dados.";
    die($msg);
}

// Definir charset
$conn->set_charset("utf8mb4");

// Definir timezone do MySQL para America/Sao_Paulo (-03:00)
$conn->query("SET time_zone = '-03:00'");