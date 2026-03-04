<?php
$pageTitle = "Agentes de IA - Atendimento";
require_once __DIR__ . '/../../../includes/header.php';

// Check permission
checkPermission(['admin', 'superadmin']);
?>

<style>
    .text-truncate-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        text-overflow: ellipsis;
        max-width: 300px;
    }

    .agent-name {
        max-width: 250px;
    }
</style>


<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="mb-1"><i class="bi bi-robot me-2"></i>Agentes de IA</h2>
        <p class="text-muted mb-0">Crie e gerencie agentes de IA personalizados</p>
    </div>
    <div class="d-flex gap-2">
        <a href="<?php echo BASE_URL; ?>admin/atendimento/chats" class="btn btn-outline-primary shadow-sm">
            <i class="bi bi-chat-dots me-2"></i>Ver Conversas
        </a>
        <button class="btn btn-primary shadow-sm" onclick="abrirModalAgente()">
            <i class="bi bi-plus-circle me-2"></i>Novo Agente
        </button>
    </div>
</div>

<!-- Estatísticas -->
<div class="row mb-4">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <div class="bg-primary bg-opacity-10 rounded-3 p-3">
                            <i class="bi bi-robot fs-3 text-primary"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="text-muted mb-1">Total de Agentes</h6>
                        <h3 class="mb-0" id="totalAgentes">0</h3>
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
                            <i class="bi bi-check-circle fs-3 text-success"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="text-muted mb-1">Agentes Ativos</h6>
                        <h3 class="mb-0" id="totalAtivos">0</h3>
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
                            <i class="bi bi-chat-dots fs-3 text-info"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="text-muted mb-1">Total de Interações</h6>
                        <h3 class="mb-0" id="totalInteracoes">0</h3>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Filtros -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-3">
                <label class="form-label small text-muted fw-bold">Filtrar por Tipo</label>
                <select class="form-select" id="filtroTipo" onchange="carregarAgentes()">
                    <option value="">Todos os Tipos</option>
                    <option value="qualificador">Qualificador</option>
                    <option value="atendimento">Atendimento</option>
                    <option value="suporte">Suporte</option>
                    <option value="vendas">Vendas</option>
                    <option value="geral">Geral</option>
                </select>
            </div>
        </div>
    </div>
</div>

<!-- Tabela de Agentes -->
<div class="card border-0 shadow-sm">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover" id="tabelaAgentes">
                <thead>
                    <tr>
                        <th>Agente</th>
                        <th>Tipo</th>
                        <th>Modelo</th>
                        <th>Configurações</th>
                        <th>Status</th>
                        <th>Interações</th>
                        <th class="text-end">Ações</th>
                    </tr>
                </thead>
                <tbody id="agentesContainer">
                    <tr>
                        <td colspan="7" class="text-center py-5">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Carregando...</span>
                            </div>
                            <p class="text-muted mt-2">Carregando agentes...</p>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Criar/Editar Agente -->
<div class="modal fade" id="modalAgente" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalAgenteTitulo">Novo Agente</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="formAgente">
                    <input type="hidden" id="agenteId" name="id">

                    <div class="mb-3">
                        <label class="form-label">Nome do Agente</label>
                        <input type="text" class="form-control" name="nome" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Descrição/Objetivo</label>
                        <textarea class="form-control" name="descricao" rows="2"></textarea>
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Tipo</label>
                            <select class="form-select" name="tipo" required>
                                <option value="qualificador">Qualificador</option>
                                <option value="atendimento">Atendimento</option>
                                <option value="suporte">Suporte</option>
                                <option value="vendas">Vendas</option>
                                <option value="geral">Geral</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Modelo de IA</label>
                            <select class="form-select" name="modelo">
                                <option value="gpt-4">GPT-4</option>
                                <option value="gpt-3.5-turbo">GPT-3.5 Turbo</option>
                                <option value="gemini-pro">Gemini Pro</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <label class="form-label mb-0">Prompt do Sistema</label>
                            <button type="button" class="btn btn-sm btn-outline-primary py-0"
                                onclick="abrirEditorAvancado()">
                                <i class="bi bi-arrows-fullscreen me-1"></i>Editor Avançado
                            </button>
                        </div>
                        <textarea class="form-control font-monospace" name="prompt_sistema" rows="4"
                            required></textarea>
                        <small class="text-muted">Instruções que definem o comportamento do agente</small>
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Temperatura (0.0 - 1.0)</label>
                            <input type="number" class="form-control" name="temperatura" step="0.01" min="0" max="1"
                                value="0.70">
                            <small class="text-muted">Criatividade das respostas</small>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Máximo de Tokens</label>
                            <input type="number" class="form-control" name="max_tokens" value="500">
                            <small class="text-muted">Tamanho máximo da resposta</small>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Palavras-chave (separadas por vírgula)</label>
                        <input type="text" class="form-control" id="palavrasChaveInput"
                            placeholder="financiamento, imóvel, crédito">
                        <small class="text-muted">Palavras que ativam este agente</small>
                    </div>

                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="agenteAtivo" name="ativo" checked>
                        <label class="form-check-label" for="agenteAtivo">Agente Ativo</label>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" onclick="salvarAgente()">Salvar</button>
            </div>
        </div>
    </div>
</div>

<script>
    const BASE_URL = "<?php echo BASE_URL; ?>";
    let modalAgenteInst;

    document.addEventListener('DOMContentLoaded', () => {
        modalAgenteInst = new bootstrap.Modal(document.getElementById('modalAgente'));
        carregarAgentes();
    });

    function carregarAgentes() {
        const tipo = document.getElementById('filtroTipo').value;
        const url = BASE_URL + 'atendimento/agentes/acoes.php?acao=listar' + (tipo ? '&tipo=' + tipo : '');

        fetch(url)
            .then(res => res.json())
            .then(response => {
                if (response.success && response.data && response.data.agentes) {
                    renderizarAgentes(response.data.agentes);
                    atualizarEstatisticas(response.data.agentes);
                } else {
                    mostrarErro('Erro ao carregar agentes');
                }
            })
            .catch(err => {
                console.error(err);
                mostrarErro('Erro ao carregar agentes');
            });
    }

    function atualizarEstatisticas(agentes) {
        document.getElementById('totalAgentes').textContent = agentes.length;
        document.getElementById('totalAtivos').textContent = agentes.filter(a => a.ativo == 1).length;

        const totalInteracoes = agentes.reduce((sum, a) => sum + parseInt(a.total_interacoes || 0), 0);
        document.getElementById('totalInteracoes').textContent = totalInteracoes;
    }

    function renderizarAgentes(agentes) {
        const container = document.getElementById('agentesContainer');

        if (agentes.length === 0) {
            container.innerHTML = `
                <tr>
                    <td colspan="7" class="text-center py-5">
                        <i class="bi bi-robot fs-1 text-muted"></i>
                        <p class="text-muted mt-2">Nenhum agente encontrado</p>
                        <button class="btn btn-primary" onclick="abrirModalAgente()">Criar Primeiro Agente</button>
                    </td>
                </tr>
            `;
            return;
        }

        let html = '';
        agentes.forEach(agente => {
            const tipoIcon = getTipoIcon(agente.tipo);
            const tipoBadge = getTipoBadge(agente.tipo);
            const palavrasChave = agente.palavras_chave ? JSON.parse(agente.palavras_chave) : [];

            html += `
                <tr>
                    <td>
                        <div class="d-flex align-items-center agent-name">
                            <div class="me-2">${tipoIcon}</div>
                            <div style="min-width: 0;">
                                <div class="fw-bold text-truncate">${agente.nome}</div>
                                <small class="text-muted text-truncate d-block" style="max-width: 250px;">${agente.descricao || '-'}</small>
                            </div>
                        </div>
                    </td>
                    <td><span class="badge ${tipoBadge}">${agente.tipo}</span></td>
                    <td><span class="badge bg-secondary">${agente.modelo}</span></td>
                    <td>
                        <small class="text-muted text-nowrap">Temp: ${agente.temperatura} | Tokens: ${agente.max_tokens}</small>
                    </td>
                    <td>
                        ${agente.ativo == 1 ? '<span class="badge bg-success">Ativo</span>' : '<span class="badge bg-secondary">Inativo</span>'}
                    </td>
                    <td>${agente.total_interacoes || 0}</td>
                    <td class="text-end">
                        <div class="btn-group btn-group-sm">
                            <button class="btn btn-outline-secondary" onclick="editarAgente(${agente.id})" title="Editar">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <a href="${BASE_URL}admin/atendimento/agentes/editor/${agente.id}" class="btn btn-info text-white" title="Editor de Prompt">
                                <i class="bi bi-file-text me-1"></i> Prompt
                            </a>
                            <button class="btn btn-outline-primary" onclick="testarAgente(${agente.id})" title="Testar">
                                <i class="bi bi-play-circle"></i>
                            </button>
                            <button class="btn btn-outline-danger" onclick="excluirAgente(${agente.id})" title="Excluir">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            `;
        });

        container.innerHTML = html;
    }

    function getTipoIcon(tipo) {
        const icons = {
            'qualificador': '🎯',
            'atendimento': '💬',
            'suporte': '🛠️',
            'vendas': '💰',
            'geral': '⚙️'
        };
        return icons[tipo] || '🤖';
    }

    function getTipoBadge(tipo) {
        const badges = {
            'qualificador': 'bg-primary',
            'atendimento': 'bg-success',
            'suporte': 'bg-warning',
            'vendas': 'bg-danger',
            'geral': 'bg-info'
        };
        return badges[tipo] || 'bg-secondary';
    }

    function abrirModalAgente() {
        document.getElementById('formAgente').reset();
        document.getElementById('agenteId').value = '';
        document.getElementById('modalAgenteTitulo').innerText = 'Novo Agente';
        modalAgenteInst.show();
    }

    function editarAgente(id) {
        fetch(BASE_URL + 'atendimento/agentes/acoes.php?acao=buscar&id=' + id)
            .then(res => res.json())
            .then(response => {
                if (response.success && response.data && response.data.agente) {
                    const agente = response.data.agente;

                    document.getElementById('agenteId').value = agente.id;
                    document.querySelector('[name=nome]').value = agente.nome;
                    document.querySelector('[name=descricao]').value = agente.descricao || '';
                    document.querySelector('[name=tipo]').value = agente.tipo;
                    document.querySelector('[name=modelo]').value = agente.modelo;
                    document.querySelector('[name=prompt_sistema]').value = agente.prompt_sistema;
                    document.querySelector('[name=temperatura]').value = agente.temperatura;
                    document.querySelector('[name=max_tokens]').value = agente.max_tokens;

                    const palavrasChave = agente.palavras_chave ? JSON.parse(agente.palavras_chave) : [];
                    document.getElementById('palavrasChaveInput').value = palavrasChave.join(', ');

                    document.getElementById('agenteAtivo').checked = agente.ativo == 1;

                    document.getElementById('modalAgenteTitulo').innerText = 'Editar Agente';
                    modalAgenteInst.show();
                }
            });
    }

    function salvarAgente() {
        const form = document.getElementById('formAgente');
        const formData = new FormData(form);
        const data = Object.fromEntries(formData.entries());

        // Processar palavras-chave
        const palavrasChaveInput = document.getElementById('palavrasChaveInput').value;
        data.palavras_chave = palavrasChaveInput ? palavrasChaveInput.split(',').map(k => k.trim()) : [];

        // Converter ativo para número
        data.ativo = document.getElementById('agenteAtivo').checked ? 1 : 0;

        fetch(BASE_URL + 'atendimento/agentes/acoes.php?acao=salvar', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data)
        })
            .then(res => res.json())
            .then(resp => {
                if (resp.success) {
                    modalAgenteInst.hide();
                    carregarAgentes();
                    alert(resp.message || 'Agente salvo com sucesso!');
                } else {
                    alert('Erro: ' + (resp.message || 'Erro desconhecido'));
                }
            });
    }

    function excluirAgente(id) {
        if (!confirm('Deseja excluir este agente? Esta ação não pode ser desfeita.')) return;

        fetch(BASE_URL + 'atendimento/agentes/acoes.php?acao=excluir', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id: id })
        })
            .then(res => res.json())
            .then(resp => {
                if (resp.success) {
                    carregarAgentes();
                    alert('Agente excluído com sucesso!');
                } else {
                    alert('Erro: ' + (resp.message || 'Erro desconhecido'));
                }
            });
    }

    function testarAgente(id) {
        const mensagem = prompt('Digite uma mensagem para testar o agente:');
        if (!mensagem) return;

        fetch(BASE_URL + 'atendimento/agentes/acoes.php?acao=testar', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ agente_id: id, mensagem: mensagem })
        })
            .then(res => res.json())
            .then(resp => {
                if (resp.success && resp.data) {
                    alert(`Resposta do Agente:\n\n${resp.data.resposta}\n\nTokens: ${resp.data.tokens_usados}\nTempo: ${resp.data.tempo_ms}ms`);
                } else {
                    alert('Erro: ' + (resp.message || 'Erro desconhecido'));
                }
            });
    }

    function mostrarErro(mensagem) {
        document.getElementById('agentesContainer').innerHTML = `
            <tr>
                <td colspan="7" class="text-center py-5">
                    <i class="bi bi-exclamation-triangle fs-1 text-danger"></i>
                    <p class="text-muted mt-2">${mensagem}</p>
                </td>
            </tr>
        `;
    }
    function abrirEditorAvancado() {
        const id = document.getElementById('agenteId').value;
        if (!id) {
            alert('Salve o agente primeiro antes de usar o editor avançado.');
            return;
        }
        window.location.href = BASE_URL + 'admin/atendimento/agentes/editor/' + id;
    }
</script>

<?php require_once __DIR__ . '/../../../includes/footer.php'; ?>