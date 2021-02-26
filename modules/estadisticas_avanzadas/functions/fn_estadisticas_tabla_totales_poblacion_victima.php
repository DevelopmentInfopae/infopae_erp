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
$respuesta = [];
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
    $consultaRes = "SELECT pobvictima.nombre, $datos ";
    $consultaRes.=" AS TOTAL FROM $tabla JOIN pobvictima ON $tabla.cod_pob_victima = pobvictima.id GROUP BY cod_pob_victima";

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
$tHeadVictima = '<tr>
    <th>Población victima</th>';
     
    foreach ($arrayMes as $mes) {

        $tHeadVictima .= '<th>'.$mesesNom[$mes].'</th>';
    
    }
  $tHeadVictima .= '<th>Total</th>
  </tr>';


  // cuerpo
  $tBodyVictima = "";
	
	$posicion = 0;
	$victimas = [];	
	$totalVictima = [];

	foreach ($respuesta2 as $mes => $valoresMes) {

		foreach ($valoresMes as $valorMes => $valor) {
			// convertimos la respuesta a un array asociativo con la clave primaria edad mes
			$victimas[$valor['nombre']][$mes] = $valor['TOTAL'];	

		}
	}

	foreach ($victimas as $victima => $valorVictima) {

		$tBodyVictima .= "<tr> <td>".$victima."</td>";

		$valorFila = 0;
		foreach ($valorVictima as $valores => $valor) {
				
				$valorFila += $valor;
			 	$tBodyVictima .= "<td>".$valor."</td>";
			 	$totalVictima[$victima]=$valorFila;			
		}
		$tBodyVictima .= "<th>" .$valorFila. "</th>"; 
		$tBodyVictima .= "</tr>";
	}

	// pie
	$tFootVictima = '<tr>
  					<th>TOTAL</th>';
  	$tTotal = 0;
  	$totalMes = ["01" => 0, "02" => 0, "03" => 0, "04" => 0, "05" => 0, "06" => 0, "07" => 0, "08" => 0, "09" => 0, "10" => 0, "11" => 0, "12" => 0];

  	foreach ($victimas as $victima => $valorVictima) {

  	foreach ($valorVictima as $mes => $valorMes) {
  		
  		$totalMes[$mes] += $valorMes;
  	}

  }


  foreach ($totalMes as $total) {

  	if ($total <> 0) {
  		 $tFootVictima .='<th>'.$total.'</th>';
  		 $tTotal += $total;
  	}
  	
  }

   
$tFootVictima .='<th>'.$tTotal.'</th>
</tr>';

$data['thead'] = $tHeadVictima;
$data['tbody'] = $tBodyVictima;
$data['tfoot'] = $tFootVictima;
$data['info'] = $totalVictima;

echo json_encode($data);



