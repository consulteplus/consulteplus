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

if (!$input || empty($input['titulo'])) {
    echo json_encode(['success' => false, 'error' => 'Título é obrigatório']);
    exit;
}

$titulo = trim($input['titulo']);
$descricao = trim($input['descricao'] ?? '');
$prioridade = strtolower($input['prioridade'] ?? 'media');

// Validar prioridade
if (!in_array($prioridade, ['baixa', 'media', 'alta', 'critica'])) {
    $prioridade = 'media';
}

$conn->begin_transaction();

try {
    // 3. Create Project (Without Diagnosis ID)
    $stmt = $conn->prepare("INSERT INTO gestao_projetos (diagnostico_id, titulo, descricao, status, prioridade, responsavel_id, data_inicio, company_id) VALUES (NULL, ?, ?, 'planejamento', ?, ?, NOW(), ?)");

    if (!$stmt) {
        throw new Exception("Erro de preparação SQL: " . $conn->error);
    }

    $stmt->bind_param("sssis", $titulo, $descricao, $prioridade, $userId, $company_id);

    if (!$stmt->execute()) {
        throw new Exception("Erro ao criar projeto: " . $stmt->error);
    }

    $projetoId = $stmt->insert_id;

    // 4. Process AI Suggestions (OKRs e Tasks)
    if (!empty($input['ai_suggestions'])) {
        $sugestoes = json_decode($input['ai_suggestions'], true);

        if ($sugestoes) {
            // Inserir OKRs
            if (!empty($sugestoes['okrs']) && is_array($sugestoes['okrs'])) {
                // Using existing table 'gestao_objetivos'
                // ADDED: company_id
                $stmtOkr = $conn->prepare("INSERT INTO gestao_objetivos (projeto_id, titulo, progresso, company_id) VALUES (?, ?, 0, ?)");
                $stmtKr = $conn->prepare("INSERT INTO gestao_resultados_chave (objetivo_id, titulo, valor_inicial, valor_meta, valor_atual, unidade) VALUES (?, ?, 0, 100, 0, '%')");

                foreach ($sugestoes['okrs'] as $okrItem) {
                    // Check for both 'objective' (English) and 'objetivo' (Portuguese)
                    $objectiveKey = isset($okrItem['objective']) ? 'objective' : 'objetivo';
                    $keyResultsKey = isset($okrItem['key_results']) ? 'key_results' : 'krs';

                    if (is_array($okrItem) && isset($okrItem[$objectiveKey])) {
                        // Structured Format (Objective + KRs)
                        $okrTitle = substr(trim($okrItem[$objectiveKey]), 0, 255);
                        // ADDED: bind company_id
                        $stmtOkr->bind_param("isi", $projetoId, $okrTitle, $company_id);
                        $stmtOkr->execute();
                        $objId = $stmtOkr->insert_id;

                        if (!empty($okrItem[$keyResultsKey]) && is_array($okrItem[$keyResultsKey])) {
                            foreach ($okrItem[$keyResultsKey] as $krTitle) {
                                $krTitleClean = substr(trim($krTitle), 0, 255);
                                $stmtKr->bind_param("is", $objId, $krTitleClean);
                                $stmtKr->execute();
                            }
                        }
                    } else {
                        // Legacy/Fallback String Format
                        $okrTitle = is_string($okrItem) ? substr(trim($okrItem), 0, 255) : 'Objetivo (Sem título)';
                        // ADDED: bind company_id
                        $stmtOkr->bind_param("isi", $projetoId, $okrTitle, $company_id);
                        $stmtOkr->execute();
                    }
                }
            }

            // Inserir Tarefas
            if (!empty($sugestoes['tarefas']) && is_array($sugestoes['tarefas'])) {
                // ADDED: company_id
                $stmtTask = $conn->prepare("INSERT INTO gestao_tarefas (projeto_id, titulo, descricao, status, prioridade, company_id) VALUES (?, ?, ?, 'todo', ?, ?)");
                foreach ($sugestoes['tarefas'] as $task) {
                    $tTitulo = substr(trim($task['titulo']), 0, 150);
                    $tDesc = trim($task['descricao'] ?? '');
                    $tPrio = isset($task['prioridade']) && in_array(strtolower($task['prioridade']), ['alta', 'media', 'baixa'])
                        ? strtolower($task['prioridade']) : 'media';

                    // ADDED: bind company_id
                    $stmtTask->bind_param("isssi", $projetoId, $tTitulo, $tDesc, $tPrio, $company_id);
                    $stmtTask->execute();
                }
            }
        }
    }

    $conn->commit();

    echo json_encode(['success' => true, 'id' => $projetoId, 'message' => 'Projeto manual criado com sucesso!']);

} catch (Exception $e) {
    $conn->rollback();
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>