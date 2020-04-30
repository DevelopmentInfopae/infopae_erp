<?php
	require_once '../../../config.php';
	require_once '../../../db/conexion.php';

	$total_priorizacion = 0;
	$perido_actual = $_SESSION["periodoActual"];
	$mes = (isset($_POST['mes']) && $_POST['mes'] != '') ? mysqli_real_escape_string($Link, $_POST["mes"]) : "";
	$sede = (isset($_POST['sede']) && $_POST['sede'] != '') ? mysqli_real_escape_string($Link, $_POST["sede"]) : "";
	$semana = (isset($_POST['semana']) && $_POST['semana'] != '') ? mysqli_real_escape_string($Link, $_POST["semana"]) : "";
	$tipo_complemento = (isset($_POST['tipo_complemento']) && $_POST['tipo_complemento'] != '') ? mysqli_real_escape_string($Link, $_POST["tipo_complemento"]) : "";

	$consulta_priorizacion = "SELECT
													    $tipo_complemento * (SELECT COUNT(DIA) FROM planilla_semanas WHERE MES='$mes' AND SEMANA='$semana') AS total_priorizacion
														FROM
													    priorizacion$semana
														WHERE
													    cod_sede = '$sede';";
	$respuesta_priorizacion = $Link->query($consulta_priorizacion) or die('Error al consultar priorizacion: '. $Link->error);
	if ($respuesta_priorizacion->num_rows > 0)
	{
		$priorizacion = $respuesta_priorizacion->fetch_object();
		$total_priorizacion = $priorizacion->total_priorizacion;
	}

	echo json_encode($total_priorizacion);