<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/database.php';

// Função helper para limpar CPF/Tel
function justNumbers($str)
{
    return preg_replace('/[^0-9]/', '', $str);
}

// Receber JSON
$input = json_decode(file_get_contents('php://input'), true);

if (!$input) {
    http_response_code(400);
    echo json_encode(['error' => 'Dados inválidos.']);
    exit;
}

// Validar Inputs
$profissional_id = $input['profissional_id'] ?? null;
$procedimento_id = $input['procedimento_id'] ?? null;
$data_hora = $input['data_hora'] ?? null;
$nome = sanitize($input['nome'] ?? '');
$cpf = sanitize($input['cpf'] ?? '');
$telefone = sanitize($input['telefone'] ?? '');

if (!$profissional_id || !$procedimento_id || !$data_hora || !$nome || !$telefone) {
    http_response_code(400);
    echo json_encode(['error' => 'Todos os campos são obrigatórios.']);
    exit;
}

// 1. Obter duração do procedimento
$stmt = $conn->prepare("SELECT duracao_minutos FROM tipos_procedimento WHERE id = ?");
$stmt->bind_param("i", $procedimento_id);
$stmt->execute();
$proc = $stmt->get_result()->fetch_assoc();

if (!$proc) {
    http_response_code(404);
    echo json_encode(['error' => 'Procedimento não encontrado.']);
    exit;
}

// 2. Verificar/Cadastrar Paciente
$clean_cpf = justNumbers($cpf);
$clean_tel = justNumbers($telefone);
$paciente_id = null;

// Tenta buscar por CPF (se valido e tem tamanho razoavel)
if (!empty($clean_cpf) && strlen($clean_cpf) >= 11) {
    // Busca flexível: compara CPF limpo com CPF limpo do banco (via REPLACE)
    $stmt = $conn->prepare("SELECT id FROM pacientes WHERE REPLACE(REPLACE(REPLACE(cpf, '.', ''), '-', ''), ' ', '') = ? LIMIT 1");
    $stmt->bind_param("s", $clean_cpf);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($row = $res->fetch_assoc()) {
        $paciente_id = $row['id'];
    }
}

// Se não achou por CPF, tenta telefone (se valido)
if (!$paciente_id && !empty($clean_tel)) {
    // Busca flexível por telefone (ultimos 8 digitos)
    $stmt = $conn->prepare("SELECT id FROM pacientes WHERE REPLACE(REPLACE(REPLACE(REPLACE(telefone, '(', ''), ')', ''), '-', ''), ' ', '') LIKE ? LIMIT 1");
    $like_tel = '%' . substr($clean_tel, -8);
    $stmt->bind_param("s", $like_tel);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($row = $res->fetch_assoc()) {
        $paciente_id = $row['id'];
    }
}

if (!$paciente_id) {
    // Novo Paciente
    $stmt = $conn->prepare("INSERT INTO pacientes (nome, cpf, telefone, origem_cadastro) VALUES (?, ?, ?, 'online')");
    $stmt->bind_param("sss", $nome, $cpf, $telefone);
    if (!$stmt->execute()) {
        http_response_code(500);
        if ($conn->errno == 1062) {
            echo json_encode(['error' => 'Já existe um paciente cadastrado com este CPF, mas houve um erro ao recuperá-lo. Por favor, contate a recepção.']);
        } else {
            echo json_encode(['error' => 'Erro ao cadastrar paciente: ' . $stmt->error]);
        }
        exit;
    }
    $paciente_id = $conn->insert_id;
}


// 3. Verificar Disponibilidade (Double Check)
// É crucial verificar novamente se o horário ainda está livre no milésimo de segundo antes de salvar
$data_hora_obj = new DateTime($data_hora);
$data_hora_fim = clone $data_hora_obj;
$duracao = (int) $proc['duracao_minutos'];
$data_hora_fim->modify("+{$duracao} minutes");

$query_check = "
    SELECT id FROM agendamentos 
    WHERE profissional_id = ? 
    AND status != 'cancelado' 
    AND (
        (data_hora < ? AND DATE_ADD(data_hora, INTERVAL duracao_minutos MINUTE) > ?)
    )
    LIMIT 1
";
// Parametros: prof_id, fim_novo, inicio_novo
$fim_str = $data_hora_fim->format('Y-m-d H:i:s');
$inicio_str = $data_hora_obj->format('Y-m-d H:i:s');

$stmt = $conn->prepare($query_check);
$stmt->bind_param("iss", $profissional_id, $fim_str, $inicio_str);
$stmt->execute();

if ($stmt->get_result()->num_rows > 0) {
    http_response_code(409); // Conflict
    echo json_encode(['error' => 'Desculpe, este horário acabou de ser ocupado. Por favor, escolha outro.']);
    exit;
}

// 4. Salvar Agendamento
// Definimos um usuário 'sistema' (id 1 admin) como criador por enquanto, ou NULL se a coluna permitir
// Como a coluna created_by é NOT NULL, vamos usar o ID 1 (Admin) como placeholder do sistema.
$created_by = 1;
$status = 'agendado'; // Poderia ser 'pendente' se tivéssemos fluxo de aprovação
$sala_id = 1; // IMPORTANTE: Precisamos de uma lógica de sala. Por enquanto, hardcoded sala 1 ou pegar da alocação.
// Melhoria: Buscar sala da alocação correspondente ao horário
$dia_semana_map = [
    'Sun' => 'domingo',
    'Mon' => 'segunda',
    'Tue' => 'terca',
    'Wed' => 'quarta',
    'Thu' => 'quinta',
    'Fri' => 'sexta',
    'Sat' => 'sabado'
];
$dia_semana_en = $data_hora_obj->format('D');
$dia_semana = $dia_semana_map[$dia_semana_en];
$hora_inicio = $data_hora_obj->format('H:i:s');

$stmt_sala = $conn->prepare("
    SELECT sala_id FROM alocacao_salas 
    WHERE profissional_id = ? AND dia_semana = ? AND ativo = 1 
    AND hora_inicio <= ? AND hora_fim >= ? 
    LIMIT 1
");
$stmt_sala->bind_param("isss", $profissional_id, $dia_semana, $hora_inicio, $hora_inicio);
$stmt_sala->execute();
$sala_res = $stmt_sala->get_result();
if ($row_sala = $sala_res->fetch_assoc()) {
    $sala_id = $row_sala['sala_id'];
}

$stmt = $conn->prepare("
    INSERT INTO agendamentos 
    (paciente_id, profissional_id, sala_id, tipo_procedimento_id, data_hora, duracao_minutos, status, observacoes, created_by, origem) 
    VALUES (?, ?, ?, ?, ?, ?, ?, 'Agendamento Online', ?, 'online')
");
// Nota: Removi campo VALOR do insert pois não existe na tabela tipos_procedimento, logo não temos valor base.

$stmt->bind_param("iiiissss", $paciente_id, $profissional_id, $sala_id, $procedimento_id, $inicio_str, $duracao, $status, $created_by);

if ($stmt->execute()) {
    $agendamento_id = $conn->insert_id;

    // TODO: Disparar Notification (n8n/WhatsApp) de confirmação aqui

    echo json_encode([
        'success' => true,
        'id' => $agendamento_id,
        'mensagem' => 'Agendamento realizado com sucesso!'
    ]);
} else {
    http_response_code(500);
    echo json_encode(['error' => 'Erro ao salvar agendamento: ' . $stmt->error]);
}
?>