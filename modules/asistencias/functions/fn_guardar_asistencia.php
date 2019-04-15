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
$grado = mysqli_real_escape_string($Link, $_POST['grado']);
$grupo = mysqli_real_escape_string($Link, $_POST['grupo']);

$dia = intval(date("d"));
$id_usuario = mysqli_real_escape_string($Link, $_SESSION['id_usuario']);

$asistencias = $_POST['asistencia'];


$consulta = " delete from Asistencia$mes$anno where Asistencia$mes$anno.mes = \"$mes\"and Asistencia$mes$anno.semana = \"$semana\"and Asistencia$mes$anno.dia = \"$dia\" and Asistencia$mes$anno.num_doc in (select focalizacion$semana.num_doc from focalizacion$semana where focalizacion$semana.cod_sede = \"$sede\" ";

if(isset($grado) && $grado != ""){
	$consulta .= "and focalizacion$semana.cod_grado = \"$grado\" "; 
}

if(isset($grupo) && $grupo != ""){
	$consulta .= "and focalizacion$semana.nom_grupo = \"$grupo\"";
}
$consulta .= " ) ";


//echo $consulta; 
$result = $Link->query($consulta) or die ('Delete'. mysqli_error($Link));





$consulta = " insert into Asistencia$mes$anno ( tipo_doc, num_doc, fecha, mes, semana, dia, asistencia, id_usuario ) values ";
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
echo json_encode($resultadoAJAX);