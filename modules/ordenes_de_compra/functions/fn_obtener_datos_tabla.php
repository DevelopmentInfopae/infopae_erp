<?php
  require_once '../../../config.php';
  require_once '../../../db/conexion.php';

  $consulta = $_POST['consulta'];

  $data = [];

  $respuesta = $Link->query($consulta) or die('Error realizar la consulta: '. $Link->error);
  if ($respuesta->num_rows > 0) {
  	while ($datos = $respuesta->fetch_assoc()) {
      $disabled = '';
      if ($datos['estado'] == 0) {
        $disabled = 'disabled';
      }
      $datos['input'] = "<input  type =      'checkbox' 
                                class =     'i-checks ordenes text-center' 
                                value =     '" .$datos['Num_OCO']. "'  
                                name =      '" .$datos['Num_OCO']. "'
                                id =        '" .$datos['Num_OCO']. "'
                                semana =    '" .$datos['Semana']. "' 
                                complemento='" .$datos['Tipo_Complem']. "' 
                                tipo =      '" .$datos['tipodespacho_nm']."' 
                                bodega =      '" .$datos['bodegaId']."' 
                                nameWarehouse =      '" .$datos['bodega']."' 
                                estado =      '" .$datos['estado']."'  $disabled 
                        />";
      $estadoOrden = '';
      if ($datos['estado'] == 0) { $estadoOrden = 'Eliminado';  $clase = "class='label label-danger'";  $icon = "<i class='fas fa-times'></i>";}                  
      if ($datos['estado'] == 1) { $estadoOrden = 'Recibido';   $clase = "class='label label-primary'"; $icon = "<i class='fas fa-check'></i>"; }                  
      if ($datos['estado'] == 2) { $estadoOrden = 'Pendiente';  $clase = "class='label label-warning'"; $icon = "<i class='fas fa-circle'></i>";}                  
      $datos['estado'] = "<span  $clase >$icon $estadoOrden</span>";              
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