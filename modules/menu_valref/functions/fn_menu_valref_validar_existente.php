<?php 

require_once '../../../config.php';
require_once '../../../db/conexion.php';

$complemento = $_POST['complemento'];
$grupoEtario = $_POST['grupoEtario'];

$consulta = "SELECT * FROM menu_valref_nutrientes WHERE Cod_Grupo_Etario = '".$grupoEtario."' AND Cod_tipo_complemento = '".$complemento."'";
$resultado = $Link->query($consulta);
if ($resultado->num_rows > 0) {
	echo "1";
} else {
	echo "0";
}