<?php
$pageTitle = "Configuração Agendamento Online";
require_once __DIR__ . '/../../includes/header.php';

checkPermission(['admin', 'secretaria']);

// Salvar Configurações
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $profissional_id = $_POST['profissional_id'];
    $ativo_online = isset($_POST['ativo_online']) ? 1 : 0;
    $slug_url = $_POST['slug_url'];
    $telemedicina_ativa = isset($_POST['telemedicina_ativa']) ? 1 : 0;
    $link_telemedicina = $_POST['link_telemedicina'];
    $sobre_mim = $_POST['sobre_mim'];
    $formacao_academica = $_POST['formacao_academica'];

    // Verificar se já existe config para este profissional
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
        $_SESSION['success'] = "Configurações atualizadas com sucesso!";
    } else {
        $_SESSION['error'] = "Erro ao salvar: " . $conn->error;
    }

    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

// Listar Profissionais
$query = "
    SELECT p.id, u.nome, e.nome as especialidade, c.ativo_online, c.slug_url, 
           c.telemedicina_ativa, c.link_telemedicina, c.sobre_mim, c.formacao_academica
    FROM profissionais p 
    JOIN users u ON p.user_id = u.id 
    JOIN especialidades e ON p.especialidade_id = e.id
    LEFT JOIN config_agendamento_online c ON p.id = c.profissional_id
    WHERE p.ativo = 1
    ORDER BY u.nome
";
$result = $conn->query($query);
?>

<div class="page-header">
    <h1><i class="bi bi-gear me-2"></i>Configurações do Sistema</h1>
</div>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4><i class="bi bi-globe me-2"></i>Agendamento Online</h4>
        <p class="text-muted">Gerencie quem aparece no site e suas configurações.</p>
    </div>
    <a href="<?php echo BASE_URL; ?>agendar" target="_blank" class="btn btn-outline-primary">
        <i class="bi bi-box-arrow-up-right me-2"></i>Ver Site Público
    </a>
</div>

<div class="card mb-4 border-0">
    <div class="card-body p-0">
        <ul class="nav nav-tabs border-bottom-0">
            <li class="nav-item">
                <a class="nav-link" href="<?php echo BASE_URL; ?>configuracoes">
                    <i class="bi bi-sliders me-2"></i>Gerais
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link active" href="#">
                    <i class="bi bi-globe me-2"></i>Agendamento Online
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="<?php echo BASE_URL; ?>especialidades">
                    <i class="bi bi-list-ul me-2"></i>Especialidades
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="<?php echo BASE_URL; ?>configuracoes/n8n">
                    <i class="bi bi-link-45deg me-2"></i>Integrações
                </a>
            </li>
        </ul>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">Profissional</th>
                        <th>Especialidade</th>
                        <th>Status Online</th>
                        <th>Link Amigável</th>
                        <th class="text-end pe-4">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td class="ps-4">
                                <div class="fw-bold"><?php echo $row['nome']; ?></div>
                            </td>
                            <td><span class="badge bg-light text-dark border"><?php echo $row['especialidade']; ?></span>
                            </td>
                            <td>
                                <?php if ($row['ativo_online']): ?>
                                    <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>Ativo</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary">Inativo</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($row['slug_url']): ?>
                                    <small class="text-muted">/agendar/<?php echo $row['slug_url']; ?></small>
                                <?php else: ?>
                                    <span class="text-muted">-</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-end pe-4">
                                <button class="btn btn-sm btn-primary btn-edit" data-bs-toggle="modal"
                                    data-bs-target="#modalConfig" data-id="<?php echo $row['id']; ?>"
                                    data-nome="<?php echo $row['nome']; ?>" data-ativo="<?php echo $row['ativo_online']; ?>"
                                    data-slug="<?php echo $row['slug_url']; ?>"
                                    data-telemed="<?php echo $row['telemedicina_ativa']; ?>"
                                    data-link="<?php echo $row['link_telemedicina']; ?>"
                                    data-sobre="<?php echo htmlspecialchars($row['sobre_mim'] ?? ''); ?>"
                                    data-formacao="<?php echo htmlspecialchars($row['formacao_academica'] ?? ''); ?>">
                                    <i class="bi bi-gear-fill me-1"></i>Configurar
                                </button>
                                <?php if ($row['ativo_online']): ?>
                                    <a href="<?php echo BASE_URL; ?>agendar/<?php echo $row['slug_url']; ?>" target="_blank"
                                        class="btn btn-sm btn-outline-secondary" title="Ver Perfil">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Configuração -->
<div class="modal fade" id="modalConfig" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form method="POST" class="modal-content">
            <input type="hidden" name="profissional_id" id="prof_id">

            <div class="modal-header">
                <h5 class="modal-title">Configurar Agendamento Online</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <h6 class="text-primary mb-3" id="prof_nome_display"></h6>

                <div class="row g-3">
                    <div class="col-md-12">
                        <div class="form-check form-switch ps-5 py-2 border rounded bg-light">
                            <input class="form-check-input" type="checkbox" id="ativo_online" name="ativo_online">
                            <label class="form-check-label fw-bold" for="ativo_online">Habilitar Agendamento
                                Online</label>
                            <div class="form-text">Se desmarcado, o médico não aparecerá na lista pública.</div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">URL Amigável (Slug)</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light">/agendar/</span>
                            <input type="text" class="form-control" name="slug_url" id="slug_url"
                                placeholder="dr-joao-silva">
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Telemedicina</label>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="telemedicina_ativa"
                                name="telemedicina_ativa">
                            <label class="form-check-label" for="telemedicina_ativa">Aceita Online</label>
                        </div>
                        <input type="text" class="form-control mt-2" name="link_telemedicina" id="link_telemedicina"
                            placeholder="Link fixo (Zoom/Meet)...">
                    </div>

                    <div class="col-12 mt-4">
                        <hr>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Sobre Mim (Bio)</label>
                        <textarea class="form-control" name="sobre_mim" id="sobre_mim" rows="4"
                            placeholder="Breve descrição para o paciente..."></textarea>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Formação Acadêmica</label>
                        <textarea class="form-control" name="formacao_academica" id="formacao_academica" rows="4"
                            placeholder="Faculdade X, Residência Y..."></textarea>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-primary">Salvar Alterações</button>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const modal = document.getElementById('modalConfig');
        modal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;

            document.getElementById('prof_id').value = button.getAttribute('data-id');
            document.getElementById('prof_nome_display').textContent = button.getAttribute('data-nome');
            document.getElementById('slug_url').value = button.getAttribute('data-slug');
            document.getElementById('link_telemedicina').value = button.getAttribute('data-link');
            document.getElementById('sobre_mim').value = button.getAttribute('data-sobre');
            document.getElementById('formacao_academica').value = button.getAttribute('data-formacao');

            document.getElementById('ativo_online').checked = button.getAttribute('data-ativo') == '1';
            document.getElementById('telemedicina_ativa').checked = button.getAttribute('data-telemed') == '1';
        });
    });
</script>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>