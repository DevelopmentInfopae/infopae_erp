<?php



//var_dump($totalEstudiantes);
// var_dump($sede_unica);
// var_dump($semanas);
// var_dump($tipoComplemento);





//$consulta = " SELECT CONCAT(f.ape1, \" \", f.ape2, \" \",f.nom1, \" \", f.nom2) AS nombre, f.num_doc, f.cod_grado, f.nom_grupo, f.nom_acudiente, f.doc_acudiente, f.tel_acudiente FROM focalizacion$semanas[0] f WHERE f.cod_sede = $sede_unica AND f.Tipo_complemento = \"$tipoComplemento\" ORDER BY f.cod_grado ASC, f.nom_grupo ASC, f.ape1 ASC, f.ape2 ASC, f.nom1 ASC, f.nom2 ASC ";


$consulta = " SELECT CONCAT(f.ape1, \" \", f.ape2, \" \",f.nom1, \" \", f.nom2) AS nombre, f.num_doc, f.cod_grado, f.nom_grupo, f.nom_acudiente, f.doc_acudiente, f.tel_acudiente FROM suplentes$semanas[0] f WHERE f.cod_sede = $sede_unica ORDER BY f.cod_grado ASC, f.nom_grupo ASC, f.ape1 ASC, f.ape2 ASC, f.nom1 ASC, f.nom2 ASC ";




//echo "<br><br>$consulta<br><br>";



$resultado = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));
$altoFila = 8;

if($resultado->num_rows >= 1){
	$totalEstudiantes = $resultado->num_rows;
	$estudianteIndice=1;
	//for ($estudianteIndice=1; $estudianteIndice < 98; $estudianteIndice++) { $row = $resultado->fetch_assoc();
	$auxGradoActual = "";
	$auxGrupoActual = "";
	while($row = $resultado->fetch_assoc()){

		if($auxGradoActual == ""){
			$auxGradoActual = $row['cod_grado'];
		}else if($auxGradoActual != $row['cod_grado']){
			$auxGradoActual = $row['cod_grado'];
			$filaActual = 1;
		}

		// Grupo actual
		if($auxGrupoActual == ""){
			$auxGrupoActual = $row['nom_grupo'];
		}else if($auxGrupoActual != $row['nom_grupo']){
			$auxGrupoActual = $row['nom_grupo'];
			$filaActual = 1;
		}

		if($filaActual == 1){
			$pdf->AddPage();
			include 'covid19_despacho_consolidado_ri_header_adicional.php';
		}

		//$despacho['num_doc'] = $row['Num_Doc'];
		$pdf->Cell(4,$altoFila,utf8_decode($estudianteIndice),'BL',0,'C',False);
		
		// Nombre del estudiante
		$pdf->Cell(42,$altoFila,utf8_decode($row['nombre']),'BL',0,'L',False);
		$pdf->Cell(17,$altoFila,utf8_decode($row['num_doc']),'BL',0,'C',False);
		
		$pdf->Cell(17,$altoFila,utf8_decode(""),'BL',0,'C',False);
		
		//var_dump($row['cod_grado']);
		$prescolar = "";
		$primaria = "";
		$basica = "";
		$media = "";

		if( $row['cod_grado'] < 1){
			$prescolar = $row['cod_grado'];
		}
		if( $row['cod_grado'] >= 1 && $row['cod_grado'] <= 5 ){
			$primaria = $row['cod_grado'];
		}
		if( $row['cod_grado'] >= 6 && $row['cod_grado'] <= 9 ){
			$basica = $row['cod_grado'];
		}
		if( $row['cod_grado'] >= 10 ){
			$media = $row['cod_grado'];
		}

		$pdf->Cell(6.50,$altoFila,utf8_decode($row['cod_grado']),'BL',0,'C',False);
		$pdf->Cell(6.50,$altoFila,utf8_decode($row['nom_grupo']),'BL',0,'C',False);
		// $pdf->Cell(3.25,$altoFila,utf8_decode($basica),'BL',0,'C',False);
		// $pdf->Cell(3.25,$altoFila,utf8_decode($media),'BL',0,'C',False);


        //var_dump($alimentosTotales);
        //var_dump($ciclosSemanas);
        $ciclosSemanasKeys = array_keys($ciclosSemanas);
        $auxTotal = 0;
		foreach ($ciclosSemanasKeys as $ciclosSemanasKey) {
            $aux = $ciclosSemanas[$ciclosSemanasKey];
            $pdf->Cell($anchoCeldaAlimento,$altoFila,utf8_decode($aux),'BL',0,'C',False);
            $auxTotal += $aux;
		}
        $pdf->Cell(18,$altoFila,utf8_decode($auxTotal),'BL',0,'C',False);
		
		
		$pdf->Cell(46,$altoFila,utf8_decode($row['nom_acudiente']),'BL',0,'L',False);
		$pdf->Cell(28,$altoFila,utf8_decode($row['doc_acudiente']),'BL',0,'L',False);
		// Teléfono del acudiente
		$pdf->Cell(22,$altoFila,utf8_decode($row['tel_acudiente']),'BL',0,'L',False);
		$pdf->Cell(0,$altoFila,utf8_decode(""),'BLR',0,'C',False);
		$pdf->Ln($altoFila);
		
		
		$estudianteIndice++;
		$filaActual++;

		
		if($filaActual > 16){
			$filaActual = 1;
		}




	}
}



