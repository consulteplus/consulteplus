<?php
require_once __DIR__ . '/../../../config/config.php';
require_once __DIR__ . '/../../../config/database.php';
require_once __DIR__ . '/../../../includes/auth.php';
require_once __DIR__ . '/../../../classes/ApiResponse.php';
require_once __DIR__ . '/../../../classes/ProductService.php';

// Configurações de erro JSON-safe
ini_set('display_errors', 0);
error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);
header('Content-Type: application/json');

try {
    // Check Permissions
    checkPermission(['admin']);

    $acao = isset($_GET['acao']) ? $_GET['acao'] : '';
    $method = $_SERVER['REQUEST_METHOD'];
    $company_id = getCompanyId();

    $productService = new ProductService();

    if ($acao === 'listar' && $method === 'GET') {
        $limit = isset($_GET['limit']) ? (int) $_GET['limit'] : 10;
        $offset = isset($_GET['offset']) ? (int) $_GET['offset'] : 0;

        $produtos = $productService->listar($company_id, $limit, $offset);
        $total = $productService->contarTotal($company_id);

        ApiResponse::success(['produtos' => $produtos, 'total' => $total]);
    }

    if ($acao === 'salvar' && $method === 'POST') {
        $data = json_decode(file_get_contents('php://input'), true);

        if (!$data) {
            ApiResponse::error("Dados inválidos", 400);
        }

        $id = $productService->salvar($data, $company_id);
        ApiResponse::success(['id' => $id], 'Produto salvo com sucesso!');
    }

    if ($acao === 'excluir' && $method === 'POST') {
        $data = json_decode(file_get_contents('php://input'), true);
        $id = (int) ($data['id'] ?? 0);

        if ($id <= 0)
            ApiResponse::error("ID inválido", 400);

        $productService->excluir($id, $company_id);
        ApiResponse::success([], 'Produto excluído com sucesso!');
    }

    ApiResponse::error('Ação inválida', 400);

} catch (Exception $e) {
    ApiResponse::error($e->getMessage(), 500);
} catch (Error $e) {
    ApiResponse::error('Erro fatal: ' . $e->getMessage(), 500);
}
?>