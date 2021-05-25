<?php 

$altoFila = 8;

$respuestaFocalizacion = $Link->query($consultaFocalizacion) or die ('Error al consultar la focalizacion '. mysqli_error($Link));
if ($respuestaFocalizacion->num_rows > 0) {
	$sedesFalse[] = 'True';
	$sedesBono = 'True';
	$estudianteIndice=1;
	while ($dataFocalizacion = $respuestaFocalizacion->fetch_assoc()) {
		if($filaActual == 1){
			$pdf->AddPage();
			include 'certificado_bono_header.php';
		}
		$tamannoFuente = 5;
		$pdf->SetFont('Arial','',$tamannoFuente);
		$pdf->Cell(4,$altoFila,utf8_decode($estudianteIndice),'BL',0,'C',False);

		// Nombre del estudiante
		$pdf->Cell(42,$altoFila,utf8_decode($dataFocalizacion['nombre']),'BL',0,'L',False);
		$pdf->Cell(17,$altoFila,utf8_decode($dataFocalizacion['num_doc']),'BL',0,'C',False);
		$pdf->Cell(20,$altoFila,utf8_decode(""),'BL',0,'C',False);
		$pdf->Cell(3.25,$altoFila,utf8_decode($dataFocalizacion['cod_grado']),'BL',0,'C',False);
		$pdf->Cell(23, $altoFila, " ", 'BL',0,'C',False);
		$pdf->Cell(23, $altoFila, " ", 'BL',0,'C',False);
		$pdf->Cell(23, $altoFila, " ", 'BL',0,'C',False);
		$pdf->Cell(46,$altoFila,utf8_decode(utf8_decode($dataFocalizacion['nom_acudiente'])),'BL',0,'L',False);
		$pdf->Cell(30,$altoFila,utf8_decode(utf8_decode($dataFocalizacion['doc_acudiente'])),'BL',0,'C',False);
		$pdf->Cell(20,$altoFila,utf8_decode(utf8_decode($dataFocalizacion['parentesco_acudiente'])),'BL',0,'C',False);
		$pdf->Cell(30,$altoFila,utf8_decode(utf8_decode($dataFocalizacion['tel_acudiente'])),'BL',0,'C',False);
		$pdf->Cell(0,$altoFila,utf8_decode(""),'BLR',0,'C',False);
		$pdf->Ln($altoFila);

		$estudianteIndice++;
		$filaActual++;
	
		if($filaActual > 15){
			$filaActual = 1;
		}

	}
}else {
	$sedesFalse[] = 'False';
	$sedesBono = 'False';
}

// if ($sedesFalse != '') {
// 	echo "<script>alert('La sede" .$codigoSede. " No tiene registro de BONO Alimentario');</script>";
// 	exit();

// }
	



