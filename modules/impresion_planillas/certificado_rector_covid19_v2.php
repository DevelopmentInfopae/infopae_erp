<?php 
error_reporting(E_ALL); 
include '../../config.php';
require_once '../../autentication.php';
require_once '../../db/conexion.php';
include '../../php/funciones.php';
require('../../fpdf181/fpdf.php');
set_time_limit (0);
ini_set('memory_limit','6000M');
date_default_timezone_set('America/Bogota');

// declaracion de variables que van por post 
$municipio = $_POST['municipio'];
$institucion = $_POST['institucion'];
$mes = $_POST['mes'];
$semanaInicial = $_POST['semana_inicial'];
$semanaFinal = $_POST['semana_final'];
$imprimirMes = $_POST['imprimirMes'];
$fechaDesde = $_POST['fecha_desde'];
$fechaHasta = $_POST['fecha_hasta'];
$observaciones = $_POST['observaciones'];

if($fechaDesde != ""){
	$fecha_desde = date("d/m/Y", strtotime($fechaDesde));
}
if($fechaHasta != ""){
	$fecha_hasta = date("d/m/Y", strtotime($fechaHasta));
}

// mes que se va a utilizar para traer el numero de la entrega
$mesEntrega = 0;
$mesEntrega = $_POST['mes'];
if($mesEntrega < 10){ $mesEntrega = '0'.$mesEntrega; }
$mesEntrega = trim($mesEntrega);

$entrega = '';
// vamos a buscar el numero de la entrega que va a estar junto al mes 
$consultaEntrega = "SELECT NumeroEntrega FROM planilla_dias WHERE mes = $mesEntrega;";
$respuestaEntrega = $Link->query($consultaEntrega) or die('Error al consultar el numero de la entrega' . mysqli_error($Link));
if ($respuestaEntrega->num_rows>0) {
	$dataEntrega = $respuestaEntrega->fetch_assoc();
	$entrega = $dataEntrega['NumeroEntrega'];
}

$nombreMesEntrega = '';
$consultaMesEntrega = "SELECT NombreMes FROM planilla_dias WHERE mes = $mesEntrega;";
$respuestaMesEntrega = $Link->query($consultaMesEntrega) or die('Error al consultar el mes de la entrega' . mysqli_error($Link));
if ($respuestaMesEntrega->num_rows > 0) {
	$dataMesEntrega = $respuestaMesEntrega->fetch_assoc();
	$nombreMesEntrega = $dataMesEntrega['NombreMes'];
}

//Imprimir mes
$imprimirMes = 0;
$mesLetras = "";
if(isset($_POST["imprimirMes"]) &&  $_POST["imprimirMes"] == "on"){
	$imprimirMes = 1;
	$mesLetras = $nombreMesEntrega;
}

// capturamos el primer dia de la semana inicial
$diaInicialSemanaInicial = '';
$consultaDiaInicial = " SELECT MIN(DIA) AS dia FROM planilla_semanas WHERE SEMANA = $semanaInicial; ";
$respuestaDiaInicial = $Link->query($consultaDiaInicial) or die ('Error al consultar el día inicial. ' . mysqli_error($Link));
if ($respuestaDiaInicial->num_rows > 0) {
	$dataDiaInicial = $respuestaDiaInicial->fetch_assoc();
	$diaInicialSemanaInicial = $dataDiaInicial['dia'];
}

// capturamos el ultimo dia de la semana final
$diaFinalSemanaFinal = '';
$consultaDiaFinal = " SELECT MAX(DIA) AS dia FROM planilla_semanas WHERE SEMANA = $semanaFinal; ";
$respuestaDiaFinal = $Link->query($consultaDiaFinal) or die ('Error al consultar el dia final. ' . mysqli_error($Link));
if ($respuestaDiaFinal->num_rows > 0) {
	$dataDiaFinal = $respuestaDiaFinal->fetch_assoc();
	$diaFinalSemanaFinal = $dataDiaFinal['dia'];
}

$tamannoFuente = 8;
class PDF extends FPDF
{
	function Header() {
		$logoInfopae = $_SESSION['p_Logo ETC'];
		$this->Image($logoInfopae, 12, 7, 95, 18.1,'jpg', '');
		$tamannoFuente = 11;
		$this->SetFont('Arial','B',$tamannoFuente);
		$this->SetTextColor(0,0,0);
		$this->Cell(91.9);
		$this->MultiCell(100,6,"CERTIFICADO DE ENTREGA DE RACIONES A INSTITUCIONES EDUCATIVAS" ,0,'C',false);
		$this->Ln(8);
	}

  	// Pie de página
	function Footer() {
		$tamannoFuente = 8;
		$this->SetY(-15);
		$this->SetFont('Arial','I',8);
	}
}

//CREACION DEL PDF
// Creación del objeto de la clase heredada
$pdf= new PDF('P','mm',array(215.9,279.4));
$pdf->SetMargins(12, 12, 12, 12);
$pdf->SetAutoPageBreak(false,5);
$pdf->AliasNbPages();
$pdf->SetTextColor(0,0,0);
$pdf->SetFillColor(192,192,192);
$pdf->SetDrawColor(0,0,0);

$anno = $_SESSION['p_ano'];
$anno2d = substr($anno,2);
$mes = $_POST['mes'];
if($mes < 10){
	$mes = '0'.$mes;
}
$municipio = $_POST['municipio'];

//Dias Semanas
$consulta = "SELECT * FROM planilla_semanas WHERE ano='$anno' AND mes='$mes' AND DIA BETWEEN $diaInicialSemanaInicial AND $diaFinalSemanaFinal";
$resultado = $Link->query($consulta) or die ('Unable to execute query. Linea 62:'. mysqli_error($Link));
if($resultado->num_rows >= 1){
	while($row = $resultado->fetch_assoc()){
		$diasSemanas[] = $row;
	}
}

//Instituciones
$consulta = "SELECT DISTINCT s.cod_inst,s.nom_inst,s.cod_mun_sede,u.ciudad,u.Departamento, usu.nombre AS nombre_rector, usu.num_doc AS documento_rector
FROM sedes$anno2d s
INNER JOIN sedes_cobertura AS sc ON (s.cod_inst=sc.cod_inst AND s.cod_Sede=sc.cod_Sede)
INNER JOIN ubicacion u ON(s.cod_mun_sede=u.codigoDANE) and u.ETC = 0
INNER JOIN instituciones ins ON ins.codigo_inst = s.cod_inst
LEFT JOIN usuarios usu ON usu.num_doc = ins.cc_rector
WHERE sc.ano='$anno' AND sc.mes='$mes' AND s.cod_mun_sede='$municipio'";
$consulta .= (isset($_POST["institucion"]) && $_POST["institucion"] != "") ? " AND s.cod_inst = '".$_POST["institucion"]."'" : "";

//echo "<br><br>$consulta<br><br>";

$resultado = $Link->query($consulta) or die ('Unable to execute query. Linea 78: '. mysqli_error($Link));

if($resultado->num_rows >= 1){
	while($row = $resultado->fetch_assoc()){
		$instituciones[$row['cod_inst']] = $row;
	}
}

//diasEntregas
$consulta = "SELECT ID ,ANO, MES, D1 AS 'D01',D2 AS D02,D3 AS D03,D4 AS D04,D5 AS D05,D6 AS D06,D7 AS D07,D8 AS D08,D9 AS D09, D10, D11, D12, D13, D14, D15, D16, D17, D18, D19, D20, D21, D22, D23, D24, D25, D26, D27, D28, D29, D30, D31 FROM planilla_dias WHERE ano='$anno' and mes='$mes'";
$resultado = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));
if($resultado->num_rows >= 1){
	while($row = $resultado->fetch_assoc()){
		$diasEntregas = $row;
	}
}

$iDias = $indiceDiaInicial = 0;
$columnasDiasEntregasDias = $camposDiasEntregasDias = $columnasCondicionEntregasDias = "";
foreach ($diasEntregas as $indiceDia => $dias) {
	if ($iDias >= 3) {
		if ($dias >= $diaInicialSemanaInicial && $dias <= $diaFinalSemanaFinal) {
			if ($indiceDiaInicial == 0) {
				$indiceDiaInicial = str_replace("D0", "", $indiceDia);
			}
			$numeroIndiceDia = intval(str_replace("D", "", $indiceDia));
			$dia  = ($numeroIndiceDia > 9) ? $indiceDia : str_replace("0", "", $indiceDia);

			$columnasDiasEntregasDias .= "SUM(". $dia .") AS ". strtolower($indiceDia) .", ";
			$columnasCondicionEntregasDias .= $dia ."+ ";
			$camposDiasEntregasDias .= $dia ." + ";
		}
	}
	$iDias++;
}

// Fechas inicial y fecha final del mes seleccionado.
$consulta_fechas = "(SELECT ANO, MES, DIA FROM planilla_semanas WHERE MES = '$mes' AND DIA BETWEEN $diaInicialSemanaInicial AND $diaFinalSemanaFinal ORDER BY SEMANA ASC, DIA ASC LIMIT 1) UNION ALL (SELECT ANO, MES, DIA FROM `planilla_semanas` WHERE MES = '$mes' AND DIA BETWEEN $diaInicialSemanaInicial AND $diaFinalSemanaFinal ORDER BY SEMANA DESC, DIA DESC LIMIT 1)";
$resultado_fechas = $Link->query($consulta_fechas) or die ('Unable to execute query. '. mysqli_error($Link));
if ($resultado_fechas->num_rows > 0) {
	while ($registros_fechas = $resultado_fechas->fetch_assoc()) {
		$fechas[] = $registros_fechas;
	}
}

//EntregasSedes
$entregasSedes = array();
$consulta = "SELECT cod_inst,cod_sede,nom_sede, tipo_complem, ". trim($columnasDiasEntregasDias, ", ") .", (". trim($camposDiasEntregasDias, " + ") .") AS numdias FROM entregas_res_$mes$anno2d WHERE (". trim($columnasCondicionEntregasDias, "+ ") .") > 0 AND cod_mun_sede = $municipio";
$consulta .= (isset($_POST["institucion"]) && $_POST["institucion"] != "") ? " AND cod_inst = '". $_POST["institucion"] ."'" : "";
$consulta .= " GROUP BY cod_sede,tipo_complem";
// var_dump($consulta);

$resultado = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));
if($resultado->num_rows >= 1){
	$codigoInicial = 0;
	while($row = $resultado->fetch_assoc()){
		if($codigoInicial != $row['cod_inst']){
			$codigoInicial = $row['cod_inst'];
		}
		$entregasSedes[$codigoInicial][] = $row;
	}
}

if(count($entregasSedes)>0){

	$consultaSemanasMes = "SELECT DISTINCT SEMANA FROM planilla_semanas WHERE MES = '$mes'";
	$resultadoSemanasMes = $Link->query($consultaSemanasMes) or die ("Error al consultar planillas_semanas: ". $Link->error);
	if ($resultadoSemanasMes->num_rows > 0) {
		while ($registrosSemanasMes = $resultadoSemanasMes->fetch_assoc()) {
			$semanasMes[] = $registrosSemanasMes;
		}
	}

	$consultaSemanasMesSeleccion = "SELECT DISTINCT SEMANA FROM planilla_semanas WHERE MES = '$mes' AND DIA BETWEEN $diaInicialSemanaInicial AND $diaFinalSemanaFinal";
	$resultadoSemanasMesSeleccion = $Link->query($consultaSemanasMesSeleccion) or die ("Error al consultar planillas_semanas: ". $Link->error);
	if ($resultadoSemanasMesSeleccion->num_rows > 0) {
		while ($registrosSemanasMesSeleccion = $resultadoSemanasMesSeleccion->fetch_assoc()) {
			$semanasMesSeleccion[] = $registrosSemanasMesSeleccion;
		}
	}

	for ($i=0; $i < count($semanasMes); $i++) {
		foreach ($semanasMesSeleccion as $semanaSeleccion) {
			if ($semanasMes[$i]["SEMANA"] == $semanaSeleccion["SEMANA"]) {
				$posicionesSemanaMes[] = $i+1;
			}
		}
	}

	// Se van a separar los dias que corresponden a cada semana
	$semanaIndice = 0;
	$numeroSemana = -1;

	foreach ($diasSemanas as $diaSemana)
	{
		if($semanaIndice != $diaSemana['SEMANA'])
		{
			$semanaIndice = $diaSemana['SEMANA'];
			$numeroSemana++;
		}

		$diasSemana[$posicionesSemanaMes[$numeroSemana]][] = $diaSemana;
	}

	$consultaDias = " SELECT COUNT(DIA) AS dias FROM planilla_semanas WHERE semana BETWEEN $semanaInicial AND $semanaFinal; ";
	$respuestaDias = $Link->query($consultaDias) or die ('Error al contar el número de días. ' . mysqli_error($Link));				
	if ($respuestaDias->num_rows > 0) {
		$dataDias = $respuestaDias->fetch_assoc();
		$numeroDias = $dataDias['dias'];
	}
	
	/* CICLO PRINCIPAL DE SEDES */
	
	foreach ($instituciones as $institucion){
		
		if (array_key_exists($institucion['cod_inst'], $entregasSedes)){
			$pdf->AddPage();
			include 'certificado_rector_covid19_header_v2.php';
			$totalesSemanas = array(0,0,0,0,0);

			// Impresión de datos de la tabla.
			if (array_key_exists($institucion['cod_inst'], $entregasSedes))
			{
				$entregasSedesInstitucion =  $entregasSedes[$institucion['cod_inst']];
			}
			else
			{
				$entregasSedesInstitucion = [];
			}

			$banderaNombres = 0;
			$lineas = 0;
			$lineasTotales = 0;
			$maxCaracteres = 27;
			
			$aux_x = $pdf->GetX();
			$aux_y = $pdf->GetY();

			$indiceFilas = 0;
			$granTotal = 0;
			foreach ($entregasSedesInstitucion as $entregasSedeInstitucion){








				$indiceFilas++;
				$pdf->SetFont('Arial','',$tamannoFuente-1);
				$lineasTotales++;
				if($banderaNombres == 0){
					$nombre = $entregasSedeInstitucion['nom_sede'];
					$banderaNombres++;
				} else{
					if($nombre != $entregasSedeInstitucion['nom_sede']){
						$linea = $lineas * 4;
						if($lineas<= 1) { $nombre = substr($nombre,0,$maxCaracteres); }
						$nombre = $entregasSedeInstitucion['nom_sede'];
						$lineas = 0;
					}
				}
				$lineas++;
				// $nombre = substr($nombre,0,);
				$pdf->Cell(95,4,utf8_decode($nombre),'LB',0,'L',false);
				$pdf->Cell(18,4,$entregasSedeInstitucion['tipo_complem'],'LB',0,'C',false);
				$indice = $indiceDiaInicial-1;
				$totalSemana = 0;
				for($i = 1; $i <= 5 ; $i++){
					if(isset($diasSemana[$i])){
						$total = 0;
						$indicePrint = 0;
						foreach ($diasSemana[$i] as $diaSemana)
						{
							$indice++;
							$res_can_comp = $Link->query("SELECT SUM(D$indice) AS cantidadComplemento FROM entregas_res_$mes$anno2d WHERE cod_inst = '". $entregasSedeInstitucion["cod_inst"] ."' AND cod_sede = '". $entregasSedeInstitucion["cod_sede"] ."' AND tipo_complem = '". $entregasSedeInstitucion["tipo_complem"] ."';") or die (mysqli_error($Link));
							if ($res_can_comp->num_rows > 0)
							{
								$reg_can_comp = $res_can_comp->fetch_assoc();

								if ($reg_can_comp["cantidadComplemento"] > 0)
								{
									$indicePrint++;
								}
							}

							if($indice < 10)
							{
								$aux = 'd0'.$indice;
							}
							else
							{
								$aux = 'd'.$indice;
							}

							$total = $total + $entregasSedeInstitucion[$aux];
							$totalSemana = $totalSemana + $entregasSedeInstitucion[$aux];
							$totalesSemanas[$i-1] = $totalesSemanas[$i-1] + $entregasSedeInstitucion[$aux];
						}
						// $pdf->Cell(13,4,$total,'R',0,'C',false);
						// $pdf->Cell(10,4,$indicePrint,'R',0,'C',false);
					}
					else
					{
						// $pdf->Cell(13,4,'','R',0,'C',false);
						// $pdf->Cell(10,4,'','R',0,'C',false);
					}
				}





				//var_dump($_POST);
				$consulta = "SELECT max(sc.num_est_focalizados) as estudiantes FROM sedes_cobertura sc WHERE sc.cod_sede = ".$entregasSedeInstitucion["cod_sede"]." AND sc.mes = ".$_POST['mes']." AND sc.semana BETWEEN ".$_POST['semana_inicial']." AND ".$_POST['semana_final'];
				//echo "<br><br>$consulta<br><br>";
				$resultado = $Link->query($consulta) or die ('Unable to execute query. Linea 62:'. mysqli_error($Link));
				if($resultado->num_rows >= 1){
					$row = $resultado->fetch_assoc();
					$totalSemana = $row['estudiantes'];
				}

		
				
				$granTotal += $totalSemana;

				$diasEntregados;
				if ($entregasSedeInstitucion['tipo_complem'] == 'RPC') {
					$diasEntregados = 1;
				}else if($entregasSedeInstitucion['tipo_complem'] != 'RPC'){
					$diasEntregados = $numeroDias;
				}

				$pdf->Cell(20,4,$totalSemana,'LBR',0,'C',false);
				$pdf->Cell(17,4,$numeroDias,'LBR',0,'C',false);
				$pdf->Cell(17,4,($totalSemana * $diasEntregados ),'LBR',0,'C',false);
				$pdf->Cell(0,4,'','LBR',0,'C',false);
				// $pdf->SetX($aux_x);
				// $pdf->Cell(45);
				// $pdf->Cell(0,4,'','B',0,'C',false);
				$pdf->Ln(4);

				//var_dump($indiceFilas);
				if($indiceFilas > 14){
					include 'certificado_rector_covid19_footer_v2.php';
					$pdf->AddPage();
					include 'certificado_rector_covid19_header_v2.php';$indiceFilas = 0;
				}
			}
			
			$linea = $lineas * 4;

			if($lineas<= 1) {
				$nombre = substr($nombre,0,$maxCaracteres-3);
				$nombre.="...";
			}

			$pdf->SetFont('Arial','B',$tamannoFuente-1);
			$pdf->Cell(113,4,'TOTAL:','LBR',0,'L',false);
			$pdf->SetFont('Arial','',$tamannoFuente-1);
			$pdf->Cell(20,4,$granTotal,'BR',0,'C',false);
			$pdf->Cell(17,4,$numeroDias,'BR',0,'C',false);
			$pdf->Cell(17,4,$granTotal,'BR',0,'C',false);
			$pdf->Cell(0,4,'','BR',0,'C',false);
		}
		
		$pdf->Ln(4);
		include 'certificado_rector_covid19_footer_v2.php';
	}

	$pdf->Output();
}
else
{
	echo "<script>alert('No se han encontrado entregas en el mes correspondiente.'); window.close()</script>";
}

function mesNombre($mes) {
	if($mes == 1){
		return 'Enero';
	}
	else if($mes == 2){
		return 'Febrero';
	}
	else if($mes == 3){
		return 'Marzo';
	}
	else if($mes == 4){
		return 'Abril';
	}
	else if($mes == 5){
		return 'Mayo';
	}
	else if($mes == 6){
		return 'Junio';
	}
	else if($mes == 7){
		return 'Julio';
	}
	else if($mes == 8){
		return 'Agosto';
	}
	else if($mes == 9){
		return 'Septiembre';
	}
	else if($mes == 10){
		return 'Octubre';
	}
	else if($mes == 11){
		return 'Noviembre';
	}
	else if($mes == 12){
		return 'Diciembre';
	}
}
