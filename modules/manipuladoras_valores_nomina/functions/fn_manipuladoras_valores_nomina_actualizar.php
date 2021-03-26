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

if ($limiteInferior > $limiteSuperior) {
      $respuestaAJAX = [
       'estado' => 0,
       'mensaje' => 'El limite inferor no puede ser mayor al limite superior'
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
    	$consultaBitacora = "INSERT INTO bitacora (fecha, usuario, tipo_accion, observacion) VALUES ('" . date("Y-m-d H-i-s") . "', '" . $_SESSION["idUsuario"] . "', '55', 'Se actualizó el valor manipuladora nomina id: <strong>".$id."</strong>')";
   		$Link->query($consultaBitacora) or die ('Unable to execute query. '. mysqli_error($Link));

  		$respuestaAJAX = [
  		'estado' => 1,
  		'mensaje' => 'El Valor manipuladora nomina se actualizó exitosamente.'
  		];
  	}
}
if ($resComplemento->num_rows == 1) {
	$sentenciaEditar = "UPDATE manipuladoras_valoresnomina SET tipo = '$tipo', limiteInferior = '$limiteInferior', limiteSuperior = '$limiteSuperior', valor ='$valor' WHERE ID = '".$id."';";
  	$repuestaEditar = $Link->query($sentenciaEditar) or die('Error al actualizar'. mysqli_error($Link));

  	if($repuestaEditar){
    	$consultaBitacora = "INSERT INTO bitacora (fecha, usuario, tipo_accion, observacion) VALUES ('" . date("Y-m-d H-i-s") . "', '" . $_SESSION["idUsuario"] . "', '55', 'Se actualizó el valor manipuladora nomina id: <strong>".$id."</strong>')";
   		$Link->query($consultaBitacora) or die ('Unable to execute query. '. mysqli_error($Link));

  		$respuestaAJAX = [
  		'estado' => 1,
  		'mensaje' => 'El Valor manipuladora nomina se actualizó exitosamente.'
  		];
  	}
}

// exit(var_dump($tipoPorID));
if ($resComplemento->num_rows > 1) {
	if ($tipo == $tipoPorID) {
		$sentenciaEditar = "UPDATE manipuladoras_valoresnomina SET tipo = '$tipo', limiteInferior = '$limiteInferior', limiteSuperior = '$limiteSuperior', valor ='$valor' WHERE ID = '".$id."';";
  		$repuestaEditar = $Link->query($sentenciaEditar) or die('Error al actualizar'. mysqli_error($Link));

  		if($repuestaEditar){
    		$consultaBitacora = "INSERT INTO bitacora (fecha, usuario, tipo_accion, observacion) VALUES ('" . date("Y-m-d H-i-s") . "', '" . $_SESSION["idUsuario"] . "', '55', 'Se actualizó el valor manipuladora nomina id: <strong>".$id."</strong>')";
   			$Link->query($consultaBitacora) or die ('Unable to execute query. '. mysqli_error($Link));

  			$respuestaAJAX = [
  			'estado' => 1,
  			'mensaje' => 'El Valor manipuladora nomina se actualizó exitosamente.'
  			];
  		}
	} 
	else{
		$respuestaAJAX = [
  		'estado' => 0,
  		'mensaje' => 'El valor manipuladora ya tiene registro para este tipo de pago'
  		];
	}	
}



echo json_encode($respuestaAJAX);

