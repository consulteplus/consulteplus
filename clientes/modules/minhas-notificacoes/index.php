<?php
$pageTitle = "Notificações";
require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../includes/notificacoes-helper.php';

// Apenas profissionais logados
checkPermission(['medico', 'admin', 'secretaria']); // Expanded permissions just in case

$userId = (int) $_SESSION['user_id'];

// Buscar ID do profissional de forma segura (apenas da mesma empresa)
$company_id = getCompanyId();
$stmt = $conn->prepare("SELECT id FROM profissionais WHERE user_id = ? AND company_id = ?");
$stmt->bind_param("ii", $userId, $company_id);
$stmt->execute();
$profissional = $stmt->get_result()->fetch_assoc();

if (!$profissional) {
    // Se não for profissional, volta pro dashboard
    header('Location: ' . BASE_URL . 'dashboard');
    exit;
}

$profissional_id = $profissional['id'];

// Marcar como lida se solicitado
if (isset($_GET['marcar_lida']) && is_numeric($_GET['marcar_lida'])) {
    marcarNotificacaoLida((int) $_GET['marcar_lida'], $profissional_id);
    header('Location: ' . BASE_URL . 'minhas-notificacoes');
    exit;
}

// Marcar todas como lidas
if (isset($_GET['marcar_todas'])) {
    marcarTodasLidas($profissional_id);
    header('Location: ' . BASE_URL . 'minhas-notificacoes');
    exit;
}

// Buscar notificações
$filtro = $_GET['filtro'] ?? 'todas';
$notificacoes = listarNotificacoes($profissional_id, 50, $filtro === 'nao_lidas');
$naoLidas = contarNotificacoesNaoLidas($profissional_id);
?>

<div class="page-header">
    <h1><i class="bi bi-bell me-2"></i>Notificações</h1>
    <div class="d-flex gap-2">
        <a href="<?php echo BASE_URL; ?>profissionais/notificacoes" class="btn btn-outline-secondary">
            <i class="bi bi-gear me-2"></i>Configurações
        </a>
        <?php if ($naoLidas > 0): ?>
            <a href="?marcar_todas=1" class="btn btn-outline-primary">
                <i class="bi bi-check-all me-2"></i>Marcar Todas como Lidas
            </a>
        <?php endif; ?>
    </div>
</div>

<!-- Filtros -->
<div class="card mb-3">
    <div class="card-body">
        <div class="btn-group" role="group">
            <a href="?filtro=todas" class="btn btn-<?php echo $filtro === 'todas' ? 'primary' : 'outline-primary'; ?>">
                Todas
            </a>
            <a href="?filtro=nao_lidas"
                class="btn btn-<?php echo $filtro === 'nao_lidas' ? 'primary' : 'outline-primary'; ?>">
                Não Lidas <?php if ($naoLidas > 0)
                    echo "({$naoLidas})"; ?>
            </a>
        </div>
    </div>
</div>

<!-- Lista de Notificações -->
<div class="card">
    <div class="card-body">
        <?php if (empty($notificacoes)): ?>
            <div class="text-center py-5 text-muted">
                <i class="bi bi-bell-slash display-1"></i>
                <p class="mt-3">Nenhuma notificação encontrada</p>
            </div>
        <?php else: ?>
            <div class="list-group list-group-flush">
                <?php foreach ($notificacoes as $notif): ?>
                    <div class="list-group-item <?php echo !$notif['lida'] ? 'bg-light' : ''; ?>">
                        <div class="d-flex w-100 justify-content-between align-items-start">
                            <div class="flex-grow-1">
                                <div class="d-flex align-items-center mb-2">
                                    <i
                                        class="bi <?php echo $notif['icone']; ?> text-<?php echo $notif['cor']; ?> me-2 fs-4"></i>
                                    <h6 class="mb-0"><?php echo htmlspecialchars($notif['titulo']); ?></h6>
                                    <?php if (!$notif['lida']): ?>
                                        <span class="badge bg-danger ms-2">Nova</span>
                                    <?php endif; ?>
                                </div>
                                <p class="mb-2 text-muted" style="white-space: pre-line;">
                                    <?php echo htmlspecialchars($notif['mensagem']); ?>
                                </p>
                                <small class="text-muted">
                                    <i class="bi bi-clock me-1"></i>
                                    <?php
                                    $diff = time() - strtotime($notif['created_at']);
                                    if ($diff < 60)
                                        echo 'Agora mesmo';
                                    elseif ($diff < 3600)
                                        echo floor($diff / 60) . ' minutos atrás';
                                    elseif ($diff < 86400)
                                        echo floor($diff / 3600) . ' horas atrás';
                                    else
                                        echo date('d/m/Y H:i', strtotime($notif['created_at']));
                                    ?>
                                </small>
                            </div>
                            <div class="ms-3">
                                <?php if ($notif['link']): ?>
                                    <a href="<?php echo $notif['link']; ?>" class="btn btn-sm btn-outline-primary mb-2">
                                        <i class="bi bi-eye"></i> Ver
                                    </a>
                                <?php endif; ?>
                                <?php if (!$notif['lida']): ?>
                                    <a href="?marcar_lida=<?php echo $notif['id']; ?>" class="btn btn-sm btn-outline-success">
                                        <i class="bi bi-check"></i> Marcar Lida
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>