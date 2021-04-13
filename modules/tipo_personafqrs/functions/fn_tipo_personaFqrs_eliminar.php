<?php
require_once '../../../db/conexion.php';
require_once '../../../config.php';

$id = (isset($_POST['id']) && $_POST['id'] != '') ? mysqli_real_escape_string($Link, $_POST['id']) : '';

// sentencia buscar para el llenar la bitacora 
$sentenciaBuscar = "SELECT Descripción FROM tipo_personafqrs WHERE id = '$id';";
$resSentenciaBuscar = $Link->query($sentenciaBuscar) or die('Error al consultar el tipo persona FQRS '. mysqli_error($Link));
if ($resSentenciaBuscar->num_rows > 0) {
    $DataSentenciaBuscar = $resSentenciaBuscar->fetch_assoc();
    $descripcion = $DataSentenciaBuscar['Descripción'];
    $descripcionSinEspacios = trim($descripcion);
}

$sentenciaEliminar = "DELETE FROM tipo_personafqrs WHERE ID = '" .$id. "';";
$respuestaEliminar = $Link->query($sentenciaEliminar) or die('Error al eliminar ' . mysqli_error($Link));
if ($respuestaEliminar) {
	$consultaBitacora = "INSERT INTO bitacora (fecha, usuario, tipo_accion, observacion) VALUES ('" . date("Y-m-d H-i-s") . "', '" . $_SESSION["idUsuario"] . "', '79', 'Se creó el tipo persona FQRS: <strong>".$descripcionSinEspacios."</strong>')";
    $Link->query($consultaBitacora) or die ('Unable to execute query. '. mysqli_error($Link));

   	$respuestaAJAX = [
		'estado' => 1,
		'mensaje' => 'El tipo persona FQRS se eliminó exitosamente.'
	];
	exit(json_encode($respuestaAJAX));
}else{
	$respuestaAJAX = [
        'estado' => 0,
        'mensaje' => 'El tipo persona FQRS NO se eliminó exitosamente.'
    ];
    exit(json_encode($respuestaAJAX));
}	
