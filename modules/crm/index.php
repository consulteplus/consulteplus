<?php
$pageTitle = "Pipeline de Vendas";
require_once __DIR__ . '/../../includes/header.php';

if (!isModuleEnabled('crm')) {
    redirect('dashboard?modulo_bloqueado=crm');
    exit;
}
require_once __DIR__ . '/../../classes/CrmService.php';

checkPermission(['admin', 'cliente']);

$crmService = new CrmService($conn);
$company_id = getCompanyId();

// 1. Identificar View e Funil
$view = $_GET['view'] ?? 'quadro';
if (!in_array($view, ['quadro', 'lista', 'calendario']))
    $view = 'quadro';

// Definir funil_id
$funil_id = isset($_GET['funil']) ? (int) $_GET['funil'] : 0;

// Buscar todos funis ativos
$funis = $crmService->listarFunis($company_id);

if ($funil_id === 0 && !empty($funis)) {
    $funil_id = $funis[0]['id'];
    foreach ($funis as $f) {
        // Se houver um padrão, prioriza ele
        if ($f['id'] == 1 || (isset($f['padrao']) && $f['padrao'])) {
            $funil_id = $f['id'];
            break;
        }
    }
}

// 2. Buscar Etapas
$etapas = $crmService->listarEtapas($funil_id, $company_id);

// 3. Buscar Usuários (Filtro)
$users_filter = $crmService->listarResponsaveis($company_id);

// 4. Parâmetros de Filtro
$filtro_busca = $_GET['busca'] ?? '';
$filtro_resp = isset($_GET['responsavel']) ? (int) $_GET['responsavel'] : 0;
$filtro_origem = $_GET['origem'] ?? '';

// Filtros Array para Service
$filtros = [
    'funil_id' => $funil_id,
    'responsavel_id' => $filtro_resp,
    'origem' => $filtro_origem,
    'busca' => $filtro_busca,
    'etapa_id' => 0 // Será preenchido no loop se quadro
];

// Variáveis para View
$total_valor_pipeline = 0;
$res_list = null; // Para Lista
$count_deals = 0; // Contador Geral
$total_pages = 0;
$page = 1;

if ($view === 'quadro') {
    // === MODO KANBAN ===
    foreach ($etapas as $etapa_id => $etapa_data) {
        $filtros['etapa_id'] = $etapa_id;
        $resultado = $crmService->listarNegocios($filtros, $company_id, 10);

        $etapas[$etapa_id]['deals'] = $resultado['deals'];
        $etapas[$etapa_id]['has_more'] = $resultado['has_more'];

        // Calcular totais
        foreach ($resultado['deals'] as $d) {
            $total_valor_pipeline += $d['valor_estimado'];
            $count_deals++;
        }
    }
} elseif ($view === 'lista') {
    // === MODO LISTA ===
    $page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
    $limit = 20;
    $offset = ($page - 1) * $limit;

    // Buscar Negócios (Lista Paginada)
    $resultado = $crmService->listarNegocios($filtros, $company_id, $limit, $offset);
    $res_list = $resultado['deals']; // Lista de arrays

    // Nota: listarNegociosService atual não retorna total count exato para paginação numerada correta
    // Para simplificar, usamos has_more para saber se tem próxima página ou estimamos
    // Refatoração ideal: Service retornar 'total_count' também.
    // Hack temporário: Se vier full limit, assume que tem mais pagina

    // Iterar para somar valor total (apenas da página atual infelizmente nesta implementação simples)
    foreach ($res_list as $d) {
        $total_valor_pipeline += $d['valor_estimado'];
    }
    $count_deals = count($res_list); // Apenas visíveis
    $total_pages = $resultado['has_more'] ? $page + 1 : $page; // Paginação simplificada
}
?>

<!-- Estilos do CRM -->
<link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/crm.css">


<div class="container-fluid py-4">

    <!-- Toolbar -->
    <div class="d-flex justify-content-between align-items-center mb-4">

        <!-- Esquerda: View Switcher -->
        <div class="d-flex align-items-center gap-3">
            <!-- Seletor de Pipeline (Dropdown Estilizado) -->
            <?php
            $current_funnel_name = "Selecione um Funil";
            foreach ($funis as $f) {
                if ($f['id'] == $funil_id) {
                    $current_funnel_name = $f['nome'];
                    break;
                }
            }
            ?>
            <div class="dropdown me-2">
                <a href="#" class="d-flex align-items-center text-dark text-decoration-none fw-bold fs-4"
                    data-bs-toggle="dropdown">
                    <?php echo htmlspecialchars($current_funnel_name); ?>
                    <i class="bi bi-chevron-down ms-2 fs-6 text-muted"></i>
                </a>
                <ul class="dropdown-menu shadow-sm border-0 mt-2">
                    <h6 class="dropdown-header small text-uppercase">Meus Funis</h6>
                    <?php foreach ($funis as $f): ?>
                        <li>
                            <a class="dropdown-item d-flex justify-content-between align-items-center py-2"
                                href="<?php echo BASE_URL; ?>crm/?view=<?php echo $view; ?>&funil=<?php echo $f['id']; ?>">
                                <?php echo htmlspecialchars($f['nome']); ?>
                                <?php if ($f['id'] == $funil_id): ?>
                                    <i class="bi bi-check-lg text-primary"></i>
                                <?php endif; ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                    <li>
                        <hr class="dropdown-divider">
                    </li>
                    <li><a class="dropdown-item small text-muted"
                            href="<?php echo BASE_URL; ?>modules/crm/config.php"><i
                                class="bi bi-gear me-2"></i>Gerenciar Funis</a></li>
                    <li><a class="dropdown-item small text-muted"
                            href="<?php echo BASE_URL; ?>modules/relatorios/crm_vendas.php"><i
                                class="bi bi-graph-up me-2"></i>Relatórios</a></li>
                </ul>
            </div>

            <div class="vr mx-2 text-muted" style="height: 25px;"></div>

            <div class="bg-gray-200 p-1 rounded-3 d-inline-flex" style="background-color: #f3f4f6;">
                <a href="<?php echo BASE_URL; ?>crm/?view=quadro&funil=<?php echo $funil_id; ?>"
                    class="btn btn-sm fw-bold px-3 <?php echo $view == 'quadro' ? 'btn-white shadow-sm' : 'text-muted'; ?>"
                    style="<?php echo $view == 'quadro' ? 'background-color: white;' : ''; ?>">
                    <i class="bi bi-kanban me-2"></i>Quadro
                </a>
                <a href="<?php echo BASE_URL; ?>crm/?view=lista&funil=<?php echo $funil_id; ?>"
                    class="btn btn-sm fw-bold px-3 <?php echo $view == 'lista' ? 'btn-white shadow-sm' : 'text-muted'; ?>"
                    style="<?php echo $view == 'lista' ? 'background-color: white;' : ''; ?>">
                    <i class="bi bi-list-ul me-2"></i>Lista
                </a>
            </div>

            <div class="text-muted border-start ps-3 small d-none d-md-block">
                <strong><?php echo $count_deals; ?></strong> oportunidades <br>
                <span class="text-success fw-bold">R$
                    <?php echo number_format($total_valor_pipeline, 2, ',', '.'); ?></span>
            </div>
        </div>

        <!-- Direita: Ações -->
        <div class="d-flex align-items-center gap-2">

            <form id="formFiltros" method="GET" class="d-flex align-items-center gap-2">

                <?php if ($funil_id): ?><input type="hidden" name="funil"
                        value="<?php echo $funil_id; ?>"><?php endif; ?>
                <input type="hidden" name="view" value="<?php echo $view; ?>"> <!-- Manter view correta na busca -->

                <!-- Busca Estilizada (Padrão Unificado) -->
                <div class="d-flex align-items-center bg-white border rounded px-2"
                    style="height: 31px; min-width: 220px;">
                    <i class="bi bi-search text-muted small me-2"></i>
                    <input type="text" class="form-control border-0 shadow-none bg-transparent p-0 small" name="busca"
                        placeholder="Buscar..." value="<?php echo htmlspecialchars($filtro_busca); ?>"
                        oninput="searchDebounce()" style="font-size: 0.875rem;">
                </div>

                <!-- Botão Toggle Filtros -->
                <button type="button" class="btn btn-white border btn-sm d-flex align-items-center text-muted bg-white"
                    data-bs-toggle="collapse" data-bs-target="#filtrosAvancados" aria-expanded="false">
                    <i class="bi bi-funnel me-1"></i> Filtrar
                </button>

                <!-- Filtros Escondidos -->
                <?php if ($filtro_resp)
                    echo '<input type="hidden" name="responsavel" value="' . $filtro_resp . '">'; ?>
                <?php if ($filtro_origem)
                    echo '<input type="hidden" name="origem" value="' . $filtro_origem . '">'; ?>
            </form>

            <div class="vr mx-1 text-muted"></div>

            <button class="btn btn-dark btn-sm px-3 fw-bold" onclick="abrirModalNovoNegocio()">
                <i class="bi bi-plus-lg me-1"></i> Nova
            </button>
        </div>
    </div>

    <!-- Área de Filtros Expansível -->
    <div class="collapse mb-4 <?php echo ($filtro_resp || $filtro_origem) ? 'show' : ''; ?>" id="filtrosAvancados">
        <div class="card card-body bg-light border-0 shadow-sm p-3">
            <form method="GET" class="row g-3 align-items-end" id="formFiltrosAvancados">
                <?php if ($funil_id): ?><input type="hidden" name="funil"
                        value="<?php echo $funil_id; ?>"><?php endif; ?>
                <input type="hidden" name="view" value="<?php echo $view; ?>">
                <input type="hidden" name="busca" value="<?php echo htmlspecialchars($filtro_busca); ?>">

                <div class="col-md-3">
                    <label class="form-label small fw-bold text-muted">Responsável</label>
                    <select class="form-select form-select-sm" name="responsavel">
                        <option value="">Todos</option>
                        <?php foreach ($users_filter as $u): ?>
                            <option value="<?php echo $u['id']; ?>" <?php echo $filtro_resp == $u['id'] ? 'selected' : ''; ?>>
                                <?php echo $u['nome']; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label small fw-bold text-muted">Origem</label>
                    <select class="form-select form-select-sm" name="origem">
                        <option value="">Todas</option>
                        <option value="Indicação" <?php echo $filtro_origem == 'Indicação' ? 'selected' : ''; ?>>Indicação
                        </option>
                        <option value="Instagram" <?php echo $filtro_origem == 'Instagram' ? 'selected' : ''; ?>>Instagram
                        </option>
                        <option value="Google" <?php echo $filtro_origem == 'Google' ? 'selected' : ''; ?>>Google</option>
                        <option value="Passante" <?php echo $filtro_origem == 'Passante' ? 'selected' : ''; ?>>Passante
                        </option>
                    </select>
                </div>

                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary btn-sm w-100">Aplicar</button>
                </div>

                <?php if ($filtro_resp || $filtro_origem): ?>
                    <div class="col-md-2">
                        <a href="<?php echo BASE_URL; ?>crm/<?php echo $view; ?>/<?php echo $funil_id; ?>"
                            class="btn btn-outline-secondary btn-sm w-100">Limpar</a>
                    </div>
                <?php endif; ?>
            </form>
        </div>
    </div>

    <script>
        let searchTimeout;
        function searchDebounce() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                document.getElementById('formFiltros').submit();
            }, 800); // 800ms delay
        }
    </script>

    <!-- Renderização Condicional da VIEW -->
    <?php if ($view === 'quadro'): ?>

        <!-- KANBAN BOARD WRAPPER -->
        <div class="kanban-overflow-container">
            <div class="kanban-board">
                <?php foreach ($etapas as $etapa_id => $etapa): ?>
                    <div class="kanban-column" ondrop="drop(event, <?php echo $etapa_id; ?>)" ondragover="allowDrop(event)">
                        <div class="kanban-column-header" style="border-bottom-color: <?php echo $etapa['cor'] ?: '#ccc'; ?>">
                            <span><?php echo htmlspecialchars($etapa['nome']); ?></span>
                            <span class="badge bg-white text-dark shadow-sm border">
                                <?php echo count($etapa['deals']); ?>
                            </span>
                        </div>

                        <div class="task-list">
                            <?php
                            $columnTotal = 0;
                            foreach ($etapa['deals'] as $deal):
                                $columnTotal += $deal['valor_estimado'];
                                $borderClass = $deal['valor_estimado'] > 1000 ? 'card-hot' : 'card-cold';
                                if ($deal['status'] == 'perdido')
                                    $borderClass .= ' opacity-75';
                                ?>
                                <div class="kanban-card <?php echo $borderClass; ?>" draggable="true"
                                    ondragstart="drag(event, <?php echo $deal['id']; ?>)"
                                    onclick="location.href='<?php echo BASE_URL; ?>crm/detalhes.php?id=<?php echo $deal['id']; ?>'">

                                    <div class="d-flex justify-content-between align-items-start">
                                        <div class="card-title text-truncate"
                                            title="<?php echo htmlspecialchars($deal['titulo']); ?>">
                                            <?php echo htmlspecialchars($deal['titulo']); ?>
                                        </div>
                                        <div class="dropdown" onclick="event.stopPropagation()">
                                            <button class="btn btn-link btn-sm p-0 text-muted" data-bs-toggle="dropdown"><i
                                                    class="bi bi-three-dots"></i></button>
                                            <ul class="dropdown-menu dropdown-menu-end">
                                                <li><a class="dropdown-item"
                                                        href="<?php echo BASE_URL; ?>crm/detalhes.php?id=<?php echo $deal['id']; ?>">Editar</a>
                                                </li>
                                                <li><a class="dropdown-item text-success" href="#"
                                                        onclick="marcarGanho(<?php echo $deal['id']; ?>)">Ganho</a></li>
                                                <li><a class="dropdown-item text-danger" href="#"
                                                        onclick="abrirModalPerdido(<?php echo $deal['id']; ?>)">Perdido</a></li>
                                            </ul>
                                        </div>
                                    </div>

                                    <?php if ($deal['paciente_nome']): ?>
                                        <div class="card-subtitle">
                                            <i class="bi bi-person"></i> <?php echo htmlspecialchars($deal['paciente_nome']); ?>
                                        </div>
                                    <?php endif; ?>

                                    <div class="d-flex justify-content-between align-items-end mt-2">
                                        <div>
                                            <div class="card-value">R$
                                                <?php echo number_format($deal['valor_estimado'], 2, ',', '.'); ?>
                                            </div>
                                            <?php if ($deal['origem']): ?>
                                                <span class="origin-badge"><?php echo htmlspecialchars($deal['origem']); ?></span>
                                            <?php endif; ?>

                                            <?php if ($deal['status'] == 'perdido'): ?>
                                                <span class="badge bg-danger ms-1" style="font-size: 0.65rem;">PERDIDO</span>
                                            <?php elseif ($deal['status'] == 'ganho' || $deal['status'] == 'anho'): ?>
                                                <span class="badge bg-success ms-1" style="font-size: 0.65rem;">GANHO</span>
                                            <?php endif; ?>
                                        </div>
                                        <?php if ($deal['responsavel_nome']): ?>
                                            <div class="user-avatar" title="<?php echo htmlspecialchars($deal['responsavel_nome']); ?>">
                                                <?php echo strtoupper(substr($deal['responsavel_nome'], 0, 1)); ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>

                                    <?php if ($deal['data_fechamento_esperada']):
                                        $days = (strtotime($deal['data_fechamento_esperada']) - time()) / 86400;
                                        if ($days < 0 && $deal['status'] == 'aberto')
                                            echo '<small class="text-danger d-block mt-1"><i class="bi bi-exclamation-circle"></i> Atrasado</small>';
                                    endif; ?>

                                </div>
                            <?php endforeach; ?>
                        </div>

                        <div class="mt-2 text-center text-muted small border-top pt-2">
                            Total: <strong>R$ <?php echo number_format($columnTotal, 2, ',', '.'); ?></strong>
                        </div>

                        <?php if (!empty($etapa['has_more'])): ?>
                            <button id="btn-more-<?php echo $etapa_id; ?>" class="btn-load-more mt-2"
                                onclick="loadMoreDeals(<?php echo $etapa_id; ?>, 10)">
                                <i class="bi bi-chevron-down"></i> Ver mais
                            </button>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <script>
            function loadMoreDeals(etapaId, offset) {
                const btn = document.getElementById('btn-more-' + etapaId);
                btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Carregando...';
                btn.disabled = true;

                const urlParams = new URLSearchParams(window.location.search);
                const filtros = {
                    busca: urlParams.get('busca') || '',
                    responsavel: urlParams.get('responsavel') || '',
                    origem: urlParams.get('origem') || '',
                    funil: urlParams.get('funil') || ''
                };

                const fetchUrl = `<?php echo BASE_URL; ?>crm/acoes.php?acao=listar_negocios&etapa_id=${etapaId}&offset=${offset}` +
                    `&busca=${encodeURIComponent(filtros.busca)}` +
                    `&responsavel=${encodeURIComponent(filtros.responsavel)}` +
                    `&origem=${encodeURIComponent(filtros.origem)}` +
                    `&funil=${encodeURIComponent(filtros.funil)}`;

                fetch(fetchUrl)
                    .then(res => res.json())
                    .then(response => {
                        if (response.success && response.data && response.data.cards) {
                            const taskList = btn.parentElement.querySelector('.task-list');
                            const tempDiv = document.createElement('div');
                            tempDiv.innerHTML = response.data.cards;
                            while (tempDiv.firstChild) { taskList.appendChild(tempDiv.firstChild); }

                            if (response.data.has_more) {
                                btn.innerHTML = '<i class="bi bi-chevron-down"></i> Ver mais';
                                btn.disabled = false;
                                btn.setAttribute('onclick', `loadMoreDeals(${etapaId}, ${offset + 10})`);
                            } else { btn.remove(); }
                        } else { btn.remove(); }
                    })
                    .catch(err => { console.error(err); btn.innerHTML = 'Erro ao carregar'; });
            }
        </script>

    <?php elseif ($view === 'lista'): ?>

        <!-- LIST VIEW -->
        <div class="card border-0 shadow-sm">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4">Negócio / Paciente</th>
                            <th>Etapa</th>
                            <th>Valor</th>
                            <th>Responsável</th>
                            <th>Origem</th>
                            <th>Status</th>
                            <th>Criado em</th>
                            <th class="text-end pe-4">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($res_list as $row): ?>
                            <tr onclick="location.href='<?php echo BASE_URL; ?>crm/negocio/<?php echo $row['id']; ?>'"
                                style="cursor: pointer;">
                                <td class="ps-4">
                                    <div class="fw-bold text-dark"><?php echo htmlspecialchars($row['titulo']); ?></div>
                                    <?php if ($row['paciente_nome']): ?>
                                        <small class="text-muted"><i class="bi bi-person"></i>
                                            <?php echo htmlspecialchars($row['paciente_nome']); ?></small>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="badge rounded-pill border text-dark"
                                        style="background-color: <?php echo $row['etapa_cor'] ?: '#fff'; ?>50; border-color: <?php echo $row['etapa_cor'] ?: '#ccc'; ?> !important;">
                                        <?php echo htmlspecialchars($row['etapa_nome']); ?>
                                    </span>
                                </td>
                                <td class="fw-bold text-success">
                                    R$ <?php echo number_format($row['valor_estimado'], 2, ',', '.'); ?>
                                </td>
                                <td>
                                    <?php if ($row['responsavel_nome']): ?>
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="user-avatar" style="width: 24px; height: 24px; font-size: 10px;">
                                                <?php echo strtoupper(substr($row['responsavel_nome'], 0, 1)); ?>
                                            </div>
                                            <small><?php echo htmlspecialchars($row['responsavel_nome']); ?></small>
                                        </div>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($row['origem']): ?><span
                                            class="origin-badge"><?php echo htmlspecialchars($row['origem']); ?></span><?php endif; ?>
                                </td>
                                <td>
                                    <?php
                                    $statusMap = ['aberto' => 'primary', 'ganho' => 'success', 'perdido' => 'danger', 'cancelado' => 'secondary'];
                                    $badgeColor = $statusMap[$row['status']] ?? 'secondary';
                                    ?>
                                    <span
                                        class="badge bg-<?php echo $badgeColor; ?>"><?php echo ucfirst($row['status']); ?></span>
                                </td>
                                <td class="text-muted small">
                                    <?php echo date('d/m/Y', strtotime($row['created_at'])); ?>
                                </td>
                                <td class="text-end pe-4">
                                    <button class="btn btn-sm btn-light border"><i class="bi bi-pencil"></i></button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (empty($res_list)): ?>
                            <tr>
                                <td colspan="8" class="text-center py-5 text-muted">Nenhum negócio encontrado.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <?php if ($total_pages > 1): ?>
                <div class="card-footer bg-white py-3">
                    <nav>
                        <ul class="pagination justify-content-center mb-0">
                            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                                <li class="page-item <?php echo $page == $i ? 'active' : ''; ?>">
                                    <a class="page-link"
                                        href="<?php echo BASE_URL; ?>crm/lista/<?php echo $funil_id; ?>?page=<?php echo $i; ?><?php echo $filtro_busca ? "&busca=$filtro_busca" : ''; ?>"><?php echo $i; ?></a>
                                </li>
                            <?php endfor; ?>
                        </ul>
                    </nav>
                </div>
            <?php endif; ?>
        </div>

    <?php endif; ?>

</div>

<!-- MODAL NOVO NEGÓCIO -->
<div class="modal fade" id="modalNovoNegocio" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Novo Negócio</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="formNovoNegocio">
                    <input type="hidden" id="novoFunilId" value="<?php echo $funil_id; ?>">

                    <div class="mb-3">
                        <label class="form-label">O que é? (Título)</label>
                        <input type="text" class="form-control" id="novoTitulo" placeholder="Ex: Tratamento de Varizes"
                            required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Valor Estimado (R$)</label>
                        <input type="text" class="form-control" id="novoValor" placeholder="0,00">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Etapa Inicial</label>
                        <select class="form-select" id="novoEtapaId">
                            <?php foreach ($etapas as $etp): ?>
                                <option value="<?php echo $etp['id']; ?>"><?php echo htmlspecialchars($etp['nome']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3 position-relative">
                        <!-- BUSCA PESSOA -->
                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <label class="form-label mb-0 fw-bold small text-muted">Pessoa (Contato)</label>
                                <a href="#" onclick="toggleNovoPaciente(event)" id="linkTogglePaciente"
                                    class="small text-decoration-none">Cadastrar Novo</a>
                            </div>
                            <div class="position-relative">
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text bg-light border-end-0"><i
                                            class="bi bi-person"></i></span>
                                    <input type="text" class="form-control border-start-0 ps-0" id="buscaPaciente"
                                        placeholder="Buscar contato..." autocomplete="off">
                                </div>
                                <input type="hidden" id="novoPacienteId">
                                <input type="hidden" id="novoPacienteTipo">
                                <div id="listaPacientes" class="list-group position-absolute w-100 shadow bg-white"
                                    style="z-index: 2000; display:none;"></div>
                            </div>
                        </div>

                        <!-- BUSCA EMPRESA -->
                        <div class="mb-3">
                            <label class="form-label mb-0 fw-bold small text-muted">Empresa</label>
                            <div class="position-relative">
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text bg-light border-end-0"><i
                                            class="bi bi-building"></i></span>
                                    <input type="text" class="form-control border-start-0 ps-0" id="buscaEmpresa"
                                        placeholder="Buscar empresa..." autocomplete="off">
                                </div>
                                <input type="hidden" id="novoEmpresaId">
                                <div id="listaEmpresas" class="list-group position-absolute w-100 shadow bg-white"
                                    style="z-index: 2000; display:none;"></div>
                            </div>
                        </div>

                        <!-- Area Cadastrar Novo (Pessoa) -->
                        <div id="areaNovoPaciente" class="d-none p-3 rounded mt-2 border bg-light">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h6 class="small text-primary mb-0 fw-bold"><i class="bi bi-person-plus-fill me-1"></i>
                                    Novo Contato</h6>
                                <button type="button" class="btn-close btn-sm"
                                    onclick="toggleNovoPaciente(event)"></button>
                            </div>
                            <input type="text" id="inputNovoNome" class="form-control mb-2 form-control-sm"
                                placeholder="Nome Completo">
                            <input type="text" id="inputNovoTelefone" class="form-control form-control-sm"
                                placeholder="Telefone / WhatsApp">
                        </div>
                    </div>

                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" onclick="salvarNegocio()">Salvar</button>
            </div>
        </div>
    </div>
</div>

<!-- MODAL PERDIDO -->
<div class="modal fade" id="modalPerdido" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Marcar como Perdido</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p class="text-muted">Descreva o motivo da perda para melhorar suas estatísticas futuras.</p>
                <form id="formPerdido">
                    <input type="hidden" id="perdidoId">
                    <div class="mb-3">
                        <label class="form-label">Motivo da Perda</label>
                        <textarea class="form-control" id="motivoPerdido" rows="3"
                            placeholder="Ex: Preço alto, optou pelo concorrente..."></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-danger" onclick="confirmarPerdido()">Marcar como Perdido</button>
            </div>
        </div>
    </div>
</div>

<script>
    let modalPerdidoInst = null;
    let modalNovoNegocioInst = null;

    document.addEventListener('DOMContentLoaded', function () {
        // Init Modals
        const modalPerdidoEl = document.getElementById('modalPerdido');
        if (modalPerdidoEl) modalPerdidoInst = new bootstrap.Modal(modalPerdidoEl);

        const modalNovoEl = document.getElementById('modalNovoNegocio');
        if (modalNovoEl) modalNovoNegocioInst = new bootstrap.Modal(modalNovoEl);
    });

    // Inject BASE_URL for JS
    const BASE_URL = "<?php echo BASE_URL; ?>";

    // Global Error Handler
    window.onerror = function (msg, url, lineNo, columnNo, error) {
        alert('Erro Global JS: ' + msg + '\nLinha: ' + lineNo);
        console.error('Global Error:', error);
        return false;
    };

    function abrirModalNovoNegocio() {
        if (modalNovoNegocioInst) {
            modalNovoNegocioInst.show();
        } else {
            console.error('Modal Novo Negócio não inicializado - Tentar reinicializar');
            const modalNovoEl = document.getElementById('modalNovoNegocio');
            if (modalNovoEl && window.bootstrap) {
                modalNovoNegocioInst = new bootstrap.Modal(modalNovoEl);
                modalNovoNegocioInst.show();
            } else {
                alert('Erro ao carregar modal. Tente recarregar a página.');
            }
        }
    }

    function abrirModalPerdido(id) {
        document.getElementById('perdidoId').value = id;
        document.getElementById('motivoPerdido').value = '';
        if (modalPerdidoInst) {
            modalPerdidoInst.show();
        } else {
            const mEl = document.getElementById('modalPerdido');
            if (mEl) {
                modalPerdidoInst = new bootstrap.Modal(mEl);
                modalPerdidoInst.show();
            }
        }
    }

    function confirmarPerdido() {
        const id = document.getElementById('perdidoId').value;
        const motivo = document.getElementById('motivoPerdido').value;

        fetch(BASE_URL + 'crm/acoes.php?acao=marcar_perdido', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id, motivo })
        })
            .then(res => res.json())
            .then(data => {
                if (data.success) location.reload();
                else alert('Erro: ' + data.message);
            })
            .catch(err => alert('Erro de conexão.'));
    }

    function marcarGanho(id) {
        if (!confirm('Parabéns! Deseja marcar este negócio como GANHO?')) return;

        fetch(BASE_URL + 'crm/acoes.php?acao=marcar_ganho', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id })
        })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                }
                else alert('Erro: ' + data.message);
            })
            .catch(err => alert('Erro de conexão.'));
    }

    // Search Person Helper
    const buscaPacienteParams = document.getElementById('buscaPaciente');
    if (buscaPacienteParams) {
        buscaPacienteParams.addEventListener('input', function () {
            const term = this.value;
            const list = document.getElementById('listaPacientes');

            if (term.length < 2) { list.style.display = 'none'; return; }

            fetch(BASE_URL + 'crm/acoes.php?acao=buscar_contatos&busca=' + encodeURIComponent(term) + '&tipo=user')
                .then(res => res.json())
                .then(response => {
                    list.innerHTML = '';
                    if (response.success && response.data && response.data.length > 0) {
                        list.style.display = 'block';
                        response.data.forEach(p => {
                            const item = document.createElement('a');
                            item.className = 'list-group-item list-group-item-action';
                            let display = p.nome;
                            if (p.email) display += ' - ' + p.email;
                            if (p.telefone) display += ' - ' + p.telefone;

                            item.textContent = display;
                            item.href = '#';
                            item.onclick = (e) => {
                                e.preventDefault();
                                document.getElementById('novoPacienteId').value = p.id;
                                document.getElementById('novoPacienteTipo').value = p.tipo || 'lead'; // Default to lead if missing
                                document.getElementById('buscaPaciente').value = p.nome;
                                list.style.display = 'none';
                            };
                            list.appendChild(item);
                        });
                    } else { list.style.display = 'none'; }
                });
        });
    }

    // Search Company Helper
    const buscaEmpresaParams = document.getElementById('buscaEmpresa');
    if (buscaEmpresaParams) {
        buscaEmpresaParams.addEventListener('input', function () {
            const term = this.value;
            const list = document.getElementById('listaEmpresas');

            if (term.length < 2) { list.style.display = 'none'; return; }

            fetch(BASE_URL + 'crm/acoes.php?acao=buscar_contatos&busca=' + encodeURIComponent(term) + '&tipo=empresa')
                .then(res => res.json())
                .then(response => {
                    list.innerHTML = '';
                    if (response.success && response.data && response.data.length > 0) {
                        list.style.display = 'block';
                        response.data.forEach(p => {
                            const item = document.createElement('a');
                            item.className = 'list-group-item list-group-item-action';
                            let display = p.nome || p.nome_fantasia;

                            item.textContent = display;
                            item.href = '#';
                            item.onclick = (e) => {
                                e.preventDefault();
                                document.getElementById('novoEmpresaId').value = p.id;
                                document.getElementById('buscaEmpresa').value = display;
                                list.style.display = 'none';
                            };
                            list.appendChild(item);
                        });
                    } else { list.style.display = 'none'; }
                });
        });
    }

    function toggleNovoPaciente(e) {
        e.preventDefault();
        // Simple toggle for the new contact area
        const areaNovo = document.getElementById('areaNovoPaciente');
        if (areaNovo.classList.contains('d-none')) {
            areaNovo.classList.remove('d-none');
        } else {
            areaNovo.classList.add('d-none');
        }
    }

    function salvarNegocio() {
        const pId = document.getElementById('novoPacienteId').value;
        const pType = document.getElementById('novoPacienteTipo').value;

        const dados = {
            titulo: document.getElementById('novoTitulo').value,
            valor: document.getElementById('novoValor').value,
            etapa_id: document.getElementById('novoEtapaId').value,
            empresa_cliente_id: document.getElementById('novoEmpresaId').value,
            // Quick create params if backend supports it:
            novo_cliente_nome: document.getElementById('inputNovoNome').value,
            novo_cliente_email: document.getElementById('inputNovoTelefone').value, // Adapting field
            funil_id: <?php echo $funil_id; ?>,
            origem: 'Indicação'
        };

        if (pType === 'lead') {
            dados.lead_id = pId;
            dados.cliente_id = null;
        } else {
            dados.cliente_id = pId;
            dados.lead_id = null;
        }

        if (!dados.titulo) {
            Swal.fire({
                icon: 'warning',
                title: 'Atenção',
                text: 'Preencha o título do negócio',
                toast: true,
                position: 'bottom-end',
                showConfirmButton: false,
                timer: 3000
            });
            return;
        }

        fetch(BASE_URL + 'crm/acoes.php?acao=criar_negocio', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(dados)
        }).then(res => res.json()).then(res => {
            if (res.success) {
                // Toast de Sucesso
                Swal.fire({
                    icon: 'success',
                    title: 'Negócio criado com sucesso!',
                    text: 'ID: ' + (res.data.id || res.data || 'N/A'), // Fallback seguro
                    toast: true,
                    position: 'bottom-end',
                    showConfirmButton: false,
                    background: '#d4edda', // Verde claro suave (Bootstrap success-ish)
                    color: '#155724', // Texto verde escuro
                    iconColor: '#155724',
                    timer: 3000,
                    timerProgressBar: true
                });

                setTimeout(() => {
                    location.reload();
                }, 1000); // Pequeno delay para ver o toast
            }
            else {
                Swal.fire({
                    icon: 'error',
                    title: 'Erro',
                    text: res.message || res.error || 'Erro desconhecido',
                    toast: true,
                    position: 'bottom-end',
                    showConfirmButton: false,
                    timer: 4000
                });
            }
        }).catch(err => {
            console.error(err);
            Swal.fire({
                icon: 'error',
                title: 'Erro de conexão',
                toast: true,
                position: 'bottom-end',
                showConfirmButton: false,
                timer: 4000
            });
        });
    }

    // Drag and Drop (Placeholder for brevity, full implement in logic if needed)
    function allowDrop(ev) { ev.preventDefault(); }
    function drag(ev, id) { ev.dataTransfer.setData("text", id); }
    function drop(ev, etapaId) {
        ev.preventDefault();
        var dealId = ev.dataTransfer.getData("text");

        // Update via AJAX
        fetch(BASE_URL + 'crm/acoes.php?acao=mover_etapa', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ deal_id: dealId, etapa_id: etapaId })
        })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    location.reload(); // Verify/Refresh
                } else {
                    alert('Erro ao mover negócio: ' + (data.error || 'Erro desconhecido'));
                }
            })
            .catch(err => {
                console.error(err);
                alert('Erro de conexão ao mover o card.');
            });
    }
</script>

<!-- SweetAlert2 (Garantir carregamento) -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- Scripts Gerais do CRM -->


<?php require_once __DIR__ . '/../../includes/footer.php'; ?>