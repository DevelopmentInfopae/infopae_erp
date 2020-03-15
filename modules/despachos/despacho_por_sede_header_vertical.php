<?php
$pdf->SetFont('Arial','',$tamannoFuente);
$logoInfopae = $_SESSION['p_Logo ETC'];
$pdf->Image($logoInfopae, 15.6 ,8.6, 64.62, 9.9,'jpg', '');
// Marco
$pdf->SetFont('Arial');
$pdf->SetTextColor(0,0,0);
$pdf->SetLineWidth(.05);
$current_y = $pdf->GetY();
$current_x = $pdf->GetX();
// $pdf->SetDrawColor(255,0,0);
// $pdf->Cell(0,255,'',1,0,'C',False);
$pdf->SetDrawColor(0,0,0);
$pdf->SetXY($current_x, $current_y);

// header
$pdf->Cell(0,9.9,'',1,0,'C',False);
$pdf->SetXY($current_x, $current_y);
$pdf->Cell(92.5,9.9,'','R',0,'C',False);
//$pdf->Cell(0,9.9,utf8_decode('REMISIÓN ENTREGA DE VÍVERES EN COMEDORES ESCOLARES - RACIÓN PREPARADA EN SITIO'),0,0,'C',False);
//$pdf->Cell(0,9.9,utf8_decode("PROGRAMA DE ALIMENTACIÓN ESCOLAR\nREMISIÓN ENTREGA DE VÍVERES EN INSTITUCIÓN EDUCATIVA\nAlmuerzo preparado en sitio población vulnerable\nTodos "),0,0,'C',False);
$pdf->SetXY($current_x+92.5, $current_y+0.5);


$pdf->SetFont('Arial','B',$tamannoFuente);
$pdf->MultiCell(0,2.3,utf8_decode("PROGRAMA DE ALIMENTACIÓN ESCOLAR\nREMISIÓN ENTREGA DE VÍVERES EN INSTITUCIÓN EDUCATIVA\nAlmuerzo preparado en sitio población vulnerable\nTodos"),0,'C',false);
$pdf->SetFont('Arial','',$tamannoFuente);


$pdf->SetXY($current_x, $current_y);
// Linea 2
$pdf->Ln(9.9);
$current_y = $pdf->GetY();
$current_x = $pdf->GetX();
$pdf->Cell(0,5,'','LBR',0,'C',False);
$pdf->SetXY($current_x, $current_y);


$pdf->SetFont('Arial','B',$tamannoFuente);
$pdf->Cell(12,5,utf8_decode('OPERADOR:'),0,0,'L',False);
$pdf->SetFont('Arial','',$tamannoFuente);
$pdf->Cell(132,5,utf8_decode($_SESSION['p_Operador']),'R',0,'L',False);


$pdf->SetFont('Arial','B',$tamannoFuente);
$pdf->Cell(24,5,utf8_decode('FECHA ELABORACIÓN:'),'R',0,'L',False);
$pdf->SetFont('Arial','',$tamannoFuente);
$pdf->Cell(0,5,utf8_decode($fechaDespacho),0,0,'L',False);


// Linea 3
$pdf->Ln(5);
$current_y = $pdf->GetY();
$current_x = $pdf->GetX();
$pdf->Cell(0,5,'','LBR',0,'C',False);
$pdf->SetXY($current_x, $current_y);
$pdf->SetFont('Arial','B',$tamannoFuente);
$pdf->Cell(17,5,utf8_decode('DEPARTAMENTO:'),0,0,'L',False);
$pdf->SetFont('Arial','',$tamannoFuente);
$pdf->Cell(101,5,utf8_decode(strtoupper($departamento)),'R',0,'L',False);
$pdf->SetFont('Arial','B',$tamannoFuente);
$pdf->Cell(12,5,utf8_decode('MUNICIPIO:'),0,0,'L',False);
$pdf->SetFont('Arial','',$tamannoFuente);
$pdf->Cell(0,5,utf8_decode($municipio),0,0,'L',False);


// Linea 4
$pdf->Ln(5);
$current_y = $pdf->GetY();
$current_x = $pdf->GetX();
$pdf->Cell(0,5,'','LR',0,'C',False);
$pdf->SetXY($current_x, $current_y);
$pdf->SetFont('Arial','B',$tamannoFuente);
$pdf->Cell(29,5,utf8_decode('ESTABLECIMIENTO EDUCATIVO:'),0,0,'L',False);
$pdf->SetFont('Arial','',$tamannoFuente);
$pdf->Cell(89,5,utf8_decode(substr($institucion, 0, 54)),'R',0,'L',False);
$pdf->SetFont('Arial','B',$tamannoFuente);
$pdf->Cell(7,5,utf8_decode('SEDE:'),0,0,'L',False);
$pdf->SetFont('Arial','',$tamannoFuente);
$pdf->Cell(0,5,utf8_decode(substr($sede, 0, 54)),0,0,'L',False);


// Linea 5
// $pdf->Ln(5);
// $current_y = $pdf->GetY();
// $current_x = $pdf->GetX();
// $pdf->Cell(0,5,'','LBR',0,'C',False);
// $pdf->SetXY($current_x, $current_y);
// $pdf->Cell(29,5,utf8_decode('DIRECCIÓN:'),'R',0,'L',False);
// $pdf->Cell(75,5,utf8_decode($municipio),'R',0,'C',False);
// $pdf->Cell(17,5,utf8_decode('BARRIO/VEREDA:'),'R',0,'L',False);
// $pdf->Cell(9,5,utf8_decode('NA'),'R',0,'C',False);
// $pdf->Cell(25,5,utf8_decode($municipio),'R',0,'C',False);
// $pdf->Cell(8,5,utf8_decode('TEL'),'R',0,'L',False);
// $pdf->Cell(0,5,utf8_decode('NA'),0,0,'C',False);

// $pdf->Ln(5);
// $pdf->Cell(0,2,'','LBR',0,'C',False);

// Linea 6
$pdf->Ln(5);
$current_y = $pdf->GetY();
$current_x = $pdf->GetX();
//$pdf->Cell(0,5,'','LR',0,'C',False);
$pdf->SetXY($current_x, $current_y);
$pdf->SetFillColor(238,236,225);
$pdf->SetFont('Arial','B',$tamannoFuente);
$pdf->Cell(34,5,utf8_decode('RANGO DE EDAD'),'TLB',0,'C',true);
$pdf->Cell(35,5,utf8_decode('N° RACIONES ADJUDICADAS'),'TLB',0,'C',true);
$current_y = $pdf->GetY();
$current_x = $pdf->GetX();
$pdf->MultiCell(22,2.5,utf8_decode('N° DE RACIONES ATENDIDAS'),'TLB','C',true);
$pdf->SetXY($current_x+22, $current_y);

$pdf->Cell(27,5,utf8_decode('N° DÍAS A ATENDER'),'TLB',0,'C',true);
$pdf->Cell(16,5,utf8_decode('N° MENÚ'),'TLB',0,'C',true);
$current_y = $pdf->GetY();
$current_x = $pdf->GetX();


$pdf->MultiCell(18,2.5,utf8_decode('N° SEMANA DEL CICLO DE MENÚS'),'TBL','C',true);
$pdf->SetXY($current_x+18, $current_y);
//$pdf->SetXY($current_x+50, $current_y);
$current_y = $pdf->GetY();
$current_x = $pdf->GetX();
$pdf->Cell(0,2.5,utf8_decode('TOTAL RACIONES'),'LTR',0,'C',true);
$pdf->SetXY($current_x, $current_y+2.5);
$pdf->Cell(15,2.5,utf8_decode('JM'),'LBT',0,'C',true);
$pdf->Cell(0,2.5,utf8_decode('JT'),1,0,'C',true);
$pdf->SetFont('Arial','',$tamannoFuente);
$pdf->SetFillColor(255,255,255);

// Linea 7
$pdf->Ln(2.5);
$current_y = $pdf->GetY();
$current_x = $pdf->GetX();
$pdf->Cell(0,12.9,'','LBR',0,'C',False);
$pdf->SetXY($current_x, $current_y);
$pdf->Cell(34,12.9,utf8_decode(''),'R',0,'C',False);
$pdf->Cell(35,12.9,utf8_decode(''),'R',0,'C',False);
$pdf->Cell(22,12.9,utf8_decode(''),'R',0,'C',False);

$auxDias = "X ".$cantDias." DIAS ".strtoupper($dias)."\n"."SEMANA: ".$semana;
$aux_y_antes = $pdf->GetY();
$aux_x_antes = $pdf->GetX();
$pdf->Cell(27,12.9,'','R',0,'C',False);


$aux_y_despues = $pdf->GetY();
$aux_x_despues = $pdf->GetX();
$pdf->SetXY($aux_x_antes, $aux_y_antes+3);
$pdf->MultiCell(26,4,utf8_decode($auxDias),0,'C',False);
$pdf->SetXY($aux_x_despues, $aux_y_despues);
$pdf->Cell(16,12.9,utf8_decode($auxMenus),'R',0,'C',False);
$pdf->Cell(18,12.9,utf8_decode($ciclo),'R',0,'C',False);


$jm = '';
$jt = '';

// 2 es la jornada de la mañana
// 3 es la jornada de la tarde

if($jornada == 2){
	$jm = $sedeGrupo1 + $sedeGrupo2 + $sedeGrupo3;
}else if($jornada == 3){
	$jt = $sedeGrupo1 + $sedeGrupo2 + $sedeGrupo3;
}

$pdf->Cell(15,12.9,utf8_decode($jm),'R',0,'C',False);
$pdf->Cell(0,12.9,utf8_decode($jt),0,0,'C',False);

$pdf->SetXY($current_x, $current_y);
$consGrupoEtario = "SELECT * FROM grupo_etario ";
$resGrupoEtario = $Link->query($consGrupoEtario);
if ($resGrupoEtario->num_rows > 0) {
	while ($ge = $resGrupoEtario->fetch_assoc()) {
	$get[] = $ge['DESCRIPCION'];
	}
}
$pdf->Cell(34,4.3,utf8_decode($get[0]),'B',5.7,'C',False);
$pdf->Cell(34,4.3,utf8_decode($get[1]),'B',5.7,'C',False);
$pdf->Cell(34,4.3,utf8_decode($get[2]),0,0,'C',False);

$pdf->SetXY($current_x+34, $current_y);
$pdf->Cell(35,4.3,utf8_decode($sedeGrupo1),'B',5,'C',False);
$pdf->Cell(35,4.3,utf8_decode($sedeGrupo2),'B',5,'C',False);
$pdf->Cell(35,4.3,utf8_decode($sedeGrupo3),0,0,'C',False);

$pdf->SetXY($current_x+34+35, $current_y);
$pdf->Cell(22,4.3,utf8_decode($sedeGrupo1),'B',5,'C',False);
$pdf->Cell(22,4.3,utf8_decode($sedeGrupo2),'B',5,'C',False);
$pdf->Cell(22,4.3,utf8_decode($sedeGrupo3),0,0,'C',False);

// Linea 8
$pdf->Ln(2);
//$pdf->Cell(0,3,'','B',0,'C',False);

// Linea 9
$pdf->Ln(3);
$current_y = $pdf->GetY();
$current_x = $pdf->GetX();
//$pdf->Cell(0,10,'','LBR',0,'C',False);
$pdf->SetXY($current_x, $current_y);
$pdf->Cell(34,10,utf8_decode(''),'R',0,'C',False);
$pdf->Cell(35,10,utf8_decode(''),'R',0,'C',False);
$pdf->Cell(11,10,utf8_decode(''),'R',0,'C',False);
$pdf->Cell(11,10,utf8_decode(''),'R',0,'C',False);
$pdf->Cell(34,10,utf8_decode(''),'R',0,'C',False);
$pdf->Cell(17,10,utf8_decode(''),'R',0,'C',False);
$pdf->Cell(24,10,utf8_decode(''),'R',0,'C',False);
$pdf->Cell(0,10,utf8_decode(''),'R',0,'C',False);


$pdf->SetFillColor(238,236,225);
$pdf->SetXY($current_x, $current_y);
$pdf->Cell(34,10,utf8_decode('ALIMENTO'),'LBT',0,'C',true);
$aux_y_antes = $pdf->GetY();
$aux_x_antes = $pdf->GetX();
$pdf->Cell(35,5,utf8_decode(''),'B',0,'C',False);
$pdf->SetXY($aux_x_antes, $aux_y_antes);
$pdf->MultiCell(35,2.5,utf8_decode('CANTIDAD DE ALIMENTOS POR NÚMERO DE RACIONES'),0,'C',true);

$pdf->SetXY($aux_x_antes, $aux_y_antes+5);
$pdf->MultiCell(11.6,2.5,utf8_decode($get[0]),'R','C',False);
$pdf->SetXY($aux_x_antes+11.6, $aux_y_antes+5);
$pdf->MultiCell(11.8,2.5,utf8_decode($get[1]),'R','C',False);
$pdf->SetXY($aux_x_antes+11.8+11.6, $aux_y_antes+5);
$pdf->MultiCell(11.6,2.5,utf8_decode($get[2]),0,'C',False);

$pdf->SetXY($current_x+34+35, $current_y+2);
$pdf->MultiCell(11,2,utf8_decode('UNIDAD DE MEDIDA'),'R','C',False);
$pdf->SetXY($current_x+34+35+10.5, $current_y+3);
$pdf->MultiCell(12,2,utf8_decode('CANTIDAD TOTAL'),0,'C',False);

$pdf->SetXY($current_x+34+35+11+11, $current_y);
$current_y = $pdf->GetY();
$current_x = $pdf->GetX();
$pdf->Cell(34,5,utf8_decode('CANTIDAD ENTREGADA'),'B',0,'C',False);
$pdf->SetXY($current_x, $current_y+5);
$pdf->Cell(16,5,utf8_decode('TOTAL'),'R',0,'C',False);
$pdf->Cell(9,5,utf8_decode('C'),'R',0,'C',False);
$pdf->Cell(9,5,utf8_decode('NC'),0,0,'C',False);

$pdf->SetXY($current_x+34, $current_y);
$current_y = $pdf->GetY();
$current_x = $pdf->GetX();
$pdf->Cell(17,5,utf8_decode(''),'B',0,'C',False);
$pdf->SetXY($current_x, $current_y);
$pdf->MultiCell(17,2.5,utf8_decode('ESPECIFICACIONES DE CALIDAD'),0,'C',False);
$pdf->SetXY($current_x, $current_y+5);
$pdf->Cell(8.5,5,utf8_decode('C'),'R',0,'C',False);
$pdf->Cell(8.5,5,utf8_decode('NC'),0,0,'C',False);

$pdf->SetXY($current_x+17, $current_y);
$current_y = $pdf->GetY();
$current_x = $pdf->GetX();
$pdf->Cell(24,5,utf8_decode('FALTANTES'),'B',0,'C',False);
$pdf->SetXY($current_x, $current_y+5);
$pdf->Cell(5,5,utf8_decode('SI'),'R',0,'C',False);
$pdf->Cell(5,5,utf8_decode('NO'),'R',0,'C',False);
$pdf->Cell(14,5,utf8_decode('CANTIDAD'),0,0,'C',False);

$pdf->SetXY($current_x+24, $current_y);
$current_y = $pdf->GetY();
$current_x = $pdf->GetX();
$pdf->Cell(0,5,utf8_decode('FALTANTES'),'B',0,'C',False);
$pdf->SetXY($current_x, $current_y+5);
$pdf->Cell(5,5,utf8_decode('SI'),'R',0,'C',False);
$pdf->Cell(5,5,utf8_decode('NO'),'R',0,'C',False);
$pdf->Cell(0,5,utf8_decode('CANTIDAD'),0,0,'C',False);

$pdf->Ln(5);
$pdf->SetFont('Arial','',$tamannoFuente);
$pdf->SetFillColor(255,255,255);