<?php
	require_once '../../header.php';
	set_time_limit (0);
	ini_set('memory_limit','6000M');

	$titulo = "Suplentes";
	$periodoActual = $_SESSION['periodoActual'];
	$codigo_municipio = $_SESSION["p_Municipio"] ;
	$codigo_departamento = $_SESSION['p_CodDepartamento'];
	$meses = [ '01' => 'Enero', '02' => 'Febrero', '03' => 'Marzo', '04' => 'Abril', '05' => 'Mayo', '06' => 'Junio', '07' => 'Julio', '08' => 'Agosto', '09' => 'Septiembre', '10' => 'Octubre', '11' => 'Noviembre', '12' => 'Diciembre'];
?>

<div class="row wrapper wrapper-content border-bottom white-bg page-heading">
	<div class="col-lg-8">
    <h2><?= $titulo; ?></h2>
		<ol class="breadcrumb">
			<li>
			 	<a href="<?php echo $baseUrl; ?>">Inicio</a>
			</li>
			<li class="active">
		  	<strong><?= $titulo; ?></strong>
			</li>
		</ol>
	</div>
	<div class="col-lg-4">
    <div class="title-action">
	    <?php if ($_SESSION['perfil'] == 1 || $_SESSION['perfil'] == 0) { ?>
		    <div class="dropdown pull-right" id="">
		    	<button class="btn btn-primary btn-outline" type="button" id="accionesTabla" data-toggle="dropdown" aria-haspopup="true">
		    		Acciones <span class="caret"></span>
		    	</button>
		    	<ul class="dropdown-menu pull-right" aria-labelledby="accionesTabla">
		    		<li>
		    			<a href="nuevo_suplente.php"><span class="fa fa-plus"></span> Nuevo</a>
		    		</li>
		    		<li>
		    			<a class="subir_suplentes"><span class="fa fa-upload"></span> Importar</a>
		    		</li>
		    	</ul>
		    </div>
	    <?php } ?>
    </div>
	</div>
</div>

<div class="wrapper wrapper-content  animated fadeInRight">
  <div class="row">
    <div class="col-sm-12">
	    <div class="ibox">
        <div class="ibox-content">
					<form id="formulario_buscar_suplentes" method="post">
						<div class="row">
							<div class="col-sm-3 form-group">
								<label for="municipio">Municipio</label>
								<select name="municipio" id="municipio" class="form-control select2" required>
									<option value="">seleccione</option>
									<?php
										$consulta_municipios = "SELECT CodigoDANE AS id, Ciudad AS nombre FROM ubicacion WHERE CodigoDANE LIKE '$codigo_departamento%'";
										if ($codigo_municipio > 0) { $consulta_municipios .= " AND CodigoDANE = '$codigo_municipio'"; }
										$consulta_municipios .= " ORDER BY Ciudad";

										$respuesta_municipio = $Link->query($consulta_municipios) or die("Error al consultar los municipio: ". $Link->error);
										if ($respuesta_municipio->num_rows > 0)
										{
											while ($municipio = $respuesta_municipio->fetch_assoc())
											{
									?>
												<option value="<?= $municipio['id']; ?>" <?= (isset($_POST['municipio']) && $_POST['municipio'] == $municipio['id']) ? 'selected' : (($municipio['id'] == $codigo_municipio) ? 'selected': ''); ?>><?= $municipio['nombre']; ?></option>
									<?php
											}
										}
									?>
								</select>
							</div>

							<div class="col-sm-3">
								<label>Institución</label>
								<select name="institucion" id="institucion" class="form-control select2" required>
									<option value="">seleccione</option>
									<?php
										if ($codigo_municipio > 0)
										{
											$consulta_instituciones = "SELECT codigo_inst AS codigo, nom_inst AS nombre FROM instituciones WHERE cod_mun = '$codigo_municipio' ORDER BY nom_inst";
											$respuesta_instituciones = $Link->query($consulta_instituciones) or die("Error al consultar las instituciones: ". $Link->error);
											if ($respuesta_instituciones->num_rows > 0)
											{
												while ($institucion = $respuesta_instituciones->fetch_assoc())
												{
									?>
													<option value="<?= $institucion['codigo'] ?>" <?= (isset($_POST['institucion']) && $_POST['institucion'] == $institucion['codigo']) ? 'selected' : ''; ?>><?= $institucion['nombre']; ?></option>
									<?php
												}
											}
										}
									?>
								</select>
								<label for="institucion" class="error"></label>
							</div>

							<div class="col-sm-3">
								<label>Sede</label>
								<select name="sede" id="sede" class="form-control select2" required>
									<option value="">Seleccione...</option>
								</select>
								<label for="sede" class="error"></label>
							</div>

							<div class="col-sm-3 form-group">
								<label for="semana">Semana</label>
								<select class="form-control select2" name="semana" id="semana" required>
									<option value="">seleccione</option>
									<?php
										$consulta_semanas = "SELECT TABLE_NAME AS tabla
																				FROM INFORMATION_SCHEMA.TABLES
																				WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME LIKE 'suplentes%'";
										$resultado_semanas = $Link->query($consulta_semanas) or die ('Error al consultar planilla_semanas: '. $Link->error);
										if($resultado_semanas->num_rows > 0)
										{
											while($semana = $resultado_semanas->fetch_assoc())
											{
												$nombre_semana = str_replace('suplentes', '', $semana["tabla"]);
												echo ($nombre_semana);
									?>
												<option value="<?= $nombre_semana; ?>" <?php if(isset($_POST['semana']) && $_POST['semana'] == $nombre_semana){ echo " selected "; } ?>><?= $nombre_semana; ?></option>
									<?php
											}
										}
									?>
								</select>
								<label for="semana" class="error"></label>
							</div>
						</div>
						<div class="row">
							<div class="col-sm-3 form-group">
								<button type="button" class="btn btn-primary" id="boton_buscar_suplentes"> <i class="fa fa-search"></i> Buscar</button>
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="wrapper wrapper-content  animated fadeInRight" id="contenedor_listado" style="display: none">
  <div class="row">
    <div class="col-sm-12">
      <div class="ibox">
        <div class="ibox-content">
          <div class="row">
          	<div class="col-sm-12">
            	<div class="table-responsive">
                <table class="table table-striped table-hover" id="tabla_suplentes">
									<thead>
										<tr>
											<th>Num doc</th>
											<th>Tipo doc</th>
											<th>Nombre</th>
											<th>Género</th>
											<th>Grado</th>
											<th>Grupo</th>
											<th>Jornada</th>
											<th>Edad</th>
											<th>Acciones</th>
										</tr>
									</thead>
									<tbody>

									</tbody>
									<tfoot>
										<tr>
											<th>Num doc</th>
											<th>Tipo doc</th>
											<th>Nombre</th>
											<th>Genero</th>
											<th>Grado</th>
											<th>Grupo</th>
											<th>Jornada</th>
											<th>Edad</th>
											<th>Acciones</th>
										</tr>
									</tfoot>
                </table>
            	</div>
            </div>
          </div>
        </div>
      </div>
    </div>
	</div>
</div>

<div class="modal inmodal fade" id="ventana_subir_suplentes" tabindex="-1" role="dialog" style="display: none;" aria-hidden="true">
  <div class="modal-dialog modal-sm">
    <div class="modal-content">
      <div class="modal-header text-primary" style="padding: 15px;">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only">Close</span></button>
        <h3><i class="fa fa-plus-circle fa-lg" aria-hidden="true"></i> Subir suplentes </h3>
      </div>
      <div class="modal-body">
				<form action="" name="form_subir_suplentes" id="form_subir_suplentes">
          <div class="row">
            <div class="col-sm-12">
              <div class="form-group">
                <label for="mes">Mes</label>
                <select class="form-control" name="mes" id="mes" required>
                  <option value="">Selección</option>
                  <?php
                    $consulta_meses = "SELECT DISTINCT MES AS mes FROM planilla_semanas;";
                    $respuesta_meses = $Link->query($consulta_meses) or die('Error al consultar los meses: '. $Link->error);
                    if($respuesta_meses->num_rows > 0){
                      while($mes = $respuesta_meses->fetch_assoc()) {
                  ?>
                      <option value="<?= $mes["mes"]; ?>"><?= $meses[$mes["mes"]]; ?></option>
                  <?php
                      }
                    }
                  ?>
                </select>
              </div>
            </div>
            <div class="col-sm-12">
              <div class="form-group">
                <label for="mes">Semana</label>
                <select class="form-control" name="semana" id="semana_modal" required>
                  <option value="">Selección</option>
                </select>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-sm-12">
              <div class="form-group">
                <label for="archivoPriorizacion">Archivo</label>
                <div class="fileinput fileinput-new input-group" data-provides="fileinput">
                  <div class="form-control" data-trigger="fileinput">
                    <i class="glyphicon glyphicon-file fileinput-exists"></i> <span class="fileinput-filename"></span>
                  </div>
                  <span class="input-group-addon btn btn-default btn-file"><span class="fileinput-new">seleccione</span><span class="fileinput-exists">Cambiar</span>
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
        <button type="button" class="btn btn-default btn-outline btn-sm" data-dismiss="modal">Cancelar</button>
        <button type="button" class="btn btn-primary btn-sm boton_subir_suplentes">Aceptar</button>
      </div>
    </div>
  </div>
</div>

<?php include '../../footer.php'; ?>

<!-- Mainly scripts -->
<script src="<?= $baseUrl; ?>/theme/js/jquery-3.1.1.min.js"></script>
<script src="<?= $baseUrl; ?>/theme/js/bootstrap.min.js"></script>
<script src="<?= $baseUrl; ?>/theme/js/plugins/metisMenu/jquery.metisMenu.js"></script>
<script src="<?= $baseUrl; ?>/theme/js/plugins/slimscroll/jquery.slimscroll.min.js"></script>
<script src="<?= $baseUrl; ?>/theme/js/plugins/dataTables/datatables.min.js"></script>

<!-- Custom and plugin javascript -->
<script src="<?= $baseUrl; ?>/theme/js/inspinia.js"></script>
<script src="<?= $baseUrl; ?>/theme/js/plugins/pace/pace.min.js"></script>
<script src="<?= $baseUrl; ?>/theme/js/plugins/toastr/toastr.min.js"></script>
<script src="<?= $baseUrl; ?>/theme/js/plugins/validate/jquery.validate.min.js"></script>
<script src="<?= $baseUrl; ?>/theme/js/plugins/iCheck/icheck.min.js"></script>
<script src="<?= $baseUrl; ?>/theme/js/plugins/steps/jquery.steps.min.js"></script>
<script src="<?= $baseUrl; ?>/theme/js/plugins/toggle/toggle.min.js"></script>
<script src="<?= $baseUrl; ?>/theme/js/plugins/select2/select2.full.min.js"></script>
<!-- Section Scripts -->

<script src="<?= $baseUrl; ?>/modules/suplentes/js/suplentes.js"></script>

<!-- Page-Level Scripts -->
<script>
		$(document).ready(function() {
			// $(document).on('click', '.subir_suplentes', function() { $('#ventana_subir_suplentes').modal(); });

	  // 	dataset1 = $('#tablaSuplentes').DataTable({
	  //   	pageLength: 25,
	  //   	responsive: true,
	  //   	dom : '<"html5buttons" B>lr<"containerBtn"><"inputFiltro"f>tip',
	  //   	buttons : [{extend:'excel', title:'Suplentes', className:'btnExportarExcel', exportOptions: {columns : [0,1,2,3,4,5,6,7]}}],
	  //   	oLanguage: {
	  //     		sLengthMenu: 'Mostrando _MENU_ registros por página',
	  //     		sZeroRecords: 'No se encontraron registros',
	  //     		sInfo: 'Mostrando _START_ a _END_ de _TOTAL_ registros',
	  //     		sInfoEmpty: 'Mostrando 0 a 0 de 0 registros',
	  //     		sInfoFiltered: '(Filtrado desde _MAX_ registros)',
	  //     		sSearch:         'Buscar: ',
	  //     		oPaginate:{
	  //       		sFirst:    'Primero',
	  //       		sLast:     'Último',
	  //       		sNext:     'Siguiente',
	  //       		sPrevious: 'Anterior'
	  //     		}
	  //   	}
	  //   }).on("draw", function(){jQuery('.estadoEst').bootstrapToggle();});

	  //  	var btnAcciones = '<div class="dropdown pull-right" id=""><button class="btn btn-primary btn-sm btn-outline" type="button" id="accionesTabla" data-toggle="dropdown" aria-haspopup="true">Acciones<span class="caret"></span></button><ul class="dropdown-menu pull-right" aria-labelledby="accionesTabla"><li><a onclick="$(\'.btnExportarExcel\').click()"><span class="fa fa-file-excel-o"></span> Exportar </a></li><li><a class="subir_suplentes"><span class="fa fa-upload"></span> Importar</a></li></ul></div>';

	  // 	$('.containerBtn').html(btnAcciones);
		});
</script>

<form id="editar_suplente" action="editar_suplente.php" method="post">
	<input type="hidden" name="numDoc" id="numDoc">
</form>

<form action="ver_suplente.php" method="post" name="verSuplente" id="verSuplente">
  	<input type="hidden" name="numDoc" id="numDoc">
  	<input type="hidden" name="tipoDoc" id="tipoDoc">
</form>

<?php mysqli_close($Link); ?>
</body>
</html>
