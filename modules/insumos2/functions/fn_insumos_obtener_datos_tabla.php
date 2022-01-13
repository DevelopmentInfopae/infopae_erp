<?php 
require_once '../../../config.php';
require_once '../../../db/conexion.php';

$consulta = $_POST['consulta'];
// var_dump($consulta); exit();
$data = [];
$num = 0;

$respuesta = $Link->query($consulta);
if ($respuesta->num_rows > 0) {
  	while ($datos = $respuesta->fetch_assoc()) {
        $num++;
      	if (isset($datos['id'])) {
        	$datos['id'] = " <input type='checkbox'  class='checkDespacho' value='".$datos['codigoSede']."' id='sede_$num' name='sedes[]' style='display:none;' data-num='$num' data-inst='".$datos['codigoInst']."' data-iddespacho='".$datos['id']."' data-mesdespacho='".$datos['mesDespacho']."'>";
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
// exit(var_dump($data));}
echo json_encode($output);