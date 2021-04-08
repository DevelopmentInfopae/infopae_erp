<?php
require_once '../../../db/conexion.php';
require_once '../../../config.php';

// recibimos los parametros para actualizar todos los campos de la tabla nomina riesgos
$id = (isset($_POST['idNominaRiesgos']) && $_POST['idNominaRiesgos'] != '') ? mysqli_real_escape_string($Link, $_POST['idNominaRiesgos']) : '';
$tipo = (isset($_POST['tipo']) && $_POST['tipo'] != '') ? mysqli_real_escape_string($Link, $_POST['tipo']) : '';
$porcentaje = (isset($_POST['porcentaje']) && $_POST['porcentaje'] != '') ? mysqli_real_escape_string($Link, $_POST['porcentaje']) : '';

$consultaValidacion = "SELECT Tipo FROM nomina_riesgos WHERE ID = '$id';";
$respuestaValidacion = $Link->query($consultaValidacion) or die('Error al consultar la nomina riesgos');
if ($respuestaValidacion->num_rows > 0) {
    $dataValidacion = $respuestaValidacion->fetch_assoc();
    $tipoTabla = $dataValidacion['Tipo'];
    if ($tipoTabla !== $tipo) {
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
    }
}



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