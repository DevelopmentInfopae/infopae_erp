<?php

require_once '../../../db/conexion.php';
require_once '../../../config.php';

$periodoActual = $_SESSION['periodoActual'];

$tipo_empleado = [
					1 => 'Empleado',
					2 => 'Manipulador',
					3 => 'Contratista',
					4 => 'Transportador',
				];
$tipo_contrato = [
					1 => 'OPS',
					2 => 'Nómina',
					3 => 'Obra labor',
					4 => 'Servicios',
				];

$tipo = $_POST['tipo'] != '' ? $_POST['tipo'] : null;
$mes = $_POST['mes'] != '' ? $_POST['mes'] : null;
$semana_inicial = $_POST['semana_inicial'] != '' ? $_POST['semana_inicial'] : null;
$semana_final = $_POST['semana_final'] != '' ? $_POST['semana_final'] : null;
$municipio = $_POST['municipio'] != '' ? $_POST['municipio'] : null;
$institucion = $_POST['institucion'] != '' ? $_POST['institucion'] : null;
$sede = $_POST['sede'] != '' ? $_POST['sede'] : null;
$tipo_complemento = $_POST['tipo_complemento'] != '' ? $_POST['tipo_complemento'] : null;

$tipo_complemento_datos = []; //TIPOS DE COMPLEMENTOS

$tipo_complemento_consulta = "SELECT * FROM tipo_complemento";
$tipo_complemento_result = $Link->query($tipo_complemento_consulta);
$txt_complementos = '';
if ($tipo_complemento_result->num_rows > 0) {
	while ($tipo_complemento_data = $tipo_complemento_result->fetch_assoc()) {
		$tipo_complemento_datos[] = $tipo_complemento_data['CODIGO'];
		$txt_complementos .= $tipo_complemento_data['CODIGO'].", ";
	}
}
$txt_complementos = trim($txt_complementos, ", ");

$valoresnomina_datos = []; //PARAMETROS SEGÚN RACIONES POR DIA

$valoresnomina_consulta = "SELECT * FROM manipuladoras_valoresnomina";
$valoresnomina_result = $Link->query($valoresnomina_consulta);
if ($valoresnomina_result->num_rows > 0) {
	while ($valoresnomina_data = $valoresnomina_result->fetch_assoc()) {
		$valoresnomina_datos[$valoresnomina_data['tipo_complem']][$valoresnomina_data['tipo']]['min'] = $valoresnomina_data['LimiteInferior'];
		$valoresnomina_datos[$valoresnomina_data['tipo_complem']][$valoresnomina_data['tipo']]['max'] = $valoresnomina_data['LimiteSuperior'];
		$valoresnomina_datos[$valoresnomina_data['tipo_complem']][$valoresnomina_data['tipo']]['tipo'] = $valoresnomina_data['tipo'];
		$valoresnomina_datos[$valoresnomina_data['tipo_complem']][$valoresnomina_data['tipo']]['valor'] = $valoresnomina_data['Valor'];
	}
}

$planilla_dias = [];

$planillas_dias_consulta = "SELECT * FROM planilla_dias WHERE mes >= '".$mes."';";
$resultado_dias_consulta = $Link->query($planillas_dias_consulta);
if ($resultado_dias_consulta->num_rows > 0) {
	$planilla_dias = $resultado_dias_consulta->fetch_assoc();
}
$dias_semanas = []; //DIAS DE LA SEMANA

$planillas_semanas_consulta = "SELECT * FROM planilla_semanas WHERE SEMANA >= '".$semana_inicial."' AND SEMANA <= '".$semana_final."'";
$resultado_semanas_consulta = $Link->query($planillas_semanas_consulta);
if ($resultado_semanas_consulta->num_rows > 0) {
	while ($planilla_semanas = $resultado_semanas_consulta->fetch_assoc()) {
		for ($i=1; $i <=31 ; $i++) { 
			if ($planilla_dias['D'.$i] == $planilla_semanas['DIA']) {
				$dias_semanas[$planilla_semanas['SEMANA']]['D'.$i] = $planilla_semanas['DIA'];
			}
		}
	}
}

$parametrosnomina_datos = []; //PARAMETROS DE NÓMINA
$parametrosnomina_consulta = "SELECT * FROM parametros_nomina";
$parametrosnomina_result = $Link->query($parametrosnomina_consulta);
if ($parametrosnomina_result->num_rows > 0) {
	$parametrosnomina_datos = $parametrosnomina_result->fetch_assoc();
}

$datos = [];
$resultados = [];

if ($tipo == 2) {
	$filas_consulta = [];
	$consulta = "SELECT 
						sedes_cobertura.semana, 
						empleados.nombre, 
						empleados.ID, 
						empleados.Nitcc, 
						empleados.TipoContrato, 
						empleados.tipo, 
						ubicacion.Ciudad, 
						ubicacion.CodigoDANE, 
						sedes.nom_sede, 
						sedes.cod_sede, 
						manipuladoras_sedes.tipo_complem, 
						$txt_complementos 
				FROM manipuladoras_sedes 
					INNER JOIN empleados ON empleados.Nitcc = manipuladoras_sedes.documento
				    INNER JOIN sedes$periodoActual as sedes ON sedes.cod_sede = manipuladoras_sedes.cod_sede
				    INNER JOIN sedes_cobertura ON sedes_cobertura.cod_sede = manipuladoras_sedes.cod_sede
				    INNER JOIN ubicacion ON ubicacion.CodigoDANE = sedes.cod_mun_sede
				WHERE manipuladoras_sedes.estado = 1
				".($mes ? "AND sedes_cobertura.mes = '".$mes."'" : "")."
				".($semana_inicial ? "AND sedes_cobertura.semana >= '".$semana_inicial."'" : "")."
				".($semana_final ? "AND sedes_cobertura.semana <= '".$semana_final."'" : "")."
				".($municipio ? "AND sedes.cod_mun_sede = '".$municipio."'" : "")."
				".($institucion ? "AND sedes.cod_inst = '".$institucion."'" : "")."
				".($sede ? "AND sedes.cod_sede = '".$sede."'" : "")."
				".($tipo_complemento ? "AND manipuladoras_sedes.tipo_complem = '".$tipo_complemento."'" : "")."
				;";
	$resultado = $Link->query($consulta);
	if ($resultado->num_rows > 0) {
		$cnt = 0;
		while ($data = $resultado->fetch_assoc()) {

			$filas_consulta[] = $data;
			if (!isset($datos[$data['Nitcc']][$data['cod_sede']][$data['tipo_complem']])) {
				$datos[$data['Nitcc']][$data['cod_sede']][$data['tipo_complem']]['nombre'] = $data['nombre'];
				$datos[$data['Nitcc']][$data['cod_sede']][$data['tipo_complem']]['Nitcc'] = $data['Nitcc'];
				$datos[$data['Nitcc']][$data['cod_sede']][$data['tipo_complem']]['TipoContrato'] = $data['TipoContrato'];
				$datos[$data['Nitcc']][$data['cod_sede']][$data['tipo_complem']]['Ciudad'] = $data['Ciudad'];
				$datos[$data['Nitcc']][$data['cod_sede']][$data['tipo_complem']]['CodigoDANE'] = $data['CodigoDANE'];
				$datos[$data['Nitcc']][$data['cod_sede']][$data['tipo_complem']]['nom_sede'] = $data['nom_sede'];
				$datos[$data['Nitcc']][$data['cod_sede']][$data['tipo_complem']]['cod_sede'] = $data['cod_sede'];
				$datos[$data['Nitcc']][$data['cod_sede']][$data['tipo_complem']]['tipo_complem'] = $data['tipo_complem'];
				$datos[$data['Nitcc']][$data['cod_sede']][$data['tipo_complem']]['tipo'] = $data['tipo'];
				$datos[$data['Nitcc']][$data['cod_sede']][$data['tipo_complem']]['num_estudiantes'] = $data[$data['tipo_complem']];
				$datos[$data['Nitcc']][$data['cod_sede']][$data['tipo_complem']]['num_registros'] = ($data[$data['tipo_complem']] > 0 ? 1 : 0);
				$datos[$data['Nitcc']][$data['cod_sede']][$data['tipo_complem']]['semanas'][$data['semana']] = $data[$data['tipo_complem']];
				$datos[$data['Nitcc']][$data['cod_sede']][$data['tipo_complem']]['ID'] = $data['ID'];
			} else {
				$datos[$data['Nitcc']][$data['cod_sede']][$data['tipo_complem']]['num_estudiantes'] += $data[$data['tipo_complem']];
				$datos[$data['Nitcc']][$data['cod_sede']][$data['tipo_complem']]['num_registros'] += ($data[$data['tipo_complem']] > 0 ? 1 : 0);
				$datos[$data['Nitcc']][$data['cod_sede']][$data['tipo_complem']]['semanas'][$data['semana']] = $data[$data['tipo_complem']];
			}
			$cnt++;
		}
		foreach ($datos as $nitcc => $sedes) {
			foreach ($sedes as $cod_sede => $complementos) {
				foreach ($complementos as $complemento => $datos) {

					$consulta_existe = "SELECT * FROM pagos_nomina WHERE 
											doc_empleado = '".$nitcc."' 
											AND cod_sede = '".$cod_sede."'
											AND tipo_complem = '".$complemento."'
											AND mes = '".$mes."'
											AND
											(
												(semquin_inicial <= '".$semana_inicial."' AND semquin_final >= '".$semana_inicial."') 
												OR 
												(semquin_inicial <= '".$semana_final."' AND semquin_final >= '".$semana_final."')
											)";
					$resulta_existe = $Link->query($consulta_existe);
					if ($resulta_existe->num_rows > 0) {
						continue;
					}

					$entregas_res = [];
					$entregas_res_consulta = "SELECT 
					SUM(D1) as D1, SUM(D2) as D2, SUM(D3) as D3, SUM(D4) as D4, SUM(D5) as D5, SUM(D6) as D6, SUM(D7) as D7, SUM(D8) as D8, SUM(D9) as D9, SUM(D10) as D10, 
					SUM(D11) as D11, SUM(D12) as D12, SUM(D13) as D13, SUM(D14) as D14, SUM(D15) as D15, SUM(D16) as D16, SUM(D17) as D17, SUM(D18) as D18, SUM(D19) as D19, SUM(D20) as D20, 
					SUM(D21) as D21, SUM(D22) as D22, SUM(D23) as D23, SUM(D24) as D24, SUM(D25) as D25, SUM(D26) as D26, SUM(D27) as D27, SUM(D28) as D28, SUM(D29) as D29, SUM(D30) as D30, 
					SUM(D31) as D31
					FROM entregas_res_".$mes.$periodoActual." WHERE cod_sede = '".$cod_sede."' AND tipo_complem = '".$complemento."';";
					$resultado_entregas_res_consulta = $Link->query($entregas_res_consulta) or die ('Error al consultar entregas_res: '. mysqli_error($Link));
					if ($resultado_entregas_res_consulta->num_rows > 0) {
						$entregas_res = $resultado_entregas_res_consulta->fetch_assoc();
					}
					$semanas_num_dias = [];
					foreach ($dias_semanas as $semana => $diasD) {
						foreach ($diasD as $D => $dia_fecha) {
							if ($entregas_res[$D] > 0) {
								if (!isset($semanas_num_dias[$semana])) {
									$semanas_num_dias[$semana] = 1;
								} else {
									$semanas_num_dias[$semana] += 1;
								}
							}
						}
					}
					// exit($entregas_res_consulta);
					// exit(json_encode($semanas_num_dias));
					$liquidaciones = [];
					$media = ($datos['num_registros'] > 0 ? round($datos['num_estudiantes'] / $datos['num_registros']) : 0);
					$datos['media'] = $media;
					$liquidacion = 0;
					$tipo_liquidacion = "";
					foreach ($datos['semanas'] as $semana => $estudiantes) {
						if (!isset($valoresnomina_datos[$datos['tipo_complem']])) {
							continue;
						}
						foreach ($valoresnomina_datos[$datos['tipo_complem']] as $tipo => $valoresnomina) {
							if ($valoresnomina['min'] <= $estudiantes && $valoresnomina['max'] >= $estudiantes) {
								$vlr_dia = 0;
								if ($tipo == 1) {
									$tipo = 'Día';
									$liquidaciones[$tipo]['tipo'] = $tipo;
									$vlr_dia = $valoresnomina['valor'];
								} else if ($tipo == 2) {
									$tipo = 'Ración';
									$liquidaciones[$tipo]['tipo'] = $tipo;
									$vlr_dia = $valoresnomina['valor'] * $estudiantes;
								}
								$dias = isset($semanas_num_dias[$semana]) ? $semanas_num_dias[$semana] : 0;
								$vlr_semana = $dias * $vlr_dia;
								$liquidaciones[$tipo]['valor'] = isset($liquidaciones[$tipo]['valor']) ? $liquidaciones[$tipo]['valor'] + $vlr_semana : $vlr_semana;
								$liquidaciones[$tipo]['dias_contratados'] = isset($liquidaciones[$tipo]['dias_contratados']) ? $liquidaciones[$tipo]['dias_contratados'] + count($dias_semanas[$semana]) : count($dias_semanas[$semana]);
								$liquidaciones[$tipo]['dias_laborados'] = isset($liquidaciones[$tipo]['dias_laborados']) ? $liquidaciones[$tipo]['dias_laborados'] + $dias : $dias;
								$liquidaciones[$tipo]['valor_base'] = $valoresnomina['valor'];
								$liquidacion += $vlr_semana;
								if (!isset($datos['dias_contratados'])) {
									$datos['dias_contratados'] = count($dias_semanas[$semana]);
								} else {
									$datos['dias_contratados'] += count($dias_semanas[$semana]);
								}
								if (!isset($datos['dias_laborados'])) {
									$datos['dias_laborados'] = $dias;
								} else {
									$datos['dias_laborados'] += $dias;
								}
								if ($tipo_liquidacion != "/".$liquidaciones[$tipo]['tipo']) {
									$tipo_liquidacion .= "/".$liquidaciones[$tipo]['tipo'];
								}
								break;
							}
						}
					}
					unset($datos['semanas']);
					$tipo_liquidacion = trim($tipo_liquidacion, "/");
					$datos['tipo_liquidacion'] = $tipo_liquidacion;
					$datos['liquidaciones'] = $liquidaciones;
					$datos['liquidacion'] = $liquidacion;
					$resultados[] = $datos; 
				}
			}
		}
	}

	$html = '<table class="table" id="box-table-a">
				<thead>
					<tr>
	                    <th style="width: 3.33%;" class="col-sm-1 text-center">
	                      <input type="checkbox" class="i-checks" name="selectVarios" id="selectVarios" value="">
	                    </th>
	                    <th style="width: 13.33%;">Nombre</th>
	                    <th style="width: 8.33%;">Cédula</th>
	                    <th style="width: 8.33%;">Tipo Contrato</th>
	                    <th style="width: 8.33%;">Municipio</th>
	                    <th style="width: 8.33%;">Sede</th>
	                    <th style="width: 8.33%;">Tipo Complemento</th>
	                    <th style="width: 8.33%;">Cobertura Prom.</th>
	                    <th style="width: 8.33%;">Liquida por</th>
	                    <th style="width: 8.33%;">Días contratados</th>
	                    <th style="width: 8.33%;">Días laborados</th>
	                    <th style="width: 8.33%;">Valor a pagar</th>
					</tr>
				</thead>
				<tbody>';
	// exit(json_encode($resultados));
	$num = 0;
	if (count($resultados) > 0) {
		$status = 1;
		foreach ($resultados as $row) {
			if ($row['liquidacion'] > 0) {
				$html .= '<tr>
							<td>
								<input type="checkbox" class="i-checks" name="key['.$num.']" value="'.$num.'">
								<input type="hidden" name="doc_empleado['.$num.']" value="'.$row['Nitcc'].'">
								<input type="hidden" name="id_empleado['.$num.']" value="'.$row['ID'].'">
							</td>
							<td>
								'.$row['nombre'].'
							</td>
							<td>
								'.$row['Nitcc'].'
							</td>
							<td>
								'.$tipo_contrato[$row['TipoContrato']].'
								<input type="hidden" name="tipo_contrato['.$num.']" value="'.$row['TipoContrato'].'">
								<input type="hidden" name="tipo_emp['.$num.']" value="'.$row['tipo'].'">
							</td>
							<td>
								'.$row['Ciudad'].'
								<input type="hidden" name="municipio_sede['.$num.']" value="'.$row['CodigoDANE'].'">
							</td>
							<td>
								'.$row['nom_sede'].'
								<input type="hidden" name="cod_sede['.$num.']" value="'.$row['cod_sede'].'">
							</td>
							<td>
								'.$row['tipo_complem'].'
								<input type="hidden" name="tipo_comple['.$num.']" value="'.$row['tipo_complem'].'">
							</td>
							<td>
								'.$row['media'].'
								<input type="hidden" name="cobertura_prom['.$num.']" value="'.$row['media'].'">
							</td>
							<td>
								'.$row['tipo_liquidacion'];
							$tip_liq = "";
							foreach ($row['liquidaciones'] as $tipo_liq => $lqd) {
								if ($tip_liq != $lqd['tipo'] && $lqd['valor'] > 0) {
									$tip_liq = $lqd['tipo'];
									$html.='<input type="hidden" name="liquida_por['.$num.'][]" value="'.$lqd['tipo'].'">';
									$html.='<input type="hidden" name="valor_base['.$num.'][]" value="'.$lqd['valor_base'].'">';
									$html.='<input type="hidden" name="valor_semana['.$num.'][]" value="'.$lqd['valor'].'">';
									$html.='<input type="hidden" name="dias_contratados_semana['.$num.'][]" value="'.$lqd['dias_contratados'].'">';
									$html.='<input type="hidden" name="dias_laborados_semana['.$num.'][]" value="'.$lqd['dias_laborados'].'">';
								}
							}
				$html .= '</td>
							<td>
								'.$row['dias_contratados'].'
								<input type="hidden" name="dias_contrato['.$num.']" value="'.$row['dias_contratados'].'">
							</td>
							<td>
								<input type="text" name="dias_laborados['.$num.']" class="form-control only_number dias_laborados" value="'.$row['dias_laborados'].'" data-max="'.$row['dias_contratados'].'" data-original="'.$row['dias_laborados'].'">
							</td>
							<td>
								'.$row['liquidacion'].'
								<input type="hidden" name="total_pagado['.$num.']" value="'.$row['liquidacion'].'">
							</td>
						</tr>';
				$num++;
			}
		}
	} else {
		$status = 0;
	}

	$html .= '</tbody>
				<tfoot>
					<tr>
		                <th style="width: 3.33%;" class="col-sm-1 text-center">
		                </th>
		                <th style="width: 13.33%;">Nombre</th>
		                <th style="width: 8.33%;">Cédula</th>
		                <th style="width: 8.33%;">Tipo Contrato</th>
		                <th style="width: 8.33%;">Municipio</th>
		                <th style="width: 8.33%;">Sede</th>
		                <th style="width: 8.33%;">Tipo Complemento</th>
		                <th style="width: 8.33%;">Cobertura Prom.</th>
		                <th style="width: 8.33%;">Liquida por</th>
		                <th style="width: 8.33%;">Días contratados</th>
		                <th style="width: 8.33%;">Días laborados</th>
		                <th style="width: 8.33%;">Valor a pagar</th>
					</tr>
				</tfoot>
			</table>';			
} else if ($tipo == 1 || $tipo == 3) {
	$consulta = "SELECT empleados.*, ubicacion.Ciudad as mun_empleado FROM empleados 
					INNER JOIN ubicacion ON ubicacion.CODIGODANE = empleados.Ciudad
					WHERE estado = 1 AND tipo = '".$tipo."'
				".($municipio ? "Ciudad = '".$municipio."'" : "")."
				;";
	$result = $Link->query($consulta);
	$empleados = [];
	if ($result->num_rows > 0) {
		while ($data = $result->fetch_assoc()) {
			$consulta_existe = "SELECT * FROM pagos_nomina WHERE 
						doc_empleado = '".$data['Nitcc']."'
						AND mes = '".$mes."'
						AND
						(
							(semquin_inicial <= '".$semana_inicial."' AND semquin_final >= '".$semana_inicial."') 
							OR 
							(semquin_inicial <= '".$semana_final."' AND semquin_final >= '".$semana_final."')
						)";
			$resulta_existe = $Link->query($consulta_existe);
			if ($resulta_existe->num_rows > 0) {
				continue;
			} else {
				$empleados[] = $data;
			}
		}
	}

	$html = '<table class="table" id="box-table-a">
				<thead>
					<tr>
	                    <th style="width: 7.14%;" class="col-sm-1 text-center">
	                      <input type="checkbox" class="i-checks" name="selectVarios" id="selectVarios" value="">
	                    </th>
	                    <th style="width: 7.14%;">Nombre</th>
	                    <th style="width: 7.14%;">Cédula</th>
	                    <th style="width: 7.14%;">Tipo Contrato</th>
	                    <th style="width: 7.14%;">Municipio</th>
	                    <th style="width: 7.14%;">Liquida por</th>
	                    <th style="width: 7.14%;">Valor base</th>
	                    <th style="width: 7.14%;">Días contratados</th>
	                    <th style="width: 7.14%;">Días laborados</th>
	                    <th style="width: 7.14%;">Días Incapacidad</th>
	                    <th style="width: 7.14%;">Aux. transporte</th>
	                    <th style="width: 7.14%;">Otros devengados</th>
	                    <th style="width: 7.14%;">Otros deducidos</th>
	                    <th style="width: 7.14%;">Valor a pagar</th>
					</tr>
				</thead>
			<tbody>';
	$num = 1;
	if (count($empleados) > 0) {
		$status = 1;
		foreach ($empleados as $empleado) {

			$dias_laborados = 30;
			$valor_base_mes = $empleado['ValorBaseMes'];
			$valor_base_mes_x_dia = $valor_base_mes / $dias_laborados;
			$aux_transporte = $parametrosnomina_datos['auxilio_trans'];
			$desc_eps = $semana_final == '02' ? $valor_base_mes * $parametrosnomina_datos['desc_eps'] : 0;
			$desc_afp = $semana_final == '02' ? $valor_base_mes * $parametrosnomina_datos['desc_afp'] : 0;
			$total_discount = $desc_eps + $desc_afp;
			if ($semana_inicial == $semana_final) {
				$dias_laborados = $dias_laborados / 2;
				$aux_transporte = $aux_transporte / 2;
				$valor_base_mes = $valor_base_mes / 2;
			}
			$aux_transporte_x_dia = $aux_transporte / $dias_laborados;
			$total_devengado = $aux_transporte + $valor_base_mes - $total_discount;
			$aux_transporte_x_dia = number_format($aux_transporte_x_dia, 2, '.', '');
			$html.='
					<tr>
						<td>
							<input type="checkbox" class="i-checks" name="key['.$num.']" value="'.$num.'">
							<input type="hidden" name="doc_empleado['.$num.']" value="'.$empleado['Nitcc'].'">
							<input type="hidden" name="id_empleado['.$num.']" value="'.$empleado['ID'].'">
						</td>
						<td>
							'.$empleado['Nombre'].'
						</td>
						<td>
							'.$empleado['Nitcc'].'
						</td>
						<td>
							'.$tipo_contrato[$empleado['TipoContrato']].'
							<input type="hidden" name="tipo_contrato['.$num.']" value="'.$empleado['TipoContrato'].'">
							<input type="hidden" name="tipo_emp['.$num.']" value="'.$empleado['tipo'].'">
						</td>
						<td>
							'.$empleado['mun_empleado'].'
							<input type="hidden" name="municipio_sede['.$num.']" value="'.$empleado['Ciudad'].'">
						</td>
						<td>
							Mes
							<input type="hidden" name="liquida_por['.$num.']" value="Mes">
						</td>
						<td> 
							<input type="text" name="valor_base['.$num.']" data-basemesxdia="'.$valor_base_mes_x_dia.'" class="form-control only_number valor_base" value="'.$empleado['ValorBaseMes'].'" readonly>
						</td>
						<td>
							'.$dias_laborados.'
							<input type="hidden" name="dias_contrato['.$num.']" value="'.$dias_laborados.'">
						</td>
						<td>
							<input type="text" name="dias_laborados['.$num.']" class="form-control only_number" value="'.$dias_laborados.'" readonly>
						</td>
						<td>
							<input type="text" name="dias_incapacidad['.$num.']" class="form-control only_number dias_incapacidad" value="0">
						</td>
						<td>
							<span class="transporte_txt">'.$aux_transporte.'</span>
							<input type="hidden" name="auxilio_transporte['.$num.']" value="'.$aux_transporte.'" data-transporteorigin="'.$aux_transporte.'" data-transportexdia="'.$aux_transporte_x_dia.'" class="aux_transporte">
						</td>
						<td>
							0
							<input type="hidden" name="auxilio_extra['.$num.']" value="0">
						</td>
						<td>
							<input type="text" name="otros_deducidos['.$num.']"  data-deducidosorigin="'.$total_discount.'" class="form-control only_number deducidos" value="'.$total_discount.'" readonly>
							<input type="hidden" name="desc_eps['.$num.']" value="'.$desc_eps.'">
							<input type="hidden" name="desc_afp['.$num.']" value="'.$desc_afp.'">
							<input type="hidden" class="desc_auxtrans_incap" name="desc_auxtrans_incap['.$num.']" value="0">
							<input type="hidden" name="otros_devengados['.$num.']" value="0">
						</td>
						<td>
							<input type="text" name="total_pagado['.$num.']" class="form-control only_number total_pagado" value="'.$total_devengado.'" readonly>
						</td>
					</tr>
			';
			$num++;
		}
	} else {
		$status = 0;
	}
	$html .= '</tbody>
				<tfoot>
					<tr>
	                    <th style="width: 7.14%;" class="col-sm-1 text-center">
	                    </th>
	                    <th style="width: 7.14%;">Nombre</th>
	                    <th style="width: 7.14%;">Cédula</th>
	                    <th style="width: 7.14%;">Tipo Contrato</th>
	                    <th style="width: 7.14%;">Municipio</th>
	                    <th style="width: 7.14%;">Liquida por</th>
	                    <th style="width: 7.14%;">Valor base</th>
	                    <th style="width: 7.14%;">Días contratados</th>
	                    <th style="width: 7.14%;">Días laborados</th>
	                    <th style="width: 7.14%;">Días Incapacidad</th>
	                    <th style="width: 7.14%;">Aux. transporte</th>
	                    <th style="width: 7.14%;">Otros devengados</th>
	                    <th style="width: 7.14%;">Otros deducidos</th>
	                    <th style="width: 7.14%;">Valor a pagar</th>
					</tr>
				</tfoot>
			</table>';
}
$data = ['status' => $status, 'html' => $html];
echo json_encode($data);