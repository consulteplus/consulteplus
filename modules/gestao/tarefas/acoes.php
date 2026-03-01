<?php
require_once __DIR__ . '/../../../config/database.php';
require_once __DIR__ . '/../../../config/config.php';
session_start();

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Não autorizado']);
    exit;
}

$action = $_POST['action'] ?? '';

if ($action === 'create') {
    $titulo = $conn->real_escape_string($_POST['titulo']);
    $descricao = $conn->real_escape_string($_POST['descricao']);
    $prioridade = $conn->real_escape_string($_POST['prioridade']);
    $projetoId = !empty($_POST['projeto_id']) ? intval($_POST['projeto_id']) : 'NULL';
    $prazo = !empty($_POST['prazo']) ? "'" . $conn->real_escape_string($_POST['prazo']) . "'" : 'NULL';
    $status = isset($_POST['status']) && in_array($_POST['status'], ['todo', 'doing', 'done']) ? $_POST['status'] : 'todo';

    $company_id = (int) ($_SESSION['company_id'] ?? 0);
    $sql = "INSERT INTO gestao_tarefas (titulo, descricao, status, prioridade, projeto_id, prazo, company_id) 
            VALUES ('$titulo', '$descricao', '$status', '$prioridade', $projetoId, $prazo, $company_id)";

    if ($conn->query($sql)) {
        echo json_encode(['success' => true, 'id' => $conn->insert_id]);
    } else {
        echo json_encode(['success' => false, 'message' => $conn->error]);
    }
} elseif ($action === 'create_batch') {
    $tasks = json_decode($_POST['tasks'], true);
    if (!is_array($tasks)) {
        echo json_encode(['success' => false, 'message' => 'Dados inválidos']);
        exit;
    }

    $errors = [];
    $count = 0;

    foreach ($tasks as $task) {
        $titulo = $conn->real_escape_string($task['titulo']);
        $descricao = $conn->real_escape_string($task['descricao'] ?? '');
        $prioridade = $conn->real_escape_string($task['prioridade'] ?? 'media');
        $status = 'todo';

        $company_id = (int) ($_SESSION['company_id'] ?? 0);
        $sql = "INSERT INTO gestao_tarefas (titulo, descricao, status, prioridade, company_id) VALUES ('$titulo', '$descricao', '$status', '$prioridade', $company_id)";
        if ($conn->query($sql)) {
            $count++;
        } else {
            $errors[] = $conn->error;
        }
    }

    if (empty($errors)) {
        echo json_encode(['success' => true, 'count' => $count]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Algumas tarefas falharam', 'errors' => $errors]);
    }
} elseif ($action === 'update') {
    $id = intval($_POST['id']);
    $titulo = $conn->real_escape_string($_POST['titulo']);
    $descricao = $conn->real_escape_string($_POST['descricao']);
    $prioridade = $conn->real_escape_string($_POST['prioridade']);
    $projetoId = !empty($_POST['projeto_id']) ? intval($_POST['projeto_id']) : 'NULL';
    $prazo = !empty($_POST['prazo']) ? "'" . $conn->real_escape_string($_POST['prazo']) . "'" : 'NULL';

    $company_id = (int) ($_SESSION['company_id'] ?? 0);
    $sql = "UPDATE gestao_tarefas SET titulo = '$titulo', descricao = '$descricao', prioridade = '$prioridade', projeto_id = $projetoId, prazo = $prazo WHERE id = $id AND company_id = $company_id";

    if ($conn->query($sql)) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => $conn->error]);
    }
} elseif ($action === 'update_status') {
    $id = intval($_POST['id']);
    $status = $conn->real_escape_string($_POST['status']);

    // Se for 'done', atualiza data_conclusao
    $extra = $status === 'done' ? ", data_conclusao = NOW()" : ", data_conclusao = NULL";

    $company_id = (int) ($_SESSION['company_id'] ?? 0);
    $sql = "UPDATE gestao_tarefas SET status = '$status' $extra WHERE id = $id AND company_id = $company_id";

    if ($conn->query($sql)) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => $conn->error]);
    }
} elseif ($action === 'delete') {
    $id = intval($_POST['id']);
    $company_id = (int) ($_SESSION['company_id'] ?? 0);
    $sql = "DELETE FROM gestao_tarefas WHERE id = $id AND company_id = $company_id";

    if ($conn->query($sql)) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => $conn->error]);
    }
} elseif ($action === 'save_suggestions') {
    $historicoId = intval($_POST['historico_id']);
    $suggestions = json_decode($_POST['suggestions'], true);

    if (!is_array($suggestions) || empty($suggestions)) {
        echo json_encode(['success' => false, 'message' => 'Nenhuma sugestão enviada']);
        exit;
    }

    $errors = [];
    foreach ($suggestions as $sug) {
        $titulo = $conn->real_escape_string($sug['titulo']);
        $descricao = $conn->real_escape_string($sug['descricao']);
        $area = $conn->real_escape_string($sug['area']);
        $impacto = $conn->real_escape_string($sug['impacto']);
        $status = 'pendente';

        $sql = "INSERT INTO gestao_diagnostico_sugestoes (historico_id, titulo, descricao, area, impacto, status) 
                VALUES ($historicoId, '$titulo', '$descricao', '$area', '$impacto', '$status')";

        if (!$conn->query($sql)) {
            $errors[] = $conn->error;
        }
    }

    if (empty($errors)) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Erro ao salvar algumas sugestões', 'errors' => $errors]);
    }

} elseif ($action === 'process_suggestions') {
    $createIds = json_decode($_POST['create_ids'], true) ?? [];
    $rejectIds = json_decode($_POST['reject_ids'], true) ?? [];

    $conn->begin_transaction();

    try {
        // 1. Processar Criações (Update Status + Insert Task)
        if (!empty($createIds)) {
            $idsStr = implode(',', array_map('intval', $createIds));

            // Marca como criada
            $conn->query("UPDATE gestao_diagnostico_sugestoes SET status = 'criada' WHERE id IN ($idsStr)");

            // Seleciona para inserir no Kanban
            $res = $conn->query("SELECT titulo, descricao FROM gestao_diagnostico_sugestoes WHERE id IN ($idsStr)");
            while ($row = $res->fetch_assoc()) {
                $titulo = $conn->real_escape_string($row['titulo']);
                $descricao = $conn->real_escape_string($row['descricao']);
                $company_id = (int) ($_SESSION['company_id'] ?? 0);
                $conn->query("INSERT INTO gestao_tarefas (titulo, descricao, status, prioridade, company_id) VALUES ('$titulo', '$descricao', 'todo', 'alta', $company_id)");
            }
        }

        // 2. Processar Rejeições
        if (!empty($rejectIds)) {
            $idsStr = implode(',', array_map('intval', $rejectIds));
            $conn->query("UPDATE gestao_diagnostico_sugestoes SET status = 'recusada' WHERE id IN ($idsStr)");
        }

        $conn->commit();
        echo json_encode(['success' => true]);

    } catch (Exception $e) {
        $conn->rollback();
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }

} else {
    echo json_encode(['success' => false, 'message' => 'Ação inválida']);
}
