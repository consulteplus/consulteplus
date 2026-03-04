<?php
$pageTitle = "Audiências - Marketing";
require_once __DIR__ . '/../../includes/header.php';

// Verificar permissões
if (!hasPermission(['admin', 'superadmin'])) {
    header('Location: ' . BASE_URL . 'dashboard');
    exit;
}
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="mb-1"><i class="bi bi-funnel me-2"></i>Audiências</h2>
        <p class="text-muted mb-0">Segmente seus leads com filtros personalizados</p>
    </div>
    <a href="criar.php" class="btn btn-primary">
        <i class="bi bi-plus-circle me-2"></i>Nova Audiência
    </a>
</div>

<!-- Estatísticas -->
<div class="row mb-4">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <div class="bg-primary bg-opacity-10 rounded-3 p-3">
                            <i class="bi bi-list-check fs-3 text-primary"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="text-muted mb-1">Total de Audiências</h6>
                        <h3 class="mb-0" id="totalAudiencias">0</h3>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <div class="bg-success bg-opacity-10 rounded-3 p-3">
                            <i class="bi bi-people fs-3 text-success"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="text-muted mb-1">Leads Segmentados</h6>
                        <h3 class="mb-0" id="totalLeads">0</h3>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <div class="bg-info bg-opacity-10 rounded-3 p-3">
                            <i class="bi bi-lightning fs-3 text-info"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="text-muted mb-1">Audiências Dinâmicas</h6>
                        <h3 class="mb-0" id="totalDinamicas">0</h3>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Tabela de Audiências -->
<div class="card border-0 shadow-sm">
    <div class="card-body">
        <div class="table-responsive">
            <table id="tabelaAudiencias" class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>Nome</th>
                        <th>Descrição</th>
                        <th>Tipo</th>
                        <th>Total de Leads</th>
                        <th>Criado em</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Preenchido via AJAX -->
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal de Confirmação de Exclusão -->
<div class="modal fade" id="modalExcluir" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirmar Exclusão</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Tem certeza que deseja excluir a audiência <strong id="nomeAudienciaExcluir"></strong>?</p>
                <p class="text-muted mb-0">Esta ação não pode ser desfeita.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-danger" id="btnConfirmarExcluir">Excluir</button>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../../../../includes/footer.php'; ?>

<script>
    let audienciaIdExcluir = null;

    $(document).ready(function () {
        carregarAudiencias();
    });

    function carregarAudiencias() {
        $.ajax({
            url: 'acoes.php?acao=listar',
            method: 'GET',
            dataType: 'json',
            success: function (response) {
                if (response.success) {
                    const audiencias = response.data;

                    // Atualizar estatísticas
                    $('#totalAudiencias').text(audiencias.length);

                    let totalLeads = 0;
                    let totalDinamicas = 0;

                    audiencias.forEach(a => {
                        totalLeads += parseInt(a.total_leads) || 0;
                        if (a.tipo === 'dinamica') totalDinamicas++;
                    });

                    $('#totalLeads').text(totalLeads.toLocaleString('pt-BR'));
                    $('#totalDinamicas').text(totalDinamicas);

                    // Destruir DataTable se já existir
                    if ($.fn.DataTable.isDataTable('#tabelaAudiencias')) {
                        $('#tabelaAudiencias').DataTable().destroy();
                    }

                    // Preencher tabela
                    const tbody = $('#tabelaAudiencias tbody');
                    tbody.empty();

                    audiencias.forEach(audiencia => {
                        const tipoBadge = audiencia.tipo === 'dinamica'
                            ? '<span class="badge bg-info">Dinâmica</span>'
                            : '<span class="badge bg-secondary">Estática</span>';

                        const dataFormatada = new Date(audiencia.criado_em).toLocaleDateString('pt-BR');

                        const row = `
                    <tr>
                        <td><strong>${audiencia.nome}</strong></td>
                        <td>${audiencia.descricao || '-'}</td>
                        <td>${tipoBadge}</td>
                        <td><span class="badge bg-primary">${audiencia.total_leads || 0} leads</span></td>
                        <td>${dataFormatada}</td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="visualizar.php?id=${audiencia.id}" class="btn btn-outline-primary" title="Visualizar">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="editar.php?id=${audiencia.id}" class="btn btn-outline-secondary" title="Editar">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <a href="exportar.php?id=${audiencia.id}" class="btn btn-outline-success" title="Exportar">
                                    <i class="bi bi-download"></i>
                                </a>
                                <button class="btn btn-outline-danger" onclick="confirmarExclusao(${audiencia.id}, '${audiencia.nome}')" title="Excluir">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                `;
                        tbody.append(row);
                    });

                    // Inicializar DataTable
                    $('#tabelaAudiencias').DataTable({
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
                        order: [[4, 'desc']], // Ordenar por data de criação
                        pageLength: 25
                    });
                }
            },
            error: function () {
                alert('Erro ao carregar audiências');
            }
        });
    }

    function confirmarExclusao(id, nome) {
        audienciaIdExcluir = id;
        $('#nomeAudienciaExcluir').text(nome);
        $('#modalExcluir').modal('show');
    }

    $('#btnConfirmarExcluir').click(function () {
        if (!audienciaIdExcluir) return;

        $.ajax({
            url: 'acoes.php?acao=excluir',
            method: 'POST',
            contentType: 'application/json',
            data: JSON.stringify({ id: audienciaIdExcluir }),
            dataType: 'json',
            success: function (response) {
                if (response.success) {
                    $('#modalExcluir').modal('hide');
                    carregarAudiencias();

                    // Mostrar mensagem de sucesso
                    const alert = `
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle me-2"></i>Audiência excluída com sucesso!
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            `;
                    $('.container-fluid').prepend(alert);
                } else {
                    alert('Erro ao excluir audiência: ' + response.message);
                }
            },
            error: function () {
                alert('Erro ao excluir audiência');
            }
        });
    });
</script>