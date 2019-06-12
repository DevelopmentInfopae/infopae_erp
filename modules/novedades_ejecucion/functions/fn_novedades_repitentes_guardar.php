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
	$codigo_repitentes = [];

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
	$columnas_dias_entregas = $columnas_suma_dias = $insertar_columnas_dias = $actualizar_columnas_dias = $actualizar_columnas_dias_novedad = "";
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
					// $columnas_dias_entregas .= "e.". $clavePlanillasDias ." AS D". $indiceDia .", ";
					$columnas_dias_entregas .= "IF((SELECT ".$clavePlanillasDias." FROM entregas_res_$mes$periodo_actual es WHERE es.cod_sede = '$sede' AND es.tipo_complem = '$tipo_complemento' AND es.tipo = 'R' AND es.num_doc = f.num_doc) = 1, 1, 0) AS D".$indiceDia.", ";



					$columnas_suma_dias .= "e.". $clavePlanillasDias . " + ";
					// $columnasDiasEntregas_res .= "IFNULL(e.". $clavePlanillasDias .", 0) AS D". $indiceDia .", ";
					// $columnasDiasSuplentes .= " 0 AS D". $indiceDia .", ";
					$insertar_columnas_dias .= $clavePlanillasDias .", ";
					$actualizar_columnas_dias .= $clavePlanillasDias . " = VALUES (".$clavePlanillasDias."), ";
					$actualizar_columnas_dias_novedad .= "d". $indiceDia ." = VALUES (d". $indiceDia ."), ";
					$indiceDia++;
				}
			}
		}
	}

	// Datos del estudiante
	$columnas_suma_dias = trim($columnas_suma_dias, "+ ");
	$columnas_dias_entregas = trim($columnas_dias_entregas, ", ");
	// $columnasDiasSuma = trim($columnasDiasSuma, " + ");
	// $columnasDiasSuplentes = trim($columnasDiasSuplentes, ", ");
	$insertar_columnas_dias = trim($insertar_columnas_dias, ", ");
	// $columnasDiasEntregas_res = trim($columnasDiasEntregas_res, ", ");
	$actualizar_columnas_dias = trim($actualizar_columnas_dias, ", ");
	$actualizar_columnas_dias_novedad = trim($actualizar_columnas_dias_novedad, ", ");
	// var_dump($_POST['numero_documentos']); exit();

  $consulta_repitentes = "SELECT
	 											    f.*,
	 											    CONCAT(f.nom1, ' ', f.nom2, ' ', f.ape1, ' ', f.ape2) AS nombre,
	 											    e.tipo_doc_nom,
	 											    s.cod_mun_sede,
												    s.cod_sede,
  													s.cod_inst,
												    s.nom_sede,
												    s.nom_inst,
	 													$columnas_dias_entregas,
	 													($columnas_suma_dias) AS suma_dias
	 												FROM
	 											    focalizacion$semana f
	 												INNER JOIN
	 													entregas_res_$mes$periodo_actual e ON e.num_doc = f.num_doc AND e.tipo_complem = f.Tipo_complemento
	 												INNER JOIN
	 													sedes$periodo_actual s ON s.cod_sede = e.cod_sede
	 												WHERE
	 											    f.cod_sede = '$sede'
	 								        AND f.Tipo_complemento = '$tipo_complemento'
	 								        AND e.tipo='F'";

	// $consulta_repitentes = "SELECT
 //  												 	*
 //  													-- MAX(D1) AS maximo_D1,
	// 											   --  MAX(D2) AS maximo_D2,
	// 											   --  MAX(D3) AS maximo_D3,
	// 											   --  MAX(D4) AS maximo_D4,
	// 											   --  MAX(D5) AS maximo_D5,
	// 											   --  SUM(suma_dias) AS suma_total_dias
 //  												FROM
	// 												(
 //  													(SELECT
	// 												    f.*,
	// 												    CONCAT(f.nom1, ' ', f.nom2, ' ', f.ape1, ' ', f.ape2) AS nombre,
	// 												    e.tipo_doc_nom,
													    // s.cod_mun_sede,
													    // s.cod_sede AS codigo_sede,
    													// s.cod_inst AS codigo_inst,
													    // s.nom_sede,
													    // s.nom_inst,
	// 														$columnas_dias_entregas,
	// 														($columnas_suma_dias) AS suma_dias
	// 													FROM
	// 												    focalizacion$semana f
	// 													INNER JOIN
	// 														entregas_res_$mes$periodo_actual e ON e.num_doc = f.num_doc AND e.tipo_complem = f.Tipo_complemento
	// 													INNER JOIN
	// 														sedes$periodo_actual s ON s.cod_sede = e.cod_sede
	// 													WHERE
	// 												    f.cod_sede = '$sede'
	// 									        AND f.Tipo_complemento = '$tipo_complemento'
	// 									        AND e.tipo='R')

	// 									        UNION ALL

	// 													(SELECT
	// 												    f.*,
	// 												    CONCAT(f.nom1, ' ', f.nom2, ' ', f.ape1, ' ', f.ape2) AS nombre,
	// 												    e.tipo_doc_nom,
	// 												    s.cod_mun_sede,
	// 												    s.cod_sede,
	// 												    s.cod_inst,
	// 												    s.nom_sede,
	// 												    s.nom_inst,
	// 														$columnas_dias_entregas,
	// 														($columnas_suma_dias) AS suma_dias
	// 													FROM
	// 												    focalizacion$semana f
	// 													INNER JOIN
	// 														entregas_res_$mes$periodo_actual e ON e.num_doc = f.num_doc AND e.tipo_complem = f.Tipo_complemento
	// 													INNER JOIN
	// 														sedes$periodo_actual s ON s.cod_sede = e.cod_sede
	// 													WHERE
	// 												    f.cod_sede = '$sede'
	// 									        AND f.Tipo_complemento = '$tipo_complemento'
	// 									        AND e.tipo='F')
	// 							        	) AS TG
	// 												GROUP BY TG.num_doc
	// 												ORDER BY TG.nombre";
// echo $consulta_repitentes; exit();
	$respuesta_repitentes = $Link->query($consulta_repitentes) or die('Error al consultar suplentes: '. $Link->error);
	if ($respuesta_repitentes->num_rows > 0)
	{
		while($registro_repitentes = $respuesta_repitentes->fetch_assoc())
		{
			$cambio_detectado = 0;
			$numero_documento = $registro_repitentes['num_doc'];

			if($registro_repitentes['D1'] == '1'){
				if(!isset($_POST[$numero_documento.'_D1'])){
					$cambio_detectado++;
					$registro_repitentes['D1'] = '0';
				}
			}else{
				if(isset($_POST[$numero_documento.'_D1'])){
					$cambio_detectado++;
					$registro_repitentes['D1'] = '1';
				}
			}

			if($registro_repitentes['D2'] == '1'){
				if(!isset($_POST[$numero_documento.'_D2'])){
					$cambio_detectado++;
					$registro_repitentes['D2'] = '0';
				}
			}else{
				if(isset($_POST[$numero_documento.'_D2'])){
					$cambio_detectado++;
					$registro_repitentes['D2'] = '1';
				}
			}

			if($registro_repitentes['D3'] == '1'){
				if(!isset($_POST[$numero_documento.'_D3'])){
					$cambio_detectado++;
					$registro_repitentes['D3'] = '0';
				}
			}else{
				if(isset($_POST[$numero_documento.'_D3'])){
					$cambio_detectado++;
					$registro_repitentes['D3'] = '1';
				}
			}

			if($registro_repitentes['D4'] == '1'){
				if(!isset($_POST[$numero_documento.'_D4'])){
					$cambio_detectado++;
					$registro_repitentes['D4'] = '0';
				}
			}else{
				if(isset($_POST[$numero_documento.'_D4'])){
					$cambio_detectado++;
					$registro_repitentes['D4'] = '1';
				}
			}

			if($registro_repitentes['D5'] == '1'){
				if(!isset($_POST[$numero_documento.'_D5'])){
					$cambio_detectado++;
					$registro_repitentes['D5'] = '0';
				}
			}else{
				if(isset($_POST[$numero_documento.'_D5'])){
					$cambio_detectado++;
					$registro_repitentes['D5'] = '1';
				}
			}

			if ($cambio_detectado > 0)
			{
				$novedades_repitentes[] = $registro_repitentes;
			}
		}
	}


	if (empty($novedades_repitentes))
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

	// Consulta que retorna todos los repitentes registrados en entregas.
	$repitentes_entregas = [];
	$documentos_repitentes = [];
	$consulta_repitente_entrega = "SELECT * FROM entregas_res_$mes$periodo_actual WHERE cod_sede='$sede' AND tipo_complem='$tipo_complemento' AND tipo='R';";
	$respuesta_repitente_entrega = $Link->query($consulta_repitente_entrega) or die('Error al consultar entregas_res_$mes$periodo_actual: '. $Link->error);
	if ($respuesta_repitente_entrega->num_rows > 0)
	{
		while($registro_repitentes_entrega = $respuesta_repitente_entrega->fetch_assoc())
		{
			$repitentes_entregas[$registro_repitentes_entrega['num_doc']] = $registro_repitentes_entrega;
			$documentos_repitentes[] = $registro_repitentes_entrega['num_doc'];
		}
	}

	// Iteración para validar si se requiere insertar o actualizar un repitente.
	$insertar = $actualizar = 0;
	$consulta_actualizar_repitente_entrega = "INSERT INTO entregas_res_$mes$periodo_actual (id, $insertar_columnas_dias) VALUES ";
	$consulta_insertar_repitentes_entrega = "INSERT INTO entregas_res_$mes$periodo_actual (tipo_doc, num_doc, tipo_doc_nom, ape1, ape2, nom1, nom2, genero, dir_res, cod_mun_res, telefono, cod_mun_nac, fecha_nac, cod_estrato, sisben, cod_discap, etnia, resguardo, cod_pob_victima, nom_mun_desp, cod_sede, cod_inst, cod_mun_inst, cod_mun_sede, nom_sede, nom_inst, cod_grado, nom_grupo, cod_jorn_est, estado_est, repitente, edad, zona_res_est, activo, tipo, $semana_tipo_complemento, tipo_complem, $insertar_columnas_dias)VALUES ";
	foreach ($novedades_repitentes as $novedad_repitente)
	{
		if(in_array($novedad_repitente["num_doc"], $documentos_repitentes))
		{
			$D1 = $novedad_repitente['D1'];
			$D2 = $novedad_repitente['D2'];
			$D3 = $novedad_repitente['D3'];
			$D4 = $novedad_repitente['D4'];
			$D5 = $novedad_repitente['D5'];

			$consulta_actualizar_repitente_entrega .= "(". $repitentes_entregas[$novedad_repitente["num_doc"]]['id'] .", $D1, $D2, $D3, $D4, $D5), ";
			$actualizar ++;
		}
		else
		{
			$D1 = $novedad_repitente['D1'];
			$D2 = $novedad_repitente['D2'];
			$D3 = $novedad_repitente['D3'];
			$D4 = $novedad_repitente['D4'];
			$D5 = $novedad_repitente['D5'];

			$consulta_insertar_repitentes_entrega .= "
			(
				'".$novedad_repitente['tipo_doc']."',
				'".$novedad_repitente['num_doc']."',
				'".$novedad_repitente['tipo_doc_nom']."',
				'".$novedad_repitente['ape1']."',
				'".$novedad_repitente['ape2']."',
				'".$novedad_repitente['nom1']."',
				'".$novedad_repitente['nom2']."',
				'".$novedad_repitente['genero']."',
				'".$novedad_repitente['dir_res']."',
				'".$novedad_repitente['cod_mun_res']."',
				'".$novedad_repitente['telefono']."',
				'".$novedad_repitente['cod_mun_nac']."',
				'".$novedad_repitente['fecha_nac']."',
				'".$novedad_repitente['cod_estrato']."',
				'".$novedad_repitente['sisben']."',
				'".$novedad_repitente['cod_discap']."',
				'".$novedad_repitente['etnia']."',
				'".$novedad_repitente['resguardo']."',
				'".$novedad_repitente['cod_pob_victima']."',
				'".$novedad_repitente['nom_mun_desp']."',
				'".$novedad_repitente['cod_sede']."',
				'".$novedad_repitente['cod_inst']."',
				'".$novedad_repitente['cod_mun_sede']."',
				'".$novedad_repitente['cod_mun_sede']."',
				'".$novedad_repitente['nom_sede']."',
				'".$novedad_repitente['nom_inst']."',
				'".$novedad_repitente['cod_grado']."',
				'".$novedad_repitente['nom_grupo']."',
				'".$novedad_repitente['cod_jorn_est']."',
				'".$novedad_repitente['estado_est']."',
				'".$novedad_repitente['repitente']."',
				'".$novedad_repitente['edad']."',
				'".$novedad_repitente['zona_res_est']."',
				'".$novedad_repitente['activo']."',
				'R',
				'".$tipo_complemento."',
				'".$tipo_complemento."',
				$D1, $D2, $D3, $D4, $D5), ";

			$insertar++;
		}
	}

	if (! empty($actualizar))
	{
		$consulta_actualizar_repitente_entrega = trim($consulta_actualizar_repitente_entrega, ", ");
		$consulta_actualizar_repitente_entrega .= " ON DUPLICATE KEY UPDATE $actualizar_columnas_dias";
		// echo $consulta_actualizar_repitente_entrega; exit();
		$respuesta_actualizar_repitente_entrega = $Link->query($consulta_actualizar_repitente_entrega) or die('Error al actualizar repitente en entregas: '. $Link->error. ' '. $consulta_actualizar_repitente_entrega);
		if ($respuesta_actualizar_repitente_entrega === FALSE)
		{
			$respuesta_ajax = [
					'estado' => 0,
					'mensaje' => "No fue posible actualizar el repitente en entregas"
				];

				echo json_encode($respuesta_ajax);
				exit();
		}
	}

	if (! empty($insertar))
	{
	  $consulta_insertar_repitentes_entrega = trim($consulta_insertar_repitentes_entrega, ", ");
	  // echo $consulta_insertar_repitentes_entrega; exit();
		$respuesta_insertar_repitentes_entrega = $Link->query($consulta_insertar_repitentes_entrega) or die('Error al insertar repitentes a entregas: '. $Link->error);
		if ($respuesta_insertar_repitentes_entrega === FALSE)
		{
			$respuesta_ajax = [
				'estado' => 0,
				'mensaje' => "No fue posible guardar los repitentes en entregas."
			];

			echo json_encode($respuesta_ajax);
			exit();
		}
	}


	$actualizar_novedad = $insertar_novedad = 0;
	$documentos_novedades_focalizacion = $novedades_focalizacion = [];
	$consulta_novedades_focalizacion = "SELECT * FROM novedades_focalizacion WHERE cod_sede='$sede' AND tipo_complem='$tipo_complemento' AND semana = '$semana';";
	$respuesta_novedades_focalizacion = $Link->query($consulta_novedades_focalizacion) or die('Error al consultar novedades_focalizacion. '. $Link->error);
	if($respuesta_novedades_focalizacion->num_rows > 0)
	{
		while($registros_novedades_focalizacion = $respuesta_novedades_focalizacion->fetch_assoc())
		{
			$novedades_focalizacion[$registros_novedades_focalizacion['num_doc_titular']] = $registros_novedades_focalizacion;
			$documentos_novedades_focalizacion[] = $registros_novedades_focalizacion['num_doc_titular'];
		}
	}

	$consulta_actualizar_novedad = "INSERT INTO novedades_focalizacion (id, d1, d2, d3, d4, d5) VALUES ";
	$consulta_insertar_novedad = "INSERT INTO novedades_focalizacion (id_usuario, fecha_hora, cod_sede, tipo_doc_titular, num_doc_titular, tipo_complem, semana, d1, d2, d3, d4, d5, tiponovedad) VALUES ";
	foreach ($novedades_repitentes as $novedad_repitente)
	{
		if (in_array($novedad_repitente['num_doc'], $documentos_novedades_focalizacion))
		{
			$id = $novedades_focalizacion[$novedad_repitente['num_doc']]['id'];
			$D1 = $novedad_repitente['D1'];
			$D2 = $novedad_repitente['D2'];
			$D3 = $novedad_repitente['D3'];
			$D4 = $novedad_repitente['D4'];
			$D5 = $novedad_repitente['D5'];

			$consulta_actualizar_novedad .= "('". $id ."', $D1, $D2, $D3, $D4, $D5), ";
			$actualizar_novedad ++;
		}
		else
		{
			$D1 = $novedad_repitente['D1'];
			$D2 = $novedad_repitente['D2'];
			$D3 = $novedad_repitente['D3'];
			$D4 = $novedad_repitente['D4'];
			$D5 = $novedad_repitente['D5'];

			$consulta_insertar_novedad .= "('".$usuario."', '".$fecha."', '".$sede."', '".$novedad_repitente['tipo_doc']."', '".$novedad_repitente['num_doc']."', '".$tipo_complemento."', '".$semana."', '".$D1."', '".$D2."', '".$D3."', '".$D4."', '".$D5."', '6'), ";
			$insertar_novedad ++;
		}
	}

	if (! empty($actualizar_novedad))
	{
		$consulta_actualizar_novedad = trim($consulta_actualizar_novedad, ", ");
		$consulta_actualizar_novedad .= " ON DUPLICATE KEY UPDATE $actualizar_columnas_dias_novedad";
		// echo $consulta_actualizar_novedad; exit();
		$respuesta_insertar_novedad = $Link->query($consulta_actualizar_novedad) or die("Error al actualizar novedades_focalizacion: ". $Link->error);
		if ($respuesta_insertar_novedad === FALSE)
		{
			$respuesta_ajax = [
				'estado' => 0,
				'mensaje' => "No fue posible actualizar la novedades de repitentes."
			];

			echo json_encode($respuesta_ajax);
			exit();
		}
	}

	if(! empty($insertar_novedad))
	{
		$consulta_insertar_novedad = trim($consulta_insertar_novedad, ", ");
		// echo $consulta_insertar_novedad; exit();
		$respuesta_insertar_novedad = $Link->query($consulta_insertar_novedad) or die("Error al insertar novedades_focalizacion: ". $Link->error);
		if ($respuesta_insertar_novedad === FALSE)
		{
			$respuesta_ajax = [
				'estado' => 0,
				'mensaje' => "No fue posible guardar las novedades de suplentes."
			];

			echo json_encode($respuesta_ajax);
			exit();
		}
	}

	$respuesta_ajax = [
		'estado' => 1,
		'mensaje' => "Novedades de repitentes agregados exitosamente."
	];

	echo json_encode($respuesta_ajax);
