<?php
require_once 'db_connection.php';

header('Content-Type: application/json');

try {
    $stmt = $pdo->query("SELECT * FROM estado ORDER BY nombre_estado");
    $estados = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode($estados);
} catch (PDOException $e) {
    echo json_encode(['error' => 'Error al cargar estados: ' . $e->getMessage()]);
}