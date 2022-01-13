<?php
error_reporting(E_ALL);
session_start();
require_once '../db/conexion.php';

$bandera = 0;
$actualizar = 0;
$barras = array();
$entregas = array();
$labelsSemanas = array();
$barrasTotalesMes = array();
$barrasTotalesSemana = array();
$entregasTotalesSemana = array();
$valores = [];
date_default_timezone_set('America/Bogota');
$mesActual = date('m');
$diaActual = date('d');
$indiceDiaActual = 0;
$entregasMesComplem = [];

// Buscar el día actual en planilla dias para saber a que D corresponde
$consulta = "select * from planilla_dias where mes = \"$mesActual\"";
$resultado = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));
if($resultado->num_rows >= 1){
	$row = $resultado->fetch_assoc();
	for($iD = 1; $iD <= 31; $iD++){
		if($row["D$iD"] == $diaActual){
			$indiceDiaActual = $iD;
		}
	}
}

if(isset($_POST['actualizar']) && $_POST['actualizar'] != ''){ $actualizar = $_POST['actualizar']; }

$rutaArchivo = "arrays_ejecutado.txt";

if($actualizar == 0){
	$contenido = '';
	if($file = fopen($rutaArchivo, "r")){
		while(!feof($file)) {
			$contenido .=fgets($file);
		}
		fclose($file);
		if($contenido != ''){
			echo $contenido;
		}
		else{
			$bandera++;
		}
	}else{
		$bandera++;
	}
}

if($actualizar == 1 || $bandera > 0){
	$periodoActual = $_SESSION['periodoActual'];

	// consulta para traer el valor de la racion 
	$consultaValores = "SELECT CODIGO, ValorRacion FROM tipo_complemento;";
	$respuestaValores = $Link->query($consultaValores) or die ('Error al consultar los valores ' . mysqli_error($Link));
	if ($respuestaValores->num_rows > 0) {
		while ($dataValores = $respuestaValores->fetch_assoc()) {
			$valores[$dataValores['CODIGO']] = $dataValores['ValorRacion'];
		}
	}

	// var_dump($diaActual);
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
			// echo $consultaEntregas;
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
				$datoGeneral[] = $totalGeneral;
			}
		}
	}

	// var_dump($datoGeneral);

    // Se va a recoger la información de planilla_dias para saber en que días se hicieron las entregas.
    $meses = array();
    $consulta = " select * from planilla_dias ";
    $resultado = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));
    if($resultado->num_rows >= 1){
        while($row = $resultado->fetch_assoc()){
            $meses[] = $row;
        }
    }

    // Se correra el array para validar hasta donde estan creadas las tablas de entregas res.
	$mesesEntregados = array();
	$labelsMes = [];
    foreach ($meses as $mes) {
			$mesConsulta = $mes['mes'];
			$consulta = " show tables like 'entregas_res_$mesConsulta$periodoActual' ";
			$resultado = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));
			if($resultado->num_rows >= 1){
				$mesesEntregados[] = $mes['mes'];
			}
			else{
				break;
			}
		}

	foreach ($mesesEntregados as $key => $mes) {
		if ($mes == '01') {
			$labelsMes[] = 'Enero'; 
		}
		if ($mes == '02') {
			$labelsMes[] = 'Febrero'; 
		}
		if ($mes == '03') {
			$labelsMes[] = 'Marzo'; 
		}
		if ($mes == '04') {
			$labelsMes[] = 'Abril'; 
		}
		if ($mes == '05') {
			$labelsMes[] = 'Mayo'; 
		}
		if ($mes == '06') {
			$labelsMes[] = 'Junio'; 
		}
		if ($mes == '07') {
			$labelsMes[] = 'Julio'; 
		}
		if ($mes == '08') {
			$labelsMes[] = 'Agosto'; 
		}
		if ($mes == '09') {
			$labelsMes[] = 'Septiembre'; 
		}
		if ($mes == '10') {
			$labelsMes[] = 'Octubre'; 
		}
		if ($mes == '11') {
			$labelsMes[] = 'Noviembre'; 
		}
		if ($mes == '12') {
			$labelsMes[] = 'Diciembre'; 
		}
	}
	// var_dump($datoGeneral);
	echo json_encode(array("entregas"=>$datoGeneral, "labels"=>$labelsMes));
	$file = fopen("arrays_ejecutado.txt", "w");
	fwrite($file, json_encode(array("entregas"=>$datoGeneral, "labels"=>$labelsMes)) . PHP_EOL);
	fclose($file);
}
