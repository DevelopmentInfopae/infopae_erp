<?php
error_reporting(E_ALL);
include '../../config.php';
require_once '../../autentication.php';
require('../../fpdf181/fpdf.php');
require_once '../../db/conexion.php';
include '../../php/funciones.php';
set_time_limit (0);
ini_set('memory_limit','6000M');
date_default_timezone_set('America/Bogota');
$tamannoFuente = 8;
class PDF extends FPDF{
  function Header()
  {
    // $logoInfopae = $_SESSION['p_Logo ETC'];
    // $logosEnte = 'imagenes/logos_planilla_ente.jpg';
    // $logosPae = 'imagenes/logos_planilla_pae.jpg';
    // $this->Image($logosEnte, 23 ,4, 43.39, 18.1,'jpg', '');
    // $this->Image($logosPae, 291.22 ,7.5, 61.38, 10.23,'jpg', '');

    $tamannoFuente = 11;
    $this->SetFont('Arial','B',$tamannoFuente);
    $this->SetTextColor(0,0,0);

    $this->Cell(91.9);
    $this->MultiCell(100,6,"CERTIFICADO DE ENTREGA DE RACIONES A INSTITUCIONES EDUCATIVAS" ,0,'C',false);

    $this->Ln(8);

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

//CREACION DEL PDF
// Creación del objeto de la clase heredada
$pdf= new PDF('P','mm',array(215.9,279.4));
$pdf->SetMargins(12, 12, 12, 12);
$pdf->SetAutoPageBreak(false,5);
$pdf->AliasNbPages();





//var_dump($_POST);
//var_dump($_SESSION);

$anno = $_SESSION['p_ano'];
$anno2d = substr($anno,2);
$mes = $_POST['mes'];
if($mes < 10){
  $mes = '0'.$mes;
}
$municipio = $_POST['municipio'];


//Dias Semanas
$consulta = "Select * from planilla_semanas where ano='$anno' and mes='$mes'";
$resultado = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));
if($resultado->num_rows >= 1){
  while($row = $resultado->fetch_assoc()){
    $diasSemanas[] = $row;
  }
}
//var_dump($diasSemanas);
//Termina Dias Semanas

//Instituciones
$consulta = "SELECT DISTINCT s.cod_inst,s.nom_inst,s.cod_mun_sede,u.ciudad,u.Departamento
FROM sedes$anno2d s
INNER JOIN sedes_cobertura AS sc ON (s.cod_inst=sc.cod_inst AND s.cod_Sede=sc.cod_Sede)
INNER JOIN ubicacion u ON(s.cod_mun_sede=u.codigoDANE) and u.ETC = 0
WHERE sc.ano='$anno' AND sc.mes='$mes' AND s.cod_mun_sede=$municipio";
$resultado = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));

if($resultado->num_rows >= 1){
  while($row = $resultado->fetch_assoc()){
    $instituciones[$row['cod_inst']] = $row;
  }
}

//diasEntregas
$consulta = "SELECT ID,ANO,MES,D1 AS 'D01',D2 AS D02,D3 AS D03,D4 AS D04,D5 AS D05,D6 AS D06,D7 AS D07,D8 AS D08,D9 AS D09,D10,D11,D12,D13,D14,D15,D16,D17,D18,D19,D20,D21,D22
from planilla_dias
WHERE ano='$anno' and mes='$mes'";
$resultado = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));
if($resultado->num_rows >= 1){
  while($row = $resultado->fetch_assoc()){
    $diasEntregas = $row;
  }
}
// var_dump($diasEntregas);
//Termina diasEntregas

//TotalesInstitucion
$consulta = "SELECT e.cod_inst,e.nom_inst,e.cod_mun_Sede,u.ciudad,u.Departamento, COALESCE (SUM(d1),0) td01, COALESCE (SUM(d2),0) td02, COALESCE (SUM(d3),0) td03, COALESCE (SUM(d4),0) td04, COALESCE (SUM(d5),0) td05, COALESCE (SUM(d6),0) td06, COALESCE (SUM(d7),0) td07, COALESCE (SUM(d8),0) td08, COALESCE (SUM(d9),0) td09, COALESCE (SUM(d10),0) td10, COALESCE (SUM(d11),0) td11, COALESCE (SUM(d12),0) td12, COALESCE (SUM(d13),0) td13, COALESCE (SUM(d14),0) td14, COALESCE (SUM(d15),0) td15, COALESCE (SUM(d16),0) td16, COALESCE (SUM(d17),0) td17, COALESCE (SUM(d18),0)td18, COALESCE (SUM(d19),0) td19, COALESCE (SUM(d20),0) td20, COALESCE (SUM(d21),0) td21, COALESCE (SUM(d22),0) td22
FROM entregas_res_$mes$anno2d e
INNER JOIN ubicacion u ON e.cod_mun_sede=u.codigodane and u.ETC = 0
WHERE e.cod_mun_sede=$municipio
GROUP BY e.cod_inst";
$resultado = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));
if($resultado->num_rows >= 1){
  while($row = $resultado->fetch_assoc()){
    $TotalesInstitucion[$row['cod_inst']] = $row;
  }
}
// var_dump($TotalesInstitucion);
//Termina TotalesInstitucion

//EntregasSedes
$entregasSedes = array();
$consulta = "SELECT cod_inst,cod_sede,nom_sede, tipo_complem, COALESCE (SUM(d1),0) d01, COALESCE (SUM(d2),0) d02, COALESCE (SUM(d3),0) d03, COALESCE (SUM(d4),0) d04, COALESCE (SUM(d5),0) d05, COALESCE (SUM(d6),0) d06, COALESCE (SUM(d7),0) d07, COALESCE (SUM(d8),0) d08, COALESCE (SUM(d9),0) d09, COALESCE (SUM(d10),0) d10, COALESCE (SUM(d11),0) d11, COALESCE (SUM(d12),0) d12, COALESCE (SUM(d13),0) d13, COALESCE (SUM(d14),0) d14, COALESCE (SUM(d15),0) d15, COALESCE (SUM(d16),0) d16, COALESCE (SUM(d17),0) d17, COALESCE (SUM(d18),0) d18, COALESCE (SUM(d19),0) d19, COALESCE (SUM(d20),0) d20, COALESCE (SUM(d21),0) d21, COALESCE (SUM(d22),0) d22, (d1+d2+d3+d4+d5+d6+d7+d8+d9+d10+d11+d12+d13+d14+d15+d16+d17+d18+d19+d20+d21+d22) numdias FROM entregas_res_$mes$anno2d WHERE (d1+d2+d3+d4+d5+d6+d7+d8+d9+d10+d11+d12+d13+d14+d15+d16+d17+d18+d19+d20+d21+d22)>0 AND cod_mun_sede=$municipio GROUP BY cod_sede,tipo_complem";
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
//Termina EntregasSedes
if(count($entregasSedes)>0){
	// Se van a separar los dias que corresponden a cada semana
	$semanaIndice = 0;
	$numeroSemana = -1;
	foreach ($diasSemanas as $diaSemana){
		if($semanaIndice != $diaSemana['SEMANA']){
			$semanaIndice = $diaSemana['SEMANA'];
			$numeroSemana++;
		}
		$diasSemana[$numeroSemana][] = $diaSemana;
	}







	foreach ($instituciones as $institucion){
		$pdf->AddPage();
		$pdf->SetTextColor(0,0,0);
		$pdf->SetFillColor(192,192,192);
		$pdf->SetDrawColor(0,0,0);

		$tamannoFuente = 8;
		$pdf->SetFont('Arial','B',$tamannoFuente);

		$pdf->Cell(0,6,'DATOS GENERALES ',0,0,'C',true);
		$pdf->Ln(10);

		$x = $pdf->GetX();
		$y = $pdf->GetY();

		$pdf->SetFont('Arial','B',$tamannoFuente);
		$pdf->Cell(32,5,utf8_decode('OPERADOR:'),'R',0,'L',false);
		$pdf->SetFont('Arial','',$tamannoFuente);
		$pdf->Cell(110,5,$_SESSION['p_Operador'],'R',0,'L',false);
		$pdf->SetFont('Arial','B',$tamannoFuente);
		$pdf->Cell(25,5,utf8_decode('CONTRATO N°:'),'R',0,'L',false);
		$pdf->SetFont('Arial','',$tamannoFuente);
		$pdf->Cell(30,5,$_SESSION['p_Contrato'],0,0,'L',false);
		$pdf->SetX($x);
		$pdf->Cell(0,5,'','B',5,'C',false);

		$pdf->SetFont('Arial','B',$tamannoFuente);
		$pdf->Cell(32,5,utf8_decode('INSTITUCIÓN:'),'R',0,'L',false);
		$pdf->SetFont('Arial','',$tamannoFuente);
		$pdf->Cell(110,5,$institucion['nom_inst'],'R',0,'L',false);
		$pdf->SetFont('Arial','B',$tamannoFuente);
		$pdf->Cell(25,5,utf8_decode('CÓDIGO DANE:'),'R',0,'L',false);
		$pdf->SetFont('Arial','',$tamannoFuente);
		$pdf->Cell(30,5,$institucion['cod_inst'],0,0,'L',false);
		$pdf->SetX($x);
		$pdf->Cell(0,5,'','B',5,'C',false);

		$pdf->SetFont('Arial','B',$tamannoFuente);
		$pdf->Cell(32,5,utf8_decode('DEPARTAMENTO:'),'R',0,'L',false);
		$pdf->SetFont('Arial','',$tamannoFuente);
		$pdf->Cell(110,5,strtoupper($institucion['Departamento']),'R',0,'L',false);
		$pdf->SetFont('Arial','B',$tamannoFuente);
		$pdf->Cell(25,5,utf8_decode('CÓDIGO DANE:'),'R',0,'L',false);
		$pdf->SetFont('Arial','',$tamannoFuente);
		$pdf->Cell(30,5,$_SESSION['p_CodDepartamento'],0,0,'L',false);
		$pdf->SetX($x);
		$pdf->Cell(0,5,'','B',5,'C',false);

		$pdf->SetFont('Arial','B',$tamannoFuente);
		$pdf->Cell(32,5,utf8_decode('MUNICIPIO:'),'R',0,'L',false);
		$pdf->SetFont('Arial','',$tamannoFuente);
		$pdf->Cell(110,5,$institucion['ciudad'],'R',0,'L',false);
		$pdf->SetFont('Arial','B',$tamannoFuente);
		$pdf->Cell(25,5,utf8_decode('CÓDIGO DANE:'),'R',0,'L',false);
		$pdf->SetFont('Arial','',$tamannoFuente);
		$pdf->Cell(30,5,$institucion['cod_mun_sede'],0,0,'L',false);
		$pdf->SetX($x);
		$pdf->Cell(0,5,'','B',5,'C',false);

		$pdf->SetFont('Arial','B',$tamannoFuente);
		$pdf->Cell(32,5,utf8_decode('FECHA EJECUCIÓN:'),'R',0,'L',false);
		$pdf->SetFont('Arial','',$tamannoFuente);
		$pdf->Cell(15,5,'Desde:','R',0,'L',false);
		$pdf->Cell(40,5,utf8_decode(''),'R',0,'L',false);
		$pdf->Cell(15,5,'Hasta:','R',0,'L',false);
		$pdf->Cell(40,5,'','R',0,'L',false);
		$pdf->SetX($x);
		$pdf->Cell(0,5,'','B',5,'C',false);

		$pdf->SetFont('Arial','B',$tamannoFuente);
		$pdf->Cell(32,5,utf8_decode('NOMBRE RECTOR:'),'R',0,'L',false);
		$pdf->SetFont('Arial','',$tamannoFuente);
		$pdf->Cell(0,5,'','R',0,'L',false);
		$pdf->SetX($x);
		$pdf->Cell(0,5,'',0,5,'C',false);

		$pdf->SetXY($x, $y);
		$pdf->Cell(0,30,'',1,0,'C',false);
		$pdf->Ln(35);

		$pdf->SetFont('Arial','B',$tamannoFuente);
		$pdf->Cell(0,6,utf8_decode('CERTIFICACIÓN'),0,0,'C',true);
		$pdf->Ln(8);
		$pdf->SetFont('Arial','',$tamannoFuente);
		$pdf->MultiCell(0,4,utf8_decode("El suscrito Rector de la Institución Educativa citada en el encabezado, certifica que se entregaron las siguientes raciones,    en las fechas señaladas y de acuerdo con la siguiente distribución:"),0,'C',false);
		$pdf->Ln(3);

		$x = $pdf->GetX();
		$y = $pdf->GetY();
		$pdf->SetXY($x, $y);
		$pdf->Cell(0,11,'',0,0,'C',true);

		$pdf->SetXY($x, $y+2);
		$pdf->SetFont('Arial','B',$tamannoFuente-1);
		$pdf->MultiCell(45,4,utf8_decode("NOMBRE DEL ESTABLECIMIENTO U CENTRO EDUCATIVO"),0,'C',false);
		$pdf->SetXY($x, $y);
		$pdf->Cell(45,11,'','R',0,'C',false);

		$aux_x = $pdf->GetX();
		$aux_y = $pdf->GetY();
		$pdf->SetXY($aux_x, $aux_y+2.5);
		$pdf->SetFont('Arial','B',$tamannoFuente-1);
		$pdf->MultiCell(15,3,utf8_decode("TIPO RACIÓN"),0,'C',false);
		$pdf->SetXY($aux_x, $aux_y);
		$pdf->Cell(15,11,'','R',0,'C',false);

		$aux_x = $pdf->GetX();
		$aux_y = $pdf->GetY();
		$pdf->SetXY($aux_x, $aux_y);
		$pdf->SetFont('Arial','B',$tamannoFuente-1);
		$pdf->Cell(23,4,'SEMANA 1','B',0,'C',false);
		$pdf->SetFont('Arial','B',$tamannoFuente-2);
		$pdf->SetXY($aux_x, $aux_y+6);
		$pdf->MultiCell(13,2,utf8_decode("N°RACION DIA"),0,'C',false);
		$pdf->SetXY($aux_x, $aux_y+4);
		$pdf->Cell(13,7,'','R',0,'C',false);
		$pdf->SetXY($aux_x+13, $aux_y+6);
		$pdf->MultiCell(10,2,utf8_decode("N° DIAS"),0,'C',false);
		$pdf->SetXY($aux_x, $aux_y);
		$pdf->Cell(23,11,'','R',0,'C',false);

		$aux_x = $pdf->GetX();
		$aux_y = $pdf->GetY();
		$pdf->SetXY($aux_x, $aux_y);
		$pdf->SetFont('Arial','B',$tamannoFuente-1);
		$pdf->Cell(23,4,'SEMANA 2','B',0,'C',false);
		$pdf->SetFont('Arial','B',$tamannoFuente-2);
		$pdf->SetXY($aux_x, $aux_y+6);
		$pdf->MultiCell(13,2,utf8_decode("N°RACION DIA"),0,'C',false);
		$pdf->SetXY($aux_x, $aux_y+4);
		$pdf->Cell(13,7,'','R',0,'C',false);
		$pdf->SetXY($aux_x+13, $aux_y+6);
		$pdf->MultiCell(10,2,utf8_decode("N° DIAS"),0,'C',false);
		$pdf->SetXY($aux_x, $aux_y);
		$pdf->Cell(23,11,'','R',0,'C',false);

		$aux_x = $pdf->GetX();
		$aux_y = $pdf->GetY();
		$pdf->SetXY($aux_x, $aux_y);
		$pdf->SetFont('Arial','B',$tamannoFuente-1);
		$pdf->Cell(23,4,'SEMANA 3','B',0,'C',false);
		$pdf->SetFont('Arial','B',$tamannoFuente-2);
		$pdf->SetXY($aux_x, $aux_y+6);
		$pdf->MultiCell(13,2,utf8_decode("N°RACION DIA"),0,'C',false);
		$pdf->SetXY($aux_x, $aux_y+4);
		$pdf->Cell(13,7,'','R',0,'C',false);
		$pdf->SetXY($aux_x+13, $aux_y+6);
		$pdf->MultiCell(10,2,utf8_decode("N° DIAS"),0,'C',false);
		$pdf->SetXY($aux_x, $aux_y);
		$pdf->Cell(23,11,'','R',0,'C',false);

		$aux_x = $pdf->GetX();
		$aux_y = $pdf->GetY();
		$pdf->SetXY($aux_x, $aux_y);
		$pdf->SetFont('Arial','B',$tamannoFuente-1);
		$pdf->Cell(23,4,'SEMANA 4','B',0,'C',false);
		$pdf->SetFont('Arial','B',$tamannoFuente-2);
		$pdf->SetXY($aux_x, $aux_y+6);
		$pdf->MultiCell(13,2,utf8_decode("N°RACION DIA"),0,'C',false);
		$pdf->SetXY($aux_x, $aux_y+4);
		$pdf->Cell(13,7,'','R',0,'C',false);
		$pdf->SetXY($aux_x+13, $aux_y+6);
		$pdf->MultiCell(10,2,utf8_decode("N° DIAS"),0,'C',false);
		$pdf->SetXY($aux_x, $aux_y);
		$pdf->Cell(23,11,'','R',0,'C',false);

		$aux_x = $pdf->GetX();
		$aux_y = $pdf->GetY();
		$pdf->SetXY($aux_x, $aux_y);
		$pdf->SetFont('Arial','B',$tamannoFuente-1);
		$pdf->Cell(23,4,'SEMANA 5','B',0,'C',false);
		$pdf->SetFont('Arial','B',$tamannoFuente-2);
		$pdf->SetXY($aux_x, $aux_y+6);
		$pdf->MultiCell(13,2,utf8_decode("N°RACION DIA"),0,'C',false);
		$pdf->SetXY($aux_x, $aux_y+4);
		$pdf->Cell(13,7,'','R',0,'C',false);
		$pdf->SetXY($aux_x+13, $aux_y+6);
		$pdf->MultiCell(10,2,utf8_decode("N° DIAS"),0,'C',false);
		$pdf->SetXY($aux_x, $aux_y);
		$pdf->Cell(23,11,'','R',0,'C',false);

		$aux_x = $pdf->GetX();
		$aux_y = $pdf->GetY();
		$pdf->SetXY($aux_x, $aux_y+2.5);
		$pdf->SetFont('Arial','B',$tamannoFuente-1);
		$pdf->MultiCell(0,3,utf8_decode("TOTAL RACIONES"),0,'C',false);




		$pdf->SetXY($x, $y);
		$pdf->Cell(0,11,'','B',0,'C',false);
		$pdf->Ln(11);



		$totalesSemanas = array(0,0,0,0,0);


		//Impresion de las entregas de cada mes
		$entregasSedesInstitucion = $entregasSedes[$institucion['cod_inst']];
		//var_dump($entregasSedesInstitucion);

		$banderaNombres = 0;
		$lineas = 0;
		$lineasTotales = 0;
		$maxCaracteres = 27;
		foreach ($entregasSedesInstitucion as $entregasSedeInstitucion){
			$lineasTotales++;
			$aux_x = $pdf->GetX();
			$aux_y = $pdf->GetY();
			if($banderaNombres == 0){
				$nombre = $entregasSedeInstitucion['nom_sede'];
				$banderaNombres++;
			}else{
				if($nombre != $entregasSedeInstitucion['nom_sede']){
					$linea = $lineas * 4;
					if($lineas<= 1){
						$nombre = substr($nombre,0,$maxCaracteres);
					}
					$pdf->SetXY($aux_x, $aux_y-$linea);
					$pdf->MultiCell(45,4,utf8_decode($nombre),0,'L',false);
					$pdf->SetXY($aux_x, $aux_y-$linea);
					$pdf->Cell(45,$linea,'','B',0,'C',false);



					$nombre = $entregasSedeInstitucion['nom_sede'];
					$lineas = 0;
					$pdf->SetXY($aux_x, $aux_y);
				}
			}
			$lineas++;







			$pdf->SetFont('Arial','',$tamannoFuente-1);
			$pdf->Cell(45,4,'','R',0,'C',false);
			$pdf->Cell(15,4,$entregasSedeInstitucion['tipo_complem'],'R',0,'C',false);



			$indice = 0;
			$totalSemana = 0;
			for($i = 0; $i < 5 ; $i++){
				if(isset($diasSemana[$i])){
					$total = 0;
					$indicePrint = 0;
					foreach ($diasSemana[$i] as $diaSemana){





						//var_dump($diaSemana);
						$indicePrint++;
						$indice++;
						if($indice < 10){
							$aux = 'd0'.$indice;
						} else{
							$aux = 'd'.$indice;
						}
						//echo  '<br>'.$aux.' - '.$entregasSedeInstitucion[$aux];

						$total = $total + $entregasSedeInstitucion[$aux];
						$totalSemana = $totalSemana + $entregasSedeInstitucion[$aux];
						$totalesSemanas[$i] = $totalesSemanas[$i] + $entregasSedeInstitucion[$aux];



					}
					$pdf->Cell(13,4,$total,'R',0,'C',false);
					$pdf->Cell(10,4,$indicePrint,'R',0,'C',false);
				}else{
					$pdf->Cell(13,4,'','R',0,'C',false);
					$pdf->Cell(10,4,'','R',0,'C',false);
				}
			}


			$pdf->Cell(0,4,$totalSemana,0,0,'C',false);
			$pdf->SetX($aux_x);
			$pdf->Cell(45);
			$pdf->Cell(0,4,'','B',0,'C',false);
			$pdf->Ln(4);












		}
		$pdf->SetXY($aux_x, $aux_y-$linea+4);

		if($lineas<= 1){
			$nombre = substr($nombre,0,$maxCaracteres);
		}

		$pdf->MultiCell(45,4,utf8_decode($nombre),0,'L',false);
		$pdf->SetXY($aux_x, $aux_y-$linea);
		$pdf->Cell(45,$linea+4,'','B',0,'C',false);
		$pdf->Ln($linea+4);



		$pdf->SetFont('Arial','B',$tamannoFuente-1);
		//$pdf->SetXY($x, $y+35);
		$pdf->Cell(60,4,'TOTAL:','R',0,'L',false);
		$pdf->SetFont('Arial','',$tamannoFuente-1);

		$granTotal = 0;
		for($i = 0; $i < 5 ; $i++){


			$pdf->Cell(23,4, $totalesSemanas[$i],'R',0,'C',false);
			$granTotal = $granTotal + $totalesSemanas[$i];
		}


		$pdf->Cell(0,4,$granTotal,0,0,'C',false);

		// Cuadro  exterior tabla
		$pdf->SetXY($x, $y);
		//var_dump($lineasTotales);
		$pdf->Cell(0,$lineasTotales*4+15,'',1,0,'C',false);
		//Termina la tabla de sedes

		$pdf->Ln(70);
		$pdf->SetFont('Arial','',$tamannoFuente-1);
		$pdf->Cell(0,4,utf8_decode('CAJMPS = Complemento Alimentario Jornada Mañana / Complemento Alimentario Jornada Tarde preparado en sitio'),0,4,'L',false);
		$pdf->Cell(0,4,utf8_decode('APS = Almuerzo preparado en sitio población vulnerable'),0,4,'L',false);
		$pdf->Cell(0,4,utf8_decode('CAJMRI = complemento alimentario jornada mañana ración industrializada.'),0,4,'L',false);

		// Tebla población
		$x = $pdf->GetX();
		$y = $pdf->GetY();
		$pdf->Cell(0,8,utf8_decode(''),0,0,'L',true);
		$pdf->SetXY($x, $y);
		$pdf->SetFont('Arial','B',$tamannoFuente-1);
		$pdf->Cell(60,8,utf8_decode('DESCRIPCIÓN'),'R',0,'C',false);

		$aux_x = $pdf->GetX();
		$aux_y = $pdf->GetY();
		$pdf->SetFont('Arial','B',$tamannoFuente-1.5);
		$pdf->MultiCell(26,4,utf8_decode("TOTAL RACIONES CAJMPS"),0,'C',false);
		$pdf->SetXY($aux_x, $aux_y);
		$pdf->Cell(26,8,utf8_decode(''),'R',0,'C',false);

		$aux_x = $pdf->GetX();
		$aux_y = $pdf->GetY();
		$pdf->MultiCell(26,4,utf8_decode("TOTAL\nRACIONES APS"),0,'C',false);
		$pdf->SetXY($aux_x, $aux_y);
		$pdf->Cell(26,8,utf8_decode(''),'R',0,'C',false);

		$aux_x = $pdf->GetX();
		$aux_y = $pdf->GetY();
		$pdf->MultiCell(26,4,utf8_decode("TOTAL\nRACIONES APS"),0,'C',false);
		$pdf->SetXY($aux_x, $aux_y);
		$pdf->Cell(26,8,utf8_decode(''),'R',0,'C',false);

		$aux_x = $pdf->GetX();
		$aux_y = $pdf->GetY();
		$pdf->MultiCell(26,4,utf8_decode("TOTAL RACIONES\nCAJMRI"),0,'C',false);
		$pdf->SetXY($aux_x, $aux_y);
		$pdf->Cell(26,8,utf8_decode(''),'R',0,'C',false);


		$aux_x = $pdf->GetX();
		$aux_y = $pdf->GetY();
		$pdf->MultiCell(0,4,utf8_decode("No. DE TITULARES DE\nDERECHO"),0,'C',false);
		$pdf->SetXY($aux_x, $aux_y);
		$pdf->Cell(0,8,utf8_decode(''),'R',0,'C',false);

		$pdf->SetXY($x, $y);
		$pdf->Cell(0,8,utf8_decode(''),'B',0,'L',false);
		$pdf->SetXY($x, $y);
		$pdf->Cell(0,28,utf8_decode(''),1,0,'L',false);

		$pdf->SetXY($x, $y);
		$pdf->Ln(8);


		$pdf->SetFont('Arial','',$tamannoFuente-1);
		$aux_x = $pdf->GetX();
		$aux_y = $pdf->GetY();
		$pdf->SetXY($aux_x, $aux_y);
		$pdf->Cell(60,4,utf8_decode('POBLACIÓN EN CONDICIÓN DE DISCAPACIDAD'),'R',0,'L',false);
		$pdf->Cell(26,4,utf8_decode(''),'R',0,'C',false);
		$pdf->Cell(26,4,utf8_decode(''),'R',0,'C',false);
		$pdf->Cell(26,4,utf8_decode(''),'R',0,'C',false);
		$pdf->Cell(26,4,utf8_decode(''),'R',0,'C',false);
		$pdf->Cell(0,4,utf8_decode(''),'R',0,'C',false);
		$pdf->SetXY($aux_x, $aux_y);
		$pdf->Cell(0,4,utf8_decode(''),'B',0,'L',false);
		$pdf->Ln(4);

		$aux_x = $pdf->GetX();
		$aux_y = $pdf->GetY();
		$pdf->SetXY($aux_x, $aux_y);
		$pdf->Cell(60,4,utf8_decode('POBLACIÓN VICTIMA DEL CONFLICTO ARMADO'),'R',0,'L',false);
		$pdf->Cell(26,4,utf8_decode(''),'R',0,'C',false);
		$pdf->Cell(26,4,utf8_decode(''),'R',0,'C',false);
		$pdf->Cell(26,4,utf8_decode(''),'R',0,'C',false);
		$pdf->Cell(26,4,utf8_decode(''),'R',0,'C',false);
		$pdf->Cell(0,4,utf8_decode(''),'R',0,'C',false);
		$pdf->SetXY($aux_x, $aux_y);
		$pdf->Cell(0,4,utf8_decode(''),'B',0,'L',false);
		$pdf->Ln(4);

		$aux_x = $pdf->GetX();
		$aux_y = $pdf->GetY();
		$pdf->SetXY($aux_x, $aux_y);
		$pdf->Cell(60,4,utf8_decode('COMUNIDADES ÉTNICAS'),'R',0,'L',false);
		$pdf->Cell(26,4,utf8_decode(''),'R',0,'C',false);
		$pdf->Cell(26,4,utf8_decode(''),'R',0,'C',false);
		$pdf->Cell(26,4,utf8_decode(''),'R',0,'C',false);
		$pdf->Cell(26,4,utf8_decode(''),'R',0,'C',false);
		$pdf->Cell(0,4,utf8_decode(''),'R',0,'C',false);
		$pdf->SetXY($aux_x, $aux_y);
		$pdf->Cell(0,4,utf8_decode(''),'B',0,'L',false);
		$pdf->Ln(4);

		$aux_x = $pdf->GetX();
		$aux_y = $pdf->GetY();
		$pdf->SetXY($aux_x, $aux_y);
		$pdf->Cell(60,4,utf8_decode('POBLACIÓN MAYORITARIA'),'R',0,'L',false);
		$pdf->Cell(26,4,utf8_decode(''),'R',0,'C',false);
		$pdf->Cell(26,4,utf8_decode(''),'R',0,'C',false);
		$pdf->Cell(26,4,utf8_decode(''),'R',0,'C',false);
		$pdf->Cell(26,4,utf8_decode(''),'R',0,'C',false);
		$pdf->Cell(0,4,utf8_decode(''),'R',0,'C',false);
		$pdf->SetXY($aux_x, $aux_y);
		$pdf->Cell(0,4,utf8_decode(''),'B',0,'L',false);
		$pdf->Ln(4);

		$pdf->SetFont('Arial','B',$tamannoFuente-1);
		$aux_x = $pdf->GetX();
		$aux_y = $pdf->GetY();
		$pdf->SetXY($aux_x, $aux_y);
		$pdf->Cell(60,4,utf8_decode('TOTAL'),'R',0,'L',false);
		$pdf->Cell(26,4,utf8_decode(''),'R',0,'C',false);
		$pdf->Cell(26,4,utf8_decode(''),'R',0,'C',false);
		$pdf->Cell(26,4,utf8_decode(''),'R',0,'C',false);
		$pdf->Cell(26,4,utf8_decode(''),'R',0,'C',false);
		$pdf->Cell(0,4,utf8_decode(''),'R',0,'C',false);
		$pdf->SetXY($aux_x, $aux_y);
		$pdf->Cell(0,4,utf8_decode(''),'B',0,'L',false);


		$pdf->Ln(8);
		$x = $pdf->GetX();
		$y = $pdf->GetY();
		$pdf->Cell(0,4,utf8_decode('OBSERVACIONES'),'B',0,'C',true);
		$pdf->SetFont('Arial','',$tamannoFuente-1);
		$pdf->SetXY($x, $y+4);
		$pdf->MultiCell(0,4,utf8_decode("lorem ipsum"),0,'L',false);
		$pdf->SetXY($x, $y);
		$pdf->Cell(0,12,utf8_decode(''),1,0,'C',false);


		$pdf->Ln(16);
		$pdf->SetFont('Arial','',$tamannoFuente);
		$pdf->Cell(0,4,utf8_decode('La presente certificación se expide como soporte de pago y con base en el registro diario de Titulares de Derecho, que se'),0,4,'L',false);
		$pdf->Cell(0,4,utf8_decode('diligencia en cada Institución Educativa atendida.'),0,4,'L',false);


		$pdf->Ln(4);
		$pdf->SetFont('Arial','B',$tamannoFuente);
		$pdf->Cell(50,4,utf8_decode('PARA CONSTANCIA SE FIRMA EN:'),0,0,'L',false);
		$pdf->Cell(30,4,utf8_decode(''),'B',0,'L',false);
		$pdf->Cell(20,4,utf8_decode(' FECHA: DIA'),0,0,'L',false);
		$pdf->Cell(35,4,utf8_decode(''),'B',0,'L',false);
		$pdf->Cell(15,4,utf8_decode('DEL AÑO'),0,0,'L',false);
		$pdf->Cell(15,4,utf8_decode(''),'B',0,'L',false);


		$pdf->Ln(8);
		$x = $pdf->GetX();
		$y = $pdf->GetY();
		$pdf->SetXY($x, $y);
		$pdf->Cell(0,12,utf8_decode(''),1,0,'C',false);

		$pdf->Ln(14);
		$x = $pdf->GetX();
		$y = $pdf->GetY();
		$pdf->SetFont('Arial','',$tamannoFuente-1);
		$pdf->Cell(0,4,utf8_decode('Impreso por Software InfoPae'),0,0,'L',false);
		$link = 'http://www.infopae.com.co';
		$pdf->SetXY($x+45, $y);
		$pdf->Write(4,'www.infopae.com.co',$link);
	}//Termina el for de instituciones










	$pdf->Output();

} else{
	echo "<h2>No se han encontrado entregas en el mes correspondiente.</h2>";
}
