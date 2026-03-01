<?php
$pageTitle = "Business Model Canvas";
require_once __DIR__ . '/../../../../includes/header.php';
checkPermission(['admin', 'cliente']);

$company_id = $_SESSION['company_id'];
$user_id = $_SESSION['user_id'];
$id = $_GET['id'] ?? null;
$item = null;
$conteudo = [
    'parceiros' => [],
    'atividades' => [],
    'recursos' => [],
    'proposta' => [],
    'relacionamento' => [],
    'canais' => [],
    'segmentos' => [],
    'custos' => [],
    'receitas' => []
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
        'parceiros' => $_POST['parceiros'] ?? [],
        'atividades' => $_POST['atividades'] ?? [],
        'recursos' => $_POST['recursos'] ?? [],
        'proposta' => $_POST['proposta'] ?? [],
        'relacionamento' => $_POST['relacionamento'] ?? [],
        'canais' => $_POST['canais'] ?? [],
        'segmentos' => $_POST['segmentos'] ?? [],
        'custos' => $_POST['custos'] ?? [],
        'receitas' => $_POST['receitas'] ?? []
    ];
    $jsonConteudo = json_encode($newConteudo, JSON_UNESCAPED_UNICODE);

    if ($id) {
        $stmt = $conn->prepare("UPDATE ferramentas_analises SET titulo = ?, conteudo = ? WHERE id = ? AND company_id = ?");
        $stmt->bind_param("ssii", $titulo, $jsonConteudo, $id, $company_id);
    } else {
        $stmt = $conn->prepare("INSERT INTO ferramentas_analises (company_id, user_id, ferramenta, titulo, conteudo) VALUES (?, ?, 'CANVAS', ?, ?)");
        $stmt->bind_param("iiss", $company_id, $user_id, $titulo, $jsonConteudo);
    }

    if ($stmt->execute()) {
        header("Location: " . BASE_URL . "admin/execucao-ferramentas/canvas");
        exit;
    }
}
?>

<style>
    .canvas-grid {
        display: grid;
        grid-template-columns: repeat(5, 1fr);
        grid-template-rows: repeat(3, min-content);
        gap: 1rem;
    }

    /* Responsive stacking for mobile */
    @media (max-width: 992px) {
        .canvas-grid {
            display: flex;
            flex-direction: column;
        }
    }

    .canvas-card {
        height: 100%;
        transition: all 0.2s;
    }

    .canvas-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1) !important;
    }

    /* Grid Areas */
    .area-kp {
        grid-column: 1;
        grid-row: 1 / span 2;
    }

    /* Key Partners */
    .area-ka {
        grid-column: 2;
        grid-row: 1;
    }

    /* Key Activities */
    .area-kr {
        grid-column: 2;
        grid-row: 2;
    }

    /* Key Resources */
    .area-vp {
        grid-column: 3;
        grid-row: 1 / span 2;
    }

    /* Value Propositions */
    .area-cr {
        grid-column: 4;
        grid-row: 1;
    }

    /* Customer Relationships */
    .area-ch {
        grid-column: 4;
        grid-row: 2;
    }

    /* Channels */
    .area-cs {
        grid-column: 5;
        grid-row: 1 / span 2;
    }

    /* Customer Segments */
    .area-cst {
        grid-column: 1 / span 2;
        grid-row: 3;
    }

    /* Cost Structure */
    .area-rs {
        grid-column: 3 / span 3;
        grid-row: 3;
    }

    /* Revenue Streams */

    .canvas-title {
        font-size: 0.9rem;
        font-weight: bold;
        text-transform: uppercase;
        color: #6c757d;
        margin-bottom: 0.5rem;
        display: flex;
        align-items: center;
    }

    .canvas-title i {
        margin-right: 0.5rem;
        font-size: 1.1rem;
    }
</style>

<div class="container-fluid container-p-y">

    <?php
    $backUrl = isset($_GET['preview']) ? BASE_URL . 'admin/execucao-ferramentas' : BASE_URL . 'admin/execucao-ferramentas';
    ?>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold m-0">
            <?php echo $id ? 'Editar Canvas' : 'Novo Business Model Canvas'; ?>
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
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body">
                <input type="text" name="titulo" class="form-control form-control-lg border-0 fs-3 fw-bold"
                    value="<?php echo $item ? htmlspecialchars($item['titulo']) : ''; ?>"
                    placeholder="Nome do Modelo de Negócios" required>
            </div>
        </div>

        <div class="canvas-grid">

            <!-- Key Partners -->
            <div class="card shadow-sm border-0 area-kp">
                <div class="card-body">
                    <div class="canvas-title"><i class="bi bi-link-45deg"></i> Parcerias Chave</div>
                    <div id="list-parceiros" class="vstack gap-2">
                        <?php foreach ($conteudo['parceiros'] as $txt): ?>
                            <div class="input-group input-group-sm">
                                <input type="text" name="parceiros[]" class="form-control border-0 bg-light"
                                    value="<?php echo htmlspecialchars($txt); ?>">
                                <button type="button" class="btn btn-light text-danger"
                                    onclick="this.parentElement.remove()"><i class="bi bi-x"></i></button>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <button type="button" class="btn btn-sm btn-link text-decoration-none p-0 mt-2"
                        onclick="addItem('parceiros')">+ Adicionar</button>
                </div>
            </div>

            <!-- Key Activities -->
            <div class="card shadow-sm border-0 area-ka">
                <div class="card-body">
                    <div class="canvas-title"><i class="bi bi-check2-circle"></i> Atividades Chave</div>
                    <div id="list-atividades" class="vstack gap-2">
                        <?php foreach ($conteudo['atividades'] as $txt): ?>
                            <div class="input-group input-group-sm">
                                <input type="text" name="atividades[]" class="form-control border-0 bg-light"
                                    value="<?php echo htmlspecialchars($txt); ?>">
                                <button type="button" class="btn btn-light text-danger"
                                    onclick="this.parentElement.remove()"><i class="bi bi-x"></i></button>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <button type="button" class="btn btn-sm btn-link text-decoration-none p-0 mt-2"
                        onclick="addItem('atividades')">+ Adicionar</button>
                </div>
            </div>

            <!-- Key Resources -->
            <div class="card shadow-sm border-0 area-kr">
                <div class="card-body">
                    <div class="canvas-title"><i class="bi bi-cpu"></i> Recursos Chave</div>
                    <div id="list-recursos" class="vstack gap-2">
                        <?php foreach ($conteudo['recursos'] as $txt): ?>
                            <div class="input-group input-group-sm">
                                <input type="text" name="recursos[]" class="form-control border-0 bg-light"
                                    value="<?php echo htmlspecialchars($txt); ?>">
                                <button type="button" class="btn btn-light text-danger"
                                    onclick="this.parentElement.remove()"><i class="bi bi-x"></i></button>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <button type="button" class="btn btn-sm btn-link text-decoration-none p-0 mt-2"
                        onclick="addItem('recursos')">+ Adicionar</button>
                </div>
            </div>

            <!-- Value Propositions -->
            <div class="card shadow-sm border-0 area-vp border-top border-4 border-primary">
                <div class="card-body">
                    <div class="canvas-title text-primary"><i class="bi bi-gift-fill"></i> Proposta de Valor</div>
                    <div id="list-proposta" class="vstack gap-2">
                        <?php foreach ($conteudo['proposta'] as $txt): ?>
                            <div class="input-group input-group-sm">
                                <input type="text" name="proposta[]" class="form-control border-0 bg-light"
                                    value="<?php echo htmlspecialchars($txt); ?>">
                                <button type="button" class="btn btn-light text-danger"
                                    onclick="this.parentElement.remove()"><i class="bi bi-x"></i></button>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <button type="button" class="btn btn-outline-secondary btn-sm w-100 mt-2"
                        onclick="addItem('proposta')">+ Adicionar</button>
                </div>
            </div>

            <!-- Customer Relationships -->
            <div class="card shadow-sm border-0 area-cr">
                <div class="card-body">
                    <div class="canvas-title"><i class="bi bi-heart"></i> Relacionamento</div>
                    <div id="list-relacionamento" class="vstack gap-2">
                        <?php foreach ($conteudo['relacionamento'] as $txt): ?>
                            <div class="input-group input-group-sm">
                                <input type="text" name="relacionamento[]" class="form-control border-0 bg-light"
                                    value="<?php echo htmlspecialchars($txt); ?>">
                                <button type="button" class="btn btn-light text-danger"
                                    onclick="this.parentElement.remove()"><i class="bi bi-x"></i></button>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <button type="button" class="btn btn-sm btn-link text-decoration-none p-0 mt-2"
                        onclick="addItem('relacionamento')">+ Adicionar</button>
                </div>
            </div>

            <!-- Channels -->
            <div class="card shadow-sm border-0 area-ch">
                <div class="card-body">
                    <div class="canvas-title"><i class="bi bi-truck"></i> Canais</div>
                    <div id="list-canais" class="vstack gap-2">
                        <?php foreach ($conteudo['canais'] as $txt): ?>
                            <div class="input-group input-group-sm">
                                <input type="text" name="canais[]" class="form-control border-0 bg-light"
                                    value="<?php echo htmlspecialchars($txt); ?>">
                                <button type="button" class="btn btn-light text-danger"
                                    onclick="this.parentElement.remove()"><i class="bi bi-x"></i></button>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <button type="button" class="btn btn-sm btn-link text-decoration-none p-0 mt-2"
                        onclick="addItem('canais')">+ Adicionar</button>
                </div>
            </div>

            <!-- Customer Segments -->
            <div class="card shadow-sm border-0 area-cs">
                <div class="card-body">
                    <div class="canvas-title"><i class="bi bi-people-fill"></i> Segmentos de Cliente</div>
                    <div id="list-segmentos" class="vstack gap-2">
                        <?php foreach ($conteudo['segmentos'] as $txt): ?>
                            <div class="input-group input-group-sm">
                                <input type="text" name="segmentos[]" class="form-control border-0 bg-light"
                                    value="<?php echo htmlspecialchars($txt); ?>">
                                <button type="button" class="btn btn-light text-danger"
                                    onclick="this.parentElement.remove()"><i class="bi bi-x"></i></button>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <button type="button" class="btn btn-sm btn-link text-decoration-none p-0 mt-2"
                        onclick="addItem('segmentos')">+ Adicionar</button>
                </div>
            </div>

            <!-- Cost Structure -->
            <div class="card shadow-sm border-0 area-cst border-top border-4 border-danger">
                <div class="card-body">
                    <div class="canvas-title text-danger"><i class="bi bi-cash"></i> Estrutura de Custos</div>
                    <div id="list-custos" class="vstack gap-2">
                        <?php foreach ($conteudo['custos'] as $txt): ?>
                            <div class="input-group input-group-sm">
                                <input type="text" name="custos[]" class="form-control border-0 bg-light"
                                    value="<?php echo htmlspecialchars($txt); ?>">
                                <button type="button" class="btn btn-light text-danger"
                                    onclick="this.parentElement.remove()"><i class="bi bi-x"></i></button>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <button type="button" class="btn btn-sm btn-link text-decoration-none p-0 mt-2"
                        onclick="addItem('custos')">+ Adicionar</button>
                </div>
            </div>

            <!-- Revenue Streams -->
            <div class="card shadow-sm border-0 area-rs border-top border-4 border-success">
                <div class="card-body">
                    <div class="canvas-title text-success"><i class="bi bi-currency-dollar"></i> Fontes de Receita</div>
                    <div id="list-receitas" class="vstack gap-2">
                        <?php foreach ($conteudo['receitas'] as $txt): ?>
                            <div class="input-group input-group-sm">
                                <input type="text" name="receitas[]" class="form-control border-0 bg-light"
                                    value="<?php echo htmlspecialchars($txt); ?>">
                                <button type="button" class="btn btn-light text-danger"
                                    onclick="this.parentElement.remove()"><i class="bi bi-x"></i></button>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <button type="button" class="btn btn-sm btn-link text-decoration-none p-0 mt-2"
                        onclick="addItem('receitas')">+ Adicionar</button>
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
        div.className = 'input-group input-group-sm';
        div.innerHTML = `
        <input type="text" name="${type}[]" class="form-control border-0 bg-light" placeholder="Novo item..." autofocus>
        <button type="button" class="btn btn-light text-danger" onclick="this.parentElement.remove()"><i class="bi bi-x"></i></button>
    `;
        list.appendChild(div);
    }
</script>

<?php require_once __DIR__ . '/../../../../includes/footer.php'; ?>