<!-- Page content ends here -->
</main>
</div>
</div>

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

<!-- Bootstrap 5 JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- DataTables JS -->
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>

<!-- Custom JS -->
<script src="<?php echo BASE_URL; ?>assets/js/script.js?v=2.3.0"></script>
<script src="<?php echo BASE_URL; ?>assets/js/form-loading.js?v=2.3.0"></script>
<script src="<?php echo BASE_URL; ?>assets/js/sidebar-toggle.js?v=2.5.0"></script>
<script src="<?php echo BASE_URL; ?>assets/js/sidebar-flyout.js?v=1.0.0"></script>


<!-- Onboarding Modal -->
<?php if (isset($_SESSION['tipo']) && $_SESSION['tipo'] === 'cliente') {
    include_once __DIR__ . '/onboarding_modal.php';
} ?>

</body>

</html>
<?php ob_end_flush(); ?>