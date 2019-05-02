<option value="">Seleccione uno</option>
<?php
	include '../../../config.php';
	require_once '../../../db/conexion.php';

	// Variables.
	$diaInicial = "";
	$mes = (isset($_POST["mes"]) && $_POST["mes"]) ? mysqli_real_escape_string($Link, $_POST["mes"]) : "";
	$diainicialSemanaAnterior = (isset($_POST["diainicialSemanaAnterior"]) && $_POST["diainicialSemanaAnterior"]) ? mysqli_real_escape_string($Link, $_POST["diainicialSemanaAnterior"]) : "";

	// Consulta que retorna los días (desde - hasta) de las semana del mes seleccionado.
	if ($diainicialSemanaAnterior != "") {
		$diaInicial = "AND DIA >= $diainicialSemanaAnterior";
	}

	$consultaPlanillaSemanas = "SELECT ID, SEMANA, MIN(DIA) AS dia_inicial, MAX(DIA) AS dia_final FROM planilla_semanas WHERE MES = '$mes' $diaInicial GROUP BY SEMANA;";
	$res_sem = $Link->query($consultaPlanillaSemanas) or die(mysqli_error($Link));

	if ($res_sem->num_rows > 0) {
		while ($reg_sem = $res_sem->fetch_assoc()) {
?>
	<option value="<?= $reg_sem["SEMANA"]; ?>" data-diainicial="<?= $reg_sem["dia_inicial"]; ?>" data-diafinal="<?= $reg_sem["dia_final"]; ?>"> <?= "SEMANA ". $reg_sem["SEMANA"]; ?> </option>
<?php
		}
	}