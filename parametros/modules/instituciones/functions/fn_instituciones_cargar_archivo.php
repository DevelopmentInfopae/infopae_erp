<?php
	require_once '../../../db/conexion.php';
	require_once '../../../config.php';
	require "../../../vendor/autoload.php";

	use PhpOffice\PhpSpreadsheet\Spreadsheet;

	$Link = new mysqli($Hostname, $Username, $Password, $Database);
	if ($Link->connect_errno) {
    echo "Fallo al contenctar a MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
  }
  $Link->set_charset("utf8");

  // Consultar todos los códigos de las instituciones.
  $arrayCodigos = [];
  $consulta = "SELECT codigo_inst FROM instituciones;";
	$resultado = $Link->query($consulta) or die ("Unable to execute query.". mysql_error($Link));
	if($resultado){
    while($registros = $resultado->fetch_assoc()){
  		$arrayCodigos[] = $registros["codigo_inst"];
    }
	}

	if (isset($_FILES["archivo"]["name"])){
		$tipoArchivo = str_replace("application/", "", $_FILES["archivo"]["type"]);

		// Validamos si el archivo es .CSV
		if($tipoArchivo == "vnd.ms-excel"){
			$fila=0;
			$linea=0;
			//Abrimos nuestro archivo
			$archivo=fopen($_FILES["archivo"]["tmp_name"], "r");
			//Recorremos para validar instituciones existentes.
			if(($datos = fgetcsv($archivo, ","))==true) {
				while(($datos = fgetcsv($archivo, ","))==true){
					if($fila>0){
					  if(in_array($datos[0], $arrayCodigos)){
					  	$result = array(
								"estado" => 0, 
								"mensaje" => "El código de Institución N°: <strong>".$datos[0]."</strong> ya se encuentra registrado en la base de datos."
							);
							echo json_encode($result);
					  	exit();
					  }
					}
					$fila++;
				}
			}else{
				$result = array(
					"estado" => 0, 
					"mensaje" => "El archivo se encuentra vacio. Por favor verificar que el archivo contenga datos."
				);
				echo json_encode($result);
				exit();
			}

			// Recorremos para ingresar los datos
			$fila = 0;
			$consultaCrear = "INSERT INTO instituciones (codigo_inst, nom_inst, cod_mun, tel_int, email_inst, cc_rector) VALUES ";
			$archivo=fopen($_FILES["archivo"]["tmp_name"], "r");
			while(($datos = fgetcsv($archivo, ","))==true){
				if($fila>0){
					$consultaCrear.="( ";
					for($columna=0; $columna<=(count($datos)-1); $columna++){
						$consultaCrear.= "'".(!($datos[$columna] == "NULL") ? $datos[$columna] : "") ."',";
					}
					$consultaCrear = trim($consultaCrear, ",") . " ),";
				}
				$fila++;
			}

			// Se ejecuta la consulta para y se devuelve el mensaje del proceso.
			$resultado2 = $Link->query(trim($consultaCrear, ",")) or die ("Unable to execute query.". mysql_error($Link));
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
		} elseif ($tipoArchivo == "vnd.openxmlformats-officedocument.spreadsheetml.sheet") {
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
					}
				}

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
				$consultaCrear = trim($consultaCrear, ",") . " ),";
			}

			// Se ejecuta la consulta para y se devuelve el mensaje del proceso.
			$resultado2 = $Link->query(trim($consultaCrear, ",")) or die ("Unable to execute query.". mysql_error($Link));
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