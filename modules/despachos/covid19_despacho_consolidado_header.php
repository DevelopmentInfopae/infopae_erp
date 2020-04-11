<?php
//header
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
$pdf->MultiCell(167,3,utf8_decode(" Programa de Alimentación Escolar - PAE \n Atención en el marco del Estado de Emergencia, Económica, Social y Ecológica, derivado de la pandemia del COVID-19 \n Modalidad - Ración para Preparar en Casa "),0,'C',False);


$pdf->SetXY($current_x+254, $current_y);
$pdf->Cell(28,7,utf8_decode('CONTROL'),'B',7,'C',False);
$pdf->Cell(28,7,utf8_decode('Versión 1'),0,7,'C',False);
$pdf->SetXY($current_x+254+28, $current_y);
$pdf->Cell(0,7,utf8_decode($fechaDespacho),'B',7,'C',False);
$pdf->Cell(0,7,utf8_decode('Página '.$pdf->PageNo().' de {nb}'),0,0,'C',False);


$pdf->Ln(7);
$pdf->Ln(2);

$pdf->Cell(4,4,'',0,0,'C',False);

$pdf->SetFont('Arial','B',$tamannoFuente);
$pdf->Cell(19,4,'DEPARTAMENTO:',0,0,'L',False);
$pdf->SetFont('Arial','',$tamannoFuente);
$pdf->Cell(17,4,'SANTANDER',0,0,'L',False);

$pdf->SetFont('Arial','B',$tamannoFuente);
$pdf->Cell(16.5,4,'CODIGO DANE:',0,0,'L',False);
$pdf->SetFont('Arial','',$tamannoFuente);
$pdf->Cell(6,4,'50',0,0,'L',False);

$pdf->SetFont('Arial','B',$tamannoFuente);
$pdf->Cell(12.5,4,'MUNICIPIO:',0,0,'L',False);
$pdf->SetFont('Arial','',$tamannoFuente);
$pdf->Cell(20,4,'VILLAVICENCIO',0,0,'L',False);

$pdf->SetFont('Arial','B',$tamannoFuente);
$pdf->Cell(16.5,4,'CODIGO DANE:',0,0,'L',False);
$pdf->SetFont('Arial','',$tamannoFuente);
$pdf->Cell(9,4,'50001',0,0,'L',False);

$pdf->SetFont('Arial','B',$tamannoFuente);
$pdf->Cell(24.3,4,utf8_decode('NOMBRE INSTITUCÓN:'),0,0,'L',False);
$pdf->SetFont('Arial','',$tamannoFuente);
$pdf->Cell(36,4,utf8_decode('COL.GENERAL CARLOS ALBAN'),0,0,'L',False);

$pdf->SetFont('Arial','B',$tamannoFuente);
$pdf->Cell(16.3,4,'CODIGO DANE:',0,0,'L',False);
$pdf->SetFont('Arial','',$tamannoFuente);
$pdf->Cell(18,4,'150001002726',0,0,'L',False);

$pdf->SetFont('Arial','B',$tamannoFuente);
$pdf->Cell(17.3,4,'NOMBRE SEDE:',0,0,'L',False);
$pdf->SetFont('Arial','',$tamannoFuente);
$pdf->Cell(36,4,utf8_decode('COL.GENERAL CARLOS ALBAN'),0,0,'L',False);

$pdf->SetFont('Arial','B',$tamannoFuente);
$pdf->Cell(16.5,4,'CODIGO DANE:',0,0,'L',False);
$pdf->SetFont('Arial','',$tamannoFuente);
$pdf->Cell(0,4,'15000100272601',0,0,'L',False);

$pdf->Ln(4);

$pdf->Cell(4,4,'',0,0,'C',False);

$pdf->SetFont('Arial','B',$tamannoFuente);
$pdf->Cell(13.5,4,'OPERADOR:',0,0,'L',False);
$pdf->SetFont('Arial','',$tamannoFuente);
$pdf->Cell(77.5,4,'UNION TEMPORAL PAE PARA VILLAVICENCIO 2020',0,0,'L',False);

$pdf->SetFont('Arial','B',$tamannoFuente);
$pdf->Cell(16.5,4,utf8_decode('CONTRATO N°:'),0,0,'L',False);
$pdf->SetFont('Arial','',$tamannoFuente);
$pdf->Cell(9,4,'049',0,0,'L',False);

$pdf->SetFont('Arial','B',$tamannoFuente);
$pdf->Cell(17.6,4,utf8_decode('MES ATENCIÓN:'),0,0,'L',False);
$pdf->SetFont('Arial','',$tamannoFuente);
$pdf->Cell(6,4,utf8_decode('Abril'),0,0,'L',False);

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

$pdf->Rotate_text($current_x+2.9, $current_y+13, utf8_decode('Nº ORDEN'), 90);
$pdf->Cell(4,18,'','TBL',0,'C',False);

$current_y = $pdf->GetY();
$current_x = $pdf->GetX();
$pdf->SetXY($current_x, $current_y+7);
$pdf->MultiCell(34,2,utf8_decode("APELLIDOS Y NOMBRES DEL\nTITULAR"),0,'C',false);
$pdf->SetXY($current_x, $current_y);
$pdf->Cell(34,18,"",'TBL',0,'C',False);

$current_y = $pdf->GetY();
$current_x = $pdf->GetX();
$pdf->SetXY($current_x, $current_y+7);
$pdf->MultiCell(17,2,utf8_decode("Nº IDENTIFICACION DEL TITULAR"),0,'C',false);
$pdf->SetXY($current_x, $current_y);
$pdf->Cell(17,18,'','TBL',0,'C',False);

$current_y = $pdf->GetY();
$current_x = $pdf->GetX();
$pdf->SetXY($current_x, $current_y+5);
$pdf->MultiCell(17,2,utf8_decode("FECHA DE ENTREGA DE LA RACIÓN PARA PREPARAR EN CASA\n(DD/MM/AAAA)"),0,'C',false);
$pdf->SetXY($current_x, $current_y);
$pdf->Cell(17,18,'','TBL',0,'C',False);



$current_y = $pdf->GetY();
$current_x = $pdf->GetX();
$pdf->Cell(13,3,'NIVEL','B',0,'C',False);
$pdf->SetXY($current_x, $current_y+3);
$pdf->Rotate_text($current_x+2.125, $current_y+3+12.5, utf8_decode("PREESCOLAR"), 90);
$pdf->Rotate_text($current_x+3+2.5, $current_y+3+11.5, utf8_decode("PRIMARIA"), 90);
$pdf->Rotate_text($current_x+3+3+2.7, $current_y+3+10.5, utf8_decode("BASICA"), 90);
$pdf->Rotate_text($current_x+3+3+3+2.8, $current_y+3+10, utf8_decode("MEDIA"), 90);
$pdf->Cell(3.25,15,'','R',0,'C',False);
$pdf->Cell(3.25,15,'','R',0,'C',False);
$pdf->Cell(3.25,15,'','R',0,'C',False);
$pdf->SetXY($current_x, $current_y);
$pdf->Cell(13,18,'','TBL',0,'C',False);


$current_y = $pdf->GetY();
$current_x = $pdf->GetX();
$pdf->Cell(93.5,3,utf8_decode('CONFORMACIÓN RACIÓN PARA PREPARAR EN CASA (RPC)'),'B',0,'C',False);
$pdf->SetXY($current_x, $current_y+3);
/* Grupos de productos de a 8.5mm de ancho para cada producto */
$pdf->Cell(17,3,utf8_decode('LECHE Y PRODUCTOS'),'B',0,'C',False);
$pdf->Cell(42.5,3,utf8_decode('PROTEICOS'),'BL',0,'C',False);
$pdf->Cell(17,3,utf8_decode('CEREALES '),'BL',0,'C',False);
$pdf->Cell(8.5,3,utf8_decode('AZÚCARES'),'BL',0,'C',False);
$pdf->Cell(8.5,3,utf8_decode('GRASAS'),'BL',0,'C',False);


/* Productos de a 8.5mm de ancho para cada producto */
$pdf->SetXY($current_x, $current_y+6);

$ancholinea = 1;
$ancho = 8.5;
$margen = ($ancho / 2) + 0.5;



$aux = "LECHE EN POLVO x 400 gr";
$long_nombre=strlen($aux);
if($long_nombre > $largoNombreProducto){
	$aux = substr($aux,0,$largoNombreProducto);
}
$pdf->Rotate_text($current_x+$margen, $current_y+14.5, utf8_decode($aux), 90);

$aux = "LECHE SABORIZADA/AVENA x 1000 ml";
$long_nombre=strlen($aux);
if($long_nombre > $largoNombreProducto){
	$aux = substr($aux,0,$largoNombreProducto);
}
$pdf->Rotate_text($current_x+($ancho*1)+$margen, $current_y+14.5, utf8_decode($aux), 90);

$aux = "FRIJOL x 500 gr";
$long_nombre=strlen($aux);
if($long_nombre > $largoNombreProducto){
	$aux = substr($aux,0,$largoNombreProducto);
}
$pdf->Rotate_text($current_x+($ancho*2)+$margen, $current_y+14.5, utf8_decode($aux), 90);

$aux = "LENTEJA x 500 gr";
$long_nombre=strlen($aux);
if($long_nombre > $largoNombreProducto){
	$aux = substr($aux,0,$largoNombreProducto);
}
$pdf->Rotate_text($current_x+($ancho*3)+$margen, $current_y+14.5, utf8_decode($aux), 90);

$aux = "HUEVO x u";
$long_nombre=strlen($aux);
if($long_nombre > $largoNombreProducto){
	$aux = substr($aux,0,$largoNombreProducto);
}
$pdf->Rotate_text($current_x+($ancho*4)+$margen, $current_y+14.5, utf8_decode($aux), 90);

$aux = "SARDINAS x 425 gr";
$long_nombre=strlen($aux);
if($long_nombre > $largoNombreProducto){
	$aux = substr($aux,0,$largoNombreProducto);
}
$pdf->Rotate_text($current_x+($ancho*5)+$margen, $current_y+14.5, utf8_decode($aux), 90);

$aux = "ATUN x 170 gr";
$long_nombre=strlen($aux);
if($long_nombre > $largoNombreProducto){
	$aux = substr($aux,0,$largoNombreProducto);
}
$pdf->Rotate_text($current_x+($ancho*6)+$margen, $current_y+14.5, utf8_decode($aux), 90);

$aux = "ARROZ x 1 kg";
$long_nombre=strlen($aux);
if($long_nombre > $largoNombreProducto){
	$aux = substr($aux,0,$largoNombreProducto);
}
$pdf->Rotate_text($current_x+($ancho*7)+$margen, $current_y+14.5, utf8_decode($aux), 90);

$aux = "PASTA x 500 gr";
$long_nombre=strlen($aux);
if($long_nombre > $largoNombreProducto){
	$aux = substr($aux,0,$largoNombreProducto);
}
$pdf->Rotate_text($current_x+($ancho*8)+$margen, $current_y+14.5, utf8_decode($aux), 90);

$aux = "PANELA x 500 gr";
$long_nombre=strlen($aux);
if($long_nombre > $largoNombreProducto){
	$aux = substr($aux,0,$largoNombreProducto);
}
$pdf->Rotate_text($current_x+($ancho*9)+$margen, $current_y+14.5, utf8_decode($aux), 90);

$aux = "ACEITE  x 250 ml";
$long_nombre=strlen($aux);
if($long_nombre > $largoNombreProducto){
	$aux = substr($aux,0,$largoNombreProducto);
}
$pdf->Rotate_text($current_x+($ancho*10)+$margen, $current_y+14.5, utf8_decode($aux), 90);

$pdf->SetXY($current_x, $current_y+6);
$pdf->Cell(8.5,9,utf8_decode(''),'B',0,'C',False);
$pdf->Cell(8.5,9,utf8_decode(''),'BL',0,'C',False);
$pdf->Cell(8.5,9,utf8_decode(''),'BL',0,'C',False);
$pdf->Cell(8.5,9,utf8_decode(''),'BL',0,'C',False);
$pdf->Cell(8.5,9,utf8_decode(''),'BL',0,'C',False);
$pdf->Cell(8.5,9,utf8_decode(''),'BL',0,'C',False);
$pdf->Cell(8.5,9,utf8_decode(''),'BL',0,'C',False);
$pdf->Cell(8.5,9,utf8_decode(''),'BL',0,'C',False);
$pdf->Cell(8.5,9,utf8_decode(''),'BL',0,'C',False);
$pdf->Cell(8.5,9,utf8_decode(''),'BL',0,'C',False);
$pdf->Cell(8.5,9,utf8_decode(''),'BL',0,'C',False);

$pdf->SetXY($current_x, $current_y+12);
$pdf->Cell(93.5,9,utf8_decode('Especifique Cantidad TOTAL entregada o colocar un guion si el alimento no se entregó en el paquete.'),0,0,'C',False);
$pdf->SetXY($current_x, $current_y);
$pdf->Cell(93.5,18,'','TBL',0,'C',False);


/* Despues de los alimentos */

$current_y = $pdf->GetY();
$current_x = $pdf->GetX();
$pdf->SetXY($current_x, $current_y+7);
$pdf->MultiCell(48,2,utf8_decode("NOMBRE COMPLETO DE QUIEN RECIBE LA RACIÓN PARA PREPARAR EN CASA (PADRE, MADRE, ACUDIENTE)"),0,'C',false);
$pdf->SetXY($current_x, $current_y);
$pdf->Cell(48,18,'','TBL',0,'C',False);

$current_y = $pdf->GetY();
$current_x = $pdf->GetX();
$pdf->SetXY($current_x, $current_y+6);
$pdf->MultiCell(28,2,utf8_decode("Nº IDENTIFICACIÓN DE QUIEN RECIBE LA RACIÓN PARA PREPARAR EN CASA"),0,'C',false);
$pdf->SetXY($current_x, $current_y);
$pdf->Cell(28,18,'','TBL',0,'C',False);

$current_y = $pdf->GetY();
$current_x = $pdf->GetX();
$pdf->SetXY($current_x, $current_y+6);
$pdf->MultiCell(28,2,utf8_decode("NÚMERO TELEFÓNICO - FIJO / CELULAR DE QUIEN RECIBE LA RACIÓN PARA PREPARAR EN CASA"),0,'C',false);
$pdf->SetXY($current_x, $current_y);
$pdf->Cell(28,18,'','TBL',0,'C',False);

$current_y = $pdf->GetY();
$current_x = $pdf->GetX();
$pdf->SetXY($current_x, $current_y+8);
$pdf->MultiCell(0,2,utf8_decode("FIRMA O HUELLA DE QUIEN RECIBE LA RACIÓN "),0,'C',false);
$pdf->SetXY($current_x, $current_y);
$pdf->Cell(0,18,'','TBLR',0,'C',False);








// $pdf->Cell(40,5.5,utf8_decode('ETC:'),'LRB',0,'C',False);
// $pdf->Cell(40,5.5,utf8_decode('MUNICIPIO:'),'LRB',0,'C',False);
// $pdf->Cell(40,5.5,utf8_decode('MES DE ENTREGA:'),'LRB',0,'C',False);



// $pdf->Ln(5.5);
// $pdf->Cell(40,5.5,utf8_decode('LUGAR DE ENTREGA:'),'LRB',0,'C',False);
// $pdf->Cell(40,5.5,utf8_decode('DIRECCION:'),'LRB',0,'C',False);
// $pdf->Cell(40,5.5,utf8_decode('ZONA URBANA:'),'LRB',0,'C',False);
// $pdf->Cell(40,5.5,utf8_decode('ZONA RURAL:'),'LRB',0,'C',False);








//$pdf->Cell(0,14,'Page '.$pdf->PageNo().' de {nb}','LTBR',0,'C',False);





// $pdf->Ln(5.5);

// $pdf->Cell(0,5.5,utf8_decode('xxxx'),'LRTB',0,'C',False);





// $pdf->Ln(50);


// $pdf->Cell(116.95,17.29,'',1,0,'C',False);
// $current_y = $pdf->GetY();
// $current_x = $pdf->GetX();
// $pdf->MultiCell(146.45,17.29,'',1,'C',false);
// $pdf->SetXY($current_x, $current_y);

// $pdf->Cell(146.45,5.7,utf8_decode('PROGRAMA DE ALIMENTACIÓN ESCOLAR'),0,5.7,'C',False);
// $pdf->Cell(146.45,5.7,utf8_decode('ORDEN DE PEDIDO DE VIVERES POR MUNICIPIO'),0,5.7,'C',False);
// $pdf->Cell(146.45,5.7,utf8_decode($descripcionTipo),0,5.7,'C',False);
// $pdf->Ln(0.19);







// 	$pdf->Ln(20);


// 	$current_y = $pdf->GetY();
// 	$current_x = $pdf->GetX();
// 	$pdf->Cell(171.8,4.76,'',1,0,'L',False);
// 	$pdf->SetXY($current_x, $current_y);
// 	$pdf->Cell(20,4.76,utf8_decode('OPERADOR:'),0,0,'L',False);
	
// 	$pdf->Cell(151.8,4.76,utf8_decode( $_SESSION['p_Operador'] ),0,0,'L',False);

// 	$current_y = $pdf->GetY();
// 	$current_x = $pdf->GetX();
// 	$pdf->Cell(91.6,4.76,'',1,0,'L',False);
// 	$pdf->SetXY($current_x, $current_y);
	
// 	$pdf->Cell(13,4.76,utf8_decode('FECHA:'),0,0,'L',False);
	
// 	$pdf->Cell(78.6,4.76,utf8_decode($fechaDespacho),0,0,'L',False);
// 	$pdf->Ln(4.76);

// 	$current_y = $pdf->GetY();
// 	$current_x = $pdf->GetX();
// 	$pdf->Cell(117,4.76,'',1,0,'L',False);
// 	$pdf->SetXY($current_x, $current_y);
	
// 	$pdf->Cell(8,4.76,utf8_decode('ETC:'),0,0,'L',False);
	
// 	$pdf->Cell(109,4.76,utf8_decode($_SESSION['p_Nombre ETC']),0,0,'L',False);

// 	$current_y = $pdf->GetY();
// 	$current_x = $pdf->GetX();
// 	$pdf->Cell(146.4,4.76,'',1,0,'L',False);
// 	$pdf->SetXY($current_x, $current_y);

// 	if($ruta == '' || $ruta == 'Todos'){
		
// 		$pdf->Cell(28,4.76,utf8_decode('MUNICIPIO O VEREDA:'),0,0,'L',False);
		

// 		$aux = '';
// 		for ($ii=0; $ii < count($municipios) ; $ii++) {
// 			if($ii > 0){
// 				$aux = $aux.", ";
// 			}
// 			$aux = $aux.$municipios[$ii];
// 		}
// 		$pdf->Cell(53.4,4.76,utf8_decode($aux),0,0,'L',False);

// 	}else{
		
// 		$pdf->Cell(8,4.76,utf8_decode('RUTA:'),0,0,'L',False);
		
// 		$pdf->Cell(46.8,4.76,$ruta,"R",0,'L',False);

		
// 		$pdf->Cell(16,4.76,utf8_decode('PROVEEDOR:'),"R",0,'L',False);
		
// 		$pdf->Cell(0,4.76,utf8_decode($nombre_proveedor),0,0,'L',False);
// 	}













// 	$pdf->Ln(4.76);






// 	$pdf->Ln(0.8);
	
// 	$pdf->Cell(42.5,8,'RANGO DE EDAD',1,0,'C',False);

// 	$current_y = $pdf->GetY();
// 	$current_x = $pdf->GetX();
// 	$pdf->Cell(36.7,8,'',1,0,'L',False);
// 	$pdf->SetXY($current_x, $current_y);
// 	$pdf->Cell(36.7,4,utf8_decode('N° DE RACIONES'),0,4,'C',False);
// 	$pdf->Cell(36.7,4,utf8_decode('ADJUDICADAS'),0,0,'C',False);
// 	$pdf->SetXY($current_x+36.7, $current_y);

// 	$current_y = $pdf->GetY();
// 	$current_x = $pdf->GetX();
// 	$pdf->Cell(36.7,8,'',1,0,'L',False);
// 	$pdf->SetXY($current_x, $current_y);
// 	$pdf->Cell(36.7,4,utf8_decode('N° DE RACIONES'),0,4,'C',False);
// 	$pdf->Cell(36.7,4,utf8_decode('ATENDIDAS'),0,0,'C',False);
// 	$pdf->SetXY($current_x+36.7, $current_y);

// 	$pdf->Cell(45,8,utf8_decode('N° DE DÍAS A ATENDER'),1,0,'C',False);

// 	$current_y = $pdf->GetY();
// 	$current_x = $pdf->GetX();
// 	$pdf->Cell(57.8,8,'',1,0,'L',False);
// 	$pdf->SetXY($current_x, $current_y);
// 	$pdf->Cell(57.8,4,utf8_decode('N° DE MENÚ Y SEMANA DEL CICLO DE'),0,4,'C',False);
// 	$pdf->Cell(57.8,4,utf8_decode('MENÚS ENTREGADO'),0,0,'C',False);
// 	$pdf->SetXY($current_x+57.8, $current_y);

// 	$pdf->Cell(44.7,8,utf8_decode('TOTAL RACIONES'),1,0,'C',False);
// 	$pdf->Ln(8);

	
// 	$current_y = $pdf->GetY();
// 	$current_x = $pdf->GetX();
// 	$pdf->Cell(42.5,14.1,'',1,0,'L',False);
// 	$pdf->SetXY($current_x, $current_y);


// 	$pdf->Cell(42.5,4.7,utf8_decode($get[0]),1,4.7,'C',False);
// 	$pdf->Cell(42.5,4.7,utf8_decode($get[1]),1,4.7,'C',False);
// 	$pdf->Cell(42.5,4.7,utf8_decode($get[2]),1,0,'C',False);
// 	$pdf->SetXY($current_x+42.5, $current_y);

// 	$current_y = $pdf->GetY();
// 	$current_x = $pdf->GetX();
// 	$pdf->Cell(36.7,14.1,'',1,0,'L',False);
// 	$pdf->SetXY($current_x, $current_y);
// 	$pdf->Cell(36.7,4.7,utf8_decode($totalGrupo1),1,4.7,'C',False);
// 	$pdf->Cell(36.7,4.7,utf8_decode($totalGrupo2),1,4.7,'C',False);
// 	$pdf->Cell(36.7,4.7,utf8_decode($totalGrupo3),1,0,'C',False);
// 	$pdf->SetXY($current_x+36.7, $current_y);

// 	$current_y = $pdf->GetY();
// 	$current_x = $pdf->GetX();
// 	$pdf->Cell(36.7,14.1,'',1,0,'L',False);
// 	$pdf->SetXY($current_x, $current_y);
// 	$pdf->Cell(36.7,4.7,utf8_decode($totalGrupo1),1,4.7,'C',False);
// 	$pdf->Cell(36.7,4.7,utf8_decode($totalGrupo2),1,4.7,'C',False);
// 	$pdf->Cell(36.7,4.7,utf8_decode($totalGrupo3),1,0,'C',False);
// 	$pdf->SetXY($current_x+36.7, $current_y);

// 	$pdf->SetFillColor(255,255,255);
// 	$current_y = $pdf->GetY();
// 	$current_x = $pdf->GetX();
// 	$pdf->Cell(45,14.1,'',1,0,'L',False);
// 	$pdf->SetXY($current_x, $current_y+2.35);





// 	$pdf->MultiCell(45,4.7,$auxDias,0,'C',False);

// 	$pdf->SetXY($current_x, $current_y+9.4);
// 	$pdf->MultiCell(45,4.7,'SEMANA: '.$semana,0,'C',False);



// 	$pdf->SetXY($current_x+45, $current_y);

// 	$current_y = $pdf->GetY();
// 	$current_x = $pdf->GetX();
// 	$pdf->Cell(57.8,14.1,'',1,0,'L',False);
// 	$pdf->SetXY($current_x, $current_y+2.35);
// 	//
// 	//$pdf->Cell(57.8,4.7,'SEMANA: '.$auxSemana,0,0,'C',False);
// 	$pdf->Cell(57.8,4.7,'SEMANA: '.$auxCiclos,0,0,'C',False);
	
// 	//$pdf->SetXY($current_x+34, $current_y+2.35);
// 	//$pdf->Cell(10,4.7,$auxSemana,0,4.7,'L',False);
// 	//
// 	$pdf->SetXY($current_x, $current_y+7,05);
// 	$pdf->Cell(57.8,4.7,'MENUS: '.$auxMenus,0,0,'C',False);
// 	//
// 	//$pdf->SetXY($current_x+33, $current_y+7,05);
// 	//$pdf->Cell(57.8,4.7,$auxCiclos,0,0,'L',False);

// 	$pdf->SetXY($current_x+57.8, $current_y);
// 	$current_y = $pdf->GetY();
// 	$current_x = $pdf->GetX();
// 	$pdf->Cell(44.7,14.1,'',1,0,'L',False);
// 	$pdf->SetXY($current_x+2, $current_y+2.35);





// 	 $jm = '';
// 	 $jt = '';

// 	 // 2 es la jornada de la mañana
// 	 // 3 es la jornada de la tarde
// 	 if($jornada == 2){
// 		$jm = $totalGrupo1 + $totalGrupo2 + $totalGrupo3;
// 	 }else if($jornada == 3){
// 		$jt = $totalGrupo1 + $totalGrupo2 + $totalGrupo3;
// 	 }
// 	$pdf->Cell(7,4.7,'JM:',0,0,'L',False);
// 	$pdf->Cell(33,4.7,$jm,'B',0,'L',False);
// 	$pdf->SetXY($current_x+2, $current_y+7.05);
// 	$pdf->Cell(7,4.7,'JT:',0,0,'L',False);
// 	$pdf->Cell(33,4.7,$jt,'B',0,'L',False);







// 	$pdf->SetXY($current_x, $current_y+14.1);
// 	$pdf->Ln(0.8);

	



//  $current_y = $pdf->GetY();
// 	$current_x = $pdf->GetX();
// 	$pdf->Cell(23.788,15,'',1,0,'C',False);
// 	$pdf->SetXY($current_x, $current_y);
// 	$pdf->Ln(2.5);
// 	$pdf->MultiCell(23.755,5,'GRUPO ALIMENTO',0,'C',False);
// 	$pdf->SetXY($current_x+23.788, $current_y);




// 	$pdf->Cell(48.972,15,'ALIMENTO',1,0,'C',False);







// 	$current_y = $pdf->GetY();
// 	$current_x = $pdf->GetX();
// 	$pdf->Cell(39.33,15,'',1,0,'L',False);
// 	$pdf->SetXY($current_x, $current_y);
// 	$pdf->Cell(39.33,8,'',1,0,'L',False);
// 	$pdf->SetXY($current_x, $current_y);
// 	$pdf->Cell(39.33,4,'CNT DE ALIMENTOS POR',0,4,'C',False);
// 	$pdf->Cell(39.33,4,utf8_decode('NÚMEROS DE RACIONES'),0,4,'C',False);
// 	$current_y2 = $pdf->GetY();
// 	$current_x2 = $pdf->GetX();
// 	$pdf->Cell(13.1,7,utf8_decode(''),1,0,'C',False);
// 	$pdf->SetXY($current_x2, $current_y2);


// 	$etario_1 = str_replace(" + 11 meses", "", $get[0]);
// 	$etario_2 = str_replace(" + 11 meses", "", $get[1]);
// 	$etario_3 = str_replace(" + 11 meses", "", $get[2]);

// 	$etario_1 = str_replace(" años", "", $etario_1);
// 	$etario_2 = str_replace(" años", "", $etario_2);
// 	$etario_3 = str_replace(" años", "", $etario_3);

// 	$pdf->Cell(13.1,3.5,utf8_decode($etario_1),0,3.5,'C',False);
// 	$pdf->Cell(13.1,3.5,utf8_decode('AÑOS'),0,3.5,'C',False);

// 	$pdf->SetXY($current_x2+13.1, $current_y2);
// 	$current_y2 = $pdf->GetY();
// 	$current_x2 = $pdf->GetX();
// 	$pdf->Cell(13.1,7,utf8_decode(''),1,0,'C',False);
// 	$pdf->SetXY($current_x2, $current_y2);
// 	$pdf->Cell(13.1,3.5,utf8_decode($etario_2),0,3.5,'C',False);
// 	$pdf->Cell(13.1,3.5,utf8_decode('AÑOS'),0,3.5,'C',False);

// 	$pdf->SetXY($current_x2+13.1, $current_y2);
// 	$current_y2 = $pdf->GetY();
// 	$current_x2 = $pdf->GetX();
// 	$pdf->Cell(13.1,7,utf8_decode(''),1,0,'C',False);
// 	$pdf->SetXY($current_x2, $current_y2);
// 	$pdf->Cell(13.1,3.5,utf8_decode($etario_3),0,3.5,'C',False);
// 	$pdf->Cell(13.1,3.5,utf8_decode('AÑOS'),0,3.5,'C',False);

// 	$pdf->SetXY($current_x+39.33, $current_y);
// 	$current_y = $pdf->GetY();
// 	$current_x = $pdf->GetX();
// 	$pdf->Cell(13.141,15,'',1,0,'L',False);
// 	$pdf->SetXY($current_x, $current_y);
// 	$pdf->Cell(13.141,5,'UNIDAD',0,5,'C',False);
// 	$pdf->Cell(13.141,5,'DE',0,5,'C',False);
// 	$pdf->Cell(13.141,5,'MEDIDA',0,5,'C',False);


// 	$pdf->SetXY($current_x+13.141, $current_y);
// 	$current_y = $pdf->GetY();
// 	$current_x = $pdf->GetX();
// 	$pdf->Cell(13.141,15,'',1,0,'L',False);
// 	$pdf->SetXY($current_x, $current_y+2.5);
// 	$pdf->Cell(13.141,5,'CNT',0,5,'C',False);
// 	$pdf->Cell(13.141,5,'TOTAL',0,5,'C',False);


// 	$pdf->SetXY($current_x+13.141, $current_y);
// 	$current_y = $pdf->GetY();
// 	$current_x = $pdf->GetX();
// 	$pdf->Cell(31.838,15,'',1,0,'L',False);
// 	$pdf->SetXY($current_x, $current_y);
// 	$pdf->Cell(31.838,4,'CANTIDAD',0,4,'C',False);
// 	$pdf->Cell(31.838,4,'ENTREGADA','B',4,'C',False);
// 	$pdf->Cell(10.6,7,'TOTAL','R',0,'C',False);
// 	$pdf->Cell(10.638,7,'C','R',0,'C',False);
// 	$pdf->Cell(10.6,7,'NC','R',0,'C',False);

// 	$pdf->SetXY($current_x+31.838, $current_y);
// 	$current_y = $pdf->GetY();
// 	$current_x = $pdf->GetX();
// 	$pdf->Cell(27.252,15,'',1,0,'L',False);
// 	$pdf->SetXY($current_x, $current_y);
// 	$pdf->Cell(27.252,4,utf8_decode('ESPECIFICACIÓN'),0,4,'C',False);
// 	$pdf->Cell(27.252,4,utf8_decode('DE CALIDAD'),'B',4,'C',False);
// 	$pdf->Cell(13.626,7,'C','R',0,'C',False);
// 	$pdf->Cell(13.626,7,'NC','R',0,'C',False);

// 	$pdf->SetXY($current_x+27.252, $current_y);
// 	$current_y = $pdf->GetY();
// 	$current_x = $pdf->GetX();
// 	$pdf->Cell(32.191,15,'',1,0,'L',False);
// 	$pdf->SetXY($current_x, $current_y);
// 	$pdf->Cell(32.191,8,utf8_decode('FALTANTES'),'B',8,'C',False);
// 	$pdf->Cell(9.349,7,'SI','R',0,'C',False);
// 	$pdf->Cell(8.819,7,'NO','R',0,'C',False);
// 	$pdf->Cell(14.023,7,'CANT','R',0,'C',False);

// 	$pdf->SetXY($current_x+32.191, $current_y);
// 	$current_y = $pdf->GetY();
// 	$current_x = $pdf->GetX();
// 	$pdf->Cell(33.747,15,'',1,0,'L',False);
// 	$pdf->SetXY($current_x, $current_y);
// 	$pdf->Cell(33.747,8,utf8_decode('DEVOLUCIÓN'),'B',8,'C',False);
// 	$pdf->Cell(9.26,7,'SI','R',0,'C',False);
// 	$pdf->Cell(9.084,7,'NO','R',0,'C',False);
// 	$pdf->Cell(15.403,7,'CANT','R',0,'C',False);

// 	$pdf->SetXY($current_x, $current_y);
// 	$pdf->Ln(15);

	
