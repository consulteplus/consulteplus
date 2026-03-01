<?php
$pageTitle = "Configurações do Sistema";
require_once __DIR__ . '/../../includes/header.php';

checkPermission(['admin']);

// --- LÓGICA DE BACKEND UNIFICADA ---

// 1. Processar Formulário GERAL (Clínica, Sistema, Backup, Agenda Interna)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['form_type']) && $_POST['form_type'] == 'geral') {
    $configs = $_POST['config'] ?? [];
    $company_id = $_SESSION['company_id'];
    foreach ($configs as $chave => $valor) {
        // Tenta UPDATE primeiro
        $stmt = $conn->prepare("UPDATE configuracoes SET valor = ? WHERE chave = ? AND company_id = ?");
        $stmt->bind_param("ssi", $valor, $chave, $company_id);
        $stmt->execute();

        // Se não afetou linhas, tenta INSERT (caso seja nova config para esta empresa)
        if ($stmt->affected_rows === 0) {
            // Verifica se a chave existe (pode ser 0 rows se valor for igual)
            $check = $conn->query("SELECT id FROM configuracoes WHERE chave = '$chave' AND company_id = $company_id");
            if ($check->num_rows === 0) {
                // Precisamos do grupo. Como não vem no POST, teremos que inferir ou buscar defaults.
                // Simplificação: Assumimos que a chave tem prefixo ou definimos grupo 'geral' como fallback
                // O ideal seria passar o grupo no hidden, mas vamos usar ON DUPLICATE que resolve tudo se tivessemos todos os campos

                // Workaround: Inserir com grupo 'clinica' se começar com clinica_, etc.
                $grupo = 'sistema';
                if (strpos($chave, 'clinica_') === 0)
                    $grupo = 'clinica';
                if (strpos($chave, 'agendamento_') === 0)
                    $grupo = 'agendamento';
                if (strpos($chave, 'backup_') === 0)
                    $grupo = 'backup';

                $stmtIns = $conn->prepare("INSERT INTO configuracoes (chave, valor, grupo, company_id) VALUES (?, ?, ?, ?)");
                $stmtIns->bind_param("sssi", $chave, $valor, $grupo, $company_id);
                $stmtIns->execute();
            }
        }
    }
    $_SESSION['success'] = "Configurações gerais salvas com sucesso!";
    // Manter na aba atual via JS (opcional) ou reload
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

// 2. Processar Formulário N8N
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['form_type']) && $_POST['form_type'] == 'n8n') {
    $webhook_url = sanitize($_POST['webhook_url']);
    $api_token = sanitize($_POST['api_token']);
    $ativo = isset($_POST['ativo']) ? 1 : 0;

    $config_file = __DIR__ . '/../../config/n8n_config.json';
    $config = [
        'webhook_url' => $webhook_url,
        'roadmap_webhook_url' => sanitize($_POST['roadmap_webhook_url']),
        'analysis_webhook_url' => sanitize($_POST['analysis_webhook_url']),
        'api_token' => $api_token,
        'ativo' => $ativo,
        'updated_at' => date('Y-m-d H:i:s')
    ];

    if (file_put_contents($config_file, json_encode($config, JSON_PRETTY_PRINT))) {
        $_SESSION['success'] = "Configurações de Integração salvas!";
    } else {
        $_SESSION['error'] = "Erro ao salvar JSON n8n.";
    }
    header("Location: " . $_SERVER['PHP_SELF'] . "#n8n");
    exit;
}

// 3. Processar Formulário Agendamento Online (Somente update simples por enquanto)
// Nota: O modal original usava AJAX ou post separado. Aqui simplificamos ou mantemos estrutura.
// Se o POST vier do modal de Agendamento Online:
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['form_type']) && $_POST['form_type'] == 'agendamento_online') {
    $profissional_id = $_POST['profissional_id'];
    $ativo_online = isset($_POST['ativo_online']) ? 1 : 0;
    $slug_url = $_POST['slug_url'];
    $telemedicina_ativa = isset($_POST['telemedicina_ativa']) ? 1 : 0;
    $link_telemedicina = $_POST['link_telemedicina'];
    $sobre_mim = $_POST['sobre_mim'];
    $formacao_academica = $_POST['formacao_academica'];

    // Verificar existência
    $company_id = $_SESSION['company_id']; // Usuários são filtrados depois, mas precisamos garantir integridade

    // A query_prof já filtra usuários da empresa. O profissional_id vem do POST.
    // Validar se profissional pertence à empresa
    $valid = $conn->query("SELECT id FROM users WHERE id = (SELECT user_id FROM profissionais WHERE id = $profissional_id) AND company_id = $company_id");
    if ($valid->num_rows === 0)
        die("Acesso negado.");

    $stmt = $conn->prepare("SELECT id FROM config_agendamento_online WHERE profissional_id = ?");
    $stmt->bind_param("i", $profissional_id);
    $stmt->execute();
    $exists = $stmt->get_result()->num_rows > 0;

    if ($exists) {
        $query = "UPDATE config_agendamento_online SET 
                  ativo_online=?, slug_url=?, telemedicina_ativa=?, link_telemedicina=?, sobre_mim=?, formacao_academica=? 
                  WHERE profissional_id=?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("isisssi", $ativo_online, $slug_url, $telemedicina_ativa, $link_telemedicina, $sobre_mim, $formacao_academica, $profissional_id);
    } else {
        $query = "INSERT INTO config_agendamento_online 
                  (ativo_online, slug_url, telemedicina_ativa, link_telemedicina, sobre_mim, formacao_academica, profissional_id) 
                  VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("isisssi", $ativo_online, $slug_url, $telemedicina_ativa, $link_telemedicina, $sobre_mim, $formacao_academica, $profissional_id);
    }

    if ($stmt->execute()) {
        $_SESSION['success'] = "Profissional atualizado!";
    } else {
        $_SESSION['error'] = "Erro BD: " . $conn->error;
    }
    header("Location: " . $_SERVER['PHP_SELF'] . "#agendamento-online");
    exit;
}

// --- CARREGAMENTO DE DADOS ---

// 1. Configs Gerais do BD
function getConfigsByGroup($conn, $grupo)
{
    $company_id = $_SESSION['company_id'];
    $stmt = $conn->prepare("SELECT * FROM configuracoes WHERE grupo = ? AND company_id = ? ORDER BY chave");
    $stmt->bind_param("si", $grupo, $company_id);
    $stmt->execute();
    return $stmt->get_result();
}
$configs_clinica = getConfigsByGroup($conn, 'clinica');
$configs_agendamento = getConfigsByGroup($conn, 'agendamento');
$configs_sistema = getConfigsByGroup($conn, 'sistema');
$configs_backup = getConfigsByGroup($conn, 'backup');

// 2. Configs N8N
$n8n_file = __DIR__ . '/../../config/n8n_config.json';
$n8n_defaults = ['webhook_url' => '', 'roadmap_webhook_url' => '', 'analysis_webhook_url' => '', 'api_token' => '', 'ativo' => 0];
$n8n_config = file_exists($n8n_file) ? array_merge($n8n_defaults, json_decode(file_get_contents($n8n_file), true)) : $n8n_defaults;

// 3. Agendamento Online (Lista Profissionais)
$query_prof = "
    SELECT p.id, u.nome, e.nome as especialidade, c.ativo_online, c.slug_url, 
           c.telemedicina_ativa, c.link_telemedicina, c.sobre_mim, c.formacao_academica
    FROM profissionais p 
    JOIN users u ON p.user_id = u.id 
    JOIN especialidades e ON p.especialidade_id = e.id
    LEFT JOIN config_agendamento_online c ON p.id = c.profissional_id
    WHERE p.ativo = 1 AND u.company_id = " . $_SESSION['company_id'] . "
    ORDER BY u.nome
";
$result_prof = $conn->query($query_prof);

// 4. Especialidades (Lista)
$result_esp = $conn->query("SELECT id, nome, descricao FROM especialidades ORDER BY nome");
?>

<div class="page-header">
    <h1><i class="bi bi-gear me-2"></i>Configurações do Sistema</h1>
</div>

<!-- MENU UNIFICADO (ABAS) -->
<ul class="nav nav-tabs mb-4" id="masterTabs" role="tablist">
    <li class="nav-item"><button class="nav-link active" data-bs-toggle="tab" data-bs-target="#clinica"><i
                class="bi bi-building me-2"></i>Clínica</button></li>
    <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#agenda-interna"><i
                class="bi bi-calendar-check me-2"></i>Agenda Interna</button></li>
    <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#agendamento-online"><i
                class="bi bi-globe me-2"></i>Agendamento Online</button></li>
    <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#especialidades"><i
                class="bi bi-list-ul me-2"></i>Especialidades</button></li>
    <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#integracoes"><i
                class="bi bi-link-45deg me-2"></i>Integrações</button></li>
</ul>

<div class="tab-content" id="masterTabsContent">

    <!-- ABA CLÍNICA (Geral) -->
    <div class="tab-pane fade show active" id="clinica">
        <form method="POST">
            <input type="hidden" name="form_type" value="geral">
            <div class="card">
                <div class="card-header"><i class="bi bi-building me-2"></i>Informações da Clínica</div>
                <div class="card-body">
                    <div class="row">
                        <?php while ($config = $configs_clinica->fetch_assoc()): ?>
                            <div class="col-md-6 mb-3">
                                <label
                                    class="form-label"><?php echo ucfirst(str_replace(['clinica_', '_'], ['', ' '], $config['chave'])); ?></label>
                                <input type="text" class="form-control" name="config[<?php echo $config['chave']; ?>]"
                                    value="<?php echo htmlspecialchars($config['valor']); ?>">
                            </div>
                        <?php endwhile; ?>
                    </div>
                    <button type="submit" class="btn btn-primary mt-3">Salvar Alterações</button>
                </div>
            </div>
        </form>
    </div>

    <!-- ABA AGENDA INTERNA -->
    <div class="tab-pane fade" id="agenda-interna">
        <form method="POST">
            <input type="hidden" name="form_type" value="geral">
            <div class="card">
                <div class="card-header"><i class="bi bi-calendar-check me-2"></i>Regras de Agendamento (Interno)</div>
                <div class="card-body">
                    <div class="row">
                        <?php while ($config = $configs_agendamento->fetch_assoc()): ?>
                            <div class="col-md-6 mb-3">
                                <label
                                    class="form-label"><?php echo ucfirst(str_replace(['agendamento_', '_'], ['', ' '], $config['chave'])); ?></label>
                                <input type="text" class="form-control" name="config[<?php echo $config['chave']; ?>]"
                                    value="<?php echo htmlspecialchars($config['valor']); ?>">
                            </div>
                        <?php endwhile; ?>
                    </div>
                    <button type="submit" class="btn btn-primary mt-3">Salvar Alterações</button>
                </div>
            </div>
        </form>
    </div>

    <!-- ABA AGENDA ONLINE (MÓDULO COMPLETO MERGEADO) -->
    <div class="tab-pane fade" id="agendamento-online">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4><i class="bi bi-globe me-2"></i>Módulo Agendamento Web</h4>
                <p class="text-muted">Gerencie quem aparece no site público.</p>
            </div>
            <a href="<?php echo BASE_URL; ?>agendar" target="_blank" class="btn btn-outline-primary"><i
                    class="bi bi-box-arrow-up-right me-2"></i>Ver Site Público</a>
        </div>
        <div class="card shadow-sm">
            <div class="card-body p-0">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">Profissional</th>
                            <th>Especialidade</th>
                            <th>Status</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $result_prof->fetch_assoc()): ?>
                            <tr>
                                <td class="ps-4 fw-bold"><?php echo $row['nome']; ?></td>
                                <td><span
                                        class="badge bg-light text-dark border"><?php echo $row['especialidade']; ?></span>
                                </td>
                                <td>
                                    <?php if ($row['ativo_online']): ?><span
                                            class="badge bg-success">Ativo</span><?php else: ?><span
                                            class="badge bg-secondary">Inativo</span><?php endif; ?>
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-primary" data-bs-toggle="modal"
                                        data-bs-target="#modalConfigProf" data-id="<?php echo $row['id']; ?>"
                                        data-nome="<?php echo $row['nome']; ?>"
                                        data-ativo="<?php echo $row['ativo_online']; ?>"
                                        data-slug="<?php echo $row['slug_url']; ?>"
                                        data-telemed="<?php echo $row['telemedicina_ativa']; ?>"
                                        data-link="<?php echo $row['link_telemedicina']; ?>"
                                        data-sobre="<?php echo htmlspecialchars($row['sobre_mim'] ?? ''); ?>"
                                        data-formacao="<?php echo htmlspecialchars($row['formacao_academica'] ?? ''); ?>">
                                        CONFIGURAR
                                    </button>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- ABA ESPECIALIDADES (VISUALIZAÇÃO) -->
    <div class="tab-pane fade" id="especialidades">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-list-ul me-2"></i>Lista de Especialidades</span>
                <a href="<?php echo BASE_URL; ?>especialidades/novo" class="btn btn-sm btn-success"><i
                        class="bi bi-plus-circle me-1"></i>Nova</a>
            </div>
            <div class="card-body p-0">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Nome</th>
                            <th>Descrição</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($esp = $result_esp->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($esp['nome']); ?></td>
                                <td><?php echo htmlspecialchars($esp['descricao']); ?></td>
                                <td>
                                    <a href="<?php echo BASE_URL; ?>especialidades/editar/<?php echo $esp['id']; ?>"
                                        class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
                                    <a href="<?php echo BASE_URL; ?>especialidades/excluir/<?php echo $esp['id']; ?>"
                                        class="btn btn-sm btn-outline-danger" onclick="return confirm('Excluir?');"><i
                                            class="bi bi-trash"></i></a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- ABA INTEGRAÇÕES (N8N) -->
    <div class="tab-pane fade" id="integracoes">
        <form method="POST">
            <input type="hidden" name="form_type" value="n8n">
            <div class="card">
                <div class="card-header"><i class="bi bi-link-45deg me-2"></i>Webhooks & API</div>
                <div class="card-body">
                    <div class="mb-3">
                        <label>URL do Webhook (Notificações)</label>
                        <input type="url" class="form-control" name="webhook_url"
                            value="<?php echo htmlspecialchars($n8n_config['webhook_url']); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label>URL do Webhook (Roadmap IA)</label>
                        <input type="url" class="form-control" name="roadmap_webhook_url"
                            value="<?php echo htmlspecialchars($n8n_config['roadmap_webhook_url'] ?? ''); ?>">
                    </div>
                    <div class="mb-3">
                        <label>URL do Webhook (Análise Executiva IA)</label>
                        <input type="url" class="form-control" name="analysis_webhook_url"
                            value="<?php echo htmlspecialchars($n8n_config['analysis_webhook_url'] ?? ''); ?>">
                    </div>
                    <div class="mb-3">
                        <label>API Token</label>
                        <input type="text" class="form-control" name="api_token"
                            value="<?php echo htmlspecialchars($n8n_config['api_token']); ?>">
                    </div>
                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox" id="ativoN8n" name="ativo" <?php echo $n8n_config['ativo'] ? 'checked' : ''; ?>>
                        <label class="form-check-label" for="ativoN8n">Integração Ativa</label>
                    </div>
                    <button type="submit" class="btn btn-primary">Salvar Integração</button>
                    <button type="button" class="btn btn-success ms-2" id="btnTestarN8n">Testar Conexão</button>
                    <div id="resN8n" class="mt-2"></div>
                </div>
            </div>
        </form>
    </div>



</div>

<!-- Modal Config Profissional (Agenda Online) -->
<div class="modal fade" id="modalConfigProf" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form method="POST" class="modal-content">
            <input type="hidden" name="form_type" value="agendamento_online">
            <input type="hidden" name="profissional_id" id="prof_id">
            <div class="modal-header">
                <h5 class="modal-title">Configurar Profissional</h5><button type="button" class="btn-close"
                    data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <h6 id="prof_nome_display" class="text-primary"></h6>
                <div class="form-check form-switch mb-3 custom-switch">
                    <input class="form-check-input" type="checkbox" id="ativo_online" name="ativo_online">
                    <label class="form-check-label">Habilitar no Site</label>
                </div>
                <label>Slug URL</label><input type="text" class="form-control mb-2" id="slug_url" name="slug_url">
                <label>Telemedicina Link</label><input type="text" class="form-control mb-2" id="link_telemedicina"
                    name="link_telemedicina">
                <div class="form-check form-switch mb-2">
                    <input class="form-check-input" type="checkbox" id="telemedicina_ativa" name="telemedicina_ativa">
                    <label>Aceita Telemedicina</label>
                </div>
                <label>Sobre</label><textarea class="form-control mb-2" id="sobre_mim" name="sobre_mim"></textarea>
                <label>Formação</label><textarea class="form-control mb-2" id="formacao_academica"
                    name="formacao_academica"></textarea>
            </div>
            <div class="modal-footer"><button type="submit" class="btn btn-primary">Salvar</button></div>
        </form>
    </div>
</div>

<script>
    // JS para Modal Agenda Online
    document.getElementById('modalConfigProf').addEventListener('show.bs.modal', function (event) {
        const btn = event.relatedTarget;
        document.getElementById('prof_id').value = btn.getAttribute('data-id');
        document.getElementById('prof_nome_display').textContent = btn.getAttribute('data-nome');
        document.getElementById('slug_url').value = btn.getAttribute('data-slug');
        document.getElementById('link_telemedicina').value = btn.getAttribute('data-link');
        document.getElementById('sobre_mim').value = btn.getAttribute('data-sobre');
        document.getElementById('formacao_academica').value = btn.getAttribute('data-formacao');
        document.getElementById('ativo_online').checked = btn.getAttribute('data-ativo') == '1';
        document.getElementById('telemedicina_ativa').checked = btn.getAttribute('data-telemed') == '1';
    });

    // JS Teste N8N
    document.getElementById('btnTestarN8n').addEventListener('click', function () {
        fetch('<?php echo BASE_URL; ?>api/n8n/test.php', { method: 'POST' })
            .then(r => r.json()).then(d => {
                document.getElementById('resN8n').innerHTML = d.success
                    ? '<span class="text-success">' + d.message + '</span>'
                    : '<span class="text-danger">' + d.message + '</span>';
            });
    });

    // Lembrar Aba Ativa via Hash
    var urlHeader = document.location.toString();
    if (urlHeader.match('#')) {
        var activeTab = document.querySelector('.nav-link[data-bs-target="#' + urlHeader.split('#')[1] + '"]');
        if (activeTab) { new bootstrap.Tab(activeTab).show(); }
    }
</script>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>