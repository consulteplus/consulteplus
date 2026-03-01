<?php
$pageTitle = "Detalhes da Empresa";
require_once __DIR__ . '/../header.php';
checkPermission(['superadmin']);

if (!isset($_GET['id']) || empty($_GET['id'])) {
    redirect('admin/empresas');
}

$companyId = intval($_GET['id']);

// Process Form Submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {

    // ADD USER
    if ($_POST['action'] === 'add_user') {
        $nome = sanitize($_POST['nome']);
        $email = sanitize($_POST['email']);
        $senha = password_hash($_POST['senha'], PASSWORD_DEFAULT);
        $tipo = sanitize($_POST['tipo']);

        if ($nome && $email && $senha) {
            $check = $conn->prepare("SELECT id FROM users WHERE email = ?");
            $check->bind_param("s", $email);
            $check->execute();
            if ($check->get_result()->num_rows > 0) {
                $error = "E-mail já cadastrado!";
            } else {
                $stmt = $conn->prepare("INSERT INTO users (nome, email, senha, company_id, tipo, ativo, created_at) VALUES (?, ?, ?, ?, ?, 1, NOW())");
                $stmt->bind_param("sssss", $nome, $email, $senha, $companyId, $tipo);
                if ($stmt->execute()) {
                    $success = "Usuário adicionado!";
                } else {
                    $error = "Erro: " . $conn->error;
                }
            }
        } else {
            $error = "Preencha todos os campos!";
        }
    }

    // EDIT USER
    elseif ($_POST['action'] === 'edit_user') {
        $userId = intval($_POST['user_id']);
        $nome = sanitize($_POST['nome']);
        $email = sanitize($_POST['email']);
        $tipo = sanitize($_POST['tipo']);
        $status = intval($_POST['status']); // 1 or 0

        // Optional password update
        $newPass = !empty($_POST['senha']) ? $_POST['senha'] : null;

        if ($nome && $email) {
            // Check if email belongs to another user
            $check = $conn->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
            $check->bind_param("si", $email, $userId);
            $check->execute();

            if ($check->get_result()->num_rows > 0) {
                $error = "E-mail já em uso por outro usuário!";
            } else {
                if ($newPass) {
                    $senhaHash = password_hash($newPass, PASSWORD_DEFAULT);
                    $stmt = $conn->prepare("UPDATE users SET nome=?, email=?, senha=?, tipo=?, ativo=? WHERE id=? AND company_id=?");
                    $stmt->bind_param("ssssiii", $nome, $email, $senhaHash, $tipo, $status, $userId, $companyId);
                } else {
                    $stmt = $conn->prepare("UPDATE users SET nome=?, email=?, tipo=?, ativo=? WHERE id=? AND company_id=?");
                    $stmt->bind_param("sssiii", $nome, $email, $tipo, $status, $userId, $companyId);
                }

                if ($stmt->execute()) {
                    $success = "Usuário atualizado!";
                } else {
                    $error = "Erro ao atualizar: " . $conn->error;
                }
            }
        }
    }

    // DELETE USER
    elseif ($_POST['action'] === 'delete_user') {
        $userId = intval($_POST['user_id']);
        // Prevent deleting yourself (if superadmin handles it, maybe allowed, but careful)
        // Check if user belongs to this company
        $stmt = $conn->prepare("DELETE FROM users WHERE id = ? AND company_id = ?");
        $stmt->bind_param("ii", $userId, $companyId);

        if ($stmt->execute()) {
            $success = "Usuário removido.";
        } else {
            $error = "Erro ao remover: " . $conn->error;
        }
    }
}

// Fetch Company Details
$stmt = $conn->prepare("SELECT * FROM empresas WHERE id = ?");
$stmt->bind_param("i", $companyId);
$stmt->execute();
$empresa = $stmt->get_result()->fetch_assoc();

if (!$empresa) {
    echo "<div class='alert alert-danger'>Empresa não encontrada.</div>";
    require_once __DIR__ . '/../../../includes/footer.php';
    exit;
}

// Fetch Users
$stmtUsers = $conn->prepare("SELECT * FROM users WHERE company_id = ? ORDER BY created_at DESC");
$stmtUsers->bind_param("i", $companyId);
$stmtUsers->execute();
$users = $stmtUsers->get_result();

// Fetch Diagnostics History
$stmtDiag = $conn->prepare("
    SELECT r.id, r.data_realizacao, m.titulo as modelo_titulo, u.nome as responsavel
    FROM gestao_diagnostico_resultados r
    LEFT JOIN gestao_diagnostico_modelos m ON r.modelo_id = m.id
    LEFT JOIN users u ON r.user_id = u.id
    WHERE r.company_id = ?
    ORDER BY r.data_realizacao DESC
    LIMIT 20
");
$stmtDiag->bind_param("i", $companyId);
$stmtDiag->execute();
$stmtDiag->execute();
$diagnosticos = $stmtDiag->get_result();

// Fetch Mentorias
$stmtMen = $conn->prepare("
    SELECT m.*, a.created_at as data_liberacao,
    (SELECT COUNT(*) FROM mentoria_conteudos c JOIN mentoria_trilhas t ON c.trilha_id = t.id WHERE t.produto_id = m.id) as total_aulas
    FROM mentoria_acesso_empresas a
    JOIN mentoria_produtos m ON a.produto_id = m.id
    WHERE a.company_id = ?
    ORDER BY a.created_at DESC
");
$stmtMen->bind_param("i", $companyId);
$stmtMen->execute();
$mentorias = $stmtMen->get_result();

// Fetch Projetos
$stmtProj = $conn->prepare("
    SELECT p.*, u.nome as responsavel_nome,
    (SELECT COUNT(*) FROM gestao_tarefas t WHERE t.projeto_id = p.id) as total_tarefas,
    (SELECT COUNT(*) FROM gestao_tarefas t WHERE t.projeto_id = p.id AND t.status = 'done') as tarefas_feitas
    FROM gestao_projetos p
    LEFT JOIN users u ON p.responsavel_id = u.id
    WHERE p.company_id = ?
    ORDER BY p.created_at DESC
");
$stmtProj->bind_param("i", $companyId);
$stmtProj->execute();
$projetos = $stmtProj->get_result();

// Fetch Tarefas (Recent)
$stmtTasks = $conn->prepare("
    SELECT t.*, p.titulo as projeto_titulo
    FROM gestao_tarefas t
    LEFT JOIN gestao_projetos p ON t.projeto_id = p.id
    WHERE t.company_id = ?
    ORDER BY t.id DESC
    LIMIT 50
");
$stmtTasks->bind_param("i", $companyId);
$stmtTasks->execute();
$tarefas = $stmtTasks->get_result();

?>

<div class="container-fluid py-4">
    <!-- Breadcrumb e Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1">
                    <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>admin/empresas">Empresas</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Detalhes</li>
                </ol>
            </nav>
            <h1 class="h3 mb-0 text-gray-800"><?php echo htmlspecialchars($empresa['nome']); ?></h1>
            <span
                class="badge <?php echo $empresa['ativo'] ? 'bg-success' : 'bg-danger'; ?> bg-opacity-10 text-<?php echo $empresa['ativo'] ? 'success' : 'danger'; ?> rounded-pill mt-2">
                <?php echo $empresa['ativo'] ? 'Ativo' : 'Inativo'; ?>
            </span>
        </div>
    </div>

    <!-- Feedback Messages -->
    <?php if (isset($success)): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?php echo $success; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>
    <?php if (isset($error)): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?php echo $error; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <!-- Info Card -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow border-0">
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="text-muted small text-uppercase fw-bold">Documento</label>
                            <div class="fw-semibold"><?php echo htmlspecialchars($empresa['documento'] ?? 'N/A'); ?>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label class="text-muted small text-uppercase fw-bold">Data Cadastro</label>
                            <div class="fw-semibold"><?php echo date('d/m/Y', strtotime($empresa['created_at'])); ?>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label class="text-muted small text-uppercase fw-bold">Plano</label>
                            <div class="fw-semibold">Free</div>
                        </div>
                        <div class="col-md-3">
                            <label class="text-muted small text-uppercase fw-bold">ID do Tenant</label>
                            <div class="fw-semibold">#<?php echo $empresa['id']; ?></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabs -->
    <ul class="nav nav-tabs mb-4" id="companyTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="users-tab" data-bs-toggle="tab" data-bs-target="#users" type="button"
                role="tab">
                <i class="bi bi-people me-2"></i>Usuários
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="history-tab" data-bs-toggle="tab" data-bs-target="#history" type="button"
                role="tab">
                <i class="bi bi-clock-history me-2"></i>Histórico
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="mentorias-tab" data-bs-toggle="tab" data-bs-target="#mentorias" type="button"
                role="tab">
                <i class="bi bi-mortarboard me-2"></i>Mentorias
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="gestao-tab" data-bs-toggle="tab" data-bs-target="#gestao" type="button"
                role="tab">
                <i class="bi bi-kanban me-2"></i>Projetos e Tarefas
            </button>
        </li>
    </ul>

    <div class="tab-content" id="companyTabsContent">

        <!-- Users Tab -->
        <div class="tab-pane fade show active" id="users" role="tabpanel">
            <div class="card shadow border-0">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Usuários Cadastrados</h6>
                    <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addUserModal">
                        <i class="bi bi-person-plus me-2"></i>Novo Usuário
                    </button>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-4">Nome</th>
                                    <th>Email</th>
                                    <th>Tipo</th>
                                    <th>Status</th>
                                    <th>Cadastro</th>
                                    <th class="text-end pe-4">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($users->num_rows > 0): ?>
                                    <?php while ($access = $users->fetch_assoc()): ?>
                                        <tr>
                                            <td class="ps-4 fw-bold text-dark"><?php echo htmlspecialchars($access['nome']); ?>
                                            </td>
                                            <td><?php echo htmlspecialchars($access['email']); ?></td>
                                            <td>
                                                <?php if ($access['tipo'] == 'admin'): ?>
                                                    <span class="badge bg-primary bg-opacity-10 text-primary">Gestor</span>
                                                <?php else: ?>
                                                    <span class="badge bg-secondary bg-opacity-10 text-secondary">Colaborador</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if (!empty($access['active']) || !empty($access['ativo'])): ?>
                                                    <span class="text-success small"><i
                                                            class="bi bi-circle-fill me-1"></i>Ativo</span>
                                                <?php else: ?>
                                                    <span class="text-danger small"><i
                                                            class="bi bi-circle-fill me-1"></i>Inativo</span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="text-muted small">
                                                <?php echo date('d/m/Y', strtotime($access['created_at'])); ?>
                                            </td>
                                            <td class="text-end pe-4">
                                                <button class="btn btn-sm btn-outline-primary me-1" data-bs-toggle="modal"
                                                    data-bs-target="#editUserModal" data-id="<?php echo $access['id']; ?>"
                                                    data-nome="<?php echo htmlspecialchars($access['nome']); ?>"
                                                    data-email="<?php echo htmlspecialchars($access['email']); ?>"
                                                    data-tipo="<?php echo $access['tipo']; ?>"
                                                    data-ativo="<?php echo $access['ativo']; ?>">
                                                    <i class="bi bi-pencil"></i>
                                                </button>
                                                <form action="" method="POST" class="d-inline"
                                                    onsubmit="return confirm('Tem certeza que deseja apagar este usuário?');">
                                                    <input type="hidden" name="action" value="delete_user">
                                                    <input type="hidden" name="user_id" value="<?php echo $access['id']; ?>">
                                                    <button type="submit" class="btn btn-sm btn-outline-danger"><i
                                                            class="bi bi-trash"></i></button>
                                                </form>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="6" class="text-center py-4 text-muted">Nenhum usuário encontrado.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- History Tab (Diagnostics) -->
        <div class="tab-pane fade" id="history" role="tabpanel">
            <div class="card shadow border-0">
                <div class="card-header bg-white py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Últimos Diagnósticos</h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-4">Data</th>
                                    <th>Modelo Aplicado</th>
                                    <th>Responsável</th>
                                    <th class="text-end pe-4">Ação</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($diagnosticos->num_rows > 0): ?>
                                    <?php while ($diag = $diagnosticos->fetch_assoc()): ?>
                                        <tr>
                                            <td class="ps-4 text-muted small">
                                                <?php echo date('d/m/Y H:i', strtotime($diag['data_realizacao'])); ?>
                                            </td>
                                            <td class="fw-bold text-dark">
                                                <?php echo htmlspecialchars($diag['modelo_titulo'] ?? 'Diagnóstico Padrão'); ?>
                                            </td>
                                            <td><?php echo htmlspecialchars($diag['responsavel'] ?? 'N/A'); ?></td>
                                            <td class="text-end pe-4">
                                                <a href="#" class="btn btn-sm btn-outline-secondary"
                                                    title="Ver Resultado (Em breve)">
                                                    <i class="bi bi-file-text"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="4" class="text-center py-4 text-muted">Nenhum diagnóstico realizado
                                            ainda.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Mentorias Tab -->
        <div class="tab-pane fade" id="mentorias" role="tabpanel">
            <div class="card shadow border-0">
                <div class="card-header bg-white py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Mentorias Liberadas</h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-4">Mentoria</th>
                                    <th>Status</th>
                                    <th>Alunos / Progresso</th>
                                    <th>Ciclo / Valor</th>
                                    <th>Liberado em</th>
                                    <th class="text-end pe-4">Ação</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($mentorias && $mentorias->num_rows > 0): ?>
                                    <?php while ($men = $mentorias->fetch_assoc()):
                                        // Fetch User Progress for this Product
                                        $prodID = $men['id'];
                                        $totalAulas = max(1, $men['total_aulas']); // Avoid div by zero
                                
                                        $sqlProg = "SELECT u.nome, COUNT(mp.id) as concluidas 
                                                    FROM users u 
                                                    LEFT JOIN mentoria_progresso mp ON u.id = mp.user_id AND mp.concluido = 1
                                                    LEFT JOIN mentoria_conteudos mc ON mp.conteudo_id = mc.id
                                                    LEFT JOIN mentoria_trilhas mt ON mc.trilha_id = mt.id AND mt.produto_id = $prodID
                                                    WHERE u.company_id = $companyId
                                                    GROUP BY u.id
                                                    HAVING concluidas > 0
                                                    ORDER BY concluidas DESC LIMIT 5";
                                        $progRes = $conn->query($sqlProg);
                                        ?>
                                        <tr>
                                            <td class="ps-4">
                                                <div class="fw-bold text-dark"><?php echo htmlspecialchars($men['titulo']); ?>
                                                </div>
                                                <small class="text-muted"><?php echo $men['total_aulas']; ?> aulas
                                                    totais</small>
                                            </td>
                                            <td>
                                                <?php if ($men['ativo']): ?>
                                                    <span class="badge bg-success bg-opacity-10 text-success">Ativa</span>
                                                <?php else: ?>
                                                    <span class="badge bg-danger bg-opacity-10 text-danger">Inativa</span>
                                                <?php endif; ?>
                                            </td>
                                            <td style="min-width: 250px;">
                                                <?php if ($progRes && $progRes->num_rows > 0): ?>
                                                    <div class="d-flex flex-column gap-2 py-1">
                                                        <?php while ($pUser = $progRes->fetch_assoc()):
                                                            $pct = round(($pUser['concluidas'] / $totalAulas) * 100);
                                                            ?>
                                                            <div>
                                                                <div class="d-flex justify-content-between small mb-1">
                                                                    <span><?php echo htmlspecialchars($pUser['nome']); ?></span>
                                                                    <span class="fw-bold"><?php echo $pct; ?>%</span>
                                                                </div>
                                                                <div class="progress" style="height: 4px;">
                                                                    <div class="progress-bar" role="progressbar"
                                                                        style="width: <?php echo $pct; ?>%"></div>
                                                                </div>
                                                            </div>
                                                        <?php endwhile; ?>
                                                    </div>
                                                <?php else: ?>
                                                    <span class="small text-muted">Nenhum progresso ainda.</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php echo $men['ciclo']; ?><br>
                                                R$ <?php echo number_format($men['valor'], 2, ',', '.'); ?>
                                            </td>
                                            <td class="text-muted small">
                                                <?php echo date('d/m/Y', strtotime($men['data_liberacao'])); ?>
                                            </td>
                                            <td class="text-end pe-4 text-nowrap">
                                                <a href="#" class="btn btn-sm btn-outline-danger"
                                                    title="Revogar Acesso (Em breve)">
                                                    <i class="bi bi-slash-circle"></i>
                                                </a>
                                                <a href="<?php echo BASE_URL; ?>admin/mentoria/distribuir"
                                                    class="btn btn-sm btn-outline-primary ms-1" title="Gerenciar Distribuição">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="5" class="text-center py-4 text-muted">Nenhuma mentoria liberada.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Gestão Tab (Projetos e Tarefas) -->
        <div class="tab-pane fade" id="gestao" role="tabpanel">
            <div class="row g-4">
                <!-- Projetos -->
                <div class="col-12">
                    <div class="card shadow border-0">
                        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                            <h6 class="m-0 font-weight-bold text-primary">Projetos em Andamento</h6>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="bg-light">
                                        <tr>
                                            <th class="ps-4">Título</th>
                                            <th>Status</th>
                                            <th>Prioridade</th>
                                            <th>Progresso</th>
                                            <th>Responsável</th>
                                            <th class="text-end pe-4">Ação</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if ($projetos && $projetos->num_rows > 0): ?>
                                            <?php while ($proj = $projetos->fetch_assoc()):
                                                $progresso = $proj['total_tarefas'] > 0 ? round(($proj['tarefas_feitas'] / $proj['total_tarefas']) * 100) : 0;
                                                ?>
                                                <tr>
                                                    <td class="ps-4 fw-bold"><?php echo htmlspecialchars($proj['titulo']); ?>
                                                    </td>
                                                    <td>
                                                        <span
                                                            class="badge bg-light text-dark border"><?php echo $proj['status'] ?? 'active'; ?></span>
                                                    </td>
                                                    <td><?php echo strtoupper($proj['prioridade']); ?></td>
                                                    <td style="width: 200px;">
                                                        <div class="d-flex align-items-center">
                                                            <div class="progress flex-grow-1" style="height: 6px;">
                                                                <div class="progress-bar" role="progressbar"
                                                                    style="width: <?php echo $progresso; ?>%"></div>
                                                            </div>
                                                            <span class="ms-2 small"><?php echo $progresso; ?>%</span>
                                                        </div>
                                                    </td>
                                                    <td class="small">
                                                        <?php echo htmlspecialchars($proj['responsavel_nome'] ?? '-'); ?>
                                                    </td>
                                                    <td class="text-end pe-4 text-nowrap">
                                                        <a href="<?php echo BASE_URL; ?>gestao/projetos/<?php echo $proj['id']; ?>"
                                                            target="_blank" class="btn btn-sm btn-outline-info"
                                                            title="Visualizar Projeto">
                                                            <i class="bi bi-eye"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                            <?php endwhile; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="5" class="text-center py-4 text-muted">Nenhum projeto
                                                    encontrado.</td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tarefas -->
                <div class="col-12">
                    <div class="card shadow border-0">
                        <div class="card-header bg-white py-3">
                            <h6 class="m-0 font-weight-bold text-info">Últimas Tarefas</h6>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="bg-light">
                                        <tr>
                                            <th class="ps-4">Tarefa</th>
                                            <th>Projeto</th>
                                            <th>Status</th>
                                            <th>Prazo</th>
                                            <th class="text-end pe-4">Ação</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if ($tarefas && $tarefas->num_rows > 0): ?>
                                            <?php while ($t = $tarefas->fetch_assoc()): ?>
                                                <tr>
                                                    <td class="ps-4"><?php echo htmlspecialchars($t['titulo']); ?></td>
                                                    <td class="small text-muted">
                                                        <?php echo htmlspecialchars($t['projeto_titulo'] ?? '-'); ?>
                                                    </td>
                                                    <td>
                                                        <?php
                                                        $statusMap = [
                                                            'todo' => ['label' => 'A Fazer', 'class' => 'secondary'],
                                                            'doing' => ['label' => 'Em Andamento', 'class' => 'primary'],
                                                            'done' => ['label' => 'Concluída', 'class' => 'success']
                                                        ];
                                                        $st = $statusMap[$t['status']] ?? $statusMap['todo'];
                                                        ?>
                                                        <span
                                                            class="badge bg-<?php echo $st['class']; ?> bg-opacity-10 text-<?php echo $st['class']; ?>">
                                                            <?php echo $st['label']; ?>
                                                        </span>
                                                    </td>
                                                    <td class="small">
                                                        <?php echo $t['prazo'] ? date('d/m/Y', strtotime($t['prazo'])) : '-'; ?>
                                                    </td>
                                                    <td class="text-end pe-4 text-nowrap">
                                                        <a href="<?php echo BASE_URL; ?>gestao/tarefas/<?php echo $t['id']; ?>"
                                                            target="_blank" class="btn btn-sm btn-outline-info"
                                                            title="Visualizar Tarefa">
                                                            <i class="bi bi-eye"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                            <?php endwhile; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="5" class="text-center py-4 text-muted">Nenhuma tarefa recente.
                                                </td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<!-- Modal Add User -->
<div class="modal fade" id="addUserModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form method="POST" class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Adicionar Novo Usuário</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="action" value="add_user">

                <div class="mb-3">
                    <label class="form-label">Nome Completo</label>
                    <input type="text" name="nome" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">E-mail</label>
                    <input type="email" name="email" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Senha Inicial</label>
                    <input type="text" name="senha" class="form-control" value="Mudar123" required>
                    <div class="form-text">Senha padrão sugerida. O usuário poderá alterar depois.</div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Tipo de Acesso</label>
                    <select name="tipo" class="form-select">
                        <option value="colaborador">Colaborador (Acesso Padrão)</option>
                        <option value="admin">Gestor (Acesso Admin)</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-primary">Adicionar</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit User -->
<div class="modal fade" id="editUserModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form method="POST" class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Editar Usuário</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="action" value="edit_user">
                <input type="hidden" name="user_id" id="edit_user_id">

                <div class="mb-3">
                    <label class="form-label">Nome Completo</label>
                    <input type="text" name="nome" id="edit_nome" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">E-mail</label>
                    <input type="email" name="email" id="edit_email" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Redefinir Senha (opcional)</label>
                    <input type="text" name="senha" class="form-control"
                        placeholder="Deixe em branco para manter a atual">
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Tipo de Acesso</label>
                        <select name="tipo" id="edit_tipo" class="form-select">
                            <option value="colaborador">Colaborador</option>
                            <option value="admin">Gestor (Admin)</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Status</label>
                        <select name="status" id="edit_status" class="form-select">
                            <option value="1">Ativo</option>
                            <option value="0">Inativo</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-primary">Salvar Alterações</button>
            </div>
        </form>
    </div>
</div>

<script>
    // Script to populate Edit Modal
    var editUserModal = document.getElementById('editUserModal');
    editUserModal.addEventListener('show.bs.modal', function (event) {
        var button = event.relatedTarget;

        var id = button.getAttribute('data-id');
        var nome = button.getAttribute('data-nome');
        var email = button.getAttribute('data-email');
        var tipo = button.getAttribute('data-tipo');
        var ativo = button.getAttribute('data-ativo');

        var modal = this;
        modal.querySelector('#edit_user_id').value = id;
        modal.querySelector('#edit_nome').value = nome;
        modal.querySelector('#edit_email').value = email;
        modal.querySelector('#edit_tipo').value = tipo;
        modal.querySelector('#edit_status').value = ativo;
    });
</script>

<?php require_once __DIR__ . '/../../../includes/footer.php'; ?>