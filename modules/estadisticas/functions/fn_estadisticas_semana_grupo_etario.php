<?php 
require_once '../../../config.php';
require_once '../../../db/conexion.php';
// require_once 'fn_estadisticas_functions.php';

$periodoActual = $_SESSION['periodoActual'];
$semana = $_POST['semana'];
$diasSemanas = $_POST['diasSemanas'];
$totalesEtarios = [];
$etarios = [];
$cantGruposEtarios = $_SESSION['cant_gruposEtarios'];
$edadInicial = 0;
$edadFinal=0;
$complementos = $_POST['tipoComplementos'];

$consGrupoEtario = "SELECT * FROM grupo_etario ORDER BY EDADINICIAL ASC";
$resGrupoEtario = $Link->query($consGrupoEtario);
if ($resGrupoEtario->num_rows > 0) {
	while ($dataGrupoEtario = $resGrupoEtario->fetch_assoc()) {
		$etarios[$dataGrupoEtario['ID']]['inicial'] = $dataGrupoEtario['EDADINICIAL'];
		$etarios[$dataGrupoEtario['ID']]['final'] = $dataGrupoEtario['EDADFINAL'];
		$etarios[$dataGrupoEtario['ID']]['DESC'] = $dataGrupoEtario['DESCRIPCION'];
	}
}

foreach ($etarios as $key => $etario) {
	foreach ($complementos as $key => $value) {
		$totalesEtarios[$etario['DESC']][$value] = 0;
	}
}

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
		$cntGE = 1;
		foreach ($etarios as $ID => $etario) {
			if ($cntGE == 1) {
				$condicional = " CAST(edad AS DECIMAL(5,0)) <= ".$etario['final']." ";
			} else if ($cntGE == sizeof($etarios)) {
				$condicional = " CAST(edad AS DECIMAL(5,0)) >= ".$etario['inicial']." ";
			} else {
				$condicional = " CAST(edad AS DECIMAL(5,0)) >= ".$etario['inicial']." AND CAST(edad AS DECIMAL(5,0)) <= ".$etario['final']." ";
			}

			$consComplementos ="SELECT tipo_complem , $datos AS totalSemana FROM entregas_res_$mes$periodoActual WHERE $condicional GROUP BY tipo_complem;";
			$resComplementos = $Link->query($consComplementos);
			if ($resComplementos->num_rows > 0) {
				while ($Complementos = $resComplementos->fetch_assoc()) {
					if (is_null($Complementos['tipo_complem'])) {
						continue;
					}
					if (isset($totalesEtarios[$etario['DESC']][$Complementos['tipo_complem']])) {
						$totalesEtarios[$etario['DESC']][$Complementos['tipo_complem']] += $Complementos['totalSemana'];
					} else {
						$totalesEtarios[$etario['DESC']][$Complementos['tipo_complem']] = $Complementos['totalSemana'];
					}
				}
			}
		$cntGE++;
		}
	}
}

$tabla = "";

$tabla.="<thead><tr><th>Grupo Etario</th>";
$cnt = 0;
foreach ($totalesEtarios as $grupoEtario => $arrayComplementos) {
	if ($cnt==0) {
		foreach ($arrayComplementos as $complemento => $total) {
			$cnt++;
			$tabla.="<th>".$complemento."</th>";
		}
	}
}
$tabla.="<th>Total</th></tr></thead>";
$tabla.="<tbody>";
$sumtotales = [];
$Totalitario = 0;
foreach ($totalesEtarios as $grupoEtario => $arrayComplementos) {
	$cntN = 0;
	$sumEtario = 0;
	foreach ($arrayComplementos as $complemento => $total) {
		if ($cntN==0) {
			$cntN++;
			$tabla.="<tr><td>".$grupoEtario."</td>";
		}
		$tabla.="<td>".$total."</td>";

		if (isset($sumtotales[$complemento])) {
			$sumtotales[$complemento] += $total;
		} else {
			$sumtotales[$complemento] = $total;
		}

		$sumEtario+=$total;
		$Totalitario+=$total;
	}
	$tabla.="<th>".$sumEtario."</th></tr>";
}
$tabla.="</tbody>";
$tabla.="<tfoot><tr><th>Total</th>";
$cnt = 0;
foreach ($totalesEtarios as $grupoEtario => $arrayComplementos) {
	if ($cnt==0) {
		foreach ($arrayComplementos as $complemento => $total) {
			$cnt++;
			$tabla.="<th>".$sumtotales[$complemento]."</th>";
		}
	}
}
$tabla.="<th>".$Totalitario."</th></tr></tfoot>";

$data['tabla'] = $tabla;
$data['info'] = $totalesEtarios;

echo json_encode($data);

// print_r($totalesEtarios);
