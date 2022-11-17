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

// $diasSemanas = $_POST['diasSemanas'];

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


$mesesRecorridos = ""; 

$respuesta2 = [];

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
    $consultaRes = "SELECT cod_estrato, $datos ";
    $consultaRes.=" AS TOTAL FROM $tabla GROUP BY cod_estrato";

    $periodo = 1;
    $respuesta = [];
	
	if ($resConsultaRes = $Link->query($consultaRes)) {
        if ($resConsultaRes->num_rows > 0) {
          while ($resEstrato = $resConsultaRes->fetch_assoc()) {
          	$respuesta[$periodo] = $resEstrato;
	        $periodo++;  	

          }
          
        }
 
      }
    $respuesta2[$mes] = $respuesta;
	$mesesRecorridos .= $mes . ' '; 	
}
$mesesRecorridos = trim($mesesRecorridos, ' ');
$arrayMes = explode(" ", $mesesRecorridos);

// funcion para quitar espacios vacios de un array
foreach ($arrayMes as $key => $link) {
	if($link === '') 
    { 
        unset($arrayMes[$key]); 
    } 
}

// encabezado
$tHeadEstrato = '<tr>
    <th>Estrato</th>';
     
    foreach ($arrayMes as $mes) {

        $tHeadEstrato .= '<th>'.$mesesNom[$mes].'</th>';
    
    }
  $tHeadEstrato .= '<th>Total</th>
  </tr>';


// cuerpo
  $tBodyEstrato = "";
	
	$posicion = 0;
	$estratos = [];	
	$totalEstrato = [];

	foreach ($respuesta2 as $mes => $valoresMes) {
		foreach ($valoresMes as $valorMes => $valor) {
			// convertimos la respuesta a un array asociativo con la clave primaria edad mes
			$estratos[$valor['cod_estrato']][$mes] = $valor['TOTAL'];	
		}
	}

  // funcion para llenar campos cuando haya  un dato en un mes y en otro no 
  foreach ($respuesta2 as $mes => $valoresMes) {
    foreach ($estratos as $estrato => $valorEstrato) {
      if (isset($estratos[$estrato][$mes])) {
        continue;
      }else{
        $estratos[$estrato][$mes] = '0';
      }
    }
    foreach ($valoresMes as $valorMes => $valor) {
    ksort($estratos[$valor['cod_estrato']]);
    }
  } 

	$estratoDato = "";
	foreach ($estratos as $estrato => $valorEstrato) {
		if ($estrato == 99 || $estrato == 9 ) {
			$estratoDato = "No aplica";
		}else{
			$estratoDato = "Estrato ".$estrato;
		}

		$tBodyEstrato .= "<tr> <td>".$estratoDato."</td>";

		$valorFila = 0;
		foreach ($valorEstrato as $valores => $valor) {
				
				$valorFila += $valor;
			 	$tBodyEstrato .= "<td>".$valor."</td>";
			 	$totalEstrato[$estratoDato]=$valorFila;			
		}
		$tBodyEstrato .= "<th>" .$valorFila. "</th>"; 
		$tBodyEstrato .= "</tr>";
	}


	// pie
	$tFootEstrato = '<tr>
  					<th>TOTAL</th>';
  	$tTotal = 0;
  	$totalMes = ["01" => 0, "02" => 0, "03" => 0, "04" => 0, "05" => 0, "06" => 0, "07" => 0, "08" => 0, "09" => 0, "10" => 0, "11" => 0, "12" => 0];

  	foreach ($estratos as $estrato => $valorEstrato) {

  	foreach ($valorEstrato as $mes => $valorMes) {
  		
  		$totalMes[$mes] += $valorMes;
  	}

  }

  // var_dump($totalMes);

  foreach ($totalMes as $total) {

  	if ($total <> 0) {
  		 $tFootEstrato .='<th>'.$total.'</th>';
  		 $tTotal += $total;
  	}
  	
  }

   
$tFootEstrato .='<th>'.$tTotal.'</th>
</tr>';

$data['thead'] = $tHeadEstrato;
$data['tbody'] = $tBodyEstrato;
$data['tfoot'] = $tFootEstrato;
$data['info'] = $totalEstrato;



echo json_encode($data);
