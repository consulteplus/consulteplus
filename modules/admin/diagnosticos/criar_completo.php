<?php
require_once __DIR__ . '/../../../config/config.php';
require_once __DIR__ . '/../../../includes/auth.php';

// Check permission
// checkPermission(['superadmin']); // Uncomment if strict

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die('Método inválido');
}

$titulo = $_POST['titulo'] ?? '';
$descricao = $_POST['descricao'] ?? '';
$perguntasJson = $_POST['perguntas_json'] ?? '[]';

if (empty($titulo)) {
    die('Título é obrigatório');
}

$conn->begin_transaction();

try {
    // 1. Create Model
    $stmtModel = $conn->prepare("INSERT INTO gestao_diagnostico_modelos (titulo, descricao, ativo) VALUES (?, ?, 1)");
    $stmtModel->bind_param("ss", $titulo, $descricao);

    if (!$stmtModel->execute()) {
        throw new Exception("Erro ao criar modelo: " . $stmtModel->error);
    }

    $modeloId = $conn->insert_id;

    // 2. Process Questions
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
            $opcoes = isset($p['opcoes']) && is_array($p['opcoes']) && count($p['opcoes']) > 0
                ? json_encode($p['opcoes'])
                : null;
            $logica = $p['logica_ia'] ?? '';
            $txtMin = $p['texto_min'] ?? '';
            $txtMax = $p['texto_max'] ?? '';
            $ordem = $index + 1;

            $stmtQuestion->bind_param(
                "isssssssi",
                $modeloId,
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
    $_SESSION['success'] = "Diagnóstico criado com sucesso com " . count($perguntas) . " perguntas!";
    redirect('admin/diagnosticos');

} catch (Exception $e) {
    $conn->rollback();
    echo "Erro Fatal: " . $e->getMessage();
}
?>