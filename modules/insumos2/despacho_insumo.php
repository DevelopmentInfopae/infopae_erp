<?php

require_once '../../header.php';

if ($permisos['despachos'] == "0") {
  ?><script type="text/javascript">
    window.open('<?= $baseUrl ?>', '_self');
  </script>
<?php exit(); }
else {
    ?><script type="text/javascript">
      const list = document.querySelector(".li_despachos");
      list.className += " active ";
	  const list2 = document.querySelector(".li_insumos");
      list2.className += " active ";
    </script>
  <?php
  }

$periodoActual = $_SESSION['periodoActual'];
$municipio = $_SESSION['p_Municipio'];
$departamento = $_SESSION['p_Departamento'];

$nomMeses = array('01' => "Enero", "02" => "Febrero", "03" => "Marzo", "04" => "Abril", "05" => "Mayo", "06" => "Junio", "07" => "Julio", "08" => "Agosto", "09" => "Septiembre", "10" => "Octubre", "11" => "Noviembre", "12" => "Diciembre");

$consultaTipoDespacho = " SELECT Id, movimiento FROM tipomovimiento WHERE Documento = 'DESI' ";
$respuestaTipoDespacho = $Link->query($consultaTipoDespacho) or die ('Error al consultar el tipo de despacho ' . mysqli_error($Link));
if ($respuestaTipoDespacho->num_rows > 0) {
	while ($dataTipoDespacho = $respuestaTipoDespacho->fetch_assoc()) {
		$tipoDespacho[$dataTipoDespacho['Id']] = $dataTipoDespacho['movimiento'];
	}
}

$consultaMeses = " SELECT DISTINCT(MES) as mes FROM planilla_semanas ";
$respuestaMeses = $Link->query($consultaMeses) or die ('Error al consultar los meses ' . mysqli_error($Link));
if ($respuestaMeses->num_rows > 0) {
	while ($dataMeses = $respuestaMeses->fetch_assoc()) {
		$meses[$dataMeses['mes']] = $dataMeses;
	}
}

$consultaComplemento = " SELECT ID, CODIGO FROM tipo_complemento WHERE ValorRacion > 0 ";
$respuestaComplemento = $Link->query($consultaComplemento) or die ('Error al consultar los complementos');
if ($respuestaComplemento->num_rows > 0) {
 	while ($dataComplemento = $respuestaComplemento->fetch_assoc()) {
 		$complementos[$dataComplemento['ID']] = $dataComplemento['CODIGO'];
 	}
}

$consultaMunicipio = '';
if ($municipio == "0") {
	$consultaMunicipio = " SELECT CodigoDANE, Ciudad FROM ubicacion WHERE Departamento LIKE '$departamento%' AND ETC != 1 ORDER BY Ciudad ASC"; 
}else{
	$consultaMunicipio = " SELECT CodigoDANE, Ciudad FROM ubicacion WHERE CodigoDANE = '$municipio' ";
}
$respuestaMunicipio = $Link->query($consultaMunicipio) or die ('Error al consultar los municipios ' . mysqli_error($Link));
if ($respuestaMunicipio->num_rows > 0) {
	while ($dataMunicipio = $respuestaMunicipio->fetch_assoc()) {
		$municipios[$dataMunicipio['CodigoDANE']] = $dataMunicipio['Ciudad'];
	}
}

$consultaRutas = " SELECT ID, Nombre FROM rutas ";
$respuestaRutas = $Link->query($consultaRutas) or die ('Error al consultar las rutas ' . mysqli_error($Link));
if ($respuestaRutas->num_rows > 0) {
 	while ($dataRutas = $respuestaRutas->fetch_assoc()) {
 		$rutas[$dataRutas['ID']] = $dataRutas['Nombre'];
 	}
 } 

 $consultaTipoTransporte = " SELECT Id, Nombre FROM tipovehiculo ";
 $respuestaTipoTransposte = $Link->query($consultaTipoTransporte) or die ('Error al consultar el tipo de transporte ' . mysqli_error($Link));
 if ($respuestaTipoTransposte->num_rows > 0) {
 	while ($dataTipoTransporte = $respuestaTipoTransposte->fetch_assoc()) {
 		$tipoTransporte[$dataTipoTransporte['Id']] = $dataTipoTransporte['Nombre']; 
 	}
 }

$nameLabel = get_titles('despachos', 'insumos', $labels);
$titulo = $nameLabel.' - Nuevo';


?>

<?php if ($_SESSION['perfil'] == "0" || $permisos['despachos'] == "2"): ?>
	<div class="row wrapper wrapper-content border-bottom white-bg page-heading">
		<div class="col-lg-8">
			<h2><?= $titulo; ?></h2>
			<ol class="breadcrumb">
		      <li>
		        <a href="<?= $baseUrl; ?>">Inicio</a>
		      </li>
		      <li>
		        <a href="despachos.php"><?= $nameLabel ?></a>
		      </li>
		      <li class="active">
		        <strong><?= $titulo; ?></strong>
		      </li>
    		</ol>
		</div> <!-- col-lg-8 -->
		<div class="col-lg-4">
    		<div class="title-action">
      			<button class="btn btn-primary guardar" onclick="submitDespacho();" id="segundoBtnSubmit" style=""><span class="fa fa-check"></span> Guardar</button>
    		</div><!-- /.title-action -->
  		</div><!-- /.col-lg-4 -->
	</div> <!-- page-heading -->

	<div class="wrapper wrapper-content animated fadeInRight">
		<div class="row">
			<div class="col-lg-12">
				<div class="ibox float-e-margins">
					<div class="ibox-content contentBackground">
          				<form class="form row" id="formDespachoInsumo">
          					<div class="form-group col-lg-3 col-sm-6">
          						<label>Tipo de despacho</label>
              					<select name="tipo_despacho" id="tipo_despacho" class="form-control" required>
              						<option value="">Seleccione...</option>
              						<?php foreach ($tipoDespacho as $id => $movimiento): ?>
              							<option value="<?= $id; ?>"><?= $movimiento; ?></option>
              						<?php endforeach ?>
              					</select>		
          					</div> <!-- col-sm-6 -->
          					<div class="form-group col-lg-3 col-sm-6">
          						<label>Proveedor / Empleado</label>
          						<select name="proveedor" id="proveedor" class="form-control" required>
          							<option value="">Seleccione...</option>
          						</select>
          					</div> <!-- col-sm-6 -->
          					<div class="form-group col-lg-3 col-sm-6">
          						<label>Mes</label>
          						<select name="mes" id="mes" class="form-control" required="">
          							<option value="">Seleccione...</option>
          							<?php foreach ($meses as $key => $value): ?>
          								<option value="<?= $key; ?>"> <?= $nomMeses[$key]; ?> </option>
          							<?php endforeach ?>
          						</select>
          					</div><!--  col-sm-6 -->
          					<div class="form-group col-lg-3 col-sm-6">
          						<label>Complemento</label>
          						<select name="complemento" id="complemento" class="form-control" required="">
          							<option value="">Seleccione...</option>
          							<option value="ALL">TOTAL COBERTURA</option>
          							<?php foreach ($complementos as $id => $codigo): ?>
          								<option value="<?= $codigo; ?>"><?= $codigo; ?></option>
          							<?php endforeach ?>
          						</select>
          					</div> <!-- col-sm-6 -->
                    <div class="form-group col-lg-3 col-sm-6">
                      <label>Manipuladoras</label>
                      <select name="manipuladoras" id="manipuladoras" class="form-control">
                        <option value="si">SI</option>
                        <option value="no">NO</option>
                      </select>
                    </div> <!-- col-sm-6 -->
          					<div class="form-group col-lg-3 col-sm-6">
          						<label>Municipio</label>
          						<select name="municipio" id="municipio" class="form-control" required="">
          							<option value="ALL">Todos</option>
          							<?php foreach ($municipios as $codigo => $ciudad): ?>
          								<option value="<?= $codigo; ?>" <?= (isset($municipio) && $municipio == $codigo ) ? 'selected' : '' ?> ><?= $ciudad; ?></option>
          							<?php endforeach ?>
          						</select>
          					</div> <!-- col-sm-6 -->
          					<div class="form-group col-lg-3 col-sm-6">
          						<label>Institución</label>
          						<select name="institucion_desp" id="institucion_desp" class="form-control select2">
          							<option value="">Todos</option>
          						</select>	
          					</div> <!-- col-sm-6 -->
          					<div class="form-group col-lg-3 col-sm-6">
          						<label>Sede</label>
          						<select name="sede" id="sede" class="form-control select2">
          							<option value="">Todos</option>
          						</select>
          					</div> <!-- col-sm-6 -->
          					<div class="form-group col-lg-3 col-sm-6">
          						<label>Rutas</label>
          						<select name="rutas" id="rutas" class="form-control">
          							<option value="">Seleccione...</option>
          							<?php foreach ($rutas as $id => $nombre): ?>
          								<option value="<?= $id; ?>"><?= $nombre; ?></option>
          							<?php endforeach ?>
          						</select>
          					</div> <!-- col-sm-6 -->
          					<div class="col-sm-12">
              					<span class="btn btn-primary" onclick="añadirSedes()"><i class="fa fa-plus"></i> </span>
              					<span class="btn btn-primary" onclick="eliminarSedes()"><i class="fa fa-minus"></i> </span>
            				</div>
            				<div class="col-sm-12">
              					<br>
              					<p>Debe seleccionar la sede a la que se le realizará el despacho.</p>
              					<div class="radio">
                					<label><input type="checkbox" name="seleccionar_todos" id="seleccionar_todos" onclick="seleccionarTodos(this)"> Seleccionar todos</label>
              					</div>
              				<table class="table" id="table">
				                <thead>
				                  <tr>
				                    <th></th>
				                    <th>Municipio</th>
				                    <th>Institución</th>
				                    <th>Sede</th>
				                  </tr>
				                </thead>
				                <tbody id="tbodySedesDespachos">

				                </tbody>
				                <tfoot>
				                  <tr>
				                    <th></th>
				                    <th>Municipio</th>
				                    <th>Institución</th>
				                    <th>Sede</th>
				                  </tr>
				                </tfoot>
              				</table>
              				<div class="alert alert-danger" role="alert" id="errDespachos" style="display: none;"></div>
           				 	</div> 

				            <hr class="col-sm-12">
				            <div class="productos col-sm-12">
				              	<h3>Productos a despachar</h3><br>
				              	<span class="btn btn-primary" onclick="anadirProducto()"><i class="fa fa-plus"></i></span>
				              	<span class="btn btn-primary" onclick="borrarProducto()"><i class="fa fa-minus"></i></span>
				             	<br>
				              	<br>
				              	<div class="row" id="productosDesp">
				              </div>
				            </div>
				            <hr class="col-sm-12">
           				 	<div class="row">
	           				 	<div class="form-group col-lg-3 col-sm-6">
	           				 		<label>Bodega Origen</label>
	           				 		<select name="bodegaOrigen" id="bodegaOrigen" class="form-control" required>
	           				 			<option value="">Seleccione proveedor/empleado</option>
	           				 		</select>
	           				 	</div> <!-- col-sm-6 -->
	           				 	<div class="form-group col-lg-3 col-sm-6">
	           				 		<label>Bodega Destino</label>
	           				 		<input type="text" class="form-control" value="Bodega asignada a sede" readonly>
	           				 	</div> <!-- col-sm-6 -->
	           				 </div> <!-- row -->
           				 	<div class="row">
	           				 	<div class="form-group col-lg-3 col-sm-6">
	           				 		<label>Tipo Transporte</label>
	           				 		<select name="tipoTransporte" id="tipoTransporte" class="form-control" required>
	           				 			<option value="">Seleccione...</option>
	           				 			<?php foreach ($tipoTransporte as $id => $nombre): ?>
	           				 				<option value="<?= $id; ?>"><?= $nombre; ?></option>
	           				 			<?php endforeach ?>
	           				 		</select>
	           				 	</div> <!-- col-sm-6 -->
	           				 	<div class="form-group col-lg-3 col-sm-6">
	           				 		<label>Placa</label>
	           				 		<input type="text" name="placa" id="placa" class="form-control">
	           				 	</div> <!-- col-sm-6 -->
	           				 	<div class="form-group col-lg-3 col-sm-6">
	           				 		<label>Conductor</label>
	           				 		<input type="text" name="conductor" id="conductor" class="form-control">
	           				 	</div> <!-- col-sm-6 -->
           				 	</div> <!-- row -->
          				</form> <!-- form -->
          				<div class="col-sm-12" style="padding: 3% 0% 2% 0%;">
            				<button class="btn btn-primary guardar" onclick="submitDespacho(1);"><span class="fa fa-check"></span> Guardar</button>
          				</div>
					</div><!-- contentBackground -->
				</div> <!-- float-e-margins -->
			</div> <!-- col-lg-12 -->
		</div> <!-- row -->
	</div> <!-- fadeInRight -->
<?php else: ?>
  	<script type="text/javascript">
    	window.open('<?= $baseUrl ?>', '_self');
  	</script>
<?php endif ?>
<?php include '../../footer.php'; ?>

<!-- Mainly scripts -->
<script src="<?= $baseUrl; ?>/theme/js/jquery-3.1.1.min.js"></script>
<script src="<?= $baseUrl; ?>/theme/js/bootstrap.min.js"></script>
<script src="<?= $baseUrl; ?>/theme/js/plugins/metisMenu/jquery.metisMenu.js"></script>
<script src="<?= $baseUrl; ?>/theme/js/plugins/slimscroll/jquery.slimscroll.min.js"></script>

<!-- Custom and plugin javascript -->
<script src="<?= $baseUrl; ?>/theme/js/inspinia.js"></script>
<script src="<?= $baseUrl; ?>/theme/js/plugins/pace/pace.min.js"></script>
<script src="<?= $baseUrl; ?>/theme/js/plugins/toastr/toastr.min.js"></script>
<script src="<?= $baseUrl; ?>/theme/js/plugins/validate/jquery.validate.min.js"></script>
<script src="<?= $baseUrl; ?>/theme/js/plugins/select2/select2.full.min.js"></script>

<script src="<?= $baseUrl; ?>/modules/insumos2/js/nuevoDespacho.js"></script>

<?php mysqli_close($Link); ?>

</body>
</html>