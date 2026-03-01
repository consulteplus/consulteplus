<?php
// Disable error handling output to avoid breaking JSON
ini_set('display_errors', 0);
error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED);

require_once __DIR__ . '/../../../config/config.php';
require_once __DIR__ . '/../../../config/database.php';
require_once __DIR__ . '/../../../includes/auth.php';

header('Content-Type: application/json');

// Apenas SuperAdmin
if (!isset($_SESSION['tipo']) || $_SESSION['tipo'] !== 'superadmin') {
    ApiResponse::error('Acesso negado', 403);
}

$acao = isset($_GET['acao']) ? $_GET['acao'] : '';
$method = $_SERVER['REQUEST_METHOD'];

// LISTAR
if ($acao === 'listar' && $method === 'GET') {
    // Check if created_at exists, just in case (optional, but good practice is to error handle)
    $sql = "SELECT u.id, u.nome, u.email, u.telefone, u.tipo, u.ativo, u.company_id, u.created_at, e.nome as empresa_nome 
            FROM users u 
            LEFT JOIN empresas e ON u.company_id = e.id 
            WHERE u.tipo != 'lead'
            ORDER BY u.created_at DESC";

    $result = $conn->query($sql);

    if (!$result) {
        ApiResponse::error("Erro ao listar usuários: " . $conn->error);
    }

    $users = [];
    while ($row = $result->fetch_assoc()) {
        $users[] = $row;
    }
    ApiResponse::success($users);
}

// SALVAR (Criar ou Editar)
if ($acao === 'salvar' && $method === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    $id = isset($data['id']) ? (int) $data['id'] : 0;
    $nome = $data['nome'] ?? '';
    $email = $data['email'] ?? '';
    $telefone = $data['telefone'] ?? '';
    $tipo = $data['tipo'] ?? 'admin';
    $company_id = isset($data['company_id']) ? (int) $data['company_id'] : 1;
    $senha = $data['senha'] ?? '';

    // Validação básica
    if (empty($nome) || empty($email)) {
        ApiResponse::error('Nome e Email são obrigatórios');
    }

    if ($id > 0) {
        // EDITAR
        // Se senha foi enviada, atualiza. Se não, mantém.
        if (!empty($senha)) {
            $senhaHash = password_hash($senha, PASSWORD_DEFAULT);
            $sql = "UPDATE users SET nome=?, email=?, telefone=?, tipo=?, company_id=?, senha=? WHERE id=?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssssisi", $nome, $email, $telefone, $tipo, $company_id, $senhaHash, $id);
        } else {
            $sql = "UPDATE users SET nome=?, email=?, telefone=?, tipo=?, company_id=? WHERE id=?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssssii", $nome, $email, $telefone, $tipo, $company_id, $id);
        }
    } else {
        // NOVO
        if (empty($senha)) {
            ApiResponse::error('Senha é obrigatória para novos usuários');
        }
        $senhaHash = password_hash($senha, PASSWORD_DEFAULT);
        $sql = "INSERT INTO users (nome, email, telefone, tipo, company_id, senha, ativo, created_at) VALUES (?, ?, ?, ?, ?, ?, 1, NOW())";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssis", $nome, $email, $telefone, $tipo, $company_id, $senhaHash);
    }

    if ($stmt->execute()) {
        ApiResponse::success([], ($id > 0 ? 'Usuário atualizado' : 'Usuário criado'));
    } else {
        // Verificar erro de duplicidade (Email)
        if ($conn->errno === 1062) {
            ApiResponse::error('Este email já está cadastrado.');
        } else {
            ApiResponse::error($conn->error, 500);
        }
    }
}

// EXCLUIR
if ($acao === 'excluir' && $method === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $id = isset($data['id']) ? (int) $data['id'] : 0;

    // Evitar excluir a si mesmo
    if ($id == $_SESSION['user_id']) {
        ApiResponse::error('Você não pode excluir a si mesmo.');
    }

    $sql = "DELETE FROM users WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        ApiResponse::success([], 'Usuário excluído');
    } else {
        ApiResponse::error($conn->error, 500);
    }
}

// DROPDOWN EMPRESAS (Para o Modal)
if ($acao === 'empresas' && $method === 'GET') {
    $result = $conn->query("SELECT id, nome FROM empresas WHERE ativo = 1 ORDER BY nome");

    if (!$result) {
        ApiResponse::error("Erro ao buscar empresas: " . $conn->error);
    }

    $empresas = [];
    while ($row = $result->fetch_assoc()) {
        $empresas[] = $row;
    }
    ApiResponse::success($empresas);
}

ApiResponse::error('Ação inválida', 400);
?>