<?php 
  require_once '../../../config.php';
  require_once '../../../db/conexion.php';

  $consulta = $_POST['consulta'];

  $data = [];
  $num = 0;

  $respuesta = $Link->query($consulta);
  if ($respuesta->num_rows > 0) {
  	while ($datos = $respuesta->fetch_assoc()) {
      $num++;
      if (isset($datos['Id'])) {
        $datos['input'] = "<input type='checkbox' class='checkDespacho' value='".$datos['Id']."' name='idDespacho[]' data-num='$num' data-inst='".$datos['cod_inst']."'><input type='checkbox' value='".$datos['BodegaDestino']."' id='sede_$num' name='sedes[]' style='display:none;'>";
      }
      if (isset($datos['Aprobado'])) {
        if ($datos['Aprobado'] == "0") {
          $datos['Aprobado'] = "Pendiente";
        } else if ($datos['Aprobado'] == "1") {
          $datos['Aprobado'] = "Aprobado";
        }
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