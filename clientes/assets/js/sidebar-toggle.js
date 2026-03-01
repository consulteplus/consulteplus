document.addEventListener('DOMContentLoaded', function () {
    const toggleBtn = document.getElementById('sidebarToggle');
    const toggleIcon = document.getElementById('sidebarToggleIcon'); // Assuming an icon element exists
    const body = document.body;

    // Function to update sidebar state and toggle icon
    function updateSidebarState(isMini) {
        if (isMini) {
            body.classList.add('sidebar-mini');
            if (toggleIcon) {
                toggleIcon.classList.remove('bi-chevron-left');
                toggleIcon.classList.add('bi-list');
            }
        } else {
            body.classList.remove('sidebar-mini');
            if (toggleIcon) {
                toggleIcon.classList.remove('bi-list');
                toggleIcon.classList.add('bi-chevron-left');
            }
        }
    }

    // Initialize state
    const savedState = localStorage.getItem('sidebarState');
    const isMobile = window.innerWidth < 768;

    // Mobile: Force Expanded (Hidden by CSS default) - Never Mini
    if (isMobile) {
        updateSidebarState(false);
    }
    // Desktop: Respect User Preference
    else if (savedState === 'collapsed') {
        updateSidebarState(true);
    } else {
        updateSidebarState(false);
    }

    // Auto-remove mini on resize to mobile
    window.addEventListener('resize', function () {
        if (window.innerWidth < 768 && document.body.classList.contains('sidebar-mini')) {
            updateSidebarState(false);
        }
    });

    if (toggleBtn) {
        toggleBtn.addEventListener('click', function (e) {
            e.preventDefault();
            const isMini = !body.classList.contains('sidebar-mini');
            updateSidebarState(isMini);
            localStorage.setItem('sidebarState', isMini ? 'collapsed' : 'expanded');
        });
    }

    // ---------------------------------------------------------
    // NEW: Prevent Bootstrap Collapse Click in Mini Mode
    // This stops the "Accordion" effect when hovering is the intended UX
    // ---------------------------------------------------------
    const navLinks = document.querySelectorAll('.sidebar .nav-link[data-bs-toggle="collapse"]');
    navLinks.forEach(link => {
        link.addEventListener('click', function (e) {
            if (document.body.classList.contains('sidebar-mini')) {
                e.preventDefault();
                e.stopPropagation();
                // Optional: You could trigger expand here if you wanted click to open sidebar
                return false;
            }
        }, true); // Use capture phase to catch it before Bootstrap
    });
});
