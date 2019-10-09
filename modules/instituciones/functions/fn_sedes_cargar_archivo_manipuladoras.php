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
		$separador = (count(fgetcsv($archivo, null, ",")) > 1) ? "," : ";";

		while(($datos = fgetcsv($archivo, null, $separador)) == TRUE) {
			$codigoInstitucion = $datos[0];
			$codigoSede = $datos[2];
			$nombreSede = $datos[3];
			$manipuladoraAPS = $datos[5];
			$manipuladoraCAJMPS = $datos[6];
			$manipuladoraCAJMRI = $datos[7];
			$manipuladoraCAJTRI = $datos[8];
			$manipuladoraCAJTPS = $datos[9];
			$cantidadManipuladora = $manipuladoraAPS + $manipuladoraCAJMPS + $manipuladoraCAJMRI + $manipuladoraCAJTRI + $manipuladoraCAJTPS;

			$consulta = "UPDATE sedes$periodoActual
						SET
							cantidad_Manipuladora = '$cantidadManipuladora',
							Manipuladora_APS = '$manipuladoraAPS',
							Manipuladora_CAJMPS = '$manipuladoraCAJMPS',
							Manipuladora_CAJMRI = '$manipuladoraCAJMRI',
							Manipuladora_CAJTRI = '$manipuladoraCAJTRI',
							Manipuladora_CAJTPS = '$manipuladoraCAJTPS'
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