<?php
session_start();
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/auth.php';

header('Content-Type: application/json');

// Ignorar warnings para manter JSON limpo
ini_set('display_errors', 0);
error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);

// DEBUG EXTREMO
file_put_contents(__DIR__ . '/debug_request.log', date('Y-m-d H:i:s') . " - Request: " . print_r($_REQUEST, true) . " Payload: " . file_get_contents('php://input') . "\n", FILE_APPEND);

// Verificar sessão
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Não autorizado']);
    exit;
}

// Função auxiliar caso não exista
if (!function_exists('getCurrentUser')) {
    function getCurrentUser()
    {
        return [
            'id' => $_SESSION['user_id'],
            'company_id' => $_SESSION['company_id'] ?? 0
        ];
    }
}

// Tentar obter usuário
$user = isset($_SESSION['user_id']) ?
    ['id' => $_SESSION['user_id'], 'company_id' => $_SESSION['company_id'] ?? 0] :
    getCurrentUser();

$company_id = $user['company_id'];

// Ler Input JSON
$json = file_get_contents('php://input');
$data = json_decode($json, true);
$acao = isset($data['acao']) ? $data['acao'] : (isset($_GET['acao']) ? $_GET['acao'] : '');

try {

    // 1. Verificar Status do Onboarding (GET)
    if ($acao === 'check_status') {

        // Verificar primeiro na empresa (flag rápida)
        $stmt = $conn->prepare("SELECT onboarding_done FROM empresas WHERE id = ?");

        if (!$stmt) {
            // Fallback para evitar erro se migration nao rodou (assumir false - não feito, mas cuidado com erro visual)
            // throw new Exception("Tabela 'empresas' ou coluna 'onboarding_done' não encontrada. (Migração pendente?) Info: " . $conn->error);
            // Melhor retornar falso silenciado para não travar console do usuário?
            // Não, se travar ele sabe que precisa atualizar.
            throw new Exception("Erro de Banco de Dados. Execute a migração v29. (" . $conn->error . ")");
        }

        $stmt->bind_param("i", $company_id);
        $stmt->execute();
        $res = $stmt->get_result();
        $row = $res->fetch_assoc();

        $done = (bool) ($row['onboarding_done'] ?? 0);

        // Double check na tabela de respostas se a flag estiver false (vai que...)
        /*
        if (!$done) {
            $stmt2 = $conn->prepare("SELECT id FROM empresa_onboarding WHERE company_id = ? LIMIT 1");
            $stmt2->bind_param("i", $company_id);
            $stmt2->execute();
            if ($stmt2->get_result()->num_rows > 0) {
                // Inconsistência: tem resposta mas flag tá 0. Atualizar.
                $conn->query("UPDATE empresas SET onboarding_done = 1 WHERE id = $company_id");
                $done = true;
            }
        }
        */

        echo json_encode(['success' => true, 'onboarding_done' => $done]);

        // 2. Salvar Respostas (POST)
    } elseif ($acao === 'salvar_respostas') {

        $tempo = $data['tempo_existencia'] ?? '';
        $gestao = $data['metodo_gestao_anterior'] ?? '';
        $sistema = $data['nome_sistema_anterior'] ?? '';
        $equipe = $data['tamanho_equipe'] ?? '';
        $segmento = isset($data['segmento']) ? json_encode($data['segmento']) : '[]';

        if (empty($tempo) || empty($gestao) || empty($equipe)) {
            throw new Exception("Preencha todos os campos obrigatórios.");
        }

        $conn->begin_transaction();

        // Inserir respostas
        $stmt = $conn->prepare("
            INSERT INTO empresa_onboarding 
            (company_id, tempo_existencia, metodo_gestao_anterior, nome_sistema_anterior, tamanho_equipe, segmento, created_at) 
            VALUES (?, ?, ?, ?, ?, ?, NOW())
        ");

        if (!$stmt) {
            throw new Exception("Erro preparando INSERT. Tabela 'empresa_onboarding' existe? (" . $conn->error . ")");
        }

        $stmt->bind_param("isssss", $company_id, $tempo, $gestao, $sistema, $equipe, $segmento);

        if (!$stmt->execute()) {
            throw new Exception("Erro ao salvar respostas: " . $conn->error);
        }

        // Atualizar flag na empresa
        $stmtUpdate = $conn->prepare("UPDATE empresas SET onboarding_done = 1 WHERE id = ?");
        $stmtUpdate->bind_param("i", $company_id);

        if (!$stmtUpdate->execute()) {
            throw new Exception("Erro ao atualizar status da empresa.");
        }

        $conn->commit();
        echo json_encode(['success' => true]);

    } else {
    }
} catch (Throwable $e) {
    // Log detalhado no arquivo que sabemos que funciona
    $logMsg = "\n=== ERROR CATCH ===\n";
    $logMsg .= "Time: " . date('Y-m-d H:i:s') . "\n";
    $logMsg .= "Message: " . $e->getMessage() . "\n";
    $logMsg .= "Session Dump: " . print_r($_SESSION, true) . "\n";
    $logMsg .= "Trace: " . $e->getTraceAsString() . "\n";
    file_put_contents(__DIR__ . '/debug_request.log', $logMsg, FILE_APPEND);

    if (isset($conn) && $conn->errno)
        $conn->rollback();

    // Retornar 200 com success:false para não poluir console do usuário com 400
    // Isso ajuda a isolar se o erro HTTP está travando o JS
    http_response_code(200);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
