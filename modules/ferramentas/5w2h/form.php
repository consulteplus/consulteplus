<?php
$pageTitle = "Editor 5W2H";
require_once __DIR__ . '/../../../includes/header.php';
checkPermission(['admin', 'cliente']);

$company_id = $_SESSION['company_id'];
$user_id = $_SESSION['user_id'];
$id = $_GET['id'] ?? null;
$item = null;
$conteudo = []; // Array of rows

// Load if editing
if ($id) {
    $stmt = $conn->prepare("SELECT * FROM ferramentas_analises WHERE id = ? AND company_id = ?");
    $stmt->bind_param("ii", $id, $company_id);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($res->num_rows > 0) {
        $item = $res->fetch_assoc();
        $conteudo = json_decode($item['conteudo'], true) ?? [];
    } else {
        die("Plano não encontrada.");
    }
}

// Handle POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_GET['preview'])) {
        echo "<script>alert('Modo Simulação confirmada.'); window.location.href='" . BASE_URL . "admin/ferramentas';</script>";
        exit;
    }

    $titulo = $_POST['titulo'];
    // Reconstruct array from POST columns
    $rows = [];
    if (isset($_POST['what'])) {
        foreach ($_POST['what'] as $k => $v) {
            $rows[] = [
                'what' => $_POST['what'][$k],
                'why' => $_POST['why'][$k],
                'where' => $_POST['where'][$k],
                'when' => $_POST['when'][$k],
                'who' => $_POST['who'][$k],
                'how' => $_POST['how'][$k],
                'how_much' => $_POST['how_much'][$k]
            ];
        }
    }

    $jsonConteudo = json_encode($rows, JSON_UNESCAPED_UNICODE);

    if ($id) {
        $stmt = $conn->prepare("UPDATE ferramentas_analises SET titulo = ?, conteudo = ? WHERE id = ? AND company_id = ?");
        $stmt->bind_param("ssii", $titulo, $jsonConteudo, $id, $company_id);
    } else {
        $stmt = $conn->prepare("INSERT INTO ferramentas_analises (company_id, user_id, ferramenta, titulo, conteudo) VALUES (?, ?, '5W2H', ?, ?)");
        $stmt->bind_param("iiss", $company_id, $user_id, $titulo, $jsonConteudo);
    }

    if ($stmt->execute()) {
        header("Location: " . BASE_URL . "ferramentas/5w2h");
        exit;
    }
}
?>

<style>
    .table-5w2h th {
        font-size: 0.85rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .table-5w2h textarea {
        resize: none;
        font-size: 0.9rem;
    }

    .btn-add-row {
        border-style: dashed;
    }
</style>

<div class="container-fluid container-p-y">

    <?php if (isset($_GET['preview'])): ?>
        <div class="alert alert-info shadow-sm mb-4">
            <i class="bi bi-info-circle-fill me-2"></i> Modo Simulação
        </div>
    <?php endif; ?>

    <?php
    $backUrl = isset($_GET['preview']) ? BASE_URL . 'admin/ferramentas' : BASE_URL . 'ferramentas';
    ?>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold m-0">
            <?php echo $id ? 'Editar Plano 5W2H' : 'Novo Plano de Ação 5W2H'; ?>
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
                    placeholder="Título do Plano de Ação..." required>
            </div>
        </div>

        <div class="card shadow-sm border-0">
            <div class="table-responsive">
                <table class="table table-hover table-5w2h align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th style="min-width: 200px" class="text-primary">What (O que)</th>
                            <th style="min-width: 150px">Why (Por que)</th>
                            <th style="min-width: 120px">Where (Onde)</th>
                            <th style="min-width: 120px">When (Quando)</th>
                            <th style="min-width: 120px">Who (Quem)</th>
                            <th style="min-width: 200px">How (Como)</th>
                            <th style="min-width: 120px" class="text-success">How Much (Quanto)</th>
                            <th style="width: 50px"></th>
                        </tr>
                    </thead>
                    <tbody id="rows-container">
                        <?php if (empty($conteudo)): ?>
                            <!-- Empty Row Template -->
                            <tr class="row-item">
                                <td><textarea name="what[]" class="form-control border-0 bg-transparent" rows="2"
                                        placeholder="Ação..." required></textarea></td>
                                <td><textarea name="why[]" class="form-control border-0 bg-transparent" rows="2"
                                        placeholder="Motivo..."></textarea></td>
                                <td><input type="text" name="where[]" class="form-control border-0 bg-transparent"
                                        placeholder="Local..."></td>
                                <td><input type="date" name="when[]" class="form-control border-0 bg-transparent"></td>
                                <td><input type="text" name="who[]" class="form-control border-0 bg-transparent"
                                        placeholder="Responsável..."></td>
                                <td><textarea name="how[]" class="form-control border-0 bg-transparent" rows="2"
                                        placeholder="Método..."></textarea></td>
                                <td><input type="text" name="how_much[]"
                                        class="form-control border-0 bg-transparent text-success fw-bold"
                                        placeholder="R$ 0,00"></td>
                                <td><button type="button" class="btn btn-link text-danger p-0" onclick="removeRow(this)"><i
                                            class="bi bi-trash"></i></button></td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($conteudo as $row): ?>
                                <tr class="row-item">
                                    <td><textarea name="what[]" class="form-control border-0 bg-transparent" rows="2"
                                            required><?php echo htmlspecialchars($row['what']); ?></textarea></td>
                                    <td><textarea name="why[]" class="form-control border-0 bg-transparent"
                                            rows="2"><?php echo htmlspecialchars($row['why']); ?></textarea></td>
                                    <td><input type="text" name="where[]" class="form-control border-0 bg-transparent"
                                            value="<?php echo htmlspecialchars($row['where']); ?>"></td>
                                    <td><input type="date" name="when[]" class="form-control border-0 bg-transparent"
                                            value="<?php echo htmlspecialchars($row['when']); ?>"></td>
                                    <td><input type="text" name="who[]" class="form-control border-0 bg-transparent"
                                            value="<?php echo htmlspecialchars($row['who']); ?>"></td>
                                    <td><textarea name="how[]" class="form-control border-0 bg-transparent"
                                            rows="2"><?php echo htmlspecialchars($row['how']); ?></textarea></td>
                                    <td><input type="text" name="how_much[]"
                                            class="form-control border-0 bg-transparent text-success fw-bold"
                                            value="<?php echo htmlspecialchars($row['how_much']); ?>"></td>
                                    <td><button type="button" class="btn btn-link text-danger p-0" onclick="removeRow(this)"><i
                                                class="bi bi-trash"></i></button></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <div class="card-footer bg-white text-center py-3">
                <button type="button" class="btn btn-outline-secondary btn-add-row w-100" onclick="addRow()">
                    <i class="bi bi-plus-lg me-2"></i>Adicionar Ação
                </button>
            </div>
        </div>

</div>
</form>
</div>

<script>
    function addRow() {
        const container = document.getElementById('rows-container');
        const tr = document.createElement('tr');
        tr.className = 'row-item';
        tr.innerHTML = `
        <td><textarea name="what[]" class="form-control border-0 bg-transparent" rows="2" placeholder="Ação..." required></textarea></td>
        <td><textarea name="why[]" class="form-control border-0 bg-transparent" rows="2" placeholder="Motivo..."></textarea></td>
        <td><input type="text" name="where[]" class="form-control border-0 bg-transparent" placeholder="Local..."></td>
        <td><input type="date" name="when[]" class="form-control border-0 bg-transparent"></td>
        <td><input type="text" name="who[]" class="form-control border-0 bg-transparent" placeholder="Responsável..."></td>
        <td><textarea name="how[]" class="form-control border-0 bg-transparent" rows="2" placeholder="Método..."></textarea></td>
        <td><input type="text" name="how_much[]" class="form-control border-0 bg-transparent text-success fw-bold" placeholder="R$ 0,00"></td>
        <td><button type="button" class="btn btn-link text-danger p-0" onclick="removeRow(this)"><i class="bi bi-trash"></i></button></td>
    `;
        container.appendChild(tr);
    }
    function removeRow(btn) {
        if (document.querySelectorAll('.row-item').length > 1) {
            btn.closest('tr').remove();
        } else {
            // Clear inputs if it's the last row
            const inputs = btn.closest('tr').querySelectorAll('input, textarea');
            inputs.forEach(input => input.value = '');
        }
    }
</script>

<?php require_once __DIR__ . '/../../../includes/footer.php'; ?>