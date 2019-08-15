<?php
	require_once '../../../config.php';
	require_once '../../../db/conexion.php';

	$data = [];
	$perido_actual = $_SESSION["periodoActual"];
	$mes = (isset($_POST['mes']) && $_POST['mes'] != '') ? mysqli_real_escape_string($Link, $_POST["mes"]) : "";
	$sede = (isset($_POST['sede']) && $_POST['sede'] != '') ? mysqli_real_escape_string($Link, $_POST["sede"]) : "";
	$semana = (isset($_POST['semana']) && $_POST['semana'] != '') ? mysqli_real_escape_string($Link, $_POST["semana"]) : "";
	$municipio = (isset($_POST['municipio']) && $_POST['municipio'] != '') ? mysqli_real_escape_string($Link, $_POST["municipio"]) : "";
	$insitucion = (isset($_POST['insitucion']) && $_POST['insitucion'] != '') ? mysqli_real_escape_string($Link, $_POST["insitucion"]) : "";
	$tipoComplemento = (isset($_POST['tipoComplemento']) && $_POST['tipoComplemento'] != '') ? mysqli_real_escape_string($Link, $_POST["tipoComplemento"]) : "";

	// Consulta que retorna los dias de planillas el mes seleccionado.
	$consultaPlanillaDias = "SELECT D1, D2, D3, D4, D5, D6, D7, D8, D9, D10, D11, D12, D13, D14, D15, D16, D17, D18, D19, D20, D21, D22, D23, D24, D25, D26, D27, D28, D29, D30, D31  FROM planilla_dias WHERE mes = '$mes';";
	$resultadoPlanillaDias = $Link->query($consultaPlanillaDias) or die("Error al consultar planilla_dias: ". $Link->error);
	if ($resultadoPlanillaDias->num_rows > 0)
	{
		while ($registroPlanillasDias = $resultadoPlanillaDias->fetch_assoc())
		{
			$planilla_dias = $registroPlanillasDias;
		}
	}

	// Consulta para determinar las columnas de días de consulta por la semana seleccionada.
	$columnasDiasEntregas_res = $columnasDiasSuma = "";
	$consultaPlanillaSemanas = "SELECT DIA as dia FROM planilla_semanas WHERE SEMANA = '$semana' AND MES ='$mes'";
	$resultadoPlanillaSemanas = $Link->query($consultaPlanillaSemanas) or die("Error al consultar planilla_semanas: ". $Link->error);
	if($resultadoPlanillaSemanas->num_rows > 0)
	{
		$indiceDia = 1;
		while($registroPlanillaSemanas = $resultadoPlanillaSemanas->fetch_assoc())
		{
			foreach ($planilla_dias as $clavePlanillasDias => $valorPlanillasDias)
			{
				if ($registroPlanillaSemanas["dia"] == $valorPlanillasDias)
				{
					$columnasDiasEntregas_res .= "e.". $clavePlanillasDias ." AS D". $indiceDia .", ";
					$columnasDiasSuma .= "e.". $clavePlanillasDias . " + ";
					$indiceDia++;
				}
			}
		}
	}

	// Datos del estudiante
	$columnasDiasSuma = trim($columnasDiasSuma, " + ");
	$columnasDiasEntregas_res = trim($columnasDiasEntregas_res, ", ");

	$consulta_focalizados = "SELECT
								td.Abreviatura AS abreviatura_documento,
								e.num_doc AS numero_documento,
								e.tipo_complem AS complemento,
								CONCAT(e.nom1,' ',e.nom2,' ',e.ape1,' ',e.ape2) AS nombre,
								f.cod_grado AS grado,
								f.nom_grupo AS grupo,
								$columnasDiasEntregas_res,
								($columnasDiasSuma) AS suma_dias
							FROM entregas_res_$mes$perido_actual e
								RIGHT JOIN focalizacion$semana f ON e.num_doc = f.num_doc AND e.cod_sede = f.cod_sede  AND e.tipo_complem = f.Tipo_complemento
								INNER JOIN tipodocumento td ON f.tipo_doc = td.id
							WHERE f.cod_sede = $sede AND e.tipo_complem = '$tipoComplemento' AND f.activo = 1 AND e.tipo='F'";

	$respuesta_focalizados = $Link->query($consulta_focalizados) or die('Error al consultar focalizacion: '. $Link->error);
	if($respuesta_focalizados->num_rows > 0)
	{
		while($registros_focalizados = $respuesta_focalizados->fetch_assoc())
		{
			$numero_documento = $registros_focalizados['numero_documento'];
			$abreviatura_documento = $registros_focalizados['abreviatura_documento'];

			$registros_focalizados['numero_documento'] = $numero_documento . '<input type="hidden" name="numero_documentos[]" value="'.$numero_documento.'"/>';
			$registros_focalizados['abreviatura_documento'] = $abreviatura_documento . '<input type="hidden" name="abreviatura_documentos[]" value="'.$abreviatura_documento.'" />';

			$chequeado = (isset($registros_focalizados['D1']) && $registros_focalizados['D1'] == 1) ? 'checked' : '';
			$registros_focalizados['D1'] = (isset($registros_focalizados['D1'])) ? '<div class="checkbox checkbox-success"><input type="checkbox" class="checkbox1" name="'.$numero_documento.'_D1" id="'.$numero_documento.'_D1" value="1" '.$chequeado.'><label for="'.$numero_documento.'_D1"></label></div>' : '';

			$chequeado = (isset($registros_focalizados['D2']) && $registros_focalizados['D2'] == 1) ? 'checked' : '';
			$registros_focalizados['D2'] = (isset($registros_focalizados['D2'])) ? '<div class="checkbox checkbox-success"><input type="checkbox" class="checkbox2" name="'.$numero_documento.'_D2" id="'.$numero_documento.'_D2" value="1" '.$chequeado.'><label for="'.$numero_documento.'_D2"></label></div>' : '';

			$chequeado = (isset($registros_focalizados['D3']) && $registros_focalizados['D3'] == 1) ? 'checked' : '';
			$registros_focalizados['D3'] = (isset($registros_focalizados['D3'])) ? '<div class="checkbox checkbox-success"><input type="checkbox" class="checkbox3" name="'.$numero_documento.'_D3" id="'.$numero_documento.'_D3" value="1" '.$chequeado.'><label for="'.$numero_documento.'_D3"></label></div>' : '';

			$chequeado = (isset($registros_focalizados['D4']) && $registros_focalizados['D4'] == 1) ? 'checked' : '';
			$registros_focalizados['D4'] = (isset($registros_focalizados['D4'])) ? '<div class="checkbox checkbox-success"><input type="checkbox" class="checkbox4" name="'.$numero_documento.'_D4" id="'.$numero_documento.'_D4" value="1" '.$chequeado.'><label for="'.$numero_documento.'_D4"></label></div>' : '';

			$chequeado = (isset($registros_focalizados['D5']) && $registros_focalizados['D5'] == 1) ? 'checked' : '';
			$registros_focalizados['D5'] = (isset($registros_focalizados['D5'])) ? '<div class="checkbox checkbox-success"><input type="checkbox" class="checkbox5" name="'.$numero_documento.'_D5" id="'.$numero_documento.'_D5" value="1" '.$chequeado.'><label for="'.$numero_documento.'_D5"></label></div>' : '';

			$data[] = $registros_focalizados;
		}
	}

	$output = [
		'sEcho' => 1,
		'iTotalRecords' => count($data),
		'iTotalDisplayRecords' => count($data),
		'aaData' => $data
	];

	echo json_encode($output);
