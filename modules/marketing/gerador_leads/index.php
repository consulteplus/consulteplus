<?php
require_once __DIR__ . '/../../../includes/header.php';
require_once __DIR__ . '/../../../../config/config.php';

// Carregar Configurações Atuais
$configFile = __DIR__ . '/../../../../config/n8n_config.json';
$configData = [];
if (file_exists($configFile)) {
    $configData = json_decode(file_get_contents($configFile), true);
}
?>



<div class="container-fluid mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold text-dark mb-1">
                <i class="bi bi-robot me-2"></i>Gerador de Leads
            </h4>
            <p class="text-muted mb-0">Use inteligência artificial para encontrar novos contatos.</p>
        </div>
        <div>
            <button class="btn btn-outline-dark me-2" onclick="abrirConfiguracoes()">
                <i class="bi bi-gear-fill me-1"></i> Configurações
            </button>
        </div>
    </div>

    <div class="card premium-card shadow-sm border-0">
        <div class="card-header bg-white border-bottom">
            <ul class="nav nav-tabs premium-tabs card-header-tabs" id="myTab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active fw-bold" id="busca-tab" data-bs-toggle="tab"
                        data-bs-target="#busca-pane" type="button" role="tab">
                        <i class="bi bi-search me-1"></i> Nova Busca
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link fw-bold" id="resultados-tab" data-bs-toggle="tab"
                        data-bs-target="#resultados-pane" type="button" role="tab" onclick="carregarResultados()">
                        <i class="bi bi-list-check me-1"></i> Resultados / Revisão
                    </button>
                </li>
            </ul>
        </div>
        <div class="card-body p-4">
            <div class="tab-content" id="myTabContent">

                <!-- ABA BUSCA -->
                <div class="tab-pane fade show active" id="busca-pane" role="tabpanel">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="list-group premium-list" id="list-tab" role="tablist">
                                <a class="list-group-item list-group-item-action active d-flex align-items-center"
                                    id="list-maps-list" data-bs-toggle="list" href="#list-maps" role="tab">
                                    <i class="bi bi-geo-alt"></i>
                                    <div>
                                        <div class="fw-bold">Google Maps</div>
                                        <small>Negócios Locais</small>
                                    </div>
                                </a>
                                <a class="list-group-item list-group-item-action d-flex align-items-center"
                                    id="list-linkedin-list" data-bs-toggle="list" href="#list-linkedin" role="tab">
                                    <i class="bi bi-linkedin"></i>
                                    <div>
                                        <div class="fw-bold">LinkedIn</div>
                                        <small>Perfis Profissionais</small>
                                    </div>
                                </a>
                                <a class="list-group-item list-group-item-action d-flex align-items-center"
                                    id="list-whatsapp-list" data-bs-toggle="list" href="#list-whatsapp" role="tab">
                                    <i class="bi bi-whatsapp"></i>
                                    <div>
                                        <div class="fw-bold">WhatsApp</div>
                                        <small>Grupos e Contatos</small>
                                    </div>
                                </a>
                            </div>
                        </div>
                        <div class="col-md-9">
                            <div class="tab-content pt-2" id="nav-tabContent">

                                <!-- Google Maps Form -->
                                <div class="tab-pane fade show active" id="list-maps" role="tabpanel">
                                    <div class="card border-0 bg-transparent">
                                        <div class="card-body">
                                            <h5 class="card-title mb-3">Buscar no Google Maps</h5>
                                            <form id="formMaps"
                                                onsubmit="event.preventDefault(); iniciarScraping('google_maps');">
                                                <div class="row g-3">
                                                    <div class="col-md-6">
                                                        <label class="form-label fw-bold">O que você procura?</label>
                                                        <input type="text" class="form-control" id="mapsKeyword"
                                                            placeholder="Ex: Dentistas, Oficinas, Restaurantes"
                                                            required>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="form-label fw-bold">Localização</label>
                                                        <input type="text" class="form-control" id="mapsLocation"
                                                            placeholder="Ex: Centro, São Paulo - SP" required>
                                                    </div>
                                                    <div class="col-12 text-end">
                                                        <button type="submit" class="btn btn-danger text-white px-4">
                                                            <i class="bi bi-play-fill me-1"></i> Iniciar Extração
                                                        </button>
                                                    </div>
                                                </div>
                                            </form>
                                            <div class="alert alert-info mt-3 small mb-0">
                                                <i class="bi bi-info-circle me-1"></i> O processo roda em segundo plano.
                                                Você será notificado ou poderá ver na aba "Resultados".
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- LinkedIn Form -->
                                <div class="tab-pane fade" id="list-linkedin" role="tabpanel">
                                    <div class="card border-0 bg-transparent">
                                        <div class="card-body">
                                            <h5 class="card-title mb-3">Buscar no LinkedIn</h5>
                                            <form id="formLinkedin"
                                                onsubmit="event.preventDefault(); iniciarScraping('linkedin');">
                                                <div class="row g-3">
                                                    <div class="col-md-6">
                                                        <label class="form-label">Cargo / Palavra-chave</label>
                                                        <input type="text" class="form-control" id="linkedinRole"
                                                            placeholder="Ex: Diretor Comercial" required>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="form-label">Empresa ou Setor</label>
                                                        <input type="text" class="form-control" id="linkedinIndustry"
                                                            placeholder="Ex: Tecnologia, Varejo">
                                                    </div>
                                                    <div class="col-12 text-end">
                                                        <button type="submit" class="btn btn-primary px-4">
                                                            <i class="bi bi-search me-1"></i> Buscar Perfis
                                                        </button>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>

                                <!-- WhatsApp Form (Integration) -->
                                <div class="tab-pane fade" id="list-whatsapp" role="tabpanel">
                                    <div class="card border-0 bg-transparent">
                                        <div class="card-body">
                                            <h5 class="card-title mb-3">Integração WhatsApp</h5>

                                            <!-- State 1: Disconnected -->
                                            <div id="whatsappDisconnected">
                                                <p class="text-muted">Conecte seu WhatsApp para extrair contatos de
                                                    grupos.</p>
                                                <div class="text-center py-4">
                                                    <button id="btnConnectWpp" class="btn btn-success px-4 py-2"
                                                        onclick="conectarWhatsapp()">
                                                        <i class="bi bi-qr-code-scan me-1"></i> Conectar WhatsApp
                                                    </button>
                                                </div>

                                                <div id="qrCodeContainer" class="text-center mt-3 d-none">
                                                    <p id="wppStatusText" class="fw-bold text-primary">Gerando QR
                                                        Code...</p>
                                                    <img id="qrImage" src="" alt="QR Code"
                                                        class="img-fluid border p-2 bg-white" style="max-width: 250px;">
                                                </div>
                                            </div>

                                            <!-- State 2: Connected -->
                                            <div id="whatsappConnected" class="d-none">
                                                <div
                                                    class="alert alert-success d-flex align-items-center justify-content-between">
                                                    <div class="d-flex align-items-center">
                                                        <i class="bi bi-check-circle-fill fs-4 me-3"></i>
                                                        <div>
                                                            <strong>Conectado com Sucesso!</strong><br>
                                                            <small>Número: <span id="wppConnectedNumber"
                                                                    class="fw-bold">...</span></small>
                                                        </div>
                                                    </div>
                                                    <button class="btn btn-sm btn-outline-danger"
                                                        onclick="desconectarWhatsapp()">
                                                        Desconectar
                                                    </button>
                                                </div>

                                                <hr>
                                                <div class="d-flex justify-content-between align-items-center mb-3">
                                                    <h6 class="mb-0 fw-bold">Seus Grupos</h6>
                                                    <button class="btn btn-sm btn-primary" onclick="listarGrupos()">
                                                        <i class="bi bi-arrow-clockwise"></i> Carregar Grupos
                                                    </button>
                                                </div>

                                                <div class="table-responsive bg-white rounded shadow-sm">
                                                    <table class="table table-hover mb-0">
                                                        <thead class="table-light">
                                                            <tr>
                                                                <th>Nome do Grupo</th>
                                                                <th>ID</th>
                                                                <th>Participantes</th>
                                                                <th width="100">Ação</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody id="tableGroupsBody">
                                                            <tr>
                                                                <td colspan="4" class="text-center text-muted py-3">
                                                                    Clique em "Carregar Grupos" para listar.
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>

                                        </div>

                                        <!-- Debug Log Area -->
                                        <div id="debugLog" class="alert alert-secondary mt-3 small font-monospace"
                                            style="white-space: pre-wrap; display:none;">Log de Debug aparecerá aqui...
                                        </div>
                                        <div class="form-check mt-2">
                                            <input class="form-check-input" type="checkbox" id="showDebug"
                                                onchange="document.getElementById('debugLog').style.display = this.checked ? 'block' : 'none'"
                                                checked>
                                            <label class="form-check-label text-muted small" for="showDebug">Mostrar Log
                                                de Debug</label>
                                        </div>

                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>

            <!-- ABA RESULTADOS -->
            <div class="tab-pane fade" id="resultados-pane" role="tabpanel">
                <div class="d-flex justify-content-between mb-3">
                    <button class="btn btn-sm btn-outline-secondary" onclick="carregarResultados()">
                        <i class="bi bi-arrow-clockwise"></i> Atualizar
                    </button>
                    <div>
                        <button class="btn btn-sm btn-success text-white" onclick="aprovarSelecionados()">
                            <i class="bi bi-check-lg"></i> Aprovar Selecionados
                        </button>
                        <button class="btn btn-sm btn-danger text-white" onclick="rejeitarSelecionados()">
                            <i class="bi bi-x-lg"></i> Rejeitar Selecionados
                        </button>
                    </div>
                </div>

                <div class="table-responsive">
                    <table id="tabelaResultados" class="table premium-table table-hover w-100">
                        <thead>
                            <tr>
                                <th width="30"><input type="checkbox" id="checkAll"></th>
                                <th>Nome</th>
                                <th>Contato</th>
                                <th>Origem</th>
                                <th>Status</th>
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
    </div>
</div>
</div>

<!-- Modal Configurações -->
<div class="modal fade" id="modalConfig" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Configurações de Integração (n8n)</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="formConfig" onsubmit="event.preventDefault(); salvarConfig();">
                    <div class="mb-3">
                        <label class="form-label fw-bold text-danger"><i class="bi bi-geo-alt"></i> Webhook Google
                            Maps</label>
                        <input type="url" class="form-control" id="cfgMaps"
                            value="<?= htmlspecialchars($configData['maps_webhook_url'] ?? '') ?>">
                        <div class="form-text">URL POST do workflow n8n para Maps.</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold text-primary"><i class="bi bi-linkedin"></i> Webhook
                            LinkedIn</label>
                        <input type="url" class="form-control" id="cfgLinkedin"
                            value="<?= htmlspecialchars($configData['linkedin_webhook_url'] ?? '') ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold text-success"><i class="bi bi-whatsapp"></i> Webhook
                            WhatsApp</label>
                        <input type="url" class="form-control" id="cfgWhatsapp"
                            value="<?= htmlspecialchars($configData['whatsapp_webhook_url'] ?? '') ?>">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" onclick="salvarConfig()">Salvar Configurações</button>
            </div>
        </div>
    </div>
</div>

<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    const BASE_URL = "<?= BASE_URL ?>";

    // Configurações
    let modalConfig;

    document.addEventListener('DOMContentLoaded', function () {
        if (typeof bootstrap !== 'undefined') {
            modalConfig = new bootstrap.Modal(document.getElementById('modalConfig'));
        } else {
            console.error('Bootstrap JS not loaded');
        }
    });

    function abrirConfiguracoes() {
        if (modalConfig) modalConfig.show();
        else alert('Erro: Bootstrap não carregado corretamente.');
    }

    function salvarConfig() {
        const data = {
            maps_webhook_url: document.getElementById('cfgMaps').value,
            linkedin_webhook_url: document.getElementById('cfgLinkedin').value,
            whatsapp_webhook_url: document.getElementById('cfgWhatsapp').value
        };

        fetch(BASE_URL + 'marketing/gerador_leads/acoes.php?acao=salvar_config', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data)
        })
            .then(r => r.json())
            .then(res => {
                if (res.success) {
                    Swal.fire('Sucesso', 'Configurações salvas!', 'success');
                    modalConfig.hide();
                } else {
                    Swal.fire('Erro', res.error, 'error');
                }
            });
    }

    // Disparo de Scraping (Maps e Linkedin)
    function iniciarScraping(source) {
        let payload = { source: source };

        if (source === 'google_maps') {
            payload.keyword = document.getElementById('mapsKeyword').value;
            payload.location = document.getElementById('mapsLocation').value;
        } else if (source === 'linkedin') {
            payload.role = document.getElementById('linkedinRole').value;
            payload.industry = document.getElementById('linkedinIndustry').value;
        }

        Swal.fire({
            title: 'Iniciando...',
            text: 'Aguardando resposta do robô... Isso pode levar 1-2 minutos.',
            didOpen: () => Swal.showLoading()
        });

        fetch(BASE_URL + 'marketing/gerador_leads/acoes.php?acao=trigger_scraping', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(payload)
        })
            .then(r => r.json())
            .then(res => {
                Swal.close();
                if (res.success) {
                    Swal.fire({
                        title: 'Concluído!',
                        text: res.message + ' Clique para ver os detalhes.',
                        icon: 'success',
                        confirmButtonText: 'Ver Relatório Debug'
                    }).then(() => {
                        window.open('/consulteplus/admin/marketing/gerador_leads/visualizar_retorno', '_blank');
                        carregarResultados();
                    });
                } else {
                    Swal.fire('Erro', res.error, 'error');
                }
            })
            .catch(err => Swal.fire('Erro', 'Falha na comunicação com o servidor.', 'error'));
    }

    // --- WHATSAPP (UAZAPI) FUNCTIONS ---

    let statusInterval;

    function conectarWhatsapp() {
        document.getElementById('btnConnectWpp').disabled = true;
        document.getElementById('btnConnectWpp').innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Iniciando...';

        // 1. Iniciar Conexão
        fetch(BASE_URL + 'marketing/gerador_leads/api_whatsapp.php?action=connect')
            .then(r => r.json())
            .then(res => {
                if (res.success) {
                    // Iniciar Polling de Status para pegar QR Code
                    document.getElementById('qrCodeContainer').classList.remove('d-none');
                    verificarStatus();
                    statusInterval = setInterval(verificarStatus, 3000); // Check every 3s
                } else {
                    Swal.fire('Erro', 'Falha ao iniciar conexão: ' + res.error, 'error');
                    resetWppButton();
                }
            })
            .catch(err => {
                Swal.fire('Erro', 'Erro de rede ao conectar.', 'error');
                resetWppButton();
            });
    }

    function resetWppButton() {
        document.getElementById('btnConnectWpp').disabled = false;
        document.getElementById('btnConnectWpp').innerHTML = '<i class="bi bi-qr-code-scan me-1"></i> Conectar WhatsApp';
    }

    function verificarStatus() {
        const debugEl = document.getElementById('debugLog');
        if (debugEl) debugEl.innerText = "Verificando status...";

        fetch(BASE_URL + 'marketing/gerador_leads/api_whatsapp.php?action=status')
            .then(r => r.json())
            .then(res => {
                console.log("Status Check:", res);
                if (debugEl) debugEl.innerText = JSON.stringify(res, null, 2);

                if (res.success) {
                    const status = res.status.status || res.status; // Pode variar dependendo da resposta exata

                    if (status === 'open' || status === 'connected') { // 'open' is common in some whatsapp apis
                        clearInterval(statusInterval);
                        document.getElementById('whatsappDisconnected').classList.add('d-none');
                        document.getElementById('whatsappConnected').classList.remove('d-none');

                        // Exibir número se disponível
                        if (res.status.number) {
                            document.getElementById('wppConnectedNumber').innerText = res.status.number;
                        } else {
                            document.getElementById('wppConnectedNumber').innerText = 'Desconhecido';
                        }

                        listarGrupos();
                    } else if (res.status.qr) {
                        // Atualizar QR Code
                        document.getElementById('qrImage').src = res.status.qr;
                        document.getElementById('wppStatusText').innerText = 'Escaneie o QR Code no seu WhatsApp';
                    }
                }
            });
    }

    function desconectarWhatsapp() {
        Swal.fire({
            title: 'Desconectar?',
            text: "Você precisará escanear o QR Code novamente para reconectar.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Sim, desconectar',
            cancelButtonText: 'Cancelar',
            confirmButtonColor: '#d33'
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.showLoading();
                fetch(BASE_URL + 'marketing/gerador_leads/api_whatsapp.php?action=logout', { method: 'DELETE' })
                    .then(r => r.json())
                    .then(res => {
                        if (res.success) {
                            Swal.fire('Desconectado', 'Sessão encerrada com sucesso.', 'success');
                            // Reset UI
                            document.getElementById('whatsappConnected').classList.add('d-none');
                            document.getElementById('whatsappDisconnected').classList.remove('d-none');
                            document.getElementById('qrCodeContainer').classList.add('d-none');
                            resetWppButton();
                            // Limpa tabela de grupos
                            document.getElementById('tableGroupsBody').innerHTML = '<tr><td colspan="4" class="text-center text-muted">WhatsApp desconectado.</td></tr>';
                        } else {
                            Swal.fire('Erro', 'Falha ao desconectar: ' + res.error, 'error');
                        }
                    })
                    .catch(err => Swal.fire('Erro', 'Erro de comunicação.', 'error'));
            }
        });
    }


    function listarGrupos() {
        const tbody = document.getElementById('tableGroupsBody');
        const debugEl = document.getElementById('debugLog');

        tbody.innerHTML = '<tr><td colspan="4" class="text-center"><div class="spinner-border text-primary" role="status"></div> Carregando grupos...</td></tr>';

        fetch(BASE_URL + 'marketing/gerador_leads/api_whatsapp.php?action=list_groups')
            .then(r => r.json())
            .then(res => {
                console.log("Groups Check:", res);
                if (debugEl) debugEl.innerText = JSON.stringify(res, null, 2);

                if (res.success) {
                    tbody.innerHTML = '';
                    if (res.groups.length === 0) {
                        tbody.innerHTML = '<tr><td colspan="4" class="text-center text-muted">Nenhum grupo encontrado.</td></tr>';
                        return;
                    }

                    res.groups.forEach(group => {
                        const tr = document.createElement('tr');

                        // Fallback para chaves com Case diferente
                        const gName = group.Name || group.name || group.subject || 'Sem Nome';
                        const gId = group.JID || group.jid || group.id;

                        let pCount = '?';
                        if (group.Participants && Array.isArray(group.Participants) && group.Participants.length > 0) {
                            pCount = group.Participants.length;
                        } else if (group.participants && Array.isArray(group.participants) && group.participants.length > 0) {
                            pCount = group.participants.length;
                        } else if (group.ParticipantCount !== undefined) {
                            pCount = group.ParticipantCount;
                        } else if (group.participantCount !== undefined) {
                            pCount = group.participantCount;
                        }

                        tr.innerHTML = `
                        <td>${gName}</td>
                        <td>${gId}</td>
                        <td>${pCount} participantes</td>
                        <td>
                            <button class="btn btn-sm btn-success" onclick="extrairContatos('${gId}', '${gName}')">
                                <i class="bi bi-box-arrow-in-down"></i> Extrair
                            </button>
                        </td>
                    `;
                        tbody.appendChild(tr);
                    });
                } else {
                    tbody.innerHTML = `<tr><td colspan="4" class="text-center text-danger">Erro ao carregar: ${res.error}</td></tr>`;
                }
            });
    }

    function extrairContatos(groupId, groupName) {
        Swal.fire({
            title: 'Extrair Contatos',
            text: `Deseja extrair os contatos do grupo "${groupName}"?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Sim, extrair',
            showLoaderOnConfirm: true,
            preConfirm: () => {
                return fetch(BASE_URL + 'marketing/gerador_leads/acoes.php?acao=extrair_whatsapp', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ groupId: groupId, groupName: groupName })
                })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error(response.statusText)
                        }
                        return response.json()
                    })
                    .catch(error => {
                        Swal.showValidationMessage(
                            `Request failed: ${error}`
                        )
                    })
            },
            allowOutsideClick: () => !Swal.isLoading()
        }).then((result) => {
            if (result.isConfirmed) {
                if (result.value.success) {
                    Swal.fire({
                        title: 'Sucesso!',
                        text: `${result.value.imported} contatos importados de ${result.value.total_participants} participantes.`,
                        icon: 'success'
                    });
                    carregarResultados(); // Atualiza a tabela de leads
                } else {
                    Swal.fire('Erro', result.value.error || 'Falha desconhecida', 'error');
                }
            }
        });
    }

    // --- END WHATSAPP FUNCTIONS ---

    // Resultados / DataTable
    let tabelaResultados;

    document.addEventListener('DOMContentLoaded', function () {
        tabelaResultados = $('#tabelaResultados').DataTable({
            language: { url: "https://cdn.datatables.net/plug-ins/1.13.4/i18n/pt-BR.json" },
            ajax: BASE_URL + 'marketing/gerador_leads/acoes.php?acao=listar_resultados',
            columns: [
                {
                    data: 'id',
                    render: function (data) {
                        return `<input type="checkbox" class="lead-check" value="${data}">`;
                    },
                    orderable: false
                },
                { data: 'nome' },
                {
                    data: null,
                    render: function (data) {
                        let c = '';
                        if (data.email) c += `<div><i class="bi bi-envelope"></i> ${data.email}</div>`;
                        if (data.telefone) c += `<div><i class="bi bi-telephone"></i> ${data.telefone}</div>`;
                        return c;
                    }
                },
                {
                    data: 'source',
                    render: function (d) {
                        if (d == 'google_maps') return '<span class="badge bg-danger">Maps</span>';
                        if (d == 'linkedin') return '<span class="badge bg-primary">LinkedIn</span>';
                        if (d == 'whatsapp') return '<span class="badge bg-success">WhatsApp</span>';
                        return '<span class="badge bg-secondary">' + d + '</span>';
                    }
                },
                {
                    data: 'status',
                    render: function (d) {
                        if (d == 'pendente') return '<span class="badge bg-warning text-dark">Pendente</span>';
                        if (d == 'aprovado') return '<span class="badge bg-success">Aprovado</span>';
                        if (d == 'rejeitado') return '<span class="badge bg-danger">Rejeitado</span>';
                        return d;
                    }
                },
                {
                    data: null,
                    render: function (data) {
                        return `
                        <button class="btn btn-sm btn-outline-success" onclick="aprovarLead(${data.id})" title="Aprovar"><i class="bi bi-check-lg"></i></button>
                        <button class="btn btn-sm btn-outline-danger" onclick="rejeitarLead(${data.id})" title="Rejeitar"><i class="bi bi-x-lg"></i></button>
                    `;
                    }
                }
            ],
            order: [[0, "desc"]] // Created at desc ideally
        });

        $('#checkAll').on('click', function () {
            $('.lead-check').prop('checked', this.checked);
        });

        // Verificar status inicial do WhatsApp ao carregar
        verificarStatus();
    });

    function carregarResultados() {
        if (tabelaResultados) {
            tabelaResultados.ajax.reload();
        }
    }

    // Ações em Lote e Individuais
    function getSelectedIds() {
        let ids = [];
        $('.lead-check:checked').each(function () {
            ids.push($(this).val());
        });
        return ids;
    }

    function aprovarLead(id) {
        processarAcao([id], 'aprovar');
    }

    function rejeitarLead(id) {
        processarAcao([id], 'rejeitar');
    }

    function aprovarSelecionados() {
        let ids = getSelectedIds();
        if (ids.length === 0) return Swal.fire('Selecione', 'Nenhum lead selecionado.', 'warning');
        processarAcao(ids, 'aprovar');
    }

    function rejeitarSelecionados() {
        let ids = getSelectedIds();
        if (ids.length === 0) return Swal.fire('Selecione', 'Nenhum lead selecionado.', 'warning');
        processarAcao(ids, 'rejeitar');
    }

    function processarAcao(ids, tipo) {
        Swal.fire({
            title: 'Tem certeza?',
            text: `Deseja ${tipo} ${ids.length} leads?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Sim'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch(BASE_URL + 'marketing/gerador_leads/acoes.php?acao=processar_leads', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ ids: ids, tipo: tipo })
                })
                    .then(r => r.json())
                    .then(res => {
                        if (res.success) {
                            Swal.fire('Sucesso', `${res.afetados} leads processados!`, 'success');
                            carregarResultados();
                            $('#checkAll').prop('checked', false);
                        } else {
                            Swal.fire('Erro', res.error, 'error');
                        }
                    });
            }
        });
    }
</script>

<?php require_once __DIR__ . '/../../../../includes/footer.php'; ?>