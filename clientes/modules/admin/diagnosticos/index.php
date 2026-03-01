<?php
$pageTitle = "Gerenciar Diagnósticos";
require_once __DIR__ . '/../header.php';
checkPermission(['superadmin']);

// Process Model Creations
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['novo_modelo'])) {
    $titulo = $_POST['titulo'];
    $desc = $_POST['descricao'];
    $stmt = $conn->prepare("INSERT INTO gestao_diagnostico_modelos (titulo, descricao, ativo) VALUES (?, ?, 1)");
    $stmt->bind_param("ss", $titulo, $desc);
    $stmt->execute();
    redirect('admin/diagnosticos');
}

// Pagination Logic
$limit = 6;
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int) $_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Count Total
$totalResult = $conn->query("SELECT COUNT(*) as total FROM gestao_diagnostico_modelos")->fetch_assoc();
$totalRows = $totalResult['total'];
$totalPages = ceil($totalRows / $limit);

$modelos = $conn->query("
    SELECT 
        m.*, 
        (SELECT COUNT(*) FROM gestao_diagnostico_perguntas p WHERE p.modelo_id = m.id) as total_perguntas,
        (SELECT COUNT(*) FROM recursos_atribuicoes a WHERE a.recurso_id = m.id AND a.recurso_tipo = 'diagnostico') as total_usos
    FROM gestao_diagnostico_modelos m 
    ORDER BY m.id DESC
    LIMIT $limit OFFSET $offset
");
?>

<div class="container-fluid py-4">
    <div
        class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 gap-3">
        <h1 class="h3 mb-0 text-gray-800">Modelos de Diagnóstico</h1>
        <div
            class="d-flex flex-wrap align-items-center gap-2 w-100 w-md-auto justify-content-start justify-content-md-end">
            <!-- View Toggles -->
            <div class="btn-group me-2" role="group">
                <button type="button" class="btn btn-outline-secondary active" id="btnGridView"
                    onclick="toggleView('grid')">
                    <i class="bi bi-grid-fill"></i>
                </button>
                <button type="button" class="btn btn-outline-secondary" id="btnListView" onclick="toggleView('list')">
                    <i class="bi bi-list-ul"></i>
                </button>
            </div>

            <a href="<?php echo BASE_URL; ?>admin/diagnosticos/atribuir"
                class="btn btn-outline-info text-nowrap flex-fill flex-md-grow-0">
                <i class="bi bi-share me-md-2"></i><span class="d-none d-sm-inline">Distribuir</span>
            </a>
            <a href="<?php echo BASE_URL; ?>admin/diagnosticos/novo"
                class="btn btn-primary text-nowrap flex-fill flex-md-grow-0">
                <i class="bi bi-plus-lg me-md-2"></i><span class="d-inline d-sm-none">Novo</span><span
                    class="d-none d-sm-inline">Novo Diagnóstico</span>
            </a>
        </div>
    </div>

    <!-- List View (Hidden by default) -->
    <div id="listView" class="d-none">
        <div class="card shadow border-0 rounded-3 overflow-hidden">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light text-uppercase small text-muted fw-bold">
                        <tr>
                            <th class="ps-4 py-3" style="width: 80px;">ID</th>
                            <th class="py-3">Diagnóstico</th>
                            <th class="py-3 text-center" style="width: 120px;">Perguntas</th>
                            <th class="py-3 text-center" style="width: 120px;">Empresas</th>
                            <th class="py-3 text-end pe-4" style="width: 100px;">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="border-top-0">
                        <?php
                        if ($modelos->num_rows > 0):
                            $modelos->data_seek(0);
                            while ($m = $modelos->fetch_assoc()):
                                ?>
                                <tr>
                                    <td class="ps-4">
                                        <span
                                            class="badge bg-secondary bg-opacity-10 text-secondary rounded-pill">#<?php echo $m['id']; ?></span>
                                    </td>
                                    <td class="py-3">
                                        <h6 class="fw-bold text-dark mb-1"><?php echo htmlspecialchars($m['titulo']); ?></h6>
                                        <div class="text-muted small text-truncate" style="max-width: 400px;">
                                            <?php echo htmlspecialchars($m['descricao'] ?: '-'); ?>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-3">
                                            <?php echo $m['total_perguntas']; ?>
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-info bg-opacity-10 text-info rounded-pill px-3">
                                            <?php echo $m['total_usos']; ?>
                                        </span>
                                    </td>
                                    <td class="text-end pe-4">
                                        <div class="dropdown">
                                            <button class="btn btn-sm btn-light border shadow-sm rounded-circle p-0"
                                                style="width:32px; height:32px" data-bs-toggle="dropdown">
                                                <i class="bi bi-three-dots"></i>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end shadow border-0">
                                                <li><a class="dropdown-item small"
                                                        href="<?php echo BASE_URL; ?>admin/diagnosticos/editar/<?php echo $m['id']; ?>"><i
                                                            class="bi bi-pencil me-2"></i>Editar</a></li>
                                                <li><a class="dropdown-item small"
                                                        href="<?php echo BASE_URL; ?>admin/diagnosticos/perguntas/<?php echo $m['id']; ?>"><i
                                                            class="bi bi-list-check me-2"></i>Perguntas</a></li>
                                                <li>
                                                    <hr class="dropdown-divider">
                                                </li>
                                                <li><a class="dropdown-item small text-danger" href="#"><i
                                                            class="bi bi-trash me-2"></i>Excluir</a></li>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                            <?php endwhile; endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- GRID VIEW -->
    <div id="gridView" class="row g-4">
        <?php
        $modelos->data_seek(0);
        while ($m = $modelos->fetch_assoc()):
            ?>
            <div class="col-md-6 col-lg-4">
                <div class="card h-100 shadow-sm border-0 border-start border-4 border-primary hover-lift transition-all">
                    <div class="card-body p-4 d-flex flex-column">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-3 py-2 fw-bold"
                                style="font-size: 0.75rem;">
                                #<?php echo str_pad($m['id'], 3, '0', STR_PAD_LEFT); ?>
                            </span>
                            <div class="dropdown">
                                <button class="btn btn-icon btn-sm btn-light rounded-circle shadow-sm" type="button"
                                    data-bs-toggle="dropdown">
                                    <i class="bi bi-three-dots-vertical text-muted"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end border-0 shadow-lg">
                                    <li>
                                        <h6 class="dropdown-header text-uppercase small fw-bold">Gerenciar</h6>
                                    </li>
                                    <li><a class="dropdown-item py-2"
                                            href="<?php echo BASE_URL; ?>admin/diagnosticos/editar/<?php echo $m['id']; ?>"><i
                                                class="bi bi-pencil me-2 text-primary"></i>Editar Detalhes</a></li>
                                    <li><a class="dropdown-item py-2"
                                            href="<?php echo BASE_URL; ?>admin/diagnosticos/perguntas/<?php echo $m['id']; ?>"><i
                                                class="bi bi-list-check me-2 text-info"></i>Perguntas & Estrutura</a></li>
                                    <li>
                                        <hr class="dropdown-divider">
                                    </li>
                                    <li><a class="dropdown-item text-danger py-2" href="#"><i
                                                class="bi bi-trash me-2"></i>Excluir Modelo</a></li>
                                </ul>
                            </div>
                        </div>

                        <h5 class="card-title fw-bold text-dark mb-2 text-truncate-2" style="min-height: 3rem;">
                            <?php echo htmlspecialchars($m['titulo']); ?>
                        </h5>

                        <div class="card-text text-muted small text-truncate-3 flex-grow-1 mb-4" style="line-height: 1.6;">
                            <?php echo htmlspecialchars($m['descricao'] ?: 'Sem descrição definida.'); ?>
                        </div>

                        <div class="d-flex gap-3 pt-3 border-top mt-auto">
                            <div class="d-flex align-items-center text-muted small" title="Total de Perguntas">
                                <i class="bi bi-ui-checks-grid me-2 text-primary"></i>
                                <span class="fw-bold text-dark me-1"><?php echo $m['total_perguntas']; ?></span> perguntas
                            </div>
                            <div class="d-flex align-items-center text-muted small" title="Empresas Atribuídas">
                                <i class="bi bi-buildings me-2 text-info"></i>
                                <span class="fw-bold text-dark me-1"><?php echo $m['total_usos']; ?></span> empresas
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    </div>

    <!-- Pagination -->
    <?php if ($totalPages > 1): ?>
        <div class="d-flex justify-content-center mt-5">
            <nav aria-label="Navegação de página">
                <ul class="pagination pagination-sm shadow-sm">
                    <li class="page-item <?php echo ($page <= 1) ? 'disabled' : ''; ?>">
                        <a class="page-link border-0 text-secondary" href="?page=<?php echo $page - 1; ?>">
                            <i class="bi bi-chevron-left"></i>
                        </a>
                    </li>

                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <li class="page-item <?php echo ($i == $page) ? 'active' : ''; ?>">
                            <a class="page-link border-0 fw-bold <?php echo ($i == $page) ? 'bg-primary text-white' : 'text-secondary'; ?>"
                                href="?page=<?php echo $i; ?>">
                                <?php echo $i; ?>
                            </a>
                        </li>
                    <?php endfor; ?>

                    <li class="page-item <?php echo ($page >= $totalPages) ? 'disabled' : ''; ?>">
                        <a class="page-link border-0 text-secondary" href="?page=<?php echo $page + 1; ?>">
                            <i class="bi bi-chevron-right"></i>
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
    <?php endif; ?>
</div>

<style>
    .hover-lift {
        transition: transform 0.2s, box-shadow 0.2s;
    }

    .hover-lift:hover {
        transform: translateY(-3px);
        box-shadow: 0 .5rem 1rem rgba(0, 0, 0, .15) !important;
    }

    .text-truncate-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .text-truncate-3 {
        display: -webkit-box;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .btn-icon {
        width: 32px;
        height: 32px;
        padding: 0;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }
</style>

<script>
    function toggleView(view) {
        const gridBtn = document.getElementById('btnGridView');
        const listBtn = document.getElementById('btnListView');
        const gridView = document.getElementById('gridView');
        const listView = document.getElementById('listView');

        if (view === 'grid') {
            gridView.classList.remove('d-none');
            listView.classList.add('d-none');
            gridBtn.classList.add('active');
            listBtn.classList.remove('active');
            // Save preference (optional)
            localStorage.setItem('diagView', 'grid');
        } else {
            gridView.classList.add('d-none');
            listView.classList.remove('d-none');
            gridBtn.classList.remove('active');
            listBtn.classList.add('active');
            localStorage.setItem('diagView', 'list');
        }
    }

    // Restore preference on load
    document.addEventListener('DOMContentLoaded', () => {
        const savedView = localStorage.getItem('diagView');
        if (savedView === 'list') {
            toggleView('list');
        }
    });
</script>

</div>

<?php require_once __DIR__ . '/../../../includes/footer.php'; ?>