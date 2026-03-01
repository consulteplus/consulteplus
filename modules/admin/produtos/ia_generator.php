<?php
$pageTitle = "Assistente de Criação IA";
require_once __DIR__ . '/../header.php';
checkPermission(['admin']);

// ProductContentService for Resources
require_once __DIR__ . '/../../../classes/ProductContentService.php';
$contentService = new ProductContentService();

$diags = $contentService->listarDiagnosticos();
$tools = $contentService->listarFerramentas();

?>

<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-primary">
                <i class="bi bi-stars me-2"></i>Criar Mentoria com IA
            </h1>
            <p class="text-muted small mb-0">Defina o tema e deixe a IA estruturar seu curso.</p>
        </div>
        <a href="<?php echo BASE_URL; ?>admin/mentoria" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-2"></i>Voltar
        </a>
    </div>

    <!-- CARD WIZARD -->
    <div class="card shadow-lg border-0 overflow-hidden">
        <!-- PROGRESS BAR -->
        <div class="progress" style="height: 4px; border-radius: 0;">
            <div class="progress-bar bg-primary" id="progressBar" style="width: 33%"></div>
        </div>

        <div class="card-body p-5">

            <!-- STEP 1: INPUT -->
            <div id="stepInput">
                <div class="text-center mb-5">
                    <div class="mb-3">
                        <span class="bg-primary bg-opacity-10 text-primary p-3 rounded-circle display-4">
                            <i class="bi bi-lightbulb"></i>
                        </span>
                    </div>
                    <h3 class="fw-bold">Sobre o que você quer ensinar?</h3>
                    <p class="text-muted">Descreva seu conhecimento e para quem ele serve.</p>
                </div>

                <div class="row justify-content-center">
                    <div class="col-md-8">
                        <form id="formIA">
                            <div class="mb-4">
                                <label class="form-label fw-bold">Tema Principal ou Título Provisório</label>
                                <input type="text" id="tema" class="form-control form-control-lg"
                                    placeholder="Ex: Marketing Digital para Iniciantes" required>
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-bold">Tipo de Produto</label>
                                <select id="tipo" class="form-select text-capitalize">
                                    <option value="mentoria" selected>Mentoria (Acompanhamento)</option>
                                    <option value="consultoria">Consultoria (Diagnóstico e Solução)</option>
                                    <option value="treinamento">Treinamento (Curso Prático)</option>
                                    <option value="workshop">Workshop (Intensivo)</option>
                                    <option value="outro">Outro</option>
                                </select>
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-bold">Público-Alvo (Opcional)</label>
                                <input type="text" id="publico" class="form-control"
                                    placeholder="Ex: Donos de pequenas empresas, dentistas, estudantes...">
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-bold">Instruções Extras (Opcional)</label>
                                <textarea id="instrucoes_extras" class="form-control" rows="3"
                                    placeholder="Ex: Quero que tenha 5 módulos, sendo o último sobre vendas."></textarea>
                                <div class="form-text">Dê comandos específicos para a IA seguir.</div>
                            </div>

                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Nível de Profundidade</label>
                                    <select id="profundidade" class="form-select">
                                        <option value="Iniciante">Iniciante</option>
                                        <option value="Intermediário" selected>Intermediário</option>
                                        <option value="Avançado">Avançado</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Duração Estimada</label>
                                    <select id="duracao" class="form-select">
                                        <option value="Curta (Express)">Curta (Express)</option>
                                        <option value="Média (Padrão)" selected>Média (Padrão)</option>
                                        <option value="Longa (Completa)">Longa (Completa)</option>
                                    </select>
                                </div>
                            </div>

                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary btn-lg rounded-pill"
                                    id="btnGerarEstrutura">
                                    <i class="bi bi-magic me-2"></i>Gerar Estrutura com IA
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- STEP 2: LOADING -->
            <div id="stepLoading" style="display:none;" class="text-center py-5">
                <div class="spinner-grow text-primary mb-3" role="status" style="width: 3rem; height: 3rem;"></div>
                <h4 class="fw-bold animate-pulse">A IA está pensando...</h4>
                <p class="text-muted">Criando módulos, aulas e sugerindo tarefas.</p>
                <div class="mt-3 text-muted small">
                    Isso pode levar de 30 a 60 segundos. Não feche a página.
                </div>
            </div>

            <!-- STEP 3: REVIEW & CONFIRM -->
            <div id="stepReview" style="display:none;">
                <div class="text-center mb-4">
                    <h3 class="fw-bold text-success"><i class="bi bi-check-circle me-2"></i>Estrutura Gerada!</h3>
                    <p class="text-muted">Revise os detalhes abaixo antes de criar o curso.</p>
                </div>

                <form method="POST" action="<?php echo BASE_URL; ?>admin/produtos/salvar_ia" id="formSalvar">
                    <input type="hidden" name="ia_data" id="inputIaData">
                    <input type="hidden" name="tipo" id="inputTipo">

                    <!-- Produto Info -->
                    <div class="bg-light p-4 rounded mb-4 border">
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-uppercase text-muted">Título Sugerido</label>
                            <input type="text" name="titulo" id="resTitulo" class="form-control fw-bold fs-5">
                        </div>
                        <div class="mb-0">
                            <label class="form-label fw-bold small text-uppercase text-muted">Descrição</label>
                            <textarea name="descricao" id="resDescricao" class="form-control" rows="2"></textarea>
                        </div>
                    </div>

                    <!-- Modulos List -->
                    <h5 class="fw-bold mb-3 border-bottom pb-2">Conteúdo Programático</h5>
                    <div id="listaModulos" class="accordion mb-4">
                        <!-- Preenchido via JS -->
                    </div>

                    <!-- JS Resource Map -->
                    <script>
                        const resourceMap = {
                            diagnosticos: {},
                            ferramentas: {}
                        };
                        <?php
                        foreach ($diags as $d) {
                            echo "resourceMap.diagnosticos[{$d['id']}] = '" . addslashes($d['titulo']) . "';\n";
                        }
                        foreach ($tools as $t) {
                            echo "resourceMap.ferramentas[{$t['id']}] = '" . addslashes($t['nome']) . "';\n";
                        }
                        ?>
                    </script>

                    <div
                        class="d-flex justify-content-between align-items-center bg-white p-3 border-top sticky-bottom">
                        <button type="button" class="btn btn-outline-secondary" onclick="resetForm()">
                            <i class="bi bi-trash me-2"></i>Descartar e Tentar Novamente
                        </button>
                        <button type="submit" class="btn btn-success btn-lg px-5">
                            <i class="bi bi-check-lg me-2"></i>Aprovar e Criar Mentoria
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</div>

<script>
    document.getElementById('formIA').addEventListener('submit', function (e) {
        e.preventDefault();

        const tema = document.getElementById('tema').value;
        const tipo = document.getElementById('tipo').value;
        const publico = document.getElementById('publico').value;
        const profundidade = document.getElementById('profundidade').value;
        const duracao = document.getElementById('duracao').value;
        const instrucoes_extras = document.getElementById('instrucoes_extras').value;

        // UI Transition
        document.getElementById('stepInput').style.display = 'none';
        document.getElementById('stepLoading').style.display = 'block';
        document.getElementById('progressBar').style.width = '66%';

        // Call API
        fetch('<?php echo BASE_URL; ?>api/n8n/gerar_mentoria.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                tema: tema,
                tipo: tipo,
                publico: publico,
                profundidade: profundidade,
                duracao: duracao,
                instrucoes_extras: instrucoes_extras
            })
        })
            .then(response => response.json())
            .then(data => {
                if (data.error || data.success === false) {
                    alert('Erro na IA: ' + (data.message || 'Resposta inválida'));
                    resetForm();
                    return;
                }

                // Tratamento Robustez: n8n pode retornar array ou objeto aninhado
                let finalData = data;

                // Caso 1: Retorno é um array [ { ... } ]
                if (Array.isArray(data) && data.length > 0) {
                    finalData = data[0];
                }

                // Caso 2: Retorno está dentro de uma propriedade 'output' ou 'json'
                if (finalData.output) finalData = finalData.output;
                if (finalData.json) finalData = finalData.json;

                // Caso 3: O conteúdo veio como string JSON dentro de uma propriedade
                if (typeof finalData === 'string') {
                    try {
                        finalData = JSON.parse(finalData);
                    } catch (e) {
                        console.error("Erro ao parsear string:", e);
                    }
                } else if (typeof finalData.content === 'string') {
                    // GPT as vezes devolve { content: "{ ... }" }
                    try {
                        finalData = JSON.parse(finalData.content);
                    } catch (e) { }
                }

                console.log("Dados processados da IA:", finalData); // Debug para o console
                renderReview(finalData);
            })
            .catch(err => {
                console.error(err);
                alert('Erro de conexão ou timeout. Verifique o console para mais detalhes.');
                resetForm();
            });
    });

    function renderReview(data) {
        // Validação básica
        if (!data.title && !data.titulo && !data.modules && !data.modulos) {
            alert("A IA retornou um formato inesperado. Veja o console (F12) para detalhes.\n\nRecebido: " + JSON.stringify(data).substring(0, 100) + "...");
            return;
        }

        // Assume data format: { title: "...", description: "...", modules: [ { title: "", lessons: [ { title: "", description: "", has_task: true, task_desc: "" } ] } ] }

        document.getElementById('stepLoading').style.display = 'none';
        document.getElementById('stepReview').style.display = 'block';
        document.getElementById('progressBar').style.width = '100%';
        document.getElementById('progressBar').classList.add('bg-success');

        document.getElementById('resTitulo').value = data.title || data.titulo;
        document.getElementById('resDescricao').value = data.description || data.descricao;

        // Save raw JSON for POST
        document.getElementById('inputIaData').value = JSON.stringify(data);

        // Save selected Type
        const tipoSelecionado = document.getElementById('tipo').value;
        document.getElementById('inputTipo').value = tipoSelecionado;



        // Remove old logic for global selection


        const container = document.getElementById('listaModulos');
        container.innerHTML = '';

        const modules = data.modules || data.modulos || [];

        modules.forEach((mod, idx) => {
            const modId = 'collapse' + idx;
            let lessonsHtml = '<ul class="list-group list-group-flush">';

            const lessons = mod.lessons || mod.aulas || [];
            lessons.forEach(lesson => {
                let badgeTask = '';
                if (lesson.has_task || lesson.tem_tarefa) {
                    badgeTask = `<span class="badge bg-warning text-dark ms-2"><i class="bi bi-list-check me-1"></i>Tarefa: ${lesson.task_description || lesson.tarefa_descricao}</span>`;
                }

                const duration = lesson.duration || lesson.duracao || '?';

                let badgeResource = '';
                if (lesson.resource_id) {
                    let resName = 'Recurso desconhecido';
                    if (lesson.resource_type === 'diagnostico' && resourceMap.diagnosticos[lesson.resource_id]) {
                        resName = resourceMap.diagnosticos[lesson.resource_id];
                        badgeResource = `<div class="badge bg-info text-dark mt-1"><i class="bi bi-ui-checks-grid me-1"></i>Diagnóstico: ${resName}</div>`;
                    } else if (lesson.resource_type === 'ferramenta' && resourceMap.ferramentas[lesson.resource_id]) {
                        resName = resourceMap.ferramentas[lesson.resource_id];
                        badgeResource = `<div class="badge bg-primary text-white mt-1"><i class="bi bi-tools me-1"></i>Ferramenta: ${resName}</div>`;
                    }
                }

                lessonsHtml += `
                    <li class="list-group-item bg-light">
                        <div class="d-flex justify-content-between">
                            <div class="fw-bold"><i class="bi bi-play-circle me-2 text-muted"></i>${lesson.title || lesson.titulo}</div>
                            <small class="text-muted"><i class="bi bi-clock me-1"></i>${duration}</small>
                        </div>
                        <div class="small text-muted ms-4">${lesson.description || lesson.descricao}</div>
                        <div class="ms-4 mt-1">
                            ${badgeTask}
                            ${badgeResource}
                        </div>
                    </li>
                `;
            });
            lessonsHtml += '</ul>';

            const html = `
                <div class="accordion-item">
                    <h2 class="accordion-header" id="heading${idx}">
                        <button class="accordion-button ${idx !== 0 ? 'collapsed' : ''}" type="button" data-bs-toggle="collapse" data-bs-target="#${modId}">
                            <span class="fw-bold">Módulo ${idx + 1}: ${mod.title || mod.titulo}</span>
                            <span class="badge bg-secondary ms-2 rounded-pill">${lessons.length} aulas</span>
                        </button>
                    </h2>
                    <div id="${modId}" class="accordion-collapse collapse ${idx === 0 ? 'show' : ''}" data-bs-parent="#listaModulos">
                        <div class="accordion-body p-0">
                            ${lessonsHtml}
                        </div>
                    </div>
                </div>
            `;
            container.innerHTML += html;
        });
    }

    function resetForm() {
        document.getElementById('stepReview').style.display = 'none';
        document.getElementById('stepLoading').style.display = 'none';
        document.getElementById('stepInput').style.display = 'block';
        document.getElementById('progressBar').style.width = '33%';
        document.getElementById('progressBar').classList.remove('bg-success');
    }
</script>

<?php require_once __DIR__ . '/../../../includes/footer.php'; ?>