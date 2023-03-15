<?php
	require_once '../../../config.php';
	require_once '../../../db/conexion.php';

	$data = [];
	$perido_actual = $_SESSION["periodoActual"];

	$municipio = (isset($_POST['municipio']) && $_POST['municipio'] != '') ? mysqli_real_escape_string($Link, $_POST["municipio"]) : "";
	$institucion = (isset($_POST['institucion']) && $_POST['institucion'] != '') ? mysqli_real_escape_string($Link, $_POST["institucion"]) : "";
	$tipoNovedad = (isset($_POST['tipoNovedad']) && $_POST['tipoNovedad'] != '') ? mysqli_real_escape_string($Link, $_POST["tipoNovedad"]) : "";
	$mes = (isset($_POST['mes']) && $_POST['mes'] != '') ? mysqli_real_escape_string($Link, $_POST["mes"]) : "";
	$sede = (isset($_POST['sede']) && $_POST['sede'] != '') ? mysqli_real_escape_string($Link, $_POST["sede"]) : "";
	$semana = (isset($_POST['semana']) && $_POST['semana'] != '') ? mysqli_real_escape_string($Link, $_POST["semana"]) : "";
	$tipoComplemento = (isset($_POST['tipoComplemento']) && $_POST['tipoComplemento'] != '') ? mysqli_real_escape_string($Link, $_POST["tipoComplemento"]) : "";

	// Consulta que retorna los dias de planillas el mes seleccionado.
	$consultaPlanillaDias = "SELECT D1, D2, D3, D4, D5, D6, D7, D8, D9, D10, 
									D11, D12, D13, D14, D15, D16, D17, D18, D19, D20, 
									D21, D22, D23, D24, D25, D26, D27, D28, D29, D30, D31  
								FROM planilla_dias WHERE mes = '$mes';";

	$resultadoPlanillaDias = $Link->query($consultaPlanillaDias) or die("Error al consultar planilla_dias: ". $Link->error);
	if ($resultadoPlanillaDias->num_rows > 0){
		while ($registroPlanillasDias = $resultadoPlanillaDias->fetch_assoc()){
			$planilla_dias = $registroPlanillasDias;
		}
	}

	// Consulta para determinar las columnas de días de consulta por la semana seleccionada.
	$columnasDiasEntregas_res = $columnasDiasSuma = $columnaFaltantes = "";
	$consultaPlanillaSemanas = "SELECT DIA as dia FROM planilla_semanas WHERE SEMANA = '$semana' AND MES_ENTREGAS ='$mes'";
	$resultadoPlanillaSemanas = $Link->query($consultaPlanillaSemanas) or die("Error al consultar planilla_semanas: ". $Link->error);
	if($resultadoPlanillaSemanas->num_rows > 0){
		$indiceDia = 1;
		while($registroPlanillaSemanas = $resultadoPlanillaSemanas->fetch_assoc()){
			foreach ($planilla_dias as $clavePlanillasDias => $valorPlanillasDias){
				if ($registroPlanillaSemanas["dia"] == $valorPlanillasDias){
					$columnasDiasEntregas_res .= "SUM(e.". $clavePlanillasDias .") AS D". $indiceDia .", ";
					$columnasDiasSuma .= "ROUND(e.". $clavePlanillasDias . ") + ";
					$columnaFaltantes .= " (COUNT(e.num_doc) - SUM(D$indiceDia)) AS faltantesD$indiceDia , ";
					$indiceDia++;
				}
			}
		}
	}

	// Datos del estudiante
	$columnaFaltantes = trim($columnaFaltantes, " , ");
	$columnasDiasSuma = trim($columnasDiasSuma, " + ");
	$columnasDiasEntregas_res = trim($columnasDiasEntregas_res, ", ");

	if ($tipoNovedad == 0) {
		$condicionMunicipio = $condicionInstitucion = $condicionSede = '';
		$condicionMunicipioNovedad = $condicionInstitucionNovedad = $condicionSedeNovedad = '';
		$novedadParcial = [];
		if ($municipio != '') {
			$condicionMunicipio = " AND e.cod_mun_res = '$municipio' ";
			$condicionMunicipioNovedad = " AND s.cod_mun_sede = '$municipio' ";
		}
		if ($institucion) {
			$condicionInstitucion = " AND e.cod_inst = '$institucion' ";
			$condicionInstitucionNovedad = " AND s.cod_inst = '$institucion' ";
		}
		if ($sede) {
			$condicionSede = " AND e.cod_sede = '$sede' ";
			$condicionSedeNovedad = " AND s.cod_sede = '$sede' ";
		}

		$validacionNovedad = " SELECT nf.cod_sede, MAX(tiponovedad) tiponovedad
								FROM novedades_focalizacion nf
								INNER JOIN sedes$perido_actual s ON s.cod_sede = nf.cod_sede 
								WHERE tiponovedad IN ('0','1')   
								$condicionMunicipioNovedad
								$condicionInstitucionNovedad
								$condicionSedeNovedad 
								AND nf.tipo_complem = '$tipoComplemento'
								AND nf.semana = '$semana'
								GROUP BY nf.cod_sede
								";					
		$respuestaValidacionNovedad = $Link->query($validacionNovedad) or die ('Error en validaciones ln 76');
		if ($respuestaValidacionNovedad->num_rows > 0) {
			while ($dataValidacionNovedad = $respuestaValidacionNovedad->fetch_assoc()) {
				$novedadParcial[$dataValidacionNovedad['cod_sede']] = $dataValidacionNovedad['tiponovedad'];
			}
		}						
		// exit(var_dump($novedadParcial));
		$consulta_focalizados = "SELECT
									u.Ciudad AS ciudad,
									e.cod_inst AS cod_inst,
									e.nom_inst AS nom_inst,
									e.cod_sede AS cod_sede,
									e.nom_sede AS nom_sede,
									e.tipo_complem AS complemento,
									$columnasDiasEntregas_res,
									$columnaFaltantes,
									SUM($columnasDiasSuma) AS suma_dias
								FROM entregas_res_$mes$perido_actual e
								RIGHT JOIN focalizacion$semana f ON e.num_doc = f.num_doc AND e.cod_sede = f.cod_sede  AND e.tipo_complem = f.Tipo_complemento
								INNER JOIN ubicacion u ON u.codigoDANE = e.cod_mun_res
								WHERE f.activo = 1 AND e.tipo='F' AND e.tipo_complem = '$tipoComplemento'
								$condicionMunicipio
								$condicionInstitucion
								$condicionSede 
								GROUP BY e.cod_sede ";
		// exit(var_dump($consulta_focalizados));						
		$respuesta_focalizados = $Link->query($consulta_focalizados) or die('Error al consultar focalizacion: '. $Link->error);
		if($respuesta_focalizados->num_rows > 0) {
			while($registros_focalizados = $respuesta_focalizados->fetch_assoc()) {
				$cod_sede = $registros_focalizados['cod_sede'];
				$nom_sede = $registros_focalizados['nom_sede'];

				$registros_focalizados['cod_sede'] = $cod_sede . '<input type="hidden" name="cod_sede[]" value="'.$cod_sede.'"/>';
				$registros_focalizados['nom_sede'] = $nom_sede . '<input type="hidden" name="nom_sede[]" value="'.$nom_sede.'" />';

				$chequeado = (isset($registros_focalizados['D1']) && $registros_focalizados['D1'] != 0) ? 'checked' : '';
				$registros_focalizados['D1'] = (isset($registros_focalizados['D1'])) ? '<div class="checkbox checkbox-success"><input type="checkbox" class="checkbox1" name="'.$cod_sede.'_D1" id="'.$cod_sede.'_D1" data-faltantes="' .$registros_focalizados['faltantesD1']. '" value="'.$registros_focalizados['D1'].'" '.$chequeado.'><label for="'.$cod_sede.'_D1"></label></div>' : '';

				$chequeado = (isset($registros_focalizados['D2']) && $registros_focalizados['D2'] != 0) ? 'checked' : '';
				$registros_focalizados['D2'] = (isset($registros_focalizados['D2'])) ? '<div class="checkbox checkbox-success"><input type="checkbox" class="checkbox2" name="'.$cod_sede.'_D2" id="'.$cod_sede.'_D2" data-faltantes="' .$registros_focalizados['faltantesD2']. '" value="'.$registros_focalizados['D2'].'" '.$chequeado.'><label for="'.$cod_sede.'_D2"></label></div>' : '';

				$chequeado = (isset($registros_focalizados['D3']) && $registros_focalizados['D3'] != 0) ? 'checked' : '';
				$registros_focalizados['D3'] = (isset($registros_focalizados['D3'])) ? '<div class="checkbox checkbox-success"><input type="checkbox" class="checkbox3" name="'.$cod_sede.'_D3" id="'.$cod_sede.'_D3" data-faltantes="' .$registros_focalizados['faltantesD3']. '" value="'.$registros_focalizados['D3'].'" '.$chequeado.'><label for="'.$cod_sede.'_D3"></label></div>' : '';

				$chequeado = (isset($registros_focalizados['D4']) && $registros_focalizados['D4'] != 0) ? 'checked' : '';
				$registros_focalizados['D4'] = (isset($registros_focalizados['D4'])) ? '<div class="checkbox checkbox-success"><input type="checkbox" class="checkbox4" name="'.$cod_sede.'_D4" id="'.$cod_sede.'_D4" data-faltantes="' .$registros_focalizados['faltantesD4']. '" value="'.$registros_focalizados['D4'].'" '.$chequeado.'><label for="'.$cod_sede.'_D4"></label></div>' : '';

				$chequeado = (isset($registros_focalizados['D5']) && $registros_focalizados['D5'] != 0) ? 'checked' : '';
				$registros_focalizados['D5'] = (isset($registros_focalizados['D5'])) ? '<div class="checkbox checkbox-success"><input type="checkbox" class="checkbox5" name="'.$cod_sede.'_D5" id="'.$cod_sede.'_D5" data-faltantes="' .$registros_focalizados['faltantesD5']. '" value="'.$registros_focalizados['D5'].'" '.$chequeado.'><label for="'.$cod_sede.'_D5"></label></div>' : '';

				$registros_focalizados['tiponovedad'] = isset($novedadParcial[$cod_sede]) ? $novedadParcial[$cod_sede] : 0 ;	
				$data[] = $registros_focalizados;
			}
		}
	}

	if ($tipoNovedad == 1) {
		$consulta_focalizados = "SELECT
									td.Abreviatura AS abreviatura_documento,
									e.num_doc AS numero_documento,
									e.tipo_complem AS complemento,
									CONCAT(e.nom1,' ',e.nom2,' ',e.ape1,' ',e.ape2) AS nombre,
									f.cod_grado AS grado,
									f.nom_grupo AS grupo,
									$columnasDiasEntregas_res,
									$columnaFaltantes,
									($columnasDiasSuma) AS suma_dias
								FROM entregas_res_$mes$perido_actual e
								RIGHT JOIN focalizacion$semana f ON e.num_doc = f.num_doc AND e.cod_sede = f.cod_sede  AND e.tipo_complem = f.Tipo_complemento
								INNER JOIN tipodocumento td ON f.tipo_doc = td.id
								WHERE f.cod_sede = $sede AND e.tipo_complem = '$tipoComplemento' AND f.activo = 1 AND e.tipo='F'
								GROUP BY f.num_doc
								ORDER BY e.cod_grado, e.nom_grupo, e.ape1, e.ape2, e.nom1, e.nom2 asc ";
		// exit(var_dump($consulta_focalizados));
		$respuesta_focalizados = $Link->query($consulta_focalizados) or die('Error al consultar focalizacion: '. $Link->error);
		if($respuesta_focalizados->num_rows > 0) {
			while($registros_focalizados = $respuesta_focalizados->fetch_assoc()) {
				$numero_documento = $registros_focalizados['numero_documento'];
				$abreviatura_documento = $registros_focalizados['abreviatura_documento'];

				$registros_focalizados['numero_documento'] = $numero_documento . '<input type="hidden" name="numero_documentos[]" value="'.$numero_documento.'"/>';
				$registros_focalizados['abreviatura_documento'] = $abreviatura_documento . '<input type="hidden" name="abreviatura_documentos[]" value="'.$abreviatura_documento.'" />';

				$chequeado = (isset($registros_focalizados['D1']) && $registros_focalizados['D1'] == 1) ? 'checked' : '';
				$registros_focalizados['D1'] = (isset($registros_focalizados['D1'])) ? '<div class="checkbox checkbox-success"><input type="checkbox" class="checkbox1" name="'.$numero_documento.'_D1" id="'.$numero_documento.'_D1" data-faltantes="' .$registros_focalizados['faltantesD1']. '" value="1" '.$chequeado.'><label for="'.$numero_documento.'_D1"></label></div>' : '';

				$chequeado = (isset($registros_focalizados['D2']) && $registros_focalizados['D2'] == 1) ? 'checked' : '';
				$registros_focalizados['D2'] = (isset($registros_focalizados['D2'])) ? '<div class="checkbox checkbox-success"><input type="checkbox" class="checkbox2" name="'.$numero_documento.'_D2" id="'.$numero_documento.'_D2" data-faltantes="' .$registros_focalizados['faltantesD2']. '" value="1" '.$chequeado.'><label for="'.$numero_documento.'_D2"></label></div>' : '';

				$chequeado = (isset($registros_focalizados['D3']) && $registros_focalizados['D3'] == 1) ? 'checked' : '';
				$registros_focalizados['D3'] = (isset($registros_focalizados['D3'])) ? '<div class="checkbox checkbox-success"><input type="checkbox" class="checkbox3" name="'.$numero_documento.'_D3" id="'.$numero_documento.'_D3" data-faltantes="' .$registros_focalizados['faltantesD3']. '" value="1" '.$chequeado.'><label for="'.$numero_documento.'_D3"></label></div>' : '';

				$chequeado = (isset($registros_focalizados['D4']) && $registros_focalizados['D4'] == 1) ? 'checked' : '';
				$registros_focalizados['D4'] = (isset($registros_focalizados['D4'])) ? '<div class="checkbox checkbox-success"><input type="checkbox" class="checkbox4" name="'.$numero_documento.'_D4" id="'.$numero_documento.'_D4" data-faltantes="' .$registros_focalizados['faltantesD4']. '" value="1" '.$chequeado.'><label for="'.$numero_documento.'_D4"></label></div>' : '';

				$chequeado = (isset($registros_focalizados['D5']) && $registros_focalizados['D5'] == 1) ? 'checked' : '';
				$registros_focalizados['D5'] = (isset($registros_focalizados['D5'])) ? '<div class="checkbox checkbox-success"><input type="checkbox" class="checkbox5" name="'.$numero_documento.'_D5" id="'.$numero_documento.'_D5" data-faltantes="' .$registros_focalizados['faltantesD5']. '" value="1" '.$chequeado.'><label for="'.$numero_documento.'_D5"></label></div>' : '';

				$data[] = $registros_focalizados;
			}
		}
	}
	// exit(var_dump($data));
	$output = [
		'sEcho' => 1,
		'iTotalRecords' => count($data),
		'iTotalDisplayRecords' => count($data),
		'aaData' => $data
	];

	echo json_encode($output);
