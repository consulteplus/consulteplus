<?php
$pageTitle = "Plano de Ação Inteligente";
require_once __DIR__ . '/../../../includes/header.php';
checkPermission(['admin', 'cliente']);

$userId = $_SESSION['user_id'] ?? 0;
$historicoId = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($historicoId === 0) {
    // Tenta pegar o último se não fornecido
    $result = $conn->query("SELECT id FROM gestao_diagnostico_resultados WHERE user_id = $userId ORDER BY id DESC LIMIT 1");
    if ($result && $row = $result->fetch_assoc()) {
        $historicoId = $row['id'];
    } else {
        header('Location: ' . BASE_URL . 'gestao/diagnostico/novo');
        exit;
    }
}

// Fetch Result Data for Context
$dados = $conn->query("SELECT * FROM gestao_diagnostico_resultados WHERE id = $historicoId AND user_id = $userId")->fetch_assoc();
if (!$dados) {
    die("Resultado não encontrado.");
}

$nivel = $dados['nivel_maturidade'];
$scores = [
    'operacao' => ['percentual' => intval($dados['score_operacao'])],
    'financeiro' => ['percentual' => intval($dados['score_financeiro'])],
    'experiencia' => ['percentual' => intval($dados['score_experiencia'])]
];

// Check for existing suggestions
$existingSuggestions = [];
$resSug = $conn->query("SELECT * FROM gestao_diagnostico_sugestoes WHERE historico_id = $historicoId ORDER BY id ASC");
if ($resSug && $resSug->num_rows > 0) {
    while ($row = $resSug->fetch_assoc()) {
        $existingSuggestions[] = $row;
    }
}

// Fetch GAPS
$gaps = [];
$sqlGaps = "SELECT p.texto_pergunta, p.secao, r.valor_escolhido 
            FROM gestao_diagnostico_respostas r 
            JOIN gestao_diagnostico_perguntas p ON r.pergunta_id = p.id 
            WHERE r.historico_id = $historicoId AND r.valor_escolhido < 75";
$resGaps = $conn->query($sqlGaps);
if ($resGaps) {
    while ($row = $resGaps->fetch_assoc()) {
        $gaps[] = [
            'pergunta' => $row['texto_pergunta'],
            'secao' => $row['secao'],
            'valor' => $row['valor_escolhido']
        ];
    }
}

// Configs
$n8nConfigFile = __DIR__ . '/../../../config/n8n_config.json';
$n8nConfig = file_exists($n8nConfigFile) ? json_decode(file_get_contents($n8nConfigFile), true) : [];
$roadmapWebhookUrl = $n8nConfig['roadmap_webhook_url'] ?? '';

?>

<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="fw-bold mb-0">Seu Plano de Ação</h1>
            <p class="text-muted">Gerado por Inteligência Artificial com base no diagnóstico
                #<?php echo $historicoId; ?></p>
        </div>
        <a href="<?php echo BASE_URL; ?>gestao/diagnostico/resultado?id=<?php echo $historicoId; ?>"
            class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i> Voltar ao Diagnóstico
        </a>
    </div>

    <!-- Loading State -->
    <div id="loadingArea"
        class="<?php echo !empty($existingSuggestions) ? 'd-none' : ''; ?> text-center py-5 bg-white rounded shadow-sm">
        <div class="spinner-border text-primary mb-3" role="status" style="width: 3rem; height: 3rem;"></div>
        <h4 class="fw-bold animate-pulse">Construindo seu roadmap personalizado...</h4>
        <p class="text-muted">Nossa IA está analisando seus gaps e priorizando as melhores ações.</p>
    </div>

    <!-- Results Area -->
    <div id="resultsArea" class="<?php echo empty($existingSuggestions) ? 'd-none' : ''; ?>">

        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white p-4 border-bottom">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 text-primary"><i class="bi bi-stars me-2"></i>Sugestões da IA</h5>
                    <div>
                        <button onclick="selectAll(true)" class="btn btn-sm btn-link text-decoration-none">Marcar
                            Todas</button>
                        <button onclick="selectAll(false)"
                            class="btn btn-sm btn-link text-decoration-none text-muted">Desmarcar</button>
                    </div>
                </div>
            </div>
            <div class="card-body p-0">
                <div id="aiTasksList" class="list-group list-group-flush">
                    <!-- Tasks will be injected here via JS -->
                </div>
            </div>
            <div class="card-footer bg-light p-4 text-end">
                <button onclick="processSelection()" id="btnSave" class="btn btn-success btn-lg shadow-sm">
                    <i class="bi bi-check-circle-fill me-2"></i> Confirmar Seleção
                </button>
            </div>
        </div>

    </div>

    <!-- Error Area -->
    <div id="errorArea" class="d-none alert alert-danger shadow-sm">
        <h5 class="alert-heading"><i class="bi bi-exclamation-triangle-fill me-2"></i>Ops! Algo deu errado.</h5>
        <p id="errorMsg" class="mb-0"></p>
        <div class="mt-3">
            <button onclick="location.reload()" class="btn btn-sm btn-outline-danger">Tentar Novamente</button>
        </div>
    </div>

</div>

<script>
    let generatedTasks = <?php echo json_encode($existingSuggestions); ?>;
    const historicoId = <?php echo $historicoId; ?>;

    document.addEventListener('DOMContentLoaded', function () {
        if (generatedTasks.length > 0) {
            renderTasks(generatedTasks);
        } else {
            generateAiRoadmap();
        }
    });

    function generateAiRoadmap() {
        const loading = document.getElementById('loadingArea');
        const results = document.getElementById('resultsArea');
        const errorArea = document.getElementById('errorArea');

        // Check config
        const webhookConfigured = '<?php echo !empty($roadmapWebhookUrl) ? "1" : ""; ?>';
        if (!webhookConfigured) {
            loading.classList.add('d-none');
            errorArea.classList.remove('d-none');
            document.getElementById('errorMsg').innerText = 'URL do Webhook da IA não configurada. Vá em Configurações.';
            return;
        }

        const payload = {
            scores: <?php echo json_encode($scores ?? []); ?>,
            gaps: <?php echo json_encode($gaps ?? []); ?>,
            nivel: '<?php echo $nivel ?? ''; ?>',
            user_name: '<?php echo $_SESSION['nome'] ?? 'Usuário'; ?>',
            context: 'Geração de Roadmap Tático: Liste tarefas acionáveis com Títulos claros e Descrições detalhadas (passo a passo) de como executar cada uma.'
        };

        fetch('<?php echo BASE_URL; ?>modules/gestao/diagnostico/proxy_n8n.php?type=roadmap', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(payload)
        })
            .then(r => {
                if (!r.ok) return r.text().then(t => { throw new Error(t || r.statusText) });
                return r.text();
            })
            .then(text => {
                console.log('Raw AI Response:', text);
                let data = parseAiResponse(text);
                let tasks = findTasksArray(data);

                if (!Array.isArray(tasks) || tasks.length === 0) {
                    // Debug: mostre o JSON recebido na mensagem de erro para entendermos o formato
                    let debugStr = JSON.stringify(data);
                    if (debugStr.length > 200) debugStr = debugStr.substring(0, 200) + '...';
                    throw new Error('Não identifiquei uma lista de tarefas. O retorno foi: ' + debugStr);
                }

                // Normaliza e Salva no Banco "Pendente"
                const normalizedTasks = tasks.map(t => ({
                    titulo: t.titulo || t.title || t.acao || t.action || 'S/ Título',
                    descricao: t.descricao || t.description || '',
                    area: t.area || t.category || t.secao || 'Geral',
                    impacto: t.impacto || t.impact || '',
                    status: 'pendente'
                }));

                saveSuggestionsToDb(normalizedTasks);
            })
            .catch(e => {
                console.error(e);
                loading.classList.add('d-none');
                errorArea.classList.remove('d-none');
                document.getElementById('errorMsg').innerText = e.message;
            });
    }

    function saveSuggestionsToDb(tasks) {
        const formData = new FormData();
        formData.append('action', 'save_suggestions');
        formData.append('historico_id', historicoId);
        formData.append('suggestions', JSON.stringify(tasks));

        fetch('<?php echo BASE_URL; ?>modules/gestao/tarefas/acoes.php', { method: 'POST', body: formData })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    // Recarrega do banco para pegar IDs
                    location.reload();
                } else {
                    throw new Error(data.message);
                }
            })
            .catch(e => {
                console.error(e);
                alert('Erro ao salvar sugestões: ' + e.message);
            });
    }

    function parseAiResponse(text) {
        // 1. Limpeza básica de Markdown
        text = text.replace(/```json/g, '').replace(/```/g, '').trim();

        try {
            // Tenta parse direto
            const data = JSON.parse(text);

            // Se for string, pode ser JSON encapsulado (double encoded)
            if (typeof data === 'string') {
                try { return JSON.parse(data); } catch (e) { return data; }
            }
            return data;
        } catch (e) {
            // Se falhou, tenta extrair JSON array via Regex
            const jsonMatch = text.match(/\[\s*\{.*\}\s*\]/s);
            if (jsonMatch) {
                try { return JSON.parse(jsonMatch[0]); } catch (e2) { }
            }

            // Tenta extrair Objeto via Regex (caso venha { "tasks": [...] })
            const objMatch = text.match(/\{\s*".*"\s*:\s*.*\}/s);
            if (objMatch) {
                try { return JSON.parse(objMatch[0]); } catch (e3) { }
            }

            throw new Error('Não foi possível converter a resposta em JSON. (' + text.substring(0, 50) + '...)');
        }
    }

    function findTasksArray(obj, depth = 0) {
        if (depth > 5 || !obj) return null;

        // 1. Já é array?
        if (Array.isArray(obj)) {
            // Verifica formato n8n [{json: {...}}]
            if (obj.length > 0 && obj[0].json && typeof obj[0].json === 'object') {
                return obj.map(i => i.json);
            }
            // Retorna o próprio array se tiver itens
            if (obj.length > 0) return obj;
        }

        // 2. É objeto? Procura recursivamente
        if (typeof obj === 'object') {
            for (let k in obj) {
                let val = obj[k];

                // NOVIDADE: Se o valor for uma string grande, pode ser um JSON preso dentro de string
                // Ex: { "output": "[{\"titulo\":...}]" }
                if (typeof val === 'string' && val.length > 10 && (val.includes('[') || val.includes('```json'))) {
                    try {
                        let cleanVal = val.replace(/```json/g, '').replace(/```/g, '').trim();
                        let jsonMatch = cleanVal.match(/\[\s*\{.*\}\s*\]/s);
                        if (jsonMatch) {
                            let parsed = JSON.parse(jsonMatch[0]);
                            if (Array.isArray(parsed) && parsed.length > 0) return parsed;
                        }
                    } catch (err) { }
                }

                // Recursão normal para objetos filhos
                if (typeof val === 'object') {
                    let found = findTasksArray(val, depth + 1);
                    if (found) return found;
                }
            }
        }

        return null;
    }

    function renderTasks(tasks) {
        generatedTasks = tasks;
        const container = document.getElementById('aiTasksList');
        const loading = document.getElementById('loadingArea');
        const results = document.getElementById('resultsArea');

        container.innerHTML = '';
        loading.classList.add('d-none');
        results.classList.remove('d-none');

        if (tasks.length === 0) {
            container.innerHTML = '<div class="p-4 text-center text-muted">Nenhuma sugestão encontrada.</div>';
            return;
        }

        tasks.forEach((task, index) => {
            const areaColor = (task.area || '').toLowerCase().includes('finan') ? 'success' : ((task.area || '').toLowerCase().includes('opera') ? 'primary' : 'info');

            let statusBadge = '';
            let disabled = '';
            let checked = 'checked';

            if (task.status === 'criada') {
                statusBadge = '<span class="badge bg-success ms-2">Já Criada</span>';
                disabled = 'disabled';
                checked = '';
            } else if (task.status === 'recusada') {
                statusBadge = '<span class="badge bg-secondary ms-2">Recusada</span>';
                disabled = 'disabled'; // Ou permitir reativar? Por enquanto disable.
                checked = '';
            }

            const html = `
                <div class="list-group-item p-3 border-bottom hover-bg-light ${task.status !== 'pendente' ? 'bg-light opacity-75' : ''}">
                    <div class="d-flex align-items-start">
                        <div class="me-3 pt-1">
                            <input class="form-check-input task-checkbox" type="checkbox" value="${index}" ${checked} ${disabled} style="width: 1.3em; height: 1.3em;">
                        </div>
                        <div class="flex-grow-1">
                            <div class="d-flex justify-content-between mb-1">
                                <div>
                                    <span class="badge bg-${areaColor} rounded-pill">${task.area || 'Geral'}</span>
                                    ${statusBadge}
                                </div>
                                ${task.impacto ? `<small class="text-success fw-bold"><i class="bi bi-graph-up me-1"></i>${task.impacto}</small>` : ''}
                            </div>
                            <h6 class="mb-1 fw-bold text-dark">${task.titulo}</h6>
                            <p class="mb-0 text-muted small">${task.descricao}</p>
                        </div>
                    </div>
                </div>
            `;
            container.innerHTML += html;
        });
    }

    function selectAll(check) {
        document.querySelectorAll('.task-checkbox:not(:disabled)').forEach(cb => cb.checked = check);
    }

    function processSelection() {
        // Encontra pendentes selecionados (para criar)
        const toCreate = [];
        // Encontra pendentes NÃO selecionados (para recusar)
        const toReject = [];

        generatedTasks.forEach((task, index) => {
            // Ignora os que já não estão pendentes
            if (task.status !== 'pendente') return;

            const checkbox = document.querySelector(`.task-checkbox[value="${index}"]`);
            if (checkbox && checkbox.checked) {
                toCreate.push(task.id);
            } else {
                toReject.push(task.id);
            }
        });

        if (toCreate.length === 0 && toReject.length === 0) {
            alert('Nada para atualizar.');
            return;
        }

        if (toCreate.length === 0 && !confirm('Você não selecionou nenhuma tarefa. Todas as pendentes serão marcadas como recusadas. Deseja continuar?')) {
            return;
        }

        const btn = document.getElementById('btnSave');
        const originalText = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Processando...';

        const formData = new FormData();
        formData.append('action', 'process_suggestions');
        formData.append('create_ids', JSON.stringify(toCreate));
        formData.append('reject_ids', JSON.stringify(toReject));

        fetch('<?php echo BASE_URL; ?>modules/gestao/tarefas/acoes.php', { method: 'POST', body: formData })
            .then(r => r.json())
            .then(d => {
                if (d.success) {
                    alert('Seleção processada com sucesso!');
                    location.reload();
                } else {
                    alert('Erro: ' + d.message);
                    btn.disabled = false;
                    btn.innerHTML = originalText;
                }
            })
            .catch(e => {
                alert('Erro de conexão.');
                btn.disabled = false;
                btn.innerHTML = originalText;
            });
    }
</script>

<?php require_once __DIR__ . '/../../../includes/footer.php'; ?>