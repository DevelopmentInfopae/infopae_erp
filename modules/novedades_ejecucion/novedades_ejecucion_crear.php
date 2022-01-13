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

	$titulo = "Nueva novedad de focalización";
?>
	<?php if ($_SESSION['perfil'] == "0" || $permisos['novedades'] == "2"): ?>

	<link rel="stylesheet" href="css/custom.css">
	<div class="flagFaltantes"><span id="complementos_faltantes">0</span> de <span id="total_priorizacion">0</span> </div>

	<div class="row wrapper wrapper-content border-bottom white-bg page-heading">
		<div class="col-lg-8">
			<h2><?= $titulo; ?></h2>
			<div class="debug"></div>
			<ol class="breadcrumb">
				<li> <a href="<?php echo $baseUrl; ?>">Inicio</a> </li>
				<li> <a href="<?php echo $baseUrl; ?>/modules/novedades_ejecucion">Novedades de focalización</a> </li>
				<li class="active"> <strong><?= $titulo; ?></strong> </li>
			</ol>
		</div>
		<div class="col-lg-4">
			<div class="title-action">
				<a href="#" target="_self" class="btn btn-primary disabled guaradarNovedad" id="boton_guardar_novedades"><i class="fa fa-check"></i> Guardar</a>
			</div>
		</div>
	</div>

	<div class="wrapper wrapper-content animated fadeInRight">
		<div class="row">
			<div class="ibox float-e-margins">
        <div class="ibox-content contentBackground">
					<form class="col-lg-12" action="" method="post" name="formulario_buscar_focalizacion" id="formulario_buscar_focalizacion" enctype="multipart/form-data">
	          <div class="row">
	            <div class="col-sm-4 form-group">
	              <label for="municipio">Municipio</label>
	              <select class="form-control" name="municipio" id="municipio" required>
	                <option value="">seleccione</option>
	                <?php
	                	$codigo_departamento = $_SESSION['p_CodDepartamento'];
	                	$consulta_municipio = "SELECT u.Ciudad, u.CodigoDANE FROM ubicacion u WHERE u.ETC = 0 AND CodigoDANE LIKE '$codigo_departamento%' ORDER BY u.Ciudad ASC";
	                	$resultado_municipio = $Link->query($consulta_municipio) or die('Error al consultar los municipios:'. $Link->error);
	                	if($resultado_municipio->num_rows > 0)
										{
											while($municipio = $resultado_municipio->fetch_assoc())
											{
												$selected = (isset($_SESSION["p_Municipio"]) && $_SESSION["p_Municipio"] == $municipio['CodigoDANE']) ? "selected" : "";
									?>
												<option value="<?= $municipio['CodigoDANE'] ?>" <?= $selected; ?>><?= $municipio['Ciudad']; ?></option>
									<?php
											}
										}
	                ?>
	              </select>
	              <label for="municipio" class="error" style="display: none;"></label>
	            </div>

	            <div class="col-sm-4 form-group">
	              <label for="institucion">Institución</label>
	              <select class="form-control" name="institucion" id="institucion" required>
	                <option value="">seleccione</option>
	                <?php
	                	$parametro_municipio = (isset($_SESSION["p_Municipio"]) && $_SESSION["p_Municipio"] > 0) ? " AND cod_mun = '". $_SESSION['p_Municipio'] ."'" : "";
                		$consulta = "SELECT i.codigo_inst, i.nom_inst FROM instituciones i WHERE 1 = 1 $parametro_municipio ORDER BY i.nom_inst";
										$resultado = $Link->query($consulta) or die ($Link->error);
										if($resultado->num_rows > 0)
										{
											while($row = $resultado->fetch_assoc())
											{
									?>
											<option value="<?= $row['codigo_inst']; ?>"><?= $row['nom_inst']; ?></option>
									<?php
											}
										}
	                ?>
	              </select>
	              <label for="institucion" class="error" style="display: none;"></label>
	            </div>

	            <div class="col-sm-4 form-group">
	              <label for="sede">Sede</label>
	              <select class="form-control" name="sede" id="sede" required>
	                <option value="">seleccione</option>
	              </select>
	              <label for="sede" class="error" style="display: none;"></label>
	            </div>

	            <div class="col-sm-4 form-group">
	              <label for="mes">Mes</label>
	              <select class="form-control" name="mes" id="mes" required>
	                <option value="">seleccione</option>
	              </select>
	              <label for="mes" class="error" style="display: none;"></label>
	            </div>

	            <div class="col-sm-4 form-group">
	              <label for="semana">Semana</label>
	              <select class="form-control" name="semana" id="semana" required>
	                <option value="">seleccione</option>
	              </select>
	              <div id="semana"> </div>
	              <label for="semana" class="error" style="display: none;"></label>
	            </div>

	            <div class="col-sm-4 form-group">
	              <label for="tipoComplemento">Tipo complemento</label>
	              <select class="form-control" name="tipoComplemento" id="tipoComplemento" required>
	                <option value="">seleccione</option>
	              </select>
	              <label for="tipoComplemento" class="error" style="display: none;"></label>
	            </div>
	          </div>

	          <div class="row">
	            <div class="col-sm-12 form-group">
	              <button class="btn btn-primary pull-right" type="button" id="btnBuscar" name="btnBuscar" value="1"><i class="fa fa-search"></i>  Buscar</button>
	            </div>
	          </div>
					</form>
					<!--  -->
        </div>
      </div>

			<form id="formulario_guardar_novedades_focalizacion">
      <div class="ibox float-e-margins" id="contenedor_tabla_focalizados" style="display: none;">
      	<div class="ibox-title">
      		<h3>Focalizados</h3>
      	</div>
        <div class="ibox-content contentBackground">
					<div class="table-responsive">
						<table class="table table-striped table-hover selectableRows tabla_focalizacion">
							<thead>
								<tr>

								</tr>
							</thead>
							<tbody>
							</tbody>
							<tfoot>
								<tr>

								</tr>
							</tfoot>
						</table>
						<input type="hidden" id="cantidad_columnas_tabla">
					</div>
        </div>
      </div>

			<div class="ibox float-e-margins">
				<div class="ibox-title">
					<h5>Datos adicionales</h5>
				</div>
				<div class="ibox-content">
					<div class="row" name="subirArchivos">
						<div class="col-sm-12 form-group">
							<label for="observaciones">Observaciones</label>
							<textarea name="observaciones" id="observaciones" class="form-control" rows="8" cols="80"></textarea>
						</div>
						<div class="col-sm-12 form-group">
							<label for="departamento">Archivo</label>
							<div class="fileinput fileinput-new input-group" data-provides="fileinput">
								<div class="form-control" data-trigger="fileinput">
									<i class="glyphicon glyphicon-file fileinput-exists"></i>
									<span class="fileinput-filename"></span>
								</div>
								<span class="input-group-addon btn btn-default btn-file">
									<span class="fileinput-new">Elegir archivo</span>
									<span class="fileinput-exists">Change</span>
									<input type="file" name="foto[]" id="foto" accept="image/jpeg,image/gif,image/png,application/pdf">
								</span>
								<a href="#" class="input-group-addon btn btn-default fileinput-exists" data-dismiss="fileinput">Remover</a>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-sm-3 form-group">
							<input type="hidden" name="mes_hidden" id="mes_hidden">
							<input type="hidden" name="sede_hidden" id="sede_hidden">
							<input type="hidden" name="semana_hidden" id="semana_hidden">
							<input type="hidden" name="municipio_hidden" id="municipio_hidden">
							<input type="hidden" name="institucion_hidden" id="institucion_hidden">
							<input type="hidden" name="tipoComplemento_hidden" id="tipoComplemento_hidden">
							<button type="button" class="btn btn-primary guaradarNovedad"><i class="fa fa-check"></i> Guardar </button>
						</div>
					</div>
				</div>
			</div>
			</form>
  	</div>
	</div>
	<?php else: ?>
		<script type="text/javascript">
      		window.open('<?= $baseUrl ?>', '_self');
    	</script>	
	<?php endif ?>

	<?php include '../../footer.php'; ?>

	<!-- Mainly scripts -->
	<script src="<?php echo $baseUrl; ?>/theme/js/jquery-3.1.1.min.js"></script>
	<script src="<?php echo $baseUrl; ?>/theme/js/bootstrap.min.js"></script>
	<script src="<?php echo $baseUrl; ?>/theme/js/plugins/metisMenu/jquery.metisMenu.js"></script>
	<script src="<?php echo $baseUrl; ?>/theme/js/plugins/slimscroll/jquery.slimscroll.min.js"></script>

	<!-- Custom and plugin javascript -->
	<script src="<?php echo $baseUrl; ?>/theme/js/inspinia.js"></script>
	<script src="<?php echo $baseUrl; ?>/theme/js/plugins/pace/pace.min.js"></script>
	<script src="<?php echo $baseUrl; ?>/theme/js/plugins/validate/jquery.validate.min.js"></script>
	<script src="<?php echo $baseUrl; ?>/theme/js/plugins/jasny/jasny-bootstrap.min.js"></script>
	<script src="<?php echo $baseUrl; ?>/theme/js/plugins/dataTables/datatables.min.js"></script>
	<script src="<?php echo $baseUrl; ?>/theme/js/plugins/toastr/toastr.min.js"></script>
	<script src="<?php echo $baseUrl; ?>/theme/js/plugins/iCheck/icheck.min.js"></script>
	<script src="<?php echo $baseUrl; ?>/theme/js/plugins/select2/select2.full.min.js"></script>
	<script src="<?php echo $baseUrl; ?>/modules/novedades_ejecucion/js/novedades_ejecucion_crear.js"></script>

	<?php mysqli_close($Link); ?>
</body>
</html>
