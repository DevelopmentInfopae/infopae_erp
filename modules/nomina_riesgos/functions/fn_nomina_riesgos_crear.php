<?php
require_once '../../../db/conexion.php';
require_once '../../../config.php';

$tipo = (isset($_POST['tipo']) && $_POST['tipo'] != '') ? mysqli_real_escape_string($Link, $_POST['tipo']) : '';
$porcentaje = (isset($_POST['porcentaje']) && $_POST['porcentaje'] != '') ? mysqli_real_escape_string($Link, $_POST['porcentaje']) : '';

// validacion para no crear el mismo tipo de nomina riesgos
$sentenciaValidacion = "SELECT tipo FROM nomina_riesgos WHERE Tipo = '$tipo';";
$resultadoValidacion = $Link->query($sentenciaValidacion) or die('Error al consultar la nómina riesgos: '. mysqli_error($Link));
if ($resultadoValidacion->num_rows > 0) {
    $respuestaAJAX = [
      'estado' => 0,
      'mensaje' => 'La Nómina Riesgos ya existe.'
    ];
    exit(json_encode($respuestaAJAX)); 
}

$sentencia = "INSERT INTO nomina_riesgos (Tipo, Porcentaje) VALUES ('$tipo','$porcentaje');";
$resultado = $Link->query($sentencia) or die('Error al crear la nómina riesgos: '. mysqli_error($Link));
  if($resultado)
  {
    $consultaBitacora = "INSERT INTO bitacora (fecha, usuario, tipo_accion, observacion) VALUES ('" . date("Y-m-d H-i-s") . "', '" . $_SESSION["idUsuario"] . "', '60', 'Se creó la nómina riesgos: $tipo')";
    $Link->query($consultaBitacora) or die ('Unable to execute query. '. mysqli_error($Link));

  	$respuestaAJAX = [
  		'estado' => 1,
  		'mensaje' => 'La Nómina Riesgos se creó exitosamente.'
  	];
  }
  else
  {
  	$respuestaAJAX = [
  		'estado' => 0,
  		'mensaje' => 'La Nómina Riesgos NO se creó exitosamente.'
  	];
  }

  echo json_encode($respuestaAJAX);