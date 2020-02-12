<?php
/**
 * Dashboard.
 * Pantalla en donde se muestran los registros 
 * que se han ido leyendo, permite la lectura, 
 * y el ingreso manual del numero de documento.
 * @author Ricardo Farfán <ricardo@xlogam.com>
 */

// G:\xampp\htdocs\infopae2019\modules\entregas_biometricas\dashboard.php:67:
// array (size=9)
//   'mes' => string '10' (length=2)
//   'semana' => string '35b' (length=3)
//   'municipio' => string '68307' (length=5)
//   'institucion' => string '268307000370' (length=12)
//   'sede' => string '26830700037001' (length=14)
//   'nivel' => string '1' (length=1)
//   'grado' => string '' (length=0)
//   'grupo' => string '' (length=0)
//   'dispositivo' => string '1' (length=1)

set_time_limit (0);
ini_set('memory_limit','6000M');

$titulo = "Entregas Biometricas";
include '../../header.php';
$cacheBusting = date("YmdHis");
?>

<link rel="stylesheet" href="css/entregas_biometricas.css?v=<?= $cacheBusting; ?>">

<?php
date_default_timezone_set('America/Bogota');

$fecha = date("Y-m-d H:i:s");
$cacheBusting = date("YmdHis");
$periodoActual = $_SESSION["periodoActual"];
$dia = intval(date("d"));
$mes = date("m");
$anno = date("Y");
$anno2d = date("y");

// Lectura de los parametros POST o GET
if(isset($_GET["sede"]) && $_GET["sede"] != ""){
	$institucionNombre = mysqli_real_escape_string($Link, $_GET['institucionNombre']);
	$sedeNombre = mysqli_real_escape_string($Link, $_GET['sedeNombre']);
	$dispositivoNombre = mysqli_real_escape_string($Link, $_GET['dispositivoNombre']);
	$dispositivo = mysqli_real_escape_string($Link, $_GET['dispositivo']);
	$nivelNombre = mysqli_real_escape_string($Link, $_GET['nivelNombre']);
	$grado = mysqli_real_escape_string($Link, $_GET['grado']);
	$gradoNombre = mysqli_real_escape_string($Link, $_GET['gradoNombre']);
	$grupo = mysqli_real_escape_string($Link, $_GET['grupo']);
}
?>

<div class="row wrapper wrapper-content border-bottom white-bg page-heading">
	<div class="col-xs-8">
			<h2>Completar entregas biometricas</h2>
			<?php //var_dump($_GET); ?>
			<ol class="breadcrumb">
				<li>
					<a href="<?php echo $baseUrl; ?>">Inicio</a>
				</li>
				<li class="active">
					<strong><?php echo $titulo; ?></strong>
				</li>
			</ol>
	</div>
</div>

<div class="wrapper wrapper-content  animated fadeInRight registroConsumo" >
	<div class="row">
		<div class="col-sm-12">
			<div class="ibox">
				<div class="ibox-title">
					<h5>Estudiantes</h5>
					<div class="ibox-tools">
						<div class="collapse-link">
							<i class="fa fa-chevron-down"></i>
						</div>
					</div>
				</div>
				<div class="ibox-content">

					<h2 class="titulo-institucion">Institución: <?= $institucionNombre ?></h2>

					<div class="color-oscuro border-oscuro">
						<div class="row">
							<div class="col-sm-4">
								<h4>Sede: <br> <?= $sedeNombre ?></h4>
							</div>
							<div class="col-sm-2 ">
								<h4>Dispositivo: <br> <?= $dispositivoNombre ?></h4>
							</div>
							<div class="col-sm-2 ">
								<h4>Nivel: <br> <?= $nivelNombre ?></h4>
							</div>
							<?php if($grado != ''){ ?>
								<div class="col-sm-2 ">
									<h4>Grado: <br> <?= $gradoNombre ?></h4>
								</div>
							<?php } ?>
							<?php if($grupo != ''){ ?>
								<div class="col-sm-2 ">
									<h4>Grupo: <br> <?= $grupo ?></h4>
								</div>
							<?php } ?>
						</div>
					</div>
					
					<div class="color-oscuro">
						<div class="row">
							<div class="col-sm-4 text-center">
								<span class="fecha-actual"></span>
							</div>
							<div class="col-sm-4 text-center">
								<span class="hora-actual"></span>
							</div>
							<div class="col-sm-4">
								<div class="form-captura">
									<form class="form-inline form-registro-biometria">
										<input type="hidden" id="dispositivo" value="<?= $dispositivo ?>">
										<input type="text" class="form-control mb-8 mr-sm-8" id="lector" placeholder="Documento" style="color: black;">
										<button type="button" class="btn btn-primary mb-2" id="btn-lector">Registrar</button>
									</form>
								</div>
							</div>
						</div>
					</div>









					<div class="table-responsive table-asistencia">
						<table class="table table-striped table-hover selectableRows dataTablesSedes" >
							<thead>
								<tr>
									<th>Registro</th>
									<th>Nombre</th>
									<th>Apellido</th>
									<th>Grado</th>
									<th>Grupo</th>
								</tr>
							</thead>
							<tbody class="entregas-qr">
								<tr>
									<td>23/01/2020 8:08:00 A.M.</td>
									<td>Pedro Jóse</td>
									<td>Perez Gomez</td>
									<td>Primero</td>
									<td>101</td>
								</tr>
							</tbody>
							<tfoot>
								<tr>
									<th>Registro</th>
									<th>Nombre</th>
									<th>Apellido</th>
									<th>Grado</th>
									<th>Grupo</th>
								</tr>
							</tfoot>
						</table>
					</div>
						
					<div class="hr-line-dashed"></div>
				</div>
			</div>
		</div>
	</div><!-- /.row -->
</div>

<?php include '../../footer.php'; ?>

<!-- Mainly scripts -->
<script src="<?php echo $baseUrl; ?>/theme/js/jquery-3.1.1.min.js"></script>
<script src="<?php echo $baseUrl; ?>/theme/js/bootstrap.min.js"></script>
<script src="<?php echo $baseUrl; ?>/theme/js/plugins/metisMenu/jquery.metisMenu.js"></script>
<script src="<?php echo $baseUrl; ?>/theme/js/plugins/slimscroll/jquery.slimscroll.min.js"></script>

<!-- Custom and plugin javascript -->
<script src="<?php echo $baseUrl; ?>/theme/js/inspinia.js"></script>
<script src="<?php echo $baseUrl; ?>/theme/js/plugins/pace/pace.min.js"></script>
<script src="<?php echo $baseUrl; ?>/theme/js/plugins/dataTables/datatables.min.js"></script>
<script src="<?php echo $baseUrl; ?>/theme/js/plugins/jasny/jasny-bootstrap.min.js"></script>
<script src="<?php echo $baseUrl; ?>/theme/js/plugins/validate/jquery.validate.min.js"></script>
<script src="<?php echo $baseUrl; ?>/theme/js/plugins/toggle/toggle.min.js"></script>
<script src="<?php echo $baseUrl; ?>/theme/js/plugins/toastr/toastr.min.js"></script>
<script src="<?php echo $baseUrl; ?>/theme/js/plugins/iCheck/icheck.min.js"></script>

<!-- Page-Level Scripts -->
<script src="<?php echo $baseUrl; ?>/modules/entregas_biometricas/js/filtro.js?v=<?= $cacheBusting; ?>"></script>
<script src="<?php echo $baseUrl; ?>/modules/entregas_biometricas/js/dashboard_barcode_qr.js?v=<?= $cacheBusting; ?>"></script>

</body>
</html>