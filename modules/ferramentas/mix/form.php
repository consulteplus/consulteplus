<?php
$pageTitle = "Mix de Marketing (4 Ps)";
require_once __DIR__ . '/../../../includes/header.php';
checkPermission(['admin', 'cliente']);

$company_id = $_SESSION['company_id'];
$user_id = $_SESSION['user_id'];
$id = $_GET['id'] ?? null;
$item = null;
$conteudo = [
    'produto' => [],
    'preco' => [],
    'praca' => [],
    'promocao' => []
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
        echo "<script>alert('Modo Simulação: Dados validados, mas não salvos no banco.'); window.location.href='" . BASE_URL . "admin/ferramentas';</script>";
        exit;
    }

    $titulo = $_POST['titulo'];
    $newConteudo = [
        'produto' => $_POST['produto'] ?? [],
        'preco' => $_POST['preco'] ?? [],
        'praca' => $_POST['praca'] ?? [],
        'promocao' => $_POST['promocao'] ?? []
    ];
    $jsonConteudo = json_encode($newConteudo, JSON_UNESCAPED_UNICODE);

    if ($id) {
        $stmt = $conn->prepare("UPDATE ferramentas_analises SET titulo = ?, conteudo = ? WHERE id = ? AND company_id = ?");
        $stmt->bind_param("ssii", $titulo, $jsonConteudo, $id, $company_id);
    } else {
        $stmt = $conn->prepare("INSERT INTO ferramentas_analises (company_id, user_id, ferramenta, titulo, conteudo) VALUES (?, ?, 'MIX', ?, ?)");
        $stmt->bind_param("iiss", $company_id, $user_id, $titulo, $jsonConteudo);
    }

    if ($stmt->execute()) {
        header("Location: " . BASE_URL . "ferramentas/mix");
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
    $backUrl = isset($_GET['preview']) ? BASE_URL . 'admin/ferramentas' : BASE_URL . 'ferramentas';
    ?>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold m-0">
            <?php echo $id ? 'Editar 4 Ps' : 'Novo Mix de Marketing'; ?>
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
                    placeholder="Título da Estratégia..." required>
            </div>
        </div>

        <div class="row g-4">
            <!-- Produto -->
            <div class="col-md-6 col-xl-3">
                <div class="card h-100 shadow-sm border-0 border-top border-4 border-primary">
                    <div class="card-header bg-white text-center py-4">
                        <div class="avatar avatar-lg bg-primary bg-opacity-10 rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center"
                            style="width: 60px; height: 60px;">
                            <i class="bi bi-box-seam text-primary fs-2"></i>
                        </div>
                        <h4 class="card-title fw-bold text-primary m-0">Produto</h4>
                        <small class="text-muted">Características, design, qualidade, variedade.</small>
                    </div>
                    <div class="card-body bg-light">
                        <div id="list-produto" class="vstack gap-2">
                            <?php foreach ($conteudo['produto'] as $txt): ?>
                                <div class="input-group">
                                    <input type="text" name="produto[]" class="form-control border-0 shadow-sm"
                                        value="<?php echo htmlspecialchars($txt); ?>">
                                    <button type="button" class="btn btn-light text-danger shadow-sm"
                                        onclick="this.parentElement.remove()"><i class="bi bi-trash"></i></button>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <button type="button" class="btn btn-outline-secondary btn-sm w-100 mt-3"
                            onclick="addItem('produto')">
                            + Adicionar
                        </button>
                    </div>
                </div>
            </div>

            <!-- Preço -->
            <div class="col-md-6 col-xl-3">
                <div class="card h-100 shadow-sm border-0 border-top border-4 border-success">
                    <div class="card-header bg-white text-center py-4">
                        <div class="avatar avatar-lg bg-success bg-opacity-10 rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center"
                            style="width: 60px; height: 60px;">
                            <i class="bi bi-tag text-success fs-2"></i>
                        </div>
                        <h4 class="card-title fw-bold text-success m-0">Preço</h4>
                        <small class="text-muted">Lista, descontos, prazos, formas de pagamento.</small>
                    </div>
                    <div class="card-body bg-light">
                        <div id="list-preco" class="vstack gap-2">
                            <?php foreach ($conteudo['preco'] as $txt): ?>
                                <div class="input-group">
                                    <input type="text" name="preco[]" class="form-control border-0 shadow-sm"
                                        value="<?php echo htmlspecialchars($txt); ?>">
                                    <button type="button" class="btn btn-light text-danger shadow-sm"
                                        onclick="this.parentElement.remove()"><i class="bi bi-trash"></i></button>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <button type="button" class="btn btn-outline-secondary btn-sm w-100 mt-3"
                            onclick="addItem('preco')">
                            + Adicionar
                        </button>
                    </div>
                </div>
            </div>

            <!-- Praça -->
            <div class="col-md-6 col-xl-3">
                <div class="card h-100 shadow-sm border-0 border-top border-4 border-warning">
                    <div class="card-header bg-white text-center py-4">
                        <div class="avatar avatar-lg bg-warning bg-opacity-10 rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center"
                            style="width: 60px; height: 60px;">
                            <i class="bi bi-truck text-warning fs-2"></i>
                        </div>
                        <h4 class="card-title fw-bold text-warning m-0">Praça</h4>
                        <small class="text-muted">Canais, cobertura, estoque, logística.</small>
                    </div>
                    <div class="card-body bg-light">
                        <div id="list-praca" class="vstack gap-2">
                            <?php foreach ($conteudo['praca'] as $txt): ?>
                                <div class="input-group">
                                    <input type="text" name="praca[]" class="form-control border-0 shadow-sm"
                                        value="<?php echo htmlspecialchars($txt); ?>">
                                    <button type="button" class="btn btn-light text-danger shadow-sm"
                                        onclick="this.parentElement.remove()"><i class="bi bi-trash"></i></button>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <button type="button" class="btn btn-outline-secondary btn-sm w-100 mt-3"
                            onclick="addItem('praca')">
                            + Adicionar
                        </button>
                    </div>
                </div>
            </div>

            <!-- Promoção -->
            <div class="col-md-6 col-xl-3">
                <div class="card h-100 shadow-sm border-0 border-top border-4 border-danger">
                    <div class="card-header bg-white text-center py-4">
                        <div class="avatar avatar-lg bg-danger bg-opacity-10 rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center"
                            style="width: 60px; height: 60px;">
                            <i class="bi bi-megaphone text-danger fs-2"></i>
                        </div>
                        <h4 class="card-title fw-bold text-danger m-0">Promoção</h4>
                        <small class="text-muted">Publicidade, vendas, branding, RP.</small>
                    </div>
                    <div class="card-body bg-light">
                        <div id="list-promocao" class="vstack gap-2">
                            <?php foreach ($conteudo['promocao'] as $txt): ?>
                                <div class="input-group">
                                    <input type="text" name="promocao[]" class="form-control border-0 shadow-sm"
                                        value="<?php echo htmlspecialchars($txt); ?>">
                                    <button type="button" class="btn btn-light text-danger shadow-sm"
                                        onclick="this.parentElement.remove()"><i class="bi bi-trash"></i></button>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <button type="button" class="btn btn-outline-secondary btn-sm w-100 mt-3"
                            onclick="addItem('promocao')">
                            + Adicionar
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
        <input type="text" name="${type}[]" class="form-control border-0 shadow-sm" placeholder="Novo item..." autofocus>
        <button type="button" class="btn btn-light text-danger shadow-sm" onclick="this.parentElement.remove()"><i class="bi bi-trash"></i></button>
    `;
        list.appendChild(div);
    }
</script>

<?php require_once __DIR__ . '/../../../includes/footer.php'; ?>