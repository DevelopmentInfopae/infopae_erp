<?php
	set_time_limit (0);

	require_once '../../../db/conexion.php';
	require_once '../../../config.php';
	require_once "../../../vendor/autoload.php";

	use PhpOffice\PhpSpreadsheet\Spreadsheet;

	// Declaración de variables.
	$mes = (isset($_POST["mes"]) && $_POST["mes"] != "") ? $_POST["mes"] : "";
	$semana = (isset($_POST["semana"]) && $_POST["semana"] != "") ? $_POST["semana"] : "";
	$validar = (isset($_POST["validar"]) && $_POST["validar"] == "true") ? 1 : 0;

	/*****************************************************************************************************/
	// Validar si ya existe la semana de focalizacion que se desea subir.
	$consultaFocalizadoSemana = "show tables like 'focalizacion". $semana ."'";
	$resultadoFocalizadoSemana = $Link->query($consultaFocalizadoSemana);
	if($resultadoFocalizadoSemana->num_rows > 0){
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
	$cantidadFilasArchivo = file($rutaArchivo);

	$consultaCantidadFocalizado="SELECT IF(SUM(num_est_focalizados) > 0, SUM(num_est_focalizados), 0) AS 'cantidadFocalizados' FROM sedes_cobertura WHERE semana = '". $semana ."';";
	$resultadoCantidadFocalizado=$Link->query($consultaCantidadFocalizado);
	if($resultadoCantidadFocalizado->num_rows > 0){
		$registroCantidadFocalizado=$resultadoCantidadFocalizado->fetch_assoc();

		if($registroCantidadFocalizado["cantidadFocalizados"] == count($cantidadFilasArchivo) || $registroCantidadFocalizado["cantidadFocalizados"] == count($cantidadFilasArchivo)){
			$respuestaAJAX = [
				"estado" => 0, 
				"mensaje" => "La cantidad de registros focalizados no coincide con la almacenada en la priorización para la semana seleccionada. ".$registroCantidadFocalizado["cantidadFocalizados"]
			];
			echo json_encode($respuestaAJAX);
			exit();
		}
	} else { 
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
	if($resultadoIns->num_rows > 0){
    while($registrosIns = $resultadoIns->fetch_assoc()){
  		$arrayCodigosInstituciones[] = $registrosIns["codigo_inst"];
    }
	}

	// Consulta todos los códigos de las sedes.
	$arrayCodigosSedes = [];
  $consultaSed = "SELECT cod_sede FROM sedes".$_SESSION["periodoActual"].";";
	$resultadoSed = $Link->query($consultaSed) or die ("Unable to execute query.". mysql_error($Link));
	if($resultadoSed->num_rows > 0){
    while($registrosSed = $resultadoSed->fetch_assoc()){
  		$arrayCodigosSedes[] = $registrosSed["cod_sede"];
    }
	}
	
	// Se valida si existe el archivo.
	if (isset($_FILES["archivoFocalizacion"]["name"]) && $_FILES["archivoFocalizacion"]["name"] != ""){

		// Obtengo los dias
		$conDiasMes = "SELECT 
									SUM(
										IF( D1 != '', 1, 0 ) + IF( D2 != '', 1, 0 ) + IF( D3 != '', 1, 0 ) + IF( D4 != '', 1, 0 ) + IF( D5 != '', 1, 0 ) + IF( D6 != '', 1, 0 ) + IF( D7 != '', 1, 0 ) + IF( D8 != '', 1, 0 ) + IF( D9 != '', 1, 0 ) + IF( D10 != '', 1, 0 ) + IF( D11 != '', 1, 0 ) + IF( D12 != '', 1, 0 ) + IF( D13 != '', 1, 0 ) + IF( D14 != '', 1, 0 ) + IF( D15 != '', 1, 0 ) + IF( D16 != '', 1, 0 ) + IF( D17 != '', 1, 0 ) + IF( D18 != '', 1, 0 ) + IF( D19 != '', 1, 0 ) + IF( D20 != '', 1, 0 ) + IF( D21 != '', 1, 0 ) + IF( D22 != '', 1, 0 ) + IF( D23 != '', 1, 0 ) + IF( D24 != '', 1, 0 ) + IF( D25 != '', 1, 0 ) + IF( D26 != '', 1, 0 ) + IF( D27 != '', 1, 0 ) + IF( D28 != '', 1, 0 ) + IF( D29 != '', 1, 0 ) + IF( D30 != '', 1, 0 ) + IF( D31 != '', 1, 0 )
									) AS 'cantidadDias' 
								FROM planilla_dias WHERE mes = '". $mes ."';";
		$resDiasMes = $Link->query($conDiasMes);
		if($resDiasMes->num_rows > 0){
			$regDiasMes = $resDiasMes->fetch_assoc(); 
			$camDias = "";
			$valDias = "";
			for ($i = 1; $i <= $regDiasMes["cantidadDias"]; $i++) {
				$camDias .= "D".$i.", ";
				$valDias .= "'1', ";
			} // Fin iteración para crear campos y valores de dias de entrega_res.
		} // Fin si hay días del mes

		if($validar){
			// Declaracion de variables
			$arrayCodEst = [];
			$arrayCodIns = [];
			$arrayCodSed = [];
			$conValCreEnt = "";
			$conValFoc = "";

			//Abrimos nuestro archivo
			$archivo=fopen($rutaArchivo, "r");
			$separador = (count(fgetcsv($archivo, null, ",")) > 1) ? "," : ";";
			
			// Iteramos el archivo
			while(($datos = fgetcsv($archivo, null, $separador))==true){
				// 	// Valida que el campo Número documento no este vacio ni nulo.
				// 	if($datos[1] == "" || is_null($datos[1])){
				// 		$result = array(
				// 			"estado" => 0, 
				// 			"mensaje" => "El estudiante con código: ". $datos[1] ." no puede tener el campo <strong>Número documento</strong> vacio o nulo. Por favor verifique el archivo e intente nuevamente."
				// 		);
				// 		echo json_encode($result);
			 //  	}

				// 	// Valida que el campo Tipo documento no este vacio ni nulo.
				// 	if($datos[0] == "" || is_null($datos[0])){
				// 		$result = array(
				// 			"estado" => 0, 
				// 			"mensaje" => "El estudiante con código: ". $datos[1] ." no puede tener el campo <strong>Tipo documento</strong> vacio o nulo. Por favor verifique el archivo e intente nuevamente."
				// 		);
				// 		echo json_encode($result);
				//   	exit();
				// 	}

				// 	// Valida que el campo genero no este vacio ni nulo.
				// 	if($datos[7] == "" || is_null($datos[7])){
				// 		$result = array(
				// 			"estado" => 0, 
				// 			"mensaje" => "El estudiante con código: ". $datos[1] ." no puede tener el campo <strong>genero</strong> vacio o nulo. Por favor verifique el archivo e intente nuevamente."
				// 		);
				// 		echo json_encode($result);
				//   	exit();
				// 	}

				// 	// Valida que el campo estrato no esté vacio ni nulo.
				// 	if($datos[13] == "" || is_null($datos[13])){
				// 		$result = array(
				// 			"estado" => 0, 
				// 			"mensaje" => "El estudiante con código: ". $datos[1] ." no puede tener el campo <strong>Código estrato</strong> vacio o nulo. Por favor verifique el archivo e intente nuevamente."
				// 		);
				// 		echo json_encode($result);
				//   	exit();
				// 	}

				// 	// Valida que el campo discapacidad no esté vacio ni nulo.
				// 	if($datos[15] == "" || is_null($datos[15])){
				// 		$result = array(
				// 			"estado" => 0, 
				// 			"mensaje" => "El estudiante con código: ". $datos[1] ." no puede tener el campo <strong>Código discapacidad</strong> vacio o nulo. Por favor verifique el archivo e intente nuevamente."
				// 		);
				// 		echo json_encode($result);
				//   	exit();
				// 	}

				// 	// Valida que el campo etnia no esté vacio ni nulo.
				// 	if($datos[16] == "" || is_null($datos[16])){
				// 		$result = array(
				// 			"estado" => 0, 
				// 			"mensaje" => "El estudiante con código: ". $datos[1] ." no puede tener el campo <strong>Étnia</strong> vacio o nulo. Por favor verifique el archivo e intente nuevamente."
				// 		);
				// 		echo json_encode($result);
				//   	exit();
				// 	}

				// 	// Valida que el campo resguardo no esté vacio ni nulo.
				// 	if($datos[17] == "" || is_null($datos[17])){
				// 		$result = array(
				// 			"estado" => 0, 
				// 			"mensaje" => "El estudiante con código: ". $datos[1] ." no puede tener el campo <strong>Resguardo</strong> vacio o nulo. Por favor verifique el archivo e intente nuevamente."
				// 		);
				// 		echo json_encode($result);
				//   	exit();
				// 	}

				// 	// Valida que el campo población victima no esté vacio ni nulo.
				// 	if($datos[18] == "" || is_null($datos[18])){
				// 		$result = array(
				// 			"estado" => 0, 
				// 			"mensaje" => "El estudiante con código: ". $datos[1] ." no puede tener el campo <strong>Población victima</strong> vacio o nulo. Por favor verifique el archivo e intente nuevamente."
				// 		);
				// 		echo json_encode($result);
				//   	exit();
				// 	}

				// 	// Valida que el campo Código institución no esté vacio ni nulo.
				// 	if($datos[23] == "" || is_null($datos[23])){
				// 		$result = array(
				// 			"estado" => 0, 
				// 			"mensaje" => "El estudiante con código: ". $datos[1] ." no puede tener el campo <strong>Código institución</strong> vacio o nulo. Por favor verifique el archivo e intente nuevamente."
				// 		);
				// 		echo json_encode($result);
				//   	exit();
				// 	}

				// 	// Valida que el campo Código sede no esté vacio ni nulo.
				// 	if($datos[24] == "" || is_null($datos[24])){
				// 		$result = array(
				// 			"estado" => 0, 
				// 			"mensaje" => "El estudiante con código: ". $datos[1] ." no puede tener el campo <strong>Código sede</strong> vacio o nulo. Por favor verifique el archivo e intente nuevamente."
				// 		);
				// 		echo json_encode($result);
				//   	exit();
				// 	}

				// 	// Valida que el campo Código grado no esté vacio ni nulo.
				// 	if($datos[27] == "" || is_null($datos[27])){
				// 		$result = array(
				// 			"estado" => 0, 
				// 			"mensaje" => "El estudiante con código: ". $datos[1] ." no puede tener el campo <strong>Código grado</strong> vacio o nulo. Por favor verifique el archivo e intente nuevamente."
				// 		);
				// 		echo json_encode($result);
				//   	exit();
				// 	}

				// 	// Valida que el campo Código jornada no esté vacio ni nulo.
				// 	if($datos[29] == "" || is_null($datos[29])){
				// 		$result = array(
				// 			"estado" => 0, 
				// 			"mensaje" => "El estudiante con código: ". $datos[1] ." no puede tener el campo <strong>Código jornada</strong> vacio o nulo. Por favor verifique el archivo e intente nuevamente."
				// 		);
				// 		echo json_encode($result);
				//   	exit();
				// 	}

				// 	// Valida que el campo Edad no esté vacio ni nulo.
				// 	if($datos[32] == "" || is_null($datos[32])){
				// 		$result = array(
				// 			"estado" => 0, 
				// 			"mensaje" => "El estudiante con código: ". $datos[1] ." no puede tener el campo <strong>Edad</strong> vacio o nulo. Por favor verifique el archivo e intente nuevamente."
				// 		);
				// 		echo json_encode($result);
				//   	exit();
				// 	}

				// 	// Valida que el campo Zona residencia no esté vacio ni nulo.
				// 	if($datos[33] == "" || is_null($datos[33])){
				// 		$result = array(
				// 			"estado" => 0, 
				// 			"mensaje" => "El estudiante con código: ". $datos[1] ." no puede tener el campo <strong>Zona residencia</strong> vacio o nulo. Por favor verifique el archivo e intente nuevamente."
				// 		);
				// 		echo json_encode($result);
				//   	exit();
				// 	}

				$arrayCodIns[] = $datos[21];
				$arrayCodSed[] = $datos[22];
				$arrayCodEst[$datos[1]][] = $datos[36];

				$conValCreEnt .= "('".$datos[0]."', '".$datos[1]."', '".$datos[2]."', '".$datos[3]."', '".$datos[4]."', '".$datos[5]."', '".$datos[6]."', '".$datos[7]."', '".$datos[8]."', '".$datos[9]."', '".$datos[10]."', '".$datos[11]."', '".$datos[12]."', '".$datos[13]."', '".$datos[14]."', '".$datos[15]."', '".$datos[16]."', '".$datos[17]."', '".$datos[18]."', '".$datos[19]."', '".$datos[20]."', '".$datos[21]."', '".$datos[22]."', '".$datos[23]."', '".$datos[24]."', '".$datos[25]."', '".$datos[26]."', '".$datos[27]."', '".$datos[28]."', '".$datos[29]."', '".$datos[30]."', '".$datos[31]."', '".$datos[32]."', '".$datos[33]."',  '".$datos[34]."',  '".$datos[35]."', '1', '".$datos[36]."',  '".$datos[36]."',  '".$datos[36]."',  '".$datos[36]."',  '".$datos[36]."', '".$datos[36]."'), ";

				$conValFoc .= "('".$datos[0]."', '".$datos[1]."', '".$datos[2]."', '".$datos[3]."', '".$datos[4]."', '".$datos[5]."', '".$datos[6]."', '".$datos[7]."', '".$datos[8]."', '".$datos[9]."', '".$datos[10]."', '".$datos[11]."', '".$datos[12]."', '".$datos[13]."', '".$datos[14]."', '".$datos[15]."', '".$datos[16]."', '".$datos[17]."', '".$datos[18]."', '".$datos[19]."', '".$datos[20]."', '".$datos[21]."', '".$datos[22]."', '".$datos[23]."', '".$datos[24]."', '".$datos[25]."', '".$datos[26]."', '".$datos[27]."', '".$datos[28]."', '".$datos[29]."', '".$datos[30]."', '".$datos[31]."', '".$datos[32]."', '".$datos[33]."',  '".$datos[34]."',  '".$datos[35]."', '1', '".$datos[36]."' ), ";
			}

			// $arrayCodInsUni = array_unique($arrayCodIns);
			// $arrayCodSedUni = array_unique($arrayCodSed);
			
			// // Validar que no exista un mismo complemento para un estudiante determinado.
			// foreach ($arrayCodEst as $i => $codEst) {
			// 	if(count($codEst) > 1){
			// 		foreach ($codEst as $k => $comEst) {
			// 			foreach ($codEst as $k2 => $comEst2) {
			// 				if($comEst == $comEst2 && $k != $k2){
			// 					$result = array(
			// 						"estado" => 0, 
			// 						"mensaje" => "El código de estudiante: ". $i ." complementos duplicados. Por favor verifique el archivo e intente nuevamente"
			// 					);
			// 					echo json_encode($result);
			// 			  	exit();
			// 				}
			// 			}
			// 		}
			// 	}	
			// }

			// // Validar que la institucion existe
			// foreach ($arrayCodInsUni as $i => $codInsDup) {
			// 	if(!in_array($codInsDup, $arrayCodigosInstituciones)){
			//   	$result = array(
			// 			"estado" => 0, 
			// 			"mensaje" => "El código de Institución N°: <strong>".$codInsDup."</strong> NO se encuentra registrado en la base de datos. Por favor ingrese al módulo de intituciones y registrelo para continuar"
			// 		);
			// 		echo json_encode($result);
			//   	exit();
			//   }
			// }

		 //  // validar que la sede existe
		 //  foreach ($arrayCodSedUni as $i => $codSedDup) {
			//   if(!in_array($codSedDup, $arrayCodigosSedes)){
			//   	$result = array(
			// 			"estado" => 0, 
			// 			"mensaje" => "El código de Sede N°: <strong>".$codSedDup."</strong> NO se encuentra registrado en la base de datos. Por favor ingrese al módulo de sedes y registrelo para continuar"
			// 		);
			// 		echo json_encode($result);
			//   	exit();
			//   }
			// }
		} 
		else 
		{
			// //Abrimos nuestro archivo
			// $archivo=fopen($rutaArchivo, "r");
			// $separador = (count(fgetcsv($archivo, null, ",")) > 1) ? "," : ";";
			
			// // Iteramos el archivo
			// while(($datos = fgetcsv($archivo, null, $separador))==true)
			// {
			// 	$conValCreEnt .= "('".$datos[0]."', '".$datos[1]."', '".$datos[2]."', '".$datos[3]."', '".$datos[4]."', '".$datos[5]."', '".$datos[6]."', '".$datos[7]."', '".$datos[8]."', '".$datos[9]."', '".$datos[10]."', '".$datos[11]."', '".$datos[12]."', '".$datos[13]."', '".$datos[14]."', '".$datos[15]."', '".$datos[16]."', '".$datos[17]."', '".$datos[18]."', '".$datos[19]."', '".$datos[20]."', '".$datos[21]."', '".$datos[22]."', '".$datos[23]."', '".$datos[24]."', '".$datos[25]."', '".$datos[26]."', '".$datos[27]."', '".$datos[28]."', '".$datos[29]."', '".$datos[30]."', '".$datos[31]."', '".$datos[32]."', '".$datos[33]."',  '".$datos[34]."',  '".$datos[35]."', '1', '".$datos[36]."',  '".$datos[36]."',  '".$datos[36]."',  '".$datos[36]."',  '".$datos[36]."', '".$datos[36]."', ". trim($valDias, ", ") ."), ";

			// 	$conValFoc .= "('".$datos[0]."', '".$datos[1]."', '".$datos[2]."', '".$datos[3]."', '".$datos[4]."', '".$datos[5]."', '".$datos[6]."', '".$datos[7]."', '".$datos[8]."', '".$datos[9]."', '".$datos[10]."', '".$datos[11]."', '".$datos[12]."', '".$datos[13]."', '".$datos[14]."', '".$datos[15]."', '".$datos[16]."', '".$datos[17]."', '".$datos[18]."', '".$datos[19]."', '".$datos[20]."', '".$datos[21]."', '".$datos[22]."', '".$datos[23]."', '".$datos[24]."', '".$datos[25]."', '".$datos[26]."', '".$datos[27]."', '".$datos[28]."', '".$datos[29]."', '".$datos[30]."', '".$datos[31]."', '".$datos[32]."', '".$datos[33]."',  '".$datos[34]."',  '".$datos[35]."', '1', '".$datos[36]."' ), ";
			// }
		}
		/*****************************************************************************************************/

		// FIN VALIDACIONES

		/*****************************************************************************************************/
			// Validar si ya existe el mes para entregas_res que se desea subir.
			$consultaEntregaResSemana = "show tables like 'entregas_res_". $semana . $_SESSION["periodoActual"] ."'";
			$resultadoEntregaResSemana = $Link->query($consultaEntregaResSemana);
			if($resultadoEntregaResSemana->num_rows == 0)
			{
				// Se crea la tabla entrega_res
				$conCreTabEntRes = "CREATE TABLE `entregas_res_". $mes . $_SESSION["periodoActual"] ."` (
															`id` INT(11) NOT NULL AUTO_INCREMENT,
															`tipo_doc` INT(11) NULL DEFAULT '0',
															`num_doc` VARCHAR(24) NULL DEFAULT '-',
															`tipo_doc_nom` VARCHAR(10) NULL DEFAULT NULL,
															`ape1` VARCHAR(50) NULL DEFAULT '-',
															`ape2` VARCHAR(50) NULL DEFAULT '-',
															`nom1` VARCHAR(50) NULL DEFAULT '-',
															`nom2` VARCHAR(50) NULL DEFAULT '-',
															`genero` VARCHAR(1) NULL DEFAULT '-',
															`dir_res` VARCHAR(200) NULL DEFAULT NULL,
															`cod_mun_res` INT(11) NULL DEFAULT NULL,
															`telefono` VARCHAR(50) NULL DEFAULT NULL,
															`cod_mun_nac` INT(11) NULL DEFAULT '0',
															`fecha_nac` DATE NULL DEFAULT '0000-00-00',
															`cod_estrato` INT(11) NULL DEFAULT '0',
															`sisben` DECIMAL(5,3) NULL DEFAULT '0.000',
															`cod_discap` INT(11) NULL DEFAULT '0',
															`etnia` INT(11) NULL DEFAULT '0',
															`resguardo` INT(11) NULL DEFAULT '0',
															`cod_pob_victima` INT(11) NULL DEFAULT '0',
															`des_dept_nom` INT(11) NULL DEFAULT NULL,
															`nom_mun_desp` INT(11) NULL DEFAULT NULL,
															`cod_sede` BIGINT(20) NULL DEFAULT '0',
															`cod_inst` BIGINT(20) NULL DEFAULT '0',
															`cod_mun_inst` INT(11) NULL DEFAULT NULL,
															`cod_mun_sede` INT(11) NULL DEFAULT NULL,
															`nom_sede` VARCHAR(200) NULL DEFAULT NULL,
															`nom_inst` VARCHAR(200) NULL DEFAULT NULL,
															`cod_grado` INT(11) NULL DEFAULT '0',
															`nom_grupo` INT(11) NULL DEFAULT NULL,
															`cod_jorn_est` INT(11) UNSIGNED NULL DEFAULT '0',
															`estado_est` VARCHAR(20) NULL DEFAULT NULL,
															`repitente` VARCHAR(2) NULL DEFAULT NULL,
															`edad` VARCHAR(4) NULL DEFAULT '0',
															`zona_res_est` INT(11) NULL DEFAULT '0',
															`id_disp_est` INT(11) UNSIGNED NULL DEFAULT '0',
															`TipoValidacion` VARCHAR(50) NULL DEFAULT '-',
															`activo` TINYINT(1) UNSIGNED NULL DEFAULT '0',
															`tipo_complem1` VARCHAR(45) NULL DEFAULT NULL,
															`tipo_complem2` VARCHAR(45) NULL DEFAULT NULL,
															`tipo_complem3` VARCHAR(45) NULL DEFAULT NULL,
															`tipo_complem4` VARCHAR(45) NULL DEFAULT NULL,
															`tipo_complem5` VARCHAR(45) NULL DEFAULT NULL,
															`tipo_complem` VARCHAR(45) NULL DEFAULT NULL,
															`D1` VARCHAR(45) NULL DEFAULT NULL,
															`D2` VARCHAR(45) NULL DEFAULT NULL,
															`D3` VARCHAR(45) NULL DEFAULT NULL,
															`D4` VARCHAR(45) NULL DEFAULT NULL,
															`D5` VARCHAR(45) NULL DEFAULT NULL,
															`D6` VARCHAR(45) NULL DEFAULT NULL,
															`D7` VARCHAR(45) NULL DEFAULT NULL,
															`D8` VARCHAR(45) NULL DEFAULT NULL,
															`D9` VARCHAR(45) NULL DEFAULT NULL,
															`D10` VARCHAR(45) NULL DEFAULT NULL,
															`D11` VARCHAR(45) NULL DEFAULT NULL,
															`D12` VARCHAR(45) NULL DEFAULT NULL,
															`D13` VARCHAR(45) NULL DEFAULT NULL,
															`D14` VARCHAR(45) NULL DEFAULT NULL,
															`D15` VARCHAR(45) NULL DEFAULT NULL,
															`D16` VARCHAR(45) NULL DEFAULT NULL,
															`D17` VARCHAR(45) NULL DEFAULT NULL,
															`D18` VARCHAR(45) NULL DEFAULT NULL,
															`D19` VARCHAR(45) NULL DEFAULT NULL,
															`D20` VARCHAR(45) NULL DEFAULT NULL,
															`D21` VARCHAR(45) NULL DEFAULT NULL,
															`D22` VARCHAR(45) NULL DEFAULT NULL,
															`D23` VARCHAR(45) NULL DEFAULT NULL,
															`D24` VARCHAR(45) NULL DEFAULT NULL,
															`D25` VARCHAR(45) NULL DEFAULT NULL,
															`D26` VARCHAR(45) NULL DEFAULT NULL,
															`D27` VARCHAR(45) NULL DEFAULT NULL,
															`D28` VARCHAR(45) NULL DEFAULT NULL,
															`D29` VARCHAR(45) NULL DEFAULT NULL,
															`D30` VARCHAR(45) NULL DEFAULT NULL,
															`D31` VARCHAR(45) NULL DEFAULT NULL,
															PRIMARY KEY (`id`),
															INDEX `Acel_est1` (`num_doc`, `cod_jorn_est`, `cod_grado`, `cod_pob_victima`, `cod_inst`, `cod_discap`) USING BTREE
															)
														COLLATE='utf8_general_ci'
														ENGINE=InnoDB
														AUTO_INCREMENT=0;";
				$resCreTabEntRes = $Link->query($conCreTabEntRes);
				if($resCreTabEntRes)
				{
					// //Abrimos nuestro archivo
					$linea = 1;
					$archivo=fopen($rutaArchivo, "r");
					$separador = (count(fgetcsv($archivo, null, ",")) > 1) ? "," : ";";

					// Declaracion variable
					$conValCreEnt = "";
					$conCreEnt = "INSERT INTO entregas_res_". $mes . $_SESSION["periodoActual"] ." ( tipo_doc, num_doc, tipo_doc_nom, ape1, ape2, nom1, nom2, genero, dir_res, cod_mun_res, telefono, cod_mun_nac, fecha_nac, cod_estrato, sisben, cod_discap, etnia, resguardo, cod_pob_victima, des_dept_nom, nom_mun_desp, cod_inst, cod_sede, cod_mun_inst, cod_mun_sede, nom_sede, nom_inst, cod_grado, nom_grupo, cod_jorn_est, estado_est, repitente, edad, zona_res_est, id_disp_est, TipoValidacion, activo, tipo_complem1, tipo_complem2, tipo_complem3, tipo_complem4, tipo_complem5, tipo_complem, ". trim($camDias, ", ") .") VALUES ";
					
					// Iteramos el archivo
					while(($datos = fgetcsv($archivo, null, $separador))==true)
					{
						$conValCreEnt .= "('".$datos[0]."', '".$datos[1]."', '".$datos[2]."', '".$datos[3]."', '".$datos[4]."', '".$datos[5]."', '".$datos[6]."', '".$datos[7]."', '".$datos[8]."', '".$datos[9]."', '".$datos[10]."', '".$datos[11]."', '".$datos[12]."', '".$datos[13]."', '".$datos[14]."', '".$datos[15]."', '".$datos[16]."', '".$datos[17]."', '".$datos[18]."', '".$datos[19]."', '".$datos[20]."', '".$datos[21]."', '".$datos[22]."', '".$datos[23]."', '".$datos[24]."', '".$datos[25]."', '".$datos[26]."', '".$datos[27]."', '".$datos[28]."', '".$datos[29]."', '".$datos[30]."', '".$datos[31]."', '".$datos[32]."', '".$datos[33]."',  '".$datos[34]."',  '".$datos[35]."', '1', '".$datos[36]."',  '".$datos[36]."',  '".$datos[36]."',  '".$datos[36]."',  '".$datos[36]."', '".$datos[36]."', ". trim($valDias, ", ") ."), ";

						if(($linea % 1000) == 0)
						{
							$Link->autocommit(FALSE);
							$Link->begin_transaction(MYSQLI_TRANS_START_READ_ONLY);
							$resCreEntres = $Link->query($conCreEnt . $conValCreEnt);
							if($resCreTabEntRes)
							{
								$Link->commit();
								echo "SUCCESS -> ".$linea." -> ".$conCreEnt . $conValCreEnt;
								$conValCreEnt = "";
							}
							else
							{
								$Link->rollback();
								$Link->query("DROP TABLE entregas_res_". $mes . $_SESSION["periodoActual"]);
								// echo "GARRAFAAAAL".$linea;
							}
						}
						else
						{
							if ($linea == count($cantidadFilasArchivo))
							{
								$Link->autocommit(FALSE);
								$Link->begin_transaction(MYSQLI_TRANS_START_READ_ONLY);
								$resCreEntres = $Link->query($conCreEnt . $conValCreEnt);
								if($resCreTabEntRes)
								{
									$Link->commit();
									echo "SUCCESS".$linea;
								}
								else
								{
									$Link->rollback();
									$Link->query("DROP TABLE entregas_res_". $mes . $_SESSION["periodoActual"]);
									echo "GARRAFAAAAL".$linea;
								}
							}
						}

						$linea++;
					} // FIN iteración para guardar registros entregas_res

					echo "SUCCESS";
				} // FIN si se creó la tabla registros_res del mes seleccionado
				else
				{
					echo "no se creo tabla entrega_res";
				}
			} // FIN si existe tabla registros_res del mes seleccion 

			

	} // FIN si existe el archivo enviado por POST
	else
	{
		$respuestaAJAX = [
			"estado" => 0, 
			"mensaje" => "No existe archivo para cargar los datos. Por favor intentelo nuevamente."
		];
	}

	/*****************************************************************************************************/

	/*****************************************************************************************************/