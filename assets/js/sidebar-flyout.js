// Sidebar Flyout Menu Handler
// Mantém o submenu aberto quando o mouse está sobre ele ou sobre o item pai

document.addEventListener('DOMContentLoaded', function () {
    if (!document.body.classList.contains('sidebar-mini')) {
        return; // Só ativa no modo mini
    }

    const navItems = document.querySelectorAll('.sidebar .nav-item');

    navItems.forEach(item => {
        const collapse = item.querySelector('.collapse');
        if (!collapse) return;

        let hideTimeout;

        // Mostrar submenu ao passar mouse no item pai
        item.addEventListener('mouseenter', function () {
            clearTimeout(hideTimeout);
            collapse.style.display = 'block';
        });

        // Esconder submenu com delay ao sair do item pai
        item.addEventListener('mouseleave', function (e) {
            // Verifica se o mouse foi para o submenu
            const rect = collapse.getBoundingClientRect();
            const mouseX = e.clientX;
            const mouseY = e.clientY;

            const isOverSubmenu = (
                mouseX >= rect.left &&
                mouseX <= rect.right &&
                mouseY >= rect.top &&
                mouseY <= rect.bottom
            );

            if (!isOverSubmenu) {
                hideTimeout = setTimeout(() => {
                    collapse.style.display = 'none';
                }, 300); // 300ms de delay
            }
        });

        // Manter submenu aberto quando mouse está sobre ele
        collapse.addEventListener('mouseenter', function () {
            clearTimeout(hideTimeout);
        });

        // Esconder submenu ao sair dele
        collapse.addEventListener('mouseleave', function () {
            hideTimeout = setTimeout(() => {
                collapse.style.display = 'none';
            }, 200);
        });
    });

    // Atualizar ao toggle da sidebar
    const toggleBtn = document.getElementById('sidebarToggle');
    if (toggleBtn) {
        toggleBtn.addEventListener('click', function () {
            setTimeout(() => {
                location.reload(); // Recarrega para reinicializar os event listeners
            }, 300);
        });
    }
});
