<?php
//Footer
// $pdf->SetXY(0, 210);
$pdf->SetFont('Arial','',5);
$pdf->SetTextColor(0,0,0);
// $pdf->Cell(0,5,'Calle 43 N° 57-14 Centro Administrativo Nacional, CAN, Bogotá, D.C.  PBX: +57 (1) 222 2800 - Fax 222 495   www.mineducacion.gov.co - atencionalciudadano@mineducacion.gov.co  Impreso por: InfoPAE - www.infopae.com.co',0,0,'C',False);
if ($tamano_carta === FALSE)
{
	$pdf->Rotate_text(326, 209, utf8_decode('Calle 43 N° 57-14 Centro Administrativo Nacional, CAN, Bogotá, D.C.  PBX: +57 (1) 222 2800 - Fax 222 495   www.mineducacion.gov.co - atencionalciudadano@mineducacion.gov.co  Impreso por: InfoPAE - www.infopae.com.co'), 90);
}
else
{
	$pdf->Rotate_text(275, 209, utf8_decode('Calle 43 N° 57-14 Centro Administrativo Nacional, CAN, Bogotá, D.C.  PBX: +57 (1) 222 2800 - Fax 222 495   www.mineducacion.gov.co - atencionalciudadano@mineducacion.gov.co  Impreso por: InfoPAE - www.infopae.com.co'), 90);
}
// $pdf->SetXY(8, 6.31);
//Termina el footer
