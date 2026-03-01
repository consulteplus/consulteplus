<?php
require_once __DIR__ . '/../../../../config/config.php';
require_once __DIR__ . '/../../../../config/database.php';

// Verificação simples de sessão
session_start();
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'Acesso negado']);
    exit;
}

// Prevent PHP notices/warnings from breaking JSON
error_reporting(0);
ini_set('display_errors', 0);

header('Content-Type: application/json');

$acao = $_GET['acao'] ?? '';
$n8nConfigFile = __DIR__ . '/../../../../config/n8n_config.json';

// --- FUNÇÕES AUXILIARES ---

function getN8nConfig()
{
    global $n8nConfigFile;
    if (file_exists($n8nConfigFile)) {
        return json_decode(file_get_contents($n8nConfigFile), true);
    }
    return [];
}

// --- AÇÕES ---

if ($acao === 'salvar_config') {
    $input = json_decode(file_get_contents('php://input'), true);

    $config = getN8nConfig();
    $config['maps_webhook_url'] = $input['maps_webhook_url'] ?? '';
    $config['linkedin_webhook_url'] = $input['linkedin_webhook_url'] ?? '';
    $config['whatsapp_webhook_url'] = $input['whatsapp_webhook_url'] ?? '';
    $config['updated_at'] = date('Y-m-d H:i:s');

    if (file_put_contents($n8nConfigFile, json_encode($config, JSON_PRETTY_PRINT))) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Erro ao salvar arquivo de configuração']);
    }
    exit;
}

// --- Handler de Erros Fatais ---
register_shutdown_function(function () {
    $error = error_get_last();
    if ($error && ($error['type'] === E_ERROR || $error['type'] === E_PARSE || $error['type'] === E_CORE_ERROR || $error['type'] === E_COMPILE_ERROR)) {
        http_response_code(500);
        // Log removido
        echo json_encode(['success' => false, 'error' => 'Erro Fatal no Servidor: ' . $error['message']]);
        exit;
    }
});

if ($acao === 'trigger_scraping') {
    try {
        $input = json_decode(file_get_contents('php://input'), true);
        $source = $input['source'] ?? '';
        $config = getN8nConfig();

        $webhookUrl = '';
        if ($source === 'google_maps')
            $webhookUrl = $config['maps_webhook_url'] ?? '';
        else if ($source === 'linkedin')
            $webhookUrl = $config['linkedin_webhook_url'] ?? '';
        else if ($source === 'whatsapp')
            $webhookUrl = $config['whatsapp_webhook_url'] ?? '';

        if (empty($webhookUrl)) {
            echo json_encode(['success' => false, 'error' => 'Webhook URL não configurada para ' . $source]);
            exit;
        }

        // Payload Específico solicitado pelo usuário
        if ($source === 'google_maps') {
            $keyword = $input['keyword'] ?? '';
            $location = $input['location'] ?? '';
            // Formato: "Clínicas odontológicas em Teresina, Brasil"
            $termo = trim($keyword . ' em ' . $location);

            // Substituir payload completo para enviar apenas o esperado
            $input = [
                'termo_de_busca' => $termo,
                'triggered_by' => $_SESSION['user_id']
            ];
        } else {
            // Outros sources continuam padrão por enquanto
            $input['triggered_by'] = $_SESSION['user_id'];
        }

        // Tentar envio com CURL (MODO SÍNCRONO - Aguardar Resposta)
        // Aumentar limites para processar 9000+ leads
        set_time_limit(300);
        ini_set('memory_limit', '512M');

        $ch = curl_init($webhookUrl);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($input));
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 240); // 4 minutos de timeout no CURL
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Fix XAMPP Local

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);

        if ($httpCode >= 200 && $httpCode < 300 && $response) {
            // Salvar Backup da Resposta (Para debug e processamento posterior se falhar)
            $backupFile = __DIR__ . '/last_response.json';
            file_put_contents($backupFile, $response);

            $responseData = json_decode($response, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                // Log removido
                echo json_encode(['success' => false, 'error' => 'Erro ao decodificar JSON do n8n: ' . json_last_error_msg()]);
                exit;
            }

            // Processar os dados retornados IMEDIATAMENTE
            $leads = [];

            // Log removido

            // Normalização da Resposta
            $is_list = false;
            if (is_array($responseData)) {
                // Se tiver chave 0 numerica, assumimos que é uma lista indexada
                if (isset($responseData[0])) {
                    $is_list = true;
                }
            }

            if ($is_list) {
                $leads = $responseData;
                // Log removido
            } elseif (isset($responseData['leads']) && is_array($responseData['leads'])) {
                $leads = $responseData['leads'];
                // Log removido
            } elseif (isset($responseData['results']) && is_array($responseData['results'])) {
                $leads = $responseData['results'];
                // Log removido
            } elseif (isset($responseData['data']) && is_array($responseData['data'])) {
                $leads = $responseData['data'];
                // Log removido
            } elseif ($responseData) {
                // Objeto único
                $leads = [$responseData];
                // Log removido
            }

            // Salvar no Banco (Usando Transação para Performance)
            global $conn;
            $conn->begin_transaction();

            try {
                $stmt = $conn->prepare("INSERT INTO leads_scraped (source, nome, telefone, email, endereco, site, instagram, raw_data, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'pendente')");

                $count = 0;
                foreach ($leads as $lead) {
                    // Normalizar campos
                    $nome = $lead['nome'] ?? $lead['title'] ?? $lead['name'] ?? 'Sem Nome';
                    $telefone = $lead['telefone'] ?? $lead['phone'] ?? $lead['phone_number'] ?? null;
                    $email = $lead['email'] ?? null;
                    $endereco = $lead['endereco'] ?? $lead['address'] ?? $lead['full_address'] ?? null;
                    $site = $lead['site'] ?? $lead['website'] ?? null;
                    $instagram = $lead['instagram'] ?? null;
                    // Limit raw_data size just in case, though mediumtext handles it
                    $raw_data = json_encode($lead);

                    // Ignorar se não tiver nome (opcional)
                    if ($nome === 'Sem Nome' && !$telefone)
                        continue;

                    $stmt->bind_param('ssssssss', $source, $nome, $telefone, $email, $endereco, $site, $instagram, $raw_data);
                    if ($stmt->execute()) {
                        $count++;
                    }
                }

                $conn->commit();

                // Log removido

                $out = json_encode(['success' => true, 'message' => "Scraping concluído! $count leads processados.", 'count' => $count]);
                if ($out === false) {
                    // Log removido
                    echo json_encode(['success' => true, 'message' => "Processado ($count), mas falha ao gerar mensagem de retorno."]);
                } else {
                    http_response_code(200);
                    echo $out;
                }

            } catch (Exception $e) {
                $conn->rollback();
                // Log removido
                echo json_encode(['success' => false, 'error' => 'Erro ao salvar leads no banco: ' . $e->getMessage()]);
            }

        } else {
            echo json_encode(['success' => false, 'error' => 'Falha na comunicação com n8n (' . $httpCode . '): ' . ($curlError ?: 'Sem resposta')]);
        }
    } catch (Throwable $t) {
        // Log removido
        echo json_encode(['success' => false, 'error' => 'Erro interno: ' . $t->getMessage()]);
    }
    exit;
}

if ($acao === 'listar_resultados') {
    global $conn;
    $sql = "SELECT * FROM leads_scraped WHERE status = 'pendente' ORDER BY created_at DESC";
    $result = $conn->query($sql);

    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }

    echo json_encode(['data' => $data]); // DataTables expects 'data' key
    exit;
}

if ($acao === 'processar_leads') {
    $input = json_decode(file_get_contents('php://input'), true);
    $ids = $input['ids'] ?? [];
    $tipo = $input['tipo'] ?? ''; // 'aprovar' ou 'rejeitar'

    if (empty($ids) || !in_array($tipo, ['aprovar', 'rejeitar'])) {
        echo json_encode(['success' => false, 'error' => 'Parâmetros inválidos']);
        exit;
    }

    global $conn;
    $company_id = null;
    $session_company = $_SESSION['company_id'] ?? null;

    if (!empty($session_company)) {
        // Validate if company actually exists
        $stmtCheck = $conn->prepare("SELECT id FROM empresas WHERE id = ?");
        $stmtCheck->bind_param('i', $session_company);
        $stmtCheck->execute();
        $resCheck = $stmtCheck->get_result();
        if ($resCheck->num_rows > 0) {
            $company_id = $session_company;
        }
    }
    // If not valid or empty, $company_id remains null, which is now allowed by DB.

    $afetados = 0;

    if ($tipo === 'rejeitar') {
        $stmt = $conn->prepare("UPDATE leads_scraped SET status = 'rejeitado' WHERE id = ?");
        foreach ($ids as $id) {
            $stmt->bind_param('i', $id);
            if ($stmt->execute())
                $afetados++;
        }
    } elseif ($tipo === 'aprovar') {
        // Mover para tabela leads
        $stmtInsert = $conn->prepare("INSERT INTO leads (company_id, nome, email, telefone, origem, status, anotacoes) VALUES (?, ?, ?, ?, ?, 'novo', ?)");
        $stmtUpdate = $conn->prepare("UPDATE leads_scraped SET status = 'aprovado' WHERE id = ?");
        $stmtGet = $conn->prepare("SELECT * FROM leads_scraped WHERE id = ?");

        foreach ($ids as $id) {
            // Pegar dados do scraped
            $stmtGet->bind_param('i', $id);
            $stmtGet->execute();
            $res = $stmtGet->get_result();
            if ($lead = $res->fetch_assoc()) {
                // Inserir na tabela leads
                // Origem = Scraper + Source
                $origem = 'Scraper (' . $lead['source'] . ')';
                $notes = "Importado via Scraper. Dados brutos: " . substr($lead['raw_data'], 0, 500);

                $stmtInsert->bind_param(
                    'isssss',
                    $company_id,
                    $lead['nome'],
                    $lead['email'],
                    $lead['telefone'],
                    $origem,
                    $notes
                );

                if ($stmtInsert->execute()) {
                    // Atualizar status na scraped
                    $stmtUpdate->bind_param('i', $id);
                    $stmtUpdate->execute();
                    $afetados++;
                }
            }
        }
    }

    echo json_encode(['success' => true, 'afetados' => $afetados]);
    exit;
}

// --- EXTRAÇÃO WHATSAPP ---
if ($acao == 'extrair_whatsapp') {
    try {
        $input = json_decode(file_get_contents('php://input'), true);
        $groupId = $input['groupId'] ?? '';
        $groupName = $input['groupName'] ?? 'Whatsapp Group';

        if (!$groupId) {
            throw new Exception('ID do grupo não informado.');
        }

        // 1. Buscar Grupos na API
        $configFile = __DIR__ . '/../../../../config/uazapi_config.json';
        if (!file_exists($configFile)) {
            throw new Exception('Configuração Uazapi ausente.');
        }
        $config = json_decode(file_get_contents($configFile), true);
        $baseUrl = rtrim($config['base_url'], '/');
        $apiToken = $config['api_token'];

        // Call /group/list (POST)
        $ch = curl_init($baseUrl . '/group/list');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(['force_refresh' => true, 'noParticipants' => false]));
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json', 'token: ' . $apiToken]);
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $res = json_decode($response, true);

        if (!$res || (empty($res['groups']) && empty($res) && !isset($res[0]))) {
            $debugShort = substr($response, 0, 200);
            throw new Exception("Falha na API ($httpCode). Retorno: $debugShort");
        }

        // Normalizar lista de grupos
        $groupsList = [];
        if (isset($res['groups']))
            $groupsList = $res['groups'];
        elseif (isset($res['data']))
            $groupsList = $res['data'];
        elseif (is_array($res) && isset($res[0]['JID']))
            $groupsList = $res;
        else
            $groupsList = is_array($res) ? $res : [];

        // 2. Encontrar o grupo
        $targetGroup = null;
        foreach ($groupsList as $g) {
            $gId = $g['JID'] ?? $g['id'] ?? '';
            if ($gId == $groupId) {
                $targetGroup = $g;
                break;
            }
        }

        if (!$targetGroup) {
            throw new Exception("Grupo não encontrado na lista atualizada. ID: $groupId");
        }

        // 3. Processar Participantes
        $participants = $targetGroup['Participants'] ?? $targetGroup['participants'] ?? []; // Case sensitive fallback
        $count = 0;

        global $conn;

        // CORREÇÃO FINAL: NÃO VINCULAR À SESSÃO.
        // O usuário especificou que o lead não deve ser vinculado à empresa da sessão logada.
        // Como não temos a empresa de destino, deixamos NULL.
        $company_id = null;

        // Prepare Statements for Safety and Performance
        $stmtCheck = $conn->prepare("SELECT id FROM leads WHERE telefone = ?");
        if (!$stmtCheck)
            throw new Exception("DB Error Check: " . $conn->error);

        // Note: binding company_id (int or null). 
        $stmtInsert = $conn->prepare("INSERT INTO leads (company_id, nome, telefone, origem, status, anotacoes, created_at) VALUES (?, ?, ?, 'whatsapp', 'novo', ?, NOW())");
        if (!$stmtInsert)
            throw new Exception("DB Error Insert: " . $conn->error);

        foreach ($participants as $p) {
            $rawPhone = $p['PhoneNumber'] ?? $p['id'] ?? '';
            $phone = explode('@', $rawPhone)[0];

            if (empty($phone))
                continue;

            $name = trim($p['DisplayName'] ?? $p['PushName'] ?? '');
            if (empty($name) || $name === 'Contato WhatsApp') {
                $name = 'Wpp ' . substr($phone, -4);
            }

            $prompt = "Grupo: " . $groupName;

            // Check Duplicate
            $stmtCheck->bind_param('s', $phone);
            $stmtCheck->execute();
            $stmtCheck->store_result();

            if ($stmtCheck->num_rows == 0) {
                // Insert
                $cId = $company_id;
                // Fix: Use 's' for company_id to allow true NULL values.
                $stmtInsert->bind_param('ssss', $cId, $name, $phone, $prompt);
                if ($stmtInsert->execute()) {
                    $count++;
                }
            }
        }

        echo json_encode(['success' => true, 'message' => "Importação concluída!", 'imported' => $count, 'total_participants' => count($participants)]);

    } catch (Throwable $e) {
        http_response_code(200);
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
    exit;
}

echo json_encode(['success' => false, 'error' => 'Ação inválida']);
