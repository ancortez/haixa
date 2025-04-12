<?php
require_once 'db_connection.php';
require_once 'auth.php';

header('Content-Type: application/json');

if (!isLoggedIn()) {
    http_response_code(401);
    echo json_encode(['error' => 'No autorizado']);
    exit();
}

try {
    $db = Database::getInstance();
    $pdo = $db->getConnection();
    
    $contactoId = intval($_GET['id'] ?? 0);
    
    if ($contactoId <= 0) {
        throw new Exception('ID de contacto inválido');
    }

    // Verificar si el contacto existe
    $stmt = $pdo->prepare("SELECT id_cliente FROM contacto_cliente WHERE id_contacto = ?");
    $stmt->execute([$contactoId]);
    $contacto = $stmt->fetch();
    
    if (!$contacto) {
        throw new Exception('El contacto no existe o ya fue eliminado');
    }
    
    // Iniciar transacción para mayor seguridad
    $pdo->beginTransaction();
    
    // Eliminar el contacto
    $stmt = $pdo->prepare("DELETE FROM contacto_cliente WHERE id_contacto = ?");
    $stmt->execute([$contactoId]);
    
    // Verificar si se afectó alguna fila
    if ($stmt->rowCount() === 0) {
        throw new Exception('No se encontró el contacto para eliminar');
    }
    
    $pdo->commit();
    
    echo json_encode([
        'success' => true,
        'message' => 'Contacto eliminado correctamente',
        'id_eliminado' => $contactoId
    ]);
    
} catch (Exception $e) {
    if (isset($pdo) && $pdo->inTransaction()) {
        $pdo->rollBack();
    }
    http_response_code(400);
    echo json_encode([
        'error' => $e->getMessage(),
        'code' => $e->getCode()
    ]);
}
?>