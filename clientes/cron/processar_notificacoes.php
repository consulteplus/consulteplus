<?php
/**
 * Processador de Notificações
 * Execute via cron a cada hora: 0 * * * * php /caminho/processar_notificacoes.php
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';

// Carregar configurações n8n
$config_file = __DIR__ . '/../config/n8n_config.json';
if (!file_exists($config_file)) {
    die("Configurações n8n não encontradas\n");
}

$config = json_decode(file_get_contents($config_file), true);

if (!$config['ativo']) {
    die("Integração n8n está desativada\n");
}

// Buscar regras ativas
$regras = $conn->query("SELECT * FROM regua_notificacoes WHERE ativo = 1");

$enviados = 0;
$erros = 0;

while ($regra = $regras->fetch_assoc()) {
    // Calcular janela de tempo
    $agora = new DateTime();
    $inicio = clone $agora;
    $fim = clone $agora;

    if ($regra['tipo_evento'] == 'lembrete') {
        // Lógica Híbrida Inteligente

        $horas = (int) $regra['horas_antes'];
        $constraint_criacao = ""; // SQL extra

        if ($horas >= 12) {
            // == REGRA DE LONGO PRAZO (ex: 24h) ==
            // - Janela: Ampliada (pega qualquer coisa entre "agora" e "o alvo") para não perder quem agendou em cima.
            // - Trava: Só envia se agendamento foi criado há mais de 3h (evita spam pós-confirmação).

            $inicio = clone $agora; // De Agora...
            $fim->add(new DateInterval('PT' . ($horas + 1) . 'H')); // ...Até a hora do evento + margem

            // Trava de 3 horas de "maturação"
            $constraint_criacao = "AND a.created_at <= DATE_SUB(NOW(), INTERVAL 3 HOUR)";

        } else {
            // == REGRA DE CURTO PRAZO (ex: 1h, 2h) ==
            // - Janela: Estrita (só envia na hora exata).
            // - Trava: Mínima (15 min) só pra garantir.

            $inicio->add(new DateInterval('PT' . $horas . 'H'));
            $fim->add(new DateInterval('PT' . ($horas + 1) . 'H'));

            $constraint_criacao = "AND a.created_at <= DATE_SUB(NOW(), INTERVAL 15 MINUTE)";
        }

        // Buscar agendamentos elegíveis
        $query = "
            SELECT 
                a.*,
                pac.nome as paciente_nome,
                pac.telefone as paciente_telefone,
                u.nome as profissional_nome,
                s.nome as sala_nome,
                tp.nome as tipo_procedimento
            FROM agendamentos a
            JOIN pacientes pac ON a.paciente_id = pac.id
            JOIN profissionais p ON a.profissional_id = p.id
            JOIN users u ON p.user_id = u.id
            JOIN salas s ON a.sala_id = s.id
            JOIN tipos_procedimento tp ON a.tipo_procedimento_id = tp.id
            WHERE a.data_hora BETWEEN ? AND ?
            AND a.status IN ('agendado', 'confirmado')
            AND a.company_id = ?
            $constraint_criacao
            AND NOT EXISTS (
                SELECT 1 FROM log_notificacoes 
                WHERE agendamento_id = a.id 
                AND regra_id = ?
                AND status = 'enviado'
            )
        ";

        $stmt = $conn->prepare($query);
        $inicio_str = $inicio->format('Y-m-d H:i:s');
        $fim_str = $fim->format('Y-m-d H:i:s');
        $stmt->bind_param("ssii", $inicio_str, $fim_str, $regra['company_id'], $regra['id']);

    } else {
        // Confirmações: buscar agendamentos criados na última hora
        $inicio->sub(new DateInterval('PT1H'));

        // Confirmações: buscar pela created_at do agendamento
        $stmt = $conn->prepare("
            SELECT 
                a.*,
                pac.nome as paciente_nome,
                pac.telefone as paciente_telefone,
                u.nome as profissional_nome,
                s.nome as sala_nome,
                tp.nome as tipo_procedimento
            FROM agendamentos a
            JOIN pacientes pac ON a.paciente_id = pac.id
            JOIN profissionais p ON a.profissional_id = p.id
            JOIN users u ON p.user_id = u.id
            JOIN salas s ON a.sala_id = s.id
            JOIN tipos_procedimento tp ON a.tipo_procedimento_id = tp.id
            WHERE a.created_at BETWEEN ? AND ?
            AND a.status IN ('agendado', 'confirmado')
            AND a.company_id = ?
            AND NOT EXISTS (
                SELECT 1 FROM log_notificacoes 
                WHERE agendamento_id = a.id 
                AND regra_id = ?
                AND status = 'enviado'
            )
        ");
        $inicio_str = $inicio->format('Y-m-d H:i:s');
        $fim_str = $fim->format('Y-m-d H:i:s');
        $stmt->bind_param("ssii", $inicio_str, $fim_str, $regra['company_id'], $regra['id']);
    }

    /* REMOVIDO DAQUI POIS JÁ FOI FEITO DENTRO DOS BLOCOS IF/ELSE PARA TER O BIND_PARAM CORRETO */
    // $inicio_str = $inicio->format('Y-m-d H:i:s');
    // $fim_str = $fim->format('Y-m-d H:i:s');
    // $stmt->bind_param("ssi", $inicio_str, $fim_str, $regra['id']);

    $stmt->execute();
    $agendamentos = $stmt->get_result();

    while ($agendamento = $agendamentos->fetch_assoc()) {
        // Substituir variáveis na mensagem
        $mensagem = str_replace(
            ['{paciente}', '{data}', '{hora}', '{profissional}', '{tipo}', '{sala}'],
            [
                $agendamento['paciente_nome'],
                date('d/m/Y', strtotime($agendamento['data_hora'])),
                date('H:i', strtotime($agendamento['data_hora'])),
                $agendamento['profissional_nome'],
                $agendamento['tipo_procedimento'],
                $agendamento['sala_nome']
            ],
            $regra['mensagem_template']
        );

        // Adicionar referência do agendamento (discreta)
        $mensagem .= "\n\n_Ref: #AG" . $agendamento['id'] . "_";

        // Preparar dados para n8n
        $dados = [
            'company_id' => $regra['company_id'], // Identificação do Tenant
            'agendamento_id' => $agendamento['id'],
            'tipo' => $regra['tipo_evento'],
            'paciente_nome' => $agendamento['paciente_nome'],
            'paciente_telefone' => $agendamento['paciente_telefone'],
            'data_hora' => $agendamento['data_hora'],
            'profissional_nome' => $agendamento['profissional_nome'],
            'tipo_procedimento' => $agendamento['tipo_procedimento'],
            'sala_nome' => $agendamento['sala_nome'],
            'mensagem' => $mensagem
        ];

        // Enviar para n8n
        $ch = curl_init($config['webhook_url']);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($dados));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: Bearer ' . ($config['api_token'] ?? '')
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        // Registrar log
        $status = ($httpCode >= 200 && $httpCode < 300) ? 'enviado' : 'erro';
        $stmt = $conn->prepare("
            INSERT INTO log_notificacoes (agendamento_id, regra_id, tipo, mensagem, status, resposta_n8n)
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        $stmt->bind_param(
            "iissss",
            $agendamento['id'],
            $regra['id'],
            $regra['tipo_evento'],
            $mensagem,
            $status,
            $response
        );
        $stmt->execute();

        if ($status == 'enviado') {
            $enviados++;
            echo "✓ Enviado para {$agendamento['paciente_nome']}\n";
        } else {
            $erros++;
            echo "✗ Erro ao enviar para {$agendamento['paciente_nome']}: HTTP $httpCode\n";
        }
    }
}

echo "\n=== Resumo ===\n";
echo "Enviados: $enviados\n";
echo "Erros: $erros\n";
?>