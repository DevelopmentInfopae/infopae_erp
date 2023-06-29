<?php
	require_once '../../header.php';
	set_time_limit (0);
	ini_set('memory_limit','6000M');

	if ($permisos['titulares_derecho'] == "0") {
	    ?><script type="text/javascript">
	      window.open('<?= $baseUrl ?>', '_self');
	    </script>
	<?php exit(); }
	else {
		?><script type="text/javascript">
		  const list = document.querySelector(".li_titulares_derecho");
		  list.className += " active ";
		  const list2 = document.querySelector(".li_suplentes");
		  list2.className += " active ";
		</script>
	  <?php
	  }

	$titulo = "Suplentes";
	$periodoActual = $_SESSION['periodoActual'];
	$codigo_municipio = $_SESSION["p_Municipio"] ;
	$codigo_departamento = $_SESSION['p_CodDepartamento'];
	$meses = [ '01' => 'Enero', '02' => 'Febrero', '03' => 'Marzo', '04' => 'Abril', '05' => 'Mayo', '06' => 'Junio', '07' => 'Julio', '08' => 'Agosto', '09' => 'Septiembre', '10' => 'Octubre', '11' => 'Noviembre', '12' => 'Diciembre'];

	$municipio = (isset($_POST['municipio']) && $_POST['municipio'] != '') ? mysqli_real_escape_string($Link, $_POST['municipio']) : '';
	$institucion = (isset($_POST['institucion']) && $_POST['institucion'] != '') ? mysqli_real_escape_string($Link, $_POST['institucion']) : '';
	$sede = (isset($_POST['sede']) && $_POST['sede'] != '') ? mysqli_real_escape_string($Link, $_POST['sede']) : '';
	$semanaPos = (isset($_POST['semana']) && $_POST['semana'] != '') ? mysqli_real_escape_string($Link, $_POST['semana']) : '';
	
// exit(var_dump());
	if (isset($_POST) && !empty($_POST)) {
		$suplentes = [];
		$consultaSuplentes = "SELECT 
							s.id,
							s.num_doc AS documento,
							td.abreviatura AS tipoDocumento,
							CONCAT(s.nom1, ' ', s.nom2, ' ', s.ape1, ' ', s.ape2) AS nombre,
							s.genero,
							s.cod_grado AS grado,
							s.nom_grupo AS grupo,
							j.nombre as jornada,
							s.edad
						FROM suplentes$semanaPos s
						LEFT JOIN tipodocumento td ON s.tipo_doc = td.id 
						LEFT JOIN jornada j ON s.cod_jorn_est = j.id
						WHERE s.cod_inst = $institucion AND s.cod_sede = $sede;	
		";
		// exit(var_dump($consultaSuplentes));
		$respuestaSuplentes = $Link->query($consultaSuplentes) or die ('Error al consultar los suplentes ' . mysqli_error($Link));
		if ($respuestaSuplentes->num_rows > 0) {
			while ($dataSuplentes = $respuestaSuplentes->fetch_assoc()) {
				$suplentes[] = $dataSuplentes;
			}
		}
	}

	$nameLabel = get_titles('titulares', 'suplentes', $labels);

?>
<div class="row wrapper wrapper-content border-bottom white-bg page-heading">
	<div class="col-lg-8">
    	<h2><?= $nameLabel; ?></h2>
		<ol class="breadcrumb">
			<li>
			 	<a href="<?php echo $baseUrl; ?>">Inicio</a>
			</li>
			<li class="active">
		  		<strong><?= $nameLabel; ?></strong>
			</li>
		</ol>
	</div>
	<div class="col-lg-4">
    	<div class="title-action">
	    <?php 
	    	if ($_SESSION['perfil'] == "0" || $permisos['titulares_derecho'] == "2") { ?>
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
					<form action="" method="post">
						<div class="row">
							<div class="col-sm-3 form-group">
								<label for="municipio">Municipio</label>
								<select name="municipio" id="municipio" class="form-control select2" required>
									<option value="">seleccione</option>
									<?php
										$condicionMunicipioRector = '';
										$condicionMunicipioCoordinador = '';
										if ($_SESSION['perfil'] == "6") {
											$documentoRector = $_SESSION['num_doc'];
											$consultaMunicipioInstitucion = "SELECT cod_mun FROM instituciones WHERE cc_rector = $documentoRector";
											$respuestaMunicipioInstitucion = $Link->query($consultaMunicipioInstitucion) or die ('Error al consultar el municipio de la institución. ' .mysqli_error($Link));
											if ($respuestaMunicipioInstitucion->num_rows > 0) {
												$dataMunicipioInstitucion = $respuestaMunicipioInstitucion->fetch_assoc();
												$codigoMunicipio = $dataMunicipioInstitucion['cod_mun'];
											}
											$condicionMunicipioRector = " AND CodigoDANE = $codigoMunicipio ";
										}
										if ($_SESSION['perfil'] == "7") {
											$documentoCoordinador = $_SESSION['num_doc'];
											$consultaMunicipioInstitucion = "SELECT i.cod_mun FROM instituciones i LEFT JOIN sedes$periodoActual s ON s.cod_inst = i.codigo_inst WHERE s.id_coordinador = $documentoCoordinador LIMIT 1 ";
											$respuestaMunicipioInstitucion = $Link->query($consultaMunicipioInstitucion) or die ('Error al consultar el municipio de la institución. ' .mysqli_error($Link));
											if ($respuestaMunicipioInstitucion->num_rows > 0) {
												$dataMunicipioInstitucion = $respuestaMunicipioInstitucion->fetch_assoc();
												$codigoMunicipio = $dataMunicipioInstitucion['cod_mun'];
											}
											$condicionMunicipioCoordinador = " AND CodigoDANE = $codigoMunicipio ";
										}

										$consulta_municipios = "SELECT CodigoDANE AS id, Ciudad AS nombre FROM ubicacion WHERE CodigoDANE LIKE '$codigo_departamento%' $condicionMunicipioRector $condicionMunicipioCoordinador ";
										if ($codigo_municipio > 0) { $consulta_municipios .= " AND CodigoDANE = '$codigo_municipio'"; }
										$consulta_municipios .= " ORDER BY Ciudad";
										// var_dump($consulta_municipios);
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
										// var_dump($codigo_municipio);
										if ($codigo_municipio > 0){
											$condicionRector = '';
											if ($_SESSION['perfil'] == '6' && $_SESSION['num_doc'] != '') {
												$consultaInstitucion = " SELECT codigo_inst FROM instituciones WHERE cc_rector = " .$_SESSION['num_doc'] . ";"; 
												$respuestaInstitucion = $Link->query($consultaInstitucion) or die ('Error al consultar la institución ' . mysqli_error($Link));
												if ($respuestaInstitucion->num_rows > 0) {
													$dataInstitucion = $respuestaInstitucion->fetch_assoc();
													$codigoInstitucion = $dataInstitucion['codigo_inst'];
												}
												$condicionRector = " AND codigo_inst = $codigoInstitucion ";
											}
											else if ($_SESSION['perfil'] == "7" && $_SESSION['num_doc'] != ""){
												$documentoCoordinador = $_SESSION['num_doc'];
												$consultaInstitucion = "SELECT i.codigo_inst FROM instituciones i LEFT JOIN sedes$periodoActual s ON s.cod_inst = i.codigo_inst WHERE id_coordinador = $documentoCoordinador LIMIT 1 ";
												$respuestaInstitucion = $Link->query($consultaInstitucion) or die ('Error al consultar la institucion ' . mysqli_error($Link));
												if ($respuestaInstitucion->num_rows > 0 ) {
													$dataInstitucion = $respuestaInstitucion->fetch_assoc();
													$codigoInstitucion = $dataInstitucion['codigo_inst'];
												}
												$condicionRector = " AND codigo_inst = $codigoInstitucion ";
 											}

											$consulta_instituciones = "SELECT codigo_inst AS codigo, nom_inst AS nombre FROM instituciones WHERE cod_mun = '$codigo_municipio' $condicionRector ORDER BY nom_inst";
											// var_dump($consulta_instituciones);
											$respuesta_instituciones = $Link->query($consulta_instituciones) or die("Error al consultar las instituciones: ". $Link->error);
											if ($respuesta_instituciones->num_rows > 0){
												while ($institucion = $respuesta_instituciones->fetch_assoc()){
									?>
													<option value="<?= $institucion['codigo'] ?>" <?= (isset($_POST['institucion']) && $_POST['institucion'] == $institucion['codigo']) ? 'selected' : ''; ?>><?= $institucion['nombre']; ?></option>
									<?php
												}
											}
										}else if($codigo_municipio == "0"){
											$condicionRector = '';
											if ($_SESSION['perfil'] == '6' && $_SESSION['num_doc'] != '') {
												$consultaInstitucion = " SELECT codigo_inst FROM instituciones WHERE cc_rector = " .$_SESSION['num_doc'] . ";"; 
												$respuestaInstitucion = $Link->query($consultaInstitucion) or die ('Error al consultar la institución ' . mysqli_error($Link));
												if ($respuestaInstitucion->num_rows > 0) {
													$dataInstitucion = $respuestaInstitucion->fetch_assoc();
													$codigoInstitucion = $dataInstitucion['codigo_inst'];
												}
												$condicionRector = " AND codigo_inst = $codigoInstitucion ";
											}
											else if ($_SESSION['perfil'] == "7" && $_SESSION['num_doc'] != ""){
												$documentoCoordinador = $_SESSION['num_doc'];
												$consultaInstitucion = "SELECT i.codigo_inst FROM instituciones i LEFT JOIN sedes$periodoActual s ON s.cod_inst = i.codigo_inst WHERE id_coordinador = $documentoCoordinador LIMIT 1 ";
												$respuestaInstitucion = $Link->query($consultaInstitucion) or die ('Error al consultar la institucion ' . mysqli_error($Link));
												if ($respuestaInstitucion->num_rows > 0 ) {
													$dataInstitucion = $respuestaInstitucion->fetch_assoc();
													$codigoInstitucion = $dataInstitucion['codigo_inst'];
												}
												$condicionRector = " AND codigo_inst = $codigoInstitucion ";
 											}

											$consulta_instituciones = "SELECT codigo_inst AS codigo, nom_inst AS nombre FROM instituciones WHERE 1=1 $condicionRector ORDER BY nom_inst";
											var_dump($consulta_instituciones);
											$respuesta_instituciones = $Link->query($consulta_instituciones) or die("Error al consultar las instituciones: ". $Link->error);
											if ($respuesta_instituciones->num_rows > 0){
												while ($institucion = $respuesta_instituciones->fetch_assoc()){
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
									<option value="">seleccione</option>
									<?php
									if (isset($_POST['sede']))
									{
										$periodo_actual = $_SESSION['periodoActual'];
										$codigo_municipio = $Link->real_escape_string($_POST['municipio']);

										$consulta_sedes = "SELECT DISTINCT cod_sede AS codigo, nom_sede AS nombre FROM sedes$periodo_actual WHERE cod_inst = '$codigo_municipio' ORDER BY nom_sede ASC";
										$respuesta_sedes = $Link->query($consulta_sedes) or die('Error al consultar las sedes: '. $Link->error);
										if ($respuesta_sedes->num_rows > 0)
										{
											while ($sede = $respuesta_sedes->fetch_assoc())
											{
									?>
										  	<option value="<?= $sede['codigo'] ?>" <?php if(isset($_POST['sede']) && $_POST['sede'] == $sede['codigo']){ echo "selected"; } ?>><?= $sede['nombre'] ?></option>
									<?php
											}
										}
									}
									?>
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
																				WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME LIKE 'suplentes%';";
										$resultado_semanas = $Link->query($consulta_semanas) or die ('Error al consultar planilla_semanas: '. $Link->error);
										if($resultado_semanas->num_rows > 0)
										{
											while($semana = $resultado_semanas->fetch_assoc())
											{
												$nombre_semana = str_replace('suplentes', '', $semana["tabla"]);
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
								<button type="submit" class="btn btn-primary" id="boton_buscar_suplentes"> <i class="fa fa-search"></i> Buscar</button>
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
</div>

<?php if (isset($suplentes) && !empty($suplentes)) {  ?>
<div class="wrapper wrapper-content  animated fadeInRight" id="contenedor_listado">
  	<div class="row">
    	<div class="col-sm-12">
      		<div class="ibox">
        		<div class="ibox-content">
          			<div class="row">
          				<div class="col-sm-12">
            				<div class="table-responsive">
                				<table class="table table-striped table-hover" id="tabla_suplentes" style="cursor: pointer;">
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
											<?php if ($_SESSION['perfil'] == "0" || $permisos['titulares_derecho'] == "1" || $permisos['titulares_derecho'] == "2"): ?>
												<th>Acciones</th>
											<?php endif ?>											
										</tr>
									</thead>
									<tbody>
										<?php foreach ($suplentes as $key => $suplente) { ?>
											<tr id="<?php echo $suplente['id'] ?>" semana = "<?php echo $semanaPos ?>">
												<td><?php echo $suplente['documento']; ?></td>
												<td><?php echo $suplente['tipoDocumento']; ?></td>
												<td><?php echo $suplente['nombre']; ?></td>
												<td><?php echo $suplente['genero']; ?></td>
												<td><?php echo $suplente['grado']; ?></td>
												<td><?php echo $suplente['grupo']; ?></td>
												<td><?php echo $suplente['jornada']; ?></td>
												<td><?php echo $suplente['edad']; ?></td>
												<?php if ($_SESSION['perfil'] == "0" || $permisos['titulares_derecho'] == "1" || $permisos['titulares_derecho'] == "2"): ?>
													<td>
														<div class="btn-group">
					                          				<div class="dropdown">
					                           	 				<button class="btn btn-primary btn-sm" type="button" id="accionesProducto" data-toggle="dropdown" aria-haspopup="true">
					                              				Acciones
					                              					<span class="caret"></span>
					                            				</button>
					                            				<ul class="dropdown-menu pull-right" aria-labelledby="accionesProducto">
					                           						<?php if ($_SESSION['perfil'] == "0" || $permisos['titulares_derecho'] == "2"): ?>
					                           							<li><a onclick="editar_suplente(<?php echo $suplente['id']; ?> , '<?php echo $semanaPos;?>' )"><span class="fas fa-pencil-alt"></span>  Editar</a></li>
					                           						<?php endif ?>
					                            				</ul>
					                          				</div>
					                        			</div>
					                    			</td>
				                    			<?php endif ?>	
											</tr>
										<?php } ?>
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
											<?php if ($_SESSION['perfil'] == 1 || $_SESSION['perfil'] == 0): ?>
												<th>Acciones</th>
											<?php endif ?>
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
<?php } ?>

<?php if (isset($suplentes) && empty($suplentes)): ?>
<div class="wrapper wrapper-content  animated fadeInRight" id="contenedor_listado">
  	<div class="row">
    	<div class="col-sm-12">
      		<div class="ibox">
        		<div class="ibox-content">
          			<div class="row">
          				<div class="col-sm-12">
          					<center><h3>No existen suplentes registrados actualmente en la sede o la semana</h3></center>
          				</div>
          			</div>
          		</div>
          	</div>
        </div>
    </div>
</div>
<?php endif ?>

<?php include '../../footer.php'; ?>

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
	                		<select class="form-control" name="semana_modal" id="semana_modal" required>
	                 	 		<option value="">seleccione</option>
	                		</select>
	              		</div>
	            	</div>
	          	</div>
	          	<div class="row">
	            	<div class="col-sm-12">
	              		<div class="form-group">
	                		<label for="archivo_suplentes">Archivo</label>
	                		<div class="input-group">
		                		<div class="fileinput fileinput-new" data-provides="fileinput">
									<span class="btn btn-default btn-file"><span class="fileinput-new">seleccione</span>
									<span class="fileinput-exists">cambiar</span><input type="file" name="archivo_suplentes" id="archivo_suplentes" accept=".csv" required="required" /></span>
									<span class="fileinput-filename"></span>
									<a href="#" class="close fileinput-exists" data-dismiss="fileinput" style="float: none; vertical-align: middle;">×</a>
								</div>
	                		</div>
	                		<label for="archivo_suplentes" class="error" style="display: none;"></label>
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
<script src="<?= $baseUrl; ?>/theme/js/plugins/jasny/jasny-bootstrap.min.js"></script>
<!-- Section Scripts -->

<script src="<?= $baseUrl; ?>/modules/suplentes/js/suplentes.js"></script>

<script type="text/javascript">
	
	  $('#tabla_suplentes').DataTable({
    buttons: [ {extend: 'excel', title: 'suplentes', className: 'btnExportarExcel', exportOptions: { columns: [ 0, 1, 2, 3, 4, 5, 6, 7] } } ],
      dom: 'lr<"containerBtn"><"inputFiltro"f>tip<"clear"><"html5buttons"B>',
      order: [ 0, 'desc'],
      oLanguage: {
          sLengthMenu: 'Mostrando _MENU_ registros por página',
          sZeroRecords: 'No se encontraron registros',
          sInfo: 'Mostrando _START_ a _END_ de _TOTAL_ registros',
          sInfoEmpty: 'Mostrando 0 a 0 de 0 registros',
          sInfoFiltered: '(Filtrado desde _MAX_ registros)',
          sSearch:         'Buscar: ',
          oPaginate:{
            sFirst:    'Primero',
            sLast:     'Último',
            sNext:     'Siguiente',
            sPrevious: 'Anterior'
          }
      },
      pageLength: 25,
      responsive: true,
    }); 

	<?php if ($_SESSION['perfil'] == "0" || $permisos['titulares_derecho'] == "1" || $permisos['titulares_derecho'] == "2"): ?>
	  	var btnAcciones = '<div class="dropdown pull-right" id=""><button class="btn btn-primary btn-sm btn-outline" type="button" id="accionesTabla" data-toggle="dropdown" aria-haspopup="true">Acciones<span class="caret"></span></button><ul class="dropdown-menu pull-right" aria-labelledby="accionesTabla"><li><a onclick="$(\'.btnExportarExcel\').click()"><span class="fa fa-file-excel-o"></span> Exportar </a></li>'+
    	<?php if ($_SESSION['perfil'] == "0" || $permisos['titulares_derecho'] == "2"): ?>
    		'<li><a class="subir_suplentes"><span class="fa fa-upload"></span> Importar</a></li>'+
    	<?php endif ?>
    	'</ul></div>';
    $('.containerBtn').html(btnAcciones);
	<?php endif ?>  


</script>

<form id="formulario_editar_suplente" action="editar_suplente.php" method="post">
	<input type="hidden" name="id_suplente" id="id_suplente">
	<input type="hidden" name="semana" id="semana">
</form>

<form action="ver_suplente.php" method="POST" name="verSuplente" id="verSuplente">
  	<input type="hidden" name="id" id="id">
  	<input type="hidden" name="semana" id="semana">
</form>



<?php mysqli_close($Link); ?>
</body>
</html>
