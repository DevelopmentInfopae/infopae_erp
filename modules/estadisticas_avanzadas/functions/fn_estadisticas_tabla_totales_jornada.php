<?php
require_once '../../../config.php';
require_once '../../../db/conexion.php';  

$periodoActual = $_SESSION['periodoActual'];

$mesesNom = array('1' => "Enero", "2" => "Febrero", "3" => "Marzo", "4" => "Abril", "5" => "Mayo", "6" => "Junio", "7" => "Julio", "8" => "Agosto", "9" => "Septiembre", "10" => "Octubre", "11" => "Noviembre", "12" => "Diciembre");

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

$consNomJornadas = "SELECT id, nombre FROM jornada";
$resNomJornadas = $Link->query($consNomJornadas);
if ($resNomJornadas->num_rows > 0) {
  while ($dataNomJornadas = $resNomJornadas->fetch_assoc()) {
    // exit(var_dump($dataNomJornadas));
    $nomJornadas[$dataNomJornadas['id']] = $dataNomJornadas['nombre'];
  }
}

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
    $consultaRes = "SELECT cod_jorn_est, $datos ";
    $consultaRes.=" AS TOTAL FROM $tabla GROUP BY cod_jorn_est";

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
$tHeadJornada = '<tr>
    <th>Jornada</th>';
     
    foreach ($arrayMes as $mes) {

        $tHeadJornada .= '<th>'.$mesesNom[$mes].'</th>';
    
    }
  $tHeadJornada .= '<th>Total</th>
  </tr>';


// cuerpo
  $tBodyJornada = "";
	
	$posicion = 0;
	$jornadas = [];	
	$totalJornada = [];

	foreach ($respuesta2 as $mes => $valoresMes) {
		foreach ($valoresMes as $valorMes => $valor) {
			// convertimos la respuesta a un array asociativo con la clave primaria edad mes
			$jornadas[$valor['cod_jorn_est']][$mes] = $valor['TOTAL'];	
		}
	}


  // funcion para llenar campos cuando haya  un dato en un mes y en otro no 
  foreach ($respuesta2 as $mes => $valoresMes) {
    foreach ($jornadas as $jornada => $valorJornada) {
      if (isset($jornadas[$jornada][$mes])) {
        continue;
      }else{
        $jornadas[$jornada][$mes] = '0';
      }
    }
    foreach ($valoresMes as $valorMes => $valor) {
    ksort($jornadas[$valor['cod_jorn_est']]);
    }
  } 

  // exit(var_dump($jornadas)); 

	$jornadaData = "";

	foreach ($jornadas as $jornada => $valorJornada) {

  $Letra = "B";
  $nombreJornada = '';
  $nombreTemporal = '';
  foreach ($nomJornadas as $idJornada => $valor) {
    if ($idJornada == $jornada) {
      $nombreJornada = $valor;
      $nombreTemporal = $nombreJornada;
    }
  }
  if ($nombreTemporal == '') {
     $nombreJornada = $jornada;
  }

		$tBodyJornada .= "<tr> <td>".$nombreJornada."</td>";

		$valorFila = 0;
		foreach ($valorJornada as $valores => $valor) {
				
				$valorFila += $valor;
			 	$tBodyJornada .= "<td>".$valor."</td>";
			 	$totalJornada[$nombreJornada]=$valorFila;			
		}
		$tBodyJornada .= "<th>" .$valorFila. "</th>"; 
		$tBodyJornada .= "</tr>";
	}

	// pie
	$tFootJornada = '<tr>
  					<th>TOTAL</th>';
  	$tTotal = 0;
  	$totalMes = ["01" => 0, "02" => 0, "03" => 0, "04" => 0, "05" => 0, "06" => 0, "07" => 0, "08" => 0, "09" => 0, "10" => 0, "11" => 0, "12" => 0];

  	foreach ($jornadas as $jornada => $valorJornada) {

  	foreach ($valorJornada as $mes => $valorMes) {
  		
  		$totalMes[$mes] += $valorMes;
  	}

  }

  // var_dump($totalMes);

  foreach ($totalMes as $total) {

  	if ($total <> 0) {
  		 $tFootJornada .='<th>'.$total.'</th>';
  		 $tTotal += $total;
  	}
  	
  }

   
$tFootJornada .='<th>'.$tTotal.'</th>
</tr>';

$data['thead'] = $tHeadJornada;
$data['tbody'] = $tBodyJornada;
$data['tfoot'] = $tFootJornada;
$data['info'] = $totalJornada;



echo json_encode($data);
