<?php
$pageTitle = "Análise SWOT";
require_once __DIR__ . '/../../../includes/header.php';
checkPermission(['admin', 'cliente']);

$company_id = $_SESSION['company_id'];

// Fetch SWOT Analyses
$stmt = $conn->prepare("SELECT id, titulo, created_at FROM ferramentas_analises WHERE company_id = ? AND ferramenta = 'SWOT' ORDER BY created_at DESC");
$stmt->bind_param("i", $company_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<div class="container-xxl flex-grow-1 container-p-y">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb breadcrumb-style1 mb-1">
                    <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>ferramentas">Ferramentas</a></li>
                    <li class="breadcrumb-item active">Análise SWOT</li>
                </ol>
            </nav>
            <h4 class="fw-bold mb-0">Minhas Análises SWOT</h4>
        </div>
        <a href="<?php echo BASE_URL; ?>ferramentas/swot/novo" class="btn btn-primary">
            <i class="bi bi-plus-lg me-2"></i>Nova Análise
        </a>
    </div>

    <!-- Empty State -->
    <?php if ($result->num_rows === 0): ?>
        <div class="card shadow-sm text-center py-5">
            <div class="card-body">
                <div class="avatar avatar-xl bg-light rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center"
                    style="width: 80px; height: 80px;">
                    <i class="bi bi-grid-1x2 text-muted fs-1"></i>
                </div>
                <h5 class="fw-bold text-dark">Nenhuma análise encontrada</h5>
                <p class="text-muted mb-4">Comece mapeando as forças e fraquezas do seu negócio.</p>
                <a href="<?php echo BASE_URL; ?>ferramentas/swot/novo" class="btn btn-primary">
                    Criar Primeira SWOT
                </a>
            </div>
        </div>
    <?php else: ?>
        <div class="row g-4">
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100 shadow-sm border-0 hover-lift">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div class="avatar bg-primary bg-opacity-10 rounded p-2">
                                    <i class="bi bi-grid-1x2-fill text-primary fs-4"></i>
                                </div>
                                <div class="dropdown">
                                    <button class="btn btn-link text-muted p-0" type="button" data-bs-toggle="dropdown">
                                        <i class="bi bi-three-dots-vertical"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end">
                                        <li><a class="dropdown-item"
                                                href="<?php echo BASE_URL; ?>ferramentas/swot/editar?id=<?php echo $row['id']; ?>"><i
                                                    class="bi bi-pencil me-2"></i>Editar</a></li>
                                        <li>
                                            <hr class="dropdown-divider">
                                        </li>
                                        <li><a class="dropdown-item text-danger"
                                                href="<?php echo BASE_URL; ?>ferramentas/swot/excluir?id=<?php echo $row['id']; ?>"
                                                onclick="return confirm('Excluir esta análise?')"><i
                                                    class="bi bi-trash me-2"></i>Excluir</a></li>
                                    </ul>
                                </div>
                            </div>
                            <h5 class="card-title fw-bold mb-1 text-truncate">
                                <?php echo htmlspecialchars($row['titulo']); ?>
                            </h5>
                            <small class="text-muted d-block mb-3">
                                Criado em
                                <?php echo date('d/m/Y', strtotime($row['created_at'])); ?>
                            </small>
                            <a href="<?php echo BASE_URL; ?>ferramentas/swot/editar/<?php echo $row['id']; ?>"
                                class="btn btn-outline-primary btn-sm w-100">
                                Visualizar / Editar
                            </a>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../../../includes/footer.php'; ?>