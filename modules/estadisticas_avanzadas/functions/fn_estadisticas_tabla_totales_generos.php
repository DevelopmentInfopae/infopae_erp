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

$consDiasSemanas = "SELECT GROUP_CONCAT(DIA) AS Dias, 
                           MES, 
                           SEMANA 
                        FROM planilla_semanas 
                        WHERE CONCAT(ANO, '-', MES, '-', DIA) <= '".date('Y-m-d')."' 
                        GROUP BY SEMANA";

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

$mesesRecorridos = ""; 
$respuesta2 = [];
foreach ($diasSemanas as $mes => $semanas) {
   $datos = "";
   $diaD = 1;
   $sem=0;
   $tabla="entregas_res_$mes$periodoActual";  //tabla donde se busca, según mes(obtenido de consulta anterior) y año
   foreach ($semanas as $semana => $dias) {  // ciclo para recorrer las semanas
      foreach ($dias as $D => $dia) {  // ciclo para recorrer los dias de la semana
         $datos.="SUM(D$diaD) + ";
         $diaD++;
      }
      $sem = $semana; //guardamos el último número de semana del mes, el cual incrementa sin reiniciar en cada mes.
   }
   $datos = trim($datos, "+ ");
   $consultaRes = "SELECT genero, $datos ";
   $consultaRes.=" AS TOTAL FROM $tabla GROUP BY genero";
   $periodo = 1;
   $respuesta = [];
  
   if ($resConsultaRes = $Link->query($consultaRes)) {
      if ($resConsultaRes->num_rows > 0) {
         while ($ResEdad = $resConsultaRes->fetch_assoc()) {
            $respuesta[$periodo] = $ResEdad;
            $periodo++;   
         }      
      }
   }
   $respuesta2[$mes] = $respuesta;
   $mesesRecorridos .= $mes.' '; 
}
$mesesRecorridos = trim($mesesRecorridos,' ');
$arrayMes = explode(" ", $mesesRecorridos);

// funcion para quitar espacios vacios de un array
foreach ($arrayMes as $key => $link) {
   if($link === '')  { 
      unset($arrayMes[$key]); 
   } 
}

// encabezado
$tHeadGeneros = ' <tr>
                     <th>Género</th>';
                     foreach ($arrayMes as $mes) {
                        $tHeadGeneros .= '<th>'.$mesesNom[$mes].'</th>';
    
                     }
                     $tHeadGeneros .= '<th>Total</th>
                  </tr>';

// cuerpo
$tBodyGeneros = "";
$posicion = 0;
$genero = []; 
$totalGenero = [];

foreach ($respuesta2 as $mes => $valoresMes) {
   foreach ($valoresMes as $valorMes => $valor) {
      $generos[$valor['genero']][$mes] = $valor['TOTAL'];    // convertimos la respuesta a un array asociativo con la clave primaria edad mes
   }
}

// funcion para llenar campos cuando haya  un dato en un mes y en otro no 
foreach ($respuesta2 as $mes => $valoresMes) {
   foreach ($generos as $genero => $valorGenero) {
      if (isset($generos[$genero][$mes])) {
        continue;
      }else{
        $generos[$genero][$mes] = '0';
      }  
   }
   foreach ($valoresMes as $valorMes => $valor) {
      ksort($generos[$valor['genero']]);
   }
}

$stringGenero = '';
foreach ($generos as $genero => $valorGenero) {
   if ($genero == 'F') {
      $stringGenero = 'Femenino';
   }
   else if ($genero == 'M') {
      $stringGenero = 'Masculino';
   }else{
      $stringGenero = $genero;
   }
   $tBodyGeneros .= "<tr> <td>".$stringGenero."</td>";
   $valorFila = 0;
   foreach ($valorGenero as $valores => $valor) {     
      $valorFila += $valor;
      $tBodyGeneros .= "<td>".$valor."</td>";
      $totalGenero[$stringGenero]=$valorFila;     
   }
   $tBodyGeneros .= "<th>" .$valorFila. "</th>"; 
   $tBodyGeneros .= "</tr>";
}

// pie
$tFootGeneros = ' <tr>
                     <th>TOTAL</th>';
                     $tTotal = 0;
                     $totalMes = ["01" => 0, "02" => 0, "03" => 0, "04" => 0, "05" => 0, "06" => 0, "07" => 0, "08" => 0, "09" => 0, "10" => 0, "11" => 0, "12" => 0];
                     foreach ($generos as $genero => $valorGenero) {
                        foreach ($valorGenero as $mes => $valorMes) {
                           $totalMes[$mes] += $valorMes;
                        }
                     }
                     foreach ($totalMes as $total) {
                        if ($total <> 0) {
                           $tFootGeneros .='<th>'.$total.'</th>';
                           $tTotal += $total;
                        }
                     } 
                     $tFootGeneros .='<th>'.$tTotal.'</th>
                  </tr>';

$data['thead'] = $tHeadGeneros;
$data['tbody'] = $tBodyGeneros;
$data['tfoot'] = $tFootGeneros;
$data['info'] = $totalGenero;

echo json_encode($data);
