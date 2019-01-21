<?php
	if (!isset($_POST['codSede'])) { header('Location: sedes.php'); }

	include '../../header.php';
	set_time_limit (0);
	ini_set('memory_limit','6000M');


	$titulo = "Sede";
	$periodoActual = $_SESSION['periodoActual'];
	$nomSede = (isset($_POST['nomSede'])) ? mysqli_real_escape_string($Link, $_POST['nomSede']) : '';
	$codSede = (isset($_POST['codSede'])) ? mysqli_real_escape_string($Link, $_POST['codSede']) : '';
	$nomInst = (isset($_POST['nomInst'])) ? mysqli_real_escape_string($Link, $_POST['nomInst']) : '';

	$consulta = "SELECT s.*, u.nombre as coordinador, jor.nombre AS nombreJornada, var.descripcion AS nombreVariacion
							FROM sedes$periodoActual s
							LEFT JOIN usuarios u on s.id_coordinador = u.id
							LEFT JOIN jornada jor ON jor.id = s.jornada
							LEFT JOIN variacion_menu var ON var.id = s.cod_variacion_menu
							WHERE s.cod_sede = $codSede ";
	$resultado = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));
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
?>

<div class="row wrapper wrapper-content border-bottom white-bg page-heading">
  <div class="col-lg-8">
    <h1><?php echo $nomInst; ?></h1>
    <h2><?php echo $titulo. ": " .$nomSede; ?></h2>
    <ol class="breadcrumb">
      <li>
          <a href="<?php echo $baseUrl; ?>">Home</a>
      </li>
      <li>
      	<a href="<?php echo $baseUrl . '/modules/instituciones/sedes.php'; ?>">Sedes</a>
      </li>
      <li class="active">
          <strong><?php echo $titulo; ?></strong>
      </li>
    </ol>
  </div>

  <div class="col-lg-4">
    <div class="title-action">
	  	<div class="btn-group">
        <div class="dropdown pull-right">
          <button class="btn btn-primary" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true">
            Acciones <span class="caret"></span>
          </button>
          <ul class="dropdown-menu pull-right keep-open-on-click" aria-labelledby="dropdownMenu1">
        	<?php if($_SESSION["perfil"] == 1 || $_SESSION["perfil"] == 0) { ?>
            <li>
              <a href="#" data-codigosede="<?php echo $codSede; ?>" name="editarSede" id="editarSede"><i class="fa fa-pencil"></i> Editar </a>
            </li>
            <li>
              <a href="#" class="verDispositivosSede" data-codigosede="<?php echo $codSede; ?>"><i class="fa fa-eye fa-lg"></i> Ver Dispositivos</a>
            </li>
            <li>
              <a href="#" class="verInfraestructuraSede" data-codigosede="<?php echo $codSede; ?>"><i class="fa fa-bank fa-lg"></i> Ver Infraestructura</a>
            </li>
            <li>
              <a href="#" class="verTitularesSede" data-codigosede="<?php echo$codSede; ?>"><i class="fa fa-child fa-lg"></i> Ver Titulares</a>
            </li>
          <?php } ?>
            <li>
	  					<a href="sede_archivos.php?sede=<?php echo $codSede;  ?>"><i class="fa fa-cloud"></i> Ver Archivos </a>
            </li>
          <?php if($_SESSION["perfil"] == 1 || $_SESSION["perfil"] == 0) { ?>
            <li class="divider"></li>
            <li >
              <a href="#">
                Estado:
                <input type="checkbox" id="inputEstadoSede<?php echo $id; ?>" data-toggle="toggle" data-size="mini" data-on="Activo" data-off="Inactivo" data-width="70" data-height="24" <?php if($estado == 1){ echo "checked"; } ?> onchange="confirmarCambioEstado(<?php echo $id; ?>, this.checked);">
              </a>
            </li>
          <?php } ?>
          </ul>
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
      		<div class="row">
	  				<div class="col-sm-2">
							<div class="fachadaSede">
								<?php if($fotoFachada == ''){ ?>
									<img src="<?php echo $baseUrl ?>/img/no_image.jpg" alt="Foto de sede">
								<?php }else{ ?>
									<img src="<?php echo $fotoFachada; ?>" alt="Foto de sede">
								<?php } ?>
							</div>
	  				</div>
	  				<div class="col-sm-4">
	  					<dl class="dl-horizontal">
								  <dt>
								  	<?php if($coordinador != ''){ ?>
			    						<strong>Coordinador: </strong><br>
			    						<?php //echo $coordinador; ?><br>
			    					<?php } ?>
			    					<?php if($direccion != ''){ ?>
				    					<strong>Dirección: </strong><br>
				    					<!-- <?php echo $direccion; ?><br> -->
			    					<?php } ?>
			    					<?php if($telefonos != ''){ ?>
				    					<strong>Telefonos: </strong><br>
				    					<!-- <?php echo $telefonos; ?><br> -->
			    					<?php } ?>
			    					<?php if($email != ''){ ?>
											<strong>Correo electronico: </strong><br>
				    					<!-- <?php echo $email; ?><br> -->
			    					<?php } ?>
			    					<?php if($nombreJornada != ''){ ?>
											<strong>Jornada: </strong><br>
				    					<!-- <?php echo $nombreJornada; ?><br> -->
			    					<?php } ?>
			    					<?php if($tipoValidacion != ''){ ?>
											<strong>Tipo validación: </strong><br>
				    					<!-- <?php echo $tipoValidacion; ?><br> -->
			    					<?php } ?>
			    					<?php if($sector != ''){ ?>
											<strong>Sector: </strong><br>
				    					<!-- <?php //echo ($sector == "1") ? "Rural" : "Urbano"; ?><br> -->
			    					<?php } ?>
			    					<?php if($tipoVariacion != ''){ ?>
											<strong>Variación menú: </strong><br>
				    					<!-- <?php echo $tipoVariacion; ?><br> -->
			    					<?php } ?>
								  </dt>
								  <dd>
								  	<?php if($coordinador != ''){ ?>
			    						<!-- <strong>Coordinador: </strong><br> -->
			    						<?php echo $coordinador; ?><br>
			    					<?php } ?>
			    					<?php if($direccion != ''){ ?>
				    					<!-- <strong>Dirección: </strong><br> -->
				    					<?php echo $direccion; ?><br>
			    					<?php } ?>
			    					<?php if($telefonos != ''){ ?>
				    					<!-- <strong>Telefonos: </strong><br> -->
				    					<?php echo $telefonos; ?><br>
			    					<?php } ?>
			    					<?php if($email != ''){ ?>
											<!-- <strong>Correo electronico: </strong><br> -->
				    					<?php echo $email; ?><br>
			    					<?php } ?>
			    					<?php if($nombreJornada != ''){ ?>
											<!-- <strong>Jornada: </strong><br> -->
				    					<?php echo $nombreJornada; ?><br>
			    					<?php } ?>
			    					<?php if($tipoValidacion != ''){ ?>
											<!-- <strong>Tipo validación: </strong><br> -->
				    					<?php echo $tipoValidacion; ?><br>
			    					<?php } ?>
			    					<?php if($sector != ''){ ?>
											<!-- <strong>Sector: </strong><br> -->
				    					<?php echo ($sector == "1") ? "Rural" : "Urbano"; ?><br>
			    					<?php } ?>
			    					<?php if($tipoVariacion != ''){ ?>
											<!-- <strong>Variación menú: </strong><br> -->
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
					<h2>Priorización</h2>
					<?php
					$priorizacion = array();
					$totalesPriorizacion = array();
					$totalesPriorizacion['APS'] = 0;
					$totalesPriorizacion['CAJMRI'] = 0;
					$totalesPriorizacion['CAJTRI'] = 0;
					$totalesPriorizacion['CAJMPS'] = 0;

					for ($i=1; $i <= 3 ; $i++) {
						$totalesPriorizacion['Etario'.$i.'_APS'] = 0;
						$totalesPriorizacion['Etario'.$i.'_CAJMRI'] = 0;
						$totalesPriorizacion['Etario'.$i.'_CAJTRI'] = 0;
						$totalesPriorizacion['Etario'.$i.'_CAJMPS'] = 0;
					}
					foreach ($semanas as $semana){
						$consulta = " select * from sedes_cobertura where semana = '$semana' and cod_sede = $codSede ";
						$resultado = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));
						if($resultado->num_rows >= 1){
							while($row = $resultado->fetch_assoc()){
								if(isset($priorizacion['APS'][$semana]) && floatval($priorizacion['APS'][$semana]) > 0){
									$priorizacion['APS'][$semana] = $priorizacion['APS'][$semana] + $row['APS'];
								}else{
									$priorizacion['APS'][$semana] = $row['APS'];
								}
								if(isset($priorizacion['CAJMRI'][$semana]) && floatval($priorizacion['CAJMRI'][$semana]) > 0){
									$priorizacion['CAJMRI'][$semana] = $priorizacion['CAJMRI'][$semana] + $row['CAJMRI'];
								}else{
									$priorizacion['CAJMRI'][$semana] = $row['CAJMRI'];
								}
								if(isset($priorizacion['CAJTRI'][$semana]) && floatval($priorizacion['CAJTRI'][$semana]) > 0){
									$priorizacion['CAJTRI'][$semana] = $priorizacion['CAJTRI'][$semana] + $row['CAJTRI'];
								}else{
									//$priorizacion['CAJTRI'][$semana] = $row['CAJTRI'];
								}
								if(isset($priorizacion['CAJMPS'][$semana]) && floatval($priorizacion['CAJMPS'][$semana]) > 0){
									$priorizacion['CAJMPS'][$semana] = $priorizacion['CAJMPS'][$semana] + $row['CAJMPS'];
								}else{
									$priorizacion['CAJMPS'][$semana] = $row['CAJMPS'];
								}

								for ($i=1; $i <= 3 ; $i++) {
									if(isset($priorizacion['Etario'.$i.'_APS'][$semana]) && floatval($priorizacion['Etario'.$i.'_APS'][$semana]) > 0){
										$priorizacion['Etario'.$i.'_APS'][$semana] = $priorizacion['Etario'.$i.'_APS'][$semana] + $row['Etario'.$i.'_APS'];
									}else{
										$priorizacion['Etario'.$i.'_APS'][$semana] = $row['Etario'.$i.'_APS'];
									}

									if(isset($priorizacion['Etario'.$i.'_CAJMRI'][$semana]) && floatval($priorizacion['Etario'.$i.'_CAJMRI'][$semana]) > 0){
										$priorizacion['Etario'.$i.'_CAJMRI'][$semana] = $priorizacion['Etario'.$i.'_CAJMRI'][$semana] + $row['Etario'.$i.'_CAJMRI'];
									}else{
										$priorizacion['Etario'.$i.'_CAJMRI'][$semana] = $row['Etario'.$i.'_CAJMRI'];
									}

									if(isset($priorizacion['Etario'.$i.'_CAJTRI'][$semana]) && floatval($priorizacion['Etario'.$i.'_CAJTRI'][$semana]) > 0){
										$priorizacion['Etario'.$i.'_CAJTRI'][$semana] = $priorizacion['Etario'.$i.'_CAJTRI'][$semana] + $row['Etario'.$i.'_CAJTRI'];
									}else{
										//$priorizacion['Etario'.$i.'_CAJTRI'][$semana] = $row['Etario'.$i.'_CAJTRI'];
									}

									if(isset($priorizacion['Etario'.$i.'_CAJMPS'][$semana]) && floatval($priorizacion['Etario'.$i.'_CAJMPS'][$semana]) > 0){
										$priorizacion['Etario'.$i.'_CAJMPS'][$semana] = $priorizacion['Etario'.$i.'_CAJMPS'][$semana] + $row['Etario'.$i.'_CAJMPS'];
									}else{
										$priorizacion['Etario'.$i.'_CAJMPS'][$semana] = $row['Etario'.$i.'_CAJMPS'];
									}
								}

								$totalesPriorizacion['APS'] = $totalesPriorizacion['APS'] + $row['APS'];
								$totalesPriorizacion['CAJMRI'] = $totalesPriorizacion['CAJMRI'] + $row['CAJMRI'];
								//$totalesPriorizacion['CAJTRI'] = $totalesPriorizacion['CAJTRI'] + $row['CAJTRI'];
								$totalesPriorizacion['CAJMPS'] = $totalesPriorizacion['CAJMPS'] + $row['CAJMPS'];

								for ($i=1; $i <= 3 ; $i++) {
									$totalesPriorizacion['Etario'.$i.'_APS'] = $totalesPriorizacion['Etario'.$i.'_APS'] + $row['Etario'.$i.'_APS'];
									$totalesPriorizacion['Etario'.$i.'_CAJMRI'] = $totalesPriorizacion['Etario'.$i.'_CAJMRI'] + $row['Etario'.$i.'_CAJMRI'];
									//$totalesPriorizacion['Etario'.$i.'_CAJTRI'] = $totalesPriorizacion['Etario'.$i.'_CAJTRI'] + $row['Etario'.$i.'_CAJTRI'];
									$totalesPriorizacion['Etario'.$i.'_CAJMPS'] = $totalesPriorizacion['Etario'.$i.'_CAJMPS'] + $row['Etario'.$i.'_CAJMPS'];
								}
							}
						}
					}

					?>
					<div class="table-responsive">
						<table class="table table-striped table-hover dataTable-priorizacion">
							<thead>
								<tr>
									<th>Complemento</th>
									<?php
										foreach ($semanas as $semana){ ?>
											<th style="text-align:center;">Sem <?php echo $semana; ?></th>
										<?php }
									?>
									<th style="text-align:center;">Total</th>
								</tr>
							</thead>
							<tbody>
								<?php for ($i=1; $i <= 3 ; $i++) {  ?>
									<tr> <td>Etario <?php echo$i; ?> APS</td> <?php foreach ($semanas as $semana){ ?> <td style="text-align:center;"><?php echo $priorizacion['Etario'.$i.'_APS'][$semana]; ?></td> <?php } ?> <td style="text-align:center;"><?php echo $totalesPriorizacion['Etario'.$i.'_APS']; ?></td> </tr>
								<?php } ?>

								<tr> <th>Total APS</th> <?php foreach ($semanas as $semana){ ?> <th style="text-align:center;"><?php echo $priorizacion['APS'][$semana]; ?></th> <?php } ?> <th style="text-align:center;"><?php echo $totalesPriorizacion['APS']; ?></th> </tr>

								<?php for ($i=1; $i <= 3 ; $i++) {  ?>
									<tr> <td>Etario <?php echo$i; ?> CAJMRI</td> <?php foreach ($semanas as $semana){ ?> <td style="text-align:center;"><?php echo $priorizacion['Etario'.$i.'_CAJMRI'][$semana]; ?></td> <?php } ?> <td style="text-align:center;"><?php echo $totalesPriorizacion['Etario'.$i.'_CAJMRI']; ?></td> </tr>
								<?php } ?>

								<tr> <th>Total CAJMRI</th> <?php foreach ($semanas as $semana){ ?> <th style="text-align:center;"><?php echo $priorizacion['CAJMRI'][$semana]; ?></th> <?php } ?> <th style="text-align:center;"><?php echo $totalesPriorizacion['CAJMRI']; ?></th> </tr>

								<!-- <?php for ($i=1; $i <= 3 ; $i++) {  ?>
									<tr> <td>Etario <?php echo$i; ?> CAJTRI</td> <?php foreach ($semanas as $semana){ ?> <td style="text-align:center;"><?php echo $priorizacion['Etario'.$i.'_CAJTRI'][$semana]; ?></td> <?php } ?> <td style="text-align:center;"><?php echo $totalesPriorizacion['Etario'.$i.'_CAJTRI']; ?></td> </tr>
								<?php } ?> -->

								<!-- <tr> <th>Total CAJTRI</th> <?php foreach ($semanas as $semana){ ?> <th style="text-align:center;"><?php echo $priorizacion['CAJTRI'][$semana]; ?></th> <?php } ?> <th style="text-align:center;"><?php echo $totalesPriorizacion['CAJTRI']; ?></th> </tr> -->

								<?php for ($i=1; $i <= 3 ; $i++) {  ?>
									<tr> <td>Etario <?php echo$i; ?> CAJMPS</td> <?php foreach ($semanas as $semana){ ?> <td style="text-align:center;"><?php echo $priorizacion['Etario'.$i.'_CAJMPS'][$semana]; ?></td> <?php } ?> <td style="text-align:center;"><?php echo $totalesPriorizacion['Etario'.$i.'_CAJMPS']; ?></td> </tr>
								<?php } ?>

								<tr> <th>Total CAJMPS</th> <?php foreach ($semanas as $semana){ ?> <th style="text-align:center;"><?php echo $priorizacion['CAJMPS'][$semana]; ?></th> <?php } ?> <th style="text-align:center;"><?php echo $totalesPriorizacion['CAJMPS']; ?></th> </tr>

							</tbody>
							<tfoot>
								<tr>
									<th>Complemento</th>
									<?php
										foreach ($semanas as $semana){ ?>
											<th style="text-align:center;">Sem <?php echo $semana; ?></th>
										<?php }
									?>
									<th style="text-align:center;">Total</th>
								</tr>
							</tfoot>
						</table>
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
							<h2>Focalización</h2>
                            <div class="clients-list">
                            <ul class="nav nav-tabs">
                                <!-- <span class="pull-right small text-muted">1406 Elements</span> -->


								<?php
								$auxIndice = 1;
								foreach ($semanas as $semana){
									?>
									<li class=" <?php if($auxIndice == 1){echo ' active '; } ?> "><a data-toggle="tab" href="#tab-<?php echo $semana; ?>"><i class="fa fa-child"></i> Sem <?php echo $semana; ?></a></li>
									<?php
									$auxIndice++;
								}
								?>






                            </ul>
                            <div class="tab-content">
								<?php
								$auxIndice = 1;
								foreach ($semanas as $semana){ ?>
									<div id="tab-<?php echo $semana; ?>" class="tab-pane <?php if($auxIndice == 1){echo ' active '; } ?>">
										<div style="overflow : hidden; height: 100%;">
											<div class="table-responsive">
												<table class="table table-striped table-hover dataTable-focalizacion">
													<thead>
														<tr>
															<th>Num doc</th>
															<th>Tipo doc</th>
															<th>Nombre</th>
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

														$consulta = " SELECT f.num_doc, t.Abreviatura AS tipo_doc, CONCAT(f.nom1, ' ', f.nom2, ' ', f.ape1, ' ', f.ape2) AS nombre, f.genero, g.nombre as grado, f.nom_grupo, jor.nombre as jornada, f.edad, f.Tipo_complemento FROM focalizacion$semana f LEFT JOIN tipodocumento t ON t.id = f.tipo_doc LEFT JOIN grados g ON g.id = f.cod_grado LEFT JOIN jornada jor ON jor.id = f.cod_jorn_est where cod_sede = '$codSede' order by f.nom1 asc ";

														$resultado = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));
														if($resultado->num_rows >= 1){
															while($row = $resultado->fetch_assoc()){ ?>
															<tr class="titular" numDoc="<?php echo $row['num_doc']; ?>" tipoDoc="<?php echo $row['tipo_doc']; ?>" semana="<?php echo $semana; ?>" style="cursor:pointer" >
																<td><?php echo $row['num_doc']; ?></td>
																<td><?php echo $row['tipo_doc']; ?></td>
																<td><?php echo $row['nombre']; ?></td>
																<td style="text-align_center;"><?php echo $row['genero']; ?></td>
																<td><?php echo $row['grado']; ?></td>
																<td style="text-align:center;"><?php echo $row['nom_grupo']; ?></td>
																<td><?php echo $row['jornada']; ?></td>
																<td style="text-align:center;"><?php echo $row['edad']; ?></td>
																<td style="text-align:center;"><?php echo $row['Tipo_complemento']; ?></td>
															</tr>
															<?php }
														}
														?>
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
															<th>Tipo COMP</th>
														</tr>
													</tfoot>
												</table>
											</div>
										</div>
									</div>
									<?php
									$auxIndice++;
								}
								?>

                            </div><!-- /.tab-content -->

						</div><!-- /.clients-list -->
                        </div>
                    </div><!-- /.ibox -->
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
	        <button type="button" class="btn btn-primary btn-outline btn-sm" data-dismiss="modal" onclick="revertirEstado();">Cancelar</button>
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

  <!-- Page-Level Scripts -->
  <script src="<?php echo $baseUrl; ?>/modules/instituciones/js/sede.js"></script>
  <script>
    $(document).ready(function(){
      $('.dataTable-focalizacion').DataTable({
				order: [[2,'asc']],
        scrollY:        "500px",
        scrollX:        true,
        scrollCollapse: true,
	      paging:         false,
        pageLength: 25,
        responsive: true,
        dom: '<"html5buttons"B>lTfgitp',
        buttons: [
          {extend: 'excel', title: 'ExampleFile'}
        ],
      });
    });

    // Evitar el burbujeo del DOM en el control dropbox
    $(document).on('click', '.dropdown li:nth-child(3)', function(e) { e.stopPropagation(); });
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

</body>
</html>