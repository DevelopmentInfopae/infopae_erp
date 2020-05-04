<?php

require_once '../../../db/conexion.php';
require_once '../../../config.php';

$mes = $_POST['mes'];
$tipo = $_POST['tipo'];

$html = "<option value=''>Seleccione...</option>";

if ($tipo == 2) {
	$consulta = "SELECT SEMANA FROM planilla_semanas WHERE MES = '".$mes."' GROUP BY SEMANA";
	$result = $Link->query($consulta);
	if ($result->num_rows > 0) {
		$cnt = 1;
		while ($data = $result->fetch_assoc()) {
			$html .= "<option value='".$data['SEMANA']."' data-num='".$cnt."'>".$data['SEMANA']."</option>";
			$cnt++;
		}
	}
}

echo $html;
