<?php
$pageTitle = "Análise PESTEL";
require_once __DIR__ . '/../../../includes/header.php';
checkPermission(['admin', 'cliente']);

$company_id = $_SESSION['company_id'];
$user_id = $_SESSION['user_id'];
$id = $_GET['id'] ?? null;
$item = null;
$conteudo = [
    'politico' => [],
    'economico' => [],
    'social' => [],
    'tecnologico' => [],
    'ambiental' => [],
    'legal' => []
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
        'politico' => $_POST['politico'] ?? [],
        'economico' => $_POST['economico'] ?? [],
        'social' => $_POST['social'] ?? [],
        'tecnologico' => $_POST['tecnologico'] ?? [],
        'ambiental' => $_POST['ambiental'] ?? [],
        'legal' => $_POST['legal'] ?? []
    ];
    $jsonConteudo = json_encode($newConteudo, JSON_UNESCAPED_UNICODE);

    if ($id) {
        $stmt = $conn->prepare("UPDATE ferramentas_analises SET titulo = ?, conteudo = ? WHERE id = ? AND company_id = ?");
        $stmt->bind_param("ssii", $titulo, $jsonConteudo, $id, $company_id);
    } else {
        $stmt = $conn->prepare("INSERT INTO ferramentas_analises (company_id, user_id, ferramenta, titulo, conteudo) VALUES (?, ?, 'PESTEL', ?, ?)");
        $stmt->bind_param("iiss", $company_id, $user_id, $titulo, $jsonConteudo);
    }

    if ($stmt->execute()) {
        header("Location: " . BASE_URL . "ferramentas/pestel");
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
            <?php echo $id ? 'Editar PESTEL' : 'Nova Análise PESTEL'; ?>
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
                    placeholder="Título da Análise..." required>
            </div>
        </div>

        <div class="row g-4">
            <!-- Político -->
            <div class="col-md-6 col-xl-4">
                <div class="card h-100 shadow-sm border-0 border-top border-4 border-danger">
                    <div class="card-header bg-white">
                        <h5 class="card-title m-0 text-danger"><i class="bi bi-bank me-2"></i>Político</h5>
                        <small class="text-muted">Políticas governamentais, estabilidade, impostos.</small>
                    </div>
                    <div class="card-body bg-light">
                        <div id="list-politico" class="vstack gap-2">
                            <?php foreach ($conteudo['politico'] as $txt): ?>
                                <div class="input-group">
                                    <input type="text" name="politico[]" class="form-control border-0 shadow-sm"
                                        value="<?php echo htmlspecialchars($txt); ?>">
                                    <button type="button" class="btn btn-light text-danger shadow-sm"
                                        onclick="this.parentElement.remove()"><i class="bi bi-trash"></i></button>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <button type="button" class="btn btn-outline-secondary btn-sm w-100 mt-3"
                            onclick="addItem('politico')">
                            + Adicionar Fator
                        </button>
                    </div>
                </div>
            </div>

            <!-- Econômico -->
            <div class="col-md-6 col-xl-4">
                <div class="card h-100 shadow-sm border-0 border-top border-4 border-primary">
                    <div class="card-header bg-white">
                        <h5 class="card-title m-0 text-primary"><i class="bi bi-graph-up me-2"></i>Econômico</h5>
                        <small class="text-muted">Inflação, juros, crescimento, câmbio.</small>
                    </div>
                    <div class="card-body bg-light">
                        <div id="list-economico" class="vstack gap-2">
                            <?php foreach ($conteudo['economico'] as $txt): ?>
                                <div class="input-group">
                                    <input type="text" name="economico[]" class="form-control border-0 shadow-sm"
                                        value="<?php echo htmlspecialchars($txt); ?>">
                                    <button type="button" class="btn btn-light text-danger shadow-sm"
                                        onclick="this.parentElement.remove()"><i class="bi bi-trash"></i></button>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <button type="button" class="btn btn-outline-secondary btn-sm w-100 mt-3"
                            onclick="addItem('economico')">
                            + Adicionar Fator
                        </button>
                    </div>
                </div>
            </div>

            <!-- Social -->
            <div class="col-md-6 col-xl-4">
                <div class="card h-100 shadow-sm border-0 border-top border-4 border-warning">
                    <div class="card-header bg-white">
                        <h5 class="card-title m-0 text-warning"><i class="bi bi-people me-2"></i>Social</h5>
                        <small class="text-muted">Demografia, cultura, hábitos de consumo.</small>
                    </div>
                    <div class="card-body bg-light">
                        <div id="list-social" class="vstack gap-2">
                            <?php foreach ($conteudo['social'] as $txt): ?>
                                <div class="input-group">
                                    <input type="text" name="social[]" class="form-control border-0 shadow-sm"
                                        value="<?php echo htmlspecialchars($txt); ?>">
                                    <button type="button" class="btn btn-light text-danger shadow-sm"
                                        onclick="this.parentElement.remove()"><i class="bi bi-trash"></i></button>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <button type="button" class="btn btn-outline-secondary btn-sm w-100 mt-3"
                            onclick="addItem('social')">
                            + Adicionar Fator
                        </button>
                    </div>
                </div>
            </div>

            <!-- Tecnológico -->
            <div class="col-md-6 col-xl-4">
                <div class="card h-100 shadow-sm border-0 border-top border-4 border-info">
                    <div class="card-header bg-white">
                        <h5 class="card-title m-0 text-info"><i class="bi bi-cpu me-2"></i>Tecnológico</h5>
                        <small class="text-muted">Inovação, automação, R&D, novas plataformas.</small>
                    </div>
                    <div class="card-body bg-light">
                        <div id="list-tecnologico" class="vstack gap-2">
                            <?php foreach ($conteudo['tecnologico'] as $txt): ?>
                                <div class="input-group">
                                    <input type="text" name="tecnologico[]" class="form-control border-0 shadow-sm"
                                        value="<?php echo htmlspecialchars($txt); ?>">
                                    <button type="button" class="btn btn-light text-danger shadow-sm"
                                        onclick="this.parentElement.remove()"><i class="bi bi-trash"></i></button>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <button type="button" class="btn btn-outline-secondary btn-sm w-100 mt-3"
                            onclick="addItem('tecnologico')">
                            + Adicionar Fator
                        </button>
                    </div>
                </div>
            </div>

            <!-- Ambiental -->
            <div class="col-md-6 col-xl-4">
                <div class="card h-100 shadow-sm border-0 border-top border-4 border-success">
                    <div class="card-header bg-white">
                        <h5 class="card-title m-0 text-success"><i class="bi bi-tree me-2"></i>Ambiental (Environmental)
                        </h5>
                        <small class="text-muted">Sustentabilidade, clima, gestão de resíduos.</small>
                    </div>
                    <div class="card-body bg-light">
                        <div id="list-ambiental" class="vstack gap-2">
                            <?php foreach ($conteudo['ambiental'] as $txt): ?>
                                <div class="input-group">
                                    <input type="text" name="ambiental[]" class="form-control border-0 shadow-sm"
                                        value="<?php echo htmlspecialchars($txt); ?>">
                                    <button type="button" class="btn btn-light text-danger shadow-sm"
                                        onclick="this.parentElement.remove()"><i class="bi bi-trash"></i></button>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <button type="button" class="btn btn-outline-secondary btn-sm w-100 mt-3"
                            onclick="addItem('ambiental')">
                            + Adicionar Fator
                        </button>
                    </div>
                </div>
            </div>

            <!-- Legal -->
            <div class="col-md-6 col-xl-4">
                <div class="card h-100 shadow-sm border-0 border-top border-4 border-dark">
                    <div class="card-header bg-white">
                        <h5 class="card-title m-0 text-dark"><i class="bi bi-gavel me-2"></i>Legal</h5>
                        <small class="text-muted">Leis trabalhistas, consumidor, segurança, saúde.</small>
                    </div>
                    <div class="card-body bg-light">
                        <div id="list-legal" class="vstack gap-2">
                            <?php foreach ($conteudo['legal'] as $txt): ?>
                                <div class="input-group">
                                    <input type="text" name="legal[]" class="form-control border-0 shadow-sm"
                                        value="<?php echo htmlspecialchars($txt); ?>">
                                    <button type="button" class="btn btn-light text-danger shadow-sm"
                                        onclick="this.parentElement.remove()"><i class="bi bi-trash"></i></button>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <button type="button" class="btn btn-outline-secondary btn-sm w-100 mt-3"
                            onclick="addItem('legal')">
                            + Adicionar Fator
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
        <input type="text" name="${type}[]" class="form-control border-0 shadow-sm" placeholder="Novo fator..." autofocus>
        <button type="button" class="btn btn-light text-danger shadow-sm" onclick="this.parentElement.remove()"><i class="bi bi-trash"></i></button>
    `;
        list.appendChild(div);
    }
</script>

<?php require_once __DIR__ . '/../../../includes/footer.php'; ?>