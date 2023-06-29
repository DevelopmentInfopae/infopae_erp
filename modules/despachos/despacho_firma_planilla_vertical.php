<?php
$pdf->SetFont('Arial','B',$tamannoFuente);
$current_y = $pdf->GetY();
$current_x = $pdf->GetX();
$pdf->Cell(0,8,'',0,0,'L',False);



$pdf->SetXY($current_x, $current_y+1.5);
$pdf->Cell(0,5,utf8_decode('C: CUMPLE     NC: NO CUMPLE'),0,0,'L',False);
$pdf->Ln(3);
$pdf->Cell(0,4,utf8_decode('OBSERVACIONES: '),'B',5,'L',False);


$pdf->SetXY($current_x, $current_y+10);
$pdf->Cell(0,4,'','B',0,'L',False);
$pdf->Ln(6);
// $pdf->Ln(5);



$current_y = $pdf->GetY();
$current_x = $pdf->GetX();


$pdf->SetXY($current_x, $current_y);

// $pdf->Ln(5);
$pdf->Cell(65,5,utf8_decode('MANIPULADOR'),'TLB',0,'C',False);
$pdf->Cell(61,5,utf8_decode('TRANSPORTADOR'),'TLB',0,'C',False);
// $pdf->Cell(65,5,'',0,0,'C',False);
$pdf->Cell(0,5,utf8_decode('INSTITUCIÓN EDUCATIVA'),'TLBR',0,'C',False);


$pdf->SetXY($current_x, $current_y+5);
$current_y = $pdf->GetY();
$pdf->Cell(65,25,utf8_decode(''),'LBR',0,'C',False);
$pdf->Cell(61,25,utf8_decode(''),'BR',0,'C',False);
$pdf->Cell(0,25,utf8_decode(''),'BR',0,'C',False);


// $current_y = $pdf->GetY();
$pdf->SetXY($current_x, $current_y);
$pdf->Cell(65,4,utf8_decode('NOMBRE MANIPULADOR (Operador):'),0,5,'L',False);
$pdf->Cell(65,4,utf8_decode(''),'B',5,'L',False);
$pdf->Cell(65,4,utf8_decode('FIRMA:'),0,0,'L',False);

$pdf->SetXY($current_x+65, $current_y);
$pdf->Cell(61,4,utf8_decode('NOMBRE QUE RECIBE (OPERADOR):'),0,5,'L',False);
$pdf->Cell(61,4,utf8_decode(''),'B',5,'L',False);
$pdf->Cell(61,4,utf8_decode('FIRMA:'),0,0,'L',False);

$pdf->SetXY($current_x+65+61, $current_y);
$pdf->Cell(0,4,utf8_decode('NOMBRE RESPONSABLE ESTABLECIMIENTO EDUCATIVO:'),0,5,'L',False);
$pdf->Cell(0,4,utf8_decode(''),'B',5,'L',False);
$pdf->Cell(0,4,utf8_decode('CARGO:'),0,5,'L',False);
$pdf->Cell(0,4,utf8_decode(''),'B',5,'L',False);
$pdf->Cell(0,4,utf8_decode('FIRMA:'),0,5,'L',False);


//$pdf->MultiCell(70,2.5,utf8_decode('N° DE RACIONES ATENDIDAS'),0,'L',false);







// $pdf->SetXY($current_x+70, $current_y);
// $current_y = $pdf->GetY();
// $current_x = $pdf->GetX();
// $pdf->Cell(56,5,'','R',0,'L',False);
// $pdf->SetXY($current_x, $current_y);
// $pdf->MultiCell(56,2.5,utf8_decode("NOMBRE MANIPULADOR DE ALIMENTOS QUE RECIBE \n (OPERADOR):"),0,'L',false);

// $pdf->SetXY($current_x+56, $current_y);
// $current_y = $pdf->GetY();
// $current_x = $pdf->GetX();
// $pdf->Cell(0,5,'',0,0,'L',False);
// $pdf->SetXY($current_x, $current_y);
// $pdf->MultiCell(56,2.5,utf8_decode("NOMBRE RESPONSABLE ESTABLECIMIENTO EDUCATIVO:"),0,'L',false);
// $pdf->SetXY($current_x, $current_y);



// $pdf->Ln(5);
// $current_y = $pdf->GetY();
// $current_x = $pdf->GetX();
// $pdf->Cell(0,5,'','RBL',0,'L',False);
// $pdf->SetXY($current_x, $current_y);
// $pdf->Cell(70,5,'','R',0,'L',False);
// $pdf->SetXY($current_x, $current_y);
// $pdf->MultiCell(70,2.5,utf8_decode('CARGO: AUXILIAR DE LOGISTICA'),0,'L',false);

// $pdf->SetXY($current_x+70, $current_y);
// $current_y = $pdf->GetY();
// $current_x = $pdf->GetX();
// $pdf->Cell(56,5,'','R',0,'L',False);
// $pdf->SetXY($current_x, $current_y);
// $pdf->MultiCell(56,2.5,utf8_decode("N° DOCUMENTO DE IDENTIFICACIÓN:"),0,'L',false);

// $pdf->SetXY($current_x+56, $current_y);
// $current_y = $pdf->GetY();
// $current_x = $pdf->GetX();
// $pdf->Cell(0,5,'',0,0,'L',False);
// $pdf->SetXY($current_x, $current_y);
// $pdf->MultiCell(56,2.5,utf8_decode("CARGO:"),0,'L',false);
// $pdf->SetXY($current_x, $current_y);



// $pdf->Ln(5);
// $current_y = $pdf->GetY();
// $current_x = $pdf->GetX();
// $pdf->Cell(0,10,'','RBL',0,'L',False);

// $pdf->SetXY($current_x, $current_y);
// $pdf->Cell(70,10,'','R',0,'L',False);
// $pdf->SetXY($current_x, $current_y);
// $pdf->Cell(70,5,'','B',0,'L',False);
// $pdf->SetXY($current_x, $current_y);
// $pdf->MultiCell(70,2.5,utf8_decode('N° DOCUMENTO DE IDENTIFICACIÓN:'),0,'L',false);
// $pdf->SetXY($current_x, $current_y+5);
// $pdf->MultiCell(70,2.5,utf8_decode('FIRMA:'),0,'L',false);

// $pdf->SetXY($current_x+70, $current_y);
// $current_y = $pdf->GetY();
// $current_x = $pdf->GetX();
// $pdf->Cell(56,10,'','R',0,'L',False);
// $pdf->SetXY($current_x, $current_y);
// $pdf->MultiCell(56,2.5,utf8_decode("FIRMA:"),0,'L',false);

// $pdf->SetXY($current_x+56, $current_y);
// $current_y = $pdf->GetY();
// $current_x = $pdf->GetX();
// $pdf->Cell(0,5,'','B',0,'L',False);
// $pdf->SetXY($current_x, $current_y);
// $pdf->MultiCell(56,2.5,utf8_decode("N° DOCUMENTO DE IDENTIFICACIÓN:"),0,'L',false);
// $pdf->SetXY($current_x, $current_y+5);
// $pdf->MultiCell(70,2.5,utf8_decode('FIRMA:'),0,'L',false);
// $pdf->SetXY($current_x, $current_y);
$pdf->Ln(50);