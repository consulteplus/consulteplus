<?php
// crm/acoes.php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../classes/CrmService.php';
require_once __DIR__ . '/../../classes/SupabaseClient.php';

// Configurações de erro JSON-safe
ini_set('display_errors', 0);
error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);
header('Content-Type: application/json');

// Verificar sessão
if (!isset($_SESSION['user_id'])) {
    ApiResponse::error("Não autorizado", 401);
}

try {
    $crmService = new CrmService($conn);
    $company_id = getCompanyId();
    $user_id = $_SESSION['user_id'];

    // Ler Input JSON ou GET
    $json = file_get_contents('php://input');
    $data = json_decode($json, true);
    $acao = $_GET['acao'] ?? '';

    // Roteamento de Ações
    switch ($acao) {
        case 'listar_negocios':
            $filtros = [
                'etapa_id' => $_GET['etapa_id'] ?? 0,
                'busca' => $_GET['busca'] ?? '',
                'responsavel' => $_GET['responsavel'] ?? 0,
                'origem' => $_GET['origem'] ?? '',
                'funil' => $_GET['funil'] ?? 0
            ];
            $offset = (int) ($_GET['offset'] ?? 0);

            // Adapter para campos antigos vs novos
            $filtrosService = [
                'etapa_id' => $filtros['etapa_id'],
                'busca' => $filtros['busca'],
                'responsavel_id' => $filtros['responsavel'],
                'origem' => $filtros['origem'],
                'funil_id' => $filtros['funil']
            ];

            $resultado = $crmService->listarNegocios($filtrosService, $company_id, 10, $offset);

            // Renderizar Cards HTML (Mantendo compatibilidade com frontend atual)
            $cards_html = "";
            foreach ($resultado['deals'] as $deal) {
                $cards_html .= renderCard($deal);
            }

            ApiResponse::success([
                'cards' => $cards_html,
                'has_more' => $resultado['has_more']
            ]);
            break;

        case 'mover_etapa':
            if (empty($data['deal_id']) || empty($data['etapa_id']))
                ApiResponse::error("Dados incompletos");

            $crmService->moverEtapa($data['deal_id'], $data['etapa_id'], $company_id, $user_id);
            ApiResponse::success([], "Negócio movido com sucesso");
            break;

        case 'criar_negocio':
            $id = $crmService->criarNegocio($data, $company_id, $user_id);
            ApiResponse::created(['id' => $id], "Negócio criado com sucesso");
            break;

        case 'editar_negocio':
            $crmService->atualizarNegocio($data['id'], $data, $company_id);
            ApiResponse::success([], "Negócio atualizado");
            break;

        case 'buscar_contatos':
            $termo = $_GET['q'] ?? $_GET['termo'] ?? $_GET['busca'] ?? '';
            $tipo = $_GET['tipo'] ?? '';

            if (strlen($termo) < 2) {
                ApiResponse::success([]);
            }

            if ($tipo === 'empresa') {
                $resultados = $crmService->buscarEmpresas($termo, $company_id);
            } else {
                // Modified behavior: Search Leads instead of System Users (Clients)
                // If you want to search ONLY leads:
                $resultados = $crmService->buscarLeads($termo);

                // If you want to merge (search both):
                // $users = $crmService->buscarUsuarios($termo, $company_id);
                // $leads = $crmService->buscarLeads($termo);
                // $resultados = array_merge($users, $leads);
            }
            ApiResponse::success($resultados);
            break;

        case 'obter_negocio':
            $deal = $crmService->buscarNegocio($_GET['id'], $company_id);
            if (!$deal)
                ApiResponse::error("Negócio não encontrado", 404);

            $timeline = $crmService->listarAtividades($_GET['id'], $company_id);

            ApiResponse::success(['deal' => $deal, 'timeline' => $timeline]);
            break;

        case 'buscar_chat_negocio':
            $negocioId = isset($_GET['negocio_id']) ? (int) $_GET['negocio_id'] : 0;

            if (!$negocioId)
                ApiResponse::error('ID do negócio obrigatório', 400);

            $negocio = $crmService->buscarNegocio($negocioId, $company_id);

            if (!$negocio)
                ApiResponse::error('Negócio não encontrado', 404);

            // Identificar Telefone
            $telefone = null;
            $nome = $negocio['cliente_nome'];

            // Se tem Lead ID
            if (!empty($negocio['lead_id'])) {
                $stmt = $conn->prepare("SELECT telefone, nome FROM leads WHERE id = ?");
                $stmt->bind_param("i", $negocio['lead_id']);
                $stmt->execute();
                $res = $stmt->get_result();
                if ($l = $res->fetch_assoc()) {
                    $telefone = $l['telefone'];
                    $nome = $l['nome']; // Atualiza com nome do lead
                }
            }
            // Fallback: Cliente tabela users/clientes antigos (se aplicável)
            elseif (!empty($negocio['cliente_id'])) {
                // Implementar se necessário, por enquanto foca em Lead
            }

            if (empty($telefone)) {
                ApiResponse::success(['encontrado' => false, 'motivo' => 'Sem telefone vinculado']);
            }

            // Normalizar telefone
            $phoneClean = preg_replace('/[^0-9]/', '', $telefone);

            try {
                if (!defined('SUPABASE_URL') || !defined('SUPABASE_KEY')) {
                    throw new Exception("Supabase não configurado.");
                }

                $client = new SupabaseClient(SUPABASE_URL, SUPABASE_KEY);

                // 1. Buscar Conversas (Todas)
                $chats = $client->get('chats', [
                    'select' => '*,resumo', // Força trazer a coluna resumo
                    'phone' => 'eq.' . $phoneClean,
                    'order' => 'created_at.desc' // Latest first
                ]);

                if (empty($chats)) {
                    ApiResponse::success(['encontrado' => false, 'motivo' => 'Nenhuma conversa iniciada']);
                }

                // Coletar IDs de todas as conversas
                $ids = array_map(function ($c) {
                    return $c['conversation_id'];
                }, $chats);
                // Filtrar IDs para garantir formato correto (se UUID, ok, se int, ok)
                $idsStr = 'in.(' . implode(',', $ids) . ')';

                // Pega o chat mais recente como referência de status principal
                $chat = $chats[0];

                // INJECT START LOGS: Garante que todo chat apareça na timeline
                $timeline = [];
                $chatSummaries = []; // Map conversation_id -> resumo

                foreach ($chats as $c) {
                    $chatSummaries[$c['conversation_id']] = isset($c['resumo']) ? $c['resumo'] : null;

                    $timeline[] = [
                        'id' => 'start_' . $c['conversation_id'],
                        'created_at' => $c['created_at'],
                        'type' => 'log',
                        'status_anterior' => '',
                        'status_novo' => 'Iniciado',
                        'alterado_por' => 'Sistema',
                        'conversation_id' => $c['conversation_id'],
                        'is_start' => true
                    ];
                }

                // 2. Buscar Mensagens (De todas as conversas)
                $mensagens = $client->get('chat_messages', [
                    'select' => '*',
                    'conversation_id' => $idsStr,
                    'order' => 'created_at.asc'
                ]);

                // 3. Buscar Logs (De todas as conversas)
                $logs = [];
                try {
                    $logs = $client->get('chat_logs', [
                        'select' => '*',
                        'conversation_id' => $idsStr,
                        'order' => 'created_at.asc'
                    ]);
                } catch (Exception $e) {
                }

                // 4. Mesclar
                // $timeline = []; // Mantém os logs de início inseridos acima
                if (is_array($mensagens)) {
                    foreach ($mensagens as $m) {
                        $m['type'] = 'message';
                        $timeline[] = $m;
                    }
                }
                if (is_array($logs)) {
                    foreach ($logs as $l) {
                        $timeline[] = [
                            'id' => 'log_' . $l['id'],
                            'created_at' => $l['created_at'],
                            'type' => 'log',
                            'status_anterior' => $l['status_anterior'],
                            'status_novo' => $l['status_novo'],
                            'alterado_por' => $l['alterado_por'],
                            'conversation_id' => $l['conversation_id']
                        ];
                    }
                }

                usort($timeline, function ($a, $b) {
                    return strtotime($a['created_at']) - strtotime($b['created_at']);
                });

                ApiResponse::success([
                    'encontrado' => true,
                    'chat' => $chat,
                    'timeline' => $timeline,
                    'cliente_nome' => $nome,
                    'total_chats' => count($chats),
                    'chat_ids' => $ids,
                    'chat_summaries' => $chatSummaries
                ]);

            } catch (Exception $e) {
                ApiResponse::error("Erro Supabase: " . $e->getMessage());
            }
            break;

        // --- FUNIS ---
        case 'criar_funil':
            $nome = $data['nome'] ?? '';
            $desc = $data['descricao'] ?? '';
            if (!$nome)
                ApiResponse::error("Nome obrigatório");
            $id = $crmService->criarFunil($company_id, $nome, $desc);
            ApiResponse::success(['id' => $id]);
            break;

        case 'editar_funil':
            $id = $data['id'] ?? 0;
            $nome = $data['nome'] ?? '';
            $desc = $data['descricao'] ?? '';
            if (!$id || !$nome)
                ApiResponse::error("Dados inválidos");
            $crmService->editarFunil($id, $company_id, $nome, $desc);
            ApiResponse::success([]);
            break;

        case 'excluir_funil':
            $id = $data['id'] ?? 0;
            if (!$id)
                ApiResponse::error("ID inválido");
            $crmService->excluirFunil($id, $company_id);
            ApiResponse::success([]);
            break;

        // --- ETAPAS ---
        case 'criar_etapa':
            $funil_id = $data['funil_id'] ?? 0;
            $nome = $data['nome'] ?? '';
            $ordem = $data['ordem'] ?? 0;
            $cor = $data['cor'] ?? '#6c757d';
            if (!$funil_id || !$nome)
                ApiResponse::error("Dados inválidos");
            $id = $crmService->criarEtapa($company_id, $funil_id, $nome, $ordem, $cor);
            ApiResponse::success(['id' => $id]);
            break;

        case 'editar_etapa':
            $id = $data['id'] ?? 0;
            $nome = $data['nome'] ?? '';
            $ordem = $data['ordem'] ?? 0;
            $cor = $data['cor'] ?? '#6c757d';
            if (!$id || !$nome)
                ApiResponse::error("Dados inválidos");
            $crmService->editarEtapa($id, $company_id, $nome, $ordem, $cor);
            ApiResponse::success([]);
            break;

        case 'excluir_etapa':
            $id = $data['id'] ?? 0;
            if (!$id)
                ApiResponse::error("ID inválido");
            $crmService->excluirEtapa($id, $company_id);
            ApiResponse::success([]);
            break;

        case 'reordenar_etapas':
            $etapas = $data['etapas'] ?? [];
            if (empty($etapas))
                ApiResponse::success([]);
            $crmService->reordenarEtapas($etapas, $company_id);
            ApiResponse::success([]);
            break;

        // --- OUTROS ---
        case 'salvar_anotacao':
            $deal_id = $data['negocio_id'] ?? 0;
            $texto = $data['descricao'] ?? '';
            $tipo = $data['tipo'] ?? 'nota';
            if (!$deal_id || !$texto)
                ApiResponse::error("Dados inválidos");
            $crmService->salvarAnotacao($company_id, $deal_id, $user_id, $tipo, $texto);
            ApiResponse::success([]);
            break;

        case 'vincular_empresa':
            $deal_id = $data['negocio_id'] ?? 0;
            $empresa_id = $data['empresa_id'] ?? 0;
            if (!$deal_id || !$empresa_id)
                ApiResponse::error("Dados inválidos");
            $crmService->vincularEmpresa($company_id, $deal_id, $empresa_id, $user_id);
            ApiResponse::success([]);
            break;

        case 'excluir_negocio':
            $id = $data['id'] ?? 0;
            if (!$id)
                ApiResponse::error("ID inválido");
            $crmService->excluirNegocio($id, $company_id);
            ApiResponse::success([]);
            break;

        default:
            ApiResponse::error("Ação não implementada: $acao", 501);
    }

} catch (Exception $e) {
    // Log do erro
    if (class_exists('Logger')) {
        Logger::error("Erro no CRM (acoes.php): " . $e->getMessage(), ['trace' => $e->getTraceAsString(), 'request' => $_REQUEST]);
    }
    ApiResponse::error($e->getMessage(), 400);
}

// Helper para renderizar o Card HTML (pode ser movido para um template user function depois)
function renderCard($deal)
{
    $titulo = htmlspecialchars($deal['titulo']);
    $cliente = htmlspecialchars($deal['cliente_nome'] ?? '');
    $valorFmt = number_format($deal['valor_estimado'], 2, ',', '.');

    $borderClass = $deal['valor_estimado'] > 1000 ? 'card-hot' : 'card-cold';
    if ($deal['status'] == 'perdido')
        $borderClass .= ' opacity-75';

    $statusBadge = "";
    if ($deal['status'] == 'perdido')
        $statusBadge = "<span class='badge bg-danger ms-1' style='font-size: 0.65rem;'>PERDIDO</span>";
    elseif ($deal['status'] == 'ganho')
        $statusBadge = "<span class='badge bg-success ms-1' style='font-size: 0.65rem;'>GANHO</span>";

    $userAvatar = "";
    if (!empty($deal['responsavel_nome'])) {
        $initial = strtoupper(substr($deal['responsavel_nome'], 0, 1));
        $userAvatar = "<div class='user-avatar' title='{$deal['responsavel_nome']}'>{$initial}</div>";
    }

    // URL Base precisa ser ajustada ou pega via config, assumindo relativa por enquanto ou global
    // Melhor usar concatenacao simples se BASE_URL estiver disponivel no global scope (config.php)
    $baseUrl = defined('BASE_URL') ? BASE_URL : '/consulteplus/';

    return "
    <div class='kanban-card {$borderClass}' draggable='true' ondragstart='drag(event, {$deal['id']})' onclick=\"location.href='{$baseUrl}crm/detalhes.php?id={$deal['id']}'\">
        <div class='d-flex justify-content-between align-items-start'>
            <div class='card-title text-truncate' title='{$titulo}'>{$titulo}</div>
            <div class='dropdown' onclick='event.stopPropagation()'>
                <button class='btn btn-link btn-sm p-0 text-muted' data-bs-toggle='dropdown'><i class='bi bi-three-dots'></i></button>
                <ul class='dropdown-menu dropdown-menu-end'>
                    <li><a class='dropdown-item' href='{$baseUrl}crm/detalhes.php?id={$deal['id']}'>Editar</a></li>
                    <li><a class='dropdown-item text-success' href='#' onclick='marcarGanho({$deal['id']})'>Ganho</a></li>
                    <li><a class='dropdown-item text-danger' href='#' onclick='abrirModalPerdido({$deal['id']})'>Perdido</a></li>
                </ul>
            </div>
        </div>
        " . ($cliente ? "<div class='card-subtitle'><i class='bi bi-person'></i> {$cliente}</div>" : "") . "
        <div class='d-flex justify-content-between align-items-end mt-2'>
            <div>
                <div class='card-value'>R$ {$valorFmt}</div>
                {$statusBadge}
            </div>
            {$userAvatar}
        </div>
    </div>";
}
