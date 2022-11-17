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

$mesesRecorridos = ""; 

$respuesta2 = [];


// ciclo para recorrer los meses
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
    $consultaRes = "SELECT edad, $datos ";
    $consultaRes.=" AS TOTAL FROM $tabla GROUP BY edad ORDER BY convert(edad, UNSIGNED)";

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
	  $mesesRecorridos .= $mes .' '; 
}
$mesesRecorridos = trim($mesesRecorridos, ' ');

// convertirmos el string en un array 
$arrayMes = explode(" ", $mesesRecorridos);

// funcion para quitar espacios vacios de un array
foreach ($arrayMes as $key => $link) {
	if($link === '') 
    { 
        unset($arrayMes[$key]); 
    } 
}

// encabezado
$tHeadEdad = '<tr>
    <th>Edad</th>';
     
    foreach ($arrayMes as $mes) {

        $tHeadEdad .= '<th>'.$mesesNom[$mes].'</th>';
    
    }
  $tHeadEdad .= '<th>Total</th>
  </tr>';



// cuerpo
$tBodyEdad = "";
	
	$posicion = 0;
	$edad = [];	
	$totalEdad = [];

// exit(var_dump($respuesta2));
	foreach ($respuesta2 as $mes => $valoresMes) {
		foreach ($valoresMes as $valorMes => $valor) {
			// convertimos la respuesta a un array asociativo con la clave primaria edad mes
			$edades[$valor['edad']][$mes] = $valor['TOTAL'];	
		}
	}

  // exit(var_dump($edades));

  // exit(var_dump($edades));
  // funcion para colocar en 0 las campos vacios
  foreach ($respuesta2 as $mes => $valoresMes) {
    foreach ($edades as $edad => $valorEdad) {
      if (isset($edades[$edad][$mes])) {
        continue;
      }else{
        $edades[$edad][$mes] = '0';
      }  
    }
    foreach ($valoresMes as $valorMes => $valor) {
      ksort($edades[$valor['edad']]);
    }
  }

  // exit(var_dump($edades)); 

	foreach ($edades as $edad => $valorEdad) {
		$tBodyEdad .= "<tr> <td>".$edad."</td>";

		$valorFila = 0;
		foreach ($valorEdad as $valores => $valor) {
				
				$valorFila += $valor;
			 	$tBodyEdad .= "<td>".$valor."</td>";
			 	$totalEdad[$edad]=$valorFila;			
		}
		$tBodyEdad .= "<th>" .$valorFila. "</th>"; 
		$tBodyEdad .= "</tr>";
	}

ksort($totalEdad);

// pie
$tFootEdad = '<tr>
  <th>TOTAL</th>';
  $tTotal = 0;
  $totalMes = ["01" => 0, "02" => 0, "03" => 0, "04" => 0, "05" => 0, "06" => 0, "07" => 0, "08" => 0, "09" => 0, "10" => 0, "11" => 0, "12" => 0];

  foreach ($edades as $edad => $valorEdad) {

  	foreach ($valorEdad as $mes => $valorMes) {
  		
  		$totalMes[$mes] += $valorMes;
  	}

  }

  // var_dump($totalMes);

  foreach ($totalMes as $total) {

  	if ($total <> 0) {
  		 $tFootEdad .='<th>'.$total.'</th>';
  		 $tTotal += $total;
  	}
  	
  }

   
$tFootEdad .='<th>'.$tTotal.'</th>
</tr>';


// var_dump($totalEdad);

$data['thead'] = $tHeadEdad;
$data['tbody'] = $tBodyEdad;
$data['tfoot'] = $tFootEdad;
$data['info'] = $totalEdad;



echo json_encode($data);




