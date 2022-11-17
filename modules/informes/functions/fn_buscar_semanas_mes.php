<option value="">Seleccione... </option>
<?php
	include '../../../config.php';
	require_once '../../../db/conexion.php';

	// $diaInicial = "";
	$mes = (isset($_POST["mes"]) && $_POST["mes"]) ? $Link->real_escape_string($_POST["mes"]) : "";
	$semana = (isset($_POST["semana"]) && $_POST["semana"]) ? $Link->real_escape_string($_POST["semana"]) : "";
	// $diainicialSemanaAnterior = (isset($_POST["diainicialSemanaAnterior"]) && $_POST["diainicialSemanaAnterior"]) ? mysqli_real_escape_string($Link, $_POST["diainicialSemanaAnterior"]) : "";

	// Consulta que retorna los días (desde - hasta) de las semana del mes seleccionado.
	// if ($diainicialSemanaAnterior != "") {
		// $diaInicial = "AND DIA >= $diainicialSemanaAnterior";
	// }

	$consulta_semanas_mes = "SELECT SEMANA AS semana FROM planilla_semanas WHERE MES = '$mes' AND SEMANA >= '$semana' GROUP BY SEMANA;";
	$respuesta_semanas_mes = $Link->query($consulta_semanas_mes) or die("Error al consultar planilla_semanas: ". $Link->error);

	if ($respuesta_semanas_mes->num_rows > 0) {
		while ($semana = $respuesta_semanas_mes->fetch_assoc()) {
?>
	<option value="<?= $semana["semana"]; ?>"><?= "Semana ". $semana["semana"]; ?> </option>
<?php
		}
	}