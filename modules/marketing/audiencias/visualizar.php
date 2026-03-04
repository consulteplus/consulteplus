<?php
$pageTitle = "Visualizar Audiência - Marketing";
require_once __DIR__ . '/../../includes/header.php';

// Verificar permissões
if (!hasPermission(['admin', 'superadmin'])) {
    header('Location: ' . BASE_URL . 'dashboard');
    exit;
}

// Buscar audiência
if (!isset($_GET['id'])) {
    header('Location: index.php');
    exit;
}

$id = (int) $_GET['id'];
require_once __DIR__ . '/../../../../config/database.php';

$sql = "SELECT a.*, u.nome as criador_nome FROM audiencias a
        LEFT JOIN users u ON a.criado_por = u.id
        WHERE a.id = ? AND a.company_id = ?";
$stmt = $conn->prepare($sql);
$company_id = getCompanyId();
$stmt->bind_param("ii", $id, $company_id);
$stmt->execute();
$audiencia = $stmt->get_result()->fetch_assoc();

if (!$audiencia) {
    header('Location: index.php');
    exit;
}

$filtros = json_decode($audiencia['filtros_json'], true);
?>

<div class="mb-4">
    <a href="index.php" class="btn btn-outline-secondary btn-sm mb-3">
        <i class="bi bi-arrow-left me-2"></i>Voltar
    </a>
    <div class="d-flex justify-content-between align-items-start">
        <div>
            <h2 class="mb-1">
                <i class="bi bi-funnel me-2"></i>
                <?php echo htmlspecialchars($audiencia['nome']); ?>
            </h2>
            <?php if ($audiencia['descricao']): ?>
                <p class="text-muted mb-0">
                    <?php echo htmlspecialchars($audiencia['descricao']); ?>
                </p>
            <?php endif; ?>
        </div>
        <div class="d-flex gap-2">
            <a href="editar.php?id=<?php echo $id; ?>" class="btn btn-outline-primary">
                <i class="bi bi-pencil me-2"></i>Editar
            </a>
            <a href="exportar.php?id=<?php echo $id; ?>" class="btn btn-success">
                <i class="bi bi-download me-2"></i>Exportar
            </a>
        </div>
    </div>
</div>

<!-- Informações da Audiência -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <h6 class="text-muted mb-2">Tipo</h6>
                <?php if ($audiencia['tipo'] === 'dinamica'): ?>
                    <span class="badge bg-info fs-6">Dinâmica</span>
                <?php else: ?>
                    <span class="badge bg-secondary fs-6">Estática</span>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <h6 class="text-muted mb-2">Total de Leads</h6>
                <h4 class="mb-0" id="totalLeads">
                    <span class="spinner-border spinner-border-sm"></span>
                </h4>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <h6 class="text-muted mb-2">Criado por</h6>
                <p class="mb-0">
                    <?php echo htmlspecialchars($audiencia['criador_nome'] ?? 'Sistema'); ?>
                </p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <h6 class="text-muted mb-2">Criado em</h6>
                <p class="mb-0">
                    <?php echo date('d/m/Y H:i', strtotime($audiencia['criado_em'])); ?>
                </p>
            </div>
        </div>
    </div>
</div>

<!-- Filtros Aplicados -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <h5 class="card-title mb-3">Filtros Aplicados</h5>

        <?php if (!empty($filtros['filtros'])): ?>
            <div class="alert alert-light">
                <strong>Condição:</strong>
                <?php echo $filtros['condicao'] === 'AND' ? 'Atender TODOS os filtros' : 'Atender QUALQUER filtro'; ?>
            </div>

            <div class="list-group">
                <?php foreach ($filtros['filtros'] as $index => $filtro): ?>
                    <div class="list-group-item">
                        <div class="d-flex align-items-center">
                            <span class="badge bg-primary me-3">
                                <?php echo $index + 1; ?>
                            </span>
                            <div>
                                <strong>
                                    <?php echo ucfirst($filtro['campo']); ?>
                                </strong>
                                <span class="text-muted">
                                    <?php echo $filtro['operador']; ?>
                                </span>
                                <?php if (is_array($filtro['valor'])): ?>
                                    <code><?php echo implode(', ', $filtro['valor']); ?></code>
                                <?php elseif ($filtro['valor']): ?>
                                    <code><?php echo htmlspecialchars($filtro['valor']); ?></code>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p class="text-muted mb-0">Nenhum filtro configurado</p>
        <?php endif; ?>
    </div>
</div>

<!-- Tabela de Leads -->
<div class="card border-0 shadow-sm">
    <div class="card-body">
        <h5 class="card-title mb-4">Leads da Audiência</h5>

        <div class="table-responsive">
            <table id="tabelaLeads" class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>Nome</th>
                        <th>Email</th>
                        <th>Telefone</th>
                        <th>Origem</th>
                        <th>Status</th>
                        <th>Data de Criação</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Preenchido via AJAX -->
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../../../../includes/footer.php'; ?>

<script>
    const audienciaId = <?php echo $id; ?>;

    $(document).ready(function () {
        carregarLeads();
    });

    function carregarLeads() {
        $.ajax({
            url: 'acoes.php?acao=leads&id=' + audienciaId,
            method: 'GET',
            dataType: 'json',
            success: function (response) {
                if (response.success && response.data) {
                    const leads = response.data.leads;

                    // Atualizar total
                    $('#totalLeads').text(leads.length);

                    // Destruir DataTable se já existir
                    if ($.fn.DataTable.isDataTable('#tabelaLeads')) {
                        $('#tabelaLeads').DataTable().destroy();
                    }

                    // Preencher tabela
                    const tbody = $('#tabelaLeads tbody');
                    tbody.empty();

                    leads.forEach(lead => {
                        const statusBadge = getStatusBadge(lead.status);
                        const dataFormatada = new Date(lead.created_at).toLocaleDateString('pt-BR');

                        const row = `
                    <tr>
                        <td><strong>${lead.nome}</strong></td>
                        <td>${lead.email || '-'}</td>
                        <td>${lead.telefone || '-'}</td>
                        <td><span class="badge bg-secondary">${lead.origem || '-'}</span></td>
                        <td>${statusBadge}</td>
                        <td>${dataFormatada}</td>
                    </tr>
                `;
                        tbody.append(row);
                    });

                    // Inicializar DataTable
                    $('#tabelaLeads').DataTable({
                        language: {
                            url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/pt-BR.json'
                        },
                        order: [[5, 'desc']], // Ordenar por data de criação
                        pageLength: 25
                    });
                }
            },
            error: function () {
                alert('Erro ao carregar leads');
            }
        });
    }

    function getStatusBadge(status) {
        const badges = {
            'novo': '<span class="badge bg-info">Novo</span>',
            'qualificado': '<span class="badge bg-success">Qualificado</span>',
            'contatado': '<span class="badge bg-primary">Contatado</span>',
            'perdido': '<span class="badge bg-danger">Perdido</span>'
        };
        return badges[status] || `<span class="badge bg-secondary">${status}</span>`;
    }
</script>