<?php
$pageTitle = "Editor 5 Forças de Porter";
require_once __DIR__ . '/../../../../includes/header.php';
checkPermission(['admin', 'cliente']);

$company_id = $_SESSION['company_id'];
$user_id = $_SESSION['user_id'];
$id = $_GET['id'] ?? null;
$item = null;
$conteudo = [
    'concorrentes' => [],
    'fornecedores' => [],
    'clientes' => [],
    'entrantes' => [],
    'substitutos' => []
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
        // Prevent saving in preview mode? Or allow saved to admin session?
        // For simplicity, we just redirect back with a message or save to a dummy user if we wanted.
        // But user said "simular", implies testing the UI. We will mimic save but not actually write to DB.
        echo "<script>alert('Modo Simulação: Dados validados, mas não salvos no banco.'); window.location.href='" . BASE_URL . "admin/execucao-ferramentas';</script>";
        exit;
    }

    $titulo = $_POST['titulo'];
    $newConteudo = [
        'concorrentes' => $_POST['concorrentes'] ?? [],
        'fornecedores' => $_POST['fornecedores'] ?? [],
        'clientes' => $_POST['clientes'] ?? [],
        'entrantes' => $_POST['entrantes'] ?? [],
        'substitutos' => $_POST['substitutos'] ?? []
    ];
    $jsonConteudo = json_encode($newConteudo, JSON_UNESCAPED_UNICODE);

    if ($id) {
        $stmt = $conn->prepare("UPDATE ferramentas_analises SET titulo = ?, conteudo = ? WHERE id = ? AND company_id = ?");
        $stmt->bind_param("ssii", $titulo, $jsonConteudo, $id, $company_id);
    } else {
        $stmt = $conn->prepare("INSERT INTO ferramentas_analises (company_id, user_id, ferramenta, titulo, conteudo) VALUES (?, ?, 'PORTER', ?, ?)");
        $stmt->bind_param("iiss", $company_id, $user_id, $titulo, $jsonConteudo);
    }

    if ($stmt->execute()) {
        header("Location: " . BASE_URL . "admin/execucao-ferramentas/porter");
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
            <?php echo $id ? 'Editar Análise Porter' : 'Nova Análise de Porter'; ?>
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

    <style>
        .porter-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 1.5rem;
            max-width: 1200px;
            margin: 0 auto;
        }

        @media (min-width: 992px) {
            .porter-grid {
                grid-template-columns: 1fr 1.2fr 1fr;
                grid-template-rows: auto auto auto;
                grid-template-areas:
                    ". entrants ."
                    "suppliers competitors buyers"
                    ". substitutes .";
                align-items: center;
            }

            .area-entrants {
                grid-area: entrants;
            }

            .area-suppliers {
                grid-area: suppliers;
            }

            .area-competitors {
                grid-area: competitors;
                z-index: 10;
                transform: scale(1.05);
            }

            .area-buyers {
                grid-area: buyers;
            }

            .area-substitutes {
                grid-area: substitutes;
            }
        }

        .porter-card {
            transition: all 0.3s ease;
            border: none;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }

        .porter-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        }

        .porter-input {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(4px);
        }
    </style>

    <form method="POST" id="toolForm">
        <div class="card shadow-sm border-0 mb-5 bg-transparent">
            <div class="card-body p-0">
                <input type="text" name="titulo"
                    class="form-control form-control-lg border-0 bg-white shadow-sm p-4 fs-3 fw-bold text-center rounded-3"
                    value="<?php echo $item ? htmlspecialchars($item['titulo']) : ''; ?>"
                    placeholder="Dê um título para sua Análise Porter..." required>
            </div>
        </div>

        <div class="porter-grid">

            <!-- TOP: Entrants -->
            <div class="area-entrants">
                <div class="card porter-card border-top border-4 border-warning h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3 text-warning">
                            <div class="bg-warning bg-opacity-10 p-2 rounded me-2">
                                <i class="bi bi-door-open fs-4"></i>
                            </div>
                            <h5 class="fw-bold m-0">Novos Entrantes</h5>
                        </div>
                        <p class="text-muted small mb-3">Ameaça de novos competidores entrarem no mercado.</p>

                        <div id="list-entrantes" class="vstack gap-2">
                            <?php foreach ($conteudo['entrantes'] as $txt): ?>
                                <div class="input-group">
                                    <input type="text" name="entrantes[]"
                                        class="form-control porter-input border-light bg-light"
                                        value="<?php echo htmlspecialchars($txt); ?>">
                                    <button type="button" class="btn btn-light text-danger"
                                        onclick="this.parentElement.remove()"><i class="bi bi-x-lg"></i></button>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <button type="button" class="btn btn-outline-secondary btn-sm w-100 mt-3"
                            onclick="addItem('entrantes', 'warning')">
                            + Adicionar Fator
                        </button>
                    </div>
                </div>
            </div>

            <!-- LEFT: Suppliers -->
            <div class="area-suppliers">
                <div class="card porter-card border-top border-4 border-success h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3 text-success">
                            <div class="bg-success bg-opacity-10 p-2 rounded me-2">
                                <i class="bi bi-box-seam fs-4"></i>
                            </div>
                            <h5 class="fw-bold m-0">Fornecedores</h5>
                        </div>
                        <p class="text-muted small mb-3">Poder de negociação dos fornecedores.</p>

                        <div id="list-fornecedores" class="vstack gap-2">
                            <?php foreach ($conteudo['fornecedores'] as $txt): ?>
                                <div class="input-group">
                                    <input type="text" name="fornecedores[]"
                                        class="form-control porter-input border-light bg-light"
                                        value="<?php echo htmlspecialchars($txt); ?>">
                                    <button type="button" class="btn btn-light text-danger"
                                        onclick="this.parentElement.remove()"><i class="bi bi-x-lg"></i></button>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <button type="button" class="btn btn-outline-secondary btn-sm w-100 mt-3"
                            onclick="addItem('fornecedores', 'success')">
                            + Adicionar Fator
                        </button>
                    </div>
                </div>
            </div>

            <!-- CENTER: Competitors -->
            <div class="area-competitors">
                <div class="card porter-card border-top border-4 border-primary h-100">
                    <div class="card-body p-4 text-center">
                        <div class="d-inline-flex bg-primary bg-opacity-10 p-3 rounded-circle mb-3">
                            <i class="bi bi-people-fill fs-2 text-primary"></i>
                        </div>
                        <h4 class="fw-bold text-primary mb-2">Rivalidade Central</h4>
                        <p class="text-muted small mb-4">Intensidade da disputa entre os concorrentes atuais.</p>

                        <div id="list-concorrentes" class="vstack gap-2 text-start">
                            <?php foreach ($conteudo['concorrentes'] as $txt): ?>
                                <div class="input-group">
                                    <input type="text" name="concorrentes[]"
                                        class="form-control border-light bg-light porter-input"
                                        value="<?php echo htmlspecialchars($txt); ?>">
                                    <button type="button" class="btn btn-light text-danger"
                                        onclick="this.parentElement.remove()"><i class="bi bi-x-lg"></i></button>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <button type="button" class="btn btn-outline-secondary btn-sm w-100 mt-4"
                            onclick="addItem('concorrentes', 'primary-dark')">
                            + Adicionar Concorrente
                        </button>
                    </div>
                </div>
            </div>

            <!-- RIGHT: Buyers -->
            <div class="area-buyers">
                <div class="card porter-card border-top border-4 border-info h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3 text-info">
                            <div class="bg-info bg-opacity-10 p-2 rounded me-2">
                                <i class="bi bi-wallet2 fs-4"></i>
                            </div>
                            <h5 class="fw-bold m-0">Clientes</h5>
                        </div>
                        <p class="text-muted small mb-3">Poder de barganha e exigência dos clientes.</p>

                        <div id="list-clientes" class="vstack gap-2">
                            <?php foreach ($conteudo['clientes'] as $txt): ?>
                                <div class="input-group">
                                    <input type="text" name="clientes[]"
                                        class="form-control porter-input border-light bg-light"
                                        value="<?php echo htmlspecialchars($txt); ?>">
                                    <button type="button" class="btn btn-light text-danger"
                                        onclick="this.parentElement.remove()"><i class="bi bi-x-lg"></i></button>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <button type="button" class="btn btn-outline-secondary btn-sm w-100 mt-3"
                            onclick="addItem('clientes', 'info')">
                            + Adicionar Fator
                        </button>
                    </div>
                </div>
            </div>

            <!-- BOTTOM: Substitutes -->
            <div class="area-substitutes">
                <div class="card porter-card border-top border-4 border-danger h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3 text-danger">
                            <div class="bg-danger bg-opacity-10 p-2 rounded me-2">
                                <i class="bi bi-arrow-repeat fs-4"></i>
                            </div>
                            <h5 class="fw-bold m-0">Substitutos</h5>
                        </div>
                        <p class="text-muted small mb-3">Ameaça de produtos ou serviços substitutos.</p>

                        <div id="list-substitutos" class="vstack gap-2">
                            <?php foreach ($conteudo['substitutos'] as $txt): ?>
                                <div class="input-group">
                                    <input type="text" name="substitutos[]"
                                        class="form-control porter-input border-light bg-light"
                                        value="<?php echo htmlspecialchars($txt); ?>">
                                    <button type="button" class="btn btn-light text-danger"
                                        onclick="this.parentElement.remove()"><i class="bi bi-x-lg"></i></button>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <button type="button" class="btn btn-outline-secondary btn-sm w-100 mt-3"
                            onclick="addItem('substitutos', 'danger')">
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
    function addItem(type, variant) {
        const list = document.getElementById('list-' + type);
        const div = document.createElement('div');
        div.className = 'input-group';

        let inputClass = 'form-control porter-input border-light bg-light';
        let btnClass = 'btn btn-light text-danger';

        // Custom style for the center dark card
        if (variant === 'primary-dark') {
            inputClass = 'form-control border-0 bg-white bg-opacity-10 text-white placeholder-white';
            btnClass = 'btn btn-link text-white text-opacity-75';
        }

        div.innerHTML = `
        <input type="text" name="${type}[]" class="${inputClass}" placeholder="Novo item..." autofocus ${variant === 'primary-dark' ? 'style="backdrop-filter: blur(5px);"' : ''}>
        <button type="button" class="${btnClass}" onclick="this.parentElement.remove()"><i class="bi bi-x-lg"></i></button>
    `;
        list.appendChild(div);
    }
</script>

<?php require_once __DIR__ . '/../../../../includes/footer.php'; ?>