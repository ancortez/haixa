<?php
/**
 * Sistema de autenticación y autorización para HAIXA
 * 
 * Maneja el login, verificación de sesión y control de acceso
 */

// Solo iniciar sesión si no está activa
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Obtiene la conexión a la base de datos
 */
function getDB() {
    try {
        require_once __DIR__ . '/db_connection.php';
        return getDBConnection();
    } catch (Exception $e) {
        error_log("Error en getDB(): " . $e->getMessage());
        throw new RuntimeException("Error al conectar con la base de datos");
    }
}

// Tiempo de inactividad antes de expirar la sesión (30 minutos)
define('SESSION_TIMEOUT', 1800);
define('LOG_TABLE', 'LOG_ACCESOS'); // Nombre consistente para la tabla

/**
 * Verifica si el usuario está logueado
 */
function isLoggedIn() {
    if (!isset($_SESSION['user_id'])) {
        return false;
    }
    
    // Verificar tiempo de inactividad
    if (isset($_SESSION['last_activity'])) {
        $inactive_time = time() - $_SESSION['last_activity'];
        if ($inactive_time > SESSION_TIMEOUT) {
            logout();
            return false;
        }
    }
    
    // Renovar tiempo de actividad
    $_SESSION['last_activity'] = time();
    
    return true;
}

/**
 * Autentica un usuario
 */
function authenticateUser($username, $password) {
    // Validar entrada
    $username = trim($username);
    if (empty($username) || empty($password)) {
        return false;
    }
    
    try {
        $db = getDB();
        
        $sql = "SELECT id_usuario, nombre_usuario, contrasena, estatus, id_rol 
                FROM USUARIOS 
                WHERE nombre_usuario = :username 
                LIMIT 1";
        
        $user = $db->fetchOne($sql, [':username' => $username]);
        
        if (!$user || $user['estatus'] != 1) {
            return false;
        }
        
        // Verificar contraseña
        if (password_verify($password, $user['contrasena'])) {
            // Contraseña correcta - configurar sesión
            $_SESSION['user_id'] = $user['id_usuario'];
            $_SESSION['username'] = $user['nombre_usuario'];
            $_SESSION['user_role'] = $user['id_rol'];
            $_SESSION['last_activity'] = time();
            $_SESSION['ip_address'] = $_SERVER['REMOTE_ADDR'];
            $_SESSION['user_agent'] = $_SERVER['HTTP_USER_AGENT'];
            
            // Registrar el acceso
            logAccess($user['id_usuario']);
            
            return true;
        }
        
        return false;
    } catch (PDOException $e) {
        error_log("Error en autenticación: " . $e->getMessage());
        return false;
    }
}

/**
 * Cierra la sesión del usuario
 */
function logout() {
    // Registrar el cierre de sesión
    if (isset($_SESSION['user_id'])) {
        logLogout($_SESSION['user_id']);
    }
    
    // Destruir la sesión completamente
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
    
    session_destroy();
}

/**
 * Verifica si la sesión es válida (no ha sido secuestrada)
 */
function validateSession() {
    if (!isset($_SESSION['ip_address']) || 
        !isset($_SESSION['user_agent']) ||
        $_SESSION['ip_address'] != $_SERVER['REMOTE_ADDR'] ||
        $_SESSION['user_agent'] != $_SERVER['HTTP_USER_AGENT']) {
        logout();
        header("Location: login.php?error=session_invalid");
        exit();
    }
}

/**
 * Verifica si el usuario tiene el rol necesario
 */
function hasRole($required_role) {
    if (!isLoggedIn()) {
        return false;
    }
    
    // Si el usuario es superadmin, tiene acceso a todo
    if ($_SESSION['user_role'] == 1) { // Asumiendo que 1 es superadmin
        return true;
    }
    
    // Verificar el rol específico
    return ($_SESSION['user_role'] == $required_role);
}

/**
 * Registra un acceso exitoso en el log
 */
function logAccess($user_id) {
    
    try {
        $sql = "INSERT INTO " . LOG_TABLE . " 
                (id_usuario, fecha_acceso, ip_acceso, user_agent) 
                VALUES 
                (:user_id, NOW(), :ip, :ua)";
        
        getDB()->executeQuery($sql, [
            ':user_id' => $user_id,
            ':ip' => $_SERVER['REMOTE_ADDR'],
            ':ua' => $_SERVER['HTTP_USER_AGENT']
        ]);
    } catch (PDOException $e) {
        error_log("Error al registrar acceso: " . $e->getMessage());
    }
}

/**
 * Registra un logout en el log
 */
function logLogout($user_id) {
    try {
        $sql = "UPDATE LOG_ACCESOS 
                SET fecha_logout = NOW() 
                WHERE id_usuario = :user_id 
                AND fecha_logout IS NULL 
                ORDER BY fecha_acceso DESC 
                LIMIT 1";
        
        getDB()->executeQuery($sql, [':user_id' => $user_id]);
    } catch (PDOException $e) {
        error_log("Error al registrar logout: " . $e->getMessage());
    }
}

/**
 * Genera un hash seguro para contraseñas
 */
function generatePasswordHash($password) {
    return password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
}

/**
 * Genera un token CSRF y lo almacena en sesión
 */
function generateCSRFToken() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Valida un token CSRF
 */
function validateCSRFToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

// Verificar la sesión en cada carga si el usuario está logueado
if (isLoggedIn()) {
    validateSession();
}

function changePassword($user_id, $new_password) {
    global $db;
    
    $hashed_password = password_hash($new_password, PASSWORD_BCRYPT);
    
    $sql = "UPDATE USUARIOS SET contrasena = :password WHERE id_usuario = :user_id";
    
    try {
        $db->executeQuery($sql, [
            ':password' => $hashed_password,
            ':user_id' => $user_id
        ]);
        return true;
    } catch (PDOException $e) {
        error_log("Error al cambiar contraseña: " . $e->getMessage());
        return false;
    }
}

function generateLogoutToken() {
    $_SESSION['logout_token'] = bin2hex(random_bytes(32));
    return $_SESSION['logout_token'];
}

function validateLogoutToken($token) {
    return isset($_SESSION['logout_token']) && hash_equals($_SESSION['logout_token'], $token);
}
?>