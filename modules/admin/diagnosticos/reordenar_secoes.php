<?php
// modules/admin/diagnosticos/reordenar_secoes.php
header('Content-Type: application/json');
require_once __DIR__ . '/../../../config/database.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Acesso negado.']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
$modelo_id = intval($input['modelo_id'] ?? 0);
$sectionsOrder = $input['sections'] ?? [];

if ($modelo_id === 0 || empty($sectionsOrder)) {
    echo json_encode(['success' => false, 'message' => 'Dados inválidos.']);
    exit;
}

// 1. Fetch all quests for this model, ordered by current order
$stmt = $conn->prepare("SELECT id, secao FROM gestao_diagnostico_perguntas WHERE modelo_id = ? ORDER BY ordem ASC");
$stmt->bind_param("i", $modelo_id);
$stmt->execute();
$result = $stmt->get_result();

$questionsBySection = [];
while ($row = $result->fetch_assoc()) {
    $questionsBySection[$row['secao']][] = $row['id'];
}

$conn->begin_transaction();
try {
    $globalOrder = 1;

    // 2. Iterate through the NEW section order
    foreach ($sectionsOrder as $secName) {
        if (isset($questionsBySection[$secName])) {
            // Update all questions in this section
            // Preserving their relative order because we fetched them ORDER BY ordem ASC
            foreach ($questionsBySection[$secName] as $qId) {
                $conn->query("UPDATE gestao_diagnostico_perguntas SET ordem = $globalOrder WHERE id = $qId");
                $globalOrder++;
            }
        }
    }

    // Handle any sections that might have been missed (orphan handling, just in case)
    // (Optional, but good practice if the UI list was incomplete)

    $conn->commit();
    echo json_encode(['success' => true]);

} catch (Exception $e) {
    $conn->rollback();
    echo json_encode(['success' => false, 'message' => 'Erro ao salvar ordem: ' . $e->getMessage()]);
}
?>