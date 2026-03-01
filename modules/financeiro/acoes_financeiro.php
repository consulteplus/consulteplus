<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/services/AsaasService.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}

$action = $_GET['action'] ?? '';
$asaasService = new AsaasService();

// Buscar dados do usuário logado para compor o CreditCardHolderInfo
$companyId = $_SESSION['company_id'];
$stmt = $conn->prepare("SELECT nome, documento, cep, endereco, numero FROM empresas WHERE id = ?");
$stmt->bind_param("i", $companyId);
$stmt->execute();
$company = $stmt->get_result()->fetch_assoc();

if ($action === 'get_pix') {
    $paymentId = $_GET['payment_id'];
    $result = $asaasService->getPixQrCode($paymentId);

    if ($result['success']) {
        echo json_encode([
            'success' => true,
            'encodedImage' => $result['data']['encodedImage'],
            'payload' => $result['data']['payload']
        ]);
    } else {
        echo json_encode(['success' => false, 'error' => $result['error']]);
    }
} elseif ($action === 'get_boleto') {
    $paymentId = $_GET['payment_id'];
    $result = $asaasService->getBoletoCode($paymentId);

    if ($result['success']) {
        echo json_encode([
            'success' => true,
            'identificationField' => $result['data']['identificationField'],
            'bankSlipUrl' => $result['data']['bankSlipUrl'] ?? '#' // URL do PDF vem em outra call as vezes, mas identificationField é o principal
        ]);
    } else {
        echo json_encode(['success' => false, 'error' => $result['error']]);
    }
} elseif ($action === 'pay_credit_card') {
    $input = json_decode(file_get_contents('php://input'), true);

    $paymentId = $input['paymentId'];
    $cardData = [
        'creditCard' => [
            'holderName' => $input['holderName'],
            'number' => $input['number'],
            'expiryMonth' => $input['expiryMonth'],
            'expiryYear' => $input['expiryYear'],
            'ccv' => $input['ccv']
        ],
        'creditCardHolderInfo' => [
            'name' => $company['nome'],
            'email' => $_SESSION['email'] ?? 'financeiro@consulteplus.com.br', // Fallback se não tiver na sessão
            'cpfCnpj' => $company['documento'],
            'postalCode' => $company['cep'] ?? '00000000',
            'addressNumber' => $company['numero'] ?? 'SN',
            'phone' => $company['telefone'] ?? '0000000000'
        ]
    ];

    $result = $asaasService->payWithCreditCard($paymentId, $cardData);

    if ($result['success']) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => $result['error']]);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid action']);
}
