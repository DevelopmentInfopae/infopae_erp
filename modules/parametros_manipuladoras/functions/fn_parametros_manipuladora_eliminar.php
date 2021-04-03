<?php
require_once '../../../db/conexion.php';
require_once '../../../config.php';

$id = (isset($_POST['id']) && $_POST['id'] != '') ? mysqli_real_escape_string($Link, $_POST['id']) : '';
$complemento = '';
$limiteInferior ='';
$limiteSuperior= '';

// sentencia buscar para el llenar la bitacora 
$sentenciaBuscar = "SELECT tipo_complem, limite_inferior, limite_superior FROM parametros_manipuladoras WHERE ID = '$id';";
$resSentenciaBuscar = $Link->query($sentenciaBuscar) or die('Error al consultar el parámetro manipuladora '. mysqli_error($Link));
if ($resSentenciaBuscar->num_rows > 0) {
    $DataSentenciaBuscar = $resSentenciaBuscar->fetch_assoc();
    $complemento = $DataSentenciaBuscar['tipo_complem'];
    $limiteInferior = $DataSentenciaBuscar['limite_inferior'];
    $limiteSuperior = $DataSentenciaBuscar['limite_superior'];
}

$sentenciaEliminar = "DELETE FROM parametros_manipuladoras WHERE ID = '".$id."';";
// exit(var_dump($sentenciaEliminar));
$resEliminar = $Link->query($sentenciaEliminar) or die('Error al eliminar el parámetro manipuladora '. mysqli_error($Link));
  if($resEliminar){
    $consultaBitacora = "INSERT INTO bitacora (fecha, usuario, tipo_accion, observacion) VALUES ('" . date("Y-m-d H-i-s") . "', '" . $_SESSION["idUsuario"] . "', '65', 'Se Eliminó el parámetro manipuladora: <strong>".$complemento.' con límite inferior '. $limiteInferior .' y límite superior '. $limiteSuperior ."</strong>')";
    $Link->query($consultaBitacora) or die ('Unable to execute query. '. mysqli_error($Link));

  	$respuestaAJAX = [
  		'estado' => 1,
  		'mensaje' => 'El parámetro manipuladora se eliminó exitosamente'
  	];
  }
  else
  {
  	$respuestaAJAX = [
  		'estado' => 0,
  		'mensaje' => 'El parámetro manipuladora NO se eliminó exitosamente.'
  	];
  }

  echo json_encode($respuestaAJAX);