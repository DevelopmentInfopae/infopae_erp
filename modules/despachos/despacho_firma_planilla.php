<?php

  $pdf->SetFont('Arial','B',8);
  $pdf->Cell(0,5,'C: Cumple  NC: No Cumple',0,5,'L',False);
  $pdf->Cell(0,5,'OBSERVACIONES:','B',5,'L',False);
  $pdf->SetFont('Arial','',8);
  $pdf->Cell(0,5,$_SESSION['observacionesDespachos'],'B',5,'L',False);
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
  $pdf->SetFont('Arial','B',8);
  $current_y = $pdf->GetY();
  $current_x = $pdf->GetX();

  $pdf->SetXY($current_x, $current_y);


  $pdf->Cell(88,4,'',1,0,'L',False);
  $pdf->Cell(88,4,'',1,0,'L',False);
  $pdf->Cell(88,4,'',1,0,'L',False);
  $pdf->ln();

  $pdf->Cell(88,12,'',1,0,'L',False);
  $pdf->Cell(88,12,'',1,0,'L',False);
  $pdf->Cell(88,12,'',1,0,'L',False);
  $pdf->ln();

  $pdf->Cell(88,16,'',1,0,'L',False);
  $pdf->Cell(88,16,'',1,0,'L',False);
  $cy = $pdf->GetY();
  $cx = $pdf->GetX();
  $pdf->Cell(88,8,'',1,0,'L',False);
  $pdf->SetXY($cx, $cy+8);
  $pdf->Cell(88,8,'',1,0,'L',False);
  $pdf->ln();

  $pdf->SetXY($current_x, $current_y);

  $pdf->Cell(88,4,'MANIPULADOR',0,0,'C',False);
  $pdf->Cell(88,4,'TRANSPORTADOR',0,0,'C',False);
  $pdf->Cell(88,4,utf8_decode('INSTITUCIÓN EDUCATIVA'),0,0,'C',False);
  $pdf->ln();

  $pdf->SetXY($current_x, $current_y-0.2);

  $pdf->Cell(88,12,'NOMBRE MANIPULADOR (Operador):',0,0,'L',False);
  $pdf->Cell(88,12,'NOMBRE RECIBE (Operador):',0,0,'L',False);
  $pdf->Cell(88,12,'NOMBRE RESPONSABLE INSTITUCION O CENTRO EDUCATIVO:',0,0,'L',False);
  $pdf->ln();

  $pdf->Cell(88,16,'FIRMA:',0,0,'L',False);
  $pdf->Cell(88,16,'FIRMA:',0,0,'L',False);
  $cy = $pdf->GetY();
  $cx = $pdf->GetX();

  $pdf->SetXY($cx, $cy+2);
  $pdf->Cell(88,8,'CARGO:',0,0,'L',False);
  $pdf->SetXY($cx, $cy+10);
  $pdf->Cell(88,8,'FIRMA:',0,0,'L',False);

  $pdf->ln();
