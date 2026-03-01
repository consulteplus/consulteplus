<?php
require_once __DIR__ . '/../../../config/config.php';
require_once __DIR__ . '/../../../config/database.php';
require_once __DIR__ . '/../../../includes/auth.php';
require_once __DIR__ . '/../../../classes/ApiResponse.php';
require_once __DIR__ . '/../../../classes/ProductContentService.php';

// Configurações de erro JSON-safe
ini_set('display_errors', 0);
error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);
header('Content-Type: application/json');

try {
    // Check Permissions
    checkPermission(['admin']);

    $acao = isset($_GET['acao']) ? $_GET['acao'] : '';
    $method = $_SERVER['REQUEST_METHOD'];

    $contentService = new ProductContentService();

    // --- TRILHAS ---

    if ($acao === 'salvar_trilha' && $method === 'POST') {
        $data = json_decode(file_get_contents('php://input'), true);
        $contentService->salvarTrilha($data);
        ApiResponse::success([], 'Trilha salva com sucesso!');
    }

    if ($acao === 'excluir_trilha' && $method === 'POST') {
        $data = json_decode(file_get_contents('php://input'), true);
        $id = (int) ($data['id'] ?? 0);

        if ($id <= 0)
            ApiResponse::error("ID inválido", 400);

        $contentService->excluirTrilha($id);
        ApiResponse::success([], 'Trilha excluída com sucesso!');
    }

    if ($acao === 'reordenar_trilhas' && $method === 'POST') {
        $data = json_decode(file_get_contents('php://input'), true);
        $contentService->reordenarTrilhas($data['order'] ?? []);
        ApiResponse::success([], 'Ordem atualizada!');
    }

    // --- CONTEÚDOS ---

    if ($acao === 'salvar_conteudo' && $method === 'POST') {
        $data = json_decode(file_get_contents('php://input'), true);
        $contentService->salvarConteudo($data);
        ApiResponse::success([], 'Conteúdo salvo com sucesso!');
    }

    if ($acao === 'excluir_conteudo' && $method === 'POST') {
        $data = json_decode(file_get_contents('php://input'), true);
        $id = (int) ($data['id'] ?? 0);

        if ($id <= 0)
            ApiResponse::error("ID inválido", 400);

        $contentService->excluirConteudo($id);
        ApiResponse::success([], 'Conteúdo excluído com sucesso!');
    }

    ApiResponse::error('Ação inválida', 400);

} catch (Exception $e) {
    ApiResponse::error($e->getMessage(), 500);
} catch (Error $e) {
    ApiResponse::error('Erro fatal: ' . $e->getMessage(), 500);
}
?>