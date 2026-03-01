<?php
$pageTitle = "Atribuir Diagnósticos";
require_once __DIR__ . '/../header.php';
checkPermission(['superadmin']);

// Process Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? 'create';

    if ($action === 'create') {
        $empresa_id = intval($_POST['empresa_id']);
        $modelo_id = intval($_POST['modelo_id']);
        $frequencia = $_POST['frequencia'];
        $data_inicio = $_POST['data_inicio'];

        // Calculate Next Date
        $proxima_data = $data_inicio;
        if ($frequencia !== 'unica') {
            // Logic to calculate next recurring date can be refined here or in a cron job
            // For now, if today is start date, next date is today. 
            // The recurrence system will update proxima_data after execution.
        }

        $stmt = $conn->prepare("INSERT INTO recursos_atribuicoes (empresa_id, recurso_id, recurso_tipo, frequencia, data_inicio, proxima_data, ativo) VALUES (?, ?, 'diagnostico', ?, ?, ?, 1)");
        $stmt->bind_param("iisss", $empresa_id, $modelo_id, $frequencia, $data_inicio, $proxima_data);

        if ($stmt->execute()) {
            $_SESSION['success'] = "Diagnóstico atribuído com sucesso!";
        } else {
            $_SESSION['error'] = "Erro ao atribuir: " . $stmt->error;
        }
    } elseif ($action === 'delete') {
        $id = intval($_POST['id']);
        $id = intval($_POST['id']);
        $conn->query("DELETE FROM recursos_atribuicoes WHERE id=$id");
        $_SESSION['success'] = "Atribuição removida.";
        $_SESSION['success'] = "Atribuição removida.";
    }

    // Redirect to self to prevent resubmission
    echo "<script>window.location.href = window.location.href;</script>";
    exit;
}

// Fetch Data
$empresas = $conn->query("SELECT id, nome FROM empresas WHERE ativo = 1 ORDER BY nome");
$modelos = $conn->query("SELECT id, titulo FROM gestao_diagnostico_modelos WHERE ativo = 1 ORDER BY titulo");

$sqlList = "
    SELECT a.*, e.nome as empresa, m.titulo as modelo
    FROM recursos_atribuicoes a
    JOIN empresas e ON a.empresa_id = e.id
    JOIN gestao_diagnostico_modelos m ON a.recurso_id = m.id
    WHERE a.recurso_tipo = 'diagnostico'
    ORDER BY a.id DESC
";
$atribuicoes = $conn->query($sqlList);
?>

<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Distribuição de Diagnósticos</h1>
            <p class="text-muted small mb-0">Defina quais empresas devem responder a quais diagnósticos.</p>
        </div>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#assignModal">
            <i class="bi bi-plus-lg me-2"></i>Nova Atribuição
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
                            <th>Diagnóstico</th>
                            <th>Frequência</th>
                            <th>Próxima Execução</th>
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
                                        <?php echo htmlspecialchars($row['modelo']); ?>
                                    </td>
                                    <td>
                                        <?php
                                        $freqLabels = [
                                            'unica' => 'Única Vez',
                                            'diaria' => 'Diária',
                                            'semanal' => 'Semanal',
                                            'mensal' => 'Mensal',
                                            'trimestral' => 'Trimestral',
                                            'semestral' => 'Semestral',
                                            'anual' => 'Anual'
                                        ];
                                        echo '<span class="badge bg-info text-dark">' . ($freqLabels[$row['frequencia']] ?? $row['frequencia']) . '</span>';
                                        ?>
                                    </td>
                                    <td>
                                        <?php if ($row['proxima_data']): ?>
                                            <i class="bi bi-calendar-event me-1 text-muted"></i>
                                            <?php echo date('d/m/Y', strtotime($row['proxima_data'])); ?>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
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
                                            onsubmit="return confirm('Remover esta atribuição?');">
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
                                <td colspan="6" class="text-center py-5 text-muted">Nenhuma atribuição encontrada.</td>
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
                    <h5 class="modal-title">Nova Atribuição</h5>
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
                        <label class="form-label">Modelo de Diagnóstico</label>
                        <select name="modelo_id" class="form-select" required>
                            <option value="">Selecione...</option>
                            <?php while ($m = $modelos->fetch_assoc()): ?>
                                <option value="<?php echo $m['id']; ?>">
                                    <?php echo htmlspecialchars($m['titulo']); ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Frequência</label>
                            <select name="frequencia" class="form-select" required>
                                <option value="unica">Única Vez</option>
                                <option value="mensal" selected>Mensal</option>
                                <option value="trimestral">Trimestral</option>
                                <option value="semestral">Semestral</option>
                                <option value="anual">Anual</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Início</label>
                            <input type="date" name="data_inicio" class="form-control"
                                value="<?php echo date('Y-m-d'); ?>" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Salvar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../../../includes/footer.php'; ?>