<?php
$pageTitle = "Nova Assinatura";
require_once __DIR__ . '/../header.php';
require_once __DIR__ . '/../../../includes/services/AsaasService.php';
checkPermission(['superadmin']);

// Fetch Companies for Dropdown
$companies = $conn->query("SELECT id, nome, documento, asaas_customer_id FROM empresas WHERE ativo = 1 ORDER BY nome ASC");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $companyId = intval($_POST['company_id']);
    $value = floatval(str_replace(',', '.', $_POST['value']));
    $cycle = $_POST['cycle']; // MONTHLY, QUARTERLY, etc.
    $nextDueDate = $_POST['next_due_date'];
    $description = sanitize($_POST['description']);
    $customerEmail = sanitize($_POST['customer_email']);
    $billingType = $_POST['billing_type'] ?? 'BOLETO'; // New Field

    if ($companyId && $value > 0 && $nextDueDate && $customerEmail) {
        // 1. Get Company Details
        $stmt = $conn->prepare("SELECT nome, documento, asaas_customer_id FROM empresas WHERE id = ?");
        $stmt->bind_param("i", $companyId);
        $stmt->execute();
        $company = $stmt->get_result()->fetch_assoc();

        $asaasService = new AsaasService();
        $customerId = $company['asaas_customer_id'];

        // 2. Create Customer in Asaas if not exists
        if (empty($customerId)) {
            // Use manual email input
            $customerResult = $asaasService->createCustomer($company['nome'], $company['documento'], $customerEmail);

            if ($customerResult['success']) {
                $customerId = $customerResult['id'];
                // Update local DB
                $updateStmt = $conn->prepare("UPDATE empresas SET asaas_customer_id = ? WHERE id = ?");
                $updateStmt->bind_param("si", $customerId, $companyId);
                $updateStmt->execute();
            } else {
                $error = "Erro ao criar cliente no Asaas: " . $customerResult['error'];
            }
        } else {
            // Se já existe, ATUALIZA para garantir que tem CPF/CNPJ e Email
            $asaasService->updateCustomer($customerId, $company['nome'], $company['documento'], $customerEmail);
        }

        // 3. Create Subscription
        if (!isset($error)) {
            $subResult = $asaasService->createSubscription($customerId, $value, $cycle, $nextDueDate, $description, $billingType);

            if ($subResult['success']) {
                $asaasSubId = $subResult['data']['id'];
                $status = strtoupper($subResult['data']['status']); // ACTIVE

                // 4. Save to Local DB
                $insertStmt = $conn->prepare("INSERT INTO financeiro_assinaturas (company_id, asaas_id, status, valor, ciclo, next_due_date, billing_type, descricao) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                $insertStmt->bind_param("issdssss", $companyId, $asaasSubId, $status, $value, $cycle, $nextDueDate, $billingType, $description);

                if ($insertStmt->execute()) {
                    $_SESSION['success'] = "Assinatura criada com sucesso!";
                    // Redirect to avoid resubmission
                    echo "<script>window.location.href = '" . BASE_URL . "admin/financeiro';</script>";
                    exit;
                } else {
                    $error = "Erro ao salvar no banco local: " . $conn->error;
                }
            } else {
                $error = "Erro ao criar assinatura no Asaas: " . $subResult['error'];
            }
        }

    } else {
        $error = "Preencha todos os campos obrigatórios.";
    }
}
?>

<div class="container-xxl flex-grow-1 container-p-y">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold py-3 mb-0">Nova Assinatura</h4>
        <a href="<?php echo BASE_URL; ?>admin/financeiro" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-2"></i>Voltar
        </a>
    </div>

    <!-- Error/Success Alerts -->
    <?php if (isset($error)): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            <?php echo $error; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="row">
        <div class="col-md-8 col-lg-6 mx-auto">
            <div class="card mb-4">
                <h5 class="card-header">Detalhes da Cobrança</h5>
                <div class="card-body">
                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label">Empresa / Cliente</label>
                            <select name="company_id" class="form-select" required>
                                <option value="">Selecione...</option>
                                <?php while ($c = $companies->fetch_assoc()): ?>
                                    <option value="<?php echo $c['id']; ?>">
                                        <?php echo htmlspecialchars($c['nome']); ?>
                                        (
                                        <?php echo htmlspecialchars($c['documento']); ?>)
                                    </option>
                                <?php endwhile; ?>
                            </select>
                            <div class="form-text">O cliente será criado automaticamente no Asaas se não existir.</div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">E-mail para Cobrança</label>
                            <input type="email" name="customer_email" class="form-control" required
                                placeholder="financeiro@empresa.com">
                            <div class="form-text">Obrigatório para registrar o cliente no Asaas.</div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Valor (R$)</label>
                                <div class="input-group input-group-merge">
                                    <span class="input-group-text">R$</span>
                                    <input type="number" step="0.01" name="value" class="form-control"
                                        placeholder="199.90" required>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Ciclo de Cobrança</label>
                                <select name="cycle" class="form-select" required>
                                    <option value="MONTHLY" selected>Mensal</option>
                                    <option value="QUARTERLY">Trimestral</option>
                                    <option value="SEMIANNUALLY">Semestral</option>
                                    <option value="YEARLY">Anual</option>
                                </select>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Forma de Pagamento</label>
                            <select name="billing_type" class="form-select" required>
                                <option value="BOLETO" selected>Boleto Bancário</option>
                                <option value="PIX">Pix</option>
                                <option value="CREDIT_CARD">Cartão de Crédito</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Data do Primeiro Vencimento</label>
                            <input type="date" name="next_due_date" class="form-control"
                                value="<?php echo date('Y-m-d', strtotime('+3 days')); ?>" required>
                            <div class="form-text">A primeira cobrança será gerada para esta data.</div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Descrição (Opcional)</label>
                            <textarea name="description" class="form-control" rows="2"
                                placeholder="Ex: Mensalidade Plano Enterprise"></textarea>
                        </div>

                        <div class="mt-4 d-grid">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="bi bi-check-lg me-2"></i>Criar Assinatura
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../../../includes/footer.php'; ?>