<?php
	require_once '../../../config.php';
	require_once '../../../db/conexion.php';

	$total_priorizacion = 0;
	$perido_actual = $_SESSION["periodoActual"];

	$municipio = (isset($_POST['municipio']) && $_POST['municipio'] != '') ? mysqli_real_escape_string($Link, $_POST["municipio"]) : "";
	$institucion = (isset($_POST['institucion']) && $_POST['institucion'] != '') ? mysqli_real_escape_string($Link, $_POST["institucion"]) : "";
	$sede = (isset($_POST['sede']) && $_POST['sede'] != '') ? mysqli_real_escape_string($Link, $_POST["sede"]) : "";
	$mes = (isset($_POST['mes']) && $_POST['mes'] != '') ? mysqli_real_escape_string($Link, $_POST["mes"]) : "";
	$semana = (isset($_POST['semana']) && $_POST['semana'] != '') ? mysqli_real_escape_string($Link, $_POST["semana"]) : "";
	$tipo_complemento = (isset($_POST['tipo_complemento']) && $_POST['tipo_complemento'] != '') ? mysqli_real_escape_string($Link, $_POST["tipo_complemento"]) : "";
	
	$condicionMunicipio = $condicionInstitucion = $condicionSede = '';
	if ($municipio != '') {
		$condicionMunicipio = " AND s.cod_mun_sede = '$municipio' ";
	}
	if ($institucion != '') {
		$condicionInstitucion = " AND s.cod_inst = '$institucion' ";
	}
	if ($sede != '') {
		$condicionSede = " AND p.cod_sede = '$sede' ";
	}

	$consulta_priorizacion = "	SELECT SUM($tipo_complemento * (SELECT COUNT(DIA) FROM planilla_semanas WHERE MES_ENTREGAS='$mes' AND SEMANA='$semana')) AS total_priorizacion
									FROM priorizacion$semana p
									JOIN sedes$perido_actual s ON s.cod_sede = p.cod_sede
									WHERE 1=1 
									$condicionMunicipio
									$condicionInstitucion
									$condicionSede
									";
	// exit(var_dump($consulta_priorizacion));
		$respuesta_priorizacion = $Link->query($consulta_priorizacion) or die('Error al consultar priorizacion: '. $Link->error);
		if ($respuesta_priorizacion->num_rows > 0) {
			$priorizacion = $respuesta_priorizacion->fetch_object();
			$total_priorizacion = $priorizacion->total_priorizacion;
		}
	

	echo json_encode($total_priorizacion);