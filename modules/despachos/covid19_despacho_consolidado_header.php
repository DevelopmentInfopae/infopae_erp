<?php
$tamannoFuente = 6;
$pdf->SetFont('Arial','',$tamannoFuente);
//header

//var_dump($nomSedes);
$nomSede = $nomSedes[$sede_unica];
// var_dump($nomSede);
// $nomInstitucion= $nomSede['nom_inst'];
// $codInstitucion = $nomSede['cod_inst'];
// $nomSede = $nomSede['nom_sede'];
// $codSede = $nomSede['cod_sede'];
// var_dump($nomSede);





$pdf->SetFont('Arial','B',$tamannoFuente+1);
$logoInfopae = $_SESSION['p_Logo ETC'];
$pdf->SetTextColor(0,0,0);
$pdf->SetLineWidth(.05);
$pdf->Image($logoInfopae, 8 ,5, 86.6, 12.359,'jpg', '');
$current_y = $pdf->GetY();
$current_x = $pdf->GetX();
$pdf->Cell(254,14,'','LTB',0,'C',False);
$pdf->Cell(28,14,'','LTB',0,'C',False);
$pdf->Cell(0,14,'','LTBR','C',False);

$pdf->SetXY($current_x+87, $current_y+2.5);
$pdf->MultiCell(167,3,utf8_decode(" Programa de Alimentación Escolar - PAE \n Atención en el marco del Estado de Emergencia, Económica, Social y Ecológica, derivado de la pandemia del COVID-19 \n Modalidad - ".$descripcionTipo),0,'C',False);


$pdf->SetXY($current_x+254, $current_y);
$pdf->Cell(28,7,utf8_decode('CONTROL'),'B',7,'C',False);
$pdf->Cell(28,7,utf8_decode('Versión 1'),0,7,'C',False);
$pdf->SetXY($current_x+254+28, $current_y);

// $fechaElaboracion[0]
$pdf->Cell(0,7,utf8_decode($fechaDespacho),'B',7,'C',False);
$pdf->Cell(0,7,utf8_decode('Página '.$pdf->GroupPageNo().' de '.$pdf->PageGroupAlias() ),0,0,'C',False);




$pdf->Ln(7);
$pdf->Ln(2);

// $pdf->Cell(4,4,'',0,0,'C',False);

$pdf->SetFont('Arial','B',$tamannoFuente);
$pdf->Cell(19,4,'DEPARTAMENTO:',0,0,'L',False);
$pdf->SetFont('Arial','',$tamannoFuente);
$pdf->Cell(17,4,$_SESSION['p_Departamento'],0,0,'L',False);

$pdf->SetFont('Arial','B',$tamannoFuente);
$pdf->Cell(16.5,4,'CODIGO DANE:',0,0,'L',False);
$pdf->SetFont('Arial','',$tamannoFuente);
$pdf->Cell(6,4,$_SESSION['p_CodDepartamento'],0,0,'L',False);

$pdf->SetFont('Arial','B',$tamannoFuente);
$pdf->Cell(12.5,4,'MUNICIPIO:',0,0,'L',False);
$pdf->SetFont('Arial','',$tamannoFuente);

$aux = '';
for ($ii=0; $ii < count($municipios) ; $ii++) {
  if($ii > 0){
	$aux = $aux.", ";
  }
  $aux = $aux.$municipios[$ii];
}
$pdf->Cell(15,4,utf8_decode($aux),0,0,'L',False);




$pdf->SetFont('Arial','B',$tamannoFuente);
$pdf->Cell(16.5,4,'CODIGO DANE:',0,0,'L',False);
$pdf->SetFont('Arial','',$tamannoFuente);
$pdf->Cell(9,4,$nomSede['cod_mun_sede'],0,0,'L',False);

$pdf->SetFont('Arial','B',$tamannoFuente);
$pdf->Cell(24.3,4,utf8_decode('NOMBRE INSTITUCÓN:'),0,0,'L',False);
$pdf->SetFont('Arial','',$tamannoFuente);


$aux = $nomSede['nom_inst'];
$aux = substr($aux, 0, 43); 
$pdf->Cell(50,4,utf8_decode($aux),0,0,'L',False);





$pdf->SetFont('Arial','B',$tamannoFuente);
$pdf->Cell(16.3,4,'CODIGO DANE:',0,0,'L',False);
$pdf->SetFont('Arial','',$tamannoFuente);
$pdf->Cell(18,4,$nomSede['cod_inst'],0,0,'L',False);

$pdf->SetFont('Arial','B',$tamannoFuente);
$pdf->Cell(17.3,4,'NOMBRE SEDE:',0,0,'L',False);
$pdf->SetFont('Arial','',$tamannoFuente);



//$aux = "123456789123456789123456789123456789123456789123456789123456789123456789123456789123456789123456789123456789123456789123456789123456789";
$aux = $nomSede['nom_sede'];
$aux = substr($aux, 0, 41); 
$pdf->Cell(48,4,utf8_decode($aux),0,0,'L',False);






$pdf->SetFont('Arial','B',$tamannoFuente);
$pdf->Cell(16.5,4,'CODIGO DANE:',0,0,'L',False);
$pdf->SetFont('Arial','',$tamannoFuente);
$pdf->Cell(0,4,$nomSede['cod_sede'],0,0,'L',False);

$pdf->Ln(4);

//$pdf->Cell(4,4,'',0,0,'C',False);

$pdf->SetFont('Arial','B',$tamannoFuente);
$pdf->Cell(13.5,4,'OPERADOR:',0,0,'L',False);
$pdf->SetFont('Arial','',$tamannoFuente);
$pdf->Cell(72.5,4,utf8_decode( $_SESSION['p_Operador'] ),0,0,'L',False);

$pdf->SetFont('Arial','B',$tamannoFuente);
$pdf->Cell(16.5,4,utf8_decode('CONTRATO N°:'),0,0,'L',False);
$pdf->SetFont('Arial','',$tamannoFuente);
$pdf->Cell(9,4,'049',0,0,'L',False);

$pdf->SetFont('Arial','B',$tamannoFuente);
$pdf->Cell(17.6,4,utf8_decode('MES ATENCIÓN:'),0,0,'L',False);
$pdf->SetFont('Arial','',$tamannoFuente);
$pdf->Cell(9,4,utf8_decode(strtoupper ($mes)." "),0,0,'L',False);

$pdf->SetFont('Arial','B',$tamannoFuente);
$pdf->Cell(6.4,4,utf8_decode('AÑO:'),0,0,'L',False);
$pdf->SetFont('Arial','',$tamannoFuente);
$pdf->Cell(10,4,utf8_decode('2020'),0,0,'L',False);

$pdf->SetFont('Arial','B',$tamannoFuente);
$pdf->Cell(24,4,utf8_decode('LUGAR DE ENTREGA:'),0,0,'L',False);
$pdf->SetFont('Arial','',$tamannoFuente);
$pdf->Cell(34,4,utf8_decode(''),'B',0,'L',False);
$pdf->Cell(4,4,utf8_decode(''),0,0,'L',False);

$pdf->SetFont('Arial','B',$tamannoFuente);
$pdf->Cell(14,4,utf8_decode('DIRECCIÓN:'),0,0,'L',False);
$pdf->SetFont('Arial','',$tamannoFuente);
$pdf->Cell(34,4,utf8_decode(''),'B',0,'L',False);
$pdf->Cell(4,4,utf8_decode(''),0,0,'L',False);

$pdf->SetFont('Arial','B',$tamannoFuente);
$pdf->Cell(8,4,utf8_decode('ZONA:'),0,0,'L',False);
$pdf->SetFont('Arial','',$tamannoFuente);
$pdf->Cell(20,4,utf8_decode(''),'B',0,'L',False);

$pdf->Ln(4);
$pdf->Ln(2);

$current_y = $pdf->GetY();
$current_x = $pdf->GetX();

$pdf->SetFont('Arial','B',$tamannoFuente-2);


//TODO sumar 6


$pdf->Rotate_text($current_x+2.9, $current_y+16, utf8_decode('Nº ORDEN'), 90);
$pdf->Cell(4,24,'','TBL',0,'C',False);

$current_y = $pdf->GetY();
$current_x = $pdf->GetX();
$pdf->SetXY($current_x, $current_y+7+3.5);
$pdf->MultiCell(42,2,utf8_decode("APELLIDOS Y NOMBRES DEL\nTITULAR"),0,'C',false);
$pdf->SetXY($current_x, $current_y);
$pdf->Cell(42,24,"",'TBL',0,'C',False);

$current_y = $pdf->GetY();
$current_x = $pdf->GetX();
$pdf->SetXY($current_x, $current_y+7+3.5);
$pdf->MultiCell(17,2,utf8_decode("Nº IDENTIFICACION DEL TITULAR"),0,'C',false);
$pdf->SetXY($current_x, $current_y);
$pdf->Cell(17,24,'','TBL',0,'C',False);

$current_y = $pdf->GetY();
$current_x = $pdf->GetX();
$pdf->SetXY($current_x, $current_y+5+3.5);
$pdf->MultiCell(17,2,utf8_decode("FECHA DE ENTREGA DE LA RACIÓN PARA PREPARAR EN CASA\n(DD/MM/AAAA)"),0,'C',false);
$pdf->SetXY($current_x, $current_y);
$pdf->Cell(17,24,'','TBL',0,'C',False);



$current_y = $pdf->GetY();
$current_x = $pdf->GetX();
$pdf->Cell(13,3,'NIVEL','B',0,'C',False);
$pdf->SetXY($current_x, $current_y+3);
$pdf->Rotate_text($current_x+2.125, $current_y+3+12.5+4, utf8_decode("PREESCOLAR"), 90);
$pdf->Rotate_text($current_x+3+2.5, $current_y+3+11.5+4, utf8_decode("PRIMARIA"), 90);
$pdf->Rotate_text($current_x+3+3+2.7, $current_y+3+10.5+4, utf8_decode("BASICA"), 90);
$pdf->Rotate_text($current_x+3+3+3+2.8, $current_y+3+10+4, utf8_decode("MEDIA"), 90);
$pdf->Cell(3.25,21,'','R',0,'C',False);
$pdf->Cell(3.25,21,'','R',0,'C',False);
$pdf->Cell(3.25,21,'','R',0,'C',False);
$pdf->SetXY($current_x, $current_y);
$pdf->Cell(13,24,'','TBL',0,'C',False);

$current_y = $pdf->GetY();
$current_x = $pdf->GetX();
$pdf->Cell(93.5,3,utf8_decode('CONFORMACIÓN RACIÓN PARA PREPARAR EN CASA (RPC)'),'B',0,'C',False);

include 'covid19_despacho_consolidado_productos.php';

$pdf->SetXY($current_x, $current_y+13.5);
$pdf->SetFont('Arial','B',$tamannoFuente);
$pdf->Cell(94,18,utf8_decode('Especifique Cantidad TOTAL entregada o colocar un guion si el alimento no se entregó en el paquete.'),0,0,'C',False);
$pdf->SetFont('Arial','',$tamannoFuente);
$pdf->SetXY($current_x, $current_y);
$pdf->Cell(94,24,'','TBL',0,'C',False);


/* Despues de los alimentos */
$pdf->SetFont('Arial','B',$tamannoFuente);
$current_y = $pdf->GetY();
$current_x = $pdf->GetX();
$pdf->SetXY($current_x, $current_y+7+3.5);
$pdf->MultiCell(46,2,utf8_decode("NOMBRE COMPLETO DE QUIEN RECIBE LA RACIÓN PARA PREPARAR EN CASA (PADRE, MADRE, ACUDIENTE)"),0,'C',false);
$pdf->SetXY($current_x, $current_y);
$pdf->Cell(46,24,'','TBL',0,'C',False);

$current_y = $pdf->GetY();
$current_x = $pdf->GetX();
$pdf->SetXY($current_x, $current_y+6+3.5);
$pdf->MultiCell(28,2,utf8_decode("Nº IDENTIFICACIÓN DE QUIEN RECIBE LA RACIÓN PARA PREPARAR EN CASA"),0,'C',false);
$pdf->SetXY($current_x, $current_y);
$pdf->Cell(28,24,'','TBL',0,'C',False);

$current_y = $pdf->GetY();
$current_x = $pdf->GetX();
$pdf->SetXY($current_x, $current_y+6+3.5);
$pdf->MultiCell(22,2,utf8_decode("NÚMERO TELEFÓNICO - FIJO / CELULAR DE QUIEN RECIBE LA RACIÓN PARA PREPARAR EN CASA"),0,'C',false);
$pdf->SetXY($current_x, $current_y);
$pdf->Cell(22,24,'','TBL',0,'C',False);

$current_y = $pdf->GetY();
$current_x = $pdf->GetX();
$pdf->SetXY($current_x, $current_y+8+3.5);
$pdf->MultiCell(0,2,utf8_decode("FIRMA O HUELLA DE QUIEN RECIBE LA RACIÓN "),0,'C',false);
$pdf->SetXY($current_x, $current_y);
$pdf->Cell(0,24,'','TBLR',0,'C',False);

$pdf->Ln(24);