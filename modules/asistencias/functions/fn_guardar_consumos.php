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


$consumieron = [];
$noConsumieron = [];
$repitieron = [];

if(isset($_POST['consumieron'])){
	$consumieron = $_POST['consumieron'];
}

if(isset($_POST['noConsumieron'])){
	$noConsumieron = $_POST['noConsumieron'];
}
// var_dump($noConsumieron);

if(isset($_POST['repitieron'])){
	$repitieron = $_POST['repitieron'];
}





$consulta = "";

$tipo_doc = "";
$num_doc = "";

$consulta = "";
foreach ($consumieron as $consumio){

	$tipo_doc = mysqli_real_escape_string($Link, $consumio["tipoDocumento"]);
	$num_doc = mysqli_real_escape_string($Link, $consumio["documento"]);

	$consulta .= " update Asistencia_det$mes$anno set consumio = 1 ";	

	if(isset($repitieron[$num_doc])){
		$consulta .= " , repitio = 1 ";
	}

	$consulta .= " where mes = \"$mes\" and semana = \"$semana\" and dia = \"$dia\" and asistencia = 1 
	and tipo_doc = \"$tipo_doc\" and num_doc = \"$num_doc\"; ";
}

foreach ($noConsumieron as $noConsumio){

	$tipo_doc = mysqli_real_escape_string($Link, $noConsumio["tipoDocumento"]);
	$num_doc = mysqli_real_escape_string($Link, $noConsumio["documento"]);

	$consulta .= " update Asistencia_det$mes$anno set consumio = 0 ";	
	$consulta .= " , repitio = 0 ";
	$consulta .= " where mes = \"$mes\" and semana = \"$semana\" and dia = \"$dia\" and asistencia = 1 

	and tipo_doc = \"$tipo_doc\" and num_doc = \"$num_doc\"; ";
}


//echo $consulta;


$result = $Link->multi_query($consulta) or die ('Insert error'. mysqli_error($Link));
if($result){
	$resultadoAJAX = array(
		"state" => 1,
		"message" => "El registro se ha realizado con éxito.",
  	);
}else{
	$resultadoAJAX = array(
		"state" => 2,
		"message" => "Error al hacer el registro.",
  	);
}
echo json_encode($resultadoAJAX);