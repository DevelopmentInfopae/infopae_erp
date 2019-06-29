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


	$respuesta_ajax = [
		'estado'=>1,
		'mensaje'=>'Novedades agregadas exitosamente.'
	];
	echo json_encode($respuesta_ajax);








// }

// 	// Consulta para eliminar el registro del estudiante en novedades de focalización.
// 	$consulta_eliminar_focalizacion = "DELETE FROM novedades_focalizacion WHERE num_doc_titular = '".$numero_documento."' AND cod_sede='".$sede."' AND tipo_complem='".$tipo_complemento."'; ";
// 	$respuesta_eliminar_focalizacion = $Link->query($consulta_eliminar_focalizacion) or die('Error al eliminar la focalizacion: '. $Link->error);

// 	$abreviatura = $abreviatura_documentos[$clave];
// 	$D1 = isset($_POST[$numero_documento.'_D1']) ? $_POST[$numero_documento.'_D1'] : 0;
// 	$D2 = isset($_POST[$numero_documento.'_D2']) ? $_POST[$numero_documento.'_D2'] : 0;
// 	$D3 = isset($_POST[$numero_documento.'_D3']) ? $_POST[$numero_documento.'_D3'] : 0;
// 	$D4 = isset($_POST[$numero_documento.'_D4']) ? $_POST[$numero_documento.'_D4'] : 0;
// 	$D5 = isset($_POST[$numero_documento.'_D5']) ? $_POST[$numero_documento.'_D5'] : 0;
// 	$consulta_nueva_focalizacion .=	"('".$usuario."', '".$fecha."', '".$sede."', '".$abreviatura."', '".$numero_documento."', '".$tipo_complemento."', '".$semana."', '".$D1."', '".$D2."', '".$D3."', '".$D4."', '".$D5."', '".$observaciones."'), ";

// $consulta_nueva_focalizacion = trim($consulta_nueva_focalizacion, ', ');
// $respuesta_nueva_focalizacion = $Link->query($consulta_nueva_focalizacion) or die('Error al insertar datos en novedades_focalizacion:'. $Link->error);
// if ($respuesta_nueva_focalizacion === FALSE)
// {
// 	echo 'No fue posible agregar las novedades de focalización';
// 	exit();
// }


// Consulta que retorna los dias de planillas el mes seleccionado.
// $consultaPlanillaDias = "SELECT D1, D2, D3, D4, D5, D6, D7, D8, D9, D10, D11, D12, D13, D14, D15, D16, D17, D18, D19, D20, D21, D22, D23, D24, D25, D26, D27, D28, D29, D30, D31  FROM planilla_dias WHERE mes = '$mes';";
// $resultadoPlanillaDias = $Link->query($consultaPlanillaDias) or die("Error al consultar planilla_dias: ". $Link->error);
// if ($resultadoPlanillaDias->num_rows > 0) {
// 	while ($registroPlanillasDias = $resultadoPlanillaDias->fetch_assoc()) {
// 		$planilla_dias = $registroPlanillasDias;
// 	}
// }

// /************************** REGISTRO DE NOVEDADES **************************/
// /******************************* FOCALIZADOS *******************************/
// 		// Consulta que retorna los datos de los dias de la semana seleccionada.
// 		$indiceSemana = 0; // Variable que almacena la cantidad de dias de la semana seleccionada.
// 		$columnasDiasEntregas_res = "";
// 		$consultaSemana = "SELECT * FROM planilla_semanas WHERE semana = '$semana'";
// 		$resultadoSemana = $Link->query($consultaSemana) or die("Error al consultar planilla_semanas: ". $Link->error);
// 		if($resultadoSemana->num_rows > 0){
// 			while($registroSemana = $resultadoSemana->fetch_assoc()) {
// 				if($registroSemana['NOMDIAS'] == 'lunes'){
// 					$diaPlanilla['indiceDias'] = 1;
// 				} else if($registroSemana['NOMDIAS'] == 'martes'){
// 					$diaPlanilla['indiceDias'] = 2;
// 				} else if($registroSemana['NOMDIAS'] == 'miércoles'){
// 					$diaPlanilla['indiceDias'] = 3;
// 				} else if($registroSemana['NOMDIAS'] == 'jueves'){
// 					$diaPlanilla['indiceDias'] = 4;
// 				} else if($registroSemana['NOMDIAS'] == 'viernes'){
// 					$diaPlanilla['indiceDias'] = 5;
// 				}

// 				// Ciclo que obtiene las columnas de los dias de planillas dias.
// 				foreach ($planilla_dias as $clavePlanillaDias => $valorPlanillaDias) {
// 					if ($registroSemana['DIA'] == $valorPlanillaDias)	{
// 						$diaPlanilla["columna"] = $clavePlanillaDias;
// 						$columnasDiasEntregas_res .= "IFNULL(e.". $clavePlanillaDias .", 0) AS D". $diaPlanilla['indiceDias'] .", ";
// 						$indiceSemana++;
// 					}
// 				}

// 				$diaPlanilla['diaPlanilla'] = $registroSemana['DIA'];
// 				$diasPlanilla[] = $diaPlanilla;
// 			}

// 			if ($indiceSemana < 5) {
// 				for ($i = $indiceSemana; $i < 5; $i++) {
// 					$columnasDiasEntregas_res .=  "0 AS D". ($i+1) .", ";
// 				}
// 			}
// 		}

		// Consulta que obtiene los datos del estudiantes focalizados.
		// $consultaNovedad = "SELECT f.tipo_doc, td.Abreviatura, f.num_doc, CONCAT(f.nom1,' ',f.nom2,' ',f.ape1,' ',f.ape2) AS nombre, '$tipo_complemento' AS complemento, ". trim($columnasDiasEntregas_res, ", ") ."
		// FROM focalizacion$semana f
		// INNER JOIN entregas_res_$mes". $_SESSION["periodo_actual"] ." e ON e.num_doc = f.num_doc AND e.cod_sede = f.cod_sede
		// LEFT JOIN tipodocumento td ON f.tipo_doc = td.id WHERE f.cod_sede = $sede AND f.Tipo_complemento = '$tipo_complemento' AND f.activo = 1";

// 		$resultadoNovedades = $Link->query($consultaNovedad) or die("Error al consultar focalizacion$semana. Linea 72: ". $Link->error);
// 		if($resultadoNovedades->num_rows > 0){
// 			while($titular = $resultadoNovedades->fetch_assoc()) {
// 				// $focalizados[] = $titular;

// 				// Condiciones para buscar las diferencias entre los días almacenados y los días cambiados en la interfaz.
// 				$documento = $titular['num_doc'];
// 				$bandera = 0;
// 				if($titular['D1'] == 1){
// 					if(!isset($_POST[$documento.'_D1'])){
// 						$bandera++;
// 						$titular['D1'] = 0;
// 					}
// 				}else{
// 					if(isset($_POST[$documento.'_D1'])){
// 						$bandera++;
// 						$titular['D1'] = 1;
// 					}
// 				}

// 				if($titular['D2'] == 1){
// 					if(!isset($_POST[$documento.'_D2'])){
// 						$bandera++;
// 						$titular['D2'] = 0;
// 					}
// 				}else{
// 					if(isset($_POST[$documento.'_D2'])){
// 						$bandera++;
// 						$titular['D2'] = 1;
// 					}
// 				}

// 				if($titular['D3'] == 1){
// 					if(!isset($_POST[$documento.'_D3'])){
// 						$bandera++;
// 						$titular['D3'] = 0;
// 					}
// 				}else{
// 					if(isset($_POST[$documento.'_D3'])){
// 						$bandera++;
// 						$titular['D3'] = 1;
// 					}
// 				}

// 				if($titular['D4'] == 1){
// 					if(!isset($_POST[$documento.'_D4'])){
// 						$bandera++;
// 						$titular['D4'] = 0;
// 					}
// 				}else{
// 					if(isset($_POST[$documento.'_D4'])){
// 						$bandera++;
// 						$titular['D4'] = 1;
// 					}
// 				}

// 				if($titular['D5'] == 1){
// 					if(!isset($_POST[$documento.'_D5'])){
// 						$bandera++;
// 						$titular['D5'] = 0;
// 					}
// 				}else{
// 					if(isset($_POST[$documento.'_D5'])){
// 						$bandera++;
// 						$titular['D5'] = 1;
// 					}
// 				}

// 				// Si existe algún cambio de dia, se almacena los datos del titular en la variables $novedades.
// 				if($bandera != 0){
// 					$novedades[] = $titular;
// 				}
// 			}
// 		}

// 		/****************************** NO FOCALIZADOS ******************************/
// 		// Consulta que obtiene los datos del estudiantes suplentes.
// 		$consultaSuplentes = "SELECT s.tipo_doc AS tipo_documento, s.tipo_doc_nom AS abreviatura, s.num_doc AS numero_documento, CONCAT(s.nom1, ' ', s.nom2,' ', s.ape1,' ', s.ape2) AS nombre_suplente, '$tipo_complemento' AS tipo_complemento, ". trim($columnasDiasEntregas_res, ", ")." FROM suplentes s LEFT JOIN entregas_res_$mes". $_SESSION["periodo_actual"] ." e ON e.num_doc = s.num_doc AND e.cod_sede = s.cod_sede AND e.tipo_complem = '$tipo_complemento' WHERE s.cod_sede = '$sede'  AND s.activo = 1";
// 		$resultadoSuplentes = $Link->query($consultaSuplentes) or die("Error al consultar suplentes. Linea 150: ". $Link->error);
// 		if($resultadoSuplentes->num_rows > 0){
// 			while($titularSuplente = $resultadoSuplentes->fetch_assoc()) {
// 				$bandera = 0;
// 				$documento = $titularSuplente['numero_documento'];

// 				// Condiciones para buscar las diferencias entre los días almacenados y los días cambiados en la interfaz.
// 				if($titularSuplente['D1'] == 1){
// 					if(!isset($_POST[$documento.'_D1'])){
// 						$bandera++;
// 						$titularSuplente['D1'] = 0;
// 					}
// 				}else{
// 					if(isset($_POST[$documento.'_D1'])){
// 						$bandera++;
// 						$titularSuplente['D1'] = 1;
// 					}
// 				}

// 				if($titularSuplente['D2'] == 1){
// 					if(!isset($_POST[$documento.'_D2'])){
// 						$bandera++;
// 						$titularSuplente['D2'] = 0;
// 					}
// 				}else{
// 					if(isset($_POST[$documento.'_D2'])){
// 						$bandera++;
// 						$titularSuplente['D2'] = 1;
// 					}
// 				}

// 				if($titularSuplente['D3'] == 1){
// 					if(!isset($_POST[$documento.'_D3'])){
// 						$bandera++;
// 						$titularSuplente['D3'] = 0;
// 					}
// 				}else{
// 					if(isset($_POST[$documento.'_D3'])){
// 						$bandera++;
// 						$titularSuplente['D3'] = 1;
// 					}
// 				}

// 				if($titularSuplente['D4'] == 1){
// 					if(!isset($_POST[$documento.'_D4'])){
// 						$bandera++;
// 						$titularSuplente['D4'] = 0;
// 					}
// 				}else{
// 					if(isset($_POST[$documento.'_D4'])){
// 						$bandera++;
// 						$titularSuplente['D4'] = 1;
// 					}
// 				}

// 				if($titularSuplente['D5'] == 1){
// 					if(!isset($_POST[$documento.'_D5'])){
// 						$bandera++;
// 						$titularSuplente['D5'] = 0;
// 					}
// 				}else{
// 					if(isset($_POST[$documento.'_D5'])){
// 						$bandera++;
// 						$titularSuplente['D5'] = 1;
// 					}
// 				}

// 				// Si existe algún cambio de dia, se almacena los datos del titular en la variables $novedadesSuplentes.
// 				if($bandera != 0){
// 					$novedadesSuplentes[] = $titularSuplente;
// 				}
// 			}
// 		}

// 		// Condición que verifica si se ha modificado los datos de los titulares focalizados y suplentes.
// 		if (!empty($novedades) || !empty($novedadesSuplentes)) {
// 			$aux = 0;
// 			$consulta = "INSERT INTO novedades_focalizacion(id_usuario, fecha_hora, cod_sede, tipo_doc_titular, num_doc_titular, tipo_complem, semana, d1, d2, d3, d4, d5, observaciones ) VALUES ";
// 			foreach ($novedades as $novedad) {
// 				$tipoDoc = $novedad['tipo_doc'];
// 				$numDoc = $novedad['num_doc'];
// 				$d1 = $novedad['D1'];
// 				$d2 = $novedad['D2'];
// 				$d3 = $novedad['D3'];
// 				$d4 = $novedad['D4'];
// 				$d5 = $novedad['D5'];
// 				$consulta .= "($usuario, '$fecha', '$sede', '$tipoDoc', '$numDoc' , '$tipo_complemento', '$semana', '$d1', '$d2', '$d3', '$d4', '$d5', '$observaciones'), ";
// 			}

// 			foreach ($novedadesSuplentes as $novedad) {
// 				$tipoDoc = $novedad['tipo_documento'];
// 				$numDoc = $novedad['numero_documento'];
// 				$d1 = $novedad['D1'];
// 				$d2 = $novedad['D2'];
// 				$d3 = $novedad['D3'];
// 				$d4 = $novedad['D4'];
// 				$d5 = $novedad['D5'];
// 				$consulta .= "($usuario, '$fecha', '$sede', '$tipoDoc', '$numDoc' , '$tipo_complemento', '$semana', '$d1', '$d2', '$d3', '$d4', '$d5', '$observaciones'), ";
// 			}

// 			$Link->query(trim($consulta, ", ")) or die("Error al insertar novedades_focalizacion. Linea 250: ". $Link->error);
// 		} else {
// 			$respuestaAJAX = [
// 				"estado" => 0,
// 				"mensaje" => "Debe modificar los datos de un titular para realizar el guardado de datos."
// 			];

// 			exit(json_encode($respuestaAJAX));
// 		}
// 		/***************************************************************************/

// 		/************************** REGISTRO DE ENTREGAS ***************************/
// 		// Consulta que retorna las semanas de mes seleccionado. Se utiliza para saber la posición de la semana del mes.
// 		$consultaSemanasMes = "SELECT DISTINCT SEMANA AS semana FROM planilla_semanas WHERE MES = '$mes'";
// 		$resultadoSemanasMes = $Link->query($consultaSemanasMes) or die("Error al consultar en planilla_semanas: ". $Link->error);
// 		if ($resultadoSemanasMes->num_rows > 0) {
// 			$posicionSemanaMes = 1;
// 			while ($registroSemanaMes = $resultadoSemanasMes->fetch_assoc()) {
// 				if ($registroSemanaMes["semana"] == $semana) {
// 					$semanaMes = $posicionSemanaMes;
// 					break;
// 				}
// 				$posicionSemanaMes++;
// 			}
// 		}

// 		$consultaActualizarEntregas = "";
// 		// Validando existencia en entregas_res
// 		foreach ($novedadesSuplentes as $novedad){
// 			$columnaDias = $valoresDias = $consultaInsertarEntregas = "";
// 			$numero_documento = $novedad['numero_documento'];

// 			// Consulta que retorna los datos del estudiante en la tabla entregas_res de acuerdo al número de documento.
// 			$consultaEntregasRes = "SELECT * FROM entregas_res_$mes$periodo_actual WHERE num_doc = '$numero_documento'";
// 			$resultado = $Link->query($consultaEntregasRes) or die("Error al consultar entregas_res_$mes$periodo_actual: ". $Link->error);

// 			// Si existe en registro en entregas_res[MES][AÑO], se actualiza, de lo contrario se inserta un nuevo registro.
// 			if($resultado->num_rows > 0) {
// 				$columnasActualizarEntregas = "";
// 				foreach ($diasPlanilla as $diaPlanilla) {
// 					$columnasActualizarEntregas .= $diaPlanilla['columna'] ." = ". $novedad['D'.$diaPlanilla['indiceDias']] .", ";
// 				}

// 				$consultaActualizarEntregas .= "UPDATE entregas_res_$mes$periodo_actual SET ". trim($columnasActualizarEntregas, ", ") ." WHERE num_doc = '". $novedad['numero_documento'] ."' AND cod_sede = '$sede' AND tipo_complem = '$tipo_complemento';";
// 			} else {
// 				foreach ($diasPlanilla as $diaPlanilla) {
// 					$columnaDias .= $diaPlanilla['columna'] .", ";
// 					$valoresDias .= "'". $novedad['D'. $diaPlanilla['indiceDias']] ."', ";
// 				}

// 				// Consulta para insertar el registro de suplentes en entregas_res.
// 				$consultaInsertarEntregas = "INSERT INTO entregas_res_$mes$periodo_actual (tipo_doc, num_doc, tipo_doc_nom, ape1, ape2, nom1, nom2, genero, dir_res, cod_mun_res, telefono, cod_mun_nac, fecha_nac, cod_estrato, sisben, cod_discap, etnia, resguardo, cod_pob_victima, des_dept_nom, nom_mun_desp, cod_sede, cod_inst, cod_mun_inst, cod_mun_sede, cod_grado, nom_grupo, cod_jorn_est, estado_est, repitente, edad, zona_res_est, activo, tipo_complem$semanaMes, tipo_complem, ". trim($columnaDias, ", ") .") SELECT tipo_doc, num_doc, tipo_doc_nom, ape1, ape2, nom1, nom2, genero, dir_res, cod_mun_res, telefono, cod_mun_nac, fecha_nac, cod_estrato, sisben, cod_discap, etnia, resguardo, cod_pob_victima, des_dept_nom, nom_mun_desp, cod_sede, cod_inst, cod_mun_inst, cod_mun_sede, cod_grado, nom_grupo, cod_jorn_est, estado_est, repitente, edad, zona_res_est, activo, '$tipo_complemento', '$tipo_complemento', ". trim($valoresDias, ", ") ." FROM suplentes WHERE num_doc = $numero_documento";
// 				$Link->query($consultaInsertarEntregas) or die("Error al insertar en entregas_res_$mes$periodo_actual. Linea 302: ". $Link->error);
// 			}
// 		}

// 		//Aplicando Novedades en entregasRES
// 		foreach ($novedades as $novedad){
// 			$columnasActualizarEntregas = "";
// 			foreach ($diasPlanilla as $diaPlanilla) {
// 				$columnasActualizarEntregas .= $diaPlanilla['columna'] ." = ". $novedad['D'.$diaPlanilla['indiceDias']] .", ";
// 			}

// 			$consultaActualizarEntregas .= "UPDATE entregas_res_$mes$periodo_actual SET ". trim($columnasActualizarEntregas, ", ") ." WHERE num_doc = '". $novedad['num_doc'] ."' AND cod_sede = '$sede' AND tipo_complem = '$tipo_complemento';";
// 		}

// 		if (!empty($consultaActualizarEntregas)) {
// 			$resultadoActualizar = $Link->multi_query($consultaActualizarEntregas) or die("Error al consultar entregas_res_$mes$periodo_actual. Linea 317: ". $Link->error);
// 		}
// 		/***************************************************************************/

// 		$respuestaAJAX = [
// 			"estado" => 1,
// 			"mensaje" => "Se ha realizado correctamente el registro."
// 		];
// 		echo json_encode($respuestaAJAX);
