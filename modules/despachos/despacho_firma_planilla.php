<?php

$pdf->SetFont('Arial','B',8);
$pdf->Cell(0,5,'C: Cumple  NC: No Cumple',0,5,'L',False);
$pdf->Cell(0,5,'OBSERVACIONES:','B',5,'L',False);
$pdf->SetFont('Arial','',8);
$pdf->Cell(0,5, isset($_SESSION['observacionesDespachos']) == true ? $_SESSION['observacionesDespachos'] : '', 'B', 5, 'L', False);
$pdf->Ln(2);

$pdf->SetFont('Arial','B',8);
$current_y = $pdf->GetY();
$current_x = $pdf->GetX();
$pdf->SetXY($current_x, $current_y);

$pdf->Cell(132,4,'',1,0,'L',False);
$pdf->Cell(132,4,'',1,0,'L',False);
$pdf->ln();

$pdf->Cell(132,12,'',1,0,'L',False);
$pdf->Cell(132,12,'',1,0,'L',False);
$pdf->ln();

$pdf->Cell(132,16,'',1,0,'L',False);
$pdf->Cell(132,16,'',1,0,'L',False);
$cy = $pdf->GetY();
$cx = $pdf->GetX();

$pdf->ln();
$pdf->SetXY($current_x, $current_y);

$pdf->Cell(132,4,'MANIPULADOR',0,0,'C',False);
$pdf->Cell(132,4,'TRANSPORTADOR',0,0,'C',False);
$pdf->ln();
$pdf->SetXY($current_x, $current_y-0.2);

$pdf->Cell(132,12,'NOMBRE MANIPULADOR (Operador):',0,0,'L',False);
$pdf->Cell(132,12,'NOMBRE RECIBE (Operador):',0,0,'L',False);
$pdf->ln();

$pdf->Cell(132,16,'FIRMA:',0,0,'L',False);
$pdf->Cell(132,16,'FIRMA:',0,0,'L',False);
$cy = $pdf->GetY();
$cx = $pdf->GetX();

$pdf->ln();
