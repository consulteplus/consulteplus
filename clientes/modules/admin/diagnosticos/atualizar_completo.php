<?php
require_once __DIR__ . '/../../../config/config.php';
require_once __DIR__ . '/../../../includes/auth.php';

// checkPermission(['superadmin']);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die('Método inválido');
}

$id = intval($_POST['id']);
$titulo = $_POST['titulo'] ?? '';
$descricao = $_POST['descricao'] ?? '';
$perguntasJson = $_POST['perguntas_json'] ?? '[]';

if ($id <= 0 || empty($titulo)) {
    die('Dados inválidos');
}

$conn->begin_transaction();

try {
    // 1. Update Model
    $stmtModel = $conn->prepare("UPDATE gestao_diagnostico_modelos SET titulo = ?, descricao = ? WHERE id = ?");
    $stmtModel->bind_param("ssi", $titulo, $descricao, $id);

    if (!$stmtModel->execute()) {
        throw new Exception("Erro ao atualizar modelo: " . $stmtModel->error);
    }

    // 2. Update Questions (Full Replace Strategy for Simplicity)
    // First, delete existing
    $conn->query("DELETE FROM gestao_diagnostico_perguntas WHERE modelo_id = $id");

    // Then re-insert
    $perguntas = json_decode($perguntasJson, true);

    if (is_array($perguntas) && count($perguntas) > 0) {
        $stmtQuestion = $conn->prepare("
            INSERT INTO gestao_diagnostico_perguntas 
            (modelo_id, secao, texto_pergunta, tipo, opcoes, logica_ia, texto_min, texto_max, ordem) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");

        foreach ($perguntas as $index => $p) {
            $secao = $p['secao'] ?? 'Geral';
            $texto = $p['texto_pergunta'];
            $tipo = $p['tipo'] ?? 'texto';
            // Handle options array or string
            $opcoes = '';
            if (isset($p['opcoes'])) {
                $opcoes = is_array($p['opcoes']) ? json_encode($p['opcoes']) : $p['opcoes'];
            }

            $logica = $p['logica_ia'] ?? '';
            $txtMin = $p['texto_min'] ?? '';
            $txtMax = $p['texto_max'] ?? '';
            $ordem = $index + 1;

            $stmtQuestion->bind_param(
                "isssssssi",
                $id,
                $secao,
                $texto,
                $tipo,
                $opcoes,
                $logica,
                $txtMin,
                $txtMax,
                $ordem
            );

            if (!$stmtQuestion->execute()) {
                throw new Exception("Erro ao inserir pergunta: " . $stmtQuestion->error);
            }
        }
    }

    $conn->commit();
    $_SESSION['success'] = "Diagnóstico atualizado com sucesso!";
    redirect('admin/diagnosticos');

} catch (Exception $e) {
    $conn->rollback();
    echo "Erro Fatal: " . $e->getMessage();
}
?>