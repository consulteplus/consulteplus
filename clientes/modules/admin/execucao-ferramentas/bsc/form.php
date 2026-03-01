<?php
$pageTitle = "Editor BSC";
require_once __DIR__ . '/../../../../includes/header.php';
checkPermission(['admin', 'cliente']);

$company_id = $_SESSION['company_id'];
$user_id = $_SESSION['user_id'];
$id = $_GET['id'] ?? null;
$item = null;
$conteudo = [
    'financeira' => [],
    'clientes' => [],
    'processos' => [],
    'aprendizado' => []
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
        'financeira' => $_POST['financeira'] ?? [],
        'clientes' => $_POST['clientes'] ?? [],
        'processos' => $_POST['processos'] ?? [],
        'aprendizado' => $_POST['aprendizado'] ?? []
    ];
    $jsonConteudo = json_encode($newConteudo, JSON_UNESCAPED_UNICODE);

    if ($id) {
        $stmt = $conn->prepare("UPDATE ferramentas_analises SET titulo = ?, conteudo = ? WHERE id = ? AND company_id = ?");
        $stmt->bind_param("ssii", $titulo, $jsonConteudo, $id, $company_id);
    } else {
        $stmt = $conn->prepare("INSERT INTO ferramentas_analises (company_id, user_id, ferramenta, titulo, conteudo) VALUES (?, ?, 'BSC', ?, ?)");
        $stmt->bind_param("iiss", $company_id, $user_id, $titulo, $jsonConteudo);
    }

    if ($stmt->execute()) {
        header("Location: " . BASE_URL . "admin/execucao-ferramentas/bsc");
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
            <?php echo $id ? 'Editar BSC' : 'Novo Balanced Scorecard'; ?>
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
        .bsc-container {
            max-width: 900px;
            margin: 0 auto;
            position: relative;
        }

        /* Connecting Line Vertical */
        .bsc-container::before {
            content: '';
            position: absolute;
            top: 50px;
            bottom: 50px;
            left: 50%;
            width: 4px;
            background: linear-gradient(to bottom, #198754 0%, #0dcaf0 33%, #0d6efd 66%, #ffc107 100%);
            transform: translateX(-50%);
            z-index: 0;
            opacity: 0.3;
        }

        .bsc-layer {
            position: relative;
            z-index: 1;
            margin-bottom: 3rem;
        }

        .bsc-card {
            background: white;
            border-radius: 1rem;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.05);
            transition: transform 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .bsc-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
        }

        .bsc-card::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 6px;
            height: 100%;
        }

        .bsc-financial::after {
            background: #198754;
        }

        .bsc-customer::after {
            background: #0dcaf0;
        }

        .bsc-internal::after {
            background: #0d6efd;
        }

        .bsc-learning::after {
            background: #ffc107;
        }

        .bsc-icon {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            margin-bottom: 1rem;
            color: white;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }

        .icon-financial {
            background: linear-gradient(135deg, #198754, #20c997);
        }

        .icon-customer {
            background: linear-gradient(135deg, #0dcaf0, #3dd5f3);
        }

        .icon-internal {
            background: linear-gradient(135deg, #0d6efd, #6610f2);
        }

        .icon-learning {
            background: linear-gradient(135deg, #ffc107, #fd7e14);
        }

        .bsc-arrow-up {
            text-align: center;
            color: #adb5bd;
            font-size: 2rem;
            margin: -2rem 0 1rem 0;
            position: relative;
            z-index: 2;
            text-shadow: 0 2px 4px rgba(255, 255, 255, 1);
        }
    </style>

    <form method="POST" id="toolForm">
        <div class="text-center mb-5">
            <input type="text" name="titulo"
                class="form-control form-control-lg border-0 bg-transparent shadow-none fs-2 fw-bold text-center"
                value="<?php echo $item ? htmlspecialchars($item['titulo']) : ''; ?>"
                placeholder="Título do Scorecard..." style="letter-spacing: -1px;" required>
            <p class="text-muted">Mapa Estratégico (Causa & Efeito)</p>
        </div>

        <div class="bsc-container">

            <!-- 1. Financeira (Topo) -->
            <div class="bsc-layer">
                <div class="bsc-card bsc-financial p-4">
                    <div class="row align-items-center">
                        <div class="col-md-3 text-center text-md-start">
                            <div class="bsc-icon icon-financial mx-auto mx-md-0">
                                <i class="bi bi-graph-up-arrow"></i>
                            </div>
                            <h5 class="fw-bold text-success mb-1">Financeira</h5>
                            <small class="text-muted d-block">Resultados para acionistas</small>
                        </div>
                        <div class="col-md-9 border-start-md px-4">
                            <div id="list-financeira" class="vstack gap-2">
                                <?php foreach ($conteudo['financeira'] as $txt): ?>
                                    <div class="input-group">
                                        <input type="text" name="financeira[]" class="form-control border-0 bg-light"
                                            value="<?php echo htmlspecialchars($txt); ?>">
                                        <button type="button" class="btn btn-light text-danger"
                                            onclick="this.parentElement.remove()"><i class="bi bi-x-lg"></i></button>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            <button type="button" class="btn btn-outline-secondary btn-sm w-100 mt-2"
                                onclick="addItem('financeira')">
                                + Adicionar Objetivo Financeiro
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bsc-arrow-up"><i class="bi bi-arrow-up-circle-fill"></i></div>

            <!-- 2. Clientes -->
            <div class="bsc-layer">
                <div class="bsc-card bsc-customer p-4">
                    <div class="row align-items-center">
                        <div class="col-md-3 text-center text-md-start">
                            <div class="bsc-icon icon-customer mx-auto mx-md-0">
                                <i class="bi bi-people"></i>
                            </div>
                            <h5 class="fw-bold text-info mb-1">Clientes</h5>
                            <small class="text-muted d-block">Valor para o mercado</small>
                        </div>
                        <div class="col-md-9 border-start-md px-4">
                            <div id="list-clientes" class="vstack gap-2">
                                <?php foreach ($conteudo['clientes'] as $txt): ?>
                                    <div class="input-group">
                                        <input type="text" name="clientes[]" class="form-control border-0 bg-light"
                                            value="<?php echo htmlspecialchars($txt); ?>">
                                        <button type="button" class="btn btn-light text-danger"
                                            onclick="this.parentElement.remove()"><i class="bi bi-x-lg"></i></button>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            <button type="button" class="btn btn-outline-secondary btn-sm w-100 mt-2"
                                onclick="addItem('clientes')">
                                + Adicionar Objetivo de Cliente
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bsc-arrow-up"><i class="bi bi-arrow-up-circle-fill"></i></div>

            <!-- 3. Processos Internos -->
            <div class="bsc-layer">
                <div class="bsc-card bsc-internal p-4">
                    <div class="row align-items-center">
                        <div class="col-md-3 text-center text-md-start">
                            <div class="bsc-icon icon-internal mx-auto mx-md-0">
                                <i class="bi bi-gear-wide-connected"></i>
                            </div>
                            <h5 class="fw-bold text-primary mb-1">Processos</h5>
                            <small class="text-muted d-block">Eficiência Operacional</small>
                        </div>
                        <div class="col-md-9 border-start-md px-4">
                            <div id="list-processos" class="vstack gap-2">
                                <?php foreach ($conteudo['processos'] as $txt): ?>
                                    <div class="input-group">
                                        <input type="text" name="processos[]" class="form-control border-0 bg-light"
                                            value="<?php echo htmlspecialchars($txt); ?>">
                                        <button type="button" class="btn btn-light text-danger"
                                            onclick="this.parentElement.remove()"><i class="bi bi-x-lg"></i></button>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            <button type="button" class="btn btn-outline-secondary btn-sm w-100 mt-2"
                                onclick="addItem('processos')">
                                + Adicionar Processo Chave
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bsc-arrow-up"><i class="bi bi-arrow-up-circle-fill"></i></div>

            <!-- 4. Aprendizado (Base) -->
            <div class="bsc-layer">
                <div class="bsc-card bsc-learning p-4">
                    <div class="row align-items-center">
                        <div class="col-md-3 text-center text-md-start">
                            <div class="bsc-icon icon-learning mx-auto mx-md-0">
                                <i class="bi bi-mortarboard"></i>
                            </div>
                            <h5 class="fw-bold text-warning mb-1">Aprendizado</h5>
                            <small class="text-muted d-block">Capital Humano e Info</small>
                        </div>
                        <div class="col-md-9 border-start-md px-4">
                            <div id="list-aprendizado" class="vstack gap-2">
                                <?php foreach ($conteudo['aprendizado'] as $txt): ?>
                                    <div class="input-group">
                                        <input type="text" name="aprendizado[]" class="form-control border-0 bg-light"
                                            value="<?php echo htmlspecialchars($txt); ?>">
                                        <button type="button" class="btn btn-light text-danger"
                                            onclick="this.parentElement.remove()"><i class="bi bi-x-lg"></i></button>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            <button type="button" class="btn btn-outline-secondary btn-sm w-100 mt-2"
                                onclick="addItem('aprendizado')">
                                + Adicionar Capacidade
                            </button>
                        </div>
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
        <input type="text" name="${type}[]" class="form-control border-0 bg-light" placeholder="Novo item..." autofocus>
        <button type="button" class="btn btn-light text-danger" onclick="this.parentElement.remove()"><i class="bi bi-x-lg"></i></button>
    `;
        list.appendChild(div);
    }
</script>

<?php require_once __DIR__ . '/../../../../includes/footer.php'; ?>