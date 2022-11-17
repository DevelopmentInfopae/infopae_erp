<option value="">Seleccione</option>
<?php 
include '../../../config.php';
require_once '../../../db/conexion.php';

$mes = (isset($_POST["mes"]) && $_POST["mes"]) ? $Link->real_escape_string($_POST["mes"]) : "";

$consulta = "SELECT SEMANA AS semana FROM planilla_semanas WHERE MES = '$mes' GROUP BY SEMANA;";
$respuesta = $Link->query($consulta) or die("Error al consultar las semanas: ". $Link->error);

if ($respuesta->num_rows > 0) {
	while ($semana = $respuesta->fetch_assoc()) {
?>
	<option value="<?= $semana["semana"]; ?>"><?= "Semana ". $semana["semana"]; ?> </option>
<?php
	}
}
