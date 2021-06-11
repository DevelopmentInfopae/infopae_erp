<?php
error_reporting(E_ALL);
session_start();
require_once '../db/conexion.php';

$valorContrato;
$periodoActual = $_SESSION['periodoActual'];
$mesActual = date('m');
$porcentajeEjecutado;
$porcentajePorEjecutar;
$cantidadTotal = 0;
$diaActual = date('d');

$consultaValorContrato = " SELECT valorContrato FROM parametros;";
$respuestaValorContrato = $Link->query($consultaValorContrato) or die ('Error al consultar el valor del contrato ' .mysqli($Link));
if ($respuestaValorContrato->num_rows > 0) {
	$dataValorContrato = $respuestaValorContrato->fetch_assoc();
	$valorContrato = $dataValorContrato['valorContrato'];
}

// consulta para traer el valor de la racion 
$consultaValores = "SELECT CODIGO, ValorRacion FROM tipo_complemento;";
$respuestaValores = $Link->query($consultaValores) or die ('Error al consultar los valores ' . mysqli_error($Link));
if ($respuestaValores->num_rows > 0) {
	while ($dataValores = $respuestaValores->fetch_assoc()) {
		$valores[$dataValores['CODIGO']] = $dataValores['ValorRacion'];
	}
}

// ENTREGAS !!! 
	
$consultaMeses = "SELECT DISTINCT(mes) FROM planilla_semanas;";
$respuestaMeses = $Link->query($consultaMeses) or die ('Error al consultar los meses ' .mysqli_error($Link));
if ($respuestaMeses->num_rows > 0) {
	while ($dataMeses = $respuestaMeses->fetch_assoc()) {
		$dias = [];
		$consultaDias = '';
		if ($dataMeses['mes'] != $mesActual) {
			$consultaDias = "SELECT DISTINCT(dia) FROM planilla_semanas WHERE MES = '" .$dataMeses['mes']. "';";
		}else if ($dataMeses['mes'] == $mesActual) {
			$consultaDias = "SELECT DISTINCT(dia) FROM planilla_semanas WHERE MES = '" .$dataMeses['mes']. "' AND DIA <= '" .$diaActual. "';";
		}		
		$respuestaDias = $Link->query($consultaDias) or die('Error al consultar los dias de entrega ' . mysqli_error($Link));
		$D = '';
		if ($respuestaDias->num_rows > 0) {			
			while ($dataDias = $respuestaDias->fetch_assoc()) {
				$dias[] = $dataDias;
			}
			$numeroDias = count($dias);
			for ($i=1; $i <= $numeroDias; $i++) { 
				$D .= ", SUM(D$i) AS D$i ";
			}
			$D = substr($D, 0, -1);
		}else {
			$numeroDias = 0;	
		}

		$consultaEntregas = "SELECT tipo_complem $D FROM entregas_res_".$dataMeses['mes'].$periodoActual." GROUP BY tipo_complem;";
		$respuestaEntregas = $Link->query($consultaEntregas) or die ('Error al consultar los consumos ' . mysqli_error($Link));
		if ($respuestaEntregas->num_rows > 0) {
			
			$totalesPorComplemento = 0;
			$totalGeneral = 0;
			while ($dataEntregas = $respuestaEntregas->fetch_assoc()) {	
				foreach ($valores as $complemento => $valor) {
					$totalesComplemento = 0;
					if ($dataEntregas['tipo_complem'] == $complemento) {
						for ($i=1; $i <= $numeroDias ; $i++) { 
							$totalesComplemento += $dataEntregas["D$i"];
						}
						$totalesPorComplemento += $totalesComplemento * $valor;
							// var_dump($totalesPorComplemento);
					}
				}
				$totalGeneral = $totalesPorComplemento;
			}
			$cantidadTotal += $totalGeneral;
		}
	}
}

// var_dump($cantidadTotal);
// $valorContrato = $valorContrato;
// $cantidadTotal = $cantidadTotal;

$porcentajeEjecutado = ($cantidadTotal / $valorContrato) * 100 ;
$porcentajeEjecutado = $porcentajeEjecutado;
$porcentajePorEjecutar = 100 - $porcentajeEjecutado;
$porcentajeEjecutado = number_format($porcentajeEjecutado, 2);
$porcentajePorEjecutar = number_format($porcentajePorEjecutar, 2);

echo json_encode(array("ejecutado"=>$porcentajeEjecutado, "porEjecutar"=>$porcentajePorEjecutar));