<?php
// El espacio para los productos es de 94mm
$anchoCeldaAlimento = 20;


// $filas = 0;
// $grupoAlimActual = '';

// $grupos_alimentarios = [];
// for ($i=0; $i < count($alimentosTotales) ; $i++){
// 	$alimento = $alimentosTotales[$i];
// 	$grupos_alimentarios[$alimento['grupo_alim']][] = $alimento;
// }
// //var_dump($alimentosTotales);
// //var_dump($grupos_alimentarios);
// $tamannoFuente = 4;
// $pdf->SetFont('Arial','',$tamannoFuente);
// $pdf->SetLineWidth(.05);


// $numeroTotalDeAlimentos = count($alimentosTotales);
// $anchoCeldaAlimento = 94 / $numeroTotalDeAlimentos;
// $pdf->SetXY($current_x, $current_y+3);
// $posicionYGrupo = $pdf->GetY();
// $posicionXGrupo = $pdf->GetX();
// $posicionXAlimento = $current_x;
// $indiceAlimentos = 0;
// $altoCeldaProducto = 15;
// foreach ($grupos_alimentarios as $nombre_grupo => $grupo_alimentario){
// 	$pdf->SetXY($posicionXGrupo, $posicionYGrupo);
// 	$ancho_celda_grupo_alimenticio = count($grupo_alimentario) * $anchoCeldaAlimento;
// 	/* Grupos de productos de a 8.5mm de ancho para cada producto */
// 	$pdf->SetFont('Arial','B',$tamannoFuente);
// 	$pdf->Cell($ancho_celda_grupo_alimenticio,3,utf8_decode($nombre_grupo),'BR',0,'C',False);
// 	$pdf->SetFont('Arial','',$tamannoFuente);
// 	$posicionYGrupo = $pdf->GetY();
// 	$posicionXGrupo = $pdf->GetX();

// 	$pdf->SetXY($posicionXGrupo - $ancho_celda_grupo_alimenticio, $posicionYGrupo+3);
	

// 	$posicionYAlimento = $posicionYGrupo+3;
// 	foreach ($grupo_alimentario as $alimento){
// 		$aux = $alimento['componente'];
// 		/* Productos de a 8.5mm de ancho para cada producto */
// 		$pdf->SetXY($posicionXAlimento, $posicionYAlimento);
// 		$ancholinea = 1;
// 		$ancho = 8.5;
// 		$margen = ($ancho / 2) + 0.5;
// 		$long_nombre=strlen($aux);
// 		if($long_nombre > $largoNombreProducto){
// 			$aux = substr($aux,0,$largoNombreProducto);
// 		}
// 		$pdf->Rotate_text($posicionXAlimento+$margen, $posicionYAlimento+$altoCeldaProducto-1, utf8_decode($aux), 90);
// 		$aux = $alimento['presentacion'];
// 		$pdf->Rotate_text($posicionXAlimento+$margen+2, $posicionYAlimento+$altoCeldaProducto-1, utf8_decode($aux), 90);
// 		$pdf->SetXY($posicionXAlimento, $posicionYAlimento);
// 		if($indiceAlimentos == 0){
// 			$pdf->Cell($anchoCeldaAlimento,$altoCeldaProducto,utf8_decode(''),'B',0,'C',False);
// 		}else{
// 			$pdf->Cell($anchoCeldaAlimento,$altoCeldaProducto,utf8_decode(''),'LB',0,'C',False);
// 		}
		
// 		$posicionXAlimento += $anchoCeldaAlimento;
// 		$indiceAlimentos++;
// 	}
// 	$posicionXAlimento = $posicionXGrupo;
	






//     // $pdf->SetXY($posicion_X_celda_grupo_alimenticio, $posicion_Y_celda_grupo_alimenticio);
//     // $pdf->MultiCell(23.788, 4, utf8_decode($nombre_grupo), 0, "C");
//     // $pdf->SetXY($posicion_X_celda_grupo_alimenticio, $posicion_Y_celda_grupo_alimenticio);
//     // $pdf->MultiCell(23.788, $altura_celda_grupo_alimenticio, "", 1);

//     // //var_dump($filaActual);
	

// }