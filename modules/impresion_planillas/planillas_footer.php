<?php
//Footer
$x = 3;
$y = 177;
$pdf->SetXY($x, $y);
$pdf->SetTextColor(0,0,0);
$pdf->Cell(0,36,'',1,36,'C',False);

$pdf->SetXY($x, $y+5.4);
$pdf->SetFont('Arial','B',7);
$pdf->Cell(81,5,'FIRMA, NOMBRE Y CEDULA RESPONSABLE DEL OPERADOR',0,0,'L',False);
$pdf->Cell(70,5,'','B',0,'L',False);
$pdf->Cell(5,5,'',0,0,'L',False);
$pdf->Cell(90,5,'FIRMA, NOMBRE Y CEDULA RECTOR ESTABLECIMIENTO EDUCATIVO',0,0,'L',False);
$pdf->Cell(70,5,'','B',0,'L',False);
$pdf->SetXY($x, $y);
$pdf->Cell(0,12,'','B',12,'C',False);

$pdf->Cell(0,5,'OBSERVACIONES:','B',5,'L',False);

$pdf->SetFont('Arial','',6);
$x = $pdf->GetX();
$y = $pdf->GetY();
$pdf->Cell(30,12,'','R',0,'C',False);
$pdf->SetXY($x, $y);
$pdf->MultiCell(30,2.9,utf8_decode('1. Sexo:  Marque F si es Femenino, y M si es Masculino.'));
$pdf->SetXY($x+30, $y);
$pdf->MultiCell(30,2.9,utf8_decode('2. Grado Educativo: Marque el grado del Titular de Derecho, P si es Preescolar y de 1 a 11 Grado.'));
$pdf->SetXY($x+30, $y);
$pdf->Cell(30,12,'','R',0,'C',False);
$pdf->MultiCell(0,2.9,utf8_decode('3. Tipo de complemento: Indique el tipo de complemento y modalidad que recibe el Titular de Derecho así: CAJMPS (Complemento Alimentario Jornada Mañana Preparado en Sitio), CAJMRI (Complemento Alimentario Jornada Mañana Ración Industrializada), CAJTPS (Complemento Alimentario Jornada Tarde Preparado en Sitio), CAJTRI (Complemento Alimentario Jornada Tarde Ración Industrializada), APS (Almuerzo Preparado en Sitio población vulnerable), RRI (Refrigerio Reforzado Industrializado), CAIE (Complemento Alimentario Industrializado para Emergencias), APSD (Almuerzo Preparado en Sitio Desplazados), CAJMPSD (Complemento Alimentario Jornada Mañana Preparado en Sitio Desplazados), CAJTPSD (Complemento Alimentario Jornada Tarde Preparado en Sitio Desplazados), CAJMRID (Complemento Alimentario Jornada Mañana Ración Industrializada Desplazados), CAJTRID (Complemento Alimentario Jornada Tarde Ración Industrializada Desplazados)'));
$pdf->SetXY($x, $y);
$pdf->Cell(0,12,'','B',12,'C',False);
$pdf->SetXY($x, $y+12.5);
$pdf->MultiCell(0,2.9,utf8_decode('NOTA: El operador/responsable de prestar el servicio en los establecimientos educativos, debe tener en cuenta: * El archivo de este documento impreso y debidamente diligenciado debe realizarse conforme a los lineamientos Técnico Administrativos del Programa PAE y estar disponible para consulta de los veedores y supervisores del mismo.  * En procura del cuidado del medio ambiente hacer uso racional de los recursos.  * La firma del presente documento da fé de la veracidad del contenido del mismo para el seguimiento, monitoreo y control de programa.  * El presente formato no debe tener tachones ni enmendaduras para garantizar la validez del mismo.'));




//
// $pdf->SetXY($x, $y);
// $pdf->Cell(0,12,'','B',0,'C',False);
// $pdf->Cell(0,12,'OBSERVACIONES:','B',12,'C',False);
//
//




//
//
//
//
// $pdf->Cell(0,5,'','B',5,'C',False);
// $pdf->Cell(0,11,'','B',12,'C',False);

// $x = $pdf->GetX();
// $y = $pdf->GetY();

$pdf->Ln(10);

// $pdf->SetXY(8, 6.31);
// //Termina el footer
