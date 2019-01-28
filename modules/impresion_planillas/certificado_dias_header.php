<?php
$pdf->SetFont('Arial','B',$tamannoFuente);
$pdf->Cell(0,6,'DATOS GENERALES ',0,0,'C',true);
$pdf->Ln(10);

$x = $pdf->GetX();
$y = $pdf->GetY();

$x1 = $pdf->GetX();
$y1 = $pdf->GetY();
$pdf->Cell(32,6,'OPERADOR:','R',0,'L',false);
$pdf->SetFont('Arial','',$tamannoFuente);







$aux = $_SESSION['p_Operador'];
$pdf->Cell(198,6,utf8_decode($aux),'R',0,'L',false);
$pdf->SetFont('Arial','B',$tamannoFuente);
$pdf->Cell(23,6,utf8_decode('CONTRATO N°:'),'R',0,'L',false);
$pdf->SetFont('Arial','',$tamannoFuente);

$aux = $_SESSION['p_Contrato'];
$pdf->Cell(0,6,utf8_decode($aux),0,0,'L',false);
$pdf->SetXY($x1, $y1);
$pdf->Cell(0,6,'','B',0,'L',false);
$pdf->Ln(6);

$x1 = $pdf->GetX();
$y1 = $pdf->GetY();
$pdf->SetFont('Arial','B',$tamannoFuente);
$pdf->Cell(32,6,utf8_decode('INSTITUCIÓN:'),'R',0,'L',false);
$pdf->SetFont('Arial','',$tamannoFuente);
$pdf->Cell(198,6,utf8_decode($institucion['nom_inst']),'R',0,'L',false);


$pdf->SetFont('Arial','B',$tamannoFuente);
$pdf->Cell(23,6,utf8_decode('CÓDIGO DANE:'),'R',0,'L',false);
$pdf->SetFont('Arial','',$tamannoFuente);


$aux = $institucion['cod_mun_sede'];
$pdf->Cell(0,6,$aux,0,0,'L',false);
$pdf->SetXY($x1, $y1);
$pdf->Cell(0,6,'','B',0,'L',false);
$pdf->Ln(6);

$x1 = $pdf->GetX();
$y1 = $pdf->GetY();
$pdf->SetFont('Arial','B',$tamannoFuente);
$pdf->Cell(32,6,'DEPARTAMENTO:','R',0,'L',false);
$pdf->SetFont('Arial','',$tamannoFuente);





$aux = $_SESSION['p_Departamento'];
$pdf->Cell(198,6,utf8_decode($aux),'R',0,'L',false);
$pdf->SetFont('Arial','B',$tamannoFuente);
$pdf->Cell(23,6,utf8_decode('CÓDIGO DANE:'),'R',0,'L',false);
$pdf->SetFont('Arial','',$tamannoFuente);

$aux = $_SESSION['p_CodDepartamento'];
$pdf->Cell(0,6,utf8_decode($aux),0,0,'L',false);
$pdf->SetXY($x1, $y1);
$pdf->Cell(0,6,'','B',0,'L',false);
$pdf->Ln(6);

$x1 = $pdf->GetX();
$y1 = $pdf->GetY();
$pdf->SetFont('Arial','B',$tamannoFuente);
$pdf->Cell(32,6,'MUNICIPIO:','R',0,'L',false);
$pdf->SetFont('Arial','',$tamannoFuente);
$pdf->Cell(198,6,utf8_decode($_POST['municipioNm']),'R',0,'L',false);
$pdf->SetFont('Arial','B',$tamannoFuente);
$pdf->Cell(23,6,utf8_decode('CÓDIGO DANE:'),'R',0,'L',false);
$pdf->SetFont('Arial','',$tamannoFuente);
$aux = $_POST['municipioNm'];
$pdf->Cell(0,6,utf8_decode($aux),0,0,'L',false);
$pdf->SetXY($x1, $y1);
$pdf->Cell(0,6,'','B',0,'L',false);
$pdf->Ln(6);

$x1 = $pdf->GetX();
$y1 = $pdf->GetY();
$pdf->SetFont('Arial','B',$tamannoFuente);
$pdf->Cell(32,6,utf8_decode('FECHA EJECUCIÓN:'),'R',0,'L',false);
$pdf->SetFont('Arial','',$tamannoFuente);
$pdf->Cell(15,6,'Desde','R',0,'L',false);
$pdf->Cell(84,6,utf8_decode($fechas[0]["DIA"]." de ".mesNombre($fechas[0]["MES"])." ". $fechas[0]["ANO"]),'R',0,'L',false);
$pdf->Cell(15,6,'Hasta','R',0,'L',false);
$pdf->Cell(84,6,utf8_decode($fechas[1]["DIA"]." de ".mesNombre($fechas[1]["MES"])." ". $fechas[1]["ANO"]),'R',0,'L',false);
$pdf->SetFont('Arial','',$tamannoFuente);
$pdf->Cell(0,6,'',0,0,'L',false);
$pdf->SetXY($x1, $y1);
$pdf->Cell(0,6,'','B',0,'L',false);
$pdf->Ln(6);

$x1 = $pdf->GetX();
$y1 = $pdf->GetY();
$pdf->SetFont('Arial','B',$tamannoFuente);
$pdf->Cell(32,6,utf8_decode('NOMBRE RECTOR:'),'R',0,'L',false);
$pdf->SetFont('Arial','',$tamannoFuente);
$pdf->Cell(0,6,utf8_decode($institucion["nombre_rector"]),0,0,'L',false);
$pdf->SetXY($x1, $y1);
$pdf->Cell(0,6,'','B',0,'L',false);
$pdf->Ln(6);

$pdf->SetXY($x, $y);
$pdf->Cell(0,36,'',1,0,'L',false);
$pdf->Ln(42);

$pdf->SetFont('Arial','B',$tamannoFuente);
$pdf->Cell(0,6,utf8_decode('CERTIFICACIÓN'),0,0,'C',true);
$pdf->Ln(10);

$pdf->SetFont('Arial','',$tamannoFuente);
$pdf->Cell(0,6,utf8_decode('El suscrito Rector de la Institución Educativa citada en el encabezado, certifica que se entregaron las siguientes raciones, en las fechas señaladas y de acuerdo con la siguiente distribución: '),0,0,'C',false);
$pdf->Ln(10);



// Impresión de cantidades, día con día
$pdf->SetFont('Arial','B',$tamannoFuente-1);
$x = $pdf->GetX();
$y = $pdf->GetY();
$x1 = $pdf->GetX();
$y1 = $pdf->GetY();
$pdf->Cell(0,8,utf8_decode(''),'B',0,'L',true);
$pdf->SetXY($x1, $y1);
$pdf->MultiCell(49,4,utf8_decode("NOMBRE DEL ESTABLECIMIENTO U CENTRO EDUCATIVO"),0,'C',false);
$x1 = $x1 + 49;
$pdf->SetXY($x1, $y1);
$pdf->MultiCell(14,4,utf8_decode("TIPO RACIÓN"),0,'C',false);
$x1 = $x1 + 14;
$pdf->SetXY($x1, $y1);
$pdf->Cell(232.5,4,utf8_decode('N° DE RACIONES POR DÍA'),'B',0,'C',false);
$y2 = $y1 + 4;
$pdf->SetXY($x1, $y2);

$auxIndice = 1;
for ($i=0; $i < 31 ; $i++) {
	$dia = '';
	$aux = $auxIndice;
	if($aux < 10){
		$aux = 'D0'.$aux;
	}else{
		$aux = 'D'.$aux;
	}
	if(isset($dias[$aux]) && $dias[$aux] != ''){
		$dia = $dias[$aux];
	}
	$pdf->Cell(7.5,4,utf8_decode($dia),'R',0,'C',false);
	$auxIndice++;
}

$x1 = $pdf->GetX();
$pdf->SetXY($x1, $y1);
$pdf->MultiCell(16,4,utf8_decode("TOTAL RACIONES"),0,'C',false);
$x1 = $x1 + 16;
$pdf->SetXY($x1, $y1);
$pdf->MultiCell(0,4,utf8_decode("TOTAL DIAS "),0,'C',false);

$pdf->SetXY($x, $y);
$pdf->Cell(49,8,utf8_decode(''),'R',0,'L',false);
$pdf->Cell(14,8,utf8_decode(''),'R',0,'L',false);
$pdf->Cell(232.5,8,utf8_decode(''),'R',0,'L',false);
$pdf->Cell(16,8,utf8_decode(''),'R',0,'L',false);
$pdf->Cell(0,8,utf8_decode(''),0,0,'L',false);
// Termina impresión de cantidades, día con día
$pdf->Ln(8);
$pdf->SetFont('Arial','',$tamannoFuente-1);
