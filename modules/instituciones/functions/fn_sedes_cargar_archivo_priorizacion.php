<?php
	set_time_limit (0);

	require_once '../../../db/conexion.php';
	require_once '../../../config.php';
	require_once "../../../vendor/autoload.php";

	use PhpOffice\PhpSpreadsheet\Spreadsheet;

	// Declaración de variables.
	$mes = (isset($_POST["mes"]) && $_POST["mes"] != "") ? $_POST["mes"] : "";
	$semana = (isset($_POST["semana"]) && $_POST["semana"] != "") ? $_POST["semana"] : "";

	// Validar que los datos no se registran para un mes y una semana existente en los registros de datos.
	$consultaSedesCobertutura = "SELECT  DISTINCT sedc.semana FROM sedes_cobertura sedc WHERE  sedc.mes = '$mes' AND sedc.semana = '$semana'";
	$resultadoSedesCobertura = $Link->query($consultaSedesCobertutura);
	if($resultadoSedesCobertura->num_rows > 0){
		$resultadoAJAX = [
			"estado" => 0,
			"mensaje" => "No es posible agregar la priorización, debido a que ya existe datos para el mes y la semana seleccionada."
		];
		echo json_encode($resultadoAJAX);
		exit;
	}

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
	if (isset($_FILES["archivoPriorizacion"]["name"]) && $_FILES["archivoPriorizacion"]["name"] != "") {
		// Declaración de datos de archivo
		$rutaArchivo = $_FILES["archivoPriorizacion"]["tmp_name"];
		$tipoArchivo = str_replace("application/", "", $_FILES["archivoPriorizacion"]["type"]);

		// Validamos si el archivo es .CSV
		if($tipoArchivo == "vnd.ms-excel" || $tipoArchivo == "text/csv")
		{
			$fila=0;
			//Abrimos nuestro archivo
			$archivo=fopen($rutaArchivo, "r");
			$separador = (count(fgetcsv($archivo, null, ",")) > 1) ? "," : ";";
			//Recorremos para validar instituciones existentes.

			while(($datos = fgetcsv($archivo, null, $separador))==true){
				if($fila>0){
					// validar que la institución existe
				  if(!in_array($datos[0], $arrayCodigosInstituciones)){
				  	$result = array(
							"estado" => 0,
							"mensaje" => "El código de Institución N°: <strong>".$datos[0]."</strong> NO se encuentra registrado en la base de datos. Por favor ingrese al módulo de intituciones y registrelo para continuar"
						);
						echo json_encode($result);
				  	exit();
				  }

				  // validar que la sede existe
				  if(!in_array($datos[1], $arrayCodigosSedes)){
				  	$result = array(
							"estado" => 0,
							"mensaje" => "El código de Sede N°: <strong>".$datos[1]."</strong> NO se encuentra registrado en la base de datos. Por favor ingrese al módulo de sedes y registrelo para continuar"
						);
						echo json_encode($result);
				  	exit();
				  }
				}
				$fila++;
			}

			$cantGruposEtarios = $_SESSION['cant_gruposEtarios'];
			$consultaComplementos = "SELECT CODIGO FROM tipo_complemento ";
			$respuestaComplementos = $Link->query($consultaComplementos) or die ('Error al consultar los complementos' . mysqli_error($Link));
			if ($respuestaComplementos->num_rows > 0) {
				while ($dataComplementos = $respuestaComplementos->fetch_assoc()) {
					$complementos[] = $dataComplementos['CODIGO']; 
				}
			}

			$columnas = '';
			$numero = 4;
			foreach ($complementos as $key => $value) {
				$columnas .= $value.',';
				$numero++;
			}

			for ($i=1; $i <= $cantGruposEtarios ; $i++) { 
				foreach ($complementos as $key1 => $value1) {
					$columnas .= "Etario".$i."_".$value1.',';
					$numero++;
				}
			}
			$columnas = trim($columnas,',');

			// Consulta para la creacion de sedes_cobertura
			$consultaCrearSedeCobertura="INSERT INTO sedes_cobertura (
														Ano, 
														cod_inst, 
														cod_sede, 
														mes, 
														semana, 
														cant_Estudiantes, 
														num_est_focalizados, 
														num_est_activos, 
														$columnas) VALUES ";

			// Consulta para la creación de prorizacion[Semana]
			$consultaCrearPriorizacion = "INSERT INTO priorizacion". $semana ." (cod_sede, cant_Estudiantes, num_est_focalizados, $columnas) VALUES ";

			$fila=0;
			//Abrimos nuestro archivo
			$archivo=fopen($rutaArchivo, "r");
			$separador = (count(fgetcsv($archivo, null, ",")) > 1) ? "," : ";";
			//Recorremos para validar instituciones existentes.

			while(($datos = fgetcsv($archivo, null, $separador))==true) {
				// Valores para la consulta de creación de sedes_cobertura
				$consultaCrearSedeCobertura.="(	'". $_SESSION['periodoActualCompleto'] ."', 
															'". $datos[0] ."', 
															'". $datos[1] ."', 
															'$mes', 
															'$semana', 
															'". $datos[2] ."', 
															'". $datos[3] ."', 
															'". $datos[4] ."', ";

															for ($x=5; $x <= $numero ; $x++) { 
																$consultaCrearSedeCobertura .= "'". $datos[$x] ."',";
															}
															$consultaCrearSedeCobertura = trim($consultaCrearSedeCobertura,',');
															$consultaCrearSedeCobertura .= "),";
															
					// Valores para la consulta de creación de priorización.
					$consultaCrearPriorizacion.="('". $datos[1] ."', 
															'". $datos[2] ."', 
															'". $datos[3] ."', ";

															for ($x=5; $x <= $numero ; $x++) { 
																$consultaCrearPriorizacion .= "'". $datos[$x] ."',";
															}
															$consultaCrearPriorizacion = trim($consultaCrearPriorizacion,',');
															$consultaCrearPriorizacion .= "),";

			}
			
		 	// Ejecutamos la consulta para sedes cobertura
	  		$resultadoCrearSedeCobertura = $Link->query(trim($consultaCrearSedeCobertura, ", ")) or die("Error al subir las sedes cobertura: ". $Link->error);
	  		if($resultadoCrearSedeCobertura) {
				$consultaCrearTablaPriorizacion = "	CREATE TABLE IF NOT EXISTS `priorizacion". $semana ."` (
																	`id` INTEGER(11) NOT NULL AUTO_INCREMENT,
																	`cod_sede` BIGINT(20) NOT NULL,
																	`cant_Estudiantes` INTEGER(11) DEFAULT '0',
																	`num_est_focalizados` INTEGER(11) UNSIGNED NOT NULL DEFAULT '0', ";

																	foreach ($complementos as $key => $value) {
																		$consultaCrearTablaPriorizacion .= $value . " INTEGER(11) UNSIGNED NOT NULL DEFAULT '0',";
																	}

																	for ($i=1; $i <= $cantGruposEtarios ; $i++) { 
																		foreach ($complementos as $key1 => $value1) {
																			$consultaCrearTablaPriorizacion .= "Etario".$i."_".$value1 . " INTEGER(10) UNSIGNED NOT NULL DEFAULT '0',";
																		}
																	}

																	$consultaCrearTablaPriorizacion .= " PRIMARY KEY (`cod_sede`),
																		UNIQUE KEY `id` (`id`)
																		)ENGINE=InnoDB
																		AUTO_INCREMENT=1 CHARACTER SET 'utf8' COLLATE 'utf8_general_ci';";
				// exit(var_dump($consultaCrearTablaPriorizacion));														
				$resultadoCrearTablePriorizacion = $Link->query($consultaCrearTablaPriorizacion) or die ('Unable to execute query. '. mysqli_error($Link));
				if($resultadoCrearTablePriorizacion) {
					// Ejecicón de la consulta para crear priorización
					// echo ($consultaCrearPriorizacion); exit();
					$resultadoCrearPriorizacion = $Link->query(trim($consultaCrearPriorizacion, ", "));
					if($resultadoCrearPriorizacion){
						$respuestaAJAX = [
							"estado" => 1,
							"mensaje" => "La importación fue realizada con éxito!"
						];
					} else {
						$respuestaAJAX = [
							"estado" => 0,
							"mensaje" => "La importación fue realizada con éxito. Sin embargo no se generó los datos de priorización"
						];
					}
				}
			} else {
				$respuestaAJAX = [
					"estado" => 0,
					"mensaje" => "La importación NO fue realizada con éxito."
				];
			}
  		} else {
  			$respuestaAJAX = [
				"estado" => 0,
				"mensaje" => "El archivo seleccionado debe ser un archivo con extensión (.csv)."
			];
  		}
} else {
		$respuestaAJAX = [
			"estado" => 0,
			"mensaje" => "No existe archivo para cargar los datos. Por favor intentelo nuevamente."
		];
}

echo json_encode($respuestaAJAX);