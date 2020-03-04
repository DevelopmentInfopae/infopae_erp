<?php
	include '../../config.php';
	$usuario = '';
	$tipoUsuario = '';
	$idUsr = '';
	$fotoUsr = '';
	require_once '../../db/conexion.php';
	include '../../autentication.php';
	include '../../php/funciones.php';
	$idUsr = $_SESSION['id_usuario'];
	$fotoUsr = $_SESSION['foto'];

	$dato_municipio = $Link->query("SELECT CodMunicipio FROM parametros") or die(mysqli_error($Link));
	if ($dato_municipio->num_rows > 0) { $municipio_defecto = $dato_municipio->fetch_array(); }

	$mes="";
	$semana="";
	$dia="";
	$mes = date('m');
	$dia = date('d');
	//$mes = date('n');
	//$dia = date('j');
	$anno = date('Y');
	$dias = array("Domingo","Lunes","Martes","Miércoles","Jueves","Viernes","Sábado");
	$diaNombre = $dias[date("w")];

	//Forzando una fecha OJO solo en desarrollo
	// $mes = '05';
	// $dia = '20';
	// $anno = '2019';

	$consulta = "SELECT semana FROM planilla_semanas WHERE mes = $mes AND dia = $dia LIMIT 1";
	$resultado = $Link->query($consulta) or die ('No se pudo hacer busqueda de la semana. '. mysqli_error($Link));
	if($resultado->num_rows >= 1){
		$row = $resultado->fetch_assoc();
		$semana = $row["semana"];
	}
?>
<!DOCTYPE html>
<html lang="es">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="X-UA-Compatible" content="ie=edge">
	<title>Dashboard Entregas Biometricas</title>
	<link rel="shortcut icon" href="<?php echo $baseUrl; ?>/favicon.ico" />
	<link href="<?php echo $baseUrl; ?>/theme/css/bootstrap.min.css" rel="stylesheet">
	<link href="<?php echo $baseUrl; ?>/theme/font-awesome/css/font-awesome.css" rel="stylesheet">
	
	
	<!-- Toastr style -->
    <link href="<?php echo $baseUrl; ?>/theme/css/plugins/toastr/toastr.min.css" rel="stylesheet">
	
	<!-- CSS de toda la aplicación -->
	<link href="<?php echo $baseUrl; ?>/theme/css/style.css" rel="stylesheet">
	<link rel="stylesheet" href="css/dashboard.css">
</head>
<body>
	<div class="dashboard-top">
		<div class="barra-top">
			<div class="barra-top-contenido">
				<div class="barra-top__left">
					<!-- <button><i class="fa fa-bars"></i></button> -->
					<div class="logo-dashboard">
						<a href="<?= $baseUrl ?>" target="_self"><img alt="entregado" src="<?= $baseUrl ?>/img/logo_b.png" /></a>				
					</div>
				</div>
				<div class="barra-top__center">
					<h1>Consulta en línea de entrega de complementos alimentarios</h1>
				</div>
				<div class="barra-top__right">
					<div class="fecha-hora">
						<?php
							switch ($mes) {
								case 1:
									$mesNombre = "Ene";
									break;
								case 2:
									$mesNombre = "Feb";
									break;
								case 3:
									$mesNombre = "Mar";
									break;
								case 4:
									$mesNombre = "Abr";
									break;
								case 5:
									$mesNombre = "May";
									break;
								case 6:
									$mesNombre = "Jun";
									break;
								case 7:
									$mesNombre = "Jul";
									break;
								case 8:
									$mesNombre = "Ago";
									break;
								case 9:
									$mesNombre = "Sep";
									break;
								case 10:
									$mesNombre = "Oct";
									break;
								case 11:
									$mesNombre = "Nov";
									break;
								case 12:
									$mesNombre = "Dic";
									break;
							}
							// var_dump($mesNombre);
							// var_dump($dia);
							// var_dump($anno);
						?>
						<span class="hora-actual">9:12am</span>
						<span class="fecha-actual">
							<?= $diaNombre ?>, <?= $mesNombre ?> <?= $dia ?> <?//= $anno ?>
						</span>
					</div>
				</div>
			</div>
		</div>

		<div class="contenedor-dashboard">
			<form action="">
				<input type="hidden" id="anno" name="anno" value="<?= $anno ?>">
				<input type="hidden" id="mes" name="mes" value="<?= $mes ?>">
				<input type="hidden" id="dia" name="dia" value="<?= $dia ?>">
				<input type="hidden" id="semana" name="semana" value="<?= $semana ?>">
				<div class="row">
					<div class="col-sm-12 filtro">
						<div class="form-filtro">
							<div class="campo">
								<label for="municipio">Municipio</label>
								<select name="municipio" id="municipio"></select>
							</div>
							<div class="campo">
								<label for="institucion">institución</label>
								<select name="institucion" id="institucion"></select>
							</div>
							<div class="campo">
								<label for="sede">Sede</label>
								<select name="sede" id="sede"></select>
							</div>
							<div class="campo">
								<button type="button" id="btnFiltro">OK</button>
							</div>

						</div>
						<!-- <div class="actualizar">
							<div class="campo">
								<button type="button" id="btnActualizar">Actualizar gráfica</button>
							</div>
						</div> -->


						<div class="totales">
							<div class="totales-contenido">
								<span class="total-entregado">0</span>/<span class="total-entregar">0</span>
							</div>
						</div>





					</div>
				</div>
			</form>

			
			
			
		</div>
		
		
		
			<div class="contenedor-grafica-dashboard">
				<div class="row">
					<div class="col-sm-12 grafica-dashboard">
						<div class="flot-chart">
							<div class="flot-chart-content" id="flot-line-chart-moving"></div>
						</div>
					</div>
				</div>
			</div>







	</div>
	<div class="dashboard-bottom">

			<div class="contenedor-dashboard__row">
				<div class="contenedor-dashboard__col contenedor-dashboard__col-izq">
					<h2>Sedes educativas</h2>
					<div class="overlay"> <i class="fa fa-refresh fa-spin"></i> </div>
					<div class="sedes">
						<?php //for ($i=0; $i < 10; $i++) { ?> 
							<!-- <div class="sede sede-16830700035901"> <div class="sede-top"> <div class="sede-left"> <i class="fa fa-circle"></i> </div> <h5>INSTITUTO INTEGRADO FRANCISCO SERRANO MUÑOZ - SEDE PRINCIPAL</h5> <h2 class="no-margins"> <span class="entregado entregado-16830700035901">0</span> / <span class="total">160</span></h2> </div> <div class="sede-bottom"> <div class="sede-left"> <div class="sede-hora-inicio"> 3:54 pm </div> </div> <div class="progress progress-mini"> <div style="width: 0%;" class="progress-bar"></div> </div> </div> </div> -->
						<?php //} ?> 
					</div>
				</div>
				<div class="contenedor-dashboard__col contenedor-dashboard__col-der">
					<h2>Titulares de derecho</h2>
					<div class="entregas">
						<?php //for ($i=0; $i < 10; $i++) { ?> 
							<!-- <div class="entrega"> <i class="fa fa-check-circle"></i> <span class="hora-estudiante">15:57:32</span> <div class="estudiante"> <h2><span class="estudiante--nombre">JOHAN AGUILAR</span> </h2> <span class="estudiante--sede">I.E. COLEGIO MARIO MORALES DELGADO - SEDE PRINCIPAL</span> </div> <div class="estudiante--validacion radiofrecuencia">Lector Huella Dactilar</div> </div> -->
						<?php //} ?> 
							
		
							
							
							
					</div>
				</div>
			</div>

	</div>
	











	<!-- Mainly scripts -->
	<script src="<?php echo $baseUrl; ?>/theme/js/jquery-3.1.1.min.js"></script>
	<script src="<?php echo $baseUrl; ?>/theme/js/bootstrap.min.js"></script>
	<script src="<?php echo $baseUrl; ?>/theme/js/plugins/metisMenu/jquery.metisMenu.js"></script>
	<script src="<?php echo $baseUrl; ?>/theme/js/plugins/slimscroll/jquery.slimscroll.min.js"></script>

	<!-- Custom and plugin javascript -->
	<script src="<?php echo $baseUrl; ?>/theme/js/plugins/toastr/toastr.min.js"></script>

	<!-- Flot -->
	<script src="<?php echo $baseUrl; ?>/theme/js/plugins/flot/jquery.flot.js"></script>
	<script src="<?php echo $baseUrl; ?>/theme/js/plugins/flot/jquery.flot.tooltip.min.js"></script>
	<script src="<?php echo $baseUrl; ?>/theme/js/plugins/flot/jquery.flot.resize.js"></script>
	<script src="<?php echo $baseUrl; ?>/theme/js/plugins/flot/jquery.flot.pie.js"></script>
	<script src="<?php echo $baseUrl; ?>/theme/js/plugins/flot/jquery.flot.time.js"></script>

	<!-- Custom and plugin javascript -->
	<script src="<?php echo $baseUrl; ?>/modules/entregas_biometricas/js/dashboard.js?v=<?= $cacheBusting; ?>"></script>

</body>
</html>