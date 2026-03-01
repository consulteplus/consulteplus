<?php
$pageTitle = "Gestão de Produtos";
require_once __DIR__ . '/../header.php';
checkPermission(['admin']);

// Deletar via API agora, lógica removida daqui
require_once __DIR__ . '/../../../classes/ProductService.php';

$productService = new ProductService();

$limit = 9;
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int) $_GET['page'] : 1;
$offset = ($page - 1) * $limit;

$company_id = $_SESSION['company_id'];

// Fetch Data with Service
$produtos = $productService->listar($company_id, $limit, $offset);
$totalRows = $productService->contarTotal($company_id);
$totalPages = ceil($totalRows / $limit);
?>

<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Meus Produtos</h1>
            <p class="text-muted small mb-0 d-none d-md-block">Gerencie seus produtos digitais e áreas de membros.</p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <!-- View Toggles -->
            <div class="btn-group me-3 d-none d-md-inline-flex" role="group">
                <button type="button" class="btn btn-outline-secondary active" id="btnGridView"
                    onclick="toggleView('grid')">
                    <i class="bi bi-grid-fill"></i>
                </button>
                <button type="button" class="btn btn-outline-secondary" id="btnListView" onclick="toggleView('list')">
                    <i class="bi bi-list-ul"></i>
                </button>
            </div>

            <a href="<?php echo BASE_URL; ?>admin/produtos/distribuir" class="btn btn-outline-info text-nowrap">
                <i class="bi bi-share me-2"></i>Distribuir
            </a>

            <a href="<?php echo BASE_URL; ?>admin/produtos/ia_generator"
                class="btn btn-outline-primary text-nowrap d-none d-lg-inline-flex">
                <i class="bi bi-stars me-2"></i>Criar com IA
            </a>

            <a href="<?php echo BASE_URL; ?>admin/produtos/produto" class="btn btn-primary text-nowrap">
                <i class="bi bi-plus-lg me-2"></i><span class="d-none d-sm-inline">Novo Produto</span><span
                    class="d-inline d-sm-none">Novo</span>
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
                            <th class="py-3">Produto</th>
                            <th class="py-3 text-center" style="width: 120px;">Módulos</th>
                            <th class="py-3 text-center" style="width: 120px;">Alunos</th>
                            <th class="py-3 text-center" style="width: 100px;">Status</th>
                            <th class="py-3 text-end pe-4" style="width: 100px;">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="border-top-0">
                        <?php
                        if (!empty($produtos)):
                            foreach ($produtos as $row):
                                ?>
                                <tr>
                                    <td class="ps-4">
                                        <span
                                            class="badge bg-secondary bg-opacity-10 text-secondary rounded-pill">#<?php echo $row['id']; ?></span>
                                    </td>
                                    <td class="py-3">
                                        <div class="d-flex align-items-center">
                                            <?php if (!empty($row['imagem_capa'])): ?>
                                                <img src="<?php echo htmlspecialchars($row['imagem_capa']); ?>" class="rounded me-3"
                                                    style="width: 48px; height: 48px; object-fit: cover;">
                                            <?php else: ?>
                                                <div class="rounded me-3 bg-light d-flex align-items-center justify-content-center text-muted"
                                                    style="width: 48px; height: 48px;">
                                                    <i class="bi bi-mortarboard"></i>
                                                </div>
                                            <?php endif; ?>
                                            <div>
                                                <h6 class="fw-bold text-dark mb-0">
                                                    <?php echo htmlspecialchars($row['titulo']); ?>
                                                </h6>
                                                <small
                                                    class="text-muted"><?php echo htmlspecialchars(mb_strimwidth($row['descricao'] ?? '', 0, 50, '...')); ?></small>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <span
                                            class="badge bg-secondary text-uppercase"><?php echo $row['tipo'] ?? 'mentoria'; ?></span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-3">
                                            <?php echo $row['total_trilhas']; ?>
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-info bg-opacity-10 text-info rounded-pill px-3">
                                            <?php echo $row['total_alunos']; ?>
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <span
                                            class="badge bg-<?php echo $row['ativo'] ? 'success' : 'secondary'; ?> bg-opacity-10 text-<?php echo $row['ativo'] ? 'success' : 'secondary'; ?>">
                                            <?php echo $row['ativo'] ? 'Ativo' : 'Inativo'; ?>
                                        </span>
                                    </td>
                                    <td class="text-end pe-4 text-nowrap">
                                        <a href="<?php echo BASE_URL; ?>admin/produtos/trilhas/<?php echo $row['id']; ?>"
                                            class="btn btn-sm btn-outline-primary" title="Gerenciar Conteúdo">
                                            <i class="bi bi-layers-fill"></i>
                                        </a>
                                        <a href="<?php echo BASE_URL; ?>admin/produtos/produto/<?php echo $row['id']; ?>"
                                            class="btn btn-sm btn-outline-secondary" title="Editar">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <a href="#" onclick="confirmDelete(<?php echo $row['id']; ?>)"
                                            class="btn btn-sm btn-outline-danger" title="Excluir">
                                            <i class="bi bi-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- GRID VIEW -->
    <div id="gridView" class="row g-4">
        <?php
        if (!empty($produtos)):
            foreach ($produtos as $row):
                ?>
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100 shadow-sm border-0 border-start border-4 border-primary hover-lift transition-all">
                        <!-- Image Header if valid -->
                        <?php if (!empty($row['imagem_capa'])): ?>
                            <div class="ratio ratio-16x9 bg-light border-bottom">
                                <img src="<?php echo htmlspecialchars($row['imagem_capa']); ?>" class="card-img-top"
                                    style="object-fit: contain;" alt="Capa">
                            </div>
                        <?php endif; ?>

                        <div class="card-body p-4 d-flex flex-column">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-3 py-2 fw-bold"
                                    style="font-size: 0.75rem;">
                                    ID: #<?php echo str_pad($row['id'], 3, '0', STR_PAD_LEFT); ?>
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
                                                href="<?php echo BASE_URL; ?>admin/produtos/produto/<?php echo $row['id']; ?>"><i
                                                    class="bi bi-pencil me-2 text-primary"></i>Editar Detalhes</a></li>
                                        <li><a class="dropdown-item py-2"
                                                href="<?php echo BASE_URL; ?>admin/produtos/trilhas/<?php echo $row['id']; ?>"><i
                                                    class="bi bi-layers me-2 text-info"></i>Gerenciar Conteúdo</a></li>
                                        <li><a class="dropdown-item py-2"
                                                href="<?php echo BASE_URL; ?>produtos/produto/<?php echo $row['id']; ?>"
                                                target="_blank"><i class="bi bi-eye me-2 text-secondary"></i>Visualizar como
                                                Aluno</a></li>
                                        <li>
                                            <hr class="dropdown-divider">
                                        </li>
                                        <li><a class="dropdown-item text-danger py-2" href="#"
                                                onclick="confirmDelete(<?php echo $row['id']; ?>)"><i
                                                    class="bi bi-trash me-2"></i>Excluir Produto</a></li>
                                    </ul>
                                </div>
                            </div>

                            <h5 class="card-title fw-bold text-dark mb-2 text-truncate-2" style="min-height: 3rem;">
                                <?php echo htmlspecialchars($row['titulo']); ?>
                            </h5>

                            <div class="card-text text-muted small text-truncate-3 flex-grow-1 mb-4" style="line-height: 1.6;">
                                <?php echo htmlspecialchars($row['descricao'] ?? 'Sem descrição definida.'); ?>
                            </div>

                            <div class="d-flex gap-3 pt-3 border-top mt-auto">
                                <div class="d-flex align-items-center text-muted small" title="Módulos/Trilhas">
                                    <i class="bi bi-collection-play me-2 text-primary"></i>
                                    <span class="fw-bold text-dark me-1"><?php echo $row['total_trilhas']; ?></span> módulos
                                </div>
                                <div class="d-flex align-items-center text-muted small" title="Alunos Ativos">
                                    <i class="bi bi-people me-2 text-info"></i>
                                    <span class="fw-bold text-dark me-1"><?php echo $row['total_alunos']; ?></span> alunos
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-12 text-center py-5">
                <i class="bi bi-clipboard-plus display-1 text-light mb-3 d-block"></i>
                <h4 class="text-muted">Nenhum produto encontrado</h4>
                <p class="text-muted mb-4 opacity-75">Comece criando seu primeiro produto digital ou use a IA para gerar.
                </p>
                <a href="<?php echo BASE_URL; ?>admin/produtos/produto" class="btn btn-primary btn-lg shadow-sm">
                    <i class="bi bi-plus-lg me-2"></i>Novo Produto
                </a>
            </div>
        <?php endif; ?>
    </div>

    <!-- Pagination (Simple Implementation) -->
    <?php if ($totalPages > 1): ?>
        <div class="d-flex justify-content-center mt-5">
            <nav>
                <ul class="pagination pagination-sm shadow-sm">
                    <li class="page-item <?php echo ($page <= 1) ? 'disabled' : ''; ?>">
                        <a class="page-link border-0 text-secondary" href="?page=<?php echo $page - 1; ?>"><i
                                class="bi bi-chevron-left"></i></a>
                    </li>
                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <li class="page-item <?php echo ($i == $page) ? 'active' : ''; ?>">
                            <a class="page-link border-0 fw-bold <?php echo ($i == $page) ? 'bg-primary text-white' : 'text-secondary'; ?>"
                                href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                        </li>
                    <?php endfor; ?>
                    <li class="page-item <?php echo ($page >= $totalPages) ? 'disabled' : ''; ?>">
                        <a class="page-link border-0 text-secondary" href="?page=<?php echo $page + 1; ?>"><i
                                class="bi bi-chevron-right"></i></a>
                    </li>
                </ul>
            </nav>
        </div>
    <?php endif; ?>

</div>

<form id="deleteForm" method="POST" action="<?php echo BASE_URL; ?>admin/produtos" style="display:none;">
    <input type="hidden" name="action" value="delete">
    <input type="hidden" name="id" id="deleteId">
</form>

<script>
    function confirmDelete(id) {
        Swal.fire({
            title: 'Tem certeza?',
            text: "Todos as trilhas e conteúdos serão apagados. Isso é irreversível!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Sim, excluir',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch('<?php echo BASE_URL; ?>modules/admin/produtos/acoes.php?acao=excluir', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ id: id })
                })
                    .then(res => res.json())
                    .then(response => {
                        if (response.success) {
                            Swal.fire(
                                'Excluído!',
                                'O produto foi removido.',
                                'success'
                            ).then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire('Erro', response.message || 'Erro ao excluir.', 'error');
                        }
                    })
                    .catch(err => Swal.fire('Erro', 'Erro de conexão.', 'error'));
            }
        });
    }

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
            localStorage.setItem('mentoriaView', 'grid');
        } else {
            gridView.classList.add('d-none');
            listView.classList.remove('d-none');
            gridBtn.classList.remove('active');
            listBtn.classList.add('active');
            localStorage.setItem('mentoriaView', 'list');
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        const savedView = localStorage.getItem('mentoriaView');
        if (savedView === 'list') {
            toggleView('list');
        }
    });
</script>



<style>
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


<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<?php require_once __DIR__ . '/../../../includes/footer.php'; ?>