<?php
require_once __DIR__ . '/../../../config/config.php';
require_once __DIR__ . '/../../../config/database.php';

header('Content-Type: application/json');
session_start();

// 1. Auth Check
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Não autorizado']);
    exit;
}

$userId = $_SESSION['user_id'];
$company_id = (int) ($_SESSION['company_id'] ?? 0);

// 2. Read Input
$input = json_decode(file_get_contents('php://input'), true);
if (!$input || empty($input['projeto'])) {
    echo json_encode(['success' => false, 'error' => 'Dados inválidos']);
    exit;
}

$diagId = isset($input['diagnostico_id']) ? intval($input['diagnostico_id']) : null;
$p = $input['projeto'];

$conn->begin_transaction();

try {
    // 3. Create Project
    $stmtProj = $conn->prepare("INSERT INTO gestao_projetos (diagnostico_id, titulo, descricao, status, prioridade, responsavel_id, data_inicio, company_id) VALUES (?, ?, ?, 'planejamento', ?, ?, NOW(), ?)");
    $prioridade = strtolower($p['prioridade'] ?? 'media');
    $stmtProj->bind_param("isssis", $diagId, $p['titulo'], $p['descricao'], $prioridade, $userId, $company_id);

    if (!$stmtProj->execute()) {
        throw new Exception("Erro ao criar projeto: " . $stmtProj->error);
    }
    $projetoId = $stmtProj->insert_id;

    // 4. Create OKRs (Objectives & Key Results)
    if (!empty($p['okrs']) && is_array($p['okrs'])) {
        // ADDED: company_id column
        $stmtObj = $conn->prepare("INSERT INTO gestao_objetivos (projeto_id, titulo, progresso, company_id) VALUES (?, ?, 0, ?)");
        $stmtKR = $conn->prepare("INSERT INTO gestao_resultados_chave (objetivo_id, titulo, valor_inicial, valor_meta, valor_atual, unidade) VALUES (?, ?, 0, 100, 0, '%')");

        foreach ($p['okrs'] as $okr) {
            $tituloObj = $okr['titulo'] ?? 'Objetivo sem título';
            // ADDED: bind company_id
            $stmtObj->bind_param("isi", $projetoId, $tituloObj, $company_id);
            $stmtObj->execute();
            $objId = $stmtObj->insert_id;

            if (!empty($okr['krs']) && is_array($okr['krs'])) {
                foreach ($okr['krs'] as $krTitle) {
                    $krText = is_string($krTitle) ? $krTitle : ($krTitle['titulo'] ?? 'KR sem título');
                    $stmtKR->bind_param("is", $objId, $krText);
                    $stmtKR->execute();
                }
            }
        }
    }

    // 5. Create Tasks
    if (!empty($p['tarefas']) && is_array($p['tarefas'])) {
        // ADDED: company_id column
        $stmtTask = $conn->prepare("INSERT INTO gestao_tarefas (projeto_id, titulo, descricao, status, prioridade, data_criacao, prazo, company_id) VALUES (?, ?, ?, 'todo', ?, NOW(), ?, ?)");

        foreach ($p['tarefas'] as $taskRaw) {
            $taskTitle = '';
            $taskDesc = '';
            $taskPrazo = null;

            if (is_array($taskRaw)) {
                $taskTitle = $taskRaw['titulo'] ?? 'Tarefa sem nome';
                $taskDesc = $taskRaw['descricao'] ?? '';
                if (!empty($taskRaw['prazo'])) {
                    $taskPrazo = $taskRaw['prazo'];
                }
            } else {
                $taskTitle = $taskRaw;
            }

            // ADDED: bind company_id
            $stmtTask->bind_param("issssi", $projetoId, $taskTitle, $taskDesc, $prioridade, $taskPrazo, $company_id);
            $stmtTask->execute();
        }
    }

    $conn->commit();
    echo json_encode(['success' => true, 'id' => $projetoId, 'message' => 'Projeto criado com sucesso!']);

} catch (Exception $e) {
    $conn->rollback();
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>