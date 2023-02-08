<?php
include '../../config.php';
require_once '../../autentication.php';
require('../../fpdf181/fpdf.php');
require_once '../../db/conexion.php';
include '../../php/funciones.php';

set_time_limit (0);
ini_set('memory_limit','6000M');
date_default_timezone_set('America/Bogota');

$tamannoFuente = 8;
$diaInicialSemanaInicial = $_POST["diaInicialSemanaInicial"];
$diaFinalSemanaInicial = $_POST["diaFinalSemanaInicial"];
$diaInicialSemanaFinal = $_POST["diaInicialSemanaFinal"];
$diaFinalSemanaFinal = $_POST["diaFinalSemanaFinal"];

class PDF extends FPDF{
  function Header()
  {
    $logoInfopae = $_SESSION['p_Logo ETC'];
    $this->Image($logoInfopae, 1.6 ,1.6, 100, 18.1,'jpg', '');
    $tamannoFuente = 10;
    $this->SetFont('Arial','B',$tamannoFuente);
    $this->SetTextColor(0,0,0);
    $this->Cell(100);
    $this->Cell(250.83,18.1,utf8_decode('CERTIFICADO DE ENTREGA DE RACIONES A INSTITUCIONES EDUCATIVAS'),0,0,'C',False);
    $this->Ln(20);
  }

  // Pie de página
  function Footer()
  {
    $tamannoFuente = 8;
    // Posición: a 1,5 cm del final
    $this->SetY(-15);
    // Arial italic 8
    $this->SetFont('Arial','I',8);
    // Número de página
    //$this->Cell(0,10,'Page '.$this->PageNo().'/{nb}',0,0,'C');
  }
}

$periodoActual = $_SESSION['periodoActual'];
$mes = $_POST['mes'];
if($mes < 10){
	$mes = '0'.$mes;
}
$anno = $_SESSION['p_ano'];
$municipio = $_POST['municipio'];
$institucion = (isset($_POST["institucion"]) && $_POST["institucion"] != "") ? $_POST["institucion"] : "";


// Consultas
// 1. Trae de palnilla semana toda la información de los días de servicio
// Select * from planilla_semanas where ano='2017' and mes='08';
// 2. Trae las insituciones del municipio seleccionado

// se hace las modificaciones para traer los datos con las priorizacion y no con sedes cobertura
$consulta_priorizacion = '';
$consultaSemanasMes = " SELECT DISTINCT(SEMANA) AS semana FROM planilla_semanas WHERE MES = $mes ";
$respuestaSemanasMes = $Link->query($consultaSemanasMes) or die ('Error al consultar las semanas del mes ' . mysqli_error($Link));
if ($respuestaSemanasMes->num_rows > 0) {
	while ($dataSemanasMes = $respuestaSemanasMes->fetch_assoc()) {
		$tablaPriorizacion = "priorizacion".$dataSemanasMes['semana'];
		$consulta = " show tables like '$tablaPriorizacion' "; 
		$result = $Link->query($consulta) or die ('Error al consultar existencia de tablas de priorizacion: '. mysqli_error($Link));
		$existe = $result->num_rows;
		if ($existe == 1) {
			$con_ins = (isset($institucion) && $institucion != "") ? " AND s.cod_inst = " . $institucion : "";
			$consulta_priorizacion .= " SELECT DISTINCT s.cod_inst, s.nom_inst, s.cod_mun_sede, u.ciudad, u.Departamento, usu.nombre AS nombre_rector, usu.num_doc AS documento_rector
							FROM sedes$periodoActual s
							INNER JOIN $tablaPriorizacion AS p ON s.cod_Sede = p.cod_Sede
							INNER JOIN ubicacion u ON (s.cod_mun_sede = u.codigoDANE) and u.ETC = 0
							INNER JOIN instituciones ins ON ins.codigo_inst = s.cod_inst
							LEFT JOIN usuarios usu ON usu.num_doc = ins.cc_rector
							WHERE s.cod_mun_sede = '$municipio'" . $con_ins; 
			$consulta_priorizacion .= ' UNION ALL ';					
		}
	}
}
$consulta_priorizacion = trim($consulta_priorizacion, "UNION ALL ");  

// $con_ins = (isset($institucion) && $institucion != "") ? " AND s.cod_inst = " . $institucion : "";
// $consulta = " SELECT
// 				DISTINCT s.cod_inst, s.nom_inst, s.cod_mun_sede, u.ciudad, u.Departamento, usu.nombre AS nombre_rector, usu.num_doc AS documento_rector
// 			FROM sedes$periodoActual s
// 			INNER JOIN sedes_cobertura AS sc ON (s.cod_inst = sc.cod_inst AND s.cod_Sede = sc.cod_Sede)
// 			INNER JOIN ubicacion u ON (s.cod_mun_sede = u.codigoDANE) and u.ETC = 0
// 			INNER JOIN instituciones ins ON ins.codigo_inst = s.cod_inst
// 			LEFT JOIN usuarios usu ON usu.num_doc = ins.cc_rector
// 			WHERE sc.ano = '$anno' AND sc.mes = '$mes' AND s.cod_mun_sede = '$municipio'" . $con_ins;  
$resultado = $Link->query($consulta_priorizacion) or die ('Unable to execute query. '. mysqli_error($Link));
if($resultado->num_rows >= 1){
	while($row = $resultado->fetch_assoc()){
		$instituciones[$row['cod_inst']] = $row;
	}
}else {
		echo "<script>alert('No existe datos para los parametros seleccionados.'); window.close();</script>";
}

// 3. Dias en los que se sirvio
$consulta = "SELECT ID, ANO, MES, D1 AS D01, D2 AS D02, D3 AS D03, D4 AS D04, D5 AS D05, D6 AS D06, D7 AS D07, D8 AS D08, D9 AS D09, D10, D11, D12, D13, D14, D15, D16, D17, D18, D19, D20, D21, D22, D23, D24, D25, D26, D27, D28, D29, D30, D31 FROM planilla_dias WHERE ano = '$anno' AND mes = '$mes' ";
$resultado = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));
if($resultado->num_rows >= 1){
	while($row = $resultado->fetch_assoc()){
		$dias = $row;
	}
}

$fila = 0;
$camposEntregaDias = $sumaCamposEntregasDias = "";
foreach ($dias as $indiceDias => $dia) {
	if ($fila >= 3) {
		if ($dia >= $diaInicialSemanaInicial &&  $dia <= $diaFinalSemanaFinal) {
			$numeroIndiceDia = intval(str_replace("D", "", $indiceDias));
			$indiceDiasReal = ($numeroIndiceDia > 9) ? $indiceDias : str_replace("0", "", $indiceDias);
			$camposEntregaDias .= "SUM(". $indiceDiasReal .") AS ". $indiceDias . ", ";
			$sumaCamposEntregasDias .= $indiceDiasReal ." + ";
		}
	}
	$fila++;
}



// 4. Totales por Institución
// $consulta = " SELECT e.cod_inst, e.nom_inst, e.cod_mun_Sede, u.ciudad, u.Departamento, COALESCE(SUM(d1), 0) td01, COALESCE(SUM(d2), 0) td02, COALESCE(SUM(d3), 0) td03, COALESCE(SUM(d4), 0) td04, COALESCE(SUM(d5), 0) td05, COALESCE(SUM(d6), 0) td06, COALESCE(SUM(d7), 0) td07, COALESCE(SUM(d8), 0) td08, COALESCE(SUM(d9), 0) td09, COALESCE(SUM(d10), 0) td10, COALESCE(SUM(d11), 0) td11, COALESCE(SUM(d12), 0) td12, COALESCE(SUM(d13), 0) td13, COALESCE(SUM(d14), 0) td14, COALESCE(SUM(d15), 0) td15, COALESCE(SUM(d16), 0) td16, COALESCE(SUM(d17), 0) td17, COALESCE(SUM(d18), 0) td18, COALESCE(SUM(d19), 0) td19, COALESCE(SUM(d20), 0) td20, COALESCE(SUM(d21), 0) td21, COALESCE(SUM(d22), 0) td22 FROM entregas_res_$mes$periodoActual e
// INNER JOIN ubicacion u ON e.cod_mun_sede = u.codigodane and u.ETC = 0
// WHERE e.cod_mun_sede = $municipio GROUP BY e.cod_inst ";
// $resultado = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));
// if($resultado->num_rows >= 1){
// 	while($row = $resultado->fetch_assoc()){
// 		$totalesInstitucion[$row['cod_inst']] = $row;
// 	}
// }

// 5.Entregas por sedes
$sedesInstitucion = array();
$consulta = "SELECT cod_inst, cod_sede, nom_sede, tipo_complem, ". trim($camposEntregaDias, ", ") . ", (". trim($sumaCamposEntregasDias, " + ") .") AS numdias FROM entregas_res_$mes$periodoActual WHERE (". trim($sumaCamposEntregasDias, " + ") .") > 0 AND cod_mun_sede = $municipio GROUP BY cod_sede , tipo_complem  ORDER BY  nom_sede";
$resultado = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));
if($resultado->num_rows >= 1){
	while($row = $resultado->fetch_assoc()){
		$entregasSede[$row['cod_sede']][] = $row;
		$sedesInstitucion[$row['cod_inst']][] =  $row['cod_sede'];
	}
}
// echo "$consulta";
// Fechas inicial y fecha final de las semanas del mes seleccionado
$consulta_fechas = "(SELECT ANO, MES, DIA FROM planilla_semanas WHERE MES = '$mes' AND DIA = $diaInicialSemanaInicial ORDER BY SEMANA ASC, DIA ASC LIMIT 1) UNION ALL (SELECT ANO, MES, DIA FROM `planilla_semanas` WHERE MES = '$mes' AND DIA = $diaFinalSemanaFinal ORDER BY SEMANA DESC, DIA DESC LIMIT 1)";
// exit($consulta_fechas);
$resultado_fechas = $Link->query($consulta_fechas) or die ('Unable to execute query. '. mysqli_error($Link));
if ($resultado_fechas->num_rows > 0) {
	while ($registros_fechas = $resultado_fechas->fetch_assoc()) {
		$fechas[] = $registros_fechas;
	}
}

//CREACION DEL PDF
// Creación del objeto de la clase heredada
$pdf= new PDF('L','mm',array(330,216));
$pdf->SetMargins(1.6, 1.6, 1.6, 1.6);
$pdf->SetAutoPageBreak(false,5);
$pdf->AliasNbPages();
$lineas = 20;
$alturaLinea = 4;

$pdf->SetTextColor(0,0,0);
$pdf->SetFillColor(192,192,192);
$pdf->SetDrawColor(82,86,89);


$x1 = $pdf->GetX();
$y1 = $pdf->GetY();
$altoEncabezado = 8;
$maximoLineas = 7;
$linea = 0;

$resultadoComplementos = $Link->query("SELECT DISTINCT tipo_complem FROM entregas_res_".$mes.$_SESSION['periodoActual']." WHERE tipo_complem IS NOT NULL;") or die (mysqli_error($Link));
if ($resultadoComplementos->num_rows > 0) {
	while ($registrosComplementos = $resultadoComplementos->fetch_assoc()) {
		$complementos[] = $registrosComplementos["tipo_complem"];
	}
}

// Consulta que retorna el order de las priorizaciones de las caracterizaciones.
$resultadoPrioridad = $Link->query("SELECT * FROM prioridad_caracterizacion ORDER BY orden") or die(mysql_error($Link));
if ($resultadoPrioridad->num_rows > 0) {
	while ($registrosPrioridad = $resultadoPrioridad->fetch_assoc()) {
		$prioridades[] = $registrosPrioridad;
	}
}


foreach ($instituciones as $institucion)
{
	$totalesPorDia = []; //Array para almacenar total por días.
	$totalDeTotales = 0; //Variable para almacenar el total de todas las sedes.

if (array_key_exists($institucion['cod_inst'], $sedesInstitucion)) {
	$pdf->AddPage();
	include 'certificado_dias_header.php';
	$linea = 0;
	if(count($sedesInstitucion )>0){
		// Obtengo las sedes de esa institución
		$sedes = $sedesInstitucion[$institucion['cod_inst']];
		$sedes = array_unique($sedes);

		foreach ($sedes as $sede) {
			$entregas = $entregasSede[$sede];

			foreach ($entregas as $entrega) {
				$contadorDias = 0; //Variable para llevar la cuenta de días en los que se realiza entrega.

				$linea++;

				$x1 = $pdf->GetX();
				$y1 = $pdf->GetY();
				if($linea > 1){
					$pdf->Cell(85);
					$pdf->Cell(0,4,'','T',0,'C',false);
				}
				$pdf->SetXY($x1, $y1);



				//Control de saltos de pagina
				if($linea > $maximoLineas){
					$linea = $linea-1;
					$pdf->SetXY($x, $y);
					$aux = ($linea*4) + $altoEncabezado;
					$pdf->Cell(0,$aux,utf8_decode(''),1,0,'C',false);
					include 'certificado_dias_footer.php';
					$pdf->AddPage();
					include 'certificado_dias_header.php';
					$linea = 1;
				}

				$x1 = $pdf->GetX();
				$y1 = $pdf->GetY();

				$pdf->Cell(85,4,utf8_decode(substr($entrega['nom_sede'],0,85)),'R',0,'L',false);

				$pdf->SetXY($x1, $y1);
				$pdf->Cell(85,4,'','B',0,'L',false);
				$pdf->Cell(14,4,utf8_decode($entrega['tipo_complem']),'R',0,'C',false);

				$total = 0;
				for ($i=0; $i < 22 ; $i++) {
					$auxIndice = $i+1;
					if($auxIndice < 10){
						$auxIndice = 'D0'.$auxIndice;
					}
					else{
						$auxIndice = 'D'.$auxIndice;
					}

					$valor=0;
					if(isset($entrega[$auxIndice]) && $entrega[$auxIndice] != ''){
						$valor = $entrega[$auxIndice];
						$total = $total + $valor;
					}
					$pdf->Cell(7.5,4,utf8_decode($valor),'R',0,'C',false);

					//Suma de totales por día.
					if (isset($totalesPorDia[$i])) {
						$totalesPorDia[$i] += $valor;
					} else {
						$totalesPorDia[$i] = $valor;
					}
					//Suma de totales por día.

					//Suma de total de días con entrega (Revisar consulta por que en algunos casos trae menos, SEDE = 16830700026001 / I.E. COLEGIO NIEVES CORTES PIC )
					if ($valor != 0) {
						$contadorDias++;
					}
					//Suma de total de días con entrega
				}

				//Suma de total por sedes.
				$totalDeTotales += $total;
				//Suma de total por sedes.

				$pdf->Cell(16,4,utf8_decode($total),'R',0,'C',false);
				// $aux = $entrega['numdias'];
				$pdf->Cell(16,4,utf8_decode($contadorDias),'R',0,'C',false);
				$pdf->Cell(0,4,utf8_decode(''),'R',0,'C',false);
				// $pdf->Cell(0,4,utf8_decode($linea),'R',0,'C',false);
				$pdf->SetXY($x1, $y1);
				if($linea != $maximoLineas){

				}

				$pdf->Ln(4);
			}
		}
		///inicio
		$linea++;

		$x1 = $pdf->GetX();
		$y1 = $pdf->GetY();
		if($linea > 1){
			$pdf->Cell(80);
			$pdf->Cell(0,4,'','T',0,'C',false);
		}
		$pdf->SetXY($x1, $y1);

		//Control de saltos de pagina
		if($linea > $maximoLineas){
			$linea = $linea-1;
			$pdf->SetXY($x, $y);
			$aux = ($linea*4) + $altoEncabezado;
			$pdf->Cell(0,$aux,utf8_decode(''),1,0,'C',false);
			include 'certificado_dias_footer.php';
			$pdf->AddPage();
			include 'certificado_dias_header.php';
			$linea = 1;
		}

		$x1 = $pdf->GetX();
		$y1 = $pdf->GetY();

		$aux = 'TOTALES';
		$aux = substr($aux,0,85);
		$pdf->Cell(85,4,utf8_decode($aux),'R',0,'R',false);

		$pdf->SetXY($x1, $y1);
		$pdf->Cell(85,4,'','B',0,'L',false);


		$aux = '';
		$pdf->Cell(14,4,utf8_decode($aux),'R',0,'C',false);

		$total = 0;
		for ($i=0; $i < 22 ; $i++) {
			$pdf->Cell(7.5,4,utf8_decode($totalesPorDia[$i]),'R',0,'C',false);
		}

		$pdf->Cell(16,4,utf8_decode($totalDeTotales),'R',0,'C',false);
		$pdf->Cell(16,4,utf8_decode(''),'R',0,'C',false);
		$pdf->Cell(0,4,utf8_decode(''),'R',0,'C',false);
		// $pdf->Cell(0,4,utf8_decode($linea),'R',0,'C',false);
		$pdf->SetXY($x1, $y1);
		if($linea != $maximoLineas){

		}

		$pdf->Ln(4);
		///fin


		$pdf->SetXY($x, $y);
		$aux = ($linea*4) + $altoEncabezado;
		$pdf->Cell(0,$aux,utf8_decode(''),1,0,'C',false);
		//Termina el contenido de las entregas
		include 'certificado_dias_footer.php';

	}
}
}
if(count($sedesInstitucion) > 0) {
	$pdf->Output();
} else {
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