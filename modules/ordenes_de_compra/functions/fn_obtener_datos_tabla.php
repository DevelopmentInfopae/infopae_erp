<?php
  require_once '../../../config.php';
  require_once '../../../db/conexion.php';

  $consulta = $_POST['consulta'];

  $data = [];

  $respuesta = $Link->query($consulta) or die('Error realizar la consulta: '. $Link->error);
  if ($respuesta->num_rows > 0) {
  	while ($datos = $respuesta->fetch_assoc()) {

      $datos['input'] = "<input  type =      'checkbox' 
                                class =     'i-checks ordenes text-center' 
                                value =     '" .$datos['Num_OCO']. "'  
                                name =      '" .$datos['Num_OCO']. "'
                                id =        '" .$datos['Num_OCO']. "'
                                semana =    '" .$datos['Semana']. "' 
                                complemento='" .$datos['Tipo_Complem']. "' 
                                tipo =      '" .$datos['tipodespacho_nm']."' 
                        />";
      // exit(var_dump($datos));  }              
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