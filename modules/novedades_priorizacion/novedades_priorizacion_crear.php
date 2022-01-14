<?php
	include '../../header.php';

	if ($permisos['novedades'] == "0") {
    	?><script type="text/javascript">
      		window.open('<?= $baseUrl ?>', '_self');
    	</script>
  	<?php exit();}

	require_once '../../db/conexion.php';
	set_time_limit (0);
	ini_set('memory_limit','6000M');
	$periodoActual = $_SESSION['periodoActual'];

	$dato_municipio = $Link->query("SELECT CodMunicipio FROM parametros") or die(mysqli_error($Link));
	if ($dato_municipio->num_rows > 0) { $municipio_defecto = $dato_municipio->fetch_array(); }
?>

<?php if ($_SESSION['perfil'] == "0" || $permisos['novedades'] == "2"): ?>

<div class="row wrapper wrapper-content border-bottom white-bg page-heading">
    <div class="col-lg-8">
	    <h2>Nueva Novedad de Priorización</h2>
		<div class="debug"></div>
        <ol class="breadcrumb">
            <li>
                <a href="<?php echo $baseUrl; ?>">Inicio</a>
            </li>
            <li>
                <a href="<?php echo $baseUrl; ?>/modules/novedades_priorizacion">Novedades de Priorización</a>
            </li>
            <li class="active">
                <strong>Novedad en Priorización crear</strong>
            </li>
        </ol>
    </div>
    <div class="col-lg-4">
        <div class="title-action">
        	<a href="#" target="_self" class="btn btn-primary guaradarNovedad"><i class="fa fa-check"></i> Guardar</a>
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
								<select class="form-control" name="municipio" id="municipio">
									<option value="">Seleccione uno</option>
								</select>
							</div>
							<div class="col-sm-4 form-group">
								<label for="institucion">Institución</label>
								<select class="form-control" name="institucion" id="institucion">
									<option value="">Seleccione una</option>
									<?php
										$consulta = "SELECT
														DISTINCT s.cod_inst,
														s.nom_inst
													FROM
														sedes$periodoActual s LEFT JOIN sedes_cobertura sc ON s.cod_sede = sc.cod_sede
													WHERE
														s.cod_mun_sede = '". $municipio_defecto["CodMunicipio"] ."'
													ORDER BY s.nom_inst";
										if($tipo != ''){
											$consulta = $consulta." and sc.$tipo > 0 ";
										}
										$resultado = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));
										if($resultado->num_rows >= 1){
											while($row = $resultado->fetch_assoc()) {
									?>
									<option value="<?php echo $row['cod_inst']; ?>" <?php if ($municipio_defecto["CodMunicipio"] == $row["cod_inst"]) { echo "selected"; } ?>><?php echo $row['nom_inst']; ?></option>
									<?php
											}
										}
									?>
								</select>
							</div>
							<div class="col-sm-4 form-group">
								<label for="sede">Sede</label>
								<select class="form-control" name="sede" id="sede">
									<option value="">Selecciones una</option>
								</select>
							</div>
							<div class="col-sm-4 form-group">
								<label for="mes">Mes</label>
								<select class="form-control" name="mes" id="mes">
									<option value="">Seleccione uno</option>
								</select>
							</div>
							<div class="col-sm-4 form-group">
								<label for="semana">Semana</label>
								<div id="semana">

								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-sm-4 form-group">
								<button class="btn btn-primary" type="button" id="btnBuscar" name="btnBuscar" value="1"><strong> <i class="fa fa-search"></i> Buscar</strong></button>
							</div>
						</div>
	        		</div>
				</div>

				<div class="ibox float-e-margins priorizacionAction">
					<div class="ibox-content contentBackground">
						<div class="table-responsive">
								<table width="100%" class="table table-striped table-bordered table-hover selectableRows">
									<thead>
										<tr>
											<th>Complemento Actual</th>
											<th>Cant Total Actual</th>
											<th>Grupo Etario 1</th>
											<th>Grupo Etario 2</th>
											<th>Grupo Etario 3</th>
										</tr>
									</thead>
									<tbody>
										<tr class="APSactual">
											<td> <input type="text" class="form-control" name="APSnm" id="APSnm" value="APS" readonly> </td>
											<td> <input type="text" class="form-control" name="APSactualTotal" id="APSactualTotal" value="" readonly style="text-align:center;"> </td>
											<td> <input type="text" class="form-control" name="APSactual1" id="APSactual1" value="" readonly style="text-align:center;"> </td>
											<td> <input type="text" class="form-control" name="APSactual2" id="APSactual2" value="" readonly style="text-align:center;"> </td>
											<td> <input type="text" class="form-control" name="APSactual3" id="APSactual3" value="" readonly style="text-align:center;"> </td>
										</tr>
										<tr class="CAJMRIactual">
											<td> <input type="text" class="form-control" name="CAJMRInm" id="CAJMRInm" value="CAJMRI" readonly> </td>
											<td> <input type="text" class="form-control" name="CAJMRIactualTotal" id="CAJMRIactualTotal" value="" readonly style="text-align:center;"> </td>
											<td> <input type="text" class="form-control" name="CAJMRIactual1" id="CAJMRIactual1" value="" readonly style="text-align:center;"> </td>
											<td> <input type="text" class="form-control" name="CAJMRIactual2" id="CAJMRIactual2" value="" readonly style="text-align:center;"> </td>
											<td> <input type="text" class="form-control" name="CAJMRIactual3" id="CAJMRIactual3" value="" readonly style="text-align:center;"> </td>
										</tr>
										<tr class="CAJTRIactual">
											<td> <input type="text" class="form-control" name="CAJTRInm" id="CAJTRInm" value="CAJTRI" readonly> </td>
											<td> <input type="text" class="form-control" name="CAJTRIactualTotal" id="CAJTRIactualTotal" value="" readonly style="text-align:center;"> </td>
											<td> <input type="text" class="form-control" name="CAJTRIactual1" id="CAJTRIactual1" value="" readonly style="text-align:center;"> </td>
											<td> <input type="text" class="form-control" name="CAJTRIactual2" id="CAJTRIactual2" value="" readonly style="text-align:center;"> </td>
											<td> <input type="text" class="form-control" name="CAJTRIactual3" id="CAJTRIactual3" value="" readonly style="text-align:center;"> </td>
										</tr>
										<tr class="CAJMPSactual">
											<td> <input type="text" class="form-control" name="CAJMPSnm" id="CAJMPSnm" value="CAJMPS" readonly> </td>
											<td> <input type="text" class="form-control" name="CAJMPSactualTotal" id="CAJMPSactualTotal" value="" readonly style="text-align:center;"> </td>
											<td> <input type="text" class="form-control" name="CAJMPSactual1" id="CAJMPSactual1" value="" readonly style="text-align:center;"> </td>
											<td> <input type="text" class="form-control" name="CAJMPSactual2" id="CAJMPSactual2" value="" readonly style="text-align:center;"> </td>
											<td> <input type="text" class="form-control" name="CAJMPSactual3" id="CAJMPSactual3" value="" readonly style="text-align:center;"> </td>
										</tr>
										<tr class="CAJTPSactual">
											<td> <input type="text" class="form-control" name="CAJTPSnm" id="CAJTPSnm" value="CAJTPS" readonly> </td>
											<td> <input type="text" class="form-control" name="CAJTPSactualTotal" id="CAJTPSactualTotal" value="" readonly style="text-align:center;"> </td>
											<td> <input type="text" class="form-control" name="CAJTPSactual1" id="CAJTPSactual1" value="" readonly style="text-align:center;"> </td>
											<td> <input type="text" class="form-control" name="CAJTPSactual2" id="CAJTPSactual2" value="" readonly style="text-align:center;"> </td>
											<td> <input type="text" class="form-control" name="CAJTPSactual3" id="CAJTPSactual3" value="" readonly style="text-align:center;"> </td>
										</tr>
										<tr class="RPCactual">
											<td> <input type="text" class="form-control" name="RPCnm" id="RPCnm" value="RPC" readonly> </td>
											<td> <input type="text" class="form-control" name="RPCactualTotal" id="RPCactualTotal" value="" readonly style="text-align:center;"> </td>
											<td> <input type="text" class="form-control" name="RPCactual1" id="RPCactual1" value="" readonly style="text-align:center;"> </td>
											<td> <input type="text" class="form-control" name="RPCactual2" id="RPCactual2" value="" readonly style="text-align:center;"> </td>
											<td> <input type="text" class="form-control" name="RPCactual3" id="RPCactual3" value="" readonly style="text-align:center;"> </td>
										</tr>
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
										<tr class="APS">
											<td> <input type="text" class="form-control" name="APSnm" id="APSnm" value="APS" readonly> </td>
											<td> <input type="text" class="form-control" name="APSTotal" id="APSTotal" value="0" readonly style="text-align:center;"> </td>
											<td> <input type="number" min="1" pattern="^[0-9]+" class="form-control" name="APS1" id="APS1" value="0"  style="text-align:center;"> </td>
											<td> <input type="number" min="1" pattern="^[0-9]+" class="form-control" name="APS2" id="APS2" value="0"  style="text-align:center;"> </td>
											<td> <input type="number" min="1" pattern="^[0-9]+" class="form-control" name="APS3" id="APS3" value="0"  style="text-align:center;"> </td>
										</tr>
										<tr class="CAJMRI">
											<td> <input type="text" class="form-control" name="CAJMRInm" id="CAJMRInm" value="CAJMRI" readonly> </td>
											<td> <input type="text" class="form-control" name="CAJMRITotal" id="CAJMRITotal" value="0" readonly style="text-align:center;"> </td>
											<td> <input type="number" min="1" pattern="^[0-9]+" class="form-control" name="CAJMRI1" id="CAJMRI1" value="0"  style="text-align:center;"> </td>
											<td> <input type="number" min="1" pattern="^[0-9]+" class="form-control" name="CAJMRI2" id="CAJMRI2" value="0"  style="text-align:center;"> </td>
											<td> <input type="number" min="1" pattern="^[0-9]+" class="form-control" name="CAJMRI3" id="CAJMRI3" value="0"  style="text-align:center;"> </td>
										</tr>
										<tr class="CAJTRI">
											<td> <input type="text" class="form-control" name="CAJTRInm" id="CAJTRInm" value="CAJTRI" readonly> </td>
											<td> <input type="text" class="form-control" name="CAJTRITotal" id="CAJTRITotal" value="0" readonly style="text-align:center;"> </td>
											<td> <input type="number" min="1" pattern="^[0-9]+" class="form-control" name="CAJTRI1" id="CAJTRI1" value="0"  style="text-align:center;"> </td>
											<td> <input type="number" min="1" pattern="^[0-9]+" class="form-control" name="CAJTRI2" id="CAJTRI2" value="0"  style="text-align:center;"> </td>
											<td> <input type="number" min="1" pattern="^[0-9]+" class="form-control" name="CAJTRI3" id="CAJTRI3" value="0"  style="text-align:center;"> </td>
										</tr>
										<tr class="CAJMPS">
											<td> <input type="text" class="form-control" name="CAJMPSnm" id="CAJMPSnm" value="CAJMPS" readonly> </td>
											<td> <input type="text" class="form-control" name="CAJMPSTotal" id="CAJMPSTotal" value="0" readonly style="text-align:center;"> </td>
											<td> <input type="number" min="1" pattern="^[0-9]+" class="form-control" name="CAJMPS1" id="CAJMPS1" value="0"  style="text-align:center;"> </td>
											<td> <input type="number" min="1" pattern="^[0-9]+" class="form-control" name="CAJMPS2" id="CAJMPS2" value="0"  style="text-align:center;"> </td>
											<td> <input type="number" min="1" pattern="^[0-9]+" class="form-control" name="CAJMPS3" id="CAJMPS3" value="0"  style="text-align:center;"> </td>
										</tr>
										<tr class="CAJTPS">
											<td> <input type="text" class="form-control" name="CAJTPSnm" id="CAJTPSnm" value="CAJTPS" readonly> </td>
											<td> <input type="text" class="form-control" name="CAJTPSTotal" id="CAJTPSTotal" value="0" readonly style="text-align:center;"> </td>
											<td> <input type="number" min="1" pattern="^[0-9]+" class="form-control" name="CAJTPS1" id="CAJTPS1" value="0"  style="text-align:center;"> </td>
											<td> <input type="number" min="1" pattern="^[0-9]+" class="form-control" name="CAJTPS2" id="CAJTPS2" value="0"  style="text-align:center;"> </td>
											<td> <input type="number" min="1" pattern="^[0-9]+" class="form-control" name="CAJTPS3" id="CAJTPS3" value="0"  style="text-align:center;"> </td>
										</tr>
										<tr class="RPC">
											<td> <input type="text" class="form-control" name="RPCnm" id="RPCnm" value="RPC" readonly> </td>
											<td> <input type="text" class="form-control" name="RPCTotal" id="RPCTotal" value="0" readonly style="text-align:center;"> </td>
											<td> <input type="number" min="1" pattern="^[0-9]+" class="form-control" name="RPC1" id="RPC1" value="0"  style="text-align:center;"> </td>
											<td> <input type="number" min="1" pattern="^[0-9]+" class="form-control" name="RPC2" id="RPC2" value="0"  style="text-align:center;"> </td>
											<td> <input type="number" min="1" pattern="^[0-9]+" class="form-control" name="RPC3" id="RPC3" value="0"  style="text-align:center;"> </td>
										</tr>
										<tr class="total">
											<td> <input type="text" class="form-control" name="totalNm" id="totalNm" value="TOTAL" readonly> </td>
											<td> <input type="text" class="form-control" name="totalTotal" id="totalTotal" value="0" readonly style="text-align:center;"> </td>
											<td> <input type="number" min="1" pattern="^[0-9]+" class="form-control" name="total1" id="total1" value="0" readonly  style="text-align:center;"> </td>
											<td> <input type="number" min="1" pattern="^[0-9]+" class="form-control" name="total2" id="total2" value="0" readonly  style="text-align:center;"> </td>
											<td> <input type="number" min="1" pattern="^[0-9]+" class="form-control" name="total3" id="total3" value="0" readonly  style="text-align:center;"> </td>
										</tr>
									</tbody>
								</table>
							</div><!-- /.table-responsive -->
					</div><!-- /.ibox-content -->
				</div><!-- /.ibox float-e-margins -->

				<div class="ibox float-e-margins priorizacionAction">
					<div class="ibox-content contentBackground">
						<div class="row">
							<div class="col-sm-12 form-group">
								<label for="observaciones">Observaciones</label>
								<textarea name="observaciones" id="observaciones" class="form-control" rows="8" cols="80"></textarea>
							</div><!-- /.col -->
						</div><!-- -/.row -->
					</div><!-- /.ibox-content -->
				</div><!-- /.ibox float-e-margins -->

				<div class="wrapper wrapper-content priorizacionAction">
					<div class="row">
						<div class="col-lg-12">
							<div class="ibox float-e-margins">
								<div class="ibox-title">
									<h5>Adjuntar Archivo</h5>
								</div>
								<div class="ibox-content">
									<div class="row" name="subirArchivos">
										<div class="col-sm-12 form-group">
											<label for="departamento">Archivo</label>
											<div class="fileinput fileinput-new input-group" data-provides="fileinput"> <div class="form-control" data-trigger="fileinput"><i class="glyphicon glyphicon-file fileinput-exists"></i> <span class="fileinput-filename"></span></div> <span class="input-group-addon btn btn-default btn-file"><span class="fileinput-new">Elegir archivo</span><span class="fileinput-exists">Change</span><input type="file" name="foto[]" id="foto" accept="image/jpeg,image/gif,image/png,application/pdf"></span> <a href="#" class="input-group-addon btn btn-default fileinput-exists" data-dismiss="fileinput">Remover</a> </div>
										</div><!-- /.col -->
									</div>
									<div class="row">
										<div class="col-sm-3 form-group">
											<button type="button" class="btn btn-primary guaradarNovedad"><i class="fa fa-check"></i> Guardar </button>
										</div><!-- /.col -->
									</div><!-- /.row -->
								</div>
							</div>
						</div>
					</div>
				</div>
			</form>
    	</div><!-- /.col-lg-12 -->
  	</div><!-- /.row -->
</div><!-- /.wrapper wrapper-content animated fadeInRight -->
<?php else: ?>
	<script type="text/javascript">
      	window.open('<?= $baseUrl ?>', '_self');
    </script>
<?php endif ?>
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
<script src="<?php echo $baseUrl; ?>/theme/js/plugins/iCheck/icheck.min.js"></script>
<script src="<?php echo $baseUrl; ?>/modules/instituciones/js/sede_archivos.js"></script>
<script src="<?php echo $baseUrl; ?>/modules/novedades_priorizacion/js/novedades_priorizacion_crear.js"></script>
<script src="<?php echo $baseUrl; ?>/theme/js/plugins/toastr/toastr.min.js"></script>
<!-- Page-Level Scripts -->

<?php mysqli_close($Link); ?>
</body>
</html>
