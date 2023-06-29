<?php
	$titulo = "Registro de entregas vía QR - BarCode";
	include '../../header.php';
	if ($permisos['entregas_biometricas'] == "0" || $permisos['entregas_biometricas'] == "1") {
		?><script type="text/javascript">
			window.open('<?= $baseUrl ?>', '_self');
		</script>
	<?php exit();}
	else {
    ?>	<script type="text/javascript">
      		const list = document.querySelector(".li_entregas_biometricas");
      		list.className += " active ";
			const list2 = document.querySelector(".li_registroBiometricoBarcode");
			list2.className += " active ";
    	</script>
  	<?php
  	}

	set_time_limit (0);
	ini_set('memory_limit','6000M');

	$periodoActual = $_SESSION["periodoActual"];
	$institucionNombre = "";

	date_default_timezone_set('America/Bogota');
	$fecha = date("Y-m-d H:i:s");
	$cacheBusting = date("YmdHis");
	$dia = intval(date("d"));
	$mes = date("m");
	$anno = date("Y");
	$anno2d = date("y");

	$validacion = "Lector de Huella";

 	$sedeP = "";
	if(isset($_GET["sede"]) && $_GET["sede"] != ""){
		$sedeP = mysqli_real_escape_string($Link, $_GET['sede']);
	}

	$institucionP = "";
	$consulta = " SELECT cod_inst FROM sedes$periodoActual WHERE cod_sede = \"$sedeP\" ";
	//echo $consulta;
	$resultado = $Link->query($consulta) or die ('No se pudo cargar la institucion. '. mysqli_error($Link));
	if($resultado->num_rows >= 1){
		$row = $resultado->fetch_assoc();
		$institucionP = $row["cod_inst"];
	}


	//Busqueda de la semana actual
	$semanaActual = "";
	$consulta = "select semana from planilla_semanas where ano = \"$anno\" and mes = \"$mes\" and dia = \"$dia\" ";
	// var_dump($consulta);				
	$resultado = $Link->query($consulta) or die ('No se pudo cargar la semana actual. '. mysqli_error($Link));
	if($resultado->num_rows >= 1){
		$row = $resultado->fetch_assoc();
		$semanaActual = $row["semana"];
	}

	$nameLabel = get_titles('entregasBiometricas', 'registroBiometricoBarcode', $labels);
	
?>

<!-- <link rel="stylesheet" href="css/custom.css?v=<?= $cacheBusting; ?>"> -->

<div class="row wrapper wrapper-content border-bottom white-bg page-heading">
	<div class="col-xs-8">
			<h2><?= $nameLabel ?></h2>
			<ol class="breadcrumb">
				<li>
					<a href="<?php echo $baseUrl; ?>">Inicio</a>
				</li>
				<li class="active">
					<strong> <?= $nameLabel ?> </strong>
				</li>
			</ol>
	</div>
	<div class="col-xs-4">
		<div class="title-action registroConsumo" style="display: none">
			<button class="btn btn-primary btnGuardar" type="button">Guardar</button>
			<button class="btn btn-primary btnSellar" type="button">Guardar Definitivamente</button>
		</div>
	<?php if($_SESSION["perfil"] == 1 || $_SESSION["perfil"] == 0) { ?>
					<!-- <a href="#" class="btn btn-primary" onclick="crearSede();"><i class="fa fa-plus"></i> Nueva</a> -->
	<?php } ?>
	<!-- <button class="btn btn-primary" id="btnRestablecerContadores">Restablecer almacenamiento local</button> -->
	</div>
</div>
<!-- /.row wrapper de la cabecera de la seccion -->

<?php
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

<input type="hidden" name="validacion" id="validacion" value="<?= $validacion ?>">
<input type="hidden" name="institucionP" id="institucionP" value="<?= $institucionP ?>">
<input type="hidden" name="sedeP" id="sedeP" value="<?= $sedeP ?>">

<?php include "filtro.php"  ?>

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
<script src="<?php echo $baseUrl; ?>/modules/entregas_biometricas/js/filtro.js?v=<?= $cacheBusting; ?>"></script>
<script src="<?php echo $baseUrl; ?>/modules/entregas_biometricas/js/index.js?v=<?= $cacheBusting; ?>"></script>
<!-- <script src="<?php echo $baseUrl; ?>/modules/entregas_biometricas/js/consumo_biometricos.js?v=<?= $cacheBusting; ?>"></script> -->

<!-- Page-Level Scripts -->

</body>
</html>
