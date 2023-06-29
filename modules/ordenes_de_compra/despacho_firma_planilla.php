<?php
$pdf->SetFont('Arial','B',$tamannoFuente);
$pdf->Cell(0,5,'D: Disponible  ND: No Disponible','TLR',5,'L',False);
$pdf->Cell(0,5,'OBSERVACIONES:','BLR',5,'L',False);
$auxObservaciones = isset($auxObservaciones) ? $auxObservaciones : '';
$pdf->Cell(0,5,$auxObservaciones,'BLR',5,'L',False);
$pdf->Cell(0,5,'','BLR',5,'L',False);
$pdf->Cell(0,5,'','BLR',5,'L',False);
// $pdf->Cell(0,5,'','LR',5,'L',False);
$current_y = $pdf->GetY();
$current_x = $pdf->GetX();
//$pdf->Ln(2);

// $pdf->Cell(0,12,'FIRMA:',0,4,'L',False);


$pdf->SetXY($current_x, $current_y);


$pdf->Cell(64,5,'ELABORADA POR (Operador):','TBL',0,'C',True);
$pdf->Cell(64,5,'AUTORIZADA POR (Operador):','TBL',0,'C',True);
$pdf->Cell(0,5,'ACEPTADA POR (Proveedor):','TBLR',0,'C',True);
$pdf->ln();

$current_y = $pdf->GetY();
$current_x = $pdf->GetX();
$pdf->Cell(64,5,'','BL',0,'C',False);
$pdf->Cell(64,5,'','BL',0,'C',False);
$pdf->Cell(0,5,'','BLR',0,'C',False);
$pdf->SetXY($current_x, $current_y-1);
$pdf->Cell(64,5,'NOMBRE:',0,0,'L',False);
$pdf->Cell(64,5,'NOMBRE:',0,0,'L',False);
$pdf->Cell(0,5,'NOMBRE:',0,0,'L',False);
$pdf->SetXY($current_x, $current_y);
$pdf->ln();


$current_y = $pdf->GetY();
$current_x = $pdf->GetX();
$pdf->Cell(64,20,'','BL',0,'C',False);
$pdf->Cell(64,20,'','BL',0,'C',False);
$pdf->Cell(0,20,'','BLR',0,'C',False);
$pdf->SetXY($current_x, $current_y-1);
$pdf->Cell(64,5,'FIRMA:',0,0,'L',False);
$pdf->Cell(64,5,'FIRMA:',0,0,'L',False);
$pdf->Cell(0,5,'FIRMA:',0,0,'L',False);
$pdf->SetXY($current_x, $current_y);

$pdf->ln(22);
$pdf->Cell(0,5,utf8_decode('Impreso por Sistema de Información Tecnológico Infopae Versión 2023'),0,0,'C',False);
$pdf->ln();
$pdf->Cell(0,5,utf8_decode('www.infopae.com.co - soporte@infopae.com.co - contacto@infopae.com.co'),0,0,'C',False);

















// $pdf->Cell(86,12,'','B',0,'L',False);
// $pdf->Cell(86,12,'','B',0,'L',False);
// $pdf->Cell(86,12,'','B',0,'L',False);
// $pdf->ln();

// $pdf->Cell(0,16,'','B',0,'L',False);
// $pdf->Cell(0,16,'','B',0,'L',False);
// $cy = $pdf->GetY();
// $cx = $pdf->GetX();
// $pdf->Cell(0,8,'','B',0,'L',False);
// $pdf->SetXY($cx, $cy+8);
// $pdf->Cell(0,8,'','B',0,'L',False);
// $pdf->ln();












// $pdf->SetXY($current_x, $current_y);

// $pdf->Cell(88,4,'MANIPULADOR',0,0,'C',False);
// $pdf->Cell(88,4,'TRANSPORTADOR',0,0,'C',False);
// $pdf->Cell(88,4,utf8_decode('INSTITUCIÓN EDUCATIVA'),0,0,'C',False);
// $pdf->ln();

// $pdf->SetXY($current_x, $current_y-0.2);

// $pdf->Cell(88,12,'NOMBRE MANIPULADOR (Operador):',0,0,'L',False);
// $pdf->Cell(88,12,'NOMBRE RECIBE (Operador):',0,0,'L',False);
// $pdf->Cell(88,12,'NOMBRE RESPONSABLE INSTITUCION O CENTRO EDUCATIVO:',0,0,'L',False);
// $pdf->ln();

// $pdf->Cell(88,16,'FIRMA:',0,0,'L',False);
// $pdf->Cell(88,16,'FIRMA:',0,0,'L',False);
// $cy = $pdf->GetY();
// $cx = $pdf->GetX();

// $pdf->SetXY($cx, $cy+2);
// $pdf->Cell(88,8,'CARGO:',0,0,'L',False);
// $pdf->SetXY($cx, $cy+10);
// $pdf->Cell(88,8,'FIRMA:',0,0,'L',False);

// $pdf->ln();
