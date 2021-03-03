<?php
	require_once '../../../db/conexion.php';
	require_once '../../../config.php';
	require "../../../vendor/autoload.php";

	use PhpOffice\PhpSpreadsheet\Spreadsheet;

	if (isset($_FILES["archivo"]["name"])){
		$log_errores = "";
		$c_crear = "INSERT INTO cronograma (mes, semana, cod_sede, fecha_desde, fecha_hasta, horario) VALUES ";

		$tipo_archivo = str_replace("application/", "", $_FILES["archivo"]["type"]);

		if($tipo_archivo == "vnd.ms-excel") {
			//Abrimos nuestro archivo
			$archivo = fopen($_FILES["archivo"]["tmp_name"], "r");

			//Recorremos para validar instituciones existentes.
			$separador = (count(fgetcsv($archivo, ",")) > 1 ? ",": ";");

			$registros = FALSE;
			while(($datos = fgetcsv($archivo, null, $separador))==true) {
				if (validar_cronograma_existente($Link, $datos[2], $datos[4])) {
					$c_crear .= "('".$datos[4]."', '".$datos[5]."', '".$datos[2]."', '".$datos[6]."', '".$datos[7]."', '".$datos[8]."'), ";
					$registros = TRUE;
				} else {
					$log_errores .= "Sede: ".$datos[2]." para el mes: ".$datos[4].". <br>";
				}
			}

			echo insertar_cronograma($Link, trim($c_crear, ", "), $log_errores, $registros);
		} else {
			echo "xlsx";
		}
	} else {
		echo "No existe archivo";
	}


	function validar_cronograma_existente($Link, $sede, $mes)
	{
		$c_cronograma_existente = "SELECT * FROM cronograma WHERE cod_sede = '".$sede."' AND mes='".$mes."';";
	    $r_cronograma_existente = $Link->query($c_cronograma_existente) or die("Error al consultar el cronograma existente: ". $Link->error);

	    if ($r_cronograma_existente->num_rows == 0) {
	        return 1;
	    } else {
	    	return 0;
	    }
	}

	function insertar_cronograma($Link, $consulta, $log_errores, $registros)
	{
		if (!$registros) {
			return json_encode([
		        "estado"=>0,
		        "mensaje"=>"No fue posible crear el cronograma.",
		        "log" => $log_errores
		    ]);
		} else {
			$r_crear = $Link->query($consulta) or die("Error al insertar el cronograma: ". $Link->error);
			if ($r_crear) {
			    return json_encode([
			        "estado"=>1,
			        "mensaje"=>"El cronograma ha sido creado exitosamente.",
			        "log" => $log_errores
			    ]);
			} else {
			    return json_encode([
			        "estado"=>0,
			        "mensaje"=>"No fue posible crear el cronograma."
			    ]);
			}
		}

	}