<?php
	include '../../header.php';
	set_time_limit (0);
	ini_set('memory_limit','6000M');

	$periodoActual = $_SESSION["periodoActual"];
	$titulo = "Registro Biometrico";
	$institucionNombre = "";

	date_default_timezone_set('America/Bogota');
	$fecha = date("Y-m-d H:i:s");
	$cacheBusting = date("YmdHis");

	$dia = intval(date("d"));
	$mes = date("m");
	$anno = date("Y");
?>

<link rel="stylesheet" href="css/custom.css?v=<?= $cacheBusting; ?>">

<div class="row wrapper wrapper-content border-bottom white-bg page-heading">
	<div class="col-xs-8">
		<h2>Registro Biometrico</h2>
		<ol class="breadcrumb">
			<li>
				<a href="<?php echo $baseUrl; ?>">Inicio</a>
			</li>
			<li class="active">
				<strong><?php echo $titulo; ?></strong>
			</li>
		</ol>
	</div>
	<div class="col-xs-4"> </div>
</div>
<!-- /.row wrapper de la cabecera de la seccion -->

<?php 
	$diaNoObligatorio = 1;
	include "filtro.php"  
?>

<div class="wrapper wrapper-content  animated fadeInRight registroConsumo" style="display: none">
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
					<input type="hidden" id="semanaActual" value="<?php echo $semanaActual; ?>">
					<input type="hidden" id="sede" value="">
                    <input type="hidden" id="validacion" name="validacion" value="Lector de Huella">


					<div class="table-responsive table-asistencia">
						<table class="table table-striped table-hover selectableRows dataTablesSedes" >
							<thead>
								<tr>
		
									<th>Documento</th>
									<th>Nombre</th>
									<th>Grado</th>
									<th>Grupo</th>				
									<th>Fecha</th>
								</tr>
							</thead>

							<tfoot>
								<tr>
				
									<th>Documento</th>
									<th>Nombre</th>
									<th>Grado</th>
									<th>Grupo</th>				
									<th>Fecha</th>
								</tr>
							</tfoot>
						</table>
					</div>				

					<div class="hr-line-dashed"></div>
					<div class="form-group row">
						<div class="col-sm-12">

							<?php if( $_SESSION['perfil'] == 0 || $_SESSION['perfil'] == 1 || $_SESSION['perfil'] == 5 ){ ?>
							<button class="btn btn-primary btnGuardar" type="button">Guardar</button>
							<?php } ?>
							<!-- <button class="btn btn-primary btnSellar" type="button">Sellar Asistencia</button> -->
						</div>
					</div>
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
<script src="<?php echo $baseUrl; ?>/modules/asistencias/js/filtro.js?v=<?= $cacheBusting; ?>"></script>
<script src="<?php echo $baseUrl; ?>/modules/asistencias/js/registro_biometrico.js?v=<?= $cacheBusting; ?>"></script>
</body>
</html>