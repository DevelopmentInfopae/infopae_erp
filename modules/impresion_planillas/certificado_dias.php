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

$Link = new mysqli($Hostname, $Username, $Password, $Database);
if ($Link->connect_errno) {
  echo "Fallo al contenctar a MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
}
$Link->set_charset("utf8");

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
//var_dump($_POST);
//var_dump($_SESSION);

// Consultas
// 1. Trae de palnilla semana toda la información de los días de servicio
// Select * from planilla_semanas where ano='2017' and mes='08';
// 2. Trae las insituciones del municipio seleccionado
$consulta = " SELECT DISTINCT s.cod_inst, s.nom_inst, s.cod_mun_sede, u.ciudad, u.Departamento, usu.nombre AS nombre_rector
FROM sedes$periodoActual s
INNER JOIN sedes_cobertura AS sc ON (s.cod_inst = sc.cod_inst AND s.cod_Sede = sc.cod_Sede)
INNER JOIN ubicacion u ON (s.cod_mun_sede = u.codigoDANE) and u.ETC = 0
INNER JOIN instituciones ins ON ins.codigo_inst = s.cod_inst
LEFT JOIN usuarios usu ON usu.num_doc = ins.cc_rector
WHERE sc.ano = '$anno' AND sc.mes = '$mes' AND s.cod_mun_sede = '$municipio' ";
$resultado = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));
if($resultado->num_rows >= 1){
	while($row = $resultado->fetch_assoc()){
		$instituciones[$row['cod_inst']] = $row;
	}
}


// 3. Dias en los que se sirvio
$consulta = " SELECT ID, ANO, MES, D1 AS 'D01', D2 AS D02, D3 AS D03, D4 AS D04, D5 AS D05, D6 AS D06, D7 AS D07, D8 AS D08, D9 AS D09, D10, D11, D12, D13, D14, D15, D16, D17, D18, D19, D20, D21, D22 FROM planilla_dias WHERE ano = '$anno' AND mes = '$mes' ";
$resultado = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));
if($resultado->num_rows >= 1){
	while($row = $resultado->fetch_assoc()){
		$dias = $row;
	}
}


// 4. Totales por Institución
$consulta = " SELECT e.cod_inst, e.nom_inst, e.cod_mun_Sede, u.ciudad, u.Departamento, COALESCE(SUM(d1), 0) td01, COALESCE(SUM(d2), 0) td02, COALESCE(SUM(d3), 0) td03, COALESCE(SUM(d4), 0) td04, COALESCE(SUM(d5), 0) td05, COALESCE(SUM(d6), 0) td06, COALESCE(SUM(d7), 0) td07, COALESCE(SUM(d8), 0) td08, COALESCE(SUM(d9), 0) td09, COALESCE(SUM(d10), 0) td10, COALESCE(SUM(d11), 0) td11, COALESCE(SUM(d12), 0) td12, COALESCE(SUM(d13), 0) td13, COALESCE(SUM(d14), 0) td14, COALESCE(SUM(d15), 0) td15, COALESCE(SUM(d16), 0) td16, COALESCE(SUM(d17), 0) td17, COALESCE(SUM(d18), 0) td18, COALESCE(SUM(d19), 0) td19, COALESCE(SUM(d20), 0) td20, COALESCE(SUM(d21), 0) td21, COALESCE(SUM(d22), 0) td22 FROM entregas_res_$mes$periodoActual e
INNER JOIN ubicacion u ON e.cod_mun_sede = u.codigodane and u.ETC = 0
WHERE e.cod_mun_sede = $municipio GROUP BY e.cod_inst ";
$resultado = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));
if($resultado->num_rows >= 1){
	while($row = $resultado->fetch_assoc()){
		$totalesInstitucion[$row['cod_inst']] = $row;
	}
}

// 5.Entregas por sedes
$sedesInstitucion = array();
$consulta = " SELECT cod_inst, cod_sede, nom_sede, tipo_complem, COALESCE(SUM(d1), 0) d01, COALESCE(SUM(d2), 0) d02, COALESCE(SUM(d3), 0) d03, COALESCE(SUM(d4), 0) d04, COALESCE(SUM(d5), 0) d05, COALESCE(SUM(d6), 0) d06, COALESCE(SUM(d7), 0) d07, COALESCE(SUM(d8), 0) d08, COALESCE(SUM(d9), 0) d09, COALESCE(SUM(d10), 0) d10, COALESCE(SUM(d11), 0) d11, COALESCE(SUM(d12), 0) d12, COALESCE(SUM(d13), 0) d13, COALESCE(SUM(d14), 0) d14, COALESCE(SUM(d15), 0) d15, COALESCE(SUM(d16), 0) d16, COALESCE(SUM(d17), 0) d17, COALESCE(SUM(d18), 0) d18, COALESCE(SUM(d19), 0) d19, COALESCE(SUM(d20), 0) d20, COALESCE(SUM(d21), 0) d21, COALESCE(SUM(d22), 0) d22, (d1 + d2 + d3 + d4 + d5 + d6 + d7 + d8 + d9 + d10 + d11 + d12 + d13 + d14 + d15 + d16 + d17 + d18 + d19 + d20 + d21 + d22) numdias FROM entregas_res_$mes$periodoActual WHERE (d1 + d2 + d3 + d4 + d5 + d6 + d7 + d8 + d9 + d10 + d11 + d12 + d13 + d14 + d15 + d16 + d17 + d18 + d19 + d20 + d21 + d22) > 0 AND cod_mun_sede = $municipio GROUP BY cod_sede , tipo_complem ";
$resultado = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));
if($resultado->num_rows >= 1){
	while($row = $resultado->fetch_assoc()){
		$entregasSede[$row['cod_sede']][] = $row;
		$sedesInstitucion[$row['cod_inst']][] =  $row['cod_sede'];
	}
}

// Fechas de mes seleccionado
$consulta_fechas = "(SELECT ANO, MES, DIA FROM `planilla_semanas` WHERE MES = '$mes' ORDER BY SEMANA ASC, DIA ASC LIMIT 1) UNION ALL (SELECT ANO, MES, DIA FROM `planilla_semanas` WHERE MES = '$mes' ORDER BY SEMANA DESC, DIA DESC LIMIT 1)";
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
$pdf->SetDrawColor(0,0,0);


$x1 = $pdf->GetX();
$y1 = $pdf->GetY();
$altoEncabezado = 8;
$maximoLineas = 7;
$linea = 0;

foreach ($instituciones as $institucion) {
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

				$linea++;



				$x1 = $pdf->GetX();
				$y1 = $pdf->GetY();
				if($linea > 1){
					$pdf->Cell(49);
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

				//var_dump($entrega);

				$x1 = $pdf->GetX();
				$y1 = $pdf->GetY();

				//var_dump($entrega);
				$aux = $entrega['nom_sede'];
				$aux = substr($aux,0,30);
				$pdf->Cell(49,4,utf8_decode($aux),'R',0,'L',false);

				$pdf->SetXY($x1, $y1);
				$pdf->Cell(49,4,'','B',0,'L',false);


				$aux = $entrega['tipo_complem'];
				$pdf->Cell(14,4,utf8_decode($aux),'R',0,'C',false);

				$total = 0;
				for ($i=0; $i < 31 ; $i++) {
					$auxIndice = $i+1;
					if($auxIndice < 10){
						$auxIndice = 'd0'.$auxIndice;
					}
					else{
						$auxIndice = 'd'.$auxIndice;
					}
					//var_dump($auxIndice);
					$valor=0;
					if(isset($entrega[$auxIndice]) && $entrega[$auxIndice] != ''){
						$valor = $entrega[$auxIndice];
						$total = $total + $valor;
					}
					$pdf->Cell(7.5,4,utf8_decode($valor),'R',0,'C',false);
				}

				$pdf->Cell(16,4,utf8_decode($total),'R',0,'C',false);
				$aux = $entrega['numdias'];
				//$pdf->Cell(0,4,utf8_decode($aux),'R',0,'C',false);
				$pdf->Cell(0,4,utf8_decode($linea),'R',0,'C',false);
				$pdf->SetXY($x1, $y1);
				if($linea != $maximoLineas){

				}

				$pdf->Ln(4);
			}
		}
		$pdf->SetXY($x, $y);
		$aux = ($linea*4) + $altoEncabezado;
		$pdf->Cell(0,$aux,utf8_decode(''),1,0,'C',false);
		//Termina el contenido de las entregas
		include 'certificado_dias_footer.php';

	}


}
if(count($sedesInstitucion )>0){
	$pdf->Output();
} else {
	echo "<h2>No se han encontrado entregas en el mes correspondiente.</h2>";
}

function mesNombre($mes){
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