document.addEventListener('DOMContentLoaded', function() {
    // Inicialización manual de los dropdowns (solo para debug)
    const dropdowns = document.querySelectorAll('.dropdown-toggle');
    
    dropdowns.forEach(function(dropdown) {
        dropdown.addEventListener('click', function(e) {
            console.log('Dropdown clickeado:', this.id);
            
            // Solo para debug - Bootstrap debería manejar esto automáticamente
            const dropdownMenu = this.nextElementSibling;
            if (dropdownMenu.classList.contains('show')) {
                dropdownMenu.classList.remove('show');
            } else {
                dropdownMenu.classList.add('show');
            }
        });
    });
});