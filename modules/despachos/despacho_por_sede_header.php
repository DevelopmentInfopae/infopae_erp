<?php

//header
  $logoInfopae = $_SESSION['p_Logo ETC'];
  $pdf->SetFont('Arial');
  $pdf->SetTextColor(0,0,0);
  $pdf->SetLineWidth(.05);
  $pdf->Image($logoInfopae, 11.5 ,7, 111.84, 15.92,'jpg', '');
  $pdf->Cell(116.95,17.29,'',1,0,'C',False);
  $current_y = $pdf->GetY();
  $current_x = $pdf->GetX();
  $pdf->MultiCell(146.45,17.29,'',1,'C',false);
  $pdf->SetXY($current_x, $current_y);
  $pdf->SetFont('Arial','B',$tamannoFuente);
  $pdf->Cell(146.45,4.3,utf8_decode('PROGRAMA DE ALIMENTACIÓN ESCOLAR'),0,4.3,'C',False);
  $pdf->Cell(146.45,4.3,utf8_decode('REMISIÓN ENTREGA DE VÍVERES EN INSTITUCIÓN EDUCATIVA'),0,4.3,'C',False);
  $pdf->Cell(146.45,4.3,utf8_decode($descripcionTipo),0,4.3,'C',False);
  $pdf->Cell(146.45,4.3,utf8_decode($tipoDespachoNm),0,4.3,'C',False);
  $pdf->Ln(0.09);

  $current_y = $pdf->GetY();
  $current_x = $pdf->GetX();
  $pdf->Cell(171.8,4.76,'',1,0,'L',False);
  $pdf->SetXY($current_x, $current_y);
  $pdf->Cell(20,4.76,utf8_decode('OPERADOR:'),0,0,'L',False);
  $pdf->SetFont('Arial','',$tamannoFuente);
  $pdf->Cell(151.8,4.76,utf8_decode( $_SESSION['p_Operador']),0,0,'L',False);

  $current_y = $pdf->GetY();
  $current_x = $pdf->GetX();
  $pdf->Cell(91.6,4.76,'',1,0,'L',False);
  $pdf->SetXY($current_x, $current_y);
  $pdf->SetFont('Arial','B',$tamannoFuente);
  $pdf->Cell(33,4.76,utf8_decode('FECHA DE ELABORACIÓN:'),0,0,'L',False);
  $pdf->SetFont('Arial','',$tamannoFuente);
  $pdf->Cell(78.6,4.76,utf8_decode($fechaDespacho),0,0,'L',False);
  $pdf->Ln(4.76);

  $current_y = $pdf->GetY();
  $current_x = $pdf->GetX();
  $pdf->Cell(117,4.76,'',1,0,'L',False);
  $pdf->SetXY($current_x, $current_y);
  $pdf->SetFont('Arial','B',$tamannoFuente);
  $pdf->Cell(8,4.76,utf8_decode('ETC:'),0,0,'L',False);
  $pdf->SetFont('Arial','',$tamannoFuente);
  $pdf->Cell(109,4.76,utf8_decode($_SESSION['p_Nombre ETC']),0,0,'L',False);

  $current_y = $pdf->GetY();
  $current_x = $pdf->GetX();
  $pdf->Cell(146.4,4.76,'',1,0,'L',False);
  $pdf->SetXY($current_x, $current_y);
  $pdf->SetFont('Arial','B',$tamannoFuente);
  $pdf->Cell(33,4.76,utf8_decode('MUNICIPIO O VEREDA:'),0,0,'L',False);
  $pdf->SetFont('Arial','',$tamannoFuente);
  $pdf->Cell(113.4,4.76,utf8_decode($municipio),0,0,'L',False);
  $pdf->Ln(4.76);

  $current_y = $pdf->GetY();
  $current_x = $pdf->GetX();
  $pdf->Cell(153.1,4.76,'',1,0,'L',False);
  $pdf->SetXY($current_x, $current_y);
  $pdf->SetFont('Arial','B',$tamannoFuente);
  $pdf->Cell(54,4.76,utf8_decode('INSTITUCIÓN O CENTRO EDUCATIVO:'),0,0,'L',False);
  $pdf->SetFont('Arial','',$tamannoFuente);



  $institucion = substr( $institucion, 0, 54 );



  $pdf->Cell(99.1,4.76,utf8_decode($institucion),0,0,'L',False);

  $current_y = $pdf->GetY();
  $current_x = $pdf->GetX();
  $pdf->Cell(110.3,4.76,'',1,0,'L',False);
  $pdf->SetXY($current_x, $current_y);
  $pdf->SetFont('Arial','B',$tamannoFuente);
  $pdf->Cell(28,4.76,utf8_decode('SEDE EDUCATIVA:'),0,0,'L',False);
  $pdf->SetFont('Arial','',$tamannoFuente);


  $sede = substr( $sede, 0, 44);

  $pdf->Cell(82.3,4.76,utf8_decode($sede),0,0,'L',False);
  $pdf->Ln(4.76);
  $pdf->Ln(0.8);

  $pdf->SetFont('Arial','B',$tamannoFuente);
  $pdf->Cell(42.5,8,'RANGO DE EDAD',1,0,'C',False);

  $current_y = $pdf->GetY();
  $current_x = $pdf->GetX();
  $pdf->Cell(36.7,8,'',1,0,'L',False);
  $pdf->SetXY($current_x, $current_y);
  $pdf->Cell(36.7,4,utf8_decode('N° DE RACIONES'),0,4,'C',False);
  $pdf->Cell(36.7,4,utf8_decode('ADJUDICADAS'),0,0,'C',False);
  $pdf->SetXY($current_x+36.7, $current_y);

  $current_y = $pdf->GetY();
  $current_x = $pdf->GetX();
  $pdf->Cell(36.7,8,'',1,0,'L',False);
  $pdf->SetXY($current_x, $current_y);
  $pdf->Cell(36.7,4,utf8_decode('N° DE RACIONES'),0,4,'C',False);
  $pdf->Cell(36.7,4,utf8_decode('ATENDIDAS'),0,0,'C',False);
  $pdf->SetXY($current_x+36.7, $current_y);

  $pdf->Cell(45,8,utf8_decode('N° DE DÍAS A ATENDER'),1,0,'C',False);

  $current_y = $pdf->GetY();
  $current_x = $pdf->GetX();
  $pdf->Cell(57.8,8,'',1,0,'L',False);
  $pdf->SetXY($current_x, $current_y);
  $pdf->Cell(57.8,4,utf8_decode('N° DE MENÚ Y SEMANA DEL CICLO DE'),0,4,'C',False);
  $pdf->Cell(57.8,4,utf8_decode('MENÚS ENTREGADO'),0,0,'C',False);
  $pdf->SetXY($current_x+57.8, $current_y);

  $pdf->Cell(44.7,8,utf8_decode('TOTAL RACIONES'),1,0,'C',False);
  $pdf->Ln(8);

  $pdf->SetFont('Arial','',$tamannoFuente);
  $current_y = $pdf->GetY();
  $current_x = $pdf->GetX();
  $pdf->Cell(42.5,14.1,'',1,0,'L',False);
  $pdf->SetXY($current_x, $current_y);
  $consGrupoEtario = "SELECT * FROM grupo_etario ";
  $resGrupoEtario = $Link->query($consGrupoEtario);
  if ($resGrupoEtario->num_rows > 0) {
    while ($ge = $resGrupoEtario->fetch_assoc()) {
      $get[] = $ge['DESCRIPCION'];
    }
  }

  $pdf->Cell(42.5,4.7,utf8_decode($get[0]),1,4.7,'C',False);
  $pdf->Cell(42.5,4.7,utf8_decode($get[1]),1,4.7,'C',False);
  $pdf->Cell(42.5,4.7,utf8_decode($get[2]),1,0,'C',False);
  $pdf->SetXY($current_x+42.5, $current_y);

  $current_y = $pdf->GetY();
  $current_x = $pdf->GetX();
  $pdf->Cell(36.7,14.1,'',1,0,'L',False);
  $pdf->SetXY($current_x, $current_y);
  $pdf->Cell(36.7,4.7,utf8_decode($sedeGrupo1),1,4.7,'C',False);
  $pdf->Cell(36.7,4.7,utf8_decode($sedeGrupo2),1,4.7,'C',False);
  $pdf->Cell(36.7,4.7,utf8_decode($sedeGrupo3),1,0,'C',False);
  $pdf->SetXY($current_x+36.7, $current_y);

  $current_y = $pdf->GetY();
  $current_x = $pdf->GetX();
  $pdf->Cell(36.7,14.1,'',1,0,'L',False);
  $pdf->SetXY($current_x, $current_y);
  $pdf->Cell(36.7,4.7,utf8_decode($sedeGrupo1),1,4.7,'C',False);
  $pdf->Cell(36.7,4.7,utf8_decode($sedeGrupo2),1,4.7,'C',False);
  $pdf->Cell(36.7,4.7,utf8_decode($sedeGrupo3),1,0,'C',False);
  $pdf->SetXY($current_x+36.7, $current_y);

  $pdf->SetFillColor(255,255,255);
  $current_y = $pdf->GetY();
  $current_x = $pdf->GetX();
  $pdf->Cell(45,14.1,'',1,0,'L',False);
  $pdf->SetXY($current_x, $current_y+2.35);







  $cantDias = explode(',', $diasDespacho);
  $cantDias = count($cantDias);
  $auxDias = "X ".$cantDias." DIAS ".strtoupper($dias);






  $pdf->MultiCell(45,4.7,$auxDias,0,'C',False);
  $pdf->SetXY($current_x, $current_y+9.4);
  $pdf->MultiCell(45,4.7,'SEMANA: '.$semana,0,'C',False);




  $pdf->SetXY($current_x+45, $current_y);

  $current_y = $pdf->GetY();
  $current_x = $pdf->GetX();
  $pdf->Cell(57.8,14.1,'',1,0,'L',False);
  $pdf->SetXY($current_x, $current_y+2.35);
  //$pdf->SetFont('Arial','B',$tamannoFuente);
  $pdf->Cell(57.8,4.7,'SEMANA: '.$ciclo,0,0,'C',False);
  //$pdf->SetFont('Arial','',$tamannoFuente);
  //$pdf->SetXY($current_x+34, $current_y+2.35);
  //$pdf->Cell(10,4.7,$semana,0,4.7,'L',False);
  //$pdf->SetFont('Arial','B',$tamannoFuente);
  $pdf->SetXY($current_x, $current_y+7,05);
  $pdf->Cell(57.8,4.7,'MENUS: '.$auxMenus,0,0,'C',False);
  $pdf->SetFont('Arial','',$tamannoFuente);
  //$pdf->SetXY($current_x+33, $current_y+7,05);
  //$pdf->Cell(57.8,4.7,$ciclo,0,0,'L',False);

  $pdf->SetXY($current_x+57.8, $current_y);
  $current_y = $pdf->GetY();
  $current_x = $pdf->GetX();
  $pdf->Cell(44.7,14.1,'',1,0,'L',False);
  $pdf->SetXY($current_x+2, $current_y+2.35);















    $jm = '';
    $jt = '';


    $totalBeneficiarios = $sedeGrupo1 + $sedeGrupo2 + $sedeGrupo3;

    // 2 es la jornada de la mañana
    // 3 es la jornada de la tarde
    if($jornada == 2){
      $jm = $sedeGrupo1 + $sedeGrupo2 + $sedeGrupo3;
    }else if($jornada == 3){
      $jt = $sedeGrupo1 + $sedeGrupo2 + $sedeGrupo3;
    }
    $pdf->Cell(7,4.7,'JM:',0,0,'L',False);
    $pdf->Cell(33,4.7,$jm,'B',0,'L',False);
    $pdf->SetXY($current_x+2, $current_y+7.05);
    $pdf->Cell(7,4.7,'JT:',0,0,'L',False);
    $pdf->Cell(33,4.7,$jt,'B',0,'L',False);

















  $pdf->SetXY($current_x, $current_y+14.1);
  $pdf->Ln(0.8);

  $pdf->SetFont('Arial','B',$tamannoFuente);





  $current_y = $pdf->GetY();
  $current_x = $pdf->GetX();
  $pdf->Cell(23.788,15,'',1,0,'C',False);
  $pdf->SetXY($current_x, $current_y);
  $pdf->Ln(2.5);
  $pdf->MultiCell(23.755,5,'GRUPO ALIMENTO',0,'C',False);
  $pdf->SetXY($current_x+23.788, $current_y);




  $pdf->Cell(48.972,15,'ALIMENTO',1,0,'C',False);




  $current_y = $pdf->GetY();
  $current_x = $pdf->GetX();
  $pdf->Cell(39.33,15,'',1,0,'L',False);
  $pdf->SetXY($current_x, $current_y);
  $pdf->Cell(39.33,8,'',1,0,'L',False);
  $pdf->SetXY($current_x, $current_y);
  $pdf->Cell(39.33,4,'CNT DE ALIMENTOS POR',0,4,'C',False);
  $pdf->Cell(39.33,4,utf8_decode('NÚMEROS DE RACIONES'),0,4,'C',False);
  $current_y2 = $pdf->GetY();
  $current_x2 = $pdf->GetX();
  $pdf->Cell(13.1,7,utf8_decode(''),1,0,'C',False);
  $pdf->SetXY($current_x2, $current_y2);

  $etario_1 = str_replace(" + 11 meses", "", $get[0]);
  $etario_2 = str_replace(" + 11 meses", "", $get[1]);
  $etario_3 = str_replace(" + 11 meses", "", $get[2]);

  $etario_1 = str_replace(" años", "", $etario_1);
  $etario_2 = str_replace(" años", "", $etario_2);
  $etario_3 = str_replace(" años", "", $etario_3);

  $pdf->Cell(13.1,3.5,utf8_decode($etario_1),0,3.5,'C',False);
  $pdf->Cell(13.1,3.5,utf8_decode('AÑOS'),0,3.5,'C',False);

  $pdf->SetXY($current_x2+13.1, $current_y2);
  $current_y2 = $pdf->GetY();
  $current_x2 = $pdf->GetX();
  $pdf->Cell(13.1,7,utf8_decode(''),1,0,'C',False);
  $pdf->SetXY($current_x2, $current_y2);
  $pdf->Cell(13.1,3.5,utf8_decode($etario_2),0,3.5,'C',False);
  $pdf->Cell(13.1,3.5,utf8_decode('AÑOS'),0,3.5,'C',False);

  $pdf->SetXY($current_x2+13.1, $current_y2);
  $current_y2 = $pdf->GetY();
  $current_x2 = $pdf->GetX();
  $pdf->Cell(13.1,7,utf8_decode(''),1,0,'C',False);
  $pdf->SetXY($current_x2, $current_y2);
  $pdf->Cell(13.1,3.5,utf8_decode($etario_3),0,3.5,'C',False);
  $pdf->Cell(13.1,3.5,utf8_decode('AÑOS'),0,3.5,'C',False);

  $pdf->SetXY($current_x+39.33, $current_y);
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
  $pdf->Cell(13.141,15,'',1,0,'L',False);
  $pdf->SetXY($current_x, $current_y+2.5);
  $pdf->Cell(13.141,5,'TOTAL',0,5,'C',False);
  $pdf->Cell(13.141,5,'REQ',0,5,'C',False);


  $pdf->SetXY($current_x+13.141, $current_y);
  $current_y = $pdf->GetY();
  $current_x = $pdf->GetX();
  $pdf->Cell(31.838,15,'',1,0,'L',False);
  $pdf->SetXY($current_x, $current_y);
  $pdf->Cell(31.838,4,'CANTIDAD',0,4,'C',False);
  $pdf->Cell(31.838,4,'ENTREGADA','B',4,'C',False);
  $pdf->Cell(10.6,7,'TOTAL','R',0,'C',False);
  $pdf->Cell(10.638,7,'C','R',0,'C',False);
  $pdf->Cell(10.6,7,'NC','R',0,'C',False);

  $pdf->SetXY($current_x+31.838, $current_y);
  $current_y = $pdf->GetY();
  $current_x = $pdf->GetX();
  $pdf->Cell(27.252,15,'',1,0,'L',False);
  $pdf->SetXY($current_x, $current_y);
  $pdf->Cell(27.252,4,utf8_decode('ESPECIFICACIÓN'),0,4,'C',False);
  $pdf->Cell(27.252,4,utf8_decode('DE CALIDAD'),'B',4,'C',False);
  $pdf->Cell(13.626,7,'C','R',0,'C',False);
  $pdf->Cell(13.626,7,'NC','R',0,'C',False);

  $pdf->SetXY($current_x+27.252, $current_y);
  $current_y = $pdf->GetY();
  $current_x = $pdf->GetX();
  $pdf->Cell(32.191,15,'',1,0,'L',False);
  $pdf->SetXY($current_x, $current_y);
  $pdf->Cell(32.191,8,utf8_decode('FALTANTES'),'B',8,'C',False);
  $pdf->Cell(9.349,7,'SI','R',0,'C',False);
  $pdf->Cell(8.819,7,'NO','R',0,'C',False);
  $pdf->Cell(14.023,7,'CANT','R',0,'C',False);

  $pdf->SetXY($current_x+32.191, $current_y);
  $current_y = $pdf->GetY();
  $current_x = $pdf->GetX();
  $pdf->Cell(33.747,15,'',1,0,'L',False);
  $pdf->SetXY($current_x, $current_y);
  $pdf->Cell(33.747,8,utf8_decode('DEVOLUCIÓN'),'B',8,'C',False);
  $pdf->Cell(9.26,7,'SI','R',0,'C',False);
  $pdf->Cell(9.084,7,'NO','R',0,'C',False);
  $pdf->Cell(15.403,7,'CANT','R',0,'C',False);

  $pdf->SetXY($current_x, $current_y);
  $pdf->Ln(15);
  //Termina el header


   $pdf->SetFont('Arial','',$tamannoFuente);
