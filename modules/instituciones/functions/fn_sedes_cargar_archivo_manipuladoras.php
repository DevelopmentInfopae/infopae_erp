<?php
require_once '../../../db/conexion.php';
require_once '../../../config.php';
require_once "../../../vendor/autoload.php";

if (isset($_FILES["archivoManipuladoras"]["name"]) && $_FILES["archivoManipuladoras"]["name"] != "") {
	$periodoActual = $_SESSION["periodoActual"];

	// Declaración de datos de archivo
	$rutaArchivo = $_FILES["archivoManipuladoras"]["tmp_name"];
	$tipoArchivo = str_replace("application/", "", $_FILES["archivoManipuladoras"]["type"]);

	if($tipoArchivo == "vnd.ms-excel" || $tipoArchivo == "text/csv") {
		//Abrimos nuestro archivo
		$archivo=fopen($rutaArchivo, "r");

		$resComp = $Link->query(" SELECT CODIGO FROM tipo_complemento ORDER BY CODIGO ");
		if ($resComp->num_rows > 0) {
			while ($dataComp = $resComp->fetch_object()) {
				$complementos[] = $dataComp;
			}
		}

		$separador = (count(fgetcsv($archivo, null, ",")) > 1) ? "," : ";";
		while(($datos = fgetcsv($archivo, null, $separador)) == TRUE) { 
			$codigoInstitucion = $datos[0];
			$codigoSede = $datos[2];
			$nombreSede = $datos[3];
			$indice = 5;
			$manipuladora['total'] = 0;
			$set = '';
			foreach ($complementos as $key => $value) {
				$manipuladora[$value->CODIGO] = $datos[$indice];
				$set .= " Manipuladora_".$value->CODIGO. " = " .$datos[$indice]. ", ";
				$manipuladora['total'] += $datos[$indice];
				$indice++;
			}
			$set .= " cantidad_Manipuladora = " .$manipuladora['total']. " ";
			$consulta = "UPDATE sedes$periodoActual
							SET $set
						WHERE cod_inst = '$codigoInstitucion' AND cod_sede = '$codigoSede'";
			$manipuladoras_actualizadas = $Link->query($consulta);
			if ($manipuladoras_actualizadas === FALSE) {
				$respuestaAjax = [
					'estado' => 0,
					'mensaje' => 'Sucedió un error al cargar los datos para la sede: '. $nombreSede
				];
				echo json_encode($respuestaAjax);
				exit();
			}
		}

		$respuestaAjax = [
			'estado'=>1,
			'mensaje'=>'Los datos de manipuladoras han sido cargados exitosamente.'
		];

	} else {
		$respuestaAjax = [
			'estado'=>0,
			'mensaje'=>'La extension del archivo no está permitido. La extención permitida es: <strong>.csv</strong>'
		];
	}
}

echo json_encode($respuestaAjax);