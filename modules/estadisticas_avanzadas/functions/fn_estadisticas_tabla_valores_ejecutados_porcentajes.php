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

$ejecutado = 0;

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
		$consComplementos ="SELECT t.CODIGO , $datos  AS total FROM entregas_res_$mes$periodoActual AS e right JOIN tipo_complemento AS t ON t.CODIGO = e.tipo_complem  GROUP BY t.CODIGO ";
		// echo $consComplementos."\n";
		$resComplementos = $Link->query($consComplementos);
		if ($resComplementos->num_rows > 0) {
			while ($Complementos = $resComplementos->fetch_assoc()) {
        if ($Complementos['total'] == '' || $Complementos['total'] == null) {
          $Complementos['total'] = 0;
          // continue;
        }
				$ejecutado += $Complementos['total']*(isset($valorComplementos[$Complementos['CODIGO']]) ? $valorComplementos[$Complementos['CODIGO']] : 0); 
			}
		}
	}
}
$valorContrato = 0;
$consValores = "SELECT ValorContrato FROM parametros";
$resValores = $Link->query($consValores);
if ($resValores->num_rows > 0) {
	if ($Valores = $resValores->fetch_assoc()) {
		$valorContrato = $Valores['ValorContrato'];
	}
}


$porejecutar = $valorContrato - $ejecutado;

// exit(var_dump($consComplementos));

$porcenEjecutado = 100 * $ejecutado / $valorContrato;
$porcenPorEjecutar = 100 * $porejecutar / $valorContrato;

$porcenEjecutado = round($porcenEjecutado, 0);
$porcenPorEjecutar = round($porcenPorEjecutar, 0);

$tabla="";

$tabla.="<thead><tr><th></th><th>Total</th><th>%</th><tr></thead>";

$tabla.="<tbody>";

$tabla.="<tr><td>Valor Contrato</td><td>$ ".number_format($valorContrato, 0, "", ".")."</td><td>100%</td></tr>";
$tabla.="<tr><td>Valor por Ejecutar</td><td>$ ".number_format($porejecutar, 0, "", ".")."</td><td>".number_format($porcenPorEjecutar, 0, ",", ".")."%</td></tr>";
$tabla.="<tr><td>Valor Ejecutado</td><td>$ ".number_format($ejecutado, 0, "", ".")."</td><td>".number_format($porcenEjecutado, 0, ",", ".")."%</td></tr>";

$tabla.="<tbody>";

$porcs = [];

$porcs['Ejecutado'] = $porcenEjecutado;
$porcs['Ejecutar'] = $porcenPorEjecutar;

$data['tabla'] = $tabla;
$data['info'] = $porcs;

echo json_encode($data);

// print_r($totalesComplementos);