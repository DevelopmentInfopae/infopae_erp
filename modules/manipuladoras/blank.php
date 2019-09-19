<?php
$titulo = 'Subir Archivo';
include 'header.php';
// $periodoActual = $_SESSION['periodoActual'];
// require_once 'db/conexion.php';
// $Link = new mysqli($Hostname, $Username, $Password, $Database);
// if ($Link->connect_errno) {
//     echo "Fallo al contenctar a MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
// }
// $Link->set_charset("utf8");
?>
<div class="row wrapper border-bottom white-bg page-heading">
	<div class="col-lg-10">
		<h2>Titular en derecho</h2>
		<ol class="breadcrumb">
			<li> <a href="<?php echo $baseUrl; ?>">Home</a> </li>
			<li class="active"> <strong>Titular</strong> </li> 
		</ol>
	</div>
	<div class="col-lg-2">

	</div>
</div>

<div class="wrapper wrapper-content">
	<div class="row animated fadeInRight">
		<div class="col-md-12">
			<div class="ibox float-e-margins">
				<div class="ibox-title">
					<h3>Titular en derecho</h3>
				</div>
				<div class="ibox-content">

				</div>
			</div>
		</div>
	</div>
</div>

<?php include '../../footer.php'; ?>

<!-- Mainly scripts -->
<script src="theme/js/jquery-3.1.1.min.js"></script>
<script src="theme/js/bootstrap.min.js"></script>
<script src="theme/js/plugins/metisMenu/jquery.metisMenu.js"></script>
<script src="theme/js/plugins/slimscroll/jquery.slimscroll.min.js"></script>

<script src="theme/js/plugins/dataTables/datatables.min.js"></script>

<!-- Custom and plugin javascript -->
<script src="theme/js/inspinia.js"></script>
<script src="theme/js/plugins/pace/pace.min.js"></script>

<!-- <script src="<?php echo $baseUrl; ?>/modules/instituciones/js/instituciones.js"></script> -->
<!-- Page-Level Scripts -->
</body>
</html>