<?php
require_once '../../../db/conexion.php';
require_once '../../../config.php';
include 'fn_fecha_asistencia.php';

$anno = $annoAsistencia2D; 
if(isset($_POST["mes"]) && $_POST["mes"] != ""){
	$mes = mysqli_real_escape_string($Link, $_POST['mes']);
}else{
	$mes = $mesAsistencia;
}
if(isset($_POST["dia"]) && $_POST["dia"] != ""){
	$dia = mysqli_real_escape_string($Link, $_POST['dia']);
}else{
	$dia = $diaAsistencia;
}
$sede = mysqli_real_escape_string($Link, $_POST['sede']);
$complemento = mysqli_real_escape_string($Link, $_POST['complemento']);
$semana = mysqli_real_escape_string($Link, $_POST['semana']);
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

if(isset($_POST['repitieron'])){
	$repitieron = $_POST['repitieron'];
}
// exit(var_dump($consumieron));
$tipo_doc = "";
$num_doc = "";
$mesTablaAsistencia = $mes;
$annoTablaAsistencia = $anno;
include 'fn_validar_existencias_tablas.php';
$consulta = "";
foreach ($consumieron as $consumio){
	$repitio = 0;
	$tipo_doc = mysqli_real_escape_string($Link, $consumio["tipoDocumento"]);
	$num_doc = mysqli_real_escape_string($Link, $consumio["documento"]);
	$consulta .= " update asistencia_det$mes$anno set consumio = 1 ";	
	if(isset($repitieron[$num_doc])){
		$consulta .= " , repitio = 1 ";
	}else{
		$consulta .= " , repitio = 0 ";
	}

	$consulta .= " where mes = \"$mes\" and dia = \"$dia\" and asistencia = 1 
	and tipo_doc = \"$tipo_doc\" and num_doc = \"$num_doc\" and complemento = \"$complemento\"; ";
}
// exit(var_dump($consulta));
foreach ($noConsumieron as $noConsumio){

	$tipo_doc = mysqli_real_escape_string($Link, $noConsumio["tipoDocumento"]);
	$num_doc = mysqli_real_escape_string($Link, $noConsumio["documento"]);

	$consulta .= " update asistencia_det$mes$anno set consumio = 0 ";	
	$consulta .= " , repitio = 0 ";
	$consulta .= " where mes = \"$mes\" and dia = \"$dia\" and asistencia = 1 

	and tipo_doc = \"$tipo_doc\" and num_doc = \"$num_doc\" and complemento = \"$complemento\"; ";
}

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