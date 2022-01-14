<?php 
require_once '../../../config.php';
require_once '../../../db/conexion.php';

$documento = $_POST['documento'];
$estado = $_POST['estado'];
$semana = $_POST['semana'];
$nuevoEstado;
$stringEstado = '';

if (isset($semana) && $semana < 10) {
	$semana = "0".$semana;
}

if (isset($estado) && $estado == 1) {
	$nuevoEstado = 0;
	$stringEstado = 'Desactivó';
}else if (isset($estado) && $estado == 0) {
	$nuevoEstado = 1;
	$stringEstado = 'Activó';
}

$actualizarFocalizacion = "UPDATE focalizacion$semana SET activo = $nuevoEstado WHERE num_doc = '$documento';";
// exit(var_dump($actualizarFocalizacion));
if ($Link->query($actualizarFocalizacion)===true) {
	$sqlBitacora = "INSERT INTO bitacora (id, fecha, usuario, tipo_accion, observacion) VALUES ('', '".date('Y-m-d H:i:s')."', '".$_SESSION['idUsuario']."', '49', '".$stringEstado." al títular de derecho con número de identificación <strong>".$documento."</strong> La semana <strong>".$semana."</strong>')";
    $Link->query($sqlBitacora);
	$respuestaAjax =[
		'estado' => 1,
		'mensaje' => 'El estado del titular fue actualizado exitosamente.'
	];
}else {
	$respuestaAjax =[
		'estado' => 0,
		'mensaje' => 'El estado del titular NO fue actualizado exitosamente.'
	];
}



echo json_encode($respuestaAjax);