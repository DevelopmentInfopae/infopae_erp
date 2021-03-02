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
    $consultaRes = "SELECT discapacidades.nombre, $datos ";
    $consultaRes.=" AS TOTAL FROM $tabla JOIN discapacidades ON $tabla.cod_discap = discapacidades.id GROUP BY cod_discap";

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
$tHeadDiscapacidad = '<tr>
    <th>Discapacidad</th>';
     
    foreach ($arrayMes as $mes) {

        $tHeadDiscapacidad .= '<th>'.$mesesNom[$mes].'</th>';
    
    }
  $tHeadDiscapacidad .= '<th>Total</th>
  </tr>';

  // cuerpo
  $tBodyDiscapacidad = "";
	
	$posicion = 0;
	$discapacidades = [];	
	$totalDiscapacidad = [];

	foreach ($respuesta2 as $mes => $valoresMes) {

		foreach ($valoresMes as $valorMes => $valor) {
			// convertimos la respuesta a un array asociativo con la clave primaria edad mes
			$discapacidades[$valor['nombre']][$mes] = $valor['TOTAL'];	

		}
	}

	foreach ($discapacidades as $discapacidad => $valorDiscapacidad) {
    $discapacidadString = ucfirst(strtoupper($discapacidad));
    utf8_decode($discapacidadString);
		$tBodyDiscapacidad .= "<tr> <td>".$discapacidadString."</td>";

		$valorFila = 0;
		foreach ($valorDiscapacidad as $valores => $valor) {
				
				$valorFila += $valor;
			 	$tBodyDiscapacidad .= "<td>".$valor."</td>";
			 	$totalDiscapacidad[$discapacidadString]=$valorFila;			
		}
		$tBodyDiscapacidad .= "<th>" .$valorFila. "</th>"; 
		$tBodyDiscapacidad .= "</tr>";
	}

	// pie
	$tFootDiscapacidad = '<tr>
  					<th>TOTAL</th>';
  	$tTotal = 0;
  	$totalMes = ["01" => 0, "02" => 0, "03" => 0, "04" => 0, "05" => 0, "06" => 0, "07" => 0, "08" => 0, "09" => 0, "10" => 0, "11" => 0, "12" => 0];

  	foreach ($discapacidades as $discapacidad => $valorDiscapacidad) {

  	foreach ($valorDiscapacidad as $mes => $valorMes) {
  		
  		$totalMes[$mes] += $valorMes;
  	}

  }


  foreach ($totalMes as $total) {

  	if ($total <> 0) {
  		 $tFootDiscapacidad .='<th>'.$total.'</th>';
  		 $tTotal += $total;
  	}
  	
  }

   
$tFootDiscapacidad .='<th>'.$tTotal.'</th>
</tr>';

$data['thead'] = $tHeadDiscapacidad;
$data['tbody'] = $tBodyDiscapacidad;
$data['tfoot'] = $tFootDiscapacidad;
$data['info'] = $totalDiscapacidad;

echo json_encode($data);


