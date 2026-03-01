<?php
require_once __DIR__ . '/../../../config/config.php';
require_once __DIR__ . '/../../../includes/auth.php';
checkPermission(['admin', 'cliente']);

$id = $_GET['id'] ?? null;
$company_id = $_SESSION['company_id'];
$redirectUrl = BASE_URL . "ferramentas/porter";

if ($id) {
    if (isset($_GET['confirm']) && $_GET['confirm'] == 1) {
        $stmt = $conn->prepare("DELETE FROM ferramentas_analises WHERE id = ? AND company_id = ?");
        $stmt->bind_param("ii", $id, $company_id);
        if ($stmt->execute()) {
            header("Location: " . $redirectUrl);
            exit;
        } else {
            die("Erro ao excluir.");
        }
    } else {

        $stmt = $conn->prepare("SELECT titulo FROM ferramentas_analises WHERE id = ? AND company_id = ?");
        $stmt->bind_param("ii", $id, $company_id);
        $stmt->execute();
        $res = $stmt->get_result();

        if ($res->num_rows > 0) {
            $row = $res->fetch_assoc();
            $titulo = $row['titulo'];

            $pageTitle = "Excluir Análise";
            require_once __DIR__ . '/../../../includes/header.php';
            ?>
            <div class="container-xxl flex-grow-1 container-p-y">
                <div class="row justify-content-center">
                    <div class="col-md-6">
                        <div class="card shadow">
                            <div class="card-body text-center p-5">
                                <i class="bi bi-exclamation-triangle text-danger display-1 mb-3"></i>
                                <h3 class="fw-bold">Tem certeza?</h3>
                                <p class="text-muted">Você está prestes a excluir "<strong>
                                        <?php echo htmlspecialchars($titulo); ?>
                                    </strong>".<br>Esta ação não pode ser desfeita.</p>
                                <div class="mt-4 d-grid gap-2 d-md-block">
                                    <a href="<?php echo $redirectUrl; ?>" class="btn btn-secondary me-2 px-4">Cancelar</a>
                                    <a href="?id=<?php echo $id; ?>&confirm=1" class="btn btn-danger px-4">Sim, Excluir</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php
            require_once __DIR__ . '/../../../includes/footer.php';
        } else {
            header("Location: " . $redirectUrl);
            exit;
        }
    }
} else {
    header("Location: " . $redirectUrl);
    exit;
}
?>