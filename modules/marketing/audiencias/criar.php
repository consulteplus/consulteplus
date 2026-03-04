<?php
$pageTitle = "Nova Audiência - Marketing";
require_once __DIR__ . '/../../includes/header.php';

// Verificar permissões
if (!hasPermission(['admin', 'superadmin'])) {
    header('Location: ' . BASE_URL . 'dashboard');
    exit;
}

// Se for edição, buscar audiência
$audiencia = null;
$isEdit = false;
if (isset($_GET['id'])) {
    $isEdit = true;
    $id = (int) $_GET['id'];

    require_once __DIR__ . '/../../../../config/database.php';
    $sql = "SELECT * FROM audiencias WHERE id = ? AND company_id = ?";
    $stmt = $conn->prepare($sql);
    $company_id = getCompanyId();
    $stmt->bind_param("ii", $id, $company_id);
    $stmt->execute();
    $audiencia = $stmt->get_result()->fetch_assoc();

    if (!$audiencia) {
        header('Location: index.php');
        exit;
    }

    $pageTitle = "Editar Audiência - Marketing";
}
?>

<!-- CSS Customizado -->
<link rel="stylesheet" href="audiencias.css">

<div class="mb-4">
    <a href="index.php" class="btn btn-outline-secondary btn-sm mb-3">
        <i class="bi bi-arrow-left me-2"></i>Voltar
    </a>
    <h2 class="mb-1">
        <i class="bi bi-funnel me-2"></i><?php echo $isEdit ? 'Editar' : 'Nova'; ?> Audiência
    </h2>
    <p class="text-muted mb-0">Crie segmentações personalizadas de leads</p>
</div>

<div class="row">
    <!-- Formulário -->
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">
                <h5 class="card-title mb-4">Informações Básicas</h5>

                <div class="mb-3">
                    <label for="nome" class="form-label">Nome da Audiência *</label>
                    <input type="text" class="form-control" id="nome" placeholder="Ex: Leads Qualificados Facebook"
                        value="<?php echo $audiencia ? htmlspecialchars($audiencia['nome']) : ''; ?>" required>
                </div>

                <div class="mb-3">
                    <label for="descricao" class="form-label">Descrição</label>
                    <textarea class="form-control" id="descricao" rows="2"
                        placeholder="Descreva o objetivo desta audiência"><?php echo $audiencia ? htmlspecialchars($audiencia['descricao']) : ''; ?></textarea>
                </div>

                <div class="mb-4">
                    <label class="form-label">Tipo de Audiência *</label>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="tipo" id="tipoDinamica" value="dinamica"
                            <?php echo (!$audiencia || $audiencia['tipo'] === 'dinamica') ? 'checked' : ''; ?>>
                        <label class="form-check-label" for="tipoDinamica">
                            <strong>Dinâmica</strong> - Atualiza automaticamente conforme novos leads atendem aos
                            critérios
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="tipo" id="tipoEstatica" value="estatica"
                            <?php echo ($audiencia && $audiencia['tipo'] === 'estatica') ? 'checked' : ''; ?>>
                        <label class="form-check-label" for="tipoEstatica">
                            <strong>Estática</strong> - Congela a lista de leads no momento da criação
                        </label>
                    </div>
                </div>

                <hr class="my-4">

                <h5 class="card-title mb-3">Filtros</h5>

                <!-- Templates de Filtros -->
                <div class="mb-4">
                    <label class="form-label small text-muted">Templates Rápidos</label>
                    <div class="filter-templates">
                        <div class="template-card" onclick="aplicarTemplate('novos_30_dias')">
                            <div class="template-icon">
                                <i class="bi bi-clock-history"></i>
                            </div>
                            <div class="template-name">Leads Novos (30 dias)</div>
                        </div>
                        <div class="template-card" onclick="aplicarTemplate('qualificados')">
                            <div class="template-icon">
                                <i class="bi bi-star-fill"></i>
                            </div>
                            <div class="template-name">Leads Qualificados</div>
                        </div>
                        <div class="template-card" onclick="aplicarTemplate('sem_email')">
                            <div class="template-icon">
                                <i class="bi bi-envelope-x"></i>
                            </div>
                            <div class="template-name">Sem Email</div>
                        </div>
                        <div class="template-card" onclick="aplicarTemplate('facebook')">
                            <div class="template-icon">
                                <i class="bi bi-facebook"></i>
                            </div>
                            <div class="template-name">Origem Facebook</div>
                        </div>
                    </div>
                </div>

                <!-- Condição Global -->
                <div class="condition-selector">
                    <span class="text-muted small">Leads devem atender:</span>
                    <select class="form-select form-select-sm w-auto" id="condicaoGlobal">
                        <option value="AND">TODOS os filtros</option>
                        <option value="OR">QUALQUER filtro</option>
                    </select>
                    <span class="condition-badge" id="condicaoBadge">AND</span>
                </div>

                <!-- Container de Filtros -->
                <div class="filter-builder" id="filtrosContainer">
                    <!-- Filtros serão adicionados aqui -->
                </div>

                <button type="button" class="add-filter-btn" onclick="adicionarFiltro()">
                    <i class="bi bi-plus-circle"></i>Adicionar Filtro
                </button>

                <hr class="my-4">

                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-primary" onclick="salvarAudiencia()">
                        <i class="bi bi-save me-2"></i>
                        <?php echo $isEdit ? 'Salvar Alterações' : 'Criar Audiência'; ?>
                    </button>
                    <a href="index.php" class="btn btn-secondary">Cancelar</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Preview -->
    <div class="col-lg-4">
        <div class="preview-card">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-0">
                    <!-- Contador -->
                    <div class="preview-counter">
                        <div class="preview-counter-number" id="totalPreview">0</div>
                        <div class="preview-counter-label">Leads encontrados</div>
                    </div>

                    <!-- Lista de Leads -->
                    <div class="p-3" id="previewContainer">
                        <div class="preview-empty">
                            <i class="bi bi-funnel"></i>
                            <p class="mb-0">Adicione filtros para ver o preview</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<input type="hidden" id="audienciaId" value="<?php echo $audiencia ? $audiencia['id'] : '0'; ?>">

<?php require_once __DIR__ . '/../../../../includes/footer.php'; ?>

<script>
    let filtroCount = 0;
    let previewTimeout;

    const camposDisponiveis = {
        'nome': { label: 'Nome', operadores: ['LIKE', '=', '!='] },
        'email': { label: 'Email', operadores: ['LIKE', '=', '!=', 'IS NULL', 'IS NOT NULL'] },
        'telefone': { label: 'Telefone', operadores: ['LIKE', '=', '!=', 'IS NULL', 'IS NOT NULL'] },
        'origem': { label: 'Origem', operadores: ['=', '!=', 'IN', 'NOT IN'] },
        'status': { label: 'Status', operadores: ['=', '!=', 'IN', 'NOT IN'] },
        'created_at': { label: 'Data de Criação', operadores: ['=', '>', '<', '>=', '<='] }
    };

    const operadoresLabels = {
        '=': 'Igual a',
        '!=': 'Diferente de',
        'LIKE': 'Contém',
        'IN': 'Está em',
        'NOT IN': 'Não está em',
        '>': 'Maior que',
        '<': 'Menor que',
        '>=': 'Maior ou igual a',
        '<=': 'Menor ou igual a',
        'IS NULL': 'Está vazio',
        'IS NOT NULL': 'Não está vazio'
    };

    $(document).ready(function () {
        <?php if ($audiencia && $audiencia['filtros_json']): ?>
            const filtrosExistentes = <?php echo $audiencia['filtros_json']; ?>;
            if (filtrosExistentes.condicao) {
                $('#condicaoGlobal').val(filtrosExistentes.condicao);
                $('#condicaoBadge').text(filtrosExistentes.condicao);
            }
            if (filtrosExistentes.filtros && filtrosExistentes.filtros.length > 0) {
                filtrosExistentes.filtros.forEach(filtro => {
                    adicionarFiltro(filtro);
                });
            }
        <?php else: ?>
            adicionarFiltro();
        <?php endif; ?>

        $('#condicaoGlobal').change(function () {
            const condicao = $(this).val();
            $('#condicaoBadge').text(condicao);
            $('.filter-connector-badge').text(condicao);
            atualizarPreview();
        });
    });

    function adicionarFiltro(dadosFiltro = null) {
        filtroCount++;
        const id = `filtro_${filtroCount}`;

        let camposOptions = '';
        for (const [campo, config] of Object.entries(camposDisponiveis)) {
            const selected = dadosFiltro && dadosFiltro.campo === campo ? 'selected' : '';
            camposOptions += `<option value="${campo}" ${selected}>${config.label}</option>`;
        }

        const html = `
        <div class="filter-card" id="${id}">
            <div class="filter-header">
                <div class="filter-number">${filtroCount}</div>
                <button type="button" class="filter-remove" onclick="removerFiltro('${id}')">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
            
            <div class="row g-2">
                <div class="col-md-4">
                    <label class="form-label small">Campo</label>
                    <select class="form-select form-select-sm campo-select" data-filtro-id="${id}" onchange="atualizarOperadores('${id}')">
                        ${camposOptions}
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label small">Operador</label>
                    <select class="form-select form-select-sm operador-select" data-filtro-id="${id}" onchange="atualizarCampoValor('${id}')">
                        <!-- Preenchido via JS -->
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label small">Valor</label>
                    <input type="text" class="form-control form-control-sm valor-input" data-filtro-id="${id}" placeholder="Digite o valor">
                </div>
            </div>
        </div>
    `;

        $('#filtrosContainer').append(html);

        // Atualizar operadores para o campo selecionado
        atualizarOperadores(id);

        // Se tem dados do filtro, preencher
        if (dadosFiltro) {
            $(`#${id} .operador-select`).val(dadosFiltro.operador);
            atualizarCampoValor(id);

            if (Array.isArray(dadosFiltro.valor)) {
                $(`#${id} .valor-input`).val(dadosFiltro.valor.join(', '));
            } else {
                $(`#${id} .valor-input`).val(dadosFiltro.valor);
            }
        }

        // Adicionar evento de mudança para atualizar preview
        $(`#${id} .campo-select, #${id} .operador-select, #${id} .valor-input`).on('change keyup', atualizarPreview);

        // Adicionar conector visual se não for o primeiro filtro
        if (filtroCount > 1) {
            adicionarConector(id);
        }
    }

    function removerFiltro(id) {
        $(`#${id}`).remove();
        atualizarPreview();
    }

    function atualizarOperadores(filtroId) {
        const campo = $(`#${filtroId} .campo-select`).val();
        const config = camposDisponiveis[campo];

        let options = '';
        config.operadores.forEach(op => {
            options += `<option value="${op}">${operadoresLabels[op]}</option>`;
        });

        $(`#${filtroId} .operador-select`).html(options);
        atualizarCampoValor(filtroId);
    }

    function atualizarCampoValor(filtroId) {
        const operador = $(`#${filtroId} .operador-select`).val();
        const valorInput = $(`#${filtroId} .valor-input`);

        if (operador === 'IS NULL' || operador === 'IS NOT NULL') {
            valorInput.prop('disabled', true).val('');
        } else if (operador === 'IN' || operador === 'NOT IN') {
            valorInput.prop('disabled', false).attr('placeholder', 'Valores separados por vírgula');
        } else {
            valorInput.prop('disabled', false).attr('placeholder', 'Digite o valor');
        }

        atualizarPreview();
    }

    function coletarFiltros() {
        const filtros = [];

        $('.filter-card').each(function () {
            const id = $(this).attr('id');
            const campo = $(`#${id} .campo-select`).val();
            const operador = $(`#${id} .operador-select`).val();
            let valor = $(`#${id} .valor-input`).val();

            // Converter valor para array se operador for IN ou NOT IN
            if ((operador === 'IN' || operador === 'NOT IN') && valor) {
                valor = valor.split(',').map(v => v.trim());
            }

            // Só adicionar se tiver valor (exceto para IS NULL/IS NOT NULL)
            if (valor || operador === 'IS NULL' || operador === 'IS NOT NULL') {
                filtros.push({ campo, operador, valor });
            }
        });

        return {
            condicao: $('#condicaoGlobal').val(),
            filtros: filtros
        };
    }

    function atualizarPreview() {
        clearTimeout(previewTimeout);
        previewTimeout = setTimeout(() => {
            const filtros = coletarFiltros();

            if (filtros.filtros.length === 0) {
                $('#totalPreview').text('0').removeClass('updating');
                $('#previewContainer').html(`
                    <div class="preview-empty">
                        <i class="bi bi-funnel"></i>
                        <p class="mb-0">Adicione filtros para ver o preview</p>
                    </div>
                `);
                return;
            }

            $.ajax({
                url: 'acoes.php?acao=preview',
                method: 'POST',
                contentType: 'application/json',
                data: JSON.stringify({ filtros }),
                dataType: 'json',
                success: function (response) {
                    if (response.success && response.data) {
                        $('#totalPreview').text(response.data.total).addClass('updating');
                        setTimeout(() => $('#totalPreview').removeClass('updating'), 500);

                        if (response.data.leads && response.data.leads.length > 0) {
                            let html = '';
                            response.data.leads.forEach(lead => {
                                const inicial = lead.nome.charAt(0).toUpperCase();
                                const email = lead.email || '';
                                const telefone = lead.telefone || '';
                                const contato = email || telefone || 'Sem contato';

                                html += `
                                <div class="preview-lead-card">
                                    <div class="d-flex align-items-center">
                                        <div class="preview-lead-avatar">${inicial}</div>
                                        <div class="flex-grow-1 ms-3">
                                            <div class="fw-semibold small">${lead.nome}</div>
                                            <div class="text-muted" style="font-size: 0.75rem;">${contato}</div>
                                        </div>
                                    </div>
                                </div>
                            `;
                            });

                            if (response.data.total > 10) {
                                html += `<p class="text-muted text-center small mt-3 mb-0">E mais ${response.data.total - 10} leads...</p>`;
                            }

                            $('#previewContainer').html(html);
                        } else {
                            $('#previewContainer').html(`
                                <div class="preview-empty">
                                    <i class="bi bi-inbox"></i>
                                    <p class="mb-0">Nenhum lead atende aos critérios</p>
                                </div>
                            `);
                        }
                    }
                }
            });
        }, 500); // Debounce de 500ms
    }

    function salvarAudiencia() {
        const nome = $('#nome').val().trim();
        const descricao = $('#descricao').val().trim();
        const tipo = $('input[name="tipo"]:checked').val();
        const filtros = coletarFiltros();
        const id = parseInt($('#audienciaId').val()) || 0;

        if (!nome) {
            alert('Por favor, preencha o nome da audiência');
            return;
        }

        if (filtros.filtros.length === 0) {
            alert('Por favor, adicione pelo menos um filtro');
            return;
        }

        const dados = { nome, descricao, tipo, filtros };
        if (id > 0) dados.id = id;

        $.ajax({
            url: 'acoes.php?acao=salvar',
            method: 'POST',
            contentType: 'application/json',
            data: JSON.stringify(dados),
            dataType: 'json',
            success: function (response) {
                if (response.success) {
                    window.location.href = 'index.php';
                } else {
                    alert('Erro ao salvar: ' + (response.message || 'Erro desconhecido'));
                    console.error('Erro do servidor:', response);
                }
            },
            error: function (xhr, status, error) {
                console.error('Erro AJAX:', xhr.responseText);
                alert('Erro ao salvar audiência: ' + error + '\nVerifique o console para mais detalhes.');
            }
        });
    }
</script>

<?php require_once __DIR__ . '/../../../../includes/footer.php'; ?>

<script>
    // Funções de templates (após jQuery estar carregado)
    window.aplicarTemplate = function (tipo) {
        $('#filtrosContainer').empty();
        filtroCount = 0;

        const templates = {
            'novos_30_dias': {
                condicao: 'AND',
                filtros: [{ campo: 'created_at', operador: '>=', valor: getDataMenos30Dias() }]
            },
            'qualificados': {
                condicao: 'AND',
                filtros: [{ campo: 'status', operador: '=', valor: 'qualificado' }]
            },
            'sem_email': {
                condicao: 'AND',
                filtros: [{ campo: 'email', operador: 'IS NULL', valor: '' }]
            },
            'facebook': {
                condicao: 'AND',
                filtros: [{ campo: 'origem', operador: '=', valor: 'Facebook' }]
            }
        };

        const template = templates[tipo];
        if (template) {
            $('#condicaoGlobal').val(template.condicao);
            $('#condicaoBadge').text(template.condicao);
            template.filtros.forEach(filtro => adicionarFiltro(filtro));
            atualizarPreview();
        }
    };

    function getDataMenos30Dias() {
        const data = new Date();
        data.setDate(data.getDate() - 30);
        return data.toISOString().split('T')[0];
    }

    window.adicionarConector = function (filtroId) {
        const condicao = $('#condicaoGlobal').val();
        const conector = `
        <div class="filter-connector" data-conector="${filtroId}">
            <span class="filter-connector-badge">${condicao}</span>
        </div>
    `;
        $(`#${filtroId}`).before(conector);
    };
</script>