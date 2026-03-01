<?php
// modules/admin/mentoria/conteudo.php
$pageTitle = "Editar Aula";
require_once __DIR__ . '/../header.php';
checkPermission(['admin']);

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Buscar aula usando Service
require_once __DIR__ . '/../../../classes/ProductContentService.php';
$contentService = new ProductContentService();

$aula = $contentService->buscarConteudo($id);

if (!$aula) {
    echo "<div class='container py-5'><div class='alert alert-danger'>Aula não encontrada.</div></div>";
    require_once __DIR__ . '/../../../includes/footer.php';
    exit;
}

// Buscar trilha e produto para breadcrumb/voltar
$trilha_res = $conn->query("SELECT * FROM mentoria_trilhas WHERE id = " . $aula['trilha_id']);
$trilha = $trilha_res->fetch_assoc();

// Buscar listas de recursos
$diags_res = $conn->query("SELECT id, titulo FROM gestao_diagnostico_modelos WHERE ativo = 1 ORDER BY titulo ASC");
$tools_res = $conn->query("SELECT id, nome FROM ferramentas_tipos WHERE ativo = 1 ORDER BY nome ASC");
?>

<style>
/* Custom Layout */
.full-height-col {
    height: calc(100vh - 140px);
    overflow-y: auto;
}
.script-area {
    font-family: 'Courier New', monospace;
    font-size: 15px; 
    line-height: 1.6;
    border: none;
    resize: none;
    background-color: #fff;
}
.script-area:focus {
    background-color: #f8fcfd;
    box-shadow: none;
    outline: none;
}
.preview-card {
    background: #f8f9fa;
    border: 1px solid #e9ecef;
}
.preview-header {
    background: white;
    border-bottom: 1px solid #eee;
    padding: 15px;
}
.live-input {
    border: 1px dashed #ccc;
    background: transparent;
    transition: all 0.2s;
}
.live-input:focus {
    border: 1px solid #86b7fe;
    background: white;
}
.video-placeholder {
    aspect-ratio: 16/9;
    background: #000;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    border-radius: 8px;
    overflow: hidden;
}
</style>

<div class="container-fluid py-0 px-0 h-100">
    <form id="formConteudo" onsubmit="salvarConteudo(event)" class="d-flex flex-column h-100">
        <input type="hidden" name="id" value="<?php echo $id; ?>">
        <input type="hidden" name="trilha_id" value="<?php echo $aula['trilha_id']; ?>">
        
        <!-- Header Toolbar -->
        <div class="bg-white border-bottom px-4 py-3 d-flex justify-content-between align-items-center shadow-sm" style="z-index: 10;">
            <div class="d-flex align-items-center">
                <a href="<?php echo BASE_URL; ?>admin/produtos/trilhas?produto_id=<?php echo $produto_id; ?>" class="btn btn-outline-secondary btn-sm me-3">
                    <i class="bi bi-arrow-left"></i>
                </a>
                <div>
                    <h1 class="h5 mb-0 fw-bold">Editar Aula</h1>
                    <small class="text-muted"><?php echo htmlspecialchars($trilha['titulo']); ?></small>
                </div>
            </div>
            <div>
                <button type="submit" class="btn btn-primary px-4">
                    <i class="bi bi-cloud-arrow-up me-2"></i> Salvar Alterações
                </button>
            </div>
        </div>

        <?php if(isset($error)): ?>
            <div class="alert alert-danger m-3"><?php echo $error; ?></div>
        <?php endif; ?>

        <div class="row g-0 flex-grow-1">
            
            <!-- ESQUERDA: ROTEIRO & PLANEJAMENTO (35%) -->
            <div class="col-lg-4 border-end bg-white d-flex flex-column full-height-col">
                <div class="p-3 border-bottom d-flex justify-content-between align-items-center bg-light">
                    <span class="fw-bold text-uppercase small text-muted"><i class="bi bi-file-text me-1"></i> Roteiro Interno</span>
                    <button type="button" class="btn btn-sm btn-outline-primary bg-white" onclick="gerarRoteiroIA()">
                        <i class="bi bi-magic me-1"></i> IA
                    </button>
                </div>
                <div class="flex-grow-1 d-flex flex-column p-0">
                    <textarea name="roteiro" id="descRoteiro" class="form-control script-area flex-grow-1 p-4" 
                              placeholder="Use este espaço para escrever seu roteiro, anotações ou planejamento. O aluno NÃO vê isso."><?php echo htmlspecialchars($aula['roteiro'] ?? ''); ?></textarea>
                </div>
                
                <!-- Configs Rápidas no Roteiro -->
                <div class="p-3 bg-light border-top">
                    <label class="form-label small fw-bold">Configurações Extras</label>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="tem_tarefa" id="checkTarefaEdit" 
                               <?php echo $aula['tem_tarefa'] ? 'checked' : ''; ?> onchange="toggleTarefaEdit()">
                        <label class="form-check-label small" for="checkTarefaEdit">Esta aula tem Tarefa?</label>
                    </div>
                    
                    <div id="areaTarefaEdit" class="mt-2 p-2 bg-white rounded border" style="display: <?php echo $aula['tem_tarefa'] ? 'block' : 'none'; ?>;">
                        <textarea name="tarefa_descricao" class="form-control form-control-sm mb-2" rows="2" placeholder="Instruções da tarefa..."><?php echo htmlspecialchars($aula['tarefa_descricao']); ?></textarea>
                        <select name="tarefa_tipo_entrega" class="form-select form-select-sm">
                            <option value="texto" <?php echo $aula['tarefa_tipo_entrega'] === 'texto' ? 'selected' : ''; ?>>Entrega em Texto</option>
                            <option value="arquivo" <?php echo $aula['tarefa_tipo_entrega'] === 'arquivo' ? 'selected' : ''; ?>>Entrega de Arquivo</option>
                            <option value="ambos" <?php echo $aula['tarefa_tipo_entrega'] === 'ambos' ? 'selected' : ''; ?>>Ambos</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- DIREITA: PREVIEW DO ALUNO (65%) -->
            <div class="col-lg-8 bg-light full-height-col p-4">
                <div class="container" style="max-width: 900px;">
                    
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="badge bg-primary">VISÃO DO ALUNO (PREVIEW)</span>
                        <div class="col-auto">
                            <input type="text" name="url_video" id="inputUrlVideo" class="form-control form-control-sm" 
                                   value="<?php echo htmlspecialchars($aula['url_video']); ?>" 
                                   placeholder="Cole o link do vídeo (Embed) aqui..."
                                   onchange="updateVideoPreview(this.value)">
                        </div>
                    </div>

                    <!-- CARD DA AULA (Simulando o Player) -->
                    <div class="card shadow-sm mb-4">
                        <div class="card-body p-0">
                            <!-- Video Player -->
                            <div class="video-placeholder rounded-top" id="videoPreviewArea">
                                <?php if($aula['url_video']): ?>
                                    <iframe src="<?php echo htmlspecialchars($aula['url_video']); ?>" width="100%" height="100%" frameborder="0" allowfullscreen></iframe>
                                <?php else: ?>
                                    <div class="text-center p-5 text-muted">
                                        <i class="bi bi-play-circle display-4"></i><br>
                                        Cole o link do vídeo acima para visualizar
                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            <!-- Conteúdo Abaixo do Vídeo -->
                            <div class="p-4">
                                <!-- Título Editável -->
                                <input type="text" name="titulo" class="form-control fs-4 fw-bold mb-3 live-input border-0 px-2" 
                                       placeholder="Título da Aula" required 
                                       value="<?php echo htmlspecialchars($aula['titulo']); ?>">
                                
                                <div class="d-flex gap-2 mb-4">
                                    <span class="badge bg-secondary p-2"><i class="bi bi-clock me-1"></i> Aula Gravada</span>
                                    <select name="tipo" class="form-select form-select-sm d-inline-block w-auto border-0 bg-light">
                                        <option value="video" <?php echo $aula['tipo'] === 'video' ? 'selected' : ''; ?>>Formato: Vídeo</option>
                                        <option value="texto" <?php echo $aula['tipo'] === 'texto' ? 'selected' : ''; ?>>Formato: Texto</option>
                                        <option value="diagnostico" <?php echo $aula['tipo'] === 'diagnostico' ? 'selected' : ''; ?>>Recurso: Diagnóstico</option>
                                        <option value="ferramenta" <?php echo $aula['tipo'] === 'ferramenta' ? 'selected' : ''; ?>>Recurso: Ferramenta</option>
                                    </select>
                                </div>
                                
                                <!-- Seleção de Recurso (Condicional) -->
                                <div id="boxResourceConfig" class="mb-4 p-3 bg-white border rounded" style="display:none;">
                                    <div id="selectDiagBox" class="mb-0" style="display:none;">
                                        <label class="form-label small fw-bold">Selecione o Diagnóstico</label>
                                        <select name="resource_id_diag" class="form-select text-dark">
                                            <option value="">-- Selecione --</option>
                                            <?php 
                                            if($diags_res) {
                                                $diags_res->data_seek(0);
                                                while($d = $diags_res->fetch_assoc()) {
                                                    $sel = ($aula['tipo'] === 'diagnostico' && $aula['resource_id'] == $d['id']) ? 'selected' : '';
                                                    echo "<option value='{$d['id']}' $sel>{$d['titulo']}</option>";
                                                }
                                            }
                                            ?>
                                        </select>
                                    </div>
                                    <div id="selectToolBox" class="mb-0" style="display:none;">
                                        <label class="form-label small fw-bold">Selecione a Ferramenta</label>
                                        <select name="resource_id_tool" class="form-select text-dark">
                                            <option value="">-- Selecione --</option>
                                            <?php 
                                            if($tools_res) {
                                                $tools_res->data_seek(0);
                                                while($t = $tools_res->fetch_assoc()) {
                                                    $sel = ($aula['tipo'] === 'ferramenta' && $aula['resource_id'] == $t['id']) ? 'selected' : '';
                                                    echo "<option value='{$t['id']}' $sel>{$t['nome']}</option>";
                                                }
                                            }
                                            ?>
                                        </select>
                                    </div>


                                <!-- Descrição Editável -->
                                <label class="text-muted small fw-bold text-uppercase mb-2">Descrição da Aula</label>
                                <textarea name="descricao" class="form-control live-input border-0 px-2" rows="8" 
                                          placeholder="Escreva a descrição que o aluno vai ler..."><?php echo htmlspecialchars($aula['descricao']); ?></textarea>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </form>
</div>

<!-- Modal Confirmar Contexto IA -->
<div class="modal fade" id="modalContextoIA" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Gerar Roteiro com IA</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p class="small text-muted">A IA vai gerar um script completo para esta aula. Quer dar alguma direção específica?</p>
                <div class="mb-3">
                    <label class="form-label fw-bold small">Aula</label>
                    <input type="text" class="form-control bg-light" value="" id="tituloDisplayIA" readonly>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-bold">Instruções Extras (Opcional)</label>
                    <textarea id="contextoExtra" class="form-control" rows="3" 
                              placeholder="Ex: Quero um tom motivacional; Dê 3 exemplos práticos; Seja breve..."></textarea>
                </div>
                
                <div id="loadingIA" class="text-center text-primary mt-3" style="display:none;">
                    <div class="spinner-border spinner-border-sm me-2"></div>
                    <strong>Criando roteiro...</strong>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" onclick="executarGeracao()">
                    <i class="bi bi-check-circle me-1"></i> Gerar Roteiro
                </button>
            </div>
        </div>
    </div>
</div>

<script>


    function salvarConteudo(e) {
        e.preventDefault();
        const form = document.getElementById('formConteudo');
        const formData = new FormData(form);
        const data = Object.fromEntries(formData.entries());
        
        // Checkboxes handling
        data.tem_tarefa = document.getElementById('checkTarefaEdit').checked ? 1 : 0;
        
        // Enviar via API
        fetch('<?php echo BASE_URL; ?>modules/admin/produtos/acoes_conteudo.php?acao=salvar_conteudo', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data)
        })
        .then(res => res.json())
        .then(response => {
            if (response.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Aula Salva!',
                    text: 'Alterações gravadas com sucesso.',
                    timer: 1500,
                    showConfirmButton: false
                }).then(() => {
                   // Opcional: Redirecionar
                });
            } else {
                Swal.fire('Erro', response.message || 'Erro ao salvar', 'error');
            }
        })
        .catch(err => Swal.fire('Erro', 'Erro de conexão.', 'error'));
    }

function toggleTarefaEdit() {
    var check = document.getElementById('checkTarefaEdit');
    var area = document.getElementById('areaTarefaEdit');
    area.style.display = check.checked ? 'block' : 'none';
}

function updateVideoPreview(url) {
    const area = document.getElementById('videoPreviewArea');
    if(url && url.includes('http')) {
        area.innerHTML = '<iframe src="'+url+'" width="100%" height="100%" frameborder="0" allowfullscreen></iframe>';
    } else {
        area.innerHTML = '<div class="text-center p-5 text-muted"><i class="bi bi-play-circle display-4"></i><br>Cole o link do vídeo acima para visualizar</div>';
    }
}

// Inicialização do Modal e Lógica IA
let modalIA;

document.addEventListener('DOMContentLoaded', function() {
    var modalEl = document.getElementById('modalContextoIA');
    if(modalEl) {
        modalIA = new bootstrap.Modal(modalEl);
    }
});

function gerarRoteiroIA() {
    // Atualiza o titulo no modal pra ficar bonitinho
    const tituloAutal = document.querySelector('input[name="titulo"]').value;
    document.getElementById('tituloDisplayIA').value = tituloAutal;

    if(modalIA) {
        modalIA.show();
    } else {
        const el = document.getElementById('modalContextoIA');
        if(el) {
            modalIA = new bootstrap.Modal(el);
            modalIA.show();
        }
    }
}

function ejecutarGeracao() { // Typo fix fallback
    executarGeracao();
}

function executarGeracao() {
    const btn = document.querySelector('#modalContextoIA .btn-primary');
    const loading = document.getElementById('loadingIA');
    const titulo = document.querySelector('input[name="titulo"]').value;
    const contexto = document.getElementById('contextoExtra').value;
    
    // UI State
    btn.disabled = true;
    loading.style.display = 'block';

    fetch('<?php echo BASE_URL; ?>api/n8n/gerar_roteiro.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({ 
            titulo: titulo,
            contexto: contexto
        })
    })
    .then(res => res.json())
    .then(data => {
        let textoFinal = null;

        let item = Array.isArray(data) ? data[0] : data;

        if (item.script) textoFinal = item.script;
        else if (item.output) textoFinal = item.output;
        else if (item.text) textoFinal = item.text;
        else if (item.json && item.json.script) textoFinal = item.json.script;

        if (typeof textoFinal === 'string') {
            textoFinal = textoFinal.trim();
            if (textoFinal.startsWith('```')) {
                textoFinal = textoFinal.replace(/^```(json)?/i, '').replace(/```$/, '').trim();
            }
            if (textoFinal.startsWith('{')) {
                try {
                    const parsed = JSON.parse(textoFinal);
                    if (parsed.script) textoFinal = parsed.script;
                } catch (e) { }
            }
        }

        if (!textoFinal && typeof item === 'object' && item.script) {
            textoFinal = item.script;
        }

        if(textoFinal) {
            const area = document.getElementById('descRoteiro');
            if(area.value.length > 20) {
                 if(confirm('Substituir roteiro atual?')) {
                     area.value = textoFinal;
                 } else {
                     area.value += "\n\n" + textoFinal;
                 }
            } else {
                area.value = textoFinal;
            }
            modalIA.hide();
        } else {
            alert('Não consegui gerar o roteiro. Tente novamente.');
        }
    })
    .catch(err => {
        console.error(err);
        alert('Erro ao comunicar com a IA.');
    })
    .finally(() => {
        btn.disabled = false;
        loading.style.display = 'none';
        document.getElementById('contextoExtra').value = ''; 
    });
}
</script>


<script>
document.addEventListener('DOMContentLoaded', function() {
    const selectTipo = document.querySelector('select[name="tipo"]');
    const boxResource = document.getElementById('boxResourceConfig');
    const boxDiag = document.getElementById('selectDiagBox');
    const boxTool = document.getElementById('selectToolBox');

    function updateResourceView() {
        const val = selectTipo.value;
        if(val === 'diagnostico' || val === 'ferramenta') {
            boxResource.style.display = 'block';
            if(val === 'diagnostico') {
                boxDiag.style.display = 'block';
                boxTool.style.display = 'none';
            } else {
                boxDiag.style.display = 'none';
                boxTool.style.display = 'block';
            }
        } else {
            boxResource.style.display = 'none';
        }
    }
    
    selectTipo.addEventListener('change', updateResourceView);
    updateResourceView(); // Init
});
</script>

<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<?php require_once __DIR__ . '/../../../includes/footer.php'; ?>
