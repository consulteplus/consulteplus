<?php
require_once __DIR__ . '/../../../config/config.php';
require_once __DIR__ . '/../../../config/database.php';
require_once __DIR__ . '/../../../includes/auth.php';

// Configurações de erro JSON-safe
ini_set('display_errors', 0);
error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);
header('Content-Type: application/json');

try {

    // Check Permissions
// (Adicionar verificação de sessão aqui se necessário)

    $acao = isset($_GET['acao']) ? $_GET['acao'] : '';
    $method = $_SERVER['REQUEST_METHOD'];

    $company_id = getCompanyId(); // Helper function or session
    $isAdmin = isset($_SESSION['tipo']) && in_array($_SESSION['tipo'], ['admin', 'superadmin']);

    if ($isAdmin) {
        // Para admins, company_id pode ser NULL (Lead Global) ou vira de um select input (futuro)
        // Por enquanto, vamos forçar NULL para evitar o erro de FK com ID 1
        $company_id = null;
    }

    if ($acao === 'listar' && $method === 'GET') {
        // Listar Leads
        $sql = "SELECT l.id, l.nome, l.email, l.telefone, l.origem, l.status as lead_status, l.created_at, e.nome as empresa_nome 
            FROM leads l 
            LEFT JOIN empresas e ON l.company_id = e.id 
            ORDER BY l.created_at DESC LIMIT 100";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $result = $stmt->get_result();

        $leads = [];
        while ($row = $result->fetch_assoc()) {
            $leads[] = $row;
        }

        // Stats via SQL for accuracy
        $total = $conn->query("SELECT COUNT(*) as c FROM leads")->fetch_assoc()['c'];
        $novos = $conn->query("SELECT COUNT(*) as c FROM leads WHERE status = 'novo'")->fetch_assoc()['c'];
        $qualificados = $conn->query("SELECT COUNT(*) as c FROM leads WHERE status = 'qualificado'")->fetch_assoc()['c'];

        $stats = [
            'total' => $total,
            'novos' => $novos,
            'qualificados' => $qualificados
        ];

        ApiResponse::success(['leads' => $leads, 'stats' => $stats]);
    }

    if ($acao === 'buscar' && $method === 'GET') {
        // Buscar Lead Detalhado
        $id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

        if ($id === 0) {
            ApiResponse::error('ID do lead é obrigatório', 400);
        }

        $sql = "SELECT l.*, e.nome as empresa_nome 
            FROM leads l 
            LEFT JOIN empresas e ON l.company_id = e.id 
            WHERE l.id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($lead = $result->fetch_assoc()) {
            ApiResponse::success(['lead' => $lead]);
        } else {
            ApiResponse::error('Lead não encontrado', 404);
        }
    }

    if ($acao === 'salvar' && $method === 'POST') {
        $data = json_decode(file_get_contents('php://input'), true);

        $id = isset($data['leadId']) ? (int) $data['leadId'] : 0;
        // Fallback ID key check
        if ($id === 0 && isset($data['id']))
            $id = (int) $data['id'];

        $nome = $data['nome'] ?? '';
        $email = $data['email'] ?? null;
        $telefone = $data['telefone'] ?? '';
        $origem = $data['origem'] ?? 'Manual';
        $status = $data['lead_status'] ?? 'novo'; // JS sends lead_status

        if (empty($email))
            $email = null;

        if (empty($nome)) {
            ApiResponse::error('Nome é obrigatório');
        }

        if ($id > 0) {
            // Editar
            $sql = "UPDATE leads SET nome=?, email=?, telefone=?, origem=?, status=? WHERE id=?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sssssi", $nome, $email, $telefone, $origem, $status, $id);
        } else {
            // Novo
            $sql = "INSERT INTO leads (company_id, nome, email, telefone, origem, status, ativo, created_at) VALUES (?, ?, ?, ?, ?, ?, 1, NOW())";
            $stmt = $conn->prepare($sql);
            // If company_id can be null, 'i' type might convert null to 0.
            // If the column is nullable and null is desired, 's' type might be better,
            // or handle it with a separate query part for null.
            // For now, keeping 'i' as per original code's type for company_id.
            $stmt->bind_param("isssss", $company_id, $nome, $email, $telefone, $origem, $status);
        }

        if ($stmt->execute()) {
            ApiResponse::success([], ($id > 0 ? 'Lead atualizado' : 'Lead criado'));
        } else {
            ApiResponse::error($conn->error, 500);
        }
    }

    if ($acao === 'excluir' && $method === 'POST') {
        $data = json_decode(file_get_contents('php://input'), true);
        $id = (int) $data['id'];

        $sql = "DELETE FROM leads WHERE id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);

        if ($stmt->execute()) {
            ApiResponse::success([], 'Lead excluído');
        } else {
            ApiResponse::error('Erro ao excluir', 500);
        }
    }

    ApiResponse::error('Ação inválida', 400);

} catch (Exception $e) {
    ApiResponse::error($e->getMessage(), 500);
} catch (Error $e) {
    ApiResponse::error('Erro fatal: ' . $e->getMessage(), 500);
}
?>