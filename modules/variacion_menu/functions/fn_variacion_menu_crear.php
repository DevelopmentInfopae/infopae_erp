<?php 
require_once '../../../db/conexion.php';
require_once '../../../config.php';

$variacion = (isset($_POST['variacion']) && $_POST['variacion'] != '') ? mysqli_real_escape_string($Link, $_POST['variacion']) : '';

// validacion nombre en blanco
$variacionSinEspacios = trim($variacion);
$caracteres = strlen($variacionSinEspacios);
if ($caracteres == 0) {
	$respuestaAJAX = [
		'estado' => 0,
		'mensaje' => 'No se puede crear con descripción en blanco.'
	];
	exit(json_encode($respuestaAJAX));
}

// validacion mismo nombre de variacion
$validacion1 = "SELECT descripcion FROM variacion_menu WHERE descripcion = '" .$variacionSinEspacios. "';";
$respuestaValidacion1 = $Link->query($validacion1) or die('Error al consultar la variacion' . mysqli_error($Link));
if ($respuestaValidacion1->num_rows > 0) {
	$respuestaAJAX = [
		'estado' => 0,
		'mensaje' => 'Ya existe un registro con esa descripción.'
	];
	exit(json_encode($respuestaAJAX));
}

$sentenciaInsert = "INSERT INTO variacion_menu (descripcion) VALUES ('$variacionSinEspacios');";
$respuestaInsert = $Link->query($sentenciaInsert) or die ('Error al insertar la variación ' . mysqli_error($Link));
if ($respuestaInsert) {
	$consultaBitacora = "INSERT INTO bitacora (fecha, usuario, tipo_accion, observacion) VALUES ('" . date("Y-m-d H-i-s") . "', '" . $_SESSION["idUsuario"] . "', '80', 'Se creó la variación: <strong>".$variacionSinEspacios."</strong>')";
    $Link->query($consultaBitacora) or die ('Unable to execute query. '. mysqli_error($Link));
    $respuestaAJAX = [
    	'estado' => 1,
    	'mensaje' => 'La variación menú se creó exitosamente.'
    ];
    exit(json_encode($respuestaAJAX));
}else{
	$respuestaAJAX = [
		'estado' => 0,
		'mensaje' => 'La variación menú NO se creó exitosamente.'
	];
	exit(json_encode($respuestaAJAX));
}