<?php
$pageTitle = "Editar Diagnóstico";
require_once __DIR__ . '/../header.php';
// checkPermission(['superadmin']);

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$company_id = $_SESSION['company_id'];

// 1. Fetch Model
$stmt = $conn->prepare("SELECT * FROM gestao_diagnostico_modelos WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$modelo = $stmt->get_result()->fetch_assoc();

if (!$modelo) {
    die("Diagnóstico não encontrado.");
}

// 2. Fetch Questions
$stmtQ = $conn->prepare("SELECT * FROM gestao_diagnostico_perguntas WHERE modelo_id = ? ORDER BY ordem ASC");
$stmtQ->bind_param("i", $id);
$stmtQ->execute();
$resQ = $stmtQ->get_result();

$perguntas = [];
while ($row = $resQ->fetch_assoc()) {
    // Decode options if JSON
    if (!empty($row['opcoes'])) {
        $decoded = json_decode($row['opcoes'], true);
        if (json_last_error() === JSON_ERROR_NONE) {
            $row['opcoes'] = $decoded;
        } else {
            // If comma separated or other format, keep as is or array
            $row['opcoes'] = [];
        }
    } else {
        $row['opcoes'] = [];
    }
    $perguntas[] = $row;
}

$perguntasJson = json_encode($perguntas);
?>

<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Editar Diagnóstico</h1>
            <p class="text-muted small mb-0">Modifique o título, descrição ou perguntas.</p>
        </div>
        <a href="<?php echo BASE_URL; ?>admin/diagnosticos" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-2"></i>Voltar
        </a>
    </div>

    <div class="row g-4">
        <!-- Coluna da Esquerda: Assistente IA (Opcional - Mantendo para quem quiser adicionar mais) -->
        <div class="col-lg-4">
            <div class="card shadow border-0 mb-4 sticky-top" style="top: 100px; z-index: 1;">
                <div class="card-header bg-gradient-primary text-white py-3">
                    <h5 class="card-title mb-0"><i class="bi bi-stars me-2"></i>Adicionar com IA</h5>
                </div>
                <div class="card-body bg-light">
                    <p class="small text-muted mb-3">
                        Gere novas perguntas para adicionar ao diagnóstico existente.
                    </p>

                    <div class="mb-3">
                        <label class="form-label small fw-bold text-uppercase text-muted">Tópico / Seção</label>
                        <input type="text" class="form-control" id="aiTopic" placeholder="Ex: Financeiro">
                    </div>

                    <button class="btn btn-outline-primary w-100 fw-bold shadow-sm" id="btnGenerate"
                        onclick="generateMoreQuestions()">
                        <i class="bi bi-plus-circle me-2"></i>Gerar Perguntas
                    </button>

                    <div id="loadingIndicator" class="text-center mt-3 d-none">
                        <div class="spinner-border text-primary spinner-border-sm" role="status"></div>
                        <span class="small ms-2 text-muted">Criando...</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Coluna da Direita: Formulário Principal -->
        <div class="col-lg-8">
            <form action="<?php echo BASE_URL; ?>modules/admin/diagnosticos/atualizar_completo.php" method="POST"
                id="diagnosticForm">
                <input type="hidden" name="id" value="<?php echo $id; ?>">
                <input type="hidden" name="perguntas_json" id="perguntasJson">

                <div class="card shadow border-0 mb-4">
                    <div class="card-body p-4">
                        <h5 class="fw-bold text-dark mb-4 border-bottom pb-2">Informações Básicas</h5>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Título do Diagnóstico</label>
                            <input type="text" name="titulo" id="inputTitulo" class="form-control form-control-lg"
                                required value="<?php echo htmlspecialchars($modelo['titulo']); ?>">
                        </div>

                        <div class="mb-0">
                            <label class="form-label fw-bold">Descrição</label>
                            <textarea name="descricao" id="inputDescricao" class="form-control"
                                rows="3"><?php echo htmlspecialchars($modelo['descricao']); ?></textarea>
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
                            <!-- JS will populate -->
                        </div>
                    </div>
                </div>

                <div class="d-grid mt-4 mb-5">
                    <button type="submit" class="btn btn-primary btn-lg fw-bold shadow-lg">
                        <i class="bi bi-save me-2"></i>Salvar Alterações
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Load existing questions
    let generatedQuestions = <?php echo $perguntasJson; ?>;

    function renderQuestions() {
        const container = document.getElementById('questionsContainer');
        const countBadge = document.getElementById('questionCount');
        const jsonInput = document.getElementById('perguntasJson');

        container.innerHTML = '';
        console.log(generatedQuestions);

        if (generatedQuestions.length === 0) {
            container.innerHTML = '<div class="text-center py-5 text-muted">Nenhuma pergunta.</div>';
            countBadge.innerText = '0 perguntas';
            jsonInput.value = '[]';
            return;
        }

        // Group by Section
        const sections = {};
        generatedQuestions.forEach((q, index) => {
            const secao = q.secao || 'Geral';
            if (!sections[secao]) sections[secao] = [];
            sections[secao].push({ ...q, tempId: index });
        });

        for (const [sectionName, questions] of Object.entries(sections)) {
            // Section Header
            const header = document.createElement('div');
            header.className = 'list-group-item bg-light fw-bold text-uppercase small text-primary mt-0 py-2 d-flex justify-content-between align-items-center';
            header.innerHTML = `<span>${sectionName}</span>`;
            container.appendChild(header);

            // Questions
            questions.forEach(q => {
                const item = document.createElement('div');
                item.className = 'list-group-item p-3';

                let optionsHtml = '';
                if (q.opcoes && Array.isArray(q.opcoes) && q.opcoes.length > 0) {
                    optionsHtml = `<div class="small text-muted mt-2"><i class="bi bi-list-ul me-1"></i> ${q.opcoes.join(', ')}</div>`;
                }

                item.innerHTML = `
                <div class="d-flex justify-content-between">
                    <div class="fw-bold mb-1">${q.texto_pergunta}</div>
                    <div>
                         <span class="badge bg-secondary opacity-50 text-white me-2" style="font-size:0.65rem">${q.tipo}</span>
                         <button type="button" class="btn btn-sm btn-link text-danger p-0 border-0" onclick="removeQuestion(${q.tempId})">
                            <i class="bi bi-trash"></i>
                         </button>
                    </div>
                </div>
                <div class="small text-muted mb-0 fst-italic">Lógica: ${q.logica_ia || 'N/A'}</div>
                ${optionsHtml}
            `;
                container.appendChild(item);
            });
        }

        countBadge.innerText = `${generatedQuestions.length} perguntas`;
        jsonInput.value = JSON.stringify(generatedQuestions);
    }

    function removeQuestion(tempId) {
        if (confirm('Remover esta pergunta?')) {
            generatedQuestions.splice(tempId, 1);
            renderQuestions(); // Re-render to update indices
        }
    }

    // Initial Render
    document.addEventListener('DOMContentLoaded', renderQuestions);

    // TODO: Add generateMoreQuestions function if needed, similar to newly creating.
    async function generateMoreQuestions() {
        const topic = document.getElementById('aiTopic').value;
        if (!topic) {
            alert('Por favor, informe um tópico.');
            return;
        }

        // UI Feedback
        const btn = document.getElementById('btnGenerate');
        const loading = document.getElementById('loadingIndicator');
        const originalText = btn.innerHTML;

        btn.disabled = true;
        btn.innerHTML = '<i class="bi bi-hourglass-split me-2"></i>Gerando...';
        loading.classList.remove('d-none');

        // This reuses the proxy_n8n but asks only for questions
        // Simplified Logic for Demo
        // ... (Would implement fetch here similar to novo.php but appending to generatedQuestions)

        alert("Funcionalidade de gerar mais perguntas em desenvolvimento. Adicione o JSON manualmente ou edite em breve.");

        btn.disabled = false;
        btn.innerHTML = originalText;
        loading.classList.add('d-none');
    }

</script>

<?php require_once __DIR__ . '/../../../includes/footer.php'; ?>