<?php
require_once __DIR__ . '/../../../config/config.php';
require_once __DIR__ . '/../../../config/database.php';

header('Content-Type: application/json');
session_start();

// Verifica login
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Não autorizado']);
    exit;
}

// Verifica método
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Método inválido']);
    exit;
}

// Recebe JSON
$input = json_decode(file_get_contents('php://input'), true);
$historicoId = isset($input['historico_id']) ? intval($input['historico_id']) : 0;
$analise = isset($input['analise']) ? trim($input['analise']) : '';

if ($historicoId <= 0 || empty($analise)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Dados inválidos']);
    exit;
}

// Verifica se o histórico pertence ao usuário logado (segurança) e busca info
$userId = $_SESSION['user_id'];
$company_id = (int) ($_SESSION['company_id'] ?? 0);
$check = $conn->prepare("SELECT id, modelo_id FROM gestao_diagnostico_resultados WHERE id = ? AND user_id = ? AND company_id = ?");
$check->bind_param("iii", $historicoId, $userId, $company_id);
$check->execute();
$resCheck = $check->get_result();
if ($resCheck->num_rows === 0) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Histórico não encontrado ou acesso negado']);
    exit;
}
$historicoData = $resCheck->fetch_assoc();
$modeloId = $historicoData['modelo_id'];

// Atualiza o banco
$scores = $input['scores'] ?? [];
$op = intval($scores['operacional'] ?? $scores['operacao'] ?? 0);
$fin = intval($scores['financeiro'] ?? 0);
$aq = intval($scores['aquisicao'] ?? $scores['vendas'] ?? 0);
$jornada = intval($scores['jornada'] ?? $scores['experiencia'] ?? 0);
$eq = intval($scores['equipe'] ?? 0);

// Projetos (JSON)
$projetosJson = isset($input['projetos']) ? json_encode($input['projetos'], JSON_UNESCAPED_UNICODE) : null;

// Calcula Média Geral (5 Pilares)
$mediaGeral = round(($op + $fin + $aq + $jornada + $eq) / 5);

// Define Nível de Maturidade
$nivelMaturidade = 'Em Análise';
if ($mediaGeral > 0) {
    if ($mediaGeral < 40)
        $nivelMaturidade = 'Sobrevivência';
    elseif ($mediaGeral < 70)
        $nivelMaturidade = 'Em Crescimento';
    else
        $nivelMaturidade = 'Alta Performance';
}

$stmt = $conn->prepare("UPDATE gestao_diagnostico_resultados SET analise_ia = ?, score_operacao = ?, score_financeiro = ?, score_aquisicao = ?, score_equipe = ?, score_jornada = ?, score_geral = ?, sugestao_projetos = ?, nivel_maturidade = ? WHERE id = ?");
$stmt->bind_param("siiiiiissi", $analise, $op, $fin, $aq, $eq, $jornada, $mediaGeral, $projetosJson, $nivelMaturidade, $historicoId);

if ($stmt->execute()) {

    // Lógica de Recorrência: Atualizar data da próxima execução
    $stmtAttrib = $conn->prepare("SELECT id, frequencia FROM recursos_atribuicoes WHERE empresa_id = ? AND recurso_id = ? AND recurso_tipo = 'diagnostico' AND ativo = 1");
    $stmtAttrib->bind_param("ii", $company_id, $modeloId);
    $stmtAttrib->execute();
    $resAttrib = $stmtAttrib->get_result();

    if ($resAttrib->num_rows > 0) {
        $attrib = $resAttrib->fetch_assoc();
        $atribuicaoId = $attrib['id'];
        $frequencia = $attrib['frequencia'];

        $novaData = null;
        $ativo = 1;

        if ($frequencia === 'unica') {
            $ativo = 0; // Desativa após uso único
        } else {
            // Calcula próxima data baseada em hoje
            $intervalos = [
                'diaria' => '+1 day',
                'semanal' => '+1 week',
                'mensal' => '+1 month',
                'trimestral' => '+3 months',
                'semestral' => '+6 months',
                'anual' => '+1 year'
            ];

            if (isset($intervalos[$frequencia])) {
                $novaData = date('Y-m-d', strtotime($intervalos[$frequencia]));
            }
        }

        // Atualiza a atribuição
        if ($novaData || $ativo === 0) {
            $sqlUpdateAttrib = "UPDATE recursos_atribuicoes SET ";
            $types = "";
            $params = [];

            if ($ativo === 0) {
                $sqlUpdateAttrib .= "ativo = 0 ";
            } else {
                $sqlUpdateAttrib .= "proxima_data = ? ";
                $types .= "s";
                $params[] = $novaData;
            }

            $sqlUpdateAttrib .= "WHERE id = ?";
            $types .= "i";
            $params[] = $atribuicaoId;

            $stmtUpdate = $conn->prepare($sqlUpdateAttrib);
            $stmtUpdate->bind_param($types, ...$params);
            $stmtUpdate->execute();
        }
    }

    echo json_encode(['success' => true]);
} else {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Erro ao salvar no banco']);
}
