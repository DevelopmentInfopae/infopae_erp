<?php
require_once '../../../db/conexion.php';
require_once '../../../config.php';

//var_dump($_POST);

$tipoComplemento = '';
$grupoEtario = '';
$menusSeleccionados = 0;

if(isset($_POST['tipoComplemento']) && $_POST['tipoComplemento'] != ''){
	$tipoComplemento = mysqli_real_escape_string($Link, $_POST['tipoComplemento']);
}
if(isset($_POST['grupoEtario']) && $_POST['grupoEtario'] != ''){
	$grupoEtario = mysqli_real_escape_string($Link, $_POST['grupoEtario']);
}
if(isset($_POST['menusSeleccionados']) && $_POST['menusSeleccionados'] != ''){
	$menusSeleccionados = mysqli_real_escape_string($Link, $_POST['menusSeleccionados']);
}

$opciones = "<option value=\"\">Seleccione uno</option>";

$consulta = " SELECT p.* FROM productos19 p WHERE p.Cod_Tipo_complemento = \"$tipoComplemento\" AND p.Cod_Grupo_Etario = \"$grupoEtario\" AND p.Codigo LIKE \"01%\" AND p.Nivel = 3 AND p.Codigo NOT IN ( $menusSeleccionados ) ";

// echo $consulta;

$resultado = $Link->query($consulta) or die ('No se pudieron cargar los muunicipios. '. mysqli_error($Link));
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