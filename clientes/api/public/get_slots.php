<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/database.php';

// Validar Inputs
$profissional_id = $_GET['profissional_id'] ?? null;
$data = $_GET['data'] ?? null;
$procedimento_id = $_GET['procedimento_id'] ?? null;

if (!$profissional_id || !$data || !$procedimento_id) {
    http_response_code(400);
    echo json_encode(['error' => 'Parâmetros obrigatórios ausentes.']);
    exit;
}

// 1. Obter duração do procedimento
$stmt = $conn->prepare("SELECT duracao_minutos FROM tipos_procedimento WHERE id = ?");
$stmt->bind_param("i", $procedimento_id);
$stmt->execute();
$result = $stmt->get_result();
$proc = $result->fetch_assoc();

if (!$proc) {
    http_response_code(404);
    echo json_encode(['error' => 'Procedimento não encontrado.']);
    exit;
}
$duracao = (int) $proc['duracao_minutos'];

// 2. Determinar dia da semana e buscar alocações (Horários Base)
$dias_map = [
    'Sun' => 'domingo',
    'Mon' => 'segunda',
    'Tue' => 'terca',
    'Wed' => 'quarta',
    'Thu' => 'quinta',
    'Fri' => 'sexta',
    'Sat' => 'sabado'
];
$dia_semana_en = date('D', strtotime($data));
$dia_semana = $dias_map[$dia_semana_en];

$query_alocacao = "
    SELECT hora_inicio, hora_fim 
    FROM alocacao_salas 
    WHERE profissional_id = ? 
    AND dia_semana = ? 
    AND ativo = 1 
    AND data_inicio <= ? 
    AND (data_fim IS NULL OR data_fim >= ?)
";
$stmt = $conn->prepare($query_alocacao);
$stmt->bind_param("isss", $profissional_id, $dia_semana, $data, $data);
$stmt->execute();
$alocacoes = $stmt->get_result();

$slots_candidatos = [];

while ($row = $alocacoes->fetch_assoc()) {
    $inicio = new DateTime($data . ' ' . $row['hora_inicio']);
    $fim = new DateTime($data . ' ' . $row['hora_fim']);

    // Gerar slots para esta janela
    while ($inicio < $fim) {
        $slot_start = clone $inicio;
        $slot_end = clone $inicio;
        $slot_end->modify("+{$duracao} minutes");

        // Se o slot termina depois do fim do turno, para
        if ($slot_end > $fim)
            break;

        // Se a data for HOJE, filtrar horários passados
        if ($data == date('Y-m-d')) {
            $agora = new DateTime();
            // Margem de segurança de 2h (via config, mas hardcoded por enquanto conforme plano)
            $agora->modify('+2 hours');
            if ($slot_start < $agora) {
                $inicio->modify("+{$duracao} minutes");
                continue;
            }
        }

        $slots_candidatos[] = [
            'start' => $slot_start->format('H:i'),
            'end' => $slot_end->format('H:i'),
            'timestamp_start' => $slot_start->getTimestamp(),
            'timestamp_end' => $slot_end->getTimestamp()
        ];

        $inicio->modify("+{$duracao} minutes");
    }
}

// 3. Buscar Agendamentos (Ocupados)
$query_agendamentos = "
    SELECT data_hora, duracao_minutos 
    FROM agendamentos 
    WHERE profissional_id = ? 
    AND DATE(data_hora) = ? 
    AND status != 'cancelado'
";
$stmt = $conn->prepare($query_agendamentos);
$stmt->bind_param("is", $profissional_id, $data);
$stmt->execute();
$ocupados_result = $stmt->get_result();

$ocupados = [];
while ($row = $ocupados_result->fetch_assoc()) {
    $start = new DateTime($row['data_hora']);
    $end = clone $start;
    $end->modify("+{$row['duracao_minutos']} minutes");

    $ocupados[] = [
        'start' => $start->getTimestamp(),
        'end' => $end->getTimestamp()
    ];
}

// 4. Buscar Bloqueios
$query_bloqueios = "
    SELECT data_inicio, data_fim 
    FROM bloqueios_agenda 
    WHERE profissional_id = ? 
    AND (
        (DATE(data_inicio) <= ? AND DATE(data_fim) >= ?)
    )
";
$stmt = $conn->prepare($query_bloqueios);
$stmt->bind_param("iss", $profissional_id, $data, $data);
$stmt->execute();
$bloqueios_result = $stmt->get_result();

while ($row = $bloqueios_result->fetch_assoc()) {
    $start = new DateTime($row['data_inicio']);
    $end = new DateTime($row['data_fim']);

    $ocupados[] = [
        'start' => $start->getTimestamp(),
        'end' => $end->getTimestamp()
    ];
}

// 5. Filtrar Slots Inválidos
$slots_disponiveis = [];

foreach ($slots_candidatos as $slot) {
    $is_livre = true;

    foreach ($ocupados as $ocupado) {
        // Verificar sobreposição de horário
        // (StartA < EndB) and (EndA > StartB)
        if ($slot['timestamp_start'] < $ocupado['end'] && $slot['timestamp_end'] > $ocupado['start']) {
            $is_livre = false;
            break;
        }
    }

    if ($is_livre) {
        $slots_disponiveis[] = $slot['start'];
    }
}

echo json_encode($slots_disponiveis);
?>