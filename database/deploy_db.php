<?php
session_start();

// REGRA DE SEGURANÇA: Apenas Admin logado ou chave secreta na URL
// Ajuste a verificação de sessão conforme a lógica do seu sistema (ex: $_SESSION['nivel'] == 'admin')
$is_admin = isset($_SESSION['user_id']) || isset($_SESSION['usuario_id']);

if (!$is_admin && !isset($_GET['key'])) {
    die('<div style="font-family:sans-serif; padding:20px; color:red; border:1px solid red; background:#fff0f0; border-radius:5px; max-width:600px; margin:50px auto;">
            <strong>ACESSO NEGADO:</strong> Você precisa estar logado como Administrador para realizar deploy de banco de dados.
            <br><br><a href="../">Voltar ao Login</a>
         </div>');
}

// Habilitar erros para debug durante deploy
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../config/database.php';

// Conexão
$dsn = "mysql:host=" . (defined('DB_HOST') ? DB_HOST : 'localhost') . ";dbname=" . (defined('DB_NAME') ? DB_NAME : 'cinco') . ";charset=utf8mb4";
$user = defined('DB_USER') ? DB_USER : 'root';
$pass = defined('DB_PASS') ? DB_PASS : '';

try {
    $pdo = new PDO($dsn, $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erro de Conexão: " . $e->getMessage());
}

// === LÓGICA DO DEPLOY ===

// 1. Ler Arquivos
$migrationDir = __DIR__ . '/migrations/';
$files = glob($migrationDir . '*.sql');
$migrations = [];

foreach ($files as $file) {
    $basename = basename($file);
    if (preg_match('/^v(\d+)_/', $basename, $matches)) {
        $version = (int) $matches[1];
        $migrations[$version] = [
            'filename' => $basename,
            'path' => $file,
            'version' => $version
        ];
    }
}
ksort($migrations);

// 2. Ler Estado Atual
try {
    $stmt = $pdo->query("SELECT version FROM sys_migrations");
    $executed = $stmt->fetchAll(PDO::FETCH_COLUMN);
} catch (PDOException $e) {
    // Se a tabela não existe, manda rodar o setup
    die("A tabela de controle do deploy não foi encontrada. <a href='setup_migrations.php'>Clique aqui para rodar o Setup Inicial</a>.");
}

// 3. Filtrar Pendentes (Apenas versões MAIORES que a última executada ou que faltam)
// Diferente do setup, aqui focamos no "GAP". Se v3 rodou e v11 existe, v11 é pendente.
$pending = [];
foreach ($migrations as $v => $m) {
    if (!in_array($v, $executed)) {
        $pending[$v] = $m;
    }
}

// 4. Executar (Se POST)
$msg = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['execute'])) {
    if (empty($pending)) {
        $msg = "<div class='alert alert-info'>Nada a executar.</div>";
    } else {
        $successCount = 0;
        $error = null;

        foreach ($pending as $v => $m) {
            $sqlContent = file_get_contents($m['path']);

            try {
                // Iniciar transação se possível (alguns DDL não suportam, mas ajuda)
                // $pdo->beginTransaction(); 

                // Executa Múltiplas Queries
                $pdo->exec($sqlContent);

                // Grava log
                $stmt = $pdo->prepare("INSERT INTO sys_migrations (version, filename, status, log) VALUES (?, ?, 'success', 'Deploy via Web')");
                $stmt->execute([$v, $m['filename']]);

                $successCount++;

            } catch (PDOException $e) {
                $error = "Erro no arquivo <strong>{$m['filename']}</strong>: <br>" . $e->getMessage();
                break; // PARA TUDO SE DER ERRO
            }
        }

        if ($error) {
            $msg = "<div class='alert alert-danger'>$error</div>";
        } else {
            $msg = "<div class='alert alert-success'>Sucesso! <strong>$successCount</strong> migrações foram aplicadas.</div>";
            // Recarregar lista
            $stmt = $pdo->query("SELECT version FROM sys_migrations");
            $executed = $stmt->fetchAll(PDO::FETCH_COLUMN);
            $pending = []; // Limpa pendentes visualmente
        }
    }
}

?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Deploy de Banco de Dados</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        body {
            background-color: #f0f2f5;
        }

        .deploy-card {
            border-left: 5px solid #0d6efd;
        }

        .file-content {
            max-height: 200px;
            overflow-y: auto;
            background: #2d2d2d;
            color: #f8f8f2;
            padding: 10px;
            font-size: 0.85rem;
            border-radius: 4px;
        }
    </style>
</head>

<body class="p-4">
    <div class="container" style="max-width: 900px;">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="mb-0"><i class="bi bi-rocket-takeoff-fill text-primary me-2"></i>Deploy DB</h2>
                <small class="text-muted">Ambiente: <?= $_SERVER['HTTP_HOST'] ?></small>
            </div>
            <a href="../../" class="btn btn-outline-secondary">Voltar ao Admin</a>
        </div>

        <?= $msg ?>

        <?php if (empty($pending)): ?>
            <div class="card shadow-sm text-center py-5">
                <div class="card-body">
                    <i class="bi bi-check-circle-fill text-success" style="font-size: 3rem;"></i>
                    <h3 class="mt-3 text-success">Tudo Atualizado!</h3>
                    <p class="text-muted">Seu banco de dados está sincronizado com a última versão dos arquivos.</p>
                    <a href="setup_migrations.php" class="btn btn-sm btn-link">Ver Histórico Completo</a>
                </div>
            </div>
        <?php else: ?>
            <div class="card shadow border-danger mb-4">
                <div class="card-header bg-danger text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="bi bi-exclamation-circle me-2"></i>Alterações Pendentes</h5>
                    <span class="badge bg-white text-danger"><?= count($pending) ?> arquivos</span>
                </div>
                <div class="card-body">
                    <p>As seguintes alterações serão aplicadas <strong>nesta ordem</strong>:</p>

                    <div class="list-group mb-4">
                        <?php foreach ($pending as $v => $m): ?>
                            <div class="list-group-item">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <h6 class="mb-0 fw-bold"><span class="badge bg-secondary me-2">v<?= $v ?></span>
                                        <?= $m['filename'] ?></h6>
                                    <button class="btn btn-sm btn-outline-dark" type="button" data-bs-toggle="collapse"
                                        data-bs-target="#code-<?= $v ?>">Ver SQL</button>
                                </div>
                                <div class="collapse" id="code-<?= $v ?>">
                                    <div class="file-content">
                                        <pre class="m-0"><?= htmlspecialchars(file_get_contents($m['path'])) ?></pre>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <div class="alert alert-warning">
                        <i class="bi bi-exclamation-triangle-fill"></i> <strong>Atenção:</strong> Esta ação é irreversível.
                        Certifique-se de ter um backup se estiver em produção.
                    </div>

                    <form method="post">
                        <input type="hidden" name="execute" value="1">
                        <button type="submit" class="btn btn-danger btn-lg w-100 py-3">
                            <i class="bi bi-play-circle-fill me-2"></i>EXECUTAR ATUALIZAÇÃO AGORA
                        </button>
                    </form>
                </div>
            </div>
        <?php endif; ?>

        <div class="text-center mt-5 text-muted small">
            Cinco DB Deployer v1.0 • <a href="setup_migrations.php" class="text-decoration-none">Gerenciar Histórico
                Manualmente</a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>