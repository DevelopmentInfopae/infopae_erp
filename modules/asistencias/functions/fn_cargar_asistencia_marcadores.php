<?php
require_once '../../../db/conexion.php';
require_once '../../../config.php';

$semanaActual = "";
$sede = "";
$asistencia = array();

if(isset($_POST['semanaActual']) && $_POST['semanaActual'] != ''){
		$semanaActual = mysqli_real_escape_string($Link, $_POST['semanaActual']);
}
if(isset($_POST['sede']) && $_POST['sede'] != ''){
		$sede = mysqli_real_escape_string($Link, $_POST['sede']);
}

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
	$dia = date("d");
}











$consulta = "select a.* from asistencia_det$mes$anno a 
left join focalizacion$semanaActual f on f.tipo_doc = a.tipo_doc and f.num_doc = a.num_doc
where a.dia = $dia and f.cod_sede = '$sede'";

// echo "<br>$consulta<br>";

$resultadoAJAX = array(
	"estado" => 0,
	"mensaje" => "Se ha presentado un error."
);

$resultado = $Link->query($consulta);
if($resultado){
	if($resultado->num_rows > 0){
		while($row = $resultado->fetch_assoc()) {
			$asistencia[] = $row;
		}
		$resultadoAJAX = array(
			"estado" => 1,
			"mensaje" => "Se ha cargado con exito.",
			"asistencia" => $asistencia
		);
	}
}
else{
	$resultadoAJAX = array(
		"estado" => 0,
		"mensaje" => "Se ha presentado un error."
	);
}
echo json_encode($resultadoAJAX);