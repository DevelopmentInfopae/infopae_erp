<?php
require_once '../../../config.php';
require_once '../../../db/conexion.php';

$periodoActual = $_SESSION['periodoActual'];
$diasSemanas = $_POST['diasSemanas'];
$mesesNom = array('1' => "Enero", "2" => "Febrero", "3" => "Marzo", "4" => "Abril", "5" => "Mayo", "6" => "Junio", "7" => "Julio", "8" => "Agosto", "9" => "Septiembre", "10" => "Octubre", "11" => "Noviembre", "12" => "Diciembre");

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
        $datos.="SUM(D$diaD) + ";
        $diaD++;
      }
    }

	if ($datos != "") {
		$datos = trim($datos, "+ ");
		$consComplementos ="SELECT tipo_complem , $datos  AS total FROM entregas_res_$mes$periodoActual GROUP BY tipo_complem;";
		$mes = (int) $mes;
		$resComplementos = $Link->query($consComplementos);
		$tcom = [];
		if ($resComplementos->num_rows > 0) {
			$totalesComplementos[$mes]["APS"] = 0;
			$totalesComplementos[$mes]["CAJMPS"] = 0;
			$totalesComplementos[$mes]["CAJMRI"] = 0;
			$totalesComplementos[$mes]["CAJTPS"] = 0;
			$totalesComplementos[$mes]["CAJTRI"] = 0;

			while ($Complementos = $resComplementos->fetch_assoc()) {
				if (isset($totalesComplementos[$mes][$Complementos['tipo_complem']])) {
					$totalesComplementos[$mes][$Complementos['tipo_complem']] += $Complementos['total']*(isset($valorComplementos[$Complementos['tipo_complem']]) ? $valorComplementos[$Complementos['tipo_complem']] : 0);
				} else {
					$totalesComplementos[$mes][$Complementos['tipo_complem']]=$Complementos['total']*(isset($valorComplementos[$Complementos['tipo_complem']]) ? $valorComplementos[$Complementos['tipo_complem']] : 0);
				}
			}
		}
	}
}

$tabla = '';
$filas_encabezado = '';

foreach ($totalesComplementos as $mes => $arrT) {
	foreach ($arrT as $complemento => $total) {
		if (!isset($tcom[$complemento])) {
			$tcom[$complemento] = 1;
		}
	}
}

foreach ($tcom as $comp => $set) {
	$filas_encabezado .= "<th>". $comp ."</th>";
}
$tabla = '<thead>
			<tr>
				<th>Mes</th>'.
				$filas_encabezado
				.'<th>Total</th>
			</tr>
		</thead>';

$totalesComplement = [];
ksort($totalesComplementos);

$tabla.="<tbody>";
foreach ($totalesComplementos as $mes => $arrT) {
	$totalM = 0;
	$tabla .= "<tr>
				<th>".$mesesNom[$mes]."</th>";
	foreach ($arrT as $complemento => $total) {
		$tabla.='<td class="text-right"> $ '.number_format($total, 0, "", ".").'</td>';
		$totalM += $total;
		if (isset($totalesComplement[$complemento])) {
			$totalesComplement[$complemento] += $total;
		} else {
			$totalesComplement[$complemento] = $total;
		}
	}

	$tabla.='	<th class="text-right"> $ '.number_format($totalM, 0, "", ".").'</th>
			<tr>';
}
$tabla.="</tbody>";



$tabla.="<tfoot>
			<tr>
				<th>Total</th>";
$tTotal = 0;
foreach ($totalesComplement as $complemento => $total) {
	$tabla.='<th class="text-right"> $ '.number_format($total, 0, "", ".").'</th>';
	$tTotal += $total;
}
$tabla.='		<th class="text-right"> $ '.number_format($tTotal, 0, "", ".").'</th>
			<tr>
		</tfoot>';

$data['tabla'] = $tabla;
$data['info'] = $totalesComplementos;
$data['totales'] = $totalesComplement;

echo json_encode($data);