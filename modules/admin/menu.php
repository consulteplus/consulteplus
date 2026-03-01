<?php
// modules/admin/menu.php
// Sidebar exclusivo para o painel administrativo
?>

<!-- Header do Menu (Branding + Toggle) -->
<div class="d-flex align-items-start justify-content-between mb-4 px-2 position-relative">
    <div class="text-center flex-grow-1">
        <h4 class="text-white fw-bold m-0">Admin</h4>
        <small class="text-white-50">Consulte+</small>
    </div>
    <button id="sidebarToggle" class="btn text-white p-0 d-none d-md-block position-absolute" style="right: 0; top: 0;"
        title="Recolher Menu">
        <i class="bi bi-chevron-left fs-4" id="sidebarToggleIcon"></i>
    </button>
</div>

<ul class="nav flex-column">

    <?php
    // Inclui os itens de menu
    include __DIR__ . '/menu_items.php';
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
                <a href="<?php echo BASE_URL; ?>logout.php" class="text-danger text-decoration-none" title="Sair"
                    style="transition: opacity 0.2s;" onmouseover="this.style.opacity='0.7'"
                    onmouseout="this.style.opacity='1'">
                    <i class="bi bi-box-arrow-right fs-5"></i>
                </a>
            </div>
        </div>
    </li>

</ul>