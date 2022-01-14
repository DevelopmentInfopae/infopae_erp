<?php
require_once '../../../db/conexion.php';
require_once '../../../config.php';
include 'fn_fecha_asistencia.php';

$semanaActual = "";
$sede = "";
$asistencia = array();
$anno = $annoAsistencia2D; 
$annoCompleto = $annoasistencia;
$periodoActual = $_SESSION['periodoActual'];

if(isset($_POST['semanaActual']) && $_POST['semanaActual'] != ''){ $semanaActual = mysqli_real_escape_string($Link, $_POST['semanaActual']); }
if(isset($_POST['sede']) && $_POST['sede'] != ''){ $sede = mysqli_real_escape_string($Link, $_POST['sede']); }
if(isset($_POST['complemento']) && $_POST['complemento'] != ''){ $complemento = mysqli_real_escape_string($Link, $_POST['complemento']); }
if(isset($_POST['mes']) && $_POST['mes'] != ""){ $mes = mysqli_real_escape_string($Link, $_POST['mes']); }else{ $mes = $mesAsistencia; }
if(isset($_POST['dia']) && $_POST['dia'] != ""){ $dia = mysqli_real_escape_string($Link, $_POST['dia']); }else{ $dia = $diaAsistencia; }

$consultaDias = " SELECT D1,D2,D3,D4,D5,D6,D7,D8,D9,D10,D11,D12,D13,D14,D15,D16,D17,D18,D19,D20,D21,D22,D23,D24,D25,D26,D27,D28,D29,D30,D31 FROM planilla_dias WHERE mes = '$mes' ";
$respuestaDias = $Link->query($consultaDias) or die ('Error al consultar los dias del mes ' . mysqli_error($Link));
if ($respuestaDias->num_rows > 0) {
	$dataDias = $respuestaDias->fetch_assoc();
	$dias = $dataDias;
	$D = array_search($dia, $dias);
}

$consultaCobertura = " SELECT $complemento AS complemento FROM sedes_cobertura WHERE Ano = $annoCompleto AND cod_sede = $sede AND mes = $mes AND semana = $semanaActual ";
$respuestaCobertura = $Link->query($consultaCobertura);
if ($respuestaCobertura->num_rows > 0) {
	$dataCobertura = $respuestaCobertura->fetch_assoc();
	$cobertura = $dataCobertura['complemento'];
	$consultaEntregas = " SELECT SUM($D) AS consume FROM entregas_res_$mes$periodoActual WHERE cod_sede = '$sede'  AND tipo_complem = '$complemento' ";
	$respuestaEntregas = $Link->query($consultaEntregas) or die ('Error al consultar las entregas ' . mysqli_error($Link));
	if ($respuestaEntregas->num_rows > 0) {
		$dataEntregas = $respuestaEntregas->fetch_assoc();
		$consumoSuplencia = $dataEntregas['consume'];
		$faltan = $cobertura - $consumoSuplencia;
		if ($faltan < 0) {
			$faltan = 0;
		}
		$resultadoAJAX = array(
			"estado" => 1,
			"mensaje" => "Se ha cargado con exito.",
			"suplencia" => $faltan
		);
	}
}else{
	$resultadoAJAX = array(
		"estado" => 0,
		"mensaje" => "Se ha presentado un error."
	);
}
echo json_encode($resultadoAJAX);
