<?php


// Esta caja tiene 264 de ancho y debe quedar centrada
$aux_y = 133;
$aux_x = 1.6;
$y = 133;
$pdf->SetFont('Arial','',$tamannoFuente-1);

$pdf->SetXY($aux_x, $aux_y);
$aux_x = $pdf->GetX();
$aux_y = $pdf->GetY();

$pdf->Cell(31.4);
$pdf->Cell(264,8,utf8_decode(''),0,0,'L',true);
$pdf->SetXY($x, $y);
$pdf->SetFont('Arial','B',$tamannoFuente-1);
$pdf->Cell(31.4);
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
$pdf->MultiCell(100,4,utf8_decode("No. DE TITULARES DE\nDERECHO"),0,'C',false);
$pdf->SetXY($aux_x, $aux_y);
$pdf->SetXY($x, $y);
$pdf->Cell(31.4);
$pdf->Cell(264,8,utf8_decode(''),'B',0,'L',false);
$pdf->SetXY($x, $y);
//$pdf->Cell(0,28,utf8_decode(''),1,0,'L',false);


$aux_y = 141;
$aux_x = 1.6;
$pdf->SetXY($x, $aux_y);













$pdf->Cell(31.4);
$pdf->Cell(60,4,utf8_decode('POBLACIÓN EN CONDICIÓN DE DISCAPACIDAD'),'R',0,'L',false);
$pdf->Cell(26,4,utf8_decode(''),'R',0,'C',false);
$pdf->Cell(26,4,utf8_decode(''),'R',0,'C',false);
$pdf->Cell(26,4,utf8_decode(''),'R',0,'C',false);
$pdf->Cell(26,4,utf8_decode(''),'R',0,'C',false);
$pdf->Cell(100,4,utf8_decode(''),0,0,'C',false);
$pdf->SetXY($aux_x, $aux_y);
$pdf->Cell(31.4);
$pdf->Cell(264,4,utf8_decode(''),'B',0,'L',false);
$pdf->Ln(4);

$aux_x = $pdf->GetX();
$aux_y = $pdf->GetY();
$pdf->SetXY($aux_x, $aux_y);
$pdf->Cell(31.4);
$pdf->Cell(60,4,utf8_decode('POBLACIÓN VICTIMA DEL CONFLICTO ARMADO'),'R',0,'L',false);
$pdf->Cell(26,4,utf8_decode(''),'R',0,'C',false);
$pdf->Cell(26,4,utf8_decode(''),'R',0,'C',false);
$pdf->Cell(26,4,utf8_decode(''),'R',0,'C',false);
$pdf->Cell(26,4,utf8_decode(''),'R',0,'C',false);
$pdf->Cell(100,4,utf8_decode(''),0,0,'C',false);
$pdf->SetXY($aux_x, $aux_y);
$pdf->Cell(31.4);
$pdf->Cell(264,4,utf8_decode(''),'B',0,'L',false);
$pdf->Ln(4);

$aux_x = $pdf->GetX();
$aux_y = $pdf->GetY();
$pdf->SetXY($aux_x, $aux_y);
$pdf->Cell(31.4);
$pdf->Cell(60,4,utf8_decode('COMUNIDADES ÉTNICAS'),'R',0,'L',false);
$pdf->Cell(26,4,utf8_decode(''),'R',0,'C',false);
$pdf->Cell(26,4,utf8_decode(''),'R',0,'C',false);
$pdf->Cell(26,4,utf8_decode(''),'R',0,'C',false);
$pdf->Cell(26,4,utf8_decode(''),'R',0,'C',false);
$pdf->Cell(100,4,utf8_decode(''),0,0,'C',false);
$pdf->SetXY($aux_x, $aux_y);
$pdf->Cell(31.4);
$pdf->Cell(264,4,utf8_decode(''),'B',0,'L',false);
$pdf->Ln(4);

$aux_x = $pdf->GetX();
$aux_y = $pdf->GetY();
$pdf->SetXY($aux_x, $aux_y);
$pdf->Cell(31.4);
$pdf->Cell(60,4,utf8_decode('POBLACIÓN MAYORITARIA'),'R',0,'L',false);
$pdf->Cell(26,4,utf8_decode(''),'R',0,'C',false);
$pdf->Cell(26,4,utf8_decode(''),'R',0,'C',false);
$pdf->Cell(26,4,utf8_decode(''),'R',0,'C',false);
$pdf->Cell(26,4,utf8_decode(''),'R',0,'C',false);
$pdf->Cell(100,4,utf8_decode(''),0,0,'C',false);
$pdf->SetXY($aux_x, $aux_y);
$pdf->Cell(31.4);
$pdf->Cell(264,4,utf8_decode(''),'B',0,'L',false);
$pdf->Ln(4);

$pdf->SetFont('Arial','B',$tamannoFuente-1);
$aux_x = $pdf->GetX();
$aux_y = $pdf->GetY();
$pdf->SetXY($aux_x, $aux_y);
$pdf->Cell(31.4);
$pdf->Cell(60,4,utf8_decode('TOTAL'),'R',0,'L',false);
$pdf->Cell(26,4,utf8_decode(''),'R',0,'C',false);
$pdf->Cell(26,4,utf8_decode(''),'R',0,'C',false);
$pdf->Cell(26,4,utf8_decode(''),'R',0,'C',false);
$pdf->Cell(26,4,utf8_decode(''),'R',0,'C',false);
$pdf->Cell(100,4,utf8_decode(''),0,0,'C',false);


$pdf->SetXY($x, $y);
$pdf->Cell(31.4);
$pdf->Cell(264,28,utf8_decode(''),1,0,'L',false);
$pdf->Ln(20);


$pdf->Ln(12);






$x = $pdf->GetX();
$y = $pdf->GetY();
$pdf->Cell(31.4);
$pdf->Cell(264,4,utf8_decode('OBSERVACIONES'),'B',0,'C',true);
$pdf->SetFont('Arial','',$tamannoFuente-1);
$pdf->SetXY($x, $y+4);
$pdf->Cell(31.4);
$pdf->MultiCell(0,4,"",0,'L',false);
$pdf->SetXY($x, $y);
$pdf->Cell(31.4);
$pdf->Cell(264,12,utf8_decode(''),1,0,'C',false);


$pdf->Ln(16);
$pdf->SetFont('Arial','',$tamannoFuente);
$pdf->Cell(31.4);
$pdf->Cell(264,4,utf8_decode('La presente certificación se expide como soporte de pago y con base en el registro diario de Titulares de Derecho, que se diligencia en cada Institución Educativa atendida.'),0,4,'L',false);
$pdf->Cell(31.4);
$pdf->Cell(264,4,utf8_decode(''),0,4,'L',false);


$pdf->Ln(1);
$pdf->SetFont('Arial','B',$tamannoFuente);
$pdf->Cell(31.4);
$pdf->Cell(50,4,utf8_decode('PARA CONSTANCIA SE FIRMA EN:'),0,0,'L',false);
$pdf->Cell(30,4,utf8_decode(''),'B',0,'L',false);
$pdf->Cell(20,4,utf8_decode(' FECHA: DIA'),0,0,'L',false);
$pdf->Cell(35,4,utf8_decode(''),'B',0,'L',false);
$pdf->Cell(15,4,utf8_decode('DEL AÑO'),0,0,'L',false);
$pdf->Cell(15,4,utf8_decode(''),'B',0,'L',false);


$pdf->Ln(8);
$x = $pdf->GetX();
$y = $pdf->GetY();
$pdf->Cell(31.4);
$pdf->Cell(264,4,utf8_decode('FIRMA DEL RECTOR'),0,4,'L',false);
$pdf->Cell(264,4,utf8_decode(''),'B',4,'L',false);
$pdf->Cell(30,4,utf8_decode('NOMBRES Y APELLIDOS DEL RECTOR:'),0,4,'L',false);

$pdf->SetXY($x, $y);
$pdf->Cell(31.4);
$pdf->Cell(264,12,utf8_decode(''),1,0,'C',false);

$pdf->Ln(14);
$x = $pdf->GetX();
$y = $pdf->GetY();
$pdf->SetFont('Arial','',$tamannoFuente-1);
$pdf->Cell(0,4,utf8_decode('Impreso por Software InfoPae'),0,0,'L',false);
$link = 'http://www.infopae.com.co';
$pdf->SetXY($x+45, $y);
$pdf->Write(4,'www.infopae.com.co',$link);
