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
		if ($resComplementos->num_rows > 0) {
			while ($Complementos = $resComplementos->fetch_assoc()) {
				if ($Complementos['tipo_complem'] == "APS") {
					if (isset($totalesComplementos[$mes]["APS"])) {
						$totalesComplementos[$mes]["APS"]+=$Complementos['total']*(isset($valorComplementos[$Complementos['tipo_complem']]));
					} else {
						$totalesComplementos[$mes]["APS"]=$Complementos['total']*(isset($valorComplementos[$Complementos['tipo_complem']]));
					}
				} else {
					if (isset($totalesComplementos[$mes]["AM/PM"])) {
						$totalesComplementos[$mes]["AM/PM"]+=$Complementos['total']*(isset($valorComplementos[$Complementos['tipo_complem']]));
					} else {
						$totalesComplementos[$mes]["AM/PM"]=$Complementos['total']*(isset($valorComplementos[$Complementos['tipo_complem']]));
					}
				}
				
			}
		}
	}

	if (!isset($totalesComplementos[$mes]["APS"])) {
		$totalesComplementos[$mes]["APS"] = 0;
	}

	if (!isset($totalesComplementos[$mes]["AM/PM"])) {
		$totalesComplementos[$mes]["AM/PM"] = 0;
	}
}

$tabla="";

$tabla.="<thead><tr><th>Mes</th><th>APS</th><th>AM/PM</th><th>Total</th><tr></thead>";

$tabla.="<tbody>";

$totalesSum=0;

$totalAPS = 0;
$totalAMPM = 0;

$totalesComplement = [];

foreach ($totalesComplementos as $mes => $totales) {
	$tabla.="<tr><td>".$mesesNom[$mes]."</td><td>$ ".number_format($totales['APS'], 0, "", ".")."</td><td>$ ".number_format($totales['AM/PM'], 0, "", ".")."</td>";
	$totalAPS+=$totales['APS'];
	$totalAMPM += $totales['AM/PM'];
	$totalMes = $totales['APS']+$totales['AM/PM'];
	$totalesSum+=$totalMes;
	$tabla.="<th>$ ".number_format($totalMes, 0, "", ".")."</th></tr>";
}
$tabla.="</tbody>";
$totalesComplement['APS'] = $totalAPS;
$totalesComplement['AM/PM'] = $totalAMPM;

$tabla.="<tfoot><tr><th>Total</th><th>$ ".number_format($totalAPS, 0, "", ".")."</th><th>$ ".number_format($totalAMPM, 0, "", ".")."</th><th>$ ".number_format($totalesSum, 0, "", ".")."</th><tr></tfoot>";

$data['tabla'] = $tabla;
$data['info'] = $totalesComplementos;
$data['totales'] = $totalesComplement;

echo json_encode($data);

// print_r($totalesComplementos);