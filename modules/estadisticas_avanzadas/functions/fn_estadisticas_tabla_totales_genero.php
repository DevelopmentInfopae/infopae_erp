<?php 
// require_once 'fn_estadisticas_head_functions.php';
require_once '../../../config.php';
require_once '../../../db/conexion.php';
$periodoActual = $_SESSION['periodoActual'];
$mesesNom = array('01' => "Enero", "02" => "Febrero", "03" => "Marzo", "04" => "Abril", "05" => "Mayo", "06" => "Junio", "07" => "Julio", "08" => "Agosto", "09" => "Septiembre", "10" => "Octubre", "11" => "Noviembre", "12" => "Diciembre");


  $diasSemanas = $_POST['diasSemanas'];

  foreach ($diasSemanas as $mes => $semanas) { //recorremos los meses
    $datos = "";
    $diaD = 1;
    $sem=0;
    $tabla="entregas_res_$mes$periodoActual"; //tabla donde se busca, según mes(obtenido de consulta anterior) y año
    foreach ($semanas as $semana => $dias) { //recorremos las semanas del mes en turno
      foreach ($dias as $D => $dia) { //recorremos los días de la semana en turno
        // echo $mes." - ".$semana." - ".$D." - ".$dia."</br>";
        $datos.="SUM(D$diaD) + ";
        $diaD++;
      }
      $datos = trim($datos, "+ ");
      $datos.= " AS semana_".$semana.", ";
      $sem = $semana; //guardamos el último número de semana del mes, el cual incrementa sin reiniciar en cada mes.
    }
    $datos = trim($datos, ", ");
    $consultaRes = "SELECT $datos FROM $tabla";
    // echo $consultaRes."</br>";
    $sqlDias="";
    foreach ($semanas as $semana => $dias) {
      foreach ($dias as $D => $dia) {
        $sqlDias.="SUM(D$diaD) + ";
        $diaD++;
      }
    }

    $sqlDias = trim($sqlDias, " +");

    $consultaGeneros = "SELECT genero, $sqlDias AS total FROM $tabla GROUP BY genero;";
    // echo $consultaGeneros;
    $resGeneros = $Link->query($consultaGeneros);
    if ($resGeneros->num_rows > 0) {
      while ($resG = $resGeneros->fetch_assoc()) {
        if (isset($totalGeneros[$resG['genero']])) {
          $totalGeneros[$resG['genero']] += $resG['total'];
        } else {
          $totalGeneros[$resG['genero']] = $resG['total'];
        }
      }
    }

  }

$tHeadGenero = "<tr>
                    <th>Género</th>
                    <th>Total</th>
                  </tr>";

$tBodyGenero = "";

$tTotal = 0;

foreach ($totalGeneros as $genero=> $total) {

  if ($genero == "F") {
    $gText = "Femenino";
  } else if ($genero == "M") {
    $gText = "Masculino";
  } else if ($genero == "-") {
    $gText = "En blanco";
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
