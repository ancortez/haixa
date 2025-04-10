<?php
require_once 'db_connection.php';

header('Content-Type: application/json');

if (!isset($_GET['estado'])) {
    echo json_encode(['error' => 'Se requiere el parámetro estado']);
    exit();
}

$estadoId = intval($_GET['estado']);

try {
    $stmt = $pdo->prepare("SELECT * FROM municipio WHERE id_estado = ? ORDER BY nombre_municipio");
    $stmt->execute([$estadoId]);
    $municipios = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode($municipios);
} catch (PDOException $e) {
    echo json_encode(['error' => 'Error al cargar municipios: ' . $e->getMessage()]);
}