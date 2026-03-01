<?php
/**
 * Helper de Notificações para Profissionais
 * Funções para criar e gerenciar notificações
 */

/**
 * Cria uma notificação para um profissional
 * 
 * @param int $profissional_id ID do profissional
 * @param string $tipo Tipo da notificação
 * @param string $titulo Título da notificação
 * @param string $mensagem Mensagem da notificação
 * @param int|null $agendamento_id ID do agendamento relacionado
 * @param string|null $link Link para ação
 * @param string $icone Ícone Bootstrap
 * @param string $cor Cor do badge
 * @return bool
 */
function criarNotificacao($profissional_id, $tipo, $titulo, $mensagem, $agendamento_id = null, $link = null, $icone = 'bi-bell', $cor = 'primary')
{
    global $conn;

    // Verificar se o profissional tem notificações do sistema ativas
    $config = obterConfigNotificacoes($profissional_id);
    if (!$config || !$config['sistema_ativo']) {
        return false;
    }

    // Verificar se o tipo de evento está ativo
    $campo_config = 'notif_' . str_replace('_', '_', $tipo);
    if (isset($config[$campo_config]) && !$config[$campo_config]) {
        return false;
    }

    $stmt = $conn->prepare("
        INSERT INTO notificacoes_sistema 
        (profissional_id, tipo, titulo, mensagem, agendamento_id, link, icone, cor)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)
    ");

    return $stmt->execute([
        $profissional_id,
        $tipo,
        $titulo,
        $mensagem,
        $agendamento_id,
        $link,
        $icone,
        $cor
    ]);
}

/**
 * Notifica novo agendamento
 */
function notificarNovoAgendamento($agendamento_id)
{
    global $conn;

    $stmt = $conn->prepare("
        SELECT 
            a.id,
            a.data_hora,
            a.profissional_id,
            pac.nome as paciente_nome,
            tp.nome as tipo_procedimento,
            s.nome as sala_nome
        FROM agendamentos a
        JOIN pacientes pac ON a.paciente_id = pac.id
        JOIN tipos_procedimento tp ON a.tipo_procedimento_id = tp.id
        JOIN salas s ON a.sala_id = s.id
        WHERE a.id = ?
    ");
    $stmt->bind_param("i", $agendamento_id);
    $stmt->execute();
    $agendamento = $stmt->get_result()->fetch_assoc();

    if (!$agendamento)
        return false;

    $data_formatada = date('d/m/Y', strtotime($agendamento['data_hora']));
    $hora_formatada = date('H:i', strtotime($agendamento['data_hora']));

    $titulo = "🆕 Novo Agendamento";
    $mensagem = "{$agendamento['paciente_nome']} - {$data_formatada} às {$hora_formatada}\n";
    $mensagem .= "📋 {$agendamento['tipo_procedimento']}\n";
    $mensagem .= "🏥 {$agendamento['sala_nome']}";

    $link = BASE_URL . "agendamentos/" . $agendamento_id;

    return criarNotificacao(
        $agendamento['profissional_id'],
        'novo_agendamento',
        $titulo,
        $mensagem,
        $agendamento_id,
        $link,
        'bi-calendar-plus',
        'success'
    );
}

/**
 * Notifica cancelamento
 */
function notificarCancelamento($agendamento_id, $motivo = null)
{
    global $conn;

    $stmt = $conn->prepare("
        SELECT 
            a.profissional_id,
            a.data_hora,
            pac.nome as paciente_nome
        FROM agendamentos a
        JOIN pacientes pac ON a.paciente_id = pac.id
        WHERE a.id = ?
    ");
    $stmt->bind_param("i", $agendamento_id);
    $stmt->execute();
    $agendamento = $stmt->get_result()->fetch_assoc();

    if (!$agendamento)
        return false;

    $data_formatada = date('d/m/Y', strtotime($agendamento['data_hora']));
    $hora_formatada = date('H:i', strtotime($agendamento['data_hora']));

    $titulo = "❌ Agendamento Cancelado";
    $mensagem = "{$agendamento['paciente_nome']} - {$data_formatada} às {$hora_formatada}";
    if ($motivo) {
        $mensagem .= "\nMotivo: {$motivo}";
    }

    $link = BASE_URL . "agendamentos/" . $agendamento_id;

    return criarNotificacao(
        $agendamento['profissional_id'],
        'cancelamento',
        $titulo,
        $mensagem,
        $agendamento_id,
        $link,
        'bi-x-circle',
        'danger'
    );
}

/**
 * Notifica confirmação do paciente
 */
function notificarConfirmacao($agendamento_id)
{
    global $conn;

    $stmt = $conn->prepare("
        SELECT 
            a.profissional_id,
            a.data_hora,
            pac.nome as paciente_nome
        FROM agendamentos a
        JOIN pacientes pac ON a.paciente_id = pac.id
        WHERE a.id = ?
    ");
    $stmt->bind_param("i", $agendamento_id);
    $stmt->execute();
    $agendamento = $stmt->get_result()->fetch_assoc();

    if (!$agendamento)
        return false;

    $data_formatada = date('d/m/Y', strtotime($agendamento['data_hora']));
    $hora_formatada = date('H:i', strtotime($agendamento['data_hora']));

    $titulo = "✅ Paciente Confirmou Presença";
    $mensagem = "{$agendamento['paciente_nome']} - {$data_formatada} às {$hora_formatada}";

    $link = BASE_URL . "agendamentos/" . $agendamento_id;

    return criarNotificacao(
        $agendamento['profissional_id'],
        'confirmacao',
        $titulo,
        $mensagem,
        $agendamento_id,
        $link,
        'bi-check-circle',
        'info'
    );
}

/**
 * Notifica reagendamento
 */
function notificarReagendamento($agendamento_id, $data_antiga, $data_nova)
{
    global $conn;

    $stmt = $conn->prepare("
        SELECT 
            a.profissional_id,
            pac.nome as paciente_nome
        FROM agendamentos a
        JOIN pacientes pac ON a.paciente_id = pac.id
        WHERE a.id = ?
    ");
    $stmt->bind_param("i", $agendamento_id);
    $stmt->execute();
    $agendamento = $stmt->get_result()->fetch_assoc();

    if (!$agendamento)
        return false;

    $data_antiga_formatada = date('d/m/Y H:i', strtotime($data_antiga));
    $data_nova_formatada = date('d/m/Y H:i', strtotime($data_nova));

    $titulo = "🔄 Agendamento Reagendado";
    $mensagem = "{$agendamento['paciente_nome']}\n";
    $mensagem .= "De: {$data_antiga_formatada}\n";
    $mensagem .= "Para: {$data_nova_formatada}";

    $link = BASE_URL . "agendamentos/" . $agendamento_id;

    return criarNotificacao(
        $agendamento['profissional_id'],
        'reagendamento',
        $titulo,
        $mensagem,
        $agendamento_id,
        $link,
        'bi-arrow-repeat',
        'warning'
    );
}

/**
 * Obtém configurações de notificação de um profissional
 */
function obterConfigNotificacoes($profissional_id)
{
    global $conn;

    $stmt = $conn->prepare("
        SELECT * FROM profissionais_notificacoes_config 
        WHERE profissional_id = ?
    ");
    $stmt->bind_param("i", $profissional_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        // Criar configuração padrão
        if (criarConfigPadrao($profissional_id)) {
            return obterConfigNotificacoes($profissional_id); // Tenta de novo apenas se tiver criado com sucesso
        }
        return false; // Evita loop infinito se falhar a criação
    }

    return $result->fetch_assoc();
}

/**
 * Cria configuração padrão para um profissional
 */
function criarConfigPadrao($profissional_id)
{
    global $conn;

    // Criar configuração padrão sem telefone (pode ser preenchido depois nas configurações)
    $stmt = $conn->prepare("
        INSERT INTO profissionais_notificacoes_config (profissional_id, telefone_whatsapp)
        VALUES (?, NULL)
    ");
    $stmt->bind_param("i", $profissional_id);
    return $stmt->execute();
}

/**
 * Conta notificações não lidas
 */
function contarNotificacoesNaoLidas($profissional_id)
{
    global $conn;

    $stmt = $conn->prepare("
        SELECT COUNT(*) as total 
        FROM notificacoes_sistema 
        WHERE profissional_id = ? AND lida = 0
    ");
    $stmt->bind_param("i", $profissional_id);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();

    return (int) $result['total'];
}

/**
 * Marca notificação como lida
 */
function marcarNotificacaoLida($notificacao_id, $profissional_id)
{
    global $conn;

    $stmt = $conn->prepare("
        UPDATE notificacoes_sistema 
        SET lida = 1, lida_em = NOW()
        WHERE id = ? AND profissional_id = ?
    ");
    $stmt->bind_param("ii", $notificacao_id, $profissional_id);
    return $stmt->execute();
}

/**
 * Marca todas as notificações como lidas
 */
function marcarTodasLidas($profissional_id)
{
    global $conn;

    $stmt = $conn->prepare("
        UPDATE notificacoes_sistema 
        SET lida = 1, lida_em = NOW()
        WHERE profissional_id = ? AND lida = 0
    ");
    $stmt->bind_param("i", $profissional_id);
    return $stmt->execute();
}

/**
 * Lista notificações de um profissional
 */
function listarNotificacoes($profissional_id, $limite = 10, $apenas_nao_lidas = false)
{
    global $conn;

    $where = "profissional_id = ?";
    if ($apenas_nao_lidas) {
        $where .= " AND lida = 0";
    }

    $stmt = $conn->prepare("
        SELECT * FROM notificacoes_sistema 
        WHERE {$where}
        ORDER BY created_at DESC
        LIMIT ?
    ");
    $stmt->bind_param("ii", $profissional_id, $limite);
    $stmt->execute();

    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}
