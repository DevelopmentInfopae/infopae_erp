<?php 
require_once '../../../config.php';
require_once '../../../db/conexion.php'; 
$periodoActual = $_SESSION['periodoActual'];
$mesesNom = array('01' => "Enero", "02" => "Febrero", "03" => "Marzo", "04" => "Abril", "05" => "Mayo", "06" => "Junio", "07" => "Julio", "08" => "Agosto", "09" => "Septiembre", "10" => "Octubre", "11" => "Noviembre", "12" => "Diciembre");

$periodo = 1;
$diasSemanas = [];
$consDiasSemanas = "SELECT GROUP_CONCAT(DIA) AS Dias, MES, SEMANA FROM planilla_semanas WHERE CONCAT(ANO, '-', MES, '-', DIA) <= '".date('Y-m-d')."' GROUP BY SEMANA";

// echo $consDiasSemanas;
$resDiasSemanas = $Link->query($consDiasSemanas);
if ($resDiasSemanas->num_rows > 0) {
  while ($dataDiasSemanas = $resDiasSemanas->fetch_assoc()) {
    $semanasP[$periodo] = $dataDiasSemanas['SEMANA'];
    $consultaTablas = " SELECT 
                          table_name AS tabla
                        FROM 
                          information_schema.tables
                        WHERE 
                          table_schema = DATABASE() AND table_name = 'entregas_res_".$dataDiasSemanas['MES']."$periodoActual'";
    $resTablas = $Link->query($consultaTablas);
    if ($resTablas->num_rows > 0) {
      $semanaPos = $dataDiasSemanas['SEMANA'];
      $arrDias = explode(",", $dataDiasSemanas['Dias']);
      sort($arrDias);
      // print_r($arrDias);
      $diasSemanas[$dataDiasSemanas['MES']][$semanaPos] = $arrDias; //obtenemos un array ordenado del siguiente modo array[mes][semana] = array[dias]
    }
    $periodo++;
  }
}

  $tipoComplementos = [];
  $consComplemento="SELECT * FROM tipo_complemento";
  $resComplemento = $Link->query($consComplemento);
  if ($resComplemento->num_rows > 0) {
    while ($Complemento = $resComplemento->fetch_assoc()) {
      $tipoComplementos[] = $Complemento['CODIGO'];
    }
  }

  $totalesSemanas = [];
  $sumTotalesSemanas = [];

  foreach ($diasSemanas as $mes => $semanas) { //recorremos los meses
    $datos = "";
    $diaD = 1;
    $sem=0;
    $tabla="entregas_res_$mes$periodoActual"; //tabla donde se busca, según mes(obtenido de consulta anterior) y año
    foreach ($semanas as $semana => $dias) { //recorremos las semanas del mes en turno
      if ($semana == $sem.'b') {
        $mismaSemanaB = "SELECT COUNT(dia) as numero FROM planilla_semanas WHERE semana IN ('$semana','$sem') GROUP BY dia LIMIT 1";
        $respuestaSemanaB = $Link->query($mismaSemanaB) or die('Error al consultar los días de la misma semana' . mysqli_error($Link));
        if ($respuestaSemanaB->num_rows > 0) {
          $dataSemanaB = $respuestaSemanaB->fetch_assoc();
          $numeroDiasRepetidos = $dataSemanaB['numero'];
          if ($numeroDiasRepetidos == 2) {
            $diaD = 1;
          }
        }
      }
      foreach ($dias as $D => $dia) { //recorremos los días de la semana en turno
        // echo $mes." - ".$semana." - ".$D." - ".$dia."</br>";
        $consultaPlanillaDias = "SELECT D$diaD FROM planilla_dias WHERE D$diaD = $dia AND mes = $mes;";
        // echo $consultaPlanillaDias."<br>";
        $respuestaConsultaPlanillaDias = $Link->query($consultaPlanillaDias);
        $consultaPlanillaDias = "SELECT D$diaD FROM planilla_dias WHERE D$diaD = $dia AND mes = $mes;";
        if ($respuestaConsultaPlanillaDias->num_rows == 1) {
          $datos.="SUM(D$diaD) + ";
          $diaD++;
        }
      }
      $datos = trim($datos, "+ ");
      $datos.= " AS semana_".$semana.", ";
      $sem = $semana; //guardamos el último número de semana del mes, el cual incrementa sin reiniciar en cada mes.
    }

    $datos = trim($datos, ", ");
    $consultaRes = "SELECT $datos FROM $tabla";
    // echo $consultaRes."</br>";
    if ($resRes = $Link->query($consultaRes)) {
    
      if ($resRes->num_rows > 0) {
        if ($Res = $resRes->fetch_assoc()) {
          foreach ($semanasP as $semanaP) { //según el último número de semana guardado previamente, recorremos las semanas que nos devuelve el mes.
            $i = $semanaP;
            if (strlen($i) == 1) {
              $i = "0".$i;
            }

            if (isset($Res['semana_'.$i])) {

              if ($Res['semana_'.$i] == "") {
                $resSemana = 0;
              } else {
                $resSemana = $Res['semana_'.$i];
              }

                $totalesSemanas[$mes]['semana_'.$i] = $resSemana; //ordenamos una array para totales por semana del siguiente modo array[mes][semana] = total semana
                if (isset($sumTotalesSemanas['semana '.$i])) {
                  $sumTotalesSemanas['semana '.$i] += $resSemana;
                } else {
                  $sumTotalesSemanas['semana '.$i] = $resSemana;
                }
            }
          }
        }
      }
    }
  }


$tHeadSemana = '<tr>
     				<th>Mes</th>';
  $numTds = 1;
  $semanaAct = "";
  foreach ($totalesSemanas as $key => $mes) { 
    foreach ($mes as $semana => $totales) { //recorremos todas las semanas obtenidas para crear las columnas
      if ($semana != $semanaAct) { //Si la semana en turno es igual a la última semana guardada, no se crea otra columna
        $numTds++; //aumentamos en 1 el número de columnas creadas
      
		$tHeadSemana .=  '<th class="column_'.str_replace("semana_", "", $semana).' verGraficas" data-semana="'.str_replace("semana_", "", $semana).'">
			  '.ucwords(str_replace("_", " ", $semana)).'
			</th>';
      }
   $semanaAct=$semana; //Guardamos el último número de semana del mes (incrementable sin reinicio por mes)
    } 
  }
$tHeadSemana .= '<th>Total</th>
</tr>';
  // var_dump($totalesSemanas);

$tBodySemana="";
foreach ($totalesSemanas as $idMes => $mes) { 
$totalmes = 0;  
$tBodySemana .= "<tr>
					<td>".$mesesNom[$idMes]."</td>";
foreach ($semanasP as $semanaP) { //según el número de columnas creadas, recorremos las semanas obtenidas
  if (strlen($semanaP) == 1) {
    $l = "0".$semanaP;
  }
  $l = $semanaP;
  if (isset($mes["semana_".$l])) { //Si en el mes en turno, está la semana del recorrido "for" imprimimos el valor en la columna nueva.
	  $tBodySemana .= '<td class="column_'.$l.' verGraficas" data-semana="'.$l.'">
	    '.$mes["semana_".$l].'
	  </td>';
    $totalmes += $mes["semana_".$l];
  } else { //Si en el mes en turno, NO está la semana del recorrido "for" imprimimos la columna nueva vacía. 
    $tBodySemana .='<td class="column_'.$l.' verGraficas" data-semana="'.$l.'">0</td>';
  }
} 
$tBodySemana .="<th>".$totalmes."</th>
</tr>";
}

$tFootSemana ='<tr>
	<th>TOTAL</th>';
$tTotal = 0;

	foreach ($semanasP as $semanaP) {
    if (strlen($semanaP) == 1) {
      $l = "0".$semanaP;
    }
    $l = $semanaP;
    $tFootSemana .='<th class="column_'.$l.' verGraficas" data-semana="'.$l.'">
      '.$sumTotalesSemanas["semana ".$l].'
    </th>';
    $tTotal += $sumTotalesSemanas["semana ".$l]; 
	} 
$tFootSemana .='<th>'.$tTotal.'</th>
</tr>';

$data['thead'] = $tHeadSemana;
$data['tbody'] = $tBodySemana;
$data['tfoot'] = $tFootSemana;
$data['diasSemanas'] = $diasSemanas;
$data['tipoComplementos'] = $tipoComplementos;

echo json_encode($data);