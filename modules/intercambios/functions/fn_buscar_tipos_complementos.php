<?php
require_once '../../../db/conexion.php';
require_once '../../../config.php';

$mes = '';
if(isset($_POST['mes']) && $_POST['mes'] != ''){
		$mes = mysqli_real_escape_string($Link, $_POST['mes']);
}
$opciones = "<option value=\"\">Seleccione uno</option>";

$consulta = " SELECT * FROM tipo_complemento order by CODIGO asc ";
// echo $consulta;

$resultado = $Link->query($consulta) or die ('No se pudieron cargar los muunicipios. '. mysqli_error($Link));
if($resultado->num_rows >= 1){
	$respuesta = 1;
	while($row = $resultado->fetch_assoc()){
		$id = $row["CODIGO"];
		$valor = $row["CODIGO"];

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