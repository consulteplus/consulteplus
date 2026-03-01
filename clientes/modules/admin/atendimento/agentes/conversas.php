<?php
require_once __DIR__ . '/../../../../config/config.php';
require_once __DIR__ . '/../../../../classes/SupabaseClient.php';
require_once __DIR__ . '/../../../../classes/ApiResponse.php';

header('Content-Type: application/json');

try {
    $acao = $_GET['acao'] ?? '';

    // Verificar se chave do Supabase está configurada
    if (SUPABASE_KEY === 'MINHA_CHAVE_AQUI' || empty(SUPABASE_KEY)) {
        throw new Exception("Chave do Supabase não configurada no config.php");
    }

    $client = new SupabaseClient(SUPABASE_URL, SUPABASE_KEY);

    if ($acao === 'listar') {
        $chats = [];

        // Tentar buscar com JOIN primeiro
        try {
            $chats = $client->get('chats', [
                'select' => '*, leads_fatepi!inner(nome_completo, curso_interesse)',
                'order' => 'created_at.desc'
            ]);
        } catch (Exception $e) {
            // Se falhar (provavelmente sem FK), busca normal
            $chats = $client->get('chats', [
                'select' => '*',
                'order' => 'created_at.desc'
            ]);
        }

        // Se ainda assim der erro (ex: tabela não existe)
        if (isset($chats['code']) || isset($chats['error'])) {
            throw new Exception("Erro ao buscar chats: " . json_encode($chats));
        }

        // Se não veio leads_fatepi no objeto (porque caiu no catch), vamos buscar manualmente
        // Coletar telefones para buscar nomes
        $phones = [];
        foreach ($chats as $chat) {
            if (!empty($chat['phone'])) {
                // Limpar telefone para garantir match
                $phone = preg_replace('/[^0-9]/', '', $chat['phone']);
                // Supabase às vezes guarda com +55 ou sem. Vamos tentar buscar exato por enquanto.
                // Idealmente, normalizar tudo. Assumindo que o banco guarda texto exato.
                $phones[] = $chat['phone'];
            }
        }
        $phones = array_unique($phones);

        $leadsMap = [];
        if (!empty($phones)) {
            // Buscar leads que tenham esses telefones
            // Nota: "in" syntax: telefone=in.(123,456)
            // Precisamos encodar os valores corretamente para URL
            $phonesStr = implode(',', array_map(function ($p) {
                return '"' . $p . '"';
            }, $phones));

            try {
                $leads = $client->get('leads_fatepi', [
                    'select' => 'telefone, nome_completo, curso_interesse',
                    'telefone' => 'in.(' . $phonesStr . ')'
                ]);

                if (is_array($leads)) {
                    foreach ($leads as $lead) {
                        $leadsMap[$lead['telefone']] = $lead;
                    }
                }
            } catch (Exception $e) {
                // Ignorar erro na busca de leads secundária
            }
        }

        // Buscar última mensagem e associar lead
        foreach ($chats as &$chat) {
            // Associar Lead Manualmente se não veio do join
            if (!isset($chat['leads_fatepi']) && isset($chat['phone']) && isset($leadsMap[$chat['phone']])) {
                $chat['leads_fatepi'] = $leadsMap[$chat['phone']];
            }

            // Buscar última mensagem
            try {
                $msgs = $client->get('chat_messages', [
                    'select' => '*',
                    'conversation_id' => 'eq.' . $chat['conversation_id'],
                    'order' => 'created_at.desc',
                    'limit' => 1
                ]);
                $chat['last_message'] = $msgs[0] ?? null;
            } catch (Exception $e) {
                $chat['last_message'] = null;
            }
        }

        ApiResponse::success(['conversas' => $chats]);
    }

    if ($acao === 'mensagens') {
        $conversationId = $_GET['conversation_id'] ?? '';

        if (empty($conversationId)) {
            ApiResponse::error("ID da conversa é obrigatório", 400);
        }

        // 1. Buscar Mensagens
        $mensagens = $client->get('chat_messages', [
            'select' => '*',
            'conversation_id' => 'eq.' . $conversationId,
            'order' => 'created_at.asc'
        ]);

        // 2. Buscar Logs de Status
        $logs = [];
        try {
            $logs = $client->get('chat_logs', [
                'select' => '*',
                'conversation_id' => 'eq.' . $conversationId,
                'order' => 'created_at.asc'
            ]);
        } catch (Exception $e) {
            // Logs table might not exist or empty
        }

        // 3. Normalizar e Mesclar
        $timeline = [];

        if (is_array($mensagens)) {
            foreach ($mensagens as $msg) {
                $msg['type'] = 'message';
                $timeline[] = $msg;
            }
        }

        if (is_array($logs)) {
            foreach ($logs as $log) {
                // Criar um pseudo-objeto de mensagem para o log
                $timeline[] = [
                    'id' => 'log_' . $log['id'],
                    'conversation_id' => $log['conversation_id'],
                    'created_at' => $log['created_at'],
                    'type' => 'log',
                    'status_anterior' => $log['status_anterior'],
                    'status_novo' => $log['status_novo'],
                    'alterado_por' => $log['alterado_por']
                ];
            }
        }

        // 4. Ordenar por data
        usort($timeline, function ($a, $b) {
            return strtotime($a['created_at']) - strtotime($b['created_at']);
        });

        ApiResponse::success(['mensagens' => $timeline]);
    }

    if ($acao === 'atualizar_status') {
        $data = json_decode(file_get_contents('php://input'), true);
        $conversationId = $data['conversation_id'] ?? '';
        $novoStatus = $data['novo_status'] ?? ''; // 'robo', 'humano', 'finalizado'

        if (empty($conversationId) || empty($novoStatus)) {
            ApiResponse::error("ID e Status são obrigatórios", 400);
        }

        // Validar status permitidos
        $permitidos = ['robo', 'humano', 'finalizado'];
        if (!in_array($novoStatus, $permitidos)) {
            ApiResponse::error("Status inválido", 400);
        }

        // Atualizar no Supabase
        // Note: O trigger no banco vai criar o log automaticamente
        try {
            $update = $client->update(
                'chats',
                ['status' => $novoStatus],
                ['conversation_id' => 'eq.' . $conversationId]
            );

            // Supabase update returns array of updated rows usually
            if (isset($update['code'])) {
                throw new Exception($update['message'] ?? 'Erro ao atualizar');
            }

            ApiResponse::success([], "Status atualizado para $novoStatus");

        } catch (Exception $e) {
            ApiResponse::error("Erro ao atualizar status: " . $e->getMessage(), 500);
        }
    }

    ApiResponse::error("Ação inválida", 400);

} catch (Exception $e) {
    ApiResponse::error($e->getMessage(), 500);
}
