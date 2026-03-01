<?php
// api/n8n/gerar_roteiro.php
header('Content-Type: application/json');
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/database.php';

session_start();
if (!isset($_SESSION['user_id']) || !in_array('admin', $_SESSION['permissoes'] ?? ['admin'])) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Acesso negado.']);
    exit;
}

// Ler config
$configFile = __DIR__ . '/../../config/n8n_config.json';
if (!file_exists($configFile)) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Configuração não encontrada.']);
    exit;
}
$config = json_decode(file_get_contents($configFile), true);
$webhookUrl = $config['roteiro_webhook_url'] ?? '';

if (empty($webhookUrl)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Webhook de Roteiro não configurado nas configurações n8n.']);
    exit;
}

// Input
$input = json_decode(file_get_contents('php://input'), true);
$titulo = $input['titulo'] ?? '';
$contexto = $input['contexto'] ?? ''; // Pode vir a descrição atual, ou infos extras

if (empty($titulo)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Título da aula é obrigatório.']);
    exit;
}

// System Prompt
$systemPrompt = <<<EOT
# Role
Você é um Copywriter Especialista em Scripts para Aulas Online e Vídeos Educativos.

# Tarefa
Escreva um Roteiro de Vídeo (Script) detalhado para uma aula online.
O roteiro deve ser engajador, direto e didático.

# Input
Título da Aula: $titulo
Contexto/Resumo: $contexto

# Formato de Saída (JSON)
Você deve retornar APENAS um JSON com o campo "script".
O conteúdo do "script" pode usar Markdown (negrito, listas) para melhor leitura.

{
  "script": "Olá pessoal, bem-vindos a mais uma aula! Hoje vamos falar sobre..."
}
EOT;

// Payload
$payload = [
    'action' => 'generate_script',
    'titulo' => $titulo,
    'contexto' => $contexto,
    'system_prompt' => $systemPrompt,
    'user_id' => $_SESSION['user_id']
];

// Curl
$ch = curl_init($webhookUrl);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 120);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode >= 200 && $httpCode < 300 && $response) {
    echo $response;
} else {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Erro n8n.', 'debug' => $response]);
}
?>