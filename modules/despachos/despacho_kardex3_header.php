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
  $pdf->MultiCell(0,17.29,'',1,'C',false);
  $pdf->SetXY($current_x, $current_y);
  $pdf->SetFont('Arial','B',$tamannoFuente);
  $pdf->Cell(0,4.3,utf8_decode('PROGRAMA DE ALIMENTACIÓN ESCOLAR'),0,4.3,'C',False);
  $pdf->Cell(0,4.3,utf8_decode('KARDEX DE VÍVERES EN INSTITUCIÓN EDUCATIVA'),0,4.3,'C',False);
  $pdf->Cell(0,4.3,utf8_decode($descripcionTipo),0,4.3,'C',False);
  $pdf->Cell(0,4.3,utf8_decode($tipoDespachoNm),0,4.3,'C',False);
  $pdf->Ln(0.09);

  $current_y = $pdf->GetY();
  $current_x = $pdf->GetX();
  $pdf->Cell(0,4.76,'',1,0,'L',False);
  $pdf->SetXY($current_x, $current_y);
  $pdf->Cell(20,4.76,utf8_decode('OPERADOR:'),0,0,'L',False);
  $pdf->SetFont('Arial','',$tamannoFuente);
  $pdf->Cell(151.8,4.76,utf8_decode( $_SESSION['p_Operador']),0,0,'L',False);

  $current_y = $pdf->GetY();
  $current_x = $pdf->GetX();

  $pdf->SetXY(290,15);
  //$pdf->Cell(0,10,utf8_decode('Página ').$pdf->PageNo().' de {nb}',0,0,'C');
  $pdf->SetXY($current_x, $current_y);



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
  $pdf->Cell(0,4.76,'',1,0,'L',False);
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
  $pdf->Cell(0,4.76,'',1,0,'L',False);
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

  $pdf->Cell(45,8,utf8_decode('N° DE DÍAS A ATENDER'),1,0,'C',False);

  $current_y = $pdf->GetY();
  $current_x = $pdf->GetX();
  $pdf->Cell(90,8,'',1,0,'L',False);
  $pdf->SetXY($current_x, $current_y);
  $pdf->Cell(90,8,utf8_decode('N° DE MENÚ Y SEMANA DEL CICLO DE MENÚS ENTREGADO'),0,4,'C',False);
  $pdf->SetXY($current_x+90, $current_y);

  $pdf->Cell(0,8,utf8_decode('TOTAL RACIONES'),1,0,'C',False);
  $pdf->Ln(8);

  $pdf->SetFont('Arial','',$tamannoFuente);
  $current_y = $pdf->GetY();
  $current_x = $pdf->GetX();
  $pdf->Cell(42.5,14.1,'',1,0,'L',False);
  $pdf->SetXY($current_x, $current_y);
  $pdf->Cell(42.5,4.7,utf8_decode('4 - 6 años 11 meses'),1,4.7,'C',False);
  $pdf->Cell(42.5,4.7,utf8_decode('7 - 12 años 11 meses'),1,4.7,'C',False);
  $pdf->Cell(42.5,4.7,utf8_decode('13 - 17 años 11 meses'),1,0,'C',False);
  $pdf->SetXY($current_x+42.5, $current_y);

  $current_y = $pdf->GetY();
  $current_x = $pdf->GetX();
  $pdf->Cell(45,14.1,'',1,0,'L',False);
  $pdf->SetXY($current_x, $current_y);
  $pdf->Cell(45,4.7,utf8_decode($sedeGrupo1),1,4.7,'C',False);
  $pdf->Cell(45,4.7,utf8_decode($sedeGrupo2),1,4.7,'C',False);
  $pdf->Cell(45,4.7,utf8_decode($sedeGrupo3),1,0,'C',False);
  $pdf->SetXY($current_x+45, $current_y);

  $current_y = $pdf->GetY();
  $current_x = $pdf->GetX();
  $pdf->Cell(45,14.1,'',1,0,'L',False);
  $pdf->SetXY($current_x, $current_y);
  $pdf->Cell(45,4.7,utf8_decode($sedeGrupo1),1,4.7,'C',False);
  $pdf->Cell(45,4.7,utf8_decode($sedeGrupo2),1,4.7,'C',False);
  $pdf->Cell(45,4.7,utf8_decode($sedeGrupo3),1,0,'C',False);
  $pdf->SetXY($current_x+45, $current_y);

  $pdf->SetFillColor(255,255,255);
  $current_y = $pdf->GetY();
  $current_x = $pdf->GetX();
  $pdf->Cell(45,14.1,'',1,0,'L',False);
  $pdf->SetXY($current_x, $current_y+2.35);



  $auxDias = "X ";
  $cantDias = explode(',', $dias);
  $cantDias = count($cantDias);
  $auxDias = "X ".$cantDias." DIAS ".strtoupper($dias);


  $pdf->MultiCell(45,4.7,$auxDias,0,'C',False);
  //$pdf->SetXY($current_x, $current_y+9.4);
  //$pdf->MultiCell(45,4.7,'SEMANA: '.$semana,0,'C',False);








  $pdf->SetXY($current_x+45, $current_y);

  $current_y = $pdf->GetY();
  $current_x = $pdf->GetX();
  $pdf->Cell(90,14.1,'',1,0,'L',False);
  $pdf->SetXY($current_x, $current_y+2.35);
  //$pdf->SetFont('Arial','B',$tamannoFuente);
  $pdf->Cell(85,4.7,'SEMANA: '.$ciclo,0,0,'C',False);
  //$pdf->SetFont('Arial','',$tamannoFuente);
  //$pdf->SetXY($current_x+47, $current_y+2.35);
  //$pdf->Cell(10,4.7,$semana,0,4.7,'L',False);
  //$pdf->SetFont('Arial','B',$tamannoFuente);
  $pdf->SetXY($current_x, $current_y+7,05);
  $pdf->Cell(85,4.7,'MENUS: '.$auxMenus,0,0,'C',False);
  //$pdf->SetFont('Arial','',$tamannoFuente);
  //$pdf->SetXY($current_x+45, $current_y+7,05);
  //$pdf->Cell(80,4.7,$ciclo,0,0,'L',False);

  $pdf->SetXY($current_x+90, $current_y);





  $current_y = $pdf->GetY();
  $current_x = $pdf->GetX();
  $pdf->Cell(0,14.1,'',1,0,'L',False);
  $pdf->SetXY($current_x+2, $current_y+2.35);

    $jm = '';
    $jt = '';

    // 2 es la jornada de la mañana
    // 3 es la jornada de la tarde
    if($jornada == 2){
      $jm = $sedeGrupo1 + $sedeGrupo2 + $sedeGrupo3;
    }else if($jornada == 3){
      $jt = $sedeGrupo1 + $sedeGrupo2 + $sedeGrupo3;
    }



     if($modalidad == 'APS'){
      $pdf->SetFont('Arial','',$tamannoFuente+7);
      $pdf->Cell(41,8,utf8_decode($jt),0,0,'C',False);
      $pdf->SetFont('Arial','',$tamannoFuente);
    }else{
      $pdf->Cell(7,4.7,'JM:',0,0,'L',False);
      $pdf->Cell(33,4.7,$jm,'B',0,'L',False);
      $pdf->SetXY($current_x+2, $current_y+7.05);
      $pdf->Cell(7,4.7,'JT:',0,0,'L',False);
      $pdf->Cell(33,4.7,$jt,'B',0,'L',False);
    }





















  $pdf->SetXY($current_x, $current_y+14.1);
  $pdf->Ln(0.8);

  $pdf->SetFont('Arial','B',$tamannoFuente);


  $pdf->Cell(44.388,15,'GRUPO ALIMENTO',1,0,'C',False);
  $pdf->Cell(44,15,'ALIMENTO',1,0,'C',False);



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
  // $pdf->Cell(17.471,5,'ENTREGADA',0,5,'C',False);
  $pdf->Cell(17.471,5,'REQUERIDA',0,5,'C',False);

  $pdf->SetXY($current_x+17.471, $current_y);
  $current_y = $pdf->GetY();
  $current_x = $pdf->GetX();
  $pdf->Cell(18,15,'',1,0,'L',False);
  $pdf->SetXY($current_x, $current_y);
  $pdf->Cell(18,15,'EXISTENCIAS',0,15,'C',False);

  $pdf->SetXY($current_x+18, $current_y);


  $current_y = $pdf->GetY();
  $current_x = $pdf->GetX();
  $pdf->Cell(31.8,15,'',1,0,'L',False);
  $pdf->SetXY($current_x, $current_y);
  $pdf->Cell(31.8,8,utf8_decode('LUNES'),'B',8,'C',False);
  $pdf->Cell(15.9,7,'CANT','R',0,'C',False);
  $pdf->Cell(15.9,7,'SALIDA','R',0,'C',False);

  $pdf->SetXY($current_x+31.8, $current_y);

  $current_y = $pdf->GetY();
  $current_x = $pdf->GetX();
  $pdf->Cell(31.8,15,'',1,0,'L',False);
  $pdf->SetXY($current_x, $current_y);
  $pdf->Cell(31.8,8,utf8_decode('MARTES'),'B',8,'C',False);
  $pdf->Cell(15.9,7,'CANT','R',0,'C',False);
  $pdf->Cell(15.9,7,'SALIDA','R',0,'C',False);

  $pdf->SetXY($current_x+31.8, $current_y);

  $current_y = $pdf->GetY();
  $current_x = $pdf->GetX();
  $pdf->Cell(31.8,15,'',1,0,'L',False);
  $pdf->SetXY($current_x, $current_y);
  $pdf->Cell(31.8,8,utf8_decode('MIERCOLES'),'B',8,'C',False);
  $pdf->Cell(15.9,7,'CANT','R',0,'C',False);
  $pdf->Cell(15.9,7,'SALIDA','R',0,'C',False);

  $pdf->SetXY($current_x+31.8, $current_y);

  $current_y = $pdf->GetY();
  $current_x = $pdf->GetX();
  $pdf->Cell(31.8,15,'',1,0,'L',False);
  $pdf->SetXY($current_x, $current_y);
  $pdf->Cell(31.8,8,utf8_decode('JUEVES'),'B',8,'C',False);
  $pdf->Cell(15.9,7,'CANT','R',0,'C',False);
  $pdf->Cell(15.9,7,'SALIDA','R',0,'C',False);

  $pdf->SetXY($current_x+31.8, $current_y);

  $current_y = $pdf->GetY();
  $current_x = $pdf->GetX();
  $pdf->Cell(31.8,15,'',1,0,'L',False);
  $pdf->SetXY($current_x, $current_y);
  $pdf->Cell(31.8,8,utf8_decode('VIERNES'),'B',8,'C',False);
  $pdf->Cell(15.9,7,'CANT','R',0,'C',False);
  $pdf->Cell(15.9,7,'SALIDA','R',0,'C',False);

  $pdf->SetXY($current_x+31.8, $current_y);

  $current_y = $pdf->GetY();
  $current_x = $pdf->GetX();
  $pdf->Cell(18,15,'',1,0,'L',False);
  $pdf->SetXY($current_x, $current_y);
  $pdf->Cell(18,15,'SALDO',0,15,'C',False);

  $pdf->SetXY($current_x+18, $current_y);

  $pdf->Ln(15);
  //Termina el header


    $pdf->SetFont('Arial','',$tamannoFuente);
