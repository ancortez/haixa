<?php
require_once 'includes/db_connection.php';

// Datos del usuario administrador
$username = 'admin';
$fullname = 'José Angel Cortez Moreno';
$role_id = 1; // Superadmin
$password = 'sabritasPEPSICO1';
$status = 1;

// Encriptar la contraseña
$hashed_password = password_hash($password, PASSWORD_BCRYPT);

try {
    // Verificar si el usuario ya existe
    $sql_check = "SELECT id_usuario FROM USUARIOS WHERE nombre_usuario = :username";
    $stmt_check = $db->executeQuery($sql_check, [':username' => $username]);
    
    if ($stmt_check->rowCount() > 0) {
        die("El usuario admin ya existe en la base de datos.");
    }

    // Insertar el nuevo usuario
    $sql = "INSERT INTO USUARIOS 
            (nombre_usuario, nombre_completo, id_rol, contrasena, estatus, fecha_creacion) 
            VALUES 
            (:username, :fullname, :role_id, :password, :status, NOW())";
    
    $params = [
        ':username' => $username,
        ':fullname' => $fullname,
        ':role_id' => $role_id,
        ':password' => $hashed_password,
        ':status' => $status
    ];
    
    $db->executeQuery($sql, $params);
    
    echo "Usuario administrador creado exitosamente.<br>";
    echo "Usuario: admin<br>";
    echo "Contraseña: sabritasPEPSICO1 (la que proporcionaste)<br>";
    echo "<strong>¡Elimina este archivo (create_admin.php) después de usarlo por seguridad!</strong>";
    
} catch (PDOException $e) {
    die("Error al crear el usuario administrador: " . $e->getMessage());
}
?>