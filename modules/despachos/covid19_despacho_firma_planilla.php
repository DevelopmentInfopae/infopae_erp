<?php
$pdf->Cell(0,2,utf8_decode(""),'B',0,'C',False);
$pdf->Ln(2);
$pdf->SetFont('Arial','B',$tamannoFuente);
$pdf->Cell(38,4,utf8_decode("Observaciones:"),'BL',0,'L',False);
$pdf->Cell(0,4,utf8_decode(""),'BLR',0,'C',False);

$pdf->Ln(7);
$pdf->Cell(33,4,utf8_decode("Firma de quien entrega la RPC:"),0,0,'L',False);
$pdf->Cell(67,4,utf8_decode(""),'B',0,'C',False);
$pdf->Cell(43,4,utf8_decode(""),0,0,'C',False);
$pdf->Cell(64,4,utf8_decode("Firma del responsable de la ETC (supervisión / interventoria):"),0,0,'L',False);
$pdf->Cell(67,4,utf8_decode(""),'B',0,'C',False);

$pdf->Ln(7);
$pdf->Cell(35,4,utf8_decode("Nombre legible de quien entrega:"),0,0,'L',False);
$pdf->Cell(65,4,utf8_decode(""),'B',0,'C',False);
$pdf->Cell(43,4,utf8_decode(""),0,0,'C',False);
$pdf->Cell(35,4,utf8_decode("Nombre legible de quien entrega:"),0,0,'L',False);
$pdf->Cell(96,4,utf8_decode(""),'B',0,'C',False);

$pdf->Ln(7);
$pdf->Cell(18,4,utf8_decode("Cargo / función:"),0,0,'L',False);
$pdf->Cell(82,4,utf8_decode(""),'B',0,'C',False);
$pdf->Cell(43,4,utf8_decode(""),0,0,'C',False);
$pdf->Cell(45,4,utf8_decode("Cargo / función del responsable de la ETC:"),0,0,'L',False);
$pdf->Cell(86,4,utf8_decode(""),'B',0,'C',False);

$pdf->Ln(7);
$pdf->Cell(21,4,utf8_decode("Número telefónico:"),0,0,'L',False);
$pdf->Cell(79,4,utf8_decode(""),'B',0,'C',False);
$pdf->Cell(43,4,utf8_decode(""),0,0,'C',False);
$pdf->Cell(47,4,utf8_decode("Número telefónico del responsable de la ETC"),0,0,'L',False);
$pdf->Cell(84,4,utf8_decode(""),'B',0,'C',False);

$pdf->Ln(7);
$pdf->Cell(0,4,utf8_decode("Impreso por: InfoPAE - www.infopae.com.co"),0,0,'L',False);