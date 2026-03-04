<?php
$pageTitle = "Editor de Prompt - Agentes de IA";

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
if (!$id) {
    echo "<div class='alert alert-danger'>ID do agente não informado.</div>";
    exit;
}
?>

<script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>

<style>
    .editor-toolbar {
        background: #f8f9fa;
        padding: 1rem;
        border-radius: 6px;
        margin-bottom: 1.5rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 1rem;
        flex-wrap: wrap;
    }

    .view-toggle {
        display: flex;
        gap: 0.5rem;
    }

    .view-toggle .btn {
        padding: 0.5rem 1rem;
        font-size: 0.9rem;
    }

    .sections-container {
        display: flex;
        flex-direction: column;
        gap: 1.5rem;
        margin-bottom: 2rem;
        max-width: 1200px;
        margin-left: auto;
        margin-right: auto;
    }

    .section-card {
        background: white;
        border: 2px solid #e2e8f0;
        border-radius: 8px;
        overflow: hidden;
        transition: all 0.3s;
        cursor: move;
    }

    .section-card:hover {
        border-color: #667eea;
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.15);
    }

    .section-card.dragging {
        opacity: 0.5;
    }

    .section-header {
        background: linear-gradient(135deg, #f7fafc 0%, #edf2f7 100%);
        padding: 1rem;
        border-bottom: 2px solid #e2e8f0;
        display: flex;
        justify-content: space-between;
        align-items: center;
        cursor: pointer;
        user-select: none;
    }

    .section-header:hover {
        background: linear-gradient(135deg, #edf2f7 0%, #e2e8f0 100%);
    }

    .section-title {
        font-weight: 600;
        color: #2d3748;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        flex: 1;
    }

    .section-title input {
        border: none;
        background: transparent;
        font-weight: 600;
        color: #2d3748;
        padding: 0.25rem;
        flex: 1;
    }

    .section-title input:focus {
        outline: 2px solid #667eea;
        border-radius: 4px;
        background: white;
    }

    .section-actions {
        display: flex;
        gap: 0.5rem;
    }

    .section-actions .btn {
        padding: 0.25rem 0.5rem;
        font-size: 0.85rem;
    }

    .section-body {
        padding: 1rem;
        max-height: 400px;
        overflow-y: auto;
    }

    .section-body.collapsed {
        display: none;
    }

    .section-body textarea {
        width: 100%;
        min-height: 200px;
        border: 1px solid #e2e8f0;
        border-radius: 6px;
        padding: 1rem;
        font-family: 'Consolas', 'Monaco', monospace;
        font-size: 0.9rem;
        line-height: 1.6;
        resize: vertical;
    }

    .section-body textarea:focus {
        outline: none;
        border-color: #667eea;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    }

    .raw-editor {
        background: #1e1e1e;
        border-radius: 8px;
        overflow: hidden;
        margin-bottom: 2rem;
    }

    .raw-editor-header {
        background: #2d2d2d;
        color: #d4d4d4;
        padding: 0.75rem 1rem;
        font-size: 0.85rem;
        font-weight: 600;
        border-bottom: 1px solid #3e3e3e;
    }

    .raw-editor textarea {
        width: 100%;
        min-height: 600px;
        background: #1e1e1e;
        color: #d4d4d4;
        border: none;
        padding: 1.5rem;
        font-family: 'Consolas', 'Monaco', monospace;
        font-size: 0.9rem;
        line-height: 1.6;
        resize: vertical;
    }

    .raw-editor textarea:focus {
        outline: none;
    }

    .preview-pane {
        background: white;
        border: 2px solid #e2e8f0;
        border-radius: 8px;
        padding: 2rem;
        margin-bottom: 2rem;
    }

    .preview-pane h1 {
        font-size: 2rem;
        font-weight: 700;
        margin-bottom: 1rem;
        color: #1a202c;
        border-bottom: 2px solid #e2e8f0;
        padding-bottom: 0.5rem;
    }

    .preview-pane h2 {
        font-size: 1.5rem;
        font-weight: 600;
        margin-top: 2rem;
        margin-bottom: 1rem;
        color: #2d3748;
        border-bottom: 1px solid #e2e8f0;
        padding-bottom: 0.5rem;
    }

    .preview-pane h3 {
        font-size: 1.25rem;
        font-weight: 600;
        margin-top: 1.5rem;
        margin-bottom: 0.75rem;
        color: #2d3748;
    }

    .preview-pane p {
        margin-bottom: 1rem;
        line-height: 1.7;
        color: #2d3748;
    }

    .preview-pane ul,
    .preview-pane ol {
        margin-bottom: 1rem;
        padding-left: 2rem;
        line-height: 1.7;
    }

    .preview-pane code {
        background: #f7fafc;
        color: #d73a49;
        padding: 0.2rem 0.4rem;
        border-radius: 3px;
        font-family: 'Consolas', monospace;
        font-size: 0.9em;
    }

    .preview-pane pre {
        background: #2d3748;
        color: #e2e8f0;
        padding: 1rem;
        border-radius: 6px;
        overflow-x: auto;
        margin: 1rem 0;
    }

    .preview-pane pre code {
        background: transparent;
        color: inherit;
        padding: 0;
    }

    .btn-add-section {
        width: 100%;
        padding: 1rem;
        border: 2px dashed #cbd5e0;
        background: #f7fafc;
        color: #4a5568;
        border-radius: 8px;
        transition: all 0.3s;
    }

    .btn-add-section:hover {
        border-color: #667eea;
        background: #edf2f7;
        color: #667eea;
    }

    .empty-state {
        text-align: center;
        padding: 3rem;
        color: #a0aec0;
    }

    .empty-state i {
        font-size: 3rem;
        margin-bottom: 1rem;
    }
</style>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="mb-1"><i class="bi bi-pencil-square me-2"></i>Editor de Prompt</h2>
        <p class="text-muted mb-0" id="agenteNome">Carregando...</p>
    </div>
    <div class="d-flex gap-2">
        <button class="btn btn-info btn-sm text-white" onclick="abrirSimulador()">
            <i class="bi bi-whatsapp me-2"></i>Testar Simulador
        </button>
        <a href="<?php echo BASE_URL; ?>atendimento/agentes" class="btn btn-outline-secondary shadow-sm">
            <i class="bi bi-arrow-left me-2"></i>Voltar
        </a>
        <button class="btn btn-primary shadow-sm" onclick="salvarPrompt()">
            <i class="bi bi-save me-2"></i>Salvar Alterações
        </button>
    </div>
</div>

<div class="editor-toolbar">
    <div class="view-toggle">
        <button class="btn btn-primary btn-sm active" onclick="switchView('sections')" id="btnSections">
            <i class="bi bi-grid-3x2 me-2"></i>Seções
        </button>
        <button class="btn btn-outline-secondary btn-sm" onclick="switchView('raw')" id="btnRaw">
            <i class="bi bi-code-square me-2"></i>Markdown
        </button>
        <button class="btn btn-outline-secondary btn-sm" onclick="switchView('preview')" id="btnPreview">
            <i class="bi bi-eye me-2"></i>Preview
        </button>
    </div>
    <div>
        <span class="badge bg-secondary" id="sectionCount">0 seções</span>
        <span class="badge bg-info ms-2" id="charCount">0 caracteres</span>
    </div>
</div>

<!-- Sections View -->
<div id="sectionsView">
    <div id="sectionsContainer" class="sections-container"></div>
    <button class="btn-add-section" onclick="addSection()">
        <i class="bi bi-plus-circle me-2"></i>Adicionar Nova Seção
    </button>
</div>

<!-- Raw Markdown View -->
<div id="rawView" style="display: none;">
    <div class="raw-editor">
        <div class="raw-editor-header">
            <i class="bi bi-code-slash me-2"></i>Markdown Completo
        </div>
        <textarea id="rawEditor" placeholder="Cole ou edite o markdown completo aqui..."></textarea>
    </div>
    <button class="btn btn-primary" onclick="parseFromRaw()">
        <i class="bi bi-arrow-repeat me-2"></i>Atualizar Seções
    </button>
</div>

<!-- Preview View -->
<div id="previewView" style="display: none;">
    <div class="preview-pane" id="fullPreview"></div>
</div>
</div>

<script>
    const agenteId = <?php echo $id; ?>;
    const BASE_URL = "<?php echo BASE_URL; ?>";
    let sections = [];
    let currentView = 'sections';

    document.addEventListener('DOMContentLoaded', () => {
        carregarDados();
        initSortable();
    });

    function initSortable() {
        const container = document.getElementById('sectionsContainer');
        new Sortable(container, {
            animation: 150,
            handle: '.section-header',
            ghostClass: 'dragging',
            onEnd: updateSectionOrder
        });
    }

    function switchView(view) {
        currentView = view;

        document.getElementById('sectionsView').style.display = view === 'sections' ? 'block' : 'none';
        document.getElementById('rawView').style.display = view === 'raw' ? 'block' : 'none';
        document.getElementById('previewView').style.display = view === 'preview' ? 'block' : 'none';

        ['btnSections', 'btnRaw', 'btnPreview'].forEach(id => {
            document.getElementById(id).classList.remove('btn-primary', 'active');
            document.getElementById(id).classList.add('btn-outline-secondary');
        });

        const btnMap = { sections: 'btnSections', raw: 'btnRaw', preview: 'btnPreview' };
        document.getElementById(btnMap[view]).classList.remove('btn-outline-secondary');
        document.getElementById(btnMap[view]).classList.add('btn-primary', 'active');

        if (view === 'raw') {
            document.getElementById('rawEditor').value = buildMarkdown();
        } else if (view === 'preview') {
            document.getElementById('fullPreview').innerHTML = marked.parse(buildMarkdown());
        }
    }

    function parseMarkdown(markdown) {
        sections = [];
        if (!markdown || !markdown.trim()) {
            renderSections();
            return;
        }

        const lines = markdown.split('\n');
        let currentSection = null;
        let headerContent = ''; // Content before first ## section
        let h1Title = null; // Extract H1 title if present

        lines.forEach(line => {
            if (line.trim().startsWith('## ')) {
                // Save previous section if exists
                if (currentSection) {
                    currentSection.content = currentSection.content.trim();
                    sections.push(currentSection);
                }

                // Create new section
                currentSection = {
                    id: Date.now() + Math.random(),
                    title: line.trim().substring(3).trim(),
                    content: '',
                    collapsed: true
                };
            } else if (currentSection) {
                // Add to current section
                currentSection.content += line + '\n';
            } else {
                // Content before first ## section
                // Check if this line is an H1 title (handle emojis and whitespace)
                const h1Match = line.trim().match(/^#\s+(.+)$/);
                if (h1Match && !h1Match[1].startsWith('#') && !h1Title) {
                    h1Title = h1Match[1].trim();
                } else {
                    headerContent += line + '\n';
                }
            }
        });

        // Save last section
        if (currentSection) {
            currentSection.content = currentSection.content.trim();
            sections.push(currentSection);
        }

        console.log('Parser Debug:', { h1Title, headerContentLength: headerContent.trim().length, sectionsCount: sections.length });

        // If there's NO H2 sections at all, create a single section with H1 as title
        if (sections.length === 0) {
            sections.push({
                id: Date.now(),
                title: h1Title || 'Instruções Principais',
                content: headerContent.trim(),
                collapsed: true
            });
        }
        // If we have H2 sections but there's leftover header content (text between H1 and first H2)
        else if (headerContent.trim()) {
            sections.unshift({
                id: Date.now(),
                title: 'Introdução',
                content: headerContent.trim(),
                collapsed: true
            });
        }

        // Note: H1 title is extracted but not used to create a section when H2 sections exist
        // This is intentional - H1 is document metadata, H2 are the actual sections

        renderSections();
    }

    function renderSections() {
        const container = document.getElementById('sectionsContainer');

        if (sections.length === 0) {
            container.innerHTML = `
                <div class="empty-state">
                    <i class="bi bi-inbox"></i>
                    <p>Nenhuma seção criada. Clique em "Adicionar Nova Seção" para começar.</p>
                </div>
            `;
            return;
        }

        container.innerHTML = sections.map((section, index) => `
            <div class="section-card" data-id="${section.id}">
                <div class="section-header" onclick="toggleSection(${index})">
                    <div class="section-title">
                        <i class="bi bi-grip-vertical text-muted"></i>
                        <input type="text" value="${escapeHtml(section.title)}" 
                               onclick="event.stopPropagation()" 
                               onchange="updateSectionTitle(${index}, this.value)"
                               placeholder="Nome da seção">
                    </div>
                    <div class="section-actions">
                        <button class="btn btn-sm btn-outline-danger" onclick="event.stopPropagation(); deleteSection(${index})">
                            <i class="bi bi-trash"></i>
                        </button>
                        <i class="bi bi-chevron-${section.collapsed ? 'down' : 'up'}"></i>
                    </div>
                </div>
                <div class="section-body ${section.collapsed ? 'collapsed' : ''}">
                    <textarea oninput="updateSectionContent(${index}, this.value)" 
                              placeholder="Conteúdo da seção...">${escapeHtml(section.content)}</textarea>
                </div>
            </div>
        `).join('');

        updateStats();
    }

    function addSection() {
        sections.push({
            id: Date.now(),
            title: 'Nova Seção',
            content: '',
            collapsed: false  // Keep new sections open so user can immediately edit
        });
        renderSections();
    }

    function deleteSection(index) {
        if (confirm('Remover esta seção?')) {
            sections.splice(index, 1);
            renderSections();
        }
    }

    function toggleSection(index) {
        sections[index].collapsed = !sections[index].collapsed;
        renderSections();
    }

    function updateSectionTitle(index, title) {
        sections[index].title = title;
        updateStats();
    }

    function updateSectionContent(index, content) {
        sections[index].content = content;
        updateStats();
    }

    function updateSectionOrder() {
        const cards = document.querySelectorAll('.section-card');
        const newOrder = [];
        cards.forEach(card => {
            const id = parseFloat(card.dataset.id);
            const section = sections.find(s => s.id === id);
            if (section) newOrder.push(section);
        });
        sections = newOrder;
    }

    function buildMarkdown() {
        return sections.map(s => `## ${s.title}\n\n${s.content.trim()}`).join('\n\n');
    }

    function parseFromRaw() {
        const markdown = document.getElementById('rawEditor').value;
        parseMarkdown(markdown);
        switchView('sections');
    }

    function updateStats() {
        document.getElementById('sectionCount').textContent = `${sections.length} ${sections.length === 1 ? 'seção' : 'seções'}`;
        document.getElementById('charCount').textContent = `${buildMarkdown().length} caracteres`;
    }

    function carregarDados() {
        fetch(BASE_URL + 'atendimento/agentes/acoes?acao=buscar&id=' + agenteId)
            .then(res => res.json())
            .then(response => {
                if (response.success && response.data && response.data.agente) {
                    const agente = response.data.agente;
                    document.getElementById('agenteNome').textContent = "Editando: " + agente.nome;
                    parseMarkdown(agente.prompt_sistema || '');
                } else {
                    showToast('Erro ao carregar agente', 'danger');
                }
            })
            .catch(err => {
                console.error(err);
                showToast('Erro de conexão', 'danger');
            });
    }

    function salvarPrompt() {
        const markdown = buildMarkdown();
        const btn = event.target;
        const originalText = btn.innerHTML;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Salvando...';
        btn.disabled = true;

        fetch(BASE_URL + 'atendimento/agentes/acoes?acao=buscar&id=' + agenteId)
            .then(res => res.json())
            .then(response => {
                if (response.success && response.data && response.data.agente) {
                    const agente = response.data.agente;
                    agente.prompt_sistema = markdown;

                    return fetch(BASE_URL + 'atendimento/agentes/acoes?acao=salvar', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify(agente)
                    });
                }
                throw new Error("Agente não encontrado");
            })
            .then(res => res.json())
            .then(resp => {
                if (resp.success) {
                    showToast('Prompt salvo com sucesso!', 'success');
                } else {
                    showToast('Erro: ' + (resp.message || 'Erro ao salvar'), 'danger');
                }
            })
            .catch(err => showToast('Erro: ' + err.message, 'danger'))
            .finally(() => {
                btn.innerHTML = originalText;
                btn.disabled = false;
            });
    }

    function showToast(message, type = 'success') {
        const toast = document.createElement('div');
        toast.className = 'position-fixed bottom-0 end-0 p-3';
        toast.style.zIndex = '9999';

        const bgClass = type === 'success' ? 'bg-success' : 'bg-danger';
        toast.innerHTML = `
            <div class="toast show align-items-center text-white ${bgClass} border-0">
                <div class="d-flex">
                    <div class="toast-body">${message}</div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" onclick="this.closest('.position-fixed').remove()"></button>
                </div>
            </div>
        `;
        document.body.appendChild(toast);
        setTimeout(() => toast.remove(), 4000);
    }

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
</script>



<!-- Modal Simulador -->
<div class="modal fade" id="modalSimulador" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title"><i class="bi bi-whatsapp me-2"></i>Simulador de WhatsApp (n8n)</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0">
                <div id="chat-container" class="p-3" style="height: 400px; overflow-y: auto; background: #e5ddd5;">
                    <div class="text-center text-muted small my-2">Início da Simulação</div>
                </div>
                <div class="p-3 bg-white border-top">
                    <div class="input-group">
                        <input type="text" id="simuladorInput" class="form-control" placeholder="Digite uma mensagem..."
                            onkeypress="if(event.key==='Enter') enviarSimulacao()">
                        <button class="btn btn-success" onclick="enviarSimulacao()">
                            <i class="bi bi-send"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function abrirSimulador() {
        const modal = new bootstrap.Modal(document.getElementById('modalSimulador'));
        modal.show();
        // Focar no input após abrir
        setTimeout(() => document.getElementById('simuladorInput').focus(), 500);
    }

    function addMessage(text, type, time = null) {
        const container = document.getElementById('chat-container');
        const div = document.createElement('div');
        const now = time || new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });

        div.className = `d-flex mb-2 ${type === 'sent' ? 'justify-content-end' : 'justify-content-start'}`;

        const bubbleColor = type === 'sent' ? '#dcf8c6' : '#ffffff';

        div.innerHTML = `
            <div style="background: ${bubbleColor}; padding: 8px 12px; border-radius: 8px; max-width: 80%; box-shadow: 0 1px 1px rgba(0,0,0,0.1);">
                <div>${text}</div>
                <div class="text-end text-muted" style="font-size: 0.7rem; margin-top: 2px;">${now}</div>
            </div>
        `;

        container.appendChild(div);
        container.scrollTop = container.scrollHeight;
    }

    async function enviarSimulacao() {
        const input = document.getElementById('simuladorInput');
        const texto = input.value.trim();

        if (!texto) return;

        // Adicionar mensagem do usuário
        addMessage(texto, 'sent');
        input.value = '';
        input.disabled = true;

        try {
            const response = await fetch(BASE_URL + 'atendimento/agentes/acoes?acao=testar_simulacao', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    mensagem: texto,
                    agente_id: <?php echo $id; ?> // Passando ID se for salvo
                })
            });

            const result = await response.json();

            if (result.success) {
                const data = result.data;
                console.log('Resposta n8n:', data);

                // Verificar se veio resposta direta do n8n
                // O n8n pode retornar JSON { "output": "Texto da IA" } ou algo do tipo
                if (data.response_json && (data.response_json.output || data.response_json.text || data.response_json.message)) {
                    const respostaIA = data.response_json.output || data.response_json.text || data.response_json.message;
                    addMessage(respostaIA, 'received');
                } else if (data.response_raw) {
                    // Tentar mostrar raw se for string curta
                    if (typeof data.response_raw === 'string' && data.response_raw.length < 500) {
                        addMessage(data.response_raw, 'received');
                    } else {
                        addMessage("✅ Enviado para n8n. (Resposta sem texto claro)", 'received');
                    }
                } else {
                    addMessage("✅ Enviado com sucesso!", 'received');
                }
            } else {
                addMessage("❌ Erro: " + (result.message || 'Falha no envio'), 'received');
            }

        } catch (error) {
            console.error(error);
            addMessage("❌ Erro de conexão", 'received');
        } finally {
            input.disabled = false;
            input.focus();
        }
    }
</script>