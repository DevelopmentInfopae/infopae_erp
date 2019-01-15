<?php
require_once '../../../config.php';
require_once '../../../db/conexion.php';

$mes = (isset($_POST['mes']) && $_POST['mes'] != '') ? mysqli_real_escape_string($Link, $_POST["mes"]) : "";
$opciones = "<option value=\"\">Seleccione una</option>";

$consulta = "select distinct semana from planilla_semanas where mes = '$mes' order by semana asc";
$resultado = $Link->query($consulta);
if($resultado->num_rows > 0){
	while($row = $resultado->fetch_assoc()) {
		$semana = $row['semana'];
		$opciones .= " <option value=\"$semana\">$semana</option> ";
	}
	$respuestaAJAX = [
		"estado" => 1,
		"opciones" => $opciones,
		"mensaje" => "Instituciones cargados correctamente."
	];
}
echo json_encode($respuestaAJAX);
