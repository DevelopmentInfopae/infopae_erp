<?php
//header

if ($cantGruposEtarios == '3') {
	$logoInfopae = $_SESSION['p_Logo ETC'];
	$pdf->SetFont('Arial');
	$pdf->SetTextColor(0,0,0);
	$pdf->SetLineWidth(.05);
	$pdf->Image($logoInfopae, 25 ,7, 65, 10,'jpg', '');
	$pdf->Cell(103,10,'','LT',0,'C',False);
	$current_y = $pdf->GetY();
	$current_x = $pdf->GetX();
	$pdf->Cell(0,10,utf8_decode(''),'LRT',0,'C',False);
	$pdf->SetXY($current_x, $current_y);
	$pdf->SetFont('Arial','B',$tamannoFuente);
	$pdf->Cell(0,2.5,utf8_decode('PROGRAMA DE ALIMENTACIÓN ESCOLAR'),0,2.5,'C',False);

	$ordenCompraGeneral = $despachosRecibidos[0];

	if($ordenCompraGeneral < 10){
		$ordenCompraGeneral = '0'.$ordenCompraGeneral;
	}
	if($ordenCompraGeneral < 100){
		$ordenCompraGeneral = '0'.$ordenCompraGeneral;
	}
	if($ordenCompraGeneral < 1000){
		$ordenCompraGeneral = '0'.$ordenCompraGeneral;
	}
	if($ordenCompraGeneral < 10000){
		$ordenCompraGeneral = '0'.$ordenCompraGeneral;
	}

	$pdf->Cell(0,2.5,utf8_decode('ORDEN DE COMPRA A PROVEEDORES Nº ').utf8_decode($ordenCompraGeneral),0,2.5,'C',False);

	// modificacion para agregar varios complementos
	$alturaComplentos  = 0; 
	foreach ($descripcionTipo as $key => $value) {
		$pdf->Cell(0,2.5,utf8_decode($value),0,2.5,'C',False);
		$alturaComplentos += 2.5;
	}

	$pdf->Cell(0,2.5,utf8_decode($tipoDespacho),'RLB',2.5,'C',False);
	$alturaComplentos += 2.5;
	$cordenadaY = $pdf->GetY();
	$cordenadaX = $pdf->GetX();
	$pdf->SetXY($cordenadaX-103, $cordenadaY-$alturaComplentos);
	$pdf->Cell(0,$alturaComplentos,'','BL',0,'C',False);

	$pdf->Ln(1.5);

	$current_y = $pdf->GetY();
	$current_x = $pdf->GetX();
	$pdf->SetXY($current_x, $current_y+$alturaComplentos);
	$pdf->Cell(15,4.76,utf8_decode('OPERADOR:'),'TLB',0,'L',False);
	$pdf->SetFont('Arial','',$tamannoFuente);
	$pdf->Cell(60,4.76,utf8_decode( $_SESSION['p_Operador'] ),'BT',0,'L',False);

	$current_y = $pdf->GetY();
	$current_x = $pdf->GetX();
	$pdf->Cell(0,4.76,'','BRT',0,'L',False);
	$pdf->SetXY($current_x, $current_y);
	$pdf->SetFont('Arial','B',$tamannoFuente);

	$pdf->Cell(15,4.76,utf8_decode('PROVEEDOR:'),'L',0,'L',False);
	$pdf->SetFont('Arial','',$tamannoFuente);
	$pdf->Cell(50,4.76,utf8_decode(mb_strtoupper ($nombre_proveedor)),0,0,'L',False);

	$pdf->SetFont('Arial','B',$tamannoFuente);
	$pdf->Cell(6,4.76,utf8_decode('NIT:'),0,0,'L',False);
	$pdf->SetFont('Arial','',$tamannoFuente);
	$pdf->Cell(20,4.76,utf8_decode(number_format($nit_proveedor, 0, ',', '.') ),'R',0,'L',False);

	$current_y = $pdf->GetY();
	$current_x = $pdf->GetX();
	$pdf->Cell(0,4.76,'','R',0,'L',False);
	$pdf->SetXY($current_x, $current_y);
	$pdf->SetFont('Arial','B',$tamannoFuente);
	$pdf->Cell(10,4.76,utf8_decode('FECHA:'),0,0,'L',False);
	$pdf->SetFont('Arial','',$tamannoFuente);
	$pdf->Cell(0,4.76,utf8_decode($fechaDespacho),0,0,'L',False);

	$pdf->Ln(4.76);
	$current_y = $pdf->GetY();
	$current_x = $pdf->GetX();
	$pdf->Cell(103,4.76,'','LR',0,'L',False);
	$pdf->SetXY($current_x, $current_y);
	$pdf->SetFont('Arial','B',$tamannoFuente);
	$pdf->Cell(8,4.76,utf8_decode('ETC:'),0,0,'L',False);
	$pdf->SetFont('Arial','',$tamannoFuente);
	$pdf->Cell(95,4.76,utf8_decode($_SESSION['p_Nombre ETC']),0,0,'L',False);

	$current_y = $pdf->GetY();
	$current_x = $pdf->GetX();
	$pdf->Cell(0,4.76,'','R',0,'L',False);
	$pdf->SetXY($current_x, $current_y);

	$pdf->SetFont('Arial','B',$tamannoFuente);
	$pdf->Cell(19,4.76,utf8_decode('RUTA/MUNICIPIO:'),0,0,'L',False);
	$pdf->SetFont('Arial','',$tamannoFuente);
	$pdf->Cell(0,4.76,utf8_decode(mb_strtoupper ($rutaMunicipio)),0,0,'L',False);

	$pdf->Ln(4.76);

	$pdf->SetFont('Arial','B',$tamannoFuente);
	$pdf->Cell(49,8,'RANGO DE EDAD','LBT',0,'C',True);

	$current_y = $pdf->GetY();
	$current_x = $pdf->GetX();
	$pdf->Cell(52.441,8,utf8_decode('N° RACIONES ADJUDICADAS'),'LBT',0,'C',True);
	$pdf->Cell(43.979,8,utf8_decode('N° DÍAS A ATENDER'),'LBT',0,'C',True);

	$current_y = $pdf->GetY();
	$current_x = $pdf->GetX();
	$pdf->Cell(30,8,utf8_decode('N° DE MENÚ'),'LBT',0,'C',True);

	$pdf->Cell(0,8,utf8_decode('TOTAL RACIONES'),1,0,'C',True);
	$pdf->Ln(8);

	$pdf->SetFont('Arial','',$tamannoFuente);
	$current_y = $pdf->GetY();
	$current_x = $pdf->GetX();

	$aux = $get[0];
	$pos = strpos($aux, 'Grupo ');
	if ($pos !== false){ $aux = substr($aux, $pos+6); }
	$pdf->Cell(49,4.7,utf8_decode($aux),'BLR',4.7,'C',False);

	$aux = $get[1];
	$pos = strpos($aux, 'Grupo ');
	if ($pos !== false){ $aux = substr($aux, $pos+6); }
	$pdf->Cell(49,4.7,utf8_decode($aux),'BLR',4.7,'C',False);

	$aux = $get[2];
	$pos = strpos($aux, 'Grupo ');
	if ($pos !== false){ $aux = substr($aux, $pos+6); }
	$pdf->Cell(49,4.7,utf8_decode($aux),'BLR',0,'C',False);
	$pdf->SetXY($current_x+49, $current_y);

	$current_y = $pdf->GetY();
	$current_x = $pdf->GetX();
	$pdf->Cell(52.441,4.7,utf8_decode($totalGrupo1),'B',4.7,'C',False);
	$pdf->Cell(52.441,4.7,utf8_decode($totalGrupo2),'B',4.7,'C',False);
	$pdf->Cell(52.441,4.7,utf8_decode($totalGrupo3),'B',4.7,'C',False);
	$pdf->SetXY($current_x+52.441, $current_y);

	$current_y = $pdf->GetY();
	$current_x = $pdf->GetX();
	$pdf->Cell(43.979,14.1,'','LB',0,'L',False);

	// N° DÍAS A ATENDER
	$pdf->SetXY($current_x, $current_y+1);
	if($tipoComplemento == 'RPC'){
		if($_POST['imprimirMesIC']){
			$pdf->MultiCell(43.979,2.66,mb_strtoupper($mes),0,'C',False);
		}
	}else{
		$pdf->MultiCell(43.979,2.66,$auxDias,0,'C',False);
		$pdf->SetXY($current_x, $current_y+9.4);
		if(strpos($semana, ',') !== false){
			$aux = "SEMANAS: $semana";
		}else{
			$aux = "SEMANA: $semana";
		}
		$pdf->MultiCell(43.979,2.66,$aux,0,'C',False);
	}

	// N° DE MENÚ
	$pdf->SetXY($current_x+43.979, $current_y);
	$current_y = $pdf->GetY();
	$current_x = $pdf->GetX();
	$pdf->Cell(30,14.1,'','LB',0,'L',False);
	$pdf->SetXY($current_x, $current_y+1);
	if($tipoComplemento == 'RPC'){
		$aux = "N/A";
		$pdf->Cell(30,4.7,$aux,0,0,'C',False);
	}
	else{
		if(strpos($auxCiclos, ',') !== false){ $aux = ""; }else{ $aux = "SEMANA: $auxCiclos"; }
		$pdf->Cell(30,3.5,$aux,0,0,'C',False);
		$pdf->SetFont('Arial','',$tamannoFuente);
		$pdf->SetXY($current_x, $current_y+5);
		$pdf->MultiCell(30,2.66,'MENUS: '.$auxMenus,0,'C',False);
	}

	$pdf->SetXY($current_x+30, $current_y);
	$current_y = $pdf->GetY();
	$current_x = $pdf->GetX();
	$pdf->Cell(0,14.1,$totalGrupo1 + $totalGrupo2 + $totalGrupo3,'LRB',0,'C',False);
	$pdf->SetXY($current_x+2, $current_y+2.35);
	$pdf->SetXY($current_x, $current_y+14.1);
	$pdf->Ln(0.8);

	$pdf->SetFont('Arial','B',$tamannoFuente);
	$current_y = $pdf->GetY();
	$current_x = $pdf->GetX();
	$pdf->Cell(49,15,'ALIMENTO','BLT',0,'C',True);
	$current_y = $pdf->GetY();
	$current_x = $pdf->GetX();
	$pdf->Cell(39.33,15,'',0,0,'L',False);
	$pdf->SetXY($current_x, $current_y);
	$pdf->Cell(39.33,8,'','LT',0,'L',True);
	$pdf->SetXY($current_x, $current_y);
	$pdf->Cell(39.33,4,'CNT DE ALIMENTOS POR',0,4,'C',False);
	$pdf->Cell(39.33,4,utf8_decode('NÚMEROS DE RACIONES'),0,4,'C',False);
	$current_y2 = $pdf->GetY();
	$current_x2 = $pdf->GetX();

	$etario_1 = $get[0];
	$etario_2 = $get[1];
	$etario_3 = $get[2];

	$etario_1 = str_replace("Grupo ", "", $get[0]);
	$etario_2 = str_replace("Grupo ", "", $get[1]);
	$etario_3 = str_replace("Grupo ", "", $get[2]);

	$pdf->SetFont('Arial','B',$tamannoFuente-0.5);
	$pdf->Cell(13.1,7,utf8_decode(''),'TLB',0,'C',True);
	$pdf->SetXY($current_x2, $current_y2);
	$pdf->MultiCell(13.1,3.5,utf8_decode($etario_1),0,'C',False);

	$pdf->SetXY($current_x2+13.1, $current_y2);
	$current_y2 = $pdf->GetY();
	$current_x2 = $pdf->GetX();
	$pdf->Cell(13.1,7,utf8_decode(''),'BLT',0,'C',True);
	$pdf->SetXY($current_x2, $current_y2);
	$pdf->MultiCell(13.1,3.5,utf8_decode($etario_2),0,'C',False);

	$pdf->SetXY($current_x2+13.1, $current_y2);
	$current_y2 = $pdf->GetY();
	$current_x2 = $pdf->GetX();
	$pdf->Cell(13.1,7,utf8_decode(''),'BLT',0,'C',True);
	$pdf->SetXY($current_x2, $current_y2);
	$pdf->MultiCell(13.1,3.5,utf8_decode($etario_3),0,'C',False);

	$pdf->SetFont('Arial','B',$tamannoFuente);
	$pdf->SetXY($current_x+39.33, $current_y);
	$current_y = $pdf->GetY();
	$current_x = $pdf->GetX();
	$pdf->Cell(13.141,15,'','BLT',0,'L',True);
	$pdf->SetXY($current_x, $current_y);
	$pdf->Cell(13.141,5,'UNIDAD',0,5,'C',False);
	$pdf->Cell(13.141,5,'DE',0,5,'C',False);
	$pdf->Cell(13.141,5,'MEDIDA',0,5,'C',False);

	$pdf->SetXY($current_x+13.141, $current_y);
	$current_y = $pdf->GetY();
	$current_x = $pdf->GetX();
	$pdf->Cell(13.141,15,'','BLT',0,'L',True);
	$pdf->SetXY($current_x, $current_y+2.5);
	$pdf->Cell(13.141,5,'CANTIDAD',0,5,'C',False);
	$pdf->Cell(13.141,5,'TOTAL',0,5,'C',False);

	$pdf->SetXY($current_x+13.141, $current_y);
	$current_y = $pdf->GetY();
	$current_x = $pdf->GetX();
	$pdf->Cell(31.838,15,'','BLT',0,'L',True);
	$pdf->SetXY($current_x, $current_y);
	$pdf->SetFont('Arial','B',$tamannoFuente-0.5);
	$pdf->Cell(31.838,8,'CANTIDAD ORDEN DE COMPRA','B',4,'C',False);
	$pdf->SetFont('Arial','B',$tamannoFuente);
	$pdf->Cell(10.6,7,'TOTAL','R',0,'C',False);
	$pdf->Cell(10.638,7,'D','R',0,'C',False);
	$pdf->Cell(10.6,7,'ND',0,0,'C',False);
	$pdf->SetXY($current_x+31.838, $current_y);

	$pdf->Cell(0,15,'OBSERVACIONES','BLTR',0,'C',True);
	$pdf->SetXY($current_x, $current_y);
	$pdf->Ln(15);
	$pdf->SetFont('Arial','',$tamannoFuente);

}

// tratamiento de los cinco grupos etarios
if ($cantGruposEtarios == '5') {
	$logoInfopae = $_SESSION['p_Logo ETC'];
	$pdf->SetFont('Arial');
	$pdf->SetTextColor(0, 0, 0);
	$pdf->SetLineWidth(.05);
	$pdf->Image($logoInfopae, 25 ,7, 65, 10,'jpg', '');
	$pdf->Cell(103, 10, '', 'LT', 0, 'C', False);
	$current_y = $pdf->GetY();
	$current_x = $pdf->GetX();
	$pdf->Cell(0, 10, utf8_decode(''), 'LRT', 0, 'C', False);
	$pdf->SetXY($current_x, $current_y);
	$pdf->SetFont('Arial', 'B', $tamannoFuente);
	$pdf->Cell(0, 2.5, utf8_decode('PROGRAMA DE ALIMENTACIÓN ESCOLAR'), 0, 2.5, 'C', False);

	$ordenCompraGeneral = $despachosRecibidos[0];
	if($ordenCompraGeneral < 10){
		$ordenCompraGeneral = '0'.$ordenCompraGeneral;
	}
	if($ordenCompraGeneral < 100){
		$ordenCompraGeneral = '0'.$ordenCompraGeneral;
	}
	if($ordenCompraGeneral < 1000){
		$ordenCompraGeneral = '0'.$ordenCompraGeneral;
	}
	if($ordenCompraGeneral < 10000){
		$ordenCompraGeneral = '0'.$ordenCompraGeneral;
	}

	$pdf->Cell(0, 2.5, utf8_decode('ORDEN DE COMPRA A PROVEEDORES Nº ').utf8_decode($ordenCompraGeneral), 0, 2.5, 'C', False);

	// modificacion para agregar varios complementos
	$alturaComplentos  = 0; 
	foreach ($descripcionTipo as $key => $value) {
		$pdf->Cell(0, 2.5, utf8_decode($value), 0, 2.5, 'C', False);
		$alturaComplentos += 2.5;
	}

	$pdf->Cell(0, 2.5, utf8_decode($tipoDespacho), 'RLB', 2.5, 'C', False);
	$alturaComplentos += 2.5;
	$cordenadaY = $pdf->GetY();
	$cordenadaX = $pdf->GetX();
	$pdf->SetXY($cordenadaX-103, $cordenadaY-$alturaComplentos);
	$pdf->Cell(0, $alturaComplentos, '', 'BL', 0, 'C', False);

	$pdf->Ln(1.5);

	$current_y = $pdf->GetY();
	$current_x = $pdf->GetX();
	$pdf->SetXY($current_x, $current_y+$alturaComplentos);
	$pdf->Cell(15, 4.76, utf8_decode('OPERADOR:'), 'TLB', 0, 'L', False);
	$pdf->SetFont('Arial','',$tamannoFuente);
	$pdf->Cell(60, 4.76, utf8_decode( $_SESSION['p_Operador'] ), 'BT', 0, 'L', False);

	$current_y = $pdf->GetY();
	$current_x = $pdf->GetX();
	$pdf->Cell(0, 4.76, '', 'BRT', 0, 'L', False);
	$pdf->SetXY($current_x, $current_y);
	$pdf->SetFont('Arial', 'B', $tamannoFuente);

	$pdf->Cell(15, 4.76, utf8_decode('PROVEEDOR:'), 'L', 0, 'L', False);
	$pdf->SetFont('Arial', '', $tamannoFuente);
	$pdf->Cell(50, 4.76, utf8_decode(mb_strtoupper ($nombre_proveedor)), 0, 0, 'L', False);

	$pdf->SetFont('Arial','B',$tamannoFuente);
	$pdf->Cell(6, 4.76, utf8_decode('NIT:'), 0, 0, 'L', False);
	$pdf->SetFont('Arial','',$tamannoFuente);
	$pdf->Cell(20, 4.76, utf8_decode(number_format($nit_proveedor, 0, ',', '.') ), 'R', 0, 'L', False);

	$current_y = $pdf->GetY();
	$current_x = $pdf->GetX();
	$pdf->Cell(0, 4.76, '', 'R', 0, 'L', False);
	$pdf->SetXY($current_x, $current_y);
	$pdf->SetFont('Arial', 'B', $tamannoFuente);
	$pdf->Cell(10, 4.76, utf8_decode('FECHA:'), 0, 0, 'L', False);
	$pdf->SetFont('Arial', '', $tamannoFuente);
	$pdf->Cell(0, 4.76, utf8_decode($fechaDespacho), 0, 0, 'L', False);

	$pdf->Ln(4.76);
	$current_y = $pdf->GetY();
	$current_x = $pdf->GetX();
	$pdf->Cell(103, 4.76, '', 'LR', 0, 'L', False);
	$pdf->SetXY($current_x, $current_y);
	$pdf->SetFont('Arial', 'B', $tamannoFuente);
	$pdf->Cell(8, 4.76, utf8_decode('ETC:'), 0, 0, 'L', False);
	$pdf->SetFont('Arial', '', $tamannoFuente);
	$pdf->Cell(95, 4.76, utf8_decode($_SESSION['p_Nombre ETC']), 0, 0, 'L', False);

	$current_y = $pdf->GetY();
	$current_x = $pdf->GetX();
	$pdf->Cell(0, 4.76, '', 'R', 0, 'L', False);
	$pdf->SetXY($current_x, $current_y);

	$pdf->SetFont('Arial', 'B', $tamannoFuente);
	$pdf->Cell(19, 4.76, utf8_decode('RUTA/MUNICIPIO:'), 0, 0, 'L', False);
	$pdf->SetFont('Arial', '', $tamannoFuente);
	$pdf->Cell(0, 4.76, utf8_decode(mb_strtoupper ($rutaMunicipio)), 0, 0, 'L', False);

	$pdf->Ln(4.76);

	$pdf->SetFont('Arial', 'B', $tamannoFuente);
	$pdf->Cell(49, 8, 'RANGO DE EDAD', 'LBT', 0, 'C', True);

	$current_y = $pdf->GetY();
	$current_x = $pdf->GetX();
	$pdf->Cell(52, 8, utf8_decode('N° RACIONES ADJUDICADAS'), 'LBT', 0, 'C', True);
	$pdf->Cell(37, 8, utf8_decode('N° DÍAS A ATENDER'), 'LBT', 0, 'C', True);

	$current_y = $pdf->GetY();
	$current_x = $pdf->GetX();
	$pdf->Cell(37, 8, utf8_decode('N° DE MENÚ'), 'LBT', 0, 'C', True);

	$pdf->Cell(0, 8, utf8_decode('TOTAL RACIONES'), 1, 0, 'C', True);
	$pdf->Ln(8);

	$pdf->SetFont('Arial','',$tamannoFuente);
	$current_y = $pdf->GetY();
	$current_x = $pdf->GetX();

	$aux = $get[0];
	$pos = strpos($aux, 'Grupo ');
	if ($pos !== false){ $aux = substr($aux, $pos+6); }
	$pdf->Cell(49,4.7,utf8_decode($aux),'BLR',4.7,'C',False);

	$aux = $get[1];
	$pos = strpos($aux, 'Grupo ');
	if ($pos !== false){ $aux = substr($aux, $pos+6); }
	$pdf->Cell(49,4.7,utf8_decode($aux),'BLR',4.7,'C',False);

	$aux = $get[2];
	$pos = strpos($aux, 'Grupo ');
	if ($pos !== false){ $aux = substr($aux, $pos+6); }
	$pdf->Cell(49,4.7,utf8_decode($aux),'BLR',4.7,'C',False);

	$aux = $get[3];
	$pos = strpos($aux, 'Grupo ');
	if ($pos !== false){ $aux = substr($aux, $pos+6); }
	$pdf->Cell(49,4.7,utf8_decode($aux),'BLR',4.7,'C',False);

	$aux = $get[4];
	$pos = strpos($aux, 'Grupo ');
	if ($pos !== false){ $aux = substr($aux, $pos+6); }
	$pdf->Cell(49,4.7,utf8_decode($aux),'BLR',0,'C',False);
	$pdf->SetXY($current_x+49, $current_y);

	$current_y = $pdf->GetY();
	$current_x = $pdf->GetX();
	$pdf->Cell(52, 4.7, utf8_decode($totalGrupo1), 'B', 4.7, 'C', False);
	$pdf->Cell(52, 4.7, utf8_decode($totalGrupo2), 'B', 4.7, 'C', False);
	$pdf->Cell(52, 4.7, utf8_decode($totalGrupo3), 'B', 4.7, 'C', False);
	$pdf->Cell(52, 4.7, utf8_decode($totalGrupo4), 'B', 4.7, 'C', False);
	$pdf->Cell(52, 4.7, utf8_decode($totalGrupo5), 'B', 4.7, 'C', False);
	$pdf->SetXY($current_x+52, $current_y);

	$current_y = $pdf->GetY();
	$current_x = $pdf->GetX();
	$pdf->Cell(37, 23.5, '', 'LB', 0, 'L', False);

	// N° DÍAS A ATENDER
	$pdf->SetXY($current_x, $current_y+3.5);
	if($tipoComplemento == 'RPC'){
		if($_POST['imprimirMesIC']){
			$pdf->MultiCell(43.979,2.66,mb_strtoupper($mes),0,'C',False);
		}
	}else{
		$pdf->MultiCell(37, 3, $auxDias, 0, 'C', False);
		$pdf->SetXY($current_x, $current_y+13);
		if(strpos($semana, ',') !== false){
			$aux = "SEMANAS: $semana";
		}else{
			$aux = "SEMANA: $semana";
		}
		$pdf->MultiCell(37, 3, $aux, 0, 'C', False);
	}

	// N° DE MENÚ
	$pdf->SetXY($current_x+37, $current_y);
	$current_y = $pdf->GetY();
	$current_x = $pdf->GetX();
	$pdf->Cell(37,23.5, '', 'LB', 0, 'L', False);
	$pdf->SetXY($current_x, $current_y+3.5);
	if($tipoComplemento == 'RPC'){
		$aux = "N/A";
		$pdf->Cell(30,4.7,$aux,0,0,'C',False);
	}
	else{
		if(strpos($auxCiclos, ',') !== false){ $aux = ""; }else{ $aux = "SEMANA: $auxCiclos"; }
		$pdf->MultiCell(37, 3, $aux, 0, 'C', False);
		$pdf->SetFont('Arial', '', $tamannoFuente);
		$pdf->SetXY($current_x, $current_y+5);
		$pdf->MultiCell(37, 3, 'MENUS: '.$auxMenus, 0, 'C', False);
	}

	$pdf->SetXY($current_x+37, $current_y);
	$current_y = $pdf->GetY();
	$current_x = $pdf->GetX();
	$pdf->Cell(0, 23.5, $totalGrupo1 + $totalGrupo2 + $totalGrupo3 + $totalGrupo4 + $totalGrupo5, 'LRB', 0, 'C', False);
	$pdf->SetXY($current_x+2, $current_y+3.5);
	$pdf->SetXY($current_x, $current_y+23.5);
	$pdf->Ln(0.8);

	$pdf->SetFont('Arial','B',$tamannoFuente);
	$current_y = $pdf->GetY();
	$current_x = $pdf->GetX();
	$pdf->Cell(49, 15, 'ALIMENTO', 'BLT', 0, 'C', True);
	$current_y = $pdf->GetY();
	$current_x = $pdf->GetX();
	$pdf->Cell(65.5, 15, '', 0, 0, 'L', False);

	$pdf->SetXY($current_x, $current_y);
	$pdf->Cell(65.5, 8, '', 'LT' ,0, 'L', True);
	$pdf->SetXY($current_x, $current_y);
	$pdf->Cell(65.5, 4, 'CNT DE ALIMENTOS POR', 0, 4, 'C', False);
	$pdf->Cell(65.5, 4, utf8_decode('NÚMEROS DE RACIONES'), 0, 4, 'C', False);
	$current_y2 = $pdf->GetY();
	$current_x2 = $pdf->GetX();

	$etario_1 = $get[0];
	$etario_2 = $get[1];
	$etario_3 = $get[2];
	$etario_4 = $get[3];
	$etario_5 = $get[4];

	$etario_1 = str_replace("Grupo ", "", $get[0]);
	$etario_2 = str_replace("Grupo ", "", $get[1]);
	$etario_3 = str_replace("Grupo ", "", $get[2]);
	$etario_4 = str_replace("Grupo ", "", $get[3]);
	$etario_5 = str_replace("Grupo ", "", $get[4]);

	$pdf->SetFont('Arial', 'B', $tamannoFuente-0.5);
	$pdf->Cell(13.1, 7, utf8_decode(''), 'TLB', 0, 'C', True);
	$pdf->SetXY($current_x2, $current_y2);
	$pdf->MultiCell(13.1, 3.5, utf8_decode($etario_1), 0, 'C', False);

	$pdf->SetXY($current_x2+13.1, $current_y2);
	$current_y2 = $pdf->GetY();
	$current_x2 = $pdf->GetX();
	$pdf->Cell(13.1, 7, utf8_decode(''), 'BLT', 0, 'C', True);
	$pdf->SetXY($current_x2, $current_y2);
	$pdf->MultiCell(13.1, 3.5, utf8_decode($etario_2), 0, 'C', False);

	$pdf->SetXY($current_x2+13.1, $current_y2);
	$current_y2 = $pdf->GetY();
	$current_x2 = $pdf->GetX();
	$pdf->Cell(13.1, 7, utf8_decode(''), 'BLT', 0, 'C', True);
	$pdf->SetXY($current_x2, $current_y2);
	$pdf->MultiCell(13.1, 3.5, utf8_decode($etario_3), 0, 'C', False);

	$pdf->SetXY($current_x2+13.1, $current_y2);
	$current_y2 = $pdf->GetY();
	$current_x2 = $pdf->GetX();
	$pdf->Cell(13.1, 7, utf8_decode(''), 'BLT', 0, 'C', True);
	$pdf->SetXY($current_x2, $current_y2);
	$pdf->MultiCell(13.1, 3.5, utf8_decode($etario_4), 0, 'C', False);

	$pdf->SetXY($current_x2+13.1, $current_y2);
	$current_y2 = $pdf->GetY();
	$current_x2 = $pdf->GetX();
	$pdf->Cell(13.1,7,utf8_decode(''),'BLT',0,'C',True);
	$pdf->SetXY($current_x2, $current_y2);
	$pdf->MultiCell(13.1,3.5,utf8_decode($etario_5),0,'C',False);

	$pdf->SetFont('Arial','B',$tamannoFuente);
	$pdf->SetXY($current_x+65.5, $current_y);
	$current_y = $pdf->GetY();
	$current_x = $pdf->GetX();
	$pdf->Cell(13.1, 15, '', 'BLT', 0, 'L', True);
	$pdf->SetXY($current_x, $current_y);
	$pdf->Cell(13.1, 5, 'UNIDAD', 0, 5, 'C', False);
	$pdf->Cell(13.1, 5, 'DE', 0, 5, 'C', False);
	$pdf->Cell(13.1, 5, 'MEDIDA', 0, 5, 'C', False);

	$pdf->SetXY($current_x+13.1, $current_y);
	$current_y = $pdf->GetY();
	$current_x = $pdf->GetX();
	$pdf->Cell(13.1, 15, '', 'BLT', 0, 'L', True);
	$pdf->SetXY($current_x, $current_y+2.5);
	$pdf->Cell(13.1, 5, 'CANTIDAD', 0, 5, 'C', False);
	$pdf->Cell(13.1, 5, 'TOTAL', 0, 5, 'C', False);

	$pdf->SetXY($current_x+13.1, $current_y);
	$current_y = $pdf->GetY();
	$current_x = $pdf->GetX();
	$pdf->Cell(31, 15, '', 'BLT', 0, 'L', True);
	$pdf->SetXY($current_x, $current_y);
	$pdf->SetFont('Arial', 'B', $tamannoFuente-0.5);
	$pdf->Cell(31, 8, 'CANTIDAD ORDEN DE COMPRA', 'B', 4, 'C', False);
	$pdf->SetFont('Arial', 'B', $tamannoFuente);
	$pdf->Cell(11, 7, 'TOTAL', 'R', 0, 'C', False);
	$pdf->Cell(10, 7, 'D', 'R', 0, 'C', False);
	$pdf->Cell(10, 7, 'ND', 0, 0, 'C', False);
	$pdf->SetXY($current_x+31, $current_y);

	$pdf->Cell(0,15,'OBSERVACIONES','BLTR',0,'C',True);
	$pdf->SetXY($current_x, $current_y);
	$pdf->Ln(15);
	$pdf->SetFont('Arial','',$tamannoFuente);
}

