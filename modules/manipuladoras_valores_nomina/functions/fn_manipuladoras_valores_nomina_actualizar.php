<?php
 require_once '../../../db/conexion.php';
 require_once '../../../config.php';

$respuesta = [];
$id = (isset($_POST['id']) && $_POST['id'] != '') ? mysqli_real_escape_string($Link, $_POST['id']) : '';
$tipoComplemento = (isset($_POST['tipoComplemento']) && $_POST['tipoComplemento'] != '') ? mysqli_real_escape_string($Link, $_POST['tipoComplemento']) : '';
$tipo = (isset($_POST['tipo']) && $_POST['tipo'] != '') ? mysqli_real_escape_string($Link, $_POST['tipo']) : '';
$limiteInferior = (isset($_POST['limiteInferior']) && $_POST['limiteInferior'] != '') ? mysqli_real_escape_string($Link, $_POST['limiteInferior']) : '';
$limiteSuperior = (isset($_POST['limiteSuperior']) && $_POST['limiteSuperior'] != '') ? mysqli_real_escape_string($Link, $_POST['limiteSuperior']) : '';
$valor = (isset($_POST['valor']) && $_POST['valor'] != '') ? mysqli_real_escape_string($Link, $_POST['valor']) : '';
$respuesta = [];

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


// vamos a validar que ese tipo no exista ya realcionado con ese tipo de complemento 
$consultaComplemento = "SELECT tipo FROM manipuladoras_valoresnomina WHERE tipo_complem = '".$tipoComplemento."';";
$consultaComplemento2 = "SELECT tipo FROM manipuladoras_valoresnomina WHERE id = '".$id."';";

$resComplemento = $Link->query($consultaComplemento) or die('Error al consultar tipo de complemento: '. mysqli_error($Link));
$resComplemento2 = $Link->query($consultaComplemento2) or die('Error al consultar tipo de complemento: '. mysqli_error($Link));

if ($resComplemento2->num_rows > 0) {
	while ($dataComplemento2 = $resComplemento2->fetch_assoc()) {
		$tipoPorID = $dataComplemento2['tipo'];
	}	
}

if($resComplemento->num_rows == 0) {
	$sentenciaEditar = "UPDATE manipuladoras_valoresnomina SET tipo = '$tipo', limiteInferior = '$limiteInferior', limiteSuperior = '$limiteSuperior', valor ='$valor' WHERE ID = '".$id."';";
  	$repuestaEditar = $Link->query($sentenciaEditar) or die('Error al actualizar'. mysqli_error($Link));

  	if($repuestaEditar){
    	$consultaBitacora = "INSERT INTO bitacora (fecha, usuario, tipo_accion, observacion) VALUES ('" . date("Y-m-d H-i-s") . "', '" . $_SESSION["idUsuario"] . "', '55', 'Se actualizó el valor manipuladora nómina id: <strong>".$id."</strong>')";
   		$Link->query($consultaBitacora) or die ('Unable to execute query. '. mysqli_error($Link));

  		$respuestaAJAX = [
  		'estado' => 1,
  		'mensaje' => 'El Valor manipuladora nómina se actualizó exitosamente.'
  		];
  	}
}
if ($resComplemento->num_rows == 1) {
	$sentenciaEditar = "UPDATE manipuladoras_valoresnomina SET tipo = '$tipo', limiteInferior = '$limiteInferior', limiteSuperior = '$limiteSuperior', valor ='$valor' WHERE ID = '".$id."';";
  	$repuestaEditar = $Link->query($sentenciaEditar) or die('Error al actualizar'. mysqli_error($Link));

  	if($repuestaEditar){
    	$consultaBitacora = "INSERT INTO bitacora (fecha, usuario, tipo_accion, observacion) VALUES ('" . date("Y-m-d H-i-s") . "', '" . $_SESSION["idUsuario"] . "', '55', 'Se actualizó el valor manipuladora nómina id: <strong>".$id."</strong>')";
   		$Link->query($consultaBitacora) or die ('Unable to execute query. '. mysqli_error($Link));

  		$respuestaAJAX = [
  		'estado' => 1,
  		'mensaje' => 'El Valor manipuladora nómina se actualizó exitosamente.'
  		];
  	}
}

// exit(var_dump($tipoPorID));
if ($resComplemento->num_rows > 1) {
  // se valida que el limite superior de conteo por dia no pueda ser igual o mayor a el limite inferior de conteo por titular
  if ($tipo == 1) {
    $consultaLimite = "SELECT limiteInferior FROM manipuladoras_valoresnomina WHERE tipo_complem = '".$tipoComplemento. "' AND tipo = 2;";
    $resConsultaLimite = $Link->query($consultaLimite) or die('Error al consultar el límite inferior '. mysqli_error($Link));
    if ($resConsultaLimite->num_rows > 0) {
        while ($DataConsultaLimite = $resConsultaLimite->fetch_assoc()) {
          if ($limiteSuperior >= $DataConsultaLimite['limiteInferior']) {
            $respuestaAJAX = [
            'estado' => 0,
            'mensaje' => 'El límite superior de pago por día no puede ser igual o mayor al límite inferior de pago por titular'
            ];
            exit (json_encode($respuestaAJAX));
          }
        }
    }
  }

  if ($tipo == 2) {
    $consultaLimite2 = "SELECT limiteSuperior FROM manipuladoras_valoresnomina WHERE tipo_complem = '".$tipoComplemento. "' AND tipo = 1;";
    $resConsultaLimite2 = $Link->query($consultaLimite2) or die('Error al consultar el limite superior '. mysqli_error($Link));
    if ($resConsultaLimite2->num_rows > 0) {
        while ($DataConsultaLimite2 = $resConsultaLimite2->fetch_assoc()) {
          if ($limiteInferior <= $DataConsultaLimite2['limiteSuperior']) {
            $respuestaAJAX = [
            'estado' => 0,
            'mensaje' => 'El límite inferior de pago por titular no puede ser igual o menor al límite superior de pago por día'
            ];
            exit (json_encode($respuestaAJAX));
          }
        }
    }
  }
  
	if ($tipo == $tipoPorID) {
		$sentenciaEditar = "UPDATE manipuladoras_valoresnomina SET tipo = '$tipo', limiteInferior = '$limiteInferior', limiteSuperior = '$limiteSuperior', valor ='$valor' WHERE ID = '".$id."';";
  		$repuestaEditar = $Link->query($sentenciaEditar) or die('Error al actualizar'. mysqli_error($Link));

  		if($repuestaEditar){
    		$consultaBitacora = "INSERT INTO bitacora (fecha, usuario, tipo_accion, observacion) VALUES ('" . date("Y-m-d H-i-s") . "', '" . $_SESSION["idUsuario"] . "', '55', 'Se actualizó el valor manipuladora nómina id: <strong>".$id."</strong>')";
   			$Link->query($consultaBitacora) or die ('Unable to execute query. '. mysqli_error($Link));

  			$respuestaAJAX = [
  			'estado' => 1,
  			'mensaje' => 'El Valor manipuladora nómina se actualizó exitosamente.'
  			];
  		}
	} 
	else{
		$respuestaAJAX = [
  		'estado' => 0,
  		'mensaje' => 'El valor manipuladora nómina ya tiene registro para este tipo de pago'
  		];
	}	
}



echo json_encode($respuestaAJAX);

