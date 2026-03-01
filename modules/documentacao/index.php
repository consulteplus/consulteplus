<?php
session_start();
require_once __DIR__ . '/../../config/config.php';

$pageTitle = "Documentação Técnica - Consulte+";

// Determine User Role
$isAdmin = false;
if (isset($_SESSION['user_id'])) {
    $isAdmin = in_array($_SESSION['tipo'], ['admin', 'superadmin']);
}

// Carregar classes
require_once __DIR__ . '/scanner.php';
require_once __DIR__ . '/parser.php';

// Inicializar scanner e parser
$scanner = new ModuleScanner(__DIR__ . '/..');
$parser = new DocsParser();

// Escanear módulos disponíveis
$modules = $scanner->scan($isAdmin);

// Determinar qual módulo exibir
$currentModule = $_GET['module'] ?? 'gestao'; // Default: gestao
$currentSection = $_GET['section'] ?? null;

// Buscar arquivo DOCS.md do módulo atual
$currentModuleData = null;
$currentDocsPath = null;

foreach ($modules as $category => $moduleList) {
    foreach ($moduleList as $mod) {
        if ($mod['name'] === $currentModule) {
            $currentModuleData = $mod;
            $currentDocsPath = $mod['path'];
            break 2;
        }
    }
}

// Se não encontrou, usar primeiro módulo disponível
if (!$currentModuleData) {
    foreach ($modules as $category => $moduleList) {
        if (!empty($moduleList)) {
            $currentModuleData = $moduleList[0];
            $currentDocsPath = $moduleList[0]['path'];
            $currentModule = $moduleList[0]['name'];
            break;
        }
    }
}

// Parsear documentação
$docs = $parser->parse($currentDocsPath);

// Carregar header
if (isset($_SESSION['user_id'])) {
    require_once __DIR__ . '/../../includes/header.php';
} else {
    ?>
    <!DOCTYPE html>
    <html lang="pt-BR">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title><?php echo $pageTitle; ?></title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
    </head>

    <body class="bg-light">
        <nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom mb-4">
            <div class="container-fluid">
                <a class="navbar-brand fw-bold text-primary" href="../../site/index.php">Consulte+</a>
                <a href="../../login.php" class="btn btn-outline-primary btn-sm">Entrar</a>
            </div>
        </nav>
        <div class="container-fluid">
            <?php
}
?>

        <style>
            /* Estilos da Documentação */
            .docs-sidebar {
                position: sticky;
                top: 20px;
                max-height: calc(100vh - 40px);
                overflow-y: auto;
                background: white;
                border-radius: 8px;
                box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            }

            .docs-sidebar .module-item {
                padding: 10px 15px;
                border-left: 3px solid transparent;
                cursor: pointer;
                transition: all 0.2s;
                text-decoration: none;
                display: block;
                color: #495057;
            }

            .docs-sidebar .module-item:hover {
                background: #f8f9fa;
                border-left-color: #0d6efd;
            }

            .docs-sidebar .module-item.active {
                background: #e7f1ff;
                border-left-color: #0d6efd;
                color: #0d6efd;
                font-weight: 600;
            }

            .docs-sidebar .category-header {
                padding: 12px 15px;
                background: #f8f9fa;
                font-weight: 700;
                font-size: 0.75rem;
                text-transform: uppercase;
                letter-spacing: 0.5px;
                color: #6c757d;
                border-bottom: 1px solid #dee2e6;
            }

            .docs-content {
                background: white;
                border-radius: 8px;
                padding: 30px;
                box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
                min-height: 600px;
            }

            .docs-content h1 {
                border-bottom: 3px solid #0d6efd;
                padding-bottom: 15px;
                margin-bottom: 30px;
            }

            .docs-content h2 {
                margin-top: 40px;
                margin-bottom: 20px;
                color: #2c3e50;
                border-left: 4px solid #0d6efd;
                padding-left: 15px;
            }

            .docs-content h3 {
                margin-top: 30px;
                margin-bottom: 15px;
                color: #34495e;
            }

            .docs-content pre {
                background: #2d3436;
                color: #dfe6e9;
                padding: 20px;
                border-radius: 6px;
                overflow-x: auto;
            }

            .docs-content code {
                background: #f8f9fa;
                padding: 2px 6px;
                border-radius: 3px;
                color: #e83e8c;
                font-size: 0.9em;
            }

            .docs-content pre code {
                background: transparent;
                padding: 0;
                color: #dfe6e9;
            }

            .docs-content table {
                width: 100%;
                margin: 20px 0;
            }

            .docs-content table th {
                background: #f8f9fa;
                font-weight: 600;
            }

            .docs-toc {
                position: sticky;
                top: 20px;
                background: #f8f9fa;
                border-radius: 8px;
                padding: 20px;
                max-height: calc(100vh - 40px);
                overflow-y: auto;
            }

            .docs-toc a {
                display: block;
                padding: 5px 0;
                color: #6c757d;
                text-decoration: none;
                font-size: 0.9rem;
                transition: color 0.2s;
            }

            .docs-toc a:hover {
                color: #0d6efd;
            }

            .docs-toc a.level-2 {
                padding-left: 0;
                font-weight: 600;
            }

            .docs-toc a.level-3 {
                padding-left: 15px;
            }

            .docs-toc a.level-4 {
                padding-left: 30px;
                font-size: 0.85rem;
            }

            .mermaid {
                text-align: center;
                margin: 30px 0;
            }

            .docs-metadata {
                background: #e7f1ff;
                border-left: 4px solid #0d6efd;
                padding: 15px;
                margin-bottom: 30px;
                border-radius: 4px;
            }

            .search-box {
                padding: 15px;
                background: white;
                border-bottom: 1px solid #dee2e6;
            }
        </style>

        <div class="row g-4">
            <!-- SIDEBAR: Lista de Módulos -->
            <div class="col-lg-3">
                <div class="docs-sidebar">
                    <!-- Busca -->
                    <div class="search-box">
                        <input type="text" id="searchDocs" class="form-control form-control-sm" placeholder="Buscar...">
                    </div>

                    <?php if (!empty($modules['public'])): ?>
                        <div class="category-header">📖 Público</div>
                        <?php foreach ($modules['public'] as $mod): ?>
                            <a href="?module=<?= $mod['name'] ?>"
                                class="module-item <?= $mod['name'] === $currentModule ? 'active' : '' ?>">
                                <i class="bi bi-<?= $mod['icon'] ?> me-2"></i><?= $mod['title'] ?>
                            </a>
                        <?php endforeach; ?>
                    <?php endif; ?>

                    <?php if (!empty($modules['user'])): ?>
                        <div class="category-header">👤 Usuário</div>
                        <?php foreach ($modules['user'] as $mod): ?>
                            <a href="?module=<?= $mod['name'] ?>"
                                class="module-item <?= $mod['name'] === $currentModule ? 'active' : '' ?>">
                                <i class="bi bi-<?= $mod['icon'] ?> me-2"></i><?= $mod['title'] ?>
                            </a>
                        <?php endforeach; ?>
                    <?php endif; ?>

                    <?php if ($isAdmin && !empty($modules['admin'])): ?>
                        <div class="category-header text-danger">🔒 Administração</div>
                        <?php foreach ($modules['admin'] as $mod): ?>
                            <a href="?module=<?= $mod['name'] ?>"
                                class="module-item <?= $mod['name'] === $currentModule ? 'active' : '' ?>">
                                <i class="bi bi-<?= $mod['icon'] ?> me-2"></i><?= $mod['title'] ?>
                            </a>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

            <!-- CONTEÚDO: Documentação Renderizada -->
            <div class="col-lg-7">
                <div class="docs-content">
                    <!-- Breadcrumb -->
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="?">Documentação</a></li>
                            <li class="breadcrumb-item active"><?= $currentModuleData['title'] ?? 'Módulo' ?></li>
                        </ol>
                    </nav>

                    <!-- Metadados -->
                    <div class="docs-metadata">
                        <div class="row">
                            <div class="col-md-6">
                                <small class="text-muted">Versão:</small>
                                <strong><?= $docs['metadata']['version'] ?></strong>
                            </div>
                            <div class="col-md-6 text-end">
                                <small class="text-muted">Atualizado em:</small>
                                <strong><?= $docs['metadata']['updated'] ?></strong>
                            </div>
                        </div>
                    </div>

                    <!-- Conteúdo Markdown Renderizado -->
                    <div id="docsContent">
                        <?= $docs['html'] ?>
                    </div>
                </div>
            </div>

            <!-- TOC: Índice da Página Atual -->
            <div class="col-lg-2 d-none d-lg-block">
                <div class="docs-toc">
                    <h6 class="text-uppercase fw-bold mb-3" style="font-size: 0.75rem; color: #6c757d;">Nesta Página
                    </h6>
                    <?php foreach ($docs['toc'] as $item): ?>
                        <a href="#<?= $item['id'] ?>" class="level-<?= $item['level'] ?>">
                            <?= htmlspecialchars($item['title']) ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- Mermaid.js para renderizar diagramas -->
        <script src="https://cdn.jsdelivr.net/npm/mermaid@10/dist/mermaid.min.js"></script>
        <script>
            // Inicializar Mermaid
            mermaid.initialize({
                startOnLoad: true,
                theme: 'default',
                securityLevel: 'loose'
            });

            // Busca simples
            document.getElementById('searchDocs')?.addEventListener('input', function (e) {
                const query = e.target.value.toLowerCase();
                const content = document.getElementById('docsContent');

                if (!query) {
                    // Remover highlights
                    content.innerHTML = content.innerHTML.replace(/<mark>/g, '').replace(/<\/mark>/g, '');
                    return;
                }

                // Simples highlight (pode melhorar com biblioteca)
                const text = content.textContent;
                if (text.toLowerCase().includes(query)) {
                    alert('Encontrado! (Implementar highlight visual)');
                }
            });

            // Scroll suave para âncoras
            document.querySelectorAll('a[href^="#"]').forEach(anchor => {
                anchor.addEventListener('click', function (e) {
                    e.preventDefault();
                    const target = document.querySelector(this.getAttribute('href'));
                    if (target) {
                        target.scrollIntoView({ behavior: 'smooth', block: 'start' });
                    }
                });
            });

            // Auto-scroll para seção se especificada na URL
            <?php if ($currentSection): ?>
                window.addEventListener('load', function () {
                    const section = document.getElementById('<?= htmlspecialchars($currentSection) ?>');
                    if (section) {
                        setTimeout(() => section.scrollIntoView({ behavior: 'smooth' }), 500);
                    }
                });
            <?php endif; ?>
        </script>

        <?php
        if (isset($_SESSION['user_id'])) {
            require_once __DIR__ . '/../../includes/footer.php';
        } else {
            echo "</div></body></html>";
        }
        ?>