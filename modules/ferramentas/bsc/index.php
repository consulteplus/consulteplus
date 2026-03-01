<?php
$pageTitle = "Balanced Scorecard (BSC)";
require_once __DIR__ . '/../../../includes/header.php';
checkPermission(['admin', 'cliente']);

$company_id = $_SESSION['company_id'];

// Get existing analyses
$stmt = $conn->prepare("SELECT * FROM ferramentas_analises WHERE company_id = ? AND ferramenta = 'BSC' ORDER BY created_at DESC");
$stmt->bind_param("i", $company_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<div class="container-xxl flex-grow-1 container-p-y">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold m-0">
            <span class="text-muted fw-light">Ferramentas /</span> Balanced Scorecard (BSC)
        </h4>
        <a href="<?php echo BASE_URL; ?>ferramentas/bsc/novo" class="btn btn-primary">
            <i class="bi bi-plus-lg me-2"></i>Novo Scorecard
        </a>
    </div>

    <?php if ($result->num_rows === 0): ?>
        <div class="text-center py-5">
            <div class="mb-3">
                <i class="bi bi-diagram-3 display-1 text-muted"></i>
            </div>
            <h4 class="text-muted">Nenhum BSC encontrado</h4>
            <p class="text-muted mb-4">Traduza sua estratégia em objetivos mensuráveis.</p>
            <a href="<?php echo BASE_URL; ?>ferramentas/bsc/novo" class="btn btn-primary">
                Começar Agora
            </a>
        </div>
    <?php else: ?>
        <div class="row g-4">
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100 shadow-sm border-0 hover-lift">
                        <div class="card-body">
                            <h5 class="card-title fw-bold mb-2">
                                <?php echo htmlspecialchars($row['titulo']); ?>
                            </h5>
                            <small class="text-muted d-block mb-3">
                                <i class="bi bi-calendar me-1"></i>
                                <?php echo date('d/m/Y H:i', strtotime($row['created_at'])); ?>
                            </small>

                            <div class="d-grid gap-2">
                                <a href="<?php echo BASE_URL; ?>ferramentas/bsc/editar/<?php echo $row['id']; ?>"
                                    class="btn btn-outline-primary">
                                    Visualizar / Editar
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../../../includes/footer.php'; ?>