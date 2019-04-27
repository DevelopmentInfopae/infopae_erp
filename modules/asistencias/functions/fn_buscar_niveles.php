<?php
require_once '../../../db/conexion.php';
require_once '../../../config.php';

$periodoActual = $_SESSION['periodoActual'];
// var_dump($_POST);

$sede = '';
$semanaActual = '';

if(isset($_POST['semanaActual']) && $_POST['semanaActual'] != ''){
	$semanaActual = mysqli_real_escape_string($Link, $_POST['semanaActual']);
}
if(isset($_POST['sede']) && $_POST['sede'] != ''){
	$sede = mysqli_real_escape_string($Link, $_POST['sede']);
}

$opciones = "<option value=\"\">Seleccione uno</option>";

$consulta = "select distinct  min(f.cod_grado) as min, max(f.cod_grado) as max from focalizacion13 f where f.cod_sede = \"$sede\" order by f.cod_grado asc ";

//echo $consulta;

$resultado = $Link->query($consulta) or die ('No se pudieron cargar los muunicipios. '. mysqli_error($Link));
if($resultado->num_rows >= 1){
	$row = $resultado->fetch_assoc();
	$min = $row['min'];
	$max = $row['max'];

	if($min < 6){
		// Tiene primaria
		$opciones .= "<option value=\"1\">Primaria</option>";
	}
	if($max > 5){
		// Tiene bachillerato
		$opciones .= "<option value=\"2\">Secundaria</option>";
	}

}
if($resultado){
		$resultadoAJAX = array(
			"estado" => 1,
			"mensaje" => "Se ha cargado con exito.",
			"opciones" => $opciones
		);
}else{
	$resultadoAJAX = array(
		"estado" => 0,
		"mensaje" => "Se ha presentado un error."
	);
}
echo json_encode($resultadoAJAX);