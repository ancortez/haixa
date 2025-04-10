<?php
/**
 * Barra de navegación principal
 */
if (!isset($_SESSION)) {
    session_start();
}
?>

<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container-fluid">
        <a class="navbar-brand" href="index.php">
            <img src="assets/img/logo.png" alt="HAIXA Logo" height="30">
        </a>
        
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        
        <div class="collapse navbar-collapse" id="mainNav">
            <ul class="navbar-nav me-auto">

                <!-- Elementos del menú izquierdo -->
                <li class="nav-item">
                    <a class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : '' ?>" href="index.php">
                        <i class="fas fa-home"></i> Inicio
                    </a>
                </li>
                
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" 
                       href="#" 
                       id="clientesDropdown" 
                       role="button" 
                       data-bs-toggle="dropdown" 
                       aria-expanded="false"
                       onclick="console.log('Click en clienteDropdown');">
                        <i class="fas fa-users"></i> Clientes
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="clientesDropdown">
                        <li>
                            <a class="dropdown-item" href="#" 
                               onclick="event.preventDefault(); console.log('Click en Buscar Cliente'); $('#buscarClienteModal').modal('show');">
                                <i class="fas fa-search"></i> Buscar Cliente
                            </a>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <a class="dropdown-item" href="index.php?page=clientes/nuevo">
                                <i class="fas fa-user-plus"></i> Nuevo Cliente
                            </a>
                        </li>
                    </ul>
                </li>
                
                <!-- Otros elementos del menú -->
                <li class="nav-item">
                    <a class="nav-link" href="#"><i class="fas fa-file-contract"></i> Contratos</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#"><i class="fas fa-cash-register"></i> Cajas</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#"><i class="fas fa-cog"></i> Configuración</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#"><i class="fas fa-chart-bar"></i> Reportes</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#"><i class="fas fa-tasks"></i> Procesos</a>
                </li>
            </ul>
            
            <!--USUARIO Y CERRAR SESION-->
            <?php if (isset($_SESSION['user_id'])): ?>
            <div class="dropdown ms-auto">
                <button class="btn btn-link nav-link dropdown-toggle d-flex align-items-center text-white p-0"
                   id="userDropdown"
                   data-bs-toggle="dropdown"
                   aria-expanded="false">
                   <i class="fas fa-user-circle me-2"></i>
                   <span class="d-none d-sm-inline"><?= htmlspecialchars($_SESSION['username']) ?></span>
                </button>
                <ul class="dropdown-menu dropdown-menu-end shadow" aria-labelledby="userDropdown">
                    <li><h6 class="dropdown-header"><?= $_SESSION['username'] ?></h6></li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <a class="dropdown-item" href="logout.php">
                            <i class="fas fa-sign-out-alt me-2 text-danger"></i>
                            <span class="text-danger">Cerrar Sesión</span>
                        </a>
                    </li>
                </ul>
            </div>
            <?php endif; ?>

        </div>
    </div>
</nav>

<!-- Modal Buscar Cliente -->
<div class="modal fade" id="buscarClienteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Buscar Cliente</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <ul class="nav nav-tabs" id="buscarClienteTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="porNombre-tab" data-bs-toggle="tab" data-bs-target="#porNombre" type="button">Por Nombre</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="porRFC-tab" data-bs-toggle="tab" data-bs-target="#porRFC" type="button">Por RFC</button>
                    </li>
                </ul>
                <div class="tab-content p-3" id="buscarClienteTabsContent">
                    <div class="tab-pane fade show active" id="porNombre" role="tabpanel">
                        <form id="buscarPorNombreForm">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label for="nombre" class="form-label">Nombre(s)</label>
                                    <input type="text" class="form-control" id="nombre" name="nombre" placeholder="Puede ser parcial" autocomplete="off">
                                </div>
                                <div class="col-md-4">
                                    <label for="apellido_paterno" class="form-label">Apellido Paterno</label>
                                    <input type="text" class="form-control" id="apellido_paterno" name="apellido_paterno" placeholder="Puede ser parcial" autocomplete="off">
                                </div>
                                <div class="col-md-4">
                                    <label for="apellido_materno" class="form-label">Apellido Materno</label>
                                    <input type="text" class="form-control" id="apellido_materno" name="apellido_materno" placeholder="Puede ser parcial" autocomplete="off">
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="tab-pane fade" id="porRFC" role="tabpanel">
                        <form id="buscarPorRCFForm">
                            <div class="row">
                                <div class="col-md-8">
                                    <label for="rfc" class="form-label">RFC</label>
                                    <input type="text" class="form-control" id="rfc" name="rfc" placeholder="Puede ser parcial" autocomplete="off">
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                
                <div id="resultadosBusqueda" class="mt-3">
                    <h6>Resultados de búsqueda:</h6>
                    <div class="table-responsive">
                        <table class="table table-sm table-hover">
                            <thead>
                                <tr>
                                    <th>Acción</th>
                                    <th>Nombre</th>
                                    <th>RFC</th>
                                    <th>Género</th>
                                </tr>
                            </thead>
                            <tbody id="resultadosBody">
                                <!-- Los resultados se cargarán aquí via AJAX -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="btnBuscarCliente">
                    <i class="fas fa-search"></i> Buscar
                </button>
            </div>
        </div>
    </div>
</div>

<script>
// Script para manejar la búsqueda de clientes
document.addEventListener('DOMContentLoaded', function() {
    const btnBuscarCliente = document.getElementById('btnBuscarCliente');
    const resultadosBusqueda = document.getElementById('resultadosBusqueda');
    const resultadosBody = document.getElementById('resultadosBody');
    
    // Mostrar resultados al hacer clic en Buscar
    btnBuscarCliente.addEventListener('click', function() {
        buscarClientes();
    });
    
    // Permitir búsqueda al presionar Enter en los campos
    document.getElementById('nombre').addEventListener('keypress', function(e) {
        if (e.key === 'Enter') buscarClientes();
    });
    document.getElementById('apellido_paterno').addEventListener('keypress', function(e) {
        if (e.key === 'Enter') buscarClientes();
    });
    document.getElementById('apellido_materno').addEventListener('keypress', function(e) {
        if (e.key === 'Enter') buscarClientes();
    });
    document.getElementById('rfc').addEventListener('keypress', function(e) {
        if (e.key === 'Enter') buscarClientes();
    });
    
    function buscarClientes() {
        const activeTab = document.querySelector('#buscarClienteTabs .nav-link.active');
        let formData;
        
        if (activeTab.id === 'porNombre-tab') {
            formData = {
                tipo: 'nombre',
                nombre: document.getElementById('nombre').value,
                apellido_paterno: document.getElementById('apellido_paterno').value,
                apellido_materno: document.getElementById('apellido_materno').value
            };
        } else {
            formData = {
                tipo: 'rfc',
                rfc: document.getElementById('rfc').value
            };
        }
        
        // Limpiar resultados anteriores
        resultadosBody.innerHTML = '<tr><td colspan="4" class="text-center">Buscando clientes...</td></tr>';
        
        // Realizar la petición AJAX
        fetch('includes/buscar_clientes.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(formData)
        })
        .then(response => response.json())
        .then(data => {
            // Limpiar resultados anteriores
            resultadosBody.innerHTML = '';
            
            // Caso 1: Error del servidor
            if (data.error) {
                resultadosBody.innerHTML = `<tr><td colspan="4" class="text-danger">${data.error}</td></tr>`;
                return;
            }
            
            // Caso 2: No es un array
            if (!Array.isArray(data)) {
                resultadosBody.innerHTML = '<tr><td colspan="4">Formato de datos inválido</td></tr>';
                return;
            }
            
            // Caso 3: Array vacío
            if (data.length === 0) {
                resultadosBody.innerHTML = '<tr><td colspan="4">No se encontraron clientes</td></tr>';
                return;
            }
            
            // Caso 4: Éxito (array con datos)
            data.forEach(cliente => {
                resultadosBody.innerHTML += `
                    <tr>
                        <td>
                            <a href="index.php?page=clientes/detalle&id=${cliente.id_cliente}" 
                               class="btn btn-link p-0" 
                               title="Seleccionar cliente">
                                <i class="fas fa-user-circle text-primary" style="font-size: 1.5rem;"></i>
                            </a>
                        </td>
                        <td>${cliente.nombres} ${cliente.apellido_paterno} ${cliente.apellido_materno}</td>
                        <td>${cliente.rfc || 'N/A'}</td>
                        <td>${cliente.nombre_genero || 'N/A'}</td>
                    </tr>
                `;
            });
        })
        .catch(error => {
            resultadosBody.innerHTML = `<tr><td colspan="4">Error de conexión: ${error.message}</td></tr>`;
        });
            
    }
    
    function cargarDetalleCliente(clienteId) {
        // Implementar la carga del detalle del cliente en el contenido principal
        window.location.href = `index.php?page=clientes/detalle&id=${clienteId}`;
    }
});
</script>

<script>
    fetch('/haixa/includes/buscar_clientes.php', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json',
    },
    body: JSON.stringify(formData)
})
.then(response => response.text())  // Cambiado a text() para ver la respuesta cruda
.then(text => {
    console.log('Respuesta cruda:', text);
    try {
        const data = JSON.parse(text);
        // Tu código para manejar los datos...
    } catch (e) {
        console.error('Error al parsear JSON:', e);
    }
})
.catch(error => {
    console.error('Error en la petición:', error);
});
</script>
<script>
    .then(data => {
    if (data.error) {
        console.error(data.error); // Muestra errores del servidor
        resultadosBody.innerHTML = `<tr><td colspan="4">${data.error}</td></tr>`;
        return;
    }
    
    if (!Array.isArray(data)) { // Si no es un array
        console.error("Respuesta inesperada:", data);
        resultadosBody.innerHTML = '<tr><td colspan="4">Formato de respuesta incorrecto</td></tr>';
        return;
    }

    // Si llega aquí, data es un array válido
    data.forEach(cliente => {
        // Tu código para mostrar resultados
    });
})
</script>