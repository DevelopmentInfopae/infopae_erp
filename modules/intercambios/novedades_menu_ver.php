<?php
	include '../../header.php';
	set_time_limit (0);
	ini_set('memory_limit','6000M');

	//var_dump($_POST);

	$idNovedad = '';
	if(isset($_POST['idNovedad']) && $_POST['idNovedad'] != ''){
		$idNovedad = mysqli_real_escape_string($Link, $_POST['idNovedad']);
	}

	// Consulta de los datos de la novedad.

	$consulta = "SELECT nm.id, nm.tipo_intercambio, IF(nm.tipo_intercambio = 1, 'Intercambio de alimento', IF(nm.tipo_intercambio = 2, 'Intercambio de preparación', 'Intercambio de día de menú')) AS tipo_intercambio_nm, nm.estado, IF(nm.estado = 1, 'Activo', 'Reversado') AS estado_nm, nm.mes, nm.semana, nm.dia, nm.tipo_complem AS tipo_complemento, ge.DESCRIPCION AS grupo_etario, CONCAT(p.Codigo, ' - ',p.Descripcion) AS menu, ft.Nombre AS producto, ft.Codigo, DATE_FORMAT(nm.fecha_vencimiento, '%d/%m/%Y') AS fecha_vencimiento, nm.url_archivo AS archivo, nm.observaciones AS observaciones FROM novedades_menu nm left join grupo_etario ge ON ge.ID = nm.cod_grupo_etario LEFT JOIN fichatecnica ft ON ft.Codigo = nm.cod_producto LEFT JOIN planilla_semanas ps ON ps.MES = nm.mes AND ps.SEMANA = nm.semana AND ps.DIA = nm.dia LEFT JOIN productos19 p ON ps.MENU = p.Orden_Ciclo AND p.Cod_Tipo_complemento = nm.tipo_complem AND p.Cod_Grupo_Etario = nm.cod_grupo_etario WHERE nm.id = $idNovedad ";

	$resultado = $Link->query($consulta) or die ('Unable to execute query - Leyendo datos de la novedad '. mysqli_error($Link));
	if($resultado->num_rows >= 1){
		$row = $resultado->fetch_assoc();
		$datosNovedad = $row;
	}

	$tipoIntercambio = $datosNovedad['tipo_intercambio'];
	$tipoIntercambioNm = $datosNovedad['tipo_intercambio_nm'];
	$estado = $datosNovedad['estado'];
	$estadoNm = $datosNovedad['estado_nm'];
	$mes = $datosNovedad['mes'];
	$mesNm = mesEnLetras($mes);
	$semana = $datosNovedad['semana'];
	$dia = $datosNovedad['dia'];
	$tipoComplemento = $datosNovedad['tipo_complemento'];
	$grupoEtario = $datosNovedad['grupo_etario'];
	$menu = $datosNovedad['menu'];
	$producto = $datosNovedad['producto'];
	$codigo = $datosNovedad['Codigo'];
	$fechaVencimiento = $datosNovedad['fecha_vencimiento'];
	$archivo = $datosNovedad['archivo'];
	$observaciones = $datosNovedad['observaciones'];
?>

<div class="row wrapper wrapper-content border-bottom white-bg page-heading">
	<div class="col-lg-8">
		<h2>Ver Novedad en Menu <?= $tipoIntercambioNm ?></h2>
		<div class="debug"></div>
		<ol class="breadcrumb">
			<li>
				<a href="<?php echo $baseUrl; ?>">Inicio</a>
			</li>
			<li> <a href="<?php echo $baseUrl; ?>/modules/intercambios">Novedades de Menú</a> </li>
			<li class="active">
				<strong>Novedad de Menú</strong>
			</li>
		</ol>
	</div>
	<div class="col-lg-4">
		<div class="title-action">
			<?php if($estado == 1){ ?>
				<?php if($_SESSION['perfil'] == 0 || $_SESSION['perfil'] == 1){ ?>
					<a href="#" class="btn btn-primary" onclick="crearNovedadPriorizacion();"><i class="fa fa-undo"></i> Reversar Intercambio </a>
				<?php } ?>
			<?php } ?>
		</div>
	</div>
</div>

<div class="wrapper wrapper-content animated fadeInRight">
	<div class="row">
		<div class="col-lg-12">
			<?php 
				if($tipoIntercambio == 1){
					include 'mostrar_intercambio_alimento.php';
				} else if($tipoIntercambio == 2){
					include 'mostrar_intercambio_preparacion.php';
				} else if($tipoIntercambio == 3){
					include 'mostrar_intercambio_dia_menu.php';
				}   
			?>
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