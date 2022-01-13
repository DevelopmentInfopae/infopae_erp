<?php 
require_once '../../../db/conexion.php';
require_once '../../../config.php';

$id = (isset($_POST['id']) && $_POST['id'] != '') ? mysqli_real_escape_string($Link, $_POST['id']) : '';
$descripcion = (isset($_POST['descripcion']) && $_POST['descripcion'] != '') ? mysqli_real_escape_string($Link, $_POST['descripcion']) : '';

$descripcionSinEspacios = trim($descripcion);
$caracteres = strlen($descripcionSinEspacios);

if ($caracteres == 0) {
  $respuestaAJAX = [
    'estado' => 0,
    'mensaje' => 'No se puede editar con una descripción vacía'
  ];
  exit(json_encode($respuestaAJAX));
}

$validacionmismoNombre = "SELECT descripcion FROM parametros_infraestructura WHERE id = '$id';";
$resValidacionMismoNombre = $Link->query($validacionmismoNombre) or die('Error al consultar el parametro infraestructura'. mysqli_error($Link));
if ($resValidacionMismoNombre->num_rows > 0) {
	$dataValidacionMismoNombre = $resValidacionMismoNombre->fetch_assoc();
	if ($descripcion == $dataValidacionMismoNombre['descripcion']) {
		$sentenciaEditar = "UPDATE parametros_infraestructura SET descripcion = '$descripcion' WHERE id = '".$id."';";

		// exit(var_dump($sentenciaEditar));
		$repuestaEditar = $Link->query($sentenciaEditar) or die('Error al actualizar'. mysqli_error($Link));

		if($repuestaEditar){
    		$consultaBitacora = "INSERT INTO bitacora (fecha, usuario, tipo_accion, observacion) VALUES ('" . date("Y-m-d H-i-s") . "', '" . $_SESSION["idUsuario"] . "', '70', 'Se actualizó el parámetro infraestructura: <strong>".$descripcion."</strong>')";
   			$Link->query($consultaBitacora) or die ('Unable to execute query. '. mysqli_error($Link));

  			$respuestaAJAX = [
  			'estado' => 1,
  			'mensaje' => 'El Parámetro infraestructura se actualizó exitosamente.'
  			];
  			exit (json_encode($respuestaAJAX));
		}
	}else{
		// validacion para que no tenga mas de un registro la misma cantidad de horas 
		$validacionNombre = "SELECT descripcion FROM parametros_infraestructura WHERE descripcion = '$descripcion';";
		$resValidacionNombre = $Link->query($validacionNombre) or die('Error al consultar la descripcion '. mysqli_error($Link));
		if ($resValidacionNombre->num_rows == 1) {
			$respuestaAJAX = [
       		'estado' => 0,
       		'mensaje' => 'Ya existe un registro con esa descripción'
      		];
      		exit (json_encode($respuestaAJAX));
		}

	}
}

$sentenciaEditar = "UPDATE parametros_infraestructura SET descripcion = '$descripcion' WHERE id = '".$id."';";

$repuestaEditar = $Link->query($sentenciaEditar) or die('Error al actualizar'. mysqli_error($Link));

if($repuestaEditar){
    $consultaBitacora = "INSERT INTO bitacora (fecha, usuario, tipo_accion, observacion) VALUES ('" . date("Y-m-d H-i-s") . "', '" . $_SESSION["idUsuario"] . "', '70', 'Se actualizó el parámetro infraestructura: <strong>".$descripcion."</strong>')";
   	$Link->query($consultaBitacora) or die ('Unable to execute query. '. mysqli_error($Link));

  	$respuestaAJAX = [
  		'estado' => 1,
  		'mensaje' => 'El Parámetro infraestructura se actualizó exitosamente.'
  		];
  		exit (json_encode($respuestaAJAX));
 }

