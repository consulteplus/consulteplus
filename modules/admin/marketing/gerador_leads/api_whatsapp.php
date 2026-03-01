<?php
require_once __DIR__ . '/../../../../config/config.php';

// Disable error display for cleaner JSON response
error_reporting(0);
ini_set('display_errors', 0);

header('Content-Type: application/json');

// Carregar Configurações
$configFile = __DIR__ . '/../../../../config/uazapi_config.json';
if (!file_exists($configFile)) {
    echo json_encode(['success' => false, 'error' => 'Arquivo de configuração do Uazapi não procurado.']);
    exit;
}

$config = json_decode(file_get_contents($configFile), true);
$baseUrl = rtrim($config['base_url'], '/');
$apiToken = $config['api_token'];

// Helper função para requisições CURL
function uazapiRequest($endpoint, $method = 'GET', $data = [])
{
    global $baseUrl, $apiToken;

    $url = $baseUrl . $endpoint;
    $ch = curl_init($url);

    $headers = [
        'Content-Type: application/json',
        'token: ' . $apiToken
    ];

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);

    if ($method === 'POST') {
        curl_setopt($ch, CURLOPT_POST, true);
        $payload = empty($data) ? '{}' : json_encode($data);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
    } elseif ($method === 'DELETE') {
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
    }

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);

    // Logging for debug
    $logMsg = date('[Y-m-d H:i:s] ') . "Request: $method $url\n";
    $logMsg .= "Response Code: $httpCode\n";
    $logMsg .= "Response Body: " . $response . "\n";
    if ($error)
        $logMsg .= "CURL Error: $error\n";
    $logMsg .= "--------------------------------------------------\n";
    file_put_contents(__DIR__ . '/whatsapp_debug.log', $logMsg, FILE_APPEND);

    if ($error) {
        return ['success' => false, 'error' => 'CURL Error: ' . $error];
    }

    return ['success' => true, 'http_code' => $httpCode, 'body' => json_decode($response, true)];
}

$action = $_GET['action'] ?? '';

// --- ACTIONS ---

if ($action === 'connect') {
    // Iniciar sessão / Gerar QR Code
    // Endpoint: POST /instance/connect
    $res = uazapiRequest('/instance/connect', 'POST', []);

    // Uazapi retorna sucesso se iniciou o processo ou já está conectado
    if ($res['success']) {
        echo json_encode(['success' => true, 'data' => $res['body']]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Falha ao conectar: ' . ($res['error'] ?? 'Erro desconhecido')]);
    }
    exit;
}

if ($action === 'logout') {
    // Desconectar / Logout
    // Endpoint: DELETE /instance/disconnect
    // O usuário informou que o endpoint correto é /instance/disconnect

    // Tentativa: Endpoint direto conforme solicitado pelo usuário
    $endpoint = '/instance/disconnect';

    // REMOVIDO: Anexo de instance_name (usuário confirmou que não é necessário)
    // if (!empty($config['instance_name'])) { ... }

    $res = uazapiRequest($endpoint, 'POST', []);

    if ($res['success']) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => $res['error'] ?? 'Erro ao desconectar']);
    }
    exit;
}

if ($action === 'status') {
    // Verificar status e pegar QR Code se necessário
    // Endpoint: GET /instance/status
    $res = uazapiRequest('/instance/status', 'GET');

    if ($res['success'] && isset($res['body'])) {
        // Tenta detectar status em várias estruturas possíveis do Uazapi
        $status = 'unknown';

        if (isset($res['body']['instance']['status'])) {
            $status = $res['body']['instance']['status'];
        } elseif (isset($res['body']['status']) && is_string($res['body']['status'])) {
            $status = $res['body']['status'];
        } elseif (isset($res['body']['status']['connected']) && $res['body']['status']['connected'] === true) {
            $status = 'connected';
        } elseif (isset($res['body']['state'])) {
            $status = $res['body']['state'];
        }

        // Se ainda for unknown, verificar se o body é string direta (algumas versões)
        if ($status === 'unknown' && is_string($res['body'])) {
            $status = $res['body'];
        }

        // Tratamento do QR Code
        $qrCode = $res['body']['qr'] ?? $res['body']['qrcode'] ?? $res['body']['base64'] ?? $res['body']['instance']['qrcode'] ?? null;

        if ($qrCode) {
            // Verificar se já tem cabeçalho
            if (strpos($qrCode, 'data:image') === false) {
                // Remover quebras de linha se houver
                $qrCode = str_replace(["\r", "\n"], '', $qrCode);
                $qrCode = 'data:image/png;base64,' . $qrCode;
            }
        }

        // Tenta Extrair o Número Conectado
        $number = null;
        if ($status === 'connected' || $status === 'open') {
            if (isset($res['body']['instance']['owner'])) {
                $number = $res['body']['instance']['owner'];
            } elseif (isset($res['body']['instance']['profileName'])) {
                $number = $res['body']['instance']['profileName']; // Fallback name
            } elseif (isset($res['body']['user']['id'])) {
                $number = $res['body']['user']['id'];
            }
            // Formatar se for JID (55119999@s.whatsapp.net)
            if ($number && strpos($number, '@') !== false) {
                $number = explode('@', $number)[0];
            }
        }

        // Monta resposta normalizada
        $normalizedResponse = [
            'status' => $status,
            'qr' => $qrCode,
            'number' => $number
        ];


        // Retorna normalizado + raw para debug
        echo json_encode(['success' => true, 'status' => $normalizedResponse, 'raw' => $res['body']]);
        exit;
    } else {
        echo json_encode(['success' => false, 'error' => 'Falha ao verificar status']);
    }
    exit;
}

if ($action === 'list_groups') {
    // Listar Grupos
    // Endpoint: GET /group/list
    $res = uazapiRequest('/group/list', 'GET');

    if ($res['success'] && isset($res['body'])) {
        $groups = [];
        $raw = $res['body'];

        // Tenta identificar onde está a lista
        if (is_array($raw)) {
            // Verifica se é um array sequencial (lista direta)
            $isList = (array_keys($raw) === range(0, count($raw) - 1));

            if ($isList) {
                $groups = $raw;
            } else {
                // É associativo, tenta procurar chaves comuns
                if (isset($raw['groups']) && is_array($raw['groups'])) {
                    $groups = $raw['groups'];
                } elseif (isset($raw['data']) && is_array($raw['data'])) {
                    $groups = $raw['data'];
                } elseif (isset($raw['items']) && is_array($raw['items'])) {
                    $groups = $raw['items'];
                } else {
                    // Fallback: Talvez retornou um único objeto? Encapsula em array
                    $groups = [$raw];
                }
            }
        }

        echo json_encode(['success' => true, 'groups' => $groups, 'raw' => $raw]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Falha ao listar grupos: ' . json_encode($res)]);
    }
    exit;
}

echo json_encode(['success' => false, 'error' => 'Ação inválida']);
