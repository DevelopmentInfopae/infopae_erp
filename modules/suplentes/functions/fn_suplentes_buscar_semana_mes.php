<?php
	require_once '../../../db/conexion.php';
	require_once '../../../config.php';

	$mes = (isset($_POST["mes"]) && $_POST["mes"] != "") ? mysqli_real_escape_string($Link, $_POST["mes"]) : "";

	$consultaSemana = "SELECT DISTINCT SEMANA AS semana FROM planilla_semanas WHERE MES = '$mes';";
	$resultadoSemana = $Link->query($consultaSemana);
	$Link->close();
?>
	<option value="">seleccione</option>
<?php
	while($registrosSemana = $resultadoSemana->fetch_assoc()) {  ?>
		<option value="<?php echo $registrosSemana["semana"]; ?>"><?php echo $registrosSemana["semana"]; ?></option>
<?php
	}
?>
