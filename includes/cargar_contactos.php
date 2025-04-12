<?php
require_once 'db_connection.php';
require_once 'auth.php';

header('Content-Type: text/html');

if (!isset($_GET['id_cliente'])) {
    die('ID de cliente no proporcionado');
}

$cliente_id = intval($_GET['id_cliente']);

// Obtener contactos del cliente
$db = Database::getInstance();
$contactos = $db->fetchAll("
    SELECT cc.*, mc.nombre_medio 
    FROM contacto_cliente cc
    JOIN medios_de_contacto mc ON cc.id_medio_contacto = mc.id_medio_contacto
    WHERE cc.id_cliente = ?
    ORDER BY cc.principal DESC, cc.fecha_registro DESC
", [$cliente_id]);

if (empty($contactos)): ?>
    <div class="alert alert-info">
        No se han registrado medios de contacto para este cliente.
    </div>
<?php else: ?>
    <table class="table table-hover">
        <thead>
            <tr>
                <th>Tipo</th>
                <th>Valor</th>
                <th>Principal</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($contactos as $contacto): ?>
            <tr>
                <td><?php echo htmlspecialchars($contacto['nombre_medio']); ?></td>
                <td><?php echo htmlspecialchars($contacto['valor_contacto']); ?></td>
                <td>
                    <?php if ($contacto['principal']): ?>
                    <span class="badge bg-success"><i class="fas fa-check"></i></span>
                    <?php else: ?>
                    <span class="badge bg-secondary"></i></span>
                    <?php endif; ?>
                </td>
                <td>
                    <button class="btn btn-sm btn-outline-primary btn-editar-contacto" 
                            data-bs-toggle="modal" 
                            data-bs-target="#editarContactoModal"
                            data-id="<?php echo $contacto['id_contacto']; ?>"
                            data-tipo="<?php echo $contacto['id_medio_contacto']; ?>"
                            data-valor="<?php echo htmlspecialchars($contacto['valor_contacto']); ?>"
                            data-principal="<?php echo $contacto['principal'] ? '1' : '0'; ?>">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="btn btn-sm btn-outline-danger btn-eliminar-contacto" data-id="<?php echo $contacto['id_contacto']; ?>">
                        <i class="fas fa-trash-alt"></i>
                    </button>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>