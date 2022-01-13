<option value="">Seleccione...</option>
<?php 
require_once '../../../db/conexion.php';

$id = (isset($_POST['tipoDocumento']) && $_POST['tipoDocumento']!= "") ? $_POST['tipoDocumento'] : 0 ;
$responsablePost = (isset($_POST['responsable']) && $_POST['responsable'] != "") ? $_POST['responsable'] : "";

$consultaTipoMovimiento = " SELECT TipoTercero FROM tipomovimiento WHERE id = $id ";
$respuestaTipoMovimiento = $Link->query($consultaTipoMovimiento) or die ('Error al consultar el responsable ' . mysqli_error($Link));
if ($respuestaTipoMovimiento->num_rows > 0) {
	$dataTipoMovimiento = $respuestaTipoMovimiento->fetch_assoc();
	$tercero = strtolower($dataTipoMovimiento['TipoTercero']);
}

$consulta = '';
if ($tercero != "" || $tercero != null) {
	if ($tercero == "proveedor") {
		$consulta = " SELECT Nitcc, Nombrecomercial AS Nombre FROM proveedores  ORDER BY  Nombre ASC ";
	}
	if ($tercero == "empleado") {
		$consulta = " SELECT Nitcc, Nombre FROM empleados ORDER BY Nombre ASC ";
	}
	$respuesta = $Link->query($consulta) or die ('Error al consultar el responsable ' . mysqli_error($Link));
	if ($respuesta->num_rows > 0) {
		while ($dataRespuesta = $respuesta->fetch_assoc()) {
			$responsables[$dataRespuesta['Nitcc']] = $dataRespuesta['Nombre'];
		}
	}
}	
foreach ($responsables as $nit => $nombre) { ?>
	<option value="<?= $nit; ?>" <?= (isset($responsablePost) && $responsablePost == $nit) ? "selected" : "" ?> ><?= $nombre; ?></option>
<?php } ?>
