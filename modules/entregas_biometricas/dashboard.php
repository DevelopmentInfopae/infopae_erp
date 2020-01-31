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
	<!-- CSS de toda la aplicación -->
	<link href="<?php echo $baseUrl; ?>/theme/css/style.css" rel="stylesheet">
	<link rel="stylesheet" href="css/dashboard.css">
</head>
<body>
	<div class="barra-top">
		<div class="barra-top__left">
			<button><i class="fa fa-bars"></i></button>
			<div class="logo-dashboard"><img alt="entregado" src="<?= $baseUrl ?>/img/logo_b.png" /></div>
		</div>
		<div class="barra-top__center">
			<h1>Consulta en línea de entrega de complementos alimentarios</h1>
		</div>
		<div class="barra-top__right">
			<div class="fecha-hora">
				<?php
					$mes = date('n');
					$dia = date('j');
					$anno = date('Y');
					switch ($mes) {
						case 1:
							$mes = "Enero";
							break;
						case 2:
							$mes = "Febrero";
							break;
						case 3:
							$mes = "Marzo";
							break;
						case 4:
							$mes = "Abril";
							break;
						case 5:
							$mes = "Mayo";
							break;
						case 6:
							$mes = "Junio";
							break;
						case 7:
							$mes = "Julio";
							break;
						case 8:
							$mes = "Agosto";
							break;
						case 9:
							$mes = "Septiembre";
							break;
						case 10:
							$mes = "Octubre";
							break;
						case 11:
							$mes = "Noviembre";
							break;
						case 12:
							$mes = "Diciembre";
							break;
					}
					// var_dump($mes);
					// var_dump($dia);
					// var_dump($anno);
				?>
				<?= $mes ?> <?= $dia ?> de <?= $anno ?>
				<span class="hora-actual">9:12am</span>
			</div>
		</div>
	</div>

	<div class="contenedor-dashboard">
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
				<div class="actualizar">
					<div class="campo">
						<button type="button" id="btnActualizar">Actualizar gráfica</button>
					</div>
				</div>
			</div>
		</div>

		<div class="row">
			<div class="col-sm-12 grafica-dashboard">
				<div class="flot-chart">
					<div class="flot-chart-content" id="flot-line-chart-moving"></div>
				</div>
			</div>
		</div>


		<div class="row">
			<div class="col-sm-6 col-dash">
				<div class="sedes">
					<?php for($i = 0 ; $i < 6 ; $i++){ ?>
						<div class="sede">
							<div class="sede-top">
								<div class="sede-left">
									<i class="fa fa-circle"></i>
								</div>
								<h5>Colegio Integrado LLano Grande</h5>
								<h2 class="no-margins"> <span class="entregado">1</span> / <span class="total">546</span></h2>
							</div>
							<div class="sede-bottom">
								<div class="sede-left">
									<div class="sede-hora-inicio">
										7:45 a.m.
									</div>
								</div>
								<div class="progress progress-mini"> <div style="width: 10%;" class="progress-bar"></div> </div>
							</div>
						</div>
						<?php } ?>
					</div>
				</div>
				<div class="col-sm-6 col-dash col-der">

					<div class="entregas">
						<?php for($i = 0 ; $i < 6 ; $i++){ ?>
						<div class="entrega">
							<i class="fa fa-check-circle"></i>
							<span class="hora-estudiante">07:45:01</span>
							<div class="estudiante-icono"> <img alt="entregado" src="<?= $baseUrl ?>/img/touch.png" /> </div>
							<div class="estudiante">
								<h2><span class="estudiante--nombre">Ricardo Farfán</span> recibió complemento <span class="estudiante--complemento">APS</span></h2>
								<p>Sede <span class="estudiante--sede">Colegio Integrado LLano Grande</span> <br> Validado a través de <span class="estudiante--validacion">huella dactilar.</span></p>
							</div>
						</div>
						<?php } ?>
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