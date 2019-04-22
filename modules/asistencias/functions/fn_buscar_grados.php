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

$consulta = "SELECT DISTINCT f.cod_grado, g.nombre FROM focalizacion$semanaActual f left join grados g on g.id = f.cod_grado WHERE f.cod_sede = \"$sede\" ORDER BY f.cod_grado ASC";


//echo $consulta;

$resultado = $Link->query($consulta) or die ('No se pudieron cargar los muunicipios. '. mysqli_error($Link));
if($resultado->num_rows >= 1){
		$respuesta = 1;
		while($row = $resultado->fetch_assoc()){
				
				$id = $row["cod_grado"];
				$valor = $row["nombre"];
				
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