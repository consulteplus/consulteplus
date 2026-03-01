<?php
$pageTitle = "Meu Perfil e Configurações";
require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../includes/services/AsaasService.php';

// Force login check
checkAuth();

$userId = $_SESSION['user_id'];
$userCompanyId = $_SESSION['company_id'] ?? 0;
$userType = $_SESSION['tipo'];

// 1. Process Profile Update
$successMsg = '';
$errorMsg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $nome = sanitize($_POST['nome']);
    $email = sanitize($_POST['email']);
    $senha = $_POST['senha'];
    $confirmaSenha = $_POST['confirma_senha'];

    if ($senha && $senha !== $confirmaSenha) {
        $errorMsg = "As senhas não conferem.";
    } else {
        $sql = "UPDATE users SET nome = ?, email = ?";
        $types = "ss";
        $params = [$nome, $email];

        if ($senha) {
            $sql .= ", senha = ?";
            $types .= "s";
            $params[] = password_hash($senha, PASSWORD_DEFAULT);
        }

        $sql .= " WHERE id = ?";
        $types .= "i";
        $params[] = $userId;

        $stmt = $conn->prepare($sql);
        $stmt->bind_param($types, ...$params);

        if ($stmt->execute()) {
            $_SESSION['nome'] = $nome; // Update session
            $successMsg = "Perfil atualizado com sucesso!";
        } else {
            $errorMsg = "Erro ao atualizar perfil.";
        }
    }
}

// 2. Fetch User Data
$stmtUser = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmtUser->bind_param("i", $userId);
$stmtUser->execute();
$user = $stmtUser->get_result()->fetch_assoc();

// 3. Fetch Subscription Data (Only for Clients/Linked Companies)
$subscription = null;
$pendingPayments = [];
$asaasError = null;

if ($userCompanyId) {
    // Get Company Asaas ID
    $stmtComp = $conn->prepare("SELECT asaas_customer_id FROM empresas WHERE id = ?");
    $stmtComp->bind_param("i", $userCompanyId);
    $stmtComp->execute();
    $company = $stmtComp->get_result()->fetch_assoc();

    if ($company && $company['asaas_customer_id']) {
        $asaasService = new AsaasService();
        $customerId = $company['asaas_customer_id'];

        // Get Pending Payments
        $paymentsResult = $asaasService->listPendingPayments($customerId);
        if ($paymentsResult['success']) {
            $pendingPayments = $paymentsResult['data']['data'];
        } else {
            $asaasError = $paymentsResult['error'];
        }

        // Get Active Subscription (Local DB)
        $stmtSub = $conn->prepare("SELECT * FROM financeiro_assinaturas WHERE company_id = ? AND status = 'ACTIVE' LIMIT 1");
        $stmtSub->bind_param("i", $userCompanyId);
        $stmtSub->execute();
        $subscription = $stmtSub->get_result()->fetch_assoc();
    }
}

// 4. Fetch Purchased Products (Compras)
$myProducts = [];
$stmtProd = $conn->prepare("
    SELECT p.id, p.titulo, p.imagem_capa, m.data_inicio
    FROM mentoria_matriculas m
    JOIN mentoria_produtos p ON m.produto_id = p.id
    WHERE m.user_id = ?
    ORDER BY m.data_inicio DESC
");
$stmtProd->bind_param("i", $userId);
$stmtProd->execute();
$resProd = $stmtProd->get_result();
while ($p = $resProd->fetch_assoc()) {
    $myProducts[] = $p;
}
?>

<div class="container-fluid py-4">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Meu Perfil</h1>
    </div>

    <?php if ($successMsg): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2"></i><?php echo $successMsg; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if ($errorMsg): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle me-2"></i><?php echo $errorMsg; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="row">
        <!-- Sidebar Tabs -->
        <div class="col-md-3 mb-4">
            <div class="list-group shadow-sm">
                <a href="#perfil" class="list-group-item list-group-item-action active d-flex align-items-center py-3"
                    data-bs-toggle="list">
                    <i class="bi bi-person-circle fs-5 me-3"></i>
                    <div>
                        <div class="fw-bold">Dados Pessoais</div>
                        <small class="text-muted">Editar informações</small>
                    </div>
                </a>
                <?php if ($userType === 'cliente'): ?>
                    <a href="#assinatura" class="list-group-item list-group-item-action d-flex align-items-center py-3"
                        data-bs-toggle="list">
                        <i class="bi bi-credit-card-2-front fs-5 me-3"></i>
                        <div>
                            <div class="fw-bold">Minha Assinatura</div>
                            <small class="text-muted">Planos e Faturas</small>
                        </div>
                    </a>
                    <a href="#compras" class="list-group-item list-group-item-action d-flex align-items-center py-3"
                        data-bs-toggle="list">
                        <i class="bi bi-bag-check fs-5 me-3"></i>
                        <div>
                            <div class="fw-bold">Minhas Compras</div>
                            <small class="text-muted">Histórico de produtos</small>
                        </div>
                    </a>
                <?php endif; ?>
            </div>
        </div>

        <!-- Content -->
        <div class="col-md-9">
            <div class="tab-content">

                <!-- PERFIL TAB -->
                <div class="tab-pane fade show active" id="perfil">
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-white py-3">
                            <h5 class="mb-0 fw-bold text-primary">Editar Perfil</h5>
                        </div>
                        <div class="card-body p-4">
                            <form method="POST">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Nome Completo</label>
                                        <input type="text" class="form-control" name="nome"
                                            value="<?php echo htmlspecialchars($user['nome']); ?>" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">E-mail</label>
                                        <input type="email" class="form-control" name="email"
                                            value="<?php echo htmlspecialchars($user['email']); ?>" required>
                                    </div>
                                    <div class="col-12">
                                        <hr class="my-4">
                                        <h6 class="text-muted mb-3"><i class="bi bi-shield-lock me-2"></i>Alterar Senha
                                            (Opcional)</h6>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Nova Senha</label>
                                        <input type="password" class="form-control" name="senha"
                                            placeholder="Deixe em branco para manter">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Confirmar Senha</label>
                                        <input type="password" class="form-control" name="confirma_senha"
                                            placeholder="Repita a nova senha">
                                    </div>
                                    <div class="col-12 text-end mt-4">
                                        <button type="submit" name="update_profile" class="btn btn-primary px-4">
                                            <i class="bi bi-save me-2"></i>Salvar Alterações
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- ASSINATURA TAB -->
                <?php if ($userType === 'cliente'): ?>
                    <div class="tab-pane fade" id="assinatura">
                        <div class="card shadow-sm border-0 mb-4">
                            <div class="card-body p-4">
                                <h5 class="fw-bold mb-4">Plano Atual</h5>
                                <?php if ($subscription): ?>
                                    <div class="d-flex align-items-center p-3 border rounded bg-light">
                                        <div class="flex-grow-1">
                                            <h2 class="text-primary mb-0">R$
                                                <?php echo number_format($subscription['valor'], 2, ',', '.'); ?>
                                            </h2>
                                            <p class="text-muted mb-0">Cobrança
                                                <?php echo $subscription['ciclo'] == 'MONTHLY' ? 'Mensal' : $subscription['ciclo']; ?>
                                            </p>
                                        </div>
                                        <div class="text-end">
                                            <span class="badge bg-success fs-6 mb-2">ATIVO</span>
                                            <div class="small text-muted">Vence em:
                                                <?php echo date('d/m/Y', strtotime($subscription['next_due_date'])); ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php else: ?>
                                    <div class="alert alert-secondary">
                                        <i class="bi bi-info-circle me-2"></i>Nenhuma assinatura ativa encontrada.
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="card shadow-sm border-0">
                            <div class="card-header bg-white py-3">
                                <h5 class="mb-0 fw-bold">Faturas Pendentes</h5>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="bg-light">
                                        <tr>
                                            <th class="ps-4">Vencimento</th>
                                            <th>Valor</th>
                                            <th>Descrição</th>
                                            <th>Status</th>
                                            <th class="text-end pe-4">Ação</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (empty($pendingPayments)): ?>
                                            <tr>
                                                <td colspan="5" class="text-center py-5 text-muted">Nenhuma fatura pendente.
                                                </td>
                                            </tr>
                                        <?php else: ?>
                                            <?php foreach ($pendingPayments as $payment): ?>
                                                <tr>
                                                    <td class="ps-4"><?php echo date('d/m/Y', strtotime($payment['dueDate'])); ?>
                                                    </td>
                                                    <td class="fw-bold text-danger">R$
                                                        <?php echo number_format($payment['value'], 2, ',', '.'); ?>
                                                    </td>
                                                    <td><?php echo htmlspecialchars($payment['description'] ?? 'Assinatura'); ?>
                                                    </td>
                                                    <td><span class="badge bg-warning text-dark">Pendente</span></td>
                                                    <td class="text-end pe-4">
                                                        <button class="btn btn-sm btn-primary"
                                                            onclick="window.location.href='<?php echo BASE_URL; ?>modules/financeiro/minha_assinatura.php'">
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

                    <!-- COMPRAS TAB -->
                    <div class="tab-pane fade" id="compras">
                        <div class="card shadow-sm border-0">
                            <div class="card-header bg-white py-3">
                                <h5 class="mb-0 fw-bold text-success"><i class="bi bi-cart-check me-2"></i>Meus Produtos
                                </h5>
                            </div>
                            <div class="card-body p-0">
                                <?php if (empty($myProducts)): ?>
                                    <div class="p-5 text-center text-muted">
                                        <i class="bi bi-basket display-4 d-block mb-3"></i>
                                        <p>Você ainda não possui produtos ativos.</p>
                                    </div>
                                <?php else: ?>
                                    <div class="list-group list-group-flush">
                                        <?php foreach ($myProducts as $prod): ?>
                                            <div class="list-group-item p-4 d-flex align-items-center">
                                                <div class="me-4 rounded bg-secondary bg-opacity-10 d-flex align-items-center justify-content-center"
                                                    style="width: 60px; height: 60px;">
                                                    <?php if (!empty($prod['imagem_capa'])): ?>
                                                        <img src="<?php echo htmlspecialchars($prod['imagem_capa']); ?>"
                                                            class="img-fluid rounded" style="max-height: 60px;">
                                                    <?php else: ?>
                                                        <i class="bi bi-box-seam fs-3 text-secondary"></i>
                                                    <?php endif; ?>
                                                </div>
                                                <div class="flex-grow-1">
                                                    <h5 class="fw-bold mb-1"><?php echo htmlspecialchars($prod['titulo']); ?></h5>
                                                    <div class="text-muted small">
                                                        <i class="bi bi-calendar-event me-1"></i> Adquirido em:
                                                        <?php echo date('d/m/Y', strtotime($prod['data_inicio'])); ?>
                                                    </div>
                                                </div>
                                                <a href="<?php echo BASE_URL; ?>produtos/ver.php?id=<?php echo $prod['id']; ?>"
                                                    class="btn btn-outline-primary btn-sm">
                                                    Acessar
                                                </a>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>