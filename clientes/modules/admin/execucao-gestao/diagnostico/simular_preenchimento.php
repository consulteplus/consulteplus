<?php
// simular_preenchimento.php
// Script para gerar um Diagnóstico Dummy Completo para testes

require_once __DIR__ . '/../../../config/config.php';
require_once __DIR__ . '/../../../config/database.php';
session_start();

// Simula usuário logado se não houver (apenas para teste, ou exige login)
if (!isset($_SESSION['user_id'])) {
    // Tenta pegar o primeiro usuário do banco
    $resUser = $conn->query("SELECT id, nome FROM usuarios LIMIT 1");
    if ($resUser && $resUser->num_rows > 0) {
        $u = $resUser->fetch_assoc();
        $_SESSION['user_id'] = $u['id'];
        $_SESSION['nome'] = $u['nome'];
        echo "Usuário simulado: " . $u['nome'] . " (ID: " . $u['id'] . ")<br>";
    } else {
        die("Nenhum usuário encontrado no banco para simular login.");
    }
}

$userId = $_SESSION['user_id'];

echo "<h2>Iniciando Simulação de Diagnóstico...</h2>";

// 1. Criar Registro de Histórico
$stmt = $conn->prepare("INSERT INTO gestao_diagnostico_resultados (user_id, score_geral, nivel_maturidade) VALUES (?, 0, 'Em Análise')");
$stmt->bind_param("i", $userId);

if ($stmt->execute()) {
    $historicoId = $stmt->insert_id;
    echo "Diagnóstico criado com ID: <strong>$historicoId</strong><br>";
} else {
    die("Erro ao criar histórico: " . $conn->error);
}

// 2. Buscar Perguntas
$perguntas = [];
$res = $conn->query("SELECT * FROM gestao_diagnostico_perguntas");
while ($row = $res->fetch_assoc()) {
    $perguntas[] = $row;
}
echo "Encontradas " . count($perguntas) . " perguntas.<br>";

// 3. Gerar Respostas Aleatórias
$stmtResp = $conn->prepare("INSERT INTO gestao_diagnostico_respostas (historico_id, pergunta_id, valor_escolhido) VALUES (?, ?, ?)");

foreach ($perguntas as $p) {
    $opcoes = json_decode($p['opcoes'], true);
    if (!is_array($opcoes))
        $opcoes = [];

    $respostaEscolhida = '';

    if ($p['tipo'] === 'numero') {
        // Gera número aleatório realista
        $respostaEscolhida = rand(1000, 50000);
    } elseif ($p['tipo'] === 'multipla') {
        // Seleciona 1 ou 2 opções aleatórias
        $keys = array_rand($opcoes, min(2, count($opcoes)));
        if (!is_array($keys))
            $keys = [$keys];
        $selecionados = [];
        foreach ($keys as $k)
            $selecionados[] = $opcoes[$k];
        $respostaEscolhida = implode(", ", $selecionados);
    } else {
        // Seleciona 1 opção aleatória (tipo radio/selecao)
        if (!empty($opcoes)) {
            $key = array_rand($opcoes);
            $respostaEscolhida = $opcoes[$key];
        } else {
            $respostaEscolhida = "Resposta Genérica Teste";
        }
    }

    $stmtResp->bind_param("iis", $historicoId, $p['id'], $respostaEscolhida);
    $stmtResp->execute();
}

echo "Respostas salvas com sucesso.<hr>";

// 4. Salvar KPIs Fictícios
$stmtKpi = $conn->prepare("INSERT INTO gestao_diagnostico_kpis (diagnostico_id, faturamento_medio_mensal, qtd_atendimentos_mes, ticket_medio, taxa_ocupacao) VALUES (?, ?, ?, ?, ?)");
$fat = 150000;
$qtd = 300;
$ticket = $fat / $qtd;
$ocup = 75;
$stmtKpi->bind_param("idddd", $historicoId, $fat, $qtd, $ticket, $ocup);
$stmtKpi->execute();

echo "<h3>Sucesso!</h3>";
echo "<p>Agora você pode visualizar o resultado processado pela IA.</p>";
echo "<a href='" . BASE_URL . "gestao/diagnostico/resultado/" . $historicoId . "' target='_blank' style='background:green; color:white; padding:10px 20px; text-decoration:none; border-radius:5px;'>VER RESULTADO >></a>";
