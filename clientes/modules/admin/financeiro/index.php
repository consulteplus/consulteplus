<?php
$pageTitle = "Financeiro - Assinaturas";
require_once __DIR__ . '/../header.php';
checkPermission(['superadmin']);

// Fetch Subscriptions
$query = "
    SELECT fa.*, e.nome as empresa_nome, e.documento
    FROM financeiro_assinaturas fa
    JOIN empresas e ON fa.company_id = e.id
    ORDER BY fa.created_at DESC
";
$result = $conn->query($query);
?>

<div class="container-xxl flex-grow-1 container-p-y">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold py-3 mb-0">Gestão Financeira</h4>
        <a href="<?php echo BASE_URL; ?>admin/financeiro/nova-assinatura" class="btn btn-primary">
            <i class="bi bi-plus-lg me-2"></i>Nova Assinatura
        </a>
    </div>

    <div class="card shadow border-0">
        <div class="card-body p-0">
            <div class="table-responsive p-3">
                <table class="table table-hover align-middle mb-0 datatable">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4">Empresa</th>
                            <th>Status</th>
                            <th>Ciclo / Valor</th>
                            <th>Próx. Vencimento</th>
                            <th>Asaas ID</th>
                            <th class="text-end pe-4">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($result && $result->num_rows > 0): ?>
                            <?php while ($row = $result->fetch_assoc()): ?>
                                <tr>
                                    <td class="ps-4">
                                        <div class="fw-bold text-dark">
                                            <?php echo htmlspecialchars($row['empresa_nome']); ?>
                                        </div>
                                        <?php if (!empty($row['documento'])): ?>
                                            <small class="text-muted">
                                                <?php echo htmlspecialchars($row['documento']); ?>
                                            </small>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php
                                        $badge = 'secondary';
                                        $statusText = $row['status'];
                                        if ($row['status'] == 'ACTIVE') {
                                            $badge = 'success';
                                            $statusText = 'Ativa';
                                        }
                                        if ($row['status'] == 'OVERDUE') {
                                            $badge = 'danger';
                                            $statusText = 'Atrasada';
                                        }
                                        ?>
                                        <span class="badge bg-<?php echo $badge; ?> bg-opacity-10 text-<?php echo $badge; ?>">
                                            <?php echo $statusText; ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php echo $row['ciclo']; ?><br>
                                        R$ <?php echo number_format($row['valor'], 2, ',', '.'); ?>
                                    </td>
                                    <td class="text-muted small">
                                        <?php echo date('d/m/Y', strtotime($row['next_due_date'])); ?>
                                    </td>
                                    <td>
                                        <small class="text-monospace text-muted">
                                            <?php echo $row['asaas_id']; ?>
                                        </small>
                                    </td>
                                    <td class="text-end pe-4 text-nowrap">
                                        <a href="javascript:void(0);" class="btn btn-sm btn-outline-danger"
                                            title="Cancelar (Em breve)">
                                            <i class="bi bi-x-circle"></i>
                                        </a>
                                        <a href="javascript:void(0);" class="btn btn-sm btn-outline-info ms-1" title="Detalhes">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="javascript:void(0);" class="btn btn-sm btn-outline-primary ms-1"
                                            title="Editar (Em breve)">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">
                                    <i class="bi bi-wallet2 fs-1 mb-3 d-block"></i>
                                    Nenhuma assinatura encontrada.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

</div>
</div>



<?php require_once __DIR__ . '/../../../includes/footer.php'; ?>