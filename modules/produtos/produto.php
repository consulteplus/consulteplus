<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/config.php'; // Para BASE_URL
session_start();

$produto_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($produto_id === 0) {
    header('Location: ' . BASE_URL . 'produtos');
    exit;
}

// Lógica inteligente: Ao clicar no produto, redirecionar para:
// 1. A última aula assistida (se tiver histórico)
// 2. Ou a primeira aula da primeira trilha.

// Por enquanto, vamos simplificar: Redireciona para a primeira aula disponível
$sql = "SELECT c.id 
        FROM mentoria_conteudos c
        JOIN mentoria_trilhas t ON c.trilha_id = t.id
        WHERE t.produto_id = $produto_id
        ORDER BY t.ordem ASC, c.ordem ASC 
        LIMIT 1";

$res = $conn->query($sql);

if ($res && $res->num_rows > 0) {
    $aula = $res->fetch_assoc();
    header("Location: " . BASE_URL . "produtos/aula/" . $aula['id']);
} else {
    // Produto sem aulas
    echo "<script>alert('Este curso ainda não tem aulas cadastradas.'); window.location.href='" . BASE_URL . "produtos';</script>";
}
exit;
?>