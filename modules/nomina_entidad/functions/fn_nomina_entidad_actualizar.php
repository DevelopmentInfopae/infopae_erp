<?php
require_once '../../../db/conexion.php';
require_once '../../../config.php';

// recibimos los parametros para actualizar todos los campos de la tabla nomina entidad
$id = (isset($_POST['idNominaEntidad']) && $_POST['idNominaEntidad'] != '') ? mysqli_real_escape_string($Link, $_POST['idNominaEntidad']) : '';
$tipo = (isset($_POST['tipo']) && $_POST['tipo'] != '') ? mysqli_real_escape_string($Link, $_POST['tipo']) : '';
$entidad = (isset($_POST['entidad']) && $_POST['entidad'] != '') ? mysqli_real_escape_string($Link, $_POST['entidad']) : '';

$sentencia = "UPDATE nomina_entidad SET Tipo = '$tipo', Entidad = '$entidad' WHERE ID = '$id';";
$resultado = $Link->query($sentencia) or die('Error al actualizar la nómina entidad'. mysqli_error($Link));
if($resultado)
  {
  $consultaBitacora = "INSERT INTO bitacora (fecha, usuario, tipo_accion, observacion) VALUES ('" . date("Y-m-d H-i-s") . "', '" . $_SESSION["idUsuario"] . "', '58', 'Se actualizó la nómina entidad $entidad ')";
  $Link->query($consultaBitacora) or die ('Unable to execute query. '. mysqli_error($Link));

  $respuestaAJAX = [
  		'estado' => 1,
  		'mensaje' => 'La Nómina Entidad se actualizó exitosamente.'
  	];
  }
  else
  {
  	$respuestaAJAX = [
  		'estado' => 0,
  		'mensaje' => 'La Nómina Entidad NO se actualizó exitosamente.'
  	];
  }

  echo json_encode($respuestaAJAX);