<?php 
require_once '../../../config.php';
require_once '../../../db/conexion.php';

$id = $_POST['id_perfil'];
$dashboard = (isset($_POST["dashboard"]) && $_POST["dashboard"] != '') ? mysqli_real_escape_string($Link, $_POST["dashboard"])  : '';

if ($dashboard == null || $dashboard == "") {
  $respuestaAJAX = [
    'dashboard' => 0,
    'mensaje' => 'El permiso NO se actualizó exitosamente.'
  ];
  exit(json_encode($respuestaAJAX));
}


$perfilNombre = "";
$consultaNombre = " SELECT nombre FROM perfiles WHERE id = $id; ";
$respuestaNombre = $Link->query($consultaNombre) or die ('Error al consultar el nombre del perfil. ' . mysqli_error($Link));
if ($respuestaNombre->num_rows > 0) {
	$dataNombre = $respuestaNombre->fetch_assoc();
	$perfilNombre = $dataNombre['nombre'];  
}  

$dashboardNombre = '';
$consultaDashboard = " SELECT descripcion FROM dashboard WHERE id = '$dashboard' ";
$respuestaDashboard = $Link->query($consultaDashboard) or die ('Error al consultar el nombre del dashboard');
if ($respuestaDashboard->num_rows > 0) {
    $dataDashboard = $respuestaDashboard->fetch_assoc();
    $dashboardNombre = $dataDashboard['descripcion'];
}

$sentenciaUpdate = " UPDATE perfiles SET dashboard = $dashboard WHERE id = $id; ";
$respuestaUpdate = $Link->query($sentenciaUpdate) or die ('Error al actualizar los permisos. ' . mysqli_error($Link));

if ($respuestaUpdate) {
	$consultaBitacora = "INSERT INTO bitacora (fecha, usuario, tipo_accion, observacion) 
                            VALUES ('" . date("Y-m-d H-i-s") . "', '" . $_SESSION["idUsuario"] . "', '100', 
                            'Cambio Dashboard A ".$dashboardNombre." en el perfil: <strong>".$perfilNombre."</strong>')";
    $Link->query($consultaBitacora) or die ('Unable to execute query. '. mysqli_error($Link));
    $respuestaAJAX = [
    	'estado' => 1,
    	'mensaje' => 'El permiso se actualizó exitosamente.'
    ];
    exit(json_encode($respuestaAJAX));
}else{
	$respuestaAJAX = [
		'estado' => 0,
		'mensaje' => 'El permiso NO se actualizó exitosamente.'
	];
	exit(json_encode($respuestaAJAX));
}