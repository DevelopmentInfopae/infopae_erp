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
$pdf->SetXY($x, $y);
$pdf->Ln(8);

$totalEstudiantesEntregas = 0; //Variable para sacar el total de estudiantes contados en todas las características.
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

		$consultaCantidadComplemento = "SELECT IFNULL(SUM((". trim($sumaCamposEntregasDias, " + ") .")),0) AS cantidadComplemento FROM entregas_res_". $mes.$_SESSION['periodoActual'] ." WHERE 1 " . $condicionInstitucion ." AND tipo_complem = '" . $complemento . "' AND ". $prioridades[$i]["campo_entregas_res"] ." != " . $prioridades[$i]["valor_NA"] . $condicion;

		$resultadoCantidadComplemento = $Link->query($consultaCantidadComplemento) or die (mysqli_error($Link));
		if ($resultadoCantidadComplemento->num_rows > 0) {
			while ($registrosCantidadComplemento = $resultadoCantidadComplemento->fetch_assoc()) {
				$cantidadComplemento = $registrosCantidadComplemento["cantidadComplemento"];
			}
		} else {
			$cantidadComplemento = 0;
		}

		$pdf->Cell((104/count($complementos)),4,$cantidadComplemento,'LB',0,'C',false);

		if ($j == 1) {
			$totalComplementos1 += $cantidadComplemento;
		} else if ($j == 2) {
			$totalComplementos2 += $cantidadComplemento;
		} else {
			$totalComplementos3 += $cantidadComplemento;
		}

		$j++;
	}

	$con_can_est_com = "SELECT COUNT(DISTINCT num_doc) cantidad FROM entregas_res_" . $mes.$_SESSION['periodoActual'] . " WHERE 1 " . $condicionInstitucion . " AND " . $prioridades[$i]["campo_entregas_res"] . " != " . $prioridades[$i]["valor_NA"] . $condicion ." AND ". trim($sumaCamposEntregasDias, "+ ") ." > 0"; //Se añade condición para no contar los estudiantes que ya se contaron en la prioridad anterior.
	$res_can_est_com = $Link->query($con_can_est_com) or die (mysql_error($Link));
	if ($res_can_est_com->num_rows > 0) {
		while ($reg_can_est_com = $res_can_est_com->fetch_assoc()) {
			$can_est_com = $reg_can_est_com["cantidad"];
		}
	} else {
		$can_est_com = 0;
	}

	$pdf->Cell(100,4,$can_est_com,'LBR',0,'C',false);
	$totalEstudiantesEntregas += $can_est_com;
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
	$con_can_may = "SELECT IFNULL(SUM((". trim($sumaCamposEntregasDias, " + ") .")),0) AS cantidadComplemento FROM entregas_res_". $mes.$_SESSION['periodoActual'] ." WHERE 1 " . $condicionInstitucion ." AND tipo_complem = '" . $complemento . "'" . $condicion;
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

$condicionMayoritaria = " AND " . $prioridades[0]["campo_entregas_res"] . " = " . $prioridades[0]["valor_NA"]." AND " . $prioridades[1]["campo_entregas_res"] . " = " . $prioridades[1]["valor_NA"]." AND " . $prioridades[2]["campo_entregas_res"] . " = " . $prioridades[2]["valor_NA"]." "; //Variable para la condición de búsqueda de estudiantes de población mayoritaria, que no cumplen con ninguna caracterización.

$con_can_est_total = "SELECT COUNT(DISTINCT num_doc) cantidad FROM entregas_res_" . $mes.$_SESSION['periodoActual'] . " WHERE 1 " . $condicionInstitucion. $condicionMayoritaria ." AND ". trim($sumaCamposEntregasDias, "+ ") ." > 0";
$res_can_est_total = $Link->query($con_can_est_total) or die (mysql_error($Link));
if ($res_can_est_total->num_rows > 0) {
	while ($reg_can_est_total = $res_can_est_total->fetch_assoc()) {
		$can_est_total = $reg_can_est_total["cantidad"];
	}
} else {
	$can_est_total = 0;
}
$pdf->Cell(100,4,$can_est_total,'RLB',0,'C',false);
$totalEstudiantesEntregas += $can_est_total;
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
		$pdf->Cell(100,4,utf8_decode($totalEstudiantesEntregas),'LBR',0,'C',false);
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


$pdf->Ln(14);
$pdf->SetFont('Arial','',$tamannoFuente);
$pdf->MultiCell(0,4,utf8_decode('La presente certificación se expide como soporte de pago y con base en el registro diario de Titulares de Derecho, que se diligencia en cada Institución Educativa atendida. Decreto 1852 de 2015 capitulo 4 artículo 2.3.1.4.4, Resolución 29452 / 2017 capítulo 4 numeral 4.1.2 Aplicación de protocolo de Suplencia reportados en documento adicional a las planillas.'),0,'L',false);

$pdf->Ln(1);
$pdf->SetFont('Arial','B',$tamannoFuente);
$pdf->Cell(50,4,utf8_decode('PARA CONSTANCIA SE FIRMA EN:'),0,0,'L',false);
$pdf->Cell(40,4,utf8_decode(''),'B',0,'L',false);
$pdf->Cell(20,4,utf8_decode(' FECHA: DIA'),0,0,'L',false);
$pdf->Cell(40,4,utf8_decode(''),'B',0,'L',false);
$pdf->Cell(15,4,utf8_decode('DEL AÑO'),0,0,'L',false);
$pdf->Cell(15,4,utf8_decode(''),'B',0,'L',false);

$pdf->Ln(8);
$x = $pdf->GetX();
$y = $pdf->GetY();
$pdf->Cell(0,4,utf8_decode('FIRMA DEL RECTOR'),0,4,'L',false);
$pdf->Cell(0,4,utf8_decode(''),'B',4,'L',false);
$pdf->Cell(30,4,utf8_decode('NOMBRES Y APELLIDOS DEL RECTOR:'),0,4,'L',false);

$pdf->SetXY($x, $y);
$pdf->Cell(0, 12, "", 1, 0, 'C', false);

$pdf->Ln(14);
$x = $pdf->GetX();
$y = $pdf->GetY();
$pdf->SetFont('Arial','',$tamannoFuente-1);
/*********************************************************/
// Consulta que retorna los tipo de complementos.
$texto_convenciones = "";
$consulta_tipo_complementos = "SELECT CODIGO AS codigo, DESCRIPCION as descripcion FROM tipo_complemento ORDER BY CODIGO";
$respuesta_tipo_complementos = $Link->query($consulta_tipo_complementos) or die("Error al consultar tipo_complemento: ". $Link->error);
if ($respuesta_tipo_complementos->num_rows > 0) {

	while($registros_tipo_complementos = $respuesta_tipo_complementos->fetch_assoc()) {
		$texto_convenciones .= $registros_tipo_complementos["codigo"]. ": ". $registros_tipo_complementos["descripcion"]. " ";
	}
}
$pdf->MultiCell(0,4,utf8_decode($texto_convenciones),0,'L',false);
/**********************************************************/
$pdf->SetXY($x+260, $y+4);
$pdf->Write(4,utf8_decode('Impreso por Software InfoPae'));

$pdf->SetXY($x+300, $y+4);
$pdf->Write(4,'www.infopae.com.co', 'http://www.infopae.com.co');
