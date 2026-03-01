<?php
// api/n8n/gerar_modulo.php
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
$tema = $input['tema'] ?? '';
$contexto = $input['contexto'] ?? '';
$qtd_aulas = $input['qtd_aulas'] ?? 5;
$produto_id = intval($input['produto_id'] ?? 0);

if (empty($tema) || $produto_id === 0) {
    echo json_encode(['success' => false, 'message' => 'Dados inválidos.']);
    exit;
}

// Load Prompt
$promptFile = __DIR__ . '/../../prompts/gerar_modulo.txt';
if (!file_exists($promptFile)) {
    echo json_encode(['success' => false, 'message' => 'Prompt não encontrado.']);
    exit;
}

$systemPrompt = file_get_contents($promptFile);
$systemPrompt = str_replace(
    ['{{tema}}', '{{contexto}}', '{{qtd_aulas}}'],
    [$tema, $contexto, $qtd_aulas],
    $systemPrompt
);

// Call n8n
$payload = [
    'action' => 'generate_module',
    'system_prompt' => $systemPrompt,
    'tema' => $tema
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
    $maybeWrapper = json_decode($text, true);
    if (is_array($maybeWrapper) && isset($maybeWrapper[0]['output'])) {
        $text = $maybeWrapper[0]['output'];
    } elseif (is_array($maybeWrapper) && isset($maybeWrapper['output'])) {
        $text = $maybeWrapper['output'];
    }

    $decoded = json_decode($text, true);
    if (json_last_error() === JSON_ERROR_NONE)
        return $decoded;

    if (preg_match('/```(?:json)?\s*(.*?)\s*```/s', $text, $matches)) {
        $decoded = json_decode($matches[1], true);
        if (json_last_error() === JSON_ERROR_NONE)
            return $decoded;
    }

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

// Validate AI Response
if (!$aiData || !isset($aiData['title']) || !isset($aiData['lessons'])) {
    echo json_encode(['success' => false, 'message' => 'IA retornou formato inválido.', 'debug' => $response]);
    exit;
}

// SAVE TO DATABASE
$conn->begin_transaction();

try {
    // 1. Get next order for Module
    $resOrder = $conn->query("SELECT MAX(ordem) as max_ordem FROM mentoria_trilhas WHERE produto_id = $produto_id");
    $rowOrder = $resOrder->fetch_assoc();
    $nextOrder = ($rowOrder['max_ordem'] ?? 0) + 1;

    // 2. Insert Module (Trilha)
    $stmtTrilha = $conn->prepare("INSERT INTO mentoria_trilhas (produto_id, titulo, ordem) VALUES (?, ?, ?)");
    $stmtTrilha->bind_param("isi", $produto_id, $aiData['title'], $nextOrder);
    $stmtTrilha->execute();
    $trilha_id = $conn->insert_id;

    // 3. Insert Lessons (Conteudos)
    $stmtAula = $conn->prepare("INSERT INTO mentoria_conteudos (trilha_id, titulo, descricao, tipo, tem_tarefa, tarefa_descricao, ordem) VALUES (?, ?, ?, ?, ?, ?, ?)");

    $aulaOrder = 1;
    foreach ($aiData['lessons'] as $aula) {
        $tipo = 'texto'; // Default or based on AI 'type' if matches enum
        if (isset($aula['type']) && $aula['type'] === 'video')
            $tipo = 'video';

        $tem_tarefa = !empty($aula['has_task']) ? 1 : 0;
        $tarefa_desc = $aula['task_description'] ?? '';

        $stmtAula->bind_param("isssisi", $trilha_id, $aula['title'], $aula['description'], $tipo, $tem_tarefa, $tarefa_desc, $aulaOrder);
        $stmtAula->execute();
        $aulaOrder++;
    }

    $conn->commit();
    echo json_encode(['success' => true, 'message' => 'Módulo criado com sucesso!']);

} catch (Exception $e) {
    $conn->rollback();
    echo json_encode(['success' => false, 'message' => 'Erro ao salvar no banco: ' . $e->getMessage()]);
}
?>