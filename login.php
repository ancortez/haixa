<?php
// Al inicio del archivo
if (isset($_GET['logout'])) {
    $success_message = 'Sesión cerrada correctamente';
}

session_start();
require_once 'includes/db_connection.php';
require_once 'includes/auth.php';

// Configuración de zona horaria
date_default_timezone_set('America/Mazatlan');

// Mostrar mensaje si se cerró sesión
$logout_message = '';
if (isset($_GET['logout'])) {
    $logout_message = '<div class="alert alert-success">Sesión cerrada correctamente.</div>';
}

if (isLoggedIn()) {
    header("Location: index.php");
    exit();
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING);
    $password = $_POST['password']; // No sanitizar para el hash
    
    if (authenticateUser($username, $password)) {
        $_SESSION['user'] = $username;
        $_SESSION['last_activity'] = time();
        header("Location: launch-dashboard.php");
        exit();
    } else {
        $error = "Usuario o contraseña incorrectos";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>HAIXA - Login</title>

    <!-- Estilos locales -->
    <link rel="stylesheet" href="assets/css/login.css">
    <!-- Bootstrap CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

</head>
<body class="login-body">
    <div class="login-container">
        
        <div class="login-logo">
            <img src="assets/img/logo.png" alt="HAIXA Logo">
        </div>
        <div class="login-form">
            <?php if ($error): ?>
            <?php echo $logout_message; ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>
            <form method="POST">
                <div class="form-group">
                    <label for="username">Usuario</label>
                    <input type="text" id="username" name="username" class="form-control" required autofocus>
                </div>
                <div class="form-group">
                    <label for="password">Contraseña</label>
                    <input type="password" id="password" name="password" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-primary btn-block">Ingresar</button>
            </form>
            <?php if (isset($success_message)): ?>
            <div class="alert alert-success mt-3">
                <?= htmlspecialchars($success_message) ?>
            </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>