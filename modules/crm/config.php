<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../includes/header.php';

if (!isModuleEnabled('crm')) {
    redirect('dashboard?modulo_bloqueado=crm');
    exit;
}
// Menu is already included by header.php

// Verificar Permissão
// if (!checkPermission('crm_config')) { ... }

// Buscar Funis e Etapas (apenas da empresa)
$company_id = getCompanyId();
$stmt = $conn->prepare("
    SELECT f.*, 
           (SELECT COUNT(*) FROM crm_negocios WHERE funil_id = f.id) as total_negocios
    FROM crm_funis f
    WHERE f.company_id = ?
    ORDER BY f.id ASC
");
$stmt->bind_param("i", $company_id);
$stmt->execute();
$res = $stmt->get_result();
$funis = [];
while ($row = $res->fetch_assoc()) {
    $etapa_stmt = $conn->prepare("SELECT * FROM crm_etapas WHERE funil_id = ? ORDER BY ordem ASC");
    $etapa_stmt->bind_param("i", $row['id']);
    $etapa_stmt->execute();
    $etapas_res = $etapa_stmt->get_result();
    $row['etapas'] = [];
    while ($e = $etapas_res->fetch_assoc()) {
        $row['etapas'][] = $e;
    }
    $funis[] = $row;
}
?>

<div class="container-fluid mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1">
                    <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>crm">CRM</a></li>
                    <li class="breadcrumb-item active">Configurações</li>
                </ol>
            </nav>
            <h4 class="fw-bold">Gestão de Funis e Etapas</h4>
        </div>
        <button class="btn btn-primary" onclick="abrirModalFunil()">
            <i class="bi bi-plus-lg me-2"></i>Novo Funil
        </button>
    </div>

    <!-- Accordion de Funis -->
    <div class="accordion shadow-sm" id="accordionFunis">
        <?php foreach ($funis as $index => $funil):
            $collapseId = "collapseFunil" . $funil['id'];
            $headingId = "headingFunil" . $funil['id'];
            $isFirst = $index === 0;
            // Escapar JSON
            $funilJson = htmlspecialchars(json_encode($funil), ENT_QUOTES, 'UTF-8');
            ?>
            <div class="accordion-item border-0 border-bottom">
                <h2 class="accordion-header" id="<?php echo $headingId; ?>">
                    <button class="accordion-button <?php echo $isFirst ? '' : 'collapsed'; ?> bg-white fw-bold text-dark"
                        type="button" data-bs-toggle="collapse" data-bs-target="#<?php echo $collapseId; ?>"
                        aria-expanded="<?php echo $isFirst ? 'true' : 'false'; ?>"
                        aria-controls="<?php echo $collapseId; ?>">
                        <?php echo htmlspecialchars($funil['nome']); ?>
                        <span class="ms-2 badge bg-light text-secondary border"><?php echo count($funil['etapas']); ?>
                            Etapas</span>
                    </button>
                </h2>
                <div id="<?php echo $collapseId; ?>"
                    class="accordion-collapse collapse <?php echo $isFirst ? 'show' : ''; ?>"
                    aria-labelledby="<?php echo $headingId; ?>" data-bs-parent="#accordionFunis">
                    <div class="accordion-body bg-light">

                        <!-- Header Interno -->
                        <div
                            class="d-flex justify-content-between align-items-center mb-3 bg-white p-3 rounded shadow-sm border">
                            <div>
                                <small class="text-muted d-block uppercase fw-bold" style="font-size: 0.7rem;">FUNIL</small>
                                <span class="fw-bold text-primary"><?php echo htmlspecialchars($funil['nome']); ?></span>
                                <div class="text-muted small"><?php echo htmlspecialchars($funil['descricao']); ?></div>
                            </div>
                            <div>
                                <button class="btn btn-sm btn-outline-secondary me-1"
                                    onclick='abrirModalEditarFunil(<?php echo $funilJson; ?>)'>
                                    <i class="bi bi-pencil"></i> Editar Funil
                                </button>
                                <?php if ($funil['total_negocios'] == 0): ?>
                                    <button class="btn btn-sm btn-outline-danger"
                                        onclick="excluirFunil(<?php echo $funil['id']; ?>)">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                <?php else: ?>
                                    <button class="btn btn-sm btn-outline-secondary" disabled title="Funil com negócios ativos">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Tabela de Etapas -->
                        <div class="bg-white rounded shadow-sm">
                            <table class="table table-hover mb-0 align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width: 50px;"></th>
                                        <th style="width: 50px;">Ordem</th>
                                        <th>Nome da Etapa</th>
                                        <th>Cor</th>
                                        <th class="text-end">Ações</th>
                                    </tr>
                                </thead>
                                <tbody class="sortable-etapas" data-funil-id="<?php echo $funil['id']; ?>">
                                    <?php foreach ($funil['etapas'] as $etapa):
                                        $etapaJson = htmlspecialchars(json_encode($etapa), ENT_QUOTES, 'UTF-8');
                                        // Detect Color Type
                                        $isHex = strpos($etapa['cor'], '#') === 0;
                                        $style = $isHex ? "background-color: {$etapa['cor']};" : "";
                                        $class = $isHex ? "" : $etapa['cor'];
                                        ?>
                                        <tr data-id="<?php echo $etapa['id']; ?>" class="draggable-item">
                                            <td class="text-center text-muted" style="cursor: grab;"><i
                                                    class="bi bi-grip-vertical"></i></td>
                                            <td class="text-center fw-bold text-muted ordem-visual">
                                                <?php echo $etapa['ordem']; ?>
                                            </td>
                                            <td><?php echo htmlspecialchars($etapa['nome']); ?></td>
                                            <td>
                                                <span class="badge rounded-pill border <?php echo $class; ?>"
                                                    style="<?php echo $style; ?> color: #fff; text-shadow: 0 1px 2px rgba(0,0,0,0.3); min-width: 60px;">
                                                    &nbsp;
                                                </span>
                                            </td>
                                            <td class="text-end">
                                                <button class="btn btn-sm btn-link text-secondary"
                                                    onclick='abrirModalEditarEtapa(<?php echo $etapaJson; ?>)'>
                                                    <i class="bi bi-pencil"></i>
                                                </button>
                                                <button class="btn btn-sm btn-link text-danger"
                                                    onclick='excluirEtapa(<?php echo $etapa["id"]; ?>)'>
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-3 text-end">
                            <button class="btn btn-sm btn-outline-primary bg-white fw-bold shadow-sm"
                                onclick="novaEtapa(<?php echo $funil['id']; ?>)">
                                <i class="bi bi-plus-lg me-1"></i> Adicionar Nova Etapa
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- Modals omitidos para brevidade se não alterados, mas preciso garantir URL no script -->
<!-- ... Rest of modals ... -->

<script>
    const BASE_URL = "<?php echo BASE_URL; ?>";
    // ... instances ...

    // ... (Mantendo modal HTML se não alterar aqui, mas o script precisa mudar)
    // Vou reincluir os modais para garantir o contexto.

</script>

<!-- REPLICANDO MODAIS E SCRIPTS COM FIX DE URL -->
<!-- Modal Funil -->
<div class="modal fade" id="modalFunil" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="tituloModalFunil">Novo Funil</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="formFunil" onsubmit="event.preventDefault(); salvarFunil();">
                    <input type="hidden" id="funilId">
                    <div class="mb-3">
                        <label class="form-label">Nome do Funil</label>
                        <input type="text" class="form-control" id="funilNome" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Descrição</label>
                        <textarea class="form-control" id="funilDescricao" rows="2"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" onclick="salvarFunil()">Salvar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Etapa -->
<div class="modal fade" id="modalEtapa" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="tituloModalEtapa">Nova Etapa</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="formEtapa" onsubmit="event.preventDefault(); salvarEtapa();">
                    <input type="hidden" id="etapaId">
                    <input type="hidden" id="etapaFunilId">

                    <div class="mb-3">
                        <label class="form-label">Nome da Etapa</label>
                        <input type="text" class="form-control" id="etapaNome" required>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Ordem</label>
                            <input type="number" class="form-control" id="etapaOrdem" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Cor</label>
                            <!-- Input Color always Hex. If coming from legacy class, default to gray? -->
                            <input type="color" class="form-control form-control-color w-100" id="etapaCor"
                                value="#6c757d">
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" onclick="salvarEtapa()">Salvar</button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
<style>
    .draggable-item {
        cursor: move;
    }

    .sortable-ghost {
        opacity: 0.5;
        background: #e9ecef;
    }
</style>

<script>
    let modalFunilInstance = null;
    let modalEtapaInstance = null;

    document.addEventListener('DOMContentLoaded', function () {
        // Fix: Mover modais para o body para evitar problemas de z-index com o backdrop
        document.querySelectorAll('.modal').forEach(modal => document.body.appendChild(modal));

        modalFunilInstance = new bootstrap.Modal(document.getElementById('modalFunil'));
        modalEtapaInstance = new bootstrap.Modal(document.getElementById('modalEtapa'));

        // Inicializar Sortable
        if (typeof Sortable !== 'undefined') {
            document.querySelectorAll('.sortable-etapas').forEach(function (el) {
                new Sortable(el, {
                    animation: 150,
                    ghostClass: 'sortable-ghost',
                    onEnd: function (evt) {
                        const funilId = el.getAttribute('data-funil-id');
                        const rows = el.querySelectorAll('tr');
                        let novaOrdem = [];
                        rows.forEach((row, index) => {
                            const num = index + 1;
                            row.querySelector('.ordem-visual').innerText = num;
                            novaOrdem.push({
                                id: row.getAttribute('data-id'),
                                ordem: num
                            });
                        });
                        salvarOrdemEtapas(funilId, novaOrdem);
                    }
                });
            });
        }
    });

    function salvarOrdemEtapas(funilId, ordemArray) {
        fetch(BASE_URL + 'crm/acoes.php?acao=reordenar_etapas', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ funil_id: funilId, etapas: ordemArray })
        })
            .then(res => res.json())
            .then(response => {
                if (!response.success) {
                    alert('Erro ao salvar ordem: ' + response.message);
                    location.reload();
                }
            })
            .catch(err => console.error('Erro de conexão', err));
    }

    // --- FUNIL ---
    function abrirModalFunil() {
        document.getElementById('formFunil').reset();
        document.getElementById('funilId').value = '';
        document.getElementById('tituloModalFunil').innerText = 'Novo Funil';
        modalFunilInstance.show();
    }

    function abrirModalEditarFunil(funil) {
        document.getElementById('funilId').value = funil.id;
        document.getElementById('funilNome').value = funil.nome;
        document.getElementById('funilDescricao').value = funil.descricao;
        document.getElementById('tituloModalFunil').innerText = 'Editar Funil';
        modalFunilInstance.show();
    }

    function salvarFunil() {
        const id = document.getElementById('funilId').value;
        const nome = document.getElementById('funilNome').value;
        const desc = document.getElementById('funilDescricao').value;

        if (!nome) return alert('Nome obrigatório');

        const acao = id ? 'editar_funil' : 'criar_funil';
        const body = { id, nome, descricao: desc };

        // Absolute Path to Avoid Friendly URL Issues
        fetch(BASE_URL + 'crm/acoes.php?acao=' + acao, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(body)
        })
            .then(res => res.json())
            .then(response => {
                if (response.success) location.reload();
                else alert('Erro: ' + (response.message || 'Erro desconhecido'));
            })
            .catch(err => {
                console.error(err);
                alert('Erro de conexão.');
            });
    }

    function excluirFunil(id) {
        if (!confirm('Tem certeza? Isso excluirá o funil e todas as etapas.')) return;

        fetch(BASE_URL + 'crm/acoes.php?acao=excluir_funil', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id })
        })
            .then(res => res.json())
            .then(response => {
                if (response.success) location.reload();
                else alert('Erro: ' + (response.message || 'Erro desconhecido'));
            })
            .catch(err => alert('Erro de conexão.'));
    }

    // --- ETAPA ---
    function novaEtapa(funilId) {
        document.getElementById('formEtapa').reset();
        document.getElementById('etapaId').value = '';
        document.getElementById('etapaFunilId').value = funilId;
        document.getElementById('etapaCor').value = '#6c757d'; // Default hex
        document.getElementById('tituloModalEtapa').innerText = 'Nova Etapa';
        modalEtapaInstance.show();
    }

    function abrirModalEditarEtapa(etapa) {
        document.getElementById('etapaId').value = etapa.id;
        document.getElementById('etapaFunilId').value = etapa.funil_id;
        document.getElementById('etapaNome').value = etapa.nome;
        document.getElementById('etapaOrdem').value = etapa.ordem;

        // Handle Color: Hex vs Class
        let cor = etapa.cor;
        if (cor.startsWith('bg-')) {
            // Map legacy classes to approx hex if possible, or just default gray
            // 'bg-primary' -> '#0d6efd'
            // 'bg-success' -> '#198754'
            // 'bg-info' -> '#0dcaf0'
            // 'bg-warning' -> '#ffc107'
            // 'bg-danger' -> '#dc3545'
            const map = {
                'bg-primary': '#0d6efd',
                'bg-secondary': '#6c757d',
                'bg-success': '#198754',
                'bg-info': '#0dcaf0',
                'bg-warning': '#ffc107',
                'bg-danger': '#dc3545',
                'bg-dark': '#212529'
            };
            cor = map[cor] || '#6c757d';
        }
        document.getElementById('etapaCor').value = cor;

        document.getElementById('tituloModalEtapa').innerText = 'Editar Etapa';
        modalEtapaInstance.show();
    }

    function salvarEtapa() {
        const id = document.getElementById('etapaId').value;
        const funil_id = document.getElementById('etapaFunilId').value;
        const nome = document.getElementById('etapaNome').value;
        const ordem = document.getElementById('etapaOrdem').value;
        const cor = document.getElementById('etapaCor').value;

        if (!nome) return alert('Nome obrigatório');

        const acao = id ? 'editar_etapa' : 'criar_etapa';
        const body = { id, funil_id, nome, ordem, cor };

        fetch(BASE_URL + 'crm/acoes.php?acao=' + acao, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(body)
        })
            .then(res => res.json())
            .then(response => {
                if (response.success) location.reload();
                else alert('Erro: ' + (response.message || 'Erro desconhecido'));
            })
            .catch(err => alert('Erro de conexão.'));
    }

    function excluirEtapa(id) {
        if (!confirm('Tem certeza que deseja excluir esta etapa?')) return;

        fetch(BASE_URL + 'crm/acoes.php?acao=excluir_etapa', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id })
        })
            .then(res => res.json())
            .then(response => {
                if (response.success) location.reload();
                else alert('Erro: ' + (response.message || 'Erro desconhecido: ' + JSON.stringify(response)));
            })
            .catch(err => alert('Erro de conexão.'));
    }
</script>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>