<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Cargando HAIXA...</title>
    <script>
        // Abrir index.php en una ventana sin controles
        window.onload = function() {
            const popup = window.open(
                "index.php", 
                "HAIXA_DASHBOARD", 
                "width=1920,height=1080,fullscreen=yes,location=no,menubar=no,status=no,toolbar=no"
            );
            
            if (!popup || popup.closed) {
                alert("¡Por favor desbloquea las ventanas emergentes para usar HAIXA!");
                window.location.href = "login.php";
            } else {
                window.close(); // Cierra esta página intermedia
            }
        };
    </script>
</head>
<body>
    <p>Cargando el panel de control...</p>
</body>
</html>