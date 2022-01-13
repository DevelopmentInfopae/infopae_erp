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
		'mensaje' => 'No puede ingresar una descripción vacía'
	];
	exit(json_encode($respuestaAJAX));
}

$validacionNombre = "SELECT descripcion FROM variacion_menu WHERE descripcion = '". $descripcionSinEspacios ."' AND ID != '" .$id. "';";
$respuestaValidacionNombre = $Link->query($validacionNombre) or die('Error al consultar la descripcion' . mysqli_error($Link));
if ($respuestaValidacionNombre->num_rows > 0) {
	$respuestaAJAX = [
		'estado' => 0,
		'mensaje' => 'Ya existe un registro con esa descripción'
	];
	exit(json_encode($respuestaAJAX));
}

$sentenciaUpdate = "UPDATE variacion_menu SET descripcion = '" . $descripcionSinEspacios . "' WHERE ID = '" . $id . "';";
$respuestaUpdate = $Link->query($sentenciaUpdate) or die ('Error al actualizar ' . mysqli_error($Link));
if ($respuestaUpdate) {
	$consultaBitacora = "INSERT INTO bitacora (fecha, usuario, tipo_accion, observacion) VALUES ('" . date("Y-m-d H-i-s") . "', '" . $_SESSION["idUsuario"] . "', '81', 'Se actualizó la variación menú: <strong>".$descripcionSinEspacios."</strong>')";
    $Link->query($consultaBitacora) or die ('Unable to execute query. '. mysqli_error($Link));

   	$respuestaAJAX = [
		'estado' => 1,
		'mensaje' => 'La variación menú se actualizó exitosamente.'
	];
	exit(json_encode($respuestaAJAX));
}else{
	$respuestaAJAX = [
        'estado' => 0,
        'mensaje' => 'La variación menú NO se actualizó exitosamente.'
    ];
    exit(json_encode($respuestaAJAX));
} 