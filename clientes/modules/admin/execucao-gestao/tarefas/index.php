<?php
$pageTitle = "Gestão de Tarefas";
require_once __DIR__ . '/../../../../includes/header.php';
require_once __DIR__ . '/../../../../config/database.php';

// Filter by Status (Grouped)
$tasks = [
    'todo' => [],
    'doing' => [],
    'done' => []
];

// Flat list for Table/Calendar
$allTasks = [];

$company_id = $_SESSION['company_id'];
$sql = "SELECT t.*, p.titulo as projeto_titulo 
        FROM gestao_tarefas t 
        LEFT JOIN gestao_projetos p ON t.projeto_id = p.id 
        WHERE t.company_id = $company_id
        ORDER BY FIELD(t.prioridade, 'alta', 'media', 'baixa'), t.prazo ASC";

$result = $conn->query($sql);

// Collect projects for filter
$filterProjects = [];
$uniqueProjects = [];

if ($result) {
    while ($row = $result->fetch_assoc()) {
        // Collect Project for Filter
        if (!empty($row['projeto_titulo']) && !in_array($row['projeto_id'], $uniqueProjects)) {
            $uniqueProjects[] = $row['projeto_id'];
            $filterProjects[] = ['id' => $row['projeto_id'], 'titulo' => $row['projeto_titulo']];
        }

        // Group for Kanban
        if (isset($tasks[$row['status']])) {
            $tasks[$row['status']][] = $row;
        } else {
            $tasks[$row['status']][] = $row; // Fallback to raw status if created dynamically
        }
        // Flat list
        $allTasks[] = $row;
    }
}
?>

<link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/locales/pt-br.global.min.js"></script>

<style>
    body {
        background-color: #f0f2f5;
    }

    /* Filters */
    .filter-bar select {
        font-size: 0.85rem;
        border-color: #e3e4e6;
        background-color: white;
        min-width: 140px;
    }

    .filter-bar select:focus {
        border-color: #37352f;
        box-shadow: none;
    }

    /* Tabs Navigation */
    .view-switcher {
        background: #e3e4e6;
        display: inline-flex;
        padding: 4px;
        border-radius: 6px;
    }

    .view-btn {
        border: none;
        background: transparent;
        color: #5a5a5a;
        padding: 6px 12px;
        font-size: 0.9rem;
        border-radius: 4px;
        font-weight: 500;
        transition: all 0.2s;
    }

    .view-btn:hover {
        background: rgba(255, 255, 255, 0.5);
    }

    .view-btn.active {
        background: white;
        color: #37352f;
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
    }

    /* Kanban Styles */
    .kanban-board {
        display: flex;
        gap: 1.5rem;
        overflow-x: auto;
        padding-bottom: 2rem;
        align-items: flex-start;
        min-height: calc(100vh - 250px);
    }

    .kanban-column {
        flex: 1;
        min-width: 320px;
        background-color: #e3e4e6;
        border-radius: 6px;
        padding: 0.75rem;
        display: flex;
        flex-direction: column;
    }

    .kanban-column-header {
        font-weight: 600;
        margin-bottom: 0.5rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0.25rem 0.5rem;
        color: #5a5a5a;
        font-size: 0.9rem;
        text-transform: uppercase;
    }

    .badge-counter {
        color: #7a7a7a;
        font-weight: 500;
        font-size: 0.85rem;
    }

    .kanban-task {
        background: white;
        border-radius: 4px;
        margin-bottom: 8px;
        cursor: grab;
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
        transition: transform 0.1s, box-shadow 0.1s;
        position: relative;
        overflow: hidden;
        border: 0;
    }

    .kanban-task:active {
        cursor: grabbing;
    }

    .kanban-task.hidden-task {
        display: none !important;
    }

    /* Task Card Sections */
    .task-header {
        padding: 12px 14px;
    }

    .task-content {
        padding: 12px 14px;
    }

    .task-footer {
        padding: 10px 14px;
        background-color: #f8f9fa;
    }

    .task-divider {
        margin: 0;
        border: 0;
        border-top: 1px solid #dee2e6;
        opacity: 1;
    }

    .text-notion-gray {
        color: #787774;
    }

    .badge-project {
        background: #e9ecef;
        color: #495057;
        font-weight: 500;
        border-radius: 4px;
        font-size: 0.75rem;
        padding: 3px 8px;
    }

    /* List View Styles */
    .task-table-row {
        cursor: pointer;
        transition: background 0.1s;
    }

    .task-table-row:hover {
        background-color: #f8f9fa;
    }

    /* Calendar Styles */
    .fc-event {
        cursor: pointer;
        border: none;
        padding: 2px 4px;
        font-size: 0.85rem;
    }
</style>

<div class="container-fluid py-4" style="max-width: 1400px;">

    <!-- Header Padronizado -->
    <div class="mb-4">
        <div class="card premium-card border-0 shadow-sm">
            <div class="card-body p-4">
                <div class="row align-items-center">
                    <div class="col-md-4">
                        <h4 class="fw-bold text-dark mb-1">Tarefas</h4>
                        <p class="text-muted mb-0">Organize e acompanhe suas atividades.</p>
                    </div>
                    <div class="col-md-8">
                        <div class="row g-2 align-items-center justify-content-end">
                            <!-- View Switcher -->
                            <div class="col-auto">
                                <div class="view-switcher d-none d-lg-inline-flex">
                                    <button class="view-btn active" onclick="switchView('kanban')"><i
                                            class="bi bi-kanban me-1"></i> Quadro</button>
                                    <button class="view-btn" onclick="switchView('lista')"><i
                                            class="bi bi-list-ul me-1"></i> Lista</button>
                                    <button class="view-btn" onclick="switchView('calendario')"><i
                                            class="bi bi-calendar3 me-1"></i> Calendário</button>
                                </div>
                            </div>

                            <!-- Search -->
                            <div class="col-auto">
                                <div class="input-group input-group-sm border rounded bg-white" style="width: 140px;">
                                    <span class="input-group-text bg-transparent border-0 pe-1"><i
                                            class="bi bi-search text-muted"></i></span>
                                    <input type="text" class="form-control border-0 ps-1 shadow-none" id="searchInput"
                                        placeholder="Buscar..." onkeyup="filterTasks()">
                                </div>
                            </div>

                            <!-- Filter -->
                            <div class="col-auto">
                                <div class="dropdown">
                                    <button
                                        class="btn btn-white border shadow-sm btn-sm px-3 d-flex align-items-center fw-500 text-secondary"
                                        type="button" data-bs-toggle="dropdown" aria-expanded="false"
                                        style="height: 38px;">
                                        <i class="bi bi-funnel me-2"></i> Filtrar
                                    </button>
                                    <div class="dropdown-menu p-3 shadow-sm border-0 dropdown-menu-end"
                                        style="width: 280px;">
                                        <h6 class="dropdown-header text-uppercase small fw-bold mb-2 ps-0 text-muted"
                                            style="letter-spacing: 0.5px;">Adicionar Filtro</h6>
                                        <div class="mb-3">
                                            <label class="form-label small text-secondary fw-bold mb-1">Status</label>
                                            <select class="form-select form-select-sm bg-light border-0"
                                                id="filterStatus" onchange="filterTasks()">
                                                <option value="all">Todos</option>
                                                <option value="todo">A Fazer</option>
                                                <option value="doing">Em Progresso</option>
                                                <option value="done">Concluído</option>
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label
                                                class="form-label small text-secondary fw-bold mb-1">Prioridade</label>
                                            <select class="form-select form-select-sm bg-light border-0"
                                                id="filterPriority" onchange="filterTasks()">
                                                <option value="all">Todas</option>
                                                <option value="alta">Alta 🔥</option>
                                                <option value="media">Média ⚡</option>
                                                <option value="baixa">Baixa ☕</option>
                                            </select>
                                        </div>
                                        <div class="mb-1">
                                            <label class="form-label small text-secondary fw-bold mb-1">Projeto</label>
                                            <select class="form-select form-select-sm bg-light border-0"
                                                id="filterProject" onchange="filterTasks()">
                                                <option value="all">Todos</option>
                                                <?php foreach ($filterProjects as $fp): ?>
                                                    <option value="<?php echo $fp['id']; ?>">
                                                        <?php echo htmlspecialchars($fp['titulo']); ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Nova Button -->
                            <div class="col-auto">
                                <button
                                    class="btn btn-dark btn-sm px-3 shadow-sm d-flex align-items-center fw-bold text-nowrap"
                                    style="height: 38px;" data-bs-toggle="modal" data-bs-target="#modalNovaTarefa">
                                    <i class="bi bi-plus-lg me-2"></i> Nova
                                </button>
                            </div>
                        </div>

                        <!-- Mobile View Switcher -->
                        <div class="d-lg-none mt-3">
                            <div class="view-switcher w-100 justify-content-between">
                                <button class="view-btn flex-fill text-center active" onclick="switchView('kanban')"><i
                                        class="bi bi-kanban"></i></button>
                                <button class="view-btn flex-fill text-center" onclick="switchView('lista')"><i
                                        class="bi bi-list-ul"></i></button>
                                <button class="view-btn flex-fill text-center" onclick="switchView('calendario')"><i
                                        class="bi bi-calendar3"></i></button>
                            </div>
                        </div>
                    </div>
                </div>


                <!-- Active Filters Chips -->
                <div id="activeFilters" class="d-flex gap-2 flex-wrap mt-3"></div>
            </div>
        </div>
    </div>

    <!-- VIEW: KANBAN -->
    <div id="view-kanban" class="view-section">
        <div class="kanban-board">
            <!-- Coluna A Fazer -->
            <div class="kanban-column" id="col-todo" ondrop="drop(event)" ondragover="allowDrop(event)"
                data-status="todo">
                <div class="kanban-column-header">
                    <div><span class="header-dot bg-warning d-inline-block rounded-circle me-2"
                            style="width:8px;height:8px;"></span>A Fazer</div>
                    <span class="badge-counter counter"><?php echo count($tasks['todo'] ?? []); ?></span>
                </div>
                <div class="task-list" style="min-height: 50px;">
                    <?php foreach (($tasks['todo'] ?? []) as $task):
                        renderTask($task);
                    endforeach; ?>
                </div>
                <div class="mt-2 text-muted small p-2 text-center text-uppercase fw-bold"
                    style="opacity: 0.6; cursor: pointer; border-radius: 4px;"
                    onmouseover="this.style.background='rgba(0,0,0,0.05)'"
                    onmouseout="this.style.background='transparent'" data-bs-toggle="modal"
                    data-bs-target="#modalNovaTarefa">+ Nova</div>
            </div>

            <!-- Coluna Em Progresso -->
            <div class="kanban-column" id="col-doing" ondrop="drop(event)" ondragover="allowDrop(event)"
                data-status="doing">
                <div class="kanban-column-header">
                    <div><span class="header-dot bg-info d-inline-block rounded-circle me-2"
                            style="width:8px;height:8px;"></span>Em Progresso</div>
                    <span class="badge-counter counter"><?php echo count($tasks['doing'] ?? []); ?></span>
                </div>
                <div class="task-list" style="min-height: 50px;">
                    <?php foreach (($tasks['doing'] ?? []) as $task):
                        renderTask($task);
                    endforeach; ?>
                </div>
            </div>

            <!-- Coluna Concluído -->
            <div class="kanban-column" id="col-done" ondrop="drop(event)" ondragover="allowDrop(event)"
                data-status="done">
                <div class="kanban-column-header">
                    <div><span class="header-dot bg-success d-inline-block rounded-circle me-2"
                            style="width:8px;height:8px;"></span>Concluído</div>
                    <span class="badge-counter counter"><?php echo count($tasks['done'] ?? []); ?></span>
                </div>
                <div class="task-list" style="min-height: 50px;">
                    <?php foreach (($tasks['done'] ?? []) as $task):
                        renderTask($task);
                    endforeach; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Rest of Views... -->



    <!-- VIEW: LISTA -->
    <div id="view-lista" class="view-section d-none">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-0">
                <table class="table table-hover mb-0 align-middle">
                    <thead class="bg-light text-muted small text-uppercase">
                        <tr>
                            <th class="ps-4 border-0">Tarefa</th>
                            <th class="border-0">Status</th>
                            <th class="border-0">Prioridade</th>
                            <th class="border-0">Projeto</th>
                            <th class="border-0">Prazo</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($allTasks as $t):
                            $statusLabel = [
                                'todo' => '<span class="badge bg-secondary">A Fazer</span>',
                                'doing' => '<span class="badge bg-info">Em Progresso</span>',
                                'done' => '<span class="badge bg-success">Concluído</span>'
                            ][$t['status']] ?? $t['status'];

                            $prioColor = ['alta' => 'danger', 'media' => 'warning', 'baixa' => 'secondary'][$t['prioridade']] ?? 'secondary';
                            ?>
                            <tr class="task-table-row"
                                onclick="location.href='<?php echo BASE_URL; ?>admin/execucao-gestao/tarefas/<?php echo $t['id']; ?>'">
                                <td class="ps-4 fw-bold text-dark"><?php echo htmlspecialchars($t['titulo']); ?></td>
                                <td class="text-nowrap"><?php echo $statusLabel; ?></td>
                                <td><span
                                        class="badge bg-<?php echo $prioColor; ?>-subtle text-<?php echo $prioColor; ?> text-uppercase"
                                        style="font-size: 0.7rem;"><?php echo $t['prioridade']; ?></span></td>
                                <td><?php echo $t['projeto_titulo'] ? '<span class="badge-project">' . $t['projeto_titulo'] . '</span>' : '-'; ?>
                                </td>
                                <td class="text-muted small text-nowrap">
                                    <?php echo $t['prazo'] ? date('d/m/Y', strtotime($t['prazo'])) : '-'; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- VIEW: CALENDARIO -->
    <div id="view-calendario" class="view-section d-none">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div id="calendar"></div>
            </div>
        </div>
    </div>

</div>

<!-- Modal Nova Tarefa -->
<div class="modal fade" id="modalNovaTarefa" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form class="modal-content" id="formNovaTarefa">
            <div class="modal-header border-0">
                <h5 class="modal-title fw-bold">Nova Tarefa</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- AI Assistant Section -->
                <div class="d-flex justify-content-end mb-2">
                    <button type="button" class="btn btn-sm btn-outline-primary border-0 fw-bold"
                        onclick="toggleAiTaskInput()">
                        <i class="bi bi-stars me-1"></i> Preencher com IA
                    </button>
                </div>

                <div id="aiTaskInput" class="bg-light p-3 rounded mb-3 border" style="display:none;">
                    <label class="form-label small fw-bold text-primary mb-1">Descreva a tarefa em linguagem
                        natural:</label>
                    <textarea class="form-control mb-2" id="aiTaskPrompt" rows="2"
                        placeholder="Ex: Urgente: Preparar relatório de vendas para sexta-feira sobre a Black Friday"></textarea>
                    <button type="button" class="btn btn-primary btn-sm w-100 fw-bold" id="btn-generate-task"
                        onclick="generateTaskWithAi()">
                        <i class="bi bi-lightning-charge-fill me-1"></i> Gerar Campos
                    </button>
                    <small class="text-muted d-block mt-1" style="font-size: 0.75rem;">A IA preencherá o formulário para
                        você.</small>
                </div>

                <input type="hidden" name="action" value="create">
                <div class="mb-3">
                    <label class="form-label small text-muted text-uppercase fw-bold">Título</label>
                    <input type="text" name="titulo" class="form-control" required
                        placeholder="O que precisa ser feito?">
                </div>
                <div class="mb-3">
                    <label class="form-label small text-muted text-uppercase fw-bold">Descrição</label>
                    <textarea name="descricao" class="form-control" rows="3" placeholder="Detalhes..."></textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label small text-muted text-uppercase fw-bold">Projeto</label>
                    <select name="projeto_id" class="form-select">
                        <option value="">(Sem Projeto)</option>
                        <?php foreach ($filterProjects as $fp): ?>
                            <option value="<?php echo $fp['id']; ?>"><?php echo htmlspecialchars($fp['titulo']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="row g-2">
                    <div class="col-6">
                        <label class="form-label small text-muted text-uppercase fw-bold">Status</label>
                        <select name="status" class="form-select">
                            <option value="todo">A Fazer</option>
                            <option value="doing">Em Progresso</option>
                            <option value="done">Concluído</option>
                        </select>
                    </div>
                    <div class="col-6">
                        <label class="form-label small text-muted text-uppercase fw-bold">Prioridade</label>
                        <select name="prioridade" class="form-select">
                            <option value="media">Média</option>
                            <option value="alta">Alta</option>
                            <option value="baixa">Baixa</option>
                        </select>
                    </div>
                </div>
                <div class="mb-3 mt-2">
                    <label class="form-label small text-muted text-uppercase fw-bold">Prazo</label>
                    <input type="date" name="prazo" class="form-control">
                </div>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-dark">Criar Tarefa</button>
            </div>
        </form>
    </div>
</div>

<?php
// Function to render single Kanban Card
function renderTask($task)
{
    if (!$task)
        return;

    // Preview Logic
    $cleanDesc = '';
    if ($task['descricao']) {
        $cleanDesc = preg_replace('/(\*\*|__)(.*?)\1/', '$2', $task['descricao']);
        $cleanDesc = str_replace(['* ', '- '], '', $cleanDesc);
        $cleanDesc = mb_substr($cleanDesc, 0, 90) . (mb_strlen($cleanDesc) > 90 ? '...' : '');
    }

    // Project badge
    $projectHtml = '';
    if (!empty($task['projeto_titulo'])) {
        $projectHtml = '<span class="badge-project">' . htmlspecialchars($task['projeto_titulo']) . '</span>';
    }


    // Priority badge
    $prioLabel = ['alta' => 'Alta', 'media' => 'Média', 'baixa' => 'Baixa'][$task['prioridade']] ?? 'Média';
    $prioColor = ['alta' => 'danger', 'media' => 'warning', 'baixa' => 'secondary'][$task['prioridade']] ?? 'secondary';
    $prioHtml = '<span class="badge bg-' . $prioColor . '-subtle text-' . $prioColor . ' small">' . $prioLabel . '</span>';

    // Update deadline format
    $deadlineHtml = '';
    if (!empty($task['prazo'])) {
        $deadlineDate = strtotime($task['prazo']);
        $formattedDeadline = date('d/m/Y', $deadlineDate);
        $isOverdue = ($deadlineDate < time() && $task['status'] !== 'done');
        $color = $isOverdue ? '#dc3545' : '#6c757d';
        $icon = $isOverdue ? 'bi-exclamation-circle-fill' : 'bi-calendar3';
        $deadlineHtml = '<span class="small me-3" style="color: ' . $color . ';" title="Prazo"><i class="bi ' . $icon . ' me-1"></i>' . $formattedDeadline . '</span>';
    }

    echo '
    <div class="kanban-task" id="task-' . $task['id'] . '" draggable="true" ondragstart="drag(event)"
         data-id="' . $task['id'] . '"
         data-prioridade="' . ($task['prioridade'] ?? 'media') . '"
         data-projeto-id="' . ($task['projeto_id'] ?? '') . '"
         data-titulo="' . htmlspecialchars($task['titulo']) . '"
         onclick="location.href=\'' . BASE_URL . 'admin/execucao-gestao/tarefas/' . $task['id'] . '\'">
        
        <!-- HEADER -->
        <div class="task-header">
            <h6 class="fw-bold mb-0" style="font-size: 0.95rem; color: #212529;">' . htmlspecialchars($task['titulo']) . '</h6>
        </div>
        
        <!-- DIVIDER -->
        <hr class="task-divider">
        
        <!-- CONTENT -->
        <div class="task-content">
            ' . ($cleanDesc ? '<p class="text-muted small mb-3" style="line-height: 1.5;">' . htmlspecialchars($cleanDesc) . '</p>' : '') . '
            
            <div class="d-flex align-items-center flex-wrap gap-2">
                ' . $prioHtml . '
                ' . $deadlineHtml . '
            </div>
        </div>
        
        ' . ($projectHtml ? '
        <!-- DIVIDER -->
        <hr class="task-divider">
        
        <!-- FOOTER -->
        <div class="task-footer">
            ' . $projectHtml . '
        </div>' : '') . '
    </div>';
}
?>

<script>
    // --- FILTERS ---
    // --- FILTERS & CHIPS ---
    function filterTasks() {
        // 1. Get Elements
        const sFilter = document.getElementById('filterStatus');
        const pFilter = document.getElementById('filterPriority');
        const projFilter = document.getElementById('filterProject');
        const searchInput = document.getElementById('searchInput'); // New Search

        const sVal = sFilter.value;
        const pVal = pFilter.value;
        const projVal = projFilter.value;
        const searchVal = searchInput.value.toLowerCase().trim(); // New Search Logic

        // 2. Render Active Chips
        renderActiveFilters(sFilter, pFilter, projFilter);

        const tasks = document.querySelectorAll('.kanban-task');

        tasks.forEach(task => {
            // Get Task Data
            // Status is on the column, so we find closest column
            const col = task.closest('.kanban-column');
            const status = col ? col.dataset.status : '';

            const priority = task.dataset.prioridade;
            const project = task.dataset.projetoId;
            const title = (task.dataset.titulo || '').toLowerCase(); // Get Title

            let show = true;

            // Apply Logic
            if (sVal !== 'all' && status !== sVal) show = false;
            if (pVal !== 'all' && priority !== pVal) show = false;
            if (projVal !== 'all' && project != projVal) show = false;
            if (searchVal && !title.includes(searchVal)) show = false; // Search Logic

            // Toggle Visibility
            if (show) {
                task.classList.remove('hidden-task');
            } else {
                task.classList.add('hidden-task');
            }
        });

        updateCounters();
    }

    function renderActiveFilters(sEl, pEl, projEl) {
        const container = document.getElementById('activeFilters');
        container.innerHTML = ''; // Clear

        const createChip = (id, label, text) => {
            const chip = document.createElement('div');
            // Notion-like Chip Style
            chip.className = 'badge bg-white text-secondary border shadow-sm d-flex align-items-center gap-2 px-2 py-1 fw-500';
            chip.style.cursor = 'pointer';
            chip.style.height = '32px';
            chip.innerHTML = `<span class="text-muted small">${label}:</span> <span class="text-dark">${text}</span> <i class="bi bi-x ms-1 text-muted" style="font-size: 1rem;"></i>`;
            chip.onclick = () => clearFilter(id);
            return chip;
        };

        if (sEl.value !== 'all') {
            const text = sEl.options[sEl.selectedIndex].text;
            container.appendChild(createChip('filterStatus', 'Status', text));
        }
        if (pEl.value !== 'all') {
            const text = pEl.options[pEl.selectedIndex].text;
            container.appendChild(createChip('filterPriority', 'Prioridade', text));
        }
        if (projEl.value !== 'all') {
            const text = projEl.options[projEl.selectedIndex].text;
            // Shorten project name if too long
            let t = text;
            if (t.length > 20) t = t.substring(0, 18) + '...';
            container.appendChild(createChip('filterProject', 'Proj', t));
        }
    }

    function clearFilter(id) {
        const el = document.getElementById(id);
        if (el) {
            el.value = 'all';
            filterTasks(); // Re-run logic
        }
    }

    // --- VIEW SWITCHER ---
    let calendarInit = false;
    let calendar;

    function switchView(viewName) {
        // Toggle buttons
        document.querySelectorAll('.view-btn').forEach(b => b.classList.remove('active'));
        event.currentTarget.classList.add('active'); // Needs 'event' available or passed

        // Hide all
        document.querySelectorAll('.view-section').forEach(el => el.classList.add('d-none'));

        // Show selected
        const target = document.getElementById('view-' + viewName);
        if (target) target.classList.remove('d-none');

        // Init Calendar if needed
        if (viewName === 'calendario' && !calendarInit) {
            initCalendar();
            calendarInit = true;
        } else if (viewName === 'calendario') {
            calendar.render(); // Re-render to fix layout issues
        }
    }

    // --- CALENDAR INIT ---
    function initCalendar() {
        const calendarEl = document.getElementById('calendar');

        // Prepare events from PHP
        const tasks = <?php
        // Prevent PHP Notices from breaking JS
        ob_start();
        $events = [];

        // 1. ADD TEST EVENT (DEBUG)
        $events[] = [
            'id' => 'test-999',
            'title' => '⚡ TESTE DE SISTEMA',
            'start' => date('Y-m-d'),
            'color' => '#000000',
            'url' => '#',
            'extendedProps' => ['description' => 'Tarefa de teste gerada pelo sistema.']
        ];

        foreach ($allTasks as $t) {
            // Skip if no deadline
            if (empty($t['prazo']) || $t['prazo'] == '0000-00-00')
                continue;

            $color = '#0d6efd'; // Default Blue
            if ($t['status'] === 'done')
                $color = '#198754'; // Green
            elseif ($t['prioridade'] === 'alta')
                $color = '#dc3545'; // Red
            elseif ($t['prioridade'] === 'media')
                $color = '#fd7e14'; // Orange
        
            $events[] = [
                'id' => $t['id'],
                'title' => $t['titulo'],
                'start' => date('Y-m-d', strtotime($t['prazo'])), // Ensure format
                'color' => $color,
                'url' => BASE_URL . 'admin/execucao-gestao/tarefas/' . $t['id'],
                'extendedProps' => [
                    'description' => $t['descricao'] ?? ''
                ]
            ];
        }
        $json = json_encode($events, JSON_UNESCAPED_UNICODE | JSON_HEX_APOS | JSON_HEX_QUOT);
        ob_end_clean(); // Discard noise
        echo $json ?: '[]'; // Safe echo
        ?>;

        // Calculate dynamic start date to find tasks (Past or Future)
        let initialDate = new Date().toISOString().split('T')[0];
        if (tasks.length > 0) {
            // Sort by date
            const sorted = [...tasks].sort((a, b) => new Date(a.start) - new Date(b.start));
            // Try to find a task in the future or today
            const future = sorted.find(t => new Date(t.start) >= new Date());
            if (future) initialDate = future.start;
            else initialDate = sorted[sorted.length - 1].start; // If all past, go to the last one
        }

        console.log('Calendar Tasks Loaded:', tasks.length);
        console.log('Calendar Initial Date:', initialDate);

        calendar = new FullCalendar.Calendar(calendarEl, {
            locale: 'pt-br',
            initialView: 'dayGridMonth',
            initialDate: initialDate, // Jump to task date
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,listWeek'
            },
            events: tasks,
            height: 'auto',
            navLinks: true,
            eventClick: function (info) {
                info.jsEvent.preventDefault();
                if (info.event.url) {
                    window.location.href = info.event.url;
                }
            },
            eventDidMount: function (info) {
                // Add tooltip using standard title attribute
                info.el.title = info.event.title + (info.event.extendedProps.description ? '\n' + info.event.extendedProps.description : '');
            }
        });
        calendar.render();
    }

    // --- KANBAN DRAG & DROP ---
    function allowDrop(ev) {
        ev.preventDefault();
        const column = ev.target.closest('.kanban-column');
        if (column) column.classList.add('bg-light');
    }

    // Fix dragstart to properly grab the ID
    function drag(ev) {
        // Find the closest .kanban-task element, even if clicked on a child
        const target = ev.target.closest('.kanban-task');
        if (target) {
            ev.dataTransfer.setData("text", target.id);
        }
    }

    function drop(ev) {
        ev.preventDefault();
        var data = ev.dataTransfer.getData("text");
        var task = document.getElementById(data);
        if (!task) return;

        // Find drop column
        const column = ev.target.closest('.kanban-column');
        if (!column) return;

        // Append visual
        const list = column.querySelector('.task-list');
        list.appendChild(task);

        // Update Status
        var taskId = task.getAttribute('data-id');
        var newStatus = column.getAttribute('data-status');

        updateTaskStatus(taskId, newStatus);
        updateCounters();
    }

    function updateTaskStatus(id, status) {
        const formData = new FormData();
        formData.append('action', 'update_status');
        formData.append('id', id);
        formData.append('status', status);
        fetch('<?php echo BASE_URL; ?>modules/admin/execucao-gestao/tarefas/acoes.php', { method: 'POST', body: formData });
    }

    function updateCounters() {
        document.querySelectorAll('.kanban-column').forEach(col => {
            // Count only cards NOT hidden
            const count = col.querySelectorAll('.kanban-task:not(.hidden-task)').length;
            col.querySelector('.counter').innerText = count;
        });
    }

    // --- NEW TASK FORM ---
    document.getElementById('formNovaTarefa').addEventListener('submit', function (e) {
        e.preventDefault();
        const formData = new FormData(this);

        fetch('<?php echo BASE_URL; ?>modules/admin/execucao-gestao/tarefas/acoes.php', { method: 'POST', body: formData })
            .then(r => r.json())
            .then(d => {
                if (d.success) location.reload();
                else alert('Erro: ' + d.message);
            });
    });
    // --- AI TASK GENERATION ---
    function toggleAiTaskInput() {
        const el = document.getElementById('aiTaskInput');
        el.style.display = el.style.display === 'none' ? 'block' : 'none';
        if (el.style.display === 'block') document.getElementById('aiTaskPrompt').focus();
    }

    function generateTaskWithAi() {
        const promptInput = document.getElementById('aiTaskPrompt');
        const text = promptInput.value.trim();
        if (!text) return alert('Descreva a tarefa primeiro.');

        const btn = document.getElementById('btn-generate-task');
        const originalHtml = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Pensando...';

        const today = new Date().toISOString().split('T')[0];

        const systemPrompt = `
# ROLE
Você é um Especialista em Produtividade.
Seu objetivo é transformar solicitações simples em tarefas detalhadas, usando apenas TEXTO PURO.

# CONTEXTO TEMPORAL
📅 Data de hoje: ${today}

# Regras de Saída (CRÍTICO)
1. Você deve retornar APENAS um objeto JSON válido.
2. Não use Markdown (sem \`\`\`json). Retorne apenas o texto cru do JSON.
3. Não inclua texto introdutório ou explicativo.
4. O idioma deve ser Português do Brasil.

# REGRA PARA A DESCRIÇÃO (SEM HTML)
NÃO use tags HTML. Use quebras de linha normais (\\n) e listas com hífen (-).
Use asteriscos duplos (**) para simular negrito nos títulos.

Estrutura da descrição:

**O QUE É:**
[Breve explicação do objetivo]

**PASSOS SUGERIDOS:**
- [Ação 1]
- [Ação 2]
- [Ação 3]

**RESULTADO ESPERADO:**
[O que teremos pronto ao final]

# FORMATO DE SAÍDA (JSON OBRIGATÓRIO)

{
  "titulo": "Título Curto e Direto (Verbo + Objeto)",
  "descricao": "(Texto formatado conforme estrutura acima)",
  "prioridade": "baixa" | "media" | "alta",
  "prazo": "YYYY-MM-DD" (Data futura calculada),
  "status": "todo"
}

# EXEMPLO DE QUALIDADE
**Entrada:** "Criar post pro instagram sobre natal"

**Saída:**
{
  "titulo": "Criar Post Carrossel Natal",
  "descricao": "**O QUE É:**\\nCriação de conteúdo engajador para comemorar o Natal e conectar com a audiência.\\n\\n**PASSOS SUGERIDOS:**\\n- Pesquisar referências visuais natalinas\\n- Escrever legenda focada em gratidão\\n- Criar arte no Canva/Photoshop\\n\\n**RESULTADO ESPERADO:**\\nPost agendado e pronto para publicação.",
  "prioridade": "media",
  "prazo": "2025-12-24",
  "status": "todo"
}

# INPUT DO USUÁRIO
${text}
`;

        // Payload similar to Analysis but simpler
        const payload = {
            prompt_sistema: systemPrompt,
            user_input: text // Some n8n flows might use this content directly
        };

        fetch('<?php echo BASE_URL; ?>modules/admin/execucao-gestao/diagnostico/proxy_n8n.php?type=task', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(payload)
        })
            .then(r => r.json())
            .then(data => {
                console.log('N8N Raw Response:', data); // Debug help

                // 1. Normalize Input (Handle Array)
                let raw = Array.isArray(data) ? data[0] : data;
                let result = raw;

                // 2. Helper to try parsing string to JSON
                const tryParse = (str) => {
                    if (typeof str !== 'string') return null;
                    try {
                        // Remove markdown blocks if present
                        const clean = str.replace(/```json/g, '').replace(/```/g, '').trim();
                        return JSON.parse(clean);
                    } catch (e) { return null; }
                };

                // 3. Deep Search for JSON content in common N8N output fields
                // Priority: direct object > output > message > content > text
                if (raw.output) {
                    result = tryParse(raw.output) || raw.output;
                } else if (raw.message && raw.message.content) {
                    result = tryParse(raw.message.content) || raw.message.content;
                } else if (raw.content) {
                    result = tryParse(raw.content) || raw.content;
                } else if (typeof raw === 'string') {
                    result = tryParse(raw) || raw;
                }

                // 4. Populate Form
                if (result && typeof result === 'object' && result.titulo) {
                    const form = document.getElementById('formNovaTarefa');
                    form.querySelector('[name="titulo"]').value = result.titulo;
                    form.querySelector('[name="descricao"]').value = result.descricao || '';
                    form.querySelector('[name="prioridade"]').value = (result.prioridade || 'media').toLowerCase();
                    if (result.prazo) form.querySelector('[name="prazo"]').value = result.prazo;
                    if (result.status && form.querySelector('[name="status"]')) form.querySelector('[name="status"]').value = result.status;

                    // Close AI Panel
                    toggleAiTaskInput();
                    promptInput.value = '';

                    // Visual feedback
                    btn.className = 'btn btn-success btn-sm w-100 fw-bold';
                    btn.innerHTML = '<i class="bi bi-check-lg"></i> Preenchido!';
                    setTimeout(() => {
                        btn.className = 'btn btn-primary btn-sm w-100 fw-bold';
                        btn.innerHTML = originalHtml;
                        btn.disabled = false;
                    }, 2000);
                } else {
                    const debugStr = typeof raw === 'object' ? JSON.stringify(raw, null, 2) : String(raw);
                    alert('A IA retornou dados, mas não lemos o "titulo".\n\nRESPOSTA BRUTA:\n' + debugStr.substring(0, 500));
                    console.warn('AI Parsed Result:', result);
                    btn.disabled = false;
                    btn.innerHTML = originalHtml;
                }
            })
            .catch(e => {
                console.error(e);
                alert('Erro de conexão com a IA (Verifique type=task no proxy).');
                btn.disabled = false;
                btn.innerHTML = originalHtml;
            });
    }
</script>

<?php require_once __DIR__ . '/../../../../includes/footer.php'; ?>