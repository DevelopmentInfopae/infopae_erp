<?php 
require_once '../../../config.php';
require_once '../../../db/conexion.php';


$num_doc = $_POST['num_doc'];
$cod_sede = $_POST['cod_sede'];
$tipo_complemento = $_POST['tipo_complemento'];
$semana = $_POST['semana'];

$consulta = "SELECT * FROM ".$semana." WHERE num_doc = ".$num_doc." AND cod_sede = ".$cod_sede." AND Tipo_complemento = ".$tipo_complemento." AND activo = 0";
$resultado = $Link->query($consulta);
if ($resultado->num_rows > 0) {
	echo "1";
} else {
	echo "0";
}

 ?>