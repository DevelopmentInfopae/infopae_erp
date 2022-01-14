<?php
	include '../../header.php';
	include 'functions/fn_fecha_asistencia.php';
	set_time_limit (0);
	ini_set('memory_limit','6000M');

	$periodoActual = $_SESSION["periodoActual"];
	$titulo = "Control Tablets";
	$institucionNombre = "";

	$dia = $diaAsistencia;
	$mes = $mesAsistencia;
	$anno = $annoasistencia;
	$anno2d = $annoAsistencia2D;




	$validacion = "tablet";
	$semanaActual = "";
	$municipio = "";
	$institucion = "";
	$sede = "";

	if(isset($_GET['mes']) && $_GET['mes'] != ''){
		$mes = mysqli_real_escape_string($Link, $_GET['mes']);
	}
	if(isset($_GET['semana']) && $_GET['semana'] != ''){
		$semanaActual = mysqli_real_escape_string($Link, $_GET['semana']);
	}else{
		//Busqueda de la semana actual
		$consulta = "select semana from planilla_semanas where ano = \"$anno\" and mes = \"$mes\" and dia = \"$dia\" ";
		//var_dump($consulta);				
		$resultado = $Link->query($consulta) or die ('No se pudo cargar la semana actual. '. mysqli_error($Link));
		if($resultado->num_rows >= 1){
			$row = $resultado->fetch_assoc();
			$semanaActual = $row["semana"];
		}
		//var_dump($semanaActual);
	}
	if(isset($_GET['dia']) && $_GET['dia'] != ''){
		$dia = mysqli_real_escape_string($Link, $_GET['dia']);
	}	
	if(isset($_GET['municipio']) && $_GET['municipio'] != ''){
		$municipio = mysqli_real_escape_string($Link, $_GET['municipio']);
	}	
	if(isset($_GET['institucion']) && $_GET['institucion'] != ''){
		$institucion = mysqli_real_escape_string($Link, $_GET['institucion']);
	}
	if(isset($_GET['sede']) && $_GET['sede'] != ''){
		$sede = mysqli_real_escape_string($Link, $_GET['sede']);
	}

	$mesTablaAsistencia = $mes;
	$annoTablaAsistencia = $anno2d;
	include 'functions/fn_validar_existencias_tablas.php';





				
?>

<link rel="stylesheet" href="css/custom.css?v=<?= $cacheBusting; ?>">

<div class="row wrapper wrapper-content border-bottom white-bg page-heading">
	<div class="col-xs-8">
			<h2>Control Tablets</h2>
			<ol class="breadcrumb">
					<li>
							<a href="<?php echo $baseUrl; ?>">Inicio</a>
					</li>
					<li class="active">
							<strong><?php echo $titulo; ?></strong>
					</li>
			</ol>
	</div>
	<div class="col-xs-4">
			<div class="title-action">
				<!-- <button class="btn btn-primary btnGuardar" type="button">Guardar</button> -->




	<?php if($_SESSION["perfil"] == 1 || $_SESSION["perfil"] == 0) { ?>
					<!-- <a href="#" class="btn btn-primary" onclick="crearSede();"><i class="fa fa-plus"></i> Nueva</a> -->
	<?php } ?>
	<!-- <button class="btn btn-primary" id="btnRestablecerContadores">Restablecer almacenamiento local</button> -->
			</div>
	</div>
</div>
<!-- /.row wrapper de la cabecera de la seccion -->





<?php include "filtro_control.php"; ?>

<?php if( count($_GET) > 0) { ?>
	<div class="wrapper wrapper-content  animated fadeInRight">
		<div class="row">
			<div class="col-sm-12">









				<div class="sedes">
					<?php
						








						// Consulta que recorre todas las sedes validadas con tableta y trae si estan selladas, total de estudiantes y total entregado.
						$consulta = " SELECT DISTINCT(s.cod_sede), s.nom_sede, s.cod_inst,  s.nom_inst,  a.estado AS sellado , (select count(DISTINCT f.num_doc) AS total from focalizacion$semanaActual f WHERE f.cod_sede = s.cod_sede ) AS total, (SELECT SUM(a2.consumio + a2.repitio) AS cantidad FROM focalizacion$semanaActual f2 left join asistencia_det$mes$periodoActual a2 ON f2.tipo_doc = a2.tipo_doc AND f2.num_doc = a2.num_doc WHERE f2.cod_sede = s.cod_sede AND a2.consumio IS not NULL and a2.dia = $dia ) AS entregado FROM sedes$periodoActual s LEFT JOIN asistencia_enc$mes$periodoActual a ON s.cod_sede = a.cod_sede and a.dia = $dia WHERE s.tipo_validacion = \"tablet\" ";
							if($municipio != ""){
								$consulta .= " and s.cod_mun_sede = \"$municipio\" ";	
							}
							if($institucion != ""){
								$consulta .= " and s.cod_inst = \"$institucion\" ";	
							}
							if($sede != ""){
								$consulta .= " and s.cod_sede = \"$sede\" ";	
							}
						//echo $consulta;




						$resultado = $Link->query($consulta) or die ('Carga de sedes:<br>'.$consulta.'<br>'. mysqli_error($Link));
						if($resultado->num_rows >= 1){
							while($row = $resultado->fetch_assoc()){
								$codSede = $row["cod_sede"]; 
								$nombreSede = $row["nom_sede"]; 
								$sellado = $row["sellado"]; 
								$total = $row["total"]; 
								$entregado = $row["entregado"]; 
								//var_dump($sellado);
								if($entregado == null || $entregado == ""){
									$entregado = 0;
								}
								$porcentaje = ($entregado / $total) * 100;
								$claseSede = "text-rojo";
								if($sellado == 2){
									$claseSede = "text-verde";
								} else if($entregado > 0){
									$claseSede = "text-naranja";
								}

							?>
								<div class="ibox">
									<div class="ibox-title">
										<h5><i class="fa fa-circle <?= $claseSede ?>"></i><?= $nombreSede ?></h5> 					
										<div class="ibox-tools">
											<div class="headerTools">
												<div class="headerToolsTotal">
													<h2 class="no-margins"> <span class="entregado"><?= $entregado ?></span> / <span class="total"><?= $total ?></span></h2>
													<div class="progress progress-mini">
														<div style="width: <?= $porcentaje ?>%;" class="progress-bar"></div>
													</div>
												</div>
												<div class="collapse-link">
													<i class="fa fa-chevron-down"></i>
												</div>
											</div>
										</div>
									</div>
									<div class="ibox-content">
										<div class="grupos">
											<?php
												// Detalle de lo entregado en cada uno de los grupos
												$consulta2 = "SELECT f.cod_grado, g.nombre, f.nom_grupo , count(num_doc) AS total ,(SELECT sum(a.consumio + a.repitio) AS cantidad FROM focalizacion$semanaActual f2 left join asistencia_det$mes$periodoActual a ON f2.tipo_doc = a.tipo_doc AND f2.num_doc = a.num_doc WHERE f2.cod_sede = $codSede AND a.consumio IS not NULL and a.dia = \"$dia\" AND f2.nom_grupo = f.nom_grupo GROUP BY f2.nom_grupo ) AS entregado FROM focalizacion$semanaActual f left join grados g on g.id = f.cod_grado WHERE f.cod_sede = $codSede GROUP BY nom_grupo "; 
												//echo $consulta2;

												$resultado2 = $Link->query($consulta2) or die ('Detalle de cada uno de los grupos:<br>'.$consulta2.'<br>'. mysqli_error($Link));
												if($resultado2->num_rows >= 1){
													while($row2 = $resultado2->fetch_assoc()){
														$nomGrado = $row2['nombre'];	
														$nomGrupo = $row2['nom_grupo'];	
														$totalGrupo = $row2['total'];	
														$entregadoGrupo = $row2['entregado'];	
														if($entregadoGrupo == null || $entregadoGrupo == ""){
															$entregadoGrupo = 0;
														}
														$porcentaje = ($entregadoGrupo / $totalGrupo) * 100;
														$claseGrupo = "text-rojo";
														if($sellado == 2){
															$claseGrupo = "text-verde";
														} else if($entregadoGrupo > 0){
															$claseGrupo = "text-naranja";
														}
											?>
														<div class="grupo">
															<div class="grupoLeft">
																<i class="fa fa-circle <?= $claseGrupo ?>"></i>
																<p><?= $nomGrado ?> - <?= $nomGrupo ?></p>	
															</div>
															<div class="grupoRight">
																<p><?= $entregadoGrupo ?> / <?= $totalGrupo ?> </p>	
																<div class="progress progress-mini">
														<div style="width: <?= $porcentaje ?>%;" class="progress-bar"></div>
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
							<?php
							}
						}
					?>
				</div>
















			</div>
		</div><!-- /.row -->
	</div>
<?php } ?>
<?php include '../../footer.php'; ?>

<!-- Mainly scripts -->
<script src="<?php echo $baseUrl; ?>/theme/js/jquery-3.1.1.min.js"></script>
<script src="<?php echo $baseUrl; ?>/theme/js/bootstrap.min.js"></script>
<script src="<?php echo $baseUrl; ?>/theme/js/plugins/metisMenu/jquery.metisMenu.js"></script>
<script src="<?php echo $baseUrl; ?>/theme/js/plugins/slimscroll/jquery.slimscroll.min.js"></script>

<!-- Custom and plugin javascript -->
<script src="<?php echo $baseUrl; ?>/theme/js/inspinia.js"></script>
<script src="<?php echo $baseUrl; ?>/theme/js/plugins/pace/pace.min.js"></script>
<script src="<?php echo $baseUrl; ?>/theme/js/plugins/dataTables/datatables.min.js"></script>
<script src="<?php echo $baseUrl; ?>/theme/js/plugins/jasny/jasny-bootstrap.min.js"></script>
<script src="<?php echo $baseUrl; ?>/theme/js/plugins/validate/jquery.validate.min.js"></script>
<script src="<?php echo $baseUrl; ?>/theme/js/plugins/toggle/toggle.min.js"></script>
<script src="<?php echo $baseUrl; ?>/theme/js/plugins/toastr/toastr.min.js"></script>
<script src="<?php echo $baseUrl; ?>/theme/js/plugins/iCheck/icheck.min.js"></script>
<script src="<?php echo $baseUrl; ?>/modules/asistencias/js/control_tablets.js?v=<?= $cacheBusting; ?>"></script>
<script src="<?php echo $baseUrl; ?>/modules/asistencias/js/filtro.js?v=<?= $cacheBusting; ?>"></script>
</body>
</html>
