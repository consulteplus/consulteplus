<?php
require_once __DIR__ . '/../../../config/config.php';
require_once __DIR__ . '/../../../config/database.php';

// Endpoint Público para receber dados do n8n
// Segurança: Idealmente validar um token no header ou na URL
// Como o n8n pode rodar em outro server, deixamos aberto por hora ou checamos um segredo simples

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method Not Allowed']);
    exit;
}

// Ler Payload
$inputJSON = file_get_contents('php://input');
$input = json_decode($inputJSON, true);

if (!$input) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid JSON']);
    exit;
}

// Suporte a lista direta ou envelopada
$leads = [];

$is_list = false;
if (is_array($input)) {
    $is_list = array_keys($input) === range(0, count($input) - 1);
}

if ($is_list) {
    // Se o input já é uma lista indexada [ {...}, {...} ]
    $leads = $input;
} elseif (isset($input['leads'])) {
    $leads = $input['leads'];
} elseif (isset($input['results'])) {
    $leads = $input['results'];
} else {
    // Objeto único
    $leads = [$input];
}

/* Campos esperados do n8n:
   - source (maps, linkedin...)
   - nome / title / name
   - telefone / phone / phone_number
   - email
   - endereco / address / full_address
   - site / website
   - instagram
*/

global $conn;
$stmt = $conn->prepare("INSERT INTO leads_scraped (source, nome, telefone, email, endereco, site, instagram, raw_data, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'pendente')");

$count = 0;

foreach ($leads as $lead) {
    // Normalizar dados
    // Tentar inferir source se não vier (ex: google_maps se tiver google_id)
    $source = $lead['source'] ?? (isset($lead['google_id']) ? 'google_maps' : 'api_unknown');

    $nome = $lead['nome'] ?? $lead['title'] ?? $lead['name'] ?? 'Sem Nome';
    $telefone = $lead['telefone'] ?? $lead['phone'] ?? $lead['phone_number'] ?? null;
    $email = $lead['email'] ?? null;
    $endereco = $lead['endereco'] ?? $lead['address'] ?? $lead['full_address'] ?? null;
    $site = $lead['site'] ?? $lead['website'] ?? null;
    $instagram = $lead['instagram'] ?? null;

    $raw_data = json_encode($lead);

    $stmt->bind_param('ssssssss', $source, $nome, $telefone, $email, $endereco, $site, $instagram, $raw_data);

    if ($stmt->execute()) {
        $count++;
    }
}

echo json_encode(['success' => true, 'inserted' => $count]);
