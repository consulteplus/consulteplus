<?php
$pageTitle = "Planejamento & Metas (OKRs)";
require_once __DIR__ . '/../../../../includes/header.php';
checkPermission(['admin', 'cliente']);

// Buscar Objetivos e seus KRs
$objetivos = [];
$company_id = $_SESSION['company_id'];
$sql = "SELECT * FROM gestao_objetivos WHERE company_id = $company_id ORDER BY prazo ASC";
$resObj = $conn->query($sql);

if ($resObj) {
    while ($obj = $resObj->fetch_assoc()) {
        $objId = $obj['id'];
        $obj['krs'] = [];

        $sqlKr = "SELECT * FROM gestao_resultados_chave WHERE objetivo_id = $objId";
        $resKr = $conn->query($sqlKr);
        while ($kr = $resKr->fetch_assoc()) {
            $obj['krs'][] = $kr;
        }
        $objetivos[] = $obj;
    }
}

// Buscar Projetos para Select
$projetos = [];
$resProjs = $conn->query("SELECT id, titulo FROM gestao_projetos WHERE company_id = $company_id ORDER BY created_at DESC");
if ($resProjs) {
    while ($p = $resProjs->fetch_assoc()) {
        $projetos[] = $p;
    }
}
?>

<!-- Header Padronizado (Sempre Visível) -->
<div class="mb-4">
    <div class="card border-0 shadow-sm bg-white">
        <div class="card-body p-4">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h1 class="h3 fw-bold mb-2">Planejamento Estratégico</h1>
                    <p class="text-muted mb-md-0">Defina seus objetivos (OKRs) e acompanhe o progresso.</p>
                </div>
                <div class="col-md-6 text-md-end mt-3 mt-md-0">
                    <button class="btn btn-primary fw-semibold" data-bs-toggle="modal"
                        data-bs-target="#modalNovoObjetivo">
                        <i class="bi bi-plus-lg me-2"></i>Novo Objetivo
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<?php if (!empty($objetivos)): ?>
    <!-- Content logic remains -->
<?php endif; ?>

<div class="row">
    <?php if (empty($objetivos)): ?>
        <!-- EMPTY STATE (Sem objetivos) -->
        <!-- EMPTY STATE (Sem objetivos) -->
        <div class="row">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-body text-center py-5 px-4">
                        <div class="mb-4">
                            <i class="bi bi-bullseye text-muted" style="font-size: 4rem; opacity: 0.3;"></i>
                        </div>

                        <h3 class="fw-bold mb-3">Nenhum Objetivo Definido</h3>

                        <p class="text-muted mb-4">
                            Sua empresa ainda não possui metas traçadas para este ciclo.
                        </p>

                        <div class="d-inline-block text-start bg-light rounded p-3 mb-4 mx-auto" style="max-width: 450px;">
                            <small class="text-muted d-flex align-items-center">
                                <i class="bi bi-graph-up-arrow text-primary me-2"></i>
                                <span>Defina OKRs (Objetivos e Resultados Chave) para alinhar o foco da sua equipe.</span>
                            </small>
                        </div>

                        <div class="d-grid gap-2 d-sm-flex justify-content-sm-center">
                            <button type="button" class="btn btn-primary px-4" data-bs-toggle="modal"
                                data-bs-target="#modalNovoObjetivo">
                                <i class="bi bi-plus-lg me-2"></i>Criar Primeiro Objetivo
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <?php foreach ($objetivos as $obj): ?>
        <div class="col-md-12 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center" style="flex: 1;">
                        <button class="btn btn-sm btn-link text-dark me-2 p-0 text-decoration-none" type="button"
                            data-bs-toggle="collapse" data-bs-target="#collapseObj<?php echo $obj['id']; ?>">
                            <i class="bi bi-chevron-down"></i>
                        </button>
                        <div>
                            <h5 class="mb-0 fw-bold text-primary"><?php echo htmlspecialchars($obj['titulo']); ?></h5>
                            <small class="text-muted">
                                <i class="bi bi-calendar-event me-1"></i> Prazo:
                                <?php echo date('d/m/Y', strtotime($obj['prazo'])); ?>
                            </small>
                        </div>
                    </div>
                    <div class="d-flex align-items-center" style="width: 40%">
                        <div class="progress w-100 me-3" style="height: 10px;">
                            <div class="progress-bar <?php echo $obj['progresso'] == 100 ? 'bg-success' : 'bg-primary'; ?>"
                                role="progressbar" style="width: <?php echo $obj['progresso']; ?>%"></div>
                        </div>
                        <span class="fw-bold"><?php echo $obj['progresso']; ?>%</span>

                        <div class="dropdown ms-3">
                            <button class="btn btn-link btn-sm text-muted p-0" data-bs-toggle="dropdown">
                                <i class="bi bi-three-dots-vertical"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                                <li><button class="dropdown-item" onclick="openAddKrModal(<?php echo $obj['id']; ?>)"><i
                                            class="bi bi-plus-circle me-2"></i>Adicionar KR</button></li>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li><button class="dropdown-item text-danger"
                                        onclick="deleteObjective(<?php echo $obj['id']; ?>)"><i
                                            class="bi bi-trash me-2"></i>Excluir</button></li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="collapse" id="collapseObj<?php echo $obj['id']; ?>">
                    <div class="card-body bg-light">
                        <?php if (!empty($obj['descricao'])): ?>
                            <p class="text-muted small mb-3 fst-italic">
                                <?php echo nl2br(htmlspecialchars($obj['descricao'])); ?>
                            </p>
                        <?php endif; ?>

                        <h6 class="text-uppercase text-muted fw-bold small mb-3">Resultados Chave (KRs)</h6>

                        <?php if (empty($obj['krs'])): ?>
                            <a href="#" onclick="openAddKrModal(<?php echo $obj['id']; ?>)"
                                class="text-decoration-none text-muted small border dashed p-3 d-block text-center rounded">
                                <i class="bi bi-plus-lg me-1"></i> Adicionar primeiro resultado chave
                            </a>
                        <?php else: ?>
                            <div class="list-group list-group-flush rounded bg-white border">
                                <?php foreach ($obj['krs'] as $kr):
                                    $percent = 0;
                                    if ($kr['valor_meta'] != $kr['valor_inicial']) {
                                        $percent = (($kr['valor_atual'] - $kr['valor_inicial']) / ($kr['valor_meta'] - $kr['valor_inicial'])) * 100;
                                    }
                                    $percent = max(0, min(100, $percent));
                                    ?>
                                    <div class="list-group-item p-3">
                                        <div class="row align-items-center">
                                            <div class="col-md-5">
                                                <span
                                                    class="fw-bold d-block text-dark"><?php echo htmlspecialchars($kr['titulo']); ?></span>
                                                <small class="text-muted">
                                                    Meta: <?php echo $kr['valor_meta'] . ' ' . $kr['unidade']; ?>
                                                    (Início: <?php echo $kr['valor_inicial']; ?>)
                                                </small>
                                            </div>
                                            <div class="col-md-4">
                                                <input type="range" class="form-range" min="<?php echo $kr['valor_inicial']; ?>"
                                                    max="<?php echo $kr['valor_meta']; ?>" step="1"
                                                    value="<?php echo $kr['valor_atual']; ?>"
                                                    onchange="updateKrValue(<?php echo $kr['id']; ?>, this.value, <?php echo $obj['id']; ?>)">
                                            </div>
                                            <div class="col-md-3 text-end">
                                                <span class="badge bg-light text-dark border me-2">
                                                    Atual: <span
                                                        id="val-display-<?php echo $kr['id']; ?>"><?php echo $kr['valor_atual']; ?></span>
                                                    <?php echo $kr['unidade']; ?>
                                                </span>
                                                <span
                                                    class="badge <?php echo $percent >= 100 ? 'bg-success' : 'bg-secondary'; ?>"><?php echo round($percent); ?>%</span>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<!-- Modal Novo Objetivo -->
<div class="modal fade" id="modalNovoObjetivo" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Novo Objetivo</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="formObjetivo">
                    <input type="hidden" name="action" value="create_objective">
                    <div class="mb-3">
                        <label class="form-label">Título do Objetivo</label>
                        <input type="text" class="form-control" name="titulo"
                            placeholder="Ex: Dobrar o faturamento anual" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Vincular ao Projeto (Opcional)</label>
                        <select class="form-select" name="projeto_id">
                            <option value="">Sem vínculo (Objetivo Geral)</option>
                            <?php foreach ($projetos as $proj): ?>
                                <option value="<?php echo $proj['id']; ?>"><?php echo htmlspecialchars($proj['titulo']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Descrição (Opcional)</label>
                        <textarea class="form-control" name="descricao" rows="2"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Prazo</label>
                        <input type="date" class="form-control" name="prazo" required>
                    </div>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">Criar Objetivo</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal Novo KR -->
<div class="modal fade" id="modalKr" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Novo Resultado Chave (KR)</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="formKr">
                    <input type="hidden" name="action" value="create_kr">
                    <input type="hidden" name="objetivo_id" id="kr_objetivo_id">

                    <div class="mb-3">
                        <label class="form-label">O que será medido?</label>
                        <input type="text" class="form-control" name="titulo"
                            placeholder="Ex: Nº de consultas particulares" required>
                    </div>
                    <div class="row g-2">
                        <div class="col-4">
                            <label class="form-label">Valor Inicial</label>
                            <input type="number" class="form-control" name="valor_inicial" value="0" step="0.01"
                                required>
                        </div>
                        <div class="col-4">
                            <label class="form-label">Valor Meta</label>
                            <input type="number" class="form-control" name="valor_meta" step="0.01" required>
                        </div>
                        <div class="col-4">
                            <label class="form-label">Unidade</label>
                            <input type="text" class="form-control" name="unidade" value="un" placeholder="%, R$, un">
                        </div>
                    </div>
                    <div class="d-grid mt-4">
                        <button type="submit" class="btn btn-primary">Adicionar KR</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    const BASE_URL = '<?php echo BASE_URL; ?>';

    function handleFormResponse(response, modalId) {
        if (response.success) {
            location.reload();
        } else {
            alert('Erro: ' + response.message);
        }
    }

    document.getElementById('formObjetivo').addEventListener('submit', function (e) {
        e.preventDefault();
        fetch(BASE_URL + 'modules/admin/execucao-gestao/planejamento/acoes.php', { method: 'POST', body: fd })
            .then(r => r.json())
            .then(d => handleFormResponse(d));
    });

    document.getElementById('formKr').addEventListener('submit', function (e) {
        e.preventDefault();
        fetch(BASE_URL + 'modules/admin/execucao-gestao/planejamento/acoes.php', { method: 'POST', body: fd })
            .then(r => r.json())
            .then(d => handleFormResponse(d));
    });

    function openAddKrModal(objId) {
        document.getElementById('kr_objetivo_id').value = objId;
        new bootstrap.Modal(document.getElementById('modalKr')).show();
    }

    function updateKrValue(id, value, objId) {
        document.getElementById('val-display-' + id).innerText = value;

        const fd = new FormData();
        fd.append('action', 'update_kr_value');
        fd.append('id', id);
        fd.append('valor', value);
        fd.append('objetivo_id', objId);

        fetch(BASE_URL + 'modules/admin/execucao-gestao/planejamento/acoes.php', { method: 'POST', body: fd })
            .then(r => r.json())
            .then(d => {
                if (d.success) {
                    // Ideal seria atualizar apenas o progresso visualmente via JS, mas reload é seguro
                    setTimeout(() => location.reload(), 500);
                }
            });
    }

    function deleteObjective(id) {
        if (!confirm('Tem certeza? Isso apagará todos os KRs vinculados.')) return;

        const fd = new FormData();
        fd.append('action', 'delete_objective');
        fd.append('id', id);

        fetch(BASE_URL + 'modules/admin/execucao-gestao/planejamento/acoes.php', { method: 'POST', body: fd })
            .then(r => r.json())
            .then(d => handleFormResponse(d));
    }
</script>

<?php require_once __DIR__ . '/../../../../includes/footer.php'; ?>