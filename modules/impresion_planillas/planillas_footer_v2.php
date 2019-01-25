<?php
//Footer
//


$x = 3;


// Condición que oculta o muestra la sección de información de las raciones.
if ($tipoPlanilla == 1 || $tipoPlanilla == 2 || $tipoPlanilla == 3 || $tipoPlanilla == 4) {
	$y = 158;
	$pdf->SetXY($x, $y);

	$pdf->Ln(5);

	$pdf->SetFont('Arial','B',$tamannoFuente);
	$pdf->Cell(72,$tamannoFuente,utf8_decode('RACIONES MENSUALES PROGRAMADAS CAJM:'),0,0,'L',False);
	$pdf->SetFont('Arial','',$tamannoFuente);
	$pdf->Cell(10,5,(($tipoComplemento == "CAJMPS" || $tipoComplemento == "CAJMRI") && ($tipoPlanilla != 1) ? $totales['titulares'] * $totalDias : ""), "B", 0, 'C', False);

	$pdf->Cell(10);
	$pdf->SetFont('Arial','B',$tamannoFuente);
	$pdf->Cell(69,$tamannoFuente,utf8_decode('RACIONES MENSUALES ENTREGADAS CAJM:'),0,0,'L',False);
	$pdf->SetFont('Arial','',$tamannoFuente);
	$pdf->Cell(10,5,(($tipoComplemento == "CAJMPS" || $tipoComplemento == "CAJMRI") && ($tipoPlanilla == 4) ? $totales['entregas'] : "" ),"B",0,'C',False);

	$pdf->Cell(10);
	$pdf->SetFont('Arial','B',$tamannoFuente);
	$pdf->Cell(30,$tamannoFuente,utf8_decode("PREPARAR EN SITIO:"),0,0,'L',False);
	$pdf->SetFont('Arial','',$tamannoFuente);
	$pdf->Cell(5,$tamannoFuente,(($tipoComplemento == "CAJMPS") && ($tipoPlanilla == 4) ? "X" : ""),1,0,'C',False);

	$pdf->Cell(10);
	$pdf->SetFont('Arial','B',$tamannoFuente);
	$pdf->Cell(27,$tamannoFuente,utf8_decode("INDUSTRIALIZADA:"),0,0,'L',False);
	$pdf->SetFont('Arial','',$tamannoFuente);
	$pdf->Cell(5,$tamannoFuente,(($tipoComplemento == "CAJMRI") && ($tipoPlanilla == 4) ? "X" : ""),1,0,'C',False);

	$pdf->Cell(10);
	$pdf->SetFont('Arial','B',$tamannoFuente);
	$pdf->Cell(18,$tamannoFuente,utf8_decode("CATERING:"),0,0,'L',False);
	$pdf->SetFont('Arial','',$tamannoFuente);
	$pdf->Cell(5,$tamannoFuente,'',1,1,'C',False);

	$pdf->Ln(1);

	$pdf->SetFont('Arial','B',$tamannoFuente);
	$pdf->Cell(72,$tamannoFuente,utf8_decode('RACIONES MENSUALES PROGRAMADAS CAJT:'),0,0,'L',False);
	$pdf->SetFont('Arial','',$tamannoFuente);
	$pdf->Cell(10,5,(($tipoComplemento == "CAJTRI") && ($tipoPlanilla != 1) ? $totales['titulares'] * $totalDias : ""), "B", 0, 'C', False);

	$pdf->Cell(10);
	$pdf->SetFont('Arial','B',$tamannoFuente);
	$pdf->Cell(69,$tamannoFuente,utf8_decode('RACIONES MENSUALES ENTREGADAS CAJT:'),0,0,'L',False);
	$pdf->SetFont('Arial','',$tamannoFuente);
	$pdf->Cell(10,5,(($tipoComplemento == "CAJTRI") && ($tipoPlanilla == 4) ? $totales['entregas'] : "" ),"B",0,'C',False);

	$pdf->Cell(10);
	$pdf->SetFont('Arial','B',$tamannoFuente);
	$pdf->Cell(30,$tamannoFuente,utf8_decode("PREPARAR EN SITIO:"),0,0,'L',False);
	$pdf->SetFont('Arial','',$tamannoFuente);
	$pdf->Cell(5,$tamannoFuente,(($tipoComplemento == "CAJTPS") && ($tipoPlanilla != 1) ? "X" : ""),1,0,'C',False);

	$pdf->Cell(10);
	$pdf->SetFont('Arial','B',$tamannoFuente);
	$pdf->Cell(27,$tamannoFuente,utf8_decode("INDUSTRIALIZADA:"),0,0,'L',False);
	$pdf->SetFont('Arial','',$tamannoFuente);
	$pdf->Cell(5,$tamannoFuente,(($tipoComplemento == "CAJTRI") && ($tipoPlanilla != 1) ? "X" : ""),1,0,'C',False);

	$pdf->Cell(10);
	$pdf->SetFont('Arial','B',$tamannoFuente);
	$pdf->Cell(18,$tamannoFuente,utf8_decode("CATERING:"),0,0,'L',False);
	$pdf->SetFont('Arial','',$tamannoFuente);
	$pdf->Cell(5,$tamannoFuente,'',1,1,'C',False);

	$pdf->Ln(1);

	$pdf->SetFont('Arial','B',$tamannoFuente);
	$pdf->Cell(72,$tamannoFuente,utf8_decode('RACIONES MENSUALES PROGRAMADAS ALMUERZOS:'),0,0,'L',False);
	$pdf->SetFont('Arial','',$tamannoFuente);
	$pdf->Cell(10,5,(($tipoComplemento == "APS") && ($tipoPlanilla != 1) ? $totales['titulares'] * $totalDias : "" ), "B", 0, 'C', False);

	$pdf->Cell(10);
	$pdf->SetFont('Arial','B',$tamannoFuente);
	$pdf->Cell(69,$tamannoFuente,utf8_decode('RACIONES MENSUALES ENTREGADAS ALMUERZOS:'),0,0,'L',False);
	$pdf->SetFont('Arial','',$tamannoFuente);
	$pdf->Cell(10,5,(($tipoComplemento == "APS" && $tipoPlanilla == 4) ? $totales['entregas'] : "" ), "B", 0, 'C', False);

	$pdf->Cell(10);
	$pdf->SetFont('Arial','B',$tamannoFuente);
	$pdf->Cell(30,$tamannoFuente,utf8_decode("PREPARAR EN SITIO:"),0,0,'L',False);
	$pdf->SetFont('Arial','',$tamannoFuente);
	$pdf->Cell(5,$tamannoFuente,(($tipoComplemento == "APS") && ($tipoPlanilla == 4) ? "X" : ""),1,0,'C',False);

	$pdf->Cell(10);
	$pdf->SetFont('Arial','B',$tamannoFuente);
	$pdf->Cell(27,$tamannoFuente,utf8_decode("INDUSTRIALIZADA:"),0,0,'L',False);
	$pdf->SetFont('Arial','',$tamannoFuente);
	$pdf->Cell(5,$tamannoFuente,'',1,0,'C',False);

	$pdf->Cell(10);
	$pdf->SetFont('Arial','B',$tamannoFuente);
	$pdf->Cell(18,$tamannoFuente,utf8_decode("CATERING:"),0,0,'L',False);
	$pdf->SetFont('Arial','',$tamannoFuente);
	$pdf->Cell(5,$tamannoFuente,'',1,0,'C',False);

	$pdf->Cell(10);
	$pdf->SetFont('Arial','B',$tamannoFuente);
	$pdf->Cell(22,$tamannoFuente,utf8_decode("OLLA COMÚN:"),0,0,'L',False);
	$pdf->SetFont('Arial','',$tamannoFuente);
	$pdf->Cell(5,$tamannoFuente,'', 1, 1, 'C',False);
} else {
	$y = 186;
	$pdf->SetXY($x, $y);
}

$pdf->Ln(1);
$pdf->SetTextColor(0,0,0);
$pdf->Cell(0,25,'',1,36,'C',False);

if ($tipoPlanilla == 5 || $tipoPlanilla == 6) {
	$pdf->SetXY($x, $y+2);
} else {
	$pdf->SetXY($x, $y+29);
}

$pdf->SetFont('Arial','B',7);
$pdf->Cell(65,5,'FIRMA Y NOMBRE RESPONSABLE DEL OPERADOR',0,0,'L',False);
$pdf->Cell(100,5,'','B',0,'L',False);
$pdf->Cell(5,5,'',0,0,'L',False);
$pdf->Cell(76,5,'FIRMA Y NOMBRE RECTOR ESTABLECIMIENTO EDUCATIVO',0,0,'L',False);
$pdf->Cell(100,5,'','B',1,'L',False);
$pdf->Cell(0,5,'',"B",1,'C',False);
$pdf->Cell(0,5,'OBSERVACIONES:','B',5,'L',False);
$pdf->SetFont('Arial','B',6);
$pdf->Cell(0, 2.9, utf8_decode('NOTA: El operador/responsable de prestar el servicio en los establecimientos educativos, debe tener en cuenta:'),0,1);
$pdf->SetFont('Arial','',6);
$pdf->MultiCell(0,2.9,utf8_decode('* El archivo de este documento impreso y debidamente diligenciado debe realizarse conforme a los lineamientos Técnico Administrativos del Programa PAE y estar disponible para consulta de los veedores y supervisores del mismo.  * En procura del cuidado del medio ambiente hacer uso racional de los recursos.  * La firma del presente documento da fé de la veracidad del contenido del mismo para el seguimiento, monitoreo y control de programa.  * El presente formato no debe tener tachones ni enmendaduras para garantizar la validez del mismo.'));

$pdf->Ln(10);
