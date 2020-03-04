<?php
$current_y = $pdf->GetY();
$current_x = $pdf->GetX();
$pdf->Cell(0,15,'','RBL',0,'L',False);
$pdf->SetXY($current_x, $current_y);
$pdf->Cell(0,5,utf8_decode('C: CUMPLE     NC: NO CUMPLE'),0,0,'L',False);
$pdf->Ln(10);
$pdf->Cell(0,5,utf8_decode('OBSERVACIONES'),0,5,'L',False);
$pdf->Cell(0,5,'','RBL',0,'L',False);
$pdf->Ln(5);
$pdf->Cell(0,5,'','RBL',0,'L',False);
$pdf->Ln(5);
$pdf->Cell(0,5,'','RBL',0,'L',False);
$pdf->Ln(5);
$pdf->Cell(0,5,'','RBL',0,'L',False);



$pdf->Ln(5);
$current_y = $pdf->GetY();
$current_x = $pdf->GetX();
$pdf->Cell(0,5,'','RBL',0,'L',False);
$pdf->SetXY($current_x, $current_y);
$pdf->Cell(70,5,'','R',0,'L',False);
$pdf->SetXY($current_x, $current_y);
$pdf->MultiCell(70,2.5,utf8_decode('N° DE RACIONES ATENDIDAS'),0,'L',false);

$pdf->SetXY($current_x+70, $current_y);
$current_y = $pdf->GetY();
$current_x = $pdf->GetX();
$pdf->Cell(56,5,'','R',0,'L',False);
$pdf->SetXY($current_x, $current_y);
$pdf->MultiCell(56,2.5,utf8_decode("NOMBRE MANIPULADOR DE ALIMENTOS QUE RECIBE \n (OPERADOR):"),0,'L',false);

$pdf->SetXY($current_x+56, $current_y);
$current_y = $pdf->GetY();
$current_x = $pdf->GetX();
$pdf->Cell(0,5,'',0,0,'L',False);
$pdf->SetXY($current_x, $current_y);
$pdf->MultiCell(56,2.5,utf8_decode("NOMBRE RESPONSABLE ESTABLECIMIENTO EDUCATIVO:"),0,'L',false);
$pdf->SetXY($current_x, $current_y);



$pdf->Ln(5);
$current_y = $pdf->GetY();
$current_x = $pdf->GetX();
$pdf->Cell(0,5,'','RBL',0,'L',False);
$pdf->SetXY($current_x, $current_y);
$pdf->Cell(70,5,'','R',0,'L',False);
$pdf->SetXY($current_x, $current_y);
$pdf->MultiCell(70,2.5,utf8_decode('CARGO: AUXILIAR DE LOGISTICA'),0,'L',false);

$pdf->SetXY($current_x+70, $current_y);
$current_y = $pdf->GetY();
$current_x = $pdf->GetX();
$pdf->Cell(56,5,'','R',0,'L',False);
$pdf->SetXY($current_x, $current_y);
$pdf->MultiCell(56,2.5,utf8_decode("N° DOCUMENTO DE IDENTIFICACIÓN:"),0,'L',false);

$pdf->SetXY($current_x+56, $current_y);
$current_y = $pdf->GetY();
$current_x = $pdf->GetX();
$pdf->Cell(0,5,'',0,0,'L',False);
$pdf->SetXY($current_x, $current_y);
$pdf->MultiCell(56,2.5,utf8_decode("CARGO:"),0,'L',false);
$pdf->SetXY($current_x, $current_y);



$pdf->Ln(5);
$current_y = $pdf->GetY();
$current_x = $pdf->GetX();
$pdf->Cell(0,10,'','RBL',0,'L',False);

$pdf->SetXY($current_x, $current_y);
$pdf->Cell(70,10,'','R',0,'L',False);
$pdf->SetXY($current_x, $current_y);
$pdf->Cell(70,5,'','B',0,'L',False);
$pdf->SetXY($current_x, $current_y);
$pdf->MultiCell(70,2.5,utf8_decode('N° DOCUMENTO DE IDENTIFICACIÓN:'),0,'L',false);
$pdf->SetXY($current_x, $current_y+5);
$pdf->MultiCell(70,2.5,utf8_decode('FIRMA:'),0,'L',false);

$pdf->SetXY($current_x+70, $current_y);
$current_y = $pdf->GetY();
$current_x = $pdf->GetX();
$pdf->Cell(56,10,'','R',0,'L',False);
$pdf->SetXY($current_x, $current_y);
$pdf->MultiCell(56,2.5,utf8_decode("FIRMA:"),0,'L',false);

$pdf->SetXY($current_x+56, $current_y);
$current_y = $pdf->GetY();
$current_x = $pdf->GetX();
$pdf->Cell(0,5,'','B',0,'L',False);
$pdf->SetXY($current_x, $current_y);
$pdf->MultiCell(56,2.5,utf8_decode("N° DOCUMENTO DE IDENTIFICACIÓN:"),0,'L',false);
$pdf->SetXY($current_x, $current_y+5);
$pdf->MultiCell(70,2.5,utf8_decode('FIRMA:'),0,'L',false);
$pdf->SetXY($current_x, $current_y);
$pdf->Ln(50);