<?php
// Roteador Central (Front Controller)
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/includes/auth.php'; // Adicionado para carregar BD e Sessões de Autenticação

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$url = isset($_GET['url']) ? rtrim($_GET['url'], '/') : '';

// 1. Redirecionamento Padrão (Raiz)
if (empty($url) || $url === 'index.php') {
    if (!isLoggedIn()) {
        redirect('login');
    }
    if (isset($_SESSION['tipo']) && $_SESSION['tipo'] === 'superadmin') {
        redirect('admin/dashboard');
    } else {
        redirect('dashboard');
    }
}

// Se não está logado, bloqueia o acesso via roteador
if (!isLoggedIn()) {
    redirect('login');
}

$segments = explode('/', $url);
$moduleConfig = $segments[0] ?? '';

// 2. Proteção Global por Módulo
if ($moduleConfig === 'atendimento') {
    if (!hasPermission(['admin', 'cliente'])) {
        redirect('dashboard');
    }
    if (!isModuleEnabled('atendimento')) {
        $_SESSION['error_message'] = "Você não tem permissão para acessar este módulo.";
        redirect('dashboard');
    }
}

// 3. Mapeamento de Rotas
$filePath = '';
$isAction = false; // Define se a rota é silenciosa (sem Header/Footer)

if ($url === 'atendimento/agentes') {
    $filePath = __DIR__ . '/modules/atendimento/agentes/index.php';
} elseif ($url === 'atendimento/agentes/acoes') {
    $filePath = __DIR__ . '/modules/atendimento/agentes/acoes.php';
    $isAction = true;
} elseif ($url === 'atendimento/agentes/chats') {
    $filePath = __DIR__ . '/modules/atendimento/agentes/chats.php';
} elseif ($url === 'atendimento/agentes/conversas') {
    $filePath = __DIR__ . '/modules/atendimento/agentes/conversas.php';
    $isAction = true; // Retorna views parciais via AJAX
} elseif (preg_match('/^atendimento\/agentes\/editor\/([0-9]+)$/', $url, $matches)) {
    $_GET['id'] = $matches[1];
    $filePath = __DIR__ . '/modules/atendimento/agentes/editor_prompt.php';
}

// 4. Execução da Rota
if ($filePath && file_exists($filePath)) {
    if (!$isAction) {
        require_once __DIR__ . '/includes/header.php';
    }

    // O arquivo específico agora só contém a lógica HTML/PHP dele
    require $filePath;

    if (!$isAction) {
        require_once __DIR__ . '/includes/footer.php';
    }
} else {
    // 404
    http_response_code(404);
    echo "<h2>404 - Página não encontrada (" . htmlspecialchars($url) . ")</h2>";
}