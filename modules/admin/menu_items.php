<?php
// modules/admin/menu_items.php
// Itens de menu compartilhados (Administração e SuperAdmin)
?>

<!-- ============================================== -->
<!-- ADMINISTRAÇÃO (GESTORES / ADMINS) -->
<!-- ============================================== -->

<?php if (hasPermission(['admin']) && (!isset($_SESSION['tipo']) || $_SESSION['tipo'] !== 'superadmin')): ?>

    <li class="nav-item ps-3 mb-2 mt-3 hide-on-mini">
        <small class="text-uppercase text-white-50 fw-bold" style="font-size: 0.75rem; letter-spacing: 1px;">
            <i class="bi bi-shield-lock me-1"></i> Administração
        </small>
    </li>

    <!-- CRM (Pipeline + Config) -->
    <li class="nav-item">
        <a class="nav-link d-flex align-items-center justify-content-between <?php echo strpos($_SERVER['REQUEST_URI'], '/admin/crm') !== false ? '' : 'collapsed'; ?>"
            href="#submenuCRM" data-bs-toggle="collapse" role="button"
            aria-expanded="<?php echo strpos($_SERVER['REQUEST_URI'], '/admin/crm') !== false ? 'true' : 'false'; ?>"
            aria-controls="submenuCRM">
            <span>
                <i class="bi bi-kanban me-2"></i> CRM
            </span>
            <i class="bi bi-chevron-down" style="font-size: 0.8em;"></i>
        </a>
        <div class="collapse <?php echo strpos($_SERVER['REQUEST_URI'], '/admin/crm') !== false ? 'show' : ''; ?>"
            id="submenuCRM">
            <ul class="nav flex-column ms-3 border-start border-secondary border-opacity-25 ps-2">
                <li class="nav-item my-1">
                    <a class="nav-link py-1 <?php echo strpos($_SERVER['REQUEST_URI'], '/admin/crm/index') !== false || rtrim($_SERVER['REQUEST_URI'], '/') == rtrim(BASE_URL . 'admin/crm', '/') ? 'active text-white' : 'text-white-50'; ?>"
                        href="<?php echo BASE_URL; ?>admin/crm">
                        <i class="bi bi-view-list me-2"></i> Pipeline
                    </a>
                </li>
                <li class="nav-item my-1">
                    <a class="nav-link py-1 <?php echo strpos($_SERVER['REQUEST_URI'], '/admin/crm/config') !== false ? 'active text-white' : 'text-white-50'; ?>"
                        href="<?php echo BASE_URL; ?>admin/crm/config">
                        <i class="bi bi-gear me-2"></i> Configurações
                    </a>
                </li>
            </ul>
        </div>
    </li>

    <!-- Marketing (Leads) -->
    <li class="nav-item">
        <a class="nav-link d-flex align-items-center justify-content-between <?php echo strpos($_SERVER['REQUEST_URI'], '/marketing') !== false ? '' : 'collapsed'; ?>"
            href="#submenuMarketing" data-bs-toggle="collapse" role="button"
            aria-expanded="<?php echo strpos($_SERVER['REQUEST_URI'], '/marketing') !== false ? 'true' : 'false'; ?>"
            aria-controls="submenuMarketing">
            <span>
                <i class="bi bi-megaphone me-2"></i> Marketing
            </span>
            <i class="bi bi-chevron-down" style="font-size: 0.8em;"></i>
        </a>
        <div class="collapse <?php echo strpos($_SERVER['REQUEST_URI'], '/marketing') !== false ? 'show' : ''; ?>"
            id="submenuMarketing">
            <ul class="nav flex-column ms-3 border-start border-secondary border-opacity-25 ps-2">
                <li class="nav-item my-1">
                    <a class="nav-link py-1 <?php echo strpos($_SERVER['REQUEST_URI'], '/marketing/leads') !== false ? 'active text-white' : 'text-white-50'; ?>"
                        href="<?php echo BASE_URL; ?>modules/admin/marketing/leads">
                        <i class="bi bi-people me-2"></i> Base de Leads
                    </a>
                </li>
                <li class="nav-item my-1">
                    <a class="nav-link py-1 <?php echo strpos($_SERVER['REQUEST_URI'], '/marketing/audiencias') !== false ? 'active text-white' : 'text-white-50'; ?>"
                        href="<?php echo BASE_URL; ?>modules/admin/marketing/audiencias">
                        <i class="bi bi-funnel me-2"></i> Audiências
                    </a>
                </li>
                <li class="nav-item my-1">
                    <a class="nav-link py-1 <?php echo strpos($_SERVER['REQUEST_URI'], '/marketing/gerador_leads') !== false ? 'active text-white' : 'text-white-50'; ?>"
                        href="<?php echo BASE_URL; ?>admin/marketing/gerador_leads">
                        <i class="bi bi-robot me-2"></i> Gerador de Leads
                    </a>
                </li>
            </ul>
        </div>
    </li>

    <!-- Atendimento (Agentes de IA) -->
    <li class="nav-item">
        <a class="nav-link d-flex align-items-center justify-content-between <?php echo strpos($_SERVER['REQUEST_URI'], '/atendimento') !== false ? '' : 'collapsed'; ?>"
            href="#submenuAtendimento" data-bs-toggle="collapse" role="button"
            aria-expanded="<?php echo strpos($_SERVER['REQUEST_URI'], '/atendimento') !== false ? 'true' : 'false'; ?>"
            aria-controls="submenuAtendimento">
            <span>
                <i class="bi bi-robot me-2"></i> Atendimento
            </span>
            <i class="bi bi-chevron-down" style="font-size: 0.8em;"></i>
        </a>
        <div class="collapse <?php echo strpos($_SERVER['REQUEST_URI'], '/atendimento') !== false ? 'show' : ''; ?>"
            id="submenuAtendimento">
            <ul class="nav flex-column ms-3 border-start border-secondary border-opacity-25 ps-2">
                <li class="nav-item my-1">
                    <a class="nav-link py-1 <?php echo strpos($_SERVER['REQUEST_URI'], '/atendimento/agentes') !== false && strpos($_SERVER['REQUEST_URI'], 'chats') === false ? 'active text-white' : 'text-white-50'; ?>"
                        href="<?php echo BASE_URL; ?>admin/atendimento/agentes">
                        <i class="bi bi-cpu me-2"></i> Agentes de IA
                    </a>
                </li>
                <li class="nav-item my-1">
                    <a class="nav-link py-1 <?php echo strpos($_SERVER['REQUEST_URI'], 'chats') !== false ? 'active text-white' : 'text-white-50'; ?>"
                        href="<?php echo BASE_URL; ?>admin/atendimento/chats">
                        <i class="bi bi-chat-dots me-2"></i> Chats
                    </a>
                </li>
            </ul>
        </div>
    </li>

    <!-- Gestão de Produtos (Mentorias, Cursos, etc) -->
    <li class="nav-item">
        <a class="nav-link <?php echo strpos($_SERVER['REQUEST_URI'], '/admin/produtos') !== false ? 'active' : ''; ?>"
            href="<?php echo BASE_URL; ?>admin/produtos">
            <i class="bi bi-box-seam"></i>
            <span>Produtos</span>
        </a>
    </li>

    <!-- Configurações (Geral) -->
    <li class="nav-item">
        <a href="<?php echo BASE_URL; ?>configuracoes" class="nav-link">
            <i class="bi bi-gear"></i>
            <span>Configurações</span>
        </a>
    </li>

<?php endif; ?>


<!-- ============================================== -->
<!-- SUPER ADMINISTRAÇÃO (GLOBAL) -->
<!-- ============================================== -->

<?php if (isset($_SESSION['tipo']) && $_SESSION['tipo'] === 'superadmin'): ?>

    <li class="nav-item mt-2">
        <a href="<?php echo BASE_URL; ?>admin/dashboard"
            class="nav-link <?php echo strpos($_SERVER['REQUEST_URI'], '/admin/dashboard') !== false ? 'active' : ''; ?>">
            <i class="bi bi-speedometer"></i>
            <span>Dashboard</span>
        </a>
    </li>

    <!-- CRM (Pipeline + Config) -->
    <li class="nav-item">
        <a class="nav-link d-flex align-items-center justify-content-between <?php echo strpos($_SERVER['REQUEST_URI'], '/admin/crm') !== false ? '' : 'collapsed'; ?>"
            href="#submenuCRMSuper" data-bs-toggle="collapse" role="button"
            aria-expanded="<?php echo strpos($_SERVER['REQUEST_URI'], '/admin/crm') !== false ? 'true' : 'false'; ?>"
            aria-controls="submenuCRMSuper">
            <span>
                <i class="bi bi-kanban me-2"></i> Comercial
            </span>
            <i class="bi bi-chevron-down" style="font-size: 0.8em;"></i>
        </a>
        <div class="collapse <?php echo strpos($_SERVER['REQUEST_URI'], '/admin/crm') !== false ? 'show' : ''; ?>"
            id="submenuCRMSuper">
            <ul class="nav flex-column ms-3 border-start border-secondary border-opacity-25 ps-2">
                <li class="nav-item my-1">
                    <a class="nav-link py-1 <?php echo strpos($_SERVER['REQUEST_URI'], '/admin/crm/index') !== false || rtrim($_SERVER['REQUEST_URI'], '/') == rtrim(BASE_URL . 'admin/crm', '/') ? 'active text-white' : 'text-white-50'; ?>"
                        href="<?php echo BASE_URL; ?>admin/crm">
                        <i class="bi bi-view-list me-2"></i> Pipeline
                    </a>
                </li>
                <li class="nav-item my-1">
                    <a class="nav-link py-1 <?php echo strpos($_SERVER['REQUEST_URI'], '/admin/crm/config') !== false ? 'active text-white' : 'text-white-50'; ?>"
                        href="<?php echo BASE_URL; ?>admin/crm/config">
                        <i class="bi bi-gear me-2"></i> Configurações
                    </a>
                </li>
            </ul>
        </div>
    </li>

    <!--    Marketing (Leads) -->
    <li class="nav-item">
        <a class="nav-link d-flex align-items-center justify-content-between <?php echo strpos($_SERVER['REQUEST_URI'], '/marketing') !== false ? '' : 'collapsed'; ?>"
            href="#submenuMarketingSuper" data-bs-toggle="collapse" role="button"
            aria-expanded="<?php echo strpos($_SERVER['REQUEST_URI'], '/marketing') !== false ? 'true' : 'false'; ?>"
            aria-controls="submenuMarketingSuper">
            <span>
                <i class="bi bi-megaphone me-2"></i> Marketing
            </span>
            <i class="bi bi-chevron-down" style="font-size: 0.8em;"></i>
        </a>
        <div class="collapse <?php echo strpos($_SERVER['REQUEST_URI'], '/marketing') !== false ? 'show' : ''; ?>"
            id="submenuMarketingSuper">
            <ul class="nav flex-column ms-3 border-start border-secondary border-opacity-25 ps-2">
                <li class="nav-item my-1">
                    <a class="nav-link py-1 <?php echo strpos($_SERVER['REQUEST_URI'], '/marketing/leads') !== false ? 'active text-white' : 'text-white-50'; ?>"
                        href="<?php echo BASE_URL; ?>modules/admin/marketing/leads">
                        <i class="bi bi-people me-2"></i> Base de Leads
                    </a>
                </li>
                <li class="nav-item my-1">
                    <a class="nav-link py-1 <?php echo strpos($_SERVER['REQUEST_URI'], '/marketing/audiencias') !== false ? 'active text-white' : 'text-white-50'; ?>"
                        href="<?php echo BASE_URL; ?>modules/admin/marketing/audiencias">
                        <i class="bi bi-funnel me-2"></i> Audiências
                    </a>
                </li>
                <li class="nav-item my-1">
                    <a class="nav-link py-1 <?php echo strpos($_SERVER['REQUEST_URI'], '/marketing/gerador_leads') !== false ? 'active text-white' : 'text-white-50'; ?>"
                        href="<?php echo BASE_URL; ?>admin/marketing/gerador_leads">
                        <i class="bi bi-robot me-2"></i> Gerador de Leads
                    </a>
                </li>
            </ul>
        </div>
    </li>

    <!-- Atendimento (Agentes de IA) - SuperAdmin -->
    <li class="nav-item">
        <a class="nav-link d-flex align-items-center justify-content-between <?php echo strpos($_SERVER['REQUEST_URI'], '/atendimento') !== false ? '' : 'collapsed'; ?>"
            href="#submenuAtendimentoSuper" data-bs-toggle="collapse" role="button"
            aria-expanded="<?php echo strpos($_SERVER['REQUEST_URI'], '/atendimento') !== false ? 'true' : 'false'; ?>"
            aria-controls="submenuAtendimentoSuper">
            <span>
                <i class="bi bi-robot me-2"></i> Atendimento
            </span>
            <i class="bi bi-chevron-down" style="font-size: 0.8em;"></i>
        </a>
        <div class="collapse <?php echo strpos($_SERVER['REQUEST_URI'], '/atendimento') !== false ? 'show' : ''; ?>"
            id="submenuAtendimentoSuper">
            <ul class="nav flex-column ms-3 border-start border-secondary border-opacity-25 ps-2">
                <li class="nav-item my-1">
                    <a class="nav-link py-1 <?php echo strpos($_SERVER['REQUEST_URI'], '/atendimento/agentes') !== false && strpos($_SERVER['REQUEST_URI'], 'chats') === false ? 'active text-white' : 'text-white-50'; ?>"
                        href="<?php echo BASE_URL; ?>admin/atendimento/agentes">
                        <i class="bi bi-cpu me-2"></i> Agentes de IA
                    </a>
                </li>
                <li class="nav-item my-1">
                    <a class="nav-link py-1 <?php echo strpos($_SERVER['REQUEST_URI'], 'chats') !== false ? 'active text-white' : 'text-white-50'; ?>"
                        href="<?php echo BASE_URL; ?>admin/atendimento/chats">
                        <i class="bi bi-chat-dots me-2"></i> Chats
                    </a>
                </li>
            </ul>
        </div>
    </li>

    <!-- Group: Produtos (Produtos, Diagnósticos, Ferramentas) -->
    <li class="nav-item">
        <a class="nav-link d-flex align-items-center justify-content-between <?php echo (strpos($_SERVER['REQUEST_URI'], '/admin/produtos') !== false || strpos($_SERVER['REQUEST_URI'], '/admin/diagnosticos') !== false || strpos($_SERVER['REQUEST_URI'], '/admin/ferramentas') !== false) ? '' : 'collapsed'; ?>"
            href="#submenuProdutos" data-bs-toggle="collapse" role="button"
            aria-expanded="<?php echo (strpos($_SERVER['REQUEST_URI'], '/admin/produtos') !== false || strpos($_SERVER['REQUEST_URI'], '/admin/diagnosticos') !== false || strpos($_SERVER['REQUEST_URI'], '/admin/ferramentas') !== false) ? 'true' : 'false'; ?>"
            aria-controls="submenuProdutos">
            <span>
                <i class="bi bi-box-seam me-2"></i> Produtos
            </span>
            <i class="bi bi-chevron-down" style="font-size: 0.8em;"></i>
        </a>
        <div class="collapse <?php echo (strpos($_SERVER['REQUEST_URI'], '/admin/produtos') !== false || strpos($_SERVER['REQUEST_URI'], '/admin/diagnosticos') !== false || strpos($_SERVER['REQUEST_URI'], '/admin/ferramentas') !== false) ? 'show' : ''; ?>"
            id="submenuProdutos">
            <ul class="nav flex-column ms-3 border-start border-secondary border-opacity-25 ps-2">
                <li class="nav-item my-1">
                    <a class="nav-link py-1 <?php echo strpos($_SERVER['REQUEST_URI'], '/admin/produtos') !== false ? 'active text-white' : 'text-white-50'; ?>"
                        href="<?php echo BASE_URL; ?>admin/produtos">
                        <i class="bi bi-box-seam me-2"></i> Produtos (Listagem)
                    </a>
                </li>
                <li class="nav-item my-1">
                    <a class="nav-link py-1 <?php echo strpos($_SERVER['REQUEST_URI'], '/admin/diagnosticos') !== false ? 'active text-white' : 'text-white-50'; ?>"
                        href="<?php echo BASE_URL; ?>admin/diagnosticos">
                        <i class="bi bi-list-check me-2"></i> Diagnósticos
                    </a>
                </li>
                <li class="nav-item my-1">
                    <a class="nav-link py-1 <?php echo strpos($_SERVER['REQUEST_URI'], '/admin/ferramentas') !== false ? 'active text-white' : 'text-white-50'; ?>"
                        href="<?php echo BASE_URL; ?>admin/ferramentas">
                        <i class="bi bi-tools me-2"></i> Ferramentas
                    </a>
                </li>
            </ul>
        </div>
    </li>

    <!-- Sistema (Empresas + Usuários) -->
    <li class="nav-item">
        <a class="nav-link d-flex align-items-center justify-content-between <?php echo (strpos($_SERVER['REQUEST_URI'], '/empresas') !== false || strpos($_SERVER['REQUEST_URI'], '/usuarios') !== false) ? '' : 'collapsed'; ?>"
            href="#submenuSistema" data-bs-toggle="collapse" role="button"
            aria-expanded="<?php echo (strpos($_SERVER['REQUEST_URI'], '/empresas') !== false || strpos($_SERVER['REQUEST_URI'], '/usuarios') !== false) ? 'true' : 'false'; ?>"
            aria-controls="submenuSistema">
            <span>
                <i class="bi bi-hdd-network me-2"></i> Sistema
            </span>
            <i class="bi bi-chevron-down" style="font-size: 0.8em;"></i>
        </a>
        <div class="collapse <?php echo (strpos($_SERVER['REQUEST_URI'], '/empresas') !== false || strpos($_SERVER['REQUEST_URI'], '/usuarios') !== false) ? 'show' : ''; ?>"
            id="submenuSistema">
            <ul class="nav flex-column ms-3 border-start border-secondary border-opacity-25 ps-2">
                <li class="nav-item my-1">
                    <a class="nav-link py-1 <?php echo strpos($_SERVER['REQUEST_URI'], '/admin/empresas') !== false ? 'active text-white' : 'text-white-50'; ?>"
                        href="<?php echo BASE_URL; ?>admin/empresas">
                        <i class="bi bi-buildings me-2"></i> Empresas
                    </a>
                </li>
                <li class="nav-item my-1">
                    <a class="nav-link py-1 <?php echo strpos($_SERVER['REQUEST_URI'], '/admin/usuarios') !== false ? 'active text-white' : 'text-white-50'; ?>"
                        href="<?php echo BASE_URL; ?>admin/usuarios">
                        <i class="bi bi-people-fill me-2"></i> Usuários
                    </a>
                </li>
                <li class="nav-item my-1">
                    <a class="nav-link py-1 <?php echo strpos($_SERVER['REQUEST_URI'], '/admin/deploy') !== false ? 'active text-white' : 'text-white-50'; ?>"
                        href="<?php echo BASE_URL; ?>modules/admin/deploy">
                        <i class="bi bi-rocket-takeoff me-2"></i> Deploy
                    </a>
                </li>
            </ul>
        </div>
    </li>

    <li class="nav-item">
        <a href="<?php echo BASE_URL; ?>admin/financeiro"
            class="nav-link <?php echo strpos($_SERVER['REQUEST_URI'], '/admin/financeiro') !== false ? 'active' : ''; ?>">
            <i class="bi bi-wallet2"></i>
            <span>Financeiro</span>
        </a>
    </li>

<?php endif; ?>