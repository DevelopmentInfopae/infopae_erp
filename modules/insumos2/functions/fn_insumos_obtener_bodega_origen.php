<?php 

require_once '../../../config.php';
require_once '../../../db/conexion.php';

$tipoDespacho = ((isset($_POST['tipoDespacho']) && $_POST['tipoDespacho'] != "") ? $_POST['tipoDespacho'] : "");
$proveedor = ((isset($_POST['proveedor']) && $_POST['proveedor'] != "") ? $_POST['proveedor'] : "");

$consTipoDespacho = "SELECT * FROM tipomovimiento WHERE Id = '".$tipoDespacho."'";
$resTipoDespacho = $Link->query($consTipoDespacho);
if ($resTipoDespacho->num_rows > 0) {
    while ($dataTipoDespacho = $resTipoDespacho->fetch_assoc()) {
      	$nombre = $dataTipoDespacho['Movimiento'];
      	$documento = $dataTipoDespacho['Documento'];
    }
}


if (strpos($nombre, "Proveedor") && $documento == "DESI") {
  	$consulta = "SELECT bodegas.ID AS id, bodegas.NOMBRE AS nombre FROM bodegas
  				INNER JOIN proveedores ON proveedores.Nitcc = '".$proveedor."'
  				INNER JOIN usuarios ON usuarios.num_doc = proveedores.Nitcc
  				INNER JOIN usuarios_bodegas AS ub ON ub.USUARIO = usuarios.id
  				AND bodegas.ID = ub.COD_BODEGA_SALIDA
  				GROUP BY bodegas.ID
  	";
} else if (strpos($nombre, "Operador") && $documento == "DESI") {
    $consulta = "SELECT bodegas.ID AS id, bodegas.NOMBRE AS nombre FROM bodegas
            	INNER JOIN empleados ON empleados.Nitcc = '".$proveedor."'
            	INNER JOIN usuarios ON usuarios.num_doc = empleados.Nitcc
            	INNER JOIN usuarios_bodegas AS ub ON ub.USUARIO = usuarios.id
            	AND bodegas.ID = ub.COD_BODEGA_SALIDA
            	GROUP BY bodegas.ID
    ";
}

$respuesta = $Link->query($consulta) or die ('Error al consultar las bodegas ' . mysqli_error($Link));
if ($respuesta->num_rows > 0) {
	while ($dataRespuesta = $respuesta->fetch_assoc()) {
		$bodegas[$dataRespuesta['id']] = $dataRespuesta['nombre'];
	}
}

?>
<?php if (isset($bodegas)): ?>
	<?php foreach ($bodegas as $id => $nombre): ?>
		<option value="<?= $id; ?>"><?= $nombre; ?></option>
	<?php endforeach ?>
<?php else: ?>
	<option value="">Sin bodegas para el proveedor seleccionado</option>	
<?php endif ?>
