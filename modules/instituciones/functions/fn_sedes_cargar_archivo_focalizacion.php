<?php
	set_time_limit (0);

	require_once '../../../db/conexion.php';
	require_once '../../../config.php';
	require_once "../../../vendor/autoload.php";

	use PhpOffice\PhpSpreadsheet\Spreadsheet;

	// Función que calcula la cantidad de dias a ingresar o actualizar.
	function calcularDias($primer_dia_semana, $ultimo_dia_semana, $dato, $insert = FALSE) {
		$dia = substr($primer_dia_semana, 1);
		$cadena_valores = "";
		$cadena_consulta = "";

		if ($insert) {
			for ($i = $dia; $i <= $ultimo_dia_semana; $i++) {
				$cadena_consulta .= "D".$i.", ";
				$cadena_valores .= "'1', ";
			}

			return [
				"campos"  => trim($cadena_consulta, ", "),
				"valores" => trim($cadena_valores, ", ")
			];
		} else {
			for ($i = $dia; $i <= $ultimo_dia_semana; $i++) {
				$cadena_consulta .= "D".$i." = '$dato', ";
			}

			return trim($cadena_consulta, ", ");
		}

	}

	// Declaración de variables.
	$mes = (isset($_POST["mes"]) && $_POST["mes"] != "") ? $_POST["mes"] : "";
	$semana = (isset($_POST["semana"]) && $_POST["semana"] != "") ? $_POST["semana"] : "";
	$validar = (isset($_POST["validar"]) && $_POST["validar"] == "true") ? 1 : 0;

	/*****************************************************************************************************/
	// Validar si ya existe la semana de focalizacion que se desea subir.
	$consultaFocalizadoSemana = "show tables like 'focalizacion". $semana ."'";
	$resultadoFocalizadoSemana = $Link->query($consultaFocalizadoSemana) or die("Error al buscar la tabla focalizacion". $semana .". Linea 44: ". $Link->error);
	if($resultadoFocalizadoSemana->num_rows > 0) {
		$respuestaAJAX = [
			"estado" => 0,
			"mensaje" => "No es posible agregar la focalización, debido a que ya existe datos para el mes y la semana seleccionada."
		];
		echo json_encode($respuestaAJAX);
		exit;
	}
	/*****************************************************************************************************/

	/*****************************************************************************************************/
	// Valida la cantidad de registros que se desea almacenar contra la cantidad de focalizados en priorización.
	$rutaArchivo = $_FILES["archivoFocalizacion"]["tmp_name"];

	$consultaCantidadFocalizado="SELECT IF(SUM(num_est_focalizados) > 0, SUM(num_est_focalizados), 0) AS 'cantidadFocalizados' FROM sedes_cobertura WHERE semana = '". $semana ."';";
	$resultadoCantidadFocalizado=$Link->query($consultaCantidadFocalizado) or die("Error al consultar sedes_cobertura. Linea 62: ". $Link->error);
	if($resultadoCantidadFocalizado->num_rows == 0) {
		$respuestaAJAX = [
			"estado" => 0,
			"mensaje" => "No existe ningun estudiante focalizado para la semana seleccionada."
		];
		echo json_encode($respuestaAJAX);
		exit();
	}
	/*****************************************************************************************************/

	/*****************************************************************************************************/
 	// Consultar todos los códigos de las instituciones.
  $arrayCodigosInstituciones = [];
  $consultaIns = "SELECT codigo_inst FROM instituciones;";
	$resultadoIns = $Link->query($consultaIns) or die ("Unable to execute query.". mysql_error($Link));
	if($resultadoIns->num_rows > 0)
	{
    while($registrosIns = $resultadoIns->fetch_assoc())
    {
  		$arrayCodigosInstituciones[] = $registrosIns["codigo_inst"];
    }
	}

	// Consulta todos los códigos de las sedes.
	$arrayCodigosSedes = [];
  $consultaSed = "SELECT cod_sede FROM sedes".$_SESSION["periodoActual"].";";
	$resultadoSed = $Link->query($consultaSed) or die ("Unable to execute query.". mysql_error($Link));
	if($resultadoSed->num_rows > 0)
	{
    while($registrosSed = $resultadoSed->fetch_assoc())
    {
  		$arrayCodigosSedes[] = $registrosSed["cod_sede"];
    }
	}

	// Se valida si existe el archivo.
	if (isset($_FILES["archivoFocalizacion"]["name"]) && $_FILES["archivoFocalizacion"]["name"] != "") {
		// Obtengo los dias
		$conDiasMes = "SELECT
						SUM(
							IF( D1 != '', 1, 0 ) + IF( D2 != '', 1, 0 ) + IF( D3 != '', 1, 0 ) + IF( D4 != '', 1, 0 ) + IF( D5 != '', 1, 0 ) + IF( D6 != '', 1, 0 ) + IF( D7 != '', 1, 0 ) + IF( D8 != '', 1, 0 ) + IF( D9 != '', 1, 0 ) + IF( D10 != '', 1, 0 ) + IF( D11 != '', 1, 0 ) + IF( D12 != '', 1, 0 ) + IF( D13 != '', 1, 0 ) + IF( D14 != '', 1, 0 ) + IF( D15 != '', 1, 0 ) + IF( D16 != '', 1, 0 ) + IF( D17 != '', 1, 0 ) + IF( D18 != '', 1, 0 ) + IF( D19 != '', 1, 0 ) + IF( D20 != '', 1, 0 ) + IF( D21 != '', 1, 0 ) + IF( D22 != '', 1, 0 ) + IF( D23 != '', 1, 0 ) + IF( D24 != '', 1, 0 ) + IF( D25 != '', 1, 0 ) + IF( D26 != '', 1, 0 ) + IF( D27 != '', 1, 0 ) + IF( D28 != '', 1, 0 ) + IF( D29 != '', 1, 0 ) + IF( D30 != '', 1, 0 ) + IF( D31 != '', 1, 0 )
						) AS 'cantidadDias'
					FROM planilla_dias WHERE mes = '". $mes ."';";
		$resDiasMes = $Link->query($conDiasMes);
		if($resDiasMes->num_rows > 0) {
			$regDiasMes = $resDiasMes->fetch_assoc();
			$camDias = "";
			$valDias = "";
			for ($i = 1; $i <= $regDiasMes["cantidadDias"]; $i++)
			{
				$camDias .= "D".$i.", ";
				$valDias .= "'1', ";
			} // Fin iteración para crear campos y valores de dias de entrega_res.
		} // Fin si hay días del mes

		// Si se requiere validación
		if($validar) {
			// Declaracion de variables
			$arrayCodEst = $arrayCodIns = $arrayCodSed =[];
			$conValCreEnt = $conValFoc = "";

			//Abrimos nuestro archivo
			$archivo=fopen($_FILES["archivoFocalizacion"]["tmp_name"], "r");
			$separador = (count(fgetcsv($archivo, null, ",")) > 1) ? "," : ";";

			// Iteramos el archivo
			while(($datos = fgetcsv($archivo, null, $separador)) == TRUE) {
					// Valida que el campo Número documento no este vacio ni nulo.
					if($datos[1] == "" || is_null($datos[1]))
					{
						$respuestaAJAX = [
							"estado" => 0,
							"mensaje" => "El estudiante con código: ". $datos[1] ." no puede tener el campo <strong>Número documento</strong> vacio o nulo. Por favor verifique el archivo e intente nuevamente."
						];
						echo json_encode($result);
			  	}

					// Valida que el campo Tipo documento no este vacio ni nulo.
					if($datos[0] == "" || is_null($datos[0])){
						$respuestaAJAX = [
							"estado" => 0,
							"mensaje" => "El estudiante con código: ". $datos[1] ." no puede tener el campo <strong>Tipo documento</strong> vacio o nulo. Por favor verifique el archivo e intente nuevamente."
						];
						echo json_encode($respuestaAJAX);
				  	exit();
					}

					// Valida que el campo genero no este vacio ni nulo.
					if($datos[7] == "" || is_null($datos[7])){
						$respuestaAJAX = [
							"estado" => 0,
							"mensaje" => "El estudiante con código: ". $datos[1] ." no puede tener el campo <strong>genero</strong> vacio o nulo. Por favor verifique el archivo e intente nuevamente."
						];
						echo json_encode($respuestaAJAX);
				  	exit();
					}

					// Valida que el campo Código estrato no esté vacio ni nulo.
					if($datos[13] == "" || is_null($datos[13])){
						$respuestaAJAX = [
							"estado" => 0,
							"mensaje" => "El estudiante con código: ". $datos[1] ." no puede tener el campo <strong>Código estrato</strong> vacio o nulo. Por favor verifique el archivo e intente nuevamente."
						];
						echo json_encode($respuestaAJAX);
				  	exit();
					}

					// Valida que el campo discapacidad no esté vacio ni nulo.
					if($datos[15] == "" || is_null($datos[15])){
						$respuestaAJAX = [
							"estado" => 0,
							"mensaje" => "El estudiante con código: ". $datos[1] ." no puede tener el campo <strong>Código discapacidad</strong> vacio o nulo. Por favor verifique el archivo e intente nuevamente."
						];
						echo json_encode($respuestaAJAX);
				  	exit();
					}

					// Valida que el campo etnia no esté vacio ni nulo.
					if($datos[16] == "" || is_null($datos[16])){
						$respuestaAJAX = [
							"estado" => 0,
							"mensaje" => "El estudiante con código: ". $datos[1] ." no puede tener el campo <strong>Étnia</strong> vacio o nulo. Por favor verifique el archivo e intente nuevamente."
						];
						echo json_encode($respuestaAJAX);
				  	exit();
					}

					// Valida que el campo resguardo no esté vacio ni nulo.
					if($datos[17] == "" || is_null($datos[17])){
						$respuestaAJAX = [
							"estado" => 0,
							"mensaje" => "El estudiante con código: ". $datos[1] ." no puede tener el campo <strong>Resguardo</strong> vacio o nulo. Por favor verifique el archivo e intente nuevamente."
						];
						echo json_encode($respuestaAJAX);
				  	exit();
					}

					// Valida que el campo población victima no esté vacio ni nulo.
					if($datos[18] == "" || is_null($datos[18])){
						$respuestaAJAX = [
							"estado" => 0,
							"mensaje" => "El estudiante con código: ". $datos[1] ." no puede tener el campo <strong>Población victima</strong> vacio o nulo. Por favor verifique el archivo e intente nuevamente."
						];
						echo json_encode($respuestaAJAX);
				  	exit();
					}

					// Valida que el campo Código institución no esté vacio ni nulo.
					if($datos[23] == "" || is_null($datos[23])){
						$respuestaAJAX = [
							"estado" => 0,
							"mensaje" => "El estudiante con código: ". $datos[1] ." no puede tener el campo <strong>Código institución</strong> vacio o nulo. Por favor verifique el archivo e intente nuevamente."
						];
						echo json_encode($respuestaAJAX);
				  	exit();
					}

					// Valida que el campo Código sede no esté vacio ni nulo.
					if($datos[24] == "" || is_null($datos[24])){
						$respuestaAJAX = [
							"estado" => 0,
							"mensaje" => "El estudiante con código: ". $datos[1] ." no puede tener el campo <strong>Código sede</strong> vacio o nulo. Por favor verifique el archivo e intente nuevamente."
						];
						echo json_encode($respuestaAJAX);
				  	exit();
					}

					// Valida que el campo Código grado no esté vacio ni nulo.
					if($datos[27] == "" || is_null($datos[27])){
						$respuestaAJAX = [
							"estado" => 0,
							"mensaje" => "El estudiante con código: ". $datos[1] ." no puede tener el campo <strong>Código grado</strong> vacio o nulo. Por favor verifique el archivo e intente nuevamente."
						];
						echo json_encode($respuestaAJAX);
				  	exit();
					}

					// Valida que el campo Código jornada no esté vacio ni nulo.
					if($datos[29] == "" || is_null($datos[29])){
						$respuestaAJAX = [
							"estado" => 0,
							"mensaje" => "El estudiante con código: ". $datos[1] ." no puede tener el campo <strong>Código jornada</strong> vacio o nulo. Por favor verifique el archivo e intente nuevamente."
						];
						echo json_encode($respuestaAJAX);
				  	exit();
					}

					// Valida que el campo Edad no esté vacio ni nulo.
					if($datos[32] == "" || is_null($datos[32])){
						$respuestaAJAX = [
							"estado" => 0,
							"mensaje" => "El estudiante con código: ". $datos[1] ." no puede tener el campo <strong>Edad</strong> vacio o nulo. Por favor verifique el archivo e intente nuevamente."
						];
						echo json_encode($respuestaAJAX);
				  	exit();
					}

					// Valida que el campo Zona residencia no esté vacio ni nulo.
					if($datos[33] == "" || is_null($datos[33])){
						$respuestaAJAX = [
							"estado" => 0,
							"mensaje" => "El estudiante con código: ". $datos[1] ." no puede tener el campo <strong>Zona residencia</strong> vacio o nulo. Por favor verifique el archivo e intente nuevamente."
						];
						echo json_encode($respuestaAJAX);
				  	exit();
					}

				$arrayCodIns[] = $datos[21];
				$arrayCodSed[] = $datos[22];
				$arrayCodEst[$datos[1]][] = $datos[36];

				// String con valores para crear entregas_res
				$conValCreEnt .= "('".$datos[0]."', '".$datos[1]."', '".$datos[2]."', '".utf8_encode($datos[3])."', '".utf8_encode($datos[4])."', '".utf8_encode($datos[5])."', '".utf8_encode($datos[6])."', '".$datos[7]."', '".utf8_encode($datos[8])."', '".$datos[9]."', '".$datos[10]."', '".$datos[11]."', '".$datos[12]."', '".$datos[13]."', '".$datos[14]."', '".$datos[15]."', '".$datos[16]."', '".$datos[17]."', '".$datos[18]."', '".($datos[19] == "" ? 0 : $datos[19])."', '".($datos[20] =="" ? 0 : $datos[20])."', '".$datos[21]."', '".$datos[22]."', '".$datos[23]."', '".$datos[24]."', '".utf8_encode($datos[25])."', '".utf8_encode($datos[26])."', '".$datos[27]."', '".$datos[28]."', '".$datos[29]."', '".$datos[30]."', '".$datos[31]."', '".$datos[32]."', '".$datos[33]."',  '".($datos[34] == "" ? 0 : $datos[34])."',  '".$datos[35]."', '1', '".$datos[36]."',  '".$datos[36]."',  '".$datos[36]."',  '".$datos[36]."',  '".$datos[36]."', '".$datos[36]."', ". trim($valDias, ", ") ."), ";

				// String con valores para crear focalización
				$conValFoc .= "('".$datos[0]."', '".$datos[1]."', '".utf8_encode($datos[3])."', '".utf8_encode($datos[4])."', '".utf8_encode($datos[5])."', '".utf8_encode($datos[6])."', '".$datos[7]."', '".utf8_encode($datos[8])."', '".$datos[9]."', '".$datos[10]."', '".$datos[11]."', '".$datos[12]."', '".$datos[13]."', '".$datos[14]."', '".$datos[15]."', '".$datos[16]."', '".$datos[17]."', '".$datos[18]."', '".($datos[19] == "" ? 0 : $datos[19])."', '".($datos[20] =="" ? 0 : $datos[20])."', '".$datos[21]."', '".$datos[22]."', '".$datos[27]."', '".$datos[28]."', '".$datos[29]."', '".$datos[30]."', '".$datos[31]."', '".$datos[32]."', '".$datos[33]."', '1', '".$datos[36]."' ), ";
			} // FIN Iteración de los datos del archivo

			// Obtener lo datos unicos de instituciones y sedes.
			$arrayCodInsUni = array_unique($arrayCodIns);
			$arrayCodSedUni = array_unique($arrayCodSed);

			// Validar que no exista un mismo complemento para un estudiante determinado.
			foreach ($arrayCodEst as $i => $codEst)
			{
				if(count($codEst) > 1)
				{
					foreach ($codEst as $k => $comEst)
					{
						foreach ($codEst as $k2 => $comEst2)
						{
							if($comEst == $comEst2 && $k != $k2)
							{
								$result = [
									"estado" => 0,
									"mensaje" => "El código de estudiante: ". $i ." complementos duplicados. Por favor verifique el archivo e intente nuevamente"
								];
								echo json_encode($result);
						  	exit();
							}
						}
					}
				}
			}

			// Validar que la institucion existe
			foreach ($arrayCodInsUni as $i => $codInsDup)
			{
				if(!in_array($codInsDup, $arrayCodigosInstituciones))
				{
			  	$result = [
						"estado" => 0,
						"mensaje" => "El código de Institución N°: <strong>".$codInsDup."</strong> NO se encuentra registrado en la base de datos. Por favor ingrese al módulo de intituciones y registrelo para continuar"
					];
					echo json_encode($result);
			  	exit();
			  }
			}

		  // validar que la sede existe
		  foreach ($arrayCodSedUni as $i => $codSedDup)
		  {
			  if(!in_array($codSedDup, $arrayCodigosSedes))
			  {
			  	$result = [
						"estado" => 0,
						"mensaje" => "El código de Sede N°: <strong>".$codSedDup."</strong> NO se encuentra registrado en la base de datos. Por favor ingrese al módulo de sedes y registrelo para continuar"
					];
					echo json_encode($result);
			  	exit();
			  }
			}
		} // Fin si se requiere validación
		else
		{
			$conValCreEnt = $conValFoc = "";
			//Abrimos nuestro archivo
			$archivo=fopen($_FILES["archivoFocalizacion"]["tmp_name"], "r");
			$separador = (count(fgetcsv($archivo, null, ",")) > 1) ? "," : ";";

			// Iteramos el archivo
			while(($datos = fgetcsv($archivo, null, $separador))==true)
			{
				// String con valores para crear entregas_res
				$conValCreEnt .= "('".$datos[0]."', '".$datos[1]."', '".$datos[2]."', '".utf8_encode($datos[3])."', '".utf8_encode($datos[4])."', '".utf8_encode($datos[5])."', '".utf8_encode($datos[6])."', '".$datos[7]."', '".utf8_encode($datos[8])."', '".$datos[9]."', '".$datos[10]."', '".$datos[11]."', '".$datos[12]."', '".$datos[13]."', '".$datos[14]."', '".$datos[15]."', '".$datos[16]."', '".$datos[17]."', '".$datos[18]."', '".($datos[19] == "" ? 0 : $datos[19])."', '".($datos[20] =="" ? 0 : $datos[20])."', '".$datos[21]."', '".$datos[22]."', '".$datos[23]."', '".$datos[24]."', '".utf8_encode($datos[25])."', '".utf8_encode($datos[26])."', '".$datos[27]."', '".$datos[28]."', '".$datos[29]."', '".$datos[30]."', '".$datos[31]."', '".$datos[32]."', '".$datos[33]."',  '".($datos[34] == "" ? 0 : $datos[34])."',  '".$datos[35]."', '1', '".$datos[36]."',  '".$datos[36]."',  '".$datos[36]."',  '".$datos[36]."',  '".$datos[36]."', '".$datos[36]."', ". trim($valDias, ", ") ."), ";

				// String con valores para crear focalización
				$conValFoc .= "('".$datos[0]."', '".$datos[1]."', '".utf8_encode($datos[3])."', '".utf8_encode($datos[4])."', '".utf8_encode($datos[5])."', '".utf8_encode($datos[6])."', '".$datos[7]."', '".utf8_encode($datos[8])."', '".$datos[9]."', '".$datos[10]."', '".$datos[11]."', '".$datos[12]."', '".$datos[13]."', '".$datos[14]."', '".$datos[15]."', '".$datos[16]."', '".$datos[17]."', '".$datos[18]."', '".($datos[19] == "" ? 0 : $datos[19])."', '".($datos[20] =="" ? 0 : $datos[20])."', '".$datos[21]."', '".$datos[22]."', '".$datos[27]."', '".$datos[28]."', '".$datos[29]."', '".$datos[30]."', '".$datos[31]."', '".$datos[32]."', '".$datos[33]."', '1', '".$datos[36]."' ), ";
			} // FIN Iteración del archivo.
		} // FIN No se requiere validaciones.
		// FIN VALIDACIONES
		/*****************************************************************************************************/

		/*****************************************************************************************************/
		// Consulta que valida si existe la tabla entregas_res_[MES][AÑO] que se desea subir.
		$consultaEntregaResSemana = "show tables like 'entregas_res_". $mes . $_SESSION["periodoActual"] ."'";
		$resultadoEntregaResSemana = $Link->query($consultaEntregaResSemana) or die("Error al consultar si existe la tabla entregas_res_". $mes . $_SESSION["periodoActual"] ." Linea 364: ". $Link->error);
		if($resultadoEntregaResSemana->num_rows == 0)
		{
			// Se crea la tabla entrega_res
			$conCreTabEntRes = "CREATE TABLE entregas_res_". $mes . $_SESSION["periodoActual"] ." (
														id INT(11) NOT NULL AUTO_INCREMENT,
														tipo_doc INT(11) NULL DEFAULT '0',
														num_doc VARCHAR(24) NULL DEFAULT '-',
														tipo_doc_nom VARCHAR(10) NULL DEFAULT NULL,
														ape1 VARCHAR(50) NULL DEFAULT '-',
														ape2 VARCHAR(50) NULL DEFAULT '-',
														nom1 VARCHAR(50) NULL DEFAULT '-',
														nom2 VARCHAR(50) NULL DEFAULT '-',
														genero VARCHAR(1) NULL DEFAULT '-',
														dir_res VARCHAR(200) NULL DEFAULT NULL,
														cod_mun_res INT(11) NULL DEFAULT NULL,
														telefono VARCHAR(50) NULL DEFAULT NULL,
														cod_mun_nac INT(11) NULL DEFAULT '0',
														fecha_nac DATE NULL DEFAULT NULL,
														cod_estrato INT(11) NULL DEFAULT '0',
														sisben DECIMAL(5,3) NULL DEFAULT '0.000',
														cod_discap INT(11) NULL DEFAULT '0',
														etnia INT(11) NULL DEFAULT '0',
														resguardo INT(11) NULL DEFAULT '0',
														cod_pob_victima INT(11) NULL DEFAULT '0',
														des_dept_nom INT(11) NULL DEFAULT NULL,
														nom_mun_desp INT(11) NULL DEFAULT NULL,
														cod_sede BIGINT(20) NULL DEFAULT '0',
														cod_inst BIGINT(20) NULL DEFAULT '0',
														cod_mun_inst INT(11) NULL DEFAULT NULL,
														cod_mun_sede INT(11) NULL DEFAULT NULL,
														nom_sede VARCHAR(200) NULL DEFAULT NULL,
														nom_inst VARCHAR(200) NULL DEFAULT NULL,
														cod_grado INT(11) NULL DEFAULT '0',
														nom_grupo INT(11) NULL DEFAULT NULL,
														cod_jorn_est INT(11) UNSIGNED NULL DEFAULT '0',
														estado_est VARCHAR(20) NULL DEFAULT NULL,
														repitente VARCHAR(2) NULL DEFAULT NULL,
														edad VARCHAR(4) NULL DEFAULT '0',
														zona_res_est INT(11) NULL DEFAULT '0',
														id_disp_est INT(11) UNSIGNED NULL DEFAULT '0',
														TipoValidacion VARCHAR(50) NULL DEFAULT '-',
														activo TINYINT(1) UNSIGNED NULL DEFAULT '0',
														tipo_complem1 VARCHAR(45) NULL DEFAULT NULL,
														tipo_complem2 VARCHAR(45) NULL DEFAULT NULL,
														tipo_complem3 VARCHAR(45) NULL DEFAULT NULL,
														tipo_complem4 VARCHAR(45) NULL DEFAULT NULL,
														tipo_complem5 VARCHAR(45) NULL DEFAULT NULL,
														tipo_complem VARCHAR(45) NULL DEFAULT NULL,
														D1 VARCHAR(45) NULL DEFAULT '0',
														D2 VARCHAR(45) NULL DEFAULT '0',
														D3 VARCHAR(45) NULL DEFAULT '0',
														D4 VARCHAR(45) NULL DEFAULT '0',
														D5 VARCHAR(45) NULL DEFAULT '0',
														D6 VARCHAR(45) NULL DEFAULT '0',
														D7 VARCHAR(45) NULL DEFAULT '0',
														D8 VARCHAR(45) NULL DEFAULT '0',
														D9 VARCHAR(45) NULL DEFAULT '0',
														D10 VARCHAR(45) NULL DEFAULT '0',
														D11 VARCHAR(45) NULL DEFAULT '0',
														D12 VARCHAR(45) NULL DEFAULT '0',
														D13 VARCHAR(45) NULL DEFAULT '0',
														D14 VARCHAR(45) NULL DEFAULT '0',
														D15 VARCHAR(45) NULL DEFAULT '0',
														D16 VARCHAR(45) NULL DEFAULT '0',
														D17 VARCHAR(45) NULL DEFAULT '0',
														D18 VARCHAR(45) NULL DEFAULT '0',
														D19 VARCHAR(45) NULL DEFAULT '0',
														D20 VARCHAR(45) NULL DEFAULT '0',
														D21 VARCHAR(45) NULL DEFAULT '0',
														D22 VARCHAR(45) NULL DEFAULT '0',
														D23 VARCHAR(45) NULL DEFAULT '0',
														D24 VARCHAR(45) NULL DEFAULT '0',
														D25 VARCHAR(45) NULL DEFAULT '0',
														D26 VARCHAR(45) NULL DEFAULT '0',
														D27 VARCHAR(45) NULL DEFAULT '0',
														D28 VARCHAR(45) NULL DEFAULT '0',
														D29 VARCHAR(45) NULL DEFAULT '0',
														D30 VARCHAR(45) NULL DEFAULT '0',
														D31 VARCHAR(45) NULL DEFAULT '0',
														PRIMARY KEY (id),
														INDEX Acel_est1 (num_doc, cod_jorn_est, cod_grado, cod_pob_victima, cod_inst, cod_discap) USING BTREE
														)
														COLLATE='utf8_general_ci'
														ENGINE=InnoDB
														AUTO_INCREMENT=0;";
			$resCreTabEntRes = $Link->query($conCreTabEntRes) or die ("Error al crear tabla entregas_res". $mes . $_SESSION["periodoActual"] ." Linea: 450". $Link->error);
			if($resCreTabEntRes) {
				// Se ingresa los registros
				// Declaración variables de consulta.
				$conCreEnt = "INSERT INTO entregas_res_". $mes . $_SESSION["periodoActual"] ." ( tipo_doc, num_doc, tipo_doc_nom, ape1, ape2, nom1, nom2, genero, dir_res, cod_mun_res, telefono, cod_mun_nac, fecha_nac, cod_estrato, sisben, cod_discap, etnia, resguardo, cod_pob_victima, des_dept_nom, nom_mun_desp, cod_inst, cod_sede, cod_mun_inst, cod_mun_sede, nom_sede, nom_inst, cod_grado, nom_grupo, cod_jorn_est, estado_est, repitente, edad, zona_res_est, id_disp_est, TipoValidacion, activo, tipo_complem1, tipo_complem2, tipo_complem3, tipo_complem4, tipo_complem5, tipo_complem, ". trim($camDias, ", ") .") VALUES ". trim($conValCreEnt, ", ");
				$resCreEntRes = $Link->query($conCreEnt) or die("Error al insertar entregas_res". $mes . $_SESSION["periodoActual"] ." Linea: 456". $Link->error);
				if (!$resCreEntRes) {
					$respuestaAJAX = [
						"estado" => 0,
						"mensaje" => "No fue posible crear datos para entregas_res"
					];
					echo json_encode($respuestaAJAX);
					exit;
				}
			} else{
				$respuestaAJAX = [
					"estado" => 0,
					"mensaje" => "No fue posible crear la tabla de Entregas"
				];
				echo json_encode($respuestaAJAX);
				exit;
			}
		} else {
			// Consulta para saber el dia en que comienza la semana seleccionada.
			$res_dias_semana = $Link->query("SELECT IF(DIA>10,DIA,CONCAT('0',DIA)) AS dia FROM planilla_semanas WHERE MES = '$mes' AND SEMANA = '$semana' ORDER BY DIA LIMIT 1") or die ("Error al consultar planilla_semanas Linea: 476". $Link->error);
			if ($res_dias_semana->num_rows > 0) {
				$dia_semana =  $res_dias_semana->fetch_assoc();

				// variable para asignar la cantidad de días por mes.
				$contador_dia = 0;
				// Variable para asignar el primer día de la semana.
				$dia_actual = 1;
				// Consulta para obtener el primer dia se la semana seleccionada.
				$res_D = $Link->query("SELECT D1,D2,D3,D4,D5,D6,D7,D8,D9,D10,D11,D12,D13,D14,D15,D16,D17,D18,D19,D20,D21,D22,D23,D24,D25,D26,D27,D28,D29,D30,D31 FROM planilla_dias WHERE mes = '$mes'") or die ("Error al consultar planilla_semanas. Linea: 485". $Link->error);
				if ($res_D->num_rows > 0) {
					$d = $res_D->fetch_assoc();
					foreach ($d as $nombre_columna => $dia) {
						// Condición que valida si el día contiene algun día de mes asignado.
						if ($dia != "") { $contador_dia += 1; }
						// Condición que valida si el dia de planilla_semanas es igual a planillas_dias.
						if ($dia == $dia_semana["dia"]) { $dia_actual = $nombre_columna; }
					}
				}

				/***************************** Modificación ************************************************/
				// Se actualiza a cero (0) las columnas de todos los registros en entregas_res[MES][AÑO], a partir del primer día de la semana seleccionada. Ej: D7.
				$consulta_actualizacion = "UPDATE entregas_res_". $mes . $_SESSION["periodoActual"] ." SET ". calcularDias($dia_actual, $contador_dia, 0) . " WHERE id > 0";
				$resultado_actualizacion = $Link->query($consulta_actualizacion) or die ("Error al actualizar en entregas_res". $mes . $_SESSION["periodoActual"] .". Linea 499: ". mysqli_error($Link));
				/**************************************************************************************/

				// Consulta que retorna todos los datos de la tabla entregas_res[MES][AÑO]
				$res_entregas_res = $Link->query("SELECT id, num_doc, cod_sede, tipo_complem FROM entregas_res_".$mes.$_SESSION["periodoActual"]." WHERE 1") or die ("Error al consultar en entregas_res". $mes . $_SESSION["periodoActual"] .". Linea 503: ". mysqli_error($Link));
				if ($res_entregas_res->num_rows > 0) {
					while ($reg_est_entregas_res = $res_entregas_res->fetch_assoc()) {
							$estudiantes_entregas_res[$reg_est_entregas_res["num_doc"]][]= $reg_est_entregas_res;
					}
				}

				//Abrimos nuestro archivo
				$archivo=fopen($_FILES["archivoFocalizacion"]["tmp_name"], "r");
				$separador = (count(fgetcsv($archivo, null, ",")) > 1) ? "," : ";";

				// Iteramos el archivo
				while(($datos = fgetcsv($archivo, null, $separador))==true) {
					// Variable para identificar si el registro es nuevo o ya existe.
					$existe = false;
					// Condición que valida si existe el documento del estudiante.
					if (isset($estudiantes_entregas_res[$datos[1]])) {
						// Iteramos los datos de entregas_res
						foreach ($estudiantes_entregas_res[$datos[1]] as $dat_est_ent_res) {
							if ($dat_est_ent_res["tipo_complem"] != $datos[36] || $dat_est_ent_res["cod_sede"] != $datos[22]) {
								if ($existe) {
									if (count($estudiantes_entregas_res[$datos[1]]) > 1) {
										$Link->query("UPDATE entregas_res_".$mes.$_SESSION["periodoActual"]." SET ". calcularDias($dia_actual, $contador_dia, 0) ." WHERE id = ". $dat_est_ent_res["id"]) or die ("Error al actualizar en entregas_res". $mes . $_SESSION["periodoActual"] .". Linea 525: ". mysqli_error($Link));
										$existe = true;
									}
								} else {
									$Link->query("UPDATE entregas_res_".$mes.$_SESSION["periodoActual"]." SET ". calcularDias($dia_actual, $contador_dia, 0) ." WHERE id = ". $dat_est_ent_res["id"]) or die ("Error al actualizar en entregas_res". $mes . $_SESSION["periodoActual"] .". Linea 529: ". mysqli_error($Link));
									$existe = false;
								}
							} else {
								if ($existe) {
									if (count($estudiantes_entregas_res[$datos[1]]) > 1) {
										$Link->query("UPDATE entregas_res_".$mes.$_SESSION["periodoActual"]." SET ". calcularDias($dia_actual, $contador_dia, 0) ." WHERE id = ". $dat_est_ent_res["id"]) or die ("Error al actualizar en entregas_res". $mes . $_SESSION["periodoActual"] .". Linea 535: ". mysqli_error($Link));
										$existe = false;
									} else {
										$Link->query("UPDATE entregas_res_".$mes.$_SESSION["periodoActual"]." SET ". calcularDias($dia_actual, $contador_dia, 0) ." WHERE id = ". $dat_est_ent_res["id"]) or die ("Error al actualizar en entregas_res". $mes . $_SESSION["periodoActual"] .". Linea 538: ". mysqli_error($Link));
									}
								} else {
									$Link->query("UPDATE entregas_res_".$mes.$_SESSION["periodoActual"]." SET ". calcularDias($dia_actual, $contador_dia, 1) ." WHERE id = ". $dat_est_ent_res["id"]) or die ("Error al actualizar en entregas_res". $mes . $_SESSION["periodoActual"] .". Linea 541: ". mysqli_error($Link));
									$existe = true;
								}
								$existe = true;
							}
						}
						// Condición que valida si se necesita agregar nuevo registro.
						if (!$existe) {
							$Link->query("INSERT INTO entregas_res_". $mes . $_SESSION["periodoActual"] ."(tipo_doc, num_doc, tipo_doc_nom, ape1, ape2, nom1, nom2, genero, dir_res, cod_mun_res, telefono, cod_mun_nac, fecha_nac, cod_estrato, sisben, cod_discap, etnia, resguardo, cod_pob_victima, des_dept_nom, nom_mun_desp, cod_inst, cod_sede, cod_mun_inst, cod_mun_sede, nom_sede, nom_inst, cod_grado, nom_grupo, cod_jorn_est, estado_est, repitente, edad, zona_res_est, id_disp_est, TipoValidacion, activo, tipo_complem1, tipo_complem2, tipo_complem3, tipo_complem4, tipo_complem5, tipo_complem, ". calcularDias($dia_actual, $contador_dia, 1, true)["campos"] .") VALUES ('".$datos[0]."', '".$datos[1]."', '".$datos[2]."', '".utf8_encode($datos[3])."', '".utf8_encode($datos[4])."', '".utf8_encode($datos[5])."', '".utf8_encode($datos[6])."', '".$datos[7]."', '".utf8_encode($datos[8])."', '".$datos[9]."', '".$datos[10]."', '".$datos[11]."', '".$datos[12]."', '".$datos[13]."', '".$datos[14]."', '".$datos[15]."', '".$datos[16]."', '".$datos[17]."', '".$datos[18]."', '".($datos[19] == "" ? 0 : $datos[19])."', '".($datos[20] =="" ? 0 : $datos[20])."', '".$datos[21]."', '".$datos[22]."', '".$datos[23]."', '".$datos[24]."', '".utf8_encode($datos[25])."', '".utf8_encode($datos[26])."', '".$datos[27]."', '".$datos[28]."', '".$datos[29]."', '".$datos[30]."', '".$datos[31]."', '".$datos[32]."', '".$datos[33]."',  '".($datos[34] == "" ? 0 : $datos[34])."',  '".$datos[35]."', '1', '".$datos[36]."',  '".$datos[36]."',  '".$datos[36]."',  '".$datos[36]."',  '".$datos[36]."', '".$datos[36]."', ". calcularDias($dia_actual, $contador_dia, 1, true)["valores"] .")") or die ("Error al actualizar en entregas_res". $mes . $_SESSION["periodoActual"] .". Linea 499: ". mysqli_error($Link));
						}
					} else {
						$Link->query("INSERT INTO entregas_res_". $mes . $_SESSION["periodoActual"] ."(tipo_doc, num_doc, tipo_doc_nom, ape1, ape2, nom1, nom2, genero, dir_res, cod_mun_res, telefono, cod_mun_nac, fecha_nac, cod_estrato, sisben, cod_discap, etnia, resguardo, cod_pob_victima, des_dept_nom, nom_mun_desp, cod_inst, cod_sede, cod_mun_inst, cod_mun_sede, nom_sede, nom_inst, cod_grado, nom_grupo, cod_jorn_est, estado_est, repitente, edad, zona_res_est, id_disp_est, TipoValidacion, activo, tipo_complem1, tipo_complem2, tipo_complem3, tipo_complem4, tipo_complem5, tipo_complem, ". calcularDias($dia_actual, $contador_dia, 1, true)["campos"] .") VALUES ('".$datos[0]."', '".$datos[1]."', '".$datos[2]."', '".utf8_encode($datos[3])."', '".utf8_encode($datos[4])."', '".utf8_encode($datos[5])."', '".utf8_encode($datos[6])."', '".$datos[7]."', '".utf8_encode($datos[8])."', '".$datos[9]."', '".$datos[10]."', '".$datos[11]."', '".$datos[12]."', '".$datos[13]."', '".$datos[14]."', '".$datos[15]."', '".$datos[16]."', '".$datos[17]."', '".$datos[18]."', '".($datos[19] == "" ? 0 : $datos[19])."', '".($datos[20] =="" ? 0 : $datos[20])."', '".$datos[21]."', '".$datos[22]."', '".$datos[23]."', '".$datos[24]."', '".utf8_encode($datos[25])."', '".utf8_encode($datos[26])."', '".$datos[27]."', '".$datos[28]."', '".$datos[29]."', '".$datos[30]."', '".$datos[31]."', '".$datos[32]."', '".$datos[33]."',  '".($datos[34] == "" ? 0 : $datos[34])."',  '".$datos[35]."', '1', '".$datos[36]."',  '".$datos[36]."',  '".$datos[36]."',  '".$datos[36]."',  '".$datos[36]."', '".$datos[36]."', ". calcularDias($dia_actual, $contador_dia, 1, true)["valores"] .")") or die ("Error al actualizar en entregas_res_". $mes . $_SESSION["periodoActual"]. ". Linea 543: " .mysqli_error($Link));
					}
				}
			} else {
				$respuestaAJAX = [
					"estado"  => 0,
					"mensaje" => "No fue posible encontrar el día para la semana seleccionada."
				];
			}
		}

		/*************************** Bloque para manipular los datos de focalización.***************************/

		// Se crea la tabla focalizacion[$mes]
		$conCreTabFoc = "CREATE TABLE focalizacion".$semana." (
											id INTEGER(11) NOT NULL AUTO_INCREMENT,
											tipo_doc INTEGER(11) DEFAULT '0',
											num_doc VARCHAR(20) COLLATE utf8_general_ci DEFAULT NULL,
											nom1 VARCHAR(50) COLLATE utf8_general_ci DEFAULT NULL,
											nom2 VARCHAR(50) COLLATE utf8_general_ci DEFAULT NULL,
											ape1 VARCHAR(50) COLLATE utf8_general_ci DEFAULT NULL,
											ape2 VARCHAR(50) COLLATE utf8_general_ci DEFAULT NULL,
											genero VARCHAR(1) COLLATE utf8_general_ci DEFAULT NULL,
											dir_res VARCHAR(200) COLLATE utf8_general_ci DEFAULT NULL,
											cod_mun_res INTEGER(11) DEFAULT '0',
											telefono VARCHAR(50) COLLATE utf8_general_ci DEFAULT NULL,
											cod_mun_nac INTEGER(11) DEFAULT '0',
											fecha_nac DATE DEFAULT NULL,
											cod_estrato INTEGER(11) DEFAULT '0',
											sisben DECIMAL(5,3) DEFAULT '0.000',
											cod_discap INTEGER(11) DEFAULT '0',
											etnia INTEGER(11) DEFAULT '0',
											resguardo INTEGER(11) DEFAULT '0',
											cod_pob_victima INTEGER(11) DEFAULT '0',
											des_dept_nom INTEGER(11) DEFAULT '0',
											nom_mun_desp INTEGER(11) DEFAULT '0',
											cod_inst BIGINT(20) DEFAULT '0',
											cod_sede BIGINT(20) DEFAULT '0',
											sede_con_faltantes INTEGER(11) DEFAULT '0',
											cod_grado INTEGER(11) DEFAULT '0',
											nom_grupo INTEGER(11) DEFAULT '0',
											cod_jorn_est INTEGER(11) DEFAULT '0',
											estado_est VARCHAR(20) COLLATE utf8_general_ci DEFAULT NULL,
											repitente VARCHAR(2) COLLATE utf8_general_ci DEFAULT 'N',
											edad VARCHAR(4) COLLATE utf8_general_ci DEFAULT '0',
											zona_res_est INTEGER(11) DEFAULT '0',
											activo TINYINT(1) DEFAULT '0',
											Tipo_complemento VARCHAR(45) COLLATE utf8_general_ci DEFAULT NULL,
											PRIMARY KEY (id),
											KEY Acel_est1 (num_doc, cod_jorn_est, cod_grado, cod_pob_victima, cod_sede, cod_discap)
											)
										ENGINE=InnoDB
										AUTO_INCREMENT=1 CHARACTER SET 'utf8' COLLATE 'utf8_general_ci';";
		$resCreTabFoc = $Link->query($conCreTabFoc) or die (mysqli_error($Link));;
		if($resCreTabFoc)
		{
			// Crear consulta para ingresar datos de focalización[$mes]
			$conCreFoc = "INSERT INTO focalizacion".$semana."
		 								(tipo_doc, num_doc, ape1, ape2, nom1, nom2, genero, dir_res, cod_mun_res, telefono, cod_mun_nac, fecha_nac, cod_estrato, sisben, cod_discap, etnia, resguardo, cod_pob_victima, des_dept_nom, nom_mun_desp, cod_inst, cod_sede, cod_grado, nom_grupo, cod_jorn_est, estado_est, repitente, edad, zona_res_est, activo, Tipo_complemento) VALUES ". trim($conValFoc, ", ");
			$resCreFoc = $Link->query($conCreFoc) or die("Error al insertar en focalizacion". $semana . ". Linea 607: " . mysqli_error($Link));
			if (!$resCreFoc)
			{
				$respuestaAJAX = [
					"estado"  => 0,
					"mensaje" => "No fue posible crear datos parafocalización."
				];
			}
			else
			{
				$respuestaAJAX = [
					"estado"  => 1,
					"mensaje" => "El proceso se realizó con Éxito"
				];
			}
		}
		else
		{
			$respuestaAJAX = [
				"estado" => 0,
				"mensaje" => "No fue posible crear la tabla de focalización"
			];
			echo json_encode($respuestaAJAX);
			exit;
		}
	} // FIN validación si existe el archivo.
	else
	{
		$respuestaAJAX = [
			"estado" => 0,
			"mensaje" => "No existe archivo para cargar los datos. Por favor intentelo nuevamente."
		];
	}

	echo json_encode($respuestaAJAX);

	/*****************************************************************************************************/

	/*****************************************************************************************************/