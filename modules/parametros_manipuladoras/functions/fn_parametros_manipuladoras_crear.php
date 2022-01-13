<?php 
require_once '../../../db/conexion.php';
require_once '../../../config.php';

$complemento = (isset($_POST['complemento']) && $_POST['complemento'] != '') ? mysqli_real_escape_string($Link, $_POST['complemento']) : '';
$cantidad = (isset($_POST['cantidad']) && $_POST['cantidad'] != '') ? mysqli_real_escape_string($Link, $_POST['cantidad']) : '';
$limiteInferior = (isset($_POST['limiteInferior']) && $_POST['limiteInferior'] != '') ? mysqli_real_escape_string($Link, $_POST['limiteInferior']) : '';
$limiteSuperior = (isset($_POST['limiteSuperior']) && $_POST['limiteSuperior'] != '') ? mysqli_real_escape_string($Link, $_POST['limiteSuperior']) : '';

if ($limiteInferior > $limiteSuperior) {
      $respuestaAJAX = [
       'estado' => 0,
       'mensaje' => 'El límite inferor no puede ser mayor al límite superior'
      ];
      exit (json_encode($respuestaAJAX));
}

// se valida que los limites no puedan quedar con el mismo valor
if ($limiteInferior == $limiteSuperior) {
      $respuestaAJAX = [
       'estado' => 0,
       'mensaje' => 'El límite inferor no puede ser igual al límite superior'
      ];
      exit (json_encode($respuestaAJAX));
}

$validacionCantidad = "SELECT cant_manipuladora FROM parametros_manipuladoras WHERE cant_manipuladora = '$cantidad' AND tipo_complem = '$complemento';";
$resValidacionCantidad = $Link->query($validacionCantidad) or die('Error al consultar la cantidad '. mysqli_error($Link));
if ($resValidacionCantidad->num_rows == 1) {
	$respuestaAJAX = [
       'estado' => 0,
       'mensaje' => 'Ya existe un registro para ese complemento y esa cantidad de manipuladoras'
      ];
      exit (json_encode($respuestaAJAX));
}

$validacionRangos = "SELECT limite_inferior, limite_superior FROM parametros_manipuladoras WHERE tipo_complem = '$complemento';";
$respuestaValidacionRangos = $Link->query($validacionRangos) or die('Error al consultar los limites '. mysqli_error($Link));
if ($respuestaValidacionRangos->num_rows > 0) {
      while ($DataValidacionRangos = $respuestaValidacionRangos->fetch_assoc()) {
            // exit(var_dump($DataValidacionRangos));
            if ($limiteInferior >= $DataValidacionRangos['limite_inferior'] && $limiteInferior <= $DataValidacionRangos['limite_superior']) {
                 $respuestaAJAX = [
                 'estado' => 0,
                 'mensaje' => 'El límite inferior esta comprendido en un rango ya existente'
                  ];
                  exit (json_encode($respuestaAJAX));
            }
            elseif ($limiteSuperior >=$DataValidacionRangos['limite_inferior'] && $limiteSuperior <= $DataValidacionRangos['limite_superior']) {
                  $respuestaAJAX = [
                 'estado' => 0,
                 'mensaje' => 'El límite superior esta comprendido en un rango ya existente'
                  ];
                  exit (json_encode($respuestaAJAX));
            }
      }
}

$limiteTemporal = '';
// validacion para que el limte superio no se sobredimensione
$validacionLimiteSuperior = "SELECT ID, limite_superior FROM parametros_manipuladoras WHERE limite_superior = (SELECT MAX(limite_superior) FROM parametros_manipuladoras WHERE tipo_complem = '$complemento'); " ;
// $idSinEspacio = trim($id);
$respuestaValidacionLimiteSuperior = $Link->query($validacionLimiteSuperior) or die('Error al consultar el límite superior '. mysqli_error($Link));
if ($respuestaValidacionLimiteSuperior->num_rows > 0) {
  $DataValidacionLimiteSuperior = $respuestaValidacionLimiteSuperior->fetch_assoc();
  if ($limiteInferior > $DataValidacionLimiteSuperior['limite_superior'] && $limiteSuperior > $DataValidacionLimiteSuperior['limite_superior']) {
    $limiteTemporal = 'ingresando rangos mayores al ultimo';
    $sentenciaInsert = "INSERT INTO  parametros_manipuladoras (tipo_complem, cant_manipuladora, limite_inferior, limite_superior) VALUES ('$complemento', '$cantidad', '$limiteInferior', '$limiteSuperior');";
    $respuestaInsert = $Link->query($sentenciaInsert) or die('Error al insertar el parámetro manipuladora'. mysqli_error($Link));
    if($respuestaInsert) {
      $consultaBitacora = "INSERT INTO bitacora (fecha, usuario, tipo_accion, observacion) VALUES ('" . date("Y-m-d H-i-s") . "', '" . $_SESSION["idUsuario"] . "', '63', 'Se creó el parámetro manipuladora: <strong>".$complemento.' con límite inferior '. $limiteInferior .' y límite superior '. $limiteSuperior ."</strong>')";
      $Link->query($consultaBitacora) or die ('Unable to execute query. '. mysqli_error($Link));

      $respuestaAJAX = [
            'estado' => 1,
            'mensaje' => 'El parámetro manipuladora se creó exitosamente.'
      ];
      exit (json_encode($respuestaAJAX));
    }
    else
    {
      $respuestaAJAX = [
            'estado' => 0,
            'mensaje' => 'El parámetro manipuladora NO se creó exitosamente.'
      ];
      exit (json_encode($respuestaAJAX));
    }
  } elseif ($limiteSuperior > $DataValidacionLimiteSuperior['limite_superior']) {
      $respuestaAJAX = [
      'estado' => 0,
      'mensaje' => 'El límite superior esta sobredimensionado'
      ];
      exit (json_encode($respuestaAJAX));
  }
}


$sentenciaInsert = "INSERT INTO  parametros_manipuladoras (tipo_complem, cant_manipuladora, limite_inferior, limite_superior) VALUES ('$complemento', '$cantidad', '$limiteInferior', '$limiteSuperior');";
$respuestaInsert = $Link->query($sentenciaInsert) or die('Error al insertar el parámetro manipuladora'. mysqli_error($Link));
  if($respuestaInsert) {
    $consultaBitacora = "INSERT INTO bitacora (fecha, usuario, tipo_accion, observacion) VALUES ('" . date("Y-m-d H-i-s") . "', '" . $_SESSION["idUsuario"] . "', '63', 'Se creó el parámetro manipuladora: <strong>".$complemento.' con límite inferior '. $limiteInferior .' y límite superior '. $limiteSuperior ."</strong>')";
    $Link->query($consultaBitacora) or die ('Unable to execute query. '. mysqli_error($Link));

      $respuestaAJAX = [
            'estado' => 1,
            'mensaje' => 'El parámetro manipuladora se creó exitosamente.'
      ];
  }
  else
  {
      $respuestaAJAX = [
            'estado' => 0,
            'mensaje' => 'El parámetro manipuladora NO se creó exitosamente.'
      ];
  }

  echo json_encode($respuestaAJAX);