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
                                    <input type="text" class="form-control" id="nombre" name="nombre">
                                </div>
                                <div class="col-md-4">
                                    <label for="apellido_paterno" class="form-label">Apellido Paterno</label>
                                    <input type="text" class="form-control" id="apellido_paterno" name="apellido_paterno">
                                </div>
                                <div class="col-md-4">
                                    <label for="apellido_materno" class="form-label">Apellido Materno</label>
                                    <input type="text" class="form-control" id="apellido_materno" name="apellido_materno">
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="tab-pane fade" id="porRFC" role="tabpanel">
                        <form id="buscarPorRCFForm">
                            <div class="row">
                                <div class="col-md-8">
                                    <label for="rfc" class="form-label">RFC</label>
                                    <input type="text" class="form-control" id="rfc" name="rfc" placeholder="Puede ser parcial">
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                
                <div id="resultadosBusqueda" class="mt-3 d-none">
                    <h6>Resultados de búsqueda:</h6>
                    <div class="table-responsive">
                        <table class="table table-sm table-hover">
                            <thead>
                                <tr>
                                    <th>Seleccionar</th>
                                    <th>Nombre</th>
                                    <th>RFC</th>
                                    <th>Género</th>
                                </tr>
                            </thead>
                            <tbody id="resultadosBody">
                                <!-- Aquí se cargarán los resultados via AJAX -->
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