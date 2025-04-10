<?php
require_once 'db_connection.php';
header('Content-Type: application/json');

try {
    $db = Database::getInstance();
    $pdo = $db->getConnection();
    
    $input = $_POST;
    
    // Si es principal, quitar principal a los demás
    if ($input['principal'] ?? false) {
        $pdo->prepare("UPDATE contacto_cliente SET principal = 0 WHERE id_cliente = ?")
            ->execute([$input['id_cliente']]);
    }
    
    $stmt = $pdo->prepare("INSERT INTO contacto_cliente 
        (id_cliente, id_medio_contacto, valor_contacto, principal) 
        VALUES (?, ?, ?, ?)");
    
    $stmt->execute([
        $input['id_cliente'],
        $input['id_medio_contacto'],
        $input['valor_contacto'],
        $input['principal'] ?? 0
    ]);
    
    echo json_encode(['success' => true]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}