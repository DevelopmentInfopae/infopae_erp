<?php
  require_once '../../../db/conexion.php';
  require_once '../../../config.php';

  $codigo = (isset($_POST['codigo']) && $_POST['codigo'] != '') ? mysqli_real_escape_string($Link, $_POST['codigo']) : '';
  $consulta = "SELECT * FROM tipovehiculo WHERE Id = '$codigo';";
  $resultado = $Link->query($consulta) or die('Error al consultar el tipo de vehículo: '. mysqli_error($Link));
  if($resultado->num_rows > 0)
  {
    $registros = $resultado->fetch_assoc();
    $nombre = $registros['Nombre'];
  }

  $consulta1 = "DELETE FROM tipovehiculo WHERE Id = '$codigo';";
  $resultado1 = $Link->query($consulta1) or die('Error al eliminar el tipo de vehículo: '. mysqli_error($Link));
  if($resultado1)
  {
    $consultaBitacora = "INSERT INTO bitacora (fecha, usuario, tipo_accion, observacion) VALUES ('" . date("Y-m-d H-i-s") . "', '" . $_SESSION["idUsuario"] . "', '28', 'Eliminó el tipo de vehículo: <strong>$nombre</strong>')";
    $Link->query($consultaBitacora) or die ('Unable to execute query. '. mysqli_error($Link));

  	$respuestaAJAX = [
  		'estado' => 1,
  		'mensaje' => 'El Tipo de vehículo se eliminó exitosamente.'
  	];
  }
  else
  {
  	$respuestaAJAX = [
  		'estado' => 0,
  		'mensaje' => 'El Tipo de vehículo NO se eliminó exitosamente.'
  	];
  }

  echo json_encode($respuestaAJAX);