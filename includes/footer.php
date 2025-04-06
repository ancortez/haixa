<?php
/**
 * Pie de página común para todas las páginas
 */
?>
        <footer class="footer mt-auto py-3 bg-light">
            <div class="container">
                <div class="row">
                    <div class="col-md-6">
                        <span class="text-muted">HAIXA &copy; <?php echo date('Y'); ?> OROMAPAS Ruiz Nayarit</span>
                    </div>
                    <div class="col-md-6 text-end">
                        <span class="text-muted">Versión 1.0.0</span>
                    </div>
                </div>
            </div>
        </footer>

        <!-- Bootstrap JS Bundle con Popper -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
        
        <!-- jQuery -->
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        
        <!-- Scripts locales -->
        <script src="<?php echo base_url(); ?>assets/js/main.js"></script>
    </div>
</body>
</html>