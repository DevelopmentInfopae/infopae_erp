<?php
require_once '../../../db/conexion.php';
require_once '../../../config.php';

$tipo = (isset($_POST['tipo']) && $_POST['tipo'] != '') ? mysqli_real_escape_string($Link, $_POST['tipo']) : '';
$entidad = (isset($_POST['entidad']) && $_POST['entidad'] != '') ? mysqli_real_escape_string($Link, $_POST['entidad']) : '';

// validacion para que no se pueda crear una misma entidad del mismo tipo 
$consultaValidacionEntidad = "SELECT Entidad FROM nomina_entidad WHERE Tipo = '$tipo' AND Entidad = '$entidad';";
$resultadoValiacionEntidad = $Link->query($consultaValidacionEntidad) or die('Error al consultar las entidades');
if ($resultadoValiacionEntidad->num_rows > 0) {
    $respuestaAJAX = [
      'estado' => 0,
      'mensaje' => 'Ya existe una entidad de ese tipo y ese nombre'
    ];
    exit(json_encode($respuestaAJAX));
}

$sentencia = "INSERT INTO nomina_entidad (Tipo, Entidad) VALUES ('$tipo','$entidad');";
$resultado = $Link->query($sentencia) or die('Error al crear la entidad: '. mysqli_error($Link));
  if($resultado)
  {
    $consultaBitacora = "INSERT INTO bitacora (fecha, usuario, tipo_accion, observacion) VALUES ('" . date("Y-m-d H-i-s") . "', '" . $_SESSION["idUsuario"] . "', '57', 'Se creó la nómina entidad: $entidad')";
    $Link->query($consultaBitacora) or die ('Unable to execute query. '. mysqli_error($Link));

  	$respuestaAJAX = [
  		'estado' => 1,
  		'mensaje' => 'La Nómina Entidad se creó exitosamente.'
  	];
  }
  else
  {
  	$respuestaAJAX = [
  		'estado' => 0,
  		'mensaje' => 'La Nómina Entidad NO se creó exitosamente.'
  	];
  }

  echo json_encode($respuestaAJAX);