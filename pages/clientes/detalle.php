<?php
// Verificar si se ha seleccionado un cliente
if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$cliente_id = intval($_GET['id']);
// Aquí iría la consulta para obtener los datos del cliente
// $cliente = obtenerClientePorId($cliente_id);
?>

<div class="row">
    <!-- Panel izquierdo (20%) -->
    <div class="col-md-3">
        <div class="card">
            <div class="card-body text-center">
                <img src="assets/img/user-default.png" class="img-fluid rounded-circle mb-3" style="max-width: 150px;">
                <h3 class="card-title"><?php echo htmlspecialchars($cliente['nombre_completo']); ?></h3>
                <p class="text-muted"><?php echo htmlspecialchars($cliente['rfc']); ?></p>
                <p><span class="badge bg-secondary"><?php echo htmlspecialchars($cliente['genero']); ?></span></p>
                
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
                <button class="nav-link" id="contacto-tab" data-bs-toggle="tab" data-bs-target="#contacto" type="button">Datos de Contacto</button>
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
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label for="nombres" class="form-label">Nombre(s)</label>
                            <input type="text" class="form-control" id="nombres" name="nombres" value="<?php echo htmlspecialchars($cliente['nombres']); ?>" disabled>
                        </div>
                        <div class="col-md-4">
                            <label for="apellido_paterno" class="form-label">Apellido Paterno</label>
                            <input type="text" class="form-control" id="apellido_paterno" name="apellido_paterno" value="<?php echo htmlspecialchars($cliente['apellido_paterno']); ?>" disabled>
                        </div>
                        <div class="col-md-4">
                            <label for="apellido_materno" class="form-label">Apellido Materno</label>
                            <input type="text" class="form-control" id="apellido_materno" name="apellido_materno" value="<?php echo htmlspecialchars($cliente['apellido_materno']); ?>" disabled>
                        </div>
                        <div class="col-md-4">
                            <label for="rfc" class="form-label">RFC</label>
                            <input type="text" class="form-control" id="rfc" name="rfc" value="<?php echo htmlspecialchars($cliente['rfc']); ?>" disabled>
                        </div>
                        <div class="col-md-4">
                            <label for="fecha_nacimiento" class="form-label">Fecha Nacimiento</label>
                            <input type="date" class="form-control" id="fecha_nacimiento" name="fecha_nacimiento" value="<?php echo htmlspecialchars($cliente['fecha_nacimiento']); ?>" disabled>
                        </div>
                        <div class="col-md-4">
                            <label for="genero" class="form-label">Género</label>
                            <select class="form-select" id="genero" name="genero" disabled>
                                <option value="1" <?php echo ($cliente['id_genero'] == 1) ? 'selected' : ''; ?>>Masculino</option>
                                <option value="2" <?php echo ($cliente['id_genero'] == 2) ? 'selected' : ''; ?>>Femenino</option>
                                <option value="3" <?php echo ($cliente['id_genero'] == 3) ? 'selected' : ''; ?>>No binario</option>
                                <option value="4" <?php echo ($cliente['id_genero'] == 4) ? 'selected' : ''; ?>>No especificado</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="mt-3">
                        <button type="button" class="btn btn-outline-primary" id="btnModificarIdentificacion">
                            <i class="fas fa-edit"></i> Modificar Datos
                        </button>
                        <button type="submit" class="btn btn-primary d-none" id="btnGuardarIdentificacion">
                            <i class="fas fa-save"></i> Guardar Cambios
                        </button>
                        <button type="button" class="btn btn-outline-secondary d-none" id="btnCancelarIdentificacion">
                            Cancelar
                        </button>
                    </div>
                </form>
            </div>
            
            <!-- Pestaña Contacto -->
            <div class="tab-pane fade" id="contacto" role="tabpanel">
                <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#nuevoContactoModal">
                    <i class="fas fa-plus"></i> Nuevo Medio de Contacto
                </button>
                
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
                                <td><?php echo htmlspecialchars($contacto['tipo_contacto']); ?></td>
                                <td><?php echo htmlspecialchars($contacto['valor_contacto']); ?></td>
                                <td>
                                    <?php if ($contacto['principal']): ?>
                                    <span class="badge bg-success"><i class="fas fa-check"></i></span>
                                    <?php else: ?>
                                    <span class="badge bg-secondary"><i class="fas fa-times"></i></span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-outline-primary" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#editarContactoModal"
                                            data-id="<?php echo $contacto['id']; ?>"
                                            data-tipo="<?php echo $contacto['id_tipo_contacto']; ?>"
                                            data-valor="<?php echo htmlspecialchars($contacto['valor_contacto']); ?>"
                                            data-principal="<?php echo $contacto['principal'] ? '1' : '0'; ?>">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-danger btnEliminarContacto" data-id="<?php echo $contacto['id']; ?>">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            
            <!-- Pestaña Domicilios -->
            <div class="tab-pane fade" id="domicilios" role="tabpanel">
                <div class="card mb-3">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0" id="domicilioTitulo">DOMICILIO 1</h5>
                        <div>
                            <button class="btn btn-sm btn-outline-primary" id="btnModificarDomicilio">
                                <i class="fas fa-edit"></i> Modificar
                            </button>
                            <button class="btn btn-sm btn-primary d-none" id="btnGuardarDomicilio">
                                <i class="fas fa-save"></i> Guardar
                            </button>
                            <button class="btn btn-sm btn-outline-secondary d-none" id="btnCancelarDomicilio">
                                Cancelar
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <form id="formDomicilio">
                            <input type="hidden" id="domicilio_id" name="id">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label for="estado" class="form-label">Estado</label>
                                    <select class="form-select" id="estado" name="estado" disabled required>
                                        <option value="">Seleccionar...</option>
                                        <?php foreach ($estados as $estado): ?>
                                        <option value="<?php echo $estado['id']; ?>" <?php echo ($domicilio_actual['id_estado'] == $estado['id']) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($estado['nombre']); ?>
                                        </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label for="municipio" class="form-label">Municipio</label>
                                    <select class="form-select" id="municipio" name="municipio" disabled required>
                                        <option value="">Seleccionar estado primero</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label for="codigo_postal" class="form-label">Código Postal</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" id="codigo_postal" name="codigo_postal" disabled required>
                                        <button class="btn btn-outline-secondary" type="button" id="btnBuscarColonias" disabled>
                                            <i class="fas fa-search"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label for="colonia" class="form-label">Colonia</label>
                                    <select class="form-select" id="colonia" name="colonia" disabled required>
                                        <option value="">Ingrese CP primero</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label for="calle" class="form-label">Calle</label>
                                    <select class="form-select" id="calle" name="calle" disabled required>
                                        <option value="">Seleccione colonia primero</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label for="numero_exterior" class="form-label">Número Exterior</label>
                                    <input type="text" class="form-control" id="numero_exterior" name="numero_exterior" disabled>
                                </div>
                                <div class="col-md-3">
                                    <label for="numero_interior" class="form-label">Número Interior</label>
                                    <input type="text" class="form-control" id="numero_interior" name="numero_interior" disabled>
                                </div>
                                <div class="col-md-3">
                                    <label for="entre_calle_1" class="form-label">Entre calle 1</label>
                                    <input type="text" class="form-control" id="entre_calle_1" name="entre_calle_1" disabled>
                                </div>
                                <div class="col-md-3">
                                    <label for="entre_calle_2" class="form-label">Entre calle 2</label>
                                    <input type="text" class="form-control" id="entre_calle_2" name="entre_calle_2" disabled>
                                </div>
                                <div class="col-12">
                                    <label for="referencia" class="form-label">Referencia</label>
                                    <textarea class="form-control" id="referencia" name="referencia" rows="2" disabled></textarea>
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
                                            <input type="radio" name="domicilioSeleccionado" value="<?php echo $domicilio['id']; ?>" 
                                                <?php echo ($domicilio['id'] == $domicilio_actual['id']) ? 'checked' : ''; ?>>
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
                    <i class="fas fa-info-circle"></i> Módulo de contratos en desarrollo.
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Nuevo Contacto -->
<div class="modal fade" id="nuevoContactoModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Nuevo Medio de Contacto</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formNuevoContacto">
                <div class="modal-body">
                    <input type="hidden" name="cliente_id" value="<?php echo $cliente_id; ?>">
                    <div class="mb-3">
                        <label for="tipo_contacto" class="form-label">Tipo de Contacto</label>
                        <select class="form-select" id="tipo_contacto" name="tipo_contacto" required>
                            <option value="">Seleccionar...</option>
                            <option value="1">Teléfono</option>
                            <option value="2">Celular</option>
                            <option value="3">Email</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="valor_contacto" class="form-label">Valor</label>
                        <input type="text" class="form-control" id="valor_contacto" name="valor_contacto" required>
                    </div>
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" id="principal" name="principal">
                        <label class="form-check-label" for="principal">Marcar como contacto principal</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar</button>
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
                    <input type="hidden" id="contacto_id" name="id">
                    <input type="hidden" name="cliente_id" value="<?php echo $cliente_id; ?>">
                    <div class="mb-3">
                        <label for="edit_tipo_contacto" class="form-label">Tipo de Contacto</label>
                        <select class="form-select" id="edit_tipo_contacto" name="tipo_contacto" required>
                            <option value="">Seleccionar...</option>
                            <option value="1">Teléfono</option>
                            <option value="2">Celular</option>
                            <option value="3">Email</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="edit_valor_contacto" class="form-label">Valor</label>
                        <input type="text" class="form-control" id="edit_valor_contacto" name="valor_contacto" required>
                    </div>
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" id="edit_principal" name="principal">
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

<script src="assets/js/clientes.js"></script>
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
</script>