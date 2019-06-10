<?php
  require_once '../../../db/conexion.php';
  require_once '../../../config.php';

  // Declaración de variables.
  $data = [];
  $periodo_actual = $_SESSION['periodoActual'];
  $mes = (isset($_POST['mes']) && ! empty($_POST['mes'])) ? $Link->real_escape_string($_POST['mes']) : '';
  $sede = (isset($_POST['sede']) && ! empty($_POST['sede'])) ? $Link->real_escape_string($_POST['sede']) : '';
  $semana = (isset($_POST['semana']) && ! empty($_POST['semana'])) ? $Link->real_escape_string($_POST['semana']) : '';
  $institucion = (isset($_POST['institucion']) && ! empty($_POST['institucion'])) ? $Link->real_escape_string($_POST['institucion']) : '';
  $tipo_complemento = (isset($_POST['tipo_complemento']) && !empty($_POST['tipo_complemento'])) ? $Link->real_escape_string($_POST['tipo_complemento']) : '';

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
	$columnasDiasEntregas_res = $columnasDiasSuma = $columnasDiasSuplentes = "";
	$consultaPlanillaSemanas = "SELECT DIA as dia FROM planilla_semanas WHERE semana = '$semana'";
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
					$columnasDiasEntregas_res .= "! ISNULL(e.". $clavePlanillasDias .") AS D". $indiceDia .", ";
					$columnasDiasSuplentes .= " 0 AS D". $indiceDia .", ";
					$indiceDia++;
				}
			}
		}
	}

	// Datos del estudiante
	$columnasDiasSuma = trim($columnasDiasSuma, " + ");
	$columnasDiasEntregas_res = trim($columnasDiasEntregas_res, ", ");
	$columnasDiasSuplentes = trim($columnasDiasSuplentes, ", ");

  $consulta_suplentes = "SELECT * FROM (
																SELECT
															    sup.tipo_doc_nom AS abreviatura_documento,
				  												sup.num_doc AS numero_documento,
				  												CONCAT(sup.nom1,' ',sup.nom2,' ',sup.ape1,' ',sup.ape2) AS nombre,
															    $columnasDiasEntregas_res
																FROM
															    suplentes$semana sup
														        LEFT JOIN
															    entregas_res_$mes$periodo_actual e ON e.num_doc = sup.num_doc
														        AND e.cod_sede = sup.cod_sede
																WHERE
															    sup.cod_sede='$sede'
															    AND e.tipo_complem = '$tipo_complemento'
															    AND e.tipo='S'

																UNION ALL

																SELECT
															    sup.tipo_doc_nom AS abreviatura_documento,
															    sup.num_doc AS numero_documento,
															    CONCAT(sup.nom1, ' ', sup.nom2, ' ', sup.ape1, ' ', sup.ape2) AS nombre,
																	$columnasDiasSuplentes
																FROM
															    suplentes$semana sup
																WHERE
															    sup.cod_sede = '$sede'
													    ) AS TG
															GROUP BY TG.numero_documento
															ORDER BY TG.nombre ASC";
	$resultado_suplentes = $Link->query($consulta_suplentes) or die("Error al consultar novedades_suplentes: ". $Link->error);
  if($resultado_suplentes->num_rows > 0)
  {
    while($registros_suplentes = $resultado_suplentes->fetch_assoc())
    {
      $numero_documento = $registros_suplentes['numero_documento'];
			$abreviatura_documento = $registros_suplentes['abreviatura_documento'];

			$registros_suplentes['numero_documento'] = $numero_documento . '<input type="hidden" name="numero_documentos[]" value="'.$numero_documento.'"/>';
			$registros_suplentes['abreviatura_documento'] = $abreviatura_documento . '<input type="hidden" name="abreviatura_documentos[]" value="'.$abreviatura_documento.'" />';

			$chequeado = (isset($registros_suplentes['D1']) && $registros_suplentes['D1'] == 1) ? 'checked' : '';
			$registros_suplentes['D1'] = (isset($registros_suplentes['D1'])) ? '<div class="checkbox checkbox-success"><input type="checkbox" class="checkbox1" name="'.$numero_documento.'_D1" id="'.$numero_documento.'_D1" value="1" '.$chequeado.'><label for="'.$numero_documento.'_D1"></label></div>' : '';

			$chequeado = (isset($registros_suplentes['D2']) && $registros_suplentes['D2'] == 1) ? 'checked' : '';
			$registros_suplentes['D2'] = (isset($registros_suplentes['D2'])) ? '<div class="checkbox checkbox-success"><input type="checkbox" class="checkbox2" name="'.$numero_documento.'_D2" id="'.$numero_documento.'_D2" value="1" '.$chequeado.'><label for="'.$numero_documento.'_D2"></label></div>' : '';

			$chequeado = (isset($registros_suplentes['D3']) && $registros_suplentes['D3'] == 1) ? 'checked' : '';
			$registros_suplentes['D3'] = (isset($registros_suplentes['D3'])) ? '<div class="checkbox checkbox-success"><input type="checkbox" class="checkbox3" name="'.$numero_documento.'_D3" id="'.$numero_documento.'_D3" value="1" '.$chequeado.'><label for="'.$numero_documento.'_D3"></label></div>' : '';

			$chequeado = (isset($registros_suplentes['D4']) && $registros_suplentes['D4'] == 1) ? 'checked' : '';
			$registros_suplentes['D4'] = (isset($registros_suplentes['D4'])) ? '<div class="checkbox checkbox-success"><input type="checkbox" class="checkbox4" name="'.$numero_documento.'_D4" id="'.$numero_documento.'_D4" value="1" '.$chequeado.'><label for="'.$numero_documento.'_D4"></label></div>' : '';

			$chequeado = (isset($registros_suplentes['D5']) && $registros_suplentes['D5'] == 1) ? 'checked' : '';
			$registros_suplentes['D5'] = (isset($registros_suplentes['D5'])) ? '<div class="checkbox checkbox-success"><input type="checkbox" class="checkbox5" name="'.$numero_documento.'_D5" id="'.$numero_documento.'_D5" value="1" '.$chequeado.'><label for="'.$numero_documento.'_D5"></label></div>' : '';

			$data[] = $registros_suplentes;
    }
  }

  $output = [
    'sEcho' => 1,
    'iTotalRecords' => count($data),
    'iTotalDisplayRecords' => count($data),
    'aaData' => $data
  ];

  echo json_encode($output);
