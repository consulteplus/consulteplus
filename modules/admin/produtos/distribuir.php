<?php
// modules/admin/produtos/distribuir.php
$pageTitle = "Distribuir Produto";
require_once __DIR__ . '/../header.php';
require_once __DIR__ . '/../../../classes/ProductService.php';
require_once __DIR__ . '/../../../classes/ProductDistributionService.php';

checkPermission(['admin', 'superadmin']);

$produto_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$company_id = $_SESSION['company_id'];

// Services
$productService = new ProductService();
$distService = new ProductDistributionService();

// Mensagens
if (isset($_SESSION['success'])) {
    echo "<div class='alert alert-success m-3'>{$_SESSION['success']}</div>";
    unset($_SESSION['success']);
}
if (isset($_SESSION['error'])) {
    echo "<div class='alert alert-danger m-3'>{$_SESSION['error']}</div>";
    unset($_SESSION['error']);
}

// Prepare Select2 CSS (Safe to include here)
?>
<!-- Select2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link rel="stylesheet"
    href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
<style>
    /* Fix Select2 Bootstrap 5 Height */
    .select2-container .select2-selection--single {
        height: 38px !important;
        padding-top: 5px;
    }
</style>
<?php

// ---------------------------------------------------------
// FLUXO 1: SELECIONAR PRODUTO (Se ID == 0)
// ---------------------------------------------------------
if ($produto_id === 0) {
    // Carregar produtos para o select inicial
    $produtos = $productService->listar($company_id, 100);
    ?>

    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow border-0">
                    <div class="card-body p-5 text-center">
                        <i class="bi bi-share display-1 text-primary mb-3"></i>
                        <h3 class="mb-4">Distribuir Produto</h3>
                        <p class="text-muted mb-4">Selecione qual produto você deseja gerenciar:</p>

                        <form action="" method="GET">
                            <div class="mb-4 text-start">
                                <label class="form-label fw-bold">Buscar Produto</label>
                                <select name="id" id="selectProduto" class="form-select form-select-lg" required>
                                    <option value="">Digite para buscar...</option>
                                    <?php foreach ($produtos as $p): ?>
                                        <option value="<?php echo $p['id']; ?>">
                                            <?php echo htmlspecialchars($p['titulo']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-primary w-100 btn-lg">
                                Continuar <i class="bi bi-arrow-right ms-2"></i>
                            </button>
                        </form>

                        <div class="mt-3">
                            <a href="<?php echo BASE_URL; ?>admin/produtos"
                                class="text-decoration-none text-muted">Cancelar</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php
    require_once __DIR__ . '/../../../includes/footer.php';
    ?>
    <!-- Scripts After Footer (Correct Order: jQuery > Select2 > Init) -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function () {
            $('#selectProduto').select2({
                theme: 'bootstrap-5',
                placeholder: "Selecione um produto",
                allowClear: true
            });
            // Focus
            $('#selectProduto').select2('open');
        });
    </script>
    <?php
    exit;
}

// ---------------------------------------------------------
// FLUXO 2: GERENCIAR DISTRIBUIÇÃO (ID > 0)
// ---------------------------------------------------------

// Buscar Produto
$mentoria = $productService->buscar($produto_id, $company_id);

if (!$mentoria) {
    echo "<div class='container py-5'><div class='alert alert-danger'>Produto não encontrado.</div></div>";
    require_once __DIR__ . '/../../../includes/footer.php';
    exit;
}

// Processar Ações (Add/Remove)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    try {
        if ($action === 'add') {
            $company_to_add = intval($_POST['company_id']);
            if ($company_to_add > 0) {
                // Service: Conceder Acesso
                $msg = $distService->concederAcesso($produto_id, $company_to_add);
                $_SESSION['success'] = $msg;
            }
        } elseif ($action === 'remove') {
            $access_id = intval($_POST['access_id']);
            $distService->removerAcesso($access_id, $produto_id);
            $_SESSION['success'] = "Acesso removido com sucesso.";
        }
    } catch (Exception $e) {
        $_SESSION['error'] = "Erro: " . $e->getMessage();
    }

    // Redirect to clear POST
    echo "<script>window.location.href='" . BASE_URL . "admin/produtos/distribuir?id=$produto_id';</script>";
    exit;
}

// Carregar Dados
$access_list = $distService->listarAcessos($produto_id);
$available_companies = $distService->listarEmpresasDisponiveis($produto_id);

?>

<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-10">

            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h5 class="mb-0 fw-bold text-gray-800">Distribuir:
                        <?php echo htmlspecialchars($mentoria['titulo']); ?></h5>
                    <p class="text-muted small mb-0">Gerencie quais empresas têm acesso a este conteúdo.</p>
                </div>
                <div>
                    <a href="<?php echo BASE_URL; ?>admin/produtos/distribuir"
                        class="btn btn-outline-primary btn-sm me-2">
                        <i class="bi bi-search me-1"></i>Trocar Produto
                    </a>
                    <a href="<?php echo BASE_URL; ?>admin/produtos" class="btn btn-outline-secondary btn-sm">
                        <i class="bi bi-arrow-left me-2"></i>Voltar
                    </a>
                </div>
            </div>

            <!-- Billing Alert -->
            <?php if ($mentoria['valor'] > 0 && ($mentoria['tipo_cobranca'] ?? 'recorrente') === 'recorrente'): ?>
                <div class="alert alert-info d-flex align-items-center border-0 shadow-sm mb-4" role="alert">
                    <i class="bi bi-cash-coin fs-4 me-3"></i>
                    <div>
                        <strong>Atenção:</strong> Este produto tem um valor de
                        <span class="badge bg-white text-info border mx-1">R$
                            <?php echo number_format($mentoria['valor'], 2, ',', '.'); ?> /
                            <?php echo $mentoria['ciclo']; ?></span>.
                        Ao adicionar uma empresa, uma assinatura será gerada automaticamente no Asaas.
                    </div>
                </div>
            <?php endif; ?>

            <div class="row g-4">
                <!-- Add Access Card -->
                <div class="col-md-5">
                    <div class="card shadow border-0 h-100">
                        <div class="card-header bg-white py-3">
                            <h6 class="mb-0 fw-bold text-primary"><i class="bi bi-plus-circle me-2"></i>Conceder Novo
                                Acesso</h6>
                        </div>
                        <div class="card-body">
                            <form method="POST">
                                <input type="hidden" name="action" value="add">
                                <div class="mb-3">
                                    <label class="form-label text-muted small text-uppercase fw-bold">Buscar
                                        Empresa</label>
                                    <select name="company_id" id="selectEmpresa" class="form-select" required>
                                        <option value="">Selecione...</option>
                                        <?php foreach ($available_companies as $c): ?>
                                            <option value="<?php echo $c['id']; ?>">
                                                <?php echo htmlspecialchars($c['nome']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <div class="form-text mt-2">
                                        <i class="bi bi-info-circle me-1"></i> Digite o nome para buscar.
                                    </div>
                                </div>
                                <div class="d-grid">
                                    <button type="submit" class="btn btn-primary" <?php echo empty($available_companies) ? 'disabled' : ''; ?>>
                                        Conceder Acesso
                                    </button>
                                </div>
                            </form>

                            <?php if (empty($available_companies)): ?>
                                <div class="alert alert-warning mt-3 small">
                                    Todas as empresas ativas já possuem acesso a este produto.
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- List Access Card -->
                <div class="col-md-7">
                    <div class="card shadow border-0 h-100">
                        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                            <h6 class="mb-0 fw-bold text-success"><i class="bi bi-people me-2"></i>Empresas com Acesso
                            </h6>
                            <span class="badge bg-light text-dark border"><?php echo count($access_list); ?></span>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive" style="max-height: 400px;">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="bg-light sticky-top">
                                        <tr>
                                            <th class="ps-4">Empresa</th>
                                            <th>Data Inicio</th>
                                            <th class="text-end pe-4">Ação</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (!empty($access_list)): ?>
                                            <?php foreach ($access_list as $acc): ?>
                                                <tr>
                                                    <td class="ps-4 fw-semibold text-dark">
                                                        <?php echo htmlspecialchars($acc['company_name']); ?>
                                                    </td>
                                                    <td class="text-muted small">
                                                        <?php echo date('d/m/Y H:i', strtotime($acc['created_at'])); ?>
                                                    </td>
                                                    <td class="text-end pe-4">
                                                        <form method="POST"
                                                            onsubmit="return confirm('Tem certeza? Isso removerá o acesso desta empresa.');"
                                                            class="d-inline">
                                                            <input type="hidden" name="action" value="remove">
                                                            <input type="hidden" name="access_id"
                                                                value="<?php echo $acc['access_id']; ?>">
                                                            <button type="submit" class="btn btn-sm btn-outline-danger"
                                                                title="Remover Acesso">
                                                                <i class="bi bi-trash"></i>
                                                            </button>
                                                        </form>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="3" class="text-center py-5 text-muted">
                                                    <i class="bi bi-inbox d-block fs-2 mb-2"></i>
                                                    Nenhuma empresa com acesso ainda.
                                                </td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
require_once __DIR__ . '/../../../includes/footer.php';
?>
<!-- Scripts After Footer (Correct Order) -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function () {
        $('#selectEmpresa').select2({
            theme: 'bootstrap-5',
            placeholder: "Digite o nome da empresa...",
            allowClear: true,
            width: '100%'
        });
    });
</script>