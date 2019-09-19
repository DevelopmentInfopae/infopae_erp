<?php
  require_once '../../../db/conexion.php';
  require_once '../../../config.php';

  $codigo = (isset($_POST['codigoAEditar']) && $_POST['codigoAEditar'] != '') ? mysqli_real_escape_string($Link, $_POST['codigoAEditar']) : '';
  $nombre = (isset($_POST['nombreAEditar']) && $_POST['nombreAEditar'] != '') ? mysqli_real_escape_string($Link, $_POST['nombreAEditar']) : '';

  $consulta1 = "UPDATE tipovehiculo SET Nombre = '$nombre' WHERE Id = '$codigo';";
  $resultado1 = $Link->query($consulta1) or die('Error al actualizar el Tipo de vehículo: '. mysqli_error($Link));
  if($resultado1)
  {
    $consultaBitacora = "INSERT INTO bitacora (fecha, usuario, tipo_accion, observacion) VALUES ('" . date("Y-m-d H-i-s") . "', '" . $_SESSION["idUsuario"] . "', '24', 'Se actualizó el Tipo de vehículo: <strong>$nombre</strong>')";
    $Link->query($consultaBitacora) or die ('Unable to execute query. '. mysqli_error($Link));

  	$respuestaAJAX = [
  		'estado' => 1,
  		'mensaje' => 'El Tipo de vehículo se actualizó exitosamente.'
  	];
  }
  else
  {
  	$respuestaAJAX = [
  		'estado' => 0,
  		'mensaje' => 'El Tipo de vehículo NO se actualizó exitosamente.'
  	];
  }

  echo json_encode($respuestaAJAX);