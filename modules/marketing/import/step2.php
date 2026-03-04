<?php
$pageTitle = "Mapeamento de Colunas";
require_once __DIR__ . '/../../../config/config.php';
require_once __DIR__ . '/../../../includes/header.php';

// Check permissions
checkPermission(['admin', 'superadmin']);

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_FILES['csv_file'])) {
    header("Location: index.php");
    exit;
}

$tipo = $_POST['tipo_importacao'] ?? 'leads';
$arquivo = $_FILES['csv_file']['tmp_name'];

// 1. Ler cabeçalho do CSV
$handle = fopen($arquivo, "r");
if ($handle === FALSE)
    die("Erro ao ler arquivo");

// Detectar delimitador (simples)
$line1 = fgets($handle);
$delimiter = (substr_count($line1, ';') > substr_count($line1, ',')) ? ';' : ',';
rewind($handle);

$headers = fgetcsv($handle, 0, $delimiter);
fclose($handle);

// Salvar arquivo temporário para o próximo passo
$tempPath = __DIR__ . '/temp_' . uniqid() . '.csv';
move_uploaded_file($arquivo, $tempPath);

// Definição de Campos do Banco (De -> Para)
$campos_leads = [
    'nome' => ['label' => 'Nome Completo (Obrigatório)', 'required' => true],
    'email' => ['label' => 'Email', 'required' => false],
    'telefone' => ['label' => 'Telefone/WhatsApp', 'required' => false],
    'origem' => ['label' => 'Origem (Ex: Google, Indicação)', 'required' => false],
];

$campos_empresas = [
    'nome_fantasia' => ['label' => 'Nome da Empresa/Fantasia (Obrigatório)', 'required' => true],
    'razao_social' => ['label' => 'Razão Social', 'required' => false],
    'cnpj' => ['label' => 'CNPJ/Documento', 'required' => false],
    'responsavel' => ['label' => 'Nome do Responsável', 'required' => false],
];

?>

<div class="container-fluid py-4">
    <div class="d-flex align-items-center mb-4">
        <a href="index.php" class="btn btn-sm btn-light rounded-circle border shadow-sm me-3">
            <i class="bi bi-arrow-left"></i>
        </a>
        <div>
            <h4 class="fw-bold mb-1">Mapeamento de Colunas</h4>
            <p class="text-muted small mb-0">Passo 2: Combine as colunas do seu arquivo com o sistema</p>
        </div>
    </div>

    <form action="import_progress.php" method="POST">
        <input type="hidden" name="csv_path" value="<?php echo htmlspecialchars($tempPath); ?>">
        <input type="hidden" name="delimiter" value="<?php echo $delimiter; ?>">
        <input type="hidden" name="tipo_importacao" value="<?php echo $tipo; ?>">

        <div class="row g-4">

            <?php if ($tipo === 'empresas' || $tipo === 'conjunto'): ?>
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-header bg-white py-3">
                            <h6 class="fw-bold mb-0 text-primary"><i class="bi bi-building me-2"></i>Dados da Empresa</h6>
                        </div>
                        <div class="card-body">
                            <?php foreach ($campos_empresas as $field => $info): ?>
                                <div class="mb-3">
                                    <label class="form-label small fw-bold text-muted">
                                        <?php echo $info['label']; ?>
                                    </label>
                                    <select class="form-select" name="map_empresa[<?php echo $field; ?>]">
                                        <option value="">-- Ignorar --</option>
                                        <?php foreach ($headers as $idx => $h): ?>
                                            <option value="<?php echo $idx; ?>" <?php // Auto-match simples
                                                           similar_text(strtolower($h), $field, $percent);
                                                           if (stripos(strtolower($h), explode(' ', $info['label'])[0]) !== false)
                                                               echo 'selected';
                                                           ?>>
                                                <?php echo htmlspecialchars($h); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <?php if ($tipo === 'leads' || $tipo === 'conjunto'): ?>
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-header bg-white py-3">
                            <h6 class="fw-bold mb-0 text-success"><i class="bi bi-person me-2"></i>Dados do Lead (Contato)
                            </h6>
                        </div>
                        <div class="card-body">
                            <?php foreach ($campos_leads as $field => $info): ?>
                                <div class="mb-3">
                                    <label class="form-label small fw-bold text-muted">
                                        <?php echo $info['label']; ?>
                                    </label>
                                    <select class="form-select" name="map_lead[<?php echo $field; ?>]">
                                        <option value="">-- Ignorar --</option>
                                        <?php foreach ($headers as $idx => $h): ?>
                                            <option value="<?php echo $idx; ?>" <?php // Auto-match
                                                           if (stripos(strtolower($h), $field) !== false)
                                                               echo 'selected';
                                                           ?>>
                                                <?php echo htmlspecialchars($h); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

        </div>

        <div class="d-flex justify-content-end mt-4">
            <button type="submit" class="btn btn-success btn-lg rounded-pill shadow px-5 fw-bold">
                <i class="bi bi-check-lg me-2"></i> Confirmar e Processar
            </button>
        </div>

    </form>
</div>

<?php require_once __DIR__ . '/../../../includes/footer.php'; ?>