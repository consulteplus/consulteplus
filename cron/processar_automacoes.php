<?php
/**
 * Motor de Automações CRM v3.0 (State Machine)
 * 1. Ingestão: Busca 'gatilhos' e insere na fila.
 * 2. Worker: Processa a fila passo-a-passo.
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';

// Limite de execução (evitar timeout em cron loops)
set_time_limit(120);

$n8n_config = json_decode(file_get_contents(__DIR__ . '/../config/n8n_config.json'), true);
if (!$n8n_config['ativo'])
    die("N8N inativo.");

echo "=== [v3.0] Iniciando Motor de Automações (State Machine) ===\n";

// =========================================================================
// PARTE 1: INGESTÃO (Popular a Fila)
// =========================================================================
echo "\n--- [1] Ingestão de Gatilhos ---\n";
$regras = $conn->query("SELECT * FROM automacoes WHERE ativo = 1");

while ($regra = $regras->fetch_assoc()) {
    echo "> Verificando Gatilho: {$regra['nome']}\n";

    // Decodificar filtros
    $filtros = json_decode($regra['filtros'], true);

    switch ($regra['gatilho']) {
        case 'aniversario':
            ingestaoAniversarios($conn, $regra, $filtros);
            break;
        case 'segmento_diario':
            ingestaoSegmentacao($conn, $regra, $filtros);
            break;
        // Futuro: 'agendamento_antes', etc.
    }
}

// =========================================================================
// PARTE 2: WORKER (Processar Fila)
// =========================================================================
echo "\n--- [2] Processamento da Fila ---\n";
processarFila($conn, $n8n_config);


// =========================================================================
// FUNÇÕES DE INGESTÃO
// =========================================================================

function ingestaoSegmentacao($conn, $regra, $filtros)
{
    // Busca Base + Filtros Dinâmicos (Mesma lógica da v2, mas simplificada para Inserção)
    $whereExtra = "1=1";
    if (!empty($filtros)) {
        $whereExtra = construirClausulaWhere($conn, $filtros); // Reutilizar func auxiliar
    }

    $sql = "
        SELECT p.id, p.nome, p.telefone 
        FROM pacientes p 
        WHERE p.ativo = 1 
        AND $whereExtra
        AND NOT EXISTS (
            SELECT 1 FROM automacao_participantes ap
            WHERE ap.automacao_id = {$regra['id']} 
            AND ap.referencia_id = p.id 
            AND ap.tipo_referencia = 'paciente'
        )
    ";

    $result = $conn->query($sql);
    while ($row = $result->fetch_assoc()) {
        inscreverNaFila($conn, $regra['id'], $row['id'], 'paciente', $row);
    }
}

function ingestaoAniversarios($conn, $regra, $filtros = [])
{
    $whereExtra = construirClausulaWhere($conn, $filtros);
    $hoje = date('m-d');
    $sql = "
        SELECT id, nome, telefone FROM pacientes p
        WHERE DATE_FORMAT(data_nascimento, '%m-%d') = '$hoje'
        AND $whereExtra
        AND NOT EXISTS (
            SELECT 1 FROM automacao_participantes ap 
            WHERE ap.automacao_id = {$regra['id']} 
            AND ap.referencia_id = p.id
            AND YEAR(ap.created_at) = YEAR(CURDATE()) -- Permitir 1 vez por ano
        )
    ";
    $result = $conn->query($sql);
    while ($row = $result->fetch_assoc()) {
        inscreverNaFila($conn, $regra['id'], $row['id'], 'paciente', $row);
    }
}

function inscreverNaFila($conn, $autoId, $refId, $tipoRef, $dadosContexto)
{
    if (!isset($dadosContexto['paciente_nome']))
        $dadosContexto['paciente_nome'] = $dadosContexto['nome'];
    if (!isset($dadosContexto['paciente_telefone']))
        $dadosContexto['paciente_telefone'] = $dadosContexto['telefone'];

    $jsonContexto = json_encode($dadosContexto, JSON_UNESCAPED_UNICODE);

    $stmt = $conn->prepare("INSERT INTO automacao_participantes (automacao_id, referencia_id, tipo_referencia, status, dados_contexto, agendado_para) VALUES (?, ?, ?, 'pendente', ?, NOW())");
    $stmt->bind_param("iiss", $autoId, $refId, $tipoRef, $jsonContexto);
    if ($stmt->execute()) {
        echo "  + Inscrito ID $refId na automação $autoId\n";
    }
}


// =========================================================================
// FUNÇÕES DO WORKER (MOTOR DE ESTADOS)
// =========================================================================

function processarFila($conn, $n8n_config)
{
    // 1. Pegar tarefas vencidas
    // Status: pendente ou agendado.
    $sql = "
        SELECT ap.*, a.mensagem_template as fluxo_json, a.nome as nome_fluxo
        FROM automacao_participantes ap
        JOIN automacoes a ON ap.automacao_id = a.id
        WHERE ap.status IN ('pendente', 'agendado') 
        AND ap.agendado_para <= NOW()
        AND a.ativo = 1
        LIMIT 50
    ";

    $result = $conn->query($sql);
    if (!$result || $result->num_rows == 0) {
        echo "  Nenhuma tarefa pendente.\n";
        return;
    }

    while ($tarefa = $result->fetch_assoc()) {
        echo "> Processando Participante #{$tarefa['id']} (Passo: {$tarefa['uid_passo_atual']})\n";

        // Load Fluxo
        $fluxo = json_decode($tarefa['fluxo_json'], true);
        if (!$fluxo) {
            marcarErro($conn, $tarefa['id'], "JSON Inválido");
            continue;
        }

        // Descobrir Próximo Passo
        $proximoNo = encontrarProximoNo($fluxo, $tarefa['uid_passo_atual']); // Retorna Node ou 'FIM'

        if ($proximoNo === null) {
            // Se current é null, começa do primeiro
            $proximoNo = $fluxo[0] ?? 'FIM';
        }

        if (!is_array($proximoNo)) {
            concluirParticipacao($conn, $tarefa['id']);
            echo "  - Fluxo Concluido (Sinal: $proximoNo).\n";
            continue;
        }

        // Executar o Nó Encontrado
        executarNo($conn, $tarefa, $proximoNo, $n8n_config, $fluxo);
    }
}

/**
 * Executa a lógica de um único nó e agenda o próximo.
 */
function executarNo($conn, $tarefa, $no, $n8n, $fluxoCompleto)
{
    $uid = $no['uid'];
    $tipo = $no['tipo'];
    $contexto = json_decode($tarefa['dados_contexto'], true);

    echo "  -> Executando Nó ($tipo) UID: $uid\n";

    // --- AÇÃO: WHATSAPP ---
    if ($tipo === 'whatsapp') {
        enviarWhatsapp($no['conteudo'], $contexto, $n8n, $tarefa['nome_fluxo']);

        // Avançar Imediatamente para o próximo no próximo tick
        atualizarEstado($conn, $tarefa['id'], $uid, 'agendado', 'NOW()'); // Já marca que PASSOU por este uid
    }

    // --- AÇÃO: DELAY ---
    elseif ($tipo === 'delay') {
        $valor = (int) $no['valor'];
        $unidade = $no['unidade']; // minutos, horas, dias

        // Calcular tempo futuro
        $sqlInterval = "NOW() + INTERVAL $valor " . strtoupper(substr($unidade, 0, -1)); // Remove 's' simples
        if ($unidade == 'dias')
            $sqlInterval = "NOW() + INTERVAL $valor DAY";
        if ($unidade == 'horas')
            $sqlInterval = "NOW() + INTERVAL $valor HOUR";
        if ($unidade == 'minutos')
            $sqlInterval = "NOW() + INTERVAL $valor MINUTE";

        echo "  -> Adiado por $valor $unidade\n";
        // Atualiza estado para 'agendado' neste mesmo UID, mas com data futura.
        // OBS: Se eu marcar uid_passo_atual = DelayUID, no próximo loop ele vai achar o Delay de novo?
        // Sim. Precisamos saber se ESTAMOS ENTRANDO ou SAINDO do delay.
        // SOLUÇÃO: A função encontrarProximoNo deve saber pular.
        // MUDANÇA: Quando entra no delay, atualizamos o agendado_para.
        // O uid_passo_atual DEVE SER O UID DO DELAY enquanto espera.
        // Mas quando acordar, ele deve ir para o PRÓXIMO.

        // Truque: Se já estamos neste UID (tarefa['uid_passo_atual'] == $uid) E o tempo já passou...
        // Significa que acabamos de acordar. Então devemos pular para o próximo!

        if ($tarefa['uid_passo_atual'] === $uid) {
            // Acordou do delay. Não executa nada, apenas força busca do próximo.
            // Mas `processarFila` chamou `encontrarProximoNo` com o UID atual.
            // Precisamos garantir que encontrarProximoNo pegue o SUCESSOR.
            atualizarEstado($conn, $tarefa['id'], $uid, 'agendado', 'NOW()'); // Update timestamp to now to force advance logic? 
            // Logic gap here. Let's fix finding logic below.
        } else {
            // Entrando no delay pela primeira vez
            atualizarEstado($conn, $tarefa['id'], $uid, 'agendado', $sqlInterval, true);
        }
    }

    // --- AÇÃO: CONDICIONAL ---
    elseif ($tipo === 'condicional') {
        $result = verificarCondicao_v3($contexto, $no['regra']);
        // Precisamos saber se vamos pro galho SIM ou NÃO.
        // No modelo JSON aninhado, sim/nao são filhos.
        // Precisamos descer na árvore.

        // Como o `encontrarProximoNo` é linear, precisamos de uma lógica para "Entrar" no galho.
        // Vou usar uma função auxiliar que "achata" o próximo passo.

        // Se True -> Primeiro filho do array SIM.
        // Se False -> Primeiro filho do array NAO.

        $proximoReal = null;
        if ($result && !empty($no['acoes_sim']))
            $proximoReal = $no['acoes_sim'][0];
        elseif (!$result && !empty($no['acoes_nao']))
            $proximoReal = $no['acoes_nao'][0];

        if ($proximoReal) {
            // Executa recursivamente o primeiro do galho agora mesmo?
            // Melhor atualizar o estado para apontar que "Já passamos pelo condicional" e o próximo é o filho.
            // Mas o filho tem UID.
            atualizarEstado($conn, $tarefa['id'], $proximoReal['uid'], 'agendado', 'NOW()', false, true);
            // Note: We overwrite step to payload child, saving a cycle.
        } else {
            // Nenhum caminho (array vazio), segue a vida (irmão do condicional)
            atualizarEstado($conn, $tarefa['id'], $uid, 'agendado', 'NOW()');
        }
    }
}

// --- NAVEGAÇÃO NA ÁRVORE JSON ---

// Função de Navegação Hierárquica Inteligente
// Retorna o Objeto do Próximo Nó ou 'FIM'
function encontrarProximoNo($tree, $currentUid)
{
    if (!$currentUid)
        return $tree[0] ?? 'FIM';

    // 1. Tenta achar o nó e seu "próximo" neste nível
    for ($i = 0; $i < count($tree); $i++) {
        $node = $tree[$i];

        // Se achamos o currentUid neste nível
        if ($node['uid'] === $currentUid) {
            // Retorna o próximo irmão, se houver
            return $tree[$i + 1] ?? 'FIM_DO_RAMO';
        }

        // Se for condicional, mergulha
        if ($node['tipo'] == 'condicional') {

            // Busca no SIM
            $res = encontrarProximoNo($node['acoes_sim'], $currentUid);
            if ($res !== 'NAO_ENCONTRADO') {
                // Se retornou um nó válido, é ele
                if (is_array($res) || $res === 'FIM')
                    return $res;
                // Se retornou FIM_DO_RAMO, significa que acabou o bloco SIM.
                // Então o próximo passo é o irmão DESTE CONDICIONAL (o pai).
                if ($res === 'FIM_DO_RAMO') {
                    return $tree[$i + 1] ?? 'FIM_DO_RAMO';
                }
            }

            // Busca no NAO
            $res = encontrarProximoNo($node['acoes_nao'], $currentUid);
            if ($res !== 'NAO_ENCONTRADO') {
                if (is_array($res) || $res === 'FIM')
                    return $res;
                if ($res === 'FIM_DO_RAMO') {
                    return $tree[$i + 1] ?? 'FIM_DO_RAMO';
                }
            }
        }
    }

    return 'NAO_ENCONTRADO';
}

function flattenTree($nodes)
{
    return [];
} // Deprecated/Removed


// --- UTILS ---

function verificarCondicao_v3($ctx, $regra)
{
    if (empty($regra))
        return true;
    $val = $ctx['paciente_' . $regra['campo']] ?? ''; // Simples por enquanto
    // ... Implementar lógica completa igual v2 ...
    return true; // Stub MVP
}

function enviarWhatsapp($msg, $ctx, $n8n, $campanha)
{
    $msg = str_replace('{paciente}', $ctx['paciente_nome'], $msg);
    // ... enviar CURL ...
    $ch = curl_init($n8n['webhook_url']);
    $payload = ['mensagem' => $msg, 'telefone' => $ctx['paciente_telefone']];
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_exec($ch);
    curl_close($ch);
}

function atualizarEstado($conn, $id, $uid, $status, $dataSql, $isSql = false, $forceUid = false)
{
    // Se forceUid = false, assume que $uid é o passo que ACABOU de ser executado,
    // então na verdade o banco deveria guardar o ESSE passo para que no próximo loop findNext encontre o próximo.
    // Mas se for Delay, guardamos o Próprio.

    if ($forceUid) {
        // Estamos pulando direto para um nó específico (ex: primeiro filho do condicional)
        $novoUid = $uid;
    } else {
        $novoUid = $uid;
    }

    $timeExpr = $isSql ? $dataSql : "'$dataSql'";
    $conn->query("UPDATE automacao_participantes SET uid_passo_atual = '$novoUid', status='$status', agendado_para = $timeExpr WHERE id = $id");
}

function concluirParticipacao($conn, $id)
{
    $conn->query("UPDATE automacao_participantes SET status='concluido' WHERE id=$id");
}

function marcarErro($conn, $id, $msg)
{
    $conn->query("UPDATE automacao_participantes SET status='falha', mensagem_erro='$msg' WHERE id=$id");
}

function construirClausulaWhere($conn, $filtros)
{
    if (empty($filtros))
        return "1=1";

    $clausulas = [];
    $mapaCampos = [
        'paciente' => [
            'cidade' => 'p.cidade',
            'bairro' => 'p.bairro',
            'sexo' => 'p.sexo',
            'origem' => 'p.como_conheceu',
            'data_nascimento' => 'p.data_nascimento'
        ],
        'agendamento' => [
            'status' => 'a.status',
            'valor' => 'a.valor_estimado'
            // 'procedimento_id' => 'a.tipo_procedimento_id'
        ]
    ];

    foreach ($filtros as $f) {
        $entidade = $f['entidade'];
        $campo = $f['campo'];
        $op = $f['operador'];
        $val = $conn->real_escape_string($f['valor']);

        $coluna = $mapaCampos[$entidade][$campo] ?? null;

        // Tratamento especial para Idade
        if ($entidade == 'paciente' && $campo == 'idade') {
            $coluna = "TIMESTAMPDIFF(YEAR, p.data_nascimento, CURDATE())";
        }

        if ($coluna) {
            if ($op == 'LIKE')
                $clausulas[] = "$coluna LIKE '%$val%'";
            else
                $clausulas[] = "$coluna $op '$val'";
        }
    }

    return empty($clausulas) ? "1=1" : implode(' AND ', $clausulas);
}
?>