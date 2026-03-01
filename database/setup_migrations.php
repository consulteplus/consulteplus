<?php
// Habilitar exibição de erros para debug
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Caminho para o arquivo de configuração
$configFile = __DIR__ . '/../config/database.php';

if (!file_exists($configFile)) {
    die("Erro: Arquivo de configuração não encontrado em: $configFile");
}

require_once $configFile;

// Conexão com o banco (supondo que o config/database.php defina as variáveis ou constantes)
// Vou tentar criar a conexão usando as variáveis comuns, se falhar, o usuário verá o erro.
// Ajuste conforme as variáveis reais do seu arquivo config/database.php
$dsn = "mysql:host=" . (defined('DB_HOST') ? DB_HOST : 'localhost') . ";dbname=" . (defined('DB_NAME') ? DB_NAME : 'cinco') . ";charset=utf8mb4";
$user = defined('DB_USER') ? DB_USER : 'root';
$pass = defined('DB_PASS') ? DB_PASS : '';

try {
    $pdo = new PDO($dsn, $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Erro de Conexão: " . $e->getMessage() . "<br>Verifique s variaveis no arquivo setup_migrations.php");
}

// 1. Criar Tabela de Controle se não existir
$msgTabela = "";
try {
    $sql = "CREATE TABLE IF NOT EXISTS sys_migrations (
        id INT AUTO_INCREMENT PRIMARY KEY,
        version VARCHAR(50) NOT NULL UNIQUE,
        filename VARCHAR(255) NOT NULL,
        executed_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        status ENUM('success', 'error') DEFAULT 'success',
        log TEXT
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

    $pdo->exec($sql);
    $msgTabela = "<div class='alert alert-success'>Tabela <strong>sys_migrations</strong> verificada/criada com sucesso.</div>";
} catch (Exception $e) {
    $msgTabela = "<div class='alert alert-danger'>Erro ao criar tabela: " . $e->getMessage() . "</div>";
}

// 2. Processar Ação (Marcar como Lido)
if (isset($_POST['action']) && $_POST['action'] == 'mark_as_done') {
    $filename = $_POST['filename'];
    $version = $_POST['version'];

    $stmt = $pdo->prepare("INSERT IGNORE INTO sys_migrations (version, filename, status, log) VALUES (?, ?, 'success', 'Marcado manualmente como executado no setup inicial')");
    $stmt->execute([$version, $filename]);
    $msgTabela .= "<div class='alert alert-info'>Arquivo <strong>$filename</strong> marcado como executado.</div>";
}

// 3. Ler arquivos da pasta
$migrationDir = __DIR__ . '/migrations/';
$files = glob($migrationDir . '*.sql');
$migrations = [];

foreach ($files as $file) {
    $basename = basename($file);
    // Extrair versão (ex: v3_nome.sql -> 3)
    if (preg_match('/^v(\d+)_/', $basename, $matches)) {
        $version = (int) $matches[1];
        $migrations[$version] = [
            'filename' => $basename,
            'path' => $file,
            'version' => $version
        ];
    }
}

// Ordenar por versão (Natural Sort)
ksort($migrations);

// 4. Verificar Status no Banco
$stmt = $pdo->query("SELECT version, executed_at FROM sys_migrations");
$executed = [];
while ($row = $stmt->fetch()) {
    $executed[$row['version']] = $row['executed_at'];
}

?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Setup de Migrations - Cinco</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        body {
            background-color: #f8f9fa;
        }

        .card-migration {
            transition: all 0.2s;
        }

        .card-migration:hover {
            transform: translateY(-2px);
            shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>

<body class="p-4">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1><i class="bi bi-database-gear me-2"></i>Setup de Migrations (Deploy DB)</h1>
            <a href="../../" class="btn btn-outline-secondary">Voltar ao Sistema</a>
        </div>

        <?= $msgTabela ?>

        <div class="card mb-4">
            <div class="card-header bg-dark text-white">Status Atual dos Arquivos</div>
            <div class="card-body p-0">
                <table class="table table-hover table-striped mb-0">
                    <thead>
                        <tr>
                            <th>Versão</th>
                            <th>Arquivo</th>
                            <th>Status no Banco</th>
                            <th>Ação</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($migrations as $v => $m): ?>
                            <?php
                            $isExecuted = isset($executed[$v]);
                            $statusClass = $isExecuted ? 'text-success' : 'text-warning';
                            $statusIcon = $isExecuted ? 'bi-check-circle-fill' : 'bi-clock-history';
                            $statusText = $isExecuted ? 'Executado em ' . date('d/m/Y H:i', strtotime($executed[$v])) : 'Pendente (Novo)';
                            ?>
                            <tr>
                                <td><span class="badge bg-secondary">v<?= $v ?></span></td>
                                <td class="font-monospace"><?= $m['filename'] ?></td>
                                <td class="<?= $statusClass ?>">
                                    <i class="bi <?= $statusIcon ?> me-1"></i> <?= $statusText ?>
                                </td>
                                <td>
                                    <?php if (!$isExecuted): ?>
                                        <form method="post" class="d-inline">
                                            <input type="hidden" name="action" value="mark_as_done">
                                            <input type="hidden" name="filename" value="<?= $m['filename'] ?>">
                                            <input type="hidden" name="version" value="<?= $v ?>">
                                            <button type="submit" class="btn btn-sm btn-outline-success"
                                                title="Marcar como Feito (Não executa o SQL, apenas grava no histórico)">
                                                <i class="bi bi-check-lg"></i> Já Existe
                                            </button>
                                        </form>
                                    <?php else: ?>
                                        <button class="btn btn-sm btn-light" disabled>OK</button>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>

                        <?php if (empty($migrations)): ?>
                            <tr>
                                <td colspan="4" class="text-center py-4 text-muted">Nenhum arquivo de migração encontrado em
                                    <code>database/migrations/</code></td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="alert alert-warning">
            <h5><i class="bi bi-exclamation-triangle"></i> Atenção</h5>
            <p class="mb-0">Use o botão <strong>"Já Existe"</strong> apenas para arquivos que você sabe que já foram
                rodados no banco manualmente. Isso evita que o sistema tente criar tabelas duplicadas no futuro.</p>
        </div>
    </div>
</body>

</html>