<?php
$pageTitle = "Minha Assinatura";
require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../includes/services/AsaasService.php';

// Apenas clientes ou superadmin
if (!isset($_SESSION['tipo']) || ($_SESSION['tipo'] !== 'cliente' && $_SESSION['tipo'] !== 'superadmin')) {
    header("Location: " . BASE_URL . "login.php");
    exit;
}

$userCompanyId = $_SESSION['company_id'] ?? 0;
// Buscar ID Asaas da empresa
$stmt = $conn->prepare("SELECT asaas_customer_id, nome, documento FROM empresas WHERE id = ?");
$stmt->bind_param("i", $userCompanyId);
$stmt->execute();
$company = $stmt->get_result()->fetch_assoc();

$asaasService = new AsaasService();
$customerId = $company['asaas_customer_id'];
$pendingPayments = [];
$subscription = null;
$error = null;

if ($customerId) {
    // 1. Buscar Faturas Pendentes
    $paymentsResult = $asaasService->listPendingPayments($customerId);
    if ($paymentsResult['success']) {
        $pendingPayments = $paymentsResult['data']['data'];
    } else {
        $error = "Erro ao buscar faturas: " . $paymentsResult['error'];
    }

    // 2. Buscar Assinatura Ativa (Opcional, pegar do banco local)
    $stmtSub = $conn->prepare("SELECT * FROM financeiro_assinaturas WHERE company_id = ? AND status = 'ACTIVE' LIMIT 1");
    $stmtSub->bind_param("i", $userCompanyId);
    $stmtSub->execute();
    $subscription = $stmtSub->get_result()->fetch_assoc();
} else {
    $error = "Sua empresa ainda não possui cadastro financeiro vinculado.";
}
?>

<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4">Minha Assinatura</h4>

    <?php if ($error): ?>
        <div class="alert alert-warning">
            <?php echo $error; ?>
        </div>
    <?php endif; ?>

    <div class="row">
        <!-- Status da Assinatura -->
        <div class="col-md-4 mb-4">
            <div class="card h-100">
                <div class="card-body">
                    <h5 class="card-title">Plano Atual</h5>
                    <?php if ($subscription): ?>
                        <h2 class="text-primary mb-0">R$
                            <?php echo number_format($subscription['valor'], 2, ',', '.'); ?>
                        </h2>
                        <p class="text-muted">Cobrança
                            <?php echo $subscription['ciclo'] == 'MONTHLY' ? 'Mensal' : $subscription['ciclo']; ?>
                        </p>
                        <span class="badge bg-success">ATIVO</span>
                        <hr>
                        <small class="text-muted">Próximo Vencimento:
                            <?php echo date('d/m/Y', strtotime($subscription['next_due_date'])); ?>
                        </small>
                    <?php else: ?>
                        <p class="text-muted">Nenhuma assinatura ativa encontrada.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Faturas em Aberto -->
        <div class="col-md-8 mb-4">
            <div class="card h-100">
                <h5 class="card-header">Faturas Pendentes</h5>
                <div class="table-responsive text-nowrap">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Vencimento</th>
                                <th>Valor</th>
                                <th>Descrição</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($pendingPayments)): ?>
                                <tr>
                                    <td colspan="4" class="text-center py-4">Nenhuma fatura pendente.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($pendingPayments as $payment): ?>
                                    <tr>
                                        <td>
                                            <?php echo date('d/m/Y', strtotime($payment['dueDate'])); ?>
                                        </td>
                                        <td class="fw-bold text-danger">R$
                                            <?php echo number_format($payment['value'], 2, ',', '.'); ?>
                                        </td>
                                        <td>
                                            <?php echo htmlspecialchars($payment['description'] ?? 'Assinatura'); ?>
                                        </td>
                                        <td>
                                            <button class="btn btn-sm btn-primary"
                                                onclick="openPaymentModal('<?php echo $payment['id']; ?>', '<?php echo $payment['value']; ?>')">
                                                <i class="bi bi-credit-card-2-front me-1"></i> Pagar
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Payment Modal -->
<div class="modal fade" id="paymentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Realizar Pagamento</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="modalPaymentId">
                <h3 class="text-center text-primary mb-4" id="modalPaymentValue">R$ 0,00</h3>

                <ul class="nav nav-pills nav-fill mb-4" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" data-bs-toggle="pill" data-bs-target="#tab-pix" type="button"
                            role="tab" onclick="loadPix()">
                            <i class="bi bi-qr-code-scan me-2"></i>Pix
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" data-bs-toggle="pill" data-bs-target="#tab-boleto" type="button"
                            role="tab" onclick="loadBoleto()">
                            <i class="bi bi-upc-scan me-2"></i>Boleto
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" data-bs-toggle="pill" data-bs-target="#tab-card" type="button"
                            role="tab">
                            <i class="bi bi-credit-card me-2"></i>Cartão de Crédito
                        </button>
                    </li>
                </ul>

                <div class="tab-content">
                    <!-- PIX -->
                    <div class="tab-pane fade show active text-center" id="tab-pix" role="tabpanel">
                        <div id="pixLoading" class="spinner-border text-primary" role="status" style="display:none;">
                        </div>
                        <div id="pixContainer" style="display:none;">
                            <p class="mb-2">Escaneie o QR Code abaixo:</p>
                            <img id="pixQrImage" src="" class="img-fluid mb-3"
                                style="max-width: 200px; border: 1px solid #ddd; padding: 10px; border-radius: 8px;">
                            <div class="input-group mb-3">
                                <input type="text" class="form-control" id="pixCopyPaste" readonly>
                                <button class="btn btn-outline-secondary" type="button"
                                    onclick="copyToClipboard('pixCopyPaste')">Copiar</button>
                            </div>
                        </div>
                    </div>

                    <!-- BOLETO -->
                    <div class="tab-pane fade text-center" id="tab-boleto" role="tabpanel">
                        <div id="boletoLoading" class="spinner-border text-primary" role="status" style="display:none;">
                        </div>
                        <div id="boletoContainer" style="display:none;">
                            <p class="mb-2">Utilize a linha digitável abaixo:</p>
                            <div class="input-group mb-3">
                                <input type="text" class="form-control font-monospace" id="boletoLine" readonly>
                                <button class="btn btn-outline-secondary" type="button"
                                    onclick="copyToClipboard('boletoLine')">Copiar</button>
                            </div>
                            <a id="boletoPdfLink" href="" target="_blank" class="btn btn-outline-primary">
                                <i class="bi bi-file-earmark-pdf me-2"></i>Baixar PDF
                            </a>
                        </div>
                    </div>

                    <!-- CARTÃO -->
                    <div class="tab-pane fade" id="tab-card" role="tabpanel">
                        <form id="cardForm" onsubmit="processCardPayment(event)">
                            <div class="row">
                                <div class="col-12 mb-3">
                                    <label class="form-label">Nome no Cartão</label>
                                    <input type="text" class="form-control" id="cardHolderName" required>
                                </div>
                                <div class="col-12 mb-3">
                                    <label class="form-label">Número do Cartão</label>
                                    <input type="text" class="form-control" id="cardNumber"
                                        placeholder="0000 0000 0000 0000" required>
                                </div>
                                <div class="col-4 mb-3">
                                    <label class="form-label">Validade (Mês/Ano)</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" id="expiryMonth" placeholder="MM"
                                            maxlength="2" required>
                                        <span class="input-group-text">/</span>
                                        <input type="text" class="form-control" id="expiryYear" placeholder="AAAA"
                                            maxlength="4" required>
                                    </div>
                                </div>
                                <div class="col-4 mb-3">
                                    <label class="form-label">CVV</label>
                                    <input type="text" class="form-control" id="ccv" placeholder="123" maxlength="4"
                                        required>
                                </div>
                            </div>

                            <!-- Hidden info needed for Asaas (holder info basically matches company info usually) -->
                            <div class="alert alert-info small"><i class="bi bi-lock-fill"></i> Seus dados são
                                processados de forma segura.</div>

                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary" id="btnPayCard">Pagar agora</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function openPaymentModal(id, value) {
        document.getElementById('modalPaymentId').value = id;
        document.getElementById('modalPaymentValue').innerText = parseFloat(value).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });
        var modal = new bootstrap.Modal(document.getElementById('paymentModal'));
        modal.show();

        // Reset tabs
        document.querySelector('[data-bs-target="#tab-pix"]').click();
    }

    function loadPix() {
        const paymentId = document.getElementById('modalPaymentId').value;
        const container = document.getElementById('pixContainer');
        const loading = document.getElementById('pixLoading');

        container.style.display = 'none';
        loading.style.display = 'block';

        fetch('acoes_financeiro.php?action=get_pix&payment_id=' + paymentId)
            .then(response => response.json())
            .then(data => {
                loading.style.display = 'none';
                if (data.success) {
                    document.getElementById('pixQrImage').src = 'data:image/jpeg;base64,' + data.encodedImage;
                    document.getElementById('pixCopyPaste').value = data.payload;
                    container.style.display = 'block';
                } else {
                    alert('Erro ao gerar Pix: ' + data.error);
                }
            });
    }

    function loadBoleto() {
        const paymentId = document.getElementById('modalPaymentId').value;
        const container = document.getElementById('boletoContainer');
        const loading = document.getElementById('boletoLoading');

        container.style.display = 'none';
        loading.style.display = 'block';

        fetch('acoes_financeiro.php?action=get_boleto&payment_id=' + paymentId)
            .then(response => response.json())
            .then(data => {
                loading.style.display = 'none';
                if (data.success) {
                    document.getElementById('boletoLine').value = data.identificationField;
                    document.getElementById('boletoPdfLink').href = data.bankSlipUrl;
                    container.style.display = 'block';
                } else {
                    alert('Erro ao gerar Boleto: ' + data.error);
                }
            });
    }

    function processCardPayment(e) {
        e.preventDefault();
        const btn = document.getElementById('btnPayCard');
        btn.disabled = true;
        btn.innerHTML = 'Processando...';

        const paymentId = document.getElementById('modalPaymentId').value;
        const data = {
            paymentId: paymentId,
            holderName: document.getElementById('cardHolderName').value,
            number: document.getElementById('cardNumber').value,
            expiryMonth: document.getElementById('expiryMonth').value,
            expiryYear: document.getElementById('expiryYear').value,
            ccv: document.getElementById('ccv').value
        };

        fetch('acoes_financeiro.php?action=pay_credit_card', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data)
        })
            .then(response => response.json())
            .then(data => {
                btn.disabled = false;
                btn.innerHTML = 'Pagar agora';
                if (data.success) {
                    alert('Pagamento realizado com sucesso!');
                    location.reload();
                } else {
                    alert('Erro no pagamento: ' + (data.error || 'Verifique os dados e tente novamente.'));
                }
            });
    }

    function copyToClipboard(elementId) {
        const copyText = document.getElementById(elementId);
        copyText.select();
        document.execCommand("copy");
        alert("Copiado!");
    }
</script>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>