<?php
require_once '../../../db/conexion.php';
require_once '../../../config.php';

$periodoActual = $_SESSION['periodoActual'];

// var_dump($_POST);

$sede = '';
if(isset($_POST['sede']) && $_POST['sede'] != ''){
		$sede = $_POST['sede'];
}
$opciones = "<option value=\"\">Seleccione uno</option>";

$consulta = " select distinct cod_grado from focalizacion01 where cod_sede = \"$sede\" order by cod_grado asc ";

// echo $consulta;

$resultado = $Link->query($consulta) or die ('No se pudieron cargar los muunicipios. '. mysqli_error($Link));
if($resultado->num_rows >= 1){
		$respuesta = 1;
		while($row = $resultado->fetch_assoc()){
				
				$id = $row["cod_grado"];
				$valor = $row["cod_grado"];
				
				$opciones .= "<option value=\"$id\"";
				// if($sede == $id){
				// 		$opciones .= " selected ";
				// }
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