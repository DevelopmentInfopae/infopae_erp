<?php
require_once '../../../db/conexion.php';
require_once '../../../config.php';

$id = (isset($_POST['id']) && $_POST['id'] != '') ? mysqli_real_escape_string($Link, $_POST['id']) : '';

$sentenciaEliminar = "DELETE FROM parametros_nomina WHERE hora_mes = '".$id."';";
// exit(var_dump($sentenciaEliminar));
$resEliminar = $Link->query($sentenciaEliminar) or die('Error al eliminar el parámetro nómina '. mysqli_error($Link));
  if($resEliminar){
    $consultaBitacora = "INSERT INTO bitacora (fecha, usuario, tipo_accion, observacion) VALUES ('" . date("Y-m-d H-i-s") . "', '" . $_SESSION["idUsuario"] . "', '68', 'Se eliminó el parámetro nómina de horas: <strong>".$id."</strong>')";
   	$Link->query($consultaBitacora) or die ('Unable to execute query. '. mysqli_error($Link));

  	$respuestaAJAX = [
  		'estado' => 1,
  		'mensaje' => 'El parámetro nómina se eliminó exitosamente'
  	];
  }
  else
  {
  	$respuestaAJAX = [
  		'estado' => 0,
  		'mensaje' => 'El parámetro nómina NO se eliminó exitosamente.'
  	];
  }

  echo json_encode($respuestaAJAX);