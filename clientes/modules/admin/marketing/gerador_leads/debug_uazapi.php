<?php
require_once __DIR__ . '/../../../../config/config.php';
require_once __DIR__ . '/api_whatsapp.php';

// Force action to status and simulate request
$_GET['action'] = 'status';

// Re-running the logic manually since we can't easily include and execute the script's logic if it's structured to run on file load.
// Actually, looking at api_whatsapp.php, it runs logic based on $action.
// Let's just use the function defined there if possible, but it's simpler to just copy the request logic for a standalone test.

$configFile = __DIR__ . '/../../../../config/uazapi_config.json';
$config = json_decode(file_get_contents($configFile), true);
$baseUrl = rtrim($config['base_url'], '/');
$apiToken = $config['api_token'];

$ch = curl_init($baseUrl . '/instance/status');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['token: ' . $apiToken]);
$response = curl_exec($ch);
curl_close($ch);

echo "Raw Response:\n" . $response . "\n";
