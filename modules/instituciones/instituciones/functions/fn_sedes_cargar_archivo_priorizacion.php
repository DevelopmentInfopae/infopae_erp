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

			// Consulta para la creacion de sedes_cobertura
			$consultaCrearSedeCobertura="INSERT INTO sedes_cobertura (Ano, cod_inst, cod_sede, mes, semana, cant_Estudiantes, num_est_focalizados, num_est_activos, APS, CAJMRI, CAJTRI, CAJMPS, CAJTPS, RPC, Etario1_APS, Etario1_CAJMRI, Etario1_CAJTRI, Etario1_CAJMPS, Etario1_CAJTPS, Etario1_RPC, Etario2_APS, Etario2_CAJMRI, Etario2_CAJTRI, Etario2_CAJMPS, Etario2_CAJTPS, Etario2_RPC, Etario3_APS, Etario3_CAJMRI, Etario3_CAJTRI, Etario3_CAJMPS, Etario3_CAJTPS, Etario3_RPC) VALUES ";

			// Consulta para la creación de prorizacion[Semana]
			$consultaCrearPriorizacion = "INSERT INTO priorizacion". $semana ." (cod_sede, cant_Estudiantes, num_est_focalizados, APS, CAJMRI, CAJTRI, CAJMPS, CAJTPS, RPC, Etario1_APS, Etario1_CAJMRI, Etario1_CAJTRI, Etario1_CAJMPS, Etario1_CAJTPS, Etario1_RPC, Etario2_APS, Etario2_CAJMRI, Etario2_CAJTRI, Etario2_CAJMPS, Etario2_CAJTPS, Etario2_RPC, Etario3_APS, Etario3_CAJMRI, Etario3_CAJTRI, Etario3_CAJMPS, Etario3_CAJTPS, Etario3_RPC) VALUES ";

			$fila=0;
			//Abrimos nuestro archivo
			$archivo=fopen($rutaArchivo, "r");
			$separador = (count(fgetcsv($archivo, null, ",")) > 1) ? "," : ";";
			//Recorremos para validar instituciones existentes.

			while(($datos = fgetcsv($archivo, null, $separador))==true) {
				// Valores para la consulta de creación de sedes_cobertura
				$consultaCrearSedeCobertura.="('". $_SESSION['periodoActualCompleto'] ."', '". $datos[0] ."', '". $datos[1] ."', '$mes', '$semana', '". $datos[2] ."', '". $datos[3] ."', '". $datos[4] ."', '". $datos[5] ."', '". $datos[6] ."', '". $datos[7] ."', '". $datos[8] ."', '". $datos[9] ."', '". $datos[10] ."', '". $datos[11] ."', '". $datos[12] ."', '". $datos[13] ."', '". $datos[14] ."', '". $datos[15] ."', '". $datos[16] ."', '". $datos[17] ."', '". $datos[18] ."', '". $datos[19] ."','". $datos[20] ."','". $datos[21] ."','". $datos[22] ."','". $datos[23] ."','". $datos[24] ."','". $datos[25] ."','". $datos[26] ."','". $datos[27] ."','". $datos[28] ."'), ";

					// Valores para la consulta de creación de priorización.
					$consultaCrearPriorizacion.="('". $datos[1] ."', '". $datos[2] ."', '". $datos[3] ."', '". $datos[5] ."', '". $datos[6] ."', '". $datos[7] ."', '". $datos[8] ."', '". $datos[9] ."', '". $datos[10] ."', '". $datos[11] ."', '". $datos[12] ."', '". $datos[13] ."', '". $datos[14] ."', '". $datos[15] ."', '". $datos[16] ."', '". $datos[17] ."', '". $datos[18] ."', '". $datos[19] ."','". $datos[20] ."','". $datos[21] ."','". $datos[22] ."','". $datos[23] ."','". $datos[24] ."','". $datos[25] ."','". $datos[26] ."','". $datos[27] ."','". $datos[28] ."'), ";

			}

		 	// Ejecutamos la consulta para sedes cobertura
	  		$resultadoCrearSedeCobertura = $Link->query(trim($consultaCrearSedeCobertura, ", ")) or die("Error al subir las sedes cobertura: ". $Link->error);
	  		if($resultadoCrearSedeCobertura) {
				$consultaCrearTablaPriorizacion = "CREATE TABLE IF NOT EXISTS `priorizacion". $semana ."` (
												`id` INTEGER(11) NOT NULL AUTO_INCREMENT,
												`cod_sede` BIGINT(20) NOT NULL,
												`cant_Estudiantes` INTEGER(11) DEFAULT '0',
												`num_est_focalizados` INTEGER(11) UNSIGNED NOT NULL DEFAULT '0',
												`APS` INTEGER(11) UNSIGNED NOT NULL DEFAULT '0',
												`CAJMRI` INTEGER(11) UNSIGNED NOT NULL DEFAULT '0',
												`CAJTRI` INTEGER(11) UNSIGNED NOT NULL DEFAULT '0',
												`CAJMPS` INTEGER(11) UNSIGNED NOT NULL DEFAULT '0',
												`CAJTPS` INTEGER(11) UNSIGNED NOT NULL DEFAULT '0',
												`RPC` INTEGER(11) UNSIGNED NOT NULL DEFAULT '0',
												`Etario1_APS` INTEGER(10) UNSIGNED NOT NULL DEFAULT '0',
												`Etario1_CAJMRI` INTEGER(10) UNSIGNED NOT NULL DEFAULT '0',
												`Etario1_CAJTRI` INTEGER(10) UNSIGNED NOT NULL DEFAULT '0',
												`Etario1_CAJMPS` INTEGER(10) UNSIGNED NOT NULL DEFAULT '0',
												`Etario1_CAJTPS` INTEGER(10) UNSIGNED NOT NULL DEFAULT '0',
												`Etario1_RPC` INTEGER(10) UNSIGNED NOT NULL DEFAULT '0',
												`Etario2_APS` INTEGER(10) UNSIGNED NOT NULL DEFAULT '0',
												`Etario2_CAJMRI` INTEGER(10) UNSIGNED NOT NULL DEFAULT '0',
												`Etario2_CAJTRI` INTEGER(10) UNSIGNED NOT NULL DEFAULT '0',
												`Etario2_CAJMPS` INTEGER(10) UNSIGNED NOT NULL DEFAULT '0',
												`Etario2_CAJTPS` INTEGER(10) UNSIGNED NOT NULL DEFAULT '0',
												`Etario2_RPC` INTEGER(10) UNSIGNED NOT NULL DEFAULT '0',
												`Etario3_APS` INTEGER(10) UNSIGNED NOT NULL DEFAULT '0',
												`Etario3_CAJMRI` INTEGER(10) UNSIGNED NOT NULL DEFAULT '0',
												`Etario3_CAJTRI` INTEGER(10) UNSIGNED NOT NULL DEFAULT '0',
												`Etario3_CAJMPS` INTEGER(10) UNSIGNED NOT NULL DEFAULT '0',
												`Etario3_CAJTPS` INTEGER(10) UNSIGNED NOT NULL DEFAULT '0',
												`Etario3_RPC` INTEGER(10) UNSIGNED NOT NULL DEFAULT '0',
												PRIMARY KEY (`cod_sede`),
												UNIQUE KEY `id` (`id`)
												)ENGINE=InnoDB
												AUTO_INCREMENT=1 CHARACTER SET 'utf8' COLLATE 'utf8_general_ci';";
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