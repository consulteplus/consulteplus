<?php
// Configurar sessão antes de iniciar
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_secure', 0);
session_start();
session_destroy();
header('Location: login.php');
exit;
?>