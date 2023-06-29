<?php
  	include '../../header.php';
	require_once 'functions/fn_fecha_asistencia.php';
  	set_time_limit (0);
  	ini_set('memory_limit','6000M');
  	$periodoActual = $_SESSION['periodoActual'];

  	if ($permisos['asistencia'] == "0") {
?>		<script type="text/javascript">
  			window.open('<?= $baseUrl ?>', '_self');
		</script>
<?php 
	exit(); 
	}

	else {
?>		<script type="text/javascript">
      		const list = document.querySelector(".li_asistencia");
      		list.className += " active ";
			const list2 = document.querySelector(".li_tablets");
      		list2.className += " active ";
    	</script>
<?php
  	}

	$periodoActual = $_SESSION["periodoActual"];
	$titulo = "Control Tablets";
	$institucionNombre = "";
	$dia = $diaAsistencia;
	$mes = $mesAsistencia;
	$anno = $annoasistencia;
	$anno2d = $annoAsistencia2D;
	$validacion = "Tablet";
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
		$consulta = "SELECT semana FROM planilla_semanas WHERE ano = \"$anno\" AND mes = \"$mes\" AND dia = \"$dia\" ";		
		$resultado = $Link->query($consulta) or die ('No se pudo cargar la semana actual. '. mysqli_error($Link));
		if($resultado->num_rows >= 1){
			$row = $resultado->fetch_assoc();
			$semanaActual = $row["semana"];
		}
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

	$nameLabel = get_titles('asistencia', 'tablets', $labels);
	$titulo = $nameLabel;
?>

<link rel="stylesheet" href="css/custom.css?v=<?= $cacheBusting; ?>">

<div class="row wrapper wrapper-content border-bottom white-bg page-heading">
	<div class="col-xs-8">
		<h2><?= $titulo ?></h2>
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
	</div>
</div>

<?php include "filtro_control.php"; ?>

<?php if( count($_GET) > 0) { ?>
	<div class="wrapper wrapper-content  animated fadeInRight">
		<div class="row">
			<div class="col-sm-12">
				<div class="sedes">
					<?php
						// Consulta que recorre todas las sedes validadas con tableta y trae si estan selladas, total de estudiantes y total entregado.
						$consulta = " SELECT DISTINCT(s.cod_sede), 
											s.nom_sede,
											s.cod_inst,  
											s.nom_inst,  
											a.estado AS sellado , 
											(SELECT COUNT(DISTINCT f.num_doc) AS total 
												FROM focalizacion$semanaActual f 
												WHERE f.cod_sede = s.cod_sede ) AS total, 
											(SELECT SUM(a2.consumio + a2.repitio) AS cantidad  
												FROM focalizacion$semanaActual f2 
												LEFT JOIN asistencia_det$mes$periodoActual a2 ON f2.tipo_doc = a2.tipo_doc AND f2.num_doc = a2.num_doc 
												WHERE f2.cod_sede = s.cod_sede AND a2.consumio IS not NULL and a2.dia = $dia ) AS entregado 
										FROM sedes$periodoActual s 
										LEFT JOIN asistencia_enc$mes$periodoActual a ON s.cod_sede = a.cod_sede and a.dia = $dia 
										WHERE s.tipo_validacion = \"Tablet\" ";
						if($municipio != ""){
							$consulta .= " and s.cod_mun_sede = \"$municipio\" ";	
						}
						if($institucion != ""){
							$consulta .= " and s.cod_inst = \"$institucion\" ";	
						}
						if($sede != ""){
							$consulta .= " and s.cod_sede = \"$sede\" ";	
						}
						$resultado = $Link->query($consulta) or die ('Carga de sedes:<br>'.$consulta.'<br>'. mysqli_error($Link));
						if($resultado->num_rows >= 1){
							while($row = $resultado->fetch_assoc()){
								$codSede = $row["cod_sede"]; 
								$nombreSede = $row["nom_sede"]; 
								$sellado = $row["sellado"]; 
								$total = $row["total"]; 
								$entregado = $row["entregado"]; 
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
												$consulta2 = "SELECT 	f.cod_grado, 
																		g.nombre, 
																		f.nom_grupo, 
																		COUNT(num_doc) AS total,
																		(SELECT sum(a.consumio + a.repitio) AS cantidad 
																			FROM focalizacion$semanaActual f2 
																			LEFT JOIN asistencia_det$mes$periodoActual a ON f2.tipo_doc = a.tipo_doc AND f2.num_doc = a.num_doc 
																			WHERE f2.cod_sede = $codSede AND a.consumio IS not NULL and a.dia = \"$dia\" 
																				AND f2.nom_grupo = f.nom_grupo 
																			GROUP BY f2.nom_grupo ) AS entregado 
																	FROM focalizacion$semanaActual f 
																	LEFT JOIN grados g ON g.id = f.cod_grado 
																	WHERE f.cod_sede = $codSede GROUP BY nom_grupo "; 
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
<script src="<?= $baseUrl; ?>/theme/js/jquery-3.1.1.min.js"></script>
<script src="<?= $baseUrl; ?>/theme/js/bootstrap.min.js"></script>
<script src="<?= $baseUrl; ?>/theme/js/plugins/metisMenu/jquery.metisMenu.js"></script>
<script src="<?= $baseUrl; ?>/theme/js/plugins/slimscroll/jquery.slimscroll.min.js"></script>

<!-- Custom and plugin javascript -->
<script src="<?= $baseUrl; ?>/theme/js/inspinia.js"></script>
<script src="<?= $baseUrl; ?>/theme/js/plugins/pace/pace.min.js"></script>
<script src="<?= $baseUrl; ?>/theme/js/plugins/dataTables/datatables.min.js"></script>
<script src="<?= $baseUrl; ?>/theme/js/plugins/jasny/jasny-bootstrap.min.js"></script>
<script src="<?= $baseUrl; ?>/theme/js/plugins/validate/jquery.validate.min.js"></script>
<script src="<?= $baseUrl; ?>/theme/js/plugins/toggle/toggle.min.js"></script>
<script src="<?= $baseUrl; ?>/theme/js/plugins/toastr/toastr.min.js"></script>
<script src="<?= $baseUrl; ?>/theme/js/plugins/iCheck/icheck.min.js"></script>
<script src="<?= $baseUrl; ?>/theme/js/plugins/select2/select2.full.min.js"></script>
<script src="<?= $baseUrl; ?>/modules/asistencias/js/control_tablets.js?v=<?= $cacheBusting; ?>"></script>
</body>
</html>
