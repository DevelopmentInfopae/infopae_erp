<?php
	include '../../header.php';

	if ($permisos['novedades'] == "0") {
    	?><script type="text/javascript">
      		window.open('<?= $baseUrl ?>', '_self');
    	</script>
  	<?php exit();}

	set_time_limit (0);
	ini_set('memory_limit','6000M');

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
		<h2>Ver Novedad en Priorización</h2>
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
											<th>Grupo Etario 1</th>
											<th>Grupo Etario 2</th>
											<th>Grupo Etario 3</th>
										</tr>
									</thead>
									<tbody>
										<?php if($datosPriorizacion['APS'] > 0 ){ ?>
											<tr class="APSactual">
												<td> <input type="text" class="form-control" name="APSnm" id="APSnm" value="APS" readonly> </td>
												<td> <input type="text" class="form-control" name="APSactualTotal" id="APSactualTotal" value="<?php echo $datosPriorizacion['APS']; ?>" readonly style="text-align:center;"> </td>
												<td> <input type="text" class="form-control" name="APSactual1" id="APSactual1" value="<?php echo $datosPriorizacion['Etario1_APS']; ?>" readonly style="text-align:center;"> </td>
												<td> <input type="text" class="form-control" name="APSactual2" id="APSactual2" value="<?php echo $datosPriorizacion['Etario2_APS']; ?>" readonly style="text-align:center;"> </td>
												<td> <input type="text" class="form-control" name="APSactual3" id="APSactual3" value="<?php echo $datosPriorizacion['Etario3_APS']; ?>" readonly style="text-align:center;"> </td>
											</tr>
										<?php	} ?>

										<?php if($datosPriorizacion['CAJMPS'] > 0 ){ ?>
											<tr class="CAJMPSactual">
												<td> <input type="text" class="form-control" name="CAJMPSnm" id="CAJMPSnm" value="CAJMPS" readonly> </td>
												<td> <input type="text" class="form-control" name="CAJMPSactualTotal" id="CAJMPSactualTotal" value="<?php echo $datosPriorizacion['CAJMPS']; ?>" readonly style="text-align:center;"> </td>
												<td> <input type="text" class="form-control" name="CAJMPSactual1" id="CAJMPSactual1" value="<?php echo $datosPriorizacion['Etario1_CAJMPS']; ?>" readonly style="text-align:center;"> </td>
												<td> <input type="text" class="form-control" name="CAJMPSactual2" id="CAJMPSactual2" value="<?php echo $datosPriorizacion['Etario2_CAJMPS']; ?>" readonly style="text-align:center;"> </td>
												<td> <input type="text" class="form-control" name="CAJMPSactual3" id="CAJMPSactual3" value="<?php echo $datosPriorizacion['Etario3_CAJMPS']; ?>" readonly style="text-align:center;"> </td>
											</tr>
										<?php	} ?>

										<?php if($datosPriorizacion['CAJTPS'] > 0 ){ ?>
											<tr class="CAJTPSactual">
												<td> <input type="text" class="form-control" name="CAJTPSnm" id="CAJTPSnm" value="CAJTPS" readonly> </td>
												<td> <input type="text" class="form-control" name="CAJTPSactualTotal" id="CAJTPSactualTotal" value="<?php echo $datosPriorizacion['CAJTPS']; ?>" readonly style="text-align:center;"> </td>
												<td> <input type="text" class="form-control" name="CAJTPSactual1" id="CAJTPSactual1" value="<?php echo $datosPriorizacion['Etario1_CAJTPS']; ?>" readonly style="text-align:center;"> </td>
												<td> <input type="text" class="form-control" name="CAJTPSactual2" id="CAJTPSactual2" value="<?php echo $datosPriorizacion['Etario2_CAJTPS']; ?>" readonly style="text-align:center;"> </td>
												<td> <input type="text" class="form-control" name="CAJTPSactual3" id="CAJTPSactual3" value="<?php echo $datosPriorizacion['Etario3_CAJTPS']; ?>" readonly style="text-align:center;"> </td>
											</tr>
										<?php	} ?>

										<?php if($datosPriorizacion['CAJMRI'] > 0 ){ ?>
											<tr class="CAJMRIactual">
												<td> <input type="text" class="form-control" name="CAJMRInm" id="CAJMRInm" value="CAJMRI" readonly> </td>
												<td> <input type="text" class="form-control" name="CAJMRIactualTotal" id="CAJMRIactualTotal" value="<?php echo $datosPriorizacion['CAJMRI']; ?>" readonly style="text-align:center;"> </td>
												<td> <input type="text" class="form-control" name="CAJMRIactual1" id="CAJMRIactual1" value="<?php echo $datosPriorizacion['Etario1_CAJMRI']; ?>" readonly style="text-align:center;"> </td>
												<td> <input type="text" class="form-control" name="CAJMRIactual2" id="CAJMRIactual2" value="<?php echo $datosPriorizacion['Etario2_CAJMRI']; ?>" readonly style="text-align:center;"> </td>
												<td> <input type="text" class="form-control" name="CAJMRIactual3" id="CAJMRIactual3" value="<?php echo $datosPriorizacion['Etario3_CAJMRI']; ?>" readonly style="text-align:center;"> </td>
											</tr>
										<?php	} ?>

										<?php if($datosPriorizacion['CAJTRI'] > 0 ){ ?>
											<tr class="CAJTRIactual">
												<td> <input type="text" class="form-control" name="CAJTRInm" id="CAJTRInm" value="CAJTRI" readonly> </td>
												<td> <input type="text" class="form-control" name="CAJTRIactualTotal" id="CAJTRIactualTotal" value="<?php echo $datosPriorizacion['CAJTRI']; ?>" readonly style="text-align:center;"> </td>
												<td> <input type="text" class="form-control" name="CAJTRIactual1" id="CAJTRIactual1" value="<?php echo $datosPriorizacion['Etario1_CAJTRI']; ?>" readonly style="text-align:center;"> </td>
												<td> <input type="text" class="form-control" name="CAJTRIactual2" id="CAJTRIactual2" value="<?php echo $datosPriorizacion['Etario2_CAJTRI']; ?>" readonly style="text-align:center;"> </td>
												<td> <input type="text" class="form-control" name="CAJTRIactual3" id="CAJTRIactual3" value="<?php echo $datosPriorizacion['Etario3_CAJTRI']; ?>" readonly style="text-align:center;"> </td>
											</tr>
										<?php	} ?>

										<?php if($datosPriorizacion['RPC'] > 0 ){ ?>
											<tr class="RPCactual">
												<td> <input type="text" class="form-control" name="RPCnm" id="RPCnm" value="RPC" readonly> </td>
												<td> <input type="text" class="form-control" name="RPCactualTotal" id="RPCactualTotal" value="<?php echo $datosPriorizacion['RPC']; ?>" readonly style="text-align:center;"> </td>
												<td> <input type="text" class="form-control" name="RPCactual1" id="RPCactual1" value="<?php echo $datosPriorizacion['Etario1_RPC']; ?>" readonly style="text-align:center;"> </td>
												<td> <input type="text" class="form-control" name="RPCactual2" id="RPCactual2" value="<?php echo $datosPriorizacion['Etario2_RPC']; ?>" readonly style="text-align:center;"> </td>
												<td> <input type="text" class="form-control" name="RPCactual3" id="RPCactual3" value="<?php echo $datosPriorizacion['Etario3_RPC']; ?>" readonly style="text-align:center;"> </td>
											</tr>
										<?php	} ?>
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
											<th>Grupo Etario 1</th>
											<th>Grupo Etario 2</th>
											<th>Grupo Etario 3</th>
										</tr>
									</thead>
									<tbody>

										<?php if($datosNovedad['APS'] > 0 ){ ?>
											<tr class="APS">
												<td> <input type="text" class="form-control" name="APSnm" id="APSnm" value="APS" readonly> </td>
												<td> <input type="text" class="form-control" name="APSTotal" id="APSTotal" value="<?php echo $datosNovedad['APS']; ?>" readonly style="text-align:center;"> </td>
												<td> <input type="text" class="form-control" name="APS1" id="APS1" value="<?php echo $datosNovedad['Etario1_APS']; ?>" readonly style="text-align:center;"> </td>
												<td> <input type="text" class="form-control" name="APS2" id="APS2" value="<?php echo $datosNovedad['Etario2_APS']; ?>" readonly style="text-align:center;"> </td>
												<td> <input type="text" class="form-control" name="APS3" id="APS3" value="<?php echo $datosNovedad['Etario3_APS']; ?>" readonly style="text-align:center;"> </td>
											</tr>
										<?php } ?>

										<?php if($datosNovedad['CAJMPS'] > 0 ){ ?>
											<tr class="CAJMPS">
												<td> <input type="text" class="form-control" name="CAJMPSnm" id="CAJMPSnm" value="CAJMPS" readonly> </td>
												<td> <input type="text" class="form-control" name="CAJMPSTotal" id="CAJMPSTotal" value="<?php echo $datosNovedad['CAJMPS']; ?>" readonly style="text-align:center;"> </td>
												<td> <input type="text" class="form-control" name="CAJMPS1" id="CAJMPS1" value="<?php echo $datosNovedad['Etario1_CAJMPS']; ?>" readonly style="text-align:center;"> </td>
												<td> <input type="text" class="form-control" name="CAJMPS2" id="CAJMPS2" value="<?php echo $datosNovedad['Etario2_CAJMPS']; ?>" readonly style="text-align:center;"> </td>
												<td> <input type="text" class="form-control" name="CAJMPS3" id="CAJMPS3" value="<?php echo $datosNovedad['Etario3_CAJMPS']; ?>" readonly style="text-align:center;"> </td>
											</tr>
										<?php } ?>

										<?php if($datosNovedad['CAJTPS'] > 0 ){ ?>
											<tr class="CAJTPS">
												<td> <input type="text" class="form-control" name="CAJTPSnm" id="CAJTPSnm" value="CAJTPS" readonly> </td>
												<td> <input type="text" class="form-control" name="CAJTPSTotal" id="CAJTPSTotal" value="<?php echo $datosNovedad['CAJTPS']; ?>" readonly style="text-align:center;"> </td>
												<td> <input type="text" class="form-control" name="CAJTPS1" id="CAJTPS1" value="<?php echo $datosNovedad['Etario1_CAJTPS']; ?>" readonly style="text-align:center;"> </td>
												<td> <input type="text" class="form-control" name="CAJTPS2" id="CAJTPS2" value="<?php echo $datosNovedad['Etario2_CAJTPS']; ?>" readonly style="text-align:center;"> </td>
												<td> <input type="text" class="form-control" name="CAJTPS3" id="CAJTPS3" value="<?php echo $datosNovedad['Etario3_CAJTPS']; ?>" readonly style="text-align:center;"> </td>
											</tr>
										<?php } ?>

										<?php if($datosNovedad['CAJMRI'] > 0 ){ ?>
											<tr class="CAJMRI">
												<td> <input type="text" class="form-control" name="CAJMRInm" id="CAJMRInm" value="CAJMRI" readonly> </td>
												<td> <input type="text" class="form-control" name="CAJMRITotal" id="CAJMRITotal" value="<?php echo $datosNovedad['CAJMRI']; ?>" readonly style="text-align:center;"> </td>
												<td> <input type="text" class="form-control" name="CAJMRI1" id="CAJMRI1" value="<?php echo $datosNovedad['Etario1_CAJMRI']; ?>" readonly style="text-align:center;"> </td>
												<td> <input type="text" class="form-control" name="CAJMRI2" id="CAJMRI2" value="<?php echo $datosNovedad['Etario2_CAJMRI']; ?>" readonly style="text-align:center;"> </td>
												<td> <input type="text" class="form-control" name="CAJMRI3" id="CAJMRI3" value="<?php echo $datosNovedad['Etario3_CAJMRI']; ?>" readonly style="text-align:center;"> </td>
											</tr>
										<?php } ?>

										<?php if($datosNovedad['CAJTRI'] > 0 ){ ?>
											<tr class="CAJTRI">
												<td> <input type="text" class="form-control" name="CAJTRInm" id="CAJTRInm" value="CAJTRI" readonly> </td>
												<td> <input type="text" class="form-control" name="CAJTRITotal" id="CAJTRITotal" value="<?php echo $datosNovedad['CAJTRI']; ?>" readonly style="text-align:center;"> </td>
												<td> <input type="text" class="form-control" name="CAJTRI1" id="CAJTRI1" value="<?php echo $datosNovedad['Etario1_CAJTRI']; ?>" readonly style="text-align:center;"> </td>
												<td> <input type="text" class="form-control" name="CAJTRI2" id="CAJTRI2" value="<?php echo $datosNovedad['Etario2_CAJTRI']; ?>" readonly style="text-align:center;"> </td>
												<td> <input type="text" class="form-control" name="CAJTRI3" id="CAJTRI3" value="<?php echo $datosNovedad['Etario3_CAJTRI']; ?>" readonly style="text-align:center;"> </td>
											</tr>
										<?php } ?>

										<?php if($datosNovedad['RPC'] > 0 ){ ?>
											<tr class="RPC">
												<td> <input type="text" class="form-control" name="RPCnm" id="RPCnm" value="RPC" readonly> </td>
												<td> <input type="text" class="form-control" name="RPCTotal" id="RPCTotal" value="<?php echo $datosNovedad['RPC']; ?>" readonly style="text-align:center;"> </td>
												<td> <input type="text" class="form-control" name="RPC1" id="RPC1" value="<?php echo $datosNovedad['Etario1_RPC']; ?>" readonly style="text-align:center;"> </td>
												<td> <input type="text" class="form-control" name="RPC2" id="RPC2" value="<?php echo $datosNovedad['Etario2_RPC']; ?>" readonly style="text-align:center;"> </td>
												<td> <input type="text" class="form-control" name="RPC3" id="RPC3" value="<?php echo $datosNovedad['Etario3_RPC']; ?>" readonly style="text-align:center;"> </td>
											</tr>
										<?php } ?>

										<tr class="total">
											<td>
												<input type="text" class="form-control" name="totalNm" id="totalNm" value="TOTAL" readonly>
											</td>
											<td>
												<input type="text" class="form-control" name="totalTotal" id="totalTotal" value="<?= $datosNovedad['APS'] + $datosNovedad['CAJMPS'] + $datosNovedad['CAJTPS'] + $datosNovedad['CAJMRI'] + $datosNovedad['CAJTRI'] + $datosNovedad['RPC']; ?>" readonly style="text-align:center;"> </td>
											<td> <input type="text" min="1" pattern="^[0-9]+" class="form-control" name="total1" id="total1" value="<?= $datosNovedad['Etario1_APS'] + $datosNovedad['Etario1_CAJMPS'] + $datosNovedad['Etario1_CAJTPS'] + $datosNovedad['Etario1_CAJMRI'] + $datosNovedad['Etario1_CAJTRI'] + $datosNovedad['Etario1_RPC']; ?>" readonly  style="text-align:center;"> </td>
											<td> <input type="text" min="1" pattern="^[0-9]+" class="form-control" name="total2" id="total2" value="<?= $datosNovedad['Etario2_APS'] + $datosNovedad['Etario2_CAJMPS'] + $datosNovedad['Etario2_CAJTPS'] + $datosNovedad['Etario2_CAJMPS'] + $datosNovedad['Etario2_CAJTPS'] + $datosNovedad['Etario2_RPC']; ?>" readonly  style="text-align:center;"> </td>
											<td> <input type="text" min="1" pattern="^[0-9]+" class="form-control" name="total3" id="total3" value="<?= $datosNovedad['Etario3_APS'] + $datosNovedad['Etario3_CAJMPS'] + $datosNovedad['Etario3_CAJTPS'] + $datosNovedad['Etario3_CAJMRI'] + $datosNovedad['Etario3_CAJTRI'] + $datosNovedad['Etario3_RPC']; ?>" readonly  style="text-align:center;"> </td>
										</tr>

									</tbody>
								</table>
							</div><!-- /.table-responsive -->
						</div><!-- /.ibox-content -->
					</div><!-- /.ibox float-e-margins -->

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
										//var_dump($ext);
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


				</form>




    	</div><!-- /.col-lg-12 -->
  	</div><!-- /.row -->
</div><!-- /.wrapper wrapper-content animated fadeInRight -->









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

<script src="<?php echo $baseUrl; ?>/modules/novedades_priorizacion/js/novedades_priorizacion_ver.js"></script>

<!-- Page-Level Scripts -->

<?php mysqli_close($Link); ?>
</body>
</html>
