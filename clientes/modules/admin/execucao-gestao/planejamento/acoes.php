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

// --- OBJETIVOS ---
if ($action === 'create_objective') {
    $titulo = $conn->real_escape_string($_POST['titulo']);
    $descricao = $conn->real_escape_string($_POST['descricao']);
    $prazo = $conn->real_escape_string($_POST['prazo']);
    $projeto_id = !empty($_POST['projeto_id']) ? intval($_POST['projeto_id']) : 'NULL';

    $company_id = (int) ($_SESSION['company_id'] ?? 0);
    $sql = "INSERT INTO gestao_objetivos (titulo, descricao, prazo, projeto_id, company_id) VALUES ('$titulo', '$descricao', '$prazo', $projeto_id, $company_id)";
    echo $conn->query($sql) ? json_encode(['success' => true]) : json_encode(['success' => false, 'message' => $conn->error]);
} elseif ($action === 'delete_objective') {
    $id = intval($_POST['id']);
    $company_id = (int) ($_SESSION['company_id'] ?? 0);
    $sql = "DELETE FROM gestao_objetivos WHERE id = $id AND company_id = $company_id";
    echo $conn->query($sql) ? json_encode(['success' => true]) : json_encode(['success' => false, 'message' => $conn->error]);
}

// --- KR (RESULTADOS CHAVE) ---
elseif ($action === 'create_kr') {
    $objetivo_id = intval($_POST['objetivo_id']);
    $titulo = $conn->real_escape_string($_POST['titulo']);
    $valor_inicial = floatval($_POST['valor_inicial']);
    $valor_meta = floatval($_POST['valor_meta']);
    $unidade = $conn->real_escape_string($_POST['unidade']);

    // $company_id = (int) ($_SESSION['company_id'] ?? 0); // Removed to fix save error
    $sql = "INSERT INTO gestao_resultados_chave (objetivo_id, titulo, valor_inicial, valor_meta, valor_atual, unidade) 
            VALUES ($objetivo_id, '$titulo', $valor_inicial, $valor_meta, $valor_inicial, '$unidade')";

    if ($conn->query($sql)) {
        recalcularProgresso($objetivo_id, $conn);
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => $conn->error]);
    }
} elseif ($action === 'update_kr_value') {
    $id = intval($_POST['id']);
    $val = floatval($_POST['valor']);
    $objetivo_id = intval($_POST['objetivo_id']); // Passado para evitar query extra

    $sql = "UPDATE gestao_resultados_chave SET valor_atual = $val WHERE id = $id";

    if ($conn->query($sql)) {
        recalcularProgresso($objetivo_id, $conn);
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => $conn->error]);
    }
}

function recalcularProgresso($objId, $conn)
{
    // Busca todos os KRs deste objetivo
    $sql = "SELECT valor_inicial, valor_meta, valor_atual FROM gestao_resultados_chave WHERE objetivo_id = $objId";
    $result = $conn->query($sql);

    $totalProgress = 0;
    $count = 0;

    while ($row = $result->fetch_assoc()) {
        $count++;
        $start = $row['valor_inicial'];
        $target = $row['valor_meta'];
        $current = $row['valor_atual'];

        if ($target == $start) {
            $p = 100;
        } else {
            $p = (($current - $start) / ($target - $start)) * 100;
        }

        $p = max(0, min(100, $p)); // Clamp 0-100
        $totalProgress += $p;
    }

    $final = $count > 0 ? round($totalProgress / $count) : 0;

    $conn->query("UPDATE gestao_objetivos SET progresso = $final WHERE id = $objId");
}
