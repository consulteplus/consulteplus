<?php
// Carregar variáveis de ambiente (deve ser o primeiro include)
require_once __DIR__ . '/env.php';

// Incluir Logger
require_once __DIR__ . '/../classes/Logger.php';
// Incluir ApiResponse
require_once __DIR__ . '/../classes/ApiResponse.php';

// Configurações Gerais
define('SITE_NAME', 'Gestão Empresarial');

// Configurações Globais
// DEBUG_MODE é true em local, false em produção automaticamente
define('DEBUG_MODE', env('APP_ENV', 'production') === 'local');
define('LOG_PATH', __DIR__ . '/../logs/app.log');

// Configurações do Supabase — via variáveis de ambiente
define('SUPABASE_URL', env('SUPABASE_URL', ''));
define('SUPABASE_KEY', env('SUPABASE_KEY', ''));


// Detecção de Ambiente
// Detecção de Ambiente Melhorada
$whitelist = ['127.0.0.1', '::1'];
$isLocal = in_array($_SERVER['REMOTE_ADDR'], $whitelist) ||
    $_SERVER['SERVER_NAME'] === 'localhost' ||
    strpos($_SERVER['SERVER_NAME'], '192.168.') === 0 ||
    strpos($_SERVER['SERVER_NAME'], '10.') === 0 ||
    strpos($_SERVER['SERVER_NAME'], '172.') === 0 ||
    substr($_SERVER['SERVER_NAME'], -6) === '.local' ||
    substr($_SERVER['SERVER_NAME'], -5) === '.test';

if ($isLocal) {
    // Lógica Automática para Localhost (Mantém a flexibilidade para desenvolvimento)
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'];
    $scriptPath = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));

    // Remove subdiretórios conhecidos para achar a raiz
    $baseDir = preg_replace('#/(modules|includes|api|config|cron|assets)/.*#', '', $scriptPath);
    $baseDir = rtrim($baseDir, '/') . '/';

    if ($baseDir === '//') {
        $baseDir = '/';
    }

    define('BASE_URL', $protocol . '://' . $host . $baseDir);
} else {
    // Lógica Fixa para Produção (Hostinger) - Evita erros de path
    define('BASE_URL', 'https://app.consulteplus.com.br/');
}

define('TIMEZONE', 'America/Sao_Paulo');

// Definir timezone
date_default_timezone_set(TIMEZONE);

// Integração ASAAS (Financeiro) — via variáveis de ambiente
define('ASAAS_API_URL', env('ASAAS_API_URL', 'https://sandbox.asaas.com/api/v3'));
define('ASAAS_API_KEY', env('ASAAS_API_KEY', ''));

// API Externa (Webhooks) — via variáveis de ambiente
define('CRM_API_KEY', env('CRM_API_KEY', ''));

// ============================================================
// Controle de Módulos por Empresa
// ============================================================

/**
 * Módulos sempre ativos — não precisam de registro no banco.
 */
define('MODULES_ALWAYS_ON', ['dashboard', 'perfil', 'configuracoes', 'financeiro']);

/**
 * Verifica se um módulo está habilitado para a empresa logada.
 * Usa cache em $_SESSION para evitar consultas repetidas por página.
 *
 * @param string $modulo   Ex: 'gestao', 'ferramentas', 'produtos'
 * @param int    $company_id  ID da empresa (usa sessão se omitido)
 * @return bool
 */
function isModuleEnabled(string $modulo, int $company_id = 0): bool
{
    // Módulos fixos: sempre ativos
    if (in_array($modulo, MODULES_ALWAYS_ON)) {
        return true;
    }

    // Usar company_id da sessão se não informado
    if ($company_id === 0) {
        $company_id = (int) ($_SESSION['company_id'] ?? 0);
    }

    if ($company_id === 0) {
        return false;
    }

    // Cache na sessão para evitar N queries por página
    $cacheKey = 'modules_' . $company_id;
    if (!isset($_SESSION[$cacheKey])) {
        global $conn;
        if (!isset($conn)) {
            return true; // fallback: permite acesso se DB não disponível
        }
        $stmt = $conn->prepare(
            "SELECT modulo, ativo FROM company_modules WHERE company_id = ?"
        );
        $stmt->bind_param("i", $company_id);
        $stmt->execute();
        $res = $stmt->get_result();

        $cache = [];
        while ($row = $res->fetch_assoc()) {
            $cache[$row['modulo']] = (bool) $row['ativo'];
        }
        $_SESSION[$cacheKey] = $cache;
    }

    // Se não tem registro, considera habilitado (novo cliente = tudo ativo por padrão)
    return $_SESSION[$cacheKey][$modulo] ?? true;
}

/**
 * Invalida o cache de módulos da sessão (chamar após salvar módulos).
 */
function clearModulesCache(int $company_id): void
{
    unset($_SESSION['modules_' . $company_id]);
}


// Funções auxiliares

/**
 * Gerar token seguro para API
 */
function generateApiToken()
{
    return bin2hex(random_bytes(32)); // 64 caracteres
}

/**
 * Validar token de API
 */
function validateApiToken($token)
{
    global $conn;

    $stmt = $conn->prepare("
        SELECT id, tipo, descricao 
        FROM api_tokens 
        WHERE token = ? 
        AND ativo = 1 
        AND (expires_at IS NULL OR expires_at > NOW())
    ");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Atualizar último uso
        $tokenData = $result->fetch_assoc();
        $updateStmt = $conn->prepare("UPDATE api_tokens SET ultimo_uso = NOW() WHERE id = ?");
        $updateStmt->bind_param("i", $tokenData['id']);
        $updateStmt->execute();

        return $tokenData;
    }

    return false;
}

/**
 * Sanitizar string
 */
function sanitize($string)
{
    return htmlspecialchars(strip_tags(trim($string)), ENT_QUOTES, 'UTF-8');
}

/**
 * Formatar data para exibição (YYYY-MM-DD -> DD/MM/YYYY)
 */
function formatDate($date)
{
    if (empty($date))
        return '';
    $d = DateTime::createFromFormat('Y-m-d', $date);
    return $d ? $d->format('d/m/Y') : $date;
}

/**
 * Formatar data e hora para exibição
 */
function formatDateTime($datetime)
{
    if (empty($datetime))
        return '';
    $d = DateTime::createFromFormat('Y-m-d H:i:s', $datetime);
    return $d ? $d->format('d/m/Y H:i') : $datetime;
}

/**
 * Converter data do formato brasileiro para MySQL (DD/MM/YYYY -> YYYY-MM-DD)
 */
function dateToMysql($date)
{
    if (empty($date))
        return null;
    $d = DateTime::createFromFormat('d/m/Y', $date);
    return $d ? $d->format('Y-m-d') : null;
}

/**
 * Gerar hash de senha
 */
function hashPassword($password)
{
    return password_hash($password, PASSWORD_BCRYPT);
}

/**
 * Verificar senha
 */
function verifyPassword($password, $hash)
{
    return password_verify($password, $hash);
}

/**
 * Redirecionar
 */
function redirect($url)
{
    header("Location: " . BASE_URL . $url);
    exit;
}

/**
 * Verificar se usuário está logado
 */
function isLoggedIn()
{
    return isset($_SESSION['user_id']);
}

/**
 * Verificar se usuário tem permissão
 */
function hasPermission($allowedTypes)
{
    if (!isLoggedIn())
        return false;

    // Superadmin tem permissão total
    if (isset($_SESSION['tipo']) && $_SESSION['tipo'] === 'superadmin') {
        return true;
    }

    return in_array($_SESSION['tipo'], $allowedTypes);
}

/**
 * Formatar telefone
 */
function formatPhone($phone)
{
    $phone = preg_replace('/[^0-9]/', '', $phone);
    if (strlen($phone) == 11) {
        return '(' . substr($phone, 0, 2) . ') ' . substr($phone, 2, 5) . '-' . substr($phone, 7);
    } elseif (strlen($phone) == 10) {
        return '(' . substr($phone, 0, 2) . ') ' . substr($phone, 2, 4) . '-' . substr($phone, 6);
    }
    return $phone;
}

/**
 * Formatar CPF
 */
function formatCPF($cpf)
{
    $cpf = preg_replace('/[^0-9]/', '', $cpf);
    if (strlen($cpf) == 11) {
        return substr($cpf, 0, 3) . '.' . substr($cpf, 3, 3) . '.' . substr($cpf, 6, 3) . '-' . substr($cpf, 9, 2);
    }
    return $cpf;
}

/**
 * Obter nome do dia da semana em português
 */
function getDiaSemana($dia)
{
    $dias = [
        'segunda' => 'Segunda-feira',
        'terca' => 'Terça-feira',
        'quarta' => 'Quarta-feira',
        'quinta' => 'Quinta-feira',
        'sexta' => 'Sexta-feira',
        'sabado' => 'Sábado',
        'domingo' => 'Domingo'
    ];
    return $dias[$dia] ?? $dia;
}

/**
 * Obter nome do turno
 */
function getTurno($turno)
{
    $turnos = [
        'manha' => 'Manhã',
        'tarde' => 'Tarde',
        'ambos' => 'Manhã e Tarde'
    ];
    return $turnos[$turno] ?? $turno;
}

/**
 * Obter badge de status de agendamento
 */
function getStatusBadge($status)
{
    $badges = [
        'agendado' => '<span class="badge bg-secondary">Agendado</span>',
        'confirmado' => '<span class="badge bg-primary">Confirmado</span>',
        'em_atendimento' => '<span class="badge bg-info">Em Atendimento</span>',
        'concluido' => '<span class="badge bg-success">Concluído</span>',
        'cancelado' => '<span class="badge bg-danger">Cancelado</span>',
        'faltou' => '<span class="badge bg-warning">Faltou</span>'
    ];
    return $badges[$status] ?? $status;
}