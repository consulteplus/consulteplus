<?php
$pageTitle = "Meus Projetos Estratégicos";
require_once __DIR__ . '/../../../../includes/header.php';
// checkPermission(['admin', 'cliente']);

$userId = $_SESSION['user_id'];

// Paginação
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int) $_GET['page'] : 1;
$limit = 6;
$offset = ($page - 1) * $limit;

// Contagem Total
$company_id = $_SESSION['company_id'];
$sqlCount = "SELECT COUNT(*) as total FROM gestao_projetos WHERE responsavel_id = $userId AND company_id = $company_id";
$totalRows = $conn->query($sqlCount)->fetch_assoc()['total'];
$totalPages = ceil($totalRows / $limit);

// Buscar Projetos com Progresso e Paginação
$sql = "
    SELECT 
        p.*, 
        COUNT(t.id) as total_tarefas,
        SUM(CASE WHEN t.status = 'done' THEN 1 ELSE 0 END) as tarefas_concluidas
    FROM gestao_projetos p
    LEFT JOIN gestao_tarefas t ON p.id = t.projeto_id
    WHERE p.responsavel_id = $userId AND p.company_id = $company_id
    GROUP BY p.id
    ORDER BY p.prioridade DESC, p.created_at DESC
    LIMIT $limit OFFSET $offset
";

// Função auxiliar para Markdown simples
function simple_markdown($text)
{
    if (empty($text))
        return 'Sem descrição.';
    $text = htmlspecialchars($text, ENT_QUOTES, 'UTF-8'); // Sanitize first
    $text = preg_replace('/\*\*(.*?)\*\*/', '<strong>$1</strong>', $text); // Bold
    $text = preg_replace('/\*(.*?)\*/', '<em>$1</em>', $text); // Italic
    $text = preg_replace('/^\s*-\s+(.*)/m', '<br>&bull; $1', $text); // Lists
    return nl2br($text);
}

$result = $conn->query($sql);
?>
<!-- ... (HTML starts) -->

<div class="container py-4">
    <!-- Header Padronizado (Sempre Visível) -->
    <div class="mb-4">
        <div class="card premium-card border-0 shadow-sm">
            <div class="card-body p-4">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <h4 class="fw-bold text-dark mb-1">Projetos Estratégicos</h4>
                        <p class="text-muted mb-0">Gerencie suas iniciativas de alto impacto.</p>
                    </div>
                    <div class="col-md-6 text-md-end mt-3 mt-md-0">
                        <div class="d-flex justify-content-md-end gap-2">
                            <a href="<?php echo BASE_URL; ?>admin/execucao-gestao/projetos/novo"
                                class="btn btn-outline-primary fw-semibold">
                                <i class="bi bi-plus-lg me-2"></i>Novo
                            </a>
                            <a href="<?php echo BASE_URL; ?>admin/execucao-gestao/diagnostico/novo"
                                class="btn btn-primary fw-semibold">
                                <i class="bi bi-stars me-2"></i>IA Diagnóstico
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Novo Projeto Manual (Mantido igual, omitido block para brevidade se não mudou) -->
    <!-- ... (Modal code remains same, assumed context logic will keep it if I don't touch it? 
         Wait, replace_file_content replaces the block. I need to include the modal if it was in the block.
         The block covers line 1 to 165. The modal starts around line 43. I MUST include the modal logic.)
         
         Actually, I will keep the modal code as is to avoid breaking it, inserting it below.
    -->

    <div class="modal fade" id="newProjectModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-4">
                <div class="modal-header border-bottom-0 pb-0">
                    <h5 class="modal-title fw-bold">Novo Projeto</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="formNewProject">
                    <div class="modal-body pt-4">
                        <!-- AI Assistant Section -->
                        <div class="d-flex justify-content-end mb-2">
                            <button type="button"
                                class="btn btn-sm btn-outline-primary border-0 fw-bold rounded-pill px-3"
                                onclick="toggleAiProjectInput()">
                                <i class="bi bi-stars me-1"></i> Preencher com IA
                            </button>
                        </div>

                        <div id="aiProjectInput" class="bg-light p-4 rounded-4 mb-3 border-0" style="display:none;">
                            <label class="form-label small fw-bold text-primary mb-1">O que vamos construir?</label>
                            <textarea class="form-control border-0 shadow-sm mb-3" id="aiProjectPrompt" rows="2"
                                placeholder="Ex: Novo site institucional moderno focando em conversão..."></textarea>
                            <button type="button" class="btn btn-primary w-100 fw-bold rounded-pill"
                                id="btn-generate-project" onclick="generateProjectWithAi()">
                                <i class="bi bi-lightning-charge-fill me-1"></i> Gerar Projeto
                            </button>
                        </div>

                        <div class="mb-3">
                            <label class="form-label text-muted small fw-bold text-uppercase">Título</label>
                            <input type="text" class="form-control form-control-lg bg-light border-0" id="projectTitle"
                                name="titulo" required placeholder="Ex: Expansão Comercial 2024">
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-muted small fw-bold text-uppercase">Objetivo / Escopo</label>
                            <textarea class="form-control bg-light border-0" id="projectDesc" name="descricao" rows="3"
                                placeholder="Detalhes da iniciativa..."></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-muted small fw-bold text-uppercase">Prioridade</label>
                            <select class="form-select bg-light border-0" id="projectPriority" name="prioridade">
                                <option value="alta">Alta 🔥</option>
                                <option value="media" selected>Média ⚡</option>
                                <option value="baixa">Baixa ☕</option>
                            </select>
                        </div>


                        <!-- Hidden Store for AI Data -->
                        <input type="hidden" name="ai_suggestions" id="aiSuggestions">

                        <!-- AI Preview Area (Lists) -->
                        <div id="aiPreviewContainer" class="mt-3 mb-3 p-3 bg-white border rounded-3 shadow-sm"
                            style="display:none; border-left: 4px solid #0d6efd !important;">
                            <h6 class="fw-bold text-primary mb-2"><i class="bi bi-robot me-1"></i> Sugestões da IA</h6>
                            <ul class="small mb-2 ps-3 text-secondary" id="aiOkrsList"></ul>
                            <ul class="small mb-0 ps-3 text-secondary" id="aiTasksList"></ul>
                        </div>

                    </div>
                    <div class="modal-footer border-0 pt-0 pb-4 pe-4">
                        <button type="button" class="btn btn-white text-muted fw-bold"
                            data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-dark px-4 rounded-pill fw-bold">Criar Projeto</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <?php if ($result && $result->num_rows > 0): ?>
        <div class="row g-4">
            <?php while ($proj = $result->fetch_assoc()):
                $total = $proj['total_tarefas'];
                $done = $proj['tarefas_concluidas'];
                $percent = ($total > 0) ? round(($done / $total) * 100) : 0;

                // Estilo Soft
                $badgeStyle = match ($proj['prioridade']) {
                    'alta' => 'background-color: #fee2e2; color: #991b1b;', // Red-100/800
                    'critica' => 'background-color: #f3f4f6; color: #1f2937;', // Gray
                    'baixa' => 'background-color: #d1fae5; color: #065f46;', // Green
                    default => 'background-color: #ffedd5; color: #9a3412;' // Orange
                };
                ?>
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100 border-0 shadow-sm hover-lift"
                        style="border-top: 4px solid; border-image: linear-gradient(90deg, <?php echo ($percent > 99 ? '#10b981' : '#3b82f6'); ?>, #8b5cf6) 1;">
                        <!-- HEADER (Title) -->
                        <div class="card-body px-4 pt-4 pb-3">
                            <h5 class="card-title fw-bold text-dark mb-0">
                                <?php echo htmlspecialchars($proj['titulo']); ?>
                            </h5>
                        </div>

                        <!-- DIVIDER (Full Width) -->
                        <hr class="my-0" style="border-color: #dee2e6; border-width: 1px;">

                        <!-- CONTENT (Description + Progress) -->
                        <div class="card-body px-4 py-3 d-flex flex-column flex-grow-1">

                            <div class="card-text text-muted small clamp-3 mb-3 flex-grow-1">
                                <?php echo simple_markdown($proj['descricao']); ?>
                            </div>

                            <div class="mb-3">
                                <div class="d-flex justify-content-between align-items-end mb-2">
                                    <span class="fw-bold text-dark h5 mb-0"><?php echo $percent; ?>%</span>
                                    <span class="small text-muted mb-1"><?php echo $done; ?>/<?php echo $total; ?> tasks</span>
                                </div>
                                <div class="progress rounded-pill bg-light" style="height: 8px;">
                                    <div class="progress-bar rounded-pill" role="progressbar"
                                        style="width: <?php echo $percent; ?>%; background-color: <?php echo ($percent > 99 ? '#10b981' : '#3b82f6'); ?>;">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- DIVIDER (Full Width) -->
                        <hr class="my-0" style="border-color: #dee2e6; border-width: 1px;">

                        <!-- FOOTER (Button) -->
                        <div class="card-body px-4 pb-4 pt-3" style="background-color: #f8f9fa;">
                            <a href="<?php echo BASE_URL; ?>admin/execucao-gestao/projetos/<?php echo $proj['id']; ?>"
                                class="btn btn-outline-primary w-100 fw-bold py-2" style="border-radius: 8px;">
                                Entrar no War Room
                            </a>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>

        <!-- Paginação -->
        <?php if ($totalPages > 1): ?>
            <nav class="mt-5 d-flex justify-content-center">
                <ul class="pagination pagination-md shadow-sm rounded-pill overflow-hidden bg-white">
                    <li class="page-item <?php echo ($page <= 1) ? 'disabled' : ''; ?>">
                        <a class="page-link border-0 text-dark" href="?page=<?php echo $page - 1; ?>"><i
                                class="bi bi-chevron-left me-1"></i> Anterior</a>
                    </li>

                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <li class="page-item <?php echo ($page == $i) ? 'active' : ''; ?>">
                            <a class="page-link border-0 fw-bold <?php echo ($page == $i) ? 'bg-dark border-dark' : 'text-dark'; ?>"
                                href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                        </li>
                    <?php endfor; ?>

                    <li class="page-item <?php echo ($page >= $totalPages) ? 'disabled' : ''; ?>">
                        <a class="page-link border-0 text-dark" href="?page=<?php echo $page + 1; ?>">Próximo <i
                                class="bi bi-chevron-right ms-1"></i></a>
                    </li>
                </ul>
            </nav>
        <?php endif; ?>

    <?php else: ?>
        <!-- EMPTY STATE (Sem projetos) -->
        <div class="row">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-body text-center py-5 px-4">
                        <div class="mb-4">
                            <i class="bi bi-kanban text-muted" style="font-size: 4rem; opacity: 0.3;"></i>
                        </div>

                        <h3 class="fw-bold mb-3">Nenhum Projeto Ativo</h3>

                        <p class="text-muted mb-4">
                            Você ainda não tem iniciativas estratégicas em andamento.
                        </p>

                        <div class="d-inline-block text-start bg-light rounded p-3 mb-4 mx-auto" style="max-width: 400px;">
                            <small class="text-muted d-flex align-items-center">
                                <i class="bi bi-lightbulb-fill text-warning me-2"></i>
                                <span>Dica: Use o Diagnóstico com IA para receber sugestões de projetos
                                    personalizados.</span>
                            </small>
                        </div>

                        <div class="d-grid gap-2 d-sm-flex justify-content-sm-center">
                            <button type="button" class="btn btn-outline-primary px-4" data-bs-toggle="modal"
                                data-bs-target="#newProjectModal">
                                <i class="bi bi-plus-lg me-2"></i>Criar Manualmente
                            </button>
                            <a href="<?php echo BASE_URL; ?>admin/execucao-gestao/diagnostico/novo"
                                class="btn btn-primary px-4">
                                <i class="bi bi-stars me-2"></i>Iniciar Diagnóstico
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<script>
    // --- AI PROJECT GENERATION ---
    function toggleAiProjectInput() {
        const el = document.getElementById('aiProjectInput');
        el.style.display = el.style.display === 'none' ? 'block' : 'none';
        if (el.style.display === 'block') document.getElementById('aiProjectPrompt').focus();
    }

    function generateProjectWithAi() {
        const promptInput = document.getElementById('aiProjectPrompt');
        const text = promptInput.value.trim();
        if (!text) return alert('Descreva o projeto primeiro.');

        const btn = document.getElementById('btn-generate-project');
        const originalHtml = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Definindo Estratégia...';

        const today = new Date().toISOString().split('T')[0];
        // ... (System prompt is sent via payload, assumed distinct logic or same variable)
        // Re-declaring key prompt variables to ensure scope safety if previous steps messed up
        const systemPrompt = `
### 🤖 YOU ARE A SENIOR PROJECT MANAGER.
Your goal is to define a Project Scope, Structured OKRs (Hierarchy), and Initial Tasks.

### 📅 CONTEXT:
Today is ${today}.

### 📝 RULES:
- Description: Use **Bold** headers.
- structure: **OBJETIVO**, **ESCOPO**, **MARCOS**.
- OKRs: Return an array of objects. Each object MUST have:
  - "objetivo": The main objective string.
  - "krs": Array of strings (Key Results).

### ⚠️ OUTPUT JSON:
{
  "titulo": "Project Title",
  "descricao": "Description...",
  "prioridade": "media" | "alta",
  "okrs": [
    { 
      "objetivo": "Main Objective 1", 
      "krs": ["Key Result 1.1", "Key Result 1.2"] 
    },
    { 
      "objetivo": "Main Objective 2", 
      "krs": ["Key Result 2.1"] 
    }
  ],
  "tarefas": [{"titulo": "T1", "descricao": "D1", "prioridade": "alta"}]
}
### INPUT:
${text}
`;

        const payload = { prompt_sistema: systemPrompt, user_input: text };

        fetch('<?php echo BASE_URL; ?>modules/admin/execucao-gestao/diagnostico/proxy_n8n.php?type=project', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(payload)
        })
            .then(async response => {
                const text = await response.text();
                try { return JSON.parse(text); }
                catch (e) { throw new Error('Falha JSON: ' + text.substring(0, 100)); }
            })
            .then(data => {
                if (data.error) throw new Error('Proxy: ' + JSON.stringify(data));

                let raw = Array.isArray(data) ? data[0] : data;
                let result = raw;

                // Robust Parse Logic
                const tryParse = (str) => {
                    if (typeof str !== 'string') return null;
                    try { return JSON.parse(str.replace(/```json/g, '').replace(/```/g, '').trim()); }
                    catch (e) { return null; }
                };

                if (raw.output) result = tryParse(raw.output) || raw.output;
                else if (raw.message && raw.message.content) result = tryParse(raw.message.content) || raw.message.content;
                else if (typeof raw === 'string') result = tryParse(raw) || raw;

                if (result && result.titulo) {
                    // 1. Fill Fields
                    document.getElementById('projectTitle').value = result.titulo;
                    document.getElementById('projectDesc').value = result.descricao || '';
                    document.getElementById('projectPriority').value = (result.prioridade || 'media').toLowerCase();

                    // 2. Store Data
                    const okrs = Array.isArray(result.okrs) ? result.okrs : [];
                    const tasks = Array.isArray(result.tarefas) ? result.tarefas : [];
                    document.getElementById('aiSuggestions').value = JSON.stringify({ okrs, tarefas: tasks });

                    // 3. Render Visual List
                    try {
                        const okrList = document.getElementById('aiOkrsList');
                        const taskList = document.getElementById('aiTasksList');
                        okrList.innerHTML = '';
                        taskList.innerHTML = '';

                        if (okrs.length > 0 || tasks.length > 0) {
                            okrs.forEach(o => {
                                // Support new Structure (Object) and Old (String)
                                if (typeof o === 'string') {
                                    okrList.innerHTML += `<li>${o}</li>`;
                                } else if (o.objetivo) {
                                    let krsHtml = '';
                                    if (Array.isArray(o.krs) && o.krs.length > 0) {
                                        krsHtml = `<ul class="ps-3 mt-1 small text-muted" style="list-style-type: circle;">${o.krs.map(k => `<li>${k}</li>`).join('')}</ul>`;
                                    }
                                    okrList.innerHTML += `<li class="mb-2"><strong>${o.objetivo}</strong>${krsHtml}</li>`;
                                }
                            });

                            tasks.forEach(t => taskList.innerHTML += `<li>${t.titulo} <span class='badge bg-light text-dark border'>${t.prioridade}</span></li>`);
                            document.getElementById('aiPreviewContainer').style.display = 'block';
                        } else {
                            document.getElementById('aiPreviewContainer').style.display = 'none';
                        }
                    } catch (renderErr) {
                        console.warn('Erro ao renderizar lista visual:', renderErr);
                    }

                    toggleAiProjectInput();
                    promptInput.value = '';

                    btn.className = 'btn btn-success btn-sm w-100 fw-bold';
                    btn.innerHTML = '<i class="bi bi-check-lg"></i> Sucesso!';
                    setTimeout(() => {
                        btn.className = 'btn btn-primary btn-sm w-100 fw-bold';
                        btn.innerHTML = originalHtml;
                        btn.disabled = false;
                    }, 2000);

                } else {
                    throw new Error('Formato inválido: ' + JSON.stringify(result).substring(0, 100));
                }
            })
            .catch(e => {
                console.error(e);
                alert('Erro: ' + e.message);
                btn.disabled = false;
                btn.innerHTML = originalHtml;
            });
    }

    document.getElementById('formNewProject').addEventListener('submit', function (e) {
        e.preventDefault();
        const btn = this.querySelector('button[type="submit"]');
        const originalText = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Criando...';

        const formData = new FormData(this);
        const data = Object.fromEntries(formData.entries());

        fetch('<?php echo BASE_URL; ?>modules/admin/execucao-gestao/projetos/criar_manual.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data)
        })
            .then(r => r.json())
            .then(d => {
                if (d.success) {
                    window.location.href = '<?php echo BASE_URL; ?>admin/execucao-gestao/projetos/detalhe.php?id=' + d.id;
                } else {
                    alert('Erro: ' + (d.error || 'Erro desconhecido'));
                    btn.disabled = false;
                    btn.innerHTML = originalText;
                }
            })
            .catch(e => {
                console.error(e);
                alert('Erro de conexão');
                btn.disabled = false;
                btn.innerHTML = originalText;
            });
    });
</script>



<?php require_once __DIR__ . '/../../../../includes/footer.php'; ?>