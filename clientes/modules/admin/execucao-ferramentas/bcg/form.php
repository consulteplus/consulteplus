<?php
$pageTitle = "Matriz BCG";
require_once __DIR__ . '/../../../../includes/header.php';
checkPermission(['admin', 'cliente']);

$company_id = $_SESSION['company_id'];
$user_id = $_SESSION['user_id'];
$id = $_GET['id'] ?? null;
$item = null;
$conteudo = [
    'estrela' => [],
    'interrogacao' => [],
    'vacaleiteira' => [],
    'abacaxi' => []
];

// Load if editing
if ($id) {
    $stmt = $conn->prepare("SELECT * FROM ferramentas_analises WHERE id = ? AND company_id = ?");
    $stmt->bind_param("ii", $id, $company_id);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($res->num_rows > 0) {
        $item = $res->fetch_assoc();
        $decoded = json_decode($item['conteudo'], true);
        if ($decoded) {
            $conteudo = array_merge($conteudo, $decoded);
        }
    } else {
        die("Análise não encontrada.");
    }
}

// Handle POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_GET['preview'])) {
        echo "<script>alert('Modo Simulação: Dados validados, mas não salvos no banco.'); window.location.href='" . BASE_URL . "admin/execucao-ferramentas';</script>";
        exit;
    }

    $titulo = $_POST['titulo'];
    $newConteudo = [
        'estrela' => $_POST['estrela'] ?? [],
        'interrogacao' => $_POST['interrogacao'] ?? [],
        'vacaleiteira' => $_POST['vacaleiteira'] ?? [],
        'abacaxi' => $_POST['abacaxi'] ?? []
    ];
    $jsonConteudo = json_encode($newConteudo, JSON_UNESCAPED_UNICODE);

    if ($id) {
        $stmt = $conn->prepare("UPDATE ferramentas_analises SET titulo = ?, conteudo = ? WHERE id = ? AND company_id = ?");
        $stmt->bind_param("ssii", $titulo, $jsonConteudo, $id, $company_id);
    } else {
        $stmt = $conn->prepare("INSERT INTO ferramentas_analises (company_id, user_id, ferramenta, titulo, conteudo) VALUES (?, ?, 'BCG', ?, ?)");
        $stmt->bind_param("iiss", $company_id, $user_id, $titulo, $jsonConteudo);
    }

    if ($stmt->execute()) {
        header("Location: " . BASE_URL . "admin/execucao-ferramentas/bcg");
        exit;
    }
}
?>

<div class="container-xxl flex-grow-1 container-p-y">

    <?php if (isset($_GET['preview'])): ?>
        <div class="alert alert-info border-start border-4 border-info shadow-sm mb-4">
            <div class="d-flex align-items-center">
                <i class="bi bi-info-circle-fill fs-4 me-3"></i>
                <div>
                    <h6 class="fw-bold mb-1">Modo de Simulação</h6>
                    <p class="mb-0 small">Você está testando a ferramenta. O salvamento será simulado.</p>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <?php
    $backUrl = isset($_GET['preview']) ? BASE_URL . 'admin/execucao-ferramentas' : BASE_URL . 'admin/execucao-ferramentas';
    ?>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold m-0">
            <?php echo $id ? 'Editar BCG' : 'Nova Matriz BCG'; ?>
        </h4>
        <div>
            <a href="<?php echo $backUrl; ?>" class="btn btn-outline-secondary me-2">
                <i class="bi bi-arrow-left me-1"></i> Voltar
            </a>
            <button type="submit" form="toolForm" class="btn btn-primary">
                <i class="bi bi-check-lg me-1"></i> Salvar
            </button>
        </div>
    </div>

    <form method="POST" id="toolForm">
        <div class="card shadow-sm border-0 mb-4 bg-transparent">
            <div class="card-body p-0">
                <input type="text" name="titulo"
                    class="form-control form-control-lg border-0 bg-white shadow-sm p-4 fs-3 fw-bold text-center rounded-3"
                    value="<?php echo $item ? htmlspecialchars($item['titulo']) : ''; ?>"
                    placeholder="Título do Portfólio..." required>
                <div class="text-center mt-3 text-muted">
                    <p class="mb-0">Classifique seus produtos baseados em <strong>Participação de Mercado</strong>
                        (Horizontal) e <strong>Crescimento do Mercado</strong> (Vertical)</p>
                </div>
            </div>
        </div>

        <div class="row g-4">

            <!-- Estrela (Alta Participação, Alto Crescimento) -->
            <div class="col-md-6">
                <div class="card h-100 shadow-sm border-0 border-top border-4 border-warning">
                    <div class="card-header bg-white text-center py-3">
                        <i class="bi bi-star-fill fs-1 text-warning mb-2 d-block"></i>
                        <h4 class="card-title fw-bold text-warning m-0">ESTRELA</h4>
                        <small class="text-muted">Alto Crescimento / Alta Participação</small>
                    </div>
                    <div class="card-body bg-light">
                        <p class="small text-center text-muted mb-3">Produtos líderes em mercados quentes. Exigem
                            investimento, mas geram receita.</p>
                        <div id="list-estrela" class="vstack gap-2">
                            <?php foreach ($conteudo['estrela'] as $txt): ?>
                                <div class="input-group">
                                    <input type="text" name="estrela[]" class="form-control border-0 shadow-sm"
                                        value="<?php echo htmlspecialchars($txt); ?>">
                                    <button type="button" class="btn btn-light text-danger shadow-sm"
                                        onclick="this.parentElement.remove()"><i class="bi bi-trash"></i></button>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <button type="button" class="btn btn-outline-secondary btn-sm w-100 mt-3"
                            onclick="addItem('estrela')">
                            <i class="bi bi-plus-lg me-1"></i> Adicionar Produto
                        </button>
                    </div>
                </div>
            </div>

            <!-- Interrogação (Baixa Participação, Alto Crescimento) -->
            <div class="col-md-6">
                <div class="card h-100 shadow-sm border-0 border-top border-4 border-primary">
                    <div class="card-header bg-white text-center py-3">
                        <i class="bi bi-question-circle-fill fs-1 text-primary mb-2 d-block"></i>
                        <h4 class="card-title fw-bold text-primary m-0">EM QUESTIONAMENTO</h4>
                        <small class="text-muted">Alto Crescimento / Baixa Participação</small>
                    </div>
                    <div class="card-body bg-light">
                        <p class="small text-center text-muted mb-3">Produtos novos ou com potencial. Exigem muito
                            investimento, retorno incerto.</p>
                        <div id="list-interrogacao" class="vstack gap-2">
                            <?php foreach ($conteudo['interrogacao'] as $txt): ?>
                                <div class="input-group">
                                    <input type="text" name="interrogacao[]" class="form-control border-0 shadow-sm"
                                        value="<?php echo htmlspecialchars($txt); ?>">
                                    <button type="button" class="btn btn-light text-danger shadow-sm"
                                        onclick="this.parentElement.remove()"><i class="bi bi-trash"></i></button>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <button type="button" class="btn btn-outline-secondary btn-sm w-100 mt-3"
                            onclick="addItem('interrogacao')">
                            <i class="bi bi-plus-lg me-1"></i> Adicionar Produto
                        </button>
                    </div>
                </div>
            </div>

            <!-- Vaca Leiteira (Alta Participação, Baixo Crescimento) -->
            <div class="col-md-6">
                <div class="card h-100 shadow-sm border-0 border-top border-4 border-success">
                    <div class="card-header bg-white text-center py-3">
                        <i class="bi bi-cash-coin fs-1 text-success mb-2 d-block"></i>
                        <h4 class="card-title fw-bold text-success m-0">VACA LEITEIRA</h4>
                        <small class="text-muted">Baixo Crescimento / Alta Participação</small>
                    </div>
                    <div class="card-body bg-light">
                        <p class="small text-center text-muted mb-3">Produtos maduros. Geram muito caixa com pouco
                            investimento.</p>
                        <div id="list-vacaleiteira" class="vstack gap-2">
                            <?php foreach ($conteudo['vacaleiteira'] as $txt): ?>
                                <div class="input-group">
                                    <input type="text" name="vacaleiteira[]" class="form-control border-0 shadow-sm"
                                        value="<?php echo htmlspecialchars($txt); ?>">
                                    <button type="button" class="btn btn-light text-danger shadow-sm"
                                        onclick="this.parentElement.remove()"><i class="bi bi-trash"></i></button>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <button type="button" class="btn btn-outline-secondary btn-sm w-100 mt-3"
                            onclick="addItem('vacaleiteira')">
                            <i class="bi bi-plus-lg me-1"></i> Adicionar Produto
                        </button>
                    </div>
                </div>
            </div>

            <!-- Abacaxi (Baixa Participação, Baixo Crescimento) -->
            <div class="col-md-6">
                <div class="card h-100 shadow-sm border-0 border-top border-4 border-danger">
                    <div class="card-header bg-white text-center py-3">
                        <i class="bi bi-emoji-frown-fill fs-1 text-danger mb-2 d-block"></i>
                        <h4 class="card-title fw-bold text-danger m-0">ABACAXI / CÃO</h4>
                        <small class="text-muted">Baixo Crescimento / Baixa Participação</small>
                    </div>
                    <div class="card-body bg-light">
                        <p class="small text-center text-muted mb-3">Produtos com baixo potencial. Geralmente devem ser
                            descontinuados.</p>
                        <div id="list-abacaxi" class="vstack gap-2">
                            <?php foreach ($conteudo['abacaxi'] as $txt): ?>
                                <div class="input-group">
                                    <input type="text" name="abacaxi[]" class="form-control border-0 shadow-sm"
                                        value="<?php echo htmlspecialchars($txt); ?>">
                                    <button type="button" class="btn btn-light text-danger shadow-sm"
                                        onclick="this.parentElement.remove()"><i class="bi bi-trash"></i></button>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <button type="button" class="btn btn-outline-secondary btn-sm w-100 mt-3"
                            onclick="addItem('abacaxi')">
                            <i class="bi bi-plus-lg me-1"></i> Adicionar Produto
                        </button>
                    </div>
                </div>
            </div>

        </div>

</div>
</form>
</div>

<script>
    function addItem(type) {
        const list = document.getElementById('list-' + type);
        const div = document.createElement('div');
        div.className = 'input-group';
        div.innerHTML = `
        <input type="text" name="${type}[]" class="form-control border-0 shadow-sm" placeholder="Novo produto..." autofocus>
        <button type="button" class="btn btn-light text-danger shadow-sm" onclick="this.parentElement.remove()"><i class="bi bi-trash"></i></button>
    `;
        list.appendChild(div);
    }
</script>

<?php require_once __DIR__ . '/../../../../includes/footer.php'; ?>