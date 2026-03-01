<?php
$pageTitle = "Resultado do Diagnóstico";
require_once __DIR__ . '/../../../includes/header.php';
checkPermission(['admin', 'cliente']);
?>
<!-- Chart.js for Radar Chart -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<?php
// ID do Usuário
$userId = $_SESSION['user_id'] ?? 0;

// Inicializa variáveis
$scores = [];
$geralPercentual = 0;
$nivel = '';
$nivelCor = '';
$mensagem = '';
$recomendacoes = [];
$gaps = []; // Gaps para IA

// Load n8n config
$n8nConfigFile = __DIR__ . '/../../../config/n8n_config.json';
$n8nConfig = file_exists($n8nConfigFile) ? json_decode(file_get_contents($n8nConfigFile), true) : [];
$roadmapWebhookUrl = $n8nConfig['roadmap_webhook_url'] ?? '';

// Cenário 1: Processar Novo Diagnóstico (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // 1. Buscar Definição de Perguntas (incluindo Lógica e Seção)
    $perguntasDb = [];
    $res = $conn->query("SELECT * FROM gestao_diagnostico_perguntas");
    while ($row = $res->fetch_assoc()) {
        $perguntasDb[$row['id']] = $row;
    }

    // 2. Criar Novo Registro de Histórico
    $company_id = $_SESSION['company_id'];
    $modelo_id = isset($_POST['modelo_id']) ? intval($_POST['modelo_id']) : 1;

    $dataHoje = date('Y-m-d H:i:s');
    $stmt = $conn->prepare("INSERT INTO gestao_diagnostico_resultados 
        (user_id, company_id, modelo_id, score_geral, nivel_maturidade, data_realizacao) 
        VALUES (?, ?, ?, 0, 'Em Análise', ?)");
    $stmt->bind_param("iiis", $userId, $company_id, $modelo_id, $dataHoje);

    if ($stmt->execute()) {
        $historicoId = $stmt->insert_id;
        $respostasParaIA = []; // Array estruturado para enviar ao N8N
        $respostasSalvas = 0;

        // 3. Processar Respostas do POST
        foreach ($_POST as $key => $value) {
            // Verifica se é uma resposta de pergunta (formato q{ID})
            if (preg_match('/^q(\d+)$/', $key, $matches)) {
                $perguntaId = $matches[1];

                if (!isset($perguntasDb[$perguntaId]))
                    continue;

                $perguntaData = $perguntasDb[$perguntaId];
                $respostaFormatada = '';

                // Trata Array (Checkbox) vs String (Radio/Input)
                if (is_array($value)) {
                    $respostaFormatada = implode(", ", $value); // Para salvar legível
                } else {
                    $respostaFormatada = trim($value);
                }

                // Salva na tabela de respostas
                // IMPORTANTE: Certifique-se que 'valor_escolhido' aceita TEXT no banco
                $stmtResp = $conn->prepare("INSERT INTO gestao_diagnostico_respostas (historico_id, pergunta_id, valor_escolhido) VALUES (?, ?, ?)");
                $stmtResp->bind_param("iis", $historicoId, $perguntaId, $respostaFormatada);
                $stmtResp->execute();
                $respostasSalvas++;

                // Adiciona ao Contexto da IA
                $respostasParaIA[] = [
                    'secao' => $perguntaData['secao'],
                    'pergunta' => $perguntaData['texto_pergunta'],
                    'resposta' => $respostaFormatada,
                    'logica_ia' => $perguntaData['logica_ia'] ?? '' // O "Porquê" da pergunta
                ];
            }
        }

        // 4. Salvar KPIs Financeiros (se existirem)
        // (Campos removidos do formulário atual, mas mantendo lógica legado caso voltem)
        $faturamento = $_POST['faturamento'] ?? 0;
        // ... (pode ser expandido depois)

        // 5. Redirecionar para Visualização (GET)
        // Passamos o ID recém criado
        redirect('gestao/diagnostico/resultado/' . $historicoId);
    } else {
        die("Erro ao salvar diagnóstico: " . $conn->error);
    }

}
// Cenário 2: Visualizar Histórico (GET)
else {
    // Tenta pegar pelo ID ou o último do usuário
    $historicoId = isset($_GET['id']) ? intval($_GET['id']) : 0;

    $company_id = $_SESSION['company_id'];
    if ($historicoId > 0) {
        $sql = "SELECT * FROM gestao_diagnostico_resultados WHERE id = $historicoId AND user_id = $userId AND company_id = $company_id";
    } else {
        $sql = "SELECT * FROM gestao_diagnostico_resultados WHERE user_id = $userId AND company_id = $company_id ORDER BY id DESC LIMIT 1";
    }

    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
        $dados = $result->fetch_assoc();
        if ($historicoId == 0)
            $historicoId = $dados['id'];

        $analiseSalva = $dados['analise_ia'] ?? null;
        $projetosSalvos = $dados['sugestao_projetos'] ?? null;

        // Coleta dados completos para re-enviar à IA se necessário
        $allAnswers = [];
        $sqlAll = "SELECT p.texto_pergunta, p.secao, p.logica_ia, r.valor_escolhido 
                   FROM gestao_diagnostico_respostas r 
                   JOIN gestao_diagnostico_perguntas p ON r.pergunta_id = p.id 
                   WHERE r.historico_id = $historicoId ORDER BY p.ordem ASC";
        $resAll = $conn->query($sqlAll);
        if ($resAll) {
            while ($row = $resAll->fetch_assoc()) {
                $allAnswers[] = [
                    'secao' => $row['secao'],
                    'pergunta' => $row['texto_pergunta'],
                    'resposta' => $row['valor_escolhido'],
                    'contexto' => $row['logica_ia']
                ];
            }
        }

    } else {
        // Nada encontrado
        header('Location: ' . BASE_URL . 'gestao/diagnostico/novo');
        exit;
    }
}

// Recomendações (Removido lógica hardcoded, agora 100% IA)
$recomendacoes = [];
// $scores não existe mais no sentido numérico antigo
$nivel = $dados['nivel_maturidade'] ?? 'Em Análise';
$nivelCor = ($nivel == 'Em Análise') ? 'secondary' : 'primary';

// Reconstrói estrutura de scores para exibição
$scores = [
    'operacional' => intval($dados['score_operacao'] ?? 0),
    'financeiro' => intval($dados['score_financeiro'] ?? 0),
    'aquisicao' => intval($dados['score_aquisicao'] ?? $dados['score_experiencia'] ?? 0),
    'jornada' => intval($dados['score_jornada'] ?? 0),
    'equipe' => intval($dados['score_equipe'] ?? 0)
];
?>

<div class="container py-5">
    <!-- Cabeçalho do Resultado -->
    <div class="text-center mb-5">
        <h1 class="display-4 fw-bold text-dark mb-3">Diagnóstico & Estratégia</h1>
        <p class="lead text-muted mx-auto" style="max-width: 700px;">
            Abaixo você confere sua nota por área e o plano de ação sugerido.
        </p>
        <span id="statusBadge" class="badge bg-<?php echo $nivelCor; ?> px-4 py-2 rounded-pill fs-6 mt-2">Status:
            <?php echo $nivel; ?></span>
    </div>

    <!-- Cards de Pilares (Scores Persistidos + IA) -->
    <div class="row g-3 mb-5 row-cols-1 row-cols-sm-2 row-cols-md-5" id="scoreCardsContainer">
        <!-- Card Operação -->
        <div class="col">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body p-4 text-center">
                    <h5 class="text-muted mb-3 fs-6"><i class="bi bi-gear-wide-connected me-2"></i>Operacional</h5>
                    <div class="display-6 fw-bold text-primary mb-2" id="score-operacional">
                        <?php echo $scores['operacional'] > 0 ? $scores['operacional'] . '%' : '--'; ?>
                    </div>
                    <div class="progress" style="height: 6px;">
                        <div id="bar-operacional" class="progress-bar bg-primary" role="progressbar"
                            style="width: <?php echo $scores['operacional']; ?>%"></div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Card Financeiro -->
        <div class="col">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body p-4 text-center">
                    <h5 class="text-muted mb-3 fs-6"><i class="bi bi-currency-dollar me-2"></i>Financeiro</h5>
                    <div class="display-6 fw-bold text-success mb-2" id="score-financeiro">
                        <?php echo $scores['financeiro'] > 0 ? $scores['financeiro'] . '%' : '--'; ?>
                    </div>
                    <div class="progress" style="height: 6px;">
                        <div id="bar-financeiro" class="progress-bar bg-success" role="progressbar"
                            style="width: <?php echo $scores['financeiro']; ?>%"></div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Card Aquisição/Mkt -->
        <div class="col">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body p-4 text-center">
                    <h5 class="text-muted mb-3 fs-6"><i class="bi bi-megaphone me-2"></i>Aquisição</h5>
                    <div class="display-6 fw-bold text-info mb-2" id="score-aquisicao">
                        <?php echo $scores['aquisicao'] > 0 ? $scores['aquisicao'] . '%' : '--'; ?>
                    </div>
                    <div class="progress" style="height: 6px;">
                        <div id="bar-aquisicao" class="progress-bar bg-info" role="progressbar"
                            style="width: <?php echo $scores['aquisicao']; ?>%"></div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Card Jornada -->
        <div class="col">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body p-4 text-center">
                    <h5 class="text-muted mb-3 fs-6"><i class="bi bi-heart-pulse me-2"></i>Jornada</h5>
                    <div class="display-6 fw-bold text-danger mb-2" id="score-jornada">
                        <?php echo $scores['jornada'] > 0 ? $scores['jornada'] . '%' : '--'; ?>
                    </div>
                    <div class="progress" style="height: 6px;">
                        <div id="bar-jornada" class="progress-bar bg-danger" role="progressbar"
                            style="width: <?php echo $scores['jornada']; ?>%"></div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Card Equipe -->
        <div class="col">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body p-4 text-center">
                    <h5 class="text-muted mb-3 fs-6"><i class="bi bi-people me-2"></i>Equipe</h5>
                    <div class="display-6 fw-bold text-warning mb-2" id="score-equipe">
                        <?php echo $scores['equipe'] > 0 ? $scores['equipe'] . '%' : '--'; ?>
                    </div>
                    <div class="progress" style="height: 6px;">
                        <div id="bar-equipe" class="progress-bar bg-warning" role="progressbar"
                            style="width: <?php echo $scores['equipe']; ?>%"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Radar Chart Visualization -->
    <div class="row mb-5">
        <div class="col-lg-8 mx-auto">
            <div class="card border-0 shadow-lg">
                <div class="card-body p-4">
                    <h4 class="fw-bold text-center mb-4">
                        <i class="bi bi-diagram-3 me-2 text-primary"></i>
                        Visão Geral por Área
                    </h4>
                    <div style="position: relative; height: 400px;">
                        <canvas id="radarChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Analysis Section -->
    <div class="card border-0 shadow-lg mb-5 bg-white overflow-hidden">
        <div class="card-header bg-dark text-white p-4">
            <div class="d-flex align-items-center">
                <i class="bi bi-robot fs-1 me-3 text-warning"></i>
                <div>
                    <h3 class="mb-0 fw-bold">Análise Estratégica AI</h3>
                    <p class="mb-0 opacity-75">Interpretando respostas qualitativas...</p>
                </div>
            </div>
        </div>
        <div class="card-body p-5 position-relative">

            <div id="analysisContent" class="p-3 rounded text-dark">
                <?php if (!empty($analiseSalva)): ?>
                    <div class="text-dark fade-in" style="line-height: 1.8; text-align: justify;">
                        <?php echo nl2br($analiseSalva); ?>
                    </div>
                <?php elseif (empty($n8nConfig['analysis_webhook_url'])): ?>
                    <div class="text-center text-muted small py-5">
                        <i class="bi bi-exclamation-circle me-1"></i> Recurso não configurado (URL do Webhook ausente).
                    </div>
                <?php else: ?>
                    <div class="d-flex flex-column align-items-center justify-content-center py-5">
                        <div class="spinner-border text-primary mb-3" role="status" style="width: 3rem; height: 3rem;">
                        </div>
                        <h4 class="text-dark fw-bold">Calculando Scores e Gerando Projetos...</h4>
                        <p class="text-muted">A IA está avaliando cada resposta.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Projects Container -->
    <div id="projectsContainer" class="mt-4 mb-5" style="display:none;">
        <h3 class="fw-bold mb-4 text-dark border-start border-5 border-primary ps-3">Projetos Sugeridos</h3>
        <div class="alert alert-info shadow-sm mb-4">
            <i class="bi bi-info-circle-fill me-2"></i>
            Estes são os projetos recomendados para elevar o nível da sua clínica.
        </div>
        <div class="row g-4" id="projectsList">
            <!-- Cards injected via JS -->
        </div>
    </div>


    <!-- Próximos Passos -->
    <div class="card border-0 shadow-sm bg-light mb-5">
        <div class="card-body p-5 text-center">
            <h3 class="fw-bold mb-3">Próximos Passos</h3>

            <div class="d-flex justify-content-center gap-3">
                <a href="<?php echo BASE_URL; ?>gestao/diagnostico/novo" class="btn btn-outline-secondary px-4">
                    <i class="bi bi-arrow-counterclockwise me-2"></i> Refazer Diagnóstico
                </a>
                <a href="<?php echo BASE_URL; ?>gestao/projetos" class="btn btn-primary px-4">
                    <i class="bi bi-kanban me-2"></i> Ir para Projetos
                </a>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Renderiza projetos salvos se existirem
        window.savedProjects = <?php echo !empty($projetosSalvos) ? $projetosSalvos : 'null'; ?>;

        if (window.savedProjects) {
            const pContainer = document.getElementById('projectsContainer');
            const pList = document.getElementById('projectsList');
            if (pContainer && pList && typeof renderProjects === 'function') {
                renderProjects(window.savedProjects, pList);
                pContainer.style.display = 'block';
                window.currentProjects = window.savedProjects;
            }
        }

        <?php if (empty($analiseSalva) && !empty($n8nConfig['analysis_webhook_url'])): ?>
            generateAnalysis();
        <?php endif; ?>
    });

    function generateAnalysis() {
        const contentBox = document.getElementById('analysisContent');

        // PROMPT ESTRUTURADO PARA O N8N (V3 - COM 5 PILARES E ANÁLISE RICA)
        const systemPrompt = `
### 🎯 SEU OBJETIVO:
Você é um Consultor Sênior de Estratégia Empresarial (Ex-McKinsey). Sua missão é ler as respostas do diagnóstico e fornecer um raio-x brutalmente honesto.

### ⚠️ FORMATO DE RESPOSTA (JSON OBRIGATÓRIO):
Sua resposta deve ser ESTRITAMENTE um objeto JSON válido. Nada de markdown fora do JSON.
**IMPORTANTE:** Ao escrever o HTML dentro do JSON, use **ASPAS SIMPLES** ('') para atributos HTML (ex: <h3 class='titulo'>) e **evite** quebras de linha reais no meio da string (use \n se necessário).

{
  "scores": {
    "operacional": (inteiro 0-100),
    "financeiro": (inteiro 0-100),
    "aquisicao": (inteiro 0-100),
    "jornada": (inteiro 0-100),
    "equipe": (inteiro 0-100)
  },
  "analise_executiva": "(HTML RICO OBRIGATÓRIO. NÃO USE MARKDOWN AQUI, USE TAGS HTML. Estruture em 3 seções: <h3>🔍 O Cenário Atual</h3> <p>Resumo direto...</p> <h3>⚠️ Principais Gargalos</h3> <ul><li>Gargalo 1...</li><li>Gargalo 2...</li></ul> <h3>🚀 O Caminho de Ouro</h3> <p>A grande oportunidade...</p>)",
  "projetos": [
    {
      "titulo": "(Nome do Projeto - Curto e Ação)",
      "descricao": "(Por que fazer isso?)",
      "prioridade": "alta",
      "okrs": [
        {
          "titulo": "(Objetivo)",
          "krs": ["(Meta 1)", "(Meta 2)"]
        }
      ],
      "tarefas": [
        {
          "titulo": "(Verbo + Ação)",
          "descricao": "O que é: ... \\nPasso a Passo:\\n1. ...",
          "prazo": "YYYY-MM-DD"
        }
      ]
    }
  ]
}

### REGRAS DE OURO:
1. **Seja Visual:** Use <h3>, <ul>, <li> e <strong> dentro do campo 'analise_executiva'. O texto deve ser escaneável.
2. **5 Pilares:** Avalie Operação, Financeiro, Aquisição, Jornada e Equipe.
3. **Sem Generalismos:** Não diga "Melhorar processos". Diga "O processo de vendas via WhatsApp está passivo e perde 40% dos leads". CITE AS RESPOSTAS DO USUÁRIO.

### CONTEXTO DO CLIENTE
O cliente preencheu um formulário de diagnóstico. As respostas seguem abaixo.
`;

        // Extract unique sections
        const uniqueSections = [...new Set(<?php echo json_encode($allAnswers ?? []); ?>.map(item => item.secao))];

        const payload = {
            respostas: <?php echo json_encode($allAnswers ?? []); ?>,
            secoes_validas: uniqueSections,
            user_name: '<?php echo $_SESSION['nome'] ?? 'Usuário'; ?>',
            prompt_sistema: systemPrompt,
            context: 'Gere a análise completa seguindo o prompt_sistema fornecido. ATENÇÃO: Gere scores APENAS para as seções listadas em secoes_validas.'
        };
        console.log("Payload sent to n8n:", payload);

        fetch('<?php echo BASE_URL; ?>modules/gestao/diagnostico/proxy_n8n.php?type=analysis', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(payload)
        })
            .then(r => {
                if (!r.ok) return r.text().then(t => { throw new Error(t || r.statusText) });
                return r.json();
            })
            .then(data => {
                console.log('Analysis Data received:', data);

                let projectsBox = document.getElementById('projectsContainer');
                let projectsList = document.getElementById('projectsList');
                let finalText = '';

                // Helper para limpar e parsear JSON vindo de LLM
                const tryParse = (input) => {
                    if (typeof input === 'object' && input !== null) return input;
                    if (typeof input !== 'string') return null;

                    // Tenta limpar Markdown ```json ... ```
                    let clean = input.replace(/```json/g, '').replace(/```/g, '').trim();
                    try { return JSON.parse(clean); } catch (e) { return null; }
                };

                let parsedData = data;

                // Se o n8n devolve { "output": "..." } ou { "text": "..." }
                if (data.output && typeof data.output === 'string') {
                    let inner = tryParse(data.output);
                    if (inner) parsedData = inner;
                    else parsedData = data.output; // Mantém como string para fallback
                } else if (typeof data === 'string') {
                    let inner = tryParse(data);
                    if (inner) parsedData = inner;
                }

                // --- CENÁRIO 1: DADOS ESTRUTURADOS (JSON VÁLIDO COM CAMPOS ESPERADOS) ---
                // Verifica se TEM cara de JSON da nossa estrutura
                if (typeof parsedData === 'object' && parsedData !== null && (parsedData.scores || parsedData.analise_executiva || parsedData.projetos)) {

                    // 1. Atualizar SCORES
                    if (parsedData.scores) {
                        updateScore('operacional', parsedData.scores.operacional || parsedData.scores.operacao || 0);
                        updateScore('financeiro', parsedData.scores.financeiro || 0);
                        updateScore('aquisicao', parsedData.scores.aquisicao || parsedData.scores.vendas || parsedData.scores.marketing || 0);
                        updateScore('jornada', parsedData.scores.jornada || parsedData.scores.experiencia || 0);
                        updateScore('equipe', parsedData.scores.equipe || parsedData.scores.lideranca || 0);

                        // Update Radar Chart
                        updateRadarChart(parsedData.scores);
                    }

                    // 2. Renderiza Texto
                    finalText = parsedData.analise_executiva || "Análise executiva não encontrada.";
                    formatAndDisplayText(finalText, contentBox);

                    // 3. Renderiza Projetos
                    if (parsedData.projetos && Array.isArray(parsedData.projetos)) {
                        renderProjects(parsedData.projetos, projectsList);
                        projectsBox.style.display = 'block';
                        window.currentProjects = parsedData.projetos;
                    }

                    // Salva análise estruturada (como string para futuro log)
                    saveAnalysisToDb(JSON.stringify(parsedData));
                    return;
                }

                // --- CENÁRIO 2: FALLBACK (TEXTO PURO OU ESTRUTURA DESCONHECIDA) ---
                console.warn("Estrutura JSON não detectada, usando fallback texto.");

                let text = '';
                if (typeof parsedData === 'string') {
                    text = parsedData;
                } else if (data && typeof data === 'object') {
                    text = data.output || data.text || data.message || JSON.stringify(data);
                } else {
                    text = String(data);
                }

                // Limpeza básica
                text = text.replace(/```json/g, '').replace(/```/g, '');

                formatAndDisplayText(text, contentBox);
                saveAnalysisToDb(text);
            })
            .catch(e => {
                console.error(e);
                document.getElementById('analysisContent').innerHTML = '<div class="alert alert-warning">Erro ao processar: ' + e.message + '</div>';
            });
    }

    function formatAndDisplayText(text, container) {
        // Formatação Visual (Titulos, Listas)
        text = text.replace(/1\.\s*(.*?):/g, '<h5 class="text-primary fw-bold mt-4 mb-3 border-bottom pb-2"><i class="bi bi-binoculars me-2"></i>1. $1</h5>');
        text = text.replace(/2\.\s*(.*?):/g, '<h5 class="text-success fw-bold mt-4 mb-3 border-bottom pb-2"><i class="bi bi-lightning-charge me-2"></i>2. $1</h5>');
        text = text.replace(/3\.\s*(.*?):/g, '<h5 class="text-danger fw-bold mt-4 mb-3 border-bottom pb-2"><i class="bi bi-exclamation-triangle me-2"></i>3. $1</h5>');
        text = text.replace(/- \s*(.*?)(?=\n|$|<)/g, '<div class="d-flex align-items-baseline mb-2 ms-3"><i class="bi bi-check2-circle text-primary me-2 flex-shrink-0"></i><span>$1</span></div>');
        text = text.replace(/\n/g, '<br>');

        container.innerHTML = '<div class="text-dark fade-in" style="font-size: 1rem; line-height: 1.6;">' + text + '</div>';
    }

    function renderProjects(projects, container) {
        container.innerHTML = '';
        projects.forEach((p, index) => {
            let okrsHtml = p.okrs ? p.okrs.map(o => `<li class="small text-muted">${o.titulo}</li>`).join('') : '';

            let html = `
                <div class="col-md-6">
                    <div class="card h-100 border-0 shadow-sm project-card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <h5 class="card-title fw-bold text-dark mb-0">${p.titulo}</h5>
                                <span class="badge bg-${getPriorityColor(p.prioridade)} rounded-pill">${p.prioridade || 'Normal'}</span>
                            </div>
                            <p class="card-text text-muted small">${p.descricao}</p>
                            
                            <div class="mt-3 bg-light p-2 rounded">
                                <strong class="small text-dark d-block mb-1"><i class="bi bi-bullseye me-1"></i>Objetivos:</strong>
                                <ul class="list-unstyled mb-0 ps-2" style="font-size: 0.85rem;">
                                    ${okrsHtml || '<li class="text-muted fst-italic">Sem objetivos definidos</li>'}
                                </ul>
                            </div>
                        </div>
                        <div class="card-footer bg-white border-0 pt-0 pb-3">
                            <button class="btn btn-outline-primary w-100 fw-bold btn-approve" 
                                onclick="approveProject(${index}, this)" id="btn-approve-${index}">
                                <i class="bi bi-check-lg me-2"></i> Aprovar e Criar
                            </button>
                        </div>
                    </div>
                </div>
            `;
            container.innerHTML += html;
        });
    }

    function getPriorityColor(p) {
        if (p === 'alta' || p === 'critica') return 'danger';
        if (p === 'media') return 'warning';
        return 'info';
    }

    function approveProject(index, btn) {
        if (!window.currentProjects || !window.currentProjects[index]) return;
        const project = window.currentProjects[index];
        const diagId = <?php echo $historicoId; ?>;

        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Criando...';

        fetch('<?php echo BASE_URL; ?>modules/gestao/projetos/criar_automatico.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ diagnostico_id: diagId, projeto: project })
        })
            .then(r => r.json())
            .then(d => {
                if (d.success) {
                    btn.className = 'btn btn-success w-100 fw-bold';
                    btn.innerHTML = '<i class="bi bi-check-circle-fill me-2"></i> Criado com Sucesso';
                    // Opcional: Redirecionar para o projeto?
                } else {
                    alert('Erro: ' + d.error);
                    btn.disabled = false;
                    btn.innerHTML = 'Tentar Novamente';
                }
            })
            .catch(e => {
                console.error(e);
                alert('Erro de conexão');
                btn.disabled = false;
            });
    }

    function saveAnalysisToDb(data) {
        let payload = { historico_id: <?php echo $historicoId; ?> };

        try {
            // Tenta ver se é objeto estruturado
            let json = (typeof data === 'object') ? data : JSON.parse(data);

            payload.analise = json.analise_executiva || json.text || (typeof data === 'string' ? data : '');
            payload.scores = json.scores || {};
            payload.projetos = json.projetos || [];

        } catch (e) {
            // É texto puro
            payload.analise = data;
        }

        fetch('<?php echo BASE_URL; ?>modules/gestao/diagnostico/salvar_analise.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(payload)
        })
            .then(r => r.json())
            .then(d => {
                if (d.success) console.log('Dados salvos com sucesso.');
                else console.error('Erro ao salvar:', d.message);
            })
            .catch(e => console.error('Erro de conexão ao salvar:', e));
    }

    function updateScore(area, score) {
        score = parseInt(score) || 0;
        const scoreEl = document.getElementById('score-' + area);
        const barEl = document.getElementById('bar-' + area);

        if (scoreEl && barEl) {
            scoreEl.innerText = score + '%';
            barEl.style.width = score + '%';
            barEl.setAttribute('aria-valuenow', score);
        }
    }

    // Radar Chart Management
    let radarChartInstance = null;

    function initRadarChart() {
        const ctx = document.getElementById('radarChart');
        if (!ctx) return;

        radarChartInstance = new Chart(ctx, {
            type: 'radar',
            data: {
                labels: [],
                datasets: [{
                    label: 'Pontuação',
                    data: [],
                    fill: true,
                    backgroundColor: 'rgba(29, 47, 95, 0.2)',
                    borderColor: 'rgba(29, 47, 95, 1)',
                    pointBackgroundColor: 'rgba(29, 47, 95, 1)',
                    pointBorderColor: '#fff',
                    pointHoverBackgroundColor: '#fff',
                    pointHoverBorderColor: 'rgba(29, 47, 95, 1)',
                    borderWidth: 2,
                    pointRadius: 5,
                    pointHoverRadius: 7
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    r: {
                        beginAtZero: true,
                        max: 100,
                        min: 0,
                        ticks: {
                            stepSize: 20,
                            font: {
                                size: 12
                            }
                        },
                        pointLabels: {
                            font: {
                                size: 14,
                                weight: 'bold'
                            }
                        },
                        grid: {
                            color: 'rgba(0, 0, 0, 0.1)'
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            label: function (context) {
                                return context.label + ': ' + context.parsed.r + '%';
                            }
                        }
                    }
                }
            }
        });
    }

    function updateRadarChart(scoresData) {
        if (!radarChartInstance) {
            initRadarChart();
        }

        if (!radarChartInstance || !scoresData) return;

        // Extract labels and values dynamically
        const labels = [];
        const values = [];

        // Map common field names to display names
        const labelMap = {
            'operacional': 'Operacional',
            'operacao': 'Operacional',
            'financeiro': 'Financeiro',
            'aquisicao': 'Aquisição',
            'vendas': 'Vendas',
            'marketing': 'Marketing',
            'jornada': 'Jornada',
            'experiencia': 'Experiência',
            'equipe': 'Equipe',
            'lideranca': 'Liderança',
            'pessoas': 'Pessoas',
            'tecnologia': 'Tecnologia',
            'inovacao': 'Inovação'
        };

        for (const [key, value] of Object.entries(scoresData)) {
            const score = parseInt(value) || 0;
            if (score > 0) {
                const displayName = labelMap[key.toLowerCase()] || key.charAt(0).toUpperCase() + key.slice(1);
                labels.push(displayName);
                values.push(score);
            }
        }

        // Update chart data
        radarChartInstance.data.labels = labels;
        radarChartInstance.data.datasets[0].data = values;
        radarChartInstance.update();
    }

    // Initialize chart on page load
    document.addEventListener('DOMContentLoaded', function () {
        initRadarChart();

        // Update with initial PHP scores if available
        const initialScores = {
            operacional: <?php echo $scores['operacional'] ?? 0; ?>,
            financeiro: <?php echo $scores['financeiro'] ?? 0; ?>,
            aquisicao: <?php echo $scores['aquisicao'] ?? 0; ?>,
            jornada: <?php echo $scores['jornada'] ?? 0; ?>,
            equipe: <?php echo $scores['equipe'] ?? 0; ?>
        };

        updateRadarChart(initialScores);
    });
</script>

<?php require_once __DIR__ . '/../../../includes/footer.php'; ?>