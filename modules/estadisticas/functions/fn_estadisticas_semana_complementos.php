<?php 
require_once '../../../config.php';
require_once '../../../db/conexion.php';

$periodoActual = $_SESSION['periodoActual'];
$semana = $_POST['semana'];
$diasSemanas = $_POST['diasSemanas'];
$tipoComplementos = $_POST['tipoComplementos'];
$totalesComplementos = [];

foreach ($diasSemanas as $mes => $SemanasArray) {
	$datos = "";
	$diaD = 0;
	foreach ($SemanasArray as $semanaF => $dia) {
		foreach ($dia as $id => $diaR) {
			$diaD++;
			if ($semanaF == $semana) {
			 $datos.="SUM(D$diaD) + ";
			}
		}
	}

	if ($datos != "") {
		$datos = trim($datos, "+ ");
		$consComplementos ="SELECT tipo_complem , $datos  AS totalSemana FROM entregas_res_$mes$periodoActual GROUP BY tipo_complem;";
		$resComplementos = $Link->query($consComplementos);
		if ($resComplementos->num_rows > 0) {
			while ($Complementos = $resComplementos->fetch_assoc()) {
				if (is_null($Complementos['tipo_complem'])) {
					continue;
				}
				if (isset($totalesComplementos[$Complementos['tipo_complem']])) {
					$totalesComplementos[$Complementos['tipo_complem']] += $Complementos['totalSemana'];
				} else {
					$totalesComplementos[$Complementos['tipo_complem']] = $Complementos['totalSemana'];
				}
			}
		}
	}
}

$sumTotal=0;
$tabla = "";
$tabla.="<thead><tr><th>Tipo Complemento</th><th>Total</th></tr></thead>";
$tabla.="<tbody>";
foreach ($totalesComplementos as $complemento => $total) {
	$sumTotal+=$total;
	$tabla.="<tr><td>".$complemento."</td><td>".$total."</td></tr>";
}
$tabla.="</tbody>";
$tabla.="<tfoot><tr><th>Total</th><th>".$sumTotal."</th></tr></tfoot>";

$data['tabla'] = $tabla;
$data['info'] = $totalesComplementos;

echo json_encode($data);
