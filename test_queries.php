<?php
require_once 'includes/db_connection.php';

try {
    $db = getDBConnection();
    
    // Test 1: Ejecutar consulta simple
    $sql = "SELECT 1 as test";
    $result = $db->executeQuery($sql)->fetch();
    echo "Test 1 OK: " . $result['test'] . "<br>";
    
    // Test 2: Consulta con parámetros
    $sql = "SELECT ? as test";
    $result = $db->executeQuery($sql, [2])->fetch();
    echo "Test 2 OK: " . $result['test'] . "<br>";
    
    // Test 3: Insertar en LOG_ACCESOS (si la tabla existe)
    try {
        $sql = "INSERT INTO LOG_ACCESOS 
                (id_usuario, fecha_acceso, ip_acceso, user_agent) 
                VALUES 
                (?, NOW(), ?, ?)";
        $db->executeQuery($sql, [1, '127.0.0.1', 'Test Agent']);
        echo "Test 3 OK: Insert realizado<br>";
    } catch (Exception $e) {
        echo "Test 3: " . $e->getMessage() . "<br>";
    }
    
} catch (Exception $e) {
    die("Error en pruebas: " . $e->getMessage());
}