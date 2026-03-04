<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../includes/header.php';

if (!isModuleEnabled('crm')) {
    redirect('dashboard?modulo_bloqueado=crm');
    exit;
}
// Menu is already included by header.php

$id_negocio = isset($_GET['id']) ? (int) $_GET['id'] : 0;
?>

<!-- Estilos CRM -->
<link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/crm.css">
<style>
    /* Chat Logic Styles - WhatsApp/Modern Style */
    .chat-wrapper {
        background-color: #efe7dd;
        /* WhatsApp BG color */
        background-image: url('https://user-images.githubusercontent.com/15075759/28719144-86dc0f70-73b1-11e7-911d-60d70fcded21.png');
        /* Subtle doodle pattern */
        background-repeat: repeat;
        border-radius: 8px;
        display: flex;
        flex-direction: column;
        height: 600px;
        /* Fixed height for scroll */
    }

    .chat-header-internal {
        background-color: #f0f2f5;
        padding: 10px 15px;
        border-bottom: 1px solid #d1d7db;
        border-radius: 8px 8px 0 0;
        display: flex;
        align-items: center;
        justify-content: space-between;
        position: sticky;
        top: 0;
        z-index: 10;
    }

    .chat-message-container {
        padding: 20px 40px;
        /* More side padding */
        display: flex;
        flex-direction: column;
        gap: 8px;
        overflow-y: auto;
        flex-grow: 1;
    }

    .msg-bubble {
        padding: 8px 12px;
        border-radius: 7.5px;
        max-width: 65%;
        position: relative;
        font-size: 0.95rem;
        box-shadow: 0 1px 0.5px rgba(0, 0, 0, 0.13);
        word-wrap: break-word;
    }

    .msg-user {
        align-self: flex-end;
        background-color: #d9fdd3;
        /* WhatsApp User Green */
        color: #111b21;
        border-bottom-right-radius: 0;
        /* Corner accent */
    }

    .msg-bot {
        align-self: flex-start;
        background-color: #ffffff;
        color: #111b21;
        border-bottom-left-radius: 0;
        /* Corner accent */
    }

    .msg-time {
        font-size: 0.70rem;
        float: right;
        margin-left: 10px;
        margin-top: 5px;
        color: #667781;
    }

    .msg-log {
        font-size: 0.8rem;
        text-align: center;
        color: #54656f;
        margin: 15px 0;
        background-color: #e6f6ff;
        /* Light blue for info */
        padding: 6px 16px;
        border-radius: 10px;
        align-self: center;
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
        border: 1px solid #cceeff;
        width: fit-content;
    }

    .msg-log i {
        color: #0099cc;
    }
</style>


<div class="container-fluid mt-4 pb-5">
    <!-- Breadcrumb & Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 bg-white px-3 py-2 rounded-pill shadow-sm">
                <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>crm/"
                        class="text-decoration-none fw-bold"><i class="bi bi-grid-fill me-1"></i> CRM</a></li>
                <li class="breadcrumb-item active text-muted" aria-current="page">Negócio #<?php echo $id_negocio; ?>
                </li>
            </ol>
        </nav>
        <span class="badge bg-light text-dark border px-3 py-2 rounded-pill"><i class="bi bi-clock me-1"></i> <span
                id="detalheDataCriacao">-</span></span>
    </div>

    <div class="row" id="loadingArea">
        <div class="col-12 text-center py-5">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Carregando...</span>
            </div>
            <p class="mt-3 text-muted fw-medium">Carregando detalhes do negócio...</p>
        </div>
    </div>

    <!-- Conteúdo Principal (Oculto até carregar) -->
    <div class="row d-none g-4" id="conteudoNegocio">

        <!-- Coluna Esquerda: Informações (Sidebar) -->
        <div class="col-lg-3">

            <!-- Card Pessoa (Lead) -->
            <div class="premium-card p-4 mb-4 text-center position-relative">
                <div class="avatar-circle">
                    <i class="bi bi-person fs-1 text-secondary"></i>
                </div>
                <h5 class="fw-bold mb-1" id="detalhePacienteNome">-</h5>
                <p class="text-muted small mb-3" id="detalhePacienteTel">-</p>

                <div class="dropdown">
                    <button class="btn btn-primary w-100 rounded-pill shadow-sm" type="button" data-bs-toggle="dropdown"
                        aria-expanded="false">
                        <i class="bi bi-plus-lg me-1"></i> Nova Interação
                    </button>
                    <ul class="dropdown-menu w-100 shadow border-0 rounded-3 mt-2">
                        <li><a class="dropdown-item py-2" href="#" onclick="abrirModalAtividade('nota')"><i
                                    class="bi bi-sticky me-2 text-warning"></i>Nota Interna</a></li>
                        <li><a class="dropdown-item py-2" href="#" onclick="abrirModalAtividade('tarefa')"><i
                                    class="bi bi-check2-square me-2 text-success"></i>Tarefa</a></li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li><a class="dropdown-item py-2" href="#" onclick="irParaAgendamento()"><i
                                    class="bi bi-calendar-event me-2 text-primary"></i>Agendar Consulta</a></li>
                    </ul>
                </div>
            </div>

            <!-- Card Empresa -->
            <div class="premium-card p-4 mb-4" id="cardEmpresa">
                <div class="d-flex align-items-center mb-3">
                    <div class="bg-light rounded-3 p-2 me-3">
                        <i class="bi bi-building fs-4 text-primary"></i>
                    </div>
                    <h6 class="fw-bold mb-0 text-uppercase small text-muted">Empresa</h6>
                </div>

                <!-- Company Info (Shown when linked) -->
                <div id="infoEmpresaContent" class="d-none">
                    <h6 class="fw-bold mb-1" id="detalheEmpresaNome">Empresa</h6>
                    <p class="text-muted small mb-0" id="detalheEmpresaDoc">-</p>
                    <button class="btn btn-sm btn-light text-primary w-100 mt-3 rounded-pill fw-bold"
                        onclick="abrirModalVincularEmpresa()">
                        <i class="bi bi-arrow-repeat me-1"></i> Alterar
                    </button>
                </div>

                <!-- Empty State (Shown when not linked) -->
                <div id="infoEmpresaEmpty" class="d-none text-center py-2">
                    <p class="text-muted small mb-3">Nenhuma empresa vinculada</p>
                    <button class="btn btn-outline-primary btn-sm w-100 rounded-pill"
                        onclick="abrirModalVincularEmpresa()">
                        <i class="bi bi-link-45deg me-1"></i> Vincular
                    </button>
                </div>
            </div>

            <!-- Detalhes do Negócio -->
            <div class="premium-card p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h6 class="fw-bold text-uppercase text-muted small mb-0">Sobre o Negócio</h6>
                    <div>
                        <button class="btn btn-sm btn-icon btn-light rounded-circle me-1" onclick="toggleEditMode()"
                            title="Editar">
                            <i class="bi bi-pencil small"></i>
                        </button>
                        <button class="btn btn-sm btn-icon btn-light text-danger rounded-circle"
                            onclick="excluirNegocio(dealId)" title="Excluir">
                            <i class="bi bi-trash small"></i>
                        </button>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="small text-muted d-block mb-1">Título</label>
                    <div class="fw-bold text-dark" id="detalheTitulo">-</div>
                    <input type="text" class="form-control d-none mt-1" id="inputTitulo">
                </div>

                <div class="mb-4">
                    <label class="small text-muted d-block mb-1">Valor Estimado</label>
                    <div class="fs-3 fw-bold text-success" id="detalheValor">R$ 0,00</div>
                    <input type="text" class="form-control d-none mt-1" id="inputValor">
                </div>

                <div class="mb-4">
                    <label class="small text-muted d-block mb-1">Fase do Funil</label>
                    <span class="badge bg-secondary fs-6 rounded-pill fw-normal px-3" id="detalheEtapa">-</span>
                </div>

                <div class="d-none" id="editActions">
                    <button class="btn btn-primary btn-sm w-100 rounded-pill mb-2"
                        onclick="salvarEdicaoDetalhes()">Salvar Alterações</button>
                    <button class="btn btn-light btn-sm w-100 rounded-pill text-muted"
                        onclick="toggleEditMode()">Cancelar</button>
                </div>
            </div>

        </div>

        <!-- Coluna Direita: Timeline (Main) -->
        <div class="col-lg-9">
            <div class="premium-card h-100 d-flex flex-column" style="min-height: 80vh;">
                <!-- Header Timeline -->
                <div class="px-4 py-4 border-bottom d-flex align-items-center bg-white rounded-top-4 flex-wrap gap-3">
                    <div class="me-auto">
                        <h5 class="fw-bold mb-0">Linha do Tempo</h5>
                        <small class="text-muted">Histórico de atividades</small>
                    </div>

                    <!-- Filters -->
                    <div class="d-flex gap-2 filter-group flex-wrap" role="group">
                        <input type="radio" class="btn-check" name="filterTimeline" id="ftAll" checked
                            onclick="filtrarTimeline('todos')">
                        <label class="btn shadow-sm" for="ftAll">Todos</label>

                        <input type="radio" class="btn-check" name="filterTimeline" id="ftNota"
                            onclick="filtrarTimeline('nota')">
                        <label class="btn shadow-sm" for="ftNota"><i class="bi bi-sticky me-1"></i>Notas</label>

                        <input type="radio" class="btn-check" name="filterTimeline" id="ftTarefa"
                            onclick="filtrarTimeline('tarefa')">
                        <label class="btn shadow-sm" for="ftTarefa"><i
                                class="bi bi-check2-square me-1"></i>Tarefas</label>

                        <input type="radio" class="btn-check" name="filterTimeline" id="ftAgend"
                            onclick="filtrarTimeline('agendamento')">
                        <label class="btn shadow-sm" for="ftAgend"><i
                                class="bi bi-calendar-event me-1"></i>Agendas</label>

                        <input type="radio" class="btn-check" name="filterTimeline" id="ftMov"
                            onclick="filtrarTimeline('movimentacao')">
                        <label class="btn shadow-sm" for="ftMov"><i class="bi bi-arrow-left-right me-1"></i>Mov.</label>

                        <input type="radio" class="btn-check" name="filterTimeline" id="ftChat"
                            onclick="filtrarTimeline('chat')">
                        <label class="btn shadow-sm" for="ftChat"><i class="bi bi-chat-dots me-1"></i>Chat</label>
                    </div>

                    <div class="dropdown">
                        <button class="btn btn-light btn-sm rounded-circle shadow-sm" type="button"
                            data-bs-toggle="dropdown">
                            <i class="bi bi-three-dots-vertical"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow border-0">
                            <li>
                                <h6 class="dropdown-header">Ações do Negócio</h6>
                            </li>
                            <li><a class="dropdown-item text-danger" href="#"
                                    onclick="excluirNegocio(<?php echo $id_negocio; ?>)">
                                    <i class="bi bi-trash me-2"></i>Excluir Negócio
                                </a></li>
                        </ul>
                    </div>
                </div>

                <!-- Timeline Feed -->
                <div class="card-body p-4 bg-light bg-opacity-10 flex-grow-1 overflow-auto">
                    <div id="detalheTimeline" class="timeline-container pe-2">
                        <!-- Injetado via JS -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Vincular Empresa -->
<div class="modal fade" id="modalVincularEmpresa" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold">Vincular Empresa</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body pt-3">
                <div class="mb-3">
                    <label class="form-label text-muted small fw-bold text-uppercase">Buscar Empresa</label>
                    <div class="position-relative">
                        <input type="text" class="form-control form-control-lg bg-light border-0" id="buscaEmpresaInput"
                            placeholder="Digite o nome da empresa...">
                        <div class="list-group position-absolute w-100 shadow mt-2 border-0 rounded-3"
                            id="resultadoBuscaEmpresa"
                            style="display:none; max-height: 250px; overflow-y: auto; z-index: 1050;"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Criar Atividade -->
<div class="modal fade" id="modalAtividade" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold" id="tituloModalAtividade">Nova Atividade</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body pt-3">
                <textarea id="textoAtividadeModal" class="form-control bg-light border-0" rows="5"
                    placeholder="Descreva os detalhes aqui..."></textarea>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary rounded-pill px-4" onclick="salvarAtividadeModal()">Salvar
                    Registro</button>
            </div>
        </div>
    </div>
</div>

<script>
    const dealId = <?php echo $id_negocio; ?>;
    const BASE_URL = "<?php echo BASE_URL; ?>"; // Inject Base URL
    let acaoAtual = 'nota';
    let pacienteId = null;
    let globalTimeline = [];

    // Global variables for Modal instances
    var modalAtividadeInst;
    var modalVincularInst;

    document.addEventListener('DOMContentLoaded', function () {
        // Fix: Mover modal para o body para evitar problemas de z-index com o backdrop
        let modalEl = document.getElementById('modalAtividade');
        if (modalEl) document.body.appendChild(modalEl);

        // Init Modals
        var elModalAtv = document.getElementById('modalAtividade');
        if (elModalAtv) modalAtividadeInst = new bootstrap.Modal(elModalAtv);

        var elModalVinc = document.getElementById('modalVincularEmpresa');
        if (elModalVinc) modalVincularInst = new bootstrap.Modal(elModalVinc);

        // Search Listener for Linking Company
        const inputBusca = document.getElementById('buscaEmpresaInput');
        const listaResultados = document.getElementById('resultadoBuscaEmpresa');
        let timeoutBusca = null;

        if (inputBusca) {
            inputBusca.addEventListener('input', function (e) {
                clearTimeout(timeoutBusca);
                const termo = e.target.value;

                if (termo.length < 2) {
                    listaResultados.style.display = 'none';
                    return;
                }

                timeoutBusca = setTimeout(() => {
                    fetch(BASE_URL + 'modules/crm/acoes.php?acao=buscar_contatos&tipo=empresa&q=' + encodeURIComponent(termo))
                        .then(res => res.json())
                        .then(response => {
                            listaResultados.innerHTML = '';
                            if (response.success && response.data && response.data.length > 0) {
                                response.data.forEach(item => {
                                    const li = document.createElement('button');
                                    li.type = 'button';
                                    li.className = 'list-group-item list-group-item-action';
                                    li.innerHTML = `<strong>${item.nome}</strong><br><small class="text-muted">${item.documento || '-'}</small>`;
                                    li.onclick = () => vincularEmpresa(item.id);
                                    listaResultados.appendChild(li);
                                });
                                listaResultados.style.display = 'block';
                            } else {
                                listaResultados.style.display = 'none';
                            }
                        });
                }, 300);
            });
        }

        // ... rest of existing init
        if (dealId > 0) {
            carregarNegocio(dealId);
        } else {
            Swal.fire('Erro', 'ID do negócio inválido.', 'error').then(() => {
                window.location.href = BASE_URL + 'crm';
            });
        }
    });

    function abrirModalVincularEmpresa() {
        document.getElementById('buscaEmpresaInput').value = '';
        document.getElementById('resultadoBuscaEmpresa').style.display = 'none';
        if (modalVincularInst) modalVincularInst.show();
    }

    function vincularEmpresa(empresaId) {
        Swal.fire({
            title: 'Vincular empresa?',
            text: "Deseja vincular esta empresa ao negócio?",
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Sim, vincular',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch(BASE_URL + 'modules/crm/acoes.php?acao=vincular_empresa', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        negocio_id: dealId,
                        empresa_id: empresaId
                    })
                })
                    .then(res => res.json())
                    .then(response => {
                        if (response.success) {
                            if (modalVincularInst) modalVincularInst.hide();
                            carregarNegocio(dealId);
                            Swal.fire({
                                icon: 'success',
                                title: 'Empresa vinculada!',
                                toast: true,
                                position: 'bottom-end',
                                showConfirmButton: false,
                                timer: 3000,
                                background: '#d4edda', color: '#155724', iconColor: '#155724'
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Erro ao vincular',
                                text: response.message || 'Erro desconhecido',
                                toast: true,
                                position: 'bottom-end',
                                showConfirmButton: false,
                                timer: 4000
                            });
                        }
                    })
                    .catch(err => {
                        Swal.fire({
                            icon: 'error',
                            title: 'Erro de conexão',
                            toast: true,
                            position: 'bottom-end',
                            showConfirmButton: false,
                            timer: 4000
                        });
                    });
            }
        });
    }

    function abrirModalAtividade(tipo) {
        acaoAtual = tipo;
        const titulos = { 'nota': 'Nova Nota Interna', 'tarefa': 'Nova Tarefa' };
        document.getElementById('tituloModalAtividade').innerText = titulos[tipo] || 'Nova Atividade';
        document.getElementById('textoAtividadeModal').value = '';
        if (modalAtividadeInst) modalAtividadeInst.show();
    }

    function salvarAtividadeModal() {
        const texto = document.getElementById('textoAtividadeModal').value;
        if (!texto.trim()) { alert('Digite algo...'); return; }

        fetch(BASE_URL + 'modules/crm/acoes.php?acao=salvar_anotacao', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                negocio_id: dealId,
                descricao: texto,
                tipo: acaoAtual
            })
        })
            .then(res => res.json())
            .then(response => {
                if (response.success) {
                    if (modalAtividadeInst) modalAtividadeInst.hide();
                    carregarNegocio(dealId); // Reload timeline
                } else {
                    alert('Erro ao salvar: ' + (response.message || 'Erro desconhecido'));
                }
            })
            .catch(err => alert('Erro de conexão ao salvar nota.'));
    }

    function setAcao(tipo, btn) {
        acaoAtual = tipo;

        // Atualizar botões
        document.querySelectorAll('.btn-group .btn').forEach(b => {
            if (b !== btn && !b.classList.contains('btn-outline-primary')) b.classList.remove('active');
        });
        if (btn) btn.classList.add('active');

        // Atualizar UI
        const txt = document.getElementById('textoAtividade');
        const lbl = document.getElementById('labelAcao');

        if (tipo === 'nota') { txt.placeholder = 'Escreva uma nota interna...'; lbl.innerHTML = '<i class="bi bi-lock"></i> Nota Interna'; txt.focus(); }
        if (tipo === 'tarefa') { txt.placeholder = 'O que precisa ser feito?'; lbl.innerHTML = '<i class="bi bi-calendar"></i> Tarefa a Fazer'; txt.focus(); }
        if (tipo === 'whatsapp') { txt.placeholder = 'Cole aqui a mensagem enviada...'; lbl.innerHTML = '<i class="bi bi-whatsapp"></i> Registro de WhatsApp'; txt.focus(); }
    }

    function irParaAgendamento() {
        if (pacienteId) {
            window.location.href = BASE_URL + 'modules/agendamentos/novo.php?paciente_id=' + pacienteId;
        } else {
            alert('Este negócio não tem cliente vinculado para agendar.');
        }
    }

    function carregarNegocio(id) {
        // Show Loading (if elements exist, better check or just try)
        if (document.getElementById('loadingArea')) document.getElementById('loadingArea').classList.remove('d-none');
        if (document.getElementById('conteudoNegocio')) document.getElementById('conteudoNegocio').classList.add('d-none');

        // Fetch Detail and Chat in parallel
        Promise.all([
            fetch(BASE_URL + 'modules/crm/acoes.php?acao=obter_negocio&id=' + id + '&_t=' + new Date().getTime()).then(r => r.json()),
            fetch(BASE_URL + 'modules/crm/acoes.php?acao=buscar_chat_negocio&negocio_id=' + id).then(r => r.json())
        ])
            .then(([resNegocio, resChat]) => {
                const response = resNegocio; // Alias
                if (response.success && response.data) {
                    const deal = response.data.deal;
                    pacienteId = deal.paciente_id;

                    // Preencher Info
                    document.getElementById('detalheTitulo').innerText = deal.titulo;
                    document.getElementById('detalheDataCriacao').innerText = new Date(deal.created_at).toLocaleDateString('pt-BR');
                    document.getElementById('detalheValor').innerText = parseFloat(deal.valor_estimado).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });

                    // Preencher Info Cliente (Pessoa)
                    if (deal.paciente_nome) {
                        document.getElementById('detalhePacienteNome').innerText = deal.paciente_nome;
                        document.getElementById('detalhePacienteTel').innerText = deal.paciente_telefone || '-';
                    } else {
                        document.getElementById('detalhePacienteNome').innerText = 'Sem Contato';
                        document.getElementById('detalhePacienteTel').innerText = '-';
                    }

                    // Preencher Info Empresa (Card Toggle)
                    const cardContent = document.getElementById('infoEmpresaContent');
                    const cardEmpty = document.getElementById('infoEmpresaEmpty');

                    if (deal.empresa_nome) {
                        // Has Company
                        cardContent.classList.remove('d-none');
                        cardEmpty.classList.add('d-none');

                        document.getElementById('detalheEmpresaNome').innerText = deal.empresa_nome;
                        document.getElementById('detalheEmpresaDoc').innerText = deal.empresa_documento || 'Sem Documento';
                    } else {
                        // No Company
                        cardContent.classList.add('d-none');
                        cardEmpty.classList.remove('d-none');
                    }

                    // Etapa
                    const badge = document.getElementById('detalheEtapa');
                    badge.innerText = deal.etapa_nome;
                    badge.className = 'badge fs-6';
                    if (deal.etapa_cor && deal.etapa_cor.startsWith('bg-')) {
                        badge.classList.add(deal.etapa_cor);
                        badge.style.backgroundColor = '';
                    } else {
                        badge.style.backgroundColor = deal.etapa_cor || '#6c757d';
                    }

                    // Timeline CRM Standard
                    globalTimeline = response.data.timeline || [];

                    // Merge Chat Logs if available
                    if (resChat.success && resChat.data.encontrado && resChat.data.timeline) {
                        const chatItems = resChat.data.timeline;
                        const logs = chatItems.filter(i => i.type === 'log');

                        logs.forEach(log => {
                            // Botão Link Conversa (Style Adjusted to auto height)
                            const btnLink = `<a href="${BASE_URL}modules/admin/atendimento/agentes/chats.php?id=${log.conversation_id}" target="_blank" class="btn btn-sm btn-outline-secondary py-1 px-2 d-inline-flex align-items-center" style="font-size: 0.8em;">Abrir Conversa #${log.conversation_id.substr(0, 8)}... <i class="bi bi-box-arrow-up-right ms-2"></i></a>`;

                            // Botão Resumo IA (se finalizado)
                            let btnResumo = '';
                            let divResumo = '';

                            if (log.status_novo === 'finalizado' || log.status_novo === 'Finalizado') {
                                const accId = 'acc_' + Math.random().toString(36).substr(2, 9);
                                const resumoContent = (resChat.data.chat_summaries && resChat.data.chat_summaries[log.conversation_id])
                                    ? resChat.data.chat_summaries[log.conversation_id]
                                    : null;

                                const textoExibicao = resumoContent || '<em class="text-secondary small">Resumo não disponível.</em>';

                                // Botão com estilo idêntico ao "Abrir Conversa" (Usando tag <a> para renderização idêntica e SEM quebras de linha para evitar white-space: pre-wrap issues)
                                btnResumo = `<a href="#" class="btn btn-sm btn-outline-secondary py-1 px-2 d-inline-flex align-items-center shadow-none text-decoration-none" data-bs-toggle="collapse" data-bs-target="#${accId}" role="button" aria-expanded="false" style="font-size: 0.8em;"><i class="bi bi-stars text-warning me-2"></i>Ver Resumo</a>`;



                                divResumo = `
                                    <div class="collapse mt-2 w-100" id="${accId}">
                                        <div class="card card-body bg-light border-0 small text-secondary p-2 shadow-sm rounded-3 text-start">
                                            <p class="mb-0" style="white-space: pre-line; line-height: 1.4;">${textoExibicao}</p>
                                        </div>
                                    </div>`;
                            }

                            // Montagem do Conteúdo (Flex row com gap pequeno)
                            const actionsRow = `<div class="d-flex align-items-center gap-2 mt-1 flex-wrap">${btnLink}${btnResumo}</div>`;

                            globalTimeline.push({
                                tipo: 'chat_log',
                                data: log.created_at,
                                conteudo: `Status: ${log.status_anterior || 'Início'} -> ${log.status_novo}${actionsRow}${divResumo}`,
                                responsavel: log.alterado_por || 'Sistema'
                            });
                        });

                        // Re-sort by date (Newest First)
                        globalTimeline.sort((a, b) => new Date(b.data) - new Date(a.data));
                    }

                    renderTimeline(globalTimeline);

                    // Mostrar Conteúdo
                    document.getElementById('loadingArea').classList.add('d-none');
                    document.getElementById('conteudoNegocio').classList.remove('d-none');

                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Erro',
                        text: response.message || 'Erro desconhecido',
                        toast: true,
                        position: 'bottom-end',
                        showConfirmButton: false,
                        timer: 4000
                    });
                }
            })
            .catch(err => {
                console.error(err);
                Swal.fire({
                    icon: 'error',
                    title: 'Erro de conexão',
                    text: 'Não foi possível carregar os detalhes.',
                    toast: true,
                    position: 'bottom-end',
                    showConfirmButton: false,
                    timer: 4000
                });
            });
    }

    function salvarAtividade() {
        const texto = document.getElementById('textoAtividade').value;
        if (!texto.trim()) {
            Swal.fire({ icon: 'warning', title: 'Atenção', text: 'Digite o conteúdo da nota/tarefa.', toast: true, position: 'bottom-end', showConfirmButton: false, timer: 3000 });
            return;
        }

        fetch(BASE_URL + 'modules/crm/acoes.php?acao=salvar_anotacao', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                negocio_id: dealId,
                descricao: texto,
                tipo: acaoAtual
            })
        })
            .then(res => res.json())
            .then(response => {
                if (response.success) {
                    document.getElementById('textoAtividade').value = '';
                    carregarNegocio(dealId); // Reload timeline
                    Swal.fire({
                        icon: 'success',
                        title: 'Salvo com sucesso!',
                        toast: true,
                        position: 'bottom-end',
                        showConfirmButton: false,
                        timer: 3000,
                        background: '#d4edda', color: '#155724', iconColor: '#155724'
                    });
                } else {
                    Swal.fire({ icon: 'error', title: 'Erro ao salvar', text: response.message || 'Erro desconhecido', toast: true, position: 'bottom-end', showConfirmButton: false, timer: 4000 });
                }
            })
            .catch(err => Swal.fire({ icon: 'error', title: 'Erro de conexão', toast: true, position: 'bottom-end', showConfirmButton: false, timer: 4000 }));
    }

    function filtrarTimeline(tipo) {
        if (tipo === 'todos') {
            renderTimeline(globalTimeline);
        } else if (tipo === 'chat') {
            // "Chat" button now filters checking for 'chat_log' type
            const filtrados = globalTimeline.filter(item => item.tipo === 'chat_log');
            renderTimeline(filtrados);
        } else {
            const filtrados = globalTimeline.filter(item => item.tipo === tipo);
            renderTimeline(filtrados);
        }
    }

    function renderChatTimeline(items, chatInfo) {
        const timelineDiv = document.getElementById('detalheTimeline');

        // Remove padding from container to let chat go full width
        if (timelineDiv.parentElement.classList.contains('p-4')) {
            timelineDiv.parentElement.classList.remove('p-4');
            timelineDiv.parentElement.classList.add('p-0');
        }

        let statusBadge = '';
        if (chatInfo && chatInfo.status) {
            const badges = {
                'robo': '<span class="badge bg-secondary"><i class="bi bi-robot me-1"></i>Robô</span>',
                'humano': '<span class="badge bg-primary"><i class="bi bi-person me-1"></i>Humano</span>',
                'finalizado': '<span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>Finalizado</span>'
            };
            statusBadge = badges[chatInfo.status] || '';
        }

        let html = `
            <div class="chat-wrapper">
                <div class="chat-header-internal">
                    <div class="d-flex align-items-center gap-3">
                        <div class="d-flex align-items-center gap-2">
                            <div class="bg-white rounded-circle p-1 border">
                                <i class="bi bi-whatsapp text-success fs-5"></i>
                            </div>
                            <div>
                                <div class="fw-bold small text-dark">Histórico</div>
                                <div class="small" style="font-size: 0.75rem;">${statusBadge}</div>
                            </div>
                        </div>
                        
                        <!-- Toggle Messages -->
                        <div class="form-check form-switch border-start ps-4 ms-2">
                            <input class="form-check-input cursor-pointer" type="checkbox" id="toggleMensagens" onchange="toggleChatMessages()">
                            <label class="form-check-label small text-muted cursor-pointer" for="toggleMensagens">Ver Mensagens</label>
                        </div>
                    </div>

                    <a href="${BASE_URL}modules/admin/atendimento/agentes/chats.php" target="_blank" class="btn btn-sm btn-light border text-muted" title="Abrir em nova aba">
                        <i class="bi bi-box-arrow-up-right"></i>
                    </a>
                </div>
                <!-- Chat Messages Container (Default: Messages Hidden) -->
                <div class="chat-message-container" id="chatScrollArea">
        `;

        if (items.length === 0) {
            html += `<div class="text-center mt-5 text-muted opacity-50">Nenhuma mensagem nesta conversa.</div>`;
        }

        items.forEach(item => {
            const time = new Date(item.created_at).toLocaleTimeString('pt-BR', { hour: '2-digit', minute: '2-digit' });

            if (item.type === 'log') {
                html += `
                    <div class="msg-log">
                        <i class="bi bi-info-circle-fill me-1"></i>
                        Status: <b>${item.status_anterior || 'Início'}</b> &rarr; <b>${item.status_novo}</b>
                        <div style="font-size: 0.7em; opacity: 0.8; margin-top:2px;">${new Date(item.created_at).toLocaleString('pt-BR')}</div>
                    </div>
                `;
            } else if (item.user_message) {
                html += `
                    <div class="msg-bubble msg-user">
                        ${item.user_message}
                        <span class="msg-time">${time}</span>
                    </div>
                `;
            } else if (item.bot_message) {
                html += `
                    <div class="msg-bubble msg-bot">
                        ${item.bot_message}
                        <span class="msg-time">${time}</span>
                    </div>
                `;
            }
        });

        html += `
                </div>
            </div>
        `;

        timelineDiv.innerHTML = html;

        // Scroll to bottom
        setTimeout(() => {
            const scrollArea = document.getElementById('chatScrollArea');
            if (scrollArea) scrollArea.scrollTop = scrollArea.scrollHeight;
        }, 100);
    }

    function toggleChatMessages() {
        const container = document.getElementById('chatScrollArea');
        const checkbox = document.getElementById('toggleMensagens');

        if (checkbox.checked) {
            container.classList.add('show-messages');
        } else {
            container.classList.remove('show-messages');
        }

        // Re-scroll to bottom as height changes
        setTimeout(() => {
            container.scrollTop = container.scrollHeight;
        }, 50);
    }

    function renderTimeline(items) {
        const timelineDiv = document.getElementById('detalheTimeline');
        if (items && items.length > 0) {
            let html = '<div class="d-flex flex-column gap-4 py-2">';
            items.forEach(item => {
                let icon = 'bi-sticky';
                let colorClass = 'bg-secondary'; // Default gray
                let iconColor = 'text-white';

                // Colors based on type for visual distinction
                if (item.tipo === 'nota') { icon = 'bi-sticky'; colorClass = 'bg-warning text-dark'; iconColor = 'text-dark'; }
                if (item.tipo === 'tarefa') { icon = 'bi-check2-square'; colorClass = 'bg-success'; }
                if (item.tipo === 'whatsapp') { icon = 'bi-whatsapp'; colorClass = 'bg-success bg-gradient'; }
                if (item.tipo === 'agendamento') { icon = 'bi-calendar-event'; colorClass = 'bg-primary'; }
                if (item.tipo === 'movimentacao') { icon = 'bi-arrow-left-right'; colorClass = 'bg-info text-dark'; iconColor = 'text-dark'; }
                if (item.tipo === 'chat_log') { icon = 'bi-chat-left-dots'; colorClass = 'bg-secondary'; }

                html += `
                    <div class="timeline-item d-flex">
                        <div class="timeline-icon-wrapper ${colorClass} ${iconColor} shadow-sm z-1">
                            <i class="bi ${icon} fs-5"></i>
                        </div>
                        <div class="timeline-card flex-grow-1 p-3 ml-3">
                            <div class="d-flex w-100 justify-content-between mb-2">
                                <h6 class="mb-0 fw-bold text-uppercase small text-primary">${item.tipo === 'chat_log' ? 'Status Chat' : item.tipo}</h6>
                                <small class="text-muted fw-medium">${new Date(item.data).toLocaleString('pt-BR', { day: '2-digit', month: '2-digit', hour: '2-digit', minute: '2-digit' })}</small>
                            </div>
                            <div class="mb-2 text-dark" style="white-space: pre-wrap; font-size: 0.95rem; line-height: 1.5;">${item.conteudo}</div>
                            <div class="text-end">
                                <span class="badge bg-light text-secondary border rounded-pill fw-normal px-2 py-1">
                                    <i class="bi bi-person-circle sm me-1"></i> ${item.responsavel || 'Sistema'}
                                </span>
                            </div>
                        </div>
                    </div>
                `;
            });
            html += '</div>';
            timelineDiv.innerHTML = html;
        } else {
            timelineDiv.innerHTML = `
                <div class="text-center py-5">
                    <div class="bg-white rounded-circle shadow-sm d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                        <i class="bi bi-inbox fs-1 text-secondary opacity-50"></i>
                    </div>
                    <p class="text-muted fw-medium">Nenhuma atividade encontrada neste filtro.</p>
                </div>
            `;
        }
    }
    function toggleEditMode() {
        const isEditing = !document.getElementById('inputTitulo').classList.contains('d-none');

        if (!isEditing) {
            // Show Inputs
            document.getElementById('inputTitulo').value = document.getElementById('detalheTitulo').innerText;
            // Clean value text (remove R$, dots, etc handled by backend text parse usually but better to send raw if possible. But here we send string). 
            // Backend takes string and cleans it.
            // Let's try to parse a bit.
            let valorTxt = document.getElementById('detalheValor').innerText.replace('R$', '').trim();
            document.getElementById('inputValor').value = valorTxt;

            document.getElementById('detalheTitulo').classList.add('d-none');
            document.getElementById('detalheValor').classList.add('d-none');
            document.getElementById('inputTitulo').classList.remove('d-none');
            document.getElementById('inputValor').classList.remove('d-none');
            document.getElementById('editActions').classList.remove('d-none');
        } else {
            // Hide Inputs
            document.getElementById('detalheTitulo').classList.remove('d-none');
            document.getElementById('detalheValor').classList.remove('d-none');
            document.getElementById('inputTitulo').classList.add('d-none');
            document.getElementById('inputValor').classList.add('d-none');
            document.getElementById('editActions').classList.add('d-none');
        }
    }

    function salvarEdicaoDetalhes() {
        const titulo = document.getElementById('inputTitulo').value;
        const valor = document.getElementById('inputValor').value;

        fetch(BASE_URL + 'modules/crm/acoes.php?acao=editar_negocio', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id: dealId, titulo: titulo, valor_estimado: valor })
        })
            .then(res => res.json())
            .then(response => {
                if (response.success) {
                    location.reload();
                    // Toasts don't survive reload without session flash, but reload is fast enough ideally or we rely on page load
                }
                else Swal.fire({ icon: 'error', title: 'Erro', text: response.message || 'Erro desconhecido', toast: true, position: 'bottom-end', showConfirmButton: false, timer: 4000 });
            })
            .catch(err => Swal.fire({ icon: 'error', title: 'Erro de conexão', toast: true, position: 'bottom-end', showConfirmButton: false, timer: 4000 }));
    }
</script>
<script>
    function excluirNegocio(id) {
        Swal.fire({
            title: 'Tem certeza?',
            text: "Excluir este negócio e todo seu histórico. Irreversível!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Sim, excluir',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch(BASE_URL + 'modules/crm/acoes.php?acao=excluir_negocio', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        id: id
                    })
                })
                    .then(res => res.json())
                    .then(response => {
                        if (response.success) {
                            Swal.fire(
                                'Excluído!',
                                'O negócio foi removido.',
                                'success'
                            ).then(() => {
                                window.location.href = BASE_URL + 'crm';
                            });
                        } else {
                            Swal.fire('Erro', response.message || 'Erro desconhecido', 'error');
                        }
                    })
                    .catch(err => {
                        console.error(err);
                        Swal.fire('Erro', 'Erro de conexão.', 'error');
                    });
            }
        });
    }
</script>

<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>