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
$mes = (isset($_POST['mes']) && $_POST['mes'] != '') ? mysqli_real_escape_string($Link, $_POST["mes"]) : "";
$sede = (isset($_POST['sede']) && $_POST['sede'] != '') ? mysqli_real_escape_string($Link, $_POST["sede"]) : "";
$semana = (isset($_POST['semana']) && $_POST['semana'] != '') ? mysqli_real_escape_string($Link, $_POST["semana"]) : "";
$municipio = (isset($_POST['municipio']) && $_POST['municipio'] != '') ? mysqli_real_escape_string($Link, $_POST["municipio"]) : "";
$insitucion = (isset($_POST['insitucion']) && $_POST['insitucion'] != '') ? mysqli_real_escape_string($Link, $_POST["insitucion"]) : "";
$observaciones = (isset($_POST['observaciones']) && $_POST['observaciones'] != '') ? mysqli_real_escape_string($Link, $_POST["observaciones"]) : "";
$tipoComplemento = (isset($_POST['tipoComplemento']) && $_POST['tipoComplemento'] != '') ? mysqli_real_escape_string($Link, $_POST["tipoComplemento"]) : "";
$periodoActual = (isset($_SESSION['periodoActual']) && $_SESSION['periodoActual'] != '') ? mysqli_real_escape_string($Link, $_SESSION["periodoActual"]) : "";

	// Consulta que retorna los dias de planillas el mes seleccionado.
$consultaPlanillaDias = "SELECT D1, D2, D3, D4, D5, D6, D7, D8, D9, D10, D11, D12, D13, D14, D15, D16, D17, D18, D19, D20, D21, D22, D23, D24, D25, D26, D27, D28, D29, D30, D31  FROM planilla_dias WHERE mes = '$mes';";
$resultadoPlanillaDias = $Link->query($consultaPlanillaDias) or die("Error al consultar planilla_dias: ". $Link->error);
if ($resultadoPlanillaDias->num_rows > 0) {
	while ($registroPlanillasDias = $resultadoPlanillaDias->fetch_assoc()) {
		$planilla_dias = $registroPlanillasDias;
	}
}

/************************** REGISTRO DE NOVEDADES **************************/
/******************************* FOCALIZADOS *******************************/
		// Consulta que retorna los datos de los dias de la semana seleccionada.
		$indiceSemana = 0; // Variable que almacena la cantidad de dias de la semana seleccionada.
		$columnasDiasEntregas_res = "";
		$consultaSemana = "SELECT * FROM planilla_semanas WHERE semana = '$semana'";
		$resultadoSemana = $Link->query($consultaSemana) or die("Error al consultar planilla_semanas: ". $Link->error);
		if($resultadoSemana->num_rows > 0){
			while($registroSemana = $resultadoSemana->fetch_assoc()) {
				if($registroSemana['NOMDIAS'] == 'lunes'){
					$diaPlanilla['indiceDias'] = 1;
				} else if($registroSemana['NOMDIAS'] == 'martes'){
					$diaPlanilla['indiceDias'] = 2;
				} else if($registroSemana['NOMDIAS'] == 'miércoles'){
					$diaPlanilla['indiceDias'] = 3;
				} else if($registroSemana['NOMDIAS'] == 'jueves'){
					$diaPlanilla['indiceDias'] = 4;
				} else if($registroSemana['NOMDIAS'] == 'viernes'){
					$diaPlanilla['indiceDias'] = 5;
				}

				// Ciclo que obtiene las columnas de los dias de planillas dias.
				foreach ($planilla_dias as $clavePlanillaDias => $valorPlanillaDias) {
					if ($registroSemana['DIA'] == $valorPlanillaDias)	{
						$diaPlanilla["columna"] = $clavePlanillaDias;
						$columnasDiasEntregas_res .= "IFNULL(e.". $clavePlanillaDias .", 0) AS D". $diaPlanilla['indiceDias'] .", ";
						$indiceSemana++;
					}
				}

				$diaPlanilla['diaPlanilla'] = $registroSemana['DIA'];
				$diasPlanilla[] = $diaPlanilla;
			}

			if ($indiceSemana < 5) {
				for ($i = $indiceSemana; $i < 5; $i++) {
					$columnasDiasEntregas_res .=  "0 AS D". ($i+1) .", ";
				}
			}
		}

		// Consulta que obtiene los datos del estudiantes focalizados.
		$consultaNovedad = "SELECT f.tipo_doc, td.Abreviatura, f.num_doc, CONCAT(f.nom1,' ',f.nom2,' ',f.ape1,' ',f.ape2) AS nombre, '$tipoComplemento' AS complemento, ". trim($columnasDiasEntregas_res, ", ") ."
		FROM focalizacion$semana f
		INNER JOIN entregas_res_$mes". $_SESSION["periodoActual"] ." e ON e.num_doc = f.num_doc AND e.cod_sede = f.cod_sede
		LEFT JOIN tipodocumento td ON f.tipo_doc = td.id WHERE f.cod_sede = $sede AND f.Tipo_complemento = '$tipoComplemento' AND f.activo = 1";

		$resultadoNovedades = $Link->query($consultaNovedad) or die("Error al consultar focalizacion$semana. Linea 72: ". $Link->error);
		if($resultadoNovedades->num_rows > 0){
			while($titular = $resultadoNovedades->fetch_assoc()) {
				// $focalizados[] = $titular;

				// Condiciones para buscar las diferencias entre los días almacenados y los días cambiados en la interfaz.
				$documento = $titular['num_doc'];
				$bandera = 0;
				if($titular['D1'] == 1){
					if(!isset($_POST[$documento.'_D1'])){
						$bandera++;
						$titular['D1'] = 0;
					}
				}else{
					if(isset($_POST[$documento.'_D1'])){
						$bandera++;
						$titular['D1'] = 1;
					}
				}

				if($titular['D2'] == 1){
					if(!isset($_POST[$documento.'_D2'])){
						$bandera++;
						$titular['D2'] = 0;
					}
				}else{
					if(isset($_POST[$documento.'_D2'])){
						$bandera++;
						$titular['D2'] = 1;
					}
				}

				if($titular['D3'] == 1){
					if(!isset($_POST[$documento.'_D3'])){
						$bandera++;
						$titular['D3'] = 0;
					}
				}else{
					if(isset($_POST[$documento.'_D3'])){
						$bandera++;
						$titular['D3'] = 1;
					}
				}

				if($titular['D4'] == 1){
					if(!isset($_POST[$documento.'_D4'])){
						$bandera++;
						$titular['D4'] = 0;
					}
				}else{
					if(isset($_POST[$documento.'_D4'])){
						$bandera++;
						$titular['D4'] = 1;
					}
				}

				if($titular['D5'] == 1){
					if(!isset($_POST[$documento.'_D5'])){
						$bandera++;
						$titular['D5'] = 0;
					}
				}else{
					if(isset($_POST[$documento.'_D5'])){
						$bandera++;
						$titular['D5'] = 1;
					}
				}

				// Si existe algún cambio de dia, se almacena los datos del titular en la variables $novedades.
				if($bandera != 0){
					$novedades[] = $titular;
				}
			}
		}

		/****************************** NO FOCALIZADOS ******************************/
		// Consulta que obtiene los datos del estudiantes suplentes.
		$consultaSuplentes = "SELECT s.tipo_doc AS tipo_documento, s.tipo_doc_nom AS abreviatura, s.num_doc AS numero_documento, CONCAT(s.nom1, ' ', s.nom2,' ', s.ape1,' ', s.ape2) AS nombre_suplente, '$tipoComplemento' AS tipo_complemento, ". trim($columnasDiasEntregas_res, ", ")." FROM suplentes s LEFT JOIN entregas_res_$mes". $_SESSION["periodoActual"] ." e ON e.num_doc = s.num_doc AND e.cod_sede = s.cod_sede AND e.tipo_complem = '$tipoComplemento' WHERE s.cod_sede = '$sede'  AND s.activo = 1";
		$resultadoSuplentes = $Link->query($consultaSuplentes) or die("Error al consultar suplentes. Linea 150: ". $Link->error);
		if($resultadoSuplentes->num_rows > 0){
			while($titularSuplente = $resultadoSuplentes->fetch_assoc()) {
				$bandera = 0;
				$documento = $titularSuplente['numero_documento'];

				// Condiciones para buscar las diferencias entre los días almacenados y los días cambiados en la interfaz.
				if($titularSuplente['D1'] == 1){
					if(!isset($_POST[$documento.'_D1'])){
						$bandera++;
						$titularSuplente['D1'] = 0;
					}
				}else{
					if(isset($_POST[$documento.'_D1'])){
						$bandera++;
						$titularSuplente['D1'] = 1;
					}
				}

				if($titularSuplente['D2'] == 1){
					if(!isset($_POST[$documento.'_D2'])){
						$bandera++;
						$titularSuplente['D2'] = 0;
					}
				}else{
					if(isset($_POST[$documento.'_D2'])){
						$bandera++;
						$titularSuplente['D2'] = 1;
					}
				}

				if($titularSuplente['D3'] == 1){
					if(!isset($_POST[$documento.'_D3'])){
						$bandera++;
						$titularSuplente['D3'] = 0;
					}
				}else{
					if(isset($_POST[$documento.'_D3'])){
						$bandera++;
						$titularSuplente['D3'] = 1;
					}
				}

				if($titularSuplente['D4'] == 1){
					if(!isset($_POST[$documento.'_D4'])){
						$bandera++;
						$titularSuplente['D4'] = 0;
					}
				}else{
					if(isset($_POST[$documento.'_D4'])){
						$bandera++;
						$titularSuplente['D4'] = 1;
					}
				}

				if($titularSuplente['D5'] == 1){
					if(!isset($_POST[$documento.'_D5'])){
						$bandera++;
						$titularSuplente['D5'] = 0;
					}
				}else{
					if(isset($_POST[$documento.'_D5'])){
						$bandera++;
						$titularSuplente['D5'] = 1;
					}
				}

				// Si existe algún cambio de dia, se almacena los datos del titular en la variables $novedadesSuplentes.
				if($bandera != 0){
					$novedadesSuplentes[] = $titularSuplente;
				}
			}
		}

		// Condición que verifica si se ha modificado los datos de los titulares focalizados y suplentes.
		if (!empty($novedades) || !empty($novedadesSuplentes)) {
			$aux = 0;
			$consulta = "INSERT INTO novedades_focalizacion(id_usuario, fecha_hora, cod_sede, tipo_doc_titular, num_doc_titular, tipo_complem, semana, d1, d2, d3, d4, d5, observaciones ) VALUES ";
			foreach ($novedades as $novedad) {
				$tipoDoc = $novedad['tipo_doc'];
				$numDoc = $novedad['num_doc'];
				$d1 = $novedad['D1'];
				$d2 = $novedad['D2'];
				$d3 = $novedad['D3'];
				$d4 = $novedad['D4'];
				$d5 = $novedad['D5'];
				$consulta .= "($usuario, '$fecha', '$sede', '$tipoDoc', '$numDoc' , '$tipoComplemento', '$semana', '$d1', '$d2', '$d3', '$d4', '$d5', '$observaciones'), ";
			}

			foreach ($novedadesSuplentes as $novedad) {
				$tipoDoc = $novedad['tipo_documento'];
				$numDoc = $novedad['numero_documento'];
				$d1 = $novedad['D1'];
				$d2 = $novedad['D2'];
				$d3 = $novedad['D3'];
				$d4 = $novedad['D4'];
				$d5 = $novedad['D5'];
				$consulta .= "($usuario, '$fecha', '$sede', '$tipoDoc', '$numDoc' , '$tipoComplemento', '$semana', '$d1', '$d2', '$d3', '$d4', '$d5', '$observaciones'), ";
			}

			$Link->query(trim($consulta, ", ")) or die("Error al insertar novedades_focalizacion. Linea 250: ". $Link->error);
		} else {
			$respuestaAJAX = [
				"estado" => 0,
				"mensaje" => "Debe modificar los datos de un titular para realizar el guardado de datos."
			];

			exit(json_encode($respuestaAJAX));
		}
		/***************************************************************************/

		/************************** REGISTRO DE ENTREGAS ***************************/
		// Consulta que retorna las semanas de mes seleccionado. Se utiliza para saber la posición de la semana del mes.
		$consultaSemanasMes = "SELECT DISTINCT SEMANA AS semana FROM planilla_semanas WHERE MES = '$mes'";
		$resultadoSemanasMes = $Link->query($consultaSemanasMes) or die("Error al consultar en planilla_semanas: ". $Link->error);
		if ($resultadoSemanasMes->num_rows > 0) {
			$posicionSemanaMes = 1;
			while ($registroSemanaMes = $resultadoSemanasMes->fetch_assoc()) {
				if ($registroSemanaMes["semana"] == $semana) {
					$semanaMes = $posicionSemanaMes;
					break;
				}
				$posicionSemanaMes++;
			}
		}

		$consultaActualizarEntregas = "";
		// Validando existencia en entregas_res
		foreach ($novedadesSuplentes as $novedad){
			$columnaDias = $valoresDias = $consultaInsertarEntregas = "";
			$numero_documento = $novedad['numero_documento'];

			// Consulta que retorna los datos del estudiante en la tabla entregas_res de acuerdo al número de documento.
			$consultaEntregasRes = "SELECT * FROM entregas_res_$mes$periodoActual WHERE num_doc = '$numero_documento'";
			$resultado = $Link->query($consultaEntregasRes) or die("Error al consultar entregas_res_$mes$periodoActual: ". $Link->error);

			// Si existe en registro en entregas_res[MES][AÑO], se actualiza, de lo contrario se inserta un nuevo registro.
			if($resultado->num_rows > 0) {
				$columnasActualizarEntregas = "";
				foreach ($diasPlanilla as $diaPlanilla) {
					$columnasActualizarEntregas .= $diaPlanilla['columna'] ." = ". $novedad['D'.$diaPlanilla['indiceDias']] .", ";
				}

				$consultaActualizarEntregas .= "UPDATE entregas_res_$mes$periodoActual SET ". trim($columnasActualizarEntregas, ", ") ." WHERE num_doc = '". $novedad['numero_documento'] ."' AND cod_sede = '$sede' AND tipo_complem = '$tipoComplemento';";
			} else {
				foreach ($diasPlanilla as $diaPlanilla) {
					$columnaDias .= $diaPlanilla['columna'] .", ";
					$valoresDias .= "'". $novedad['D'. $diaPlanilla['indiceDias']] ."', ";
				}

				// Consulta para insertar el registro de suplentes en entregas_res.
				$consultaInsertarEntregas = "INSERT INTO entregas_res_$mes$periodoActual (tipo_doc, num_doc, tipo_doc_nom, ape1, ape2, nom1, nom2, genero, dir_res, cod_mun_res, telefono, cod_mun_nac, fecha_nac, cod_estrato, sisben, cod_discap, etnia, resguardo, cod_pob_victima, des_dept_nom, nom_mun_desp, cod_sede, cod_inst, cod_mun_inst, cod_mun_sede, cod_grado, nom_grupo, cod_jorn_est, estado_est, repitente, edad, zona_res_est, activo, tipo_complem$semanaMes, tipo_complem, ". trim($columnaDias, ", ") .") SELECT tipo_doc, num_doc, tipo_doc_nom, ape1, ape2, nom1, nom2, genero, dir_res, cod_mun_res, telefono, cod_mun_nac, fecha_nac, cod_estrato, sisben, cod_discap, etnia, resguardo, cod_pob_victima, des_dept_nom, nom_mun_desp, cod_sede, cod_inst, cod_mun_inst, cod_mun_sede, cod_grado, nom_grupo, cod_jorn_est, estado_est, repitente, edad, zona_res_est, activo, '$tipoComplemento', '$tipoComplemento', ". trim($valoresDias, ", ") ." FROM suplentes WHERE num_doc = $numero_documento";
				$Link->query($consultaInsertarEntregas) or die("Error al insertar en entregas_res_$mes$periodoActual. Linea 302: ". $Link->error);
			}
		}

		//Aplicando Novedades en entregasRES
		foreach ($novedades as $novedad){
			$columnasActualizarEntregas = "";
			foreach ($diasPlanilla as $diaPlanilla) {
				$columnasActualizarEntregas .= $diaPlanilla['columna'] ." = ". $novedad['D'.$diaPlanilla['indiceDias']] .", ";
			}

			$consultaActualizarEntregas .= "UPDATE entregas_res_$mes$periodoActual SET ". trim($columnasActualizarEntregas, ", ") ." WHERE num_doc = '". $novedad['num_doc'] ."' AND cod_sede = '$sede' AND tipo_complem = '$tipoComplemento';";
		}

		if (!empty($consultaActualizarEntregas)) {
			$resultadoActualizar = $Link->multi_query($consultaActualizarEntregas) or die("Error al consultar entregas_res_$mes$periodoActual. Linea 317: ". $Link->error);
		}
		/***************************************************************************/

		$respuestaAJAX = [
			"estado" => 1,
			"mensaje" => "Se ha realizado correctamente el registro."
		];
		echo json_encode($respuestaAJAX);
