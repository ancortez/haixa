<?php
require_once 'db_connection.php';

header('Content-Type: application/json');

if (!isset($_GET['cp']) || !isset($_GET['estado'])) {
    echo json_encode(['error' => 'Se requieren los parámetros cp y estado']);
    exit();
}

$cp = trim($_GET['cp']);
$estadoId = intval($_GET['estado']);

try {
    $db = Database::getInstance();
    $pdo = $db->getConnection();
    
    // Verificar que el CP pertenezca al estado
    $stmt = $pdo->prepare("
        SELECT COUNT(*) 
        FROM codigos_postales cp
        JOIN municipio m ON cp.id_municipio = m.id_municipio
        WHERE cp.codigo_postal = ? AND m.id_estado = ?
    ");
    $stmt->execute([$cp, $estadoId]);
    
    if ($stmt->fetchColumn() == 0) {
        echo json_encode(['error' => 'El código postal no existe en el estado seleccionado']);
        exit();
    }
    
    // Obtener colonias para el CP
    $stmt = $pdo->prepare("
        SELECT c.id_colonia, c.nombre_colonia 
        FROM colonias c
        JOIN codigos_postales cp ON c.id_codigo_postal = cp.id_codigo_postal
        WHERE cp.codigo_postal = ?
        ORDER BY c.nombre_colonia
    ");
    $stmt->execute([$cp]);
    $colonias = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode($colonias);
} catch (PDOException $e) {
    echo json_encode(['error' => 'Error al cargar colonias: ' . $e->getMessage()]);
}