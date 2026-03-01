<?php
// Configurações de sessão (antes de iniciar)
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.cookie_httponly', 1);
    ini_set('session.use_only_cookies', 1);
    ini_set('session.cookie_secure', 0); // Mudar para 1 se usar HTTPS
    session_start();
}

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/config.php';

/**
 * Verificar se usuário está autenticado
 */
function checkAuth()
{
    if (!isset($_SESSION['user_id'])) {
        redirect('login');
    }
    checkModuleBoundary();
}

/**
 * Verificar fronteiras de módulos (Admin vs Cliente)
 */
function checkModuleBoundary()
{
    // Se não estiver logado, não aplica (checkAuth já redireciona)
    if (!isset($_SESSION['tipo']))
        return;

    // Normalizar URI
    $uri = str_replace('\\', '/', $_SERVER['REQUEST_URI']);
    $role = $_SESSION['tipo'];
    $isAdmin = in_array($role, ['admin', 'superadmin']);

    // 1. Bloquear Cliente na área Admin
    if (strpos($uri, '/modules/admin/') !== false) {
        if (!$isAdmin) {
            $_SESSION['error'] = 'Acesso não autorizado à área administrativa.';
            redirect('dashboard');
        }
    }

    // 2. Bloquear Admin na área Cliente (Conforme solicitado: "vice versa")
    // Regra: Está em /modules/ mas NÃO está em /modules/admin/
    // Exceção: Scripts de API ou simulação se houver (mas a regra foi estrita)
    if (strpos($uri, '/modules/') !== false && strpos($uri, '/modules/admin/') === false) {
        if ($isAdmin) {
            // Permitir 'simular' caso seja uma funcionalidade existente
            // if (strpos($uri, 'simular') !== false) return;

            $_SESSION['warning'] = 'Administradores devem usar a área administrativa.';
            redirect('admin/dashboard');
        }
    }
}

/**
 * Verificar permissão de acesso
 */
function checkPermission($allowedTypes = [])
{
    checkAuth();

    // Superadmin bypass
    if ($_SESSION['tipo'] === 'superadmin') {
        return;
    }

    if (!empty($allowedTypes) && !in_array($_SESSION['tipo'], $allowedTypes)) {
        $_SESSION['error'] = 'Você não tem permissão para acessar esta página.';
        redirect('dashboard');
    }
}

/**
 * Fazer login
 */
function login($email, $password)
{
    global $conn;

    $stmt = $conn->prepare("SELECT id, nome, email, senha, tipo, company_id FROM users WHERE email = ? AND ativo = 1");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();

        if (verifyPassword($password, $user['senha'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['nome'] = $user['nome'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['tipo'] = $user['tipo'];
            $_SESSION['company_id'] = $user['company_id']; // Multi-tenancy support
            return true;
        }
    }

    return false;
}

/**
 * Fazer logout
 */
function logout()
{
    session_destroy();
    redirect('login');
}

/**
 * Obter dados do usuário logado
 */
function getCurrentUser()
{
    if (!isLoggedIn())
        return null;

    return [
        'id' => $_SESSION['user_id'],
        'nome' => $_SESSION['nome'],
        'email' => $_SESSION['email'],
        'tipo' => $_SESSION['tipo'],
        'company_id' => $_SESSION['company_id'] ?? 1 // Fallback for legacy sessions
    ];
}

/**
 * Obter ID da empresa do usuário logado (Multi-tenancy)
 */
function getCompanyId()
{
    return $_SESSION['company_id'] ?? 9; // Fallback to 9 for dev env (since 1 doesn't exist)
}