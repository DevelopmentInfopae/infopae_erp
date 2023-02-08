<?php 
include "../../header.php";

if ($permisos['orden_compra'] == "0") {
?><script type="text/javascript">
      window.open('<?= $baseUrl ?>', '_self');
  </script>
<?php exit(); }

else {
	?><script type="text/javascript">
	  const list = document.querySelector(".li_informes");
	  list.className += " active ";
	</script>
	<?php
	}

// consulta meses existentes en entregas
$consultaMeses = " SHOW TABLES LIKE 'entregas_res_%' ";
$respuestaMeses = $Link->query($consultaMeses) or die ('Error al consultar los meses ln 12');
if ($respuestaMeses->num_rows > 0) {
	while ($dataMeses = $respuestaMeses->fetch_assoc()) {
		$aux = (array_values($dataMeses));
		$meses[] = substr($aux[0], 13, -2);
	}
}

$mesesNom = [ 	"01" => "ENERO", 
					"02" => "FEBRERO", 
					"03" => "MARZO", 
					"04" => "ABRIL", 
					"05" => "MAYO", 
					"06" => "JUNIO",
					"07" => "JULIO",
					"08" => "AGOSTO",
					"09" => "SEPTIEMBRE",
					"10" => "OCTUBRE",
					"11" => "NOVIEMBRE",
					"12" => "DICIEMBRE" ];

$municipios = [];
if ($_SESSION["p_Municipio"] == "0" ) {
	$consultaMunicipios = " SELECT Ciudad, CodigoDANE FROM ubicacion WHERE CodigoDANE LIKE '" .$_SESSION['p_CodDepartamento']. "%' ";
	$respuestaMunicipios = $Link->query($consultaMunicipios) or die ('Error al consultar los municipios ln 34');
	if ($respuestaMunicipios->num_rows > 0) {
		while ($dataMunicipios = $respuestaMunicipios->fetch_assoc()) {
			$municipios[$dataMunicipios['CodigoDANE']] = $dataMunicipios['Ciudad'];
		}
	}
}else if ($_SESSION['p_Municipio'] != 0){
	$consultaMunicipios = " SELECT Ciudad, CodigoDANE FROM ubicacion WHERE CodigoDANE = '" .$_SESSION['p_Municipio']. "'";
	$respuestaMunicipios = $Link->query($consultaMunicipios) or die ('Error al consultar el municipio ln 44');
	if ($respuestaMunicipios->num_rows > 0) {
		while ($dataMunicipios = $respuestaMunicipios->fetch_assoc()) {
			$municipios[$dataMunicipios['CodigoDANE']] = $dataMunicipios['Ciudad'];
		}
	}
}

$consultaRutas = " SELECT ID, Nombre FROM rutas ";
$respuestaRutas = $Link->query($consultaRutas) or die ('Error al consultar las rutas ln 53');
if ($respuestaRutas->num_rows > 0) {
	while ($dataRutas = $respuestaRutas->fetch_assoc()) {
		$rutas[$dataRutas['ID']] = $dataRutas['Nombre']; 
	}
}

$consultaComplementos = " SELECT CODIGO FROM tipo_complemento ";
$respuestaComplementos = $Link->query($consultaComplementos) or die ('Error al consultar los complementos ln 61');
if ($respuestaComplementos->num_rows > 0) {
	while ($dataComplementos = $respuestaComplementos->fetch_assoc()) {
		$complementos[$dataComplementos['CODIGO']] = $dataComplementos['CODIGO'];
	}
}
$nameLabel = get_titles('informes', 'informeInejecuciones', $labels);
$titulo = $nameLabel;
?>

<style type="text/css">
   .select2-container--open {
      z-index: 9999999
  }
</style>

<div class="row wrapper wrapper-content	white-bg page-heading">
	<div class="col-md-6 col-sm-12">
		<h2><?= $titulo ?></h2>
		<ol class="breadcrumb">
			<li> <a href="<?= $baseUrl ?>">Home </a></li>
			<li class="active"><strong><?= $titulo ?></strong></li>
		</ol>
	</div>
</div>

<div class="wrapper wrapper-content animated fadeInRight">
	<div class="row">
		<div class="col-12">
			<div class="ibox float-e-margins">
				<div class="ibox-content contentBackground">
					<h2>Parámetros de Consulta</h2>
					<form name="formInejecuciones" id="formInejecuciones" >
						<div class="row">

							<div class="col-md-3 col-sm-12 form-group">
								<label for="mes">Mes*</label>
								<select class="form-control" name="mes" id="mes" required>
									<!-- <option value=""> Seleccione... </option> -->
									<?php foreach ($meses as $key => $value): ?>
										<option value="<?= $value ?>" <?= !(isset($meses[$key+1])) ? 'selected' : '' ?>  > <?= $mesesNom[$value] ?> </option>
									<?php endforeach ?>
								</select>
							</div>
							<div class="col-md-3 col-sm-12 form-group">
								<label for="semana">Semana</label>
								<select class="form-control" name="semana" id="semana">
									<option value="">Seleccione... </option>
								</select>
							</div>
							<div class="col-md-3 col-sm-12 form-group">
								<label for="municipio">Municipio</label>
								<select class="form-control" name="municipio" id="municipio">
									<?php if ($_SESSION['p_Municipio'] != 0 ): ?>
										<?php foreach ($municipios as $key => $value): ?>
											<option value="<?= $key ?>"> <?= $value ?> </option>
										<?php endforeach ?>
									<?php else: ?>
										<option value="">Seleccione... </option>
										<?php foreach ($municipios as $key => $value): ?>
											<option value="<?= $key ?>"> <?= $value ?> </option>
										<?php endforeach ?>
									<?php endif ?>
								</select>
							</div>
							<div class="col-md-3 col-sm-12 form-group">
								<label for="ruta">Ruta</label>
								<select class="form-control" name="ruta" id="ruta">
									<option value="">Seleccione...</option>
									<?php foreach ($rutas as $key => $value): ?>
										<option value="<? $key ?>"> <?= $value ?> </option>
									<?php endforeach ?>
								</select>
							</div>
						</div> <!-- row -->

						<div class="row">
							<div class="col-md-3 col-sm-12 form-group">
								<label for="institucion">Institución</label>
								<select class="form-control" name="institucion" id="institucion">
									<option value="">Seleccione...</option>
								</select>
							</div>
							<div class="col-md-3 col-sm-12 form-group">
								<label for="sede">Sede</label>
								<select class="form-control" name="sede" id="sede">
									<option value="">Seleccione... </option>
								</select>
							</div>
							<div class="col-md-3 col-sm-12 form-group">
								<label for="complemento">Complemento</label>
								<select class="form-control" name="complemento" id="complemento">
									<option value="">Seleccione... </option>
									<?php foreach ($complementos as $key => $value): ?>
										<option value="<?= $key ?>"> <?= $value ?> </option>
									<?php endforeach ?>
								</select>
							</div>
						</div> <!-- row -->

						<div class="row">
							<div class="col-md-3 col-sm-12 form-group">
								<button class="btn btn-primary" name="btnBuscar" id="btnBuscar" value="1"><strong><i class="fa fa-search"></i> Consultar</strong></button>
							</div>
						</div>
					</form>
				</div> <!-- ibox-content -->
			</div> <!-- ibox -->
		</div> <!-- col -->
	</div> <!-- row -->
</div> <!-- wrapper -->

<div class="wrapper wrapper-content animated fadeInRight rowTable" style="display: none;">
	<div class="row">
		<div class="col-12">
			<div class="ibox float-e-margins">
				<div class="ibox-content contentBackground">
					<div class="table-responsive mt-5">
						<!-- <table class="table selectableRows table-hover table-striped" id="box-table-movimientos" > -->
						<!-- <table class="table selectableRows table-striped table-bordered" id="box-table-movimientos" > -->
						<table class="table table-striped table-bordered table-hover selectableRows" id="box-table-movimientos" >
							<thead id="tHead">
			              
			            </thead>
			            <tbody id="tBody">
			              
			            </tbody>
			            <tfoot id="tFoot">
			              
			            </tfoot>
						</table>
					</div> <!-- table-responsive -->
				</div> <!-- ibox-content -->
			</div> <!-- ibox -->
		</div> <!-- col -->
	</div> <!-- row -->
</div> <!-- wrapper -->

<script src="<?= $baseUrl; ?>/theme/js/jquery-3.1.1.min.js"></script>
<script src="<?= $baseUrl; ?>/theme/js/bootstrap.min.js"></script>
<script src="<?= $baseUrl; ?>/theme/js/plugins/metisMenu/jquery.metisMenu.js"></script>
<script src="<?= $baseUrl; ?>/theme/js/plugins/slimscroll/jquery.slimscroll.min.js"></script>
<script src="<?= $baseUrl; ?>/theme/js/inspinia.js"></script>

<script src="<?= $baseUrl; ?>/theme/js/plugins/select2/select2.full.min.js"></script>
<script src="<?= $baseUrl; ?>/theme/js/plugins/validate/jquery.validate.min.js"></script>
<script src="<?= $baseUrl; ?>/theme/js/plugins/dataTables/datatables.min.js"></script>

<script src="<?= $baseUrl; ?>/modules/inejecuciones/js/inejecuciones.js"></script>


