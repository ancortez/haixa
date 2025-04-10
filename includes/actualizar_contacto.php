<?php
require_once 'db_connection.php';
require_once 'auth.php';

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
    $contactoId = intval($input['id_contacto'] ?? 0);
    
    if ($contactoId <= 0) {
        throw new Exception('ID de contacto inválido');
    }

    // Validaciones básicas
    if (empty($input['id_medio_contacto'])) {
        throw new Exception('El tipo de contacto es requerido');
    }
    
    if (empty($input['valor_contacto'])) {
        throw new Exception('El valor del contacto es requerido');
    }
    
    // Si es principal, quitar principal a los demás
    if ($input['principal'] ?? false) {
        $pdo->prepare("UPDATE contacto_cliente SET principal = 0 WHERE id_cliente = ? AND id_contacto != ?")
            ->execute([$input['id_cliente'], $contactoId]);
    }
    
    $stmt = $pdo->prepare("UPDATE contacto_cliente SET 
        id_medio_contacto = ?,
        valor_contacto = ?,
        principal = ?,
        fecha_actualizacion = NOW()
        WHERE id_contacto = ?");
    
    $success = $stmt->execute([
        $input['id_medio_contacto'],
        trim($input['valor_contacto']),
        $input['principal'] ?? 0,
        $contactoId
    ]);
    
    if (!$success) {
        throw new Exception('Error al ejecutar la actualización');
    }
    
    echo json_encode(['success' => true, 'message' => 'Contacto actualizado correctamente']);
    
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['error' => $e->getMessage()]);
}