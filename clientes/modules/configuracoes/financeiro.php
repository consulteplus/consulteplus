<?php
$pageTitle = "Configurações Financeiras";
require_once __DIR__ . '/../../includes/header.php';

checkPermission(['admin']);

// Processar Ações (Adicionar / Alternar Status)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Adicionar Nova Categoria
    if (isset($_POST['action']) && $_POST['action'] === 'add') {
        $nome = sanitize($_POST['nome']);
        $tipo = sanitize($_POST['tipo']);
        $grupo = sanitize($_POST['grupo_dre']);

        if (!empty($nome)) {
            $company_id = $_SESSION['company_id'];
            $stmt = $conn->prepare("INSERT INTO financeiro_categorias (nome, tipo, grupo_dre, ativo, company_id) VALUES (?, ?, ?, 1, ?)");
            $stmt->bind_param("sssi", $nome, $tipo, $grupo, $company_id);
            if ($stmt->execute()) {
                $_SESSION['success'] = "Categoria adicionada com sucesso!";
            } else {
                $_SESSION['error'] = "Erro ao adicionar: " . $conn->error;
            }
        }
    }

    // Alternar Status (Ativo/Inativo)
    if (isset($_POST['action']) && $_POST['action'] === 'toggle') {
        $id = (int) $_POST['id'];
        $company_id = $_SESSION['company_id'];
        $conn->query("UPDATE financeiro_categorias SET ativo = NOT ativo WHERE id = $id AND company_id = $company_id");
        $_SESSION['success'] = "Status atualizado!";
    }

    redirect('configuracoes/financeiro');
}

// Buscar Categorias
$company_id = $_SESSION['company_id'];
$receitas = $conn->query("SELECT * FROM financeiro_categorias WHERE tipo = 'receita' AND company_id = $company_id ORDER BY nome");
$despesas = $conn->query("SELECT * FROM financeiro_categorias WHERE tipo = 'despesa' AND company_id = $company_id ORDER BY nome");

// Map de Nomes Amigáveis para Grupos
$gruposMap = [
    'receita_operacional' => 'Receita Operacional',
    'deducoes' => '(-) Deduções/Impostos',
    'custos_variaveis' => '(-) Custos Variáveis',
    'despesas_fixas' => '(-) Despesas Fixas',
    'despesas_pessoal' => '(-) Pessoal/Folha',
    'despesas_financeiras' => '(-) Financeiras',
    'investimentos' => 'Investimentos',
    'outros' => 'Outros'
];
?>

<div class="row align-items-center mb-4">
    <div class="col-md-6">
        <h1><i class="bi bi-tags"></i> Categorias Financeiras</h1>
        <p class="text-muted">Configure o Plano de Contas para o DRE Gerencial.</p>
    </div>
    <div class="col-md-6 text-end">
        <a href="<?php echo BASE_URL; ?>financeiro" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Voltar para Financeiro
        </a>
    </div>
</div>

<div class="row">
    <!-- Formulário de Nova Categoria -->
    <div class="col-md-4 mb-4">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-primary text-white">
                <h5 class="card-title mb-0"><i class="bi bi-plus-circle"></i> Nova Categoria</h5>
            </div>
            <div class="card-body">
                <form method="POST">
                    <input type="hidden" name="action" value="add">

                    <div class="mb-3">
                        <label class="form-label">Nome da Categoria</label>
                        <input type="text" class="form-control" name="nome" required placeholder="Ex: Combustível">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Tipo</label>
                        <select class="form-select" name="tipo" id="tipoSelect" required>
                            <option value="despesa">Despesa (Saída)</option>
                            <option value="receita">Receita (Entrada)</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Grupo DRE (Contábil)</label>
                        <select class="form-select" name="grupo_dre" id="grupoSelect" required>
                            <option value="outros">Outros</option>
                            <optgroup label="Despesas">
                                <option value="despesas_fixas">Despesas Fixas (Aluguel, Luz)</option>
                                <option value="despesas_pessoal">Pessoal (Salários, Pró-labore)</option>
                                <option value="custos_variaveis">Custos Variáveis (Materiais, Comissões)</option>
                                <option value="deducoes">Impostos / Deduções</option>
                                <option value="investimentos">Investimentos (Equipamentos)</option>
                            </optgroup>
                            <optgroup label="Receitas">
                                <option value="receita_operacional">Receita Operacional (Consultas)</option>
                            </optgroup>
                        </select>
                    </div>

                    <button type="submit" class="btn btn-primary w-100">Adicionar</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Lista de Categorias -->
    <div class="col-md-8">
        <div class="row">
            <!-- Coluna Receitas -->
            <div class="col-md-6">
                <div class="card border-success mb-3">
                    <div class="card-header bg-success text-white">
                        <i class="bi bi-arrow-down-circle"></i> Receitas
                    </div>
                    <ul class="list-group list-group-flush">
                        <?php while ($cat = $receitas->fetch_assoc()): ?>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <span
                                        class="<?php echo $cat['ativo'] ? '' : 'text-decoration-line-through text-muted'; ?> d-block fw-bold">
                                        <?php echo htmlspecialchars($cat['nome']); ?>
                                    </span>
                                    <small class="text-muted" style="font-size: 0.75rem;">
                                        <?php echo $gruposMap[$cat['grupo_dre'] ?? 'outros']; ?>
                                    </small>
                                </div>
                                <form method="POST" class="d-inline">
                                    <input type="hidden" name="action" value="toggle">
                                    <input type="hidden" name="id" value="<?php echo $cat['id']; ?>">
                                    <button type="submit" class="btn btn-sm btn-link text-decoration-none"
                                        title="Ativar/Desativar">
                                        <?php if ($cat['ativo']): ?>
                                            <i class="bi bi-toggle-on text-success fs-4"></i>
                                        <?php else: ?>
                                            <i class="bi bi-toggle-off text-muted fs-4"></i>
                                        <?php endif; ?>
                                    </button>
                                </form>
                            </li>
                        <?php endwhile; ?>
                    </ul>
                </div>
            </div>

            <!-- Coluna Despesas -->
            <div class="col-md-6">
                <div class="card border-danger mb-3">
                    <div class="card-header bg-danger text-white">
                        <i class="bi bi-arrow-up-circle"></i> Despesas
                    </div>
                    <ul class="list-group list-group-flush">
                        <?php while ($cat = $despesas->fetch_assoc()): ?>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <span
                                        class="<?php echo $cat['ativo'] ? '' : 'text-decoration-line-through text-muted'; ?> d-block fw-bold">
                                        <?php echo htmlspecialchars($cat['nome']); ?>
                                    </span>
                                    <small class="text-muted" style="font-size: 0.75rem;">
                                        <?php echo $gruposMap[$cat['grupo_dre'] ?? 'outros']; ?>
                                    </small>
                                </div>
                                <form method="POST" class="d-inline">
                                    <input type="hidden" name="action" value="toggle">
                                    <input type="hidden" name="id" value="<?php echo $cat['id']; ?>">
                                    <button type="submit" class="btn btn-sm btn-link text-decoration-none"
                                        title="Ativar/Desativar">
                                        <?php if ($cat['ativo']): ?>
                                            <i class="bi bi-toggle-on text-success fs-4"></i>
                                        <?php else: ?>
                                            <i class="bi bi-toggle-off text-muted fs-4"></i>
                                        <?php endif; ?>
                                    </button>
                                </form>
                            </li>
                        <?php endwhile; ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>