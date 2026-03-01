<?php
// Configurações do Banco de Dados
// Detecção de Ambiente (Local vs Produção)
$isLocal = ($_SERVER['SERVER_NAME'] === 'localhost' || $_SERVER['REMOTE_ADDR'] === '127.0.0.1' || $_SERVER['REMOTE_ADDR'] === '::1');

if ($isLocal) {
    // AMBIENTE LOCAL (XAMPP/WAMP)
    define('DB_HOST', 'localhost');
    define('DB_USER', 'root');
    define('DB_PASS', '');
    define('DB_NAME', 'consulte'); // Updated per user request
} else {
    // AMBIENTE PRODUÇÃO (HOSTINGER)
    define('DB_HOST', 'localhost');
    define('DB_USER', 'u635775069_consulte');
    define('DB_PASS', 'Gremio271293@');
    define('DB_NAME', 'u635775069_consulte');
}

// Criar conexão
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Verificar conexão
if ($conn->connect_error) {
    die("Erro de conexão: " . $conn->connect_error);
}

// Definir charset
$conn->set_charset("utf8mb4");

// Definir timezone do MySQL para America/Sao_Paulo (-03:00)
// Necessário porque o MySQL pode estar em UTC
$conn->query("SET time_zone = '-03:00'");