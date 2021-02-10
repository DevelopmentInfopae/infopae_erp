<?php


$tamannoFuente = 8;

$pdf->SetFont('Arial','B',$tamannoFuente);

$pdf->Cell(0,6,'DATOS GENERALES ',0,0,'C',true);
$pdf->Ln(10);

$x = $pdf->GetX();
$y = $pdf->GetY();

$pdf->SetFont('Arial','B',$tamannoFuente);
$pdf->Cell(32,5,utf8_decode('OPERADOR:'),'R',0,'L',false);
$pdf->SetFont('Arial','',$tamannoFuente);
$pdf->Cell(102,5,utf8_decode($_SESSION['p_Operador']),'R',0,'L',false);
$pdf->SetFont('Arial','B',$tamannoFuente);
$pdf->Cell(25,5,utf8_decode('CONTRATO N°:'),'R',0,'L',false);
$pdf->SetFont('Arial','',$tamannoFuente);
$pdf->Cell(38,5,$_SESSION['p_Contrato'],0,0,'L',false);
$pdf->SetX($x);
$pdf->Cell(0,5,'','B',5,'C',false);





$pdf->SetFont('Arial','B',$tamannoFuente);
$pdf->Cell(32,5,utf8_decode('INSTITUCIÓN:'),'R',0,'L',false);
$pdf->SetFont('Arial','',$tamannoFuente);
$pdf->Cell(102,5,utf8_decode($institucion['nom_inst']),'R',0,'L',false);
$pdf->SetFont('Arial','B',$tamannoFuente);
$pdf->Cell(25,5,utf8_decode('CÓDIGO DANE:'),'R',0,'L',false);
$pdf->SetFont('Arial','',$tamannoFuente);
$pdf->Cell(38,5,$institucion['cod_inst'],0,0,'L',false);
$pdf->SetX($x);
$pdf->Cell(0,5,'','B',5,'C',false);

$pdf->SetFont('Arial','B',$tamannoFuente);
$pdf->Cell(32,5,utf8_decode('DEPARTAMENTO:'),'R',0,'L',false);
$pdf->SetFont('Arial','',$tamannoFuente);
$pdf->Cell(102,5,utf8_decode(strtoupper($institucion['Departamento'])),'R',0,'L',false);
$pdf->SetFont('Arial','B',$tamannoFuente);
$pdf->Cell(25,5,utf8_decode('CÓDIGO DANE:'),'R',0,'L',false);
$pdf->SetFont('Arial','',$tamannoFuente);
$pdf->Cell(38,5,$_SESSION['p_CodDepartamento'],0,0,'L',false);
$pdf->SetX($x);
$pdf->Cell(0,5,'','B',5,'C',false);

$pdf->SetFont('Arial','B',$tamannoFuente);
$pdf->Cell(32,5,utf8_decode('MUNICIPIO:'),'R',0,'L',false);
$pdf->SetFont('Arial','',$tamannoFuente);
$pdf->Cell(102,5,utf8_decode($institucion['ciudad']),'R',0,'L',false);
$pdf->SetFont('Arial','B',$tamannoFuente);
$pdf->Cell(25,5,utf8_decode('CÓDIGO DANE:'),'R',0,'L',false);
$pdf->SetFont('Arial','',$tamannoFuente);
$pdf->Cell(38,5,$institucion['cod_mun_sede'],0,0,'L',false);
$pdf->SetX($x);
$pdf->Cell(0,5,'','B',5,'C',false);

$pdf->SetFont('Arial','B',$tamannoFuente);
$pdf->Cell(32,5,utf8_decode('FECHA:'),'R',0,'L',false);
$pdf->SetFont('Arial','',$tamannoFuente);
$pdf->Cell(15,5,'Desde:','R',0,'L',false);





$aux = $fechas[0]["DIA"]." de ".mesNombre($fechas[0]["MES"])." ". $fechas[0]["ANO"];
$aux = $fecha_desde;

$pdf->Cell(40,5,utf8_decode($aux),'R',0,'L',false);
$pdf->Cell(15,5,'Hasta:','R',0,'L',false);

$aux = $fechas[1]["DIA"]." de ".mesNombre($fechas[1]["MES"])." ". $fechas[1]["ANO"];
$aux = $fecha_hasta;

$pdf->Cell(32,5,utf8_decode($aux),'R',0,'L',false);


$pdf->SetFont('Arial','B',$tamannoFuente);
$pdf->Cell(25,5,utf8_decode('MES:'),'R',0,'L',false);
$pdf->SetFont('Arial','',$tamannoFuente);
$aux = mb_strtoupper($mesLetras);
$pdf->Cell(38,5,$aux,0,0,'L',false);



$pdf->SetX($x);
$pdf->Cell(0,5,'','B',5,'C',false);









$pdf->SetFont('Arial','B',$tamannoFuente);
$pdf->Cell(32,5,utf8_decode('NOMBRE RECTOR:'),'R',0,'L',false);
$pdf->SetFont('Arial','',$tamannoFuente);
$pdf->Cell(102,5,utf8_decode($institucion["nombre_rector"]),'R',0,'L',false);
$pdf->SetFont('Arial','B',$tamannoFuente);
$pdf->Cell(25,5,utf8_decode('DOC. RECTOR:'),'R',0,'L',false);
$pdf->SetFont('Arial','',$tamannoFuente);
$pdf->Cell(0,5,$institucion["documento_rector"],0,5,'L',false);
$pdf->SetX($x);

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
// $pdf->Cell(0,11,'',0,0,'C',true);

// $pdf->SetXY($x, $y+2);
$pdf->SetFont('Arial','B',$tamannoFuente-1);
$pdf->Cell(130,10,utf8_decode("NOMBRE DEL ESTABLECIMIENTO U CENTRO EDUCATIVO"),'LTB',0,'C',true);
$pdf->Cell(30,10,utf8_decode("TIPO RACIÓN"),'LTB',0,'C',true);
$pdf->Cell(0,10,utf8_decode("TOTAL, RACIONES"),'LTBR',0,'C',true);
$pdf->Ln(10);