<?php
require_once '../../../db/conexion.php';
require_once '../../../config.php';

$grupoEtario = '';
$tipoComplemento = '';

if(isset($_POST['grupoEtario']) && $_POST['grupoEtario'] != ''){
	$grupoEtario = mysqli_real_escape_string($Link, $_POST['grupoEtario']);
}
if(isset($_POST['tipoComplemento']) && $_POST['tipoComplemento'] != ''){
	$tipoComplemento = mysqli_real_escape_string($Link, $_POST['tipoComplemento']);
}

$opciones = "<option value=\"\">Seleccione uno</option>";


//echo $tipoComplemento;
if($tipoComplemento == "CAJMRI" || $tipoComplemento == "CAJTRI"){
	$consulta = " SELECT * FROM productos19 WHERE TipodeProducto = \"Industrializado\" ";
}else{
	$consulta = " SELECT * FROM productos19 WHERE TipodeProducto = \"Preparación\" AND Cod_Grupo_Etario = $grupoEtario ";
}
//echo $consulta;

$resultado = $Link->query($consulta) or die ('No se pudieron cargar las preparaciones. '.$consulta." ". mysqli_error($Link));
if($resultado->num_rows >= 1){
	$respuesta = 1;
	while($row = $resultado->fetch_assoc()){
		$id = $row["Codigo"];
		$valor = $row["Descripcion"];

		$opciones .= "<option value=\"$id\"";
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
echo json_encode($resultadoAJAX);