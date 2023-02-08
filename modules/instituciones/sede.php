<?php
	if (!isset($_POST['codSede'])) { header('Location: sedes.php'); }

	include '../../header.php';
	set_time_limit (0);
	ini_set('memory_limit','6000M');

	if ($permisos['instituciones'] == "0") {
   	 	?><script type="text/javascript">
      		window.open('<?= $baseUrl ?>', '_self');
    	</script>
  	<?php exit(); }
	  else {
		?><script type="text/javascript">
		  const list = document.querySelector(".li_sedes");
		  list.className += " active ";
		  </script>
		<?php
	  }
	

	
	$periodoActual = $_SESSION['periodoActual'];
	$nomSede = (isset($_POST['nomSede'])) ? mysqli_real_escape_string($Link, $_POST['nomSede']) : '';
	$codSede = (isset($_POST['codSede'])) ? mysqli_real_escape_string($Link, $_POST['codSede']) : '';
	$nomInst = (isset($_POST['nomInst'])) ? mysqli_real_escape_string($Link, $_POST['nomInst']) : '';

	$consulta = "SELECT
					s.*,
					u.nombre as coordinador,
					jor.nombre AS nombreJornada,
					var.descripcion AS nombreVariacion,
					ubicacion.Ciudad as municipio
				FROM sedes$periodoActual s
					LEFT JOIN usuarios u on s.id_coordinador = u.id
					LEFT JOIN jornada jor ON jor.id = s.jornada
					LEFT JOIN variacion_menu var ON var.id = s.cod_variacion_menu
					LEFT JOIN ubicacion ON ubicacion.CodigoDANE = s.cod_mun_sede
				WHERE s.cod_sede = $codSede ";
	$resultado = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));
	$datos_sede = [];
	if($resultado->num_rows >= 1){
		$row = $resultado->fetch_assoc();
		$id = $row['id'];
		$email = $row['email'];
		$estado = $row['estado'];
		$sector = $row["sector"];
		$direccion = $row['direccion'];
		$telefonos = $row['telefonos'];
		$fotoFachada = $row['url_foto'];
		$coordinador = $row['coordinador'];
		$nombreJornada = $row['nombreJornada'];
		$tipoVariacion = $row['nombreVariacion'];
		$tipoValidacion = $row['tipo_validacion'];
		$manipuladoras = $row['cantidad_Manipuladora'];
		$datos_sede = $row;
	}

	$consulta = "SELECT DISTINCT semana FROM planilla_semanas";
	$resultado = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));
	if($resultado->num_rows > 0){
		while($row = $resultado->fetch_assoc()){
			$aux = $row['semana'];
			$consulta2 = " show tables like 'focalizacion$aux' ";
			$resultado2 = $Link->query($consulta2) or die ('Unable to execute query. '. mysqli_error($Link));
			if($resultado2->num_rows >= 1){
			 	$semanas[] = $aux;
			}
		}
	}

	$cantGruposEtarios = $_SESSION['cant_gruposEtarios'];
	$consultaComplementos = "SELECT CODIGO FROM tipo_complemento ";
	$respuestaComplementos = $Link->query($consultaComplementos) or die ('Error al consultar los complementos' . mysqli_error($Link));
	if ($respuestaComplementos->num_rows > 0) {
		while ($dataComplementos = $respuestaComplementos->fetch_assoc()) {
			$complementos[] = $dataComplementos['CODIGO']; 
		}
	}
	$nameLabel = get_titles('sedes', 'sedes', $labels);
	$titulo = $nameLabel;
?>

<div class="row wrapper wrapper-content border-bottom white-bg page-heading">
  <div class="col-lg-8">
    <h1><?php echo $nomInst; ?></h1>
    <h2><?php echo $nameLabel. ": " .$nomSede; ?></h2>
    <ol class="breadcrumb">
      <li>
          <a href="<?php echo $baseUrl; ?>">Inicio</a>
      </li>
      <?php if ($_SESSION['perfil'] != 6 && $_SESSION['perfil'] != 7): ?> 
      <li>
      	<a href="<?php echo $baseUrl . '/modules/instituciones/sedes.php'; ?>"><?= $titulo ?></a>
      </li>
      <?php endif ?>
      <li class="active">
          <strong><?php echo $nameLabel; ?></strong>
      </li>
    </ol>
  </div>

  <?php if(($_SESSION['perfil'] == "0" || $permisos['instituciones'] == "1" || $permisos['instituciones'] == "2") && ($_SESSION['perfil'] != "6" && $_SESSION['perfil'] != "7")) { ?>
  <div class="col-lg-4">
    <div class="title-action">
	  	<div class="btn-group">
        <div class="dropdown pull-right">
          <button class="btn btn-primary" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true">
            Acciones <span class="caret"></span>
          </button>
          <ul class="dropdown-menu pull-right keep-open-on-click" aria-labelledby="dropdownMenu1">
          	<?php if ($_SESSION['perfil'] == "0" || $permisos['instituciones'] == "2"): ?>
          		<li>
	              <a href="#" data-codigosede="<?php echo $codSede; ?>" name="editarSede" id="editarSede"><i class="fas fa-pencil-alt fa-lg"></i> Editar </a>
	            </li>
          	<?php endif ?>
	          <li>
	            <a href="#" class="verDispositivosSede" data-codigosede="<?php echo $codSede; ?>"><i class="fa fa-eye fa-lg"></i> Ver Dispositivos</a>
	          </li>
	          <li>
	            <a href="#" class="verInfraestructuraSede" data-codigosede="<?php echo $codSede; ?>"><i class="fa fa-bank fa-lg"></i> Ver Infraestructura</a>
	          </li>
	          <li>
	           	<a href="#" class="verTitularesSede" data-codigosede="<?php echo$codSede; ?>"><i class="fa fa-child fa-lg"></i> Ver Titulares</a>
	          </li>
	          <li>
							<a href="sede_archivos.php?sede=<?php echo $codSede;  ?>"><i class="fa fa-cloud"></i> Ver Archivos </a>
            </li>
            <?php if ($_SESSION['perfil'] == "0" || $permisos['instituciones'] == "2"): ?>
            	<li class="divider"></li>
            		<li>
              		<a href="#">
                		Estado:
                		<input type="checkbox" id="inputEstadoSede<?php echo $id; ?>" data-toggle="toggle" data-size="mini" data-on="Activo" data-off="Inactivo" data-width="70" data-height="24" <?php if($estado == 1){ echo "checked"; } ?> onchange="confirmarCambioEstado(<?php echo $id; ?>, this.checked);">
              		</a>
            		</li>
            	<?php endif ?>
          </ul>
        </div>
      </div>
    </div>
  </div>
  <?php } ?>
</div>

<div class="wrapper wrapper-content  animated fadeInRight">
  <div class="row">
    <div class="col-sm-12">
      <div class="ibox">
      	<div class="ibox-content">
      		<div class="row">
  					<div class="col-sm-2">
							<div class="fachadaSede">
								<?php if($fotoFachada == '') { ?>
									<img src="<?php echo $baseUrl ?>/img/no_image.jpg" alt="Foto de sede">
								<?php } else { ?>
									<img src="<?php echo $fotoFachada; ?>" alt="Foto de sede">
								<?php } ?>
							</div>
  					</div>
  					<div class="col-sm-4">
  						<dl class="dl-horizontal">
	              <dt>
		    					<?php if($datos_sede['municipio'] != ''){ ?>
										<strong>Municipio: </strong><br>
		    					<?php } ?>
		    					<?php if($sector != ''){ ?>
										<strong>Sector: </strong><br>
		    					<?php } ?>
							  	<?php if($coordinador != ''){ ?>
		    						<strong>Coordinador: </strong><br>
		    					<?php } ?>
		    					<?php if($direccion != ''){ ?>
			    					<strong>Dirección: </strong><br>
		    					<?php } ?>
		    					<?php if($telefonos != ''){ ?>
			    					<strong>Telefonos: </strong><br>
		    					<?php } ?>
		    					<?php if($email != ''){ ?>
										<strong>Correo electronico: </strong><br>
		    					<?php } ?>
		    					<?php if($nombreJornada != ''){ ?>
										<strong>Jornada: </strong><br>
		    					<?php } ?>
		    					<?php if($tipoValidacion != ''){ ?>
										<strong>Tipo validación: </strong><br>
		    					<?php } ?>
		    					<?php if($tipoVariacion != ''){ ?>
										<strong>Variación menú: </strong><br>
		    					<?php } ?>
								</dt>
							<dd>
	    					<?php if($datos_sede['municipio'] != ''){ ?>
		    					<?php echo $datos_sede['municipio']; ?><br>
	    					<?php } ?>
	    					<?php if($sector != ''){ ?>
		    					<?php echo ($sector == "1") ? "Rural" : "Urbano"; ?><br>
	    					<?php } ?>
						  	<?php if($coordinador != ''){ ?>
	    						<?php echo $coordinador; ?><br>
	    					<?php } ?>
	    					<?php if($direccion != ''){ ?>
		    					<?php echo $direccion; ?><br>
	    					<?php } ?>
	    					<?php if($telefonos != ''){ ?>
		    					<?php echo $telefonos; ?><br>
	    					<?php } ?>
	    					<?php if($email != ''){ ?>
		    					<?php echo $email; ?><br>
	    					<?php } ?>
	    					<?php if($nombreJornada != ''){ ?>
		    					<?php echo $nombreJornada; ?><br>
	    					<?php } ?>
	    					<?php if($tipoValidacion != ''){ ?>
		    					<?php echo $tipoValidacion; ?><br>
	    					<?php } ?>
	    					<?php if($tipoVariacion != ''){ ?>
		    					<?php echo $tipoVariacion; ?><br>
	    					<?php } ?>
							</dd>
						</dl>
  				</div>
  				<div class="col-sm-6">
						<label>Manipuladoras</label></br>
						<label>
							<?php
								$cantidad_manipuladoras = $manipuladoras;
								while($manipuladoras > 0)
								{
							?>
							<i class="fa fa-child fa-2x text-muted"></i>
							<?php
								$manipuladoras--;
								}
							?>
						</label>
						<label class="text-muted" style="font-size: 26px;">= <?php echo $cantidad_manipuladoras; ?></label>
  				</div>
  			</div>
  			<div class="row">
  				<div class="col-md-12 table-responsive">
  					<table class="table table-striped">
  						<thead>
  							<tr>
  								<th>Complemento</th>
										<?php foreach ($complementos as $key => $value): ?>
											<th><?= $value; ?></th>
										<?php endforeach ?>
									<th>Total</th>	
  							</tr>
  						</thead>
  						<tbody>
  							<tr>
  								<td>Cantidad manipuladoras</td>
  								<?php foreach ($complementos as $key => $value): ?>
										<td><?= $datos_sede['Manipuladora_'.$value]; ?></td>
  								<?php endforeach ?>
  								<td><?= $datos_sede['cantidad_Manipuladora'] ?></td>
  							</tr>
  						</tbody>
  					</table>
  				</div>
  			</div>
      	</div>
      </div>
    </div>
  </div>
</div>

<div class="wrapper wrapper-content  animated fadeInRight">
  <div class="row">
    <div class="col-sm-12">
      <div class="ibox">
        <div class="ibox-content">
					<?php
						$priorizacion = array();
						$totalesPriorizacion = array();
						foreach ($complementos as $key => $value) {
							$totalesPriorizacion[$value] = 0;
						}

						for ($i=1; $i <= $cantGruposEtarios ; $i++) {
							foreach ($complementos as $key => $value) {
								$totalesPriorizacion['Etario'.$i.'_'.$value] = 0;
							}
						}

						foreach ($semanas as $semana) {
							$consulta = "SELECT * FROM sedes_cobertura WHERE semana = '$semana' AND cod_sede = '$codSede'";
							$resultado = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));
							if($resultado->num_rows >= 1) {
								while($row = $resultado->fetch_assoc()) {
									foreach ($complementos as $key => $value) {
										if (isset($priorizacion[$value][$semana]) && floatval($priorizacion[$value][$semana]) > 0 ) {
											$priorizacion[$value][$semana] = $priorizacion[$value][$semana] + $row[$value];
										}else{
											$priorizacion[$value][$semana] = $row[$value];
										}
									}

									for ($i=1; $i <= $cantGruposEtarios ; $i++) {
										foreach ($complementos as $key => $value) {
											if (isset($priorizacion['Etario'.$i.'_'.$value][$semana]) && floatval($priorizacion['Etario'.$i.'_'.$value][$semana]) > 0 ) {
												$priorizacion['Etario'.$i.'_'.$value][$semana] = $priorizacion['Etario'.$i.'_'.$value][$semana] + $row['Etario'.$i.'_'.$value];
											} else {
												$priorizacion['Etario'.$i.'_'.$value][$semana] = $row['Etario'.$i.'_'.$value];
											}
										}
									}

									foreach ($complementos as $key => $value) {
										$totalesPriorizacion[$value] = $totalesPriorizacion[$value] + $row[$value];
									}

									for ($i=1; $i <= $cantGruposEtarios ; $i++) {
										foreach ($complementos as $key => $value) {
											$totalesPriorizacion['Etario'.$i.'_'.$value] = $totalesPriorizacion['Etario'.$i.'_'.$value] + $row['Etario'.$i.'_'.$value];
										}
									}
								}
							}
						}
					?>
					<h2>Priorización</h2>
					<div class="table-responsive">
						<table class="table table-striped table-hover dataTable-priorizacion">
							<thead>
								<tr>
									<th>Complemento</th>
									<?php foreach ($semanas as $semana){ ?>
										<th class="text-center">Sem <?= $semana; ?></th>
									<?php } ?>
									<th class="text-center">Total</th>
								</tr>
							</thead>
							<tbody>
								<?php foreach ($complementos as $key => $value): ?>
									<?php for ($i=1; $i <= $cantGruposEtarios ; $i++) {  ?>
										<tr>
											<td>Etario <?= $i; ?> <?= $value; ?></td>
												<?php foreach ($semanas as $semana){ ?>
													<td class="text-center">
														<?= (isset($priorizacion['Etario'.$i.'_'.$value][$semana])) ? $priorizacion['Etario'.$i.'_'.$value][$semana] : ""; ?>
													</td>
												<?php } ?>
											<td class="text-center"><?= $totalesPriorizacion['Etario'.$i.'_'.$value]; ?></td>
										</tr>
									<?php } ?>
									<tr>
										<th>Total <?= $value; ?></th>
											<?php foreach ($semanas as $semana){ ?>
												<th class="text-center"><?= isset($priorizacion[$value][$semana]) ? $priorizacion[$value][$semana] : ""; ?></th>
											<?php } ?>
										<th class="text-center"><?= $totalesPriorizacion[$value]; ?></th>
									</tr>	
								<?php endforeach ?>

							</tbody>
							<tfoot>
								<tr>
									<th>Complemento</th>
									<?php foreach ($semanas as $semana){ ?>
										<th class="text-center">Sem <?= $semana; ?></th>
									<?php } ?>
									<th class="text-center">Total</th>
								</tr>
							</tfoot>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="wrapper wrapper-content animated fadeInRight">
  <div class="row">
    <div class="col-lg-12">
      <div class="ibox float-e-margins">
        <div class="ibox-content contentBackground">
        	<h2>Novedades de priorización</h2>
          <table class="table table-striped table-hover selectableRows dataTablesNovedadesPriorizacion">
            <thead>
              <tr>
                <th>Municipio</th>
                <th>Institución</th>
                <th>Sede</th>
                <th>Fecha</th>
                <th>APS</th>
                <th>CAJMRI</th>
                <th>CAJTRI</th>
                <th>CAJMPS</th>
                <th>CAJTPS</th>
                <th>RPC</th>
                <th>Semana</th>
                <th>Observaciones</th>
              </tr>
            </thead>
            <tbody>
            </tbody>
            <tfoot>
              <tr>
								<th>Municipio</th>
								<th>Institución</th>
								<th>Sede</th>
								<th>Fecha</th>
								<th>APS</th>
								<th>CAJMRI</th>
								<th>CAJTRI</th>
								<th>CAJMPS</th>
								<th>CAJTPS</th>
								<th>RPC</th>
								<th>Semana</th>
								<th>Observaciones</th>
              </tr>
            </tfoot>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- FOCALIZACIÓN -->
<div class="wrapper wrapper-content  animated fadeInRight">
  	<div class="row">
    	<div class="col-sm-12">
      		<div class="ibox">
        		<div class="ibox-content">
		  			<h2>Focalización</h2>
          			<div class="clients-list">
            			<ul class="nav nav-tabs">
							<?php
								$auxIndice = 1;
								foreach ($semanas as $semana){
							?>
								<li class="<?php if($auxIndice == 1){ echo ' active '; } ?> "><a data-toggle="tab" href="#tab-<?= $semana; ?>"><i class="fa fa-child"></i> Sem <?= $semana; ?></a></li>
							<?php
									$auxIndice++;
								}
							?>
            			</ul>
	          			<div class="tab-content">
							<?php
								$auxIndice = 1;
								foreach ($semanas as $semana){ ?>
									<div id="tab-<?= $semana; ?>" class="tab-pane <?php if($auxIndice == 1){ echo ' active '; } ?>">
										<div class="table-responsive">
											<br>
											<table class="table table-striped table-hover dataTable-focalizacion" style="width: 100;">
												<thead>
													<tr>
														<th>Num doc</th>
														<th>Tipo doc</th>
														<th>Nombre</th>
														<th>Zona</th>
														<th>Genero</th>
														<th>Grado</th>
														<th>Grupo</th>
														<th>Jornada</th>
														<th>Edad</th>
														<th>Tipo COMP</th>
													</tr>
												</thead>
												<tbody>
													<?php
														$consulta = "SELECT
																			f.num_doc,
																			t.Abreviatura AS tipo_doc,
																			CONCAT(f.nom1, ' ', f.nom2, ' ', f.ape1, ' ', f.ape2) AS nombre,
																			f.zona_res_est,
																			f.genero,
																			g.nombre as grado,
																			f.nom_grupo,
																			jor.nombre as jornada,
																			f.edad,
																			f.Tipo_complemento
																	FROM focalizacion$semana f
																		LEFT JOIN tipodocumento t ON t.id = f.tipo_doc
																		LEFT JOIN grados g ON g.id = f.cod_grado
																		LEFT JOIN jornada jor ON jor.id = f.cod_jorn_est
																	WHERE cod_sede = '$codSede' order by f.nom1 asc ";

														$resultado = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));
														if($resultado->num_rows >= 1){
															while($row = $resultado->fetch_assoc()){ ?>
																<tr class="titular" numDoc="<?php echo $row['num_doc']; ?>" tipoDoc="<?php echo $row['tipo_doc']; ?>" semana="<?php echo $semana; ?>" style="cursor:pointer" >
																	<td><?php echo $row['num_doc']; ?></td>
																	<td><?php echo $row['tipo_doc']; ?></td>
																	<td><?php echo $row['nombre']; ?></td>
																	<td><?php echo $row['zona_res_est'] == 1 ? 'Rural' : 'Urbana'; ?></td>
																	<td style="text-align_center;"><?php echo $row['genero']; ?></td>
																	<td><?php echo $row['grado']; ?></td>
																	<td style="text-align:center;"><?php echo $row['nom_grupo']; ?></td>
																	<td><?php echo $row['jornada']; ?></td>
																	<td style="text-align:center;"><?php echo $row['edad']; ?></td>
																	<td style="text-align:center;"><?php echo $row['Tipo_complemento']; ?></td>
																</tr>
													<?php
																}
															}
													?>
												</tbody>
												<tfoot>
													<tr>
														<th>Num doc</th>
														<th>Tipo doc</th>
														<th>Nombre</th>
														<th>Zona</th>
														<th>Genero</th>
														<th>Grado</th>
														<th>Grupo</th>
														<th>Jornada</th>
														<th>Edad</th>
														<th>Tipo COMP</th>
													</tr>
												</tfoot>
											</table>
										</div>
									</div>
							<?php
								$auxIndice++;
								}
							?>
	          			</div><!--  tab-content -->
					</div><!--  client-list -->
      			</div>
    		</div>
  		</div>
	</div>
</div>

<!-- NOVEDADES DE FOCALIZACIÓN -->
<div class="wrapper wrapper-content animated fadeInRight">
  <div class="row">
    <div class="col-lg-12">
      <div class="ibox float-e-margins">
        <div class="ibox-content contentBackground">
          <table class="table table-striped table-hover selectableRows dataTablesNovedadesFocalizacion">
          	<h2>Novedades de focalización</h2>
            <thead>
              <tr>
                <th>ID</th>
                <th>Municipio</th>
                <th>Institución</th>
                <th>Sede</th>
                <th>Tipo Doc.</th>
                <th>Documento</th>
								<th>Titular</th>
                <th>Complemento</th>
                <th>Semana</th>
                <th>L</th>
                <th>M</th>
                <th>X</th>
                <th>J</th>
                <th>V</th>
              </tr>
            </thead>
            <tbody>
            </tbody>
            <tfoot>
              <tr>
								<th>ID</th>
								<th>Municipio</th>
								<th>Institución</th>
								<th>Sede</th>
								<th>Tipo Doc.</th>
								<th>Documento</th>
								<th>Titular</th>
								<th>Complemento</th>
								<th>Semana</th>
								<th>L</th>
								<th>M</th>
								<th>X</th>
								<th>J</th>
								<th>V</th>
              </tr>
            </tfoot>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Ventana modal confirmar -->
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
        <button type="button" class="btn btn-danger btn-outline btn-sm" data-dismiss="modal" onclick="revertirEstado();">Cancelar</button>
        <button type="button" class="btn btn-primary btn-sm" data-dismiss="modal" onclick="cambiarEstado();">Aceptar</button>
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
<script src="<?php echo $baseUrl; ?>/modules/instituciones/js/dataTables.fixedColumns.min.js"></script>
<link href="<?php echo $baseUrl; ?>/modules/instituciones/css/fixedColumns.dataTables.min.css" rel="stylesheet"/>

<!-- Custom and plugin javascript -->
<script src="<?php echo $baseUrl; ?>/theme/js/inspinia.js"></script>
<script src="<?php echo $baseUrl; ?>/theme/js/plugins/pace/pace.min.js"></script>
<script src="<?php echo $baseUrl; ?>/theme/js/plugins/toggle/toggle.min.js"></script>
<script src="<?php echo $baseUrl; ?>/theme/js/plugins/toastr/toastr.min.js"></script>
<script src="<?php echo $baseUrl; ?>/theme/js/plugins/iCheck/icheck.min.js"></script>
<script src="<?php echo $baseUrl; ?>/theme/js/plugins/validate/jquery.validate.min.js"></script>


<!-- Page-Level Scripts -->
<script src="<?php echo $baseUrl; ?>/modules/instituciones/js/sede.js"></script>
  <script>
    $(document).ready(function(){
			$('#loader').fadeIn();

    	// Evitar el burbujeo del DOM en el control dropbox
    	$(document).on('click', '.dropdown li:nth-child(3)', function(e) { e.stopPropagation(); });

			// Evento para ver
			$(document).on('click', '.dataTablesNovedadesPriorizacion tbody td:nth-child(-n+9)', function(){
				var tr = $(this).closest('tr');
				var datos = datatables.row(tr).data();
				$('#formVerNovedad #idNovedad').val(datos.id);
				$('#formVerNovedad').submit();
			});

			// Configuración para la tabla de focalizaciones
      	$('.dataTable-focalizacion').DataTable({
			order: [[2,'asc']],
	    	paging:         true,
        	pageLength: 10,
        	responsive: true,
        	dom: 'l<"inputFiltro"f>tip<"html5buttons"B>',
        	buttons: [
          		{extend: 'excel', title: 'ExampleFile'}
        	],
        	oLanguage: {
		        sLengthMenu: 'Mostrando _MENU_ registros',
		        sZeroRecords: 'No se encontraron registros',
		        sInfo: 'Mostrando _START_ a _END_ de _TOTAL_ registros ',
		        sInfoEmpty: 'Mostrando 0 a 0 de 0 registros',
		        sInfoFiltered: '(Filtrado desde _MAX_ registros)',
		        sSearch:         'Buscar: '
	      	}
      	});

      	// Configuración para la tabla de novedades priorización.
    	datatables = $('.dataTablesNovedadesPriorizacion').DataTable({
	      ajax: {
	        method: 'POST',
	        url: '../novedades_priorizacion/functions/fn_novedades_priorizacion_buscar_datatables.php',
	        data: {
	        	codSede: '<?= $codSede; ?>'
	        }
	      },
	      columns:[
	        { data: 'municipio'},
	        { data: 'nom_inst'},
	        { data: 'nom_sede'},
	        { data: 'fecha_hora'},
	        { data: 'APS'},
	        { data: 'CAJMRI'},
	        { data: 'CAJTRI'},
	        { data: 'CAJMPS'},
	        { data: 'CAJTPS'},
	        { data: 'RPC'},
	        { data: 'Semana'},
	        { data: 'observaciones'}
	      ],
	      buttons: [ {extend: 'excel', title: 'Sedes', className: 'btnExportarExcel', exportOptions: { columns: [0,1,2,3,4,5,6,7] } } ],
	      dom: 'lr<"inputFiltro"f>tip<"html5buttons"B>',
	      oLanguage: {
	        sLengthMenu: 'Mostrando _MENU_ registros',
	        sZeroRecords: 'No se encontraron registros',
	        sInfo: 'Mostrando _START_ a _END_ de _TOTAL_ registros ',
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
	      pageLength: 10,
	      responsive: true
	    }).on("draw", function(){ $('#loader').fadeOut(); });

    	// Configuración para la tabla de novedades de focalización.
	    datatables = $('.dataTablesNovedadesFocalizacion').DataTable({
	      ajax: {
	        method: 'POST',
	        url: '../novedades_ejecucion/functions/fn_novedades_focalizacion_index_buscar_datatables.php',
	        data: {
	        	codSede: '<?= $codSede; ?>'
	        }
	      },
	      columns:[
	        { data: 'id'},
	        { data: 'municipio'},
	        { data: 'nom_inst'},
					{ data: 'nom_sede'},
	        { data: 'Abreviatura'},
	        { data: 'num_doc_titular'},
	        { data: 'nombre'},
	        { data: 'tipo_complem'},
	        { data: 'semana'},
	        { data: 'd1'},
	        { data: 'd2'},
	        { data: 'd3'},
	        { data: 'd4'},
	        { data: 'd5'}
	      ],
	      buttons: [ {extend: 'excel', title: 'Sedes', className: 'btnExportarExcel', exportOptions: { columns: [0,1,2,3,4,5,6,7] } } ],
	      dom: 'lr<"containerBtn"><"inputFiltro"f>tip<"html5buttons"B>',
	      oLanguage: {
	        sLengthMenu: 'Mostrando _MENU_ registros',
	        sZeroRecords: 'No se encontraron registros',
	        sInfo: 'Mostrando _START_ a _END_ de _TOTAL_ registros ',
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
	      pageLength: 10,
	      responsive: true,
	      "preDrawCallback": function( settings ) {
	        $('#loader').fadeIn();
	      }
	    }).on("draw", function(){
	    	$('#loader').fadeOut();
	    });
    });
  </script>

	<?php mysqli_close($Link); ?>

	<form action="despacho_por_sede.php" method="post" name="formDespachoPorSede" id="formDespachoPorSede">
	  <input type="hidden" name="despachoAnnoI" id="despachoAnnoI" value="">
	  <input type="hidden" name="despachoMesI" id="despachoMesI" value="">
	  <input type="hidden" name="despacho" id="despacho" value="">
	</form>

	<form action="despachos.php" id="parametrosBusqueda" method="get">
	  <input type="hidden" id="pb_annoi" name="pb_annoi" value="">
	  <input type="hidden" id="pb_mes" name="pb_mes" value="">
	  <input type="hidden" id="pb_diai" name="pb_diai" value="">
	  <input type="hidden" id="pb_annof" name="pb_annof" value="">
	  <input type="hidden" id="pb_mesf" name="pb_mesf" value="">
	  <input type="hidden" id="pb_diaf" name="pb_diaf" value="">
	  <input type="hidden" id="pb_tipo" name="pb_tipo" value="">
	  <input type="hidden" id="pb_municipio" name="pb_municipio" value="">
	  <input type="hidden" id="pb_institucion" name="pb_institucion" value="">
	  <input type="hidden" id="pb_sede" name="pb_sede" value="">
	  <input type="hidden" id="pb_tipoDespacho" name="pb_tipoDespacho" value="">
	  <input type="hidden" id="pb_ruta" name="pb_ruta" value="">
	  <input type="hidden" id="pb_btnBuscar" name="pb_btnBuscar" value="">
	</form>

	<form action="<?php echo $baseUrl; ?>/modules/titulares_derecho/titular.php" method="GET" name="verTitular" id="verTitular">
		<input type="hidden" name="numDoc" id="numDoc">
		<input type="hidden" name="tipoDoc" id="tipoDoc">
		<input type="hidden" name="semana" id="semana">
		<input type="hidden" name="sede" id="sede" value="<?php echo $_POST['codSede']; ?>">
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

	<form action="../novedades_priorizacion/novedades_priorizacion_ver.php" method="post" name="formVerNovedad" id="formVerNovedad">
	  <input type="hidden" name="idNovedad" id="idNovedad">
	</form>

</body>
</html>