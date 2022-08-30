<?php
	include '../../header.php';
	if ($permisos['novedades'] == "0") {
?>		<script type="text/javascript">
      	window.open('<?= $baseUrl ?>', '_self');
    	</script>
<?php exit();}
	$titulo = 'Usuarios';
	$periodoActual = $_SESSION['periodoActual'];

	$consultaMeses = " SELECT DISTINCT mes FROM planilla_semanas ";
	$respuestaMeses = $Link->query($consultaMeses) or die ('Error al consultar los meses');
	if ($respuestaMeses->num_rows > 0) {
		while ($dataRespuestaMeses = $respuestaMeses->fetch_assoc()) {
			$meses[] = $dataRespuestaMeses['mes'];
		}
	}

	$nomMeses = [
      "01" => "Enero",
      "02" => "Febrero",
      "03" => "Marzo",
      "04" => "Abril",
      "05" => "Mayo",
      "06" => "Junio",
      "07" => "Julio",
      "08" => "Agosto",
      "09" => "Septiembre",
      "10" => "Octobre",
      "11" => "Novienmbre",
      "12" => "Diciembre"
	];

	$consultaComplementos = "SELECT CODIGO, ID FROM tipo_complemento ORDER BY CODIGO ";
	$respuestaComplementos = $Link->query($consultaComplementos) or die (mysqli_error($Link));
	if ($respuestaComplementos->num_rows > 0) {
		while ($dataComplementos = $respuestaComplementos->fetch_assoc()) {
			$complementos[$dataComplementos['CODIGO']] = $dataComplementos['CODIGO'];
		}
	}

	$consultaNovedad = "SELECT 
									nm.id,
									nm.mes AS mes,
									nm.semana AS semana,
									IF(nm.tipo_intercambio = 1, 'Intercambio de alimento', IF(nm.tipo_intercambio = 2, 'Intercambio de preparación', 'Intercambio de día de menú')) AS tipo, 
									nm.menu AS menu,
									nm.tipo_complem AS tipo_complemento,
									ge.DESCRIPCION AS grupo_etario,
									LOWER(DATE_FORMAT(nm.fecha_registro, '%d/%m/%Y %h:%I:%s %p')) AS fecha_registro,
									DATE_FORMAT(nm.fecha_vencimiento, '%d/%m/%Y') AS fecha_vencimiento,
									IF(nm.estado = 1, 'Activo', 'Reversado') AS estado,
									(SELECT descripcion FROM variacion_menu vm WHERE vm.id = nm.variacion_menu ) AS variacion
								FROM novedades_menu nm
								left join grupo_etario ge ON ge.ID = nm.cod_grupo_etario
								LEFT JOIN productos$periodoActual p ON p.Codigo = nm.cod_producto 
								WHERE 1 = 1 ";
					
	if (isset($_POST["mes"]) && !empty($_POST["mes"])) { $consultaNovedad.=" AND nm.mes = '".$_POST["mes"]."'"; }
	if (isset($_POST["semana"]) && !empty($_POST["semana"])) { $consultaNovedad.=" AND nm.semana = '".$_POST["semana"]."'"; }
	if (isset($_POST["estado"])) { $consultaNovedad.=" AND nm.estado = '".$_POST["estado"]."'"; }
	if (isset($_POST["complemento"]) && !empty($_POST["complemento"])) { $consultaNovedad.=" AND nm.tipo_complem = '".$_POST["complemento"]."'"; }
	if (isset($_POST["tipoNovedad"]) && !empty($_POST["tipoNovedad"])) { $consultaNovedad.=" AND nm.tipo_intercambio = '".$_POST["tipoNovedad"]."'"; }
	$consultaNovedad .= " ORDER BY nm.fecha_registro desc "; 
	$respuestaNovedad = $Link->query($consultaNovedad) or die('Error al consultar las novedades ' .mysqli_error($Link));
	if ($respuestaNovedad->num_rows > 0) {
		while ($dataRespuestaNovedad = $respuestaNovedad->fetch_assoc()) {
			$respuestas[] = $dataRespuestaNovedad;
		}
	}
	// exit(var_dump($consultaNovedad));
?>

<div class="row wrapper wrapper-content border-bottom white-bg page-heading">
	<div class="col-lg-8">
		<h2>Novedades de menú</h2>
		<ol class="breadcrumb">
			<li>
				<a href="<?php echo $baseUrl; ?>">Inicio</a>
			</li>
			<li class="active">
				<strong>Novedades de menú</strong>
			</li>
		</ol>
	</div>
</div>

<!-- seccion parametros de busqueda -->
<div class="wrapper wrapper-content  animated fadeInRight">
	<div class="row">
		<div class="col-lg-12">
			<div class="ibox">
				<div class="ibox-content">
					<div class="row">
						<div class="col-sm-12">
							<form action="#" id="formNovedadesMenu" name="formNovedadesMenu" method="post">
								<div class="row">
									<div class="col-sm-6 col-md-2 form-group">
										<label for="mes">Mes</label>
										<select class="form-control" id="mes" name="mes">
											<option value="">Seleccione Uno</option>
											<?php foreach ($meses as $key => $value): ?>
												<option value="<?= $value ?>">
													<?= $nomMeses[$value]; ?>
												</option>
											<?php endforeach ?>
										</select>
									</div>
									
									<div class="col-sm-6 col-md-2 form-group">
										<label for="semana">Semana</label>
										<select class="form-control" id="semana" name="semana">
											<option value="">Seleccione Uno</option>
										</select>
									</div>

									<div class="col-sm-6 col-md-2 form-group">
										<label for="estado">Estado</label>
										<select class="form-control" id="estado" name="estado">
											<option value="1" selected >Activo</option>
											<option value="0">Reversado</option>
										</select>
									</div>

									<div class="col-sm-6 col-md-3 form-group">
										<label for="complemento">Complemento</label>
										<select class="form-control" id="complemento" name="complemento">
											<option value="">Seleccione Uno</option>
											<?php foreach ($complementos as $key => $value): ?>
												<option value="<?= $key ?>"><?= $value ?></option>
											<?php endforeach ?>
										</select>
									</div>

									<div class="col-sm-6 col-md-3 form-group">
										<label for="tipoNovedad">Tipo de novedad</label>
										<select class="form-control" id="tipoNovedad" name="tipoNovedad">
											<!-- 1 => Alimento 2 => Preparación 3 => Dia Menú  -->
											<option value="">Seleccione Uno</option>
											<option value="1">Intercambio de alimento</option>
											<option value="2">Intercambio de preparación</option>
											<option value="3">Intercambio de día menú</option>
										</select>
									</div>

								</div> 
								<div class="row">
									<div class="col-sm form-group">
										<button class="btn btn-primary" type="submit" name="buscar" id="buscar" style="float: right; margin-right: 20px;"><i class="fa fa-search"></i> Buscar</button>
									</div>
								</div>
							</form> <!-- form -->
						</div> <!-- col-sm-12 -->
					</div><!-- row -->
				</div> <!-- ibox-content -->
			</div> <!-- ibox -->
		</div> <!-- col-lg-12 -->

		<div class="col-lg-12">
					<div class="ibox float-e-margins">
						<div class="ibox-content contentBackground">
							<table class="table table-striped table-hover selectableRows dataTablesNovedadesPriorizacion">
								<thead>
									<tr>
										<th>Número</th>
										<th>Mes</th>
										<th>Semana</th>
										<th>Tipo de novedad</th>
										<th>Menú</th>
										<th>Variación</th>
										<th>Tipo de complemento</th>
										<th>Grupo etario</th>
										<th>Fecha registro</th>
										<th>Fecha vencimiento</th>
										<th>Estado</th>
									</tr>
								</thead>
								<tbody>
									<?php 
										if (isset($respuestas)) { ?>
											<?php foreach ($respuestas as $key => $respuesta): ?>
												<tr>
													<td><?= $respuesta['id']; ?></td>
													<td><?= $respuesta['mes']; ?></td>
													<td><?= $respuesta['semana']; ?></td>
													<td><?= $respuesta['tipo']?></td>
													<td><?= $respuesta['menu']?></td>
													<td><?= $respuesta['variacion']?></td>
													<td><?= $respuesta['tipo_complemento']?></td>
													<td><?= $respuesta['grupo_etario']?></td>
													<td><?= $respuesta['fecha_registro']?></td>
													<td><?= $respuesta['fecha_vencimiento']?></td>
													<td><?= $respuesta['estado']?></td>
												</tr>
											<?php endforeach ?>
									<?php } ?>	
								</tbody>
								<tfoot>
									<tr>
										<th>Número</th>
										<th>Mes</th>
										<th>Semana</th>
										<th>Tipo de novedad</th>
										<th>Menú</th>
										<th>Variación</th>
										<th>Tipo de complemento</th>
										<th>Grupo etario</th>
										<th>Fecha registro</th>
										<th>Fecha vencimiento</th>
										<th>Estado</th>
									</tr>
								</tfoot>
							</table>
						</div>
					</div>
				</div>
			</div>
		</div>

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
		
		<input type="hidden" id="opcion" value="<?= $permisos['novedades'] ?>">

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
		<script src="<?php echo $baseUrl; ?>/theme/js/plugins/iCheck/icheck.min.js"></script>
		<script src="<?php echo $baseUrl; ?>/theme/js/plugins/toastr/toastr.min.js"></script>
		<script src="<?php echo $baseUrl; ?>/theme/js/plugins/toggle/toggle.min.js"></script>
		<script src="<?php echo $baseUrl; ?>/modules/intercambios/js/novedades_menu.js"></script>

		<form action="novedades_menu_ver.php" method="post" name="formVerNovedad" id="formVerNovedad" target="_blank" >
			<input type="hidden" name="idNovedad" id="idNovedad">
		</form>
	</body>
</html>