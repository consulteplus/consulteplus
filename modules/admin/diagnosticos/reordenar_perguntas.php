<?php
// modules/admin/diagnosticos/reordenar_perguntas.php
header('Content-Type: application/json');
require_once __DIR__ . '/../../../config/database.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Acesso negado.']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
$ids = $input['ids'] ?? [];

if (empty($ids)) {
    echo json_encode(['success' => false, 'message' => 'Nenhum ID fornecido.']);
    exit;
}

// Ensure all IDs are integers
$ids = array_map('intval', $ids);
$idsList = implode(',', $ids);

try {
    $conn->begin_transaction();

    // 1. Get current 'ordem' values for these questions to preserve their "slots"
    $sql = "SELECT id, ordem FROM gestao_diagnostico_perguntas WHERE id IN ($idsList) ORDER BY ordem ASC";
    $result = $conn->query($sql);

    $slots = [];
    while ($row = $result->fetch_assoc()) {
        $slots[] = $row['ordem'];
    }

    // Sort slots to ensure we use them in ascending order
    sort($slots);

    // 2. Assign the slots to the IDs in the order provided by the frontend
    foreach ($ids as $index => $id) {
        if (isset($slots[$index])) {
            $newOrdem = $slots[$index];
            $conn->query("UPDATE gestao_diagnostico_perguntas SET ordem = $newOrdem WHERE id = $id");
        }
    }

    $conn->commit();
    echo json_encode(['success' => true]);

} catch (Exception $e) {
    if ($conn->connect_errno) {
        $conn->rollback();
    }
    echo json_encode(['success' => false, 'message' => 'Erro: ' . $e->getMessage()]);
}
?>