<?php
/**
 * Archivo para cerrar sesión de manera segura
 */

require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/db_connection.php';

// Verificar si hay sesión activa
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Registrar logout si hay usuario en sesión
if (isset($_SESSION['user_id'])) {
    logLogout($_SESSION['user_id']);
}

// Destruir todas las variables de sesión
$_SESSION = array();

// Borrar la cookie de sesión
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(), 
        '', 
        time() - 42000,
        $params["path"], 
        $params["domain"],
        $params["secure"], 
        $params["httponly"]
    );
}

// Destruir la sesión
session_destroy();

// Redirigir al login con mensaje de éxito
header("Location: login.php?logout=1");
exit();
?>