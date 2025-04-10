<?php
require_once __DIR__ . '/db_connection.php';

header('Content-Type: application/json');

// Obtiene la conexión usando tu patrón Singleton
$db = Database::getInstance();
$pdo = $db->getConnection();

$input = json_decode(file_get_contents('php://input'), true);

try {
    if ($input['tipo'] === 'nombre') {
        $nombre = '%' . $input['nombre'] . '%';
        $apellido_paterno = '%' . $input['apellido_paterno'] . '%';
        $apellido_materno = $input['apellido_materno'] ? '%' . $input['apellido_materno'] . '%' : '%';
        
        // Usando tu nuevo método executeQuery
        $clientes = $db->fetchAll("
            SELECT c.id_cliente, c.nombres, c.apellido_paterno, c.apellido_materno, c.rfc, g.nombre_genero
            FROM clientes c
            LEFT JOIN genero g ON c.id_genero = g.id_genero
            WHERE c.nombres LIKE ? AND c.apellido_paterno LIKE ? AND c.apellido_materno LIKE ?
            ORDER BY c.apellido_paterno, c.apellido_materno, c.nombres
            LIMIT 50
        ", [$nombre, $apellido_paterno, $apellido_materno]);
    } else {
        $rfc = '%' . $input['rfc'] . '%';
        
        $clientes = $db->fetchAll("
            SELECT c.id_cliente, c.nombres, c.apellido_paterno, c.apellido_materno, c.rfc, g.nombre_genero
            FROM clientes c
            LEFT JOIN genero g ON c.id_genero = g.id_genero
            WHERE c.rfc LIKE ?
            ORDER BY c.rfc
            LIMIT 50
        ", [$rfc]);
    }
    
    echo json_encode($clientes ?: []); // Devuelve array vacío si no hay resultados
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'error' => 'Error en el servidor',
        'message' => $e->getMessage()
    ]);
}