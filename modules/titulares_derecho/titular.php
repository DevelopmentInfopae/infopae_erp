<?php
$titulo = 'Titular en derecho';
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
    </script>
  <?php
  }

$periodoActual = $_SESSION['periodoActual'];
$semana = '';
$numDoc = 0;
$tipoDoc = '';

if(isset($_GET['semana'])){
	$semana = $_GET['semana'];
}
if(isset($_GET['numDoc'])){
	$numDoc = $_GET['numDoc'];
}
if(isset($_GET['tipoDoc'])){
	$tipoDoc = $_GET['tipoDoc'];
}
if(isset($_POST['semana'])){
	$semana = $_POST['semana'];
}
if(isset($_POST['numDoc'])){
	$numDoc = $_POST['numDoc'];
}
if(isset($_POST['tipoDoc'])){
	$tipoDoc = $_POST['tipoDoc'];
}

$consulta = " SELECT 	f.num_doc, 
						f.activo, 
						t.Abreviatura AS tipo_doc, 
						CONCAT(f.nom1, ' ', f.nom2, ' ', f.ape1, ' ', f.ape2) AS nombre, 
						f.genero, 
						g.nombre AS grado, 
						f.nom_grupo, 
						jor.nombre AS jornada, 
						f.edad, 
						f.Tipo_complemento, 
						s.cod_sede,
						s.nom_sede, 
						s.nom_inst, 
						f.* 
					FROM focalizacion$semana AS f 
					LEFT JOIN tipodocumento AS t ON t.id = f.tipo_doc 
					LEFT JOIN grados AS g ON g.id = f.cod_grado 
					LEFT JOIN jornada AS jor ON jor.id = f.cod_jorn_est 
					LEFT JOIN sedes$periodoActual AS s on s.cod_sede = f.cod_sede 
					WHERE f.num_doc = '$numDoc' AND t.Abreviatura = '$tipoDoc' 
					ORDER BY f.nom1 ASC ";

$resultado = $Link->query($consulta) or die ('Unable to execute query de focalización. '. mysqli_error($Link));
if($resultado->num_rows >= 1){
	$row = $resultado->fetch_assoc();
	$cod_sede = $row['cod_sede'];
}
?>

<div class="row wrapper border-bottom white-bg page-heading">
	<div class="col-lg-8">
		<h2>Titular de derecho</h2>
		<ol class="breadcrumb">
			<li>
				<a href="<?php echo $baseUrl; ?>">Home</a>
			</li>
			<?php   if($_SESSION['perfil'] != 6){  ?>
				<?php if(!isset($_POST['sede'])){ ?> <li> <a href="index.php?semana=<?php echo $semana; ?>">Titulares</a> </li> <?php } else{ ?>
					<li> <script> document.write('<a href="' + document.referrer + '">Sede</a>'); </script></li>
				<?php } ?>
			<?php } ?>
			<li class="active">
				<strong>Titular</strong>
			</li>
		</ol>
	</div>
	<?php if ($_SESSION['perfil'] == "0" || $permisos['titulares_derecho'] == "1" || $permisos['titulares_derecho'] == "2"): ?>
		<div class="col-lg-4">
		  	<div class="title-action">
				<div class="dropdown pull-right">
			        <button class="btn btn-primary" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true">  Acciones <span class="caret"></span></button>
			        <ul class="dropdown-menu pull-right" aria-labelledby="dropdownMenu1">
			        	<?php if ($_SESSION['perfil'] == "0" || $permisos['titulares_derecho'] == "2") : ?>
			          		<li><a  onclick="editarTitular(<?php echo $numDoc; ?>, <?= "'$semana'"; ?>)"><span class="fas fa-pencil-alt"></span> Editar </a></li>
			          	<?php endif ?>	
			          		<li><a onclick="exportarTitular(<?php echo $numDoc;?>, <?= "'$semana'"; ?>)" ><span class="fa fa-file-excel-o"></span> Exportar </a></li>
			          	<?php if ($_SESSION['perfil'] == "0" || $permisos['titulares_derecho'] == "2"): ?>
			          		<?php if ($row['activo'] == 1): ?> 		
                    			<li data-idtitular="<?php echo $numDoc; ?>" data-accion="1">
                    				<a onclick="confirmarCambioEstado(<?php echo $numDoc;?>, <?php echo $row['activo']; ?>, <?php echo "$semana"; ?>)"> Estado : 
                    				<input id="inputEstado<?= $row['num_doc'];?>" class="form-control" type="checkbox" data-toggle="toggle" data-size="mini" data-on="Activo" data-off="Inactivo" data-width="74px" checked>
                    				</a>
                    			</li>
                   			<?php elseif($row['activo'] == 0): ?>
                    			<li data-idtitular="<?php echo $numDoc;  ?>" data-accion="0">
                    				<a onclick="confirmarCambioEstado(<?php echo $numDoc;?>, <?php echo $row['activo']; ?>, <?php echo "$semana"; ?>)"> Estado : 
                    				<input id="inputEstado<?= $row['num_doc'];?>" class="form-control" type="checkbox" data-toggle="toggle" data-size="mini" data-on="Activo" data-off="Inactivo" data-width="74px" >
                    				</a>
                    			</li>
			        		<?php else: ?>
			       	  			<?php if ($row['activo'] == 1): ?>
			          				<li><a><span class="fa fa-check"></span> Estado : <b>Activo</b></a></li>
			          			<?php elseif ($row['activo'] == 0): ?>
			          				<li><a></span> Estado : <b>Inactivo</b></a></li>
			          			<?php endif ?>
			        		<?php endif ?>
			          	<?php endif ?>	
			        </ul>
		      	</div>
	 		</div>
		</div>
	<?php endif ?>
</div> <!--  wrapper -->

<div class="wrapper wrapper-content">
	<div class="row animated fadeInRight">
		<div class="col-md-4">
			<div class="ibox float-e-margins">
				<div class="ibox-title">
					<h4 style="text-align: center;"><strong><?php echo $row['nombre'];  ?></strong></h4>
				</div>
				<div>
					<div class="ibox-content no-padding border-left-right">
						<?php
							if($row['genero'] == 'M'){
								$ruta = 'img/ninno.jpg';
								$icono = 'fa-male';
								$sexo = 'Masculino';
							}else{
								$ruta = 'img/ninna.jpg';
								$icono = 'fa-female';
								$sexo = 'femenino';
							}
						?>
						<img alt="image" class="img-responsive" src="<?php echo $ruta; ?>">
					</div>
					<div class="ibox-content profile-content">
						<p style="text-align: center;"> <span style="width: 20px; display: inline-block; text-align: center;"><i class="fa fa-map-marker"></i></span> <?php echo $row['dir_res']; ?> </p>
						<p style="text-align: center;"> <span style="width: 20px; display: inline-block; text-align: center;"><i class="fa fa-building"></i></span><strong>Institución</strong></p>
						<p style="text-align: center;"> <?php echo $row['nom_inst']; ?> </p>
						<p style="text-align: center;"> <span style="width: 20px; display: inline-block; text-align: center;"><i class="fa fa-book"></i></span><strong>Sede</strong></p>
						<p style="text-align: center;"> <?php echo $row['nom_sede']; ?> </p>
						<p style="text-align: center;"> <span style="width: 20px; display: inline-block; text-align: center;"><i class="fa fa-id-card-o"></i></span><strong>Documento</strong>
						<br> <?php echo $tipoDoc;  ?> # <?php echo $numDoc;  ?> </p>
						<p style="text-align: center;"> <span style="width: 20px; display: inline-block; text-align: center;"><i class="fa fa-graduation-cap"></i></span><strong>Curso</strong>
							<br><?php echo $row['grado']; ?> <?php echo $row['nom_grupo']; ?>
						</p>
						<p style="text-align: center;"> <span style="width: 20px; display: inline-block; text-align: center;"><i class="fa <?php echo $icono; ?>"></i></span><strong>Género</strong>
							<br> <?php echo ucwords($sexo); ?>
						</p>
						<p style="text-align: center;"> <span style="width: 20px; display: inline-block; text-align: center;"><i class="fa fa-clock-o"></i></span><strong>Jornada</strong>
							<br> <?php echo $row['jornada']; ?>
						</p>
						<p style="text-align: center;"> <span style="width: 20px; display: inline-block; text-align: center;"><i class="fa fa-child"></i></span><strong>Edad</strong>
							<br> <?php echo $row['edad']; ?> años<br>
						</p>
						<p style="text-align: center;"> <span style="width: 20px; display: inline-block; text-align: center;"><i class="fa fa-home"></i></span><strong>Estrato</strong>
							<br> <?php echo $row['cod_estrato']; ?>
						</p>
					</div>
				</div>
			</div>
		</div><!--  col-md-4 -->

		<div class="col-md-8">
			<div class="row">
				<div class="col-md-12">
					<div class="ibox float-e-margins">
						<div class="ibox-title">
							<h5>Consumo de complementos alimentarios</h5>
							<div class="ibox-tools">
								<a class="collapse-link">
									<i class="fa fa-chevron-up"></i>
								</a>
								<a class="close-link">
									<i class="fa fa-times"></i>
								</a>
							</div>
						</div>
						<div class="ibox-content">
							<div>
								<?php
									$tipoDocPost = $tipoDoc;
									$numDocPost = $numDoc;
									$semanaPost = $semana;
									$filas = "";
									$fila = "";
									$semanaArray = array();
									$busqueda = '';

									$datosTabla = [];
									$datosTabla['mes'] = 0;
									$datosTabla['semana'] = 0;
									$datosTabla['tipoComplemento'] = 0;
									$datosTabla['lunes'] = 0;
									$datosTabla['martes'] = 0;
									$datosTabla['miércoles'] = 0;
									$datosTabla['jueves'] = 0;
									$datosTabla['viernes'] = 0;
									$datosTabla['validacion'] = 0;

									$consultaConsecutivo = " SELECT MAX(CONSECUTIVO) AS consecutivo FROM planilla_semanas WHERE SEMANA = '$semanaPost' "; //buscamos el maximo consecutivo para buscar los meses involucrados en la semana buscada
									$respuestaConsecutivo = $Link->query($consultaConsecutivo) or die ('Error al consultar el consecutivo ln 202');
									if ($respuestaConsecutivo->num_rows > 0) {
										$dataConsecutivo = $respuestaConsecutivo->fetch_assoc();
										// buscamos los distintos meses involucrados en la consulta
										$consultaMeses = " SELECT DISTINCT(MES) AS mes FROM planilla_semanas WHERE CONSECUTIVO <= " .$dataConsecutivo['consecutivo']. " ORDER BY CONSECUTIVO ";
										$respuestaMeses = $Link->query($consultaMeses) or die ('Error al consultar los meses ln 206');
										if ($respuestaMeses->num_rows > 0) {
											while ($dataMeses = $respuestaMeses->fetch_assoc()) {
												$fila = '';
												// primero vamos a buscar los dias del mes 
												$consultaPlanillaDias = " SELECT 	NombreMes, D1, D2, D3, D4, D5, D6, D7, D8, D9, D10, D11, D12, D13, D14, D15, D16, D17, D18, D19, D20, D21, D22, D23, D24, D25, D26, D27, D28, D29, D30, D31 
																				FROM planilla_dias WHERE mes = " .$dataMeses['mes'];
												$respuestaPlanillaDias = $Link->query($consultaPlanillaDias) or die ('Error al consultar los dias del mes ln 200');
												if ($respuestaPlanillaDias->num_rows > 0) {
													$dataPlanillaDias = $respuestaPlanillaDias->fetch_assoc();
													$planillaDias = $dataPlanillaDias;
													$nomMes = $dataPlanillaDias['NombreMes'];
												}
												// segundo vamos a consultar las semanas distintas del mes 
												$consultaSemanas = " SELECT DISTINCT(SEMANA) AS semana FROM planilla_semanas WHERE MES = '" .$dataMeses['mes']. "'";
												$respuestaSemanas = $Link->query($consultaSemanas) or die('Error al consultar las semanas ln 207');
												if ($respuestaSemanas->num_rows > 0) {
													while ($dataSemanas = $respuestaSemanas->fetch_assoc()) { // en cada cambio de semana consultaremos la tabla entregas
														unset($complemento);
														$consume = 0;
														$semanaEnCurso = $dataSemanas['semana'];
														$busqueda = " '$semanaEnCurso' AS semana, tipo_complem, ";
														$consultaDias = " SELECT DIA, NOMDIAS FROM planilla_semanas 
																			WHERE MES = '" .$dataMeses['mes']. "' AND SEMANA = '" .$dataSemanas['semana']. "'";
														$respuestaDias = $Link->query($consultaDias) or die ('Error al consultar los dias de la semana ln 212');
														if ($respuestaDias->num_rows > 0) {
															$nomDias = [];
															$dias = [];
															while ($dataDias = $respuestaDias->fetch_assoc()) {
																$nomDias[$dataDias['DIA']] = $dataDias['NOMDIAS'];
																// vamos a recorrer todos los dias del mes buscando las coincidencias con los dias de la semana
																
																// 
																foreach ($planillaDias as $keyD => $valueD) {
																	if ($valueD == $dataDias['DIA']) {
																		$busqueda .= " $keyD".', '; 
																		$dias[$keyD] = $dataDias['NOMDIAS'];
																	}
																}
															}
														}
														$busqueda .= " TipoValidacion ";
														$consultaEntregas = "SELECT " .$busqueda. " FROM entregas_res_".$dataMeses['mes'].$periodoActual. " WHERE num_doc = '$numDocPost' AND cod_sede = '$cod_sede' AND tipo = 'F' "; 
														$respuestaEntregas = $Link->query($consultaEntregas) or die ('Error al consultar las entregas ln 227');
														$entregas = [];
														if ($respuestaEntregas->num_rows > 0) {
															$dataEntregas = $respuestaEntregas->fetch_assoc();
															$dataEntregas['mes'] = $dataMeses['mes'];
															$entregas[] = $dataEntregas;
															$complemento = $dataEntregas['tipo_complem'];
														}
														
														$fila .= '<tr>';
														$fila .= '<td style="text-align:center;">' .$nomMes. '</td>';
														$fila .= '<td style="text-align:center;">' .$dataSemanas['semana']. '</td>';
														if (isset($complemento)) {
															$fila .= '<td style="text-align:center;">' .$complemento. '</td>';
														}else {
															$fila .= '<td style="text-align:center;">' .''. '</td>';
														}
															
														$data['viernes'] = $data['jueves'] = $data['miércoles'] = $data['martes'] = $data['lunes'] = 0;
														// var_dump($data);
														foreach ($data as $keyD => $valueD) {  // recorremos en los cinco dias 
															$consume = 0;
															foreach ($entregas as $keyE => $valueE) { // recorremos las entregas de la semana
																if (isset($valueE['mes']) && $valueE['mes'] == $dataMeses['mes']) {
																	foreach ($dias as $keyP => $valueP) {
																		if (isset($valueE[$keyP]) && $valueE[$keyP] == 1) {
																			if ($keyD == $dias[$keyP]) {
																				$consume = 1;
																				break;
																			}
																		}
																	}
																}else{
																	$consume = 0;
																}
																$tipoValidacion = $valueE['TipoValidacion'];
															}
															if ($consume == 1) {
																$fila .= '<td style="text-align:center;">' .'x'. '</td>';
															}else{
																$fila .= '<td style="text-align:center;">' .'-'. '</td>';
															}
														}
														if (isset($tipoValidacion)) {
															$fila .= '<td style="text-align:center;">' .$tipoValidacion. '</td>';
														}else{
															$fila .= '<td style="text-align:center;">' .''. '</td>';
														}	
														$fila .= '</tr>';	
													}
													$filas .= $fila;
												}
											}
										}
									}
								?>
							</div>

							<div class="table-responsive">
								<table class="table table-striped table-hover dataTables-sedes">
									<thead>
										<tr>
											<th>Mes</th>
											<th>Semana</th>
											<th>Complemento</th>
											<th>Lunes</th>
											<th>Martes</th>
											<th>Miércoles</th>
											<th>Jueves</th>
											<th>Viernes</th>
											<th>Validación</th>
										</tr>
									</thead>
									<tbody>
										<?php echo $filas; ?>
									</tbody>
									<tfoot>
										<tr>
											<th>Mes</th>
											<th>Semana</th>
											<th>Complemento</th>
											<th>Lunes</th>
											<th>Martes</th>
											<th>Miércoles</th>
											<th>Jueves</th>
											<th>Viernes</th>
											<th>Validación</th>
										</tr>
									</tfoot>
								</table>
							</div>
						</div>
					</div>
				</div>

				<div class="col-lg-12">
            		<div class="ibox float-e-margins">
                		<div class="ibox-title">
                    		<h5>Novedades</h5>
                    		<div class="ibox-tools">
                        		<a class="collapse-link">
                            		<i class="fa fa-chevron-up"></i>
                        		</a>
                        		<a class="dropdown-toggle" data-toggle="dropdown" href="#">
                            		<i class="fa fa-wrench"></i>
                        		</a>
                        		<ul class="dropdown-menu dropdown-user">
                            		<li>
										<a href="#">Config option 1</a>
                            		</li>
                            		<li>
										<a href="#">Config option 2</a>
                            		</li>
                        		</ul>
                        		<a class="close-link">
                            		<i class="fa fa-times"></i>
                        		</a>
                    		</div>
                		</div>

                		<div class="ibox-content inspinia-timeline">
							<?php
								$fecha_hoy = date('Y-m-d H:i:s');
								$consultaNovedad = "SELECT  np.fecha_hora, 
															np.id, 
															u.Ciudad as municipio, 
															s.nom_inst, 
															s.nom_sede, 
															td.Abreviatura,
															np.num_doc_titular, 
															np.tipo_complem, 
															np.semana, 
															np.d1, 
															np.d2, 
															np.d3, 
															np.d4, 
															np.d5, 
															np.observaciones 
														FROM novedades_focalizacion np 
															LEFT JOIN sedes$periodoActual s ON np.cod_sede = s.cod_sede 
															LEFT JOIN tipodocumento td ON np.tipo_doc_titular = td.id 
															LEFT JOIN ubicacion u ON u.CodigoDANE = s.cod_mun_sede 
														WHERE np.num_doc_titular = $numDoc AND np.fecha_hora <= '{$fecha_hoy}' ORDER BY np.id DESC";
								$resultadoNovedades = $Link->query($consultaNovedad);
								if($resultadoNovedades->num_rows > 0){
									while($row = $resultadoNovedades->fetch_assoc()){
										$fecha = new DateTime($row['fecha_hora']);
										$hora = $fecha->format('h:i:s a');
										$fecha = $fecha->format('d/m/Y');
							?>

							<div class="timeline-item">
								<div class="row">
									<div class="col-xs-3 date">
										<i class="fa fa-briefcase"></i>
										<?php echo $fecha; ?>
										<br/>
										<small class="text-navy"><?php echo $hora; ?></small>
									</div>
									<div class="col-xs-7 content no-top-border">
										<p class="m-b-xs"><strong>Novedad de focalización</strong></p>
										<p><?php echo $row['observaciones']; ?></p>
											<div class="table-responsive">
												<table class="table table-striped table-hover dataTables-sedes">
													<thead>
														<th>L</th>
														<th>M</th>
														<th>X</th>
														<th>J</th>
														<th>V</th>
													</thead>
													<tbody>
														<td><?php $aux = $row['d1']; if($aux == 1){ echo "X"; } ?></td>
														<td><?php $aux = $row['d2']; if($aux == 1){ echo "X"; } ?></td>
														<td><?php $aux = $row['d3']; if($aux == 1){ echo "X"; } ?></td>
														<td><?php $aux = $row['d4']; if($aux == 1){ echo "X"; } ?></td>
														<td><?php $aux = $row['d5']; if($aux == 1){ echo "X"; } ?></td>
													</tbody>
													<tfoot>
														<th>L</th>
														<th>M</th>
														<th>X</th>
														<th>J</th>
														<th>V</th>
													</tfoot>
												</table>
											</div>
										</div>
									</div>
								</div>
								<?php
									}
								}

								?>
                			</div>
            			</div>
        			</div>
   				</div>
			</div>
	</div>
</div>

<!-- ventana confirmar cambio de estado -->
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
        		<input type="hidden" id="documentoACambiar">
        		<input type="hidden" id="estadoACambiar">
        		<input type="hidden" id="semanaDeCambio">
        		<button type="button" class="btn btn-danger btn-outline btn-sm" data-dismiss="modal" onclick="revertirEstado();">Cancelar</button>
        		<button type="button" class="btn btn-primary btn-sm" data-dismiss="modal" onclick="cambiarEstado();">Aceptar</button>
      		</div>
    	</div>
  	</div>	
</div>

<form id="editar_titular" action="editar_titular.php" method="post">
	<input type="hidden" name="num_doc_editar" id="num_doc_editar">
	<input type="hidden" name="semana_editar" id="semana_editar">
</form>

<form id="exportar_titular" action="exportar_titular.php" method="post">
	<input type="hidden" name="num_doc_exportar" id="num_doc_exportar">
	<input type="hidden" name="semana" id="semana">
</form>

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
<script src="<?php echo $baseUrl; ?>/theme/js/plugins/toastr/toastr.min.js"></script>
<script src="<?php echo $baseUrl; ?>/theme/js/plugins/validate/jquery.validate.min.js"></script>
<script src="<?php echo $baseUrl; ?>/theme/js/plugins/iCheck/icheck.min.js"></script>
<script src="<?php echo $baseUrl; ?>/theme/js/plugins/steps/jquery.steps.min.js"></script>
<script src="<?php echo $baseUrl; ?>/theme/js/plugins/toggle/toggle.min.js"></script>

<!-- Peity -->
<script src="<?php echo $baseUrl; ?>/theme/js/plugins/peity/jquery.peity.min.js"></script>

<!-- Peity -->
<script src="<?php echo $baseUrl; ?>/theme/js/demo/peity-demo.js"></script>
<script src="<?php echo $baseUrl; ?>/modules/titulares_derecho/js/titulares_derecho.js"></script>
<?php mysqli_close($Link); ?>
</body>
</html>
