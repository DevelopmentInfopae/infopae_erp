<?php
require_once '../../../db/conexion.php';
require_once '../../../config.php';

// var_dump($_POST);
//var_dump($_SESSION);

$fecha = date("Y-m-d H:i:s");
$anno = date("y");


if(isset($_POST['mes']) && $_POST['mes'] != ""){
	$mes = mysqli_real_escape_string($Link, $_POST['mes']);
}else{
	$mes = date("m");
}

if(isset($_POST['dia']) && $_POST['dia'] != ""){
	$dia = mysqli_real_escape_string($Link, $_POST['dia']);
}else{
	$dia = intval(date("d"));
}





$sede = mysqli_real_escape_string($Link, $_POST['sede']);
$semana = mysqli_real_escape_string($Link, $_POST['semana']);
$id_usuario = mysqli_real_escape_string($Link, $_SESSION['id_usuario']);

$repitentes = $_POST['repitente'];


$tipo_doc = "";
$num_doc = "";
$repite = "";



$mesTablaAsistencia = $mes;
$annoTablaAsistencia = $anno;
include 'fn_validar_existencias_tablas.php';









$consulta = "";


foreach ($repitentes as $repitente){

	$tipo_doc = mysqli_real_escape_string($Link, $repitente["tipoDocumento"]);
	$num_doc = mysqli_real_escape_string($Link, $repitente["documento"]);
	$repite = mysqli_real_escape_string($Link, $repitente["repite"]);

	$favorito = "";
	if(isset($repitente["favorito"]) && $repitente["favorito"] != ""){
		$favorito = mysqli_real_escape_string($Link, $repitente["favorito"]);
		//echo $favorito;
		if($favorito == 0){
			$consulta .= " delete from repitentesfavoritos where tipo_doc = \"$tipo_doc\" and num_doc = \"$num_doc\"; ";
		} else if($favorito == 1){
			$consulta .= " insert into repitentesfavoritos ( tipo_doc, num_doc ) values ( \"$tipo_doc\" , \"$num_doc\" ); ";
		} 
	}





	$consulta .= " update asistencia_det$mes$anno set repite = \"$repite\" where mes = \"$mes\" and semana = \"$semana\" and dia = \"$dia\" and asistencia = 1 and tipo_doc = \"$tipo_doc\" and num_doc = \"$num_doc\"; ";
}

//echo $consulta;


$result = $Link->multi_query($consulta) or die ("Insert error<br><br>$consulta<br><br>".mysqli_error($Link));
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