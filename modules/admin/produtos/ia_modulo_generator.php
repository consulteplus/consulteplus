<?php
$pageTitle = "Gerador de Módulos com IA";
require_once __DIR__ . '/../header.php';
checkPermission(['admin']);

$produto_id = isset($_GET['produto_id']) ? intval($_GET['produto_id']) : 0;
$company_id = $_SESSION['company_id'];

// Check permission
// Product Service
require_once __DIR__ . '/../../../classes/ProductService.php';
$productService = new ProductService();

$produto = $productService->buscar($produto_id, $company_id);

if (!$produto) {
    die("Produto não encontrado.");
}
?>

<div class="container-fluid py-4">
    <div class="d-flex align-items-center mb-4">
        <a href="<?php echo BASE_URL; ?>admin/produtos/trilhas/<?php echo $produto_id; ?>"
            class="btn btn-outline-secondary me-3">
            <i class="bi bi-arrow-left"></i> Voltar
        </a>
        <div>
            <h1 class="h3 mb-0 text-gray-800">Criar Módulo com IA</h1>
            <p class="text-muted small mb-0">Produto: <strong>
                    <?php echo htmlspecialchars($produto['titulo']); ?>
                </strong></p>
        </div>
    </div>

    <!-- Intro / Start -->
    <div id="step-intro" class="card shadow-sm border-0 text-center py-5">
        <div class="card-body">
            <div class="mb-4">
                <i class="bi bi-stars text-warning display-1"></i>
            </div>
            <h3 class="mb-3">Vamos expandir seu produto?</h3>
            <p class="text-muted mb-4" style="max-width: 600px; margin: 0 auto;">
                Nossa IA vai analisar a estrutura atual do seu curso, identificar lacunas e sugerir
                3 novos módulos incríveis para seus alunos.
            </p>
            <button class="btn btn-primary btn-lg px-5 shadow" onclick="fetchSuggestions()">
                <i class="bi bi-magic me-2"></i> Analisar e Sugerir Módulos
            </button>
        </div>
    </div>

    <!-- Loading -->
    <div id="step-loading" class="text-center py-5" style="display: none;">
        <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;" role="status"></div>
        <h4 class="mt-4 fw-normal text-muted blink-text">Analisando estrutura do curso...</h4>
        <p class="small text-muted">Aguarde, nossa IA está pensando.</p>
    </div>

    <!-- Suggestions List -->
    <div id="step-results" style="display: none;">
        <h4 class="mb-4">Sugestões Encontradas:</h4>
        <div class="row g-4" id="suggestions-container">
            <!-- Cards will be injected here -->
        </div>

        <div class="text-center mt-5">
            <button class="btn btn-outline-secondary" onclick="fetchSuggestions()">
                <i class="bi bi-arrow-clockwise me-2"></i> Tentar Novamente / Outras Ideias
            </button>
        </div>
    </div>

    <!-- Generating Module Modal -->
    <div class="modal fade" id="modalGenerating" data-bs-backdrop="static" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content text-center py-4">
                <div class="modal-body">
                    <div class="spinner-border text-success mb-3" role="status"></div>
                    <h5>Criando o Módulo...</h5>
                    <p class="text-muted mb-0">Escrevendo aulas, descrição e tarefas.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    const produtoId = <?php echo $produto_id; ?>;

    async function fetchSuggestions() {
        document.getElementById('step-intro').style.display = 'none';
        document.getElementById('step-results').style.display = 'none';
        document.getElementById('step-loading').style.display = 'block';

        try {
            const response = await fetch('<?php echo BASE_URL; ?>api/n8n/sugerir_modulos.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ produto_id: produtoId })
            });

            const data = await response.json();

            if (!data.success) throw new Error(data.message || 'Erro desconhecido');

            renderSuggestions(data.suggestions);

        } catch (error) {
            console.error(error);
            alert('Erro ao buscar sugestões: ' + error.message);
            document.getElementById('step-intro').style.display = 'block';
            document.getElementById('step-loading').style.display = 'none';
        }
    }

    function renderSuggestions(list) {
        const container = document.getElementById('suggestions-container');
        container.innerHTML = '';

        list.forEach(item => {
            const card = `
                <div class="col-md-4">
                    <div class="card h-100 border-0 shadow-sm suggestion-card highlight-hover">
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title fw-bold text-primary mb-3">${item.title}</h5>
                            <p class="card-text text-muted flex-grow-1">${item.reason}</p>
                            <div class="d-grid mt-3">
                                <button class="btn btn-outline-primary" onclick="generateModule('${item.title}')">
                                    <i class="bi bi-plus-lg me-2"></i> Adicionar este Módulo
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            container.innerHTML += card;
        });

        document.getElementById('step-loading').style.display = 'none';
        document.getElementById('step-results').style.display = 'block';
    }

    async function generateModule(title) {
        if (!confirm(`Gerar o módulo "${title}" e todas as suas aulas agora?`)) return;

        var myModal = new bootstrap.Modal(document.getElementById('modalGenerating'));
        myModal.show();

        try {
            const response = await fetch('<?php echo BASE_URL; ?>api/n8n/gerar_modulo.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    produto_id: produtoId,
                    tema: title,
                    qtd_aulas: 5, // Default
                    contexto: 'Sugestão automática da IA para complementar o curso.'
                })
            });

            const data = await response.json();

            if (!data.success) throw new Error(data.message || 'Erro na geração');

            // Redirect back to tracks
            window.location.href = '<?php echo BASE_URL; ?>admin/produtos/trilhas/' + produtoId;

        } catch (error) {
            console.error(error);
            myModal.hide();
            alert('Erro ao gerar módulo: ' + error.message);
        }
    }
</script>

<style>
    .highlight-hover:hover {
        transform: translateY(-5px);
        transition: transform 0.2s ease;
        border: 1px solid #0d6efd !important;
    }

    .blink-text {
        animation: blinker 1.5s linear infinite;
    }

    @keyframes blinker {
        50% {
            opacity: 0.5;
        }
    }
</style>

<?php require_once __DIR__ . '/../../../includes/footer.php'; ?>