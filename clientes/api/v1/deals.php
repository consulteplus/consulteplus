<?php
// api/v1/deals.php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../classes/ApiResponse.php';
require_once __DIR__ . '/../../classes/Logger.php';
require_once __DIR__ . '/../../classes/CrmService.php';

// Headers
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, X-API-KEY');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Auth
$headers = getallheaders();
$apiKey = $headers['X-API-KEY'] ?? $headers['x-api-key'] ?? '';
if ($apiKey !== CRM_API_KEY) {
    ApiResponse::error("Unauthorized", 401);
}

try {
    $service = new CrmService($conn);
    $method = $_SERVER['REQUEST_METHOD'];
    $companyId = $_GET['company_id'] ?? 1; // Default 1 for Deals often required

    if ($method === 'POST') {
        $json = file_get_contents('php://input');
        $data = json_decode($json, true);
        if (!$data)
            ApiResponse::error("Invalid JSON", 400);

        $action = $_GET['action'] ?? null;
        $dealId = $data['id'] ?? $data['deal_id'] ?? 0;

        // --- Adicionar Nota ---
        if ($action === 'add_note') {
            if (!$dealId)
                ApiResponse::error("Deal ID required", 400);
            $texto = $data['text'] ?? $data['descricao'];
            if (!$texto)
                ApiResponse::error("Text required", 400);

            $tipo = $data['tipo'] ?? 'nota';
            $userId = $data['user_id'] ?? 1;

            $service->salvarAnotacao($companyId, $dealId, $userId, $tipo, $texto);
            ApiResponse::success([], "Note added");
            exit;
        }

        // --- Atualizar Etapa ---
        if ($action === 'update_stage') {
            if (!$dealId)
                ApiResponse::error("Deal ID required", 400);
            if (!isset($data['etapa_id']))
                ApiResponse::error("etapa_id required", 400);

            $service->moverEtapa($dealId, $data['etapa_id'], $companyId, $data['user_id'] ?? 1);
            ApiResponse::success([], "Stage updated");
            exit;
        }

        // --- Criar ou Atualizar (Genérico) ---
        if ($dealId) {
            // Update
            $service->atualizarNegocio($dealId, $data, $companyId);
            ApiResponse::success([], "Deal updated");
        } else {
            // Create
            if (empty($data['titulo']))
                ApiResponse::error("Title required", 400);
            $userId = $data['user_id'] ?? 1;
            $id = $service->criarNegocio($data, $companyId, $userId);
            ApiResponse::created(['id' => $id, 'message' => 'Deal created']);
        }
    }

} catch (Exception $e) {
    ApiResponse::error($e->getMessage(), 500);
}
