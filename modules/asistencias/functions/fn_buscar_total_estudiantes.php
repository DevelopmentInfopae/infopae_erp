<?php
require_once '../../../db/conexion.php';
require_once '../../../config.php';

$semanaActual = "";
$sede = "";
$total = "";
if(isset($_POST['semanaActual']) && $_POST['semanaActual'] != ''){
		$semanaActual = mysqli_real_escape_string($Link, $_POST['semanaActual']);
}
if(isset($_POST['sede']) && $_POST['sede'] != ''){
		$sede = mysqli_real_escape_string($Link, $_POST['sede']);
}

$consulta = " select count(distinct num_doc) as total from focalizacion$semanaActual where cod_sede = \"$sede\" ";
$resultado = $Link->query($consulta) or die ('No se pudo cargar el total de estudiantes focalizados en  la sede. '. mysqli_error($Link));
if($resultado->num_rows >= 1){
	$row = $resultado->fetch_assoc();
	$total = $row["total"];
}if($resultado){
	$resultadoAJAX = array(
		"estado" => 1,
		"mensaje" => "Se ha cargado con exito.",
		"total" => $total
	);
}else{
	$resultadoAJAX = array(
		"estado" => 0,
		"mensaje" => "Se ha presentado un error."
	);
}
echo json_encode($resultadoAJAX);