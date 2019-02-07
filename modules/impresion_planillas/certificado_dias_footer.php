<?php
// Esta caja tiene 264 de ancho y debe quedar centrada
$aux_y = 133;
$aux_x = 1.6;
$y = 133;
$pdf->SetFont('Arial','',$tamannoFuente-1);

$pdf->SetXY($aux_x, $aux_y);
$aux_x = $pdf->GetX();
$aux_y = $pdf->GetY();

// $pdf->Cell(31.4);
// $pdf->Cell(264,8,utf8_decode('klsdjfkjsdkfjklsdñjf'),1,0,'L',true);
$pdf->SetXY($x, $y);
$pdf->SetFont('Arial','B',$tamannoFuente-1);
$pdf->Cell(31.4);
$pdf->Cell(60,8,utf8_decode('DESCRIPCIÓN'),1,0,'C',true);

// Ciclo para dibujar el encabezado de los tipos de complemento.
foreach ($complementos as $complemento) {
	$aux_x = $pdf->GetX();
	$aux_y = $pdf->GetY();
	$pdf->SetFont('Arial','B',$tamannoFuente-1.5);
	$pdf->MultiCell((104/count($complementos)),4,utf8_decode("TOTAL RACIONES\n". $complemento),1,'C',true);
	$pdf->SetXY($aux_x, $aux_y);
	$pdf->Cell((104/count($complementos)),8,utf8_decode(''),'R',0,'C',false);
}

$aux_x = $pdf->GetX();
$aux_y = $pdf->GetY();
$pdf->MultiCell(100,4,utf8_decode("No. DE TITULARES DE\nDERECHO"),1,'C',true);
// $pdf->SetXY($aux_x, $aux_y);
// $pdf->Cell(0,8,utf8_decode(''),'R',0,'C',false);

// $pdf->SetXY($x, $y);
// $pdf->Cell(0,8,utf8_decode(''),'B',0,'L',false);
// $pdf->SetXY($x, $y);
// $pdf->Cell(0,28,utf8_decode(''),1,0,'L',false);

$pdf->SetXY($x, $y);
$pdf->Ln(8);


$totalComplementos1 = $totalComplementos2 = $totalComplementos3 = 0;
$condicionInstitucion = (isset($institucion['cod_inst']) && $institucion['cod_inst'] != "") ? " AND cod_inst = '".$institucion['cod_inst']."'" : "";
for ($i=0; $i < count($prioridades); $i++) {
	$pdf->SetFont('Arial','',$tamannoFuente-1);
	$aux_x = $pdf->GetX();
	$aux_y = $pdf->GetY();
	$pdf->SetXY($aux_x, $aux_y);
	$pdf->Cell(31.4);
	$pdf->Cell(60,4,utf8_decode(strtoupper($prioridades[$i]["descripcion"])),"RLB",0,'L',false);
	$j = 1;
	foreach ($complementos as $complemento) {
		$condicion = "";
		if ($i == 1) {
			$condicion .= " AND " . $prioridades[0]["campo_entregas_res"] . " = " . $prioridades[0]["valor_NA"];
		} else if ($i == 2) {
			$condicion .= " AND " . $prioridades[1]["campo_entregas_res"] . " = " . $prioridades[1]["valor_NA"] . " AND " . $prioridades[0]["campo_entregas_res"] . " = " . $prioridades[0]["valor_NA"];
		}

		$consultaCantidadComplemento = "SELECT IFNULL(SUM((IFNULL(D1,0) + IFNULL(D2,0) + IFNULL(D3,0) + IFNULL(D4,0) + IFNULL(D5,0) + IFNULL(D6,0) + IFNULL(D7,0) + IFNULL(D8,0) + IFNULL(D9,0) + IFNULL(D10,0) + IFNULL(D11,0) + IFNULL(D12,0) + IFNULL(D13,0) + IFNULL(D14,0) + IFNULL(D15,0) + IFNULL(D16,0) + IFNULL(D17,0) + IFNULL(D18,0) + IFNULL(D19,0) + IFNULL(D20,0) + IFNULL(D21,0) + IFNULL(D22,0) + IFNULL(D23,0) + IFNULL(D24,0) + IFNULL(D25,0) + IFNULL(D26,0) + IFNULL(D27,0) + IFNULL(D28,0) + IFNULL(D29,0) + IFNULL(D30,0) + IFNULL(D31,0))),0) AS cantidadComplemento FROM entregas_res_". $mes.$_SESSION['periodoActual'] ." WHERE 1 " . $condicionInstitucion ." AND tipo_complem = '" . $complemento . "' AND ". $prioridades[$i]["campo_entregas_res"] ." != " . $prioridades[$i]["valor_NA"] . $condicion;

		$resultadoCantidadComplemento = $Link->query($consultaCantidadComplemento) or die (mysqli_error($Link));
		if ($resultadoCantidadComplemento->num_rows > 0) {
			while ($registrosCantidadComplemento = $resultadoCantidadComplemento->fetch_assoc()) {
				$cantidadComplemento = $registrosCantidadComplemento["cantidadComplemento"];
			}
		} else {
			$cantidadComplemento = 0;
		}

		if ($j == 1) {
			$totalComplementos1 += $cantidadComplemento;
		} else if ($j == 2) {
			$totalComplementos2 += $cantidadComplemento;
		} else {
			$totalComplementos3 += $cantidadComplemento;
		}

		$pdf->Cell((104/count($complementos)),4,$cantidadComplemento,'LB',0,'C',false);
		$j++;
	}

	$con_can_est_com = "SELECT COUNT(*) cantidad FROM entregas_res_" . $mes.$_SESSION['periodoActual'] . " WHERE 1 " . $condicionInstitucion . " AND " . $prioridades[$i]["campo_entregas_res"] . " != " . $prioridades[$i]["valor_NA"];
	$res_can_est_com = $Link->query($con_can_est_com) or die (mysql_error($Link));
	if ($res_can_est_com->num_rows > 0) {
		while ($reg_can_est_com = $res_can_est_com->fetch_assoc()) {
			$can_est_com = $reg_can_est_com["cantidad"];
		}
	} else {
		$can_est_com = 0;
	}

	$pdf->Cell(100,4,$can_est_com,'LBR',0,'C',false);
	$pdf->Ln(4);
}


// Se dibuja la fila para la POBLACIÓN MAYORITARIA.
$aux_x = $pdf->GetX();
$aux_y = $pdf->GetY();
$pdf->SetXY($aux_x, $aux_y);
$pdf->Cell(31.4);
$pdf->Cell(60,4,utf8_decode('POBLACIÓN MAYORITARIA'),'RLB',0,'L',false);
$columna = 1;
foreach ($complementos as $complemento) {
	$condicion = "";
	foreach ($prioridades as $prioridad) {
		$condicion .= " AND ". $prioridad["campo_entregas_res"] ." = " . $prioridad["valor_NA"];
	}
	$con_can_may = "SELECT IFNULL(SUM((IFNULL(D1,0) + IFNULL(D2,0) + IFNULL(D3,0) + IFNULL(D4,0) + IFNULL(D5,0) + IFNULL(D6,0) + IFNULL(D7,0) + IFNULL(D8,0) + IFNULL(D9,0) + IFNULL(D10,0) + IFNULL(D11,0) + IFNULL(D12,0) + IFNULL(D13,0) + IFNULL(D14,0) + IFNULL(D15,0) + IFNULL(D16,0) + IFNULL(D17,0) + IFNULL(D18,0) + IFNULL(D19,0) + IFNULL(D20,0) + IFNULL(D21,0) + IFNULL(D22,0) + IFNULL(D23,0) + IFNULL(D24,0) + IFNULL(D25,0) + IFNULL(D26,0) + IFNULL(D27,0) + IFNULL(D28,0) + IFNULL(D29,0) + IFNULL(D30,0) + IFNULL(D31,0))),0) AS cantidadComplemento FROM entregas_res_". $mes.$_SESSION['periodoActual'] ." WHERE 1 " . $condicionInstitucion ." AND tipo_complem = '" . $complemento . "'" . $condicion;
	$res_can_may = $Link->query($con_can_may) or die (mysqli_error($Link));
	if ($res_can_may->num_rows > 0) {
		while ($reg_can_may = $res_can_may->fetch_assoc()) {
			$cantidadMayoritaria = $reg_can_may["cantidadComplemento"];
		}
	} else {
		$cantidadMayoritaria = 1;
	}

	if ($columna == 1) {
		$totalComplementos1 += $cantidadMayoritaria;
	} else if ($columna == 2) {
		$totalComplementos2 += $cantidadMayoritaria;
	} else {
		$totalComplementos3 += $cantidadMayoritaria;
	}

	$pdf->Cell((104/count($complementos)),4,$cantidadMayoritaria,'LB',0,'C',false);
	$columna++;
}

$con_can_est_total = "SELECT COUNT(*) cantidad FROM entregas_res_" . $mes.$_SESSION['periodoActual'] . " WHERE 1 " . $condicionInstitucion;
$res_can_est_total = $Link->query($con_can_est_total) or die (mysql_error($Link));
if ($res_can_est_total->num_rows > 0) {
	while ($reg_can_est_total = $res_can_est_total->fetch_assoc()) {
		$can_est_total = $reg_can_est_total["cantidad"];
	}
} else {
	$can_est_total = 0;
}
$pdf->Cell(100,4,$can_est_total,'RLB',0,'C',false);
$pdf->Ln(4);


		$pdf->SetFont('Arial','B',$tamannoFuente-1);
		$aux_x = $pdf->GetX();
		$aux_y = $pdf->GetY();
		$pdf->SetXY($aux_x, $aux_y);
		$pdf->Cell(31.4);
		$pdf->Cell(60,4,utf8_decode('TOTAL'),'RLB',0,'L',false);
		$columna = 1;
		foreach ($complementos as $complemento) {
			if ($columna == 1) {
				$pdf->Cell((104/count($complementos)),4,$totalComplementos1,'LB',0,'C',false);
			} else if ($columna == 2) {
				$pdf->Cell((104/count($complementos)),4,$totalComplementos2,'LB',0,'C',false);
			} else {
				$pdf->Cell((104/count($complementos)),4,$totalComplementos3,'LB',0,'C',false);
			}
			$columna++;
		}
		$pdf->Cell(100,4,utf8_decode(''),'LBR',0,'C',false);
		$pdf->Ln(5);

$x = $pdf->GetX();
$y = $pdf->GetY();
$pdf->Cell(31.4);
$pdf->Cell(264,4,utf8_decode('OBSERVACIONES'),'B',0,'C',true);
$pdf->SetFont('Arial','',$tamannoFuente-1);
$pdf->SetXY($x, $y+4);
$pdf->Cell(31.4);
$pdf->MultiCell(0,4,"",0,'L',false);
$pdf->SetXY($x, $y);
$pdf->Cell(31.4);
$pdf->Cell(264,12,utf8_decode(''),1,0,'C',false);


$pdf->Ln(16);
$pdf->SetFont('Arial','',$tamannoFuente);
$pdf->Cell(31.4);
$pdf->Cell(264,4,utf8_decode('La presente certificación se expide como soporte de pago y con base en el registro diario de Titulares de Derecho, que se diligencia en cada Institución Educativa atendida.'),0,4,'L',false);
$pdf->Cell(31.4);
$pdf->Cell(264,4,utf8_decode(''),0,4,'L',false);


$pdf->Ln(1);
$pdf->SetFont('Arial','B',$tamannoFuente);
$pdf->Cell(31.4);
$pdf->Cell(50,4,utf8_decode('PARA CONSTANCIA SE FIRMA EN:'),0,0,'L',false);
$pdf->Cell(30,4,utf8_decode(''),'B',0,'L',false);
$pdf->Cell(20,4,utf8_decode(' FECHA: DIA'),0,0,'L',false);
$pdf->Cell(35,4,utf8_decode(''),'B',0,'L',false);
$pdf->Cell(15,4,utf8_decode('DEL AÑO'),0,0,'L',false);
$pdf->Cell(15,4,utf8_decode(''),'B',0,'L',false);


$pdf->Ln(8);
$x = $pdf->GetX();
$y = $pdf->GetY();
$pdf->Cell(31.4);
$pdf->Cell(264,4,utf8_decode('FIRMA DEL RECTOR'),0,4,'L',false);
$pdf->Cell(264,4,utf8_decode(''),'B',4,'L',false);
$pdf->Cell(30,4,utf8_decode('NOMBRES Y APELLIDOS DEL RECTOR:'),0,4,'L',false);

$pdf->SetXY($x, $y);
$pdf->Cell(31.4);
$pdf->Cell(264,12,utf8_decode(''),1,0,'C',false);

$pdf->Ln(14);
$x = $pdf->GetX();
$y = $pdf->GetY();
$pdf->SetFont('Arial','',$tamannoFuente-1);
$pdf->Cell(0,4,utf8_decode('Impreso por Software InfoPae'),0,0,'L',false);
$link = 'http://www.infopae.com.co';
$pdf->SetXY($x+45, $y);
$pdf->Write(4,'www.infopae.com.co',$link);
