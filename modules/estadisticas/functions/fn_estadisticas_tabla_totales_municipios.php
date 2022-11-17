<?php
require_once '../../../config.php';
require_once '../../../db/conexion.php';

// $diasSemanas = $_POST['diasSemanas'];
$periodoActual = $_SESSION['periodoActual'];
$codDepartamento = $_SESSION['p_CodDepartamento'];
require_once 'fn_estadisticas_functions.php';

$diasSemanas = buscar_dias_semanas($Link, $periodoActual);
foreach ($diasSemanas as $key => $value) {
   foreach ($value as $key2 => $value2) {
      $semanasP[$key2] = $key2;  
   }
}

$municipios = [];
$totalesMunicipios = [];
$sumTotalesSemanas = [];
$totalesMunicipios2 = [];
$totalesMunicipios2 = [];
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

$sem2 = 0;
foreach ($diasSemanas as $mes => $semanas) { //recorremos los meses
   $datos = "";
   $diaD = 1;
   $tabla="entregas_res_$mes$periodoActual"; //tabla donde se busca, según mes(obtenido de consulta anterior) y año
   foreach ($semanas as $semana => $dias) { //recorremos las semanas del mes en turno	
      foreach ($dias as $D => $dia) { //recorremos los días de la semana en turno
			$consultaPlanillaDias = "SELECT D$diaD FROM planilla_dias WHERE D$diaD = $dia AND mes = $mes;";
      		$respuestaConsultaPlanillaDias = $Link->query($consultaPlanillaDias);
        	$consultaPlanillaDias = "SELECT D$diaD FROM planilla_dias WHERE D$diaD = $dia AND mes = $mes;";
        	if ($respuestaConsultaPlanillaDias->num_rows == 1) {
          		$datos.="SUM(D$diaD) + ";
          		$diaD++;
       	 	}
      	}
      	$datos = trim($datos, "+ ");
      	$datos.= " AS semana_".$semana.", ";
     	$sem2++;
   	}
   	$datos = trim($datos, ", ");

	$consMunicipios = "SELECT cod_mun_sede, $datos FROM $tabla GROUP BY cod_mun_sede";
	$resMunicipios= $Link->query($consMunicipios);
 	if ($resMunicipios->num_rows > 0) {
 		while ($dataMunicipios = $resMunicipios->fetch_assoc()) {
 			foreach ($municipios as $codigo => $Ciudad) {
 				if ($codigo == $dataMunicipios['cod_mun_sede']) {
		 			foreach ($semanasP as $semanaP) {
		 				$i = $semanaP;
		 				if (strlen($i) == 1) { 
		 					$i = "0".$i; 
		 				}
		 				if (!isset($dataMunicipios["semana_".$i]) && !isset($totalesMunicipios[$Ciudad]["semana_".$i])) {
		 					$totalesMunicipios[$Ciudad]["semana_".$i] = 0;
		 				} else if (!isset($dataMunicipios["semana_".$i]) && isset($totalesMunicipios[$Ciudad]["semana_".$i])) {
		 					// ninguna operacion	
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

		 				if (isset($sumTotalesSemanas['semana_'.$i])) {
		               $sumTotalesSemanas['semana_'.$i] += isset($dataMunicipios['semana_'.$i]) ? $dataMunicipios['semana_'.$i] : 0;
		            } else if (!isset($sumTotalesSemanas['semana_'.$i])) {
		               $sumTotalesSemanas['semana_'.$i] = isset($dataMunicipios['semana_'.$i]) ? $dataMunicipios['semana_'.$i] : 0;
		            }
		 			}
		 		}
	 		}
 		}
 	}
}

$tabla = '';
$numTds = 1;
$semanaAct = "";
$tHeadSemana = '	<tr>
     						<th>Municipio</th>';     				
							foreach ($totalesMunicipios as $codigo => $semanaArr) {
    							foreach ($semanaArr as $semana => $totales) { //recorremos todas las semanas obtenidas para crear las columnas
	    							if ($numTds <= $sem2 || $numTds."b" == $sem2) {
	    								if ($semana != $semanaAct) { //Si la semana en turno es igual a la última semana guardada, no se crea otra columna
		        							$numTds++; //aumentamos en 1 el número de columnas creadas
											$tHeadSemana .=  '<th>
					  						'.ucwords(str_replace("_", " ", $semana)).'
											</th>';
	      							}
	    							}
	    							$semanaAct=$semana; //Guardamos el último número de semana del mes (incrementable sin reinicio por mes)
    							}
							}
							$tHeadSemana .= '<th>Total</th>
						</tr>';

$tBodySemana="";
foreach ($totalesMunicipios as $codigo => $semanaArr) {
	$tBodySemana .= "	<tr>
								<td>".$codigo."</td>";
								foreach ($semanasP as $semanaP) { //según el número de columnas creadas, recorremos las semanas obtenidas
									$l = $semanaP;
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
								$tBodySemana .="<td>".$totalesMunicipios2[$codigo]."</td>
							</tr>";
}

$tFootSemana ='<tr>
						<th>TOTAL</th>';
						foreach ($semanasP as $semanaP) {
							$l = $semanaP;
							if (strlen($l) == 1) { $l = "0".$l; }
								$suma_total_semana = (isset($sumTotalesSemanas["semana_".$l])) ? $sumTotalesSemanas["semana_".$l] : 0;
								$tFootSemana .='<th>
	    						'.$suma_total_semana.'
								</th>';
							}
							$tFootSemana .='<th>Total</th>
					</tr>';

$data['thead'] = $tHeadSemana;
$data['tbody'] = $tBodySemana;
$data['tfoot'] = $tFootSemana;
$data['info'] = $totalesMunicipios3;
$data['codDepartamento'] = $codDepartamento;

echo json_encode($data);