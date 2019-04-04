<?php
	require_once '../../../config.php';
	require_once '../../../db/conexion.php';

	$data = [];
	$mes = (isset($_POST['mes']) && $_POST['mes'] != '') ? mysqli_real_escape_string($Link, $_POST["mes"]) : "";
	$sede = (isset($_POST['sede']) && $_POST['sede'] != '') ? mysqli_real_escape_string($Link, $_POST["sede"]) : "";
	$semana = (isset($_POST['semana']) && $_POST['semana'] != '') ? mysqli_real_escape_string($Link, $_POST["semana"]) : "";
	$municipio = (isset($_POST['municipio']) && $_POST['municipio'] != '') ? mysqli_real_escape_string($Link, $_POST["municipio"]) : "";
	$insitucion = (isset($_POST['insitucion']) && $_POST['insitucion'] != '') ? mysqli_real_escape_string($Link, $_POST["insitucion"]) : "";
	$tipoComplemento = (isset($_POST['tipoComplemento']) && $_POST['tipoComplemento'] != '') ? mysqli_real_escape_string($Link, $_POST["tipoComplemento"]) : "";

	// Consulta que retorna los dias de planillas el mes seleccionado.
	$consultaPlanillaDias = "SELECT D1, D2, D3, D4, D5, D6, D7, D8, D9, D10, D11, D12, D13, D14, D15, D16, D17, D18, D19, D20, D21, D22, D23, D24, D25, D26, D27, D28, D29, D30, D31  FROM planilla_dias WHERE mes = '$mes';";
	$resultadoPlanillaDias = $Link->query($consultaPlanillaDias) or die("Error al consultar planilla_dias: ". $Link->error);
	if ($resultadoPlanillaDias->num_rows > 0) {
		while ($registroPlanillasDias = $resultadoPlanillaDias->fetch_assoc()) {
			$planilla_dias = $registroPlanillasDias;
		}
	}

	// Consulta para determinar las columnas de días de consulta por la semana seleccionada.
	$columnasDiasEntregas_res = $columnasDiasSuma = "";
	$consultaPlanillaSemanas = "SELECT * FROM planilla_semanas WHERE semana = '$semana'";
	$resultadoPlanillaSemanas = $Link->query($consultaPlanillaSemanas) or die("Error al consultar planilla_semanas: ". $Link->error);
	if($resultadoPlanillaSemanas->num_rows > 0){
		$indiceDia = 1;
		while($registroPlanillaSemanas = $resultadoPlanillaSemanas->fetch_assoc()) {
			foreach ($planilla_dias as $clavePlanillasDias => $valorPlanillasDias) {
				if ($registroPlanillaSemanas["DIA"] == $valorPlanillasDias) {
					if ($registroPlanillaSemanas["NOMDIAS"] == "lunes"){ $indiceDia = 1; }
					if ($registroPlanillaSemanas["NOMDIAS"] == "martes"){ $indiceDia = 2; }
					if ($registroPlanillaSemanas["NOMDIAS"] == "miercoles"){ $indiceDia = 3; }
					if ($registroPlanillaSemanas["NOMDIAS"] == "jueves"){ $indiceDia = 4; }
					if ($registroPlanillaSemanas["NOMDIAS"] == "viernes"){ $indiceDia = 5; }
					$columnasDiasEntregas_res .= "e.". $clavePlanillasDias ." AS D". $indiceDia .", ";
					$columnasDiasSuma .= "e.". $clavePlanillasDias . " + ";
					$indiceDia++;
				}
			}
		}
	}

	// echo $columnasDiasEntregas_res;

	// Datos del estudiante
	$consultaNovedad = "SELECT td.Abreviatura, f.num_doc, CONCAT(f.nom1,' ',f.nom2,' ',f.ape1,' ',f.ape2) AS nombre, '$tipoComplemento' AS complemento, ". trim($columnasDiasEntregas_res, ", ") .", (". trim($columnasDiasSuma, " + ") .") AS sumaDias
	FROM focalizacion$semana f
	INNER JOIN entregas_res_$mes". $_SESSION["periodoActual"] ." e ON e.num_doc = f.num_doc AND e.cod_sede = f.cod_sede  AND e.tipo_complem = f.Tipo_complemento
	INNER JOIN tipodocumento td ON f.tipo_doc = td.id
	WHERE f.cod_sede = $sede AND f.Tipo_complemento = '$tipoComplemento' AND f.activo = 1";

	$resultadoNovedades = $Link->query($consultaNovedad);
	if($resultadoNovedades->num_rows > 0) {
		while($registrosSedes = $resultadoNovedades->fetch_assoc()) {
			$data[] = $registrosSedes;
		}
	}

	$output = [
		'sEcho' => 1,
		'iTotalRecords' => count($data),
		'iTotalDisplayRecords' => count($data),
		'aaData' => $data
	];

	echo json_encode($output);
