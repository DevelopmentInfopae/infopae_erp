<?php 
require_once '../../../config.php';
require_once '../../../db/conexion.php';

$periodoActual = $_SESSION['periodoActual'];
$diasSemanas = $_POST['diasSemanas'];
$mesesNom = array('01' => "Enero", "02" => "Febrero", "03" => "Marzo", "04" => "Abril", "05" => "Mayo", "06" => "Junio", "07" => "Julio", "08" => "Agosto", "09" => "Septiembre", "10" => "Octubre", "11" => "Noviembre", "12" => "Diciembre");

$valorComplementos = [];
$totalesComplementos = [];

$consTipoComplemento = "SELECT * FROM tipo_complemento";
$resTipoComplemento = $Link->query($consTipoComplemento);
if ($resTipoComplemento->num_rows > 0) {
	while ($TipoComplemento = $resTipoComplemento->fetch_assoc()) {
		$valorComplementos[$TipoComplemento['CODIGO']] = $TipoComplemento['ValorRacion'];
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

	if ($datos != "") {
		$datos = trim($datos, "+ ");
		$consComplementos ="SELECT tipo_complem , $datos  AS total FROM entregas_res_$mes$periodoActual GROUP BY tipo_complem;";
		// echo $consComplementos."\n";
		$resComplementos = $Link->query($consComplementos);
		$tcom = [];
		if ($resComplementos->num_rows > 0) {
			while ($Complementos = $resComplementos->fetch_assoc()) {
				// if ($Complementos['tipo_complem'] == "APS") {
				// 	if (isset($totalesComplementos[$mes]["APS"])) {
				// 		$totalesComplementos[$mes]["APS"]+=$Complementos['total']*(isset($valorComplementos[$Complementos['tipo_complem']]));
				// 	} else {
				// 		$totalesComplementos[$mes]["APS"]=$Complementos['total']*(isset($valorComplementos[$Complementos['tipo_complem']]));
				// 	}
				// } else {
				// 	if (isset($totalesComplementos[$mes]["AM/PM"])) {
				// 		$totalesComplementos[$mes]["AM/PM"]+=$Complementos['total']*(isset($valorComplementos[$Complementos['tipo_complem']]));
				// 	} else {
				// 		$totalesComplementos[$mes]["AM/PM"]=$Complementos['total']*(isset($valorComplementos[$Complementos['tipo_complem']]));
				// 	}
				// }

				if ($Complementos['total'] == '') {
					continue;
				}

				if (isset($totalesComplementos[$mes][$Complementos['tipo_complem']])) {
					$totalesComplementos[$mes][$Complementos['tipo_complem']]+=$Complementos['total']*(isset($valorComplementos[$Complementos['tipo_complem']]) ? $valorComplementos[$Complementos['tipo_complem']] : 0);
				} else {
					$totalesComplementos[$mes][$Complementos['tipo_complem']]=$Complementos['total']*(isset($valorComplementos[$Complementos['tipo_complem']]) ? $valorComplementos[$Complementos['tipo_complem']] : 0);
				}
				if (!isset($tcom[$Complementos['tipo_complem']])) {
					$tcom[$Complementos['tipo_complem']] = 1;
				}
			}
		}
	}

	// if (!isset($totalesComplementos[$mes]["APS"])) {
	// 	$totalesComplementos[$mes]["APS"] = 0;
	// }

	// if (!isset($totalesComplementos[$mes]["AM/PM"])) {
	// 	$totalesComplementos[$mes]["AM/PM"] = 0;
	// }
}

// var_dump($totalesComplementos);

$tabla="";

$tabla.="<thead>
			<tr>
				<th>Mes</th>";
foreach ($tcom as $comp => $set) {
	$tabla.="<th>".$comp."</th>";
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