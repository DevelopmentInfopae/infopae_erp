<?php
	include '../../header.php';
	set_time_limit (0);
	ini_set('memory_limit','6000M');

	var_dump($_POST);

	$idNovedad = (isset($_POST['idNovedad']) && $_POST['idNovedad'] != '') ? mysqli_real_escape_string($Link, $_POST["idNovedad"]) : "";
	$periodoActual = mysqli_real_escape_string($Link, $_SESSION['periodoActual']);

	$consulta = " SELECT DISTINCT dp.ID, u.Ciudad, s.nom_inst, s.nom_sede, ps.MES, dp.* FROM novedades_priorizacion dp LEFT JOIN sedes$periodoActual s ON dp.cod_sede = s.cod_sede LEFT JOIN  ubicacion u ON s.cod_mun_sede = u.CodigoDANE LEFT JOIN planilla_semanas ps ON dp.Semana = ps.SEMANA WHERE dp.id = $idNovedad ";
	$resultado = $Link->query($consulta) or die ('Unable to execute query - Leyendo datos de la novedad '. mysqli_error($Link));
	if($resultado->num_rows >= 1){
		$row = $resultado->fetch_assoc();
		$datosNovedad = $row;
	}

	$mesNm = mesEnLetras($datosNovedad['MES']);
	$semana = $datosNovedad['Semana'];
	$codSede = $datosNovedad['cod_sede'];

	$consulta = " SELECT * FROM priorizacion$semana WHERE cod_sede = $codSede ";
	$resultado = $Link->query($consulta) or die ('Unable to execute query - Leyendo priprización original '. mysqli_error($Link));
	if($resultado->num_rows >= 1){
		$row = $resultado->fetch_assoc();
		$datosPriorizacion = $row;
	}
?>

<div class="row wrapper wrapper-content border-bottom white-bg page-heading">
	<div class="col-lg-8">
		<h2>Ver Novedad en Menu XXXXXXXXXXXXXXXXX</h2>
		<div class="debug"></div>
		<ol class="breadcrumb">
			<li>
				<a href="<?php echo $baseUrl; ?>">Inicio</a>
			</li>
			<li> <a href="<?php echo $baseUrl; ?>/modules/novedades_priorizacion">Novedades de Priorización</a> </li>
			<li class="active">
				<strong>Novedad de Priorización</strong>
			</li>
		</ol>
	</div>
	<div class="col-lg-4">
		<div class="title-action">
			<?php if($_SESSION['perfil'] == 0 || $_SESSION['perfil'] == 1){ ?>
				<a href="#" class="btn btn-primary" onclick="crearNovedadPriorizacion();"><i class="fa fa-plus"></i> Nuevo </a>
			<?php } ?>
		</div>
	</div>
</div>

<div class="wrapper wrapper-content animated fadeInRight">
	<div class="row">
		<div class="col-lg-12">
			<?php //include 'mosatrar_borrar.php'; ?>
		</div>
	</div>
</div>

<div class="modal fade" id="myModal" role="dialog">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">Modal Header</h4>
			</div>
			<div class="modal-body">
				<p>This is a small modal.</p>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
			</div>
		</div>
	</div>
</div>

<?php include '../../footer.php'; ?>

<!-- Mainly scripts -->
<script src="<?php echo $baseUrl; ?>/theme/js/jquery-3.1.1.min.js"></script>
<script src="<?php echo $baseUrl; ?>/theme/js/bootstrap.min.js"></script>
<script src="<?php echo $baseUrl; ?>/theme/js/plugins/metisMenu/jquery.metisMenu.js"></script>
<script src="<?php echo $baseUrl; ?>/theme/js/plugins/slimscroll/jquery.slimscroll.min.js"></script>
<script src="<?php echo $baseUrl; ?>/theme/js/plugins/dataTables/datatables.min.js"></script>

<!-- Custom and plugin javascript -->
<script src="<?php echo $baseUrl; ?>/theme/js/inspinia.js"></script>
<script src="<?php echo $baseUrl; ?>/theme/js/plugins/pace/pace.min.js"></script>

<!-- Jasny -->
<script src="<?php echo $baseUrl; ?>/theme/js/plugins/jasny/jasny-bootstrap.min.js"></script>

<!-- DROPZONE -->
<script src="<?php echo $baseUrl; ?>/theme/js/plugins/dropzone/dropzone.js"></script>

<!-- CodeMirror -->
<script src="<?php echo $baseUrl; ?>/theme/js/plugins/codemirror/codemirror.js"></script>
<script src="<?php echo $baseUrl; ?>/theme/js/plugins/codemirror/mode/xml/xml.js"></script>

<script src="<?php echo $baseUrl; ?>/modules/instituciones/js/sede_archivos.js"></script>

<script src="<?php echo $baseUrl; ?>/modules/intercambios/js/novedades_menu_ver.js"></script>

</body>
</html>
