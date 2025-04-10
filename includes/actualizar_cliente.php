<?php
require_once __DIR__ . '/db_connection.php';
require_once __DIR__ . '/auth.php';

header('Content-Type: application/json');

// Verificar autenticación
if (!isLoggedIn()) {
    http_response_code(401);
    echo json_encode(['error' => 'No autorizado']);
    exit();
}

try {
    $db = Database::getInstance();
    $pdo = $db->getConnection();
    
    $input = $_POST;
    $id = intval($input['id_cliente'] ?? 0);
    
    if ($id <= 0) {
        throw new Exception('ID de cliente inválido');
    }

    // Validaciones básicas
    if (empty($input['nombres'])) {
        throw new Exception('El nombre es requerido');
    }
    
    if (empty($input['apellido_paterno'])) {
        throw new Exception('El apellido paterno es requerido');
    }
    
    // Validar RFC si existe
    if (!empty($input['rfc'])) {
        if (!preg_match('/^[A-Z&Ñ]{3,4}[0-9]{6}[A-Z0-9]{3}$/', $input['rfc'])) {
            throw new Exception('El RFC no tiene un formato válido');
        }
    }
    
    $stmt = $pdo->prepare("UPDATE clientes SET 
        nombres = ?, 
        apellido_paterno = ?,
        apellido_materno = ?,
        rfc = ?,
        fecha_nacimiento = ?,
        id_genero = ?,
        fecha_actualizacion = NOW()
        WHERE id_cliente = ?");
    
    $success = $stmt->execute([
        trim($input['nombres']),
        trim($input['apellido_paterno']),
        !empty($input['apellido_materno']) ? trim($input['apellido_materno']) : null,
        !empty($input['rfc']) ? trim($input['rfc']) : null,
        !empty($input['fecha_nacimiento']) ? $input['fecha_nacimiento'] : null,
        !empty($input['genero']) ? intval($input['genero']) : null,
        $id
    ]);
    
    if (!$success) {
        throw new Exception('Error al ejecutar la consulta');
    }
    
    echo json_encode(['success' => true, 'message' => 'Datos actualizados correctamente']);
    
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['error' => $e->getMessage()]);
}