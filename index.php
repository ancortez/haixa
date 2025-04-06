<?php
require_once 'includes/auth.php';
require_once 'includes/db_connection.php';
require_once 'includes/functions.php'; // Agrega esta línea

if (!isLoggedIn()) {
    header("Location: login.php");
    exit();
}

// Configuración de zona horaria
date_default_timezone_set('America/Mazatlan');

$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HAIXA - <?php echo $page_title ?? 'Inicio'; ?></title>
    
    <!-- Bootstrap CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />

    <!-- Estilos locales -->
    <link rel="stylesheet" href="assets/css/main.css">

    <!--<link rel="stylesheet" href="assets/css/main.css">-->
    <!--<link rel="stylesheet" href="assets/css/overrides.css">-->
    <!-- Favicon -->
    <!--<link rel="icon" href="assets/img/favicon.ico">-->

</head>
<body>
    <?php //include 'includes/header.php'; ?>
    <?php include 'includes/nav.php'; ?>
    
    <main class="container-fluid">
        <?php 
        $page = isset($_GET['page']) ? 'pages/'.$_GET['page'].'.php' : 'pages/dashboard.php';
        include file_exists($page) ? $page : 'pages/404.php';
        ?>
    </main>
    
    <?php include 'includes/footer.php'; ?>
    
    <!-- jQuery CDN -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- Puedes cargar el JS de Bootstrap pero NO inicializar dropdowns -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" 
        data-no-dropdowns></script>

<script>
  // Evita que Bootstrap inicialice dropdowns
  document.addEventListener('DOMContentLoaded', function() {
    if (document.querySelector('[data-no-dropdowns]')) {
      Dropdown.prototype._getConfig = function() { return { autoClose: false }; };
      Dropdown.prototype.show = function() {};
    }
  });
</script>
    <script src="assets/js/dropdowns.js"></script> <!-- Agrega esta línea -->

</body>
</html>