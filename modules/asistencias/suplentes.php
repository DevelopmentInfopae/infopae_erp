<?php
include '../../header.php';
include 'functions/fn_fecha_asistencia.php';
set_time_limit (0);
ini_set('memory_limit','6000M');

$periodoActual = $_SESSION["periodoActual"];
$titulo = "Suplentes";
$institucionNombre = "";
$dia = $diaAsistencia;
$mes = $mesAsistencia;
$anno = $annoasistencia;

//Busqueda de la semana actual
$semanaActual = "";
$consulta = "select semana from planilla_semanas where ano = \"$anno\" and mes = \"$mes\" and dia = \"$dia\" ";	
$resultado = $Link->query($consulta) or die ('No se pudo cargar la semana actual. '. mysqli_error($Link));
if($resultado->num_rows >= 1){
	$row = $resultado->fetch_assoc();
	$semanaActual = $row["semana"];
}

$consulta = " select distinct semana from planilla_semanas ";
$resultado = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));
if($resultado->num_rows >= 1){
	while($row = $resultado->fetch_assoc()){
		$aux = $row['semana'];
		$consulta2 = " show tables LIKE 'focalizacion$aux' ";
		$resultado2 = $Link->query($consulta2) or die ('Unable to execute query. '. mysqli_error($Link));
		if($resultado2->num_rows >= 1){
			$semanas[] = $aux;
		}
	}
}			
?>

<link rel="stylesheet" href="css/custom.css?v=<?= $cacheBusting; ?>">
<div class="flagFaltantes">Faltan <span class="asistenciaFaltantes">0</span> de <span class="asistenciaTotal">0</span> </div>
<div class="row wrapper wrapper-content border-bottom white-bg page-heading"> <!-- page-heading -->
	<div class="col-xs-8"> <!-- col -->
		<h2>Seleccionar Suplentes</h2>
		<ol class="breadcrumb">
			<li>
				<a href="<?php echo $baseUrl; ?>">Inicio</a>
			</li>
			<li class="active">
				<strong><?php echo $titulo; ?></strong>
			</li>
		</ol>
	</div>
	<div class="col-xs-4">
		<div class="title-action">
			<button class="btn btn-primary btnGuardar" type="button"><span class="fa fa-check"></span> Guardar</button>
		</div>
	</div>
</div>
<?php
	include "filtro.php"  
?>

<div class="wrapper wrapper-content  animated fadeInRight"> <!-- fadeInRight -->
	<div class="row">
		<div class="col-sm-12">
			<div class="ibox">
				<div class="ibox-title">
					<h5>Suplentes</h5>
					<div class="ibox-tools">
						<div class="collapse-link">
							<i class="fa fa-chevron-down"></i>
						</div>
					</div>
				</div> <!-- ibox-title -->
				<div class="ibox-content">
					<input type="hidden" id="semanaActual" value="<?= $semanaActual; ?>">
					<input type="hidden" id="banderaRegistros" value="">
					<div class="table-responsive table-asistencia">
						<table class="table table-striped table-hover selectableRows dataTablesSedes" >
							<thead>
								<tr>
									<th></th> 
									<th>Documento</th>
									<th>Nombre</th>
									<th>Grado</th>
									<th>Grupo</th>
								</tr>
							</thead>

							<tfoot>
								<tr>
									<th></th> 
									<th>Documento</th>
									<th>Nombre</th>
									<th>Grado</th>
									<th>Grupo</th>
								</tr>
							</tfoot>
						</table>
					</div>
					<div class="hr-line-dashed"></div>
					<div class="form-group row">
						<div class="col-sm-12">
							<button class="btn btn-primary btnGuardar" type="button"><span class="fa fa-check"></span>  Guardar</button>
						</div>
					</div>
				</div> <!-- ibox-content -->
			</div> <!-- ibox -->
		</div> <!-- col-sm-12 -->
	</div> <!-- row -->
</div>


<?php include '../../footer.php'; ?>

<!-- Mainly scripts -->
<script src="<?= $baseUrl; ?>/theme/js/jquery-3.1.1.min.js"></script>
<script src="<?= $baseUrl; ?>/theme/js/bootstrap.min.js"></script>
<script src="<?= $baseUrl; ?>/theme/js/plugins/metisMenu/jquery.metisMenu.js"></script>
<script src="<?= $baseUrl; ?>/theme/js/plugins/slimscroll/jquery.slimscroll.min.js"></script>

<!-- Custom and plugin javascript -->
<script src="<?= $baseUrl; ?>/theme/js/inspinia.js"></script>
<script src="<?= $baseUrl; ?>/theme/js/plugins/pace/pace.min.js"></script>
<script src="<?= $baseUrl; ?>/theme/js/plugins/dataTables/datatables.min.js"></script>
<script src="<?= $baseUrl; ?>/theme/js/plugins/jasny/jasny-bootstrap.min.js"></script>
<script src="<?= $baseUrl; ?>/theme/js/plugins/validate/jquery.validate.min.js"></script>
<script src="<?= $baseUrl; ?>/theme/js/plugins/toggle/toggle.min.js"></script>
<script src="<?= $baseUrl; ?>/theme/js/plugins/toastr/toastr.min.js"></script>
<script src="<?= $baseUrl; ?>/theme/js/plugins/iCheck/icheck.min.js"></script>
<script src="<?= $baseUrl; ?>/theme/js/plugins/select2/select2.full.min.js"></script>
<script src="<?= $baseUrl; ?>/modules/asistencias/js/filtro.js?v=<?= $cacheBusting; ?>"></script>
<script src="<?= $baseUrl; ?>/modules/asistencias/js/suplentes.js?v=<?= $cacheBusting; ?>"></script>

</body>
</html>
