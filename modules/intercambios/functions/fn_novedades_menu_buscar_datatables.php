<?php
require_once '../../../db/conexion.php';
require_once '../../../config.php';

$periodoActual = $_SESSION['periodoActual'];

$consultaNovedad = "SELECT 
						nm.id,
						nm.mes AS mes,
						nm.semana AS semana,
						IF(nm.tipo_intercambio = 1, 'Intercambio de alimento', IF(nm.tipo_intercambio = 2, 'Intercambio de preparación', 'Intercambio de día de menú')) AS tipo, 
						nm.menu AS menu,
						nm.tipo_complem AS tipo_complemento,
						ge.DESCRIPCION AS grupo_etario,
						LOWER(DATE_FORMAT(nm.fecha_registro, '%d/%m/%Y %h:%I:%s %p')) AS fecha_registro,
						DATE_FORMAT(nm.fecha_vencimiento, '%d/%m/%Y') AS fecha_vencimiento,
						IF(nm.estado = 1, 'Activo', 'Reversado') AS estado,
						(SELECT descripcion FROM variacion_menu vm WHERE vm.id = nm.variacion_menu ) AS variacion
					FROM novedades_menu nm
					left join grupo_etario ge ON ge.ID = nm.cod_grupo_etario
					LEFT JOIN productos$periodoActual p ON p.Codigo = nm.cod_producto
					ORDER BY nm.fecha_registro desc";
// echo "$consultaNovedad";
$data = array();
$resultadoNovedades = $Link->query($consultaNovedad) or die ('Consulta de novedades de menú'. mysqli_error($Link));
if($resultadoNovedades){
	if($resultadoNovedades->num_rows > 0){
		while($registrosSedes = $resultadoNovedades->fetch_assoc()) {
			$data[] = $registrosSedes;
		}
	}
}

$output = [
	'sEcho' => 1,
	'iTotalRecords' => count($data),
	'iTotalDisplayRecords' => count($data),
	'aaData' => $data
];

echo json_encode($output);
