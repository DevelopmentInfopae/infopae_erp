<?php

if ($cantGruposEtarios == 3) {
	//header
	$logoInfopae = $_SESSION['p_Logo ETC'];
	$pdf->SetFont('Arial');
	$pdf->SetTextColor(0,0,0);
	$pdf->SetLineWidth(.05);
	$pdf->Image($logoInfopae, 25 ,7, 65, 10,'jpg', '');
	$pdf->Cell(103,10,'','TLB',0,'C',False);
	$current_y = $pdf->GetY();
	$current_x = $pdf->GetX();
	//$pdf->MultiCell(0,17.29,'',0,'C',false);
	$pdf->Cell(0,10,utf8_decode(''),'LRT',0,'C',False);
	$pdf->SetXY($current_x, $current_y);
	$pdf->SetFont('Arial','B',$tamannoFuente);
	$pdf->Cell(0,2.5,utf8_decode('PROGRAMA DE ALIMENTACIÓN ESCOLAR'),0,2.5,'C',False);

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

	$pdf->Cell(0,2.5,utf8_decode($descripcionTipo),0,2.5,'C',False);
	$pdf->Cell(0,2.5,utf8_decode($tipoDespacho),0,2.5,'C',False);
	$pdf->Ln(0);

	$current_y = $pdf->GetY();
	$current_x = $pdf->GetX();
	$pdf->SetXY($current_x, $current_y);
	$pdf->Cell(15,4.76,utf8_decode('OPERADOR:'),'LB',0,'L',False);
	$pdf->SetFont('Arial','',$tamannoFuente);
	$pdf->Cell(60,4.76,utf8_decode( $_SESSION['p_Operador'] ),'B',0,'L',False);

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
	$pdf->Cell(65.582,8,utf8_decode('N° RACIONES ADJUDICADAS'),'LBT',0,'C',True);
	$pdf->Cell(31.838,8,utf8_decode('N° DÍAS A ATENDER'),'LBT',0,'C',True);

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
	$pdf->Cell(65.582,4.7,utf8_decode($totalGrupo1),'B',4.7,'C',False);
	$pdf->Cell(65.582,4.7,utf8_decode($totalGrupo2),'B',4.7,'C',False);
	$pdf->Cell(65.582,4.7,utf8_decode($totalGrupo3),'B',4.7,'C',False);
	$pdf->SetXY($current_x+65.582, $current_y);

	$current_y = $pdf->GetY();
	$current_x = $pdf->GetX();
	$pdf->Cell(31.838,14.1,'','LB',0,'L',False);

	// N° DÍAS A ATENDER
	$pdf->SetXY($current_x, $current_y+2.35);
	if($tipoComplemento == 'RPC'){
		if($_POST['imprimirMesI']){
			$pdf->MultiCell(31.838,4.7,mb_strtoupper($mes),0,'C',False);
		}
	}else{
		$pdf->MultiCell(31.838,4.7,$auxDias,0,'C',False);
		$pdf->SetXY($current_x, $current_y+9.4);
		if(strpos($semana, ',') !== false){
			$aux = "SEMANAS: $semana";
		}else{
			$aux = "SEMANA: $semana";
		}
		$pdf->MultiCell(31.838,4.7,$aux,0,'C',False);
	}

	// N° DE MENÚ
	$pdf->SetXY($current_x+31.838, $current_y);
	$current_y = $pdf->GetY();
	$current_x = $pdf->GetX();
	$pdf->Cell(30,14.1,'','LB',0,'L',False);
	$pdf->SetXY($current_x, $current_y+2.35);
	if($tipoComplemento == 'RPC'){
		$aux = "N/A";
		$pdf->Cell(30,4.7,$aux,0,0,'C',False);
	}
	else{
		if(strpos($auxCiclos, ',') !== false){ $aux = "SEMANAS: $auxCiclos"; }else{ $aux = "SEMANA: $auxCiclos"; }
		$pdf->Cell(30,4.7,$aux,0,0,'C',False);
		$pdf->SetFont('Arial','',$tamannoFuente);
		$pdf->SetXY($current_x, $current_y+7,05);
		$pdf->Cell(30,4.7,'MENUS: '.$auxMenus,0,0,'C',False);
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
	//$pdf->Cell(31.838,4,'ENTREGADA','B',4,'C',False);
	$pdf->Cell(10.6,7,'TOTAL','R',0,'C',False);
	$pdf->Cell(10.638,7,'D','R',0,'C',False);
	$pdf->Cell(10.6,7,'ND',0,0,'C',False);

	$pdf->SetXY($current_x+31.838, $current_y);
	$pdf->Cell(0,15,'OBSERVACIONES','BLTR',0,'C',True);
	$pdf->SetXY($current_x, $current_y);
	$pdf->Ln(15);
	$pdf->SetFont('Arial','',$tamannoFuente);
}



// manejo encabezado cuando son cinco grupos etarios
if ($cantGruposEtarios == '5') {
		//header
	$logoInfopae = $_SESSION['p_Logo ETC'];
	$pdf->SetFont('Arial');
	$pdf->SetTextColor(0,0,0);
	$pdf->SetLineWidth(.05);
	$pdf->Image($logoInfopae, 25 ,7, 65, 10,'jpg', '');
	$pdf->Cell(103,10,'','TLB',0,'C',False);
	$current_y = $pdf->GetY();
	$current_x = $pdf->GetX();
	//$pdf->MultiCell(0,17.29,'',0,'C',false);
	$pdf->Cell(75,10,utf8_decode(''),'LRT',0,'C',False);
	$pdf->SetXY($current_x, $current_y);
	$pdf->SetFont('Arial','B',$tamannoFuente);
	if($ordenCompraGeneral < 10){
		$AuxordenCompraGeneral = '0'.$ordenCompraGeneral;
	}
	if($ordenCompraGeneral < 100){
		$AuxordenCompraGeneral = '0'.$ordenCompraGeneral;
	}
	if($ordenCompraGeneral < 1000){
		$AuxordenCompraGeneral = '0'.$ordenCompraGeneral;
	}
	if($ordenCompraGeneral < 10000){
		$AuxordenCompraGeneral = '0'.$ordenCompraGeneral;
	}
	$pdf->Cell(75, 3.33, utf8_decode('PROGRAMA DE ALIMENTACIÓN ESCOLAR'), 'R', 0 , 'C', False);
	$pdf->Cell(0, 3.33, utf8_decode('Nº ').utf8_decode($AuxordenCompraGeneral),'TR',1,'C',False);
	


	$pdf->SetXY($current_x, $current_y+3.33);
	$pdf->Cell(75, 3.33, utf8_decode('ORDEN DE COMPRA A PROVEEDORES'),0, 0, 'C',False);
	$pdf->Cell(0, 3.33, utf8_decode('TIPO ALIMENTO'),'TR',1,'C',False);

	$pdf->SetXY($current_x, $current_y+6.66);
	$auxDescripcionTipo = substr($descripcionTipo, 0, 70);
	$pdf->Cell(75, 3.33, utf8_decode($auxDescripcionTipo),0, 0, 'C', False);
	$pdf->Cell(0, 3.33, utf8_decode($tipoDespacho),'R',1,'C',False);

	$pdf->Ln(0);

	$current_y = $pdf->GetY();
	$current_x = $pdf->GetX();
	$pdf->SetXY($current_x, $current_y);
	$pdf->Cell(15,4.76,utf8_decode('OPERADOR:'),'LB',0,'L',False);
	$pdf->SetFont('Arial','',$tamannoFuente);
	$pdf->Cell(60,4.76,utf8_decode( substr($_SESSION['p_Operador'],0,45 ) ),'B',0,'L',False);

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
	$pdf->Cell(95,4.76,utf8_decode(mb_strtoupper($_SESSION['p_Nombre ETC'])),0,0,'L',False);

	$current_y = $pdf->GetY();
	$current_x = $pdf->GetX();
	$pdf->Cell(0,4.76,'','R',0,'L',False);
	$pdf->SetXY($current_x, $current_y);

	$pdf->SetFont('Arial','B',$tamannoFuente);
	$pdf->Cell(19,4.76,utf8_decode('RUTA/MUNICIPIO:'),0,0,'L',False);
	$pdf->SetFont('Arial','',$tamannoFuente);
	$pdf->Cell(0,4.76,utf8_decode(mb_strtoupper ($rutaMunicipio)),0,1,'L',False);

	$auxIntitucion = '';
	$auxSede = '';
	$consultaInstitucion = " SELECT nom_inst
								FROM sedes$periodoActual s 
								INNER JOIN orden_compra_enc$mesAnno o ON s.cod_sede = o.cod_sede
								WHERE o.Num_OCO = '$ordenCompraGeneral' ";
	$respuestaInstitucion = $Link->query($consultaInstitucion) or die ('Err ln 361');							
	if ($respuestaInstitucion->num_rows == 1) {
		$dataInstitucion = $respuestaInstitucion->fetch_assoc();
		$auxIntitucion = $dataInstitucion['nom_inst'];
		$consultaSedesAux = " SELECT DISTINCT(nom_sede)
								FROM sedes$periodoActual s 
								INNER JOIN orden_compra_enc$mesAnno o ON s.cod_sede = o.cod_sede
								WHERE o.Num_OCO = '$ordenCompraGeneral' ";
		$respuestaSedesAux = $Link->query($consultaSedesAux) or die ('Error ln 370');
		if ($respuestaSedesAux->num_rows == 1) {
			$dataSedesAux = $respuestaSedesAux->fetch_assoc();
			$auxSede = $dataSedesAux['nom_sede'];
		}else{
			$auxSede = 'Múltiples';
		}						
	}else{
		$auxIntitucion = 'Múltiples';
		$auxSede = 'Múltiples';	
	}

	$pdf->SetFont('Arial','B',$tamannoFuente);
	$pdf->Cell(19,4.76,utf8_decode('INSTITUCIÓN:'),'TBL',0,'L',False);
	$pdf->SetFont('Arial','',$tamannoFuente);
	$pdf->Cell(84, 4.76, utf8_decode(mb_strtoupper ($auxIntitucion)),'RTB',0,'L',False);

	$pdf->SetFont('Arial','B',$tamannoFuente);
	$pdf->Cell(10, 4.76, utf8_decode('SEDE:'),'TBL',0,'L',False);
	$pdf->SetFont('Arial','',$tamannoFuente);
	$pdf->Cell(0, 4.76, utf8_decode(mb_strtoupper ($auxSede)),'RTB',0,'L',False);


	$pdf->Ln(4.76);
	$pdf->SetFont('Arial','B',$tamannoFuente);
	$pdf->Cell(49,8,'RANGO DE EDAD','LBT',0,'C',True);

	$current_y = $pdf->GetY();
	$current_x = $pdf->GetX();
	$pdf->Cell(52,8,utf8_decode('N° RACIONES ADJUDICADAS'),'LBT',0,'C',True);
	$pdf->Cell(37,8,utf8_decode('N° DÍAS A ATENDER'),'LBT',0,'C',True);

	$current_y = $pdf->GetY();
	$current_x = $pdf->GetX();
	$pdf->Cell(37,8,utf8_decode('N° DE MENÚ'),'LBT',0,'C',True);

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
	$pdf->Cell(52,4.7,utf8_decode($totalGrupo1),'B',4.7,'C',False);
	$pdf->Cell(52,4.7,utf8_decode($totalGrupo2),'B',4.7,'C',False);
	$pdf->Cell(52,4.7,utf8_decode($totalGrupo3),'B',4.7,'C',False);
	$pdf->Cell(52,4.7,utf8_decode($totalGrupo4),'B',4.7,'C',False);
	$pdf->Cell(52,4.7,utf8_decode($totalGrupo5),'B',4.7,'C',False);
	$pdf->SetXY($current_x+52, $current_y);

	$current_y = $pdf->GetY();
	$current_x = $pdf->GetX();
	$pdf->Cell(37,23.5,'','LB',0,'L',False);

	// N° DÍAS A ATENDER
	$pdf->SetXY($current_x, $current_y+3);
	if($tipoComplemento == 'RPC'){
		if($_POST['imprimirMesI']){
			$pdf->MultiCell(37,4.7,mb_strtoupper($mes),0,'C',False);
		}
	}else{
		$pdf->MultiCell(37, 3.5, mb_strtoupper($dias), 0, 'C', False);
		if(strpos($semana, ',') !== false){
			$aux = "SEMANAS: $semana";
		}else{
			$aux = "SEMANA: $semana";
		}
		$pdf->SetXY($current_x, $current_y+13);
		$pdf->MultiCell(37, 3.5, $aux,0,'C',False);
	}
	// N° DE MENÚ
	$pdf->SetXY($current_x+37, $current_y);
	$current_y = $pdf->GetY();
	$current_x = $pdf->GetX();
	$pdf->Cell(37,23.5,'','LB',0,'L',False);
	$pdf->SetXY($current_x, $current_y+3);
	if($tipoComplemento == 'RPC'){
		$aux = "N/A";
		$pdf->Cell(30,4.7,$aux,0,0,'C',False);
	}
	else{
		if(strpos($auxCiclos, ',') !== false){ $aux = "SEMANAS: $auxCiclos"; }else{ $aux = "SEMANA: $auxCiclos"; }
		$pdf->MultiCell(37, 3.5, $aux, 0, 'C', False);
		$pdf->SetFont('Arial','',$tamannoFuente);
		$pdf->SetXY($current_x, $current_y+6.5);
		$pdf->MultiCell(37, 3.5, 'MENUS: '.$auxMenus, 0, 'C', False);
	}

	$pdf->SetXY($current_x+37, $current_y);
	$current_y = $pdf->GetY();
	$current_x = $pdf->GetX();
	$pdf->Cell(0, 23.5, $totalGrupo1 + $totalGrupo2 + $totalGrupo3 + $totalGrupo4 + $totalGrupo5, 'LRB', 0, 'C', False);
	$pdf->SetXY($current_x+2, $current_y+2.35);
	$pdf->SetXY($current_x, $current_y+23.5);

	$pdf->Ln(0.8);

	$pdf->SetFont('Arial','B',$tamannoFuente);
	$current_y = $pdf->GetY();
	$current_x = $pdf->GetX();
	$pdf->Cell(49,15,'ALIMENTO','BLT',0,'C',True);
		
	$current_y = $pdf->GetY();
	$current_x = $pdf->GetX();
	$pdf->Cell(65.5, 15, '', 0, 0, 'L', False);
	$pdf->SetXY($current_x, $current_y);
	$pdf->Cell(65.5, 8, '', 'LT', 0, 'L', True);
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

	$etario_1 = substr($etario_1,0, 15);
	$etario_2 = substr($etario_2,0, 15);
	$etario_3 = substr($etario_3,0, 15);
	$etario_4 = substr($etario_4,0, 15);
	$etario_5 = substr($etario_5,0, 15);

	// $etario_2 = str_replace("Grupo ", "", $get[1]);
	// $etario_3 = str_replace("Grupo ", "", $get[2]);
	// $etario_4 = str_replace("Grupo ", "", $get[3]);
	// $etario_5 = str_replace("Grupo ", "", $get[4]);

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

	$pdf->SetXY($current_x2+13.1, $current_y2);
	$current_y2 = $pdf->GetY();
	$current_x2 = $pdf->GetX();
	$pdf->Cell(13.1,7,utf8_decode(''),'BLT',0,'C',True);
	$pdf->SetXY($current_x2, $current_y2);
	$pdf->MultiCell(13.1,3.5,utf8_decode($etario_4),0,'C',False);

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