<?php
	require_once '../../../config.php';
	require_once '../../../db/conexion.php';
	date_default_timezone_set('America/Bogota');

	$dias = [];
	$novedades = [];
	$focalizados = [];
	$sinFocalizar = [];
	$diasPlanilla = [];
	$novedadesSuplentes = [];

	$fecha = date('Y-m-d H:i:s');
	$usuario = $_SESSION['idUsuario'];
	$periodo_actual = $_SESSION['periodoActual'];
	$mes = (isset($_POST['mes_hidden']) &&  !empty($_POST['mes_hidden'])) ? $Link->real_escape_string($_POST["mes_hidden"]) : "";
	$sede = (isset($_POST['sede_hidden']) && ! empty($_POST['sede_hidden'])) ? $Link->real_escape_string($_POST["sede_hidden"]) : "";
	$semana = (isset($_POST['semana_hidden']) && ! empty($_POST['semana_hidden'])) ? $Link->real_escape_string($_POST["semana_hidden"]) : "";
	$municipio = (isset($_POST['municipio_hidden']) && ! empty($_POST['municipio_hidden'])) ? $Link->real_escape_string($_POST["municipio_hidden"]) : "";
	$institucion = (isset($_POST['institucion_hidden']) && ! empty($_POST['institucion_hidden'])) ? $Link->real_escape_string($_POST["institucion_hidden"]) : "";
	$observaciones = (isset($_POST['observaciones']) && ! empty($_POST['observaciones'])) ? $Link->real_escape_string($_POST["observaciones"]) : "";
	$numero_documentos = (isset($_POST['numero_documentos']) && ! empty($_POST['numero_documentos'])) ? $_POST['numero_documentos']: "";
	$tipo_complemento = (isset($_POST['tipoComplemento_hidden']) && ! empty($_POST['tipoComplemento_hidden'])) ? $Link->real_escape_string($_POST["tipoComplemento_hidden"]) : "";
	$abreviatura_documentos = (isset($_POST['abreviatura_documentos']) && ! empty($_POST['abreviatura_documentos'])) ? $_POST['abreviatura_documentos'] : "";

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
	$respuesta_suplentes = $Link->query($consulta_suplentes) or die('Error al consultar suplentes: '. $Link->error);
	if ($respuesta_suplentes->num_rows > 0)
	{
		while($registro_suplentes = $respuesta_suplentes->fetch_assoc())
		{
			$cambio_detectado = 0;
			$numero_documento = $registro_suplentes['numero_documento'];

			if($registro_suplentes['D1'] == 1){
				if(!isset($_POST[$numero_documento.'_D1'])){
					$cambio_detectado++;
					$registro_suplentes['D1'] = 0;
				}
			}else{
				if(isset($_POST[$numero_documento.'_D1'])){
					$cambio_detectado++;
					$registro_suplentes['D1'] = 1;
				}
			}

			if($registro_suplentes['D2'] == 1){
				if(!isset($_POST[$numero_documento.'_D2'])){
					$cambio_detectado++;
					$registro_suplentes['D2'] = 0;
				}
			}else{
				if(isset($_POST[$numero_documento.'_D2'])){
					$cambio_detectado++;
					$registro_suplentes['D2'] = 1;
				}
			}

			if($registro_suplentes['D3'] == 1){
				if(!isset($_POST[$numero_documento.'_D3'])){
					$cambio_detectado++;
					$registro_suplentes['D3'] = 0;
				}
			}else{
				if(isset($_POST[$numero_documento.'_D3'])){
					$cambio_detectado++;
					$registro_suplentes['D3'] = 1;
				}
			}

			if($registro_suplentes['D4'] == 1){
				if(!isset($_POST[$numero_documento.'_D4'])){
					$cambio_detectado++;
					$registro_suplentes['D4'] = 0;
				}
			}else{
				if(isset($_POST[$numero_documento.'_D4'])){
					$cambio_detectado++;
					$registro_suplentes['D4'] = 1;
				}
			}

			if($registro_suplentes['D5'] == 1){
				if(!isset($_POST[$numero_documento.'_D5'])){
					$cambio_detectado++;
					$registro_suplentes['D5'] = 0;
				}
			}else{
				if(isset($_POST[$numero_documento.'_D5'])){
					$cambio_detectado++;
					$registro_suplentes['D5'] = 1;
				}
			}

			if ($cambio_detectado > 0)
			{
				$novedades_suplentes[] = $registro_suplentes;
			}
		}

		var_dump($novedades_suplentes);
	}


	if (empty($novedades_suplentes))
	{
		echo "No se detectaron cambios para su posterior guardado";
		exit();
	}

	$consulta_suplentes_entregas = "SELECT id, num_doc AS numero_documento FROM entregas_res_$mes$periodo_actual WHERE cod_sede='$sede' AND tipo_complem='$tipo_complemento' AND tipo='S';";
	$respuesta_suplentes_entregas = $Link->query($consulta_suplentes_entregas) or die('Error al consultar suplentes en entregas_res$mes$periodo_actual: '. $Link->error);
	if ($respuesta_suplentes_entregas->num_rows > 0)
	{
		while ($registro_suplentes_entregas = $respuesta_suplentes_entregas->fetch_assoc())
		{
			if (in_array($registro_suplentes_entregas['numero_documento'], $novedades_suplentes))
			{
				$consulta_insertar_suplentes = "INSERT INTO entregas_res_$mes$perido_actual VALUES ()";
			}
		}
	}