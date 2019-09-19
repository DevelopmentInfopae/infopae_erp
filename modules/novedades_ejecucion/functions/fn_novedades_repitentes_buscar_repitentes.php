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
	$columnas_dias_entregas = $columnas_suma_dias = $columnas_dias_maximo_entregas = "";
	$consultaPlanillaSemanas = "SELECT DIA as dia FROM planilla_semanas WHERE MES='$mes' AND semana = '$semana'";
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
					$columnas_dias_entregas .= "e.". $clavePlanillasDias ." AS D". $indiceDia .", ";
					$columnas_suma_dias .= "e.". $clavePlanillasDias . " + ";
					$columnas_dias_maximo_entregas .= "MAX(D". $indiceDia .") AS maximo_D". $indiceDia .", ";
					$indiceDia++;
				}
			}
		}
	}

	// Datos del estudiante
	$columnas_suma_dias = trim($columnas_suma_dias, "+ ");
	$columnas_dias_entregas = trim($columnas_dias_entregas, ", ");
	$columnas_dias_maximo_entregas = trim($columnas_dias_maximo_entregas, ", ");

	$repitentes_existentes = [];
	$consulta_repitentes = "SELECT
	 											    e.num_doc,
	 													$columnas_dias_entregas
	 												FROM
	 											    focalizacion$semana f
	 												INNER JOIN
	 													entregas_res_$mes$periodo_actual e ON e.num_doc = f.num_doc AND e.tipo_complem = f.Tipo_complemento AND e.cod_sede = f.cod_sede
	 												INNER JOIN
	 													sedes$periodo_actual s ON s.cod_sede = e.cod_sede
	 												WHERE
	 											    f.cod_sede = '$sede'
	 								        AND f.Tipo_complemento = '$tipo_complemento'
	 								        AND e.tipo='R'";

	$resultado_repitentes = $Link->query($consulta_repitentes) or die("Error al consultar novedades_suplentes: ". $Link->error);
  if($resultado_repitentes->num_rows > 0)
  {
    while($registros_repitentes = $resultado_repitentes->fetch_assoc())
    {
			$repitentes_existentes[] = $registros_repitentes;
    }
  }

  echo json_encode($repitentes_existentes);
