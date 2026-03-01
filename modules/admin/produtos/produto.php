<?php
$pageTitle = "Editar Produto";
require_once __DIR__ . '/../header.php';
checkPermission(['admin']);

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$company_id = $_SESSION['company_id'];
$produto = [
    'titulo' => '',
    'descricao' => '',
    'imagem_capa' => '',
    'ativo' => 1,
    'valor' => 0.00,
    'ciclo' => 'MONTHLY',
    'tipo_cobranca' => 'recorrente',
    'link_checkout' => ''
];


// Carregar Service
require_once __DIR__ . '/../../../classes/ProductService.php';
$productService = new ProductService();

// Carregar dados se edição
if ($id > 0) {
    $produto = $productService->buscar($id, $company_id);
    if (!$produto) {
        // Redireciona ou mostra erro limpo
        echo "<script>alert('Produto não encontrado'); window.location.href='" . BASE_URL . "admin/produtos';</script>";
        exit;
    }
}
?>

<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-bold"><?php echo $id > 0 ? 'Editar Produto' : 'Novo Produto'; ?></h5>
                </div>
                <div class="card-body p-4">
                    <?php if (isset($error)): ?>
                        <div class="alert alert-danger"><?php echo $error; ?></div>
                    <?php endif; ?>


                    <form id="formProduto" onsubmit="salvarProduto(event)">
                        <input type="hidden" name="id" value="<?php echo $id; ?>">
                        <!-- Título e Tipo -->
                        <div class="row mb-3">
                            <div class="col-md-8">
                                <label class="form-label">Título do Produto <span class="text-danger">*</span></label>
                                <input type="text" name="titulo" class="form-control" required
                                    value="<?php echo htmlspecialchars($produto['titulo']); ?>" placeholder="Ex: Curso Avançado de Vendas">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Tipo <span class="text-danger">*</span></label>
                                <select name="tipo" class="form-select" required>
                                    <option value="mentoria" <?php echo ($produto['tipo'] ?? '') == 'mentoria' ? 'selected' : ''; ?>>Mentoria</option>
                                    <option value="consultoria" <?php echo ($produto['tipo'] ?? '') == 'consultoria' ? 'selected' : ''; ?>>Consultoria</option>
                                    <option value="treinamento" <?php echo ($produto['tipo'] ?? '') == 'treinamento' ? 'selected' : ''; ?>>Treinamento</option>
                                    <option value="workshop" <?php echo ($produto['tipo'] ?? '') == 'workshop' ? 'selected' : ''; ?>>Workshop</option>
                                    <option value="outro" <?php echo ($produto['tipo'] ?? '') == 'outro' ? 'selected' : ''; ?>>Outro</option>
                                </select>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Descrição</label><textarea name="descricao" class="form-control" rows="4" placeholder="Sobre o que é esse produto?"><?php echo htmlspecialchars($produto['descricao']); ?></textarea>
                        </div>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Tipo de Cobrança</label>
                                <select name="tipo_cobranca" id="tipo_cobranca" class="form-select" onchange="toggleCiclo()">
                                    <option value="unico" <?php echo ($produto['tipo_cobranca'] ?? '') == 'unico' ? 'selected' : ''; ?>>Pagamento Único (Vitalício)</option>
                                    <option value="recorrente" <?php echo ($produto['tipo_cobranca'] ?? 'recorrente') == 'recorrente' ? 'selected' : ''; ?>>Assinatura (Recorrente)</option>
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Preço (R$)</label>
                                <div class="input-group">
                                    <span class="input-group-text">R$</span>
                                    <input type="number" step="0.01" name="valor" class="form-control" 
                                        value="<?php echo number_format($produto['valor'] ?? 0, 2, '.', ''); ?>">
                                </div>
                            </div>
                            <div class="col-md-4 mb-3" id="colCiclo">
                                <label class="form-label">Ciclo de Cobrança</label>
                                <select name="ciclo" class="form-select">
                                    <option value="MONTHLY" <?php echo ($produto['ciclo'] ?? '') == 'MONTHLY' ? 'selected' : ''; ?>>Mensal</option>
                                    <option value="QUARTERLY" <?php echo ($produto['ciclo'] ?? '') == 'QUARTERLY' ? 'selected' : ''; ?>>Trimestral</option>
                                    <option value="SEMIANNUALLY" <?php echo ($produto['ciclo'] ?? '') == 'SEMIANNUALLY' ? 'selected' : ''; ?>>Semestral</option>
                                    <option value="YEARLY" <?php echo ($produto['ciclo'] ?? '') == 'YEARLY' ? 'selected' : ''; ?>>Anual</option>
                                </select>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Link de Checkout Externo (Opcional)</label>
                            <input type="url" name="link_checkout" class="form-control"
                                value="<?php echo htmlspecialchars($produto['link_checkout'] ?? ''); ?>" placeholder="https://pay.kiwify.com.br/...">
                            <div class="form-text">Se preenchido, o botão 'Comprar' levará para este link.</div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">URL da Imagem de Capa</label>
                            <input type="url" name="imagem_capa" class="form-control"
                                value="<?php echo htmlspecialchars($produto['imagem_capa']); ?>" placeholder="https://...">
                            <div class="form-text">Cole o link de uma imagem hospedada (ex: imgur, seu site).</div>
                        </div>

                        <div class="mb-3 form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="ativo" id="ativoCheck" 
                                <?php echo $produto['ativo'] ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="ativoCheck">Produto Ativo (Visível para alunos)</label>
                        </div>

                        <div class="d-flex justify-content-between mt-4">
                            <a href="<?php echo BASE_URL; ?>admin/produtos" class="btn btn-outline-secondary">Cancelar</a>
                            <button type="submit" class="btn btn-primary px-4">Salvar Produto</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function toggleCiclo() {
        const tipo = document.getElementById('tipo_cobranca').value;
        const colCiclo = document.getElementById('colCiclo');
        
        if (tipo === 'unico') {
            colCiclo.style.display = 'none';
        } else {
            colCiclo.style.display = 'block';
        }
    }
    
    function salvarProduto(e) {
        e.preventDefault();
        
        const form = document.getElementById('formProduto');
        const formData = new FormData(form);
        const data = Object.fromEntries(formData.entries());
        
        // Checkbox fix
        data.ativo = document.getElementById('ativoCheck').checked ? 1 : 0;
        
        // Use ABSOLUTE PATH for API to avoid 404 on rewritten URLs
        fetch('<?php echo BASE_URL; ?>modules/admin/produtos/acoes.php?acao=salvar', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data)
        })
        .then(res => res.json())
        .then(response => {
            if (response.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Sucesso!',
                    text: response.message,
                    timer: 2000,
                    showConfirmButton: false
                }).then(() => {
                    window.location.href = '<?php echo BASE_URL; ?>admin/produtos';
                });
            } else {
                Swal.fire('Erro', response.message || 'Erro desconhecido', 'error');
            }
        })
        .catch(err => Swal.fire('Erro', 'Erro de conexão.', 'error'));
    }

    // Run on load
    document.addEventListener("DOMContentLoaded", toggleCiclo);
</script>

<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<?php require_once __DIR__ . '/../../../includes/footer.php'; ?>
