<?php
require_once __DIR__ . '/../../../../config/config.php';
require_once __DIR__ . '/../../../../config/database.php';
require_once __DIR__ . '/../../../../includes/auth.php';

header('Content-Type: application/json');

// Check permission
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'Acesso negado']);
    exit;
}

// Read Config from Session
if (!isset($_SESSION['import_batch'])) {
    echo json_encode(['success' => false, 'error' => 'Configuração de importação não encontrada.']);
    exit;
}

$config = $_SESSION['import_batch'];
$csvPath = $config['csv_path'];
$delimiter = $config['delimiter'];
$tipo = $config['tipo'];
$mapEmpresa = $config['map_empresa'];
$mapLead = $config['map_lead'];
$tenant_id = $_SESSION['company_id'] ?? 1;

// Parameters
$offset = isset($_GET['offset']) ? (int) $_GET['offset'] : 0;
$limit = isset($_GET['limit']) ? (int) $_GET['limit'] : 50;

if (!file_exists($csvPath)) {
    echo json_encode(['success' => false, 'error' => 'Arquivo CSV não encontrado.']);
    exit;
}

// Helper Function
function getVal($row, $map, $field)
{
    if (isset($map[$field]) && $map[$field] !== '' && isset($row[$map[$field]])) {
        return trim($row[$map[$field]]);
    }
    return null;
}

$stats = ['processed' => 0, 'inserted' => 0, 'errors' => 0];
$log = [];

try {
    $file = new SplFileObject($csvPath);
    $file->setFlags(SplFileObject::READ_CSV | SplFileObject::READ_AHEAD | SplFileObject::SKIP_EMPTY);
    $file->setCsvControl($delimiter);

    // Seek to offset (Line 0 is header, so offset 0 means start after header usually, 
    // but SplFileObject uses 0-indexed lines. 
    // If Step 1 skipped header, we treat offset relative to data rows? 
    // Easier: Offset is absolute line number.
    // Line 0 = Header. Start at Line 1.

    $startLine = $offset + 1; // Skip header (line 0)
    $file->seek($startLine);

    // Check if we are at EOF
    if ($file->eof()) {
        echo json_encode([
            'success' => true,
            'processed_count' => 0,
            'finished' => true,
            'logs' => $log
        ]);
        exit;
    }

    $count = 0;

    while (!$file->eof() && $count < $limit) {
        $row = $file->current();

        // Validation: Empty rows
        if (empty($row) || (count($row) === 1 && $row[0] === null)) {
            $file->next();
            continue;
        }

        $stats['processed']++;
        $currentLine = $startLine + $count + 1; // Human readable line number

        $conn->begin_transaction();
        try {
            $empresaIdParaLead = null;

            // --- 1. PROCESSAR EMPRESA ---
            if ($tipo === 'empresas' || $tipo === 'conjunto') {
                $nomeEmpresa = getVal($row, $mapEmpresa, 'nome_fantasia');

                if ($nomeEmpresa) {
                    $stmtCheck = $conn->prepare("SELECT id FROM empresas WHERE nome = ? LIMIT 1");
                    $stmtCheck->bind_param("s", $nomeEmpresa);
                    $stmtCheck->execute();
                    $resCheck = $stmtCheck->get_result();

                    if ($rowE = $resCheck->fetch_assoc()) {
                        $empresaIdParaLead = $rowE['id'];
                    } else {
                        $razao = getVal($row, $mapEmpresa, 'razao_social');
                        $cnpj = getVal($row, $mapEmpresa, 'cnpj');

                        $stmtInsE = $conn->prepare("INSERT INTO empresas (nome, documento, ativo, created_at) VALUES (?, ?, 1, NOW())");
                        $stmtInsE->bind_param("ss", $nomeEmpresa, $cnpj);

                        if ($stmtInsE->execute()) {
                            $empresaIdParaLead = $conn->insert_id;
                        } else {
                            throw new Exception("Erro ao criar empresa: " . $conn->error);
                        }
                    }
                } else {
                    if ($tipo === 'empresas')
                        throw new Exception("Nome da empresa não encontrado.");
                }
            }

            // --- 2. PROCESSAR LEAD ---
            if ($tipo === 'leads' || $tipo === 'conjunto') {
                $nomeLead = getVal($row, $mapLead, 'nome');

                if ($nomeLead) {
                    $targetCompanyId = ($tipo === 'conjunto' && $empresaIdParaLead) ? $empresaIdParaLead : $tenant_id;
                    $email = getVal($row, $mapLead, 'email');
                    $telefone = getVal($row, $mapLead, 'telefone');

                    // Email pode ser nulo na nova tabela leads
                    if (empty($email)) {
                        $email = null;
                    }

                    $exists = false;

                    // 1. Verificar por Email (se existir)
                    if ($email) {
                        $stmtCheck = $conn->prepare("SELECT id FROM leads WHERE email = ? AND company_id = ? LIMIT 1");
                        $stmtCheck->bind_param("si", $email, $targetCompanyId);
                        $stmtCheck->execute();
                        if ($stmtCheck->get_result()->num_rows > 0)
                            $exists = true;
                    }

                    // 2. Verificar por Telefone (se não achou por email e tiver telefone)
                    if (!$exists && $telefone) {
                        $stmtCheckT = $conn->prepare("SELECT id FROM leads WHERE telefone = ? AND company_id = ? LIMIT 1");
                        $stmtCheckT->bind_param("si", $telefone, $targetCompanyId);
                        $stmtCheckT->execute();
                        if ($stmtCheckT->get_result()->num_rows > 0)
                            $exists = true;
                    }

                    if (!$exists) {
                        $origem = getVal($row, $mapLead, 'origem') ?: 'Importação CSV';

                        $stmtIns = $conn->prepare("INSERT INTO leads (company_id, nome, email, telefone, origem, status, ativo, created_at) VALUES (?, ?, ?, ?, ?, 'novo', 1, NOW())");
                        // status default 'novo'
                        $stmtIns->bind_param("issss", $targetCompanyId, $nomeLead, $email, $telefone, $origem);

                        if (!$stmtIns->execute()) {
                            throw new Exception("Erro ao criar lead: " . $conn->error);
                        }
                    }
                }
            }

            $conn->commit();
            $stats['inserted']++;

        } catch (Exception $e) {
            $conn->rollback();
            $stats['errors']++;
            $log[] = "Linha $currentLine: " . $e->getMessage();
        }

        $file->next();
        $count++;
    }

    $finished = $file->eof();

    echo json_encode([
        'success' => true,
        'processed_count' => $stats['processed'],
        'inserted_count' => $stats['inserted'],
        'error_count' => $stats['errors'],
        'logs' => $log,
        'finished' => $finished
    ]);

} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>