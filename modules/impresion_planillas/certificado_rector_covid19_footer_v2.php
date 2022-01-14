<?php
$pdf->Ln(2);
$pdf->SetFont('Arial','',$tamannoFuente-1);
/*********************************************************/
// Consulta que retorna los tipo de complementos.
$texto_convenciones = "";
$consulta_tipo_complementos = "SELECT CODIGO AS codigo, DESCRIPCION as descripcion FROM tipo_complemento ORDER BY CODIGO";
$respuesta_tipo_complementos = $Link->query($consulta_tipo_complementos) or die("Error al consultar tipo_complemento: ". $Link->error);
if ($respuesta_tipo_complementos->num_rows > 0) {
	while($registros_tipo_complementos = $respuesta_tipo_complementos->fetch_assoc()) {
		$pdf->Cell(0,3,utf8_decode($registros_tipo_complementos["codigo"] .": ". $registros_tipo_complementos["descripcion"] .". "),0,4,'L',false);
	}
}
$pdf->Ln(2);
/**********************************************************/

// Tebla tipos de caracterización.
$x = $pdf->GetX();
$y = $pdf->GetY();
$pdf->Cell(0,8,utf8_decode(''),0,0,'L',true);
$pdf->SetXY($x, $y);
$pdf->SetFont('Arial','B',$tamannoFuente-1);
$pdf->Cell(154,8,utf8_decode('DESCRIPCIÓN'),'R',0,'C',false);

/////////////////////////////////////////////////////////////////////////

// Consulta que retorna los complementos que han sido asignados en el transcurso del mes.
$complementos = [];
$resultadoComplementos = $Link->query("SELECT DISTINCT tipo_complem FROM entregas_res_".$mes.$_SESSION['periodoActual']." WHERE tipo_complem IS NOT NULL;") or die (mysqli_error($Link));
if ($resultadoComplementos->num_rows > 0)
{
	while ($registrosComplementos = $resultadoComplementos->fetch_assoc())
	{
		$complementos[] = $registrosComplementos["tipo_complem"];
	}
}
// foreach ($complementos as $complemento)
// {
// 	$aux_x = $pdf->GetX();
// 	$aux_y = $pdf->GetY();
// 	$pdf->SetFont('Arial','B',$tamannoFuente-1.5);
	
	
// 	$pdf->MultiCell((104/count($complementos)),4,utf8_decode("TOTAL, RACIONES\n". $complemento),0,'C',false);
// 	$pdf->SetXY($aux_x, $aux_y);
// 	$pdf->Cell((104/count($complementos)),8,utf8_decode(''),'R',0,'C',false);
// }
/////////////////////////////////////////////////////////////////////////


$aux_x = $pdf->GetX();
$aux_y = $pdf->GetY();
$pdf->MultiCell(0,4,utf8_decode("No. DE TITULARES DE\nDERECHO"),0,'C',false);
$pdf->SetXY($aux_x, $aux_y);
$pdf->Cell(0,8,utf8_decode(''),'R',0,'C',false);

$pdf->SetXY($x, $y);
$pdf->Cell(0,8,utf8_decode(''),'B',0,'L',false);
$pdf->SetXY($x, $y);
$pdf->Cell(0,28,utf8_decode(''),1,0,'L',false);

$pdf->SetXY($x, $y);
$pdf->Ln(8);

// Consulta que retorna el orden de la prioridad del tipo de pocblación.
$prioridades = [];
$resultadoPrioridad = $Link->query("SELECT * FROM prioridad_caracterizacion ORDER BY orden") or die(mysql_error($Link));
if ($resultadoPrioridad->num_rows > 0)
{
	while ($registrosPrioridad = $resultadoPrioridad->fetch_assoc())
	{
		$prioridades[] = $registrosPrioridad;
	}
}

$totalEstudiantesEntregas = 0; //Variable para sacar el total de estudiantes contados en todas las características.
$totalComplementos1 = $totalComplementos2 = $totalComplementos3 = 0;
$condicionInstitucion = (isset($_POST["institucion"]) && $_POST["institucion"] != "") ? " AND cod_inst = '".$_POST["institucion"]."'" : " AND cod_inst = '". $institucion['cod_inst'] ."'";
for ($i=0; $i < count($prioridades); $i++)
{
	$pdf->SetFont('Arial','',$tamannoFuente-1);
	$aux_x = $pdf->GetX();
	$aux_y = $pdf->GetY();
	$pdf->SetXY($aux_x, $aux_y);
	$pdf->Cell(154,4,utf8_decode(strtoupper($prioridades[$i]["descripcion"])),'R',0,'L',false);
	$columna = 1;


	$condicion = "";
	if ($i == 1) {
		$condicion .= " AND " . $prioridades[0]["campo_entregas_res"] . " = " . $prioridades[0]["valor_NA"];
	} else if ($i == 2) {
		$condicion .= " AND " . $prioridades[1]["campo_entregas_res"] . " = " . $prioridades[1]["valor_NA"] . " AND " . $prioridades[0]["campo_entregas_res"] . " = " . $prioridades[0]["valor_NA"];
	}




	// foreach ($complementos as $complemento)
	// {

	// 	$consultaCantidadComplemento = "SELECT IFNULL(SUM((". trim($camposDiasEntregasDias, "+ ") .")),0) AS cantidadComplemento FROM entregas_res_". $mes.$_SESSION['periodoActual'] ." WHERE 1 " . $condicionInstitucion ." AND tipo_complem = '" . $complemento . "' AND ". $prioridades[$i]["campo_entregas_res"] ." != " . $prioridades[$i]["valor_NA"] . $condicion;
	// 	$resultadoCantidadComplemento = $Link->query($consultaCantidadComplemento) or die (mysqli_error($Link));
	// 	if ($resultadoCantidadComplemento->num_rows > 0)
	// 	{
	// 		while ($registrosCantidadComplemento = $resultadoCantidadComplemento->fetch_assoc())
	// 		{
	// 			$cantidadComplemento = $registrosCantidadComplemento["cantidadComplemento"];
	// 		}
	// 	}
	// 	else
	// 	{
	// 		$cantidadComplemento = 0;
	// 	}

	// 	$pdf->Cell((104/count($complementos)),4,$cantidadComplemento,'R',0,'C',false);

	// 	if ($columna == 1)
	// 	{
	// 		$totalComplementos1 += $cantidadComplemento;
	// 	}
	// 	else if ($columna == 2)
	// 	{
	// 		$totalComplementos2 += $cantidadComplemento;
	// 	}
	// 	else
	// 	{
	// 		$totalComplementos3 += $cantidadComplemento;
	// 	}

	// 	$columna++;
	// }

	$con_can_est_com = "SELECT COUNT(*) cantidad FROM entregas_res_" . $mes.$_SESSION['periodoActual'] . " WHERE 1 " . $condicionInstitucion . " AND " . $prioridades[$i]["campo_entregas_res"] . " != " . $prioridades[$i]["valor_NA"] . $condicion ." AND ". trim($camposDiasEntregasDias, "+ ") ." > 0";
	$res_can_est_com = $Link->query($con_can_est_com) or die (mysql_error($Link));
	if ($res_can_est_com->num_rows > 0)
	{
		while ($reg_can_est_com = $res_can_est_com->fetch_assoc())
		{
			$can_est_com = $reg_can_est_com["cantidad"];
			$totalEstudiantesEntregas += $can_est_com;
		}
	}
	else
	{
		$can_est_com = 0;
	}

	$pdf->Cell(0,4,$can_est_com,'R',0,'C',false);
	$pdf->SetXY($aux_x, $aux_y);
	$pdf->Cell(0,4,utf8_decode(''),'B',0,'L',false);
	$pdf->Ln(4);
}



$aux_x = $pdf->GetX();
$aux_y = $pdf->GetY();
$pdf->SetXY($aux_x, $aux_y);
$pdf->Cell(154,4,utf8_decode('POBLACIÓN MAYORITARIA'),'R',0,'L',false);
$columna = 1;
// foreach ($complementos as $complemento){
// 	$condicion = "";
// 	foreach ($prioridades as $prioridad)
// 	{
// 		$condicion .= " AND ". $prioridad["campo_entregas_res"] ." = " . $prioridad["valor_NA"];
// 	}
// 	$con_can_may = "SELECT IFNULL(SUM((". trim($camposDiasEntregasDias, "+ ") .")),0) AS cantidadComplemento FROM entregas_res_". $mes.$_SESSION['periodoActual'] ." WHERE 1 " . $condicionInstitucion ." AND tipo_complem = '" . $complemento . "'" . $condicion;
// 	$res_can_may = $Link->query($con_can_may) or die (mysqli_error($Link));
// 	if ($res_can_may->num_rows > 0) {
// 		while ($reg_can_may = $res_can_may->fetch_assoc())
// 		{
// 			$cantidadMayoritaria = $reg_can_may["cantidadComplemento"];
// 		}
// 	}
// 	else
// 	{
// 		$cantidadMayoritaria = 0;
// 	}

// 	$pdf->Cell((104/count($complementos)),4,$cantidadMayoritaria,'R',0,'C',false);

// 	if ($columna == 1)
// 	{
// 		$totalComplementos1 += $cantidadMayoritaria;
// 	}
// 	else if ($columna == 2)
// 	{
// 		$totalComplementos2 += $cantidadMayoritaria;
// 	}
// 	else
// 	{
// 		$totalComplementos3 += $cantidadMayoritaria;
// 	}

// 	$columna++;
// }

$condicionMayoritaria = " AND " . $prioridades[0]["campo_entregas_res"] . " = " . $prioridades[0]["valor_NA"]." AND " . $prioridades[1]["campo_entregas_res"] . " = " . $prioridades[1]["valor_NA"]." AND " . $prioridades[2]["campo_entregas_res"] . " = " . $prioridades[2]["valor_NA"]." "; //Variable para la condición de búsqueda de estudiantes de población mayoritaria, que no cumplen con ninguna caracterización.

$con_can_est_total = "SELECT COUNT(DISTINCT num_doc) cantidad FROM entregas_res_" . $mes.$_SESSION['periodoActual'] . " WHERE 1 " . $condicionInstitucion.$condicionMayoritaria ." AND ". trim($camposDiasEntregasDias, "+ ") ." > 0";
$res_can_est_total = $Link->query($con_can_est_total) or die (mysql_error($Link));
if ($res_can_est_total->num_rows > 0) {
while ($reg_can_est_total = $res_can_est_total->fetch_assoc()) {
	$can_est_total = $reg_can_est_total["cantidad"];
	$totalEstudiantesEntregas += $can_est_total;
}
} else {
$can_est_total = 0;
}
$pdf->Cell(0,4,$can_est_total,'R',0,'C',false);
$pdf->SetXY($aux_x, $aux_y);
$pdf->Cell(0,4,utf8_decode(''),'B',0,'L',false);
$pdf->Ln(4);

$pdf->SetFont('Arial','B',$tamannoFuente-1);
$aux_x = $pdf->GetX();
$aux_y = $pdf->GetY();
$pdf->SetXY($aux_x, $aux_y);
$pdf->Cell(154,4,utf8_decode('GRAN TOTAL'),'R',0,'L',false);
$columna = 1;
// foreach ($complementos as $complemento) {
// if ($columna == 1) {
// 	$pdf->Cell((104/count($complementos)),4,$totalComplementos1,'R',0,'C',false);
// } else if ($columna == 2) {
// 	$pdf->Cell((104/count($complementos)),4,$totalComplementos2,'R',0,'C',false);
// } else {
// 	$pdf->Cell((104/count($complementos)),4,$totalComplementos3,'R',0,'C',false);
// }
// $columna++;
// }

$pdf->SetFont('Arial','',$tamannoFuente);

$pdf->Cell(0,4,utf8_decode($totalEstudiantesEntregas),'R',0,'C',false);
$pdf->SetXY($aux_x, $aux_y);
$pdf->Cell(0,4,utf8_decode(''),'B',0,'L',false);


$pdf->Ln(8);
$x = $pdf->GetX();
$y = $pdf->GetY();
$pdf->Cell(0,4,utf8_decode('OBSERVACIONES'),'B',0,'C',true);
$pdf->SetFont('Arial','',$tamannoFuente-1);
$pdf->SetXY($x, $y+4);
$pdf->MultiCell(0,4,$observaciones,0,'L',false);
$pdf->SetXY($x, $y);
$pdf->Cell(0,12,utf8_decode(''),1,0,'C',false);


$pdf->Ln(16);
$pdf->SetFont('Arial','',$tamannoFuente);
// $pdf->Cell(0,4,utf8_decode('La presente certificación se expide como soporte de consolidación de entregas en las sedes indicadas de las cuales, se valida con la confirmación de'),0,4,'J',false);
// $pdf->Cell(0,4,utf8_decode('recepción de las personas autorizadas para reclamar el complemento del titular designado a través de la focalización enviada por la ETC, que se'),0,4,'J',false);
// $pdf->Cell(0,4,utf8_decode('diligencia en cada sede educativa atendida. Aplicación de protocolo de suplencia reportados en documento adicional a las planillas y movilidad entre'),0,4,'L',false);
// $pdf->Cell(0,4,utf8_decode('sedes de acuerdo a lo autorizado por el rector o los delegados presentes en la entrega. Decreto 1852 de 2015, Resolución 29452 / 2017 MEN,'),0,4,'L',false);
// $pdf->Cell(0,4,utf8_decode('Resolución 006  007 2020  UAPA.'),0,4,'L',false);

$pdf->MultiCell(0,4, utf8_decode('La presente certificación se expide como soporte de pago y consolidación de  entregas en las sedes indicadas de las cuales, se valida  con la confirmación de recepción de las personas autorizadas para reclamar el complemento del titular designado a través de la focalización enviada por la ETC, que se diligencia en cada sede educativa atentida. Aplicación de protocolo de suplencia reportados en documento adicional a las planillas y movilidad entre sedes de acuerdo a lo autorizado por el rector o los delegados presentes en la entrega. decreto 1852 de 2015, Resolución 29452 de 2017 MEN, Resolucion 006-007 de 2020 UAPA. '), 0, 'J', false);


$pdf->Ln(4);
$pdf->SetFont('Arial','B',$tamannoFuente);
$pdf->Cell(50,4,utf8_decode('PARA CONSTANCIA SE FIRMA EN:'),0,0,'L',false);
$pdf->Cell(30,4,utf8_decode(''),'B',0,'L',false);
$pdf->Cell(20,4,utf8_decode(' FECHA: DIA'),0,0,'L',false);
$pdf->Cell(35,4,utf8_decode(''),'B',0,'L',false);
$pdf->Cell(15,4,utf8_decode('DEL AÑO'),0,0,'L',false);
$pdf->Cell(15,4,utf8_decode(''),'B',0,'L',false);


$pdf->Ln(8);
$x = $pdf->GetX();
$y = $pdf->GetY();
$pdf->SetXY($x, $y);
$pdf->Cell(0,6,utf8_decode('FIRMA DEL RECTOR:'),1,12,'L',false);
$pdf->Cell(0,6,utf8_decode('NOMBRES Y APELLIDOS DEL RECTOR:'),'LRB',0,'L',false);

$pdf->Ln(14);
$x = $pdf->GetX();
$y = $pdf->GetY();
$pdf->SetFont('Arial','',$tamannoFuente-1);
$pdf->Cell(0,4,utf8_decode('Impreso por Software InfoPae'),0,0,'L',false);
$link = 'http://www.infopae.com.co';
$pdf->SetXY($x+45, $y);
$pdf->Write(4,'www.infopae.com.co',$link);