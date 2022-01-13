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

    $consultaEtnia = "SELECT id, descripcion FROM etnia";
    if ($resConsultaEtnia = $Link->query($consultaEtnia)) {
      while ($resEtnia = $resConsultaEtnia->fetch_assoc()) {
        $respuestaEtnias[$resEtnia['id']] = $resEtnia['descripcion'];
      }
    }

    $consultaRes = "SELECT etnia, $datos ";
    $consultaRes.=" AS TOTAL FROM $tabla GROUP BY etnia";

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
$tHeadEtnia = '<tr>
    <th>Etnia</th>';
     
    foreach ($arrayMes as $mes) {

        $tHeadEtnia .= '<th>'.$mesesNom[$mes].'</th>';
    
    }
  $tHeadEtnia .= '<th>Total</th>
  </tr>';


  // cuerpo
  $tBodyEtnia = "";
	
	$posicion = 0;
	$etnias = [];	
	$totalEtnia = [];

    // funcion para capturar los valores que existen en entregasres y no en la tabla discapacidades
  foreach ($respuesta2 as $mes => $valoresMes) {
    foreach ($valoresMes as $valorMes => $valor) {
      $idTemporal = '';
      foreach ($respuestaEtnias as $id => $nombre) {      
        if ($valor['etnia'] == $id) {
          $etnias[$nombre][$mes] = $valor['TOTAL']; 
          $idTemporal = $id; 
        }
      }
      if ($idTemporal == '') {
        $etnias[$valor['etnia']][$mes] = $valor['TOTAL'];
      }
    }
  }


	// foreach ($respuesta2 as $mes => $valoresMes) {
	// 	foreach ($valoresMes as $valorMes => $valor) {
	// 		// convertimos la respuesta a un array asociativo con la clave primaria edad mes
	// 		$etnias[$valor['descripcion']][$mes] = $valor['TOTAL'];	
	// 	}
	// }

  // funcion para llenar campos cuando haya  un dato en un mes y en otro no 
  foreach ($respuesta2 as $mes => $valoresMes) {
    foreach ($etnias as $etnia => $valorEtnia) {
      if (isset($etnias[$etnia][$mes])) {
        continue;
        ksort($etnias[$etnia]);
      }else{
        $etnias[$etnia][$mes] = '0';
        ksort($etnias[$etnia]);
      }
    }
  } 

	foreach ($etnias as $etnia => $valorEtnia) {
    $etniaString = ucfirst(strtoupper($etnia));
    utf8_decode($etniaString);
		$tBodyEtnia .= "<tr> <td>".$etniaString."</td>";

		$valorFila = 0;
		foreach ($valorEtnia as $valores => $valor) {
				
				$valorFila += $valor;
			 	$tBodyEtnia .= "<td>".$valor."</td>";
			 	$totalEtnia[$etniaString]=$valorFila;			
		}
		$tBodyEtnia .= "<th>" .$valorFila. "</th>"; 
		$tBodyEtnia .= "</tr>";
	}

	// pie
	$tFootEtnia = '<tr>
  					<th>TOTAL</th>';
  	$tTotal = 0;
  	$totalMes = ["01" => 0, "02" => 0, "03" => 0, "04" => 0, "05" => 0, "06" => 0, "07" => 0, "08" => 0, "09" => 0, "10" => 0, "11" => 0, "12" => 0];

  	foreach ($etnias as $etnia => $valorEtnia) {

  	foreach ($valorEtnia as $mes => $valorMes) {
  		
  		$totalMes[$mes] += $valorMes;
  	}

  }


  foreach ($totalMes as $total) {

  	if ($total <> 0) {
  		 $tFootEtnia .='<th>'.$total.'</th>';
  		 $tTotal += $total;
  	}
  	
  }

   
$tFootEtnia .='<th>'.$tTotal.'</th>
</tr>';

$data['thead'] = $tHeadEtnia;
$data['tbody'] = $tBodyEtnia;
$data['tfoot'] = $tFootEtnia;
$data['info'] = $totalEtnia;

echo json_encode($data);

