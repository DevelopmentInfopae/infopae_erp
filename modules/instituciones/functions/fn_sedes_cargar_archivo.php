<?php
	require_once '../../../db/conexion.php';
	require_once '../../../config.php';
	require_once "../../../vendor/autoload.php";

	use PhpOffice\PhpSpreadsheet\Spreadsheet;

  // Consultar todos los códigos de las instituciones.
  $arrayCodigosInstituciones = [];
  $consultaIns = "SELECT codigo_inst FROM instituciones;";
	$resultadoIns = $Link->query($consultaIns) or die ("Unable to execute query.". mysql_error($Link));
	if($resultadoIns){
    while($registrosIns = $resultadoIns->fetch_assoc()){
  		$arrayCodigosInstituciones[] = $registrosIns["codigo_inst"];
    }
	}

	// Consulta todos los códigos de las sedes.
	$arrayCodigosSedes = [];
  $consultaSed = "SELECT cod_sede FROM sedes".$_SESSION["periodoActual"].";";
	$resultadoSed = $Link->query($consultaSed) or die ("Unable to execute query.". mysql_error($Link));
	if($resultadoSed){
    while($registrosSed = $resultadoSed->fetch_assoc()){
  		$arrayCodigosSedes[] = $registrosSed["cod_sede"];
    }
	}

	// Se valida si existe el archivo.
	if (isset($_FILES["archivoSede"]["name"])){
		// Metadatos del archivo
		$rutaArchivo = $_FILES["archivoSede"]["tmp_name"];
		$tipoArchivo = str_replace("application/", "", $_FILES["archivoSede"]["type"]);

		// Validamos si el archivo es .CSV
		if($tipoArchivo == "vnd.ms-excel"){
			$fila=0;
			$linea=0;

			//Abrimos nuestro archivo
			$archivoCSV=fopen($rutaArchivo, "r");
			$separador = (count(fgetcsv($archivoCSV, ",")) > 1 ? ",": ";");

			//Declaración de variable de consulta para las sedes.
			$consultaCrearInstitucion="INSERT INTO instituciones (codigo_inst, nom_inst, cod_mun, tel_int, email_inst, cc_rector) VALUES ";
			//Declaración de variable de consulta para las instituciones.
			$consultaCrearSede="INSERT INTO sedes".$_SESSION["periodoActual"]." (cod_inst, cod_sede, nom_sede, cod_mun_sede, nom_inst, tipo_validacion, Tipo_Complemento, direccion, telefonos, email, id_coordinador, sector, cod_variacion_menu, url_foto) VALUES ";

			//Recorremos el archivo para interar los datos.
			while(($datos = fgetcsv($archivoCSV, null, $separador))==true){
					// Valida si existe la institución, sino existe la crea y se apila en el array de código de instituciones.
				  if(!in_array($datos[0], $arrayCodigosInstituciones)){
				  	$consultaCrearInstitucion.="('".$datos[0]."', '".utf8_encode($datos[4])."', '".$datos[3]."', '', '', '0'), ";
			  		$arrayCodigosInstituciones[] = $datos[0];
				  }

				  // Valida si existe la sede, sino existe se crea y se apila en el array de código de sedes.
				  if(!in_array($datos[1], $arrayCodigosSedes)){
				  	$consultaCrearSede.="('".$datos[0]."', '".$datos[1]."', '".utf8_encode($datos[2])."', '".$datos[3]."', '".utf8_encode($datos[4])."', '".$datos[5]."', '".$datos[6]."', '".$datos[7]."', '".$datos[8]."', '".$datos[9]."', '".$datos[10]."', '".$datos[11]."', '".$datos[12]."', ''), ";
			  		$arrayCodigosSedes[] = $datos[1];
				  }
			}

			//ejecutamos la consulta para las instituciones.
			$resultadoCrearInstitucion = $Link->query(trim($consultaCrearInstitucion, ", "));
			if($resultadoCrearInstitucion)
			{
				// Registro de la Bitácora
				$consultaBitacora = "INSERT INTO bitacora (fecha, usuario, tipo_accion, observacion) VALUES ('". date('Y-m-d H-i-s') ."', '" . $_SESSION["idUsuario"] . "', '30', 'Creó instituciones masivamente mediante archivo con extención <strong>.CSV</strong> ')";
				$Link->query($consultaBitacora) or die ('Unable to execute query. '. mysqli_error($Link));
			}

			//ejecutamos la consulta para las sedes.
	  	$resultadoCrearSede = $Link->query(trim($consultaCrearSede, ", "));
	  	if($resultadoCrearSede)
	  	{
				// Registro de la Bitácora
				$consultaBitacora = "INSERT INTO bitacora (fecha, usuario, tipo_accion, observacion) VALUES ('". date('Y-m-d H-i-s') ."', '" . $_SESSION["idUsuario"] . "', '31', 'Creó sedes masivamente mediante archivo con extención <strong>.CSV</strong> ')";
				$Link->query($consultaBitacora) or die ('Unable to execute query. '. mysqli_error($Link));
	  	}

			// Se genera el mensaje
			$result = [
				"estado" => 1,
				"mensaje" => "El proceso fue realizado exitosamente."
			];

			//Cerramos el archivo
			fclose($archivoCSV);
		} elseif ($tipoArchivo == "vnd.openxmlformats-officedocument.spreadsheetml.sheet") {
			// Se crea la cconfiguración para la lectura del .xlsx
			$lector=\PhpOffice\PhpSpreadsheet\IOFactory::createReader("Xlsx");
			$spreadSheet=$lector->load($rutaArchivo);
			$hoja=$spreadSheet->getActiveSheet();

			//Declaración de variable de consulta para las sedes.
			$consultaCrearInstitucion="INSERT INTO instituciones (codigo_inst, nom_inst, cod_mun, tel_int, email_inst, cc_rector) VALUES ";
			//Declaración de variable de consulta para las instituciones.
			$consultaCrearSede="INSERT INTO sedes". $_SESSION["periodoActual"] ." (cod_inst, cod_sede, nom_sede, cod_mun_sede, nom_inst, tipo_validacion, Tipo_Complemento, direccion, telefonos, email, id_coordinador, sector, cod_variacion_menu, url_foto) VALUES ";

			//Iteramos para validar. Se itera desde la segunda fila.
			foreach($hoja->getRowIterator(2) as $fila){
				// Obtenemos el código de la institución
				$codigoInstitucion=$hoja->getCellByColumnAndRow(1, $fila->getRowIndex());
				$codigoSede=$hoja->getCellByColumnAndRow(2, $fila->getRowIndex());
				$codigoMunicipio=$hoja->getCellByColumnAndRow(4, $fila->getRowIndex());
				$nombreInstitucion=$hoja->getCellByColumnAndRow(5, $fila->getRowIndex());

				// Validar si existe la institución, sino existe la crea y se apila en el array de código de instituciones.
				if(!in_array($codigoInstitucion, $arrayCodigosInstituciones)){
					$consultaCrearInstitucion.="('$codigoInstitucion', '$nombreInstitucion', '$codigoMunicipio', '', '', '0'), ";
		  		$arrayCodigosInstituciones[] = $codigoInstitucion;
				}

				// Valida si existe la sede, sino existe se crea y se apila en el array de código de sedes.
				  if(!in_array($codigoSede, $arrayCodigosSedes)){
						$nombreSede=$hoja->getCellByColumnAndRow(3, $fila->getRowIndex());
						$tipoValidacion=$hoja->getCellByColumnAndRow(6, $fila->getRowIndex());
						$tipoComplemento=$hoja->getCellByColumnAndRow(7, $fila->getRowIndex());
						$direccion=$hoja->getCellByColumnAndRow(8, $fila->getRowIndex());
						$telefono=$hoja->getCellByColumnAndRow(9, $fila->getRowIndex());
						$email=$hoja->getCellByColumnAndRow(10, $fila->getRowIndex());
						$idCoordinador=$hoja->getCellByColumnAndRow(11, $fila->getRowIndex());
						$sector=$hoja->getCellByColumnAndRow(12, $fila->getRowIndex());
						$codigoVariacionMenu=$hoja->getCellByColumnAndRow(13, $fila->getRowIndex());

				  	$consultaCrearSede .= "('$codigoInstitucion', '$codigoSede', '$nombreSede', '$codigoMunicipio', '$nombreInstitucion', '$tipoValidacion', '$tipoComplemento', '$direccion', '$telefono', '$email', '$idCoordinador', '$sector', '$codigoVariacionMenu', ''), ";
			  		$arrayCodigosSedes[] = $codigoSede;
				  }
			}

			// Ejecutamos la consulta para las instituciones.
			$resultadoCrearInstitucion = $Link->query(trim($consultaCrearInstitucion, ", "));
			if($resultadoCrearInstitucion){
				// Registro de la Bitácora
				$consultaBitacora = "INSERT INTO bitacora (fecha, usuario, tipo_accion, observacion) VALUES ('". date('Y-m-d H-i-s') ."', '" . $_SESSION["idUsuario"] . "', '30', 'Creó instituciones masivamente mediante archivo con extención <strong>.CSV</strong> ')";
				$Link->query($consultaBitacora) or die ('Unable to execute query. '. mysqli_error($Link));
			}

			// Ejecutamos la consulta para las sedes.
	  	$resultadoCrearSede = $Link->query(trim($consultaCrearSede, ", "));
	  	if($resultadoCrearSede){
				// Registro de la Bitácora
				$consultaBitacora = "INSERT INTO bitacora (fecha, usuario, tipo_accion, observacion) VALUES ('". date('Y-m-d H-i-s') ."', '" . $_SESSION["idUsuario"] . "', '31', 'Creó sedes masivamente mediante archivo con extención <strong>.CSV</strong> ')";
				$Link->query($consultaBitacora) or die ('Unable to execute query. '. mysqli_error($Link));
	  	}

			// Se genera el mensaje
			$result = [
				"estado" => 1,
				"mensaje" => "El proceso fue realizado exitosamente."
			];
		}
	}

	// Devolvemos la respuesta a la petición Ajax.
	echo json_encode($result);