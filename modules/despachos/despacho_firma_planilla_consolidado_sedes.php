<?php

  $pdf->SetFont('Arial','B',8);
  $pdf->Cell(0,5,utf8_decode('C: Cumple  NC: No Cumple'),0,5,'L',False);
  $pdf->Cell(0,5,utf8_decode('OBSERVACIONES:'),'B',5,'L',False);
  $pdf->SetFont('Arial','',8);
  $pdf->Cell(0,5,utf8_decode($_SESSION['observacionesDespachos']),'B',5,'L',False);
  $pdf->Ln(2);

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
  $pdf->Cell(88,4,utf8_decode('SUPERVISIÓN'),0,0,'C',False);
  $pdf->Cell(88,4,utf8_decode('TRANSPORTADOR'),0,0,'C',False);
  $pdf->Cell(88,4,utf8_decode('INSTITUCIÓN EDUCATIVA'),0,0,'C',False);
  $pdf->ln();

  $pdf->SetXY($current_x, $current_y-0.2);
  $pdf->Cell(88,12, utf8_decode('NOMBRE RESPONSABLE SUPERVISIÓN:'),0,0,'L',False);
  $pdf->Cell(88,12, utf8_decode('NOMBRE RECIBE (Operador):'),0,0,'L',False);
  $pdf->Cell(88,12, utf8_decode('NOMBRE RESPONSABLE INSTITUCIÓN:'),0,0,'L',False);
  $pdf->ln();

  $pdf->Cell(88,16, utf8_decode('FIRMA:'),0,0,'L',False);
  $pdf->Cell(88,16, utf8_decode('FIRMA:'),0,0,'L',False);
  $cy = $pdf->GetY();
  $cx = $pdf->GetX();
  $pdf->SetXY($cx, $cy+2);
  $pdf->Cell(88,8,utf8_decode('CARGO:'),0,0,'L',False);
  $pdf->SetXY($cx, $cy+10);
  $pdf->Cell(88,8,utf8_decode('FIRMA:'),0,0,'L',False);
  $pdf->ln();
