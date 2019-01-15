<?php

  $pdf->SetFont('Arial','B',8);
  $pdf->Cell(0,5,'C: Cumple  NC: No Cumple',0,5,'L',False);
  $pdf->Cell(0,5,'OBSERVACIONES:','B',5,'L',False);
  $pdf->Cell(0,5,'','B',5,'L',False);
  $pdf->Ln(2);

  $current_y = $pdf->GetY();
  $current_x = $pdf->GetX();
  $pdf->Cell(169.157,16,'',1,0,'L',False);
  $pdf->SetXY($current_x, $current_y);
  $pdf->Cell(169.157,4,'NOMBRE TRANSPORTADOR (Operador):',0,4,'L',False);
  $pdf->Cell(169.157,4,'FIRMA:','B',4,'L',False);
  $pdf->Cell(169.157,4,'NOMBRE MANIPULADOR DE ALIMENTOS QUE RECIBE (Operador):',0,4,'L',False);
  $pdf->Cell(169.157,4,'FIRMA:',0,4,'L',False);
  
  $pdf->SetXY($current_x+169.157, $current_y);
  $current_y = $pdf->GetY();
  $current_x = $pdf->GetX();
  $pdf->Cell(0,16,'',1,0,'L',False);
  $pdf->SetXY($current_x, $current_y);
  $pdf->Cell(0,6,utf8_decode('NOMBRE RESPONSABLE INSTITUCIÓN O CENTRO EDUCATIVO:'),0,4,'L',False);
  $pdf->Cell(0,5,'CARGO:','B',4,'L',False);
  $pdf->Cell(0,5,'FIRMA:',0,4,'L',False);