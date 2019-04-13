<?php
require_once '../../../db/conexion.php';
require_once '../../../config.php';

$periodoActual = $_SESSION['periodoActual'];

// var_dump($_POST);

$grado = '';
$semanaActual = '';
if(isset($_POST['semanaActual']) && $_POST['semanaActual'] != ''){
		$semanaActual = $_POST['semanaActual'];
}
if(isset($_POST['grado']) && $_POST['grado'] != ''){
		$grado = $_POST['grado'];
}




$opciones = "<option value=\"\">Seleccione uno</option>";


if($grado != ""){
	$consulta = "select distinct nom_grupo from focalizacion$semanaActual where cod_grado = $grado order by nom_grupo asc ";
	// echo $consulta;

	$resultado = $Link->query($consulta) or die ('No se pudieron cargar los grupos. '.$consulta. mysqli_error($Link));
	if($resultado->num_rows >= 1){
			$respuesta = 1;
			while($row = $resultado->fetch_assoc()){
					
					$id = $row["nom_grupo"];
					$valor = $row["nom_grupo"];
					
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
}else{
	$resultadoAJAX = array(
		"estado" => 1,
		"mensaje" => "Se ha cargado con exito.",
		"opciones" => $opciones
	);	
}


echo json_encode($resultadoAJAX);