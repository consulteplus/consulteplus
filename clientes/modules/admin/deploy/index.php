<?php
// 1. CARREGAR DEPENDÊNCIAS ESSENCIAIS
session_start();
require_once __DIR__ . '/../../../config/config.php';
require_once __DIR__ . '/../../../config/database.php';
require_once __DIR__ . '/../../../includes/auth.php'; // Adicionar auth

// Caminhos (Atualizados para estar em modules/admin/deploy)
$ftpConfigFile = __DIR__ . '/../../../config/ftp_deploy.json';
$migrationDir = __DIR__ . '/../../../database/migrations/';
$maintenanceFile = __DIR__ . '/../../../.maintenance';

// Lógica de Manutenção
if (isset($_POST['toggle_maintenance'])) {
    if (file_exists($maintenanceFile)) {
        unlink($maintenanceFile);
        $_SESSION['success'] = "Modo manutenção DESATIVADO. Sistema online.";
    } else {
        file_put_contents($maintenanceFile, "Em manutenção desde " . date('d/m/Y H:i'));
        $_SESSION['warning'] = "Modo manutenção ATIVADO. Usuários verão aviso de bloqueio.";
    }
    header("Location: index.php?tab=ftp");
    exit;
}

// Renderização HTML
$pageTitle = "Central de Deploy";
require_once __DIR__ . '/../../../includes/header.php';
checkPermission(['admin', 'superadmin']);

// --- LÓGICA DE DADOS (Restaurada) ---

// 1. Config FTP
$ftpConfig = file_exists($ftpConfigFile) ? json_decode(file_get_contents($ftpConfigFile), true) : [];

// 2. Migrations
$files = glob($migrationDir . '*.sql');
$migrations = [];
if ($files) {
    foreach ($files as $file) {
        $basename = basename($file);
        if (preg_match('/^v(\d+)_/', $basename, $matches)) {
            $version = (int)$matches[1];
            $migrations[$version] = [
                'filename' => $basename,
                'path' => $file,
                'version' => $version
            ];
        }
    }
    ksort($migrations);
}

// 3. Status DB
$executed = [];
$dbError = null;
try {
    $res = $conn->query("SELECT version, executed_at FROM sys_migrations");
    if ($res) {
        while ($row = $res->fetch_assoc()) {
            $executed[$row['version']] = $row['executed_at'];
        }
    } else {
        $dbError = "Tabela sys_migrations não encontrada.";
    }
} catch (Exception $e) {
    $dbError = $e->getMessage();
}

$pending = [];
$history = [];
foreach ($migrations as $v => $m) {
    if (!isset($executed[$v])) {
        $pending[$v] = $m;
    } else {
        $history[$v] = $m;
        $history[$v]['executed_at'] = $executed[$v];
    }
}
krsort($history);

$activeTab = isset($_GET['tab']) ? $_GET['tab'] : 'db';
$isMaintenance = file_exists($maintenanceFile);
?>

<div class="container-fluid py-4">
    
    <!-- MENSAGENS DE FEEDBACK (SESSÃO) -->
    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i><?= $_SESSION['success'] ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i><?= $_SESSION['error'] ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>
    
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800"><i class="bi bi-rocket-takeoff me-2"></i>Central de Deploy</h1>
            <p class="text-muted small mb-0">Gerencie a publicação de novas versões (Banco de Dados & Arquivos)</p>
        </div>
        
        <form method="post" class="d-inline">
            <input type="hidden" name="toggle_maintenance" value="1">
            <?php if ($isMaintenance): ?>
                <button type="submit" class="btn btn-warning shadow-sm animate__animated animate__pulse animate__infinite">
                    <i class="bi bi-cone-striped me-2"></i><strong>MODO MANUTENÇÃO ATIVO</strong> (Desativar)
                </button>
            <?php else: ?>
                <button type="submit" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-power me-2"></i>Ativar Modo Manutenção
                </button>
            <?php endif; ?>
        </form>
    </div>

    <!-- ABAS -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <ul class="nav nav-tabs card-header-tabs" id="deployTabs" role="tablist">
                <li class="nav-item">
                    <a class="nav-link <?= $activeTab == 'db' ? 'active' : '' ?>" id="db-tab" data-bs-toggle="tab" href="#db" role="tab">
                        <i class="bi bi-database me-2"></i>Banco de Dados
                        <?php if(!empty($pending)): ?>
                            <span class="badge bg-danger ms-2"><?= count($pending) ?></span>
                        <?php endif; ?>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $activeTab == 'ftp' ? 'active' : '' ?>" id="ftp-tab" data-bs-toggle="tab" href="#ftp" role="tab">
                        <i class="bi bi-folder me-2"></i>Arquivos & FTP
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="history-tab" data-bs-toggle="tab" href="#history" role="tab">
                        <i class="bi bi-clock-history me-2"></i>Histórico
                    </a>
                </li>
            </ul>
        </div>
        <div class="card-body">
            <div class="tab-content">
                
                <!-- ABA DB -->
                <div class="tab-pane fade <?= $activeTab == 'db' ? 'show active' : '' ?>" id="db" role="tabpanel">
                    <?php if (isset($dbError)): ?>
                        <div class="alert alert-warning">
                            <h4 class="alert-heading">Setup Necessário!</h4>
                            <p>Tabela de migrações ausente.</p>
                            <a href="../../database/setup_migrations.php" class="btn btn-warning">Setup Inicial</a>
                        </div>
                    <?php elseif (empty($pending)): ?>
                        <div class="text-center py-5">
                            <i class="bi bi-check-circle text-success fs-1"></i>
                            <h4 class="mt-3">Banco de Dados Sincronizado</h4>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-danger d-flex align-items-center">
                            <i class="bi bi-exclamation-triangle-fill fs-2 me-3"></i>
                            <strong>Atenção:</strong> Alterações pendentes detectadas.
                        </div>
                        <div class="card border-danger mb-4">
                            <div class="card-header bg-danger text-white">Pendências</div>
                            <ul class="list-group list-group-flush">
                                <?php foreach ($pending as $v => $m): ?>
                                    <li class="list-group-item d-flex justify-content-between">
                                        <span><span class="badge bg-secondary me-2">v<?= $v ?></span> <?= $m['filename'] ?></span>
                                        <button class="btn btn-sm btn-outline-dark" data-bs-toggle="collapse" data-bs-target="#codepreview<?= $v ?>">Ver SQL</button>
                                    </li>
                                    <div class="collapse" id="codepreview<?= $v ?>"><div class="card-body bg-light"><pre class="small mb-0"><?= htmlspecialchars(file_get_contents($m['path'])) ?></pre></div></div>
                                <?php endforeach; ?>
                            </ul>
                            <div class="card-footer">
                                <!-- Action aponta para execute_db.php -->
                                <form method="post" action="execute_db.php" onsubmit="return confirm('Confirmar atualização do banco? Essa ação não pode ser desfeita.');">
                                    <textarea name="pending_json" style="display:none;"><?= json_encode($pending) ?></textarea>
                                    <button type="submit" name="execute_migrations" class="btn btn-danger w-100 fw-bold">
                                        EXECUTAR ATUALIZAÇÃO AGORA
                                    </button>
                                </form>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- ABA FTP -->
                <div class="tab-pane fade <?= $activeTab == 'ftp' ? 'show active' : '' ?>" id="ftp" role="tabpanel">
                     <div class="row">
                        <div class="col-md-5 border-end">
                            <h5 class="mb-3">Configuração FTP</h5>
                            <!-- Action aponta para save_config.php -->
                            <form method="post" action="save_config.php">
                                <div class="mb-3"><label>Host</label><input type="text" name="ftp_host" class="form-control" value="<?= $ftpConfig['host'] ?? '' ?>" required></div>
                                <div class="row">
                                    <div class="col-md-8 mb-3"><label>User</label><input type="text" name="ftp_user" class="form-control" value="<?= $ftpConfig['user'] ?? '' ?>" required></div>
                                    <div class="col-md-4 mb-3"><label>Port</label><input type="number" name="ftp_port" class="form-control" value="<?= $ftpConfig['port'] ?? 21 ?>"></div>
                                </div>
                                <div class="mb-3"><label>Senha</label><input type="password" name="ftp_pass" class="form-control" placeholder="<?= !empty($ftpConfig['pass']) ? '******' : '' ?>"></div>
                                <div class="mb-3"><label>Remote Path</label><input type="text" name="ftp_path" class="form-control" value="<?= $ftpConfig['path'] ?? '/' ?>" required></div>
                                <div class="mb-3"><label>Excludes</label><textarea name="ftp_exclude" class="form-control small" rows="4"><?= $ftpConfig['exclude'] ?? '' ?></textarea></div>
                                <button type="submit" class="btn btn-primary w-100">Salvar Credenciais</button>
                            </form>
                        </div>
                        <div class="col-md-7 ps-md-4">
                             <h5 class="mb-3 text-success">Sincronização</h5>
                             <div class="card mb-3">
                                <div class="card-body">
                                    <strong>Destino:</strong> <code>ftp://<?= $ftpConfig['host'] ?? '...' ?><?= $ftpConfig['path'] ?? '' ?></code>
                                    <hr>
                                    <button onclick="iniciarDeploy()" class="btn btn-success w-100" <?= empty($ftpConfig['host']) ? 'disabled' : '' ?>>INICIAR UPLOAD AGORA</button>
                                </div>
                             </div>
                             <div id="deploy-terminal" class="bg-dark text-white p-3 rounded small" style="height:300px;overflow-y:auto;display:none;"><div id="deploy-log">Aguardando...</div></div>
                        </div>
                     </div>
                </div>

                <!-- ABA HISTORICO -->
                <div class="tab-pane fade" id="history" role="tabpanel">
                     <table class="table table-hover table-striped">
                        <thead><tr><th>Versão</th><th>Arquivo</th><th>Data</th><th>Status</th></tr></thead>
                        <tbody>
                            <?php foreach ($history as $v => $h): ?>
                                <tr><td>v<?= $v ?></td><td><?= $h['filename'] ?></td><td><?= date('d/m/Y H:i', strtotime($h['executed_at'])) ?></td><td><span class="badge bg-success">Sucesso</span></td></tr>
                            <?php endforeach; ?>
                        </tbody>
                     </table>
                </div>

            </div>
        </div>
    </div>
</div>

<script>
function iniciarDeploy() {
    if(!confirm("Iniciar upload para FTP?")) return;
    const term = document.getElementById('deploy-terminal');
    const log = document.getElementById('deploy-log');
    term.style.display='block';
    log.innerHTML='Conectando...<br>';
    const evt = new EventSource('ftp_worker.php');
    evt.onmessage = function(e) {
        const d = JSON.parse(e.data);
        if(d.type==='log') log.innerHTML+=`<div>${d.msg}</div>`;
        if(d.type==='error') { log.innerHTML+=`<div class="text-danger">${d.msg}</div>`; evt.close(); }
        if(d.type==='success') { log.innerHTML+=`<div class="text-success">${d.msg}</div>`; evt.close(); }
        term.scrollTop=term.scrollHeight;
    };
    evt.onerror = function() { log.innerHTML+='<div>Fim da conexão.</div>'; evt.close(); };
}
</script>

<?php require_once __DIR__ . '/../../../includes/footer.php'; ?>