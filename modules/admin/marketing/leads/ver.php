<?php
$pageTitle = "Detalhes do Lead";
require_once __DIR__ . '/../../../../config/config.php';
require_once __DIR__ . '/../../header.php';

// Check permission
checkPermission(['admin', 'medico', 'secretaria']);

$leadId = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if ($leadId === 0) {
    header('Location: index.php');
    exit;
}
?>

<style>
    .info-card {
        transition: transform 0.2s, box-shadow 0.2s;
        border: none;
        box-shadow: 0 2px 15px rgba(0, 0, 0, 0.05);
    }

    .info-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
    }

    .info-label {
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        color: #6c757d;
        margin-bottom: 0.25rem;
    }

    .info-value {
        font-size: 1rem;
        font-weight: 500;
        color: #212529;
    }

    .avatar-large {
        width: 80px;
        height: 80px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 2rem;
        box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
    }

    .timeline {
        position: relative;
        padding-left: 30px;
    }

    .timeline::before {
        content: '';
        position: absolute;
        left: 8px;
        top: 0;
        bottom: 0;
        width: 2px;
        background: #e9ecef;
    }

    .timeline-item {
        position: relative;
        padding-bottom: 1.5rem;
    }

    .timeline-item::before {
        content: '';
        position: absolute;
        left: -26px;
        top: 4px;
        width: 12px;
        height: 12px;
        border-radius: 50%;
        background: #fff;
        border: 3px solid #667eea;
    }
</style>

<div class="container-fluid py-4">

    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="index.php"><i class="bi bi-arrow-left me-1"></i> Base de Leads</a></li>
            <li class="breadcrumb-item active" id="breadcrumbNome">Carregando...</li>
        </ol>
    </nav>

    <!-- Header -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center gap-3">
                    <div class="avatar-large" id="avatarLead">--</div>
                    <div>
                        <h3 class="fw-bold mb-1" id="leadNome">Carregando...</h3>
                        <div class="d-flex gap-2 align-items-center flex-wrap">
                            <span class="badge bg-primary rounded-pill" id="leadStatus">-</span>
                            <span class="text-muted">|</span>
                            <small class="text-muted"><i class="bi bi-tag me-1"></i><span
                                    id="leadOrigem">-</span></small>
                        </div>
                    </div>
                </div>
                <div class="btn-group">
                    <button class="btn btn-outline-secondary" onclick="editarLead()" title="Editar">
                        <i class="bi bi-pencil"></i>
                    </button>
                    <button class="btn btn-outline-success" onclick="converterEmNegocio()" title="Converter em Negócio">
                        <i class="bi bi-arrow-right-circle"></i>
                    </button>
                    <button class="btn btn-outline-danger" onclick="excluirLead()" title="Excluir">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Content -->
    <div class="row g-4">

        <!-- Informações Básicas -->
        <div class="col-md-6">
            <div class="card info-card h-100">
                <div class="card-body">
                    <h5 class="fw-bold mb-4">
                        <i class="bi bi-person-circle text-primary me-2"></i>
                        Informações Básicas
                    </h5>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="info-label">Nome Completo</div>
                            <div class="info-value" id="infoNome">-</div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-label">Email</div>
                            <div class="info-value" id="infoEmail">-</div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-label">Telefone</div>
                            <div class="info-value" id="infoTelefone">-</div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-label">Empresa</div>
                            <div class="info-value" id="infoEmpresa">-</div>
                        </div>
                        <div class="col-md-12">
                            <div class="info-label">Origem</div>
                            <div class="info-value" id="infoOrigemFull">-</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Status e Qualificação -->
        <div class="col-md-6">
            <div class="card info-card h-100">
                <div class="card-body">
                    <h5 class="fw-bold mb-4">
                        <i class="bi bi-graph-up text-success me-2"></i>
                        Status e Qualificação
                    </h5>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="info-label">Status Atual</div>
                            <div class="info-value">
                                <span class="badge bg-primary rounded-pill" id="infoStatus">-</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-label">Score</div>
                            <div class="info-value" id="infoScore">-</div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-label">Data de Cadastro</div>
                            <div class="info-value" id="infoCriado">-</div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-label">Última Atualização</div>
                            <div class="info-value" id="infoAtualizado">-</div>
                        </div>
                        <div class="col-md-12">
                            <div class="info-label">Ativo</div>
                            <div class="info-value">
                                <span class="badge bg-success" id="infoAtivo">Sim</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Histórico de Interações -->
        <div class="col-12">
            <div class="card info-card">
                <div class="card-body">
                    <h5 class="fw-bold mb-4">
                        <i class="bi bi-clock-history text-info me-2"></i>
                        Histórico de Interações
                    </h5>

                    <div class="timeline" id="timelineLead">
                        <div class="timeline-item">
                            <small class="text-muted">Carregando histórico...</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

</div>

<script>
    const BASE_URL = "<?php echo BASE_URL; ?>";
    const LEAD_ID = <?php echo $leadId; ?>;
    let leadData = null;

    document.addEventListener('DOMContentLoaded', () => {
        carregarLead();
    });

    function carregarLead() {
        fetch(BASE_URL + 'modules/admin/marketing/leads/acoes.php?acao=buscar&id=' + LEAD_ID)
            .then(res => res.json())
            .then(response => {
                if (response.success && response.data && response.data.lead) {
                    leadData = response.data.lead;
                    preencherDados(leadData);
                } else {
                    alert('Erro ao carregar lead: ' + (response.message || 'Lead não encontrado'));
                    window.location.href = 'index.php';
                }
            })
            .catch(err => {
                console.error(err);
                alert('Erro ao carregar lead');
                window.location.href = 'index.php';
            });
    }

    function preencherDados(lead) {
        // Breadcrumb e Header
        document.getElementById('breadcrumbNome').textContent = lead.nome;
        document.getElementById('leadNome').textContent = lead.nome;

        // Avatar
        const iniciais = lead.nome.substring(0, 2).toUpperCase();
        document.getElementById('avatarLead').textContent = iniciais;

        // Status Badge
        const statusBadge = document.getElementById('leadStatus');
        statusBadge.textContent = (lead.status || 'novo').toUpperCase();
        statusBadge.className = 'badge rounded-pill ' + getStatusClass(lead.status);

        document.getElementById('leadOrigem').textContent = lead.origem || 'N/A';

        // Informações Básicas
        document.getElementById('infoNome').textContent = lead.nome;
        document.getElementById('infoEmail').textContent = lead.email || '-';
        document.getElementById('infoTelefone').textContent = lead.telefone || '-';
        document.getElementById('infoEmpresa').textContent = lead.empresa_nome || '-';
        document.getElementById('infoOrigemFull').textContent = lead.origem || '-';

        // Status e Qualificação
        const statusInfo = document.getElementById('infoStatus');
        statusInfo.textContent = (lead.status || 'novo').toUpperCase();
        statusInfo.className = 'badge rounded-pill ' + getStatusClass(lead.status);

        document.getElementById('infoScore').textContent = lead.score || '0';
        document.getElementById('infoCriado').textContent = lead.created_at ? formatarData(lead.created_at) : '-';
        document.getElementById('infoAtualizado').textContent = lead.updated_at ? formatarData(lead.updated_at) : '-';

        const ativoSpan = document.getElementById('infoAtivo');
        ativoSpan.textContent = lead.ativo == 1 ? 'Sim' : 'Não';
        ativoSpan.className = 'badge ' + (lead.ativo == 1 ? 'bg-success' : 'bg-danger');

        // Timeline (placeholder - pode ser expandido com dados reais)
        carregarHistorico(lead);
    }

    function carregarHistorico(lead) {
        const timeline = document.getElementById('timelineLead');

        let html = '';

        // Evento de criação
        if (lead.created_at) {
            html += `
                <div class="timeline-item">
                    <div class="d-flex justify-content-between">
                        <div>
                            <strong>Lead Criado</strong>
                            <p class="text-muted small mb-0">Lead adicionado ao sistema via ${lead.origem || 'Manual'}</p>
                        </div>
                        <small class="text-muted">${formatarData(lead.created_at)}</small>
                    </div>
                </div>
            `;
        }

        // Evento de atualização
        if (lead.updated_at && lead.updated_at !== lead.created_at) {
            html += `
                <div class="timeline-item">
                    <div class="d-flex justify-content-between">
                        <div>
                            <strong>Lead Atualizado</strong>
                            <p class="text-muted small mb-0">Informações do lead foram modificadas</p>
                        </div>
                        <small class="text-muted">${formatarData(lead.updated_at)}</small>
                    </div>
                </div>
            `;
        }

        if (html === '') {
            html = '<div class="timeline-item"><small class="text-muted">Nenhum histórico disponível</small></div>';
        }

        timeline.innerHTML = html;
    }

    function getStatusClass(status) {
        const classes = {
            'novo': 'bg-info text-dark',
            'qualificado': 'bg-primary',
            'cliente': 'bg-success',
            'convertido': 'bg-success',
            'descartado': 'bg-secondary'
        };
        return classes[status] || 'bg-secondary';
    }

    function formatarData(dataStr) {
        const data = new Date(dataStr);
        return data.toLocaleDateString('pt-BR') + ' às ' + data.toLocaleTimeString('pt-BR', { hour: '2-digit', minute: '2-digit' });
    }

    function editarLead() {
        window.location.href = 'index.php?edit=' + LEAD_ID;
    }

    function converterEmNegocio() {
        if (!confirm('Deseja converter este lead em um negócio?')) return;

        // TODO: Implementar conversão via API
        alert('Funcionalidade em desenvolvimento');
    }

    function excluirLead() {
        if (!confirm('Tem certeza que deseja excluir este lead? Esta ação não pode ser desfeita.')) return;

        fetch(BASE_URL + 'modules/admin/marketing/leads/acoes.php?acao=excluir', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id: LEAD_ID })
        })
            .then(res => res.json())
            .then(resp => {
                if (resp.success) {
                    alert('Lead excluído com sucesso');
                    window.location.href = 'index.php';
                } else {
                    alert('Erro ao excluir: ' + (resp.message || 'Erro desconhecido'));
                }
            });
    }
</script>

<?php require_once __DIR__ . '/../../../../includes/footer.php'; ?>