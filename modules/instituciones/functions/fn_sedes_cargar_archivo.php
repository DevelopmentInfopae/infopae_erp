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

// Consulta para los codigos de los municipios
$arrayMunicipios = [];
$consultaMunicipios = "SELECT CodigoDANE FROM ubicacion WHERE CodigoDANE LIKE '" . $_SESSION["p_CodDepartamento"]."%';";
$respuestaMunicipios = $Link->query($consultaMunicipios) or die ('Error al consultar los municipios. ' . mysqli_error($Link));
if ($respuestaMunicipios->num_rows > 0) {
	while ($dataMunicipios = $respuestaMunicipios->fetch_assoc()) {
		$arrayMunicipios[] = $dataMunicipios['CodigoDANE'];
	}
}

$zonaPae = '';
$valorZonaPae = '';
if ($_SESSION['p_Municipio'] == "0") {
	$zonaPae = ", Zona_Pae "; 
}

// Se valida si existe el archivo.
if (isset($_FILES["archivoSede"]["name"])){
	// Metadatos del archivo
	$rutaArchivo = $_FILES["archivoSede"]["tmp_name"];
	$tipoArchivo = str_replace("application/", "", $_FILES["archivoSede"]["type"]);

	// Validamos si el archivo es .CSV
	if($tipoArchivo == "vnd.ms-excel" || $tipoArchivo == "text/csv"){
		$fila = 1;
		$linea = 0;
		$filaValidaciones = 1;

		//Abrimos nuestro archivo
		$archivoCSV=fopen($rutaArchivo, "r");
		$separador = (count(fgetcsv($archivoCSV, ",")) > 1 ? ",": ";");

		//Declaración de variable de consulta para las sedes.
		$consultaCrearInstitucion="INSERT INTO instituciones (codigo_inst, nom_inst, cod_mun, tel_int, email_inst, cc_rector) VALUES ";

		//Declaración de variable de consulta para las instituciones.
		$consultaCrearSede="INSERT INTO sedes".$_SESSION["periodoActual"]." (cod_inst, cod_sede, nom_sede, cod_mun_sede, nom_inst, tipo_validacion, Tipo_Complemento, direccion, telefonos, email, id_coordinador, sector, cod_variacion_menu $zonaPae ) VALUES ";
		// var_dump($archivoCSV);
		//Recorremos el archivo para interar los datos.
		while(($datos = fgetcsv($archivoCSV, null, $separador))==true){
			// Codigo jerson
			// validacion datos en la importacion de sedes educativas
			// validacion para que un codigo de institucion no venga vacio o con texto
			if ($datos[0] == " " || $datos[0] == 0) {
				$result = array(
					"estado" => 0, 
					"mensaje" => "El código de Institución no puede estar vacío o contener texto en la fila N° ".($filaValidaciones+1)."."
				);
				exit(json_encode($result));
			}

			// validacion para que el codigo de la sede no venga vacio o con texto 
			if ($datos[1] == " " || $datos[1] == 0) {
				$result = array(
					"estado" => 0, 
					"mensaje" => "El código de Sede no puede estar vacío o contener texto en la fila N° ".($filaValidaciones+1)."."
				);
				exit(json_encode($result));
			}

			// validacion numero entero positivo
			if ($datos[1] < 0 || strrpos($datos[1],",")) {
				$result = array(
					"estado" => 0, 
					"mensaje" => "El código de Sede no puede ser negativo o decimal N° ".($filaValidaciones+1)."."
				);
				exit(json_encode($result));
			}

			// validacion para no crear una sede con un codigo ya existente
			if (in_array($datos[1],$arrayCodigosSedes)) {
				$result = array(
					"estado" => 0, 
					"mensaje" => "El código de Sede ya se encuentra registrado en la fila N° ".($filaValidaciones+1)."."
				);
				exit(json_encode($result));
			}

			// validacion para no crear una sede con nombre en blanco
			$nombreSedeSinEspacios = trim($datos[2]);
			if ($nombreSedeSinEspacios == "") {
				$result = array(
					"estado" => 0,
					"mensaje" => "El nombre de la sede no puede estar vacío en la fila N° " .($filaValidaciones+1).".");
				exit(json_encode($result));
			}

			// validacion para los codigos de municipio 
			if ($_SESSION["p_Municipio"] != 0) {
				if ($datos[3] != $_SESSION["p_Municipio"]) {
					$result = array(
						"estado" => 0,
						"mensaje" => "El Código Municipio no esta comprendido en el municipio del contrato en la fila N° " .($filaValidaciones+1).".");
					exit(json_encode($result));
				}
			}else if ($_SESSION["p_Municipio"] == 0) {
				if (!in_array($datos[3], $arrayMunicipios)) {
					$result = array(
						"estado" => 0,
						"mensaje" => "El Código Municipio no esta comprendido en los municipios del contrato en la fila N° " .($filaValidaciones+1).".");
					exit(json_encode($result));
				}
			}

			// validacion nombre institucion 
			$nombreInstitucion = '';
			if (in_array($datos[0], $arrayCodigosInstituciones)) {
				$consultaNombreInstitucion = "SELECT nom_inst FROM instituciones WHERE codigo_inst = '".$datos[0]."';";
				$respuestaNombreInstitucion = $Link->query($consultaNombreInstitucion) or die ('Error al consultar el nombre de la institucion. ' . mysqli_error($Link));
				if ($respuestaNombreInstitucion->num_rows > 0 ) {
					$dataNombreInstitucion = $respuestaNombreInstitucion->fetch_assoc();
					$nombreInstitucion = $dataNombreInstitucion['nom_inst'];
				}
				$nombreInstitucionBase = trim($nombreInstitucion);
				$nombreInstitucionArchivo = trim($datos[4]);
				if ($nombreInstitucionBase != $nombreInstitucionArchivo) {
					$result = array(
						"estado" => 0,
						"mensaje" => "El Nombre Institucion NO corresponde al código institucion en la fila N° " .($filaValidaciones+1).".");
					exit(json_encode($result));
				}
			}

			if ($zonaPae != "" && isset($datos[13])) {
				$valorZonaPae = ' ,' . "'" . $datos[13] . "'";
			}

			// Valida si existe la institución, sino existe la crea y se apila en el array de código de instituciones.
			if(!in_array($datos[0], $arrayCodigosInstituciones)){
				$consultaCrearInstitucion.="('".$datos[0]."', '".utf8_encode($datos[4])."', '".$datos[3]."', '', '', '0'), ";
			  	$arrayCodigosInstituciones[] = $datos[0];
			}


			// Valida si existe la sede, sino existe se crea y se apila en el array de código de sedes.
			if(!in_array($datos[1], $arrayCodigosSedes)){
				$consultaCrearSede.="('".$datos[0]."', '".$datos[1]."', '".utf8_encode($datos[2])."', '".$datos[3]."', '".utf8_encode($datos[4])."', '".$datos[5]."', '".$datos[6]."', '".$datos[7]."', '".$datos[8]."', '".$datos[9]."', '".$datos[10]."', '".$datos[11]."', '".$datos[12]."'". $valorZonaPae."), ";
			  		$arrayCodigosSedes[] = $datos[1];
			}
			$filaValidaciones++;
		}
		// exit(var_dump($consultaCrearSede));
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
		$fila = 0;
		$filaValidaciones = 1;
		// Se crea la cconfiguración para la lectura del .xlsx
		$lector=\PhpOffice\PhpSpreadsheet\IOFactory::createReader("Xlsx");
		$spreadSheet=$lector->load($rutaArchivo);
		$hoja=$spreadSheet->getActiveSheet();

		$zonaPae = '';
		$valorZonaPae = '';
		if ($_SESSION['p_Municipio'] == "0") {
			$zonaPae = ", Zona_Pae "; 
		}

		//Declaración de variable de consulta para las sedes.
		$consultaCrearInstitucion="INSERT INTO instituciones (codigo_inst, nom_inst, cod_mun, tel_int, email_inst, cc_rector) VALUES ";

		//Declaración de variable de consulta para las instituciones.
		$consultaCrearSede="INSERT INTO sedes". $_SESSION["periodoActual"] ." (cod_inst, cod_sede, nom_sede, cod_mun_sede, nom_inst, tipo_validacion, Tipo_Complemento, direccion, telefonos, email, id_coordinador, sector, cod_variacion_menu $zonaPae) VALUES ";

		//Iteramos para validar. Se itera desde la segunda fila.
		foreach($hoja->getRowIterator(2) as $fila){
			// Obtenemos el código de la institución
			$codigoInstitucion = $hoja->getCellByColumnAndRow(1, $fila->getRowIndex());
			$codigoSede = $hoja->getCellByColumnAndRow(2, $fila->getRowIndex());
			$codigoMunicipio = $hoja->getCellByColumnAndRow(4, $fila->getRowIndex());
			$nombreInstitucion = $hoja->getCellByColumnAndRow(5, $fila->getRowIndex());
			$codigoSedeValidaciones = $hoja->getCellByColumnAndRow(2, $fila->getRowIndex())->getValue();
			$codigoInstitucionValidaciones = $hoja->getCellByColumnAndRow(1, $fila->getRowIndex())->getValue();
			$nombreInstitucionValidaciones = $hoja->getCellByColumnAndRow(5, $fila->getRowIndex())->getValue();
			$codigoMunicipioValidaciones = $hoja->getCellByColumnAndRow(4, $fila->getRowIndex())->getValue();

			if ($codigoInstitucionValidaciones < 0 || strrpos($codigoInstitucionValidaciones,",")  || strrpos($codigoInstitucionValidaciones,".") || $codigoInstitucionValidaciones == '') {
				$result = array(
					"estado" => 0, 
					"mensaje" => "El código de Institución no puede ser negativo, decimal o estar en blanco en la fila N° ".($filaValidaciones+1)."."
				);
				exit(json_encode($result));
			}

			// validar si el codigo de la sede viene negativo o con decimales 
			if ($codigoSedeValidaciones < 0  || strrpos($codigoSedeValidaciones,",")  || strrpos($codigoSedeValidaciones,".") || $codigoSedeValidaciones == '') {
				$result = array(
					"estado" => 0, 
					"mensaje" => "El código de Sede no puede ser negativo, decimal o estar en blanco en la fila N° ".($filaValidaciones+1)."."
				);
				exit(json_encode($result));
			}

			if ($nombreInstitucionValidaciones == '') {
				$result = array(
					"estado" => 0, 
					"mensaje" => "El nombre de Institución no puede estar en blanco en la fila N° ".($filaValidaciones+1)."."
				);
				exit(json_encode($result));
			}

			if (!in_array($codigoMunicipioValidaciones, $arrayMunicipios)) {
				$result = array(
					"estado" => 0, 
					"mensaje" => "El código Municipio no esta comprendido en los municipios del contrato en la fila N° ".($filaValidaciones+1)."."
				);
				exit(json_encode($result));
			}

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
				
				if ($zonaPae != "") {
					// $valorZonaPae = ' ,' . "'" . $datos[13] . "'";
					$valorZonaPae = ' ,' . "'" . $hoja->getCellByColumnAndRow(14, $fila->getRowIndex()) . "'"; 
				}

				$consultaCrearSede .= "('$codigoInstitucion', '$codigoSede', '$nombreSede', '$codigoMunicipio', '$nombreInstitucion', '$tipoValidacion', '$tipoComplemento', '$direccion', '$telefono', '$email', '$idCoordinador', '$sector', '$codigoVariacionMenu' $valorZonaPae), ";
			  		$arrayCodigosSedes[] = $codigoSede;
			}
			$filaValidaciones ++;
		}
		exit(var_dump($consultaCrearSede));
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