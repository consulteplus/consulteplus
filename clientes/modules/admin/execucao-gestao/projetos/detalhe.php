<?php
$pageTitle = "Detalhes do Projeto";
require_once __DIR__ . '/../../../../includes/header.php';

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$userId = $_SESSION['user_id'];

$company_id = $_SESSION['company_id'];

// Buscar Projeto
$stmt = null;
$isAdmin = false;
if (isset($_SESSION['user_id'])) {
    $uType = $conn->query("SELECT tipo FROM users WHERE id = {$_SESSION['user_id']}")->fetch_assoc()['tipo'] ?? '';
    if (in_array($uType, ['admin', 'superadmin']))
        $isAdmin = true;
}

if ($isAdmin) {
    $stmt = $conn->prepare("SELECT * FROM gestao_projetos WHERE id = ?");
    $stmt->bind_param("i", $id);
} else {
    $stmt = $conn->prepare("SELECT * FROM gestao_projetos WHERE id = ? AND responsavel_id = ? AND company_id = ?");
    $stmt->bind_param("iii", $id, $userId, $company_id);
}
$stmt->execute();
$project = $stmt->get_result()->fetch_assoc();

if (!$project) {
    echo "<div class='container py-5'><div class='alert alert-danger'>Projeto não encontrado.</div></div>";
    require_once __DIR__ . '/../../../includes/footer.php';
    exit;
}

// Buscar OKRs
$okrs = [];
$resOkrs = $conn->query("SELECT * FROM gestao_objetivos WHERE projeto_id = $id");
while ($okr = $resOkrs->fetch_assoc()) {
    $okr['krs'] = [];
    $resKrs = $conn->query("SELECT * FROM gestao_resultados_chave WHERE objetivo_id = " . $okr['id']);
    while ($kr = $resKrs->fetch_assoc()) {
        $okr['krs'][] = $kr;
    }
    $okrs[] = $okr;
}

// Buscar Tarefas (Resumo Numérico)
$resTasks = $conn->query("SELECT status, COUNT(*) as qtd FROM gestao_tarefas WHERE projeto_id = $id GROUP BY status");
$taskStats = ['todo' => 0, 'doing' => 0, 'done' => 0];
$totalTasks = 0;
while ($row = $resTasks->fetch_assoc()) {
    $taskStats[$row['status']] = $row['qtd'];
    $totalTasks += $row['qtd'];
}
$percent = ($totalTasks > 0) ? round(($taskStats['done'] / $totalTasks) * 100) : 0;
?>

<div class="container py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1">
                    <li class="breadcrumb-item"><a
                            href="<?php echo BASE_URL; ?>admin/execucao-gestao/projetos">Projetos</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Detalhes</li>
                </ol>
            </nav>
            <h2 class="fw-bold mb-0">
                <?php echo htmlspecialchars($project['titulo']); ?>
                <span class="badge bg-secondary fs-6 align-middle ms-2"><?php echo $project['status']; ?></span>
            </h2>
        </div>
        <div>
            <a href="<?php echo BASE_URL; ?>admin/execucao-gestao/tarefas?projeto_id=<?php echo $id; ?>"
                class="btn btn-outline-primary">
                <i class="bi bi-kanban me-2"></i>Ver no Kanban Geral
            </a>
            <button class="btn btn-primary" onclick="alert('Funcionalidade de edição em breve')">
                <i class="bi bi-pencil me-2"></i>Editar
            </button>
        </div>
    </div>

    <!-- Stats Row -->
    <div class="row g-4 mb-4">
        <div class="col-md-8">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <h6 class="text-muted text-uppercase fw-bold mb-3" style="font-size: 0.75rem;">Progresso Geral</h6>
                    <div class="d-flex align-items-center">
                        <div class="progress flex-grow-1" style="height: 15px;">
                            <div class="progress-bar bg-success" role="progressbar"
                                style="width: <?php echo $percent; ?>%">
                                <?php echo $percent; ?>%
                            </div>
                        </div>
                        <span
                            class="ms-3 fw-bold text-muted"><?php echo $taskStats['done']; ?>/<?php echo $totalTasks; ?>
                            tarefas</span>
                    </div>
                    <div class="mt-3 text-secondary mb-0" style="white-space: pre-wrap; font-family: inherit;"><?php
                    $desc = htmlspecialchars($project['descricao']);
                    // Parse Markdown Bold
                    $desc = preg_replace('/\*\*(.*?)\*\*/', '<strong class="text-dark">$1</strong>', $desc);
                    // Parse Markdown Lists (hyphen or asterisk at start of line)
                    $desc = preg_replace('/^[\-\*]\s+(.*)$/m', '• $1', $desc);
                    echo $desc;
                    ?></div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <h6 class="text-muted text-uppercase fw-bold mb-3" style="font-size: 0.75rem;">Status das Tarefas
                    </h6>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <span><i class="bi bi-circle text-secondary me-2"></i>A Fazer</span>
                            <span class="badge bg-light text-dark rounded-pill"><?php echo $taskStats['todo']; ?></span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <span><i class="bi bi-play-circle text-primary me-2"></i>Em Andamento</span>
                            <span
                                class="badge bg-light text-dark rounded-pill"><?php echo $taskStats['doing']; ?></span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <span><i class="bi bi-check-circle text-success me-2"></i>Concluídas</span>
                            <span class="badge bg-light text-dark rounded-pill"><?php echo $taskStats['done']; ?></span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabs: OKRs e Kanban Board Project View -->
    <ul class="nav nav-tabs mb-4" id="projectTabs" role="tablist">
        <li class="nav-item">
            <button class="nav-link active" id="okrs-tab" data-bs-toggle="tab" data-bs-target="#okrs" type="button">
                <i class="bi bi-bullseye me-2"></i>Objetivos & KRs
            </button>
        </li>
        <li class="nav-item">
            <button class="nav-link" id="tasks-tab" data-bs-toggle="tab" data-bs-target="#tasks" type="button">
                <i class="bi bi-list-task me-2"></i>Tarefas do Projeto
            </button>
        </li>
    </ul>

    <div class="tab-content" id="projectTabsContent">
        <!-- OKRs TAB -->
        <div class="tab-pane fade show active" id="okrs" role="tabpanel">
            <?php if (empty($okrs)): ?>
                <div class="alert alert-light text-center py-5">
                    Nenhum objetivo definido para este projeto.
                </div>
            <?php else: ?>
                <div class="row g-4">
                    <?php foreach ($okrs as $obj): ?>
                        <div class="col-12">
                            <div class="card border-0 shadow-sm">
                                <div class="card-header bg-white py-3">
                                    <h5 class="mb-0 fw-bold text-primary">
                                        <i class="bi bi-flag-fill me-2"></i> <?php echo $obj['titulo']; ?>
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <?php if (empty($obj['krs'])): ?>
                                        <p class="text-muted fst-italic mb-0">Sem Key Results cadastrados.</p>
                                    <?php else: ?>
                                        <div class="table-responsive">
                                            <table class="table table-borderless align-middle mb-0">
                                                <thead class="text-muted small text-uppercase">
                                                    <tr>
                                                        <th>Resultado Chave (KR)</th>
                                                        <th style="width: 150px;">Progresso</th>
                                                        <th style="width: 100px;" class="text-end">Status</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php foreach ($obj['krs'] as $kr):
                                                        $krPercent = ($kr['valor_meta'] > 0) ? ($kr['valor_atual'] / $kr['valor_meta']) * 100 : 0;
                                                        $krPercent = min(100, max(0, $krPercent));
                                                        ?>
                                                        <tr>
                                                            <td class="fw-bold text-dark"><?php echo $kr['titulo']; ?></td>
                                                            <td>
                                                                <div class="d-flex align-items-center">
                                                                    <div class="progress flex-grow-1" style="height: 6px;">
                                                                        <div class="progress-bar bg-info"
                                                                            style="width: <?php echo $krPercent; ?>%"></div>
                                                                    </div>
                                                                    <span
                                                                        class="ms-2 small text-muted"><?php echo round($krPercent); ?>%</span>
                                                                </div>
                                                            </td>
                                                            <td class="text-end">
                                                                <span
                                                                    class="badge bg-light text-dark border"><?php echo $kr['valor_atual']; ?>/<?php echo $kr['valor_meta']; ?></span>
                                                            </td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- TASKS TAB (List View for now) -->
        <div class="tab-pane fade" id="tasks" role="tabpanel">
            <!-- Embedded Kanban or List? Let's verify if we can iframe or just list. For V1, a list is safer. -->
            <div class="alert alert-info">
                <i class="bi bi-info-circle me-2"></i>
                Para gerenciar o fluxo visualmente, acesse o <a
                    href="<?php echo BASE_URL; ?>gestao/tarefas?projeto_id=<?php echo $id; ?>" class="fw-bold">Kanban
                    Completo</a>.
            </div>

            <?php
            // Re-fetch detailed tasks
            $resTaskList = $conn->query("SELECT * FROM gestao_tarefas WHERE projeto_id = $id ORDER BY status, prioridade DESC");
            ?>
            <div class="list-group shadow-sm">
                <?php while ($t = $resTaskList->fetch_assoc()):
                    $statusIcon = match ($t['status']) {
                        'done' => 'bi-check-circle-fill text-success',
                        'doing' => 'bi-play-circle-fill text-primary',
                        default => 'bi-circle text-secondary'
                    };
                    ?>
                    <div class="list-group-item d-flex align-items-center py-3">
                        <i class="bi <?php echo $statusIcon; ?> fs-4 me-3"></i>
                        <div class="flex-grow-1">
                            <h6 class="mb-0 fw-bold"><?php echo $t['titulo']; ?></h6>
                            <small class="text-muted text-uppercase" style="font-size: 0.7rem;">
                                <?php if (!empty($t['prazo'])):
                                    $d = new DateTime($t['prazo']);
                                    $isOverdue = ($d < new DateTime() && $t['status'] !== 'done');
                                    $class = $isOverdue ? 'text-danger fw-bold' : '';
                                    ?>
                                    <span class="<?php echo $class; ?> me-2"><i
                                            class="bi bi-calendar-event me-1"></i><?php echo $d->format('d/m'); ?></span> •
                                <?php endif; ?>
                                <?php echo $t['status']; ?> • <?php echo $t['prioridade']; ?>
                            </small>
                        </div>
                        <a href="#" class="btn btn-sm btn-light"><i class="bi bi-pencil"></i></a>
                    </div>
                <?php endwhile; ?>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../../../../includes/footer.php'; ?>