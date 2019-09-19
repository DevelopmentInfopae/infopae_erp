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

	$consulta_focalizados = "SELECT * FROM novedades_focalizacion WHERE semana = '".$semana."' AND cod_sede = '".$sede."' AND tipo_complem = '".$tipo_complemento."';";
	$respuesta_focalizados = $Link->query($consulta_focalizados) or die('Error al consultar novedades_focalizacion: '. $Link->error);
	if($respuesta_focalizados->num_rows > 0)
	{
		while($registro_focalizacion = $respuesta_focalizados->fetch_assoc())
		{
			$bandera = 0;
			$numero_documento = $registro_focalizacion['num_doc_titular'];

			if($registro_focalizacion['d1'] == 1){
				if(!isset($_POST[$numero_documento.'_D1'])){
					$bandera++;
					$registro_focalizacion['d1'] = 0;
				}
			}else{
				if(isset($_POST[$numero_documento.'_D1'])){
					$bandera++;
					$registro_focalizacion['d1'] = 1;
				}
			}

			if($registro_focalizacion['d2'] == 1){
				if(!isset($_POST[$numero_documento.'_D2'])){
					$bandera++;
					$registro_focalizacion['d2'] = 0;
				}
			}else{
				if(isset($_POST[$numero_documento.'_D2'])){
					$bandera++;
					$registro_focalizacion['d2'] = 1;
				}
			}

			if($registro_focalizacion['d3'] == 1){
				if(!isset($_POST[$numero_documento.'_D3'])){
					$bandera++;
					$registro_focalizacion['d3'] = 0;
				}
			}else{
				if(isset($_POST[$numero_documento.'_D3'])){
					$bandera++;
					$registro_focalizacion['d3'] = 1;
				}
			}

			if($registro_focalizacion['d4'] == 1){
				if(!isset($_POST[$numero_documento.'_D4'])){
					$bandera++;
					$registro_focalizacion['d4'] = 0;
				}
			}else{
				if(isset($_POST[$numero_documento.'_D4'])){
					$bandera++;
					$registro_focalizacion['d4'] = 1;
				}
			}

			if($registro_focalizacion['d5'] == 1){
				if(!isset($_POST[$numero_documento.'_D5'])){
					$bandera++;
					$registro_focalizacion['d5'] = 0;
				}
			}else{
				if(isset($_POST[$numero_documento.'_D5'])){
					$bandera++;
					$registro_focalizacion['d5'] = 1;
				}
			}

			if ($bandera > 0)
			{
				$novedades[] = $registro_focalizacion;
			}
		}

		if (empty($novedades))
		{
			$respuesta_ajax = [
				'estado'=>0,
				'mensaje'=>'No se detectaron novedades a registrar. Por favor realice alguna novedad para continuar.'
			];
			echo json_encode($respuesta_ajax);
			exit();
		}

		$consulta_actualizar_focalizacion = "INSERT INTO novedades_focalizacion (id, d1, d2, d3, d4, d5) VALUES ";
		foreach ($novedades as $novedad)
		{
			$id = $novedad['id'];
			$D1 = $novedad['d1'];
			$D2 = $novedad['d2'];
			$D3 = $novedad['d3'];
			$D4 = $novedad['d4'];
			$D5 = $novedad['d5'];

			$consulta_actualizar_focalizacion .= "('".$id."', '".$D1."', '".$D2."', '".$D3."', '".$D4."', '".$D5."'), ";
		}

		$consulta_actualizar_focalizacion = trim($consulta_actualizar_focalizacion, ', ');
		$consulta_actualizar_focalizacion .= " ON DUPLICATE KEY UPDATE d1 =  VALUES(d1), d2 =  VALUES(d2), d3 =  VALUES(d3), d4 =  VALUES(d4), d5 =  VALUES(d5)";
		$respuesta_actualizar_focalizacion = $Link->query($consulta_actualizar_focalizacion) or die("Error al actualizar novedades_focalizacion: ". $Link->error);
	}
	else
	{
		$consulta_nueva_focalizacion = "INSERT INTO novedades_focalizacion(id_usuario, fecha_hora, cod_sede, tipo_doc_titular, num_doc_titular, tipo_complem, semana, d1, d2, d3, d4, d5, observaciones ) VALUES ";
		foreach ($numero_documentos as $clave => $numero_documento)
		{
			$abreviatura = $abreviatura_documentos[$clave];
			$D1 = isset($_POST[$numero_documento.'_D1']) ? $_POST[$numero_documento.'_D1'] : 0;
			$D2 = isset($_POST[$numero_documento.'_D2']) ? $_POST[$numero_documento.'_D2'] : 0;
			$D3 = isset($_POST[$numero_documento.'_D3']) ? $_POST[$numero_documento.'_D3'] : 0;
			$D4 = isset($_POST[$numero_documento.'_D4']) ? $_POST[$numero_documento.'_D4'] : 0;
			$D5 = isset($_POST[$numero_documento.'_D5']) ? $_POST[$numero_documento.'_D5'] : 0;
			$consulta_nueva_focalizacion .=	"('".$usuario."', '".$fecha."', '".$sede."', '".$abreviatura."', '".$numero_documento."', '".$tipo_complemento."', '".$semana."', '".$D1."', '".$D2."', '".$D3."', '".$D4."', '".$D5."', '".$observaciones."'), ";
		}

		$consulta_nueva_focalizacion = trim($consulta_nueva_focalizacion, ', ');
		$respuesta_nueva_focalizacion = $Link->query($consulta_nueva_focalizacion) or die('Error al insertar datos en novedades_focalizacion:'. $Link->error);
		if ($respuesta_nueva_focalizacion === FALSE)
		{
			$respuesta_ajax = [
				'estado'=>0,
				'mensaje'=>'No fue posible guardar las novedades de focalización.'
			];
			echo json_encode($respuesta_ajax);
			exit();
		}
	}

	///////////////////////////////////////////////////////////////////////////////////////////////////////////////

	// Consulta que retorna los dias de planillas el mes seleccionado.
	$consulta_dias_mes = "SELECT D1, D2, D3, D4, D5, D6, D7, D8, D9, D10, D11, D12, D13, D14, D15, D16, D17, D18, D19, D20, D21, D22, D23, D24, D25, D26, D27, D28, D29, D30, D31  FROM planilla_dias WHERE mes = '$mes';";
	$resultado_dias_mes = $Link->query($consulta_dias_mes) or die("Error al consultar planilla_dias: ". $Link->error);
	if ($resultado_dias_mes->num_rows > 0)
	{
		while ($registro_dias_mes = $resultado_dias_mes->fetch_assoc())
		{
			$dias_mes = $registro_dias_mes;
		}
	}

	// Consulta que retorna las semanas de mes seleccionado. Se utiliza para saber las columnas a actualizar para los días de entregas_res.
	$consulta_dias_semanas = "SELECT IF(LENGTH(DIA) > 1, DIA, CONCAT('0', DIA)) AS dia FROM planilla_semanas WHERE SEMANA = '$semana'";
	$resultado_dias_semanas = $Link->query($consulta_dias_semanas) or die("Error al consultar en planilla_semanas: ". $Link->error);
	if ($resultado_dias_semanas->num_rows > 0)
	{
		$cantidad_dias_semana = 1;
		$dias_entregas_res = [];
		$campos_actualizar = '';
		$campos_actualizar_duplicados = '';

		while ($registro_dias_semana = $resultado_dias_semanas->fetch_assoc())
		{
			$dias_semana = $registro_dias_semana['dia'];

			foreach ($dias_mes as $columna_dias_mes => $dia_mes)
			{
				if ($dia_mes == $dias_semana)
				{
					$dias_entregas_res[$cantidad_dias_semana] = $columna_dias_mes;
					$campos_actualizar .= $columna_dias_mes.', ';
					$campos_actualizar_duplicados .= $columna_dias_mes . ' = VALUES ('.$columna_dias_mes.'), ';
					$cantidad_dias_semana++;
				}
			}
		}
	}

	// Consulta que retorna los estudiantes registrados en entregas_res de acuerdo a los filtros ingresados.
	$titulares_entregas_res = [];
	$consulta_titulares_entregas = "SELECT * FROM entregas_res_$mes$periodo_actual WHERE cod_sede = '".$sede."' AND tipo_complem = '".$tipo_complemento."' AND tipo = 'F';";
	$respuesta_titulares_entregas_res = $Link->query($consulta_titulares_entregas) or die('Error al consultar entregas_res_$mes$periodo_actual: '. $Link->error);
	if ($respuesta_titulares_entregas_res->num_rows > 0)
	{
		while ($registros_titulares_entregas_res = $respuesta_titulares_entregas_res->fetch_assoc())
		{
			$titulares_entregas_res[] = $registros_titulares_entregas_res;
		}
	}

	// Consulta que retorna los estudiantes focalizados agregados o actualizados anteriormente. Se utiliza para construir la consultas de actualización en entregas_res.
	$consulta_novedades_focalizacion_actual = "SELECT num_doc_titular AS numero_documento, cod_sede, tipo_complem, d1, d2, d3, d4, d5 FROM novedades_focalizacion WHERE semana = '$semana' AND cod_sede = '$sede' AND tipo_complem = '$tipo_complemento'";
	$respuesta_novedades_focalizacion_actual = $Link->query($consulta_novedades_focalizacion_actual) or die('Error al consultar novedades_focalizacion: '. $Link->error);
	if ($respuesta_novedades_focalizacion_actual->num_rows > 0)
	{
		$campos_actualizar = trim($campos_actualizar, ', ');
		$consulta_actualizar_titulares = "INSERT INTO entregas_res_$mes$periodo_actual (id, ". $campos_actualizar . ") VALUES ";
		while($registro_novedades_focalizacion_actual = $respuesta_novedades_focalizacion_actual->fetch_assoc())
		{
			$novedades_focalizacion_actual = $registro_novedades_focalizacion_actual;

			foreach ($titulares_entregas_res as $titular)
			{
				if ($titular['num_doc'] == $novedades_focalizacion_actual['numero_documento'])
				{
					$consulta_actualizar_titulares .= "('". $titular['id'] ."', ";
					for ($i = 1; $i < $cantidad_dias_semana; $i++)
					{
						$consulta_actualizar_titulares .= "'". $novedades_focalizacion_actual['d'. $i] ."', ";
					}
					$consulta_actualizar_titulares = trim($consulta_actualizar_titulares, ', ') . "), ";
				}
			}
		}

		$campos_actualizar_duplicados = trim($campos_actualizar_duplicados, ', ');
		$consulta_actualizar_titulares = trim($consulta_actualizar_titulares, ', ');
		$consulta_actualizar_titulares .= " ON DUPLICATE KEY UPDATE ". $campos_actualizar_duplicados;
	}

	$respuesta_actualizar_titulares = $Link->query($consulta_actualizar_titulares) or die('Error al actualizar entregas_res_$mes$periodo_actual: '. $Link->error);
	if ($respuesta_actualizar_titulares === FALSE)
	{
		$respuesta_ajax = [
			'estado'=>0,
			'mensaje'=>'No fue posible actualizar las novedades diarias.'
		];
		echo json_encode($respuesta_ajax);
		exit();
	}

	///////////////////////////////////////////////////////////////////////////////
	/*// Consulta que retorna los estudiantes registrados en entregas_res de acuerdo a los filtros ingresados.
	$repitentes_entregas = [];
	$consulta_repitentes_entregas = "SELECT * FROM entregas_res_$mes$periodo_actual WHERE cod_sede = '".$sede."' AND tipo_complem = '".$tipo_complemento."' AND tipo = 'R';";
	$respuesta_repitentes_entregas = $Link->query($consulta_titulares_entregas) or die('Error al consultar entregas_res_$mes$periodo_actual: '. $Link->error);
	if ($respuesta_repitentes_entregas->num_rows > 0)
	{
		while ($registros_repitentes_entregas = $respuesta_repitentes_entregas->fetch_assoc())
		{
			$repitentes_entregas[] = $registros_repitentes_entregas;
		}
	}

	// Consulta que retorna los estudiantes focalizados agregados o actualizados anteriormente. Se utiliza para construir la consultas de actualización en entregas_res.
	$consulta_novedades_focalizacion_actual2 = "SELECT num_doc_titular AS numero_documento, cod_sede, tipo_complem, d1, d2, d3, d4, d5 FROM novedades_focalizacion WHERE semana = '$semana' AND cod_sede = '$sede' AND tipo_complem = '$tipo_complemento'";
	$respuesta_novedades_focalizacion_actual2 = $Link->query($consulta_novedades_focalizacion_actual2) or die('Error al consultar novedades_focalizacion: '. $Link->error);
	if ($respuesta_novedades_focalizacion_actual2->num_rows > 0)
	{
		$campos_actualizar = trim($campos_actualizar, ', ');
		$consulta_actualizar_repitentes = "INSERT INTO entregas_res_$mes$periodo_actual (id, ". $campos_actualizar . ") VALUES ";
		while($registro_novedades_focalizacion_actual2 = $respuesta_novedades_focalizacion_actual2->fetch_assoc())
		{
			$novedades_focalizacion_actual2 = $registro_novedades_focalizacion_actual2;

			foreach ($repitentes_entregas as $repitente)
			{
				if ($repitente['num_doc'] == $novedades_focalizacion_actual2['numero_documento'])
				{
					$consulta_actualizar_repitentes .= "('". $repitente['id'] ."', ";
					for ($i = 1; $i < $cantidad_dias_semana; $i++)
					{
						$consulta_actualizar_repitentes .= "'". $novedades_focalizacion_actual2['d'. $i] ."', ";
					}
					$consulta_actualizar_repitentes = trim($consulta_actualizar_repitentes, ', ') . "), ";
				}
			}
		}

		$campos_actualizar_duplicados = trim($campos_actualizar_duplicados, ', ');
		$consulta_actualizar_repitentes = trim($consulta_actualizar_repitentes, ', ');
		$consulta_actualizar_repitentes .= " ON DUPLICATE KEY UPDATE ". $campos_actualizar_duplicados;
	}

	$respuesta_actualizar_repitentes = $Link->query($consulta_actualizar_repitentes) or die('Error al actualizar repitentes en entregas_res_$mes$periodo_actual: '. $Link->error);*/
	///////////////////////////////////////////////////////////////////////////////

	$respuesta_ajax = [
		'estado'=>1,
		'mensaje'=>'Novedades agregadas exitosamente.'
	];
	echo json_encode($respuesta_ajax);
