<?php
require_once '../../../db/conexion.php';
require_once '../../../config.php';

// var_dump($_POST);
//var_dump($_SESSION);

$fecha = date("Y-m-d H:i:s");
$anno = date("y"); 
$mes = date("m");


$sede = mysqli_real_escape_string($Link, $_POST['sede']);
$semana = mysqli_real_escape_string($Link, $_POST['semana']);
$dia = intval(date("d"));
$id_usuario = mysqli_real_escape_string($Link, $_SESSION['id_usuario']);

$repitentes = $_POST['repitente'];

$consulta = " update Asistencia$mes$anno set repite = 1 where mes = \"$mes\" and semana = \"$semana\" and dia = \"$dia\" and asistencia = 1  ";
$aux = 0;
foreach ($asistencias as $asistencia){
	if($aux > 0){
		$consulta .= " , ";
	}
	$consulta .= " ( ";
	$auxField = mysqli_real_escape_string($Link, $asistencia["tipoDocumento"]);
	$consulta .= " \"$auxField\", ";	
	$auxField = mysqli_real_escape_string($Link, $asistencia["documento"]);
	$consulta .= " \"$auxField\", ";
	$consulta .= " \"$fecha\", ";
	$consulta .= " \"$mes\", ";
	$consulta .= " \"$semana\", ";
	$consulta .= " \"$dia\", ";
	$auxField = mysqli_real_escape_string($Link, $asistencia["asistencia"]);
	$consulta .= " $auxField, ";
	$consulta .= " $id_usuario ";
	$consulta .= " ) ";
	$aux++;
}
echo $consulta;



// $result = $Link->query($consulta) or die ('Insert error'. mysqli_error($Link));
// if($result){
// 	$resultadoAJAX = array(
// 		"state" => 1,
// 		"message" => "El registro se ha realizado con éxito.",
//   	);
// }else{
// 	$resultadoAJAX = array(
// 		"state" => 2,
// 		"message" => "Error al hacer el registro.",
//   	);
// }
// echo json_encode($resultadoAJAX);