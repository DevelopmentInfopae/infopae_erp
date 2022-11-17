<?php 
include '../../../config.php';
require_once '../../../db/conexion.php';

$mes = (isset($_POST["mes"]) && $_POST["mes"]) ? $Link->real_escape_string($_POST["mes"]) : "";
$semana = (isset($_POST["semana"]) && $_POST["semana"]) ? $Link->real_escape_string($_POST["semana"]) : "";
$municipio = (isset($_POST["municipio"]) && $_POST["municipio"]) ? $Link->real_escape_string($_POST["municipio"]) : "";
$ruta = (isset($_POST["ruta"]) && $_POST["ruta"]) ? $Link->real_escape_string($_POST["ruta"]) : "";
$institucion = (isset($_POST["institucion"]) && $_POST["institucion"]) ? $Link->real_escape_string($_POST["institucion"]) : "";
$sede = (isset($_POST["sede"]) && $_POST["sede"]) ? $Link->real_escape_string($_POST["sede"]) : "";
$complemento = (isset($_POST["complemento"]) && $_POST["complemento"]) ? $Link->real_escape_string($_POST["complemento"]) : "";

$entregas = "entregas_res_".$mes.$_SESSION['periodoActual'];

$consultaDias = " SELECT * FROM planilla_dias WHERE mes = '$mes' ";
$respuestaDias = $Link->query($consultaDias) or die ('Error al consultar los dias ln 16');
if ($respuestaDias->num_rows > 0) {
	$dataDias = $respuestaDias->fetch_assoc();
	$dias2 = $dataDias;
	foreach ($dias2 as $keyP => $valueP) {
		if($keyP[0] == 'D'){
			$dias[$keyP] = $valueP;
		}
	}
}
// exit(var_dump($dias));
$concatenada_1 = '';
$concatenada_4 = '';
$auxiliarSemana = [];

$consultaSemanas = " SELECT DISTINCT SEMANA AS semana FROM planilla_semanas WHERE MES = '$mes' "; 
$consultaSemanas .= ( ($semana != '') ? " AND SEMANA = '$semana' " : '' ) . " ORDER BY SEMANA "; 
$respuestaSemanas = $Link->query($consultaSemanas) or die ('Error al consultar las semanas ln 23');
if ($respuestaSemanas->num_rows > 0) {
	while ($dataSemanas = $respuestaSemanas->fetch_assoc()) {
		$auxiliarSemanaDist[] = $dataSemanas;
		$concatenada_1 .= ' ( ';
		$concatenada_4 .= ' ( ';
		$consultaDiasSemana = " SELECT DISTINCT DIA AS dia FROM planilla_semanas WHERE SEMANA = '" .$dataSemanas['semana']. "' ";
		$respuestaDiasSemana = $Link->query($consultaDiasSemana) or die ('Error al consultar los dias ln 27');
		if ($respuestaDiasSemana->num_rows > 0) {
			$concatenada_2 = '';
			$concatenada_3 = ' ( ';
			
			while ($dataDiasSemana = $respuestaDiasSemana->fetch_assoc()) {	
				// $auxiliarSemana[][$dataSemanas['semana']] = $dataDiasSemana['dia'];
				$auxiliarSemana[$dataDiasSemana['dia']] = $dataSemanas['semana'];
				foreach ($dias as $keyD => $valueD) {
					if (intval($valueD) == intval($dataDiasSemana['dia'])) {
						$concatenada_1 .= " SUM($keyD) + ";
						$concatenada_2 .= " SUM($keyD) AS $keyD, ";
						$concatenada_3 .= " $keyD + ";
						$concatenada_4 .= " SUM($keyD) + ";
					}
				}
			}
			$concatenada_1 = trim($concatenada_1, ' + ');
			$concatenada_1 .= " ) AS Semana".$dataSemanas['semana']. ", ";
			$concatenada_1 .= $concatenada_2;
			$concatenada_3 = trim($concatenada_3, ' + ');
			$concatenada_3 .= " ) AS TotalDias" .$dataSemanas['semana']. ', ' ;
			$concatenada_1 .= $concatenada_3;
			$concatenada_4 = trim($concatenada_4, ' + ');
			$concatenada_4 .=  " ) * t.ValorRacion AS Vsemana".$dataSemanas['semana']. ', ';
		}
	}
}

$concatenada_1 .= ' t.ValorRacion, ';
$concatenada_4 = trim($concatenada_4, ', ');
$concatenada_1 .= $concatenada_4;

$consultaGeneral = " SELECT u.Ciudad,
							e.cod_inst,
							e.nom_inst,
							e.cod_sede,
							e.nom_sede,
							e.tipo_complem,
							$concatenada_1
						FROM $entregas e
						INNER JOIN ubicacion u on (e.cod_mun_sede = u.codigoDANE)
						INNER JOIN tipo_complemento t on (e.tipo_complem = t.CODIGO)
						WHERE 1 = 1 ";

if (isset($municipio) && $municipio != '' ) {
	$consultaGeneral .= " AND e.cod_mun_sede = '$municipio' ";
}
if (isset($ruta) && $ruta != '' ) {
	$consultaGeneral .= " AND e.cod_sede IN (SELECT cod_Sede FROM rutasedes WHERE IDRUTA = '$ruta') ";
}
if (isset($institucion) && $institucion != '' ) {
	$consultaGeneral .= " AND e.cod_inst = '$institucion' ";
}
if (isset($sede) && $sede != '' ) {
	$consultaGeneral .= " AND e.cod_sede = '$sede' ";
}
if (isset($complemento) && $complemento != '') {
	$consultaGeneral .= " AND e.tipo_complem = '$complemento' ";
}
$consultaGeneral .= " GROUP BY u.Ciudad, e.cod_inst, e.cod_sede, e.tipo_complem ";
// exit(var_dump($consultaGeneral));
$respuestaGeneral = $Link->query($consultaGeneral) or die('Error al consultar ln 90');
if ($respuestaGeneral->num_rows > 0) {
	while ($dataGeneral = $respuestaGeneral->fetch_assoc()) {
		$general[] = $dataGeneral;
	}
}else{
	$general=[];
}

$tHead  = 		"<tr style='height: 4em;''>
    				<th> Ciudad </th>
					<th> Código Institución </th>
					<th> Nombre Institución </th>
					<th> Código Sede </th>
					<th> Nombre Sede </th>
					<th> Tipo Complemento </th>
					<th> Valor Ración </th> ";

foreach ($auxiliarSemanaDist as $keyA => $valueA) {
	$diasB = [];
	foreach ($auxiliarSemana as $keyS => $valueS) { 
		if ($valueA['semana'] == $valueS) {
			$diasB[] = $keyS;
		}
	}
	$tHead .= " <th> Semana" .$valueA['semana']. "</th> ";
	foreach ($diasB as $keyB => $valueB) {
		$tHead .= " <th> D$valueB </th> ";
	}
	$tHead .= " <th> total días" .$valueA['semana']. "</th> 
				<th> Priorización </th>
				<th> Días </th>
				<th> Inejecución </th>
				<th> Valor Semana" .$valueA['semana']. "</th>"; 
}
$tHead .= "</tr>";
$tFoot = $tHead;

$tBody = '';
foreach ($general as $keyG => $valueG) {
	$tBody .=  "<tr>" ;
	$tBody .= "<td>" .$valueG['Ciudad']. "</td>";
	$tBody .= "<td>" .$valueG['cod_inst']. "</td>";
	$tBody .= "<td>" .$valueG['nom_inst']. "</td>";
	$tBody .= "<td>" .$valueG['cod_sede']. "</td>";
	$tBody .= "<td>" .$valueG['nom_sede']. "</td>";
	$tBody .= "<td>" .$valueG['tipo_complem']. "</td>";
	$tBody .= "<td class='text-center'>" .'$'.number_format($valueG['ValorRacion'], 2, ',', '.') . "</td>";

	foreach ($auxiliarSemanaDist as $keyA => $valueA) {
		$diasB = [];
		$contadorDias = 0;
		foreach ($auxiliarSemana as $keyS => $valueS) { 
			if ($valueA['semana'] == $valueS) {
				$diasB[] = $keyS;
			}
		}
		$tBody .= " <td class='text-center'>" .$valueG['Semana'.$valueA['semana']]. "</td> ";
		foreach ($diasB as $keyB => $valueB) {
			$indice = array_keys($dias, strval($valueB));
			$tBody .= " <td>" .$valueG[$indice[0]]. "</td> ";
			$contadorDias ++;
		}
		$tBody .= " <td class='text-center'>" .$valueG['TotalDias'.$valueA['semana']]. "</td>" ;
							$complemento = $valueG['tipo_complem'];
							$semanaEnCurso = $valueA['semana'];
							$sede = $valueG['cod_sede'];
							$validacionExistencia = " SHOW TABLES LIKE 'priorizacion$semanaEnCurso' ";
							$respuestaValidacion = $Link->query($validacionExistencia) or die ('Error consultado las priorizacion ln 171');
							if ($respuestaValidacion->num_rows > 0) {
								$consultaPriorizacion = " SELECT $complemento AS comp FROM priorizacion$semanaEnCurso WHERE cod_sede = '$sede' ";
								$respuestaPriorizacion = $Link->query($consultaPriorizacion) or die ('Error consultando coberturas ln 161');
								if ($respuestaPriorizacion->num_rows > 0) {
									$dataPriorizacion = $respuestaPriorizacion->fetch_assoc();
									$comp = $dataPriorizacion['comp'];
								}else{
									$comp = 0;
								}	
							}else{
								$comp = 0;
							}
		  	
		$tBody .= "<td class='text-center'>" .$comp. "</td>" ;				  	
		$tBody .= "<td class='text-center'>" .$comp*$contadorDias. "</td> ";
		$valorSemana = floatval($valueG['Semana'.$valueA['semana']]);
		$valorPriorizado = floatval($comp*$contadorDias);
		$diferencia = $valorSemana - $valorPriorizado; 
		$tBody .= "<td class='text-center'>" .$diferencia. "</td>";
		$tBody .= "<td class='text-center'>" .'$'.number_format($valueG['Vsemana'.$valueA['semana']], 2, ',', '.'). "</td>"; 
	}
	$tBody .= "</tr>";
}					

$data['thead'] = $tHead;
$data['tfoot'] = $tFoot;	
$data['tbody'] = $tBody;

echo json_encode($data);
