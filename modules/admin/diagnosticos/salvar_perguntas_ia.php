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
if (!$input || empty($input['perguntas']) || empty($input['modelo_id'])) {
    echo json_encode(['success' => false, 'error' => 'Dados inválidos']);
    exit;
}

$modeloId = intval($input['modelo_id']);
$perguntas = $input['perguntas'];

$conn->begin_transaction();

try {
    // Determine the current max order to append new questions
    $resOrdem = $conn->query("SELECT MAX(ordem) as max_ordem FROM gestao_diagnostico_perguntas WHERE modelo_id = $modeloId");
    $rowOrdem = $resOrdem->fetch_assoc();
    $nextOrdem = ($rowOrdem['max_ordem'] ?? 0) + 1;

    $stmt = $conn->prepare("INSERT INTO gestao_diagnostico_perguntas (modelo_id, secao, texto_pergunta, tipo, opcoes, logica_ia, ordem, texto_min, texto_max) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");

    foreach ($perguntas as $p) {
        $secao = $p['secao'] ?? 'Geral';
        $texto = $p['texto_pergunta'] ?? 'Pergunta sem texto';
        $tipo = $p['tipo'] ?? 'escala';

        // Handle Options (Array to JSON)
        $opcoes = null;
        if (!empty($p['opcoes'])) {
            $opcoes = is_array($p['opcoes']) ? json_encode($p['opcoes'], JSON_UNESCAPED_UNICODE) : $p['opcoes'];
        }

        $logica = $p['logica_ia'] ?? '';
        $ordem = isset($p['ordem']) ? intval($p['ordem']) : $nextOrdem++;

        $txtMin = $p['texto_min'] ?? '';
        $txtMax = $p['texto_max'] ?? '';

        $stmt->bind_param("isssssiss", $modeloId, $secao, $texto, $tipo, $opcoes, $logica, $ordem, $txtMin, $txtMax);

        if (!$stmt->execute()) {
            throw new Exception("Erro ao inserir pergunta: " . $stmt->error);
        }
    }

    $conn->commit();
    echo json_encode(['success' => true, 'count' => count($perguntas), 'message' => 'Perguntas criadas com sucesso!']);

} catch (Exception $e) {
    $conn->rollback();
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>