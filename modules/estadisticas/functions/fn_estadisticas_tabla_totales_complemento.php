<?php
  // require_once 'fn_estadisticas_head_functions.php';
  require_once '../../../config.php';
  require_once '../../../db/conexion.php';
  $periodoActual = $_SESSION['periodoActual'];
  $mesesNom = array('1' => "Enero", "2" => "Febrero", "3" => "Marzo", "4" => "Abril", "5" => "Mayo", "6" => "Junio", "7" => "Julio", "8" => "Agosto", "9" => "Septiembre", "10" => "Octubre", "11" => "Noviembre", "12" => "Diciembre");

  $diasSemanas = $_POST['diasSemanas'];
  $tipoComplementos = $_POST['tipoComplementos'];
  sort($tipoComplementos);

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

    $mes = (int) $mes;
    $totalesComplementos[$mes]["APS"] = 0;
    $totalesComplementos[$mes]["CAJMPS"] = 0;
    $totalesComplementos[$mes]["CAJMRI"] = 0;
    $totalesComplementos[$mes]["CAJTPS"] = 0;
    $totalesComplementos[$mes]["CAJTRI"] = 0;
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
$tipoComplementos = [];
foreach ($totalesComplementos as $mes => $valoresMes) {
  foreach ($valoresMes as $complemento => $totales) {
    $tipoComplementos[$complemento] = 1;
  }
}

  $tHeadComp = '<tr>
                  <th>Mes</th>';
    foreach ($tipoComplementos as $complemento => $setted) {
        $tHeadComp .= '<th>'.$complemento.'</th>';
    }
  $tHeadComp .= '</tr>';

  $tBodyComp = "";
    foreach ($totalesComplementos as $mes => $valoresMes) {
     $tBodyComp .= '<tr>
                    <td>'.$mesesNom[$mes].'</td>';
      foreach ($valoresMes as $complemento => $totales) {
       $tBodyComp .= '<td>'.$totales.'</td>';

      }
      $tBodyComp .="<tr>";
    }

  $tFootComp = '<tr>
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