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

        <!-- Dashboard (sempre ativo) -->
        <?php if (!isset($_SESSION['tipo']) || $_SESSION['tipo'] !== 'superadmin'): ?>
            <li class="nav-item">
                <a class="nav-link <?php echo strpos($_SERVER['REQUEST_URI'], '/dashboard') !== false ? 'active' : ''; ?>"
                    href="<?php echo BASE_URL; ?>dashboard">
                    <i class="bi bi-speedometer2"></i> <span>Dashboard</span>
                </a>
            </li>
        <?php endif; ?>

        <!-- CRM -->
        <?php if (hasPermission(['admin', 'cliente']) && isModuleEnabled('crm')): ?>
            <?php $crmActive = strpos($_SERVER['REQUEST_URI'], '/crm') !== false; ?>
            <li class="nav-item">
                <a class="nav-link d-flex align-items-center justify-content-between <?php echo $crmActive ? '' : 'collapsed'; ?>"
                    href="#submenuCRM" data-bs-toggle="collapse" role="button"
                    aria-expanded="<?php echo $crmActive ? 'true' : 'false'; ?>" aria-controls="submenuCRM">
                    <span><i class="bi bi-funnel me-2"></i> CRM</span>
                    <i class="bi bi-chevron-down" style="font-size:0.8em;"></i>
                </a>
                <div class="collapse <?php echo $crmActive ? 'show' : ''; ?>" id="submenuCRM">
                    <ul class="nav flex-column ms-3 border-start ps-2" style="border-color:rgba(255,255,255,0.15)!important;">
                        <li class="nav-item my-1"><a
                                class="nav-link py-1 <?php echo (strpos($_SERVER['REQUEST_URI'], '/crm') !== false && strpos($_SERVER['REQUEST_URI'], '/config') === false) ? 'active' : 'text-muted'; ?>"
                                href="<?php echo BASE_URL; ?>crm"><i class="bi bi-view-list me-2"></i> Pipeline</a></li>
                        <li class="nav-item my-1"><a
                                class="nav-link py-1 <?php echo strpos($_SERVER['REQUEST_URI'], '/crm/config') !== false ? 'active' : 'text-muted'; ?>"
                                href="<?php echo BASE_URL; ?>crm/config"><i class="bi bi-gear me-2"></i> Configurações</a></li>
                    </ul>
                </div>
            </li>
        <?php endif; ?>

        <!-- Atendimento -->
        <?php if (hasPermission(['admin', 'cliente']) && isModuleEnabled('atendimento')): ?>
            <?php $atendActive = strpos($_SERVER['REQUEST_URI'], '/atendimento') !== false; ?>
            <li class="nav-item">
                <a class="nav-link d-flex align-items-center justify-content-between <?php echo $atendActive ? '' : 'collapsed'; ?>"
                    href="#submenuAtend" data-bs-toggle="collapse" role="button"
                    aria-expanded="<?php echo $atendActive ? 'true' : 'false'; ?>" aria-controls="submenuAtend">
                    <span><i class="bi bi-headset me-2"></i> Atendimento</span>
                    <i class="bi bi-chevron-down" style="font-size:0.8em;"></i>
                </a>
                <div class="collapse <?php echo $atendActive ? 'show' : ''; ?>" id="submenuAtend">
                    <ul class="nav flex-column ms-3 border-start ps-2" style="border-color:rgba(255,255,255,0.15)!important;">
                        <li class="nav-item my-1"><a
                                class="nav-link py-1 <?php echo strpos($_SERVER['REQUEST_URI'], '/atendimento/agentes') !== false ? 'active' : 'text-muted'; ?>"
                                href="<?php echo BASE_URL; ?>atendimento/agentes"><i class="bi bi-robot me-2"></i> Agentes
                                IA</a></li>
                        <li class="nav-item my-1"><a
                                class="nav-link py-1 <?php echo strpos($_SERVER['REQUEST_URI'], '/atendimento/agentes/chats') !== false ? 'active' : 'text-muted'; ?>"
                                href="<?php echo BASE_URL; ?>atendimento/agentes/chats"><i class="bi bi-chat-dots me-2"></i>
                                Conversas</a></li>
                    </ul>
                </div>
            </li>
        <?php endif; ?>

        <!-- Marketing -->
        <?php if (hasPermission(['admin', 'cliente']) && isModuleEnabled('marketing')): ?>
            <?php $mktActive = strpos($_SERVER['REQUEST_URI'], '/marketing') !== false; ?>
            <li class="nav-item">
                <a class="nav-link d-flex align-items-center justify-content-between <?php echo $mktActive ? '' : 'collapsed'; ?>"
                    href="#submenuMkt" data-bs-toggle="collapse" role="button"
                    aria-expanded="<?php echo $mktActive ? 'true' : 'false'; ?>" aria-controls="submenuMkt">
                    <span><i class="bi bi-megaphone me-2"></i> Marketing</span>
                    <i class="bi bi-chevron-down" style="font-size:0.8em;"></i>
                </a>
                <div class="collapse <?php echo $mktActive ? 'show' : ''; ?>" id="submenuMkt">
                    <ul class="nav flex-column ms-3 border-start ps-2" style="border-color:rgba(255,255,255,0.15)!important;">
                        <li class="nav-item my-1"><a
                                class="nav-link py-1 <?php echo strpos($_SERVER['REQUEST_URI'], '/marketing/leads') !== false ? 'active' : 'text-muted'; ?>"
                                href="<?php echo BASE_URL; ?>marketing/leads"><i class="bi bi-person-lines-fill me-2"></i>
                                Leads</a></li>
                        <li class="nav-item my-1"><a
                                class="nav-link py-1 <?php echo strpos($_SERVER['REQUEST_URI'], '/marketing/audiencias') !== false ? 'active' : 'text-muted'; ?>"
                                href="<?php echo BASE_URL; ?>marketing/audiencias"><i class="bi bi-people me-2"></i>
                                Audiências</a></li>
                        <li class="nav-item my-1"><a
                                class="nav-link py-1 <?php echo strpos($_SERVER['REQUEST_URI'], '/marketing/gerador') !== false ? 'active' : 'text-muted'; ?>"
                                href="<?php echo BASE_URL; ?>marketing/gerador_leads"><i class="bi bi-magnet me-2"></i> Gerador
                                de Leads</a></li>
                    </ul>
                </div>
            </li>
        <?php endif; ?>

        <!-- Produtos (Treinamentos + Ferramentas) -->
        <?php
        $produtosActive = (strpos($_SERVER['REQUEST_URI'], '/produtos') !== false && strpos($_SERVER['REQUEST_URI'], '/admin/') === false)
            || strpos($_SERVER['REQUEST_URI'], '/ferramentas') !== false;
        ?>
        <?php if (hasPermission(['admin', 'cliente']) && (isModuleEnabled('produtos') || isModuleEnabled('ferramentas'))): ?>
            <li class="nav-item">
                <a class="nav-link d-flex align-items-center justify-content-between <?php echo $produtosActive ? '' : 'collapsed'; ?>"
                    href="#submenuProdutos" data-bs-toggle="collapse" role="button"
                    aria-expanded="<?php echo $produtosActive ? 'true' : 'false'; ?>" aria-controls="submenuProdutos">
                    <span><i class="bi bi-grid me-2"></i> Produtos</span>
                    <i class="bi bi-chevron-down" style="font-size:0.8em;"></i>
                </a>
                <div class="collapse <?php echo $produtosActive ? 'show' : ''; ?>" id="submenuProdutos">
                    <ul class="nav flex-column ms-3 border-start ps-2" style="border-color:rgba(255,255,255,0.15)!important;">
                        <?php if (isModuleEnabled('produtos')): ?>
                            <li class="nav-item my-1"><a
                                    class="nav-link py-1 <?php echo strpos($_SERVER['REQUEST_URI'], '/produtos') !== false && strpos($_SERVER['REQUEST_URI'], '/admin/') === false ? 'active' : 'text-muted'; ?>"
                                    href="<?php echo BASE_URL; ?>produtos"><i class="bi bi-play-btn me-2"></i> Treinamentos</a></li>
                        <?php endif; ?>
                        <?php if (isModuleEnabled('ferramentas')): ?>
                            <li class="nav-item my-1"><a
                                    class="nav-link py-1 <?php echo strpos($_SERVER['REQUEST_URI'], '/ferramentas') !== false ? 'active' : 'text-muted'; ?>"
                                    href="<?php echo BASE_URL; ?>ferramentas"><i class="bi bi-tools me-2"></i> Ferramentas</a></li>
                        <?php endif; ?>
                    </ul>
                </div>
            </li>
        <?php endif; ?>

        <!-- Gestão (Projetos / Tarefas / OKRs) -->
        <?php $gestaoActive = strpos($_SERVER['REQUEST_URI'], '/gestao') !== false; ?>
        <?php if (hasPermission(['admin', 'cliente']) && isModuleEnabled('gestao')): ?>
            <li class="nav-item">
                <a class="nav-link d-flex align-items-center justify-content-between <?php echo $gestaoActive ? '' : 'collapsed'; ?>"
                    href="#submenuGestao" data-bs-toggle="collapse" role="button"
                    aria-expanded="<?php echo $gestaoActive ? 'true' : 'false'; ?>" aria-controls="submenuGestao">
                    <span><i class="bi bi-briefcase me-2"></i> Gestão</span>
                    <i class="bi bi-chevron-down" style="font-size:0.8em;"></i>
                </a>
                <div class="collapse <?php echo $gestaoActive ? 'show' : ''; ?>" id="submenuGestao">
                    <ul class="nav flex-column ms-3 border-start ps-2" style="border-color:rgba(255,255,255,0.15)!important;">
                        <li class="nav-item my-1"><a
                                class="nav-link py-1 <?php echo strpos($_SERVER['REQUEST_URI'], '/gestao/projetos') !== false ? 'active' : 'text-muted'; ?>"
                                href="<?php echo BASE_URL; ?>gestao/projetos"><i class="bi bi-diagram-3 me-2"></i> Projetos</a>
                        </li>
                        <li class="nav-item my-1"><a
                                class="nav-link py-1 <?php echo strpos($_SERVER['REQUEST_URI'], '/gestao/tarefas') !== false ? 'active' : 'text-muted'; ?>"
                                href="<?php echo BASE_URL; ?>gestao/tarefas"><i class="bi bi-kanban me-2"></i> Tarefas</a></li>
                        <li class="nav-item my-1"><a
                                class="nav-link py-1 <?php echo strpos($_SERVER['REQUEST_URI'], '/gestao/planejamento') !== false ? 'active' : 'text-muted'; ?>"
                                href="<?php echo BASE_URL; ?>gestao/planejamento"><i class="bi bi-bullseye me-2"></i> OKRs</a>
                        </li>
                    </ul>
                </div>
            </li>
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