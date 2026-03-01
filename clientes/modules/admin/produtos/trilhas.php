<?php
$pageTitle = "Gerenciar Conteúdo";
require_once __DIR__ . '/../header.php';
checkPermission(['admin']);

$produto_id = isset($_GET['produto_id']) ? intval($_GET['produto_id']) : 0;
$company_id = $_SESSION['company_id'];

// Verificar se o produto existe e pertence à empresa
$stmt = $conn->prepare("SELECT * FROM mentoria_produtos WHERE id = ? AND company_id = ?");
$stmt->bind_param("ii", $produto_id, $company_id);
$stmt->execute();
$produto = $stmt->get_result()->fetch_assoc();

if (!$produto) {
    die("Produto não encontrado ou acesso negado.");
}

// PROCESSAR FORMULÁRIOS (LEGADO REMOVIDO) - Agora via API
// if ($_SERVER['REQUEST_METHOD'] === 'POST') { ... }

// BUSCAR DADOS
require_once __DIR__ . '/../../../classes/ProductContentService.php';
$contentService = new ProductContentService();

// Carregar Trilhas e Conteúdos
// Para otimizar, podemos carregar tudo aqui ou usar AJAX. Vamos manter PHP inicial.
$trilhas = $contentService->listarTrilhas($produto_id);

// Buscar listas de recursos (para o modal)
$diags_res = $conn->query("SELECT id, titulo FROM gestao_diagnostico_modelos WHERE ativo = 1 ORDER BY titulo ASC");
$tools_res = $conn->query("SELECT id, nome FROM ferramentas_tipos WHERE ativo = 1 ORDER BY nome ASC");
?>

<div class="container-fluid py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1">
                    <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>admin/produtos">Produtos</a></li>
                    <li class="breadcrumb-item active">
                        <?php echo htmlspecialchars($produto['titulo']); ?>
                    </li>
                </ol>
            </nav>
            <h1 class="h3 mb-0 text-gray-800">Estrutura do Curso</h1>
        </div>
        <div class="btn-group">
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalNovaTrilha">
                <i class="bi bi-folder-plus me-2"></i>Nova Trilha/Módulo
            </button>
            <button type="button" class="btn btn-primary dropdown-toggle dropdown-toggle-split"
                data-bs-toggle="dropdown" aria-expanded="false">
                <span class="visually-hidden">Toggle Dropdown</span>
            </button>
            <ul class="dropdown-menu dropdown-menu-end">
                <li>
                    <a class="dropdown-item"
                        href="<?php echo BASE_URL; ?>admin/produtos/ia-generator/<?php echo $produto_id; ?>">
                        <i class="bi bi-stars me-2 text-warning"></i>Criar com IA (Sugestões)
                    </a>
                </li>
            </ul>
        </div>
    </div>

    <!-- SortableJS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.14.0/Sortable.min.js"></script>

    <!-- Lista de Trilhas -->
    <div class="accordion" id="accordionTrilhas">
        <?php if (!empty($trilhas)): ?>
            <?php foreach ($trilhas as $trilha): ?>
                <div class="accordion-item mb-3 border shadow-sm rounded overflow-hidden"
                    data-id="<?php echo $trilha['id']; ?>">
                    <h2 class="accordion-header d-flex align-items-stretch bg-white" id="heading<?php echo $trilha['id']; ?>">
                        <div class="drag-handle d-flex align-items-center px-3 cursor-move bg-light border-end hover-bg-gray">
                            <i class="bi bi-grip-vertical text-muted"></i>
                        </div>
                        <button class="accordion-button collapsed bg-white fw-bold text-dark border-0 shadow-none ps-3"
                            type="button" data-bs-toggle="collapse" data-bs-target="#collapse<?php echo $trilha['id']; ?>">
                            <div class="d-flex align-items-center w-100 me-3">
                                <span class="me-auto">
                                    <i class="bi bi-collection-play me-2 text-primary"></i>
                                    <?php echo htmlspecialchars($trilha['titulo']); ?>
                                </span>
                                <span class="badge bg-light text-muted border me-3">
                                    <?php echo $trilha['qtd_aulas']; ?> aulas
                                </span>
                            </div>
                        </button>
                    </h2>
                    <div id="collapse<?php echo $trilha['id']; ?>" class="accordion-collapse collapse"
                        data-bs-parent="#accordionTrilhas">
                        <div class="accordion-body bg-light">
                            <!-- Ações da Trilha -->
                            <div class="d-flex justify-content-end mb-3 border-bottom pb-2">
                                <button class="btn btn-sm btn-outline-success me-2"
                                    onclick="abrirModalConteudo(<?php echo $trilha['id']; ?>)">
                                    <i class="bi bi-plus-lg"></i> Adicionar Aula
                                </button>
                                <button class="btn btn-sm btn-outline-danger"
                                    onclick="excluirTrilha(<?php echo $trilha['id']; ?>)">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>

                            <!-- Lista de Conteúdos -->
                            <?php
                            // Carregar conteúdos desta trilha usando Service
                            $conteudos = $contentService->listarConteudos($trilha['id']);
                            ?>
                            <?php
                            // Carregar conteúdos desta trilha usando Service
                            $conteudos = $contentService->listarConteudos($trilha['id']);
                            ?>

                            <?php if (!empty($conteudos)): ?>
                                <ul class="list-group list-group-flush rounded">
                                    <?php foreach ($conteudos as $aula): ?>
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            <div>
                                                <i
                                                    class="bi bi-<?php echo $aula['tipo'] === 'video' ? 'play-circle' : ($aula['tipo'] === 'diagnostico' ? 'ui-checks-grid' : ($aula['tipo'] === 'ferramenta' ? 'tools' : 'file-text')); ?> me-2 text-secondary"></i>
                                                <a href="<?php echo BASE_URL; ?>produtos/aula/<?php echo $aula['id']; ?>"
                                                    target="_blank" class="text-decoration-none text-dark">
                                                    <?php echo htmlspecialchars($aula['titulo']); ?>
                                                    <i class="bi bi-box-arrow-up-right small text-muted ms-1"
                                                        style="font-size:0.75rem;"></i>
                                                </a>
                                                <?php if ($aula['tem_tarefa']): ?>
                                                    <span class="badge bg-warning text-dark ms-2" style="font-size: 0.65em;">
                                                        <i class="bi bi-check-square"></i> Tarefa
                                                    </span>
                                                <?php endif; ?>

                                                <?php if ($aula['tipo'] === 'diagnostico' && $aula['diag_titulo']): ?>
                                                    <span class="badge bg-info text-dark ms-2" style="font-size: 0.65em;">
                                                        <i class="bi bi-ui-checks-grid"></i> Diagnóstico:
                                                        <?php echo htmlspecialchars($aula['diag_titulo']); ?>
                                                    </span>
                                                <?php elseif ($aula['tipo'] === 'ferramenta' && $aula['tool_nome']): ?>
                                                    <span class="badge bg-primary ms-2" style="font-size: 0.65em;">
                                                        <i class="bi bi-tools"></i> Ferramenta:
                                                        <?php echo htmlspecialchars($aula['tool_nome']); ?>
                                                    </span>
                                                <?php endif; ?>
                                            </div>
                                            <div class="d-flex align-items-center">
                                                <a href="<?php echo BASE_URL; ?>admin/produtos/conteudo?id=<?php echo $aula['id']; ?>"
                                                    class="btn btn-sm text-primary border-0 me-2" title="Editar Aula">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                <button class="btn btn-sm text-danger border-0 p-0" title="Excluir"
                                                    onclick="excluirConteudo(<?php echo $aula['id']; ?>)">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </div>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php else: ?>
                                <p class="text-muted text-center py-3 mb-0 small">Nenhuma aula nesta trilha ainda.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="text-center py-5">
                <i class="bi bi-layers text-muted display-4"></i>
                <p class="mt-3 text-muted">Este produto ainda não tem conteúdo.</p>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalNovaTrilha">
                    Criar Primeiro Módulo
                </button>
            </div>
        <?php endif; ?>
    </div>

    <!-- Modal Nova Trilha -->
    <div class="modal fade" id="modalNovaTrilha" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="formNovaTrilha" onsubmit="salvarTrilha(event)">
                    <input type="hidden" name="produto_id" value="<?php echo $produto_id; ?>">
                    <div class="modal-header">
                        <h5 class="modal-title">Novo Módulo/Trilha</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Título</label>
                            <input type="text" name="titulo" class="form-control" required
                                placeholder="Ex: Módulo 1 - Fundamentos">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Ordem</label>
                            <input type="number" name="ordem" class="form-control" value="1">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                        <button type="submit" class="btn btn-primary">Salvar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Nova Trilha IA -->
    <div class="modal fade" id="modalNovaTrilhaIA" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-stars text-warning me-2"></i>Criar Módulo com IA</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="formGerarModuloIA">
                        <input type="hidden" name="produto_id" value="<?php echo $produto_id; ?>">
                        <div class="mb-3">
                            <label class="form-label">Tema do Módulo</label>
                            <input type="text" id="ia_tema" class="form-control" required
                                placeholder="Ex: Finanças para Iniciantes">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Contexto (Opcional)</label>
                            <textarea id="ia_contexto" class="form-control" rows="2"
                                placeholder="Ex: Focado em pequenos empresários..."></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Quantidade de Aulas</label>
                            <input type="number" id="ia_qtd_aulas" class="form-control" value="5" min="1" max="15">
                        </div>
                    </form>

                    <!-- Loading State -->
                    <div id="iaLoading" class="text-center py-4" style="display: none;">
                        <div class="spinner-border text-primary mb-3" role="status"></div>
                        <p class="text-muted blink-text">Criando estrutura do módulo...</p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" onclick="gerarModuloIA()">
                        <i class="bi bi-magic me-2"></i>Gerar Módulo
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        async function gerarModuloIA() {
            const btn = document.querySelector('#modalNovaTrilhaIA .btn-primary');
            const form = document.getElementById('formGerarModuloIA');
            const loading = document.getElementById('iaLoading');
            const inputs = {
                tema: document.getElementById('ia_tema').value,
                contexto: document.getElementById('ia_contexto').value,
                qtd_aulas: document.getElementById('ia_qtd_aulas').value,
                produto_id: <?php echo $produto_id; ?>
            };

            if (!inputs.tema) {
                alert('Por favor, informe o tema do módulo.');
                return;
            }

            // UI State
            form.style.display = 'none';
            loading.style.display = 'block';
            btn.disabled = true;

            try {
                const response = await fetch('<?php echo BASE_URL; ?>api/n8n/gerar_modulo.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(inputs)
                });

                const data = await response.json();

                if (!data.success && !data.title) {
                    // Check if it's the raw JSON from n8n (sometimes n8n returns just the object without 'success' wrapper)
                    // If data.title exists, it succeeded.
                    if (!data.title) throw new Error(data.message || 'Erro desconhecido');
                }

                // Success! Reload to show new module
                window.location.reload();

            } catch (error) {
                console.error(error);
                alert('Erro ao gerar módulo: ' + error.message);
                // Reset UI
                form.style.display = 'block';
                loading.style.display = 'none';
                btn.disabled = false;
            }
        }
    </script>
    <div class="modal fade" id="modalNovoConteudo" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-content">
                    <form id="formNovoConteudo" onsubmit="salvarConteudo(event)">
                        <input type="hidden" name="trilha_id" id="inputTrilhaId">
                        <div class="modal-header">
                            <h5 class="modal-title">Adicionar Aula</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-8 mb-3">
                                    <label class="form-label">Título da Aula</label>
                                    <input type="text" name="titulo" class="form-control" required>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Tipo</label>
                                    <select name="tipo" class="form-select" id="selectTipoAula">
                                        <option value="video">Vídeo</option>
                                        <option value="texto">Texto/Artigo</option>
                                        <option value="diagnostico">Recurso: Diagnóstico</option>
                                        <option value="ferramenta">Recurso: Ferramenta</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Seleção de Recurso (Condicional para Modal) -->
                            <div id="boxResourceConfigModal" class="mb-3 p-3 bg-light border rounded"
                                style="display:none;">
                                <div id="selectDiagBoxModal" class="mb-0" style="display:none;">
                                    <label class="form-label small fw-bold">Selecione o Diagnóstico</label>
                                    <select name="resource_id_diag" class="form-select text-dark">
                                        <option value="">-- Selecione --</option>
                                        <?php
                                        if ($diags_res) {
                                            $diags_res->data_seek(0);
                                            while ($d = $diags_res->fetch_assoc()) {
                                                echo "<option value='{$d['id']}'>{$d['titulo']}</option>";
                                            }
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div id="selectToolBoxModal" class="mb-0" style="display:none;">
                                    <label class="form-label small fw-bold">Selecione a Ferramenta</label>
                                    <select name="resource_id_tool" class="form-select text-dark">
                                        <option value="">-- Selecione --</option>
                                        <?php
                                        if ($tools_res) {
                                            $tools_res->data_seek(0);
                                            while ($t = $tools_res->fetch_assoc()) {
                                                echo "<option value='{$t['id']}'>{$t['nome']}</option>";
                                            }
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>

                            <div id="campoVideo" class="mb-3">
                                <label class="form-label">Link do Vídeo (Embed)</label>
                                <input type="url" name="url_video" class="form-control"
                                    placeholder="https://www.youtube.com/embed/...">
                                <div class="form-text">Youtube, Vimeo, PandaVideo, etc.</div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Descrição / Resumo</label>
                                <textarea name="descricao" class="form-control" rows="3"></textarea>
                            </div>

                            <hr>

                            <div class="form-check form-switch mb-3">
                                <input class="form-check-input" type="checkbox" name="tem_tarefa" id="checkTarefa"
                                    onchange="toggleTarefa()">
                                <label class="form-check-label fw-bold" for="checkTarefa">Incluir Tarefa para o
                                    Aluno</label>
                            </div>

                            <div id="areaTarefa" style="display:none;" class="bg-light p-3 rounded">
                                <div class="mb-3">
                                    <label class="form-label">Instruções da Tarefa</label>
                                    <textarea name="tarefa_descricao" class="form-control" rows="2"
                                        placeholder="O que o aluno deve fazer?"></textarea>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Tipo de Entrega</label>
                                    <select name="tarefa_tipo_entrega" class="form-select">
                                        <option value="texto">Apenas Texto</option>
                                        <option value="arquivo">Upload de Arquivo</option>
                                        <option value="ambos">Texto + Arquivo</option>
                                    </select>
                                </div>
                            </div>

                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn btn-primary">Adicionar Aula</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Scripts da API -->
        <script>
            const API_URL = '<?php echo BASE_URL; ?>modules/admin/produtos/acoes_conteudo.php';

            function salvarTrilha(e) {
                e.preventDefault();
                const form = document.getElementById('formNovaTrilha');
                const data = Object.fromEntries(new FormData(form).entries());

                fetch(API_URL + '?acao=salvar_trilha', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(data)
                })
                    .then(res => res.json())
                    .then(response => {
                        if (response.success) {
                            Swal.fire({ icon: 'success', title: 'Sucesso', text: response.message, timer: 1500, showConfirmButton: false })
                                .then(() => location.reload());
                        } else {
                            Swal.fire('Erro', response.message, 'error');
                        }
                    });
            }

            function excluirTrilha(id) {
                Swal.fire({
                    title: 'Excluir Trilha?',
                    text: "Todas as aulas dentro dela serão perdidas.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    confirmButtonText: 'Sim, excluir'
                }).then((result) => {
                    if (result.isConfirmed) {
                        fetch(API_URL + '?acao=excluir_trilha', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json' },
                            body: JSON.stringify({ id: id })
                        })
                            .then(res => res.json())
                            .then(response => {
                                if (response.success) {
                                    Swal.fire('Excluído!', 'Trilha removida.', 'success').then(() => location.reload());
                                } else {
                                    Swal.fire('Erro', response.message, 'error');
                                }
                            });
                    }
                });
            }

            function salvarConteudo(e) {
                e.preventDefault();
                const form = document.getElementById('formNovoConteudo');
                const formData = new FormData(form);
                const data = Object.fromEntries(formData.entries());
                data.tem_tarefa = document.getElementById('checkTarefa').checked ? 1 : 0;

                fetch(API_URL + '?acao=salvar_conteudo', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(data)
                })
                    .then(res => res.json())
                    .then(response => {
                        if (response.success) {
                            Swal.fire({ icon: 'success', title: 'Sucesso', text: 'Conteúdo criado!', timer: 1500, showConfirmButton: false })
                                .then(() => location.reload());
                        } else {
                            Swal.fire('Erro', response.message, 'error');
                        }
                    });
            }

            function excluirConteudo(id) {
                Swal.fire({
                    title: 'Excluir Aula?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    confirmButtonText: 'Sim, excluir'
                }).then((result) => {
                    if (result.isConfirmed) {
                        fetch(API_URL + '?acao=excluir_conteudo', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json' },
                            body: JSON.stringify({ id: id })
                        })
                            .then(res => res.json())
                            .then(response => {
                                if (response.success) {
                                    Swal.fire('Excluído!', 'Aula removida.', 'success').then(() => location.reload());
                                } else {
                                    Swal.fire('Erro', response.message || 'Erro desconhecido', 'error');
                                }
                            });
                    }
                });
            }
        </script>

        <script>
            function abrirModalConteudo(trilhaId) {
                document.getElementById('inputTrilhaId').value = trilhaId;
                var modal = new bootstrap.Modal(document.getElementById('modalNovoConteudo'));
                modal.show();
            }

            function toggleTarefa() {
                var check = document.getElementById('checkTarefa');
                var area = document.getElementById('areaTarefa');
                area.style.display = check.checked ? 'block' : 'none';
            }

            // Script para controlar exibição dos recursos no Modal
            document.addEventListener('DOMContentLoaded', function () {
                const selectTipo = document.getElementById('selectTipoAula');
                const boxResource = document.getElementById('boxResourceConfigModal');
                const boxDiag = document.getElementById('selectDiagBoxModal');
                const boxTool = document.getElementById('selectToolBoxModal');

                if (selectTipo) {
                    selectTipo.addEventListener('change', function () {
                        const val = this.value;
                        if (val === 'diagnostico' || val === 'ferramenta') {
                            boxResource.style.display = 'block';
                            if (val === 'diagnostico') {
                                boxDiag.style.display = 'block';
                                boxTool.style.display = 'none';
                            } else {
                                boxDiag.style.display = 'none';
                                boxTool.style.display = 'block';
                            }
                        } else {
                            boxResource.style.display = 'none';
                        }
                    });
                }
            });
        </script>

        <script>
            // Sortable JS Init
            const el = document.getElementById('accordionTrilhas');
            if (el) {
                Sortable.create(el, {
                    handle: '.drag-handle', // Drag handle selector within list items
                    animation: 150,  // ms, animation speed moving items when sorting, `0` — without animation
                    ghostClass: 'bg-light',  // Class name for the drop placeholder
                    onEnd: function (evt) {
                        const items = el.querySelectorAll('[data-id]');
                        const order = Array.from(items).map(item => item.getAttribute('data-id'));

                        // Send new order to API
                        fetch(API_URL + '?acao=reordenar_trilhas', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json' },
                            body: JSON.stringify({ order: order })
                        })
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    // Optional: Show toast
                                } else {
                                    alert('Erro ao salvar ordem.');
                                }
                            })
                            .catch(error => console.error('Error:', error));
                    },
                });
            }
        </script>

        <style>
            .cursor-move {
                cursor: move;
            }

            .drag-handle:hover {
                color: #0d6efd !important;
            }
        </style>


        <!-- SweetAlert2 -->
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

        <?php require_once __DIR__ . '/../../../includes/footer.php'; ?>