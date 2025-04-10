<?php

require_once 'includes/db_connection.php';
require_once 'includes/auth.php';

// Obtener la conexión usando tu clase Database
$db = Database::getInstance();
$pdo = $db->getConnection();

// Verificar si se ha seleccionado un cliente
if (!isset($_GET['id'])) {
    header("Location: ../../../index.php");
    exit();
}

$cliente_id = intval($_GET['id']);

// Obtener datos del cliente
function obtenerClientePorId($id) {
    global $db; // Usamos $db en lugar de $pdo
    
    // Obtener datos del cliente
    $cliente = $db->fetchOne("
        SELECT c.*, g.nombre_genero 
        FROM clientes c
        LEFT JOIN genero g ON c.id_genero = g.id_genero
        WHERE c.id_cliente = ?
    ", [$id]);
    
    if (!$cliente) {
        return null;
    }
    
    // Obtener contactos
    $cliente['contactos'] = $db->fetchAll("
        SELECT cc.*, mc.nombre_medio 
        FROM contacto_cliente cc
        JOIN medios_de_contacto mc ON cc.id_medio_contacto = mc.id_medio_contacto
        WHERE cc.id_cliente = ?
        ORDER BY cc.principal DESC, cc.fecha_registro DESC
    ", [$id]);

    // Obtener domicilios
    $cliente['domicilios'] = $db->fetchAll("
        SELECT d.*, 
               e.nombre_estado AS estado,
               m.nombre_municipio AS municipio,
               cp.codigo_postal,
               col.nombre_colonia AS colonia,
               cal.nombre_calle AS calle
        FROM domicilio d
        JOIN estado e ON d.id_estado = e.id_estado
        JOIN municipio m ON d.id_municipio = m.id_municipio
        JOIN codigos_postales cp ON d.id_codigo_postal = cp.id_codigo_postal
        JOIN colonias col ON d.id_colonia = col.id_colonia
        LEFT JOIN calles cal ON d.id_calle = cal.id_calle
        WHERE d.id_cliente = ?
        ORDER BY d.id_domicilio ASC
    ", [$id]);
    
    return $cliente;
}

$cliente = obtenerClientePorId($cliente_id);

if (!$cliente) {
    header("Location: ../../../index.php?error=cliente_no_encontrado");
    exit();
}

// Obtener lista de estados para el formulario de domicilios
$estados = $db->fetchAll("SELECT * FROM estado ORDER BY nombre_estado");

// Obtener el primer domicilio como activo por defecto
$domicilio_actual = $cliente['domicilios'][0] ?? null;
?>

<div class="row">
    <!-- Panel izquierdo (20%) -->
    <div class="col-md-3">
        <div class="card">
            <div class="card-body text-center">
                <img src="assets/img/default_user.png" class="img-fluid rounded-circle mb-3" style="max-width: 150px;">
                <h3 class="card-title"><?php echo htmlspecialchars($cliente['nombres'] . ' ' . $cliente['apellido_paterno'] . ' ' . ($cliente['apellido_materno'] ?? '')); ?></h3>
                <p class="text-muted"><?php echo htmlspecialchars($cliente['rfc'] ?? 'Sin RFC'); ?></p>
                <p><span class="badge bg-secondary"><?php echo htmlspecialchars($cliente['nombre_genero'] ?? 'No especificado'); ?></span></p>
                
                <hr>
                
                <div class="d-grid gap-2">
                    <button class="btn btn-outline-primary">
                        <i class="fas fa-file-invoice"></i> Nuevo Contrato
                    </button>
                    <button class="btn btn-outline-secondary">
                        <i class="fas fa-print"></i> Imprimir Ficha
                    </button>
                </div>
            </div>
        </div>
    </div>
    
     <!-- Panel derecho (80%) con pestañas -->
    <div class="col-md-9">
        <ul class="nav nav-tabs" id="clienteTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="identificacion-tab" data-bs-toggle="tab" data-bs-target="#identificacion" type="button">Identificación</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="contacto-tab" data-bs-toggle="tab" data-bs-target="#contacto" type="button">Medios de Contacto</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="domicilios-tab" data-bs-toggle="tab" data-bs-target="#domicilios" type="button">Domicilios</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="contratos-tab" data-bs-toggle="tab" data-bs-target="#contratos" type="button">Contratos</button>
            </li>
        </ul>
        
        <div class="tab-content p-3 border border-top-0 rounded-bottom">
            
            <!-- Pestaña Identificación -->
            <div class="tab-pane fade show active" id="identificacion" role="tabpanel">
                <form id="formIdentificacion">
                    <input type="hidden" name="id_cliente" value="<?php echo $cliente_id; ?>">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label for="nombres" class="form-label">Nombre(s)</label>
                            <input type="text" class="form-control" id="nombres" name="nombres" value="<?php echo htmlspecialchars($cliente['nombres']); ?>" autocomplete="off">
                        </div>
                        <div class="col-md-4">
                            <label for="apellido_paterno" class="form-label">Apellido Paterno</label>
                            <input type="text" class="form-control" id="apellido_paterno" name="apellido_paterno" value="<?php echo htmlspecialchars($cliente['apellido_paterno']); ?>" autocomplete="off">
                        </div>
                        <div class="col-md-4">
                            <label for="apellido_materno" class="form-label">Apellido Materno</label>
                            <input type="text" class="form-control" id="apellido_materno" name="apellido_materno" value="<?php echo htmlspecialchars($cliente['apellido_materno'] ?? ''); ?>" autocomplete="off">
                        </div>
                        <div class="col-md-4">
                            <label for="rfc" class="form-label">RFC</label>
                            <input type="text" class="form-control" id="rfc" name="rfc" value="<?php echo htmlspecialchars($cliente['rfc'] ?? ''); ?>" autocomplete="off">
                        </div>
                        <div class="col-md-4">
                            <label for="fecha_nacimiento" class="form-label">Fecha Nacimiento</label>
                            <input type="date" class="form-control" id="fecha_nacimiento" name="fecha_nacimiento" value="<?php echo htmlspecialchars($cliente['fecha_nacimiento'] ?? ''); ?>">
                        </div>
                        <div class="col-md-4">
                            <label for="genero" class="form-label">Género</label>
                            <select class="form-select" id="genero" name="genero">
                                <?php
                                $generos = $db->fetchAll("SELECT * FROM genero");
                                foreach ($generos as $genero) {
                                    $selected = ($genero['id_genero'] == $cliente['id_genero']) ? 'selected' : '';
                                    echo "<option value=\"{$genero['id_genero']}\" $selected>{$genero['nombre_genero']}</option>";
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    
                    <div class="mt-3">
                        <button type="submit" class="btn btn-primary" id="btnGuardarIdentificacion">
                            <i class="fas fa-save"></i> Guardar Datos
                        </button>
                        <button type="button" class="btn btn-outline-secondary" id="btnCancelarIdentificacion">
                            Cancelar
                        </button>
                    </div>
                </form>
            </div>

            <!-- Modal de Confirmación -->
            <div class="modal fade" id="confirmacionModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header bg-warning">
                            <h5 class="modal-title"><i class="fas fa-exclamation-triangle"></i> Confirmar Cambios</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <p>¿Está seguro de modificar estos datos? Los cambios realizados son importantes y afectarán registros relacionados.</p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                            <button type="button" class="btn btn-primary" id="btnConfirmarCambios">Confirmar</button>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Pestaña Contacto -->
            <div class="tab-pane fade" id="contacto" role="tabpanel">
                <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#nuevoContactoModal">
                    <i class="fas fa-plus"></i> Nuevo Medio de Contacto
                </button>
                
                <?php if (empty($cliente['contactos'])): ?>
                <div class="alert alert-info">
                    No se han registrado medios de contacto para este cliente.
                </div>
                <?php else: ?>
                <div class="table-responsive">
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
                            <?php foreach ($cliente['contactos'] as $contacto): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($contacto['nombre_medio']); ?></td>
                                <td><?php echo htmlspecialchars($contacto['valor_contacto']); ?></td>
                                <td>
                                    <?php if ($contacto['principal']): ?>
                                    <span class="badge bg-success"><i class="fas fa-check"></i></span>
                                    <?php else: ?>
                                    <span class="badge bg-secondary"><i class="fas fa-times"></i></span>
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
                </div>
                <?php endif; ?>
            </div>
            
            <!-- Pestaña Domicilios -->
            <div class="tab-pane fade" id="domicilios" role="tabpanel">
                <div class="card mb-3">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div>
                            <button class="btn btn-sm btn-primary me-2" id="btnNuevoDomicilio">
                                <i class="fas fa-plus"></i> Nuevo Domicilio
                            </button>
                            <h5 class="mb-0 d-inline-block" id="domicilioTitulo">
                                <?php echo $domicilio_actual ? 'DOMICILIO ' . $domicilio_actual['id_domicilio'] : 'SIN DOMICILIOS'; ?>
                            </h5>
                        </div>
                        <div>
                            <?php if ($domicilio_actual): ?>
                            <a href="#" class="text-primary" id="linkModificarDomicilio">
                                <i class="fas fa-edit"></i> Modificar datos
                            </a>
                            <button class="btn btn-sm btn-primary d-none ms-2" id="btnGuardarDomicilio">
                                <i class="fas fa-save"></i> Guardar
                            </button>
                            <button class="btn btn-sm btn-outline-secondary d-none" id="btnCancelarDomicilio">
                                Cancelar
                            </button>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="card-body">
                        <form id="formDomicilio">
                            <input type="hidden" id="domicilio_id" name="id" value="<?php echo $domicilio_actual['id_domicilio'] ?? ''; ?>">
                            <input type="hidden" name="id_cliente" value="<?php echo $cliente_id; ?>">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label for="estado" class="form-label">Estado</label>
                                    <select class="form-select" id="estado" name="estado" disabled required>
                                        <option value="">Seleccionar...</option>
                                        <?php foreach ($estados as $estado): ?>
                                        <option value="<?php echo $estado['id_estado']; ?>" 
                                            <?php echo ($domicilio_actual && $domicilio_actual['id_estado'] == $estado['id_estado']) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($estado['nombre_estado']); ?>
                                        </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label for="municipio" class="form-label">Municipio</label>
                                    <select class="form-select" id="municipio" name="municipio" disabled required>
                                        <option value="">Seleccionar estado primero</option>
                                        <?php if ($domicilio_actual): ?>
                                            <option value="<?php echo $domicilio_actual['id_municipio']; ?>" selected>
                                                <?php echo htmlspecialchars($domicilio_actual['municipio']); ?>
                                            </option>
                                        <?php endif; ?>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label for="codigo_postal" class="form-label">Código Postal</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" id="codigo_postal" name="codigo_postal" 
                                               value="<?php echo $domicilio_actual['codigo_postal'] ?? ''; ?>" disabled required>
                                        <button class="btn btn-outline-secondary" type="button" id="btnBuscarColonias" disabled>
                                            <i class="fas fa-search"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label for="colonia" class="form-label">Colonia</label>
                                    <select class="form-select" id="colonia" name="colonia" disabled required>
                                        <option value="">Ingrese CP primero</option>
                                        <?php if ($domicilio_actual): ?>
                                            <option value="<?php echo $domicilio_actual['id_colonia']; ?>" selected>
                                                <?php echo htmlspecialchars($domicilio_actual['colonia']); ?>
                                            </option>
                                        <?php endif; ?>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label for="calle" class="form-label">Calle</label>
                                    <select class="form-select" id="calle" name="calle" disabled>
                                        <option value="">Seleccione colonia primero</option>
                                        <?php if ($domicilio_actual && $domicilio_actual['id_calle']): ?>
                                            <option value="<?php echo $domicilio_actual['id_calle']; ?>" selected>
                                                <?php echo htmlspecialchars($domicilio_actual['calle']); ?>
                                            </option>
                                        <?php endif; ?>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label for="numero_exterior" class="form-label">Número Exterior</label>
                                    <input type="text" class="form-control" id="numero_exterior" name="numero_exterior" 
                                           value="<?php echo htmlspecialchars($domicilio_actual['numero_exterior'] ?? ''); ?>" disabled>
                                </div>
                                <div class="col-md-3">
                                    <label for="numero_interior" class="form-label">Número Interior</label>
                                    <input type="text" class="form-control" id="numero_interior" name="numero_interior" 
                                           value="<?php echo htmlspecialchars($domicilio_actual['numero_interior'] ?? ''); ?>" disabled>
                                </div>
                                <div class="col-md-3">
                                    <label for="entre_calle_1" class="form-label">Entre calle 1</label>
                                    <input type="text" class="form-control" id="entre_calle_1" name="entre_calle_1" 
                                           value="<?php echo htmlspecialchars($domicilio_actual['entre_calle_1'] ?? ''); ?>" disabled>
                                </div>
                                <div class="col-md-3">
                                    <label for="entre_calle_2" class="form-label">Entre calle 2</label>
                                    <input type="text" class="form-control" id="entre_calle_2" name="entre_calle_2" 
                                           value="<?php echo htmlspecialchars($domicilio_actual['entre_calle_2'] ?? ''); ?>" disabled>
                                </div>
                                <div class="col-12">
                                    <label for="referencia" class="form-label">Referencia</label>
                                    <textarea class="form-control" id="referencia" name="referencia" rows="2" disabled><?php echo htmlspecialchars($domicilio_actual['referencia'] ?? ''); ?></textarea>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Lista de Domicilios</h5>
                        <button class="btn btn-sm btn-primary" id="btnNuevoDomicilio">
                            <i class="fas fa-plus"></i> Nuevo Domicilio
                        </button>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Seleccionar</th>
                                        <th>Domicilio</th>
                                        <th>Principal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($cliente['domicilios'] as $domicilio): ?>
                                    <tr>
                                        <td>
                                            <input type="radio" name="domicilioSeleccionado" value="<?php echo $domicilio['id_domicilio']; ?>" 
                                                <?php echo ($domicilio['id_domicilio'] == $domicilio_actual['id_domicilio']) ? 'checked' : ''; ?>
                                                onchange="cambiarDomicilio(<?php echo $domicilio['id_domicilio']; ?>)">
                                        </td>
                                        <td>
                                            <?php echo htmlspecialchars($domicilio['calle'] . ' ' . $domicilio['numero_exterior']); ?>
                                            <?php if (!empty($domicilio['numero_interior'])) echo 'Int. ' . htmlspecialchars($domicilio['numero_interior']); ?>
                                            <br>
                                            <small class="text-muted"><?php echo htmlspecialchars($domicilio['colonia'] . ', CP ' . $domicilio['codigo_postal']); ?></small>
                                        </td>
                                        <td>
                                            <?php if ($domicilio['principal']): ?>
                                            <span class="badge bg-success">Principal</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Pestaña Contratos -->
            <div class="tab-pane fade" id="contratos" role="tabpanel">
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> Módulo de contratos en desarrollo. Próximamente podrá gestionar los contratos asociados a este cliente.
                </div>
            </div>
        </div>
    </div>
</div>


<!-- Modal Nuevo Contacto - Actualizado -->
<div class="modal fade" id="nuevoContactoModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="fas fa-plus-circle"></i> Nuevo Medio de Contacto</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formNuevoContacto">
                <div class="modal-body">
                    <input type="hidden" name="id_cliente" value="<?php echo $cliente_id; ?>">
                    <div class="mb-3">
                        <label for="tipo_contacto" class="form-label">Tipo de Contacto *</label>
                        <select class="form-select" id="tipo_contacto" name="id_medio_contacto" required>
                            <option value="">Seleccionar...</option>
                            <?php
                            // Obtener medios de contacto desde la base de datos
                            $medios_contacto = $db->fetchAll("SELECT * FROM medios_de_contacto ORDER BY nombre_medio");
                            foreach ($medios_contacto as $medio) {
                                echo '<option value="'.$medio['id_medio_contacto'].'">'.htmlspecialchars($medio['nombre_medio']).'</option>';
                            }
                            ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="valor_contacto" class="form-label">Valor *</label>
                        <input type="text" class="form-control" id="valor_contacto" name="valor_contacto" required>
                        <div class="form-text">Ingrese el teléfono o email según corresponda</div>
                    </div>
                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox" id="principal" name="principal" value="1">
                        <label class="form-check-label" for="principal">Contacto principal</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times"></i> Cancelar
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Guardar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Editar Contacto -->
<div class="modal fade" id="editarContactoModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Editar Medio de Contacto</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formEditarContacto">
                <div class="modal-body">
                    <input type="hidden" id="contacto_id" name="id_contacto">
                    <input type="hidden" name="id_cliente" value="<?php echo $cliente_id; ?>">
                    <div class="mb-3">
                        <label for="edit_tipo_contacto" class="form-label">Tipo de Contacto</label>
                        <select class="form-select" id="edit_tipo_contacto" name="id_medio_contacto" required>
                            <option value="">Seleccionar...</option>
                            <?php
                            // Obtener medios de contacto desde la base de datos
                            $medios_contacto = $db->fetchAll("SELECT * FROM medios_de_contacto ORDER BY nombre_medio");
                            foreach ($medios_contacto as $medio) {
                                echo '<option value="'.$medio['id_medio_contacto'].'">'.htmlspecialchars($medio['nombre_medio']).'</option>';
                            }
                            ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="edit_valor_contacto" class="form-label">Valor</label>
                        <input type="text" class="form-control" id="edit_valor_contacto" name="valor_contacto" required>
                    </div>
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" id="edit_principal" name="principal" value="1">
                        <label class="form-check-label" for="edit_principal">Marcar como contacto principal</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Script para manejar la interacción del cliente -->
<script>
// Inicialización de eventos para el modal de editar contacto
document.getElementById('editarContactoModal').addEventListener('show.bs.modal', function (event) {
    var button = event.relatedTarget;
    var id = button.getAttribute('data-id');
    var tipo = button.getAttribute('data-tipo');
    var valor = button.getAttribute('data-valor');
    var principal = button.getAttribute('data-principal');
    
    document.getElementById('contacto_id').value = id;
    document.getElementById('edit_tipo_contacto').value = tipo;
    document.getElementById('edit_valor_contacto').value = valor;
    document.getElementById('edit_principal').checked = (principal === '1');
});

// Función para cambiar el domicilio mostrado
function cambiarDomicilio(idDomicilio) {
    // Implementar la carga del domicilio seleccionado via AJAX
    fetch(`/haixa/includes/cargar_domicilio.php?id=${idDomicilio}`)
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                alert(data.error);
                return;
            }
            
            // Actualizar el formulario con los datos del domicilio
            document.getElementById('domicilio_id').value = data.id_domicilio;
            document.getElementById('domicilioTitulo').textContent = `DOMICILIO ${data.id_domicilio}`;
            document.getElementById('estado').value = data.id_estado;
            // Actualizar otros campos del domicilio...
            
            // Cargar municipios para el estado seleccionado
            cargarMunicipios(data.id_estado, data.id_municipio);
        });
}

// Función para cargar municipios
function cargarMunicipios(idEstado, idMunicipioSeleccionado = null) {
    const municipioSelect = document.getElementById('municipio');
    municipioSelect.innerHTML = '<option value="">Cargando...</option>';
    
    fetch(`/haixa/includes/cargar_municipios.php?estado=${idEstado}`)
        .then(response => response.json())
        .then(data => {
            municipioSelect.innerHTML = '<option value="">Seleccionar...</option>';
            data.forEach(municipio => {
                const option = document.createElement('option');
                option.value = municipio.id_municipio;
                option.textContent = municipio.nombre_municipio;
                if (idMunicipioSeleccionado && municipio.id_municipio == idMunicipioSeleccionado) {
                    option.selected = true;
                }
                municipioSelect.appendChild(option);
            });
        });
}
</script>