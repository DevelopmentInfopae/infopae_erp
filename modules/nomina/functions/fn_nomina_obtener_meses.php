<?php

require_once '../../../db/conexion.php';
require_once '../../../config.php';

$tipo = $_POST['tipo'];

$meses = [
		 	'01' => 'Enero',
		 	'02' => 'Febrero',
		 	'03' => 'Marzo',
		 	'04' => 'Abril',
		 	'05' => 'Mayo',
		 	'06' => 'Junio',
		 	'07' => 'Julio',
		 	'08' => 'Agosto',
		 	'09' => 'Septiembre',
		 	'10' => 'Octubre',
		 	'11' => 'Noviembre',
		 	'12' => 'Diciembre',
		 ];
$html = "<option value=''>Seleccione...</option>";
if ($tipo == 2) {
	$consulta = "SELECT MES FROM planilla_semanas GROUP BY MES;";
	$result = $Link->query($consulta);
	if ($result->num_rows > 0) {
		while ($data = $result->fetch_assoc()) {
			$html.="<option value='".$data['MES']."'>".$meses[$data['MES']]."</option>";
		}
	}
}

echo $html;
