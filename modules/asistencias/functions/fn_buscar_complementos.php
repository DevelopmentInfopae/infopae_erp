<?php
require_once '../../../db/conexion.php';
require_once '../../../config.php';

$periodoActual = $_SESSION['periodoActual'];
$sede = '';
$semanaActual = '';
$complemento = '';
if(isset($_POST['semanaActual']) && $_POST['semanaActual'] != ''){
	$semanaActual = mysqli_real_escape_string($Link, $_POST['semanaActual']);
}
if(isset($_POST['sede']) && $_POST['sede'] != ''){
	$sede = mysqli_real_escape_string($Link, $_POST['sede']);
}
if(isset($_POST['complemento']) && $_POST['complemento'] != ''){
	$complemento = mysqli_real_escape_string($Link, $_POST['complemento']);
}

$opciones = "<option value=\"\">Seleccione uno</option>";

$consulta = " SELECT tipo_complemento AS complemento 
				FROM focalizacion".$semanaActual." 
				WHERE cod_sede = $sede
				GROUP BY complemento ORDER BY complemento asc ";

$resultado = $Link->query($consulta) or die ('No se pudieron cargar los niveles. '. mysqli_error($Link));
if($resultado->num_rows >= 1){
	while($row = $resultado->fetch_assoc()){
		$selected = ($complemento == $row['complemento']) ? 'selected' : '';
		$id = $row["complemento"];
		$valor = $row["complemento"];
		$opciones .= "<option value=\"$id\" $selected ";
		$opciones .= ">";
		$opciones .= "$valor</option>";
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