<?php
require_once '../../../db/conexion.php';
require_once '../../../config.php';

$periodoActual = $_SESSION['periodoActual'];
$grado = '';
$semanaActual = '';
$sede = '';
$grupo = '';

if(isset($_POST['semanaActual']) && $_POST['semanaActual'] != ''){
	$semanaActual = mysqli_real_escape_string($Link, $_POST['semanaActual']);
}
if(isset($_POST['grado']) && $_POST['grado'] != ''){
	$grado = mysqli_real_escape_string($Link, $_POST['grado']);
}
if(isset($_POST['sede']) && $_POST['sede'] != ''){
	$sede = mysqli_real_escape_string($Link, $_POST['sede']);
}
if(isset($_POST['grupo']) && $_POST['grupo'] != ''){
	$grupo = mysqli_real_escape_string($Link, $_POST['grupo']);
}
// exit(var_dump($_POST));
$opciones = "<option value=\"\">Seleccione uno</option>";
if($grado != ""){
	$consulta = "SELECT DISTINCT(nom_grupo) 
					FROM focalizacion$semanaActual 
					WHERE cod_grado = $grado AND cod_sede = \"$sede\"
					ORDER BY nom_grupo ASC "; 
	$resultado = $Link->query($consulta) or die ('No se pudieron cargar los grupos. '.$consulta. mysqli_error($Link));
	if($resultado->num_rows >= 1){
		$respuesta = 1;
		while($row = $resultado->fetch_assoc()){
			$selected = ($grupo == $row['nom_grupo']) ? 'selected' : '';
			$id = $row["nom_grupo"];
			$valor = $row["nom_grupo"];		
			$opciones .= "<option value=\"$id\" $selected ";
			$opciones .= ">";
			$opciones .= "$valor</option>";
		}
	}if($resultado){
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
}else{
	$resultadoAJAX = array(
		"estado" => 1,
		"mensaje" => "Se ha cargado con exito.",
		"opciones" => $opciones
	);	
}

echo json_encode($resultadoAJAX);