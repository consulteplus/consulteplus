<?php
$pageTitle = "Novo Diagnóstico";
require_once __DIR__ . '/../header.php';
checkPermission(['superadmin']);
?>

<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Criar Novo Diagnóstico</h1>
            <p class="text-muted small mb-0">Use a IA para estruturar ou crie manualmente.</p>
        </div>
        <a href="<?php echo BASE_URL; ?>admin/diagnosticos" class="btn btn-outline-secondary">
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
                        Deixe a inteligência artificial criar a estrutura completa do seu diagnóstico
                    </p>
                </div>
                
                <div class="card-body p-4">
                    <!-- Info Alert -->
                    <div class="alert alert-info border-0 mb-4" style="background-color: #e3f2fd;">
                        <div class="d-flex align-items-start">
                            <i class="bi bi-info-circle-fill text-primary me-2 mt-1"></i>
                            <small class="text-dark">
                                Preencha os campos abaixo com o máximo de detalhes possível para obter melhores resultados.
                            </small>
                        </div>
                    </div>

                    <!-- Form Fields -->
                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark">
                            <i class="bi bi-building me-1 text-primary"></i>Segmento de Mercado
                        </label>
                        <input type="text" class="form-control" id="aiSegment" 
                               placeholder="Ex: Varejo de Moda, Tecnologia, Saúde...">
                        <small class="text-muted">Qual o setor de atuação?</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark">
                            <i class="bi bi-people me-1 text-primary"></i>Público-Alvo
                        </label>
                        <input type="text" class="form-control" id="aiAudience" 
                               placeholder="Ex: Gerentes, Diretores, Empreendedores...">
                        <small class="text-muted">Quem vai responder este diagnóstico?</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark">
                            <i class="bi bi-bullseye me-1 text-primary"></i>Objetivo Principal
                        </label>
                        <textarea class="form-control" id="aiGoal" rows="3"
                                  placeholder="Ex: Avaliar a maturidade digital da empresa e identificar oportunidades de melhoria..."></textarea>
                        <small class="text-muted">O que você deseja descobrir ou medir?</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark">
                            <i class="bi bi-list-check me-1 text-primary"></i>Número de Perguntas
                        </label>
                        <select class="form-select" id="aiQuestionCount">
                            <option value="5-8">5 a 8 perguntas (Rápido)</option>
                            <option value="10-15" selected>10 a 15 perguntas (Recomendado)</option>
                            <option value="20-25">20 a 25 perguntas (Detalhado)</option>
                            <option value="30-40">30 a 40 perguntas (Completo)</option>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold text-dark">
                            <i class="bi bi-diagram-3 me-1 text-primary"></i>Áreas de Foco
                        </label>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="areaFinanceiro" checked>
                            <label class="form-check-label" for="areaFinanceiro">Financeiro</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="areaOperacional" checked>
                            <label class="form-check-label" for="areaOperacional">Operacional</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="areaPessoas" checked>
                            <label class="form-check-label" for="areaPessoas">Pessoas & Cultura</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="areaMarketing">
                            <label class="form-check-label" for="areaMarketing">Marketing & Vendas</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="areaTecnologia">
                            <label class="form-check-label" for="areaTecnologia">Tecnologia & Inovação</label>
                        </div>
                    </div>

                    <button class="btn btn-primary w-100 fw-bold shadow-sm py-3" id="btnGenerate"
                            onclick="generateDiagnostic()">
                        <i class="bi bi-lightning-charge-fill me-2"></i>Gerar Diagnóstico com IA
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
            <form action="<?php echo BASE_URL; ?>modules/admin/diagnosticos/criar_completo.php" method="POST"
                id="diagnosticForm">
                <input type="hidden" name="perguntas_json" id="perguntasJson">

                <div class="card shadow border-0 mb-4">
                    <div class="card-body p-4">
                        <h5 class="fw-bold text-dark mb-4 border-bottom pb-2">Informações Básicas</h5>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Título do Diagnóstico</label>
                            <input type="text" name="titulo" id="inputTitulo" class="form-control form-control-lg"
                                required placeholder="Digite o título...">
                        </div>

                        <div class="mb-0">
                            <label class="form-label fw-bold">Descrição</label>
                            <textarea name="descricao" id="inputDescricao" class="form-control" rows="3"
                                placeholder="Do que se trata este diagnóstico?"></textarea>
                        </div>
                    </div>
                </div>

                <div class="card shadow border-0">
                    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0 fw-bold text-dark">Perguntas Estruturadas</h5>
                        <span class="badge bg-light text-dark border" id="questionCount">0 perguntas</span>
                    </div>
                    <div class="card-body p-0">
                        <div id="questionsContainer" class="list-group list-group-flush">
                            <div class="text-center py-5 text-muted" id="emptyState">
                                <i class="bi bi-list-check display-4 mb-3 d-block text-secondary opacity-50"></i>
                                Nenhuma pergunta gerada ainda.<br>
                                Use o Assistente IA ao lado ou adicione manualmente.
                            </div>
                        </div>
                    </div>
                    <!-- Future: Add manual 'Add Question' button here -->
                </div>

                <div class="d-grid mt-4 mb-5">
                    <button type="submit" class="btn btn-success btn-lg fw-bold shadow-lg">
                        <i class="bi bi-check-circle-fill me-2"></i>Salvar Diagnóstico Completo
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    let generatedQuestions = [];

    async function generateDiagnostic() {
        const segment = document.getElementById('aiSegment').value;
        const audience = document.getElementById('aiAudience').value;
        const goal = document.getElementById('aiGoal').value;
        const questionCount = document.getElementById('aiQuestionCount').value;
        
        // Get selected focus areas
        const focusAreas = [];
        if (document.getElementById('areaFinanceiro').checked) focusAreas.push('Financeiro');
        if (document.getElementById('areaOperacional').checked) focusAreas.push('Operacional');
        if (document.getElementById('areaPessoas').checked) focusAreas.push('Pessoas & Cultura');
        if (document.getElementById('areaMarketing').checked) focusAreas.push('Marketing & Vendas');
        if (document.getElementById('areaTecnologia').checked) focusAreas.push('Tecnologia & Inovação');

        if (!goal) {
            alert('Por favor, defina pelo menos o Objetivo Principal.');
            return;
        }

        if (focusAreas.length === 0) {
            alert('Por favor, selecione pelo menos uma Área de Foco.');
            return;
        }

        // UI Feedback
        const btn = document.getElementById('btnGenerate');
        const loading = document.getElementById('loadingIndicator');
        const originalText = btn.innerHTML;

        btn.disabled = true;
        btn.innerHTML = '<i class="bi bi-hourglass-split me-2"></i>Gerando...';
        loading.classList.remove('d-none');

        // Enhanced Prompt Construction
        let userPrompt = `Crie um diagnóstico empresarial completo:
- Segmento: ${segment || 'Geral'}
- Público-Alvo: ${audience || 'Gestores'}
- Objetivo: ${goal}
- Quantidade: ${questionCount}
- Áreas: ${focusAreas.join(', ')}

Distribua as perguntas equilibradamente entre as áreas.`;

        try {
            const response = await fetch('<?php echo BASE_URL; ?>modules/gestao/diagnostico/proxy_n8n.php?type=diagnostic', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    prompt_sistema: `
### ROLE
Você é um Consultor de Negócios Sênior.
Crie um diagnóstico completo com Título, Descrição e Perguntas.

### FORMATO JSON OBRIGATÓRIO
{
  "titulo": "Título Sugerido",
  "descricao": "Descrição detalhada do propósito...",
  "perguntas": [
    {
      "secao": "Nome da Seção",
      "texto_pergunta": "Texto da pergunta?",
      "tipo": "selecao" | "escala" | "texto",
      "opcoes": ["A", "B"] (se aplicável),
      "logica_ia": "Motivo desta pergunta..."
    }
  ]
}

### REGRAS
- Idioma: Português (Brasil).
- Crie de 5 a 10 perguntas por seção.
- Use seções claras (ex: Financeiro, Operacional, Pessoas).
`,
                    user_input: userPrompt
                })
            });

            const text = await response.text();
            let data;
            try {
                data = JSON.parse(text);
            } catch (e) {
                console.error('Server HTML Error:', text);
                throw new Error('Erro na resposta do servidor. Verifique o console.');
            }

            if (data.error) throw new Error(data.error);

            // Parsing Logic
            let content = data.output || (data.message ? data.message.content : data);
            if (typeof content === 'string') {
                content = JSON.parse(content.replace(/```json/g, '').replace(/```/g, '').trim());
            }

            // Fill Form
            document.getElementById('inputTitulo').value = content.titulo || '';
            document.getElementById('inputDescricao').value = content.descricao || '';

            // Render Questions
            if (content.perguntas && Array.isArray(content.perguntas)) {
                generatedQuestions = content.perguntas;
                renderQuestions();
            }

        } catch (error) {
            alert('Erro ao gerar: ' + error.message);
        } finally {
            btn.disabled = false;
            btn.innerHTML = originalText;
            loading.classList.add('d-none');
        }
    }

    function renderQuestions() {
        const container = document.getElementById('questionsContainer');
        const emptyState = document.getElementById('emptyState');
        const countBadge = document.getElementById('questionCount');
        const jsonInput = document.getElementById('perguntasJson');

        container.innerHTML = '';

        if (generatedQuestions.length === 0) {
            if (emptyState) container.appendChild(emptyState);
            countBadge.innerText = '0 perguntas';
            jsonInput.value = '';
            return;
        }

        // Group by Section
        const sections = {};
        generatedQuestions.forEach((q, index) => {
            if (!sections[q.secao]) sections[q.secao] = [];
            sections[q.secao].push({ ...q, originalIndex: index });
        });

        for (const [sectionName, questions] of Object.entries(sections)) {
            // Section Header
            const header = document.createElement('div');
            header.className = 'list-group-item bg-light fw-bold text-uppercase small text-primary mt-0 py-2';
            header.innerText = sectionName;
            container.appendChild(header);

            // Questions
            questions.forEach(q => {
                const item = document.createElement('div');
                item.className = 'list-group-item p-3';
                item.innerHTML = `
                <div class="d-flex justify-content-between">
                    <div class="fw-bold mb-1">${q.texto_pergunta}</div>
                    <span class="badge bg-secondary opacity-50 text-white" style="font-size:0.65rem">${q.tipo}</span>
                </div>
                <div class="small text-muted mb-2 fst-italic">Intenção: ${q.logica_ia || 'N/A'}</div>
                ${q.opcoes ? `<div class="small text-muted"><i class="bi bi-list-ul me-1"></i> ${q.opcoes.join(', ')}</div>` : ''}
            `;
                container.appendChild(item);
            });
        }

        countBadge.innerText = `${generatedQuestions.length} perguntas`;
        jsonInput.value = JSON.stringify(generatedQuestions);
    }
</script>

<?php require_once __DIR__ . '/../../../includes/footer.php'; ?>