<?php 
require_once '../../../config.php';
require_once '../../../db/conexion.php';

$periodoActual = $_SESSION['periodoActual'];
// $diasSemanas = $_POST['diasSemanas'];
$mesesNom = array('01' => "Enero", "02" => "Febrero", "03" => "Marzo", "04" => "Abril", "05" => "Mayo", "06" => "Junio", "07" => "Julio", "08" => "Agosto", "09" => "Septiembre", "10" => "Octubre", "11" => "Noviembre", "12" => "Diciembre");

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

$valorComplementos = [];
$totalesComplementos = []; 
$tipoComplementos = [];

$consTipoComplemento = "SELECT * FROM tipo_complemento";
$resTipoComplemento = $Link->query($consTipoComplemento);
if ($resTipoComplemento->num_rows > 0) {
	while ($TipoComplemento = $resTipoComplemento->fetch_assoc()) {
		$valorComplementos[$TipoComplemento['CODIGO']] = $TipoComplemento['ValorRacion'];
		$tipoComplementos[] = $TipoComplemento['CODIGO'];
	}
}

foreach ($diasSemanas as $mes => $SemanasArray) {
	$datos="";
	$diaD = 1;
	foreach ($SemanasArray as $semana => $dias) { //recorremos las semanas del mes en turno
      foreach ($dias as $D => $dia) { //recorremos los días de la semana en turno
        // echo $mes." - ".$semana." - ".$D." - ".$dia."</br>";
        $datos.="SUM(D$diaD) + ";
        $diaD++;
      }
    }

    foreach ($tipoComplementos as $key => $complemento) {
    // 	$consultaResComplemento = "SELECT ";
    //   	$diaD = 1;
    //   	foreach ($semanas as $semana => $dias) {
    //     	foreach ($dias as $D => $dia) {
    //      	 	$consultaResComplemento.="SUM(D$diaD) + ";
    //       		$diaD++;
    //     }
    // }
    

    $arrayCom;	
	if ($datos != "") {
		$datos = trim($datos, "+ ");
		$consComplementos ="SELECT tipo_complem , $datos  AS total FROM entregas_res_$mes$periodoActual WHERE tipo_complem = '$complemento' GROUP BY tipo_complem ";
		// echo $consComplementos."\n";
		$resComplementos = $Link->query($consComplementos);
		$tcom = [];
		if ($resComplementos->num_rows > 0) {
			while ($Complementos = $resComplementos->fetch_assoc()) {

		
				if ($Complementos['total'] == '') {
					continue;
				}

				if (isset($totalesComplementos[$mes][$Complementos['tipo_complem']])) {
					$totalesComplementos[$mes][$Complementos['tipo_complem']]+=$Complementos['total']*(isset($valorComplementos[$Complementos['tipo_complem']]) ? $valorComplementos[$Complementos['tipo_complem']] : 0);
				} else {
					$totalesComplementos[$mes][$Complementos['tipo_complem']]=$Complementos['total']*(isset($valorComplementos[$Complementos['tipo_complem']]) ? $valorComplementos[$Complementos['tipo_complem']] : 0);
				}
			    $arrayCom[$Complementos['tipo_complem']] = ($Complementos['tipo_complem']);
			}

		}
	}
}

}

// foreach ($arrayMes as $key => $link) {
// 	if($link === '') 
//     { 
//         unset($arrayMes[$key]); 
//     } 
// }


$tabla="";

$tabla.="<thead>
			<tr>
				<th>Mes</th>";
foreach ($arrayCom as $comp => $set) {
	$tabla.="<th>".$comp."</th>";
	// var_dump($tcom);
}
$tabla.="		<th>Total</th>
			</tr>
		</thead>";


$tabla.="<tbody>";

$totalesComplement = [];

foreach ($totalesComplementos as $mes => $arrT) {
	$totalM = 0;
	$tabla.="<tr>
				<th>".$mesesNom[$mes]."</th>";
		foreach ($arrT as $complemento => $total) {
			$tabla.="<td> $ ".number_format($total, 0, "", ".")."</td>";	
			$totalM += $total;
			if (isset($totalesComplement[$complemento])) {
				$totalesComplement[$complemento] += $total;
			} else {
				$totalesComplement[$complemento] = $total;
			}
		}

	$tabla.="	<th> $ ".number_format($totalM, 0, "", ".")."</th>
			<tr>";
}
$tabla.="</tbody>";



$tabla.="<tfoot>
			<tr>
				<th>Total</th>";
$tTotal = 0;
foreach ($totalesComplement as $complemento => $total) {
	$tabla.="<th> $ ".number_format($total, 0, "", ".")."</th>";
	$tTotal += $total;
}
$tabla.="		<th> $ ".number_format($tTotal, 0, "", ".")."</th>
			<tr>
		</tfoot>";

$data['tabla'] = $tabla;
$data['info'] = $totalesComplementos;
$data['totales'] = $totalesComplement;

echo json_encode($data);

// print_r($totalesComplementos);