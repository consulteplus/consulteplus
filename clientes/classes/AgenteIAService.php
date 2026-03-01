<?php

class AgenteIAService
{
    private $conn;

    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    /**
     * Listar Agentes com Filtros
     */
    public function listar($filtros = [], $companyId = null, $limit = 100)
    {
        $where = ["1=1"];
        $params = [];
        $types = "";

        if ($companyId !== null) {
            $where[] = "(company_id = ? OR company_id IS NULL)";
            $params[] = $companyId;
            $types .= "i";
        }

        if (!empty($filtros['tipo'])) {
            $where[] = "tipo = ?";
            $params[] = $filtros['tipo'];
            $types .= "s";
        }

        if (!empty($filtros['ativo'])) {
            $where[] = "ativo = ?";
            $params[] = $filtros['ativo'];
            $types .= "i";
        }

        $sql = "SELECT a.*, 
                (SELECT COUNT(*) FROM agentes_historico WHERE agente_id = a.id) as total_interacoes
                FROM agentes_ia a
                WHERE " . implode(" AND ", $where) . "
                ORDER BY a.criado_em DESC
                LIMIT ?";

        $params[] = $limit;
        $types .= "i";

        $stmt = $this->conn->prepare($sql);
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Buscar Agente por ID
     */
    public function buscarPorId($id, $companyId = null)
    {
        $sql = "SELECT a.*, 
                (SELECT COUNT(*) FROM agentes_historico WHERE agente_id = a.id) as total_interacoes,
                (SELECT SUM(tokens_usados) FROM agentes_historico WHERE agente_id = a.id) as total_tokens
                FROM agentes_ia a
                WHERE a.id = ?";

        if ($companyId !== null) {
            $sql .= " AND (a.company_id = ? OR a.company_id IS NULL)";
        }

        $stmt = $this->conn->prepare($sql);

        if ($companyId !== null) {
            $stmt->bind_param("ii", $id, $companyId);
        } else {
            $stmt->bind_param("i", $id);
        }

        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    /**
     * Criar Agente
     */
    public function criar($dados, $companyId = null, $usuarioId = null)
    {
        $sql = "INSERT INTO agentes_ia 
                (company_id, nome, descricao, tipo, prompt_sistema, temperatura, max_tokens, palavras_chave, modelo, ativo, criado_por, criado_em) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";

        $stmt = $this->conn->prepare($sql);

        $palavrasChave = isset($dados['palavras_chave']) ? json_encode($dados['palavras_chave']) : null;
        $temperatura = $dados['temperatura'] ?? 0.70;
        $maxTokens = $dados['max_tokens'] ?? 500;
        $modelo = $dados['modelo'] ?? 'gpt-4';
        $ativo = $dados['ativo'] ?? 1;

        $stmt->bind_param(
            "issssdissii",
            $companyId,
            $dados['nome'],
            $dados['descricao'],
            $dados['tipo'],
            $dados['prompt_sistema'],
            $temperatura,
            $maxTokens,
            $palavrasChave,
            $modelo,
            $ativo,
            $usuarioId
        );

        if ($stmt->execute()) {
            return [
                'action' => 'created',
                'agente_id' => $stmt->insert_id
            ];
        }

        throw new Exception($this->conn->error);
    }

    /**
     * Atualizar Agente
     */
    public function atualizar($id, $dados, $companyId = null)
    {
        $campos = [];
        $params = [];
        $types = "";

        if (isset($dados['nome'])) {
            $campos[] = "nome = ?";
            $params[] = $dados['nome'];
            $types .= "s";
        }

        if (isset($dados['descricao'])) {
            $campos[] = "descricao = ?";
            $params[] = $dados['descricao'];
            $types .= "s";
        }

        if (isset($dados['tipo'])) {
            $campos[] = "tipo = ?";
            $params[] = $dados['tipo'];
            $types .= "s";
        }

        if (isset($dados['prompt_sistema'])) {
            $campos[] = "prompt_sistema = ?";
            $params[] = $dados['prompt_sistema'];
            $types .= "s";
        }

        if (isset($dados['temperatura'])) {
            $campos[] = "temperatura = ?";
            $params[] = $dados['temperatura'];
            $types .= "d";
        }

        if (isset($dados['max_tokens'])) {
            $campos[] = "max_tokens = ?";
            $params[] = $dados['max_tokens'];
            $types .= "i";
        }

        if (isset($dados['palavras_chave'])) {
            $campos[] = "palavras_chave = ?";
            $params[] = json_encode($dados['palavras_chave']);
            $types .= "s";
        }

        if (isset($dados['modelo'])) {
            $campos[] = "modelo = ?";
            $params[] = $dados['modelo'];
            $types .= "s";
        }

        if (isset($dados['ativo'])) {
            $campos[] = "ativo = ?";
            $params[] = $dados['ativo'];
            $types .= "i";
        }

        if (empty($campos)) {
            return ['action' => 'no_changes', 'agente_id' => $id];
        }

        $campos[] = "atualizado_em = NOW()";

        $sql = "UPDATE agentes_ia SET " . implode(", ", $campos) . " WHERE id = ?";

        if ($companyId !== null) {
            $sql .= " AND (company_id = ? OR company_id IS NULL)";
            $params[] = $id;
            $params[] = $companyId;
            $types .= "ii";
        } else {
            $params[] = $id;
            $types .= "i";
        }

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param($types, ...$params);

        if ($stmt->execute()) {
            return [
                'action' => 'updated',
                'agente_id' => $id
            ];
        }

        throw new Exception($this->conn->error);
    }

    /**
     * Excluir Agente
     */
    public function excluir($id, $companyId = null)
    {
        $sql = "DELETE FROM agentes_ia WHERE id = ?";

        if ($companyId !== null) {
            $sql .= " AND (company_id = ? OR company_id IS NULL)";
        }

        $stmt = $this->conn->prepare($sql);

        if ($companyId !== null) {
            $stmt->bind_param("ii", $id, $companyId);
        } else {
            $stmt->bind_param("i", $id);
        }

        return $stmt->execute();
    }

    /**
     * Executar Agente (Produção)
     */
    public function executar($agenteId, $mensagem, $leadId = null, $usuarioId = null, $contexto = [])
    {
        $agente = $this->buscarPorId($agenteId);

        if (!$agente || !$agente['ativo']) {
            throw new Exception("Agente não encontrado ou inativo");
        }

        $inicio = microtime(true);

        try {
            // Chamar API de IA (OpenAI/Gemini)
            $resposta = $this->chamarIA($agente, $mensagem, $contexto);

            $tempoMs = (int) ((microtime(true) - $inicio) * 1000);

            // Salvar no histórico
            $this->salvarHistorico([
                'agente_id' => $agenteId,
                'lead_id' => $leadId,
                'usuario_id' => $usuarioId,
                'tipo_interacao' => 'producao',
                'mensagem' => $mensagem,
                'resposta' => $resposta['texto'],
                'tokens_usados' => $resposta['tokens'],
                'tempo_resposta_ms' => $tempoMs,
                'sucesso' => 1
            ]);

            return [
                'resposta' => $resposta['texto'],
                'tokens_usados' => $resposta['tokens'],
                'tempo_ms' => $tempoMs
            ];

        } catch (Exception $e) {
            // Salvar erro no histórico
            $this->salvarHistorico([
                'agente_id' => $agenteId,
                'lead_id' => $leadId,
                'usuario_id' => $usuarioId,
                'tipo_interacao' => 'producao',
                'mensagem' => $mensagem,
                'sucesso' => 0,
                'erro' => $e->getMessage()
            ]);

            throw $e;
        }
    }

    /**
     * Testar Agente (Não salva em produção)
     */
    public function testar($agenteId, $mensagem, $usuarioId = null)
    {
        $agente = $this->buscarPorId($agenteId);

        if (!$agente) {
            throw new Exception("Agente não encontrado");
        }

        $inicio = microtime(true);

        try {
            $resposta = $this->chamarIA($agente, $mensagem);
            $tempoMs = (int) ((microtime(true) - $inicio) * 1000);

            // Salvar como teste
            $this->salvarHistorico([
                'agente_id' => $agenteId,
                'usuario_id' => $usuarioId,
                'tipo_interacao' => 'teste',
                'mensagem' => $mensagem,
                'resposta' => $resposta['texto'],
                'tokens_usados' => $resposta['tokens'],
                'tempo_resposta_ms' => $tempoMs,
                'sucesso' => 1
            ]);

            return [
                'resposta' => $resposta['texto'],
                'tokens_usados' => $resposta['tokens'],
                'tempo_ms' => $tempoMs
            ];

        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Testar Simulacao (Envia JSON igual ao WhatsApp para o Webhook)
     */
    public function testarSimulacao($mensagem, $numeroTeste = '558699999999')
    {
        // Carregar URL do Webhook
        $configFile = __DIR__ . '/../config/n8n_config.json';
        if (!file_exists($configFile)) {
            throw new Exception("Arquivo de configuração do n8n não encontrado.");
        }
        $config = json_decode(file_get_contents($configFile), true);
        $url = $config['webhook_url'] ?? '';

        if (empty($url)) {
            throw new Exception("URL do Webhook principal não configurada.");
        }

        // Montar Payload (Apenas o corpo da mensagem real)
        $timestamp = time() * 1000; // Milissegundos
        $messageId = strtoupper(bin2hex(random_bytes(16)));
        $lid = "151921901977667@lid";
        $remoteJid = $numeroTeste . "@s.whatsapp.net";

        $payload = [
            "BaseUrl" => "https://consulteplus.uazapi.com",
            "EventType" => "messages",
            "chat" => [
                "chatbot_agentResetMemoryAt" => 0,
                "chatbot_disableUntil" => 0,
                "chatbot_lastTriggerAt" => 0,
                "chatbot_lastTrigger_id" => "",
                "id" => "",
                "image" => "",
                "imagePreview" => "",
                "lead_assignedAttendant_id" => "",
                "lead_email" => "",
                "lead_field01" => "",
                "lead_field02" => "",
                "lead_field03" => "",
                "lead_field04" => "",
                "lead_field05" => "",
                "lead_field06" => "",
                "lead_field07" => "",
                "lead_field08" => "",
                "lead_field09" => "",
                "lead_field10" => "",
                "lead_field11" => "",
                "lead_field12" => "",
                "lead_field13" => "",
                "lead_field14" => "",
                "lead_field15" => "",
                "lead_field16" => "",
                "lead_field17" => "",
                "lead_field18" => "",
                "lead_field19" => "",
                "lead_field20" => "",
                "lead_fullName" => "",
                "lead_isTicketOpen" => false,
                "lead_kanbanOrder" => 0,
                "lead_name" => "",
                "lead_notes" => "",
                "lead_personalid" => "",
                "lead_status" => "",
                "lead_tags" => [],
                "name" => "Usuario Teste",
                "owner" => "558695150152",
                "phone" => "+" . substr($numeroTeste, 0, 2) . " " . substr($numeroTeste, 2, 2) . " " . substr($numeroTeste, 4, 5) . "-" . substr($numeroTeste, 9),
                "wa_archived" => false,
                "wa_chatid" => $remoteJid,
                "wa_chatlid" => $lid,
                "wa_contactName" => "",
                "wa_ephemeralExpiration" => 0,
                "wa_fastid" => "558695150152:" . $numeroTeste,
                "wa_isBlocked" => false,
                "wa_isGroup" => false,
                "wa_isGroup_admin" => false,
                "wa_isGroup_announce" => false,
                "wa_isGroup_community" => false,
                "wa_isGroup_member" => false,
                "wa_isPinned" => false,
                "wa_label" => [],
                "wa_lastMessageSender" => $lid,
                "wa_lastMessageTextVote" => $mensagem,
                "wa_lastMessageType" => "Conversation",
                "wa_lastMsgTimestamp" => $timestamp,
                "wa_muteEndTime" => 0,
                "wa_name" => "Usuario Teste",
                "wa_unreadCount" => 1
            ],
            "chatSource" => "updated",
            "instanceName" => "fatepi",
            "message" => [
                "buttonOrListid" => "",
                "chatid" => $remoteJid,
                "chatlid" => $lid,
                "content" => $mensagem,
                "convertOptions" => "",
                "edited" => "",
                "fromMe" => false,
                "groupName" => "Unknown",
                "id" => "558695150152:" . $messageId,
                "isGroup" => false,
                "mediaType" => "",
                "messageTimestamp" => $timestamp,
                "messageType" => "Conversation",
                "messageid" => $messageId,
                "owner" => "558695150152",
                "quoted" => "",
                "reaction" => "",
                "sender" => $lid,
                "senderName" => "Usuario Teste",
                "sender_lid" => $lid,
                "sender_pn" => $remoteJid,
                "source" => "android",
                "status" => "",
                "text" => $mensagem,
                "track_id" => "",
                "track_source" => "",
                "type" => "text",
                "vote" => "",
                "wasSentByApi" => false
            ],
            "owner" => "558695150152",
            "token" => "81317ad5-b564-46a1-bda7-de2de7c1e970"
        ];

        // Enviar para o n8n
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([$payload])); // Envia array com 1 objeto, conforme visto no exemplo body: [ { ... } ]
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        // Desabilitar verificação SSL para evitar erro de certificado local no XAMPP/Windows
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);

        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'User-Agent: uazapiGO-Webhook/1.0', // Simulando o User-Agent real
            'Accept: */*'
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        if ($error) {
            throw new Exception("Erro no cURL: " . $error);
        }

        if ($httpCode >= 400) {
            throw new Exception("Erro HTTP $httpCode: " . $response);
        }

        // Tentar decodificar resposta JSON se houver
        $jsonResponse = json_decode($response, true);

        return [
            'success' => true,
            'sent_payload' => [$payload],
            'response_raw' => $response,
            'response_json' => $jsonResponse
        ];
    }

    private function chamarIA($agente, $mensagem, $contexto = [])
    {
        // TODO: Implementar integração com OpenAI/Gemini
        // Por enquanto, retorno simulado

        return [
            'texto' => "Resposta simulada do agente: " . $agente['nome'] . "\nMensagem recebida: " . $mensagem,
            'tokens' => 50
        ];
    }

    /**
     * Salvar no Histórico
     */
    private function salvarHistorico($dados)
    {
        $sql = "INSERT INTO agentes_historico 
                (agente_id, lead_id, usuario_id, tipo_interacao, mensagem, resposta, tokens_usados, tempo_resposta_ms, sucesso, erro, criado_em) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param(
            "iiisssiiis",
            $dados['agente_id'],
            $dados['lead_id'],
            $dados['usuario_id'],
            $dados['tipo_interacao'],
            $dados['mensagem'],
            $dados['resposta'] ?? null,
            $dados['tokens_usados'] ?? null,
            $dados['tempo_resposta_ms'] ?? null,
            $dados['sucesso'],
            $dados['erro'] ?? null
        );

        return $stmt->execute();
    }

    /**
     * Buscar Histórico
     */
    public function buscarHistorico($agenteId, $limit = 50, $tipo = null)
    {
        $sql = "SELECT h.*, 
                l.nome as lead_nome
                FROM agentes_historico h
                LEFT JOIN leads l ON h.lead_id = l.id
                WHERE h.agente_id = ?";

        $params = [$agenteId];
        $types = "i";

        if ($tipo) {
            $sql .= " AND h.tipo_interacao = ?";
            $params[] = $tipo;
            $types .= "s";
        }

        $sql .= " ORDER BY h.criado_em DESC LIMIT ?";
        $params[] = $limit;
        $types .= "i";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param($types, ...$params);
        $stmt->execute();

        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
}
