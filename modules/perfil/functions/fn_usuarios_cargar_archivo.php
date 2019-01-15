<?php
	require_once '../../../db/conexion.php';
	require_once '../../../config.php';
	require_once "../../../vendor/autoload.php";

	use PhpOffice\PhpSpreadsheet\Spreadsheet;

  // Consultar todos los números de los documentos de los usuarios.
  $arrayCodigos = [];
  $consulta = "SELECT num_doc FROM usuarios";
	$resultado = $Link->query($consulta) or die ("Unable to execute query.". mysql_error($Link));
	if($resultado){
    while($registros = $resultado->fetch_assoc()){
  		$arrayCodigos[] = $registros["num_doc"];
    }
	}

	if (isset($_FILES["archivo"]["name"])){
		$rutaArchivo = $_FILES["archivo"]["tmp_name"];
		$tipoArchivo = str_replace("application/", "", $_FILES["archivo"]["type"]);

		// Validamos si el archivo es .CSV
		if($tipoArchivo == "vnd.ms-excel"){
			$fila=0;
			$linea=0;
			//Abrimos nuestro archivo
			$archivo=fopen($rutaArchivo, "r");
			//Recorremos para validar instituciones existentes.
			if(($datos = fgetcsv($archivo, ","))==true) {
				while(($datos = fgetcsv($archivo, ","))==true){
					if($fila>0){
					  if(in_array($datos[0], $arrayCodigos)){
					  	$result = array(
								"estado" => 0, 
								"mensaje" => "El código de Usuario N°: <strong>".$datos[0]."</strong> ya se encuentra registrado en la base de datos."
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
			$consultaCrear = "INSERT INTO usuarios (num_doc, nombre, direccion, cod_mun, telefono, email, id_perfil, Tipo_Usuario, clave, nueva_clave, estado) VALUES ";
			$archivo=fopen($rutaArchivo, "r");
			while(($datos = fgetcsv($archivo, ","))==true){
				if($fila>0){
					$consultaCrear.="( ";

					for($columna=0; $columna<=(count($datos)-1); $columna++){
						$consultaCrear.= "'".(!($datos[$columna] == "NULL") ? $datos[$columna] : "") ."',";
					}

					// Se crea la contraseña para el usuario en cuestión.
				  $numeroDocumento = $datos[0];
					$letraInicial = substr($datos[1], 0, 1);
					$contrasena = sha1($letraInicial . $numeroDocumento);

					$consultaCrear = trim($consultaCrear, ",") . ",'$contrasena','0', '0' ),";
				}
				$fila++;
			}

			// Se ejecuta la consulta para y se devuelve el mensaje del proceso.
			$resultado2 = $Link->query(trim($consultaCrear, ","))/* or die ("Unable to execute query.". mysql_error($Link))*/;
			if($resultado2){
				$result = array(
					"estado" => 1, 
					"mensaje" => "Los datos se han importado exitosamente."
				);

				// Registro de la Bitácora
				$consultaBitacora = "INSERT INTO bitacora (fecha, usuario, tipo_accion, observacion) VALUES ('". date('Y-m-d H-i-s') ."', '" . $SESSION["idUsuario"] . "', '32', 'Creó usuarios masivamente mediante archivo con extención <strong>.CSV</strong> ')";
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

			$lector = \PhpOffice\PhpSpreadsheet\IOFactory::createReader("Xlsx");
			$spreadSheet = $lector->load($rutaArchivo);
			$sheet =  $spreadSheet->getActiveSheet();

			//Recorremos para validar usuarios existentes.
			foreach ($sheet->getRowIterator(2) as $fila) {
				$cellIterator = $fila->getCellIterator("A", "A");
				$cellIterator->setIterateOnlyExistingCells(false);

				foreach ($cellIterator as $celda) {
					if(!is_null($celda)){
						$valor = $celda->getCalculatedValue();
						if(in_array($valor, $arrayCodigos)){
					  	$result = array(
								"estado" => 0, 
								"mensaje" => "El documento de Usuario N°: <strong>".$valor."</strong> ya se encuentra registrado en la base de datos."
							);
							echo json_encode($result);
					  	exit();
					  }
					}
				}

			}

			// Recorremos para almacenar los datos a la BD
			$consultaCrear = "INSERT INTO usuarios (num_doc, nombre, direccion, cod_mun, telefono, email, id_perfil, Tipo_Usuario, clave, nueva_clave, estado) VALUES ";
			foreach ($sheet->getRowIterator(2) as $fila) {
				$cellIterator = $fila->getCellIterator("A");
				$cellIterator->setIterateOnlyExistingCells(false);
				$consultaCrear.="( ";
				foreach ($cellIterator as  $celda) {
					if(!is_null($celda)){
						$consultaCrear.= "'".$celda->getCalculatedValue()."',";
					} else {
						$consultaCrear.= "'',";
					}
				}

			  // Se crea la contraseña para el usuario en cuestión.
			  $numeroDocumento = $sheet->getCellByColumnAndRow(1, $fila->getRowIndex());
				$letraInicial = substr($sheet->getCellByColumnAndRow(2, $fila->getRowIndex()), 0, 1);
				$contrasena = sha1($letraInicial . $numeroDocumento);

				$consultaCrear = trim($consultaCrear, ",") . ",'$contrasena','0', '0' ),";
			}

			// Se ejecuta la consulta para y se devuelve el mensaje del proceso.
			$resultado2 = $Link->query(trim($consultaCrear, ",")); /*or die ("Unable to execute query.". mysql_error($Link));*/
			if($resultado2){
				$result = array(
					"estado" => 1, 
					"mensaje" => "Los datos se han importado exitosamente."
				);

				// Registro de la Bitácora
				$consultaBitacora = "INSERT INTO bitacora (fecha, usuario, tipo_accion, observacion) VALUES ('". date('Y-m-d H-i-s') ."', '" . $_SESSION[""] . "', '32', 'Creó usuarios masivamente mediante archivo con extención <strong>.XLSX</strong> ')";
				$Link->query($consultaBitacora) or die ('Unable to execute query. '. mysqli_error($Link));
			} else {
				$result = array(
					"estado" => 0, 
					"mensaje" => "Se ha presentado un error al intentar guardar los datos. Por favor comuníquese con el adminstrador del sitio InfoPae." . trim($consultaCrear, ",")
				);
			}
		}
	}

	echo json_encode($result);