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

$consulta = " SELECT * FROM dispositivos WHERE cod_sede = \"$sede\" ";

//echo $consulta;

$resultado = $Link->query($consulta) or die ('No se pudieron cargar los niveles. '. mysqli_error($Link));
if($resultado->num_rows >= 1){
	$row = $resultado->fetch_assoc();
    $referencia = $row['referencia'];
    $idDispositivo = $row['id'];
    $opciones .= "<option value=\"$idDispositivo\">$referencia</option>";
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