<?php
//header
  $logoInfopae = $_SESSION['p_Logo ETC'];
  $pdf->SetFont('Arial');
  $pdf->SetTextColor(0,0,0);
  $pdf->SetLineWidth(.05);
  $pdf->Image($logoInfopae, 11.5 ,10, 111.84, 15.92,'jpg', '');
  $pdf->Cell(116.95,21.6,'',1,0,'C',False);
  $current_y = $pdf->GetY();
  $current_x = $pdf->GetX();
  $pdf->MultiCell(0,21.6,'',1,'C',false);


  $pdf->SetXY($current_x, $current_y);
  $pdf->SetFont('Arial','B',$tamannoFuente);



  // $pdf->Cell(0,4.3,utf8_decode('PROGRAMA DE ALIMENTACIÓN ESCOLAR'),0,4.3,'C',False);
  // $pdf->Cell(0,4.3,utf8_decode('KARDEX DE VÍVERES EN INSTITUCIÓN EDUCATIVA'),0,4.3,'C',False);
  // $pdf->Cell(0,4.3,utf8_decode($descripcionTipo),0,4.3,'C',False);
  // //$pdf->Cell(0,4.3,utf8_decode($tipoDespachoNm),0,4.3,'C',False);
  // $pdf->Cell(0,4.3,'',0,4.3,'C',False);
  // $pdf->Ln(0.09);
//
//   $current_y = $pdf->GetY();
//   $current_x = $pdf->GetX();
//   $pdf->Cell(0,4.76,'',1,0,'L',False);
//   $pdf->SetXY($current_x, $current_y);
//   $pdf->Cell(20,4.76,utf8_decode('OPERADOR:'),0,0,'L',False);
//   $pdf->SetFont('Arial','',$tamannoFuente);
//   $pdf->Cell(151.8,4.76,utf8_decode( $_SESSION['p_Operador']),0,0,'L',False);
//
//   $pdf->SetFont('Arial','B',$tamannoFuente);
//   $pdf->Cell(34,4.76,utf8_decode('FECHA DE ELABORACIÓN:'),'L',0,'L',False);
//   $pdf->SetFont('Arial','',$tamannoFuente);
//   $pdf->Cell(0,4.76,utf8_decode($fechaDespacho),0,0,'L',False);
//
//   $current_y = $pdf->GetY();
//   $current_x = $pdf->GetX();
//
//   $pdf->SetXY(290,15);
//   //$pdf->Cell(0,10,utf8_decode('Página ').$pdf->PageNo().' de {nb}',0,0,'C');
//   $pdf->SetXY($current_x, $current_y);
//
//
//
//
//
//
//
//
//   $pdf->Ln(4.76);
//   $current_y = $pdf->GetY();
//   $current_x = $pdf->GetX();
//   $pdf->Cell(117,4.76,'',1,0,'L',False);
//   $pdf->SetXY($current_x, $current_y);
//   $pdf->SetFont('Arial','B',$tamannoFuente);
//   $pdf->Cell(8,4.76,utf8_decode('ETC:'),0,0,'L',False);
//   $pdf->SetFont('Arial','',$tamannoFuente);
//   $pdf->Cell(109,4.76,utf8_decode($_SESSION['p_Nombre ETC']),0,0,'L',False);
//
//   $current_y = $pdf->GetY();
//   $current_x = $pdf->GetX();
//   $pdf->Cell(0,4.76,'',1,0,'L',False);
//   $pdf->SetXY($current_x, $current_y);
//
//   if($ruta == '' || $ruta == 'Todos'){
//     $pdf->SetFont('Arial','B',$tamannoFuente);
//     $pdf->Cell(28,4.76,utf8_decode('MUNICIPIO O VEREDA:'),0,0,'L',False);
//     $pdf->SetFont('Arial','',$tamannoFuente);
//
//     $aux = '';
//     for ($ii=0; $ii < count($municipios) ; $ii++) {
//       if($ii > 0){
//         $aux = $aux.", ";
//       }
//       $aux = $aux.$municipios[$ii];
//     }
//     $pdf->Cell(53.4,4.76,utf8_decode($aux),0,0,'L',False);
//
//   }else{
//     $pdf->SetFont('Arial','B',$tamannoFuente);
//     $pdf->Cell(8,4.76,utf8_decode('RUTA:'),0,0,'L',False);
//     $pdf->SetFont('Arial','',$tamannoFuente);
//     $pdf->Cell(52,4.76,$ruta,0,0,'L',False);
//   }
//
//
//
//
// // Nombre de institución y de sede 2/03/2018
//
// $pdf->Ln(4.76);
//   $current_y = $pdf->GetY();
//   $current_x = $pdf->GetX();
//   $pdf->Cell(150,4.76,'',1,0,'L',False);
//   $pdf->SetXY($current_x, $current_y);
//   $pdf->SetFont('Arial','B',$tamannoFuente);
//   $pdf->Cell(50,4.76,utf8_decode('INSTITUCIÓN O CENTRO EDUCATIVO:'),0,0,'L',False);
//   $pdf->SetFont('Arial','',$tamannoFuente);
//   $pdf->Cell(100,4.76,utf8_decode($nomInstitucion),0,0,'L',False);
//
//   $current_y = $pdf->GetY();
//   $current_x = $pdf->GetX();
//   $pdf->Cell(0,4.76,'',1,0,'L',False);
//   $pdf->SetXY($current_x, $current_y);
//
//   $pdf->SetFont('Arial','B',$tamannoFuente);
//   $pdf->Cell(28,4.76,utf8_decode('SEDE EDUCATIVA:'),0,0,'L',False);
//   $pdf->SetFont('Arial','',$tamannoFuente);
//   $pdf->Cell(53.4,4.76,utf8_decode($nomSede ),0,0,'L',False);

// Termina nombre de institución y de sede 2/03/2018
$pdf->Cell(0,4.3,utf8_decode('PROGRAMA DE ALIMENTACIÓN ESCOLAR'),0,4.3,'C',False);
$pdf->Cell(0,4.3,utf8_decode('DOCUMENTO GUÍA DE DESCARGUE DIARIO DE'),0,4.3,'C',False);
$pdf->Cell(0,4.3,utf8_decode('INVENTARIO DE VÍVERES EN INSTITUCIÓN EDUCATIVA'),0,4.3,'C',False);
$pdf->Cell(0,4.3,utf8_decode($descripcionTipo),0,4.3,'C',False);
//$pdf->Cell(0,4.3,utf8_decode($tipoDespachoNm),0,4.3,'C',False);

// $pdf->Ln(30);
//Margen izquierdo de 8mm
$pdf->SetXY(8,27.9);
$current_y = $pdf->GetY();
$current_x = $pdf->GetX();
$pdf->Cell(0,4.76,'',1,0,'L',False);
$pdf->SetXY($current_x, $current_y);

$pdf->SetFont('Arial','B',$tamannoFuente);
$pdf->Cell(20,4.76,utf8_decode('OPERADOR:'),0,0,'L',False);
$pdf->SetFont('Arial','',$tamannoFuente);
$pdf->Cell(90,4.76,utf8_decode( $_SESSION['p_Operador']),'R',0,'L',False);


//$pdf->Cell(65,4.76,utf8_decode($municipio),'R',0,'L',False);
  if($ruta == '' || $ruta == 'Todos'){
    $pdf->SetFont('Arial','B',$tamannoFuente);
    $pdf->Cell(28,4.76,utf8_decode('MUNICIPIO O VEREDA:'),0,0,'L',False);
    $pdf->SetFont('Arial','',$tamannoFuente);

    $aux = '';
    for ($ii=0; $ii < count($municipios) ; $ii++) {
      if($ii > 0){
        $aux = $aux.", ";
      }
      $aux = $aux.$municipios[$ii];
    }
    $pdf->Cell(53.4,4.76,utf8_decode($aux),0,0,'L',False);

  }else{
    $pdf->SetFont('Arial','B',$tamannoFuente);
    $pdf->Cell(8,4.76,utf8_decode('RUTA:'),0,0,'L',False);
    $pdf->SetFont('Arial','',$tamannoFuente);
    $pdf->Cell(52,4.76,$ruta,0,0,'L',False);
  }


$pdf->SetFont('Arial','B',$tamannoFuente);
$pdf->Cell(34,4.76,utf8_decode('FECHA DE ELABORACION:'),0,0,'L',False);
$pdf->SetFont('Arial','',$tamannoFuente);
$pdf->Cell(0,4.76,utf8_decode($fechaDespacho),0,0,'L',False);

$current_y = $pdf->GetY();
$current_x = $pdf->GetX();

$pdf->SetXY(290,15);
//$pdf->Cell(0,10,utf8_decode('Página ').$pdf->PageNo().' de {nb}',0,0,'C');
$pdf->SetXY($current_x, $current_y);

$pdf->Ln(4.76);


$current_y = $pdf->GetY();
$current_x = $pdf->GetX();
$pdf->Cell(53,4.76,'',1,0,'L',False);
$pdf->SetXY($current_x, $current_y);
$pdf->SetFont('Arial','B',$tamannoFuente);
$pdf->Cell(8,4.76,utf8_decode('ETC:'),0,0,'L',False);
$pdf->SetFont('Arial','',$tamannoFuente);
$pdf->Cell(45,4.76,utf8_decode($_SESSION['p_Nombre ETC']),0,0,'L',False);

$current_y = $pdf->GetY();
$current_x = $pdf->GetX();
$pdf->Cell(125.1,4.76,'','B',0,'L',False);
$pdf->SetXY($current_x, $current_y);
$pdf->SetFont('Arial','B',$tamannoFuente);
$pdf->Cell(40,4.76,utf8_decode('INSTITUCIÓN O CENTRO EDUCATIVO:'),0,0,'L',False);
$pdf->SetFont('Arial','',$tamannoFuente);

// $nomInstitucion = "123456789123456789123456789123456789123456789123456789123456789123456789";
//$nomInstitucion = substr( $nomInstitucion, 0, 54 );

  if (strlen($nomInstitucion) > 65) {
    $nomInstitucion = substr($nomInstitucion, 0, 66);
    $nomInstitucion .= "...";
  }
$pdf->Cell(85,4.76,utf8_decode($nomInstitucion),0,0,'L',False); //$nomInstitucion

$current_y = $pdf->GetY();
$current_x = $pdf->GetX();
$pdf->Cell(0,4.76,'',1,0,'L',False);
$pdf->SetXY($current_x, $current_y);
$pdf->SetFont('Arial','B',$tamannoFuente);
$pdf->Cell(20,4.76,utf8_decode('SEDE EDUCATIVA:'),0,0,'L',False);
$pdf->SetFont('Arial','',$tamannoFuente);
//$sede = substr( $sede, 0, 44);
// $nomSede = "123456789123456789123456789123456789123456789123456789123456789123456789";
if (strlen($nomSede) > 49) {
    $nomSede = substr($nomSede, 0, 50);
    $nomSede .= "...";
  }
$pdf->Cell(82.3,4.76,utf8_decode($nomSede),0,0,'L',False);

$pdf->Ln(4.76);






  $pdf->Ln(0.8);
  $pdf->SetFont('Arial','B',$tamannoFuente);
  $pdf->Cell(42.5,8,'RANGO DE EDAD',1,0,'C',False);

  $current_y = $pdf->GetY();
  $current_x = $pdf->GetX();
  $pdf->Cell(45,8,'',1,0,'L',False);
  $pdf->SetXY($current_x, $current_y);
  $pdf->Cell(45,8,utf8_decode('N° DE RACIONES ADJUDICADAS'),0,4,'C',False);
  $pdf->SetXY($current_x+45, $current_y);

  $current_y = $pdf->GetY();
  $current_x = $pdf->GetX();
  $pdf->Cell(45,8,'',1,0,'L',False);
  $pdf->SetXY($current_x, $current_y);
  $pdf->Cell(45,8,utf8_decode('N° DE RACIONES ATENDIDAS'),0,4,'C',False);
  $pdf->SetXY($current_x+45, $current_y);

  $pdf->Cell(35,8,utf8_decode('N° DE DÍAS A ATENDER'),1,0,'C',False);

  $current_y = $pdf->GetY();
  $current_x = $pdf->GetX();
  $pdf->Cell(75,8,'',1,0,'L',False);
  $pdf->SetXY($current_x, $current_y);
  $pdf->Cell(75,8,utf8_decode('N° DE MENÚ Y SEMANA DEL CICLO DE MENÚS ENTREGADO'),0,4,'C',False);
  $pdf->SetXY($current_x+75, $current_y);

  $pdf->Cell(0,8,utf8_decode('TOTAL RACIONES'),1,0,'C',False);
  $pdf->Ln(8);

  $pdf->SetFont('Arial','',$tamannoFuente);
  $current_y = $pdf->GetY();
  $current_x = $pdf->GetX();
  $pdf->Cell(42.5,14.1,'',1,0,'L',False);
  $pdf->SetXY($current_x, $current_y);

  $pdf->Cell(42.5,4.7,utf8_decode($get[0]),1,4.7,'C',False);
  $pdf->Cell(42.5,4.7,utf8_decode($get[1]),1,4.7,'C',False);
  $pdf->Cell(42.5,4.7,utf8_decode($get[2]),1,0,'C',False);
  $pdf->SetXY($current_x+42.5, $current_y);

  $current_y = $pdf->GetY();
  $current_x = $pdf->GetX();
  $pdf->Cell(45,14.1,'',1,0,'L',False);
  $pdf->SetXY($current_x, $current_y);
  $pdf->Cell(45,4.7,utf8_decode($totalGrupo1),1,4.7,'C',False);
  $pdf->Cell(45,4.7,utf8_decode($totalGrupo2),1,4.7,'C',False);
  $pdf->Cell(45,4.7,utf8_decode($totalGrupo3),1,0,'C',False);
  $pdf->SetXY($current_x+45, $current_y);

  $current_y = $pdf->GetY();
  $current_x = $pdf->GetX();
  $pdf->Cell(45,14.1,'',1,0,'L',False);
  $pdf->SetXY($current_x, $current_y);
  $pdf->Cell(45,4.7,utf8_decode($totalGrupo1),1,4.7,'C',False);
  $pdf->Cell(45,4.7,utf8_decode($totalGrupo2),1,4.7,'C',False);
  $pdf->Cell(45,4.7,utf8_decode($totalGrupo3),1,0,'C',False);
  $pdf->SetXY($current_x+45, $current_y);

//dias a atender
  $pdf->SetFillColor(255,255,255);
  $current_y = $pdf->GetY();
  $current_x = $pdf->GetX();
  $pdf->Cell(35,14.1,'',1,0,'L',False);
  $pdf->SetXY($current_x, $current_y+2.35);
  $pdf->MultiCell(35,4.7,utf8_decode($auxDias),0,'C',False);

  $pdf->SetXY($current_x, $current_y+9.4);

  $pdf->SetXY($current_x+35, $current_y);
  //ciclos
  $current_y = $pdf->GetY();
  $current_x = $pdf->GetX();
  $pdf->Cell(75,14.1,'',1,0,'L',False);
  $pdf->SetXY($current_x, $current_y+2.35);
  $pdf->Cell(75,4.7,'SEMANA: '.$auxCiclos,0,0,'C',False);
  $pdf->SetFont('Arial','',$tamannoFuente);
  $pdf->SetXY($current_x, $current_y+7,05);
  $pdf->Cell(75,4.7,utf8_decode('MENÚS: '.$auxMenus),0,0,'C',False);

  $pdf->SetXY($current_x+75, $current_y);
  $current_y = $pdf->GetY();
  $current_x = $pdf->GetX();
  $pdf->Cell(0,14.1,'',1,0,'L',False);
  $pdf->SetXY($current_x+2, $current_y+2.35);





   $jm = '';
   $jt = '';

   // 2 es la jornada de la mañana
   // 3 es la jornada de la tarde
   if($jornada == 2){
    $jm = $totalGrupo1 + $totalGrupo2 + $totalGrupo3;
   }else if($jornada == 3){
    $jt = $totalGrupo1 + $totalGrupo2 + $totalGrupo3;
   }

  if($modalidad == 'APS'){
    $pdf->SetFont('Arial','',$tamannoFuente+7);
    $pdf->Cell(0,8,utf8_decode($jt),0,0,'C',False);
    $pdf->SetFont('Arial','',$tamannoFuente);
  }else{
    $pdf->Cell(5,4.7,'JM:',0,0,'L',False);
    $pdf->Cell(10,4.7,$jm,'B',0,'L',False);
    $pdf->SetXY($current_x+2, $current_y+7.05);
    $pdf->Cell(5,4.7,'JT:',0,0,'L',False);
    $pdf->Cell(10,4.7,$jt,'B',0,'L',False);
  }

  $pdf->SetXY($current_x, $current_y+14.1);
  $pdf->Ln(0.8);

  $pdf->SetFont('Arial','B',$tamannoFuente);


  $pdf->Cell(42.4,15,'GRUPO ALIMENTO',1,0,'C',False);
  $pdf->Cell(45.2,15,'ALIMENTO',1,0,'C',False);



  $current_y = $pdf->GetY();
  $current_x = $pdf->GetX();
  $pdf->Cell(13.141,15,'',1,0,'L',False);
  $pdf->SetXY($current_x, $current_y);
  $pdf->Cell(13.141,5,'UNIDAD',0,5,'C',False);
  $pdf->Cell(13.141,5,'DE',0,5,'C',False);
  $pdf->Cell(13.141,5,'MEDIDA',0,5,'C',False);

  $pdf->SetXY($current_x+13.141, $current_y);
  $current_y = $pdf->GetY();
  $current_x = $pdf->GetX();
  $pdf->Cell(17.471,15,'',1,0,'L',False);
  $pdf->SetXY($current_x, $current_y+2.5);
  $pdf->Cell(17.471,5,'CANT',0,5,'C',False);
  $pdf->Cell(17.471,5,'ENTREGADA',0,5,'C',False);

  $pdf->SetXY($current_x+17.471, $current_y);
  $current_y = $pdf->GetY();
  $current_x = $pdf->GetX();
  $pdf->Cell(16,15,'',1,0,'L',False);
  $pdf->SetXY($current_x, $current_y);
  $pdf->Cell(16,15,'EXISTENCIAS',0,15,'C',False);

  $pdf->SetXY($current_x+16, $current_y);

  $current_y = $pdf->GetY();
  $current_x = $pdf->GetX();
  $pdf->Cell(22,15,'',1,0,'L',False);
  $pdf->SetXY($current_x, $current_y);
  $pdf->Cell(22,8,utf8_decode('LUNES'),'B',8,'C',False);
  $pdf->Cell(22,7,'CANT','R',0,'C',False);

  $pdf->SetXY($current_x+22, $current_y);

  $current_y = $pdf->GetY();
  $current_x = $pdf->GetX();
  $pdf->Cell(22,15,'',1,0,'L',False);
  $pdf->SetXY($current_x, $current_y);
  $pdf->Cell(22,8,utf8_decode('MARTES'),'B',8,'C',False);
  $pdf->Cell(22,7,'CANT','R',0,'C',False);

  $pdf->SetXY($current_x+22, $current_y);

  $current_y = $pdf->GetY();
  $current_x = $pdf->GetX();
  $pdf->Cell(22,15,'',1,0,'L',False);
  $pdf->SetXY($current_x, $current_y);
  $pdf->Cell(22,8,utf8_decode('MIÉRCOLES'),'B',8,'C',False);
  $pdf->Cell(22,7,'CANT','R',0,'C',False);

  $pdf->SetXY($current_x+22, $current_y);

  $current_y = $pdf->GetY();
  $current_x = $pdf->GetX();
  $pdf->Cell(22,15,'',1,0,'L',False);
  $pdf->SetXY($current_x, $current_y);
  $pdf->Cell(22,8,utf8_decode('JUEVES'),'B',8,'C',False);
  $pdf->Cell(22,7,'CANT','R',0,'C',False);

  $pdf->SetXY($current_x+22, $current_y);

  $current_y = $pdf->GetY();
  $current_x = $pdf->GetX();
  $pdf->Cell(22,15,'',1,0,'L',False);
  $pdf->SetXY($current_x, $current_y);
  $pdf->Cell(22,8,utf8_decode('VIERNES'),'B',8,'C',False);
  $pdf->Cell(22,7,'CANT','R',0,'C',False);

  $pdf->SetXY($current_x+22, $current_y);

  $current_y = $pdf->GetY();
  $current_x = $pdf->GetX();
  $pdf->Cell(19.7,15,'',1,0,'L',False);
  $pdf->SetXY($current_x, $current_y);
  $pdf->Cell(19.7,15,'SALDO',0,15,'C',False);

  $pdf->SetXY($current_x+19.7, $current_y);

  $pdf->Ln(15);
  $pdf->SetFont('Arial','',$tamannoFuente);
