<?php
require_once '../../../db/conexion.php';
require_once '../../../config.php';

$codigo = (isset($_POST['codigoAEditar']) && $_POST['codigoAEditar'] != '') ? mysqli_real_escape_string($Link, $_POST['codigoAEditar']) : '';
$nombre = (isset($_POST['nombreAEditar']) && $_POST['nombreAEditar'] != '') ? mysqli_real_escape_string($Link, $_POST['nombreAEditar']) : '';
$redondeo = (isset($_POST['redondeo']) && $_POST['redondeo'] != '') ? mysqli_real_escape_string($Link, $_POST['redondeo']) : '';

$consulta1 = "UPDATE tipo_despacho SET Descripcion = '$nombre', Redondeo = '$redondeo' WHERE Id = '$codigo';";
$resultado1 = $Link->query($consulta1) or die('Error al actualizar el Tipo de despacho: '. mysqli_error($Link));
if($resultado1) {
    $consultaBitacora = "INSERT INTO bitacora (fecha, usuario, tipo_accion, observacion) VALUES ('" . date("Y-m-d H-i-s") . "', '" . $_SESSION["idUsuario"] . "', '24', 'Se actualizó el Tipo de despacho: <strong>$nombre</strong>')";
    $Link->query($consultaBitacora) or die ('Unable to execute query. '. mysqli_error($Link));
  	$respuestaAJAX = [
  		'estado' => 1,
  		'mensaje' => 'El Tipo de despacho se actualizó exitosamente.'
  	];
} else {
  	$respuestaAJAX = [
  		'estado' => 0,
  		'mensaje' => 'El Tipo de despacho NO se actualizó exitosamente.'
  	];
}
echo json_encode($respuestaAJAX);