<?php

require_once '../../../db/conexion.php';
require_once '../../../config.php';

$periodoActual = $_SESSION['periodoActual'];
$key = isset($_POST['key']) ? $_POST['key'] : [];
$documento = isset($_POST['doc_empleado']) ? $_POST['doc_empleado'] : null;
$tipo_contrato = isset($_POST['tipo_contrato']) ? $_POST['tipo_contrato'] : null;
$tipo_emp = isset($_POST['tipo_emp']) ? $_POST['tipo_emp'] : null;
$municipio_sede = isset($_POST['municipio_sede']) ? $_POST['municipio_sede'] : null;
$cod_sede = isset($_POST['cod_sede']) ? $_POST['cod_sede'] : null;
$tipo_comple = isset($_POST['tipo_comple']) ? $_POST['tipo_comple'] : null;
$cobertura_prom = isset($_POST['cobertura_prom']) ? $_POST['cobertura_prom'] : null;
$liquida_por = isset($_POST['liquida_por']) ? $_POST['liquida_por'] : null;
$valor_base = isset($_POST['valor_base']) ? $_POST['valor_base'] : null;
$valor_semana = isset($_POST['valor_semana']) ? $_POST['valor_semana'] : null;
$dias_contratados_semana = isset($_POST['dias_contratados_semana']) ? $_POST['dias_contratados_semana'] : null;
$dias_laborados_semana = isset($_POST['dias_laborados_semana']) ? $_POST['dias_laborados_semana'] : null;
$dias_contrato = isset($_POST['dias_contrato']) ? $_POST['dias_contrato'] : null;
$dias_laborados = isset($_POST['dias_laborados']) ? $_POST['dias_laborados'] : null;
$total_pagado = isset($_POST['total_pagado']) ? $_POST['total_pagado'] : null;
$id_empleado = isset($_POST['id_empleado']) ? $_POST['id_empleado'] : null;

//EMPLEADO
$dias_incapacidad = isset($_POST['dias_incapacidad']) ? $_POST['dias_incapacidad'] : null;
$auxilio_transporte = isset($_POST['auxilio_transporte']) ? $_POST['auxilio_transporte'] : null;
$auxilio_extra = isset($_POST['auxilio_extra']) ? $_POST['auxilio_extra'] : null;
$otros_deducidos = isset($_POST['otros_deducidos']) ? $_POST['otros_deducidos'] : null;
$desc_eps = isset($_POST['desc_eps']) ? $_POST['desc_eps'] : null;
$desc_afp = isset($_POST['desc_afp']) ? $_POST['desc_afp'] : null;
$desc_auxtrans_incap = isset($_POST['desc_auxtrans_incap']) ? $_POST['desc_auxtrans_incap'] : null;
$otros_devengados = isset($_POST['otros_devengados']) ? $_POST['otros_devengados'] : null;

$mes = isset($_POST['mes']) ? $_POST['mes'] : null;
$semana_inicial = isset($_POST['semana_inicial']) ? $_POST['semana_inicial'] : null;
$semana_final = isset($_POST['semana_final']) ? $_POST['semana_final'] : null;
$tipo = isset($_POST['tipo']) ? $_POST['tipo'] : null;

$data_documento = [];

$documentos_consulta = "SELECT * FROM documentos WHERE modulo = 'Nomina';";
$resultado_documentos_consulta = $Link->query($documentos_consulta);
if ($resultado_documentos_consulta->num_rows > 0) {
	$data_documento = $resultado_documentos_consulta->fetch_assoc();
}

$prefijo = $data_documento['Tipo'];
$consecutivo = $data_documento['Consecutivo'];

$liquida_por_arr = [
						'Día' => 1,
						'Ración' => 2,
						'Mes' => 3,
						'Factura' => 4,
					];
// exit(json_encode($valor_base));
$nit = 0;
if (count($key) > 0) {
	foreach ($key as $id_row => $row) {

		if ($tipo == 1) {

			if ($nit != $documento[$row]) {
				if ($nit != 0) {
					$consecutivo++;		
				}
				$nit = $documento[$row];
			}
			$insert = "
			INSERT INTO `pagos_nomina`
			(
			`documento`,
			`numero`,
			`Fecha`,
			`tipo_empleado`,
			`mes`,
			`semquin_inicial`,
			`semquin_final`,
			`doc_empleado`,
			`tipo_contrato`,
			`cod_mun_sede`,
			`cod_sede`,
			`tipo_complem`,
			`cobertura`,
			`liquida_por`,
			`dias_contrato`,
			`dias_laborados`,
			`valor_base`,
			`total_pagado`,
			`dias_incapacidad`,
			`auxilio_transporte`,
			`auxilio_extra`,
			`otros_deducidos`,
			`desc_eps`,
			`desc_afp`,
			`desc_auxtrans_incap`,
			`otros_devengados`,
			`id_usuario`
			)
			VALUES
			(
			'".$prefijo."',
			'".$consecutivo."',
			'".date('Y-m-d H:i:s')."',
			'".$tipo_emp[$row]."',
			'".$mes."',
			'".$semana_inicial."',
			'".$semana_final."',
			'".$documento[$row]."',
			'".$tipo_contrato[$row]."',
			'".$municipio_sede[$row]."',
			'0',
			'".$tipo_comple[$row]."',
			'0',
			'".$liquida_por_arr[$liquida_por[$row]]."',
			'".$dias_contrato[$row]."',
			'".$dias_laborados[$row]."',
			'".$valor_base[$row]."',
			'".$total_pagado[$row]."',
			'".$dias_incapacidad[$row]."',
			'".$auxilio_transporte[$row]."',
			'".$auxilio_extra[$row]."',
			'".$otros_deducidos[$row]."',
			'".$desc_eps[$row]."',
			'".$desc_afp[$row]."',
			'".$desc_auxtrans_incap[$row]."',
			'".$otros_devengados[$row]."',
			'".$_SESSION["idUsuario"]."'
			);";
			$Link->query($insert) or die (mysqli_error($Link));

		} else if ($tipo == 2) {

			foreach ($liquida_por[$row] as $num_lqp => $txt_liquida_por) {
				if ($nit != $documento[$row]) {
					if ($nit != 0) {
						$consecutivo++;		
					}
					$nit = $documento[$row];
				}
				$vlr_base = $valor_base[$row][$num_lqp];
				$vlr_semana = $valor_semana[$row][$num_lqp];
				$dias_lab_semana = $dias_laborados_semana[$row][$num_lqp];
				$dias_con_semana = $dias_contratados_semana[$row][$num_lqp];
				// exit(json_encode($vlr_base));
				$insert = "
				INSERT INTO `pagos_nomina`
				(
				`documento`,
				`numero`,
				`Fecha`,
				`tipo_empleado`,
				`mes`,
				`semquin_inicial`,
				`semquin_final`,
				`doc_empleado`,
				`tipo_contrato`,
				`cod_mun_sede`,
				`cod_sede`,
				`tipo_complem`,
				`cobertura`,
				`liquida_por`,
				`dias_contrato`,
				`dias_laborados`,
				`valor_base`,
				`total_pagado`,
				`id_usuario`
				)
				VALUES
				(
				'".$prefijo."',
				'".$consecutivo."',
				'".date('Y-m-d H:i:s')."',
				'".$tipo_emp[$row]."',
				'".$mes."',
				'".$semana_inicial."',
				'".$semana_final."',
				'".$documento[$row]."',
				'".$tipo_contrato[$row]."',
				'".$municipio_sede[$row]."',
				'".$cod_sede[$row]."',
				'".$tipo_comple[$row]."',
				'".$cobertura_prom[$row]."',
				'".$liquida_por_arr[$txt_liquida_por]."',
				'".$dias_con_semana."',
				'".$dias_lab_semana."',
				'".$vlr_base."',
				'".$vlr_semana."',
				'".$_SESSION["idUsuario"]."'
				);";
				$Link->query($insert) or die (mysqli_error($Link));
			}

		}
	}
	$consecutivo++;
	$update_documento = "UPDATE documentos SET Consecutivo = '".$consecutivo."' WHERE id = '".$data_documento['Id']."'";
	$Link->query($update_documento);
	// exit($insert);
	$respuestaAJAX = [
	  'estado' => 1,
	  'mensaje' => 'Registros creados correctamente'
	];
	echo json_encode($respuestaAJAX);
} else {
	$respuestaAJAX = [
	  'estado' => 0,
	  'mensaje' => 'No se seleccionó ninguna liquidación, por favor seleccione al menos una'
	];
	echo json_encode($respuestaAJAX);
}
