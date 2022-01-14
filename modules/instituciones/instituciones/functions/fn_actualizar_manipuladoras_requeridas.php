<?php
	require_once '../../../db/conexion.php';
	require_once '../../../config.php';
	$periodoActual = $_SESSION["periodoActual"];

	$tipo_complemento_consulta = "SELECT * FROM tipo_complemento";
	$tipo_complemento_result = $Link->query($tipo_complemento_consulta);
	$tipo_complemento_datos = [];
	$txt_complementos = '';
	if ($tipo_complemento_result->num_rows > 0) {
		while ($tipo_complemento_data = $tipo_complemento_result->fetch_assoc()) {
			$tipo_complemento_datos[] = $tipo_complemento_data['CODIGO'];
			$txt_complementos .= $tipo_complemento_data['CODIGO'].", ";
		}
	}
	$txt_complementos = trim($txt_complementos, ", ");


	$parametros_consulta = "SELECT * FROM parametros_manipuladoras";
	$parametros_result = $Link->query($parametros_consulta);
	$parametros_datos = [];
	$cnt = 0;
	if ($parametros_result->num_rows > 0) {
		while ($parametros_data = $parametros_result->fetch_assoc()) {
			$parametros_datos[$parametros_data['tipo_complem']][$cnt]['min'] = $parametros_data['limite_inferior'];
			$parametros_datos[$parametros_data['tipo_complem']][$cnt]['max'] = $parametros_data['limite_superior'];
			$parametros_datos[$parametros_data['tipo_complem']][$cnt]['cant_manipuladora'] = $parametros_data['cant_manipuladora'];
			$cnt++;
		}
	}

	$consulta = "SELECT cod_sede, semana, cant_estudiantes, ".$txt_complementos."
	FROM sedes_cobertura as sc1
	WHERE id IN (SELECT MAX(id) AS id
	             FROM sedes_cobertura as sc2
	             WHERE sc2.cod_sede = sc1.cod_sede
	             GROUP BY cod_sede);";

	$result = $Link->query($consulta) or die ('Error al consultar, revisar tabla tipo_complemento: '. mysqli_error($Link));
	$datos = [];
	if ($result->num_rows > 0) {
		while ($data = $result->fetch_assoc()) {
			foreach ($tipo_complemento_datos as $key => $tipo_complemento) {
				if (isset($parametros_datos[$tipo_complemento])) {
					foreach ($parametros_datos[$tipo_complemento] as $key => $parametro) {
						if (isset($data[$tipo_complemento]) && $parametro['min'] <= $data[$tipo_complemento] && $parametro['max'] >= $data[$tipo_complemento]) {
							$datos[$data['cod_sede']][$tipo_complemento] = $parametro['cant_manipuladora'];
						}
					}
				}
			}
		}
	}

	$limpiar = "UPDATE sedes$periodoActual SET
		cantidad_Manipuladora = 0,
	    Manipuladora_APS = 0,
	    Manipuladora_CAJMPS = 0,
	    Manipuladora_CAJMRI = 0,
	    Manipuladora_CAJTRI = 0,
	    Manipuladora_CAJTPS = 0,
	    Manipuladora_RPC = 0;";
	$Link->query($limpiar) or die ('Error al limpiar: '. mysqli_error($Link));

	foreach ($datos as $cod_sede => $complementos_manipuladoras) {
		$total_sede = 0;
		$sql = "UPDATE sedes$periodoActual SET ";
		foreach ($complementos_manipuladoras as $complemento => $manipuladoras) {
			$sql .= " Manipuladora_".$complemento." = '".$manipuladoras."', ";
			$total_sede += $manipuladoras;
		}
		$sql.=" cantidad_Manipuladora = '".$total_sede."' WHERE cod_sede = ".$cod_sede;
		$Link->query($sql) or die ('Error al actualizar : '. mysqli_error($Link));
	}

	echo "1";
