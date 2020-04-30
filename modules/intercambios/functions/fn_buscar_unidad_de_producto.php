<?php
require_once '../../../db/conexion.php';
require_once '../../../config.php';

$periodoActual = $_SESSION['periodoActual'];

$codigo = '';
if(isset($_POST['codigo']) && $_POST['codigo'] != ''){
		$codigo = mysqli_real_escape_string($Link, $_POST['codigo']);
}

$consulta = " SELECT NombreUnidad1 FROM productos$periodoActual WHERE Nivel = 3 AND Codigo = $codigo ";
// echo $consulta;

$resultado = $Link->query($consulta) or die ('No se pudieron cargar los muunicipios. '. mysqli_error($Link));
if($resultado->num_rows >= 1){
	$respuesta = 1;
	$row = $resultado->fetch_assoc();
	$unidad = $row["NombreUnidad1"];
}if($resultado){
	$resultadoAJAX = array(
		"estado" => 1,
		"mensaje" => "Se ha cargado con exito.",
		"unidad" => $unidad
	);
}else{
	$resultadoAJAX = array(
		"estado" => 0,
		"mensaje" => "Se ha presentado un error."
	);
}
echo json_encode($resultadoAJAX);