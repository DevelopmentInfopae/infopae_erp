<?php
require_once '../../../config.php';
require_once '../../../db/conexion.php';
$data = [];
$municipio = (isset($_POST['municipio']) && $_POST['municipio'] != '') ? mysqli_real_escape_string($Link, $_POST["municipio"]) : "";
$insitucion = (isset($_POST['insitucion']) && $_POST['insitucion'] != '') ? mysqli_real_escape_string($Link, $_POST["insitucion"]) : "";
$sede = (isset($_POST['sede']) && $_POST['sede'] != '') ? mysqli_real_escape_string($Link, $_POST["sede"]) : "";
$mes = (isset($_POST['mes']) && $_POST['mes'] != '') ? mysqli_real_escape_string($Link, $_POST["mes"]) : "";
$semana = (isset($_POST['semana']) && $_POST['semana'] != '') ? mysqli_real_escape_string($Link, $_POST["semana"]) : "";
$tipoComplemento = (isset($_POST['tipoComplemento']) && $_POST['tipoComplemento'] != '') ? mysqli_real_escape_string($Link, $_POST["tipoComplemento"]) : "";

//Array de los días de la semana
$dias = array();
$dias['D1'] = 0;
$dias['D2'] = 0;
$dias['D3'] = 0;
$dias['D4'] = 0;
$dias['D5'] = 0;

// Dias de esa semana
$consultaNovedad = "SELECT * FROM planilla_semanas WHERE semana = \"$semana\"";
$resultadoNovedades = $Link->query($consultaNovedad);
if($resultadoNovedades->num_rows > 0){
	while($row = $resultadoNovedades->fetch_assoc()) {
		if($row['NOMDIAS'] == 'lunes'){
			$dias['D1']	= 1;
		} else if($row['NOMDIAS'] == 'martes'){
			$dias['D2']	= 1;
		} else if($row['NOMDIAS'] == 'miércoles'){
			$dias['D3']	= 1;
		} else if($row['NOMDIAS'] == 'jueves'){
			$dias['D4']	= 1;
		} else if($row['NOMDIAS'] == 'viernes'){
			$dias['D5']	= 1;
		}
	}
}

// Datos del estudiante
$consultaNovedad = "select td.Abreviatura, f.num_doc, CONCAT(nom1,\" \",nom2,\" \",ape1,\" \",ape2) AS nombre, \"$tipoComplemento\" AS complemento, \"0\" as D1, \"0\" as D2, \"0\" as D3, \"0\" as D4, \"0\" as D5 FROM focalizacion$semana f LEFT JOIN tipodocumento td ON f.tipo_doc = td.id WHERE f.cod_sede = $sede AND f.Tipo_complemento = '$tipoComplemento' AND f.activo = 1";

$resultadoNovedades = $Link->query($consultaNovedad);
if($resultadoNovedades->num_rows > 0){
	while($registrosSedes = $resultadoNovedades->fetch_assoc()) {
		$registrosSedes['D1'] = $dias['D1'];
		$registrosSedes['D2'] = $dias['D2'];
		$registrosSedes['D3'] = $dias['D3'];
		$registrosSedes['D4'] = $dias['D4'];
		$registrosSedes['D5'] = $dias['D5'];
		$data[] = $registrosSedes;
	}
}

$output = [
	'sEcho' => 1,
	'iTotalRecords' => count($data),
	'iTotalDisplayRecords' => count($data),
	'aaData' => $data
];

echo json_encode($output);
