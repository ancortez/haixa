<?php
require_once 'db_connection.php';

$clienteId = intval($_GET['id_cliente'] ?? 0);

if ($clienteId <= 0) {
    die('ID de cliente inválido');
}

try {
    $db = Database::getInstance();
    $pdo = $db->getConnection();
    
    $contactos = $pdo->prepare("
        SELECT cc.*, mc.nombre_medio 
        FROM contacto_cliente cc
        JOIN medios_de_contacto mc ON cc.id_medio_contacto = mc.id_medio_contacto
        WHERE cc.id_cliente = ?
        ORDER BY cc.principal DESC, cc.fecha_registro DESC
    ")->execute([$clienteId])->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($contactos)) {
        echo '<tr><td colspan="4" class="text-center">No hay contactos registrados</td></tr>';
        exit;
    }
    
    foreach ($contactos as $contacto) {
        echo '<tr>
            <td>'.htmlspecialchars($contacto['nombre_medio']).'</td>
            <td>'.htmlspecialchars($contacto['valor_contacto']).'</td>
            <td>'.($contacto['principal'] ? '<span class="badge bg-success">Principal</span>' : '').'</td>
            <td>
                <button class="btn btn-sm btn-outline-primary btn-editar-contacto" 
                    data-bs-toggle="modal" 
                    data-bs-target="#editarContactoModal"
                    data-id="'.$contacto['id_contacto'].'"
                    data-tipo="'.$contacto['id_medio_contacto'].'"
                    data-valor="'.htmlspecialchars($contacto['valor_contacto']).'"
                    data-principal="'.($contacto['principal'] ? '1' : '0').'">
                    <i class="fas fa-edit"></i>
                </button>
                <button class="btn btn-sm btn-outline-danger btn-eliminar-contacto" 
                    data-id="'.$contacto['id_contacto'].'">
                    <i class="fas fa-trash-alt"></i>
                </button>
            </td>
        </tr>';
    }
} catch (Exception $e) {
    echo '<tr><td colspan="4" class="text-danger">Error al cargar contactos: '.htmlspecialchars($e->getMessage()).'</td></tr>';
}