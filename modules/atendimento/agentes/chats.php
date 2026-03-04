<?php
$pageTitle = "Atendimento - Chats";
require_once __DIR__ . '/../../includes/header.php';

// Check permission
checkPermission(['admin', 'superadmin']);
?>

<style>
    .chat-container {
        height: calc(100vh - 180px);
        min-height: 500px;
        background: #fff;
        border-radius: 0.5rem;
        overflow: hidden;
        box-shadow: 0 2px 15px rgba(0, 0, 0, 0.05);
    }

    .chat-sidebar {
        width: 350px;
        border-right: 1px solid #e9ecef;
        display: flex;
        flex-direction: column;
    }

    .chat-list {
        flex: 1;
        overflow-y: auto;
    }

    .chat-item {
        padding: 15px;
        border-bottom: 1px solid #f8f9fa;
        cursor: pointer;
        transition: background 0.2s;
    }

    .chat-item:hover,
    .chat-item.active {
        background: #f8f9fa;
        border-left: 3px solid var(--primary-color);
    }

    .chat-content {
        flex: 1;
        display: flex;
        flex-direction: column;
        background: #f8f9fa;
    }

    .chat-messages {
        flex: 1;
        overflow-y: auto;
        padding: 20px;
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    .message {
        max-width: 70%;
        padding: 10px 15px;
        border-radius: 12px;
        position: relative;
        font-size: 0.95rem;
    }

    .message.user {
        align-self: flex-end;
        background: #dcf8c6;
        /* WhatsApp Greenish */
        color: #000;
        border-bottom-right-radius: 2px;
    }

    .message.bot {
        align-self: flex-start;
        background: #fff;
        border: 1px solid #e9ecef;
        border-bottom-left-radius: 2px;
    }

    .message-time {
        font-size: 0.7rem;
        opacity: 0.7;
        margin-top: 5px;
        text-align: right;
    }

    .empty-state {
        display: flex;
        align-items: center;
        justify-content: center;
        height: 100%;
        color: #6c757d;
        flex-direction: column;
    }
</style>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="mb-1"><i class="bi bi-chat-dots me-2"></i>Atendimentos</h2>
        <p class="text-muted mb-0">Gerencie as conversas do Supabase</p>
    </div>
    <a href="<?php echo BASE_URL; ?>admin/atendimento/agentes" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-2"></i>Voltar para Agentes
    </a>
</div>

<div class="chat-container d-flex">
    <!-- Sidebar -->
    <div class="chat-sidebar">
        <div class="p-3 border-bottom">
            <input type="text" class="form-control" id="searchChat" placeholder="Buscar conversa...">
        </div>
        <div class="chat-list" id="chatList">
            <div class="text-center py-5 text-muted">
                <div class="spinner-border text-primary spinner-border-sm mb-2"></div>
                <p>Carregando conversas...</p>
            </div>
        </div>
    </div>

    <!-- Chat Content -->
    <div class="chat-content">
        <div class="chat-header p-3 bg-white border-bottom d-flex justify-content-between align-items-center"
            id="chatHeader" style="display: none !important;">
            <div class="d-flex align-items-center">
                <div class="bg-primary bg-opacity-10 rounded-circle p-2 me-3 text-primary">
                    <i class="bi bi-person-fill fs-5"></i>
                </div>
                <div>
                    <h6 class="mb-0 fw-bold" id="currentChatName">Nome do Cliente</h6>
                    <small class="text-muted" id="currentChatPhone">+55 11 99999-9999</small>
                </div>
            </div>
            <div class="d-flex align-items-center">
                <div id="chatActions" class="me-2"></div>
                <div id="headerStatusBadge"></div>
            </div>
        </div>

        <div class="chat-messages" id="messagesContainer">
            <div class="empty-state">
                <i class="bi bi-chat-text fs-1 mb-3"></i>
                <h5>Selecione uma conversa</h5>
                <p>Escolha um atendimento à esquerda para ver o histórico.</p>
            </div>
        </div>

        <div class="p-3 bg-white border-top" id="chatInputArea" style="display: none;">
            <div class="input-group">
                <input type="text" class="form-control"
                    placeholder="Digite uma mensagem (apenas visualização por enquanto)" disabled>
                <button class="btn btn-primary" disabled>
                    <i class="bi bi-send"></i>
                </button>
            </div>
            <small class="text-muted mt-1 d-block text-center" style="font-size: 0.75rem;">
                <i class="bi bi-info-circle me-1"></i> Modo visualização (Integração Supabase)
            </small>
        </div>
    </div>
</div>

<script>
    const BASE_URL = "<?php echo BASE_URL; ?>";
    let activeChatId = null;

    document.addEventListener('DOMContentLoaded', () => {
        carregarConversas();
    });

    function carregarConversas() {
        fetch(BASE_URL + 'admin/atendimento/agentes/conversas?acao=listar')
            .then(res => res.json())
            .then(response => {
                if (response.success && response.data && response.data.conversas) {
                    renderizarListaConversas(response.data.conversas);
                } else {
                    document.getElementById('chatList').innerHTML = `<p class="text-center p-4 text-danger">Erro ao carregar conversas</p>`;
                }
            })
            .catch(err => {
                console.error(err);
                document.getElementById('chatList').innerHTML = `<p class="text-center p-4 text-danger">Erro de conexão</p>`;
            });
    }

    function getStatusBadge(status) {
        const badges = {
            'robo': '<span class="badge bg-secondary"><i class="bi bi-robot me-1"></i>Robô</span>',
            'humano': '<span class="badge bg-primary"><i class="bi bi-person me-1"></i>Humano</span>',
            'finalizado': '<span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>Finalizado</span>'
        };
        return badges[status] || '<span class="badge bg-light text-dark">' + status + '</span>';
    }

    function renderizarListaConversas(conversas) {
        const container = document.getElementById('chatList');

        if (conversas.length === 0) {
            container.innerHTML = `<p class="text-center p-4 text-muted">Nenhuma conversa encontrada</p>`;
            return;
        }

        let html = '';
        conversas.forEach(chat => {
            const nome = chat.leads_fatepi ? chat.leads_fatepi.nome_completo : 'Desconhecido';
            const telefone = chat.phone || 'Sem número';
            const ultimaMsg = chat.last_message ? (chat.last_message.user_message || chat.last_message.bot_message || 'Sem mensagens') : 'Nova conversa';
            const data = new Date(chat.created_at).toLocaleDateString('pt-BR');
            // Status Default if null
            const status = chat.status || 'robo';

            const safeId = chat.conversation_id || chat.id;

            html += `
                <div class="chat-item" onclick="abrirConversa('${chat.conversation_id}', '${nome}', '${telefone}', '${status}', this)">
                    <div class="d-flex justify-content-between mb-1">
                        <strong class="text-truncate" style="max-width: 120px;">${nome}</strong>
                        <small class="text-muted" style="font-size: 0.7rem;">${data}</small>
                    </div>
                    <div class="mb-1">${getStatusBadge(status)}</div>
                    <div class="small text-muted text-truncate">${telefone}</div>
                    <div class="small text-secondary text-truncate mt-1" style="opacity: 0.8;">
                        <i class="bi bi-check2-all me-1"></i> ${ultimaMsg}
                    </div>
                </div>
            `;
        });

        container.innerHTML = html;
    }

    function abrirConversa(conversationId, nome, telefone, status, element) {
        // Highlight active item
        document.querySelectorAll('.chat-item').forEach(el => el.classList.remove('active'));
        element.classList.add('active');

        activeChatId = conversationId;

        // Update Header
        document.getElementById('currentChatName').textContent = nome;
        document.getElementById('currentChatPhone').textContent = telefone;

        // Show Status in Header (create element if missing or update)
        let statusContainer = document.getElementById('headerStatusBadge');
        if (!statusContainer) {
            statusContainer = document.createElement('div');
            statusContainer.id = 'headerStatusBadge';
            document.querySelector('#chatHeader > div:last-child').innerHTML = ''; // Clear "Ativo" badge
            document.querySelector('#chatHeader > div:last-child').appendChild(statusContainer);
        }
        statusContainer.innerHTML = getStatusBadge(status);

        // Update Action Buttons
        atualizarBotoesAcao(status);

        document.getElementById('chatHeader').style.display = 'flex';
        document.getElementById('chatInputArea').style.display = 'block';

        // Load Messages
        const messagesContainer = document.getElementById('messagesContainer');
        messagesContainer.innerHTML = `
            <div class="d-flex justify-content-center align-items-center h-100">
                <div class="spinner-border text-primary"></div>
            </div>
        `;

        fetch(BASE_URL + 'admin/atendimento/agentes/conversas?acao=mensagens&conversation_id=' + conversationId)
            .then(res => res.json())
            .then(response => {
                if (response.success && response.data && response.data.mensagens) {
                    renderizarMensagens(response.data.mensagens);
                } else {
                    messagesContainer.innerHTML = `<p class="text-center mt-5 text-muted">Sem mensagens nesta conversa.</p>`;
                }
            });
    }

    function renderizarMensagens(itens) {
        const container = document.getElementById('messagesContainer');
        container.innerHTML = '';

        if (itens.length === 0) {
            container.innerHTML = `<p class="text-center mt-5 text-muted">Nenhuma mensagem encontrada.</p>`;
            return;
        }

        itens.forEach(item => {
            if (item.type === 'log') {
                container.innerHTML += `
                    <div class="text-center my-3">
                        <small class="badge bg-light text-secondary border rounded-pill px-3 py-1">
                            <i class="bi bi-info-circle me-1"></i>
                            Status alterado: <b>${item.status_anterior || '?'}</b> ➝ <b>${item.status_novo}</b>
                            <br><span style="font-size: 0.7em; opacity: 0.7;">${formatarHora(item.created_at)}</span>
                        </small>
                    </div>
                `;
                return;
            }

            // User Message
            if (item.user_message) {
                container.innerHTML += `
                    <div class="message user">
                        ${item.user_message}
                        <div class="message-time">${formatarHora(item.created_at)}</div>
                    </div>
                `;
            }

            // Bot Message
            if (item.bot_message) {
                container.innerHTML += `
                    <div class="message bot">
                        ${item.bot_message}
                        <div class="message-time">${formatarHora(item.created_at)}</div>
                    </div>
                `;
            }
        });

        // Scroll to bottom
        container.scrollTop = container.scrollHeight;
    }

    function formatarHora(dateString) {
        const date = new Date(dateString);
        return date.toLocaleTimeString('pt-BR', { hour: '2-digit', minute: '2-digit' });
    }

    function atualizarBotoesAcao(status) {
        const container = document.getElementById('chatActions');
        container.innerHTML = '';

        if (status === 'robo') {
            container.innerHTML = `
                <button class="btn btn-sm btn-outline-primary me-1" onclick="alterarStatus('humano')">
                    <i class="bi bi-person-check"></i> Assumir
                </button>
                <button class="btn btn-sm btn-outline-danger" onclick="alterarStatus('finalizado')">
                    <i class="bi bi-x-circle"></i> Finalizar
                </button>
            `;
        } else if (status === 'humano') {
            container.innerHTML = `
                <button class="btn btn-sm btn-outline-success" onclick="alterarStatus('finalizado')">
                    <i class="bi bi-check-lg"></i> Finalizar Atendimento
                </button>
            `;
        } else if (status === 'finalizado') {
            container.innerHTML = `
                <button class="btn btn-sm btn-outline-secondary" onclick="alterarStatus('humano')">
                    <i class="bi bi-arrow-counterclockwise"></i> Reabrir
                </button>
            `;
        }
    }

    function alterarStatus(novoStatus) {
        if (!activeChatId) return;

        // Optimistic UI update or Spinner? Spinner is safer.
        const container = document.getElementById('chatActions');
        container.innerHTML = '<div class="spinner-border spinner-border-sm text-muted"></div>';

        fetch(BASE_URL + 'admin/atendimento/agentes/conversas?acao=atualizar_status', {
            method: 'POST',
            body: JSON.stringify({
                conversation_id: activeChatId,
                novo_status: novoStatus
            })
        })
            .then(res => res.json())
            .then(response => {
                if (response.success) {
                    // Refresh Chat
                    // We need to fetch basic info again to update List Item badge too?
                    // Or just reload everything.
                    carregarConversas(); // Reload list
                    // Reload current chat (to see log)
                    // We need names/phones. stored in DOM? 
                    // We can just call abrirConversa if we have data.
                    // Simpler: reload messages.

                    // Update Badge manually
                    document.getElementById('headerStatusBadge').innerHTML = getStatusBadge(novoStatus);
                    atualizarBotoesAcao(novoStatus);

                    // Reload Messages to see the Log
                    const messagesContainer = document.getElementById('messagesContainer');
                    fetch(BASE_URL + 'admin/atendimento/agentes/conversas?acao=mensagens&conversation_id=' + activeChatId)
                        .then(r => r.json())
                        .then(res => {
                            if (res.success) renderizarMensagens(res.data.mensagens);
                        });

                } else {
                    alert('Erro ao atualizar status: ' + (response.message || 'Erro desconhecido'));
                    // Restore buttons
                    atualizarBotoesAcao('erro'); // Will imply empty or default
                }
            })
            .catch(err => {
                console.error(err);
                alert('Erro de conexão');
            });
    }
</script>

<?php require_once __DIR__ . '/../../../../includes/footer.php'; ?>