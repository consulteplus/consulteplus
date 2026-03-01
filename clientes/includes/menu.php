<!-- Sidebar Menu -->
<?php // Menu Principal ?>
<div class="mb-4 px-3 text-center">
    <img src="<?php echo BASE_URL; ?>assets/img/logo-consulte-plus.png" alt="Consulte+"
        style="max-height: 45px; max-width: 180px; display: inline-block;">
</div>

<!-- Separator -->
<hr class="my-3 mx-3" style="border-color: rgba(0,0,0,0.15); opacity: 1;">

<!-- Toggle Button (On Sidebar Edge) -->
<button id="sidebarToggle" class="btn p-0 d-none d-md-block"
    style="position: absolute; right: -22px; top: 20px; z-index: 10000;" title="Recolher Menu">
    <i class="bi bi-chevron-left fs-4" id="sidebarToggleIcon"></i>
</button>

<?php
// Logic to determine which menu section should be open
$uri = $_SERVER['REQUEST_URI'];
$showGestao = strpos($uri, '/gestao/') !== false;
?>

<ul class="nav flex-column">

    <!-- ============================================== -->
    <!-- MEU ESPAÇO (CLIENTE / DIÁRIO) -->
    <!-- ============================================== -->

    <?php if ($_SESSION['tipo'] !== 'superadmin'): ?>

        <!-- Dashboard -->
        <?php if (!isset($_SESSION['tipo']) || $_SESSION['tipo'] !== 'superadmin'): ?>
            <li class="nav-item">
                <a class="nav-link <?php echo strpos($_SERVER['REQUEST_URI'], '/dashboard') !== false ? 'active' : ''; ?>"
                    href="<?php echo BASE_URL; ?>dashboard">
                    <i class="bi bi-speedometer2"></i> <span>Dashboard</span>
                </a>
            </li>
        <?php endif; ?>

        <!-- Financeiro removido do menu (acesso via Perfil) -->


        <!-- Diagnóstico (Wizard) -->
        <?php if (hasPermission(['admin', 'cliente'])): ?>
            <li class="nav-item">
                <a class="nav-link <?php echo strpos($_SERVER['REQUEST_URI'], '/gestao/diagnostico') !== false ? 'active' : ''; ?>"
                    href="<?php echo BASE_URL; ?>gestao/diagnostico">
                    <i class="bi bi-ui-checks"></i> <span>Diagnóstico</span>
                </a>
            </li>
        <?php endif; ?>

        <!-- Produtos (Visualização Aluno) -->
        <?php if (hasPermission(['cliente'])): ?>
            <li class="nav-item">
                <a class="nav-link <?php echo strpos($_SERVER['REQUEST_URI'], '/produtos') !== false && strpos($_SERVER['REQUEST_URI'], '/admin/') === false ? 'active' : ''; ?>"
                    href="<?php echo BASE_URL; ?>produtos">
                    <i class="bi bi-box-seam"></i>
                    <span>Produtos</span>
                </a>
            </li>
        <?php endif; ?>

        <!-- Gestão Diária (Projetos/Tarefas/OKRs) -->
        <?php if (hasPermission(['admin', 'cliente'])): ?>
            <li class="nav-item">
                <a class="nav-link <?php echo strpos($_SERVER['REQUEST_URI'], '/gestao/projetos') !== false ? 'active' : ''; ?>"
                    href="<?php echo BASE_URL; ?>gestao/projetos">
                    <i class="bi bi-diagram-3"></i> <span>Projetos</span>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link <?php echo strpos($_SERVER['REQUEST_URI'], '/gestao/tarefas') !== false ? 'active' : ''; ?>"
                    href="<?php echo BASE_URL; ?>gestao/tarefas">
                    <i class="bi bi-kanban"></i> <span>Tarefas</span>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link <?php echo strpos($_SERVER['REQUEST_URI'], '/gestao/planejamento') !== false ? 'active' : ''; ?>"
                    href="<?php echo BASE_URL; ?>gestao/planejamento">
                    <i class="bi bi-bullseye"></i> <span>OKRs</span>
                </a>
            </li>

            <!-- Ferramentas removido do menu (acesso via Produtos) -->
        <?php endif; ?>

    <?php endif; ?>


    <!-- ============================================== -->
    <!-- ADMINISTRAÇÃO & SUPERADMIN (Externalizado) -->
    <!-- ============================================== -->

    <?php
    // Inclui apenas os ITENS do menu administrativo (sem header/footer duplicado)
    $adminMenuPath = __DIR__ . '/../modules/admin/menu_items.php';
    if (file_exists($adminMenuPath)) {
        include $adminMenuPath;
    }
    ?>

    <!-- Separador Footer -->
    <hr class="my-2 border-secondary opacity-25 mt-auto">

    <!-- Usuário / Logout -->
    <li class="nav-item mt-3">
        <div class="bg-white rounded px-3 py-2 d-flex justify-content-between align-items-center">
            <div class="text-dark d-flex align-items-center">
                <i class="bi bi-person-circle me-2 fs-5"></i>
                <span class="fw-bold">
                    <?php
                    $primeiroNome = explode(' ', $_SESSION['nome'] ?? 'Usuário')[0];
                    echo htmlspecialchars($primeiroNome);
                    ?>
                </span>
            </div>
            <div class="d-flex align-items-center gap-3 ms-auto">
                <a href="<?php echo BASE_URL; ?>modules/perfil" class="text-secondary text-decoration-none"
                    title="Meu Perfil" style="transition: opacity 0.2s;" onmouseover="this.style.opacity='0.7'"
                    onmouseout="this.style.opacity='1'">
                    <i class="bi bi-gear fs-5"></i>
                </a>
                <div class="vr bg-secondary opacity-25"></div>
                <a href="<?php echo BASE_URL; ?>logout.php" class="text-danger text-decoration-none" title="Sair"
                    style="transition: opacity 0.2s;" onmouseover="this.style.opacity='0.7'"
                    onmouseout="this.style.opacity='1'">
                    <i class="bi bi-box-arrow-right fs-5"></i>
                </a>
            </div>
        </div>
    </li>
</ul>