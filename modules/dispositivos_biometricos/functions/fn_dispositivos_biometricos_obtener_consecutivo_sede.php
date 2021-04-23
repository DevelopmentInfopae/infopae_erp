<?php 
require_once '../../../config.php';
require_once '../../../db/conexion.php';

$iddispositivo = $_POST['iddispositivo'];
$sede = $_POST['sede'];
// exit(var_dump($_POST));

$consultaSede = "SELECT cod_sede FROM biometria WHERE id_dispositivo = ".$iddispositivo."  AND cod_sede = ".$sede."  limit 1";
// var_dump($consultaSede);
$resultadoSede = $Link->query($consultaSede) or die('No se encontraron sedes asociadas a este dispositivo ' .mysqli_error($Link));
if ($resultadoSede->num_rows > 0) {
	$dataSede = $resultadoSede->fetch_assoc();
	$respuesta = array('estado' => "asociada");
	exit (json_encode($respuesta));
}else{
	$consultaCantidad = "SELECT COUNT(id_bioest) as cantidad FROM biometria WHERE id_dispositivo = ".$iddispositivo.";";
	$respuestaCantidad = $Link->query($consultaCantidad) or die ('Error al contar la cantidad de id' . mysqli_error($Link));
	if ($respuestaCantidad->num_rows > 0) {
		$dataCantidad = $respuestaCantidad->fetch_assoc();
		$cantidad = $dataCantidad['cantidad'];
	}
	$respuesta = array('estado' => "noAsociada" , 'cnt' => $cantidad);
	exit(json_encode($respuesta));
}