<?php
$tamannoFuente = 5;
$pdf->SetFont('Arial','',$tamannoFuente);
//Footer
$current_y = $pdf->GetY();
$current_x = $pdf->GetX();
$pdf->SetXY(0, 264);
$pdf->SetFont('Arial','',5);
$pdf->SetTextColor(0,0,0);
$pdf->MultiCell(0,2.5,utf8_decode("Calle 43 No. 57 - 14 Centro Administrativo Nacional - CAN, Bogotá D.C. \n PBX: +57 (1) 222 2800 -  Fax 222 4953 \n www.mineducacion.gov.co - atencionalciudadano@mineducacion.gov.co \n Impreso por: InfoPAE - www.infopae.com.co"),0,'C',false);
$pdf->SetXY($current_x, $current_y);