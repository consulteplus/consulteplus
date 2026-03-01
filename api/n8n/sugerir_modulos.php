<?php
// api/n8n/sugerir_modulos.php
header('Content-Type: application/json');
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/database.php';

session_start();
if (!isset($_SESSION['user_id'])) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Acesso negado.']);
    exit;
}

// Configs
$configFile = __DIR__ . '/../../config/n8n_config.json';
$config = json_decode(file_get_contents($configFile), true);
$webhookUrl = $config['mentoria_webhook_url'] ?? '';

if (empty($webhookUrl)) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Webhook não configurado.']);
    exit;
}

// Input
$input = json_decode(file_get_contents('php://input'), true);
$produto_id = intval($input['produto_id'] ?? 0);

if ($produto_id === 0) {
    echo json_encode(['success' => false, 'message' => 'Produto ID inválido.']);
    exit;
}

// 1. Fetch Course Context
$stmt = $conn->prepare("SELECT titulo, descricao FROM mentoria_produtos WHERE id = ?");
$stmt->bind_param("i", $produto_id);
$stmt->execute();
$prod = $stmt->get_result()->fetch_assoc();

if (!$prod) {
    echo json_encode(['success' => false, 'message' => 'Produto não encontrado.']);
    exit;
}

// 2. Fetch Existing Modules
$stmtMod = $conn->prepare("SELECT titulo FROM mentoria_trilhas WHERE produto_id = ? ORDER BY ordem ASC");
$stmtMod->bind_param("i", $produto_id);
$stmtMod->execute();
$resMod = $stmtMod->get_result();

$modulosAtuais = [];
while ($row = $resMod->fetch_assoc()) {
    $modulosAtuais[] = "- " . $row['titulo'];
}
$modulosStr = implode("\n", $modulosAtuais);
if (empty($modulosStr))
    $modulosStr = "(Nenhum módulo criado ainda)";

// 3. Load & Prepare Prompt
$promptFile = __DIR__ . '/../../prompts/sugerir_modulos.txt';
if (!file_exists($promptFile)) {
    echo json_encode(['success' => false, 'message' => 'Prompt não encontrado.']);
    exit;
}

$systemPrompt = file_get_contents($promptFile);
$systemPrompt = str_replace(
    ['{{curso_titulo}}', '{{curso_descricao}}', '{{modulos_atuais}}'],
    [$prod['titulo'], $prod['descricao'] ?? 'Sem descrição', $modulosStr],
    $systemPrompt
);

// 4. Call n8n
$payload = [
    'action' => 'suggest_modules',
    'system_prompt' => $systemPrompt
];

$ch = curl_init($webhookUrl);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 120);
$response = curl_exec($ch);
curl_close($ch);

function extractJson($text)
{
    // 1. Unpack n8n wrapper if present
    // n8n might return [{ "output": "..." }]
    $maybeWrapper = json_decode($text, true);
    if (is_array($maybeWrapper) && isset($maybeWrapper[0]['output'])) {
        $text = $maybeWrapper[0]['output'];
    } elseif (is_array($maybeWrapper) && isset($maybeWrapper['output'])) {
        $text = $maybeWrapper['output'];
    }

    // 2. Try direct decode
    $decoded = json_decode($text, true);
    if (json_last_error() === JSON_ERROR_NONE)
        return $decoded;

    // 3. Try extracting from markdown code block
    if (preg_match('/```(?:json)?\s*(.*?)\s*```/s', $text, $matches)) {
        $decoded = json_decode($matches[1], true);
        if (json_last_error() === JSON_ERROR_NONE)
            return $decoded;
    }

    // 4. Try finding first { and last } (most aggressive)
    $start = strpos($text, '{');
    $end = strrpos($text, '}');
    if ($start !== false && $end !== false && $end > $start) {
        $jsonCandidate = substr($text, $start, $end - $start + 1);
        $decoded = json_decode($jsonCandidate, true);
        if (json_last_error() === JSON_ERROR_NONE)
            return $decoded;
    }

    return null;
}

$aiData = extractJson($response);

// 5. Return Suggestions
if (!$aiData || !isset($aiData['suggestions'])) {
    echo json_encode(['success' => false, 'message' => 'IA retornou formato inválido.', 'debug' => $response]);
    exit;
}

echo json_encode(['success' => true, 'suggestions' => $aiData['suggestions']]);
?>