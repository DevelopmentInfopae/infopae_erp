<?php
$filas = 0;
$grupoAlimActual = '';

$grupos_alimentarios = [];
for ($i=0; $i < count($alimentosTotales) ; $i++){
	$alimento = $alimentosTotales[$i];
	$grupos_alimentarios[$alimento['grupo_alim']][] = $alimento;
}
//var_dump($alimentosTotales);
//var_dump($grupos_alimentarios);
$tamannoFuente = 4;
$pdf->SetFont('Arial','',$tamannoFuente);
$pdf->SetLineWidth(.05);


// El espacio para los productos es de 94mm
$numeroTotalDeAlimentos = count($alimentosTotales);
$anchoCeldaAlimento = 94 / $numeroTotalDeAlimentos;
$pdf->SetXY($current_x, $current_y+3);
$posicionYGrupo = $pdf->GetY();
$posicionXGrupo = $pdf->GetX();
$posicionXAlimento = $current_x;
$indiceAlimentos = 0;
$altoCeldaProducto = 15;
foreach ($grupos_alimentarios as $nombre_grupo => $grupo_alimentario){
	$pdf->SetXY($posicionXGrupo, $posicionYGrupo);
	$ancho_celda_grupo_alimenticio = count($grupo_alimentario) * $anchoCeldaAlimento;
	/* Grupos de productos de a 8.5mm de ancho para cada producto */
	$pdf->Cell($ancho_celda_grupo_alimenticio,3,utf8_decode($nombre_grupo),'BR',0,'C',False);
	$posicionYGrupo = $pdf->GetY();
	$posicionXGrupo = $pdf->GetX();

	$pdf->SetXY($posicionXGrupo - $ancho_celda_grupo_alimenticio, $posicionYGrupo+3);
	

	$posicionYAlimento = $posicionYGrupo+3;
	foreach ($grupo_alimentario as $alimento){
		$aux = $alimento['componente'];
		/* Productos de a 8.5mm de ancho para cada producto */
		$pdf->SetXY($posicionXAlimento, $posicionYAlimento);
		$ancholinea = 1;
		$ancho = 8.5;
		$margen = ($ancho / 2) + 0.5;
		$long_nombre=strlen($aux);
		if($long_nombre > $largoNombreProducto){
			$aux = substr($aux,0,$largoNombreProducto);
		}
		$pdf->Rotate_text($posicionXAlimento+$margen, $posicionYAlimento+$altoCeldaProducto-1, utf8_decode($aux), 90);
		$aux = $alimento['presentacion'];
		$pdf->Rotate_text($posicionXAlimento+$margen+2, $posicionYAlimento+$altoCeldaProducto-1, utf8_decode($aux), 90);
		$pdf->SetXY($posicionXAlimento, $posicionYAlimento);
		if($indiceAlimentos == 0){
			$pdf->Cell($anchoCeldaAlimento,$altoCeldaProducto,utf8_decode(''),'B',0,'C',False);
		}else{
			$pdf->Cell($anchoCeldaAlimento,$altoCeldaProducto,utf8_decode(''),'LB',0,'C',False);
		}
		
		$posicionXAlimento += $anchoCeldaAlimento;
		$indiceAlimentos++;
	}
	$posicionXAlimento = $posicionXGrupo;
	






    // $pdf->SetXY($posicion_X_celda_grupo_alimenticio, $posicion_Y_celda_grupo_alimenticio);
    // $pdf->MultiCell(23.788, 4, utf8_decode($nombre_grupo), 0, "C");
    // $pdf->SetXY($posicion_X_celda_grupo_alimenticio, $posicion_Y_celda_grupo_alimenticio);
    // $pdf->MultiCell(23.788, $altura_celda_grupo_alimenticio, "", 1);

    // //var_dump($filaActual);
	

}
// $pdf->Ln(4); -->





































// $pdf->SetXY($current_x, $current_y+3);
// /* Grupos de productos de a 8.5mm de ancho para cada producto */
// $pdf->Cell(17,3,utf8_decode('LECHE Y PRODUCTOS'),'B',0,'C',False);
// $pdf->Cell(42.5,3,utf8_decode('PROTEICOS'),'BL',0,'C',False);
// $pdf->Cell(17,3,utf8_decode('CEREALES '),'BL',0,'C',False);
// $pdf->Cell(8.5,3,utf8_decode('AZÚCARES'),'BL',0,'C',False);
// $pdf->Cell(8.5,3,utf8_decode('GRASAS'),'BL',0,'C',False);


// /* Productos de a 8.5mm de ancho para cada producto */
// $pdf->SetXY($current_x, $current_y+6);

// $ancholinea = 1;
// $ancho = 8.5;
// $margen = ($ancho / 2) + 0.5;



// $aux = "LECHE EN POLVO x 400 gr";
// $long_nombre=strlen($aux);
// if($long_nombre > $largoNombreProducto){
// 	$aux = substr($aux,0,$largoNombreProducto);
// }
// $pdf->Rotate_text($current_x+$margen, $current_y+14.5, utf8_decode($aux), 90);

// $aux = "LECHE SABORIZADA/AVENA x 1000 ml";
// $long_nombre=strlen($aux);
// if($long_nombre > $largoNombreProducto){
// 	$aux = substr($aux,0,$largoNombreProducto);
// }
// $pdf->Rotate_text($current_x+($ancho*1)+$margen, $current_y+14.5, utf8_decode($aux), 90);

// $aux = "FRIJOL x 500 gr";
// $long_nombre=strlen($aux);
// if($long_nombre > $largoNombreProducto){
// 	$aux = substr($aux,0,$largoNombreProducto);
// }
// $pdf->Rotate_text($current_x+($ancho*2)+$margen, $current_y+14.5, utf8_decode($aux), 90);

// $aux = "LENTEJA x 500 gr";
// $long_nombre=strlen($aux);
// if($long_nombre > $largoNombreProducto){
// 	$aux = substr($aux,0,$largoNombreProducto);
// }
// $pdf->Rotate_text($current_x+($ancho*3)+$margen, $current_y+14.5, utf8_decode($aux), 90);

// $aux = "HUEVO x u";
// $long_nombre=strlen($aux);
// if($long_nombre > $largoNombreProducto){
// 	$aux = substr($aux,0,$largoNombreProducto);
// }
// $pdf->Rotate_text($current_x+($ancho*4)+$margen, $current_y+14.5, utf8_decode($aux), 90);

// $aux = "SARDINAS x 425 gr";
// $long_nombre=strlen($aux);
// if($long_nombre > $largoNombreProducto){
// 	$aux = substr($aux,0,$largoNombreProducto);
// }
// $pdf->Rotate_text($current_x+($ancho*5)+$margen, $current_y+14.5, utf8_decode($aux), 90);

// $aux = "ATUN x 170 gr";
// $long_nombre=strlen($aux);
// if($long_nombre > $largoNombreProducto){
// 	$aux = substr($aux,0,$largoNombreProducto);
// }
// $pdf->Rotate_text($current_x+($ancho*6)+$margen, $current_y+14.5, utf8_decode($aux), 90);

// $aux = "ARROZ x 1 kg";
// $long_nombre=strlen($aux);
// if($long_nombre > $largoNombreProducto){
// 	$aux = substr($aux,0,$largoNombreProducto);
// }
// $pdf->Rotate_text($current_x+($ancho*7)+$margen, $current_y+14.5, utf8_decode($aux), 90);

// $aux = "PASTA x 500 gr";
// $long_nombre=strlen($aux);
// if($long_nombre > $largoNombreProducto){
// 	$aux = substr($aux,0,$largoNombreProducto);
// }
// $pdf->Rotate_text($current_x+($ancho*8)+$margen, $current_y+14.5, utf8_decode($aux), 90);

// $aux = "PANELA x 500 gr";
// $long_nombre=strlen($aux);
// if($long_nombre > $largoNombreProducto){
// 	$aux = substr($aux,0,$largoNombreProducto);
// }
// $pdf->Rotate_text($current_x+($ancho*9)+$margen, $current_y+14.5, utf8_decode($aux), 90);

// $aux = "ACEITE  x 250 ml";
// $long_nombre=strlen($aux);
// if($long_nombre > $largoNombreProducto){
// 	$aux = substr($aux,0,$largoNombreProducto);
// }
// $pdf->Rotate_text($current_x+($ancho*10)+$margen, $current_y+14.5, utf8_decode($aux), 90);



















// $pdf->SetXY($current_x, $current_y+6);
// $pdf->Cell(8.545,9,utf8_decode(''),'B',0,'C',False);
// $pdf->Cell(8.545,9,utf8_decode(''),'BL',0,'C',False);
// $pdf->Cell(8.545,9,utf8_decode(''),'BL',0,'C',False);
// $pdf->Cell(8.545,9,utf8_decode(''),'BL',0,'C',False);
// $pdf->Cell(8.545,9,utf8_decode(''),'BL',0,'C',False);
// $pdf->Cell(8.545,9,utf8_decode(''),'BL',0,'C',False);
// $pdf->Cell(8.545,9,utf8_decode(''),'BL',0,'C',False);
// $pdf->Cell(8.545,9,utf8_decode(''),'BL',0,'C',False);
// $pdf->Cell(8.545,9,utf8_decode(''),'BL',0,'C',False);
// $pdf->Cell(8.545,9,utf8_decode(''),'BL',0,'C',False);
// $pdf->Cell(8.545,9,utf8_decode(''),'BL',0,'C',False);