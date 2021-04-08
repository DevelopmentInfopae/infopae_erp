<?php
require_once '../../../db/conexion.php';
require_once '../../../config.php';

$id = (isset($_POST['id']) && $_POST['id'] != '') ? mysqli_real_escape_string($Link, $_POST['id']) : '';

// sentencia buscar para el llenar la bitacora 
$sentenciaBuscar = "SELECT descripcion FROM parametros_infraestructura WHERE id = '$id';";
$resSentenciaBuscar = $Link->query($sentenciaBuscar) or die('Error al consultar el parámetro infraestructura '. mysqli_error($Link));
if ($resSentenciaBuscar->num_rows > 0) {
    $DataSentenciaBuscar = $resSentenciaBuscar->fetch_assoc();
    $descripcion = $DataSentenciaBuscar['descripcion'];
}

$sentenciaEliminar = "DELETE FROM parametros_infraestructura WHERE ID = '".$id."';";
// exit(var_dump($sentenciaEliminar));
$resEliminar = $Link->query($sentenciaEliminar) or die('Error al eliminar el parámetro infraestructura '. mysqli_error($Link));
  if($resEliminar){
    $consultaBitacora = "INSERT INTO bitacora (fecha, usuario, tipo_accion, observacion) VALUES ('" . date("Y-m-d H-i-s") . "', '" . $_SESSION["idUsuario"] . "', '71', 'Se eliminó el parámetro infraestructura: <strong>".$descripcion."</strong>')";
    $Link->query($consultaBitacora) or die ('Unable to execute query. '. mysqli_error($Link));

  	$respuestaAJAX = [
  		'estado' => 1,
  		'mensaje' => 'El parámetro infraestructura se eliminó exitosamente'
  	];
  }
  else
  {
  	$respuestaAJAX = [
  		'estado' => 0,
  		'mensaje' => 'El parámetro infraestructura NO se eliminó exitosamente.'
  	];
  }

  echo json_encode($respuestaAJAX);