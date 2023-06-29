<?php
	include '../../header.php';

	if ($permisos['novedades'] == "0") {
    	?><script type="text/javascript">
      		window.open('<?= $baseUrl ?>', '_self');
    	</script>
<?php exit();}
  	else {
?>	<script type="text/javascript">
      	const list = document.querySelector(".li_novedades");
      	list.className += " active ";
      	const list2 = document.querySelector(".li_priorizacion");
      	list2.className += " active ";
    </script>
<?php
  	}

	set_time_limit (0);
	ini_set('memory_limit','6000M');

	$idNovedad = (isset($_POST['idNovedad']) && $_POST['idNovedad'] != '') ? mysqli_real_escape_string($Link, $_POST["idNovedad"]) : "";
	$periodoActual = mysqli_real_escape_string($Link, $_SESSION['periodoActual']);

	$consulta = " SELECT 	DISTINCT dp.ID, 
							u.Ciudad, 
							s.nom_inst, 
							s.nom_sede, 
							ps.MES, 
							dp.* 
						FROM novedades_priorizacion dp 
						LEFT JOIN sedes$periodoActual s ON dp.cod_sede = s.cod_sede 
						LEFT JOIN  ubicacion u ON s.cod_mun_sede = u.CodigoDANE 
						LEFT JOIN planilla_semanas ps ON dp.Semana = ps.SEMANA 
						WHERE dp.id = $idNovedad ";
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

	$cantGruposEtarios = $_SESSION['cant_gruposEtarios'];
	$consultaComplementos = "SELECT CODIGO, ID FROM tipo_complemento ORDER BY CODIGO ";
	$respuestaComplementos = $Link->query($consultaComplementos) or die (mysqli_error($Link));
	if ($respuestaComplementos->num_rows > 0) {
		while ($dataComplementos = $respuestaComplementos->fetch_object()) {
			$complementos[] = $dataComplementos;
		}
	}
	$auxTotalComplemento = 0;
	$nameLabel = get_titles('novedades', 'priorizacion', $labels); 
	$titulo = $nameLabel . ' - Ver';
?>

<div class="row wrapper wrapper-content border-bottom white-bg page-heading">
	<div class="col-lg-8">
		<h2><?= $titulo ?></h2>
		<ol class="breadcrumb">
			<li>
				<a href="<?php echo $baseUrl; ?>">Inicio</a>
			</li>
			<li> <a href="<?php echo $baseUrl; ?>/modules/novedades_priorizacion"><?= $nameLabel ?></a> </li>
			<li class="active">
				<strong><?= $titulo ?></strong>
			</li>
		</ol>
	</div>
	<div class="col-lg-4">
		<div class="title-action">
			<?php if($_SESSION['perfil'] == "0" || $permisos['novedades'] == "2"){ ?>
				<a href="#" class="btn btn-primary" onclick="crearNovedadPriorizacion();"><i class="fa fa-plus"></i> Nuevo </a>
			<?php } ?>
		</div>
	</div>
</div>

<div class="wrapper wrapper-content animated fadeInRight">
	<div class="row">
    	<div class="col-lg-12">
			<form class="col-lg-12" action="" method="post" name="formArchivos" id="formArchivos" enctype="multipart/form-data">
				<div class="ibox float-e-margins">
					<div class="ibox-content contentBackground">
						<div class="row">
							<div class="col-sm-4 form-group">
								<label for="municipio">Municipio</label>
								<input type="text" name="municipio" id="municipio" value="<?php echo $datosNovedad['Ciudad']; ?>" class="form-control" readonly>
							</div><!-- /.col -->
							<div class="col-sm-8 form-group">
								<label for="institucion">Institución</label>
								<input type="text" name="institucion" id="institucion" value="<?php echo $datosNovedad['nom_inst']; ?>" class="form-control" readonly>
							</div><!-- /.col -->
							<div class="col-sm-6 form-group">
								<label for="sede">Sede</label>
								<input type="text" name="sede" id="sede" value="<?php echo $datosNovedad['nom_sede']; ?>" class="form-control" readonly>
							</div><!-- /.col -->
							<div class="col-sm-3 form-group">
								<label for="mes">Mes</label>
								<input type="text" name="mes" id="mes" value="<?php echo $mesNm; ?>" class="form-control" readonly>
							</div><!-- /.col -->
							<div class="col-sm-3 form-group">
								<label for="semana">Semana</label>
								<input type="text" name="semana" id="semana" value="<?php echo $datosNovedad['Semana']; ?>" class="form-control" readonly>
							</div><!-- /.col -->
						</div><!-- -/.row -->
					</div><!-- /.ibox-content -->
				</div><!-- /.ibox float-e-margins -->

				<div class="ibox float-e-margins priorizacionAction">
					<div class="ibox-content contentBackground">
						<div class="table-responsive">
							<table width="100%" class="table table-striped table-bordered table-hover selectableRows">
								<thead>
									<tr>
										<th>Complemento Inicial</th>
										<th>Cant Total Inicial</th>
										<?php 
											for ($i=1; $i <= $cantGruposEtarios; $i++) { 
										?>
												<th>Grupo Etario <?= $i ?></th>
										<?php 
											}
										?>
									</tr>
								</thead>
								<tbody>
									<?php foreach ($complementos as $key => $value): ?>
										<?php if ($datosPriorizacion[$value->CODIGO] > 0): ?>
											<tr class="<?= $key ?>actual">
												<td> 
													<input 	type="text" 
															class="form-control" 
															name="<?= $value->CODIGO ?>nm" 
															id="<?= $value->CODIGO ?>nm" 
															value="<?= $value->CODIGO ?>" 
															readonly> 
												</td>
												<td> 
													<input 	type="text" 
															class="form-control" 
															name="<?= $value->CODIGO ?>actualTotal" 
															id="<?= $value->CODIGO ?>actualTotal" 
															value="<?= $datosPriorizacion[$value->CODIGO]?> " 
															readonly 
															style="text-align:center;"> 
												</td>
												<?php 
													for ($i=1; $i <= $cantGruposEtarios ; $i++) { 
												?>
														<td> 
															<input 	type="text" 
																	class="form-control" 
																	name="<?= $value->CODIGO ?>actual<?= $i ?>" 
																	id="<?= $value->CODIGO ?>actual<?= $i ?>" 
																	value="<?= $datosPriorizacion['Etario'.$i.'_'.$value->CODIGO]; ?>" 
																	readonly 
																	style="text-align:center;"> </td>
												<?php 		
													}
												?>
											</tr>
										<?php endif ?>
									<?php endforeach ?>
								</tbody>
							</table>
						</div><!-- /.table-responsive -->
					</div><!-- /.ibox-content -->
				</div><!-- /.ibox float-e-margins -->

				<div class="ibox float-e-margins priorizacionAction">
					<div class="ibox-content contentBackground">
						<div class="table-responsive">
							<table width="100%" class="table table-striped table-bordered table-hover selectableRows tablaNuevasCantidades">
								<thead>
									<tr>
										<th>Nuevo Complemento</th>
										<th>Nueva Cant Total</th>
										<?php 
											for ($i=1; $i <= $cantGruposEtarios; $i++) { 
										?>
												<th>Grupo Etario <?= $i ?></th>
										<?php 
											}
										?>
									</tr>
								</thead>
								<tbody>
									<?php foreach ($complementos as $key => $value): ?>
										<tr class="<?= $value->CODIGO ?>">
											<td> 
												<input 	type="text" 
														class="form-control" 
														name="<?= $value->CODIGO ?>nm" 
														id="<?= $value->CODIGO ?>nm" 
														value="<?= $value->CODIGO ?>" 
														readonly> 
											</td>
											<td> 
												<input 	type="text" 
														class="form-control" 
														name="<?= $value->CODIGO ?>actualTotal" 
														id="<?= $value->CODIGO ?>actualTotal" 
														value="<?= $datosNovedad[$value->CODIGO]?> " 
														readonly 
														style="text-align:center;"> 
											</td>
											<?php 
												for ($i=1; $i <= $cantGruposEtarios ; $i++) { 
											?>
													<td> 
														<input 	type="text" 
																class="form-control" 
																name="<?= $value->CODIGO ?>actual<?= $i ?>" 
																id="<?= $value->CODIGO ?>actual<?= $i ?>" 
																value="<?= $datosNovedad['Etario'.$i.'_'.$value->CODIGO]; ?>" 
																readonly 
																style="text-align:center;"> 
													</td>
											<?php 		
													}
											?> 
										</tr>
									<?php endforeach ?>
									<tr class="total">
										<td>
											<input type="text" class="form-control" name="totalNm" id="totalNm" value="TOTAL" readonly>
										</td>
										<?php foreach ($complementos as $key => $value): ?>
										<?php 
											$auxTotalComplemento += $datosNovedad[$value->CODIGO]; 
										?>
										<?php endforeach ?>
										<td>
											<input 	type="text" 
													class="form-control" 
													name="totalTotal" 
													id="totalTotal" 
													value="<?= $auxTotalComplemento ?>" 
													readonly 
													style="text-align:center;"> 
										</td>
										<?php 
											for ($i=1; $i <= $cantGruposEtarios; $i++) {
												$aux = 0; 
										?>
												<?php foreach ($complementos as $key => $value): ?>
													<?php $aux += $datosNovedad['Etario'.$i.'_'.$value->CODIGO] ?>
												<?php endforeach ?>
												<td> 
													<input 	type="text" 
															min="1" 
															pattern="^[0-9]+" 
															class="form-control" 
															name="total<?= $i ?>" 
															id="total<?= $i ?>" 
															value="<?= $aux ?>" 
															readonly  
															style="text-align:center;"> </td>
										<?php 
											}
										?>
									</tr>
								</tbody>
							</table>
						</div><!-- /.table-responsive -->
					</div><!-- /.ibox-content -->
				</div><!-- /.ibox float-e-margins -->

				<!-- vamos a mostrar el archivo solo si existe una direccion si no el div ira completo con la observacion -->
				<?php if ($datosNovedad['arch_adjunto'] !== ""): ?> 
					<div class="ibox float-e-margins priorizacionAction">
						<div class="ibox-content contentBackground">
							<div class="row">
								<div class="col-sm-6 form-group">
									<label for="observaciones">Observaciones</label>
									<textarea name="observaciones" id="observaciones" class="form-control" rows="8" cols="80" readonly><?php echo $datosNovedad['observaciones']; ?></textarea>
								</div><!-- /.col -->
								<div class="col-sm-6 form-group">
									<label for="departamento">Archivo</label>
									<div style="text-align:center; box-sizing:border-box; padding:20px">
										<?php
											$url = $baseUrl."/".$datosNovedad['arch_adjunto'];
											$ext = substr($url,-3);
										?>
										<a href="<?php echo $url; ?>" target="_blank" style="color:#1ab394;">
										<?php
											if($ext == 'pdf'){
												echo "<i class=\"fa fa-file-pdf-o\" style=\"font-size:60px\"></i>";
											}else{
												echo "<i class=\"fa fa-file-image-o\" style=\"font-size:60px\"></i>";
											}
										?>
										<h3>Ver Archivo</h3>
									</a>
								</div>
							</div><!-- /.col -->
						</div><!-- -/.row -->
					</div><!-- /.ibox-content -->
				</div><!-- /.ibox float-e-margins -->

				<?php else: ?>
					<div class="ibox float-e-margins priorizacionAction">
						<div class="ibox-content contentBackground">
							<div class="row">
								<div class="col-sm-12 form-group">
									<label for="observaciones">Observaciones</label>
									<textarea name="observaciones" id="observaciones" class="form-control" rows="8" cols="80" readonly><?php echo $datosNovedad['observaciones']; ?></textarea>
								</div><!-- /.col -->
							</div><!-- -/.row -->
						</div><!-- /.ibox-content -->
					</div><!-- /.ibox float-e-margins -->
				<?php endif ?>
			</form>
    	</div><!-- /.col-lg-12 -->
  	</div><!-- /.row -->
</div><!-- /.wrapper wrapper-content animated fadeInRight -->


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

<script src="<?php echo $baseUrl; ?>/modules/novedades_priorizacion/js/novedades_priorizacion_ver.js"></script>

<!-- Page-Level Scripts -->

<?php mysqli_close($Link); ?>
</body>
</html>
