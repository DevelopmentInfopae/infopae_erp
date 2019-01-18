<?php

  $pdf->SetFont('Arial','B',8);
  $pdf->Cell(0,5,'C: Cumple  NC: No Cumple',0,5,'L',False);
  $pdf->Cell(0,5,'OBSERVACIONES:','B',5,'L',False);
  $pdf->Cell(0,5,'','B',5,'L',False);
  $pdf->Ln(2);

  // $current_y = $pdf->GetY();
  // $current_x = $pdf->GetX();

  // $pdf->Cell(169.157,24,'',1,0,'L',False);
  // $pdf->SetXY($current_x, $current_y);
  // $pdf->Cell(169.157,4,'NOMBRE TRANSPORTADOR (Operador):',0,4,'L',False);
  // $pdf->Cell(169.157,8,'FIRMA:','B',4,'L',False);
  // $pdf->Cell(169.157,4,'NOMBRE MANIPULADOR DE ALIMENTOS QUE RECIBE (Operador):',0,4,'L',False);
  // $pdf->Cell(169.157,8,'FIRMA:',0,4,'L',False);
  
  // $pdf->SetXY($current_x+169.157, $current_y);
  // $current_y = $pdf->GetY();
  // $current_x = $pdf->GetX();
  // $pdf->Cell(0,24,'',1,0,'L',False);
  // $pdf->SetXY($current_x, $current_y);
  // $pdf->Cell(0,4,utf8_decode('NOMBRE RESPONSABLE INSTITUCIÓN O CENTRO EDUCATIVO:'),0,4,'L',False);
  // $pdf->Cell(0,8,'CARGO:','B',4,'L',False);
  // $pdf->Cell(0,12,'FIRMA:',0,4,'L',False);

  $current_y = $pdf->GetY();
  $current_x = $pdf->GetX();

  $pdf->SetXY($current_x, $current_y);


  $pdf->Cell(88,4,'',1,0,'L',False);
  $pdf->Cell(88,4,'',1,0,'L',False);
  $pdf->Cell(88,4,'',1,0,'L',False);
  $pdf->ln();

  $pdf->Cell(88,8,'',1,0,'L',False);
  $pdf->Cell(88,8,'',1,0,'L',False);
  $pdf->Cell(88,8,'',1,0,'L',False);
  $pdf->ln();

  $pdf->Cell(88,12,'',1,0,'L',False);
  $pdf->Cell(88,12,'',1,0,'L',False);
  $cy = $pdf->GetY();
  $cx = $pdf->GetX();
  $pdf->Cell(88,6,'',1,0,'L',False);
  $pdf->SetXY($cx, $cy+6);
  $pdf->Cell(88,6,'',1,0,'L',False);
  $pdf->ln();

  $pdf->SetXY($current_x, $current_y);

  $pdf->Cell(88,4,'TRANSPORTADOR',1,0,'C',False);
  $pdf->Cell(88,4,'MANIPULADOR',1,0,'C',False);
  $pdf->Cell(88,4,utf8_decode('INSTITUCIÓN EDUCATIVA'),1,0,'C',False);
  $pdf->ln();

  $pdf->Cell(88,8,'NOMBRE (Operador):',1,0,'L',False);
  $pdf->Cell(88,8,'NOMBRE RECIBE (Operador):',1,0,'L',False);
  $pdf->Cell(88,8,'NOMBRE RESPONSABLE:',1,0,'L',False);
  $pdf->ln();

  $pdf->Cell(88,12,'FIRMA:',1,0,'L',False);
  $pdf->Cell(88,12,'FIRMA:',1,0,'L',False);
  $cy = $pdf->GetY();
  $cx = $pdf->GetX();
  $pdf->Cell(88,6,'CARGO:',1,0,'L',False);
  $pdf->SetXY($cx, $cy+6);
  $pdf->Cell(88,6,'FIRMA:',1,0,'L',False);

  $pdf->ln();
