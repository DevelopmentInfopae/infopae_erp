<?php
require_once '../../../config.php';
require_once '../../../db/conexion.php';
require_once '../../../php/funciones.php';

$sede = (isset($_POST['sede']) && $_POST['sede'] != '') ? mysqli_real_escape_string($Link, $_POST["sede"]) : "";
$opciones = "<option value=\"\">Seleccione una</option>";

$consulta = "SELECT DISTINCT sc.mes FROM sedes_cobertura sc WHERE sc.cod_sede = '$sede' ORDER BY sc.mes ASC";
$resultado = $Link->query($consulta);
if($resultado->num_rows > 0){
	while($row = $resultado->fetch_assoc()) {
		$mes = $row['mes'];
		$nombre = mesEnLetras($mes);
		$opciones .= " <option value=\"$mes\">$nombre</option> ";
	}
	$respuestaAJAX = [
		"estado" => 1,
		"opciones" => $opciones,
		"mensaje" => "Instituciones cargados correctamente."
	];
}
echo json_encode($respuestaAJAX);