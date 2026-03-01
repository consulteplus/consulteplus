<?php
$pageTitle = "Gerenciar Ferramentas";
require_once __DIR__ . '/../../../includes/header.php';
checkPermission(['superadmin']);

// Handle Status Toggle
if (isset($_GET['toggle_id']) && isset($_GET['status'])) {
    $id = intval($_GET['toggle_id']);
    $status = intval($_GET['status']);
    $conn->execute_query("UPDATE ferramentas_tipos SET ativo = ? WHERE id = ?", [$status, $id]);
    header("Location: " . BASE_URL . "admin/ferramentas");
    exit;
}

// Fetch Tools
$result = $conn->query("SELECT * FROM ferramentas_tipos ORDER BY ordem ASC");
?>

<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Catálogo de Ferramentas</h1>
            <p class="text-muted small mb-0">Gerencie a visibilidade das ferramentas para seus clientes</p>
        </div>
        <a href="<?php echo BASE_URL; ?>admin/ferramentas/atribuir" class="btn btn-outline-dark me-2">
            <i class="bi bi-person-lock me-2"></i>Controlar Acessos
        </a>
        <a href="<?php echo BASE_URL; ?>ferramentas" target="_blank" class="btn btn-outline-primary">
            <i class="bi bi-eye me-2"></i>Visualizar como Cliente
        </a>
    </div>

    <!-- Table Card -->
    <div class="card shadow border-0">
        <div class="card-body p-0">
            <div class="table-responsive p-3">
                <table class="table table-hover align-middle mb-0 datatable">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4">Ordem</th>
                            <th>Ferramenta</th>
                            <th>Slug</th>
                            <th>Descrição</th>
                            <th>Status</th>
                            <th class="text-end text-nowrap">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td class="ps-4 text-muted border-0">
                                    #<?php echo $row['ordem']; ?>
                                </td>
                                <td class="border-0">
                                    <div class="d-flex align-items-center">
                                        <div class="avatar bg-light rounded me-3 d-flex align-items-center justify-content-center border"
                                            style="width: 40px; height: 40px;">
                                            <i class="bi <?php echo $row['icone']; ?> fs-5 text-primary"></i>
                                        </div>
                                        <span class="fw-bold text-dark"><?php echo htmlspecialchars($row['nome']); ?></span>
                                    </div>
                                </td>
                                <td class="border-0">
                                    <code
                                        class="text-muted bg-light px-2 py-1 rounded small"><?php echo $row['slug']; ?></code>
                                </td>
                                <td class="border-0">
                                    <small class="text-muted d-block text-truncate" style="max-width: 350px;">
                                        <?php echo htmlspecialchars($row['descricao']); ?>
                                    </small>
                                </td>
                                <td class="border-0">
                                    <?php if ($row['ativo']): ?>
                                        <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3">Ativo</span>
                                    <?php else: ?>
                                        <span
                                            class="badge bg-secondary bg-opacity-10 text-secondary rounded-pill px-3">Inativo</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-end text-nowrap border-0">
                                    <?php if ($row['ativo']): ?>
                                        <a href="?toggle_id=<?php echo $row['id']; ?>&status=0"
                                            class="btn btn-sm btn-outline-danger" title="Desativar">
                                            <i class="bi bi-eye-slash"></i>
                                        </a>
                                    <?php else: ?>
                                        <a href="?toggle_id=<?php echo $row['id']; ?>&status=1"
                                            class="btn btn-sm btn-outline-success" title="Ativar">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                    <?php endif; ?>

                                    <a href="<?php echo BASE_URL; ?>admin/ferramentas/editar/<?php echo $row['id']; ?>"
                                        class="btn btn-sm btn-outline-info ms-1" title="Editar">
                                        <i class="bi bi-pencil"></i>
                                    </a>

                                    <a href="<?php echo BASE_URL; ?>ferramentas/<?php echo $row['slug']; ?>/simular"
                                        class="btn btn-sm btn-outline-primary ms-1" title="Simular Ferramenta">
                                        <i class="bi bi-play-circle"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../../../includes/footer.php'; ?>