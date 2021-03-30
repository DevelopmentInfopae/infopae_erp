<?php
 require_once '../../../db/conexion.php';
 require_once '../../../config.php';

$id = (isset($_POST['id']) && $_POST['id'] != '') ? mysqli_real_escape_string($Link, $_POST['id']) : '';

$sentenciaEliminar = "DELETE FROM manipuladoras_valoresnomina WHERE ID = '".$id."';";
$resEliminar = $Link->query($sentenciaEliminar) or die('Error al eliminar el valor manipuladora nómina '. mysqli_error($Link));
  if($resEliminar){
    $consultaBitacora = "INSERT INTO bitacora (fecha, usuario, tipo_accion, observacion) VALUES ('" . date("Y-m-d H-i-s") . "', '" . $_SESSION["idUsuario"] . "', '56', 'Se elimino el valor manipuladora nómina: <strong>".$id."</strong>')";
    $Link->query($consultaBitacora) or die ('Unable to execute query. '. mysqli_error($Link));

  	$respuestaAJAX = [
  		'estado' => 1,
  		'mensaje' => 'El valor manipuladora nómina se elimino exitosamente'
  	];
  }
  else
  {
  	$respuestaAJAX = [
  		'estado' => 0,
  		'mensaje' => 'El valor manipuladora nómina NO se actualizó exitosamente.'
  	];
  }

  echo json_encode($respuestaAJAX);