<?php
/**
 * API CRM - Gerenciamento de Negócios (Deals)
 * 
 * Endpoints:
 * GET  /api/crm/negocios.php       -> Lista negócios (filtros: status, funil_id, limit)
 * GET  /api/crm/negocios.php?id=X  -> Detalhes de um negócio
 * POST /api/crm/negocios.php       -> Cria novo negócio (+ paciente opcional)
 * PUT  /api/crm/negocios.php?id=X  -> Atualiza negócio (status, etapa, valores)
 */

// Headers CORS e JSON
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST, PUT, OPTIONS");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Handle OPTIONS request for CORS preflight
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/database.php';

// Helper function for JSON response
function response($data, $code = 200)
{
    http_response_code($code);
    echo json_encode($data);
    exit;
}

$method = $_SERVER['REQUEST_METHOD'];
$id = isset($_GET['id']) ? (int) $_GET['id'] : null;

// ==========================================
// GET - Listar ou Detalhar
// ==========================================
if ($method === 'GET') {
    if ($id) {
        // Buscar um negócio específico
        $stmt = $conn->prepare("
            SELECT n.*, p.nome as paciente_nome, p.telefone as paciente_telefone, f.nome as funil_nome, e.nome as etapa_nome
            FROM crm_negocios n
            LEFT JOIN pacientes p ON n.paciente_id = p.id
            LEFT JOIN crm_funis f ON n.funil_id = f.id
            LEFT JOIN crm_etapas e ON n.etapa_id = e.id
            WHERE n.id = ?
        ");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $res = $stmt->get_result();
        if ($row = $res->fetch_assoc())
            response(['success' => true, 'data' => $row]);
        else
            response(['success' => false, 'error' => 'Negócio não encontrado'], 404);

    } else {
        // Listar negócios
        $limit = isset($_GET['limit']) ? (int) $_GET['limit'] : 50;
        $status = isset($_GET['status']) ? $_GET['status'] : null; // aberto, ganho, perdido
        $funil_id = isset($_GET['funil_id']) ? (int) $_GET['funil_id'] : null;

        $sql = "SELECT n.id, n.titulo, n.valor_estimado, n.status, n.origem, n.created_at, 
                       p.nome as paciente_nome 
                FROM crm_negocios n
                LEFT JOIN pacientes p ON n.paciente_id = p.id
                WHERE 1=1";

        $params = [];
        $types = "";

        if ($status) {
            $sql .= " AND n.status = ?";
            $params[] = $status;
            $types .= "s";
        }
        if ($funil_id) {
            $sql .= " AND n.funil_id = ?";
            $params[] = $funil_id;
            $types .= "i";
        }

        $sql .= " ORDER BY n.id DESC LIMIT ?";
        $params[] = $limit;
        $types .= "i";

        $stmt = $conn->prepare($sql);
        if (!empty($params))
            $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $result = $stmt->get_result();

        $deals = [];
        while ($row = $result->fetch_assoc())
            $deals[] = $row;

        response(['success' => true, 'count' => count($deals), 'data' => $deals]);
    }
}

// ==========================================
// POST - Criar Negócio
// ==========================================
if ($method === 'POST') {
    $input = json_decode(file_get_contents("php://input"), true);

    if (empty($input['titulo'])) {
        response(['success' => false, 'error' => 'Campo titulo é obrigatório'], 400);
    }

    $titulo = $input['titulo'];
    $valor = isset($input['valor']) ? (float) $input['valor'] : 0.00;
    $origem = isset($input['origem']) ? $input['origem'] : 'API';
    $funil_id = isset($input['funil_id']) ? (int) $input['funil_id'] : 1; // Default pipeline

    // Etapa: se não informada, pega a primeira do funil
    $etapa_id = isset($input['etapa_id']) ? (int) $input['etapa_id'] : null;
    if (!$etapa_id) {
        $resEtapa = $conn->query("SELECT id FROM crm_etapas WHERE funil_id = $funil_id ORDER BY ordem ASC LIMIT 1");
        if ($rowEtapa = $resEtapa->fetch_assoc())
            $etapa_id = $rowEtapa['id'];
        else
            response(['success' => false, 'error' => 'Funil inválido ou sem etapas'], 400);
    }

    // Paciente Logic (Busca ou Cria)
    $paciente_id = isset($input['paciente_id']) ? (int) $input['paciente_id'] : null;

    if (!$paciente_id && !empty($input['cliente'])) {
        $cli = $input['cliente'];
        $nome = $cli['nome'] ?? '';
        $telefone = $cli['telefone'] ?? '';

        if ($nome) {
            // Tenta achar por telefone
            if ($telefone) {
                $stmtP = $conn->prepare("SELECT id FROM pacientes WHERE telefone LIKE ? LIMIT 1");
                $likeTel = "%" . substr(preg_replace('/[^0-9]/', '', $telefone), -8); // Match last 8 digits
                $stmtP->bind_param("s", $likeTel);
                $stmtP->execute();
                $resP = $stmtP->get_result();
                if ($rowP = $resP->fetch_assoc()) {
                    $paciente_id = $rowP['id'];
                }
            }

            // Se ainda null, cria
            if (!$paciente_id) {
                $stmtNew = $conn->prepare("INSERT INTO pacientes (nome, telefone, created_at) VALUES (?, ?, NOW())");
                $stmtNew->bind_param("ss", $nome, $telefone);
                if ($stmtNew->execute()) {
                    $paciente_id = $conn->insert_id;
                }
            }
        }
    }

    // Insert Deal
    $stmt = $conn->prepare("INSERT INTO crm_negocios (titulo, valor_estimado, paciente_id, funil_id, etapa_id, origem, status, created_at) VALUES (?, ?, ?, ?, ?, ?, 'aberto', NOW())");
    $stmt->bind_param("sddiss", $titulo, $valor, $paciente_id, $funil_id, $etapa_id, $origem);

    if ($stmt->execute()) {
        response(['success' => true, 'id' => $conn->insert_id, 'message' => 'Negócio criado com sucesso'], 201);
    } else {
        response(['success' => false, 'error' => $conn->error], 500);
    }
}

// ==========================================
// PUT - Atualizar Negócio
// ==========================================
if ($method === 'PUT') {
    if (!$id)
        response(['success' => false, 'error' => 'ID obrigatório na URL'], 400);

    $input = json_decode(file_get_contents("php://input"), true);

    // Construct dynamic update query
    $fields = [];
    $types = "";
    $params = [];

    if (isset($input['titulo'])) {
        $fields[] = "titulo=?";
        $params[] = $input['titulo'];
        $types .= "s";
    }
    if (isset($input['valor'])) {
        $fields[] = "valor_estimado=?";
        $params[] = (float) $input['valor'];
        $types .= "d";
    }
    if (isset($input['status'])) {
        $fields[] = "status=?";
        $params[] = $input['status'];
        $types .= "s";
    }
    if (isset($input['etapa_id'])) {
        $fields[] = "etapa_id=?";
        $params[] = (int) $input['etapa_id'];
        $types .= "i";
    }
    if (isset($input['motivo_perda'])) {
        $fields[] = "motivo_perda=?";
        $params[] = $input['motivo_perda'];
        $types .= "s";
    }

    if (empty($fields))
        response(['success' => false, 'error' => 'Nenhum dado para atualizar'], 400);

    $params[] = $id;
    $types .= "i";

    $sql = "UPDATE crm_negocios SET " . implode(", ", $fields) . ", updated_at=NOW() WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param($types, ...$params);

    if ($stmt->execute())
        response(['success' => true, 'message' => 'Atualizado com sucesso']);
    else
        response(['success' => false, 'error' => $conn->error], 500);
}

response(['success' => false, 'error' => 'Método não suportado'], 405);
