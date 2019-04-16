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

$consumieron = $_POST['consumieron'];
$repitieron = $_POST['repitieron'];
$consulta = "";

$tipo_doc = "";
$num_doc = "";

//var_dump($repitieron);

$consulta = " update Asistencia$mes$anno set consumio = 0, repitio = 0 where Asistencia$mes$anno.mes = \"$mes\"and Asistencia$mes$anno.semana = \"$semana\"and Asistencia$mes$anno.dia = \"$dia\" and Asistencia$mes$anno.num_doc in (select focalizacion$semana.num_doc from focalizacion$semana where focalizacion$semana.cod_sede = \"$sede\" ";

// if(isset($grado) && $grado != ""){
// 	$consulta .= "and focalizacion$semana.cod_grado = \"$grado\" "; 
// }

// if(isset($grupo) && $grupo != ""){
// 	$consulta .= "and focalizacion$semana.nom_grupo = \"$grupo\"";
// }

$consulta .= " ) ";

//echo $consulta; 
$result = $Link->query($consulta) or die ('Reinicio de entregas'. mysqli_error($Link));


$consulta = "";
foreach ($consumieron as $consumio){

	$tipo_doc = mysqli_real_escape_string($Link, $consumio["tipoDocumento"]);
	$num_doc = mysqli_real_escape_string($Link, $consumio["documento"]);

	$consulta .= " update Asistencia$mes$anno set consumio = 1 ";	

	if(isset($repitieron[$num_doc])){
		$consulta .= " , repitio = 1 ";
	}

	$consulta .= " where mes = \"$mes\" and semana = \"$semana\" and dia = \"$dia\" and asistencia = 1 and id_usuario = $id_usuario and tipo_doc = \"$tipo_doc\" and num_doc = \"$num_doc\"; ";

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