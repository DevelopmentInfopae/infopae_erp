<?php
require_once '../../../config.php';
require_once '../../../db/conexion.php';

$mes = (isset($_POST['mes']) && $_POST['mes'] != '') ? mysqli_real_escape_string($Link, $_POST["mes"]) : "";
$opciones = "<option value=\"\">Seleccione una</option>";

$consulta = "SELECT DISTINCT ps.semana, (SELECT count(*) FROM planilla_semanas WHERE semana = ps.semana) AS cantidad_dias  FROM planilla_semanas ps WHERE mes = '$mes' ORDER BY semana ASC";
$resultado = $Link->query($consulta);
if($resultado->num_rows > 0){
	while($row = $resultado->fetch_assoc()) {
		$semana = $row['semana'];
		$cantidadDias = $row["cantidad_dias"];
		$opciones .= " <option value=\"$semana\" data-cantidaddias=\"$cantidadDias\">$semana</option> ";
	}
	$respuestaAJAX = [
		"estado" => 1,
		"opciones" => $opciones,
		"mensaje" => "Instituciones cargados correctamente."
	];
}
echo json_encode($respuestaAJAX);
