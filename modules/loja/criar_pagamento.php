<?php
session_start();
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/services/AsaasService.php';

// Verificação de Sessão
if (!isset($_SESSION['user_id'])) {
    header("Location: " . BASE_URL . "login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$produto_id = isset($_GET['produto_id']) ? (int) $_GET['produto_id'] : 0;

if (!$produto_id) {
    die("ID do produto inválido.");
}

// 1. Buscar Dados do Usuário (apenas para fallback de email/nome)
$stmt = $conn->prepare("SELECT nome, email FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

if (!$user) {
    die("Usuário não encontrado.");
}

$company_id = $_SESSION['company_id'];

// 1.5 Buscar Dados da Empresa (Documento e Asaas ID)
$stmtComp = $conn->prepare("SELECT id, nome, documento, asaas_customer_id FROM empresas WHERE id = ?");
$stmtComp->bind_param("i", $company_id);
$stmtComp->execute();
$company = $stmtComp->get_result()->fetch_assoc();

if (!$company) {
    die("Empresa vinculada não encontrada.");
}

// 2. Buscar Dados do Produto
$stmtProd = $conn->prepare("SELECT titulo, valor, descricao, ciclo FROM mentoria_produtos WHERE id = ?");
$stmtProd->bind_param("i", $produto_id);
$stmtProd->execute();
$prod = $stmtProd->get_result()->fetch_assoc();

if (!$prod) {
    die("Produto não encontrado.");
}
if ($prod['valor'] <= 0) {
    die("Este produto é gratuito ou não possui valor definido.");
}

$asaasService = new AsaasService();

// 3. Garantir Cliente (Empresa) no Asaas
$customerId = $company['asaas_customer_id'];

if (empty($customerId)) {
    // Usa dados da empresa para Nome/Doc, e do Usuario para Email (contato principal)
    $customerName = $company['nome'] ?: $user['nome'];
    $customerEmail = $user['email'];
    $customerDoc = preg_replace('/[^0-9]/', '', $company['documento']);

    if (empty($customerDoc)) {
        die("Empresa sem documento cadastrado. Atualize o perfil da empresa.");
    }

    $resCustomer = $asaasService->createCustomer($customerName, $customerDoc, $customerEmail);

    if (!$resCustomer['success']) {
        die("Erro ao criar cliente no Asaas: " . $resCustomer['error']);
    }

    $customerId = $resCustomer['id'];

    // Salva no banco local (Tabela empresas)
    $stmtUpd = $conn->prepare("UPDATE empresas SET asaas_customer_id = ? WHERE id = ?");
    $stmtUpd->bind_param("si", $customerId, $company_id);
    $stmtUpd->execute();
}

// 4. Criar Cobrança (Assinatura ou Pagamento Único)
$isRecurring = isset($prod['ciclo']) && in_array($prod['ciclo'], ['MONTHLY', 'QUARTERLY', 'SEMIANNUALLY', 'YEARLY']);

// Define data de vencimento para +1 dia (para boleto/pix) ou hoje? 
// Para checkout hosted, geralmente se define +3 dias ou +5 dias.
$dueDate = date('Y-m-d', strtotime('+3 days'));

if ($isRecurring) {
    // Cria Assinatura
    $resp = $asaasService->createSubscription(
        $customerId,
        $prod['valor'],
        $prod['ciclo'], // MONTHLY, etc
        $dueDate, // Data da primeira cobrança
        "Assinatura: " . $prod['titulo'],
        'UNDEFINED' // Permite escolher
    );
} else {
    // Cria Pagamento Único
    $resp = $asaasService->createPayment(
        $customerId,
        $prod['valor'],
        $dueDate,
        "Compra: " . $prod['titulo'],
        'UNDEFINED' // Permite usuario escolher billingType
    );
}

if ($resp['success']) {
    // Redireciona para o Link da Fatura (Hosted Page)
    $paymentLink = $resp['data']['invoiceUrl']; // Assinatura ou Pagamento retornam invoiceUrl?
    // Verificando retorno: Assinaturas retornam objeto subscription, pode não ter invoiceUrl direto no root.
    // Pagamentos (payments) retornam 'invoiceUrl'.
    // Para assinaturas, precisamos pegar a cobrança gerada OU apenas mandar para o link da assinatura se existir (geralmente nao tem link direto de pagto unico na resposta de assinatura).
    // CORREÇÃO: createSubscription no Asaas não retorna invoiceUrl da primeira cobrança direto no objeto subscription level root as vezes.
    // Mas geralmente retorna. Se não, precisariamos listar pagamentos.
    // Vamos assumir que retornou ou buscar.

    if (isset($resp['data']['invoiceUrl'])) {
        header("Location: " . $resp['data']['invoiceUrl']);
        exit;
    }
    // Fallback: Se for assinatura e não veio link, buscar cobranças pendentes
    if ($isRecurring && isset($resp['data']['id'])) {
        // Buscando a cobrança gerada para essa assinatura
        sleep(1); // Esperar propagar
        $cobrancas = $asaasService->listPendingPayments($customerId);
        if ($cobrancas['success'] && !empty($cobrancas['data']['data'])) {
            // Pega a primeira (mais recente)
            $link = $cobrancas['data']['data'][0]['invoiceUrl'];
            header("Location: " . $link);
            exit;
        }
    }

    die("Cobrança gerada, mas não foi possível obter o link de pagamento. Consulte seu email.");

} else {
    die("Erro ao gerar pagamento: " . $resp['error']);
}
?>