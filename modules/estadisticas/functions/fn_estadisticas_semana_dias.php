<?php 
require_once '../../../config.php';
require_once '../../../db/conexion.php';
$mesesNom = array('01' => "Ene", "02" => "Feb", "03" => "Mar", "04" => "Abr", "05" => "Mayo", "06" => "Jun", "07" => "Jul", "08" => "Ago", "09" => "Sep", "10" => "Oct", "11" => "Nov", "12" => "Dic");
$periodoActual = $_SESSION['periodoActual'];
$semana = $_POST['semana'];
$diasSemanas = $_POST['diasSemanas'];

$dias = [];
$diasComplementos = [];
$mesDeDia = [];

foreach ($diasSemanas as $mes => $SemanasArray) {
	$datos = "";
	$diaD = 0;
	foreach ($SemanasArray as $semanaF => $dia) {
		// echo $semanaF."\n";
		foreach ($dia as $id => $diaR) {
			$diaD++;
			if ($semanaF == $semana) {
			 $datos.="SUM(D$diaD) AS '".$diaR."', ";
			 $dias[] = $diaR;
			 $mesDeDia[$diaR] = $mes;
			}
		}
	}

	if ($datos != "") {
		$datos = trim($datos, ", ");

		$consComplementos ="SELECT tipo_complem , $datos FROM entregas_res_$mes$periodoActual GROUP BY tipo_complem;";
		$resComplementos = $Link->query($consComplementos);
		if ($resComplementos->num_rows > 0) {
			while ($Complementos = $resComplementos->fetch_assoc()) {
				if (is_null($Complementos['tipo_complem'])) {
						continue;
					}
				// print_r($Complementos);
				foreach ($dias as $id => $diaFecha) {
					// echo "Dia : ".$diaFecha."- Mes :".$mes;
					if (!isset($diasComplementos[$diaFecha."-".$mesesNom[$mesDeDia[$diaFecha]]][$Complementos['tipo_complem']]) && isset($Complementos[$diaFecha])) {
						$diasComplementos[$diaFecha."-".$mesesNom[$mesDeDia[$diaFecha]]][$Complementos['tipo_complem']] = $Complementos[$diaFecha];
					} else if (!isset($diasComplementos[$diaFecha."-".$mesesNom[$mesDeDia[$diaFecha]]][$Complementos['tipo_complem']])) {
						$diasComplementos[$diaFecha."-".$mesesNom[$mesDeDia[$diaFecha]]][$Complementos['tipo_complem']] = 0;
					}
				}
			}
		}
		// echo $consComplementos."\n";
	}
}

// print_r($diasComplementos);


$sumtotales = [];
$totalitario = 0;
$tabla = "<thead><tr><th>Día</th>";
$cnt = 0;
foreach ($diasComplementos as $diaFecha => $ArrayComplementos) {
	if ($cnt==0) {
		foreach ($ArrayComplementos as $complemento => $total) {
			$tabla.="<th>".$complemento."</th>";
		}
	}
	$cnt++;
}
$tabla.="<th>Total</th></tr></thead>";

$tabla.="<tbody>";
foreach ($diasComplementos as $diaFecha => $ArrayComplementos) {
	$sumDia = 0;
	$tabla.="<tr><td>".$diaFecha."</td>";
	foreach ($ArrayComplementos as $complemento => $total) {
		$tabla.="<td>".$total."</td>";

		if (isset($sumtotales[$complemento])) {
			$sumtotales[$complemento] += $total;
		} else {
			$sumtotales[$complemento] = $total;
		}

		$sumDia += $total;
		$totalitario += $total;
	}
	$tabla.="<th>".$sumDia."</th></tr>";
}
$tabla.="</tbody>";

$tabla.= "<tfoot><tr><th>Total</th>";
$cnt=0;
foreach ($diasComplementos as $diaFecha => $ArrayComplementos) {
	if ($cnt==0) {
		foreach ($ArrayComplementos as $complemento => $total) {
			$tabla.="<th>".$sumtotales[$complemento]."</th>";
		}
	}
	$cnt++;
}
$tabla.="<th>".$totalitario."</th></tr></tfoot>";

$data['tabla'] = $tabla;
$data['info'] = $diasComplementos;

echo json_encode($data);
