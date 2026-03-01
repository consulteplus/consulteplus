<?php
$pageTitle = "Atribuir Ferramentas";
require_once __DIR__ . '/../../../includes/header.php';
checkPermission(['superadmin']);

// Process Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? 'create';

    if ($action === 'create') {
        $empresa_id = intval($_POST['empresa_id']);
        $recurso_id = intval($_POST['recurso_id']);
        $frequencia = $_POST['frequencia'] ?? 'unica'; // Ferramentas geralmente são acesso contínuo, mas mantemos estrutura
        $data_inicio = $_POST['data_inicio'] ?? date('Y-m-d');

        $stmt = $conn->prepare("INSERT INTO recursos_atribuicoes (empresa_id, recurso_id, recurso_tipo, frequencia, data_inicio, proxima_data, ativo) VALUES (?, ?, 'ferramenta', ?, ?, NULL, 1)");
        $stmt->bind_param("iiss", $empresa_id, $recurso_id, $frequencia, $data_inicio);

        if ($stmt->execute()) {
            $_SESSION['success'] = "Ferramenta liberada com sucesso!";
        } else {
            $_SESSION['error'] = "Erro ao atribuir: " . $stmt->error;
        }
    } elseif ($action === 'delete') {
        $id = intval($_POST['id']);
        $conn->query("DELETE FROM recursos_atribuicoes WHERE id=$id");
        $_SESSION['success'] = "Acesso removido.";
    }

    // Redirect to self
    echo "<script>window.location.href = window.location.href;</script>";
    exit;
}

// Fetch Data
$empresas = $conn->query("SELECT id, nome FROM empresas WHERE ativo = 1 ORDER BY nome");
$ferramentas = $conn->query("SELECT id, nome FROM ferramentas_tipos WHERE ativo = 1 ORDER BY nome");

$sqlList = "
    SELECT a.*, e.nome as empresa, f.nome as ferramenta
    FROM recursos_atribuicoes a
    JOIN empresas e ON a.empresa_id = e.id
    JOIN ferramentas_tipos f ON a.recurso_id = f.id
    WHERE a.recurso_tipo = 'ferramenta'
    ORDER BY a.id DESC
";
$atribuicoes = $conn->query($sqlList);
?>

<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Distribuição de Ferramentas</h1>
            <p class="text-muted small mb-0">Gerencie quais empresas têm acesso a quais ferramentas.</p>
        </div>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#assignModal">
            <i class="bi bi-plus-lg me-2"></i>Liberar Acesso
        </button>
    </div>

    <!-- Lista de Atribuições -->
    <div class="card shadow border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4">Empresa</th>
                            <th>Ferramenta</th>
                            <th>Data Liberação</th>
                            <th>Status</th>
                            <th class="text-end pe-4">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($atribuicoes && $atribuicoes->num_rows > 0): ?>
                            <?php while ($row = $atribuicoes->fetch_assoc()): ?>
                                <tr>
                                    <td class="ps-4 fw-bold text-dark">
                                        <?php echo htmlspecialchars($row['empresa']); ?>
                                    </td>
                                    <td>
                                        <i class="bi bi-tools text-primary me-2"></i>
                                        <?php echo htmlspecialchars($row['ferramenta']); ?>
                                    </td>
                                    <td>
                                        <i class="bi bi-calendar-check me-1 text-muted"></i>
                                        <?php echo date('d/m/Y', strtotime($row['data_inicio'])); ?>
                                    </td>
                                    <td>
                                        <?php if ($row['ativo']): ?>
                                            <span class="badge bg-success bg-opacity-10 text-success">Ativo</span>
                                        <?php else: ?>
                                            <span class="badge bg-danger bg-opacity-10 text-danger">Inativo</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-end pe-4">
                                        <form method="POST" class="d-inline"
                                            onsubmit="return confirm('Remover acesso desta empresa?');">
                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="text-center py-5 text-muted">Nenhuma atribuição encontrada.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Nova Atribuição -->
<div class="modal fade" id="assignModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST">
                <input type="hidden" name="action" value="create">
                <div class="modal-header">
                    <h5 class="modal-title">Liberar Ferramenta</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Empresa</label>
                        <select name="empresa_id" class="form-select" required>
                            <option value="">Selecione...</option>
                            <?php while ($e = $empresas->fetch_assoc()): ?>
                                <option value="<?php echo $e['id']; ?>">
                                    <?php echo htmlspecialchars($e['nome']); ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Ferramenta</label>
                        <select name="recurso_id" class="form-select" required>
                            <option value="">Selecione...</option>
                            <?php while ($m = $ferramentas->fetch_assoc()): ?>
                                <option value="<?php echo $m['id']; ?>">
                                    <?php echo htmlspecialchars($m['nome']); ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Data de Liberação</label>
                        <input type="date" name="data_inicio" class="form-control" value="<?php echo date('Y-m-d'); ?>"
                            required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Liberar Acesso</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../../../includes/footer.php'; ?>