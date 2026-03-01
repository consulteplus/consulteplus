<?php
require_once __DIR__ . '/../../../../config/config.php';
require_once __DIR__ . '/../../../../config/database.php';
require_once __DIR__ . '/../../../../includes/auth.php';

header('Content-Type: application/json');

checkAuth();

$acao = isset($_GET['acao']) ? $_GET['acao'] : '';
$method = $_SERVER['REQUEST_METHOD'];
$company_id = getCompanyId();

// ========== LISTAR AUDIÊNCIAS ==========
if ($acao === 'listar' && $method === 'GET') {
    $sql = "SELECT a.*, u.nome as criador_nome,
            (SELECT COUNT(*) FROM audiencia_leads al WHERE al.audiencia_id = a.id) as total_estatico
            FROM audiencias a
            LEFT JOIN users u ON a.criado_por = u.id
            WHERE a.company_id = ?
            ORDER BY a.criado_em DESC";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $company_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $audiencias = [];
    while ($row = $result->fetch_assoc()) {
        // Para audiências dinâmicas, calcular total em tempo real
        if ($row['tipo'] === 'dinamica') {
            $row['total_leads'] = contarLeadsPorFiltros($conn, $company_id, $row['filtros_json']);
        } else {
            $row['total_leads'] = $row['total_estatico'];
        }
        $audiencias[] = $row;
    }

    ApiResponse::success($audiencias);
}

// ========== SALVAR AUDIÊNCIA ==========
if ($acao === 'salvar' && $method === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    $id = isset($data['id']) ? (int) $data['id'] : 0;
    $nome = $data['nome'] ?? '';
    $descricao = $data['descricao'] ?? '';
    $tipo = $data['tipo'] ?? 'dinamica';
    $filtros_json = isset($data['filtros']) ? json_encode($data['filtros']) : '[]';
    $criado_por = $_SESSION['user_id'] ?? null;

    if (empty($nome)) {
        ApiResponse::error('Nome é obrigatório');
    }

    if ($id > 0) {
        // Editar
        $sql = "UPDATE audiencias SET nome=?, descricao=?, tipo=?, filtros_json=?, atualizado_em=NOW() WHERE id=? AND company_id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssii", $nome, $descricao, $tipo, $filtros_json, $id, $company_id);

        if ($stmt->execute()) {
            // Se mudou para estática, popular tabela audiencia_leads
            if ($tipo === 'estatica') {
                popularAudienciaEstatica($conn, $id, $company_id, $filtros_json);
            }
            ApiResponse::success(['id' => $id], 'Audiência atualizada');
        } else {
            ApiResponse::error($conn->error, 500);
        }
    } else {
        // Criar
        $sql = "INSERT INTO audiencias (company_id, nome, descricao, tipo, filtros_json, criado_por, criado_em) 
                VALUES (?, ?, ?, ?, ?, ?, NOW())";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("issssi", $company_id, $nome, $descricao, $tipo, $filtros_json, $criado_por);

        if ($stmt->execute()) {
            $new_id = $conn->insert_id;

            // Se é estática, popular tabela audiencia_leads
            if ($tipo === 'estatica') {
                popularAudienciaEstatica($conn, $new_id, $company_id, $filtros_json);
            }

            ApiResponse::created(['id' => $new_id], 'Audiência criada com sucesso');
        } else {
            ApiResponse::error($conn->error, 500);
        }
    }
}

// ========== EXCLUIR AUDIÊNCIA ==========
if ($acao === 'excluir' && $method === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $id = (int) $data['id'];

    $sql = "DELETE FROM audiencias WHERE id=? AND company_id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $id, $company_id);

    if ($stmt->execute()) {
        ApiResponse::success([], 'Audiência excluída');
    } else {
        ApiResponse::error('Erro ao excluir', 500);
    }
}

// ========== PREVIEW DE LEADS ==========
if ($acao === 'preview' && $method === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $filtros_json = isset($data['filtros']) ? json_encode($data['filtros']) : '[]';

    $leads = buscarLeadsPorFiltros($conn, $company_id, $filtros_json, 10);
    $total = contarLeadsPorFiltros($conn, $company_id, $filtros_json);

    ApiResponse::success(['leads' => $leads, 'total' => $total]);
}

// ========== OBTER LEADS DA AUDIÊNCIA ==========
if ($acao === 'leads' && $method === 'GET') {
    $id = (int) $_GET['id'];

    // Buscar audiência
    $sql = "SELECT * FROM audiencias WHERE id=? AND company_id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $id, $company_id);
    $stmt->execute();
    $audiencia = $stmt->get_result()->fetch_assoc();

    if (!$audiencia) {
        ApiResponse::error('Audiência não encontrada', 404);
    }

    if ($audiencia['tipo'] === 'dinamica') {
        // Buscar leads dinamicamente
        $leads = buscarLeadsPorFiltros($conn, $company_id, $audiencia['filtros_json']);
    } else {
        // Buscar leads estáticos
        $sql = "SELECT l.* FROM leads l
                INNER JOIN audiencia_leads al ON l.id = al.lead_id
                WHERE al.audiencia_id = ?
                ORDER BY al.adicionado_em DESC";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();

        $leads = [];
        while ($row = $result->fetch_assoc()) {
            $leads[] = $row;
        }
    }

    ApiResponse::success(['leads' => $leads]);
}

// ========== FUNÇÕES AUXILIARES ==========

function buscarLeadsPorFiltros($conn, $company_id, $filtros_json, $limit = null)
{
    $filtros = json_decode($filtros_json, true);

    $sql = "SELECT * FROM leads WHERE (company_id = ? OR company_id IS NULL)";
    $params = [$company_id];
    $types = "i";

    if (!empty($filtros['filtros'])) {
        $condicao = $filtros['condicao'] ?? 'AND';
        $where_clauses = [];

        foreach ($filtros['filtros'] as $filtro) {
            $campo = $filtro['campo'];
            $operador = $filtro['operador'];
            $valor = $filtro['valor'];

            switch ($operador) {
                case '=':
                    $where_clauses[] = "LOWER($campo) = LOWER(?)";
                    $params[] = $valor;
                    $types .= "s";
                    break;
                case '!=':
                    $where_clauses[] = "LOWER($campo) != LOWER(?)";
                    $params[] = $valor;
                    $types .= "s";
                    break;
                case 'LIKE':
                    $where_clauses[] = "LOWER($campo) LIKE LOWER(?)";
                    $params[] = "%$valor%";
                    $types .= "s";
                    break;
                case 'IN':
                    $placeholders = implode(',', array_fill(0, count($valor), '?'));
                    $where_clauses[] = "$campo IN ($placeholders)";
                    foreach ($valor as $v) {
                        $params[] = $v;
                        $types .= "s";
                    }
                    break;
                case 'NOT IN':
                    $placeholders = implode(',', array_fill(0, count($valor), '?'));
                    $where_clauses[] = "$campo NOT IN ($placeholders)";
                    foreach ($valor as $v) {
                        $params[] = $v;
                        $types .= "s";
                    }
                    break;
                case '>':
                case '<':
                case '>=':
                case '<=':
                    $where_clauses[] = "$campo $operador ?";
                    $params[] = $valor;
                    $types .= "s";
                    break;
                case 'IS NULL':
                    $where_clauses[] = "$campo IS NULL";
                    break;
                case 'IS NOT NULL':
                    $where_clauses[] = "$campo IS NOT NULL";
                    break;
            }
        }

        if (!empty($where_clauses)) {
            $sql .= " AND (" . implode(" $condicao ", $where_clauses) . ")";
        }
    }

    $sql .= " ORDER BY created_at DESC";

    if ($limit) {
        $sql .= " LIMIT $limit";
    }

    $stmt = $conn->prepare($sql);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $result = $stmt->get_result();

    $leads = [];
    while ($row = $result->fetch_assoc()) {
        $leads[] = $row;
    }

    return $leads;
}

function contarLeadsPorFiltros($conn, $company_id, $filtros_json)
{
    $filtros = json_decode($filtros_json, true);

    $sql = "SELECT COUNT(*) as total FROM leads WHERE (company_id = ? OR company_id IS NULL)";
    $params = [$company_id];
    $types = "i";

    if (!empty($filtros['filtros'])) {
        $condicao = $filtros['condicao'] ?? 'AND';
        $where_clauses = [];

        foreach ($filtros['filtros'] as $filtro) {
            $campo = $filtro['campo'];
            $operador = $filtro['operador'];
            $valor = $filtro['valor'];

            switch ($operador) {
                case '=':
                    $where_clauses[] = "LOWER($campo) = LOWER(?)";
                    $params[] = $valor;
                    $types .= "s";
                    break;
                case '!=':
                    $where_clauses[] = "LOWER($campo) != LOWER(?)";
                    $params[] = $valor;
                    $types .= "s";
                    break;
                case 'LIKE':
                    $where_clauses[] = "LOWER($campo) LIKE LOWER(?)";
                    $params[] = "%$valor%";
                    $types .= "s";
                    break;
                case 'IN':
                    $placeholders = implode(',', array_fill(0, count($valor), '?'));
                    $where_clauses[] = "$campo IN ($placeholders)";
                    foreach ($valor as $v) {
                        $params[] = $v;
                        $types .= "s";
                    }
                    break;
                case 'NOT IN':
                    $placeholders = implode(',', array_fill(0, count($valor), '?'));
                    $where_clauses[] = "$campo NOT IN ($placeholders)";
                    foreach ($valor as $v) {
                        $params[] = $v;
                        $types .= "s";
                    }
                    break;
                case '>':
                case '<':
                case '>=':
                case '<=':
                    $where_clauses[] = "$campo $operador ?";
                    $params[] = $valor;
                    $types .= "s";
                    break;
                case 'IS NULL':
                    $where_clauses[] = "$campo IS NULL";
                    break;
                case 'IS NOT NULL':
                    $where_clauses[] = "$campo IS NOT NULL";
                    break;
            }
        }

        if (!empty($where_clauses)) {
            $sql .= " AND (" . implode(" $condicao ", $where_clauses) . ")";
        }
    }

    $stmt = $conn->prepare($sql);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    return $row['total'];
}

function popularAudienciaEstatica($conn, $audiencia_id, $company_id, $filtros_json)
{
    // Limpar leads existentes
    $sql = "DELETE FROM audiencia_leads WHERE audiencia_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $audiencia_id);
    $stmt->execute();

    // Buscar leads que atendem aos filtros
    $leads = buscarLeadsPorFiltros($conn, $company_id, $filtros_json);

    // Inserir na tabela audiencia_leads
    if (!empty($leads)) {
        $sql = "INSERT INTO audiencia_leads (audiencia_id, lead_id) VALUES (?, ?)";
        $stmt = $conn->prepare($sql);

        foreach ($leads as $lead) {
            $stmt->bind_param("ii", $audiencia_id, $lead['id']);
            $stmt->execute();
        }
    }

    // Atualizar total_leads
    $total = count($leads);
    $sql = "UPDATE audiencias SET total_leads = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $total, $audiencia_id);
    $stmt->execute();
}

ApiResponse::error('Ação inválida', 400);
?>