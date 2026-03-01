<?php
$pageTitle = "Detalhe da Tarefa";
require_once __DIR__ . '/../../../includes/header.php';
require_once __DIR__ . '/../../../config/database.php';

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id) {
    header('Location: ' . BASE_URL . 'gestao/tarefas');
    exit;
}

// Fetch Task
$company_id = $_SESSION['company_id'];

$isAdmin = false;
if (isset($_SESSION['user_id'])) {
    $uType = $conn->query("SELECT tipo FROM users WHERE id = {$_SESSION['user_id']}")->fetch_assoc()['tipo'] ?? '';
    if (in_array($uType, ['admin', 'superadmin']))
        $isAdmin = true;
}

$sql = "SELECT t.*, p.titulo as projeto_titulo 
        FROM gestao_tarefas t 
        LEFT JOIN gestao_projetos p ON t.projeto_id = p.id 
        WHERE t.id = $id";

if (!$isAdmin) {
    $sql .= " AND t.company_id = $company_id";
}
$result = $conn->query($sql);
$task = $result->fetch_assoc();

if (!$task) {
    echo "<div class='container py-5'>Tarefa não encontrada.</div>";
    require_once __DIR__ . '/../../../includes/footer.php';
    exit;
}

// Projects
$projects = [];
$resProj = $conn->query("SELECT id, titulo FROM gestao_projetos ORDER BY created_at DESC");
while ($p = $resProj->fetch_assoc()) {
    $projects[] = $p;
}
?>

<style>
    body {
        background-color: #fff;
        color: #37352f;
    }

    .notion-container {
        max-width: 900px;
        margin: 0 auto;
        padding-top: 40px;
        padding-bottom: 80px;
    }

    /* Typography */
    .notion-title {
        font-weight: 700;
        color: #37352f;
        font-size: 2.5rem;
        line-height: 1.2;
    }

    .property-label {
        color: #9d9b97;
        /* Notion light gray text */
        font-size: 0.9rem;
        display: flex;
        align-items: center;
        height: 34px;
        /* Align with input height */
    }

    .property-icon {
        width: 20px;
        margin-right: 8px;
        text-align: center;
    }

    /* Inputs Reset */
    .notion-input,
    .notion-select,
    .notion-textarea {
        background: transparent;
        border: 1px solid transparent;
        border-radius: 4px;
        padding: 4px 8px;
        width: 100%;
        color: #37352f;
        transition: background 0.1s, border-color 0.1s;
    }

    .notion-input:hover,
    .notion-select:hover,
    .notion-textarea:hover {
        background-color: #f7f6f3;
        /* Slight hover bg */
    }

    .notion-input:focus,
    .notion-select:focus,
    .notion-textarea:focus {
        background-color: #fff;
        border-color: #d0d0d0;
        /* Focus border */
        box-shadow: 0 0 0 2px rgba(46, 170, 220, 0.1);
        outline: none;
    }

    .notion-textarea {
        resize: none;
        min-height: 300px;
    }

    .notion-description {
        min-height: 100px;
        padding: 4px 8px;
        cursor: text;
        line-height: 1.6;
    }

    .notion-description li {
        list-style-type: disc;
        list-style-position: inside;
        margin-left: 10px;
    }

    .notion-description strong {
        font-weight: 600;
        color: #37352f;
    }

    /* Grid Layout for Properties */
    .properties-grid {
        display: grid;
        grid-template-columns: 140px 1fr;
        gap: 4px 16px;
        margin-bottom: 24px;
        align-items: center;
    }

    .divider {
        border-top: 1px solid #e9e9e8;
        margin: 30px 0;
    }

    /* Breadcrumb */
    .n-breadcrumb a {
        color: #787774;
        text-decoration: none;
        font-size: 0.9rem;
    }

    .n-breadcrumb a:hover {
        text-decoration: underline;
        color: #37352f;
    }
</style>

<div class="container-fluid notion-container">

    <!-- Top Bar -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="n-breadcrumb">
            <a href="<?php echo BASE_URL; ?>gestao/tarefas">Tarefas</a>
            <span class="mx-1 text-muted">/</span>
            <span class="text-muted">#<?php echo $task['id']; ?></span>
        </div>

        <div class="d-flex gap-2">
            <button class="btn btn-link text-danger text-decoration-none btn-sm" onclick="deleteTask()">
                <i class="bi bi-trash"></i>
            </button>
        </div>
    </div>

    <form id="formTaskMain">
        <input type="hidden" name="action" value="update">
        <input type="hidden" name="id" value="<?php echo $task['id']; ?>">

        <!-- Big Title -->
        <div class="mb-4">
            <input type="text" name="titulo" class="notion-input notion-title p-0 ps-1"
                value="<?php echo htmlspecialchars($task['titulo']); ?>" placeholder="Nome da Tarefa"
                autocomplete="off">
        </div>

        <!-- Properties Section -->
        <div class="properties-grid">

            <!-- Status -->
            <div class="property-label"><i class="bi bi-circle-half property-icon"></i> Status</div>
            <div>
                <select name="status" class="notion-select" onchange="submitForm()">
                    <option value="todo" <?php echo $task['status'] == 'todo' ? 'selected' : ''; ?>>A Fazer</option>
                    <option value="doing" <?php echo $task['status'] == 'doing' ? 'selected' : ''; ?>>Em Progresso
                    </option>
                    <option value="done" <?php echo $task['status'] == 'done' ? 'selected' : ''; ?>>Concluído</option>
                </select>
            </div>

            <!-- Priority -->
            <div class="property-label"><i class="bi bi-flag property-icon"></i> Prioridade</div>
            <div>
                <select name="prioridade" class="notion-select" onchange="submitForm()">
                    <option value="baixa" <?php echo $task['prioridade'] == 'baixa' ? 'selected' : ''; ?>>Baixa</option>
                    <option value="media" <?php echo $task['prioridade'] == 'media' ? 'selected' : ''; ?>>Média</option>
                    <option value="alta" <?php echo $task['prioridade'] == 'alta' ? 'selected' : ''; ?>>Alta</option>
                </select>
            </div>

            <!-- Project -->
            <div class="property-label"><i class="bi bi-folder2 property-icon"></i> Projeto</div>
            <div>
                <select name="projeto_id" class="notion-select" onchange="submitForm()">
                    <option value="">Sem Projeto</option>
                    <?php foreach ($projects as $proj): ?>
                        <option value="<?php echo $proj['id']; ?>" <?php echo $task['projeto_id'] == $proj['id'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($proj['titulo']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Deadline -->
            <div class="property-label"><i class="bi bi-calendar4 property-icon"></i> Prazo</div>
            <div>
                <input type="date" name="prazo" class="notion-input"
                    value="<?php echo !empty($task['prazo']) ? date('Y-m-d', strtotime($task['prazo'])) : ''; ?>"
                    onchange="submitForm()">
            </div>

        </div>

        <div class="divider"></div>

        <!-- Description Content -->
        <div>
            <div class="property-label mb-2"><i class="bi bi-text-paragraph property-icon"></i> Descrição e Detalhes
            </div>

            <!-- Rendered View (Click to Edit) -->
            <div id="descView" class="notion-description" onclick="enableEdit()" title="Clique para editar">
                <!-- Content injected via JS -->
            </div>

            <!-- Raw Editor (Hidden by default) -->
            <textarea name="descricao" id="descEdit" class="notion-textarea d-none"
                placeholder="Clique para adicionar detalhes..." oninput="autoResize(this)"
                onblur="disableEdit()"><?php echo htmlspecialchars($task['descricao'] ?? ''); ?></textarea>
        </div>

    </form>

    <!-- Save Indicator -->
    <div id="saveStatus" class="position-fixed bottom-0 end-0 m-4 text-muted small"
        style="opacity: 0; transition: opacity 0.3s; background: rgba(255,255,255,0.8); padding: 5px 10px; border-radius: 4px;">
        <i class="bi bi-check-circle-fill text-success me-1"></i> Salvo
    </div>
</div>

<script>
    const form = document.getElementById('formTaskMain');
    const saveStatus = document.getElementById('saveStatus');
    const descView = document.getElementById('descView');
    const descEdit = document.getElementById('descEdit');
    let timeoutId;

    // Init
    window.addEventListener('load', () => {
        updateView();
        autoResize(descEdit);
    });

    function toggleDesc(mode) {
        if (mode === 'edit') {
            descView.classList.add('d-none');
            descEdit.classList.remove('d-none');
            descEdit.focus();
            autoResize(descEdit);
        } else {
            descEdit.classList.add('d-none');
            descView.classList.remove('d-none');
        }
    }

    function enableEdit() {
        toggleDesc('edit');
    }

    function disableEdit() {
        toggleDesc('view');
        updateView();
        submitForm(); // Trigger save on blur
    }

    function updateView() {
        const raw = descEdit.value;
        if (!raw.trim()) {
            descView.innerHTML = '<span class="text-muted fst-italic">Clique para adicionar uma descrição...</span>';
            return;
        }
        descView.innerHTML = parseMarkdown(raw);
    }

    // Simple Markdown Parser (Notion-ish)
    function parseMarkdown(text) {
        let html = text
            // Escape HTML
            .replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;")
            // Bold (**text**)
            .replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>')
            // Lists (- item or 1. item)
            .replace(/^\s*[-•]\s+(.*)$/gm, '<li>$1</li>')
            .replace(/^\s*(\d+\.)\s+(.*)$/gm, '<li>$2</li>')
            // Line Breaks to <br>, but NOT after list items to avoid double spacing
            .replace(/\n/g, '<br>')
            .replace(/<\/li><br>/g, '</li>');

        return html;
    }

    function autoResize(el) {
        el.style.height = 'auto';
        el.style.height = el.scrollHeight + 'px';
    }

    // Auto-save
    form.addEventListener('input', function (e) {
        // Debounce only for text inputs (Title)
        // Description saves on blur to avoid jitter
        if (e.target.tagName === 'INPUT') {
            showSaving();
            clearTimeout(timeoutId);
            timeoutId = setTimeout(submitForm, 1000);
        }
    });

    function showSaving() {
        saveStatus.style.opacity = '1';
        saveStatus.innerHTML = '<i class="bi bi-arrow-clockwise spinner-border spinner-border-sm me-1"></i> Salvando...';
    }
    // ... rest of script

    function showSaved() {
        saveStatus.style.opacity = '1';
        saveStatus.innerHTML = '<i class="bi bi-check-circle-fill text-success me-1"></i> Salvo';
        setTimeout(() => { saveStatus.style.opacity = '0'; }, 2000);
    }

    function submitForm() {
        showSaving();
        const formData = new FormData(form);

        if (!formData.get('action')) formData.append('action', 'update');

        fetch('<?php echo BASE_URL; ?>modules/gestao/tarefas/acoes.php', { method: 'POST', body: formData })
            .then(r => r.json())
            .then(d => {
                if (d.success) showSaved();
                else alert('Erro ao salvar: ' + d.message);
            })
            .catch(e => console.error(e));
    }

    function deleteTask() {
        if (!confirm('Tem certeza?')) return;

        const formData = new FormData();
        formData.append('action', 'delete');
        formData.append('id', '<?php echo $task['id']; ?>');

        fetch('<?php echo BASE_URL; ?>modules/gestao/tarefas/acoes.php', { method: 'POST', body: formData })
            .then(r => r.json())
            .then(d => {
                if (d.success) window.location.href = '<?php echo BASE_URL; ?>gestao/tarefas';
                else alert('Erro: ' + d.message);
            });
    }
</script>

<?php require_once __DIR__ . '/../../../includes/footer.php'; ?>