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
	$tipo_complemento = (isset($_POST['tipo_complemento_hidden']) && ! empty($_POST['tipo_complemento_hidden'])) ? $Link->real_escape_string($_POST["tipo_complemento_hidden"]) : "";
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
	$columnasDiasEntregas_res = $columnasDiasSuma = $columnasDiasSuplentes = $insertar_columnas_dias = $actualizar_columnas_dias = $columnas_dias_tabla_general = "";
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
					$columnasDiasEntregas_res .= "IFNULL(e.". $clavePlanillasDias .", 0) AS D". $indiceDia ."i, ";
					$columnasDiasSuplentes .= " 0 AS D". $indiceDia .", ";
					$insertar_columnas_dias .= $clavePlanillasDias .", ";
					$actualizar_columnas_dias .= $clavePlanillasDias . ' = VALUES ('.$clavePlanillasDias.'), ';
					$columnas_dias_tabla_general .= "MAX(TG.D".$indiceDia."i) AS D".$indiceDia.", ";
					$indiceDia++;
				}
			}
		}
	}

	// Datos del estudiante
	$columnasDiasSuma = trim($columnasDiasSuma, " + ");
	$columnasDiasSuplentes = trim($columnasDiasSuplentes, ", ");
	$insertar_columnas_dias = trim($insertar_columnas_dias, ", ");
	$columnasDiasEntregas_res = trim($columnasDiasEntregas_res, ", ");
	$actualizar_columnas_dias = trim($actualizar_columnas_dias, ", ");
	$columnas_dias_tabla_general = trim($columnas_dias_tabla_general, ", ");

  $consulta_suplentes = "SELECT TG.*, $columnas_dias_tabla_general FROM (
																SELECT
																	sup.tipo_doc AS tipo_documento,
															    sup.tipo_doc_nom AS abreviatura_documento,
				  												sup.num_doc AS numero_documento,
				  												CONCAT(sup.nom1,' ',sup.nom2,' ',sup.ape1,' ',sup.ape2) AS nombre,
															    $columnasDiasEntregas_res,
															    sup.*
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
																	sup.tipo_doc AS tipo_documento,
															    sup.tipo_doc_nom AS abreviatura_documento,
															    sup.num_doc AS numero_documento,
															    CONCAT(sup.nom1, ' ', sup.nom2, ' ', sup.ape1, ' ', sup.ape2) AS nombre,
																	$columnasDiasSuplentes,
																	sup.*
																FROM
															    suplentes$semana sup
																WHERE
															    sup.cod_sede = '$sede'
													    ) AS TG
															GROUP BY TG.numero_documento
															ORDER BY TG.nombre ASC";
  // echo $consulta_suplentes; exit();
	$respuesta_suplentes = $Link->query($consulta_suplentes) or die('Error al consultar suplentes: '. $Link->error);
	if ($respuesta_suplentes->num_rows > 0)
	{
		while($registro_suplentes = $respuesta_suplentes->fetch_assoc())
		{
			$cambio_detectado = 0;
			$numero_documento = $registro_suplentes['numero_documento'];

			if(isset($registro_suplentes['D1']))
			{
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
			}

			if(isset($registro_suplentes['D2']))
			{
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
			}

			if(isset($registro_suplentes['D3']))
			{
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
			}

			if(isset($registro_suplentes['D4']))
			{
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
			}

			if(isset($registro_suplentes['D5']))
			{
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
			}

			if ($cambio_detectado > 0)
			{
				$novedades_suplentes[] = $registro_suplentes;
			}
		}
	}

	// var_dump($novedades_suplentes); exit();

	if (empty($novedades_suplentes))
	{
		$respuesta_ajax = [
			'estado' => 0,
			'mensaje' => "No se detectaron cambios para su posterior guardado"
		];

		echo json_encode($respuesta_ajax);
		exit();
	}

	// Consulta para saber la posición de la semana seleccionada en el mes.
	$consulta_semanas_mes = "SELECT DISTINCT(SEMANA) AS semana FROM planilla_semanas WHERE MES='$mes';";
	$respuesta_semanas_mes = $Link->query($consulta_semanas_mes) or die('Error al consultar planillas semanas: '. $Link->error);
	if ($respuesta_semanas_mes->num_rows > 0)
	{
		$posicion_semana = 1;
		while($registros_semana_mes = $respuesta_semanas_mes->fetch_assoc())
		{
			if ($registros_semana_mes['semana'] == $semana)
			{
				$semana_tipo_complemento = "tipo_complem". $posicion_semana;
			}

			$posicion_semana++;
		}
	}

	// Iteración para identificar si se agrega o actualiza el suplente en entregas.
	foreach ($novedades_suplentes as $suplente)
	{
		$campos_actualizar_entregas = "";
		$tipo_documento = $suplente['tipo_documento'];
		$numero_documento = $suplente['numero_documento'];

		if (isset($suplente['D1'])) { $campos_actualizar_entregas .= ", ". $suplente['D1']; }
		if (isset($suplente['D2'])) { $campos_actualizar_entregas .= ", ". $suplente['D2']; }
		if (isset($suplente['D3'])) { $campos_actualizar_entregas .= ", ". $suplente['D3']; }
		if (isset($suplente['D4'])) { $campos_actualizar_entregas .= ", ". $suplente['D4']; }
		if (isset($suplente['D5'])) { $campos_actualizar_entregas .= ", ". $suplente['D5']; }


		$consulta_suplente_entrega = "SELECT id, num_doc FROM entregas_res_$mes$periodo_actual WHERE num_doc='$numero_documento' AND cod_sede='$sede' AND tipo_complem='$tipo_complemento' AND tipo='S';";
		$respuesta_suplente_entrega = $Link->query($consulta_suplente_entrega) or die('Error al consultar entregas_res_$mes$periodo_actual: '. $Link->error);

		if ($respuesta_suplente_entrega->num_rows > 0)
		{
			$suplente_entrega = $respuesta_suplente_entrega->fetch_assoc();
			$consulta_actualizar_suplente_entrega = "INSERT INTO entregas_res_$mes$periodo_actual (id, $insertar_columnas_dias) VALUES (". $suplente_entrega['id'] . $campos_actualizar_entregas .") ON DUPLICATE KEY UPDATE $actualizar_columnas_dias";
			$respuesta_actualizar_suplente_entrega = $Link->query($consulta_actualizar_suplente_entrega) or die('Error al actualizar suplente en entregas: '. $Link->error);
			// echo $consulta_actualizar_suplente_entrega; exit();
			if ($respuesta_actualizar_suplente_entrega === FALSE)
			{
				$respuesta_ajax = [
					'estado' => 0,
					'mensaje' => "No fue posible actualizar el suplente en entregas"
				];

				echo json_encode($respuesta_ajax);
				exit();
			}
		}
		else
		{
			$campo_insertar_entregas = "";
			$id_disp_est = (! is_null($suplente['id_disp_est'])) ? $suplente['id_disp_est'] : 0;
			$des_dept_nom = (! is_null($suplente['des_dept_nom'])) ? $suplente['des_dept_nom'] : 0;

			if (isset($suplente['D1'])) { $campo_insertar_entregas .= ", ". $suplente['D1']; }
			if (isset($suplente['D2'])) { $campo_insertar_entregas .= ", ". $suplente['D2']; }
			if (isset($suplente['D3'])) { $campo_insertar_entregas .= ", ". $suplente['D3']; }
			if (isset($suplente['D4'])) { $campo_insertar_entregas .= ", ". $suplente['D4']; }
			if (isset($suplente['D5'])) { $campo_insertar_entregas .= ", ". $suplente['D5']; }


			$consulta_insertar_suplente_entrega = "INSERT INTO entregas_res_$mes$periodo_actual (
				tipo_doc,
				num_doc,
				tipo_doc_nom,
				ape1,
				ape2,
				nom1,
				nom2,
				genero,
				dir_res,
				cod_mun_res,
				telefono,
				cod_mun_nac,
				fecha_nac,
				cod_estrato,
				sisben,
				cod_discap,
				etnia,
				resguardo,
				cod_pob_victima,
				des_dept_nom,
				nom_mun_desp,
				cod_sede,
				cod_inst,
				cod_mun_inst,
				cod_mun_sede,
				nom_sede,
				nom_inst,
				cod_grado,
				nom_grupo,
				cod_jorn_est,
				estado_est,
				repitente,
				edad,
				zona_res_est,
				id_disp_est,
				TipoValidacion,
				activo,
				tipo,
				$semana_tipo_complemento,
				tipo_complem,
				$insertar_columnas_dias)
				VALUES (
				'".$suplente['tipo_doc']."',
				'".$suplente['num_doc']."',
				'".$suplente['tipo_doc_nom']."',
				'".$suplente['ape1']."',
				'".$suplente['ape2']."',
				'".$suplente['nom1']."',
				'".$suplente['nom2']."',
				'".$suplente['genero']."',
				'".$suplente['dir_res']."',
				'".$suplente['cod_mun_res']."',
				'".$suplente['telefono']."',
				'".$suplente['cod_mun_nac']."',
				'".$suplente['fecha_nac']."',
				'".$suplente['cod_estrato']."',
				'".$suplente['sisben']."',
				'".$suplente['cod_discap']."',
				'".$suplente['etnia']."',
				'".$suplente['resguardo']."',
				'".$suplente['cod_pob_victima']."',
				'".$des_dept_nom."',
				'".$suplente['nom_mun_desp']."',
				'".$suplente['cod_sede']."',
				'".$suplente['cod_inst']."',
				'".$suplente['cod_mun_inst']."',
				'".$suplente['cod_mun_sede']."',
				'".$suplente['nom_sede']."',
				'".$suplente['nom_inst']."',
				'".$suplente['cod_grado']."',
				'".$suplente['nom_grupo']."',
				'".$suplente['cod_jorn_est']."',
				'".$suplente['estado_est']."',
				'".$suplente['repitente']."',
				'".$suplente['edad']."',
				'".$suplente['zona_res_est']."',
				'".$id_disp_est."',
				'".$suplente['TipoValidacion']."',
				'".$suplente['activo']."',
				'S',
				'".$tipo_complemento."',
				'".$tipo_complemento."' $campo_insertar_entregas);";
			$respuesta_insertar_suplente_entrega = $Link->query($consulta_insertar_suplente_entrega) or die('Error al insertar suplentes a entregas: '. $Link->error);
			// echo $consulta_insertar_suplente_entrega; exit();
			if ($respuesta_insertar_suplente_entrega === FALSE)
			{
				$respuesta_ajax = [
					'estado' => 0,
					'mensaje' => "No se pudo agregar el titular como suplente."
				];

				echo json_encode($respuesta_ajax);
				exit();
			}
		}

		$D1 = (! isset($suplente['D1'])) ? 0 : $suplente['D1'];
		$D2 = (! isset($suplente['D2'])) ? 0 : $suplente['D2'];
		$D3 = (! isset($suplente['D3'])) ? 0 : $suplente['D3'];
		$D4 = (! isset($suplente['D4'])) ? 0 : $suplente['D4'];
		$D5 = (! isset($suplente['D5'])) ? 0 : $suplente['D5'];

		$consulta_insertar_novedad = "INSERT INTO novedades_focalizacion (id_usuario, fecha_hora, cod_sede, tipo_doc_titular, num_doc_titular, tipo_complem, semana, d1, d2, d3, d4, d5, tiponovedad) VALUES ('".$usuario."', '".$fecha."', '".$sede."', '".$tipo_documento."', '".$numero_documento."', '".$tipo_complemento."', '".$semana."', '".$D1."', '".$D2."', '".$D3."', '".$D4."', '".$D5."', '5')";
		// echo $consulta_insertar_novedad; exit();
		$respuesta_insertar_novedad = $Link->query($consulta_insertar_novedad) or die("Error al insertar novedades_focalizacion: ". $Link->error);
		if ($respuesta_insertar_novedad === FALSE)
		{
			$respuesta_ajax = [
				'estado' => 0,
				'mensaje' => "No se pudo agregar la novedad de suplentes."
			];

			echo json_encode($respuesta_ajax);
			exit();
		}
	}

	$respuesta_ajax = [
		'estado' => 1,
		'mensaje' => "Novedades de suplentes agregados exitosamente."
	];

	echo json_encode($respuesta_ajax);
