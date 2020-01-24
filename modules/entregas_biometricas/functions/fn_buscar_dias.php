<?php
require_once '../../../db/conexion.php';
require_once '../../../config.php';

$mes = '';
$semana = '';
if(isset($_POST['mes']) && $_POST['mes'] != ''){
		$mes = mysqli_real_escape_string($Link, $_POST['mes']);
}
if(isset($_POST['semana']) && $_POST['semana'] != ''){
		$semana = mysqli_real_escape_string($Link, $_POST['semana']);
}



$opciones = "<option value=\"\">Seleccione uno</option>";

$consulta = " select distinct(dia) as dia from planilla_semanas where mes = \"$mes\" and semana = \"$semana\" order by dia+0 asc ";
// echo $consulta;

$resultado = $Link->query($consulta) or die ('No se pudieron cargar los muunicipios. '. mysqli_error($Link));
if($resultado->num_rows >= 1){
	$respuesta = 1;
	while($row = $resultado->fetch_assoc()){				
		$id = $row["dia"];
		$valor = $row["dia"];
		
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