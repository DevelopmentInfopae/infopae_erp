<?php
require_once '../../../db/conexion.php';
require_once '../../../config.php';
$data = [];
$consulta = "SELECT  DATE_FORMAT(cp.fecha, \"%d/%m/%Y %H:%i:%s\") AS fecha, IF(cp.tipo = 1, \"Entrada\", \"Salida\") as evento, e.Nombre, e.Nitcc, e.cargo FROM control_personal cp LEFT JOIN empleados e ON cp.num_doc = e.Nitcc ORDER BY cp.ID desc";
//echo "<br><br>$consulta<br><br>";

$resultado = $Link->query($consulta);
if($resultado->num_rows > 0){ while($row = $resultado->fetch_assoc()) { $data[] = $row; } }

$output = [
	'sEcho' => 1,
	'iTotalRecords' => count($data),
	'iTotalDisplayRecords' => count($data),
	'aaData' => $data
];

echo json_encode($output);