<?php
// api/v1/leads.php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../classes/ApiResponse.php';
require_once __DIR__ . '/../../classes/Logger.php';
require_once __DIR__ . '/../../classes/LeadService.php';

// Headers
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
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

// Instancia
try {
    $service = new LeadService($conn);
    $method = $_SERVER['REQUEST_METHOD'];
    $companyId = $_GET['company_id'] ?? null; // Admin/Global default null

    // --- GET: Listar ---
    if ($method === 'GET') {
        $filtros = [
            'status' => $_GET['status'] ?? null,
            'origem' => $_GET['origem'] ?? null,
            'telefone' => $_GET['telefone'] ?? null
        ];
        $limit = (int) ($_GET['limit'] ?? 50);

        $leads = $service->listar($filtros, $companyId, $limit);
        ApiResponse::success(['leads' => $leads]);
    }

    // --- POST: Criar / Atualizar / Converter ---
    if ($method === 'POST') {
        $json = file_get_contents('php://input');
        $data = json_decode($json, true);
        if (!$data)
            ApiResponse::error("Invalid JSON", 400);

        $action = $_GET['action'] ?? null; // ?action=convert

        // 1. Converter
        if ($action === 'convert') {
            $leadId = $data['lead_id'] ?? $data['id'] ?? 0;
            if (!$leadId)
                ApiResponse::error("Lead ID required", 400);

            $funilId = $data['funil_id'] ?? 1;
            $etapaId = $data['etapa_id'] ?? 0;
            $userId = $data['user_id'] ?? 1;

            $negocioId = $service->converterEmNegocio($leadId, $companyId, $userId, $funilId, $etapaId);
            ApiResponse::success(['deal_id' => $negocioId], "Lead converted successfully");
            exit;
        }

        // 2. Criar ou Atualizar (Upsert Padrão)
        // Validação relaxada (Nome OU Email OU Telefone)
        $hasId = !empty($data['id']);
        $hasEmail = !empty($data['email']);
        $hasPhone = !empty($data['telefone']);
        $hasName = !empty($data['nome']);

        if ($hasId || $hasEmail || $hasPhone || $hasName) {
            $res = $service->upsertLead($data, $companyId);
            ApiResponse::success(['result' => $res], "Lead processed successfully");
        } else {
            ApiResponse::error("At least Name, Email, Phone or ID required", 400);
        }
    }

} catch (Exception $e) {
    ApiResponse::error($e->getMessage(), 500);
}
