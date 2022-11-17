<?php
require_once '../../../config.php';
require_once '../../../db/conexion.php';
require_once 'fn_estadisticas_functions.php';

$periodoActual = $_SESSION['periodoActual'];
$diasSemanas = buscar_dias_semanas($Link, $periodoActual);
$tipoComplementos = buscar_tipo_complementos($Link);
sort($tipoComplementos);

foreach ($diasSemanas as $mes => $semanas) { //recorremos los meses
   $datos = "";
   $diaD = 1;
   $tabla="entregas_res_$mes$periodoActual"; //tabla donde se busca, según mes(obtenido de consulta anterior) y año
   $mes = intval($mes);
   foreach ($tipoComplementos as $key => $complemento) {
      $totalesComplementos[$mes][$complemento] = 0;
      $consultaResComplemento = "SELECT ";
      $diaD = 1;
      foreach ($semanas as $semana => $dias) {
         foreach ($dias as $D => $dia) {
            $consultaResComplemento.="SUM(D$diaD) + ";
            $diaD++;
         }
      }
      $consultaResComplemento = trim($consultaResComplemento, "+ ");
      $consultaResComplemento.=" AS TOTAL FROM $tabla WHERE tipo_complem = '$complemento' GROUP BY tipo_complem";
      if ($resResComplemento = $Link->query($consultaResComplemento)) {
         if ($resResComplemento->num_rows > 0) {
            if ($ResComplemento = $resResComplemento->fetch_assoc()) {
               if ($ResComplemento['TOTAL'] == "") {
                  $ResComplemento2 = 0;
               } else {
                  $ResComplemento2 = $ResComplemento['TOTAL'];
               }
               if (isset($totalesComplementos[$mes][$complemento])) {
                  $totalesComplementos[$mes][$complemento] += $ResComplemento2;
               } else {
                  $totalesComplementos[$mes][$complemento] = $ResComplemento2;
               }
               if (isset($sumTotalesComplementos[$complemento])) {
                  $sumTotalesComplementos[$complemento] += $ResComplemento2;
               } else {
                  $sumTotalesComplementos[$complemento] = $ResComplemento2;
               }
            }
         }
      }
   }
}

ksort($totalesComplementos);
// exit(var_dump($totalesComplementos));
$tipoComplementos = [];
foreach ($totalesComplementos as $mes => $valoresMes) {
   foreach ($valoresMes as $complemento => $totales) {
      $tipoComplementos[$complemento] = 1;
   }
}

$tHeadComp = ' <tr>
                  <th>Mes</th>';
                  foreach ($tipoComplementos as $complemento => $setted) {
                     $tHeadComp .= '<th>'.$complemento.'</th>';
                  }
$tHeadComp .= '</tr>';

$tBodyComp = "";
foreach ($totalesComplementos as $mes => $valoresMes) {
   // var_dump(buscar_meses_nombre($mes));
   $tBodyComp .= '<tr>
                  <td>'.buscar_meses_nombre(intval($mes)).'</td>';
   foreach ($valoresMes as $complemento => $totales) {
      $tBodyComp .= '<td>'.$totales.'</td>';
   }
   $tBodyComp .="<tr>";
   }

$tFootComp = ' <tr>
                  <th>TOTAL</th>';
                  foreach ($tipoComplementos as $complemento => $setted) {
                     if (isset($sumTotalesComplementos[$complemento])) {
                        $tFootComp .='<th>'.$sumTotalesComplementos[$complemento].'</th>';
                     } else {
                        $tFootComp.="<th>0</th>";
                     }
                  }
                  $tFootComp .='</tr>';
$data['thead'] = $tHeadComp;
$data['tbody'] = $tBodyComp;
$data['tfoot'] = $tFootComp;
$data['info'] = $totalesComplementos;

echo json_encode($data);