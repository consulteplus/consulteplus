<?php
$pageTitle = "Importação de Dados";
require_once __DIR__ . '/../../../../config/config.php';
require_once __DIR__ . '/../../../includes/header.php';

// Check permissions
checkPermission(['admin', 'superadmin']);
?>

<div class="container-fluid py-4">

    <div class="d-flex align-items-center mb-4">
        <a href="<?php echo BASE_URL; ?>marketing/leads"
            class="btn btn-sm btn-light rounded-circle border shadow-sm me-3">
            <i class="bi bi-arrow-left"></i>
        </a>
        <div>
            <h4 class="fw-bold mb-1">Importação de Dados</h4>
            <p class="text-muted small mb-0">Passo 1: Upload do Arquivo (CSV)</p>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">

                    <form action="step2.php" method="POST" enctype="multipart/form-data">

                        <!-- Tipo de Importação -->
                        <div class="mb-4">
                            <label class="form-label fw-bold small text-uppercase text-muted">O que você deseja
                                importar?</label>

                            <div class="list-group">
                                <label class="list-group-item d-flex gap-3 align-items-center p-3"
                                    style="cursor: pointer;">
                                    <input class="form-check-input flex-shrink-0" type="radio" name="tipo_importacao"
                                        value="leads" checked>
                                    <span>
                                        <strong class="d-block">Apenas Leads (Contatos)</strong>
                                        <small class="text-muted">Importar pessoas para a tabela de usuários.</small>
                                    </span>
                                </label>
                                <label class="list-group-item d-flex gap-3 align-items-center p-3"
                                    style="cursor: pointer;">
                                    <input class="form-check-input flex-shrink-0" type="radio" name="tipo_importacao"
                                        value="empresas">
                                    <span>
                                        <strong class="d-block">Apenas Empresas</strong>
                                        <small class="text-muted">Importar clientes PJ para a tabela de
                                            empresas.</small>
                                    </span>
                                </label>
                                <label
                                    class="list-group-item d-flex gap-3 align-items-center p-3 bg-light border-primary"
                                    style="cursor: pointer;">
                                    <input class="form-check-input flex-shrink-0" type="radio" name="tipo_importacao"
                                        value="conjunto">
                                    <span>
                                        <strong class="d-block text-primary">Empresas + Contatos (Conjunto)</strong>
                                        <small class="text-muted">Cria a Empresa e vincula o Contato
                                            automaticamente.</small>
                                    </span>
                                </label>
                            </div>
                        </div>

                        <!-- Upload File -->
                        <div class="mb-4">
                            <label class="form-label fw-bold small text-uppercase text-muted">Arquivo CSV</label>
                            <input type="file" class="form-control form-control-lg" name="csv_file" accept=".csv"
                                required>
                            <div class="form-text mt-2">
                                Certifique-se que o arquivo está separado por <strong>vírgula (,)</strong> ou
                                <strong>ponto-e-vírgula (;)</strong>.
                                A primeira linha deve conter o cabeçalho.
                            </div>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary rounded-pill py-2 fw-bold">
                                Continuar para Mapeamento <i class="bi bi-arrow-right ms-2"></i>
                            </button>
                        </div>

                    </form>

                </div>
            </div>
        </div>
    </div>

</div>

<?php require_once __DIR__ . '/../../../../includes/footer.php'; ?>