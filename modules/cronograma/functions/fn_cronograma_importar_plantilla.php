<?php
	require_once '../../../db/conexion.php';
	require_once '../../../config.php';
	require "../../../vendor/autoload.php";

	if (isset($_FILES["archivo"]["name"])  && $_FILES["archivo"]["name"] != ""){
		$tipo_archivo = str_replace("application/", "", $_FILES["archivo"]["type"]);

		if($tipo_archivo == "vnd.ms-excel" || $tipoArchivo == "text/csv") {
			$log_errores = "";
			$cronogramas_guardados = 0;

			$archivo = fopen($_FILES["archivo"]["tmp_name"], "r");
			$separador = (count(fgetcsv($archivo, null, ",")) > 1) ? "," : ";";

			while(($datos = fgetcsv($archivo, null, $separador)) == TRUE) {
				$cronograma_existente = validar_cronograma_existente($Link, $datos[4], $datos[6]);

				if ($cronograma_existente == TRUE) {
				 	$cronograma_guardado = insertar_cronograma($Link, $datos);

				 	if ($cronograma_guardado == TRUE) {
				 		$cronogramas_guardados++;
				 	}
				} else {
					$log_errores .= "Sede: ".$datos[4]." para el mes: ".$datos[6].". <br>";
				}
			}

			if ($cronogramas_guardados > 0) {
				echo json_encode([
					'estado'=>1,
					'mensaje'=>'El cronograma ha sido creado exitosamente',
					'log'=>$log_errores
				]);
			} else {
				echo json_encode([
					'estado'=>0,
					'mensaje'=>'No fue posible crear el cronograma',
					'log'=>$log_errores
				]);
			}
		} else {
			echo json_encode([
				'estado'=>0,
				'mensaje'=>'La extension del archivo no está permitido. La extención permitida es: <strong>.csv</strong>'
			]);
		}
	} else {
		echo json_encode([
			'estado'=>0,
			'mensaje'=>'No existe archivo para la importación'
		]);
	}


	function validar_cronograma_existente($Link, $sede, $mes)
	{
		$c_cronograma_existente = "SELECT * FROM cronograma WHERE cod_sede = '".$sede."' AND mes='".$mes."';";
	    $r_cronograma_existente = $Link->query($c_cronograma_existente) or die("Error al consultar el cronograma existente: ". $Link->error);

	    if ($r_cronograma_existente->num_rows == 0) {
	        return TRUE;
	    } else {
	    	return FALSE;
	    }
	}

	function insertar_cronograma($Link, $datos)
	{
		$mes = (int) $datos[6];
		$semana = (int) $datos[7];

		if ($mes >= 1 && $mes <= 12) {
			$c_crear = "INSERT INTO cronograma (mes, semana, cod_sede, fecha_desde, fecha_hasta, horario) VALUES ('".$mes."', '".$semana."', '".$datos[4]."', '".$datos[8]."', '".$datos[9]."', '".$datos[10]."');";
			$r_crear = $Link->query($c_crear) or die("Error al insertar el cronograma: ". $Link->error);

			if ($r_crear) {
				return TRUE;
			} else {
				return FALSE;
			}
		} else {
			return FALSE;
		}
	}