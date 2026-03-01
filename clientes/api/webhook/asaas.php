<?php
// api/webhook/asaas.php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/config.php';

// Log simple headers for debugging
$headers = getallheaders();
$sourceToken = $headers['asaas-access-token'] ?? ''; // Verifique se configurou token no Asaas

// Capture Payload
$json = file_get_contents('php://input');
$event = json_decode($json, true);

// Basic Validation
if (!$event || !isset($event['event'])) {
    http_response_code(400);
    exit;
}

// Log Incoming Event (Optional: create a logs table or file)
// file_put_contents(__DIR__ . '/asaas.log', date('Y-m-d H:i:s') . " - " . $json . "\n", FILE_APPEND);

$eventType = $event['event'];
$payment = $event['payment'];

$asaasId = $payment['id'];
$statusAsaas = $payment['status']; // PENDING, RECEIVED, OVERDUE, etc.
$value = $payment['value'];
$dueDate = $payment['dueDate'];
$paymentDate = $payment['paymentDate'] ?? null; // Can be null
$description = $payment['description'] ?? 'Assinatura';
$subscriptionId = $payment['subscription'] ?? null;
$customerId = $payment['customer'];

// 1. Find Company by Asaas Customer ID
$stmtComp = $conn->prepare("SELECT id FROM empresas WHERE asaas_customer_id = ?");
$stmtComp->bind_param("s", $customerId);
$stmtComp->execute();
$resComp = $stmtComp->get_result();

if ($resComp->num_rows === 0) {
    // Customer not found in our DB, maybe log warning
    http_response_code(200); // Return 200 to acknowledge receipt
    exit;
}

$company = $resComp->fetch_assoc();
$companyId = $company['id'];

// 2. Map Status
$statusMap = [
    'PENDING' => 'PENDENTE',
    'RECEIVED' => 'PAGO',
    'CONFIRMED' => 'PAGO',
    'OVERDUE' => 'VENCIDO',
    'REFUNDED' => 'CANCELADO',
    'RECEIVED_IN_CASH' => 'PAGO'
];

$finalStatus = $statusMap[$statusAsaas] ?? 'PENDENTE';

// 3. Find Internal Subscription ID (if exists)
$localSubId = null;
if ($subscriptionId) {
    $stmtSub = $conn->prepare("SELECT id FROM financeiro_assinaturas WHERE asaas_id = ?");
    $stmtSub->bind_param("s", $subscriptionId);
    $stmtSub->execute();
    $resSub = $stmtSub->get_result();
    if ($resSub->num_rows > 0) {
        $localSubId = $resSub->fetch_assoc()['id'];
    }
}

// 4. Insert or Update Entry in financeiro_lancamentos
// Check if this specific payment ID already exists
$stmtCheck = $conn->prepare("SELECT id FROM financeiro_lancamentos WHERE asaas_payment_id = ?");
$stmtCheck->bind_param("s", $asaasId);
$stmtCheck->execute();
$resCheck = $stmtCheck->get_result();

if ($resCheck->num_rows > 0) {
    // UPDATE
    $lancamentoId = $resCheck->fetch_assoc()['id'];
    
    // Only update if status changed or data changed
    $stmtUpd = $conn->prepare("UPDATE financeiro_lancamentos SET status = ?, data_pagamento = ?, valor = ? WHERE id = ?");
    $stmtUpd->bind_param("ssdi", $finalStatus, $paymentDate, $value, $lancamentoId);
    $stmtUpd->execute();
    
} else {
    // INSERT (Only if useful event)
    if (in_array($eventType, ['PAYMENT_CREATED', 'PAYMENT_RECEIVED', 'PAYMENT_OVERDUE', 'PAYMENT_UPDATED'])) {
        $tipo = $localSubId ? 'RECORRENCIA' : 'AVULSO';
        $formaPagamento = $payment['billingType'] ?? 'BOLETO';
        
        $stmtIns = $conn->prepare("INSERT INTO financeiro_lancamentos 
            (company_id, tipo, titulo, descricao, valor, data_vencimento, data_pagamento, status, forma_pagamento, asaas_payment_id, assinatura_id) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            
        $stmtIns->bind_param("isssdsssssi", 
            $companyId, 
            $tipo, 
            $description, 
            $description, 
            $value, 
            $dueDate, 
            $paymentDate, 
            $finalStatus, 
            $formaPagamento, 
            $asaasId, 
            $localSubId
        );
        $stmtIns->execute();
    }
}

// 5. If Payment Received, Ensure Access is Granted/Active
if ($finalStatus === 'PAGO' && $localSubId) {
    // Example: Could extend expiry date logic here if needed
}

http_response_code(200);
echo json_encode(['success' => true]);
?>
