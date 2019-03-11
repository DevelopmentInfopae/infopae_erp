<?php
//Footer
//


$x = 3;


// Condición que oculta o muestra la sección de información de las raciones.
if ($tipoPlanilla == 1 || $tipoPlanilla == 2 || $tipoPlanilla == 3 || $tipoPlanilla == 4) {
	$y = 158;
	$altura = 5;
	$pdf->SetXY($x, $y);

	$pdf->Ln(5);

	$pdf->SetFont('Arial','B',$tamannoFuente);
	$pdf->Cell(72,$altura,utf8_decode('RACIONES MENSUALES PROGRAMADAS CAJM:'),0,0,'L',False);
	$pdf->SetFont('Arial','',$tamannoFuente);
	$pdf->Cell(10,$altura,(($tipoComplemento == "CAJMPS" || $tipoComplemento == "CAJMRI") && ($tipoPlanilla != 1) ? $totalProgramadoMes : ""), "B", 0, 'C', False);

	$pdf->Cell(10);
	$pdf->SetFont('Arial','B',$tamannoFuente);
	$pdf->Cell(69,$altura,utf8_decode('RACIONES MENSUALES ENTREGADAS CAJM:'),0,0,'L',False);
	$pdf->SetFont('Arial','',$tamannoFuente);
	$pdf->Cell(10,$altura,(($tipoComplemento == "CAJMPS" || $tipoComplemento == "CAJMRI") && ($tipoPlanilla == 4) ? $totales['entregas'] : "" ),"B",0,'C',False);

	$pdf->Cell(10);
	$pdf->SetFont('Arial','B',$tamannoFuente);
	$pdf->Cell(30,$altura,utf8_decode("PREPARAR EN SITIO:"),0,0,'L',False);
	$pdf->SetFont('Arial','',$tamannoFuente);
	$pdf->Cell(5,$altura,(($tipoComplemento == "CAJMPS") && ($tipoPlanilla == 4) ? "X" : ""),1,0,'C',False);

	$pdf->Cell(10);
	$pdf->SetFont('Arial','B',$tamannoFuente);
	$pdf->Cell(27,$altura,utf8_decode("INDUSTRIALIZADA:"),0,0,'L',False);
	$pdf->SetFont('Arial','',$tamannoFuente);
	$pdf->Cell(5,$altura,(($tipoComplemento == "CAJMRI") && ($tipoPlanilla == 4) ? "X" : ""),1,0,'C',False);

	$pdf->Cell(10);
	$pdf->SetFont('Arial','B',$tamannoFuente);
	$pdf->Cell(18,$altura,utf8_decode("CATERING:"),0,0,'L',False);
	$pdf->SetFont('Arial','',$tamannoFuente);
	$pdf->Cell(5,$altura,'',1,1,'C',False);

	$pdf->Ln(1);

	$pdf->SetFont('Arial','B',$tamannoFuente);
	$pdf->Cell(72,$altura,utf8_decode('RACIONES MENSUALES PROGRAMADAS CAJT:'),0,0,'L',False);
	$pdf->SetFont('Arial','',$tamannoFuente);
	$pdf->Cell(10,$altura,(($tipoComplemento == "CAJTRI") && ($tipoPlanilla != 1) ? $totalProgramadoMes : ""), "B", 0, 'C', False);

	$pdf->Cell(10);
	$pdf->SetFont('Arial','B',$tamannoFuente);
	$pdf->Cell(69,$altura,utf8_decode('RACIONES MENSUALES ENTREGADAS CAJT:'),0,0,'L',False);
	$pdf->SetFont('Arial','',$tamannoFuente);
	$pdf->Cell(10,$altura,(($tipoComplemento == "CAJTRI") && ($tipoPlanilla == 4) ? $totales['entregas'] : "" ),"B",0,'C',False);

	$pdf->Cell(10);
	$pdf->SetFont('Arial','B',$tamannoFuente);
	$pdf->Cell(30,$altura,utf8_decode("PREPARAR EN SITIO:"),0,0,'L',False);
	$pdf->SetFont('Arial','',$tamannoFuente);
	$pdf->Cell(5,$altura,(($tipoComplemento == "CAJTPS") && ($tipoPlanilla != 1) ? "X" : ""),1,0,'C',False);

	$pdf->Cell(10);
	$pdf->SetFont('Arial','B',$tamannoFuente);
	$pdf->Cell(27,$altura,utf8_decode("INDUSTRIALIZADA:"),0,0,'L',False);
	$pdf->SetFont('Arial','',$tamannoFuente);
	$pdf->Cell(5,$altura,(($tipoComplemento == "CAJTRI") && ($tipoPlanilla != 1) ? "X" : ""),1,0,'C',False);

	$pdf->Cell(10);
	$pdf->SetFont('Arial','B',$tamannoFuente);
	$pdf->Cell(18,$altura,utf8_decode("CATERING:"),0,0,'L',False);
	$pdf->SetFont('Arial','',$tamannoFuente);
	$pdf->Cell(5,$altura,'',1,1,'C',False);

	$pdf->Ln(1);

	$pdf->SetFont('Arial','B',$tamannoFuente);
	$pdf->Cell(72,$altura,utf8_decode('RACIONES MENSUALES PROGRAMADAS ALMUERZOS:'),0,0,'L',False);
	$pdf->SetFont('Arial','',$tamannoFuente);
	$pdf->Cell(10,$altura,(($tipoComplemento == "APS") && ($tipoPlanilla != 1) ? $totalProgramadoMes : "" ), "B", 0, 'C', False);

	$pdf->Cell(10);
	$pdf->SetFont('Arial','B',$tamannoFuente);
	$pdf->Cell(69,$altura,utf8_decode('RACIONES MENSUALES ENTREGADAS ALMUERZOS:'),0,0,'L',False);
	$pdf->SetFont('Arial','',$tamannoFuente);
	$pdf->Cell(10,$altura,(($tipoComplemento == "APS" && $tipoPlanilla == 4) ? $totales['entregas'] : "" ), "B", 0, 'C', False);

	$pdf->Cell(10);
	$pdf->SetFont('Arial','B',$tamannoFuente);
	$pdf->Cell(30,$altura,utf8_decode("PREPARAR EN SITIO:"),0,0,'L',False);
	$pdf->SetFont('Arial','',$tamannoFuente);
	$pdf->Cell(5,$altura,(($tipoComplemento == "APS") && ($tipoPlanilla == 4) ? "X" : ""),1,0,'C',False);

	$pdf->Cell(10);
	$pdf->SetFont('Arial','B',$tamannoFuente);
	$pdf->Cell(27,$altura,utf8_decode("INDUSTRIALIZADA:"),0,0,'L',False);
	$pdf->SetFont('Arial','',$tamannoFuente);
	$pdf->Cell(5,$altura,'',1,0,'C',False);

	$pdf->Cell(10);
	$pdf->SetFont('Arial','B',$tamannoFuente);
	$pdf->Cell(18,$altura,utf8_decode("CATERING:"),0,0,'L',False);
	$pdf->SetFont('Arial','',$tamannoFuente);
	$pdf->Cell(5,$altura,'',1,0,'C',False);

	$pdf->Cell(10);
	$pdf->SetFont('Arial','B',$tamannoFuente);
	$pdf->Cell(22,$altura,utf8_decode("OLLA COMÚN:"),0,0,'L',False);
	$pdf->SetFont('Arial','',$tamannoFuente);
	$pdf->Cell(5,$altura,'', 1, 1, 'C',False);
} else {
	$y = 182;
	$pdf->SetXY($x, $y);
}

$pdf->Ln(1);
$pdf->SetTextColor(0,0,0);
$pdf->Cell(0,30,'',1,36,'C',False);

if ($tipoPlanilla == 5 || $tipoPlanilla == 6) {
	$pdf->SetXY($x, $y+1);
} else {
	$pdf->SetXY($x, $y+23);
}

$pdf->SetFont('Arial','B',7);
$pdf->Cell(65,5,'FIRMA Y NOMBRE RESPONSABLE DEL OPERADOR',0,0,'L',False);
$pdf->Cell(100,5,'','B',0,'L',False);
$pdf->Cell(2,5,'',0,0,'L',False);
$pdf->Cell(82,5,'FIRMA, NOMBRE Y DOC. RECTOR ESTABLECIMIENTO EDUCATIVO',0,0,'L',False);
$pdf->Cell(98,5,'','B',1,'L',False);
$pdf->Cell(0,5,'',"B",1,'C',False);
$pdf->Cell(0,5,'OBSERVACIONES:','B',5,'L',False);
$pdf->SetFont('Arial','B',6);
$pdf->Cell(0, 2.9, utf8_decode('NOTA: El operador/responsable de prestar el servicio en los establecimientos educativos, debe tener en cuenta:'),0,1);
$pdf->SetFont('Arial','',6);
$pdf->MultiCell(0,2.9,utf8_decode('* El archivo de este documento impreso y debidamente diligenciado debe realizarse conforme a los lineamientos Técnico Administrativos del Programa PAE y estar disponible para consulta de los veedores y supervisores del mismo.  * En procura del cuidado del medio ambiente hacer uso racional de los recursos.  * La firma del presente documento da fé de la veracidad del contenido del mismo para el seguimiento, monitoreo y control de programa.  * El presente formato no debe tener tachones ni enmendaduras para garantizar la validez del mismo.'));

/*********************************************************/
$texto_convenciones = "";
$consulta_tipo_complementos = "SELECT CODIGO AS codigo, DESCRIPCION as descripcion FROM tipo_complemento ORDER BY CODIGO";
$respuesta_tipo_complementos = $Link->query($consulta_tipo_complementos) or die("Error al consultar tipo_complemento: ". $Link->error);
if ($respuesta_tipo_complementos->num_rows > 0) {
	while($registros_tipo_complementos = $respuesta_tipo_complementos->fetch_assoc()) {
		$texto_convenciones .= "* ". $registros_tipo_complementos["codigo"] .": ". $registros_tipo_complementos["descripcion"] .". ";
	}
}
/*********************************************************/
$pdf->SetFont('Arial','B',6);
$pdf->Cell(0, 2.9, utf8_decode('CONVENCIONES: '),0,1);
$pdf->SetFont('Arial','',6);
$pdf->MultiCell(0,2.9,utf8_decode($texto_convenciones));
$pdf->Ln(10);
