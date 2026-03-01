<?php
// Prevent any HTML output from errors
ini_set('display_errors', 0);
error_reporting(0);

// Start buffering to capture unwanted output (warnings, notices, whitespace)
ob_start();

try {
    require_once __DIR__ . '/../../../config/config.php';

    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    // Clear buffer before sensing headers to be safe
    ob_clean();
    header('Content-Type: application/json');
    set_time_limit(300);

    // Verificar autenticação
    if (!isset($_SESSION['user_id'])) {
        throw new Exception('Não autorizado', 401);
    }

    // Ler input
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);

    if (!$data) {
        throw new Exception('Dados inválidos ou vazios', 400);
    }

    // Carregar Configuração
    $n8nConfigFile = __DIR__ . '/../../../config/n8n_config.json';
    if (!file_exists($n8nConfigFile)) {
        throw new Exception('Configuração n8n não encontrada', 500);
    }

    $n8nConfig = json_decode(file_get_contents($n8nConfigFile), true);

    // Select webhook based on type
    $type = $_GET['type'] ?? 'roadmap';
    $webhookUrl = '';

    if ($type === 'analysis') {
        $webhookUrl = $n8nConfig['analysis_webhook_url'] ?? '';
    } elseif ($type === 'task') {
        $webhookUrl = $n8nConfig['task_webhook_url'] ?? '';
    } elseif ($type === 'project') {
        $webhookUrl = $n8nConfig['project_webhook_url'] ?? '';
    } elseif ($type === 'diagnostic') {
        $webhookUrl = $n8nConfig['diagnostic_webhook_url'] ?? '';
    } else {
        $webhookUrl = $n8nConfig['roadmap_webhook_url'] ?? '';
    }

    if (empty($webhookUrl)) {
        throw new Exception('Webhook URL (' . $type . ') não configurada', 400);
    }

    // Fazer a requisição para o n8n
    $ch = curl_init($webhookUrl);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Accept: application/json'
    ]);
    curl_setopt($ch, CURLOPT_TIMEOUT, 300); // 5 minutes for large AI generations


    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);

    if ($error) {
        throw new Exception('Erro ao conectar com n8n: ' . $error, 502);
    }

    if ($httpCode >= 400) {
        throw new Exception('Erro do n8n: ' . $response, $httpCode);
    }

    // Success - Clean buffer and echo JSON
    ob_clean();
    echo $response;

} catch (Exception $e) {
    ob_clean(); // Discard any garbage collected
    http_response_code($e->getCode() ?: 500);
    header('Content-Type: application/json'); // Ensure header is set
    echo json_encode(['error' => $e->getMessage()]);
} catch (Throwable $t) {
    ob_clean();
    http_response_code(500);
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Erro interno: ' . $t->getMessage()]);
}
exit;
?>
