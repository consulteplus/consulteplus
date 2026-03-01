<?php
require_once __DIR__ . '/../../../config/config.php';
require_once __DIR__ . '/../../../config/database.php';
require_once __DIR__ . '/../../../includes/auth.php';

header('Content-Type: application/json');
session_start();

// 1. Auth Check
if (!isset($_SESSION['user_id']) || $_SESSION['tipo'] !== 'superadmin') {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Não autorizado']);
    exit;
}

// 2. Read Input
$input = json_decode(file_get_contents('php://input'), true);
if (!$input || empty($input['titulo']) || empty($input['perguntas'])) {
    echo json_encode(['success' => false, 'error' => 'Dados inválidos']);
    exit;
}

$conn->begin_transaction();

try {
    // 3. Create Model
    $stmtModel = $conn->prepare("INSERT INTO gestao_diagnostico_modelos (titulo, descricao, ativo, created_at) VALUES (?, ?, 1, NOW())");
    $titulo = $input['titulo'];
    $descricao = $input['descricao'] ?? '';

    $stmtModel->bind_param("ss", $titulo, $descricao);

    if (!$stmtModel->execute()) {
        throw new Exception("Erro ao criar modelo: " . $stmtModel->error);
    }

    $modeloId = $stmtModel->insert_id;

    // 4. Create Questions
    $stmtPerg = $conn->prepare("INSERT INTO gestao_diagnostico_perguntas (modelo_id, secao, texto_pergunta, tipo, opcoes, logica_ia, ordem, texto_min, texto_max) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");

    // Start order from 1
    $ordem = 1;

    foreach ($input['perguntas'] as $p) {
        $secao = $p['secao'] ?? 'Geral';
        $texto = $p['texto_pergunta'] ?? 'Pergunta sem texto';
        $tipo = $p['tipo'] ?? 'escala';

        // Handle Options (Array to JSON)
        $opcoes = null;
        if (!empty($p['opcoes'])) {
            $opcoes = is_array($p['opcoes']) ? json_encode($p['opcoes'], JSON_UNESCAPED_UNICODE) : $p['opcoes'];
        }

        $logica = $p['logica_ia'] ?? '';
        $txtMin = $p['texto_min'] ?? '';
        $txtMax = $p['texto_max'] ?? '';

        $stmtPerg->bind_param("isssssiss", $modeloId, $secao, $texto, $tipo, $opcoes, $logica, $ordem, $txtMin, $txtMax);

        if (!$stmtPerg->execute()) {
            throw new Exception("Erro ao inserir pergunta: " . $stmtPerg->error);
        }
        $ordem++;
    }

    $conn->commit();
    echo json_encode(['success' => true, 'id' => $modeloId, 'message' => 'Diagnóstico criado com sucesso!']);

} catch (Exception $e) {
    $conn->rollback();
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>