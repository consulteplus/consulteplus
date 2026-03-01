<?php
// Configurar sessão antes de iniciar
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_secure', 0);
session_start();

// Se não estiver logado, redirecionar para login
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Redirecionar todos para o painel administrativo
header('Location: modules/admin/dashboard/index.php');
exit;
?>