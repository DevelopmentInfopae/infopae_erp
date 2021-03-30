<?php
require_once '../../../db/conexion.php';
require_once '../../../config.php';

// recibimos los parametros para actualizar todos los campos de la tabla nomina riesgos
$id = (isset($_POST['idNominaRiesgos']) && $_POST['idNominaRiesgos'] != '') ? mysqli_real_escape_string($Link, $_POST['idNominaRiesgos']) : '';
$tipo = (isset($_POST['tipo']) && $_POST['tipo'] != '') ? mysqli_real_escape_string($Link, $_POST['tipo']) : '';
$porcentaje = (isset($_POST['porcentaje']) && $_POST['porcentaje'] != '') ? mysqli_real_escape_string($Link, $_POST['porcentaje']) : '';


$sentencia = "UPDATE nomina_riesgos SET Tipo = '$tipo', Porcentaje = '$porcentaje' WHERE ID = '$id';";
$resultado = $Link->query($sentencia) or die('Error al actualizar la nómina riesgos'. mysqli_error($Link));
if($resultado)
  {
  $consultaBitacora = "INSERT INTO bitacora (fecha, usuario, tipo_accion, observacion) VALUES ('" . date("Y-m-d H-i-s") . "', '" . $_SESSION["idUsuario"] . "', '61', 'Se actualizó la nómina riesgos $tipo ')";
  $Link->query($consultaBitacora) or die ('Unable to execute query. '. mysqli_error($Link));

  $respuestaAJAX = [
  		'estado' => 1,
  		'mensaje' => 'La Nómina Riesgos se actualizó exitosamente.'
  	];
  }
  else
  {
  	$respuestaAJAX = [
  		'estado' => 0,
  		'mensaje' => 'La Nómina Riesgos NO se actualizó exitosamente.'
  	];
  }

  echo json_encode($respuestaAJAX);