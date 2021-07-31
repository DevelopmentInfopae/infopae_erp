<?php
require_once '../../../db/conexion.php';
require_once '../../../config.php';
require "../../../vendor/autoload.php";

use PhpOffice\PhpSpreadsheet\Spreadsheet;

// Consultar todos los códigos de las instituciones.
$arrayCodigos = [];
$consulta = "SELECT codigo_inst FROM instituciones;";
$resultado = $Link->query($consulta) or die ("Unable to execute query.". mysql_error($Link));
if($resultado){
    while($registros = $resultado->fetch_assoc()){
  		$arrayCodigos[] = $registros["codigo_inst"];
    }
}

$condicionMunicipios = "";
if ($_SESSION['p_Municipio'] == 0) {
	$condicionMunicipios = "CodigoDANE like '" .$_SESSION['p_CodDepartamento']. "%'";
}else if ($_SESSION['p_Municipio'] != 0) {
	$condicionMunicipios = "CodigoDANE = '" .$_SESSION['p_Municipio']. "'";
}

$arrayMunicipios = [];
$consultaMunicipios =  "SELECT CodigoDANE FROM ubicacion WHERE " .$condicionMunicipios.";";
$respuestaMunicipios = $Link->query($consultaMunicipios) or die ('Error al consultar los municipios ' .mysqli_error($Link));
if ($respuestaMunicipios->num_rows > 0) {
	while ($dataMunicipios = $respuestaMunicipios->fetch_assoc()) {
		$arrayMunicipios[] = $dataMunicipios['CodigoDANE'];
	}
}

$arrayRectores = [];
$consultaRectores = "SELECT num_doc FROM usuarios WHERE id_perfil = 6;";
$respuestaRectores = $Link->query($consultaRectores) or die ('Error al consultar los rectores ' . mysqli_error($Link));
if ($respuestaRectores->num_rows > 0) {
	while ($dataRectores = $respuestaRectores->fetch_assoc()) {
		$arrayRectores[] = $dataRectores['num_doc'];
	}
}

if (isset($_FILES["archivo"]["name"])){
	$tipoArchivo = str_replace("application/", "", $_FILES["archivo"]["type"]);

	// Validamos si el archivo es .CSV
	if($tipoArchivo == "vnd.ms-excel" || $tipoArchivo == "text/csv"){
		$fila=1;
		$linea=0;
		//Abrimos nuestro archivo
		$archivo=fopen($_FILES["archivo"]["tmp_name"], "r");


		//Recorremos para hacer las respectivas validaciones en la importacion de un archivo csv
		$separador = (count(fgetcsv($archivo, ",")) > 1 ? ",": ";");	
		while(($datos = fgetcsv($archivo, null, $separador))==true){
			$aguja = $datos[0];
			if(in_array($aguja, $arrayCodigos)){
				$result = array(
					"estado" => 0, 
					"mensaje" => "El código de Institución N°: <strong>".$datos[0]."</strong> ya se encuentra registrado en la base de datos."
				);
				exit(json_encode($result));
			}
			if ($datos[0] == " " || $datos[0] == 0) {
				$result = array(
					"estado" => 0, 
					"mensaje" => "El código de Institución no puede estar vacío o contener texto en la fila N° ".($fila+1)."."
				);
				exit(json_encode($result));
			}
			$nombreSinEspacios = trim($datos[1]);
			$caracteres = strlen($nombreSinEspacios);
			if ($caracteres == 0) {
				$result = array(
					"estado" => 0, 
					"mensaje" => "El nombre de Institución no puede estar vacío en la fila N° ".($fila+1)."."
				);
				exit(json_encode($result));
			}
			if (in_array($datos[2], $arrayMunicipios) === false) {
				$result = array(
					"estado" => 0, 
					"mensaje" => "El código del municipio no esta comprendido en los municipios del contrato en la fila N° ".($fila+1)."."
				);
				exit(json_encode($result));
			}
			if ($datos[2] == " ") {
				$result = array(
					"estado" => 0, 
					"mensaje" => "El código del municipio no puede estar vacío en la fila N° ".($fila+1)."."
				);
				exit(json_encode($result));
			}
			if (in_array($datos[5], $arrayRectores) === false) {
				$result = array(
					"estado" => 0, 
					"mensaje" => "La Cedula Rector no esta comprendida en los rectores ya existentes en la fila N° ".($fila+1)."."
				);
				exit(json_encode($result));
			}
			$fila++;
		}
		
		// Recorremos para ingresar los datos
		$fila = 0;
		$consultaCrear = "INSERT INTO instituciones (codigo_inst, nom_inst, cod_mun, tel_int, email_inst, cc_rector) VALUES ";
		$archivo=fopen($_FILES["archivo"]["tmp_name"], "r");
		while(($datos = fgetcsv($archivo, null, $separador))==true){
			if($fila>0){
				$consultaCrear.="(";
				for($columna=0; $columna<=(count($datos)-1); $columna++){
					$consultaCrear.= "'".(!($datos[$columna] == "NULL") ? utf8_encode($datos[$columna]) : "") ."', ";
				}
				$consultaCrear = trim($consultaCrear, ", ") . "), ";
			}
			$fila++;
		}

		// Se ejecuta la consulta para y se devuelve el mensaje del proceso.
		$resultado2 = $Link->query(trim($consultaCrear, ", ")) or die ("Unable to execute query.". mysqli_error($Link));
		if($resultado2){
			$result = array(
				"estado" => 1, 
				"mensaje" => "Los datos se han importado exitosamente."
			);

			// Registro de la Bitácora
			$consultaBitacora = "INSERT INTO bitacora (fecha, usuario, tipo_accion, observacion) VALUES ('". date('Y-m-d H-i-s') ."', '" . $_SESSION["idUsuario"] . "', '30', 'Creó instituciones masivamente mediante archivo con extención <strong>.CSV</strong> ')";
			$Link->query($consultaBitacora) or die ('Unable to execute query. '. mysqli_error($Link));
		} else {
			$result = array(
				"estado" => 0, 
				"mensaje" => "Se ha presentado un error. Por favor comuníquese con el adminstrador del sitio InfoPae."
			);
		}
		//Cerramos el archivo
		fclose($archivo);
	} 

	// validacion cuando el archivo es xlsx
	elseif ($tipoArchivo == "vnd.openxmlformats-officedocument.spreadsheetml.sheet") {
		$filaC = 1;
		$rutaArchivo = $_FILES["archivo"]["tmp_name"];	

		$lector = \PhpOffice\PhpSpreadsheet\IOFactory::createReader("Xlsx");
		$spreadSheet = $lector->load($rutaArchivo);
		$sheet =  $spreadSheet->getActiveSheet();

		//Recorremos para validar instituciones existentes.
		$consultaCrear = "INSERT INTO instituciones (codigo_inst, nom_inst, cod_mun, tel_int, email_inst, cc_rector) VALUES ";
		foreach ($sheet->getRowIterator(2) as $fila) {
			$cellIterator = $fila->getCellIterator("A", "A");
			$cellIterator->setIterateOnlyExistingCells(false);
			foreach ($cellIterator as $celda) {
				if(!is_null($celda)){
					$valor = $celda->getCalculatedValue();
					if(in_array($valor, $arrayCodigos)){
					   $result = array(
							"estado" => 0, 
							"mensaje" => "El código de Institución N°: <strong>".$valor."</strong> ya se encuentra registrado en la base de datos."
						);
						echo json_encode($result);
					  	exit();
					}
					if ($valor == " " || $valor == 0) {
						$result = array(
							"estado" => 0, 
							"mensaje" => "El código de Institución no puede estar vacío o contener texto en la fila N° ".($filaC+1)."."
						);
						exit(json_encode($result));
					}
				}
			}
			$filaC++;
		}
		$filaC = 1;
		foreach ($sheet->getRowIterator(2) as $fila) {
			$cellIterator = $fila->getCellIterator("B", "B");
			$cellIterator->setIterateOnlyExistingCells(false);
			foreach ($cellIterator as $celda) {
				if(!is_null($celda)){
					$valor2 = $celda->getCalculatedValue();
					$nombreSinEspacios = trim($valor2);
					$caracteres = strlen($nombreSinEspacios);
					if($caracteres == 0){
					   $result = array(
							"estado" => 0, 
							"mensaje" => "El nombre de Institución no puede estar vacío en la fila N° ".($filaC+1)."."
						);
						echo json_encode($result);
					  	exit();
					}
				}
			}
			$filaC++;
		}

		$filaC = 1;
		foreach ($sheet->getRowIterator(2) as $fila) {
			$cellIterator = $fila->getCellIterator("C", "C");
			$cellIterator->setIterateOnlyExistingCells(false);
			foreach ($cellIterator as $celda) {
				if(!is_null($celda)){
					$valor3 = $celda->getCalculatedValue();
					if(in_array($valor3, $arrayMunicipios) === false){
					   $result = array(
							"estado" => 0, 
							"mensaje" => "El código del municipio no esta comprendido en los municipios del contrato en la fila N° ".($filaC+1)."."
						);
						echo json_encode($result);
					  	exit();
					}
				}
			}
			$filaC++;
		}

		$filaC = 1;
		foreach ($sheet->getRowIterator(2) as $fila) {
			$cellIterator = $fila->getCellIterator("F", "F");
			$cellIterator->setIterateOnlyExistingCells(false);
			foreach ($cellIterator as $celda) {
				if(!is_null($celda)){
					$valor4 = $celda->getCalculatedValue();
					if(in_array($valor4, $arrayRectores) === false){
					   $result = array(
							"estado" => 0, 
							"mensaje" => "La Cedula Rector no esta comprendida en los rectores ya existentes en la fila N° ".($filaC+1)."."
						);
						echo json_encode($result);
					  	exit();
					}
				}
			}
			$filaC++;
		}

			// Recorremos para almacenar los datos a la BD
			foreach ($sheet->getRowIterator(2) as $fila) {
				$cellIterator = $fila->getCellIterator("A");
				$cellIterator->setIterateOnlyExistingCells(false);
				$consultaCrear.="( ";
				foreach ($cellIterator as $celda) {
					$valor = $celda->getCalculatedValue();
					if(!is_null($celda)){
						$consultaCrear.= "'".$valor."',";
					} else {
						$consultaCrear.= "'',";
					}
				}
				$consultaCrear = trim($consultaCrear, ",") . " ), ";
			}

			// Se ejecuta la consulta para y se devuelve el mensaje del proceso.
			$consultaCrear = trim($consultaCrear, ", ");
			$resultado2 = $Link->query($consultaCrear) or die ("Unable to execute query.". mysql_error($Link));
			if($resultado2){
				$result = array(
					"estado" => 1, 
					"mensaje" => "Los datos se han importado exitosamente."
				);

				// Registro de la Bitácora
				$consultaBitacora = "INSERT INTO bitacora (fecha, usuario, tipo_accion, observacion) VALUES ('". date('Y-m-d H-i-s') ."', '" . $_SESSION["idUsuario"] . "', '30', 'Creó instituciones masivamente mediante archivo con extención <strong>.XLSX</strong> ')";
				$Link->query($consultaBitacora) or die ('Unable to execute query. '. mysqli_error($Link));
			} else {
				$result = array(
					"estado" => 0, 
					"mensaje" => "Se ha presentado un error. Por favor comuníquese con el adminstrador del sitio InfoPae."
				);
			}
		}
	}

	echo json_encode($result);