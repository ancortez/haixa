<?php
require_once 'includes/db_connection.php';

try {
    $db = getDBConnection();
    echo "¡Conexión exitosa!<br>";
    echo "Versión de MySQL: " . $db->query('SELECT version()')->fetchColumn();
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}