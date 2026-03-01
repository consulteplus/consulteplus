<?php
$pageTitle = "Editor SWOT";
require_once __DIR__ . '/../../../includes/header.php';
checkPermission(['admin', 'cliente']);

$company_id = $_SESSION['company_id'];
$user_id = $_SESSION['user_id'];
$id = $_GET['id'] ?? null;
$item = null;
$conteudo = [
    'forcas' => [],
    'fraquezas' => [],
    'oportunidades' => [],
    'ameacas' => []
];

// Load existing data if editing
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
        die("Análise não encontrada ou sem permissão.");
    }
}

// Handle POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo = $_POST['titulo'];
    $newConteudo = [
        'forcas' => $_POST['forcas'] ?? [],
        'fraquezas' => $_POST['fraquezas'] ?? [],
        'oportunidades' => $_POST['oportunidades'] ?? [],
        'ameacas' => $_POST['ameacas'] ?? []
    ];
    $jsonConteudo = json_encode($newConteudo, JSON_UNESCAPED_UNICODE);

    if ($id) {
        // Update
        $stmt = $conn->prepare("UPDATE ferramentas_analises SET titulo = ?, conteudo = ? WHERE id = ? AND company_id = ?");
        $stmt->bind_param("ssii", $titulo, $jsonConteudo, $id, $company_id);
    } else {
        // Create
        $stmt = $conn->prepare("INSERT INTO ferramentas_analises (company_id, user_id, ferramenta, titulo, conteudo) VALUES (?, ?, 'SWOT', ?, ?)");
        $stmt->bind_param("iiss", $company_id, $user_id, $titulo, $jsonConteudo);
    }

    if ($stmt->execute()) {
        header("Location: " . BASE_URL . "ferramentas/swot");
        exit;
    } else {
        $error = "Erro ao salvar: " . $conn->error;
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
                    <p class="mb-0 small">Você está visualizando esta ferramenta como Administrador para testar a
                        experiência do usuário.</p>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <?php
    $backUrl = isset($_GET['preview']) ? BASE_URL . 'admin/ferramentas' : BASE_URL . 'ferramentas';
    ?>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold m-0">
            <?php echo $id ? 'Editar Análise SWOT' : 'Nova Análise SWOT'; ?>
        </h4>
        <div>
            <a href="<?php echo $backUrl; ?>" class="btn btn-outline-secondary me-2">
                <i class="bi bi-arrow-left me-1"></i> Voltar
            </a>
            <button type="submit" form="swotForm" class="btn btn-primary">
                <i class="bi bi-check-lg me-1"></i> Salvar
            </button>
        </div>
    </div>

    <form method="POST" id="swotForm">
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body">
                <label class="form-label">Título da Análise</label>
                <input type="text" name="titulo" class="form-control form-control-lg"
                    value="<?php echo $item ? htmlspecialchars($item['titulo']) : ''; ?>"
                    placeholder="Ex: Planejamento Estratégico 2026" required>
            </div>
        </div>

        <div class="row g-4">
            <!-- Forças -->
            <div class="col-md-6">
                <div class="card h-100 shadow-sm border-0 border-top border-4 border-success">
                    <div class="card-header bg-white">
                        <h5 class="card-title m-0 text-success"><i class="bi bi-arrow-up-circle me-2"></i>Forças</h5>
                        <small class="text-muted">Fatores internos positivos</small>
                    </div>
                    <div class="card-body bg-light">
                        <div id="list-forcas" class="vstack gap-2">
                            <?php foreach ($conteudo['forcas'] as $txt): ?>
                                <div class="input-group">
                                    <input type="text" name="forcas[]" class="form-control border-0 shadow-sm"
                                        value="<?php echo htmlspecialchars($txt); ?>">
                                    <button type="button" class="btn btn-light text-danger shadow-sm"
                                        onclick="this.parentElement.remove()"><i class="bi bi-trash"></i></button>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <button type="button" class="btn btn-outline-secondary btn-sm mt-3 w-100"
                            onclick="addItem('forcas')">
                            <i class="bi bi-plus-lg me-1"></i> Adicionar Força
                        </button>
                    </div>
                </div>
            </div>

            <!-- Fraquezas -->
            <div class="col-md-6">
                <div class="card h-100 shadow-sm border-0 border-top border-4 border-danger">
                    <div class="card-header bg-white">
                        <h5 class="card-title m-0 text-danger"><i class="bi bi-arrow-down-circle me-2"></i>Fraquezas
                        </h5>
                        <small class="text-muted">Fatores internos negativos</small>
                    </div>
                    <div class="card-body bg-light">
                        <div id="list-fraquezas" class="vstack gap-2">
                            <?php foreach ($conteudo['fraquezas'] as $txt): ?>
                                <div class="input-group">
                                    <input type="text" name="fraquezas[]" class="form-control border-0 shadow-sm"
                                        value="<?php echo htmlspecialchars($txt); ?>">
                                    <button type="button" class="btn btn-light text-danger shadow-sm"
                                        onclick="this.parentElement.remove()"><i class="bi bi-trash"></i></button>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <button type="button" class="btn btn-outline-secondary btn-sm mt-3 w-100"
                            onclick="addItem('fraquezas')">
                            <i class="bi bi-plus-lg me-1"></i> Adicionar Fraqueza
                        </button>
                    </div>
                </div>
            </div>

            <!-- Oportunidades -->
            <div class="col-md-6">
                <div class="card h-100 shadow-sm border-0 border-top border-4 border-primary">
                    <div class="card-header bg-white">
                        <h5 class="card-title m-0 text-primary"><i class="bi bi-lightbulb me-2"></i>Oportunidades</h5>
                        <small class="text-muted">Fatores externos positivos</small>
                    </div>
                    <div class="card-body bg-light">
                        <div id="list-oportunidades" class="vstack gap-2">
                            <?php foreach ($conteudo['oportunidades'] as $txt): ?>
                                <div class="input-group">
                                    <input type="text" name="oportunidades[]" class="form-control border-0 shadow-sm"
                                        value="<?php echo htmlspecialchars($txt); ?>">
                                    <button type="button" class="btn btn-light text-danger shadow-sm"
                                        onclick="this.parentElement.remove()"><i class="bi bi-trash"></i></button>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <button type="button" class="btn btn-outline-secondary btn-sm mt-3 w-100"
                            onclick="addItem('oportunidades')">
                            <i class="bi bi-plus-lg me-1"></i> Adicionar Oportunidade
                        </button>
                    </div>
                </div>
            </div>

            <!-- Ameaças -->
            <div class="col-md-6">
                <div class="card h-100 shadow-sm border-0 border-top border-4 border-warning">
                    <div class="card-header bg-white">
                        <h5 class="card-title m-0 text-warning"><i class="bi bi-exclamation-triangle me-2"></i>Ameaças
                        </h5>
                        <small class="text-muted">Fatores externos negativos</small>
                    </div>
                    <div class="card-body bg-light">
                        <div id="list-ameacas" class="vstack gap-2">
                            <?php foreach ($conteudo['ameacas'] as $txt): ?>
                                <div class="input-group">
                                    <input type="text" name="ameacas[]" class="form-control border-0 shadow-sm"
                                        value="<?php echo htmlspecialchars($txt); ?>">
                                    <button type="button" class="btn btn-light text-danger shadow-sm"
                                        onclick="this.parentElement.remove()"><i class="bi bi-trash"></i></button>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <button type="button" class="btn btn-outline-secondary btn-sm mt-3 w-100"
                            onclick="addItem('ameacas')">
                            <i class="bi bi-plus-lg me-1"></i> Adicionar Ameaça
                        </button>
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