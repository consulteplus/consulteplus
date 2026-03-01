<?php
// api/mentoria/reordenar_trilhas.php
header('Content-Type: application/json');
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/database.php';

session_start();

// Validar sessão admin
if (!isset($_SESSION['user_id'])) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Acesso negado.']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
$order = $input['order'] ?? [];

if (empty($order)) { // Check if empty array
    echo json_encode(['success' => false, 'message' => 'Nenhuma ordem recebida.']);
    exit;
}

$conn->begin_transaction();
try {
    $stmt = $conn->prepare("UPDATE mentoria_trilhas SET ordem = ? WHERE id = ?");

    foreach ($order as $index => $id) {
        $ordem = $index + 1;
        $stmt->bind_param("ii", $ordem, $id);
        $stmt->execute();
    }

    $conn->commit();
    echo json_encode(['success' => true]);

} catch (Exception $e) {
    $conn->rollback();
    echo json_encode(['success' => false, 'message' => 'Erro ao reordenar: ' . $e->getMessage()]);
}
?>