<?php
include '../../header.php';

if ($permisos['novedades'] == "0") {
    ?><script type="text/javascript">
      	window.open('<?= $baseUrl ?>', '_self');
    </script>
<?php exit();}

set_time_limit (0);
ini_set('memory_limit','6000M');

$periodoActual = $_SESSION["periodoActual"];
$titulo = "Intercambio de alimento";
$institucionNombre = "";

date_default_timezone_set('America/Bogota');
$fecha = date("Y-m-d H:i:s");
$cacheBusting = date("YmdHis");

$dia = intval(date("d"));
$mes = date("m");
$anno = date("Y");

$consultaVariaciones = " SELECT id, descripcion FROM variacion_menu ";
$respuestaVariaciones = $Link->query($consultaVariaciones) or die('Error al consultar las variaciones');
if ($respuestaVariaciones->num_rows > 0) {
	while($dataVariaciones = $respuestaVariaciones->fetch_assoc()){
		$variaciones[$dataVariaciones['id']] = $dataVariaciones['descripcion'];
	}
}

?>

<?php if ($_SESSION['perfil'] == "0" || $permisos['novedades'] == "2"): ?>
<link rel="stylesheet" href="css/custom.css?v=<?= $cacheBusting; ?>">
<div class="row wrapper wrapper-content border-bottom white-bg page-heading">
	<div class="col-xs-8">
		<h2>Intercambio de Alimento</h2>
		<ol class="breadcrumb">
			<li>
				<a href="<?php echo $baseUrl; ?>">Inicio</a>
			</li>
			<li> 
				<a href="<?php echo $baseUrl; ?>/modules/intercambios">Novedades de Menú</a> 
			</li>
			<li class="active">
				<strong><?php echo $titulo; ?></strong>
			</li>
		</ol>
	</div>
</div>

<!-- FILTRO DE BUSQUEDA -->
<?php //include "filtro.php"  ?>
<form action="" id="formParametros" name="formParametros" method="post" enctype="multipart/form-data">
	<div class="wrapper wrapper-content  animated fadeInRight">
		<div class="row">
			<div class="col-sm-12">
				<div class="ibox">
					<div class="ibox-title">
						<h5>Busqueda</h5>
					</div>
					<div class="ibox-content">
						<div class="row">
							<div class="col-sm-12">				
								<div class="row">
									<?php if($_SESSION["perfil"] == 1 || $_SESSION["perfil"] == 0 || $_SESSION["perfil"] == 5 || $_SESSION["perfil"] == 6 || $_SESSION["perfil"] == 3) { ?>
										<div class="col-sm-4 form-group">
											<label for="mes">Mes</label>
											<select class="form-control" name="mes" id="mes" required>
												<option value="">Seleccione uno</option>
												<option value="01">Enero</option>									
												<option value="02">Febrero</option>									
												<option value="03">Marzo</option>									
												<option value="04">Abril</option>									
												<option value="05">Mayo</option>									
												<option value="06">Junio</option>									
												<option value="07">Julio</option>									
												<option value="08">Agosto</option>									
												<option value="09">Septiembre</option>									
												<option value="10">Octubre</option>									
												<option value="11">Noviembre</option>									
												<option value="12">Diciembre</option>									
											</select>
										</div>

										<div class="col-sm-4 form-group">
											<label for="semana">Semana</label>
											<select class="form-control" name="semana" id="semana" required>
												<option value="">Seleccione uno</option>									
											</select>
										</div>

										<div class="col-sm-4 form-group">
											<label for="dia">Día</label>
											<select class="form-control" name="dia" id="dia" required>
												<option value="">Seleccione uno</option>									
											</select>
										</div>

										<div class="col-sm-4 form-group">
											<label for="tipoComplemento">Tipo de complemento</label>
											<select class="form-control" name="tipoComplemento" id="tipoComplemento" required>
												<option value="">Seleccione uno</option>								
											</select>
										</div>

										<div class="col-sm-4 form-group">
											<label for="grupoEtario">Grupo etario</label>
											<select class="form-control" name="grupoEtario" id="grupoEtario" required>
												<option value="">Seleccione uno</option>								
											</select>
										</div>

										<div class="col-sm-4 form-group">
											<label for="variacion">Variación</label>										
											<select class="form-control" name="variacion" id="variacion" required>
												<option value="">Seleccione uno</option>
												<?php foreach ($variaciones as $key => $value): ?>
													<option value="<?= $key ?>"><?= strtoupper($value) ?></option>									
												<?php endforeach ?>								
											</select>		
										</div>

										<div class="col-sm-4 form-group">
											<label for="menu">Menú</label>
											<input type="hidden" name="codigoMenu" id="codigoMenu">											
											<input type="text" class="form-control" name="menu" id="menu" readonly="readonly">								
											<input type="hidden" name="numero_menu" id="numero_menu">			
										</div>

										<div class="col-sm-8 form-group">
											<label for="preparaciones">Preparaciones</label>
											<select class="form-control" name="preparaciones" id="preparaciones" required>
												<option value="">Seleccione uno</option>								
											</select>
										</div>
									<?php } ?>
								</div>
								<div class="hr-line-dashed"></div>
								<div class="form-group row">
									<div class="col-sm-12">
										<button class="btn btn-primary" type="button" id="btnBuscar"> <i class="fa fa-search"></i> Buscar</button>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<!-- Aqui lleganlas cajas de ajax con la preparación original -->
	<!-- y las opciones para modificarla -->
	<div class="boxPreparacion"></div>
</form>
<?php else: ?>
	<script type="text/javascript">
      	window.open('<?= $baseUrl ?>', '_self');
    </script>
<?php endif ?>

<?php include '../../footer.php'; ?>

<!-- Mainly scripts -->
<script src="<?php echo $baseUrl; ?>/theme/js/jquery-3.1.1.min.js"></script>
<script src="<?php echo $baseUrl; ?>/theme/js/bootstrap.min.js"></script>
<script src="<?php echo $baseUrl; ?>/theme/js/plugins/metisMenu/jquery.metisMenu.js"></script>
<script src="<?php echo $baseUrl; ?>/theme/js/plugins/slimscroll/jquery.slimscroll.min.js"></script>

<!-- Custom and plugin javascript -->
<script src="<?php echo $baseUrl; ?>/theme/js/inspinia.js"></script>
<script src="<?php echo $baseUrl; ?>/theme/js/plugins/pace/pace.min.js"></script>


<!-- Data picker -->
<script src="<?php echo $baseUrl; ?>/theme/js/plugins/datapicker/bootstrap-datepicker.js"></script>

<!-- Date picker en español -->
<script src="<?php echo $baseUrl; ?>/js/bootstrap-datepicker.es.js"></script>

<script src="<?php echo $baseUrl; ?>/theme/js/plugins/dataTables/datatables.min.js"></script>
<script src="<?php echo $baseUrl; ?>/theme/js/plugins/jasny/jasny-bootstrap.min.js"></script>
<script src="<?php echo $baseUrl; ?>/theme/js/plugins/validate/jquery.validate.min.js"></script>
<script src="<?php echo $baseUrl; ?>/theme/js/plugins/toggle/toggle.min.js"></script>
<script src="<?php echo $baseUrl; ?>/theme/js/plugins/toastr/toastr.min.js"></script>
<script src="<?php echo $baseUrl; ?>/theme/js/plugins/iCheck/icheck.min.js"></script>
<script src="<?php echo $baseUrl; ?>/theme/js/plugins/select2/select2.full.min.js"></script>
<script src="<?php echo $baseUrl; ?>/modules/intercambios/js/filtro.js?v=<?= $cacheBusting; ?>"></script>
<script src="<?php echo $baseUrl; ?>/modules/intercambios/js/intercambio_alimento.js?v=<?= $cacheBusting; ?>"></script>

</body>
</html>