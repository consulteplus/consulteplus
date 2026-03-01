<?php
$pageTitle = "Gerenciar Empresas";
require_once __DIR__ . '/../header.php';
checkPermission(['superadmin']);

// Process Actions (Ativar/Desativar) - Simple Implementation
if (isset($_GET['action']) && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $action = $_GET['action'];
    if ($action === 'activate') {
        $conn->query("UPDATE empresas SET ativo = 1 WHERE id = $id");
        // Also activate owner user?
    } elseif ($action === 'deactivate') {
        $conn->query("UPDATE empresas SET ativo = 0 WHERE id = $id");
    }
    redirect('modules/admin/empresas');
}

// Process Add Company
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'create_company') {
    $nome = sanitize($_POST['nome']);
    $documento = isset($_POST['documento']) ? sanitize($_POST['documento']) : null;

    if ($nome) {
        $stmt = $conn->prepare("INSERT INTO empresas (nome, documento, ativo, created_at) VALUES (?, ?, 1, NOW())");
        $stmt->bind_param("ss", $nome, $documento);
        if ($stmt->execute()) {
            $newId = $conn->insert_id;
            // Redirect to view to add users
            redirect('modules/admin/empresas/view.php?id=' . $newId . '&msg=company_created');
        } else {
            $error = "Erro ao criar empresa: " . $conn->error;
        }
    } else {
        $error = "Nome da empresa é obrigatório.";
    }
}

// List Companies
// List Companies (Optimized to avoid duplicates)
$sql = "SELECT e.*, 
        (SELECT u.nome FROM users u WHERE u.company_id = e.id AND u.tipo = 'admin' ORDER BY u.id ASC LIMIT 1) as dono_nome,
        (SELECT u.email FROM users u WHERE u.company_id = e.id AND u.tipo = 'admin' ORDER BY u.id ASC LIMIT 1) as dono_email
        FROM empresas e 
        ORDER BY e.created_at DESC";
$result = $conn->query($sql);
?>

<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Empresas (Tenants)</h1>
            <p class="text-muted small mb-0"><?php echo $result->num_rows; ?> empresas encontradas</p>
        </div>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCompanyModal"><i
                class="bi bi-plus-lg me-2"></i>Nova Empresa</button>
    </div>

    <div class="card shadow border-0">
        <div class="card-body p-0">
            <div class="table-responsive p-3">
                <table class="table table-hover align-middle mb-0 datatable">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4">ID</th>
                            <th>Empresa</th>
                            <th>Dono / Email</th>
                            <th>Plano</th>
                            <th>Status</th>
                            <th>Cadastro</th>
                            <th class="text-center text-nowrap">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($result && $result->num_rows > 0): ?>
                            <?php while ($row = $result->fetch_assoc()): ?>
                                <tr>
                                    <td class="ps-4 text-muted">#
                                        <?php echo $row['id']; ?>
                                    </td>
                                    <td>
                                        <div class="fw-bold text-dark">
                                            <?php echo htmlspecialchars($row['nome']); ?>
                                        </div>
                                        <?php if (!empty($row['documento'])): ?>
                                            <small class="text-muted">
                                                <?php echo htmlspecialchars($row['documento']); ?>
                                            </small>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="text-dark">
                                            <?php echo htmlspecialchars($row['dono_nome'] ?? 'Sem dono'); ?>
                                        </div>
                                        <a href="mailto:<?php echo $row['dono_email']; ?>" class="small text-decoration-none">
                                            <?php echo $row['dono_email']; ?>
                                        </a>
                                    </td>
                                    <td>
                                        <span class="badge bg-info bg-opacity-10 text-info">Free</span>
                                    </td>
                                    <td>
                                        <?php if ($row['ativo']): ?>
                                            <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3">Ativo</span>
                                        <?php else: ?>
                                            <span class="badge bg-danger bg-opacity-10 text-danger rounded-pill px-3">Inativo</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-muted small">
                                        <?php echo date('d/m/Y', strtotime($row['created_at'])); ?>
                                    </td>
                                    <td class="text-end text-nowrap">
                                        <?php if ($row['ativo']): ?>
                                            <a href="?action=deactivate&id=<?php echo $row['id']; ?>"
                                                class="btn btn-sm btn-outline-danger" title="Desativar"
                                                onclick="return confirm('Tem certeza?');">
                                                <i class="bi bi-slash-circle"></i>
                                            </a>
                                        <?php else: ?>
                                            <a href="?action=activate&id=<?php echo $row['id']; ?>"
                                                class="btn btn-sm btn-outline-success" title="Ativar">
                                                <i class="bi bi-check-lg"></i>
                                            </a>
                                        <?php endif; ?>
                                        <a href="<?php echo BASE_URL; ?>admin/empresas/view/<?php echo $row['id']; ?>"
                                            class="btn btn-sm btn-outline-info ms-1" title="Visualizar">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="#" class="btn btn-sm btn-outline-primary ms-1" title="Editar"><i
                                                class="bi bi-pencil"></i></a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted">Nenhuma empresa encontrada.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>


<!-- Modal Add Company -->
<div class="modal fade" id="addCompanyModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form method="POST" class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Nova Empresa</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="action" value="create_company">

                <?php if (isset($error)): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endif; ?>

                <div class="mb-3">
                    <label class="form-label fw-bold">Nome da Empresa <span class="text-danger">*</span></label>
                    <input type="text" name="nome" class="form-control" required placeholder="Ex: Acme Corp">
                </div>

                <div class="mb-3">
                    <label class="form-label">Documento (CNPJ/CPF) <span
                            class="text-muted small">(Opcional)</span></label>
                    <input type="text" name="documento" class="form-control" placeholder="Apenas números">
                </div>

                <div class="alert alert-info small">
                    <i class="bi bi-info-circle me-1"></i> Após criar a empresa, você será redirecionado para cadastrar
                    o primeiro usuário administrador.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-primary">Criar Empresa</button>
            </div>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/../../../includes/footer.php'; ?>