<?php
$pageTitle = "Início";
require_once __DIR__ . '/../../includes/header.php';

$user_id = $_SESSION['user_id'];
$company_id = $_SESSION['company_id'];
$user_name = explode(' ', $_SESSION['nome'])[0]; // Primeiro nome

// Buscar produtos do usuário (matriculados ou com acesso da empresa)
$sql_produtos = "SELECT DISTINCT mp.*, 
    (SELECT COUNT(*) FROM mentoria_matriculas mm WHERE mm.produto_id = mp.id AND mm.user_id = ? AND mm.ativo = 1) as matriculado,
    (SELECT COUNT(*) FROM mentoria_acesso_empresas mae WHERE mae.produto_id = mp.id AND mae.company_id = ?) as acesso_empresa
    FROM mentoria_produtos mp
    WHERE mp.ativo = 1
    HAVING (matriculado > 0 OR acesso_empresa > 0)
    ORDER BY mp.id DESC
    LIMIT 6";
$stmt = $conn->prepare($sql_produtos);
$stmt->bind_param("ii", $user_id, $company_id);
$stmt->execute();
$produtos = $stmt->get_result();
?>

<div class="container py-4">
    <!-- Welcome Section -->
    <div class="mb-5">
        <div class="card premium-card border-0 shadow-sm">
            <div class="card-body p-4">
                <h4 class="fw-bold text-dark mb-1">Olá, <?php echo htmlspecialchars($user_name); ?>! 👋</h4>
                <p class="text-muted mb-0">Bem-vindo de volta. Continue de onde parou ou explore novos conteúdos.</p>
            </div>
        </div>
    </div>

    <!-- Meus Produtos -->
    <div class="mb-4">
        <div class="card premium-card border-0 shadow-sm mb-4">
            <div class="card-body p-3">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold mb-0">Meus Produtos</h5>
                    <a href="<?php echo BASE_URL; ?>produtos" class="btn btn-outline-primary btn-sm">
                        Ver Todos <i class="bi bi-arrow-right ms-1"></i>
                    </a>
                </div>
            </div>
        </div>

        <?php if ($produtos->num_rows > 0): ?>
            <div class="row g-4">
                <?php while ($produto = $produtos->fetch_assoc()): ?>
                    <div class="col-md-6 col-lg-4">
                        <a href="<?php echo BASE_URL; ?>produtos/produto/<?php echo $produto['id']; ?>"
                            class="text-decoration-none">
                            <div class="card h-100 border-0 shadow-sm hover-lift">
                                <!-- Imagem -->
                                <div class="position-relative" style="height: 180px; overflow: hidden;">
                                    <?php if (!empty($produto['imagem_capa'])): ?>
                                        <img src="<?php echo htmlspecialchars($produto['imagem_capa']); ?>"
                                            class="card-img-top w-100 h-100" style="object-fit: cover;"
                                            alt="<?php echo htmlspecialchars($produto['titulo']); ?>">
                                    <?php else: ?>
                                        <div class="w-100 h-100 bg-gradient-primary d-flex align-items-center justify-content-center"
                                            style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                                            <i class="bi bi-mortarboard-fill text-white" style="font-size: 3rem;"></i>
                                        </div>
                                    <?php endif; ?>

                                    <!-- Badge tipo -->
                                    <span class="position-absolute top-0 end-0 m-2 badge bg-dark bg-opacity-75">
                                        <?php echo $produto['tipo'] ?? 'Curso'; ?>
                                    </span>
                                </div>

                                <div class="card-body">
                                    <h5 class="card-title fw-bold mb-2">
                                        <?php echo htmlspecialchars($produto['titulo']); ?>
                                    </h5>
                                    <p class="card-text text-muted small mb-3" style="
                                    display: -webkit-box;
                                    -webkit-line-clamp: 2;
                                    -webkit-box-orient: vertical;
                                    overflow: hidden;">
                                        <?php echo htmlspecialchars($produto['descricao'] ?? 'Sem descrição'); ?>
                                    </p>

                                    <!-- Progress bar (placeholder - pode ser implementado depois) -->
                                    <div class="mb-3">
                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                            <small class="text-muted">Progresso</small>
                                            <small class="text-muted fw-bold">0%</small>
                                        </div>
                                        <div class="progress" style="height: 6px;">
                                            <div class="progress-bar bg-primary" style="width: 0%"></div>
                                        </div>
                                    </div>

                                    <button class="btn btn-primary btn-sm w-100">
                                        Continuar Aprendendo <i class="bi bi-arrow-right ms-1"></i>
                                    </button>
                                </div>
                            </div>
                        </a>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <!-- Empty State -->
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center py-5">
                    <div class="mb-4">
                        <i class="bi bi-collection text-muted" style="font-size: 4rem; opacity: 0.3;"></i>
                    </div>
                    <h5 class="fw-bold mb-2">Nenhum produto matriculado</h5>
                    <p class="text-muted mb-4">Explore nossa loja e comece sua jornada de aprendizado!</p>
                    <a href="<?php echo BASE_URL; ?>produtos?view=loja" class="btn btn-primary">
                        <i class="bi bi-shop me-2"></i>Ir para a Loja
                    </a>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <!-- Ações Rápidas -->
    <div class="mb-4">
        <div class="card premium-card border-0 shadow-sm mb-4">
            <div class="card-body p-3">
                <h5 class="fw-bold mb-0">Ações Rápidas</h5>
            </div>
        </div>
        <div class="row g-3">
            <div class="col-md-4">
                <a href="<?php echo BASE_URL; ?>gestao/diagnostico" class="text-decoration-none">
                    <div class="card border-0 shadow-sm hover-lift h-100">
                        <div class="card-body d-flex align-items-center">
                            <div class="bg-primary bg-opacity-10 text-primary rounded-3 p-3 me-3">
                                <i class="bi bi-ui-checks fs-3"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold mb-1">Diagnóstico</h6>
                                <small class="text-muted">Avaliar minha empresa</small>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-md-4">
                <a href="<?php echo BASE_URL; ?>produtos?view=loja" class="text-decoration-none">
                    <div class="card border-0 shadow-sm hover-lift h-100">
                        <div class="card-body d-flex align-items-center">
                            <div class="bg-success bg-opacity-10 text-success rounded-3 p-3 me-3">
                                <i class="bi bi-shop fs-3"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold mb-1">Loja</h6>
                                <small class="text-muted">Explorar produtos</small>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-md-4">
                <a href="<?php echo BASE_URL; ?>gestao/projetos" class="text-decoration-none">
                    <div class="card border-0 shadow-sm hover-lift h-100">
                        <div class="card-body d-flex align-items-center">
                            <div class="bg-info bg-opacity-10 text-info rounded-3 p-3 me-3">
                                <i class="bi bi-diagram-3 fs-3"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold mb-1">Projetos</h6>
                                <small class="text-muted">Gerenciar projetos</small>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>