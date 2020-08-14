<?php
$titulo = 'Titular en derecho';
require_once '../../header.php';
set_time_limit (0);
ini_set('memory_limit','6000M');

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


$consulta = " SELECT f.num_doc, f.activo, t.Abreviatura AS tipo_doc, CONCAT(f.nom1, ' ', f.nom2, ' ', f.ape1, ' ', f.ape2) AS nombre, f.genero, g.nombre AS grado, f.nom_grupo, jor.nombre AS jornada, f.edad, f.Tipo_complemento, s.nom_sede, s.nom_inst, f.* FROM focalizacion$semana AS f LEFT JOIN tipodocumento AS t ON t.id = f.tipo_doc LEFT JOIN grados AS g ON g.id = f.cod_grado LEFT JOIN jornada AS jor ON jor.id = f.cod_jorn_est LEFT JOIN sedes$periodoActual AS s on s.cod_sede = f.cod_sede WHERE f.num_doc = '$numDoc' AND t.Abreviatura = '$tipoDoc' ORDER BY f.nom1 ASC ";

$resultado = $Link->query($consulta) or die ('Unable to execute query de focalización. '. mysqli_error($Link));
if($resultado->num_rows >= 1){
$row = $resultado->fetch_assoc();
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
		<div class="col-lg-4">
		  	<div class="title-action">
				<div class="dropdown pull-right">
			        <button class="btn btn-primary" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true">  Acciones <span class="caret"></span>
			        </button>
			        <ul class="dropdown-menu pull-right" aria-labelledby="dropdownMenu1">
			        <?php if ($_SESSION['perfil'] == 1) : ?>
			          <li><a  onclick="editarTitular(<?php echo $numDoc; ?>)"><span class="fa fa-pencil"></span> Editar </a></li>
		          	<?php if ($row['activo'] == 1): ?>
                    	<li data-idtitular="<?php echo $numDoc; ?>" data-accion="1"><a> Estado : <input class="form-control" type="checkbox" data-toggle="toggle" data-size="mini" data-on="Activo" data-off="Inactivo" data-width="74px" checked></a></li>
                   	<?php elseif($row['activo'] == 0): ?>
                    	<li data-idtitular="<?php echo $numDoc;  ?>" data-accion="0"><a> Estado : <input class="form-control" type="checkbox" data-toggle="toggle" data-size="mini" data-on="Activo" data-off="Inactivo" data-width="74px" ></a></li>
                    <?php endif ?>
			        <?php else: ?>
			       	  <?php if ($row['activo'] == 1): ?>
			          	<li><a><span class="fa fa-check"></span> Estado : <b>Activo</b></a></li>
			          <?php elseif ($row['activo'] == 0): ?>
			          	<li><a></span> Estado : <b>Inactivo</b></a></li>
			          <?php endif ?>
			        <?php endif ?>

			          <li><a href="#" ><span class="fa fa-file-excel-o"></span> Exportar </a></li>
			        </ul>
		      	</div>
	 		</div>
		</div>
	</div>
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

						<p style="text-align: center;">
							<span style="width: 20px; display: inline-block; text-align: center;"><i class="fa fa-id-card-o"></i></span><strong>Documento</strong>
						<br> <?php echo $tipoDoc;  ?> # <?php echo $numDoc;  ?> </p>

						<p style="text-align: center;">
							<span style="width: 20px; display: inline-block; text-align: center;"><i class="fa fa-graduation-cap"></i></span><strong>Curso</strong>
				<br>
							<?php echo $row['grado']; ?> <?php echo $row['nom_grupo']; ?>
						</p>



						<p style="text-align: center;">
							<span style="width: 20px; display: inline-block; text-align: center;"><i class="fa <?php echo $icono; ?>"></i></span><strong>Género</strong>
					<br>
							<?php echo ucwords($sexo); ?>
						</p>

						<p style="text-align: center;">
							<span style="width: 20px; display: inline-block; text-align: center;"><i class="fa fa-clock-o"></i></span><strong>Jornada</strong>
					<br>
							<?php echo $row['jornada']; ?>
						</p>

						<p style="text-align: center;">
							<span style="width: 20px; display: inline-block; text-align: center;"><i class="fa fa-child"></i></span><strong>Edad</strong>
						<br>
							<?php echo $row['edad']; ?> años<br>
						</p>


						<p style="text-align: center;">
							<span style="width: 20px; display: inline-block; text-align: center;"><i class="fa fa-home"></i></span><strong>Estrato</strong>

						<br>
							<?php echo $row['cod_estrato']; ?>
						</p>
						<!-- <div class="row m-t-lg">
							<div class="col-md-4">
								<span class="bar">5,3,9,6,5,9,7,3,5,2</span>
								<h5><strong>169</strong> Entregas</h5>
							</div>
							<div class="col-md-4">
								<span class="line">5,3,9,6,5,9,7,3,5,2</span>
								<h5><strong>28</strong> Novedades</h5>
							</div>
							<div class="col-md-4">
								<span class="bar">5,3,2,-1,-3,-2,2,3,5,2</span>
								<h5><strong>240</strong> Ausencias</h5>
							</div>
						</div> -->
						<div class="user-button">
							<!-- <div class="row">
								<div class="col-md-6">
									<button type="button" class="btn btn-primary btn-sm btn-block"><i class="fa fa-envelope"></i> Send Message</button>
								</div>
								<div class="col-md-6">
									<button type="button" class="btn btn-default btn-sm btn-block"><i class="fa fa-coffee"></i> Buy a coffee</button>
								</div>
							</div> -->
						</div>
					</div>
			</div>
		</div>
			</div>




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
						<!-- <a class="dropdown-toggle" data-toggle="dropdown" href="#">
							<i class="fa fa-wrench"></i>
						</a>
						<ul class="dropdown-menu dropdown-user">
							<li><a href="#">Config option 1</a>
							</li>
							<li><a href="#">Config option 2</a>
							</li>
						</ul> -->
						<a class="close-link">
							<i class="fa fa-times"></i>
						</a>
					</div>
				</div>
				<div class="ibox-content">
					<div>
						<?php
						// Variables a utilizar
						$tipoDoc = $tipoDoc;
						$numDoc = $numDoc;
						$filas = "";
						$fila = "";
						$semana = array();
						$indiceDia = -1;
						$mesActual = 0;
						$semanaActual = 0;
						$ultimoMes = 0;

						$semana['semana'] = '';
						$semana['complemento'] = '';
						$semana['lunes'] = '';
						$semana['martes'] = '';
						$semana['miércoles'] = '';
						$semana['jueves'] = '';
						$semana['viernes'] = '';
						$semana['validacion'] = '';

						$mesesEntregas = array();
						$vsql="SELECT TABLE_NAME as mes FROM information_schema.TABLES WHERE  table_schema = '$Database' AND   TABLE_NAME LIKE 'entregas_res_%'";
						$result = $Link->query($vsql) or die ('Unable to execute query. '. mysqli_error($Link));
						while($row = $result->fetch_assoc()) {
							$aux = $row['mes'];
							$aux = substr($aux, 13, -2);
							$mesesEntregas[] = $aux;
						}

						//Consulta planilla semanas
						$consulta = " select * from planilla_semanas WHERE MES <= ".date('m')." AND DIA <= ".date('d')."";
						// $consulta = " select * from planilla_semanas";
						$diasCobertura = array();
						$entregas = array();
						$resultado = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));
						if($resultado->num_rows >= 1){
							while($row = $resultado->fetch_assoc()){
								$diasCobertura[] = $row;
							}
						}

						//Buscando las entregas
						foreach ($mesesEntregas as $mesEntregas) {
							$consulta = " select * from entregas_res_$mesEntregas$periodoActual where tipo_doc_nom = '$tipoDoc' and num_doc = '$numDoc' order by id ";
							$resultado = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));
							if($resultado->num_rows >= 1){
								while($row = $resultado->fetch_assoc()){
									$row['mes'] = $mesEntregas;
									$entregas[] = $row;
								}
							}
						}

						for ($i=1; $i <= $ultimoMes ; $i++) {
							if($i < 10){
								$aux = '0'.$i;
							}else{
								$aux = $i;
							}
							// //$consulta = " select * from entregas_res_$aux$periodoActual where tipo_doc_nom = '$tipoDoc' and num_doc = '$numDoc' order by id ";
							// ////echo "<br><br>$consulta<br><br>";
							// //$resultado = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));
							// if($resultado->num_rows >= 1){
							// 	while($row = $resultado->fetch_assoc()){
							// 		$row['mes'] = $i;
							// 		$entregas[] = $row;
							// 	}
							// }
						}

						//var_dump($entregas);

						$semanaActual = 0;
						$mesActual = 0;
						$otroRegistro = 0;


						foreach ($entregas as $key => $val) {



								$entregasMes = $entregas[$key];
								for ($i=1; $i < 22 ; $i++) {
									$indiceDia++;

									if(isset($diasCobertura[$indiceDia])){
										$diaCobertura = $diasCobertura[$indiceDia];
									}



									//var_dump($diaCobertura);
									$aux = 'D'.$i;
									$varlorEntrega = $entregasMes[$aux];

									//var_dump($varlorEntrega);
									if($varlorEntrega == 0){
										if(isset($entregas[$key+1])){
											$entregasMesOtroRegistro = $entregas[$key+1];
											if($entregasMes['mes'] == $entregasMesOtroRegistro['mes'] ){
												//echo "<br> Otro registro del mismo mes";
												if($entregasMesOtroRegistro[$aux] == 1){
													//echo "<br> Otro registro del mismo mes positivo";
													$otroRegistro = 1;
													//$semana[$diaCobertura['NOMDIAS']] = 'X';
												}
											}
										}
									}

									//echo "<br>".$entregasMes['mes']."/".$aux." - ".intval($diaCobertura['MES'])." Semana: ".$semanaActual;

									if($entregasMes['mes'] != intval($diaCobertura['MES'])){
										$indiceDia--;
										break;
									}





										if($semanaActual != $diaCobertura['SEMANA']){
											if($semanaActual != 0){
												//echo "<br>Nueva semana";
												//var_dump($semana);
												//var_dump($indiceDia);
												$fila = '';
												$fila .= '<tr>';
												$fila .= '<td style="text-align:center;">'.$semana['semana'].'</td>';
												$fila .= '<td style="text-align:center;">'.$semana['complemento'].'</td>';
												$fila .= '<td style="text-align:center;">'.$semana['lunes'].'</td>';
												$fila .= '<td style="text-align:center;">'.$semana['martes'].'</td>';
												$fila .= '<td style="text-align:center;">'.$semana['miércoles'].'</td>';
												$fila .= '<td style="text-align:center;">'.$semana['jueves'].'</td>';
												$fila .= '<td style="text-align:center;">'.$semana['viernes'].'</td>';
												$fila .= '<td style="text-align:center;">'.$semana['validacion'].'</td>';
												$fila .= '</tr>';
												$filas .= $fila;
											}
											$semanaActual = $diaCobertura['SEMANA'];
											$semana['semana'] = $semanaActual;
											$semana['complemento'] = $entregasMes['tipo_complem'];
											$semana['lunes'] = '';
											$semana['martes'] = '';
											$semana['miércoles'] = '';
											$semana['jueves'] = '';
											$semana['viernes'] = '';
											$semana['validacion'] = $entregasMes['TipoValidacion'];
										}
										//echo "<br>".$varlorEntrega;
										//echo "<br>".$semanaActual;
										//var_dump($diaCobertura);

									if($varlorEntrega == 1){
										$semana[$diaCobertura['NOMDIAS']] = 'X';
									}

								}



								//$diasCobertura
								//$indiceDia
								//$semana
								//$fila


						}
						// Al terminar el proceso debemos recoger los valores de la ultima semana
						//var_dump($semana);
						//var_dump($indiceDia);
						$fila = '';
						$fila .= '<tr>';
						$fila .= '<td style="text-align:center;">'.$semana['semana'].'</td>';
						$fila .= '<td style="text-align:center;">'.$semana['complemento'].'</td>';
						$fila .= '<td style="text-align:center;">'.$semana['lunes'].'</td>';
						$fila .= '<td style="text-align:center;">'.$semana['martes'].'</td>';
						$fila .= '<td style="text-align:center;">'.$semana['miércoles'].'</td>';
						$fila .= '<td style="text-align:center;">'.$semana['jueves'].'</td>';
						$fila .= '<td style="text-align:center;">'.$semana['viernes'].'</td>';
						$fila .= '<td style="text-align:center;">'.$semana['validacion'].'</td>';
						$fila .= '</tr>';
						$filas .= $fila;








//
// 						//var_dump($diasCobertura);
// 						foreach ($diasCobertura as $diaCobertura) {
// 							//var_dump($diaCobertura);
// 							if($mesActual != $diaCobertura['MES']){
// 								$mesActual = $diaCobertura['MES'];
// 								// Validando que haya entregas
// 								$consulta = " show tables like 'entregas_res_$mesActual$periodoActual' ";
// 								$resultado = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));
// 								if($resultado->num_rows >= 1){
// 									//Entregas al estudiante
// 									$consulta2 = " select * from entregas_res_$mesActual$periodoActual where num_doc = '$numDoc' and tipo_doc_nom = '$tipoDoc' ";
// 									$resultado2 = $Link->query($consulta2) or die ('Unable to execute query. '. mysqli_error($Link));
// 									if($resultado2->num_rows >= 1){
// 										while($row = $resultado2->fetch_assoc()){
// 											$entregasMes[] = $row;
// 										}
// 									}
// 								}else{
// 									break;
// 								}
// 							}
// 							if($semanaActual != $diaCobertura['SEMANA']){
// 								$filas .= $fila;
// 								$fila = '';
// 							}

// $fila .= "<tr>";
// $fila .= "<td>";
// $fila .= $diaCobertura['SEMANA'];
// $fila .= "</td>";
// $fila .= "<td>";
// $fila .= $row['tipo_complem'];
// $fila .= "</td>";
// $fila .= "<td>";
// $fila .= $diaCobertura['SEMANA'];
// $fila .= "</td>";
// $fila .= "<td>";
// $fila .= $diaCobertura['SEMANA'];
// $fila .= "</td>";
// $fila .= "<td>";
// $fila .= $diaCobertura['SEMANA'];
// $fila .= "</td>";
// $fila .= "<td>";
// $fila .= $diaCobertura['SEMANA'];
// $fila .= "</td>";
// $fila .= "<td>";
// $fila .= $diaCobertura['SEMANA'];
// $fila .= "</td>";
// $fila .= "<td>";
// $fila .= $diaCobertura['SEMANA'];
// $fila .= "</td>";
// $fila .= "</tr>";





















							// Cuando no hay cambio de mes se realiza el analisis día con día

							// $entregaMes = $entregasMes[0];



							//echo "<br>".$fila."<br>";

						//}

						// var_dump($diasCobertura);
						// var_dump($entregasMes);



						?>
					</div>
					<div class="table-responsive">
						<table class="table table-striped table-hover dataTables-sedes">
							<thead>
								<tr>
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
                    <!-- <span class="label label-primary">Meeting today</span> -->
                    <div class="ibox-tools">
                        <a class="collapse-link">
                            <i class="fa fa-chevron-up"></i>
                        </a>
                        <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                            <i class="fa fa-wrench"></i>
                        </a>
                        <ul class="dropdown-menu dropdown-user">
                            <li><a href="#">Config option 1</a>
                            </li>
                            <li><a href="#">Config option 2</a>
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
									$consultaNovedad = "SELECT 
																np.fecha_hora, 
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
														// echo $consultaNovedad;
									$resultadoNovedades = $Link->query($consultaNovedad);
									if($resultadoNovedades->num_rows > 0){
										while($row = $resultadoNovedades->fetch_assoc()){
										 //var_dump($row);
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

<form id="editar_titular" action="editar_titular.php" method="post">
	<input type="hidden" name="num_doc_editar" id="num_doc_editar">
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

<!-- Page-Level Scripts -->


<?php mysqli_close($Link); ?>
</body>
</html>
