<?php




//var_dump($totalEstudiantes);
// var_dump($sede_unica);
// var_dump($semanas);
// var_dump($tipoComplemento);

$consulta = " SELECT CONCAT(f.ape1, \" \", f.ape2, \" \",f.nom1, \" \", f.nom2) AS nombre, f.num_doc, f.cod_grado FROM focalizacion$semanas[0] f WHERE f.cod_sede = $sede_unica AND f.Tipo_complemento = \"$tipoComplemento\" ORDER BY f.cod_grado ASC, f.ape1  ASC ";

$resultado = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));
$altoFila = 8;
$tamannoFuente = 5;
$pdf->SetFont('Arial','',$tamannoFuente);

if($resultado->num_rows >= 1){
	$totalEstudiantes = $resultado->num_rows;
	$estudianteIndice=1;
	//for ($estudianteIndice=1; $estudianteIndice < 98; $estudianteIndice++) { $row = $resultado->fetch_assoc();
	while($row = $resultado->fetch_assoc()){

		//$despacho['num_doc'] = $row['Num_Doc'];
		$pdf->Cell(4,$altoFila,utf8_decode($estudianteIndice),'BL',0,'C',False);
		
		// Nombre del estudiante
		$pdf->Cell(40,$altoFila,utf8_decode($row['nombre']),'BL',0,'L',False);
		$pdf->Cell(17,$altoFila,utf8_decode($row['num_doc']),'BL',0,'C',False);
		
		$pdf->Cell(17,$altoFila,utf8_decode(""),'BL',0,'C',False);
		
		//var_dump($row['cod_grado']);
		$prescolar = "";
		$primaria = "";
		$basica = "";
		$media = "";

		if( $row['cod_grado'] < 1){
			$prescolar = "X";
		}
		if( $row['cod_grado'] >= 1 && $row['cod_grado'] <= 5 ){
			$primaria = "X";
		}
		if( $row['cod_grado'] >= 6 && $row['cod_grado'] <= 8 ){
			$basica = "X";
		}
		if( $row['cod_grado'] >= 9 ){
			$media = "X";
		}

		$pdf->Cell(3.25,$altoFila,utf8_decode($prescolar),'BL',0,'C',False);
		$pdf->Cell(3.25,$altoFila,utf8_decode($primaria),'BL',0,'C',False);
		$pdf->Cell(3.25,$altoFila,utf8_decode($basica),'BL',0,'C',False);
		$pdf->Cell(3.25,$altoFila,utf8_decode($media),'BL',0,'C',False);


		//var_dump($alimentosTotales);
		foreach ($alimentosTotales as $alimento) {
			$aux = $alimento['grupo1']+$alimento['grupo2']+$alimento['grupo3'];
			$aux = $aux / $totalEstudiantes;
			$pdf->Cell($anchoCeldaAlimento,$altoFila,utf8_decode(number_format($aux, 2, '.', '')),'BL',0,'C',False);
		}
		
		
		$pdf->Cell(48,$altoFila,utf8_decode(""),'BL',0,'C',False);
		$pdf->Cell(28,$altoFila,utf8_decode(""),'BL',0,'C',False);
		// Teléfono del acudiente
		$pdf->Cell(22,$altoFila,utf8_decode(""),'BL',0,'C',False);
		$pdf->Cell(0,$altoFila,utf8_decode(""),'BLR',0,'C',False);
		$pdf->Ln($altoFila);
		
		
		$estudianteIndice++;
		$filaActual++;

		
		//var_dump($filaActual);
		if($filaActual > 15){
			$filaActual = 1;
			$pdf->AddPage();
			// include 'covid19_despacho_consolidado_footer.php';
			include 'covid19_despacho_consolidado_header.php';
		}
	}
}




