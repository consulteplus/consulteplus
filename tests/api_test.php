<?php
// api_test.php
// Script to verify API endpoints locally

// Configuration
$baseUrl = 'http://localhost/consulteplus/api/v1';
$apiKey = 'sk_crm_83b2ca0d57eb82212ff47d449d6b126483b'; // As defined in config.php

function callApi($endpoint, $method = 'GET', $data = []) {
    global $baseUrl, $apiKey;
    $url = $baseUrl . $endpoint;
    
    $ch = curl_init($url);
    
    $headers = [
        'X-API-KEY: ' . $apiKey,
        'Content-Type: application/json',
        'Accept: application/json'
    ];
    
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
    
    if ($method === 'POST' || $method === 'PUT') {
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    }
    
    // Disable SSL verification for local testing if needed
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    
    curl_close($ch);
    
    return [
        'code' => $httpCode,
        'body' => json_decode($response, true),
        'raw_body' => $response,
        'error' => $error
    ];
}

echo "Starting API Verification...\n";
echo "Base URL: $baseUrl\n";
echo "API Key: " . substr($apiKey, 0, 5) . "...\n\n";

// 1. Test List Leads
echo "[TEST 1] Listing Leads (GET /leads.php)...\n";
$res1 = callApi('/leads.php?limit=5');
if ($res1['code'] === 200) {
    echo "SUCCESS: " . count($res1['body']['data'] ?? []) . " leads found.\n";
} else {
    echo "FAILED: HTTP " . $res1['code'] . "\n";
    echo "Response: " . $res1['raw_body'] . "\n";
}
echo "------------------------------------------------\n";

// 2. Test Create Lead
echo "[TEST 2] Creating Lead (POST /leads.php)...\n";
$newLead = [
    'nome' => 'Test API User ' . time(),
    'email' => 'test_api_' . time() . '@example.com',
    'telefone' => '1199999' . rand(1000, 9999),
    'origem' => 'API Test'
];
$res2 = callApi('/leads.php', 'POST', $newLead);
if ($res2['code'] === 200 || $res2['code'] === 201) {
    echo "SUCCESS: Lead created/upserted.\n";
    $leadId = $res2['body']['data']['id'] ?? null;
    echo "Lead ID: $leadId\n";
} else {
    echo "FAILED: HTTP " . $res2['code'] . "\n";
    echo "Response: " . $res2['raw_body'] . "\n";
    $leadId = null;
}
echo "------------------------------------------------\n";

// 3. Test Create Deal (if lead created)
if ($leadId) {
    echo "[TEST 3] Creating Deal (POST /deals.php)...\n";
    $newDeal = [
        'titulo' => 'Deal from API Test',
        'valor_estimado' => 1234.56,
        'cliente_id' => $leadId,
        'funil_id' => 1,
        'etapa_id' => 1
    ];
    $res3 = callApi('/deals.php', 'POST', $newDeal);
    if ($res3['code'] === 200 || $res3['code'] === 201) {
        echo "SUCCESS: Deal created.\n";
        $dealId = $res3['body']['data']['id'] ?? null;
        echo "Deal ID: $dealId\n";
    } else {
        echo "FAILED: HTTP " . $res3['code'] . "\n";
        echo "Response: " . $res3['raw_body'] . "\n";
        $dealId = null;
    }
    echo "------------------------------------------------\n";
    
    // 4. Test Add Note to Deal
    if ($dealId) {
        echo "[TEST 4] Adding Note to Deal (POST /deals.php?action=add_note)...\n";
        $note = [
            'deal_id' => $dealId,
            'text' => 'This is a test note from API verification script.',
            'tipo' => 'obs'
        ];
        $res4 = callApi('/deals.php?action=add_note', 'POST', $note);
        if ($res4['code'] === 200) {
             echo "SUCCESS: Note added.\n";
        } else {
             echo "FAILED: HTTP " . $res4['code'] . "\n";
             echo "Response: " . $res4['raw_body'] . "\n";
        }
    }
}

echo "\nVerification script completed.\n";
