<?php
$pageTitle = "Novo Projeto";
require_once __DIR__ . '/../../../includes/header.php';
checkPermission(['admin', 'cliente']);
?>

<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Criar Novo Projeto</h1>
            <p class="text-muted small mb-0">Use a IA para estruturar ou crie manualmente.</p>
        </div>
        <a href="<?php echo BASE_URL; ?>gestao/projetos" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-2"></i>Voltar
        </a>
    </div>

    <div class="row g-4">
        <!-- Coluna da Esquerda: Assistente IA -->
        <div class="col-lg-4">
            <div class="card shadow-lg border-0 mb-4 sticky-top" style="top: 100px; z-index: 1;">
                <!-- AI Badge Header -->
                <div class="card-header bg-gradient text-white py-3 position-relative"
                    style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                    <div class="d-flex align-items-center justify-content-between">
                        <h5 class="card-title mb-0 fw-bold">
                            <i class="bi bi-stars me-2"></i>Assistente IA
                        </h5>
                        <span class="badge bg-white bg-opacity-25 px-3 py-2">
                            <i class="bi bi-robot me-1"></i>Powered by AI
                        </span>
                    </div>
                    <p class="small mb-0 mt-2 opacity-90">
                        Deixe a inteligência artificial estruturar seu projeto
                    </p>
                </div>

                <div class="card-body p-4">
                    <!-- Info Alert -->
                    <div class="alert alert-info border-0 mb-4" style="background-color: #e3f2fd;">
                        <div class="d-flex align-items-start">
                            <i class="bi bi-info-circle-fill text-primary me-2 mt-1"></i>
                            <small class="text-dark">
                                Descreva seu projeto e a IA criará a estrutura completa.
                            </small>
                        </div>
                    </div>

                    <!-- Form Fields -->
                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark">
                            <i class="bi bi-lightbulb me-1 text-primary"></i>Nome do Projeto
                        </label>
                        <input type="text" class="form-control" id="aiProjectName"
                            placeholder="Ex: Expansão Digital 2024">
                        <small class="text-muted">Qual o nome do projeto?</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark">
                            <i class="bi bi-bullseye me-1 text-primary"></i>Objetivo Principal
                        </label>
                        <textarea class="form-control" id="aiGoal" rows="3"
                            placeholder="Ex: Aumentar presença digital e vendas online em 50%..."></textarea>
                        <small class="text-muted">O que você quer alcançar?</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark">
                            <i class="bi bi-calendar-range me-1 text-primary"></i>Prazo Estimado
                        </label>
                        <select class="form-select" id="aiTimeline">
                            <option value="1-3">1 a 3 meses (Curto prazo)</option>
                            <option value="3-6" selected>3 a 6 meses (Médio prazo)</option>
                            <option value="6-12">6 a 12 meses (Longo prazo)</option>
                            <option value="12+">Mais de 12 meses</option>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold text-dark">
                            <i class="bi bi-flag me-1 text-primary"></i>Prioridade
                        </label>
                        <select class="form-select" id="aiPriority">
                            <option value="alta">Alta - Crítico para o negócio</option>
                            <option value="media" selected>Média - Importante</option>
                            <option value="baixa">Baixa - Pode aguardar</option>
                        </select>
                    </div>

                    <button class="btn btn-primary w-100 fw-bold shadow-sm py-3" id="btnGenerate"
                        onclick="generateProject()">
                        <i class="bi bi-lightning-charge-fill me-2"></i>Gerar Projeto com IA
                    </button>

                    <div id="loadingIndicator" class="text-center mt-3 d-none">
                        <div class="spinner-border text-primary" role="status"></div>
                        <p class="small mt-2 text-muted mb-0">
                            <i class="bi bi-cpu me-1"></i>Processando com inteligência artificial...
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Coluna da Direita: Formulário Principal -->
        <div class="col-lg-8">
            <form action="<?php echo BASE_URL; ?>modules/gestao/projetos/criar_manual.php" method="POST"
                id="projectForm">

                <!-- Informações Básicas -->
                <div class="card shadow border-0 mb-4">
                    <div class="card-body p-4">
                        <h5 class="fw-bold text-dark mb-4 border-bottom pb-2">Informações Básicas</h5>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Nome do Projeto</label>
                            <input type="text" name="nome" id="inputNome" class="form-control form-control-lg" required
                                placeholder="Digite o nome do projeto...">
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Descrição</label>
                            <textarea name="descricao" id="inputDescricao" class="form-control" rows="4"
                                placeholder="Descreva o projeto, seus objetivos e contexto..."></textarea>
                            <small class="text-muted">Suporta formatação básica: **negrito**, *itálico*, -
                                listas</small>
                        </div>

                        <div class="mb-0">
                            <label class="form-label fw-bold">Prioridade</label>
                            <select name="prioridade" id="inputPrioridade" class="form-select" required>
                                <option value="alta">🔴 Alta - Crítico</option>
                                <option value="media" selected>🟡 Média - Importante</option>
                                <option value="baixa">🟢 Baixa - Pode aguardar</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Cronograma -->
                <div class="card shadow border-0 mb-4">
                    <div class="card-body p-4">
                        <h5 class="fw-bold text-dark mb-4 border-bottom pb-2">Cronograma</h5>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Data de Início</label>
                                <input type="date" name="data_inicio" id="inputDataInicio" class="form-control"
                                    value="<?php echo date('Y-m-d'); ?>">
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Data de Término (Estimada)</label>
                                <input type="date" name="data_fim" id="inputDataFim" class="form-control">
                            </div>
                        </div>

                        <div id="durationDisplay" class="alert alert-light border mb-0 d-none">
                            <i class="bi bi-clock me-2"></i>
                            <strong>Duração estimada:</strong> <span id="durationText"></span>
                        </div>
                    </div>
                </div>

                <!-- Escopo & Objetivos -->
                <div class="card shadow border-0 mb-4">
                    <div class="card-body p-4">
                        <h5 class="fw-bold text-dark mb-4 border-bottom pb-2">Escopo & Objetivos</h5>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Objetivos Principais</label>
                            <textarea name="objetivos" id="inputObjetivos" class="form-control" rows="4"
                                placeholder="Liste os principais objetivos do projeto...&#10;- Objetivo 1&#10;- Objetivo 2&#10;- Objetivo 3"></textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Critérios de Sucesso</label>
                            <textarea name="criterios_sucesso" id="inputCriterios" class="form-control" rows="3"
                                placeholder="Como saberemos que o projeto foi bem-sucedido?"></textarea>
                        </div>

                        <div class="mb-0">
                            <label class="form-label fw-bold">Principais Entregas</label>
                            <textarea name="entregas" id="inputEntregas" class="form-control" rows="3"
                                placeholder="Quais são os principais entregáveis?"></textarea>
                        </div>
                    </div>
                </div>

                <!-- OKRs Sugeridos -->
                <div class="card shadow border-0 mb-4" id="okrsCard" style="display: none;">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center mb-4 border-bottom pb-2">
                            <i class="bi bi-target text-primary me-2" style="font-size: 1.25rem;"></i>
                            <h5 class="fw-bold text-dark mb-0">OKRs Sugeridos pela IA</h5>
                        </div>
                        <div id="okrsList" class="list-group list-group-flush"></div>
                        <input type="hidden" name="okrs_json" id="okrsJson">
                    </div>
                </div>

                <!-- Tarefas Sugeridas -->
                <div class="card shadow border-0 mb-4" id="tasksCard" style="display: none;">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center mb-4 border-bottom pb-2">
                            <i class="bi bi-list-check text-success me-2" style="font-size: 1.25rem;"></i>
                            <h5 class="fw-bold text-dark mb-0">Tarefas Sugeridas pela IA</h5>
                        </div>
                        <div id="tasksList" class="list-group list-group-flush"></div>
                        <input type="hidden" name="tasks_json" id="tasksJson">
                    </div>
                </div>

                <div class="d-grid mb-5">
                    <button type="submit" class="btn btn-success btn-lg fw-bold shadow-lg">
                        <i class="bi bi-check-circle-fill me-2"></i>Criar Projeto
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Form Submit Handler
    document.getElementById('projectForm').addEventListener('submit', async function (e) {
        e.preventDefault();

        const formData = {
            titulo: document.getElementById('inputNome').value,
            descricao: document.getElementById('inputDescricao').value,
            prioridade: document.getElementById('inputPrioridade').value,
            objetivos: document.getElementById('inputObjetivos').value,
            criterios_sucesso: document.getElementById('inputCriterios').value,
            entregas: document.getElementById('inputEntregas').value,
            data_inicio: document.getElementById('inputDataInicio').value,
            data_fim: document.getElementById('inputDataFim').value
        };

        // Add OKRs and Tasks if available
        const okrsJson = document.getElementById('okrsJson').value;
        const tasksJson = document.getElementById('tasksJson').value;

        if (okrsJson) {
            formData.ai_suggestions = JSON.stringify({
                okrs: JSON.parse(okrsJson),
                tarefas: tasksJson ? JSON.parse(tasksJson) : []
            });
        }

        console.log('FormData being sent:', formData);

        try {
            const response = await fetch('<?php echo BASE_URL; ?>modules/gestao/projetos/criar_manual.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(formData)
            });

            const result = await response.json();

            if (result.success) {
                alert('Projeto criado com sucesso!');
                window.location.href = '<?php echo BASE_URL; ?>gestao/projetos/' + result.id;
            } else {
                alert('Erro: ' + result.error);
            }
        } catch (error) {
            alert('Erro ao criar projeto: ' + error.message);
        }
    });

    // Calculate and display duration
    function updateDuration() {
        const startDate = document.getElementById('inputDataInicio').value;
        const endDate = document.getElementById('inputDataFim').value;

        if (startDate && endDate) {
            const start = new Date(startDate);
            const end = new Date(endDate);
            const diffTime = Math.abs(end - start);
            const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
            const months = Math.floor(diffDays / 30);
            const days = diffDays % 30;

            let durationText = '';
            if (months > 0) {
                durationText = `${months} ${months === 1 ? 'mês' : 'meses'}`;
                if (days > 0) durationText += ` e ${days} ${days === 1 ? 'dia' : 'dias'}`;
            } else {
                durationText = `${diffDays} ${diffDays === 1 ? 'dia' : 'dias'}`;
            }

            document.getElementById('durationText').textContent = durationText;
            document.getElementById('durationDisplay').classList.remove('d-none');
        } else {
            document.getElementById('durationDisplay').classList.add('d-none');
        }
    }

    document.getElementById('inputDataInicio').addEventListener('change', updateDuration);
    document.getElementById('inputDataFim').addEventListener('change', updateDuration);

    // AI Project Generation
    async function generateProject() {
        const projectName = document.getElementById('aiProjectName').value;
        const goal = document.getElementById('aiGoal').value;
        const timeline = document.getElementById('aiTimeline').value;
        const priority = document.getElementById('aiPriority').value;

        if (!projectName || !goal) {
            alert('Por favor, preencha pelo menos o nome e objetivo do projeto.');
            return;
        }

        const btn = document.getElementById('btnGenerate');
        const loading = document.getElementById('loadingIndicator');
        const originalText = btn.innerHTML;

        btn.disabled = true;
        btn.innerHTML = '<i class="bi bi-hourglass-split me-2"></i>Gerando...';
        loading.classList.remove('d-none');

        // Declare today first
        const today = new Date().toISOString().split('T')[0];

        // Adapt rules based on timeline
        let okrsCount, tasksCount;
        switch (timeline) {
            case '1-3':
                okrsCount = '1-2 OKRs com 2-3 Key Results cada';
                tasksCount = '3-5 tarefas iniciais';
                break;
            case '3-6':
                okrsCount = '2-3 OKRs com 3 Key Results cada';
                tasksCount = '5-8 tarefas iniciais';
                break;
            case '6-12':
                okrsCount = '3-4 OKRs com 3-4 Key Results cada';
                tasksCount = '8-12 tarefas iniciais';
                break;
            case '12+':
                okrsCount = '4-5 OKRs com 3-5 Key Results cada';
                tasksCount = '12-15 tarefas iniciais';
                break;
            default:
                okrsCount = '2-3 OKRs com 3 Key Results cada';
                tasksCount = '5-8 tarefas iniciais';
        }

        const userPrompt = `
# CONTEXTO DO PROJETO

**Nome:** ${projectName}
**Objetivo Principal:** ${goal}
**Prazo:** ${timeline} meses
**Prioridade:** ${priority}

# TAREFA
Crie uma estrutura completa e detalhada para este projeto, incluindo:
1. Descrição contextualizada
2. Objetivos específicos e mensuráveis
3. Critérios claros de sucesso
4. Principais entregas
5. OKRs (Objectives and Key Results) alinhados ao objetivo
6. Tarefas iniciais priorizadas

Seja específico, prático e alinhado ao prazo de ${timeline} meses.
`;

        try {
            const response = await fetch('<?php echo BASE_URL; ?>modules/gestao/diagnostico/proxy_n8n.php?type=project', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    prompt_sistema: `
# ROLE
Você é um Consultor de Projetos Estratégicos especializado em OKRs e metodologias ágeis.
Sua missão é estruturar projetos de forma clara, mensurável e executável.

# CONTEXTO TEMPORAL
📅 Data Atual: ${today}
⏱️ Prazo do Projeto: ${timeline} meses

# Regras de Saída (CRÍTICO)
1. Você deve retornar APENAS um objeto JSON válido.
2. Não use Markdown (sem \`\`\`json). Retorne apenas o texto cru do JSON.
3. Não inclua texto introdutório ou explicativo.
4. O idioma deve ser Português do Brasil.
5. Todos os campos devem ser preenchidos com conteúdo relevante e específico.

# FORMATO DE SAÍDA (JSON OBRIGATÓRIO)

{
  "nome": "Nome do Projeto (claro e objetivo)",
  "descricao": "Descrição detalhada incluindo:\\n- Contexto e justificativa\\n- Importância estratégica\\n- Benefícios esperados",
  "objetivos": "- Objetivo 1: Descrição específica e mensurável\\n- Objetivo 2: Descrição específica e mensurável\\n- Objetivo 3: Descrição específica e mensurável",
  "criterios_sucesso": "- Critério 1: Métrica ou indicador claro\\n- Critério 2: Métrica ou indicador claro\\n- Critério 3: Métrica ou indicador claro",
  "entregas": "- Entrega 1: Descrição do entregável\\n- Entrega 2: Descrição do entregável\\n- Entrega 3: Descrição do entregável",
  "okrs": [
    {
      "objective": "Objetivo estratégico mensurável (O1)",
      "key_results": [
        "KR1: Métrica específica com valor alvo",
        "KR2: Métrica específica com valor alvo",
        "KR3: Métrica específica com valor alvo"
      ]
    }
  ],
  "tasks": [
    {
      "titulo": "Tarefa acionável (verbo + objeto)",
      "descricao": "**O QUE É:**\\n[Explicação breve]\\n\\n**PASSOS SUGERIDOS:**\\n- Passo 1\\n- Passo 2\\n\\n**RESULTADO ESPERADO:**\\n[O que será alcançado]",
      "prioridade": "alta"
    }
  ]
}

# REGRAS DE GERAÇÃO
✅ Idioma: Português (Brasil)
✅ Crie ${okrsCount} alinhados ao objetivo principal
✅ Sugira ${tasksCount} priorizadas e sequenciais
✅ Use métricas SMART (Específicas, Mensuráveis, Atingíveis, Relevantes, Temporais)
✅ Objetivos devem usar listas com marcadores (-)
✅ Key Results devem ter valores numéricos ou percentuais quando possível
✅ Tarefas devem começar com verbos de ação
✅ Descrição das tarefas deve seguir o formato: **O QUE É**, **PASSOS SUGERIDOS**, **RESULTADO ESPERADO**
✅ Adapte a complexidade e escopo ao prazo de ${timeline} meses
✅ Priorize tarefas: alta (urgente/crítico), media (importante), baixa (desejável)

# Input do Usuário
Nome do Projeto: ${projectName}
Objetivo Principal: ${goal}
Prazo: ${timeline} meses
Prioridade: ${priority}

# EXEMPLOS DE QUALIDADE
**BOM OKR:**
- Objective: "Aumentar engajamento digital"
- KR1: "Alcançar 10.000 seguidores no Instagram"
- KR2: "Obter taxa de engajamento de 5%"
- KR3: "Gerar 500 leads qualificados"

**BOA TAREFA:**
- Titulo: "Criar calendário editorial mensal"
- Descricao: "Planejar 30 posts com temas, formatos e CTAs definidos"
- Prioridade: "alta"
`,
                    user_input: userPrompt
                })
            });

            const text = await response.text();
            let data;
            try {
                data = JSON.parse(text);
            } catch (e) {
                console.error('Server Error:', text);
                throw new Error('Erro na resposta do servidor.');
            }

            if (data.error) throw new Error(data.error);

            let content = data.output || (data.message ? data.message.content : data);
            if (typeof content === 'string') {
                content = JSON.parse(content.replace(/```json/g, '').replace(/```/g, '').trim());
            }

            // Fill Basic Form Fields
            document.getElementById('inputNome').value = content.nome || projectName;
            document.getElementById('inputDescricao').value = content.descricao || '';
            document.getElementById('inputObjetivos').value = content.objetivos || '';
            document.getElementById('inputCriterios').value = content.criterios_sucesso || '';
            document.getElementById('inputEntregas').value = content.entregas || '';
            document.getElementById('inputPrioridade').value = priority;

            // Set dates based on timeline
            const startDate = new Date();
            document.getElementById('inputDataInicio').value = startDate.toISOString().split('T')[0];

            const endDate = new Date(startDate);
            const timelineMonths = parseInt(timeline.split('-')[1] || timeline.replace('+', ''));
            endDate.setMonth(endDate.getMonth() + timelineMonths);
            document.getElementById('inputDataFim').value = endDate.toISOString().split('T')[0];

            updateDuration();

            // Display OKRs
            if (content.okrs && content.okrs.length > 0) {
                const okrsList = document.getElementById('okrsList');
                okrsList.innerHTML = '';

                content.okrs.forEach((okr, index) => {
                    if (!okr.objective) return; // Skip if no objective

                    const okrItem = document.createElement('div');
                    okrItem.className = 'list-group-item border-0 px-0';

                    let krHtml = '';
                    if (okr.key_results && Array.isArray(okr.key_results) && okr.key_results.length > 0) {
                        krHtml = `
                            <ul class="small text-muted mb-0 ps-4">
                                ${okr.key_results.map((kr, krIndex) => `
                                    <li class="mb-1">
                                        <span class="badge bg-light text-dark me-1">KR${krIndex + 1}</span>
                                        ${kr}
                                    </li>
                                `).join('')}
                            </ul>
                        `;
                    }

                    okrItem.innerHTML = `
                        <div class="d-flex align-items-start mb-2">
                            <span class="badge bg-primary me-2 mt-1">O${index + 1}</span>
                            <strong class="text-dark">${okr.objective}</strong>
                        </div>
                        ${krHtml}
                    `;
                    okrsList.appendChild(okrItem);
                });

                document.getElementById('okrsCard').style.display = 'block';
                document.getElementById('okrsJson').value = JSON.stringify(content.okrs);
            }

            // Display Tasks
            if (content.tasks && content.tasks.length > 0) {
                const tasksList = document.getElementById('tasksList');
                tasksList.innerHTML = '';

                const priorityColors = {
                    'alta': 'danger',
                    'media': 'warning',
                    'baixa': 'success'
                };

                content.tasks.forEach((task, index) => {
                    const taskItem = document.createElement('div');
                    taskItem.className = 'list-group-item border-0 px-0 py-2';
                    taskItem.innerHTML = `
                        <div class="d-flex align-items-start">
                            <i class="bi bi-check-circle text-success me-2 mt-1"></i>
                            <div class="flex-grow-1">
                                <div class="d-flex justify-content-between align-items-start">
                                    <strong class="text-dark">${task.titulo}</strong>
                                    <span class="badge bg-${priorityColors[task.prioridade] || 'secondary'} ms-2">
                                        ${task.prioridade}
                                    </span>
                                </div>
                                <small class="text-muted">${task.descricao}</small>
                            </div>
                        </div>
                    `;
                    tasksList.appendChild(taskItem);
                });

                document.getElementById('tasksCard').style.display = 'block';
                document.getElementById('tasksJson').value = JSON.stringify(content.tasks);
            }

            // Scroll to show results
            document.getElementById('okrsCard').scrollIntoView({ behavior: 'smooth', block: 'nearest' });

        } catch (error) {
            alert('Erro ao gerar projeto: ' + error.message);
        } finally {
            btn.disabled = false;
            btn.innerHTML = originalText;
            loading.classList.add('d-none');
        }
    }
</script>

<?php require_once __DIR__ . '/../../../includes/footer.php'; ?>