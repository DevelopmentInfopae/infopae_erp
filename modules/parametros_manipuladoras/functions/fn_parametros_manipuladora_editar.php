<?php
require_once '../../../db/conexion.php';
require_once '../../../config.php';

$id = (isset($_POST['id']) && $_POST['id'] != '') ? mysqli_real_escape_string($Link, $_POST['id']) : '';
$tipoComplemento = (isset($_POST['tipoComplemento']) && $_POST['tipoComplemento'] != '') ? mysqli_real_escape_string($Link, $_POST['tipoComplemento']) : '';
$cantidad = (isset($_POST['cantidad']) && $_POST['cantidad'] != '') ? mysqli_real_escape_string($Link, $_POST['cantidad']) : '';
$limiteInferior = (isset($_POST['limiteInferior']) && $_POST['limiteInferior'] != '') ? mysqli_real_escape_string($Link, $_POST['limiteInferior']) : '';
$limiteSuperior = (isset($_POST['limiteSuperior']) && $_POST['limiteSuperior'] != '') ? mysqli_real_escape_string($Link, $_POST['limiteSuperior']) : '';


// validamos que el limite inferior no pueda ser mayor al limite superior
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

// validacion para actualizar solo los limites inferior y superior con la misma cantidad de manipuladoras
$limiteTemporal = '';
$validacionMismaManipuladora = "SELECT cant_manipuladora FROM parametros_manipuladoras WHERE ID = '$id';";
$resValidacionMismaManipuladora = $Link->query($validacionMismaManipuladora) or die('Error al consultar la cantidad de manipuladoras '. mysqli_error($Link));
if($resValidacionMismaManipuladora->num_rows > 0){
  $dataValidacionMismaManipuladora = $resValidacionMismaManipuladora->fetch_assoc();
  $cantidadTabla = $dataValidacionMismaManipuladora['cant_manipuladora'];
  if ($cantidadTabla == $cantidad) {

    // validacion para que los limites no se crucen entre ellos 
    $validacionRangos = "SELECT limite_inferior, limite_superior FROM parametros_manipuladoras WHERE tipo_complem = '$tipoComplemento' AND ID != '$id';";
		$respuestaValidacionRangos = $Link->query($validacionRangos) or die('Error al consultar los límites '. mysqli_error($Link));
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
        if ($limiteSuperior >= $DataValidacionRangos['limite_inferior'] && $limiteSuperior <= $DataValidacionRangos['limite_superior']) {
          $respuestaAJAX = [
            'estado' => 0,
            'mensaje' => 'El límite superior esta comprendido en un rango ya existente'
          ];
          exit (json_encode($respuestaAJAX));
        }
      }
		}

		// validacion para que el limte superio no se sobredimensione
		$validacionLimiteSuperior = "SELECT ID, limite_superior FROM parametros_manipuladoras WHERE limite_superior = (SELECT MAX(limite_superior) FROM parametros_manipuladoras )";
		$idSinEspacio = trim($id);
		$respuestaValidacionLimiteSuperior = $Link->query($validacionLimiteSuperior) or die('Error al consultar el límite superior '. mysqli_error($Link));
		if ($respuestaValidacionLimiteSuperior->num_rows > 0) {
			$DataValidacionLimiteSuperior = $respuestaValidacionLimiteSuperior->fetch_assoc();
			if ($idSinEspacio == $DataValidacionLimiteSuperior['ID']) {
				$limiteTemporal = 'Este id es el del el limite superior mas alto de ese complemento';
			}
			if ($limiteSuperior > $DataValidacionLimiteSuperior['limite_superior']) {
				$respuestaAJAX = [
          'estado' => 0,
          'mensaje' => 'El límite superior esta sobredimensionado'
        ];
        exit (json_encode($respuestaAJAX));
			}
		}

    $sentenciaEditar = "UPDATE parametros_manipuladoras SET limite_inferior = '$limiteInferior', limite_superior = '$limiteSuperior' WHERE ID = '".$id."';";
  	$repuestaEditar = $Link->query($sentenciaEditar) or die('Error al actualizar'. mysqli_error($Link));

  		if($repuestaEditar){
    		$consultaBitacora = "INSERT INTO bitacora (fecha, usuario, tipo_accion, observacion) VALUES ('" . date("Y-m-d H-i-s") . "', '" . $_SESSION["idUsuario"] . "', '64', 'Se actualizó el parámetro manipuladora: <strong>".$tipoComplemento.' con límite inferior '. $limiteInferior .' y límite superior '. $limiteSuperior ."</strong>')";
   			$Link->query($consultaBitacora) or die ('Unable to execute query. '. mysqli_error($Link));

  			$respuestaAJAX = [
  			'estado' => 1,
  			'mensaje' => 'El Parámetro manipuladora se actualizó exitosamente.'
  			];
  			exit (json_encode($respuestaAJAX));
    	}
  	}
    // seccion donde vamos a validar si en la actualizacion se cambia de numero de manipuladoras
    else{
      // validacion cantidad de manipuladoras
      $validacionCantidad = "SELECT cant_manipuladora FROM parametros_manipuladoras WHERE cant_manipuladora = '$cantidad' AND tipo_complem = '$tipoComplemento';";
      $resValidacionCantidad = $Link->query($validacionCantidad) or die('Error al consultar la cantidad '. mysqli_error($Link));
      if ($resValidacionCantidad->num_rows == 1) {
        $respuestaAJAX = [
          'estado' => 0,
          'mensaje' => 'Ya existe un registro para ese complemento y esa cantidad de manipuladoras'
          ];
        exit (json_encode($respuestaAJAX));
      }

      // validacion para que los limites no se crucen entre ellos 
      $validacionRangos = "SELECT limite_inferior, limite_superior FROM parametros_manipuladoras WHERE tipo_complem = '$tipoComplemento' AND ID != '$id';";
      $respuestaValidacionRangos = $Link->query($validacionRangos) or die('Error al consultar los límites '. mysqli_error($Link));
      if ($respuestaValidacionRangos->num_rows > 0) {
        while ($DataValidacionRangos = $respuestaValidacionRangos->fetch_assoc()) {
          if ($limiteInferior >= $DataValidacionRangos['limite_inferior'] && $limiteInferior <= $DataValidacionRangos['limite_superior']) {
            $respuestaAJAX = [
              'estado' => 0,
              'mensaje' => 'El límite inferior esta comprendido en un rango ya existente'
            ];
            exit (json_encode($respuestaAJAX));
          }
          if ($limiteSuperior >= $DataValidacionRangos['limite_inferior'] && $limiteSuperior <= $DataValidacionRangos['limite_superior']) {
            $respuestaAJAX = [
              'estado' => 0,
              'mensaje' => 'El límite superior esta comprendido en un rango ya existente'
            ];
            exit (json_encode($respuestaAJAX));
          }
        }
      }

      // validacion para que el limte superio no se sobredimensione
      $validacionLimiteSuperior = "SELECT ID, limite_superior FROM parametros_manipuladoras WHERE limite_superior = (SELECT MAX(limite_superior) FROM parametros_manipuladoras )";
      $idSinEspacio = trim($id);
      $respuestaValidacionLimiteSuperior = $Link->query($validacionLimiteSuperior) or die('Error al consultar el límite superior '. mysqli_error($Link));
      if ($respuestaValidacionLimiteSuperior->num_rows > 0) {
        $DataValidacionLimiteSuperior = $respuestaValidacionLimiteSuperior->fetch_assoc();
        if ($idSinEspacio == $DataValidacionLimiteSuperior['ID']) {
          $limiteTemporal = 'Este id es el del el limite superior mas alto de ese complemento';
        }
        if ($limiteSuperior > $DataValidacionLimiteSuperior['limite_superior']) {
          $respuestaAJAX = [
            'estado' => 0,
            'mensaje' => 'El límite superior esta sobredimensionado'
          ];
          exit (json_encode($respuestaAJAX));
        }
      }

      $sentenciaEditar = "UPDATE parametros_manipuladoras SET cant_manipuladora = $cantidad, limite_inferior = '$limiteInferior', limite_superior = '$limiteSuperior' WHERE ID = '".$id."';";
      $repuestaEditar = $Link->query($sentenciaEditar) or die('Error al actualizar'. mysqli_error($Link));

      if($repuestaEditar){
        $consultaBitacora = "INSERT INTO bitacora (fecha, usuario, tipo_accion, observacion) VALUES ('" . date("Y-m-d H-i-s") . "', '" . $_SESSION["idUsuario"] . "', '64', 'Se actualizó el parámetro manipuladora: <strong>".$tipoComplemento.' con límite inferior '. $limiteInferior .' y límite superior '. $limiteSuperior ."</strong>')";
        $Link->query($consultaBitacora) or die ('Unable to execute query. '. mysqli_error($Link));

        $respuestaAJAX = [
        'estado' => 1,
        'mensaje' => 'El Parámetro manipuladora se actualizó exitosamente.'
        ];
        exit (json_encode($respuestaAJAX));
      }
    } 
}
