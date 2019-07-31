<?php
  require_once '../../../db/conexion.php';
  require_once '../../../config.php';

  $consultaNovedad = "SELECT 
nm.id,
nm.mes AS mes,
nm.semana AS semana,




IF(nm.tipo_intercambio = 1, 'Intercambio de alimento', IF(nm.tipo_intercambio = 2, 'Intercambio de preparación', 'Intercambio de día de menú')) AS tipo, 


nm.tipo_complem AS tipo_complemento,
ge.DESCRIPCION AS grupo_etario,

LOWER(DATE_FORMAT(nm.fecha_registro, '%d/%m/%Y %h:%I:%s %p')) AS fecha_registro,







DATE_FORMAT(nm.fecha_vencimiento, '%d/%m/%Y') AS fecha_vencimiento,

IF(nm.estado = 1, 'Activo', 'Reversado') AS estado


FROM novedades_menu nm
left join grupo_etario ge ON ge.ID = nm.cod_grupo_etario
LEFT JOIN productos19 p ON p.Codigo = nm.cod_producto
ORDER BY nm.fecha_registro desc";


//echo $consultaNovedad;


  $resultadoNovedades = $Link->query($consultaNovedad);
  if($resultadoNovedades->num_rows > 0){
    while($registrosSedes = $resultadoNovedades->fetch_assoc()) {
			//$aux = $registrosSedes['fecha_hora'];
			//$aux = date("d/m/Y h:i:s a", strtotime($aux));
			//$registrosSedes['fecha_hora'] = $aux;
      $data[] = $registrosSedes;
    }
  }

  $output = [
    'sEcho' => 1,
    'iTotalRecords' => count($data),
    'iTotalDisplayRecords' => count($data),
    'aaData' => $data
  ];

  echo json_encode($output);
