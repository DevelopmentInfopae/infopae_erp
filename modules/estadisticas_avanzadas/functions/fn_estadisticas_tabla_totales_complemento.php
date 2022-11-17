<?php 

require_once '../../../config.php';
require_once '../../../db/conexion.php';
$periodoActual = $_SESSION['periodoActual'];
$mesesNom = array('01' => "Enero", 
                  "02" => "Febrero", 
                  "03" => "Marzo", 
                  "04" => "Abril", 
                  "05" => "Mayo", 
                  "06" => "Junio", 
                  "07" => "Julio", 
                  "08" => "Agosto", 
                  "09" => "Septiembre", 
                  "10" => "Octubre", 
                  "11" => "Noviembre", 
                  "12" => "Diciembre"
               );

$diasSemanas = [];
$consDiasSemanas = "SELECT GROUP_CONCAT(DIA) AS Dias, MES, SEMANA FROM planilla_semanas WHERE CONCAT(ANO, '-', MES, '-', DIA) <= '".date('Y-m-d')."' GROUP BY SEMANA";
$resDiasSemanas = $Link->query($consDiasSemanas);
if ($resDiasSemanas->num_rows > 0) {
   while ($dataDiasSemanas = $resDiasSemanas->fetch_assoc()) {
      $consultaTablas = "  SELECT 
                              table_name AS tabla
                           FROM 
                              information_schema.tables
                           WHERE 
                              table_schema = DATABASE() AND table_name = 'entregas_res_".$dataDiasSemanas['MES']."$periodoActual'";
      $resTablas = $Link->query($consultaTablas);
      if ($resTablas->num_rows > 0) {
         $semanaPos = str_replace("b", "", $dataDiasSemanas['SEMANA']);
         $arrDias = explode(",", $dataDiasSemanas['Dias']);
         sort($arrDias);
         $diasSemanas[$dataDiasSemanas['MES']][$semanaPos] = $arrDias; //obtenemos un array ordenado del siguiente modo array[mes][semana] = array[dias]
      }
   }
}

$tipoComplementos = [];
$consComplemento="SELECT * FROM tipo_complemento WHERE valorRacion > 0;";
$resComplemento = $Link->query($consComplemento);
if ($resComplemento->num_rows > 0) {
   while ($Complemento = $resComplemento->fetch_assoc()) {
      $tipoComplementos[] = $Complemento['CODIGO'];
   }
}

foreach ($diasSemanas as $mes => $semanas) { //recorremos los meses
   $datos = "";
   $diaD = 1;
   $sem=0;
   $tabla="entregas_res_$mes$periodoActual"; //tabla donde se busca, según mes(obtenido de consulta anterior) y año
   foreach ($semanas as $semana => $dias) { //recorremos las semanas del mes en turno
      foreach ($dias as $D => $dia) { //recorremos los días de la semana en turno
         $datos.="SUM(D$diaD) + ";
         $diaD++;
      }
      $datos = trim($datos, "+ ");
      $datos.= " AS semana_".$semana.", ";
      $sem = $semana; //guardamos el último número de semana del mes, el cual incrementa sin reiniciar en cada mes.
   }
   $datos = trim($datos, ", ");
   $consultaRes = "SELECT $datos FROM $tabla";
      
   foreach ($tipoComplementos as $key => $complemento) {
      $consultaResComplemento = "SELECT ";
      $diaD = 1;
      foreach ($semanas as $semana => $dias) {
         foreach ($dias as $D => $dia) {
            $consultaResComplemento.="SUM(D$diaD) + ";
            $diaD++;
         }
      }
      $consultaResComplemento = trim($consultaResComplemento, "+ ");
      $consultaResComplemento.=" AS TOTAL FROM $tabla WHERE tipo_complem = '$complemento'";
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
                  $totalesComplementosGrafica[intval($mes)][$complemento] += $ResComplemento2;
               } else {
                  $totalesComplementos[$mes][$complemento] = $ResComplemento2;
                  $totalesComplementosGrafica[intval($mes)][$complemento] = $ResComplemento2;
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

$tHeadComp = ' <tr>
                  <th>Mes</th>';
                  foreach ($tipoComplementos as $key => $complemento) {
                     $tHeadComp .= '<th>'.$complemento.'</th>';
                  }
                  $tHeadComp .= '<th>Total</th>
               </tr>';

$tBodyComp = "";
foreach ($totalesComplementos as $mes => $valoresMes) {
$totalMes = 0;
$tBodyComp .= '<tr>
                  <td>'.$mesesNom[$mes].'</td>';
                  foreach ($valoresMes as $complemento => $totales) {
                     $tBodyComp .= '<td>'.$totales.'</td>';
                     $totalMes += $totales;
                  }
                  $tBodyComp .="<th>".$totalMes."</th>
               <tr>";
}

$tFootComp = ' <tr>
                  <th>TOTAL</th>';
                  $tTotal = 0;
                  foreach ($sumTotalesComplementos as $complemento => $total) {
                     $tFootComp .='<th>'.$total.'</th>';
                     $tTotal += $total;
                  }
                  $tFootComp .='<th>'.$tTotal.'</th>
               </tr>';

$data['thead'] = $tHeadComp;
$data['tbody'] = $tBodyComp;
$data['tfoot'] = $tFootComp;
$data['info'] = $totalesComplementosGrafica;

echo json_encode($data);
 
