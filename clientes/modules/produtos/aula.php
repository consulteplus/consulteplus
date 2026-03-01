<?php
// modules/produtos/aula.php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../includes/auth.php';

// session_start(); // Removido pois auth.php já inicia a sessão
checkPermission(['admin', 'cliente']);

$aula_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$user_id = $_SESSION['user_id'];

// 1. Buscar detalhes da aula atual
$stmt = $conn->prepare("
    SELECT c.*, t.produto_id, t.titulo as trilha_titulo, p.titulo as produto_titulo,
           d.titulo as diag_titulo,
           f.nome as tool_nome, f.slug as tool_slug, f.icone as tool_icone
    FROM mentoria_conteudos c
    JOIN mentoria_trilhas t ON c.trilha_id = t.id
    JOIN mentoria_produtos p ON t.produto_id = p.id
    LEFT JOIN gestao_diagnostico_modelos d ON c.resource_id = d.id AND c.tipo = 'diagnostico'
    LEFT JOIN ferramentas_tipos f ON c.resource_id = f.id AND c.tipo = 'ferramenta'
    WHERE c.id = ?
");
$stmt->bind_param("i", $aula_id);
$stmt->execute();
$aula = $stmt->get_result()->fetch_assoc();

if (!$aula) {
    die("Aula não encontrada.");
    header("Location: " . BASE_URL . "produtos");
    // Idealmente redirecionar para produtos/index
}

$produto_id = $aula['produto_id'];

// 2. Marcar como visto (Simples: acessou = visto)
// No futuro, pode ser via botão "Marcar como Concluída" via AJAX
$conn->query("INSERT IGNORE INTO mentoria_progresso (user_id, conteudo_id, concluido, data_conclusao) VALUES ($user_id, $aula_id, 1, NOW())");


// 3. Buscar estrutura completa para o Menu Lateral
$sqlEstrutura = "
    SELECT t.id as trilha_id, t.titulo as trilha_titulo, 
           c.id as aula_id, c.titulo as aula_titulo, c.tipo,
           (SELECT COUNT(*) FROM mentoria_progresso mp WHERE mp.user_id = $user_id AND mp.conteudo_id = c.id AND mp.concluido = 1) as concluido
    FROM mentoria_trilhas t
    JOIN mentoria_conteudos c ON t.id = c.trilha_id
    WHERE t.produto_id = $produto_id
    ORDER BY t.ordem ASC, c.ordem ASC
";
$resEstrutura = $conn->query($sqlEstrutura);

$estrutura = [];
while ($row = $resEstrutura->fetch_assoc()) {
    $estrutura[$row['trilha_id']]['titulo'] = $row['trilha_titulo'];
    $estrutura[$row['trilha_id']]['aulas'][] = $row;
}
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        <?php echo htmlspecialchars($aula['titulo']); ?> -
        <?php echo htmlspecialchars($aula['produto_titulo']); ?>
    </title>

    <!-- CSS (Mesmo do Header) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --sidebar-width: 350px;
            --primary-color: #4f46e5;
        }

        body {
            font-family: 'Outfit', sans-serif;
            background: #f8f9fa;
            overflow: hidden;
        }

        .player-layout {
            display: flex;
            height: 100vh;
        }

        .player-content {
            flex: 1;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
        }

        .player-sidebar {
            width: var(--sidebar-width);
            background: white;
            border-left: 1px solid #e5e7eb;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
        }

        /* Sidebar Styles */
        .sidebar-header {
            padding: 1.5rem;
            border-bottom: 1px solid #f0f0f0;
        }

        .module-header {
            padding: 1rem 1.5rem;
            background: #f8f9fa;
            font-weight: 600;
            font-size: 0.9rem;
            color: #6c757d;
            border-bottom: 1px solid #eee;
        }

        .lesson-item {
            display: flex;
            align-items: center;
            padding: 1rem 1.5rem;
            text-decoration: none;
            color: #333;
            border-bottom: 1px solid #fcfcfc;
            transition: all 0.2s;
        }

        .lesson-item:hover {
            background: #f8f9fa;
        }

        .lesson-item.active {
            background: #eef2ff;
            color: var(--primary-color);
            border-left: 3px solid var(--primary-color);
        }

        .lesson-icon {
            margin-right: 10px;
            font-size: 1.1rem;
        }

        .lesson-check {
            margin-left: auto;
            color: #10b981;
        }

        /* Video Area */
        .video-container {
            background: black;
            width: 100%;
            aspect-ratio: 16/9;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        iframe {
            width: 100%;
            height: 100%;
            border: 0;
        }

        /* Content Area */
        .content-body {
            padding: 2rem;
            max-width: 900px;
            margin: 0 auto;
            width: 100%;
        }

        @media (max-width: 991px) {
            .player-layout {
                flex-direction: column;
                overflow-y: auto;
            }

            .player-sidebar {
                width: 100%;
                height: auto;
                border-left: 0;
                border-top: 1px solid #eee;
            }

            .video-container {
                aspect-ratio: 16/9;
            }

            body {
                overflow: auto;
            }
        }
    </style>
</head>

<body>

    <div class="player-layout">
        <!-- ÁREA DE CONTEÚDO (ESQUERDA/TOPO) -->
        <div class="player-content">
            <!-- Navegação Topo -->
            <div class="bg-white px-4 py-3 border-bottom d-flex align-items-center justify-content-between">
                <a href="<?php echo BASE_URL; ?>produtos" class="text-decoration-none text-secondary fw-bold">
                    <i class="bi bi-arrow-left me-1"></i> Voltar
                </a>
                <span class="fw-bold">
                    <?php echo htmlspecialchars($aula['produto_titulo']); ?>
                </span>
                <div style="width: 60px;"></div> <!-- Spacer -->
            </div>

            <!-- Player de Vídeo -->
            <?php if ($aula['tipo'] === 'video' && !empty($aula['url_video'])): ?>
                <div class="video-container">
                    <iframe src="<?php echo htmlspecialchars($aula['url_video']); ?>" allowfullscreen></iframe>
                </div>
            <?php endif; ?>

            <!-- Corpo do Texto / Descrição -->
            <div class="content-body">
                <h1 class="h3 fw-bold mb-3">
                    <?php echo htmlspecialchars($aula['titulo']); ?>
                </h1>

                <div class="text-muted mb-4" style="white-space: pre-line;">
                    <?php echo $aula['descricao']; ?>
                </div>

                <!-- BOTÃO DE TAREFA (INTEGRAÇÃO) -->
                <?php if ($aula['tem_tarefa']): ?>
                    <div class="card border-warning mb-4 bg-warning bg-opacity-10">
                        <div class="card-body">
                            <h5 class="card-title fw-bold text-dark">
                                <i class="bi bi-list-check me-2"></i>Tarefa da Aula
                            </h5>
                            <p class="card-text text-dark">
                                <?php echo nl2br(htmlspecialchars($aula['tarefa_descricao'])); ?>
                            </p>

                            <div class="mt-3">
                                <button data-titulo="<?php echo htmlspecialchars('Tarefa: ' . $aula['titulo']); ?>"
                                    data-descricao="<?php echo htmlspecialchars($aula['tarefa_descricao']); ?>"
                                    onclick="criarTarefaGestao(this)" class="btn btn-dark" id="btnTarefa">
                                    <i class="bi bi-plus-square me-2"></i>Adicionar ao meu Kanban
                                </button>
                                <small class="text-muted d-block mt-2">
                                    * Isso criará um card no seu módulo de Gestão de Tarefas automaticamente.
                                </small>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- RECURSO VINCULADO (DIAGNÓSTICO OU FERRAMENTA) -->
                <?php if ($aula['tipo'] === 'diagnostico' && $aula['diag_titulo']): ?>
                    <div class="card border-info mb-4 bg-info bg-opacity-10">
                        <div class="card-body">
                            <h5 class="card-title fw-bold text-dark">
                                <i class="bi bi-ui-checks-grid me-2"></i>Diagnóstico Prático
                            </h5>
                            <p class="card-text text-dark">
                                Esta aula inclui um diagnóstico prático:
                                <strong><?php echo htmlspecialchars($aula['diag_titulo']); ?></strong>.
                                Utilize essa ferramenta para avaliar o cenário atual.
                            </p>
                            <a href="<?php echo BASE_URL; ?>modules/gestao/diagnostico/novo.php?modelo_id=<?php echo $aula['resource_id']; ?>"
                                class="btn btn-info text-white" target="_blank">
                                <i class="bi bi-play-circle me-2"></i>Iniciar Diagnóstico
                            </a>
                        </div>
                    </div>
                <?php elseif ($aula['tipo'] === 'ferramenta' && $aula['tool_nome']): ?>
                    <div class="card border-primary mb-4 bg-primary bg-opacity-10">
                        <div class="card-body">
                            <h5 class="card-title fw-bold text-dark">
                                <i class="bi <?php echo $aula['tool_icone'] ?? 'bi-tools'; ?> me-2"></i>Ferramenta
                                Recomendada
                            </h5>
                            <p class="card-text text-dark">
                                Utilize a ferramenta <strong><?php echo htmlspecialchars($aula['tool_nome']); ?></strong>
                                para aplicar os conceitos desta aula.
                            </p>
                            <a href="<?php echo BASE_URL; ?>ferramentas/<?php echo htmlspecialchars($aula['tool_slug']); ?>"
                                class="btn btn-primary" target="_blank">
                                <i class="bi bi-box-arrow-up-right me-2"></i>Acessar Ferramenta
                            </a>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Navegação Anterior/Próximo (Simplificada) -->
                <div class="d-flex justify-content-between mt-5 pt-3 border-top">
                    <button class="btn btn-outline-secondary" disabled>Aula Anterior</button>
                    <button class="btn btn-primary" onclick="window.location.reload()">Próxima Aula</button>
                </div>
            </div>
        </div>

        <!-- SIDEBAR (DIREITA) -->
        <div class="player-sidebar">
            <div class="sidebar-header">
                <h5 class="mb-0 fw-bold">Conteúdo do Curso</h5>
                <div class="progress mt-2" style="height: 6px;">
                    <div class="progress-bar bg-success" style="width: 10%"></div>
                </div>
                <small class="text-muted">10% concluído</small>
            </div>

            <div class="modules-list">
                <?php foreach ($estrutura as $trilha): ?>
                    <div class="module-section">
                        <div class="module-header">
                            <?php echo htmlspecialchars($trilha['titulo']); ?>
                        </div>
                        <?php if (isset($trilha['aulas'])): ?>
                            <?php foreach ($trilha['aulas'] as $item): ?>
                                <a href="<?php echo BASE_URL; ?>produtos/aula/<?php echo $item['aula_id']; ?>"
                                    class="lesson-item <?php echo ($item['aula_id'] == $aula_id) ? 'active' : ''; ?>">

                                    <i
                                        class="bi bi-<?php echo $item['tipo'] === 'video' ? 'play-circle' : 'file-text'; ?> lesson-icon"></i>

                                    <span class="text-truncate">
                                        <?php echo htmlspecialchars($item['aula_titulo']); ?>
                                    </span>

                                    <?php if ($item['concluido']): ?>
                                        <i class="bi bi-check-circle-fill lesson-check"></i>
                                    <?php endif; ?>
                                </a>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <script>
        function criarTarefaGestao(btn) {
            const titulo = btn.dataset.titulo;
            const descricao = btn.dataset.descricao;

            if (confirm('Deseja realmente adicionar esta tarefa ao seu quadro Kanban?')) {
                // Disable button
                const originalText = btn.innerHTML;
                btn.disabled = true;
                btn.innerHTML = '<i class="bi bi-hourglass-split me-2"></i>Processando...';

                const formData = new FormData();
                formData.append('action', 'create');
                formData.append('titulo', titulo);
                formData.append('descricao', descricao);
                formData.append('prioridade', 'alta');
                formData.append('status', 'todo');

                fetch('<?php echo BASE_URL; ?>modules/gestao/tarefas/acoes.php', {
                    method: 'POST',
                    body: formData
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert('Sucesso! Tarefa adicionada ao Kanban com ID #' + data.id);
                            btn.classList.remove('btn-dark');
                            btn.classList.add('btn-success');
                            btn.innerHTML = '<i class="bi bi-check-lg me-2"></i>Adicionado ao Kanban';
                            btn.disabled = true; // Prevent double add
                        } else {
                            alert('Erro ao criar tarefa: ' + (data.message || 'Erro desconhecido'));
                            btn.disabled = false;
                            btn.innerHTML = originalText;
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Erro na comunicação com o servidor');
                        btn.disabled = false;
                        btn.innerHTML = originalText;
                    });
            }
        }
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>