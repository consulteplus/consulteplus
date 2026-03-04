<?php
require_once __DIR__ . '/../../../../config/config.php';
require_once __DIR__ . '/../../../../config/database.php';
require_once __DIR__ . '/../../../../includes/auth.php';
require_once __DIR__ . '/../../../../classes/AgenteIAService.php';
require_once __DIR__ . '/../../../../classes/ApiResponse.php';

header('Content-Type: application/json');

try {
    $acao = $_GET['acao'] ?? '';
    $method = $_SERVER['REQUEST_METHOD'];

    $companyId = getCompanyId();
    $usuarioId = $_SESSION['user_id'] ?? null;

    $service = new AgenteIAService($conn);

    // GET: Listar
    if ($acao === 'listar' && $method === 'GET') {
        $filtros = [
            'tipo' => $_GET['tipo'] ?? null,
            'ativo' => $_GET['ativo'] ?? null
        ];

        $agentes = $service->listar($filtros, $companyId);
        ApiResponse::success(['agentes' => $agentes]);
    }

    // GET: Buscar
    if ($acao === 'buscar' && $method === 'GET') {
        $id = (int) ($_GET['id'] ?? 0);

        if ($id === 0) {
            ApiResponse::error('ID do agente é obrigatório', 400);
        }

        $agente = $service->buscarPorId($id, $companyId);

        if (!$agente) {
            ApiResponse::error('Agente não encontrado', 404);
        }

        ApiResponse::success(['agente' => $agente]);
    }

    // POST: Salvar
    if ($acao === 'salvar' && $method === 'POST') {
        $data = json_decode(file_get_contents('php://input'), true);

        if (!$data) {
            ApiResponse::error("Invalid JSON", 400);
        }

        $id = $data['id'] ?? 0;

        if ($id > 0) {
            // Atualizar
            $result = $service->atualizar($id, $data, $companyId);
            ApiResponse::success($result, "Agente atualizado com sucesso");
        } else {
            // Criar - usar NULL para agentes globais
            $result = $service->criar($data, null, $usuarioId);
            ApiResponse::success($result, "Agente criado com sucesso");
        }
    }

    // POST: Excluir
    if ($acao === 'excluir' && $method === 'POST') {
        $data = json_decode(file_get_contents('php://input'), true);
        $id = (int) ($data['id'] ?? 0);

        if ($id === 0) {
            ApiResponse::error('ID do agente é obrigatório', 400);
        }

        $service->excluir($id, $companyId);
        ApiResponse::success([], "Agente excluído com sucesso");
    }

    // POST: Testar
    if ($acao === 'testar' && $method === 'POST') {
        $data = json_decode(file_get_contents('php://input'), true);

        $agenteId = (int) ($data['agente_id'] ?? 0);
        $mensagem = $data['mensagem'] ?? '';

        if ($agenteId === 0 || empty($mensagem)) {
            ApiResponse::error('Agente ID e mensagem são obrigatórios', 400);
        }

        $resultado = $service->testar($agenteId, $mensagem, $usuarioId);
        ApiResponse::success($resultado);
    }

    // POST: Testar Simulação (Webhook Real)
    if ($acao === 'testar_simulacao' && $method === 'POST') {
        try {
            // Enable error logging to file
            ini_set('log_errors', 1);
            ini_set('error_log', __DIR__ . '/debug_simulador.log');

            $input = file_get_contents('php://input');
            error_log("Input received: " . $input);

            $data = json_decode($input, true);

            $mensagem = $data['mensagem'] ?? '';
            $numeroTeste = $data['numero'] ?? '558699999999';

            if (empty($mensagem)) {
                ApiResponse::error('Mensagem é obrigatória', 400);
            }

            // Envia para o n8n simulando WhatsApp
            $resultado = $service->testarSimulacao($mensagem, $numeroTeste);
            error_log("Result: " . print_r($resultado, true));

            ApiResponse::success($resultado);
        } catch (Throwable $t) {
            error_log("Fatal Error in testar_simulacao: " . $t->getMessage() . "\n" . $t->getTraceAsString());
            ApiResponse::error("Erro interno: " . $t->getMessage(), 500);
        }
    }

    // GET: Histórico
    if ($acao === 'historico' && $method === 'GET') {
        $agenteId = (int) ($_GET['agente_id'] ?? 0);
        $limit = (int) ($_GET['limit'] ?? 50);
        $tipo = $_GET['tipo'] ?? null;

        if ($agenteId === 0) {
            ApiResponse::error('Agente ID é obrigatório', 400);
        }

        $historico = $service->buscarHistorico($agenteId, $limit, $tipo);
        ApiResponse::success(['historico' => $historico]);
    }

    ApiResponse::error('Ação inválida', 400);

} catch (Exception $e) {
    ApiResponse::error($e->getMessage(), 500);
}
?>