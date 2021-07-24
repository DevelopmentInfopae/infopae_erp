<?php 

require_once '../../../config.php';
require_once '../../../db/conexion.php';

$id = $_POST['idPerfil'];
$estado = (isset($_POST["estado"]) && $_POST["estado"] != '') ? mysqli_real_escape_string($Link, $_POST["estado"])  : '';
$modulo = $_POST['modulo'];

if ($estado == null || $estado == "") {
  $respuestaAJAX = [
    'estado' => 0,
    'mensaje' => 'El permiso NO se actualizó exitosamente.'
  ];
  exit(json_encode($respuestaAJAX));
}

$nuevoEstado = "";
$nuevoEstadoString = "";

if ($estado == "0") {
	$nuevoEstado = "0";
	$nuevoEstadoString = "Desactivo";
}else if ($estado == "1") {
	$nuevoEstado = "1";
	$nuevoEstadoString = "Lectura";
}else if ($estado == "2") {
  $nuevoEstado = "2";
  $nuevoEstadoString = "Lectura y escritura";
}

$columna = "";
  if ($modulo ==  "entregas_biometricas") { $columna = "entregas_biometricas"; }
  else if ($modulo ==  "instituciones") { $columna = "Instituciones"; }
  else if ($modulo == 'archivos') { $columna = "archivos_globales"; }
  else if ($modulo == 'titulares') { $columna = "titulares_derecho"; }
  else if ($modulo == "menus") { $columna = "menus";}
  else if ($modulo == "diagnostico") { $columna = "diagnostico_infraestructura"; }
  else if ($modulo == "dispositivos") { $columna = "dispositivos_biometricos"; }
  else if ($modulo == "despachos") { $columna = "despachos"; }
  else if ($modulo == "ordenes") { $columna = "orden_compra"; } 
  else if ($modulo == "entregas") { $columna = "entrega_complementos"; }
  else if ($modulo == "novedades") { $columna = "novedades"; }
  else if ($modulo == "nomina") { $columna = "nomina"; }
  else if ($modulo == "fqrs") { $columna = "fqrs"; }
  else if ($modulo == "informes") { $columna = "informes"; }
  else if ($modulo == "asistencia") { $columna = "asistencia"; }
  else if ($modulo == "control") { $columna = "control_acceso"; }
  else if ($modulo == "procesos") { $columna = "procesos"; }
  else if ($modulo == "configuracion") { $columna = "configuracion"; }
  else if ($modulo == "escritura") { $columna = "escritura"; }

$perfilNombre = "";
$consultaNombre = " SELECT nombre FROM perfiles WHERE id = $id; ";
$respuestaNombre = $Link->query($consultaNombre) or die ('Error al consultar el nombre del perfil. ' . mysqli_error($Link));
if ($respuestaNombre->num_rows > 0) {
	$dataNombre = $respuestaNombre->fetch_assoc();
	$perfilNombre = $dataNombre['nombre'];  
}  

$sentenciaUpdate = " UPDATE perfiles SET $columna = $nuevoEstado WHERE id = $id; ";
$respuestaUpdate = $Link->query($sentenciaUpdate) or die ('Error al actualizar los permisos. ' . mysqli_error($Link));
// var_dump($sentenciaUpdate);
if ($respuestaUpdate) {
	$consultaBitacora = "INSERT INTO bitacora (fecha, usuario, tipo_accion, observacion) VALUES ('" . date("Y-m-d H-i-s") . "', '" . $_SESSION["idUsuario"] . "', '83', '".$nuevoEstadoString." el modulo ".$columna." en el perfil: <strong>".$perfilNombre."</strong>')";
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