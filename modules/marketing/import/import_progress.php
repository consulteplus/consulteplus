<?php
require_once __DIR__ . '/../../../config/config.php';
require_once __DIR__ . '/../../../includes/auth.php';

// Check permissions
checkPermission(['admin', 'cliente']);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['csv_path'])) {
    // Setup Session for Batch Processing
    $_SESSION['import_batch'] = [
        'csv_path' => $_POST['csv_path'],
        'delimiter' => $_POST['delimiter'],
        'tipo' => $_POST['tipo_importacao'],
        'map_empresa' => $_POST['map_empresa'] ?? [],
        'map_lead' => $_POST['map_lead'] ?? []
    ];
} else if (!isset($_SESSION['import_batch'])) {
    die("Acesso inválido ou sessão expirada.");
}

$csvPath = $_SESSION['import_batch']['csv_path'];
$pageTitle = "Importando...";

// Count Total Lines for Progress Bar
$totalLines = 0;
if (file_exists($csvPath)) {
    $fp = fopen($csvPath, 'r');
    while (!feof($fp)) {
        if (fgets($fp))
            $totalLines++;
    }
    fclose($fp);
    $totalLines--; // Subtract header
    if ($totalLines < 0)
        $totalLines = 0;
}

require_once __DIR__ . '/../../../includes/header.php';
?>

<div class="container py-5">
    <div class="card border-0 shadow-sm mx-auto" style="max-width: 800px;">
        <div class="card-body p-5">
            <h3 class="fw-bold mb-4">Importando Dados...</h3>

            <p class="text-muted mb-2">Processando arquivo CSV. Por favor, não feche esta página.</p>

            <!-- Progress Bar -->
            <div class="progress mb-4" style="height: 25px;">
                <div id="progressBar" class="progress-bar progress-bar-striped progress-bar-animated bg-primary"
                    role="progressbar" style="width: 0%">0%</div>
            </div>

            <!-- Stats Grid -->
            <div class="row text-center g-3 mb-4">
                <div class="col-4 border-end">
                    <h2 class="fw-bold mb-0" id="statProcessed">0</h2>
                    <small class="text-muted">Processados</small>
                </div>
                <div class="col-4 border-end">
                    <h2 class="fw-bold text-success mb-0" id="statInserted">0</h2>
                    <small class="text-muted">Sucessos</small>
                </div>
                <div class="col-4">
                    <h2 class="fw-bold text-danger mb-0" id="statErrors">0</h2>
                    <small class="text-muted">Erros</small>
                </div>
            </div>

            <!-- Console Log -->
            <div class="card bg-light border-0">
                <div class="card-header bg-transparent fw-bold small text-uppercase text-muted">Log de Processamento
                </div>
                <div class="card-body p-2"
                    style="height: 200px; overflow-y: auto; font-family: monospace; font-size: 0.85rem;"
                    id="consoleLog">
                    <div class="text-muted">Iniciando importação...</div>
                </div>
            </div>

            <!-- Completion Actions (Hidden initially) -->
            <div id="completionArea" class="mt-4 text-center d-none">
                <div class="alert alert-success d-flex align-items-center justify-content-center gap-2">
                    <i class="bi bi-check-circle-fill fs-4"></i>
                    <div>Importação concluída com sucesso!</div>
                </div>
                <div class="d-flex justify-content-center gap-2 mt-3">
                    <a href="../leads/index.php" class="btn btn-primary px-4 rounded-pill">Ir para Leads</a>
                    <a href="index.php" class="btn btn-outline-secondary px-4 rounded-pill">Nova Importação</a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    const totalLines = <?php echo $totalLines; ?>;
    let textLog = document.getElementById('consoleLog');
    let offset = 0;
    const limit = 50;

    // Stats
    let totalProcessed = 0;
    let totalInserted = 0;
    let totalErrors = 0;

    function log(msg, type = 'text-muted') {
        const div = document.createElement('div');
        div.className = type + ' border-bottom border-light py-1';
        div.innerText = msg;
        textLog.appendChild(div);
        textLog.scrollTop = textLog.scrollHeight;
    }

    function updateUI() {
        let pct = 0;
        if (totalLines > 0) {
            pct = Math.round((totalProcessed / totalLines) * 100);
        }
        if (pct > 100) pct = 100;

        const bar = document.getElementById('progressBar');
        bar.style.width = pct + '%';
        bar.innerText = pct + '%';

        document.getElementById('statProcessed').innerText = totalProcessed;
        document.getElementById('statInserted').innerText = totalInserted;
        document.getElementById('statErrors').innerText = totalErrors;
    }

    function processBatch() {
        fetch(`api_process.php?offset=${totalProcessed}&limit=${limit}`)
            .then(res => res.json())
            .then(data => {
                if (!data.success) {
                    log('Erro Fatal: ' + data.error, 'text-danger fw-bold');
                    return;
                }

                // Update Stats
                totalProcessed += data.processed_count;
                totalInserted += data.inserted_count;
                totalErrors += data.error_count;

                // Logs
                if (data.logs && data.logs.length > 0) {
                    data.logs.forEach(l => log(l, 'text-danger'));
                }

                updateUI();

                if (!data.finished) {
                    // Continue
                    processBatch();
                } else {
                    // Finished
                    finishImport();
                }
            })
            .catch(err => {
                log('Erro de Rede: ' + err, 'text-danger fw-bold');
            });
    }

    function finishImport() {
        document.getElementById('progressBar').classList.remove('progress-bar-animated');
        document.getElementById('progressBar').classList.add('bg-success');
        document.getElementById('progressBar').innerText = 'Concluído';

        document.getElementById('completionArea').classList.remove('d-none');
        log('Processamento finalizado.', 'text-success fw-bold');

        // Cleanup temp file? The API doesn't delete it yet because multiple calls.
        // We could have a cleanup call or just leave it for system clean.
        // Actually, let's just leave it for now, or add a cleanup param to the last call.
    }

    // Start
    document.addEventListener('DOMContentLoaded', () => {
        if (totalLines === 0) {
            log('Arquivo vazio ou erro ao ler linhas.', 'text-danger');
            finishImport();
        } else {
            console.log('Total Lines:', totalLines);
            processBatch();
        }
    });
</script>

<?php require_once __DIR__ . '/../../../includes/footer.php'; ?>