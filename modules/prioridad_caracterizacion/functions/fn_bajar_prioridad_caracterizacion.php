<?php
require_once '../../../db/conexion.php';
require_once '../../../config.php';

$id = (isset($_POST['idP']) && $_POST['idP'] != '') ? mysqli_real_escape_string($Link, $_POST['idP']) : '';

// consulta para traer el id del orden mayor en la tabla 
$validacionOrdenMayor = "SELECT id FROM prioridad_caracterizacion WHERE orden = (SELECT min(orden) FROM prioridad_caracterizacion);";
$respuestaValidacionOrdenMayor = $Link->query($validacionOrdenMayor) or die('Error al consultar el id del orden menor:'.mysqli_error($Link));
if ($respuestaValidacionOrdenMayor->num_rows > 0) {
	$dataValidacionOrdenMayor = $respuestaValidacionOrdenMayor->fetch_assoc();
	if ($id == $dataValidacionOrdenMayor['id']) {
		$respuestaAJAX = [
			'estado' => 0,
			'mensaje' => 'Este es el orden menor existente'
		];
		exit(json_encode($respuestaAJAX));	
	}
}

$consultaOrden = "SELECT orden, descripcion FROM prioridad_caracterizacion WHERE id = '".$id."';";
$respuestaOrden = $Link->query($consultaOrden) or die('Error al consultar el orden:' .mysqli_error($Link));
if ($respuestaOrden->num_rows > 0) {
	$dataOrden = $respuestaOrden->fetch_assoc();
	$ordenActual = $dataOrden['orden'];
	$descripcion = $dataOrden['descripcion'];
	$ordenDisminuido = --$ordenActual;
	// exit(var_dump($consultaOrden));
	// sentencia updata para subir el numero del orden 
	$sentenciaUpdateBajar = "UPDATE prioridad_caracterizacion SET orden = '".$ordenDisminuido."' WHERE id = '".$id."';";
	// exit(var_dump($sentenciaUpdateSubir));
	$respuestaUpdateBajar = $Link->query($sentenciaUpdateBajar) or die('Error al actualizar el orden'. mysqli_error($Link));
	if ($respuestaUpdateBajar) {
		$consultaBitacora = "INSERT INTO bitacora (fecha, usuario, tipo_accion, observacion) VALUES ('" . date("Y-m-d H-i-s") . "', '" . $_SESSION["idUsuario"] . "', '73', 'Se bajo el orden prioridad caracterizacion: <strong>".$descripcion."</strong>')";
   		$Link->query($consultaBitacora) or die ('Unable to execute query. '. mysqli_error($Link));

   		// secuencia para bajar si existe el orden si es igual al orden aumentado 
		$consultaOrdenExistente = "SELECT id, orden, descripcion FROM prioridad_caracterizacion WHERE orden = '" .$ordenDisminuido. "' AND id !='" . $id ."';";
		// exit(var_dump($consultaOrdenExistente));
		$respuestaOrdenExistente = $Link->query($consultaOrdenExistente) or die('Error al consultar el orden:' . mysqli_error($Link));
		if ($respuestaOrdenExistente->num_rows > 0) {
			$dataOrdenExistente = $respuestaOrdenExistente->fetch_assoc();
			$idE = $dataOrdenExistente['id'];
			$ordenE = $dataOrdenExistente['orden'];
			$descripcionE = $dataOrdenExistente['descripcion'];
			$ordenAumentado = ++$ordenE;
			$sentenciaUpdateSubir = "UPDATE prioridad_caracterizacion SET orden = '" .$ordenAumentado. "' WHERE id = '" .$idE. "';";
			$respuestaUpdateSubir = $Link->query($sentenciaUpdateSubir) or die('Error al actualizar el orden' . mysqli_error($Link));
			if ($respuestaUpdateSubir) {
				$consultaBitacora = "INSERT INTO bitacora (fecha, usuario, tipo_accion, observacion) VALUES ('" . date("Y-m-d H-i-s") . "', '" . $_SESSION["idUsuario"] . "', '72', 'Se subio el orden prioridad caracterizacion: <strong>".$descripcionE."</strong>')";
   				$Link->query($consultaBitacora) or die ('Unable to execute query. '. mysqli_error($Link));
			}
		}

  		$respuestaAJAX = [
  		'estado' => 1,
  		'mensaje' => 'El Orden prioridad caracterización se disminuyó exitosamente'
  		];
  		exit (json_encode($respuestaAJAX));
	}
}