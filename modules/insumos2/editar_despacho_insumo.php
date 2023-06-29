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
$meses = array('01' => "Enero", "02" => "Febrero", "03" => "Marzo", "04" => "Abril", "05" => "Mayo", "06" => "Junio", "07" => "Julio", "08" => "Agosto", "09" => "Septiembre", "10" => "Octubre", "11" => "Noviembre", "12" => "Diciembre");

$periodoActual = $_SESSION['periodoActual'];
$idDespacho = $_POST['id_despacho'];
$mes = $_POST['mesTabla'];
$num = 0;
// exit(var_dump($_POST));
$insumosmov = "insumosmov".$mes.$periodoActual;
$insumosmovdet = 'insumosmovdet'.$mes.$periodoActual;

$consultaInsumos = " SELECT s.nom_inst AS nomInst,
							s.nom_sede AS nomSede,
							u.Ciudad AS ciudad,
							i.Numero AS numero,
							i.BodegaDestino AS bodegaDestino,
							i.Tipo AS tipo,
							i.Complemento AS complemento,
							i.TipoTransporte AS tipoTransporte,
							i.Placa AS placa, 
							i.ResponsableRecibe AS conductor,
							i.Nitcc AS nit,
							i.BodegaOrigen AS bodegaOrigen
					FROM $insumosmov i
					INNER JOIN sedes$periodoActual s ON i.BodegaDestino = s.cod_sede
					INNER JOIN ubicacion u ON u.CodigoDANE = s.cod_mun_sede
					WHERE i.Id = $idDespacho		
			 		";
$respuestaInsumos = $Link->query($consultaInsumos) or die ('Error al consultar los insumos ' . mysqli_error($Link));
if ($respuestaInsumos->num_rows > 0) {
	$encabezado = $respuestaInsumos->fetch_assoc();
}

$consultaTipoDespacho = " SELECT Id, Movimiento FROM tipomovimiento WHERE Documento = 'DESI' ";
$respuestaTipoDespacho = $Link->query($consultaTipoDespacho) or die ('Error al consultar el tipo de movimiento ' .mysqli_error($Link));
if ($respuestaTipoDespacho->num_rows > 0) {
	while ($dataTipoDespacho = $respuestaTipoDespacho->fetch_assoc()) {
		$tipoDespacho[$dataTipoDespacho['Id']] = $dataTipoDespacho['Movimiento'];
	}
}

$consultaProveedor = "";
$consultaidTipo = " SELECT Id FROM tipomovimiento WHERE Documento = 'DESI' AND Movimiento = '" .$encabezado['tipo']. "'";
$respuestaIdTipo = $Link->query($consultaidTipo) or die ('Error al consultar el id del movimiento ' . mysqli_error($Link));
if ($respuestaIdTipo->num_rows > 0) {
	$dataIdTipo = $respuestaIdTipo->fetch_assoc();
	$idTipo = $dataIdTipo['Id'];
	if ($idTipo == "1") {
		$consultaProveedor = " SELECT Nitcc AS nit, Nombrecomercial AS nombre FROM proveedores ";
	}else if ($idTipo == "2") {
		$consultaProveedor = " SELECT Nitcc AS nit, Nombre AS nombre FROM empleados ";
	}
}
$respuestaProveedor = $Link->query($consultaProveedor) or die ('Error al consultar al responsable ' . mysqli_error($Link));
if ($respuestaProveedor->num_rows > 0) {
	while ($dataProveedor = $respuestaProveedor->fetch_assoc()) {
		$responsables[$dataProveedor['nit']] = $dataProveedor['nombre'];
	}
}

$consultaProductos = " SELECT Codigo, Descripcion FROM productos$periodoActual WHERE Codigo LIKE '05%' AND Nivel = 3 ";
$respuestaProductos = $Link->query($consultaProductos) or die ('Error al consultar los productos ' . mysqli_error($Link));
if ($respuestaProductos->num_rows > 0) {
	while ($dataProductos = $respuestaProductos->fetch_assoc()) {
		$productos[$dataProductos['Codigo']] = $dataProductos['Descripcion']; 
	}
}

$consultaProductosDespachados = " SELECT CodigoProducto, Descripcion, Id, Complemento, Item, Numero FROM $insumosmovdet WHERE Numero = '" .$encabezado['numero']. "'";
$respuestaProductosDespachados = $Link->query($consultaProductosDespachados) or die ('Error al consultar los productos despachados ' . mysqli_error($Link));
if ($respuestaProductosDespachados->num_rows > 0) {
	while ($dataProductosDespachados = $respuestaProductosDespachados->fetch_assoc()) {
		$productosDespachados[] = $dataProductosDespachados;
	}
}

$consultaTipoTransporte = " SELECT Id, Nombre FROM tipovehiculo ";
$respuestaTipoTransporte = $Link->query($consultaTipoTransporte) or die ('Error al consultar el tipo de transporte ' . mysqli_error($Link));
if ($respuestaTipoTransporte->num_rows > 0) {
	while ($dataTipoTransporte = $respuestaTipoTransporte->fetch_assoc()) {
		$tipoTransporte[$dataTipoTransporte['Id']] = $dataTipoTransporte['Nombre'];
	}
}

$consultaUsuarios = " SELECT id FROM usuarios WHERE num_doc = '" .$encabezado['nit']. "'";
$respuestaUsuarios = $Link->query($consultaUsuarios) or die ('Error al consultar los usuarios ' . mysqli_error($Link));
if ($respuestaUsuarios->num_rows > 0) {
	$dataUsuarios = $respuestaUsuarios->fetch_assoc();
	$idUsuario = $dataUsuarios['id'];
	$consultaBodegaOrigen = " SELECT DISTINCT(ub.COD_BODEGA_SALIDA) AS bodegaOrigen, b.NOMBRE AS nombre FROM usuarios_bodegas ub INNER JOIN bodegas b ON b.ID = ub.COD_BODEGA_SALIDA WHERE ub.USUARIO = $idUsuario ";
	$respuestaBodegaOrigen = $Link->query($consultaBodegaOrigen) or die ('Error al consultar la bodega origen ' . mysqli_error($Link));
	if ($respuestaBodegaOrigen->num_rows > 0) {
		while ($dataBodegaOrigen = $respuestaBodegaOrigen->fetch_assoc()) {
			$bodegaOrigen[$dataBodegaOrigen['bodegaOrigen']] = $dataBodegaOrigen['nombre'];
		}
	}
}

$nameLabel = get_titles('despachos', 'insumos', $labels);
$titulo = $nameLabel . ' - Editar';
?>

<?php if ($_SESSION['perfil'] == "0" || $permisos['despachos'] == "2"): ?>
	<div class="row wrapper wrapper-content border-bottom white-bg page-heading">
		<div class="col-lg-8">
			<h2><?= $titulo; ?></h2>
			<ol class="breadcrumb">
			    <li>
			       	<a href="<?php echo $baseUrl; ?>">Inicio</a>
			    </li>
			    <li>
			        <a href="despachos.php"><?= $nameLabel ?></a>
			    </li>
			    <li class="active">
			        <strong><?php echo $titulo; ?></strong>
			    </li>
			</ol>
		</div> <!-- col-lg-8 -->
		<div class="col-lg-4">
    		<div class="title-action">
      			<button class="btn btn-primary guardar" onclick="submitDespacho(1);" id="segundoBtnSubmit" style=""><span class="fa fa-check"></span> Guardar</button>
    		</div><!-- /.title-action -->
  		</div><!-- /.col -->
	</div> <!-- page-heading -->

	<div class="wrapper wrapper-content animated fadeInRight">
		<div class="row">
			<div class="col-lg-12">
      			<div class="ibox float-e-margins">
        			<div class="ibox-content contentBackground"> 
          				<form class="form row" id="formDespachoInsumo">
          					<input type="hidden" name="idDespacho" id="idDespacho" value="<?= $idDespacho; ?>">
          					<input type="hidden" name="numero" id="numero" value="<?= $encabezado['numero']; ?>">
          					<input type="hidden" name="sede" id="sede" value="<?= $encabezado['bodegaDestino']; ?>">
          					<div class="form-group col-lg-3 col-sm-6">
          						<label>Tipo de despacho</label>
          						<select name="tipoDespacho" id="tipoDespacho" class="form-control">
          							<?php foreach ($tipoDespacho as $id => $movimiento): ?>
          								<option value="<?= $id; ?>" <?= ($movimiento == $encabezado['tipo']) ? "selected" : "" ?>> <?= $movimiento; ?></option>
          							<?php endforeach ?>
          						</select>
          					</div> <!-- col-sm-6 -->
          					<div class="form-group col-lg-3 col-sm-6">
          						<label>Proveedor / Empleado</label>
          						<select name="proveedor" id="proveedor" class="form-control" required>
          							<?php foreach ($responsables as $nit => $nombre): ?>
          								<option value="<?= $nit; ?>" <?= ($nit == $encabezado['nit']) ? "selected" : "" ?>> <?= $nombre;?> </option>
          							<?php endforeach ?>
          						</select>
          					</div> <!-- col-sm-6 -->
          					<div class="col-lg-12">
          						<br>
          						<table class="table">
          							<thead>
          								<tr>
          									<th>Municipio</th>
          									<th>Institución</th>
          									<th>Sede</th>
          								</tr>
          							</thead>
          							<tbody>
          								<tr>
          									<td><?= $encabezado['ciudad']; ?></td>
          									<td><?= $encabezado['nomInst']; ?></td>
          									<td><?= $encabezado['nomSede']; ?></td>
          								</tr>
          							</tbody>
          							<tfoot>
          								<tr>
          									<th>Municipio</th>
          									<th>Institución</th>
          									<th>Sede</th>
          								</tr>
          							</tfoot>
          						</table>
          						<div class="alert alert-danger" role="alert" id="errDespachos" style="display: none;"></div>
          					</div> <!-- col-lg-12 -->
          					<div class="productos col-sm-12">
          					 	<h3>Productos a despachar</h3><br>
              					<span class="btn btn-primary" onclick="anadirProducto()"><i class="fa fa-plus"></i></span>
              					<span class="btn btn-primary" onclick="borrarProducto()"><i class="fa fa-minus"></i></span>
              					<br>
              					<br>
              					<div class="row" id="productosDesp">
              						<?php foreach ($productosDespachados as $key => $producto): $num ++; ?>
              							<div class="col-lg-3 row" id="producto_<?= $num; ?>">
				                          	<div class="col-sm-2">
				                            	<button class="btn btn-danger btn-outline btn-sm" type="button" data-iddet="<?= $producto['Id']; ?>" data-numdoc="<?= $producto['Numero']; ?>" data-mestabla="<?= $mes; ?>" data-numdet="<?= $producto['Item']; ?>" title='Eliminar producto del despacho' data-toggle="modal" data-target="#modalEliminarProductoDespacho"><span class="fa fa-trash"></span></button>
				                          	</div>
				                          	<div class="col-sm-10">
				                          		<select class="form-control productodesp" onchange="validaProductos(this, '<?= $num; ?>')" name="productoDespacho[]" id="producto_<?= $num; ?>" required>
				                          			<?php foreach ($productos as $codigo => $descripcion): ?>
				                          				<?php if ($codigo == $producto['CodigoProducto']): ?>
				                          					<option value="<?= $codigo; ?>" <?= ($codigo == $producto['CodigoProducto']) ? "selected" : "" ?>> <?= $descripcion; ?> </option>
				                          					<input type="hidden" name="DescInsumo[]" id="descIns_<?= $num; ?>" value="<?= $producto['Descripcion']; ?>">
                          									<input type="hidden" name="item[]" id="item_<?= $num; ?>" value="<?= $producto['Item']; ?>">
				                          				<?php endif ?>
				                          			<?php endforeach ?>
				                          		</select>
				                          	</div> <!-- col-sm-10 -->
              							</div><!-- col-lg-3 -->
              						<?php endforeach ?>
              					</div> <!-- row -->
          					</div> <!-- col-sm-12 -->
          					<hr class="col-lg-12">
          					<div class="form-group col-sm-3">
					            <label>Tipo complemento</label>
					            <input type="text" class="form-control" name="tipo_complemento" id="tipo_complemento" value="<?= $encabezado['complemento']?>" readonly>
					        </div>
					        <div class="form-group col-sm-3">
              					<label>Mes a despachar</label>
              					<input type="text" id="mes" value="<?= $meses[$mes]; ?>" class="form-control" readonly>
              					<input type="hidden" name="mes" id="mes" value="<?= $mes; ?>">
            				</div>
            				<div class="form-group col-sm-3">
              					<label>Bodega Origen</label>
					            <select name="bodega_origen" id="bodega_origen" class="form-control" required>
					                <?php foreach ($bodegaOrigen as $id => $nombre): ?>
					                	<option value="<?= $id ?>" <?= ($id == $encabezado['bodegaOrigen']) ? "selected" : "" ?>><?= $nombre ?></option>
					                <?php endforeach ?>
					            </select>
            				</div>
            				<div class="form-group col-sm-3">
              					<label>Bodega Destino</label>
              					<input type="text" class="form-control" value="Bodega asignada a sede" readonly>
            				</div>
            				<hr class="col-lg-12">
							<div class="col-lg-3">
								<label>Tipo Transporte</label>
								<select name="tipoTransporte" id="tipoTransporte" class="form-control">
									<?php foreach ($tipoTransporte as $id => $nombre): ?>
										<option value="<?= $id; ?>" <?= ($id == $encabezado['tipoTransporte']) ? "selected" : "" ?>> <?= $nombre; ?> </option>
									<?php endforeach ?>	
								</select>
							</div> <!-- col-lg-3 -->
							<div class="col-lg-3">
								<label>Placa</label>
								<input type="text" name="placa" id="placa" class="form-control" value="<?= $encabezado['placa']; ?>">
							</div> <!-- col-lg-3 -->
							<div class="col-lg-3">
								<label>Conductor</label>
								<input type="text" name="conductor" id="conductor" class="form-control" value="<?= $encabezado['conductor']; ?>">
							</div><!-- col-lg-3 -->
          				</form>	
          				<div class="col-sm-12" style="padding: 3% 0% 2% 0%;">
				            <button class="btn btn-primary guardar" onclick="submitDespacho(1);"><span class="fa fa-check"></span> Guardar</button>
				        </div>
        			</div><!-- contentBackground -->
				</div> <!-- float-e-margins -->
			</div> <!-- col-lg-12 -->
		</div> <!-- row -->
	</div> <!-- fadeInRight -->

	<div class="modal inmodal fade" id="modalEliminarProductoDespacho" tabindex="-1" role="dialog" style="display: none;" aria-hidden="true">
 		<div class="modal-dialog modal-sm">
   			<div class="modal-content">
     			<div class="modal-header text-info" id="tipoCabecera" style="padding: 15px;">
       				<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only">Cerrar</span></button>
       				<h3><i class="fa fa-question-circle fa-lg" aria-hidden="true"></i> Confirmación InfoPAE </h3>
     			</div>
     			<div class="modal-body" style="text-align: center;">
         			<h3>¿Está seguro de eliminar el producto del despacho?</h3>
         			<p id="mensajeConfirm" style="display: none;"><b>¡Atención! </b> Eliminará el último producto del despacho, por lo que el despacho se eliminará también.</p>
         			<input type="hidden" name="num_det" id="num_det">
         			<input type="hidden" name="id_det_despacho" id="id_det_despacho">
         			<input type="hidden" name="numdoc_eliminar_det" id="numdoc_eliminar_det">
         			<input type="hidden" name="mes_tabla_eliminar_det" id="mes_tabla_eliminar_det">
     			</div>
     			<div class="modal-footer">
       				<button type="button" class="btn btn-white btn-sm" data-dismiss="modal">No</button>
       				<button type="button" class="btn btn-primary btn-sm" id="tipoBoton" onclick="eliminarProductoDespacho()">Si</button>
     			</div>
   			</div>
 		</div>
	</div>
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

<script src="<?php echo $baseUrl; ?>/theme/js/plugins/dataTables/datatables.min.js"></script>

<!-- Custom and plugin javascript -->
<script src="<?php echo $baseUrl; ?>/theme/js/inspinia.js"></script>
<script src="<?php echo $baseUrl; ?>/theme/js/plugins/pace/pace.min.js"></script>
<script src="<?php echo $baseUrl; ?>/theme/js/plugins/toastr/toastr.min.js"></script>
<script src="<?php echo $baseUrl; ?>/theme/js/plugins/validate/jquery.validate.min.js"></script>
<script src="<?php echo $baseUrl; ?>/theme/js/plugins/select2/select2.full.min.js"></script>

<!-- Section Scripts -->
<script src="<?php echo $baseUrl; ?>/modules/insumos2/js/editarDespacho.js"></script>
<?php mysqli_close($Link); ?>

</body>
</html> 		