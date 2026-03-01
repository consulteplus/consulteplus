<?php
// ENABLE ERROR REPORTING FOR DEBUGGING
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$pageTitle = "Ferramentas Estratégicas";

// Wrap require in try-catch to catch file inclusion errors
try {
    require_once __DIR__ . '/../../includes/header.php';
} catch (Throwable $e) {
    die("Error loading header: " . $e->getMessage());
}

try {
    if (!function_exists('checkPermission')) {
        throw new Exception("Function checkPermission not found.");
    }
    checkPermission(['admin', 'cliente']);
} catch (Throwable $e) {
    echo "<div class='alert alert-danger'>Auth Error: " . $e->getMessage() . "</div>";
}

echo "<div class='container-xxl flex-grow-1 container-p-y'>";
echo "    <h4 class='fw-bold py-3 mb-4'>";
echo "        <i class='bi bi-tools me-2'></i>Ferramentas Estratégicas";
echo "    </h4>";

// Check Database Connection
if (!isset($conn)) {
    echo "<div class='alert alert-danger'>Erro: Conexão com banco de dados não encontrada (\$conn).</div>";
} else {
    // Check Table Existence first
    $tableCheck = $conn->query("SHOW TABLES LIKE 'ferramentas_tipos'");
    if (!$tableCheck || $tableCheck->num_rows === 0) {
        echo "<div class='alert alert-warning'>Tabela 'ferramentas_tipos' não encontrada no banco de dados.</div>";
    } else {
        // Fetch Active Tools (Filtered by Permission)
        $userId = $_SESSION['user_id'] ?? 0;
        $company_id = $_SESSION['company_id'] ?? 0;
        $isAdmin = ($_SESSION['tipo'] === 'admin' || $_SESSION['tipo'] === 'superadmin');

        $sql = "SELECT t.* FROM ferramentas_tipos t";

        if (!$isAdmin) {
            $sql .= " JOIN recursos_atribuicoes a ON t.id = a.recurso_id 
                      WHERE t.ativo = 1 
                      AND a.empresa_id = $company_id 
                      AND a.recurso_tipo = 'ferramenta' 
                      AND a.ativo = 1";
        } else {
            $sql .= " WHERE t.ativo = 1";
        }

        $sql .= " ORDER BY t.ordem ASC";
        $result = $conn->query($sql);

        if (!$result) {
            echo "<div class='alert alert-danger'>Erro na consulta SQL: " . $conn->error . "</div>";
        } elseif ($result->num_rows === 0) {
            echo "<div class='alert alert-info'>Nenhuma ferramenta está disponível no momento.</div>";
        } else {
            echo "<div class='row g-4'>";
            while ($tool = $result->fetch_assoc()) {
                // Check if module code exists
                $modulePath = __DIR__ . '/' . $tool['slug'] . '/index.php';
                $isImplemented = file_exists($modulePath);
                $link = BASE_URL . 'ferramentas/' . $tool['slug'];
                ?>
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100 shadow border-0 hover-lift text-center p-3">
                        <div class="card-body">
                            <div class="avatar avatar-xl bg-primary bg-opacity-10 rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center"
                                style="width: 80px; height: 80px;">
                                <i class="bi <?php echo $tool['icone']; ?> text-primary fs-1"></i>
                            </div>
                            <h5 class="card-title fw-bold"><?php echo htmlspecialchars($tool['nome']); ?></h5>
                            <p class="card-text text-muted mb-4 small">
                                <?php echo htmlspecialchars($tool['descricao']); ?>
                            </p>

                            <?php if ($isImplemented): ?>
                                <a href="<?php echo $link; ?>" class="btn btn-primary w-100">
                                    Acessar Ferramenta
                                </a>
                            <?php else: ?>
                                <button class="btn btn-outline-secondary w-100" disabled title="Módulo não implementado no sistema">
                                    Em Desenvolvimento
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php
            }
            echo "</div>";
        }
    }
}
echo "</div>";

try {
    require_once __DIR__ . '/../../includes/footer.php';
} catch (Throwable $e) {
    echo "Error loading footer: " . $e->getMessage();
}
?>