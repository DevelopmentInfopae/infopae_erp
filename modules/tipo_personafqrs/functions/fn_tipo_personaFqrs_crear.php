<?php 
require_once '../../../db/conexion.php';
require_once '../../../config.php';

$descripcion = (isset($_POST['descripcion']) && $_POST['descripcion'] != '') ? mysqli_real_escape_string($Link, $_POST['descripcion']) : '';

// validacion que no exita con el mismo nombre
$descripcionSinEspacios = trim($descripcion);
$caracteres = strlen($descripcionSinEspacios);

if ($caracteres == 0) {
	$respuestaAJAX = [
		'estado' => 0,
		'mensaje' => 'No puede ingresar una descripción vacía'
	];
	exit(json_encode($respuestaAJAX));
}

$validacionNombre = "SELECT Descripción FROM tipo_personafqrs WHERE Descripción = '". $descripcionSinEspacios ."';";
$respuestaValidacionNombre = $Link->query($validacionNombre) or die('Error al consultar la descripcion' . mysqli_error($Link));
if ($respuestaValidacionNombre->num_rows > 0) {
	$respuestaAJAX = [
		'estado' => 0,
		'mensaje' => 'Ya existe un registro con esa descripción'
	];
	exit(json_encode($respuestaAJAX));
}

$sentenciaInsert = "INSERT INTO tipo_personafqrs (Descripción) VALUES ('$descripcionSinEspacios');";
$respuestaInsert = $Link->query($sentenciaInsert) or die ('Error al insertar en la tabla tipo personafqrs' . mysqli_error($Link));
if ($respuestaInsert) {
	$consultaBitacora = "INSERT INTO bitacora (fecha, usuario, tipo_accion, observacion) VALUES ('" . date("Y-m-d H-i-s") . "', '" . $_SESSION["idUsuario"] . "', '77', 'Se creó el tipo persona FQRS: <strong>".$descripcionSinEspacios."</strong>')";
    $Link->query($consultaBitacora) or die ('Unable to execute query. '. mysqli_error($Link));

   	$respuestaAJAX = [
		'estado' => 1,
		'mensaje' => 'El tipo persona FQRS se creó exitosamente.'
	];
	exit(json_encode($respuestaAJAX));
}else{
	$respuestaAJAX = [
        'estado' => 0,
        'mensaje' => 'El tipo persona FQRS NO se creó exitosamente.'
    ];
    exit(json_encode($respuestaAJAX));
}