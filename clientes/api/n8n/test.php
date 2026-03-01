<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/database.php';

header('Content-Type: application/json');

// Carregar configurações n8n
$config_file = __DIR__ . '/../../config/n8n_config.json';
if (!file_exists($config_file)) {
    echo json_encode(['success' => false, 'message' => 'Configurações n8n não encontradas']);
    exit;
}

$config = json_decode(file_get_contents($config_file), true);

if (!$config['ativo']) {
    echo json_encode(['success' => false, 'message' => 'Integração n8n está desativada']);
    exit;
}

if (empty($config['webhook_url'])) {
    echo json_encode(['success' => false, 'message' => 'URL do webhook não configurada']);
    exit;
}

// Dados de teste
$dados = [
    'tipo' => 'teste',
    'paciente_nome' => 'Paciente Teste',
    'paciente_telefone' => '11999999999',
    'data_hora' => date('Y-m-d H:i:s', strtotime('+1 day')),
    'profissional_nome' => 'Dr. Teste',
    'tipo_procedimento' => 'Consulta Teste',
    'mensagem' => 'Esta é uma mensagem de teste do sistema de clínica'
];

// Enviar para n8n
$ch = curl_init($config['webhook_url']);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($dados));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Authorization: Bearer ' . ($config['api_token'] ?? '')
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode >= 200 && $httpCode < 300) {
    echo json_encode([
        'success' => true,
        'message' => 'Teste enviado com sucesso! Código HTTP: ' . $httpCode,
        'response' => $response
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Erro ao enviar. Código HTTP: ' . $httpCode,
        'response' => $response
    ]);
}
?>