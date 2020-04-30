<?php
  require_once '../../../config.php';
  require_once '../../../db/conexion.php';

  $consulta = $_POST['consulta'];

  $data = [];

  $respuesta = $Link->query($consulta) or die('Error realizar la consulta: '. $Link->error);
  if ($respuesta->num_rows > 0) {
  	while ($datos = $respuesta->fetch_assoc()) {
      if ($datos['Umedida'] == 'u') {
        $datos['Cantidad'] = number_format((float) $datos['Cantidad'], 0, ',', '.');
      } else {
        $datos['Cantidad'] = number_format((float) $datos['Cantidad'], 2, ',', '.');
      }

  		$data[] = $datos;
  	}
  }

   $output = [
      'sEcho' => 1,
      'iTotalRecords' => count($data),
      'iTotalDisplayRecords' => count($data),
      'aaData' => $data
    ];

  echo json_encode($output);