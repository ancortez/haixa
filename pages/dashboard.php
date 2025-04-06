<?php
/**
 * Página de inicio/dashboard del sistema
 */
$page_title = 'HAIXA - Inicio';
?>

<div class="container-fluid p-4">
    <h1 class="h3 mb-4"><i class="fas fa-tachometer-alt me-2"></i>Panel de Control</h1>
    
    <div class="row">
        <!-- Tarjeta de Bienvenida -->
        <div class="col-md-12 mb-4">
            <div class="card border-primary">
                <div class="card-header bg-primary text-white">
                    <i class="fas fa-home me-2"></i>Bienvenido al Sistema HAIXA
                </div>
                <div class="card-body">
                    <h5 class="card-title">Hola, <?php echo htmlspecialchars($_SESSION['username'] ?? 'Usuario'); ?></h5>
                    <p class="card-text">Sistema de gestión de cobro de agua potable para OROMAPAS Ruiz, Nayarit.</p>
                    <p class="card-text"><small class="text-muted">Último acceso: <?php echo $_SESSION['last_activity'] ?? 'Nunca'; ?></small></p>
                </div>
            </div>
        </div>
        
        <!-- Estadísticas Rápidas -->
        <div class="col-md-4 mb-4">
            <div class="card h-100">
                <div class="card-header bg-success text-white">
                    <i class="fas fa-users me-2"></i>Clientes Registrados
                </div>
                <div class="card-body text-center">
                    <h2 class="display-4">0</h2>
                    <p class="card-text">Total de clientes en el sistema</p>
                </div>
                <div class="card-footer">
                    <a href="?page=clientes" class="btn btn-sm btn-success">Ver Clientes</a>
                </div>
            </div>
        </div>
        
        <div class="col-md-4 mb-4">
            <div class="card h-100">
                <div class="card-header bg-info text-white">
                    <i class="fas fa-file-invoice-dollar me-2"></i>Pagos Recientes
                </div>
                <div class="card-body text-center">
                    <h2 class="display-4">0</h2>
                    <p class="card-text">Pagos registrados hoy</p>
                </div>
                <div class="card-footer">
                    <a href="?page=cajas" class="btn btn-sm btn-info">Ver Pagos</a>
                </div>
            </div>
        </div>
        
        <div class="col-md-4 mb-4">
            <div class="card h-100">
                <div class="card-header bg-warning text-dark">
                    <i class="fas fa-exclamation-triangle me-2"></i>Alertas
                </div>
                <div class="card-body text-center">
                    <h2 class="display-4">0</h2>
                    <p class="card-text">Alertas pendientes</p>
                </div>
                <div class="card-footer">
                    <a href="?page=reportes" class="btn btn-sm btn-warning">Ver Alertas</a>
                </div>
            </div>
        </div>
    </div>
</div>