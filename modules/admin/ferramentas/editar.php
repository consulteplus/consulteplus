<?php
$pageTitle = "Editar Ferramenta";
require_once __DIR__ . '/../../../includes/header.php';
checkPermission(['superadmin']);

$id = $_GET['id'] ?? null;
if (!$id)
    die("ID inválido");

// Handle Post
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = $_POST['nome'];
    $descricao = $_POST['descricao'];
    $icone = $_POST['icone'];
    $ordem = intval($_POST['ordem']);
    $ativo = isset($_POST['ativo']) ? 1 : 0;

    $stmt = $conn->prepare("UPDATE ferramentas_tipos SET nome=?, descricao=?, icone=?, ordem=?, ativo=? WHERE id=?");
    $stmt->bind_param("sssiis", $nome, $descricao, $icone, $ordem, $ativo, $id);

    if ($stmt->execute()) {
        header("Location: " . BASE_URL . "admin/ferramentas");
        exit;
    }
}

// Fetch Data
$stmt = $conn->prepare("SELECT * FROM ferramentas_tipos WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$tool = $stmt->get_result()->fetch_assoc();

if (!$tool)
    die("Ferramenta não encontrada");
?>

<div class="container-xxl flex-grow-1 container-p-y">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold m-0">Editar:
            <?php echo htmlspecialchars($tool['nome']); ?>
        </h4>
        <div>
            <a href="<?php echo BASE_URL; ?>ferramentas/<?php echo $tool['slug']; ?>/simular"
                class="btn btn-outline-primary me-2">
                <i class="bi bi-play-circle me-1"></i> Simular
            </a>
            <a href="<?php echo BASE_URL; ?>admin/ferramentas" class="btn btn-outline-secondary">Cancelar</a>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-body">
            <form method="POST">
                <div class="mb-3">
                    <label class="form-label">Nome da Ferramenta</label>
                    <input type="text" name="nome" class="form-control"
                        value="<?php echo htmlspecialchars($tool['nome']); ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Descrição (Exibida no Card)</label>
                    <textarea name="descricao" class="form-control" rows="3"
                        required><?php echo htmlspecialchars($tool['descricao']); ?></textarea>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Ícone (Bootstrap Icons)</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi <?php echo $tool['icone']; ?>"
                                    id="iconPreview"></i></span>
                            <input type="text" name="icone" id="iconInput" class="form-control"
                                value="<?php echo htmlspecialchars($tool['icone']); ?>" placeholder="bi-tools">
                        </div>
                        <small class="text-muted">Ex: bi-trophy, bi-graph-up</small>
                    </div>

                    <div class="col-md-3 mb-3">
                        <label class="form-label">Ordem</label>
                        <input type="number" name="ordem" class="form-control"
                            value="<?php echo intval($tool['ordem']); ?>">
                    </div>

                    <div class="col-md-3 mb-3 d-flex align-items-end">
                        <div class="form-check form-switch mb-2">
                            <input class="form-check-input" type="checkbox" name="ativo" id="ativoSwitch" <?php echo $tool['ativo'] ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="ativoSwitch">Ativo para Clientes</label>
                        </div>
                    </div>
                </div>

                <div class="mt-3 text-end">
                    <button type="submit" class="btn btn-primary px-4">Salvar Alterações</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.getElementById('iconInput').addEventListener('input', function () {
        document.getElementById('iconPreview').className = 'bi ' + this.value;
    });
</script>

<?php require_once __DIR__ . '/../../../includes/footer.php'; ?>