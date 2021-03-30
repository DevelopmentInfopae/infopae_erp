<?php
require_once '../../../db/conexion.php';
require_once '../../../config.php';

$tipo = (isset($_POST['tipo']) && $_POST['tipo'] != '') ? mysqli_real_escape_string($Link, $_POST['tipo']) : '';
$entidad = (isset($_POST['entidad']) && $_POST['entidad'] != '') ? mysqli_real_escape_string($Link, $_POST['entidad']) : '';

$sentencia = "INSERT INTO nomina_entidad (Tipo, Entidad) VALUES ('$tipo','$entidad');";
$resultado = $Link->query($sentencia) or die('Error al crear la entidad: '. mysqli_error($Link));
  if($resultado)
  {
    $consultaBitacora = "INSERT INTO bitacora (fecha, usuario, tipo_accion, observacion) VALUES ('" . date("Y-m-d H-i-s") . "', '" . $_SESSION["idUsuario"] . "', '57', 'Se creó la nómina entidad: $entidad')";
    $Link->query($consultaBitacora) or die ('Unable to execute query. '. mysqli_error($Link));

  	$respuestaAJAX = [
  		'estado' => 1,
  		'mensaje' => 'La Nómina Entidad fue creada exitosamente.'
  	];
  }
  else
  {
  	$respuestaAJAX = [
  		'estado' => 0,
  		'mensaje' => 'La Nómina Entidad NO se creo exitosamente.'
  	];
  }

  echo json_encode($respuestaAJAX);