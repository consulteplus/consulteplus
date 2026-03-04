<?php
require_once __DIR__ . '/../../../config/config.php';
require_once __DIR__ . '/../../../config/database.php';
require_once __DIR__ . '/../../../includes/auth.php';

checkAuth();

if (!isset($_GET['id'])) {
    header('Location: index.php');
    exit;
}

$id = (int) $_GET['id'];
$company_id = getCompanyId();

// Buscar audiência
$sql = "SELECT * FROM audiencias WHERE id = ? AND company_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $id, $company_id);
$stmt->execute();
$audiencia = $stmt->get_result()->fetch_assoc();

if (!$audiencia) {
    header('Location: index.php');
    exit;
}

// Buscar leads
if ($audiencia['tipo'] === 'dinamica') {
    // Buscar leads dinamicamente usando filtros
    require_once 'acoes';
    $leads = buscarLeadsPorFiltros($conn, $company_id, $audiencia['filtros_json']);
} else {
    // Buscar leads estáticos
    $sql = "SELECT l.* FROM leads l
            INNER JOIN audiencia_leads al ON l.id = al.lead_id
            WHERE al.audiencia_id = ?
            ORDER BY al.adicionado_em DESC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    $leads = [];
    while ($row = $result->fetch_assoc()) {
        $leads[] = $row;
    }
}

// Gerar CSV
$filename = 'audiencia_' . preg_replace('/[^a-z0-9]/i', '_', $audiencia['nome']) . '_' . date('Y-m-d') . '.csv';

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="' . $filename . '"');

// Criar output stream
$output = fopen('php://output', 'w');

// BOM para UTF-8
fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF));

// Cabeçalhos
fputcsv($output, ['ID', 'Nome', 'Email', 'Telefone', 'Origem', 'Status', 'Data de Criação'], ';');

// Dados
foreach ($leads as $lead) {
    fputcsv($output, [
        $lead['id'],
        $lead['nome'],
        $lead['email'] ?? '',
        $lead['telefone'] ?? '',
        $lead['origem'] ?? '',
        $lead['status'] ?? '',
        date('d/m/Y H:i', strtotime($lead['created_at']))
    ], ';');
}

fclose($output);
exit;
?>