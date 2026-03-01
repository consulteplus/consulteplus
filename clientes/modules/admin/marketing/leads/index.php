<?php
$pageTitle = "Base de Leads";
require_once __DIR__ . '/../../../../config/config.php';
require_once __DIR__ . '/../../header.php';

// Check permission
checkPermission(['admin', 'medico', 'secretaria']);

?>

<style>
    .lead-card {
        transition: transform 0.2s, box-shadow 0.2s;
        border: none;
        box-shadow: 0 2px 15px rgba(0, 0, 0, 0.05);
    }

    .lead-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
    }

    .avatar-initials {
        width: 35px;
        height: 35px;
        background: #e9ecef;
        color: #495057;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
        font-size: 0.85rem;
    }
</style>

<div class="container-fluid py-4">

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1">Base de Leads</h4>
            <p class="text-muted small mb-0">Gerencie seus contatos e oportunidades de marketing.</p>
        </div>
        <div class="d-flex gap-2">
            <a href="../import/index.php" class="btn btn-outline-primary shadow-sm">
                <i class="bi bi-upload me-1"></i> Importar
            </a>
            <button class="btn btn-primary shadow-sm" onclick="abrirModalLead()">
                <i class="bi bi-plus-lg me-1"></i> Novo Lead
            </button>
        </div>
    </div>

    <!-- KPIs Rápidos -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card lead-card p-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <small class="text-muted fw-bold text-uppercase">Total de Leads</small>
                        <h3 class="fw-bold mb-0 mt-1" id="kpiTotal">-</h3>
                    </div>
                    <div class="bg-primary bg-opacity-10 text-primary p-3 rounded-circle">
                        <i class="bi bi-people fs-4"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card lead-card p-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <small class="text-muted fw-bold text-uppercase">Novos</small>
                        <h3 class="fw-bold mb-0 mt-1 text-info" id="kpiNovos">-</h3>
                    </div>
                    <div class="bg-info bg-opacity-10 text-info p-3 rounded-circle">
                        <i class="bi bi-star fs-4"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card lead-card p-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <small class="text-muted fw-bold text-uppercase">Qualificados</small>
                        <h3 class="fw-bold mb-0 mt-1 text-success" id="kpiQualificados">-</h3>
                    </div>
                    <div class="bg-success bg-opacity-10 text-success p-3 rounded-circle">
                        <i class="bi bi-check-circle fs-4"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabela -->
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive p-3">
                <table id="tableLeads" class="table table-hover align-middle mb-0 w-100">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4">Nome / Email</th>
                            <th>Telefone</th>
                            <th>Empresa</th>
                            <th>Origem</th>
                            <th>Status</th>
                            <th>Data Cadastro</th>
                            <th class="text-end pe-4">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- DataTables will populate this -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

<!-- Modal Lead -->
<div class="modal fade" id="modalLead" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold" id="modalLeadTitulo">Novo Lead</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="formLead">
                    <input type="hidden" id="leadId" name="id">

                    <div class="mb-3">
                        <label class="form-label small text-muted fw-bold">Nome Completo</label>
                        <input type="text" class="form-control" name="nome" required>
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-md-6">
                            <label class="form-label small text-muted fw-bold">Email</label>
                            <input type="email" class="form-control" name="email">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small text-muted fw-bold">Telefone</label>
                            <input type="text" class="form-control" name="telefone">
                        </div>
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-md-6">
                            <label class="form-label small text-muted fw-bold">Origem</label>
                            <select class="form-select" name="origem">
                                <option value="">Selecione...</option>
                                <option value="Site">Site</option>
                                <option value="Instagram">Instagram</option>
                                <option value="Google">Google Ads</option>
                                <option value="Indicação">Indicação</option>
                                <option value="Evento">Evento</option>
                                <option value="Outro">Outro</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small text-muted fw-bold">Status</label>
                            <select class="form-select" name="lead_status">
                                <option value="novo">Novo</option>
                                <option value="qualificado">Qualificado</option>
                                <option value="cliente">Convertido em Cliente</option>
                                <option value="descartado">Descartado</option>
                            </select>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary rounded-pill px-4" onclick="salvarLead()">Salvar</button>
            </div>
        </div>
    </div>
</div>

<script>
    const BASE_URL = "<?php echo BASE_URL; ?>";
    let modalLeadInst;
    let tableLeads;

    document.addEventListener('DOMContentLoaded', () => {
        modalLeadInst = new bootstrap.Modal(document.getElementById('modalLead'));
        initDataTable();

        // Load KPIs separately since DataTables handles the list
        loadKPIs();
    });

    function initDataTable() {
        tableLeads = $('#tableLeads').DataTable({
            language: {
                "sEmptyTable": "Nenhum registro encontrado",
                "sInfo": "Mostrando de _START_ até _END_ de _TOTAL_ registros",
                "sInfoEmpty": "Mostrando 0 até 0 de 0 registros",
                "sInfoFiltered": "(Filtrados de _MAX_ registros)",
                "sInfoPostFix": "",
                "sInfoThousands": ".",
                "sLengthMenu": "_MENU_ resultados por página",
                "sLoadingRecords": "Carregando...",
                "sProcessing": "Processando...",
                "sZeroRecords": "Nenhum registro encontrado",
                "sSearch": "Pesquisar",
                "oPaginate": {
                    "sNext": "Próximo",
                    "sPrevious": "Anterior",
                    "sFirst": "Primeiro",
                    "sLast": "Último"
                },
                "oAria": {
                    "sSortAscending": ": Ordenar colunas de forma ascendente",
                    "sSortDescending": ": Ordenar colunas de forma descendente"
                }
            },
            ajax: {
                url: BASE_URL + 'modules/admin/marketing/leads/acoes.php?acao=listar',
                dataSrc: function (json) {
                    return json.data.leads || [];
                }
            },
            order: [[5, 'desc']], // Sort by Created At
            columns: [
                {
                    data: null,
                    render: function (data, type, row) {
                        let iniciais = row.nome.substring(0, 2).toUpperCase();
                        return `
                        <div class="d-flex align-items-center ps-2">
                            <div class="avatar-initials me-3">${iniciais}</div>
                            <div>
                                <div class="fw-bold text-dark">${row.nome}</div>
                                <small class="text-muted">${row.email || '-'}</small>
                            </div>
                        </div>`;
                    }
                },
                { data: 'telefone', render: function (data) { return data || '-'; } },
                {
                    data: 'empresa_nome',
                    render: function (data) {
                        return `<small class="text-muted fw-bold">${data || '-'}</small>`;
                    }
                },
                {
                    data: 'origem',
                    render: function (data) {
                        return `<span class="badge bg-light text-dark border">${data || 'N/A'}</span>`;
                    }
                },
                {
                    data: 'lead_status',
                    render: function (data) {
                        let statusBadge = 'bg-secondary';
                        if (data === 'novo') statusBadge = 'bg-info text-dark';
                        if (data === 'qualificado') statusBadge = 'bg-primary';
                        if (data === 'cliente') statusBadge = 'bg-success';
                        return `<span class="badge ${statusBadge} rounded-pill">${data.toUpperCase()}</span>`;
                    }
                },
                {
                    data: 'created_at',
                    render: function (data) {
                        return data ? new Date(data).toLocaleDateString('pt-BR') : '-';
                    }
                },
                {
                    data: 'id',
                    className: 'text-end pe-4',
                    orderable: false,
                    render: function (data, type, row) {
                        let json = JSON.stringify(row).replace(/"/g, '&quot;');
                        return `
                        <div class="btn-group btn-group-sm">
                            <a href="${BASE_URL}admin/marketing/leads/${data}" class="btn btn-outline-primary" title="Visualizar">
                                <i class="bi bi-eye"></i>
                            </a>
                            <button class="btn btn-outline-secondary" onclick='editarLead(${json})' title="Editar">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <button class="btn btn-outline-danger" onclick="excluirLead(${data})" title="Excluir">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                        `;
                    }
                }
            ]
        });
    }

    function loadKPIs() {
        fetch(BASE_URL + 'modules/admin/marketing/leads/acoes.php?acao=listar')
            .then(res => res.json())
            .then(response => {
                if (response.success && response.data && response.data.stats) {
                    updateKPIs(response.data.stats);
                }
            });
    }

    function reloadTable() {
        tableLeads.ajax.reload(null, false); // Reload without resetting page
        loadKPIs(); // Refresh KPIs too
    }

    function updateKPIs(stats) {
        document.getElementById('kpiTotal').innerText = stats.total || 0;
        document.getElementById('kpiNovos').innerText = stats.novos || 0;
        document.getElementById('kpiQualificados').innerText = stats.qualificados || 0;
    }

    function abrirModalLead() {
        document.getElementById('formLead').reset();
        document.getElementById('leadId').value = '';
        document.getElementById('modalLeadTitulo').innerText = 'Novo Lead';
        modalLeadInst.show();
    }

    function editarLead(lead) {
        document.getElementById('leadId').value = lead.id;
        document.querySelector('[name=nome]').value = lead.nome;
        document.querySelector('[name=email]').value = lead.email;
        document.querySelector('[name=telefone]').value = lead.telefone;
        document.querySelector('[name=origem]').value = lead.origem;
        document.querySelector('[name=lead_status]').value = lead.lead_status;

        document.getElementById('modalLeadTitulo').innerText = 'Editar Lead';
        modalLeadInst.show();
    }

    function salvarLead() {
        const form = document.getElementById('formLead');
        const formData = new FormData(form);
        const data = Object.fromEntries(formData.entries());

        fetch(BASE_URL + 'modules/admin/marketing/leads/acoes.php?acao=salvar', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data)
        })
            .then(res => res.json())
            .then(resp => {
                if (resp.success) {
                    modalLeadInst.hide();
                    reloadTable();
                } else {
                    alert('Erro: ' + (resp.message || 'Erro desconhecido'));
                }
            });
    }

    function excluirLead(id) {
        if (!confirm('Deseja excluir este lead?')) return;

        fetch(BASE_URL + 'modules/admin/marketing/leads/acoes.php?acao=excluir', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id: id })
        })
            .then(res => res.json())
            .then(resp => {
                if (resp.success) reloadTable();
                else alert('Erro: ' + (resp.message || 'Erro desconhecido'));
            });
    }
</script>

<?php require_once __DIR__ . '/../../../../includes/footer.php'; ?>