<?php

$pdf->SetXY(3,191.5);
$pdf->SetTextColor(0,0,0);

if ($tipoPlanilla == 5 || $tipoPlanilla == 6 /*|| $tipoPlanilla == 7 || $tipoPlanilla == 8*/) {
	// $pdf->SetXY($x, $y+1);
} else {
	// $pdf->SetXY($x, $y);
}

$pdf->Cell(0,5,'Observaciones:','1',5,'L',False);
$pdf->SetFont('Arial','B',6);

/*********************************************************/
$texto_convenciones = "";
$consulta_tipo_complementos = "SELECT CODIGO AS codigo, DESCRIPCION as descripcion FROM tipo_complemento ORDER BY CODIGO";
$respuesta_tipo_complementos = $Link->query($consulta_tipo_complementos) or die("Error al consultar tipo_complemento: ". $Link->error);
if ($respuesta_tipo_complementos->num_rows > 0) {
	while($registros_tipo_complementos = $respuesta_tipo_complementos->fetch_assoc()) {
		$texto_convenciones .= "* ". $registros_tipo_complementos["codigo"] .": ". $registros_tipo_complementos["descripcion"] .". ";
	}
}

$texto_convenciones .= "* SP: Sin pertenencia étnica";
/*********************************************************/

$pdf->SetFillColor(200,200,200);
$pdf->SetFont('Arial','B',5);
$pdf->MultiCell('25','3',utf8_decode('1.Sexo: Marque  F, si es femenino y M, si es masculino'),1,'J',TRUE);
$corY = $pdf->getY();
$corX = $pdf->getX();
$pdf->SetXY($corX+25, $corY-9);
$pdf->MultiCell('40','3',utf8_decode('2. Grado Educativo: Marque el grado del Titular de Derecho de P, como preescolar y de 1 a 11'),1,'J',TRUE);
$corY = $pdf->getY();
$corX = $pdf->getX();
$pdf->SetXY($corX+65, $corY-9);
$pdf->MultiCell('0','3',utf8_decode('3. Tipo de complemento: Indique el tipo de complemento y modalidad que recibe el titular de Derecho , así: CAJMPS (Complemento Alimentario Jornada mañana preparado en sitio , CAJMRI (Complemento Alimentario Jornada mañana Ración Industrializada , : CAJTPS (Complemento Alimentario Jornada Tarde  preparado en sitio , CAJTRI (Complemento Alimentario Jornada tarde Ración Industrializada APS (Almuerzo Preparado en Sitio población Vulnerable), RRI (Refrigerio Reforzado Industrializado), CAIE (Complemento Alimentario  Industrializado para Emergencias), APSD (Almuerzo preparado en sitio Desplazados), CAJMPSD (Complemento Alimentario Jornada Mañana preparado en sitio Desplazados)CAJTPSD (Complemento Alimentario Jornada Tarde preparado en sitio Desplazados)CAJMRID (Complemento Alimentario Jornada mañana Ración Industrializada Desplazados,CAJTRID (Complemento Alimentario Jornada Tarde Ración Industrializada Desplazados).'),1,'J',TRUE);

$pdf->Cell(0, 2.9, utf8_decode('NOTA: El operador/responsable de prestar el servicio en los establecimientos educativos, debe tener en cuenta:'),'LR',1);
$pdf->SetFont('Arial','',6);
$pdf->MultiCell(0,2.9,utf8_decode('* El archivo de este documento impreso y debidamente diligenciado debe realizarse conforme a los lineamientos Técnico Administrativos del Programa PAE y estar disponible para consulta de los veedores y supervisores del mismo.  * En procura del cuidado del medio ambiente hacer uso racional de los recursos.  * La firma del presente documento da fé de la veracidad del contenido del mismo para el seguimiento, monitoreo y control de programa.  * El presente formato no debe tener tachones ni enmendaduras para garantizar la validez del mismo.'),'LRB','J',FALSE);


