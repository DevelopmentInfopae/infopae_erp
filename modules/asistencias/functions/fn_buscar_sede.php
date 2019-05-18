<?php
require_once '../../../db/conexion.php';
require_once '../../../config.php';

$periodoActual = mysqli_real_escape_string($Link, $_SESSION['periodoActual']);

$institucion = '';
if(isset($_POST['institucion']) && $_POST['institucion'] != ''){
		$institucion = mysqli_real_escape_string($Link, $_POST['institucion']);
}
$opciones = "<option value=\"\">Seleccione uno</option>";

$consulta = " select * from sedes$periodoActual where tipo_validacion = \"Tablet\" and cod_inst = \"$institucion\" ";
$consulta = $consulta." order by nom_sede asc ";

// echo $consulta;

$resultado = $Link->query($consulta) or die ('No se pudieron cargar los muunicipios. '. mysqli_error($Link));
if($resultado->num_rows >= 1){
		$respuesta = 1;
		while($row = $resultado->fetch_assoc()){
				
				$id = $row["cod_sede"];
				$valor = $row["nom_sede"];
				
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