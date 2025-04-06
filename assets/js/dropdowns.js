document.addEventListener('DOMContentLoaded', function() {
    console.log('Script dropdowns.js cargado correctamente');
    
    // Cerrar todos los dropdowns
    function closeAllDropdowns() {
        document.querySelectorAll('.dropdown-menu').forEach(menu => {
            menu.style.opacity = '0';
            menu.style.visibility = 'hidden';
            menu.style.pointerEvents = 'none';
        });
        document.querySelectorAll('.dropdown-toggle').forEach(toggle => {
            toggle.classList.remove('active');
        });
    }

    // Abrir un dropdown específico
    function openDropdown(menu) {
        closeAllDropdowns();

        // Ajuste de posición para menús del lado derecho
        if (menu.classList.contains('dropdown-menu-end')) {
            menu.style.right = '0';
            menu.style.left = 'auto';
        }
    
        menu.style.opacity = '1';
        menu.style.visibility = 'visible';
        menu.style.pointerEvents = 'auto';
        menu.previousElementSibling.classList.add('active');
    }

    // Manejar clicks en los dropdown toggles
    document.querySelectorAll('.dropdown-toggle').forEach(toggle => {
        toggle.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            const menu = this.nextElementSibling;
            const isOpen = menu.style.visibility === 'visible';
            
            if (isOpen) {
                closeAllDropdowns();
            } else {
                openDropdown(menu);
            }
        });
    });

    // Cerrar al hacer click fuera
    document.addEventListener('click', function() {
        closeAllDropdowns();
    });

    // Prevenir que clicks dentro del dropdown lo cierren
    document.querySelectorAll('.dropdown-menu').forEach(menu => {
        menu.addEventListener('click', function(e) {
            e.stopPropagation();
        });
    });

    console.log('Dropdowns inicializados correctamente');
});