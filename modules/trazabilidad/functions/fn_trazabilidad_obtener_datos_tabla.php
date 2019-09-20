<?php 
  require_once '../../../config.php';
  require_once '../../../db/conexion.php';

  $consulta = $_POST['consulta'];

  $data = [];

  $respuesta = $Link->query($consulta);
  if ($respuesta->num_rows > 0) {
  	while ($datos = $respuesta->fetch_assoc()) {
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