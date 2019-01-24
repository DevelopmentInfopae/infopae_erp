<?php
$pdf->SetFont('Arial','B',$tamannoFuente);
$pdf->Cell(30,$tamannoFuente,utf8_decode('DEPARTAMENTO:'),0,0,'L',False);
$pdf->SetFont('Arial','',$tamannoFuente);
$pdf->Cell(60,$tamannoFuente,utf8_decode($departamento),0,0,'L',False);
$pdf->SetFont('Arial','B',$tamannoFuente);
$pdf->Cell(26,$tamannoFuente,utf8_decode('CÓDIGO DANE:'),0,0,'L',False);
$pdf->SetFont('Arial','',$tamannoFuente);
$pdf->Cell(40,$tamannoFuente,utf8_decode($_SESSION['p_CodDepartamento']),0,0,'L',False);

if($codigoSede){
	if(isset($sedes[$codigoSede])){
		$nombre_sede = $sedes[$codigoSede]['nom_sede'];
		$codigo_sede = $codigoSede;
		$nombre_institucion = $sedes[$codigoSede]['nom_inst'];
		$codigo_institucion = $sedes[$codigoSede]['cod_inst'];
	}else{
		$nombre_sede = '';
		$codigo_sede = "";
		$nombre_institucion = '';
		$codigo_institucion = "";
	}
} else {
	$nombre_sede = '';
	$codigo_sede = "";
	$nombre_institucion = '';
	$codigo_institucion = "";
}

$pdf->SetFont('Arial','B',$tamannoFuente);
$pdf->Cell(35,$tamannoFuente,utf8_decode('NOMBRE SEDE:'),0,0,'L',False);
$pdf->SetFont('Arial','',$tamannoFuente);
$pdf->Cell(90,$tamannoFuente,utf8_decode($nombre_sede),0,0,'L',False);
$pdf->SetFont('Arial','B',$tamannoFuente);
$pdf->Cell(20,$tamannoFuente,utf8_decode('CÓDIGO DANE:'),0,0,'L',False);
$pdf->SetFont('Arial','',$tamannoFuente);
$pdf->Cell(0,$tamannoFuente,utf8_decode($codigo_sede),0,0,'L',False);
$pdf->Ln(5);
$pdf->SetFont('Arial','B',$tamannoFuente);
$pdf->Cell(30,$tamannoFuente,utf8_decode('MUNICIPIO:'),0,0,'L',False);
$pdf->SetFont('Arial','',$tamannoFuente);
$pdf->Cell(60,$tamannoFuente,utf8_decode($municipioNm),0,0,'L',False);
$pdf->SetFont('Arial','B',$tamannoFuente);
$pdf->Cell(26,$tamannoFuente,utf8_decode('CÓDIGO DANE:'),0,0,'L',False);
$pdf->SetFont('Arial','',$tamannoFuente);
$pdf->Cell(40,$tamannoFuente,utf8_decode($_POST['municipio']),0,0,'L',False);
$pdf->SetFont('Arial','B',$tamannoFuente);
$pdf->Cell(35,$tamannoFuente,utf8_decode('NOMBRE INSTITUCIÓN:'),0,0,'L',False);
$pdf->SetFont('Arial','',$tamannoFuente);
$pdf->Cell(90,$tamannoFuente,utf8_decode($nombre_institucion),0,0,'L',False);
$pdf->SetFont('Arial','B',$tamannoFuente);
$pdf->Cell(20,$tamannoFuente,utf8_decode('CÓDIGO DANE:'),0,0,'L',False);
$pdf->SetFont('Arial','',$tamannoFuente);
$pdf->Cell(0,$tamannoFuente,utf8_decode($codigo_institucion),0,0,'L',False);
$pdf->Ln(5);
$pdf->SetFont('Arial','B',$tamannoFuente);
$pdf->Cell(30,$tamannoFuente,utf8_decode('OPERADOR:'),0,0,'L',False);
$pdf->SetFont('Arial','',$tamannoFuente);
$pdf->Cell(126,$tamannoFuente,utf8_decode($operador),0,0,'L',False);
$pdf->SetFont('Arial','B',$tamannoFuente);
$pdf->Cell(35,$tamannoFuente,utf8_decode('MES ATENCIÓN:'),0,0,'L',False);
$pdf->SetFont('Arial','',$tamannoFuente);

$mesNm = mesNombre($_POST['mes']);
if($mesAdicional > 0) {
	$mesAdicional = intval($_POST['mes']+1);
	if($mesAdicional > 12) {
		$mesAdicional = 1;
	}
	$mesNm.=' - '.$mesAdicional;
}
$pdf->Cell(30,$tamannoFuente,utf8_decode($mesNm),0,0,'L',False);
$pdf->SetFont('Arial','B',$tamannoFuente);
$pdf->Cell(10,$tamannoFuente,utf8_decode('AÑO:'),0,0,'L',False);
$pdf->SetFont('Arial','',$tamannoFuente);
$pdf->Cell(40,$tamannoFuente,utf8_decode($_SESSION['p_ano']),0,0,'L',False);
$pdf->Ln(5);
$pdf->SetFont('Arial','B',$tamannoFuente);
$pdf->Cell(30,$tamannoFuente,utf8_decode('CONTRATO N°:'),0,0,'L',False);
$pdf->SetFont('Arial','',$tamannoFuente);
$pdf->Cell(40,$tamannoFuente,utf8_decode($_SESSION['p_Contrato']),0,0,'L',False);

if (isset($pagina)){
	$aux = 'Página '.$pagina.' de '.$paginas;
}else{
	$aux = '';
}
$pdf->Cell(0,$tamannoFuente,utf8_decode($aux),0,0,'R',False);

// // Condición que oculta o muestra la sección de información de las raciones.
// if ($tipoPlanilla == 1 || $tipoPlanilla == 2 || $tipoPlanilla == 3 || $tipoPlanilla == 4) {
// 	$pdf->Ln(5);
// 	$pdf->SetFont('Arial','B',$tamannoFuente);
// 	$pdf->Cell(95,$tamannoFuente,utf8_decode('RACIONES PROGRAMADAS COMPLEMENTO ALIMENTARIO:'),0,0,'L',False);
// 	$pdf->SetFont('Arial','',$tamannoFuente);


// 	// Los cálculos de las entregas programadas se realiza con la información
// 	// total de titulares de derecho * la cantidad total de días del mes por sede.

// 	if(isset($totales)){
// 		$aux = $totales['titulares']*$totalDias;
// 	}else{
// 		$aux = '';
// 	}

// 	$pdf->Cell(25,5,$aux,'B',0,'C',False);
// 	$pdf->SetFont('Arial','B',$tamannoFuente);
// 	$pdf->Cell(10);
// 	$pdf->Cell(60,$tamannoFuente,utf8_decode('RACIONES ATENDIDAS:'),0,0,'L',False);
// 	$pdf->Cell(10,$tamannoFuente,utf8_decode('A.M:'),0,0,'L',False);
// 	$pdf->Cell(25,5,'','B',0,'C',False);
// 	$pdf->Cell(10);
// 	$pdf->Cell(10,$tamannoFuente,utf8_decode('P.M:'),0,0,'L',False);
// 	$pdf->Cell(25,5,'','B',0,'C',False);
// 	$pdf->Ln(5);
// 	$pdf->SetFont('Arial','B',$tamannoFuente);
// 	$pdf->Cell(95,$tamannoFuente,utf8_decode('RACIONES PROGRAMADAS ALMUERZO:'),0,0,'L',False);
// 	$pdf->SetFont('Arial','',$tamannoFuente);

// 	if(isset($totales)){
// 		$aux = $totales['titulares']*$totalDias;
// 	}else{
// 		$aux = '';
// 	}

// 	$pdf->Cell(25,5,$aux,'B',0,'C',False);
// 	$pdf->SetFont('Arial','B',$tamannoFuente);
// 	$pdf->Cell(10);
// 	$pdf->Cell(70,$tamannoFuente,utf8_decode('RACIONES ENTREGADAS ALMUERZO: '),0,0,'L',False);
// 	$pdf->SetFont('Arial','',$tamannoFuente);

// 	if(isset($totales)){
// 		$aux = $totales['entregas'];
// 	}else{
// 		$aux = '';
// 	}

// 	$pdf->Cell(25,5,utf8_decode($aux),'B',0,'C',False);
// }

$pdf->Ln(8);
$x = $pdf->GetX();
$y = $pdf->GetY();
$pdf->SetFillColor(245,245,245);
$pdf->Cell(0,14,utf8_decode(''),0,0,'R',True);
$pdf->SetXY($x, $y);
$pdf->Cell(0,14,utf8_decode(''),'T',0,'R',False);
$pdf->SetXY($x, $y);
$pdf->Cell(0,14,utf8_decode(''),'L',0,'R',False);
$pdf->SetXY($x, $y);
$pdf->Cell(0,14,utf8_decode(''),'R',0,'R',False);

$pdf->SetXY($x, $y);
$pdf->SetFont('Arial','B',$tamannoFuente);
$pdf->Cell(8,14,utf8_decode('No'),'R',0,'C',False);

$x = $pdf->GetX();
$y = $pdf->GetY();
$pdf->SetXY($x, $y-1.6);
$pdf->Cell(10,14,utf8_decode('Tipo'),0,0,'C',False);
$pdf->SetXY($x, $y+1.6);
$pdf->Cell(10,14,utf8_decode('Dcto'),0,0,'C',False);
$pdf->SetXY($x, $y);
$pdf->Cell(10,14,utf8_decode(''),'R',0,'C',False);


$x = $pdf->GetX();
$y = $pdf->GetY();
$pdf->SetXY($x, $y-1.6);
$pdf->Cell(20,14,utf8_decode('N° Doc de'),0,0,'C',False);
$pdf->SetXY($x, $y+1.6);
$pdf->Cell(20,14,utf8_decode('Identidad'),0,0,'C',False);
$pdf->SetXY($x, $y);
$pdf->Cell(20,14,utf8_decode(''),'R',0,'C',False);


$x = $pdf->GetX();
$y = $pdf->GetY();
$pdf->SetXY($x, $y-1.6);
if ($tipoPlanilla == 5) { $anchoDatosNombre = 31.7; } else { $anchoDatosNombre = 28; }
$pdf->Cell($anchoDatosNombre,14,utf8_decode('1° Nombre'),0,0,'C',False);
$pdf->SetXY($x, $y+1.6);
$pdf->Cell($anchoDatosNombre,14,utf8_decode('del Titular'),0,0,'C',False);
$pdf->SetXY($x, $y);
$pdf->Cell($anchoDatosNombre,14,utf8_decode(''),'R',0,'C',False);


$x = $pdf->GetX();
$y = $pdf->GetY();
$pdf->SetXY($x, $y-1.6);
$pdf->Cell($anchoDatosNombre,14,utf8_decode('2° Nombre'),0,0,'C',False);
$pdf->SetXY($x, $y+1.6);
$pdf->Cell($anchoDatosNombre,14,utf8_decode('del Titular'),0,0,'C',False);
$pdf->SetXY($x, $y);
$pdf->Cell($anchoDatosNombre,14,utf8_decode(''),'R',0,'C',False);


$x = $pdf->GetX();
$y = $pdf->GetY();
$pdf->SetXY($x, $y-1.6);
$pdf->Cell($anchoDatosNombre,14,utf8_decode('1° Apellido'),0,0,'C',False);
$pdf->SetXY($x, $y+1.6);
$pdf->Cell($anchoDatosNombre,14,utf8_decode('del Titular'),0,0,'C',False);
$pdf->SetXY($x, $y);
$pdf->Cell($anchoDatosNombre,14,utf8_decode(''),'R',0,'C',False);


$x = $pdf->GetX();
$y = $pdf->GetY();
$pdf->SetXY($x, $y-1.6);
$pdf->Cell($anchoDatosNombre,14,utf8_decode('2° Apellido'),0,0,'C',False);
$pdf->SetXY($x, $y+1.6);
$pdf->Cell($anchoDatosNombre,14,utf8_decode('del Titular'),0,0,'C',False);
$pdf->SetXY($x, $y);
$pdf->Cell($anchoDatosNombre,14,utf8_decode(''),'R',0,'C',False);


$x = $pdf->GetX();
$y = $pdf->GetY();
$pdf->SetXY($x, $y);
$pdf->RotatedText($x+4.5,$y+12,utf8_decode("Edad"),90);
$pdf->Cell(10,14,utf8_decode(''),0,0,'C',False);
// $pdf->SetXY($x, $y+1.6);
// $pdf->Cell(14,14,utf8_decode(''),0,0,'C',False);
$pdf->SetXY($x, $y);
$pdf->Cell(7,14,utf8_decode(''),'R',0,'C',False);


// Condición que oculta o muestra las columnas de sexo y grado.
if ($tipoPlanilla == 1 || $tipoPlanilla == 2 || $tipoPlanilla == 3 || $tipoPlanilla == 4) {
	$x = $pdf->GetX();
	$y = $pdf->GetY();
	$pdf->SetXY($x, $y);
	$pdf->RotatedText($x+4.5,$y+12,utf8_decode("P. Étnica"),90);
	$pdf->Cell(7,14,utf8_decode(''),'R',0,'C',False);

	$x = $pdf->GetX();
	$y = $pdf->GetY();
	$pdf->SetXY($x, $y);
	$pdf->RotatedText($x+3.5,$y+10,'Sexo',90);
	$pdf->Cell(5,14,utf8_decode(''),'R',0,'C',False);

	$x = $pdf->GetX();
	$y = $pdf->GetY();
	$pdf->SetXY($x, $y);
	$pdf->RotatedText($x+3,$y+11,'Grado',90);
	$pdf->RotatedText($x+6,$y+13,utf8_decode('Educación'),90);
	$pdf->Cell(7,14,utf8_decode(''),'R',0,'C',False);
}


$x = $pdf->GetX();
$y = $pdf->GetY();
$pdf->SetXY($x, $y-1.6);
$pdf->Cell(13,14,utf8_decode('Tipo'),0,0,'C',False);
$pdf->SetXY($x, $y+1.6);
$pdf->Cell(13,14,utf8_decode('comp'),0,0,'C',False);
$pdf->SetXY($x, $y);
$pdf->Cell(13,14,utf8_decode(''),'R',0,'C',False);


$x = $pdf->GetX();
$y = $pdf->GetY();
$pdf->Cell(0,7,utf8_decode('Fecha de Entrega'),'B',0,'C',False);

$pdf->SetXY($x, $y+7);

if($tipoPlanilla != 1){
	$dia = 0;
	for($i = 0 ; $i < 24 ; $i++){
		$dia++;
		if($dia < 10){
			$auxDia = 'D0'.$dia;
		}
		else{
			$auxDia = 'D'.$dia;
		}
		if(isset($dias[$auxDia])){
	 		$pdf->Cell(6,7,utf8_decode($dias[$auxDia]),'R',0,'C',False);
		}else{
	 		$pdf->Cell(6,7,"",'R',0,'C',False);
		}
	}
}else{
	for($i = 0 ; $i < 25 ; $i++){
	  $pdf->Cell(6,7,"",'R',0,'C',False);
	}
}

$x = $pdf->GetX();
$y = $pdf->GetY();
$xCuadroFilas = $pdf->GetX();
$yCuadroFilas = $pdf->GetY();

$pdf->SetXY($x, $y-1.6);
$pdf->Cell(0,7,utf8_decode('Total'),0,0,'C',False);
$pdf->SetXY($x, $y+1.6);
$pdf->Cell(0,7,utf8_decode('días'),0,0,'C',False);

$pdf->SetXY($x, $y);
$pdf->Ln(7);
