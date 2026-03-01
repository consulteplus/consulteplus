<?php
// api/n8n/gerar_mentoria.php
header('Content-Type: application/json');
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/database.php';

// Validar sessão (Apenas admin logado pode disparar)
session_start();
if (!isset($_SESSION['user_id']) || !in_array('admin', $_SESSION['permissoes'] ?? ['admin'])) {
  http_response_code(403);
  echo json_encode(['success' => false, 'message' => 'Acesso negado.']);
  exit;
}

// Ler URL do arquivo de config
$configFile = __DIR__ . '/../../config/n8n_config.json';
if (!file_exists($configFile)) {
  http_response_code(500);
  echo json_encode(['success' => false, 'message' => 'Configuração n8n não encontrada.']);
  exit;
}

$config = json_decode(file_get_contents($configFile), true);
$webhookUrl = $config['mentoria_webhook_url'] ?? '';

if (empty($webhookUrl)) {
  http_response_code(400);
  echo json_encode(['success' => false, 'message' => 'Webhook de Mentoria não configurado.']);
  exit;
}

// Pegar dados do POST
$input = json_decode(file_get_contents('php://input'), true);
$tema = $input['tema'] ?? '';
$publico = $input['publico'] ?? '';
$profundidade = $input['profundidade'] ?? 'Intermediário';
$duracao = $input['duracao'] ?? 'Média';

if (empty($tema)) {
  http_response_code(400);
  echo json_encode(['success' => false, 'message' => 'O tema é obrigatório.']);
  exit;
}

// Prompt do Sistema para garantir o formato JSON correto
// Carregar Prompt do Arquivo
$promptFile = __DIR__ . '/../../prompts/gerar_mentoria.txt';
if (!file_exists($promptFile)) {
  http_response_code(500);
  echo json_encode(['success' => false, 'message' => 'Arquivo de prompt não encontrado.']);
  exit;
}

$systemPrompt = file_get_contents($promptFile);

// --- 1. Buscar Diagnósticos Disponíveis ---
$diagList = "Nenhum disponível.";
$resDiag = $conn->query("SELECT id, titulo FROM gestao_diagnostico_modelos WHERE ativo = 1");
if ($resDiag && $resDiag->num_rows > 0) {
  $diags = [];
  while ($d = $resDiag->fetch_assoc()) {
    $diags[] = "- ID {$d['id']}: {$d['titulo']}";
  }
  $diagList = implode("\n", $diags);
}

// --- 2. Buscar Ferramentas Disponíveis ---
$toolList = "Nenhuma disponível.";
$resTools = $conn->query("SELECT id, nome FROM ferramentas_tipos WHERE ativo = 1");
if ($resTools && $resTools->num_rows > 0) {
  $tools = [];
  while ($t = $resTools->fetch_assoc()) {
    $tools[] = "- ID {$t['id']}: {$t['nome']}";
  }
  $toolList = implode("\n", $tools);
}

// Substituir variáveis no prompt
$instrucoes_extras = $input['instrucoes_extras'] ?? 'Nenhuma instrução extra.';

// Substituir variáveis no prompt
$systemPrompt = str_replace(
  ['{{tema}}', '{{publico}}', '{{profundidade}}', '{{duracao}}', '{{tipo}}', '{{instrucoes_extras}}', '{{diagnosticos_list}}', '{{ferramentas_list}}'],
  [$tema, $publico, $profundidade, $duracao, $input['tipo'] ?? 'mentoria', $instrucoes_extras, $diagList, $toolList],
  $systemPrompt
);

// Preparar Payload para o n8n
$payload = [
  'action' => 'generate_structure',
  'tema' => $tema,
  'publico' => $publico,
  'profundidade' => $profundidade,
  'duracao' => $duracao,
  'system_prompt' => $systemPrompt, // Enviando o prompt junto!
  'user_id' => $_SESSION['user_id'],
  'request_time' => date('Y-m-d H:i:s')
];

// Enviar para o n8n
$ch = curl_init($webhookUrl);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 120); // Timeout maior pois a IA pode demorar

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curlError = curl_error($ch);
curl_close($ch);

if ($httpCode >= 200 && $httpCode < 300 && $response) {
  // A resposta do n8n deve ser um JSON com a estrutura sugerida
  echo $response;
} else {
  http_response_code(500);
  echo json_encode([
    'success' => false,
    'message' => 'Erro ao comunicar com n8n.',
    'debug' => $curlError,
    'http_code' => $httpCode
  ]);
}
?>