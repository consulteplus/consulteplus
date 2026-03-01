<?php
$pageTitle = "Meus Produtos";
require_once __DIR__ . '/../../includes/header.php';
// checkPermission(['admin', 'cliente']); // Permite ambos

$company_id = $_SESSION['company_id'];
$user_id = $_SESSION['user_id'];

$view = isset($_GET['view']) ? $_GET['view'] : 'meus_produtos';
$is_loja = ($view === 'loja');

// BUSCAR PRODUTOS
// Se for 'meus_produtos', filtra apenas o que tem acesso
$sql = "SELECT DISTINCT mp.*,
        (SELECT COUNT(*) FROM mentoria_matriculas mm WHERE mm.produto_id = mp.id AND mm.user_id = $user_id AND mm.ativo = 1) as matriculado,
        (SELECT COUNT(*) FROM mentoria_acesso_empresas mae WHERE mae.produto_id = mp.id AND mae.company_id = $company_id) as acesso_empresa
        FROM mentoria_produtos mp
        WHERE mp.ativo = 1 ";

if (!$is_loja) {
    // A query abaixo usa HAVING porque matriculado e acesso_empresa são aliases
    $sql .= " HAVING (matriculado > 0 OR acesso_empresa > 0) ";
}

$sql .= " ORDER BY mp.id DESC";
$result = $conn->query($sql);
?>

<div class="container py-4">
    <!-- Page Header -->
    <div class="mb-4">
        <div class="card premium-card border-0 shadow-sm">
            <div class="card-body p-4">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <h4 class="fw-bold text-dark mb-1">
                            <?php echo $is_loja ? 'Loja de Produtos' : 'Meus Produtos'; ?></h4>
                        <p class="text-muted mb-0">
                            <?php echo $is_loja ? 'Confira todos os produtos disponíveis para você.' : 'Acesse seus cursos e consultorias.'; ?>
                        </p>
                    </div>
                    <div class="col-md-6 text-md-end mt-3 mt-md-0">
                        <div class="btn-group shadow-sm">
                            <a href="?view=meus_produtos"
                                class="btn <?php echo !$is_loja ? 'btn-primary' : 'btn-outline-primary'; ?> fw-semibold">
                                <i class="bi bi-collection-play me-2"></i>Meus Produtos
                            </a>
                            <a href="?view=loja"
                                class="btn <?php echo $is_loja ? 'btn-primary' : 'btn-outline-primary'; ?> fw-semibold">
                                <i class="bi bi-shop me-2"></i>Loja
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php if ($result && $result->num_rows > 0): ?>
        <div class="row g-4">
            <?php while ($row = $result->fetch_assoc()): ?>
                <?php
                $has_access = $row['matriculado'] > 0 || $row['acesso_empresa'] > 0 || $row['valor'] <= 0 || in_array('admin', $_SESSION['permissoes'] ?? []);
                $link_target = $has_access ? BASE_URL . "produtos/produto/" . $row['id'] : '#';

                if (!$has_access) {
                    if (!empty($row['link_checkout'])) {
                        $link_target = $row['link_checkout'];
                    } elseif ($row['valor'] > 0) {
                        $link_target = BASE_URL . "modules/loja/criar_pagamento.php?produto_id=" . $row['id'];
                    }
                }
                ?>
                <div class="col-md-6 col-lg-4">
                    <a href="<?php echo $link_target; ?>" <?php echo (!$has_access && !empty($row['link_checkout'])) ? 'target="_blank"' : ''; ?> class="text-decoration-none">
                        <div class="card h-100 border-0 shadow-sm product-card">
                            <!-- Product Image -->
                            <div class="position-relative overflow-hidden" style="height: 240px;">
                                <?php if (!empty($row['imagem_capa'])): ?>
                                    <img src="<?php echo htmlspecialchars($row['imagem_capa']); ?>"
                                        class="w-100 h-100 object-fit-cover product-image"
                                        alt="<?php echo htmlspecialchars($row['titulo']); ?>">
                                <?php else: ?>
                                    <div class="w-100 h-100 d-flex align-items-center justify-content-center text-white position-relative"
                                        style="background: linear-gradient(135deg, #1D2F5F 0%, #2a4575 100%);">
                                        <i class="bi bi-mortarboard-fill" style="font-size: 4rem; opacity: 0.3;"></i>
                                    </div>
                                <?php endif; ?>

                                <!-- Gradient Overlay -->
                                <div class="position-absolute top-0 start-0 w-100 h-100"
                                    style="background: linear-gradient(to bottom, transparent 50%, rgba(0,0,0,0.4) 100%);">
                                </div>

                                <!-- Status Badge -->
                                <div class="position-absolute top-0 end-0 m-3">
                                    <?php if (!$has_access): ?>
                                        <span class="badge bg-warning text-dark px-3 py-2 shadow-sm">
                                            <i class="bi bi-lock-fill me-1"></i>Bloqueado
                                        </span>
                                    <?php endif; ?>
                                </div>

                                <!-- Category Badge -->
                                <div class="position-absolute bottom-0 start-0 m-3">
                                    <span class="badge bg-dark bg-opacity-75 text-white px-3 py-2">
                                        <?php echo strtoupper($row['tipo'] ?? 'CURSO'); ?>
                                    </span>
                                </div>
                            </div>

                            <!-- Card Body -->
                            <div class="card-body p-4 d-flex flex-column">
                                <!-- Title -->
                                <h5 class="card-title fw-bold mb-2 text-dark" style="min-height: 3rem; line-height: 1.5;">
                                    <?php echo htmlspecialchars($row['titulo']); ?>
                                </h5>

                                <!-- Description -->
                                <p class="card-text text-muted small mb-3 flex-grow-1"
                                    style="line-height: 1.6; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                                    <?php echo htmlspecialchars($row['descricao'] ?? 'Conteúdo exclusivo para seu desenvolvimento.'); ?>
                                </p>

                                <!-- Price & Action -->
                                <div class="mt-auto">
                                    <?php if ($has_access): ?>
                                        <!-- Access Button -->
                                        <div class="d-flex align-items-center justify-content-between p-3 bg-light rounded">
                                            <span class="text-success fw-semibold small">
                                                <i class="bi bi-check-circle-fill me-1"></i>Acesso Liberado
                                            </span>
                                            <span class="text-primary fw-bold">
                                                ACESSAR <i class="bi bi-arrow-right ms-1"></i>
                                            </span>
                                        </div>
                                    <?php else: ?>
                                        <!-- Purchase Section -->
                                        <div class="d-flex align-items-center justify-content-between mb-2">
                                            <?php if ($row['valor'] > 0): ?>
                                                <div>
                                                    <small class="text-muted d-block">Investimento</small>
                                                    <span class="h4 fw-bold text-success mb-0">
                                                        R$ <?php echo number_format($row['valor'], 2, ',', '.'); ?>
                                                    </span>
                                                </div>
                                            <?php else: ?>
                                                <span class="badge bg-success px-3 py-2">
                                                    <i class="bi bi-gift-fill me-1"></i>GRATUITO
                                                </span>
                                            <?php endif; ?>
                                        </div>

                                        <?php if (!empty($row['link_checkout']) || $row['valor'] > 0): ?>
                                            <button class="btn btn-success w-100 fw-semibold py-2 purchase-btn">
                                                <i class="bi bi-cart-fill me-2"></i>COMPRAR AGORA
                                            </button>
                                        <?php else: ?>
                                            <button class="btn btn-secondary w-100 disabled">
                                                Em Breve
                                            </button>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            <?php endwhile; ?>
        </div>
    <?php else: ?>
        <!-- EMPTY STATE (Sem produtos) -->
        <div class="row">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-body text-center py-5 px-4">
                        <div class="mb-4">
                            <?php if (!$is_loja): ?>
                                <i class="bi bi-collection text-muted" style="font-size: 4rem; opacity: 0.3;"></i>
                            <?php else: ?>
                                <i class="bi bi-bag-x text-muted" style="font-size: 4rem; opacity: 0.3;"></i>
                            <?php endif; ?>
                        </div>

                        <h3 class="fw-bold mb-3">
                            <?php echo !$is_loja ? 'Nenhum Curso Ativo' : 'Nenhum Produto Disponível'; ?>
                        </h3>

                        <p class="text-muted mb-4">
                            <?php echo !$is_loja
                                ? 'Você ainda não possui produtos liberados. Visite a loja para ver o catálogo completo.'
                                : 'No momento, não há produtos cadastrados na loja. Volte em breve!'; ?>
                        </p>

                        <?php if (!$is_loja): ?>
                            <a href="?view=loja" class="btn btn-primary px-4">
                                <i class="bi bi-shop me-2"></i>Ir para a Loja
                            </a>
                        <?php else: ?>
                            <a href="<?php echo BASE_URL; ?>dashboard" class="btn btn-outline-primary px-4">
                                <i class="bi bi-arrow-left me-2"></i>Voltar para o Início
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>
</div>
</div>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>