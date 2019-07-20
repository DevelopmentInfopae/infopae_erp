<?php
	include '../../header.php';
	set_time_limit (0);
	ini_set('memory_limit','6000M');

	$periodoActual = $_SESSION["periodoActual"];
	$titulo = "Intercambio de alimento";
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
			<h2>Intercambio de Preparación</h2>
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
			<!-- <button class="btn btn-primary btnGuardar" type="button">Guardar</button> -->




	<?php if($_SESSION["perfil"] == 1 || $_SESSION["perfil"] == 0) { ?>
					<!-- <a href="#" class="btn btn-primary" onclick="crearSede();"><i class="fa fa-plus"></i> Nueva</a> -->
	<?php } ?>
	<!-- <button class="btn btn-primary" id="btnRestablecerContadores">Restablecer almacenamiento local</button> -->
			</div>
	</div>
</div>

<!-- /.row wrapper de la cabecera de la seccion -->


<!-- FILTRO DE BUSQUEDA -->
<?php //include "filtro.php"  ?>

<div class="wrapper wrapper-content  animated fadeInRight">
	<div class="row">
		<div class="col-sm-12">
			<div class="ibox">
				<div class="ibox-title">
					<h5>Busqueda</h5>
<!-- 					<div class="ibox-tools">
						<div class="collapse-link"> <i class="fa fa-chevron-down"></i> </div>
					</div> -->
				</div>
				<div class="ibox-content">
					<div class="row">
						<div class="col-sm-12">
							<form action="" id="formParametros" name="formParametros" method="post">
								<div class="row">
									<?php if($_SESSION["perfil"] == 1 || $_SESSION["perfil"] == 0 || $_SESSION["perfil"] == 5 || $_SESSION["perfil"] == 6 || $_SESSION["perfil"] == 3) { ?>
										
										<div class="col-sm-4 form-group">
											<label for="mes">Mes</label>
											<select class="form-control" name="mes" id="mes" required>
												<option value="">Seleccione uno</option>
												<option value="01">Enero</option>									
												<option value="02">Febrero</option>									
												<option value="03">Marzo</option>									
												<option value="04">Abril</option>									
												<option value="05">Mayo</option>									
												<option value="06">Junio</option>									
												<option value="07">Julio</option>									
												<option value="08">Agosto</option>									
												<option value="09">Septiembre</option>									
												<option value="10">Octubre</option>									
												<option value="11">Noviembre</option>									
												<option value="12">Diciembre</option>									
											</select>
										</div>

										<div class="col-sm-4 form-group">
											<label for="semana">Semana</label>
											<select class="form-control" name="semana" id="semana" required>
												<option value="">Seleccione uno</option>									
											</select>
										</div>

										<div class="col-sm-4 form-group">
											<label for="dia">Día</label>
											<select class="form-control" name="dia" id="dia" required>
												<option value="">Seleccione uno</option>									
											</select>
										</div>

										<div class="col-sm-4 form-group">
											<label for="tipoComplemento">Tipo de complemento</label>
											<select class="form-control" name="tipoComplemento" id="tipoComplemento" required>
												<option value="">Seleccione uno</option>								
											</select>
										</div>

										<div class="col-sm-4 form-group">
											<label for="grupoEtario">Grupo etario</label>
											<select class="form-control" name="grupoEtario" id="grupoEtario" required>
												<option value="">Seleccione uno</option>								
											</select>
										</div>

										<div class="col-sm-4 form-group">
											<label for="menu">Menú</label>
											<input type="hidden" name="codigoMenu" id="codigoMenu">											
											<input type="text" class="form-control" name="menu" id="menu" readonly="readonly">
										</div>





									<?php } ?>
								</div>

								<div class="hr-line-dashed"></div>

								<div class="form-group row">
									<div class="col-sm-12">
										<button class="btn btn-primary" type="button" id="btnBuscar"> <i class="fa fa-search"></i> Buscar</button>
									</div>
								</div>

							</form>

						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<!-- Aqui lleganlas cajas de ajax con la preparación original y las opciones para modificarla -->
<div class="boxPreparacion"></div>



<div class="modal inmodal fade" id="ventanaConfirmar" tabindex="-1" role="dialog" style="display: none;" aria-hidden="true">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<div class="modal-header text-info" style="padding: 15px;">
				<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only">Close</span></button>
				<h3><i class="fa fa-question-circle fa-lg" aria-hidden="true"></i> Información InfoPAE </h3>
			</div>
			<div class="modal-body">
					<p class="text-center"></p>
			</div>
			<div class="modal-footer">
				<input type="hidden" id="codigoACambiar">
				<input type="hidden" id="estadoACambiar">
				<button type="button" class="btn btn-primary btn-outline btn-sm" data-dismiss="modal" onclick="revertirEstado();">Cancelar</button>
				<button type="button" class="btn btn-primary btn-sm" data-dismiss="modal" onclick="cambiarEstado();">Aceptar</button>
			</div>
		</div>
	</div>
</div>

<!-- Ventana formulari para la priorización -->
<div class="modal inmodal fade" id="ventanaFormularioPri" tabindex="-1" role="dialog" style="display: none;" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header text-info" style="padding: 15px;">
				<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only">Close</span></button>
				<h3><i class="fa fa-upload fa-lg" aria-hidden="true"></i> Importar Priorización  </h3>
			</div>
			<div class="modal-body">
				<form action="" name="frmSubirArchivoPriorizacion" id="frmSubirArchivoPriorizacion">
					<div class="row">
						<div class="col-md-4">
							<div class="form-group">
								<label for="mes">Mes</label>
								<select class="form-control" name="mes" id="mes" required>
									<option value="">Selección</option>
									<?php
										$consultaMes = "SELECT distinct MES AS mes FROM planilla_semanas;";
										$resultadoMes = $Link->query($consultaMes);
										if($resultadoMes->num_rows > 0){
											while($registros = $resultadoMes->fetch_assoc()) {
									?>
											<option value="<?php echo $registros["mes"]; ?>"><?php echo $registros["mes"]; ?></option>
									<?php
											}
										}
									?>
								</select>
							</div>
						</div>
						<div class="col-md-4">
							<div class="form-group">
								<label for="mes">Semana</label>
								<select class="form-control" name="semana" id="semana" required>
									<option value="">Selección</option>
								</select>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-10">
							<div class="form-group">
								<label for="archivoPriorizacion">Archivo</label>
								<div class="fileinput fileinput-new input-group" data-provides="fileinput">
									<div class="form-control" data-trigger="fileinput">
										<i class="glyphicon glyphicon-file fileinput-exists"></i> <span class="fileinput-filename"></span>
									</div>
									<span class="input-group-addon btn btn-default btn-file"><span class="fileinput-new">Seleccionar archivo</span><span class="fileinput-exists">Cambiar</span>
										<input type="file" name="archivoPriorizacion" id="archivoPriorizacion" accept=".csv, .xlsx" required>
									</span>
									<a href="#" class="input-group-addon btn btn-default fileinput-exists" data-dismiss="fileinput">Borrar</a>
								</div>
								<label for="archivoPriorizacion" class="error" style="display: none;"></label>
							</div>
							<label class="text-warning">Para mayor eficacia es mejor subir el archivo con extensión .CSV </label>
						</div>
					</div>
				</form>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-primary btn-outline btn-sm" data-dismiss="modal">Cancelar</button>
				<button type="button" class="btn btn-primary btn-sm" id="subirArchivoPriorizacion">Aceptar</button>
			</div>
		</div>
	</div>
</div>

<!-- Ventana formulari para la focalización -->
<div class="modal inmodal fade" id="ventanaFormularioFoc" tabindex="-1" role="dialog" style="display: none;" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header text-info" style="padding: 15px;">
				<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only">Close</span></button>
				<h3><i class="fa fa-upload fa-lg" aria-hidden="true"></i> Importar Focalización  </h3>
			</div>
			<div class="modal-body">
				<form action="" name="frmSubirArchivoFocalizacion" id="frmSubirArchivoFocalizacion">
					<div class="row">
						<div class="col-md-4">
							<div class="form-group">
								<label for="mesFocalizacion">Mes</label>
								<select class="form-control" name="mesFocalizacion" id="mesFocalizacion" required>
									<option value="">Selección</option>
									<?php
										$consultaMes = "SELECT distinct MES AS mes FROM planilla_semanas;";
										$resultadoMes = $Link->query($consultaMes);
										if($resultadoMes->num_rows > 0){
											while($registros = $resultadoMes->fetch_assoc()) {
									?>
											<option value="<?php echo $registros["mes"]; ?>"><?php echo $registros["mes"]; ?></option>
									<?php
											}
										}
									?>
								</select>
							</div>
						</div>
						<div class="col-md-4">
							<div class="form-group">
								<label for="semanaFocalizacion">Semana</label>
								<select class="form-control" name="semanaFocalizacion" id="semanaFocalizacion" required>
									<option value="">Selección</option>
								</select>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-10">
							<div class="form-group">
								<label for="archivoFocalizacion">Archivo</label>
								<div class="fileinput fileinput-new input-group" data-provides="fileinput">
									<div class="form-control" data-trigger="fileinput">
										<i class="glyphicon glyphicon-file fileinput-exists"></i> <span class="fileinput-filename"></span>
									</div>
									<span class="input-group-addon btn btn-default btn-file"><span class="fileinput-new">Seleccionar archivo</span><span class="fileinput-exists">Cambiar</span>
										<input type="file" name="archivoFocalizacion" id="archivoFocalizacion" accept=".csv" required>
									</span>
									<a href="#" class="input-group-addon btn btn-default fileinput-exists" data-dismiss="fileinput">Borrar</a>
								</div>
								<label for="archivoFocalizacion" class="error" style="display: none;"></label>
							</div>
							<label class="text-warning">Para mayor eficacia es mejor subir el archivo con extensión .CSV </label>
						</div>
						<div class="col-md-2">
							<label for="archivoFocalizacion">Validar</label>
							<input type="checkbox" name="validar" id="validar" data-toggle="toggle" data-on="si" data-off="no" checked>
						</div>
					</div>
				</form>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-primary btn-outline btn-sm" data-dismiss="modal" onclick="$('#ventanaFormularioFoc').on('hidden.bs.modal', function (e) { $('#frmSubirArchivoFocalizacion')[0].reset(); })">Cancelar</button>
				<button type="button" class="btn btn-primary btn-sm" id="subirArchivoFocalizacion">Aceptar</button>
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

<!-- Custom and plugin javascript -->
<script src="<?php echo $baseUrl; ?>/theme/js/inspinia.js"></script>
<script src="<?php echo $baseUrl; ?>/theme/js/plugins/pace/pace.min.js"></script>
<script src="<?php echo $baseUrl; ?>/theme/js/plugins/dataTables/datatables.min.js"></script>
<script src="<?php echo $baseUrl; ?>/theme/js/plugins/jasny/jasny-bootstrap.min.js"></script>
<script src="<?php echo $baseUrl; ?>/theme/js/plugins/validate/jquery.validate.min.js"></script>
<script src="<?php echo $baseUrl; ?>/theme/js/plugins/toggle/toggle.min.js"></script>
<script src="<?php echo $baseUrl; ?>/theme/js/plugins/toastr/toastr.min.js"></script>
<script src="<?php echo $baseUrl; ?>/theme/js/plugins/iCheck/icheck.min.js"></script>

<script src="<?php echo $baseUrl; ?>/theme/js/plugins/select2/select2.full.min.js"></script>

<script src="<?php echo $baseUrl; ?>/modules/intercambios/js/intercambio_alimento.js?v=<?= $cacheBusting; ?>"></script>
<script src="<?php echo $baseUrl; ?>/modules/intercambios/js/filtro.js?v=<?= $cacheBusting; ?>"></script>







<script type="text/javascript">
/*
	if ($('#IdFT').val() != null && $('#tipoProducto').val() != "04") {
	for (var i = <?php if(isset($cntFTD)){echo $cntFTD;} else {echo 0;} ?>; i > 0; i--) {
	obtenerProductos(i);
	}
	}

	if ($('#tipoProducto').val() == "01") {
	ocultarDatosDetPreparado(1);
	} else if ($('#tipoProducto').val() == "02") {
	ocultarDatosDetPreparado(2);
	}

	//$('.productoFichaTecnicaDet select').select2({width : "100%"});
	console.log(numProducto);
	
*/

</script>















<!-- Page-Level Scripts -->

	<form action="sede.php" method="post" name="formVerSede" id="formVerSede">
		<input type="hidden" name="codSede" id="codSede">
		<input type="hidden" name="nomSede" id="nomSede">
		<input type="hidden" name="nomInst" id="nomInst">
	</form>

	<form action="sede_editar.php" method="post" name="formEditarSede" id="formEditarSede">
		<input type="hidden" name="codigoSede" id="codigoSede">
		<input type="hidden" name="nombreSede" id="nombreSede">
	</form>

	<form action="../dispositivos_biometricos/index.php" method="post" name="formDispositivosSede" id="formDispositivosSede">
		<input type="hidden" name="cod_sede" id="cod_sede" value="">
	</form>

	<form action="../infraestructuras/ver_infraestructura.php" method="post" name="formInfraestructuraSede" id="formInfraestructuraSede">
		<input type="hidden" name="cod_sede" id="cod_sede" value="">
	</form>

	<form action="../titulares_derecho/index.php" method="post" name="formTitularesSede" id="formTitularesSede">
		<input type="hidden" name="cod_sede" id="cod_sede" value="">
	</form>

</body>
</html>
