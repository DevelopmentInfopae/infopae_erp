<?php
// manejo formato cuando son cinco grupos etarios a manejar guardados desde la session

//header
$logoInfopae = $_SESSION['p_Logo ETC'];
$pdf->SetFont('Arial');
$pdf->SetTextColor(0, 0, 0);
$pdf->SetLineWidth(.05);
$pdf->Image($logoInfopae, 10, 8, 97, 13, 'jpg', '');
$pdf->Cell(100, 15, '', 1, 0, 'C', False);
$current_y = $pdf->GetY();
$current_x = $pdf->GetX();

$pdf->MultiCell(0, 15, '', 1, 'C', false);
$pdf->SetXY($current_x, $current_y);
$pdf->SetFont('Arial', 'B', $tamannoFuente);

$pdf->Cell(0, 1.5, utf8_decode(''), 0, 1.5, 'C', False);
$pdf->Cell(0, 3, utf8_decode('PROGRAMA DE ALIMENTACIÓN ESCOLAR'), 0, 3, 'C', False);
$pdf->Cell(0, 3, utf8_decode('DOCUMENTO GUÍA DE DESCARGUE DIARIO DE'), 0, 3, 'C', False);
$pdf->Cell(0, 3, utf8_decode('INVENTARIO DE VÍVERES EN INSTITUCIÓN EDUCATIVA'), 0, 3, 'C', False);
$pdf->Cell(0, 3, utf8_decode($descripcionTipo), 0, 4.3, 'C', False);
$pdf->Cell(0, 1.5, utf8_decode(''),0, 1.5, 'C', False);

// primera fila
$pdf->SetXY(8, 21.3);
$current_y = $pdf->GetY();
$current_x = $pdf->GetX();
$pdf->Cell(0, 4.76, '', 1, 0, 'L', False);
$pdf->SetXY($current_x, $current_y);
$pdf->SetFont('Arial','B',$tamannoFuente);
$pdf->Cell(15, 4.76, utf8_decode('OPERADOR:'), 0, 0, 'L', False);
$pdf->SetFont('Arial','',$tamannoFuente);
$pdf->Cell(85, 4.76, utf8_decode( $_SESSION['p_Operador']), 'R', 0, 'L', False);
$pdf->SetFont('Arial','B',$tamannoFuente);
$pdf->Cell(34,4.76,utf8_decode('FECHA DE ELABORACION:'),0,0,'L',False);
$pdf->SetFont('Arial','',$tamannoFuente);
$pdf->Cell(0,4.76,utf8_decode($fechaDespacho),0,0,'L',False);
   
// segunda fila
$current_y = $pdf->GetY();
$current_x = $pdf->GetX();
$pdf->SetXY($current_x, $current_y);
$pdf->Ln(4.76);
$current_y = $pdf->GetY();
$current_x = $pdf->GetX();
$pdf->Cell(100, 4.76, '', 1, 0, 'L', False);
$pdf->SetXY($current_x, $current_y);
$pdf->SetFont('Arial','B',$tamannoFuente);
$pdf->Cell(8,4.76,utf8_decode('ETC:'),0,0,'L',False);
$pdf->SetFont('Arial','',$tamannoFuente);
$pdf->Cell(92,4.76,utf8_decode($_SESSION['p_Nombre ETC']),0,0,'L',False);
$pdf->SetFont('Arial','B',$tamannoFuente);
$pdf->Cell(28, 4.76, utf8_decode('MUNICIPIO O VEREDA:'), 'B', 0, 'L', False);
$pdf->SetFont('Arial','',$tamannoFuente);
$aux = '';
for ($ii=0; $ii < count($municipios) ; $ii++) {
   if($ii > 0){
      $aux = $aux.", ";
   }
   $aux = $aux.$municipios[$ii];
}
$pdf->Cell(0, 4.76, utf8_decode($aux), 'R', 0, 'L', False);

// tercera fila
$pdf->Ln(4.76);
$current_y = $pdf->GetY();
$current_x = $pdf->GetX();
$pdf->Cell(100, 4.76, '', 'B', 0, 'L', False);
$pdf->SetXY($current_x, $current_y);
$pdf->SetFont('Arial','B',$tamannoFuente);
$pdf->Cell(40, 4.76, utf8_decode('INSTITUCIÓN O CENTRO EDUCATIVO:'), 'L', 0, 'L', False);
$pdf->SetFont('Arial','',$tamannoFuente);
if (strlen($nomInstitucion) > 43) {
   $nomInstitucion = substr($nomInstitucion, 0, 43);
   $nomInstitucion .= "...";
}
$pdf->Cell(60,4.76,utf8_decode($nomInstitucion),0,0,'L',False); //$nomInstitucion
$current_y = $pdf->GetY();
$current_x = $pdf->GetX();
$pdf->Cell(0,4.76,'',1,0,'L',False);
$pdf->SetXY($current_x, $current_y);
$pdf->SetFont('Arial','B',$tamannoFuente);
$pdf->Cell(20,4.76,utf8_decode('SEDE EDUCATIVA:'),0,0,'L',False);
$pdf->SetFont('Arial','',$tamannoFuente);
if (strlen($nomSede) > 57) {
   $nomSede = substr($nomSede, 0, 57);
   $nomSede .= "...";
}
$pdf->Cell(82.3,4.76,utf8_decode($nomSede),0,0,'L',False);
$pdf->Ln(4.76);

// cuarta fila
$pdf->Ln(0.8);
$pdf->SetFont('Arial', 'B', $tamannoFuente);
$pdf->Cell(35, 8, 'RANGO DE GRADO', 1, 0, 'C', False);
$current_y = $pdf->GetY();
$current_x = $pdf->GetX();
$pdf->Cell(35, 8, '', 1, 0, 'L', False);
$pdf->SetXY($current_x, $current_y);
$pdf->Cell(35, 8, utf8_decode('N° DE RACIONES ADJUDICADAS'), 0, 4, 'C', False);
$pdf->SetXY($current_x+35, $current_y);

$current_y = $pdf->GetY();
$current_x = $pdf->GetX();
$pdf->Cell(33, 8, '', 1, 0, 'L', False);
$pdf->SetXY($current_x, $current_y);
$pdf->Cell(33, 8, utf8_decode('N° DE RACIONES ATENDIDAS'), 0, 4, 'C', False);
$pdf->SetXY($current_x+33, $current_y);
$pdf->Cell(33,8,utf8_decode('N° DE DÍAS A ATENDER'),1,0,'C',False);
   
$current_y = $pdf->GetY();
$current_x = $pdf->GetX();
$pdf->Cell(37.5, 8, '', 1, 0, 'L', False);
$pdf->SetXY($current_x, $current_y);
$pdf->Cell(37.5, 4, utf8_decode('N° DE MENÚ Y SEMANA DEL'), 0, 0, 'C', False);
$pdf->SetXY($current_x, $current_y+4);
$pdf->Cell(37.5, 4, utf8_decode("CICLO DE MENÚS ENTREGADO"), 0, 0, 'C', False);
$pdf->SetXY($current_x+37.5, $current_y);
$pdf->Cell(0,8,utf8_decode('TOTAL RACIONES'),1,0,'C',False);

// quinta fila
$pdf->Ln(8);
$pdf->SetFont('Arial','',$tamannoFuente);
$current_y = $pdf->GetY();
$current_x = $pdf->GetX();
$pdf->Cell(35, 23.5, '', 1, 0, 'L', False);
$pdf->SetXY($current_x, $current_y);
$pdf->Cell(35, 4.7, utf8_decode($get[0]), 1, 4.7, 'C', False);
$pdf->Cell(35, 4.7, utf8_decode($get[1]), 1, 4.7, 'C', False);
$pdf->Cell(35, 4.7, utf8_decode($get[2]), 1, 4.7, 'C', False);
$pdf->Cell(35, 4.7, utf8_decode($get[3]), 1, 4.7, 'C', False);
$pdf->Cell(35, 4.7, utf8_decode($get[4]), 1, 0, 'C', False);
$pdf->SetXY($current_x+35, $current_y);
$current_y = $pdf->GetY();
$current_x = $pdf->GetX();
$pdf->SetXY($current_x, $current_y);
$pdf->Cell(35, 4.7, utf8_decode($totalGrupo1), 1, 4.7, 'C', False);
$pdf->Cell(35, 4.7, utf8_decode($totalGrupo2), 1, 4.7, 'C', False);
$pdf->Cell(35, 4.7, utf8_decode($totalGrupo3), 1, 4.7, 'C', False);
$pdf->Cell(35, 4.7, utf8_decode($totalGrupo4), 1, 4.7, 'C', False);
$pdf->Cell(35, 4.7, utf8_decode($totalGrupo5), 1, 0, 'C', False);
$pdf->SetXY($current_x+35, $current_y);
$current_y = $pdf->GetY();
$current_x = $pdf->GetX();
$pdf->SetXY($current_x, $current_y);
$pdf->Cell(33, 4.7, utf8_decode($totalGrupo1), 1, 4.7, 'C', False);
$pdf->Cell(33, 4.7, utf8_decode($totalGrupo2), 1, 4.7, 'C', False);
$pdf->Cell(33, 4.7, utf8_decode($totalGrupo3), 1, 4.7, 'C', False);
$pdf->Cell(33, 4.7, utf8_decode($totalGrupo4), 1, 4.7, 'C', False);
$pdf->Cell(33, 4.7, utf8_decode($totalGrupo5), 1, 0, 'C', False);
$pdf->SetXY($current_x+33, $current_y);

//dias a atender
$pdf->SetFillColor(255, 255, 255);
$current_y = $pdf->GetY();
$current_x = $pdf->GetX();
$pdf->Cell(33, 23.5, '', 1, 0, 'L', False);
$pdf->SetXY($current_x, $current_y+8);
$pdf->MultiCell(33, 4, utf8_decode($auxDias), 0, 'C', False);
$pdf->SetXY($current_x+33, $current_y);

//ciclos
$current_y = $pdf->GetY();
$current_x = $pdf->GetX();
$pdf->Cell(37.5, 23.5, '', 1, 0, 'L', False);
$pdf->SetXY($current_x, $current_y+8);
$pdf->Cell(37.5, 4.7, 'SEMANA: '.$auxCiclos, 0, 0, 'C', False);
$pdf->SetFont('Arial', '', $tamannoFuente);
$pdf->SetXY($current_x, $current_y+10);
$pdf->MultiCell(37.5, 4.7, utf8_decode('MENÚS: '.$auxMenus), 'LR', 'C', False);
$pdf->SetXY($current_x+37.5, $current_y);
$current_y = $pdf->GetY();
$current_x = $pdf->GetX();
$pdf->Cell(0, 23.5, '', 1, 0, 'L', False);
$pdf->SetXY($current_x+2, $current_y+4.7);

$jm = '';
$jt = '';
// 2 es la jornada de la mañana
// 3 es la jornada de la tarde
if($jornada == 2){
   $jm = $totalGrupo1 + $totalGrupo2 + $totalGrupo3 + $totalGrupo4 + $totalGrupo5;
}else if($jornada == 3){
   $jt = $totalGrupo1 + $totalGrupo2 + $totalGrupo3 + $totalGrupo4 + $totalGrupo5;
}

if($modalidad == 'APS'){
   $pdf->SetFont('Arial', '', $tamannoFuente+5);
   $pdf->Cell(0, 12, utf8_decode($jt), 0, 0, 'C', False);
   $pdf->SetFont('Arial', '', $tamannoFuente);
}else{
   $pdf->Cell(5, 7, 'JM:', 0, 0, 'L', False);
   $pdf->Cell(15, 7, $jm, 'B', 0, 'C', False);
   $pdf->SetXY($current_x+2, $current_y+11);
   $pdf->Cell(5, 7, 'JT:', 0, 0, 'L', False);
   $pdf->Cell(15, 7, $jt, 'B', 0, 'C', False);
}

$pdf->SetXY($current_x, $current_y+23.5);
$pdf->Ln(0.8);
   
$pdf->SetFont('Arial','B',$tamannoFuente);
$pdf->Cell(45.2, 15, 'ALIMENTO', 1, 0, 'C', False);
$current_y = $pdf->GetY();
$current_x = $pdf->GetX();
$pdf->Cell(13, 15, '', 1, 0, 'L', False);
$pdf->SetXY($current_x, $current_y);
$pdf->Cell(13, 5, 'UNIDAD', 0, 5, 'C', False);
$pdf->Cell(13, 5, 'DE', 0, 5, 'C', False);
$pdf->Cell(13, 5, 'MEDIDA', 0, 5, 'C', False);

$pdf->SetXY($current_x+13, $current_y);
$current_y = $pdf->GetY();
$current_x = $pdf->GetX();
   
$pdf->Cell(15, 15, '', 1, 0, 'L', False);
$pdf->SetXY($current_x, $current_y+2.5);
$pdf->Cell(15, 5, 'CANT', 0, 5, 'C', False);
$pdf->Cell(15, 5, 'ENTREGADA', 0, 5, 'C', False);

$pdf->SetXY($current_x+15, $current_y);
$current_y = $pdf->GetY();
$current_x = $pdf->GetX();
$pdf->Cell(16, 15, '', 1, 0, 'L', False);
$pdf->SetXY($current_x, $current_y);
$pdf->Cell(16, 15, 'EXISTENCIAS', 0, 1, 'C', False);
$pdf->SetXY($current_x+16, $current_y);

$current_y = $pdf->GetY();
$current_x = $pdf->GetX();
$pdf->Cell(18, 15, '', 1, 0, 'L', False);
$pdf->SetXY($current_x, $current_y);
$pdf->Cell(18, 8, utf8_decode('LUNES'), 'B', 8, 'C', False);
$pdf->Cell(18, 7, 'CANT', 'R', 0, 'C', False);

$pdf->SetXY($current_x+18, $current_y);
$current_y = $pdf->GetY();
$current_x = $pdf->GetX();
$pdf->Cell(18, 15, '', 1, 0, 'L', False);
$pdf->SetXY($current_x, $current_y);
$pdf->Cell(18, 8, utf8_decode('MARTES'), 'B', 8, 'C', False);
$pdf->Cell(18, 7, 'CANT', 'R', 0, 'C', False);

$pdf->SetXY($current_x+18, $current_y);
$current_y = $pdf->GetY();
$current_x = $pdf->GetX();
$pdf->Cell(18, 15, '', 1, 0, 'L', False);
$pdf->SetXY($current_x, $current_y);
$pdf->Cell(18, 8, utf8_decode('MIÉRCOLES'), 'B', 8, 'C', False);
$pdf->Cell(18, 7, 'CANT', 'R', 0, 'C', False);

$pdf->SetXY($current_x+18, $current_y);
$current_y = $pdf->GetY();
$current_x = $pdf->GetX();
$pdf->Cell(18, 15, '', 1, 0, 'L', False);
$pdf->SetXY($current_x, $current_y);
$pdf->Cell(18, 8, utf8_decode('JUEVES'), 'B', 8, 'C', False);
$pdf->Cell(18, 7, 'CANT', 'R', 0, 'C', False);
   
$pdf->SetXY($current_x+18, $current_y);
$current_y = $pdf->GetY();
$current_x = $pdf->GetX();
$pdf->Cell(18, 15, '', 1, 0, 'L', False);
$pdf->SetXY($current_x, $current_y);
$pdf->Cell(18, 8, utf8_decode('VIERNES'), 'B', 8, 'C', False);
$pdf->Cell(18, 7, 'CANT', 'R', 0, 'C', False);

$pdf->SetXY($current_x+18, $current_y);
$current_y = $pdf->GetY();
$current_x = $pdf->GetX();
$pdf->Cell(0, 15, '', 1, 0, 'L', False);
$pdf->SetXY($current_x, $current_y);
$pdf->Cell(0, 15, 'SALDO', 0, 15, 'C', False);

$pdf->SetXY($current_x+19.7, $current_y);
$pdf->Ln(15);
$pdf->SetFont('Arial','',$tamannoFuente);


