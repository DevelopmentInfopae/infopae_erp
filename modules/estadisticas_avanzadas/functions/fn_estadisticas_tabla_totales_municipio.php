<?php
require_once '../../../config.php';
require_once '../../../db/conexion.php';  

$periodoActual = $_SESSION['periodoActual'];

$mesesNom = array('1' => "Enero", "2" => "Febrero", "3" => "Marzo", "4" => "Abril", "5" => "Mayo", "6" => "Junio", "7" => "Julio", "8" => "Agosto", "9" => "Septiembre", "10" => "Octubre", "11" => "Noviembre", "12" => "Diciembre");

$codigoMunicipio = '';
$consCodigoMunicipio = "SELECT codMunicipio FROM parametros";
$resCodigoMunicipio = $Link->query($consCodigoMunicipio);
if ($resCodigoMunicipio->num_rows > 0) {
  while ($dataCodigoMunicipio = $resCodigoMunicipio->fetch_assoc()) {
    $codigoMunicipio = $dataCodigoMunicipio['codMunicipio'];
  }
}

// var_dump($codigoMunicipio);

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

// condicional para enviar datos cuando el codMunicipio esta vacio y se pueda crear el mapa de lo contrario listara las sedes educativas
if ($codigoMunicipio == '0') {
  $mesesRecorridos = ""; 
  $respuesta = [];
  $respuesta2 = [];
  $codDepartamento = $_SESSION['p_CodDepartamento'];

  foreach ($diasSemanas as $mes => $semanas) {
    $datos = "";
    $diaD = 1;
    $sem=0;
    //tabla donde se busca, según mes(obtenido de consulta anterior) y año
    $tabla="entregas_res_$mes$periodoActual"; 

    // ciclo para recorrer las semanas
    foreach ($semanas as $semana => $dias) {
      // ciclo para recorrer los dias de la semana
        foreach ($dias as $D => $dia) { 
        $datos.="SUM(D$diaD) + ";
        $diaD++;
      }
      $sem = $semana; //guardamos el último número de semana del mes, el cual incrementa sin reiniciar en cada mes.
    }

    $datos = trim($datos, "+ ");
    $consultaRes = "SELECT ubicacion.codigoDane, ubicacion.Ciudad, $datos ";
    $consultaRes.=" AS TOTAL FROM $tabla JOIN ubicacion ON $tabla.cod_mun_res = ubicacion.CodigoDane GROUP BY cod_mun_res";

    $periodo = 1;

    if ($resConsultaRes = $Link->query($consultaRes)) {
        if ($resConsultaRes->num_rows > 0) {
          while ($resEstrato = $resConsultaRes->fetch_assoc()) {
            $respuesta[$periodo] = $resEstrato;
          $periodo++;   

          }
          
        }
 
      }
    $respuesta2[$mes] = $respuesta;
    $mesesRecorridos .= $mes;
    }

    $arrayMes = explode("0", $mesesRecorridos);

    // funcion para quitar espacios vacios de un array
    foreach ($arrayMes as $key => $link) {
      if($link === '') 
        { 
        unset($arrayMes[$key]); 
        } 
    }

    // encabezado
    $tHeadMunicipio = '<tr>
        <th>Municipio</th>';
         
        foreach ($arrayMes as $mes) {

            $tHeadMunicipio .= '<th>'.$mesesNom[$mes].'</th>';
        
        }
      $tHeadMunicipio .= '<th>Total</th>
      </tr>';


      // cuerpo
      $tBodyMunicipio = "";
      
      $posicion = 0;
      $municipios = []; 
      $totalMunicipio = [];
      $codigos = [];
      $totalCodigo = [];
      $totalesMunicipio = [];

      foreach ($respuesta2 as $mes => $valoresMes) {

        foreach ($valoresMes as $valorMes => $valor) {
          // convertimos la respuesta a un array asociativo con la clave primaria edad mes
          $municipios[$valor['Ciudad']][$mes] = $valor['TOTAL'];  
          $codigos[$valor['codigoDane']][$mes] = $valor['TOTAL'];
        }
      }
      // var_dump($codigos);

      foreach ($municipios as $municipio => $valorMunicipio) {

        $tBodyMunicipio .= "<tr> <td>".$municipio."</td>";

        $valorFila = 0;
        foreach ($valorMunicipio as $valores => $valor) {
            
            $valorFila += $valor;
            $tBodyMunicipio .= "<td>".$valor."</td>";
            $totalMunicipio[$municipio]=$valorFila; 
        }

        $tBodyMunicipio .= "<th>" .$valorFila. "</th>"; 
        $tBodyMunicipio .= "</tr>";
      }

      $munTemp = '';

      foreach ($municipios as $municipio => $valorMunicipio) {
        $munTemp = $municipio;
        $varTemp = $valorMunicipio;
        foreach ($codigos as $codigo => $valorCodigo) {
          $valorFila = 0;
            foreach ($valorMunicipio as $valores => $valor) {      
            $valorFila += $valor;
              if (isset($totalesMunicipio[$codigo][0]) <> $varTemp) {
                $totalesMunicipio[$codigo][0] = $valorFila;
                $varTemp = '';
              }    
            }
            
            if (isset($totalesMunicipio[$codigo][1]) <> $munTemp) {
               $totalesMunicipio[$codigo][1] = $municipio;
               $munTemp = '';
            }   
        }  
      }

    // var_dump($totalesMunicipio);

      // pie
      $tFootMunicipio = '<tr>
                <th>TOTAL</th>';
        $tTotal = 0;
        $totalMes = ["01" => 0, "02" => 0, "03" => 0, "04" => 0, "05" => 0, "06" => 0, "07" => 0, "08" => 0, "09" => 0, "10" => 0, "11" => 0, "12" => 0];

        foreach ($municipios as $municipio => $valorMunicipio) {

        foreach ($valorMunicipio as $mes => $valorMes) {
          
          $totalMes[$mes] += $valorMes;
        }

      }


      foreach ($totalMes as $total) {

        if ($total <> 0) {
           $tFootMunicipio .='<th>'.$total.'</th>';
           $tTotal += $total;
        }
        
      }

       
    $tFootMunicipio .='<th>'.$tTotal.'</th>
    </tr>';

    $data['thead'] = $tHeadMunicipio;
    $data['tbody'] = $tBodyMunicipio;
    $data['tfoot'] = $tFootMunicipio;
    $data['info'] = $totalesMunicipio;
    $data['codDepartamento'] = $codDepartamento;
    $data['codMunicipio'] = $codigoMunicipio;

    echo json_encode($data);
}

else{

$mesesRecorridos = ""; 
$respuesta = [];
$respuesta2 = [];
$respuestaSedes = [];

foreach ($diasSemanas as $mes => $semanas) {
  $datos = "";
    $diaD = 1;
    $sem=0;
    //tabla donde se busca, según mes(obtenido de consulta anterior) y año
    $tabla="entregas_res_$mes$periodoActual"; 

    // ciclo para recorrer las semanas
    foreach ($semanas as $semana => $dias) {
      // ciclo para recorrer los dias de la semana
        foreach ($dias as $D => $dia) { 
        $datos.="SUM(D$diaD) + ";
        $diaD++;
      }
      $sem = $semana; //guardamos el último número de semana del mes, el cual incrementa sin reiniciar en cada mes.
    }

    $datos = trim($datos, "+ ");
    $consultaRes = "SELECT cod_sede, $datos ";
    $consultaRes.=" AS TOTAL FROM $tabla GROUP BY cod_sede";

    $periodo = 1; 

    // var_dump($consultaRes);   

  if ($resConsultaRes = $Link->query($consultaRes)) {
        if ($resConsultaRes->num_rows > 0) {
          while ($resEstrato = $resConsultaRes->fetch_assoc()) {
            $respuesta[$periodo] = $resEstrato;
          $periodo++;   

          }
        }
 
      }
    $respuesta2[$mes] = $respuesta;
  $mesesRecorridos .= $mes;

  // consulta en la que vamos a almacenar las sedes que se van a mostrar en la tabla 
  // $periodo2 = 0;
  $consultaSedes = "SELECT cod_sede, nom_sede FROM sedes$periodoActual";
  if ($resConsultaSedes = $Link->query($consultaSedes)) {
     if ($resConsultaSedes->num_rows > 0) {
       while ($resSedes = $resConsultaSedes->fetch_assoc()) {
         $respuestaSedes[$resSedes['cod_sede']] = $resSedes['nom_sede'];
       }
     }
  }
}

$arrayMes = explode("0", $mesesRecorridos);

// funcion para quitar espacios vacios de un array
foreach ($arrayMes as $key => $link) {
  if($link === '') 
    { 
        unset($arrayMes[$key]); 
    } 
}

// encabezado
$tHeadSedes = '<tr>
    <th>Sedes educativas</th>';
     
    foreach ($arrayMes as $mes) {

        $tHeadSedes .= '<th>'.$mesesNom[$mes].'</th>';
    
    }
  $tHeadSedes .= '<th>Total</th>
  </tr>';

 // cuerpo
  $tBodySedes = "";
  
  $posicion = 0;
  $sedes = []; 
  $totalSedes = [];

  foreach ($respuesta2 as $mes => $valoresMes) {
    foreach ($valoresMes as $valorMes => $valor) {
      // convertimos la respuesta a un array asociativo con la clave primaria edad mes
      $sedes[$valor['cod_sede']][$mes] = $valor['TOTAL']; 

    }
  }

foreach ($sedes as $sede => $valorSede) {
  foreach ($respuestaSedes as $codigoSede => $nombre) {
     if ($sede == $codigoSede) {
       $nombreSede = $nombre;
     }
  }
    $tBodySedes .= "<tr> <td>".$nombreSede."</td>";

    $valorFila = 0;
    foreach ($valorSede as $valores => $valor) {
        
        $valorFila += $valor;
        $tBodySedes .= "<td>".$valor."</td>";
        $totalSedes[$nombreSede]=$valorFila;
    }
    $tBodySedes .= "<th>" .$valorFila. "</th>"; 
    $tBodySedes .= "</tr>";    
}

// var_dump($tBodySedes);
// pie
  $tFootSedes = '<tr>
            <th>TOTAL</th>';
    $tTotal = 0;
    $totalMes = ["01" => 0, "02" => 0, "03" => 0, "04" => 0, "05" => 0, "06" => 0, "07" => 0, "08" => 0, "09" => 0, "10" => 0, "11" => 0, "12" => 0];

    foreach ($sedes as $sede => $valorSede) {

    foreach ($valorSede as $mes => $valorMes) {
      
      $totalMes[$mes] += $valorMes;
    }

  }



  foreach ($totalMes as $total) {

    if ($total <> 0) {
       $tFootSedes .='<th>'.$total.'</th>';
       $tTotal += $total;
    }
    
  }

   
$tFootSedes .='<th>'.$tTotal.'</th>
</tr>';


$data['thead'] = $tHeadSedes;
$data['tbody'] = $tBodySedes;
$data['tfoot'] = $tFootSedes;
$data['info'] = $totalSedes;
$data['codMunicipio'] = $codigoMunicipio;

echo json_encode($data);  
  

}









