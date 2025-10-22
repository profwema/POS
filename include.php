<script type="text/javascript">
	var dataTableVaue = null;
</script>

<!-- ═══════════════════════════════════════════════════════════════ -->
<!-- MODERN ADMIN RTL - JAVASCRIPT LIBRARIES (2025) -->
<!-- ═══════════════════════════════════════════════════════════════ -->

<!-- jQuery already loaded in header.php -->

<!-- Bootstrap 5.3.2 Bundle (includes Popper) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"
	integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>

<!-- DataTables 1.13.7 -->
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap5.min.js"></script>

<!-- Select2 4.1.0 -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<!-- Chart.js 4.4.0 -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

<!-- SweetAlert2 11.10.0 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.10.0/dist/sweetalert2.all.min.js"></script>

<!-- Alpine.js 3.13.3 (Lightweight JS Framework) -->
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.13.3/dist/cdn.min.js"></script>

<!-- Axios 1.6.2 (Better AJAX) -->
<script src="https://cdn.jsdelivr.net/npm/axios@1.6.2/dist/axios.min.js"></script>

<!-- ═══════════════════════════════════════════════════════════════ -->
<!-- LEGACY SCRIPTS (For Compatibility) -->
<!-- ═══════════════════════════════════════════════════════════════ -->

<script src="js/ajax-upload.js"></script>
<script src="js/base64.js"></script>
<script src="js/html2canvas.js"></script>
<script src="js/jquery.base64.js"></script>
<script src="js/jspdf.js"></script>
<script src="js/sprintf.js"></script>
<script src="js/tableExport.js"></script>
<script src="js/custom.js"></script>

<!-- ═══════════════════════════════════════════════════════════════ -->
<!-- MODERN ADMIN RTL - CUSTOM SCRIPTS -->
<!-- ═══════════════════════════════════════════════════════════════ -->

<!-- Modern Admin Interactive Features -->
<script src="js/modern-admin.js"></script>

<!-- Initialize Modern Features -->
<script>
	// Initialize all modern features when DOM is ready
	document.addEventListener('DOMContentLoaded', function() {

		// Initialize DataTables with modern settings
		if (typeof $.fn.DataTable !== 'undefined') {
			$('.datatable').DataTable({
				responsive: true,
				language: {
					url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/ar.json'
				},
				pageLength: 25,
				dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>rtip'
			});
		}

		// Initialize Select2
		if (typeof $.fn.select2 !== 'undefined') {
			$('.select2').select2({
				theme: 'bootstrap-5',
				dir: 'rtl',
				language: 'ar'
			});
		}

		// Initialize tooltips
		const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
		const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl));

		// Logout functionality
		const logoutBtn = document.getElementById('logout');
		if (logoutBtn) {
			logoutBtn.addEventListener('click', function(e) {
				e.preventDefault();
				e.stopPropagation();

				// Call logout function
				if (typeof logout === 'function') {
					logout();
				} else {
					// Fallback: direct AJAX call
					$.ajax({
						url: "controller.php?l=1&f=logout",
						success: function(result) {
							$("#overlay").hide(300);
							location.replace("index.php");
						}
					});
				}
			});
		}

		console.log('✅ Modern Admin RTL initialized successfully!');
	});
</script>

<!-- Layout CSS is now in css/layout-fix.css -->

<!-- end: JavaScript -->

<?php mysqli_close(); ?>