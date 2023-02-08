<?php



  $current_y = $pdf->GetY();
  $current_x = $pdf->GetX();

  $pdf->SetXY($current_x, $current_y);

  $pdf->SetFont('Arial','B',8);

  //$pdf->Cell(0,5,'C: Cumple  NC: No Cumple',0,5,'L',False);

  $pdf->Cell(0,5,'OBSERVACIONES:','B',5,'L',False);

  $pdf->Cell(0,5,'','B',5,'L',False);

  $pdf->Ln(2);




  $pdf->SetFont('Arial','',7);
  $current_y = $pdf->GetY();
  $current_x = $pdf->GetX();
  $pdf->Cell(0,30,utf8_decode(''),1,0,'C',False);
  $pdf->SetXY($current_x, $current_y);
  $pdf->Cell(0,5,utf8_decode(''),1,0,'C',False);
  $pdf->SetXY($current_x, $current_y);
  $pdf->Cell(0,5,utf8_decode('MANIPULADOR'),'R',0,'C',False);







  $pdf->Ln(5);
  $current_y = $pdf->GetY();
  $current_x = $pdf->GetX();
  $pdf->Cell(0,10,utf8_decode(''),1,0,'C',False);
  $pdf->SetXY($current_x, $current_y);
  $pdf->Cell(0,10,utf8_decode(''),'R',0,'C',False);
  $pdf->SetXY($current_x, $current_y);
  $pdf->Cell(0,5,utf8_decode('NOMBRE DE MANIPULADOR DE ALIMENTOS RESPONSABLE (operador):'),0,0,'L',False);
  $pdf->Ln(10);
  $current_y = $pdf->GetY();
  $current_x = $pdf->GetX();
  $pdf->Cell(0,15,utf8_decode(''),'R',0,'C',False);
  $pdf->SetXY($current_x, $current_y);
  $pdf->Cell(90,5,utf8_decode('FIRMA:'),0,0,'L',False);