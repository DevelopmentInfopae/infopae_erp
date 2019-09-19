<?php
  require_once '../../../db/conexion.php';
  require_once '../../../config.php';

  $nombre = (isset($_POST['nombre']) && $_POST['nombre'] != '') ? mysqli_real_escape_string($Link, $_POST['nombre']) : '';

  $consulta1 = "INSERT INTO tipovehiculo (Nombre) VALUES ('$nombre');";
  $resultado1 = $Link->query($consulta1) or die('Error al crear Tipo vehiculo: '. mysqli_error($Link));
  if($resultado1)
  {
    $consultaBitacora = "INSERT INTO bitacora (fecha, usuario, tipo_accion, observacion) VALUES ('" . date("Y-m-d H-i-s") . "', '" . $_SESSION["idUsuario"] . "', '27', 'Se creó el Tipo de vehículo: <strong>$nombre</strong>')";
    $Link->query($consultaBitacora) or die ('Unable to execute query. '. mysqli_error($Link));

  	$respuestaAJAX = [
  		'estado' => 1,
  		'mensaje' => 'El Tipo de vehículo se creo exitosamente.'
  	];
  }
  else
  {
  	$respuestaAJAX = [
  		'estado' => 0,
  		'mensaje' => 'El Tipo de vehículo NO se creo exitosamente.'
  	];
  }

  echo json_encode($respuestaAJAX);