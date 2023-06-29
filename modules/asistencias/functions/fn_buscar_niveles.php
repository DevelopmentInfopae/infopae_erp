<?php
require_once '../../../db/conexion.php';
require_once '../../../config.php';

$periodoActual = $_SESSION['periodoActual'];
$sede = '';
$semanaActual = '';
$nivel = ''; 
if(isset($_POST['semanaActual']) && $_POST['semanaActual'] != ''){
	$semanaActual = mysqli_real_escape_string($Link, $_POST['semanaActual']);
}
if(isset($_POST['sede']) && $_POST['sede'] != ''){
	$sede = mysqli_real_escape_string($Link, $_POST['sede']);
}
if(isset($_POST['nivel']) && $_POST['nivel'] != ''){
	$nivel = mysqli_real_escape_string($Link, $_POST['nivel']);
}
$opciones = "<option value=\"\">Seleccione uno</option>";
$consulta = "SELECT DISTINCT  min(f.cod_grado) AS min, max(f.cod_grado) AS max 
				FROM focalizacion$semanaActual f 
				WHERE f.cod_sede = \"$sede\" 
				ORDER BY f.cod_grado ASC ";
// exit(var_dump($consulta));
$resultado = $Link->query($consulta) or die ('No se pudieron cargar los niveles. '. mysqli_error($Link));
if($resultado->num_rows >= 1){
	$row = $resultado->fetch_assoc();
	$min = $row['min'];
	$max = $row['max'];
	if($min < 6){
		// Tiene primaria
		$selected = ($nivel==1) ? 'selected' : '';
		$opciones .= "<option value=\"1\" $selected >Primaria</option>";
	}
	if($max > 5){
		// Tiene bachillerato
		$selected = ($nivel==2) ? 'selected' : '';
		$opciones .= "<option value=\"2\" $selected>Secundaria</option>";
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