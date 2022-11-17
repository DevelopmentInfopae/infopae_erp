<?php 
require_once '../../../config.php';
require_once '../../../db/conexion.php'; 
require_once 'fn_estadisticas_functions.php';

$periodoActual = $_SESSION['periodoActual'];
$diasSemanas = buscar_dias_semanas($Link, $periodoActual);
$tipoComplementos = buscar_tipo_complementos($Link);
foreach ($diasSemanas as $key => $value) {
   foreach ($value as $key2 => $value2) {
      $semanasP[$key2] = $key2;  
   }
}
// exit(var_dump($diasSemanas));
$totalesSemanas = [];
$sumTotalesSemanas = [];
foreach ($diasSemanas as $mes => $semanas) { //recorremos los meses
   $datos = "";
   $diaD = 1;
   $tabla="entregas_res_$mes$periodoActual"; //tabla donde se busca, según mes(obtenido de consulta anterior) y año
   foreach ($semanas as $semana => $dias) { //recorremos las semanas del mes en turno
      foreach ($dias as $D => $dia) { //recorremos los días de la semana en turno
         $consultaPlanillaDias = "SELECT D$diaD FROM planilla_dias WHERE D$diaD = $dia AND mes = $mes;";
         $respuestaConsultaPlanillaDias = $Link->query($consultaPlanillaDias);
         if ($respuestaConsultaPlanillaDias->num_rows == 1) {
            $datos.="SUM(D$diaD) + ";
            $diaD++;
         }
      }
      $datos = trim($datos, "+ ");
      $datos.= " AS semana_".$semana.", ";
   }

   $datos = trim($datos, ", ");
   $consultaRes = "SELECT $datos FROM $tabla";
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
			                  '.ucwords(str_replace("_", " ", $semana)).'</th>';
                        }
                        $semanaAct=$semana; //Guardamos el último número de semana del mes (incrementable sin reinicio por mes)
                     }  
                  }
                  $tHeadSemana .= '<th>Total</th>
               </tr>';

$tBodySemana="";
foreach ($totalesSemanas as $idMes => $mes) { 
   $totalmes = 0;  
   $tBodySemana .= " <tr>
					         <td>".buscar_meses_nombre(intval($idMes))."</td>";
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

$tFootSemana ='   <tr>
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