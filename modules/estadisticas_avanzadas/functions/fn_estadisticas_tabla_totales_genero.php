<?php 
// require_once 'fn_estadisticas_head_functions.php';
require_once '../../../config.php';
require_once '../../../db/conexion.php';
$periodoActual = $_SESSION['periodoActual'];
$mesesNom = array('01' => "Enero", "02" => "Febrero", "03" => "Marzo", "04" => "Abril", "05" => "Mayo", "06" => "Junio", "07" => "Julio", "08" => "Agosto", "09" => "Septiembre", "10" => "Octubre", "11" => "Noviembre", "12" => "Diciembre");

$diasSemanas = [];
  $consDiasSemanas = "SELECT GROUP_CONCAT(DIA) AS Dias, MES, SEMANA FROM planilla_semanas WHERE CONCAT(ANO, '-', MES, '-', DIA) <= '".date('Y-m-d')."' GROUP BY SEMANA";
  // echo $consDiasSemanas;
  $resDiasSemanas = $Link->query($consDiasSemanas);
  if ($resDiasSemanas->num_rows > 0) {
    while ($dataDiasSemanas = $resDiasSemanas->fetch_assoc()) {

      $consultaTablas = "SELECT 
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
        // echo ($arrDias);
        $diasSemanas[$dataDiasSemanas['MES']][$semanaPos] = $arrDias; //obtenemos un array ordenado del siguiente modo array[mes][semana] = array[dias]
      }
    }
  }

  // $diasSemanas = $_POST['diasSemanas'];

  foreach ($diasSemanas as $mes => $semanas) { //recorremos los meses
    $datos = "";
    $diaD = 1;
    $sem=0;
    $diaDD = 1;
    $tabla="entregas_res_$mes$periodoActual"; //tabla donde se busca, según mes(obtenido de consulta anterior) y año
    foreach ($semanas as $semana => $dias) { //recorremos las semanas del mes en turno
      foreach ($dias as $D => $dia) { //recorremos los días de la semana en turno
        // echo $mes." - ".$semana." - ".$D." - ".$dia."</br>";
        $datos.="SUM(D$diaD) + ";
        $diaD++;
      }
      // echo $datos;
      $datos = trim($datos, "+ ");
      $datos.= " AS semana_".$semana.", ";
      // echo $datos;
      $sem = $semana; //guardamos el último número de semana del mes, el cual incrementa sin reiniciar en cada mes.
    }
    $datos = trim($datos, ", ");
    $consultaRes = "SELECT $datos FROM $tabla";
    // echo $consultaRes."</br>";
    $sqlDias="";
    foreach ($semanas as $semana => $dias) {
      foreach ($dias as $D => $dia) {
        $sqlDias.="SUM(D$diaDD) + ";
        // echo $sqlDias;
        $diaDD++;
      }
    }

    $sqlDias = trim($sqlDias, " +");
    // echo $sqlDias;

    $consultaGeneros = "SELECT genero, $sqlDias AS total FROM $tabla GROUP BY genero;";
    // echo $consultaGeneros;  
    $resGeneros = $Link->query($consultaGeneros);
    if ($resGeneros->num_rows > 0) {
      while ($resG = $resGeneros->fetch_assoc()) {
        if (isset($totalGeneros[$resG['genero']])) {
          $totalGeneros[$resG['genero']] += $resG['total'];
          // var_dump($totalGeneros) ;
        } else {
          $totalGeneros[$resG['genero']] = $resG['total'];
          // var_dump($totalGeneros) ;
        }
      }
    }

  }

// exit(var_dump($consultaGeneros));  

$tHeadGenero = "<tr>
                    <th>Género</th>
                    <th>Total</th>
                  </tr>";

$tBodyGenero = "";

$tTotal = 0;

foreach ($totalGeneros as $genero=> $total) {
// var_dump($totalGeneros);
  if ($genero == "F") {
    $gText = "Femenino";
  } else if ($genero == "M") {
    $gText = "Masculino";
  } else {
    $gText = $genero;
  }

  $tBodyGenero.="<tr>
                  <td>".$gText."</td>
                  <td>".$total."</td>
                </tr>";
  $tTotal += is_null($total) ? 0 : $total;
}

$tFootGenero = "<tr>
                  <th>Total</th>
                  <th>".$tTotal."</th>
                </tr>";


$data['thead'] = $tHeadGenero;
$data['tbody'] = $tBodyGenero;
$data['tfoot'] = $tFootGenero;
$data['info'] = $totalGeneros;

echo json_encode($data);
