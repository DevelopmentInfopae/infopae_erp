<?php
require_once '../../../db/conexion.php';
require_once '../../../config.php';
require_once 'fn_fecha_asistencia.php';

// var_dump($_POST);
//var_dump($_SESSION);

$anno = $annoAsistencia2D; 

if( isset($_POST["mes"]) && $_POST["mes"] != "" ){
	$mes = mysqli_real_escape_string($Link, $_POST["mes"]);
}else{
	$mes = $mesAsistencia;
}

if( isset($_POST["dia"]) && $_POST["dia"] != "" ){
	$dia = mysqli_real_escape_string($Link,$_POST["dia"]);
}else{
	$dia = $diaAsistencia;
}






$sede = mysqli_real_escape_string($Link, $_POST['sede']);
$semana = mysqli_real_escape_string($Link, $_POST['semana']);
$grado = mysqli_real_escape_string($Link, $_POST['grado']);
$grupo = mysqli_real_escape_string($Link, $_POST['grupo']);
$banderaRegistros = mysqli_real_escape_string($Link, $_POST['banderaRegistros']);


$id_usuario = mysqli_real_escape_string($Link, $_SESSION['id_usuario']);

$asistencias = $_POST['asistencia'];

$mesTablaAsistencia = $mes;
$annoTablaAsistencia = $anno;
include 'fn_validar_existencias_tablas.php';



if($banderaRegistros == 0){
	//Insertar no habria necesidad de borrar
	$consulta = " insert into asistencia_det$mes$anno ( tipo_doc, num_doc, fecha, mes, semana, dia, asistencia, id_usuario, repite, consumio, repitio ) values ";
	$aux = 0;
	$valores = "";
	foreach ($asistencias as $asistencia){
		if($aux > 0){
			$valores .= " , ";
		}
		$valores .= " ( ";
		$auxField = mysqli_real_escape_string($Link, $asistencia["tipoDocumento"]);
		$valores .= " \"$auxField\", ";	
		$auxField = mysqli_real_escape_string($Link, $asistencia["documento"]);
		$valores .= " \"$auxField\", ";
		$valores .= " \"$fecha\", ";
		$valores .= " \"$mes\", ";
		$valores .= " \"$semana\", ";
		$valores .= " \"$dia\", ";
		$auxField = mysqli_real_escape_string($Link, $asistencia["asistencia"]);
		$valores .= " $auxField, ";
		$valores .= " $id_usuario, ";
		$valores .= " 0, ";
		$valores .= " 0, ";
		$valores .= " 0 ";
		$valores .= " ) ";
		$aux++;
	}

	$consulta .= $valores;
	$consulta .= " ON DUPLICATE KEY UPDATE 
	fecha = values(fecha),
	asistencia = values(asistencia), 
	id_usuario = values(id_usuario), 	
	repite = values(repite), 
	consumio = values(consumio), 
	repitio = values(repitio) ";
	//echo $consulta;
	

	$result = $Link->query($consulta) or die ('Insert error'. mysqli_error($Link));
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
}else if($banderaRegistros == 1){
	//Actualizar las asistencias esto con el fin de no perder registros de consumo
	//Si una asistencia = 0, se iguala a 0 que repite, si consumio o si repitió. 
	$consulta = "";
	foreach ($asistencias as $asistencia){

		$tipo_doc = mysqli_real_escape_string($Link, $asistencia["tipoDocumento"]);
		$num_doc = mysqli_real_escape_string($Link, $asistencia["documento"]);
		$asistenciaVal = mysqli_real_escape_string($Link, $asistencia["asistencia"]);

		$consulta .= " update asistencia_det$mes$anno set asistencia = $asistenciaVal  ";

		if($asistenciaVal == 0){
			$consulta .= " , repite = 0, consumio = 0, repitio = 0 ";	
		}
		$consulta .= " where mes = \"$mes\" and semana = \"$semana\" and dia = \"$dia\" and id_usuario = $id_usuario and tipo_doc = \"$tipo_doc\" and num_doc = \"$num_doc\"; "; 

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

}
echo json_encode($resultadoAJAX);