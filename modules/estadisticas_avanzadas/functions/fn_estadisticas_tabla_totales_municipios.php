<?php 
require_once '../../../config.php';
require_once '../../../db/conexion.php';
$periodoActual = $_SESSION['periodoActual'];
$mesesNom = array('01' => "Enero", "02" => "Febrero", "03" => "Marzo", "04" => "Abril", "05" => "Mayo", "06" => "Junio", "07" => "Julio", "08" => "Agosto", "09" => "Septiembre", "10" => "Octubre", "11" => "Noviembre", "12" => "Diciembre");

$diasSemanas = $_POST['diasSemanas'];

$municipios = [];
$totalesMunicipios = [];
$totalesMunicipios2 = [];
$totalesMunicipios2 = [];
$sumTotalesSemanas = [];

$consultaMunicipios = "SELECT DISTINCT
                        ubicacion.CodigoDANE, ubicacion.Ciudad
                    FROM
                        ubicacion,
                        parametros
                    WHERE
                        ubicacion.ETC = 0 
                        AND ubicacion.CodigoDane LIKE CONCAT(parametros.CodDepartamento, '%')
                        AND EXISTS( SELECT DISTINCT
                            cod_mun
                        FROM
                            instituciones
                        WHERE
                            cod_mun = ubicacion.CodigoDANE)
                    ORDER BY ubicacion.Ciudad ASC";
$resultadoMunicipios = $Link->query($consultaMunicipios);
if ($resultadoMunicipios->num_rows > 0) {
	while ($dataMunicipios = $resultadoMunicipios->fetch_assoc()) {
		$municipios[$dataMunicipios['CodigoDANE']] = $dataMunicipios['Ciudad']; 
	}
}

$codDepartamento = 0;

$conscodDepartamento = "SELECT CodDepartamento FROM parametros";
$rescodDepartamento = $Link->query($conscodDepartamento);

if ($rescodDepartamento->num_rows > 0) {
	if ($datacodDepartamento = $rescodDepartamento->fetch_assoc()) {
		$codDepartamento = $datacodDepartamento['CodDepartamento'];
	}
}


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
    // echo $consultaRes."</br>";
 		$consMunicipios = "SELECT cod_mun_sede, $datos FROM $tabla GROUP BY cod_mun_sede";
 		// echo $consMunicipios."\n";
 		$resMunicipios= $Link->query($consMunicipios);
	 	if ($resMunicipios->num_rows > 0) {
	 		while ($dataMunicipios = $resMunicipios->fetch_assoc()) {
	 			foreach ($municipios as $codigo => $Ciudad) {
	 				if ($codigo == $dataMunicipios['cod_mun_sede']) {
			 			for ($i=1; $i <= $sem ; $i++) {
			 				if (strlen($i) == 1) {
				              $i = "0".$i;
				            }
			 				if (!isset($dataMunicipios["semana_".$i]) && !isset($totalesMunicipios[$Ciudad]["semana_".$i])) {
			 					$totalesMunicipios[$Ciudad]["semana_".$i] = 0;
			 				} else if (!isset($dataMunicipios["semana_".$i]) && isset($totalesMunicipios[$Ciudad]["semana_".$i])) {
			 					# code...
			 				} else {
			 					if (isset($totalesMunicipios[$Ciudad]["semana_".$i])) {
			 						$totalesMunicipios[$Ciudad]["semana_".$i] += $dataMunicipios['semana_'.$i];
			 					} else {
			 						$totalesMunicipios[$Ciudad]["semana_".$i] = $dataMunicipios['semana_'.$i];
			 					}
			 				}

			 				if (isset($totalesMunicipios2[$Ciudad]) && isset($dataMunicipios['semana_'.$i])) {
			                  $totalesMunicipios2[$Ciudad] += $dataMunicipios['semana_'.$i];
			                } else if (!isset($totalesMunicipios2[$Ciudad]) && isset($dataMunicipios['semana_'.$i])) {
			                  $totalesMunicipios2[$Ciudad] = $dataMunicipios['semana_'.$i];
			                }

			                if (isset($totalesMunicipios3[$codigo]) && isset($dataMunicipios['semana_'.$i])) {
			                  $totalesMunicipios3[$codigo][0] += $dataMunicipios['semana_'.$i];
			                } else if (!isset($totalesMunicipios3[$codigo]) && isset($dataMunicipios['semana_'.$i])) {
			                  $totalesMunicipios3[$codigo][0] = $dataMunicipios['semana_'.$i];
			                  $totalesMunicipios3[$codigo][1] = $Ciudad;
			                }

			 				if (isset($sumTotalesSemanas['semana_'.$i]) && isset($dataMunicipios['semana_'.$i])) {
			                  $sumTotalesSemanas['semana_'.$i] += $dataMunicipios['semana_'.$i];
			                } else if (!isset($sumTotalesSemanas['semana_'.$i]) && isset($dataMunicipios['semana_'.$i])) {
			                  $sumTotalesSemanas['semana_'.$i] = $dataMunicipios['semana_'.$i];
			                }
			 			}
			 		}
		 		}
	 		}
	 	}

  }

$tabla = '';

$tHeadSemana = '<tr>
     				<th>Municipio</th>';
  $numTds = 1;
  $semanaAct = "";
  foreach ($totalesMunicipios as $codigo => $semanaArr) { 
    foreach ($semanaArr as $semana => $totales) { //recorremos todas las semanas obtenidas para crear las columnas
	    if ($numTds <= $sem) {
	    	if ($semana != $semanaAct) { //Si la semana en turno es igual a la última semana guardada, no se crea otra columna
		        $numTds++; //aumentamos en 1 el número de columnas creadas
		      
				$tHeadSemana .=  '<th>
					  '.ucwords(str_replace("_", " ", $semana)).'
					</th>';
	      	}
	   		$semanaAct=$semana; //Guardamos el último número de semana del mes (incrementable sin reinicio por mes)
	    }
    } 
  }
$tHeadSemana .= '<th>Total</th></tr>';
  // var_dump($totalesMunicipios);

$tBodySemana="";
foreach ($totalesMunicipios as $codigo => $semanaArr) { 
$tBodySemana .= "<tr>
					<td>".$codigo."</td>";
for ($l=1; $l < $numTds ; $l++) { //según el número de columnas creadas, recorremos las semanas obtenidas
  if (strlen($l) == 1) {
    $l = "0".$l;
  }
  if (isset($semanaArr["semana_".$l])) { //Si en el mes en turno, está la semana del recorrido "for" imprimimos el valor en la columna nueva.
	  $tBodySemana .= '<td>
	    '.$semanaArr["semana_".$l].'
	  </td>';
  } else { //Si en el mes en turno, NO está la semana del recorrido "for" imprimimos la columna nueva vacía. 
    $tBodySemana .='<td>0</td>';
  }
} 
$tBodySemana .="<td>".$totalesMunicipios2[$codigo]."</td></tr>";
}

$tFootSemana ='<tr>
	<th>TOTAL</th>';

	for ($l=1; $l <= sizeof($sumTotalesSemanas); $l++) {
	  if (strlen($l) == 1) {
	    $l = "0".$l;
	  }
	  $tFootSemana .='<th>
	    '.$sumTotalesSemanas["semana_".$l].'
	  </th>';
	
	  } 
$tFootSemana .='<th>Total</th></tr>';

$data['thead'] = $tHeadSemana;
$data['tbody'] = $tBodySemana;
$data['tfoot'] = $tFootSemana;
$data['info'] = $totalesMunicipios3;
$data['codDepartamento'] = $codDepartamento;

echo json_encode($data);

  // print_r($totalesMunicipios);