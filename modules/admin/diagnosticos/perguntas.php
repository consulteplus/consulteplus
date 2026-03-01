<?php
$pageTitle = "Gerenciar Perguntas";
require_once __DIR__ . '/../header.php';
checkPermission(['superadmin']);

$modelo_id = isset($_GET['modelo_id']) ? intval($_GET['modelo_id']) : 0;
if ($modelo_id === 0) {
    redirect('modules/admin/diagnosticos');
}

// Get Model Info
$stmtModel = $conn->prepare("SELECT * FROM gestao_diagnostico_modelos WHERE id = ?");
$stmtModel->bind_param("i", $modelo_id);
$stmtModel->execute();
$modelo = $stmtModel->get_result()->fetch_assoc();

if (!$modelo) {
    die("Modelo não encontrado.");
}

// Handle Form Submission (Add/Edit)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? 'create';
    $secao = $_POST['secao'];
    $texto = $_POST['texto_pergunta'];
    $tipo = $_POST['tipo'];
    $opcoes = !empty($_POST['opcoes']) ? $_POST['opcoes'] : NULL;
    $logica = $_POST['logica_ia'];
    $ordem = intval($_POST['ordem']);
    $txtMin = $_POST['texto_min'] ?? '';
    $txtMax = $_POST['texto_max'] ?? '';

    if ($action === 'create') {
        $stmt = $conn->prepare("INSERT INTO gestao_diagnostico_perguntas (modelo_id, secao, texto_pergunta, tipo, opcoes, logica_ia, ordem, texto_min, texto_max) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("isssssiss", $modelo_id, $secao, $texto, $tipo, $opcoes, $logica, $ordem, $txtMin, $txtMax);
        $stmt->execute();
    } elseif ($action === 'edit') {
        $id = intval($_POST['id']);
        $stmt = $conn->prepare("UPDATE gestao_diagnostico_perguntas SET secao=?, texto_pergunta=?, tipo=?, opcoes=?, logica_ia=?, ordem=?, texto_min=?, texto_max=? WHERE id=?");
        $stmt->bind_param("sssssissi", $secao, $texto, $tipo, $opcoes, $logica, $ordem, $txtMin, $txtMax, $id);
        $stmt->execute();
    } elseif ($action === 'delete') {
        $id = intval($_POST['id']);
        $conn->query("DELETE FROM gestao_diagnostico_perguntas WHERE id=$id");
    }

    // Refresh to avoid resubmission
    echo "<script>window.location.href = window.location.href;</script>";
    exit;
}

// Fetch Questions
$sql = "SELECT * FROM gestao_diagnostico_perguntas WHERE modelo_id = $modelo_id ORDER BY ordem ASC";
$resPerguntas = $conn->query($sql);
?>

<div class="container-fluid py-4">
    <div class="mb-4">
        <a href="<?php echo BASE_URL; ?>modules/admin/diagnosticos"
            class="text-decoration-none text-muted mb-2 d-inline-block">
            <i class="bi bi-arrow-left me-1"></i> Voltar para Modelos
        </a>
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
            <h1 class="h3 mb-0 text-gray-800">
                Perguntas: <span class="text-primary"><?php echo htmlspecialchars($modelo['titulo']); ?></span>
            </h1>
            <div class="d-flex gap-2">
                <button class="btn btn-outline-primary text-nowrap" onclick="toggleAiModal()">
                    <i class="bi bi-stars me-2"></i><span class="d-none d-sm-inline">Gerar com IA</span>
                </button>
                <button class="btn btn-primary text-nowrap" onclick="openModal('create')">
                    <i class="bi bi-plus-lg me-2"></i><span class="d-none d-sm-inline">Nova Pergunta</span><span
                        class="d-inline d-sm-none">Nova</span>
                </button>
            </div>
        </div>
    </div>

    <?php
    // Group Questions by Section
    $grouped = [];
    $orderMap = [];
    if ($resPerguntas && $resPerguntas->num_rows > 0) {
        while ($p = $resPerguntas->fetch_assoc()) {
            $grouped[$p['secao']][] = $p;
        }
    }
    ?>

    <div class="row">
        <div class="col-12">
            <!-- Accordion Container -->
            <div id="accordionSections" class="accordion">
                <?php if (!empty($grouped)): ?>
                    <?php foreach ($grouped as $secaoName => $questions): ?>
                        <?php $safeId = md5($secaoName); ?>
                        <div class="accordion-item mb-3 border shadow-sm rounded overflow-hidden"
                            data-section="<?php echo htmlspecialchars($secaoName); ?>">
                            <h2 class="accordion-header bg-white d-flex align-items-stretch" id="heading<?php echo $safeId; ?>">
                                <div
                                    class="drag-handle d-flex align-items-center px-3 cursor-move bg-light border-end hover-bg-gray">
                                    <i class="bi bi-grip-vertical text-muted"></i>
                                </div>
                                <button class="accordion-button collapsed bg-white fw-bold text-dark border-0 shadow-none ps-3"
                                    type="button" data-bs-toggle="collapse" data-bs-target="#collapse<?php echo $safeId; ?>">
                                    <span
                                        class="badge bg-primary bg-opacity-10 text-primary me-2"><?php echo count($questions); ?></span>
                                    <?php echo htmlspecialchars($secaoName); ?>
                                </button>
                            </h2>
                            <div id="collapse<?php echo $safeId; ?>" class="accordion-collapse collapse"
                                data-bs-parent="#accordionSections">
                                <div class="accordion-body p-0">
                                    <div class="table-responsive">
                                        <table class="table table-hover align-middle mb-0">
                                            <thead class="bg-light">
                                                <tr>
                                                    <th class="ps-4" style="width: 50px;">Ord</th>
                                                    <th>Pergunta</th>
                                                    <th class="d-none d-md-table-cell">Tipo</th>
                                                    <th class="d-none d-lg-table-cell" style="width: 200px;">Lógica IA</th>
                                                    <th class="text-end pe-4" style="width: 120px;">Ações</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($questions as $p): ?>
                                                    <tr data-id="<?php echo $p['id']; ?>">
                                                        <td class="ps-4 text-muted">
                                                            <div class="d-flex align-items-center">
                                                                <i class="bi bi-grip-vertical me-2 drag-handle-question cursor-move"
                                                                    title="Arrastar"></i>
                                                                <span class="fw-bold"><?php echo $p['ordem']; ?></span>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <div class="fw-bold mb-1 text-break">
                                                                <?php echo htmlspecialchars($p['texto_pergunta']); ?>
                                                            </div>
                                                            <span class="badge bg-light text-dark border d-md-none mb-1">
                                                                <?php echo ucfirst($p['tipo']); ?>
                                                            </span>
                                                            <?php if ($p['tipo'] === 'selecao' || $p['tipo'] === 'multipla'): ?>
                                                                <small class="text-muted d-block text-truncate"
                                                                    style="max-width: 300px;">
                                                                    <i class="bi bi-list-ul me-1"></i>
                                                                    <?php echo htmlspecialchars($p['opcoes']); ?>
                                                                </small>
                                                            <?php endif; ?>
                                                        </td>
                                                        <td class="d-none d-md-table-cell">
                                                            <?php
                                                            $badges = [
                                                                'escala' => 'bg-info',
                                                                'selecao' => 'bg-primary',
                                                                'multipla' => 'bg-warning',
                                                                'texto' => 'bg-secondary',
                                                                'numero' => 'bg-dark'
                                                            ];
                                                            $bg = $badges[$p['tipo']] ?? 'bg-secondary';
                                                            ?>
                                                            <span
                                                                class="badge <?php echo $bg; ?>"><?php echo ucfirst($p['tipo']); ?></span>
                                                        </td>
                                                        <td class="small text-muted fst-italic d-none d-lg-table-cell">
                                                            <?php echo mb_strimwidth(htmlspecialchars($p['logica_ia'] ?? '-'), 0, 50, "..."); ?>
                                                        </td>
                                                        <td class="text-end pe-4 text-nowrap">
                                                            <button class="btn btn-sm btn-outline-primary me-1"
                                                                onclick='openModal("edit", <?php echo json_encode($p); ?>)'>
                                                                <i class="bi bi-pencil"></i>
                                                            </button>
                                                            <form method="POST" class="d-inline"
                                                                onsubmit="return confirm('Excluir pergunta?');">
                                                                <input type="hidden" name="action" value="delete">
                                                                <input type="hidden" name="id" value="<?php echo $p['id']; ?>">
                                                                <button type="submit" class="btn btn-sm btn-outline-danger"><i
                                                                        class="bi bi-trash"></i></button>
                                                            </form>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="card shadow-sm text-center py-5">
                        <div class="card-body">
                            <i class="bi bi-clipboard-plus display-4 text-muted opacity-25"></i>
                            <h5 class="mt-3 text-muted">Nenhuma pergunta encontrada.</h5>
                            <p class="small text-muted">Comece criando uma nova pergunta ou use a IA para gerar.</p>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Modal Pergunta -->
<div class="modal fade" id="questionModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="POST">
                <input type="hidden" name="action" id="modalAction" value="create">
                <input type="hidden" name="id" id="modalId" value="">

                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Nova Pergunta</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-2">
                            <label class="form-label">Ordem</label>
                            <input type="number" name="ordem" id="inputOrdem" class="form-control" value="0" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Seção</label>
                            <input type="text" name="secao" id="inputSecao" class="form-control" list="secoesList"
                                required>
                            <datalist id="secoesList">
                                <option value="perfil">Perfil</option>
                                <option value="financeiro">Financeiro</option>
                                <option value="operacional">Operacional</option>
                                <option value="aquisicao">Aquisição</option>
                                <option value="equipe">Equipe</option>
                                <option value="jornada">Jornada</option>
                            </datalist>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Tipo</label>
                            <select name="tipo" id="inputTipo" class="form-select" onchange="toggleOpcoes()">
                                <option value="escala">Escala (0-100)</option>
                                <option value="selecao">Seleção Única</option>
                                <option value="multipla">Múltipla Escolha</option>
                                <option value="texto">Texto Livre</option>
                                <option value="numero">Número</option>
                            </select>
                        </div>

                        <div class="col-12">
                            <label class="form-label">Texto da Pergunta</label>
                            <input type="text" name="texto_pergunta" id="inputTexto" class="form-control" required>
                        </div>

                        <!-- Opções (JSON) -->
                        <div class="col-12" id="divOpcoes" style="display:none;">
                            <label class="form-label">Opções (Formato JSON Array)</label>
                            <div class="input-group">
                                <textarea name="opcoes" id="inputOpcoes" class="form-control font-monospace" rows="3"
                                    placeholder='["Opção A", "Opção B", "Opção C"]'></textarea>
                                <button class="btn btn-outline-secondary" type="button"
                                    onclick="formatJson()">Format</button>
                            </div>
                            <small class="text-muted">Ex: ["Sim", "Não", "Talvez"]</small>
                        </div>

                        <!-- Escala Labels -->
                        <div class="col-md-6 divEscala">
                            <label class="form-label">Label Min (0)</label>
                            <input type="text" name="texto_min" id="inputMin" class="form-control"
                                placeholder="Péssimo">
                        </div>
                        <div class="col-md-6 divEscala">
                            <label class="form-label">Label Max (100)</label>
                            <input type="text" name="texto_max" id="inputMax" class="form-control"
                                placeholder="Excelente">
                        </div>

                        <div class="col-12">
                            <label class="form-label">Lógica IA (Contexto)</label>
                            <textarea name="logica_ia" id="inputLogica" class="form-control" rows="2"
                                placeholder="Explique para a IA o que essa resposta significa..."></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Salvar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal IA -->
<div class="modal fade" id="aiModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header border-bottom-0 pb-0">
                <h5 class="modal-title fw-bold"><i class="bi bi-stars text-primary me-2"></i>Gerar Diagnóstico com IA
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body pt-4">
                <p class="text-muted small mb-3">Descreva o objetivo deste diagnóstico e a IA criará as perguntas para
                    você.</p>

                <textarea class="form-control bg-light border-0 shadow-sm mb-3" id="aiPrompt" rows="4"
                    placeholder="Ex: Diagnóstico completo para clínicas odontológicas focado em vendas e marketing..."></textarea>

                <div id="aiPreview" class="d-none bg-light p-3 rounded small mb-3 border-start border-4 border-primary"
                    style="max-height: 200px; overflow-y: auto;">
                    <h6 class="fw-bold mb-2">Perguntas Sugeridas:</h6>
                    <ul id="aiList" class="ps-3 mb-0"></ul>
                </div>

                <button class="btn btn-primary w-100 fw-bold rounded-pill" id="btnGenerateAi" onclick="generateAi()">
                    <i class="bi bi-lightning-charge-fill me-1"></i> Gerar Perguntas
                </button>

                <button class="btn btn-success w-100 fw-bold rounded-pill d-none mt-2" id="btnSaveAi"
                    onclick="saveAi()">
                    <i class="bi bi-check-lg me-1"></i> Salvar no Banco
                </button>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../../../includes/footer.php'; ?>

<!-- SortableJS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.14.0/Sortable.min.js"></script>
<style>
    .cursor-move {
        cursor: move;
    }

    .drag-handle:hover {
        color: #0d6efd !important;
    }

    .hover-bg-gray:hover {
        background-color: #f8f9fa !important;
    }
</style>

<script>
    // Sortable Sections Initialization
    document.addEventListener('DOMContentLoaded', function () {
        const el = document.getElementById('accordionSections');
        if (el && typeof Sortable !== 'undefined') {
            // Sort Sections
            Sortable.create(el, {
                handle: '.drag-handle',
                animation: 150,
                ghostClass: 'bg-light',
                onEnd: function (evt) {
                    const items = el.querySelectorAll('[data-section]');
                    const sections = Array.from(items).map(item => item.getAttribute('data-section'));

                    // Send new order
                    fetch('<?php echo BASE_URL; ?>modules/admin/diagnosticos/reordenar_secoes.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ modelo_id: <?php echo $modelo_id; ?>, sections: sections })
                    })
                        .then(r => r.json())
                        .then(d => {
                            if (!d.success) alert('Erro ao salvar ordem das seções: ' + d.message);
                        });
                }
            });

            // Sort Questions (Inside each accordion)
            const questionTables = document.querySelectorAll('.table tbody');
            questionTables.forEach(tbody => {
                Sortable.create(tbody, {
                    handle: '.drag-handle-question',
                    animation: 150,
                    ghostClass: 'bg-light',
                    forceFallback: true, // Fix for tables
                    onEnd: function (evt) {
                        const rows = tbody.querySelectorAll('tr[data-id]');
                        const ids = Array.from(rows).map(row => row.getAttribute('data-id'));

                        fetch('<?php echo BASE_URL; ?>modules/admin/diagnosticos/reordenar_perguntas.php', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json' },
                            body: JSON.stringify({ ids: ids })
                        })
                            .then(r => r.json())
                            .then(d => {
                                if (!d.success) alert('Erro ao salvar ordem das perguntas: ' + d.message);
                            });
                    }
                });
            });
        }
    });

    const modalEl = document.getElementById('questionModal');
    let modal;

    document.addEventListener('DOMContentLoaded', function () {
        if (typeof bootstrap !== 'undefined') {
            modal = new bootstrap.Modal(modalEl);
        }
    });

    function toggleOpcoes() {
        const tipo = document.getElementById('inputTipo').value;
        const divOpcoes = document.getElementById('divOpcoes');
        const divEscala = document.querySelectorAll('.divEscala');

        if (tipo === 'selecao' || tipo === 'multipla') {
            divOpcoes.style.display = 'block';
        } else {
            divOpcoes.style.display = 'none';
        }

        divEscala.forEach(el => {
            el.style.display = (tipo === 'escala') ? 'block' : 'none';
        });
    }

    function openModal(mode, data = null) {
        document.getElementById('modalAction').value = mode;
        if (mode === 'edit' && data) {
            document.getElementById('modalTitle').innerText = 'Editar Pergunta #' + data.id;
            document.getElementById('modalId').value = data.id;
            document.getElementById('inputOrdem').value = data.ordem;
            document.getElementById('inputSecao').value = data.secao;
            document.getElementById('inputTipo').value = data.tipo;
            document.getElementById('inputTexto').value = data.texto_pergunta;
            document.getElementById('inputOpcoes').value = data.opcoes;
            document.getElementById('inputLogica').value = data.logica_ia;
            document.getElementById('inputMin').value = data.texto_min;
            document.getElementById('inputMax').value = data.texto_max;
        } else {
            document.getElementById('modalTitle').innerText = 'Nova Pergunta';
            document.getElementById('modalId').value = '';
            document.getElementById('inputOrdem').value = 0;
            document.getElementById('inputSecao').value = '';
            document.getElementById('inputTipo').value = 'escala';
            document.getElementById('inputTexto').value = '';
            document.getElementById('inputOpcoes').value = '';
            document.getElementById('inputLogica').value = '';
            document.getElementById('inputMin').value = '';
            document.getElementById('inputMax').value = '';
        }
        toggleOpcoes();

        // Lazy init if not ready yet
        if (!modal && typeof bootstrap !== 'undefined') {
            modal = new bootstrap.Modal(modalEl);
        }

        if (modal) modal.show();
    }

    function formatJson() {
        const el = document.getElementById('inputOpcoes');
        try {
            const obj = JSON.parse(el.value);
            el.value = JSON.stringify(obj, null, 2);
        } catch (e) {
            alert('JSON inválido');
        }
    }

    // --- AI LOGIC ---
    const aiModalEl = document.getElementById('aiModal');
    let aiModal;
    let generatedQuestions = [];

    document.addEventListener('DOMContentLoaded', function () {
        if (typeof bootstrap !== 'undefined') {
            aiModal = new bootstrap.Modal(aiModalEl);
        }
    });

    function toggleAiModal() {
        if (!aiModal && typeof bootstrap !== 'undefined') aiModal = new bootstrap.Modal(aiModalEl);
        aiModal.show();
    }

    function generateAi() {
        const prompt = document.getElementById('aiPrompt').value;
        if (!prompt) return alert('Digite uma descrição.');

        const btn = document.getElementById('btnGenerateAi');
        const originalHtml = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Criando...';

        const systemPrompt = `
### ROLE
Você é um Consultor de Negócios especialista. Crie um questionário de diagnóstico estruturado.

### FORMATO DE SAÍDA (JSON)
{
  "perguntas": [
    {
      "secao": "Nome da Categoria",
      "texto_pergunta": "Texto da pergunta?",
      "tipo": "selecao" | "escala" | "texto" | "numero" | "multipla",
      "opcoes": ["Op1", "Op2"] (Obrigatório se tipo for selecao/multipla, senão null),
      "logica_ia": "Explicação da intenção...",
      "texto_min": "Label 0 (apenas se escala)",
      "texto_max": "Label 100 (apenas se escala)"
    }
  ]
}

### REGRAS
- Idioma: Português (Brasil).
- Crie de 5 a 10 perguntas estratégicas POR SEÇÃO.
- Use "escala" para avaliações qualitativas (0-100).
- Use "selecao" para cenários específicos.
`;

        const payload = { prompt_sistema: systemPrompt, user_input: prompt };

        // Use Diagnostic Proxy
        fetch('<?php echo BASE_URL; ?>modules/gestao/diagnostico/proxy_n8n.php?type=diagnostic', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(payload)
        })
            .then(async response => {
                const text = await response.text();
                let data;
                try {
                    data = JSON.parse(text);
                } catch (e) {
                    console.error('SERVER RESPONSE:', text);
                    throw new Error('O servidor retornou um erro HTML em vez de JSON. Consulte o console do navegador para ver o erro detalhado.');
                }
                return data;
            })
            .then(data => {
                if (data.error) {
                    throw new Error(data.error);
                }

                let content = null;
                // Robust parsing logic similar to projects
                if (data.output) content = data.output;
                else if (data.message && data.message.content) content = data.message.content;
                else if (Array.isArray(data) && data[0].output) content = data[0].output;
                else content = data;

                if (typeof content === 'string') {
                    try {
                        content = JSON.parse(content.replace(/```json/g, '').replace(/```/g, '').trim());
                    } catch (e) { console.error(e); }
                }

                if (content && content.perguntas) {
                    generatedQuestions = content.perguntas;
                    renderAiPreview();
                    document.getElementById('btnSaveAi').classList.remove('d-none');
                    btn.classList.add('d-none'); // Hide generate button
                } else {
                    throw new Error('Formato inválido: ' + JSON.stringify(content));
                }
            })
            .catch(e => {
                alert('Erro: ' + e.message);
                btn.innerHTML = originalHtml;
                btn.disabled = false;
            });
    }

    function renderAiPreview() {
        const list = document.getElementById('aiList');
        const container = document.getElementById('aiPreview');
        list.innerHTML = '';
        container.classList.remove('d-none');

        generatedQuestions.forEach(q => {
            list.innerHTML += `<li><span class="badge bg-secondary me-2">${q.secao}</span> ${q.texto_pergunta}</li>`;
        });
    }

    function saveAi() {
        const btn = document.getElementById('btnSaveAi');
        btn.disabled = true;
        btn.innerHTML = 'Salvando...';

        fetch('<?php echo BASE_URL; ?>modules/admin/diagnosticos/salvar_perguntas_ia.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                modelo_id: <?php echo $modelo_id; ?>,
                perguntas: generatedQuestions
            })
        })
            .then(r => r.json())
            .then(d => {
                if (d.success) {
                    location.reload();
                } else {
                    alert('Erro ao salvar: ' + d.error);
                    btn.disabled = false;
                    btn.innerHTML = 'Tentar Novamente';
                }
            });
    }
</script>