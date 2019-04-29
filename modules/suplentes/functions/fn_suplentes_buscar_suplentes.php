<?php
require_once '../../../config.php';
require_once '../../../db/conexion.php';

$data = [];
$codigo_sede = $Link->real_escape_string($_POST['sede']);
$semana = $Link->real_escape_string($_POST['semana']);

$consulta_suplentes = "SELECT *, CONCAT(sup.nom1, ' ', sup.nom2, ' ', sup.ape1, ' ', sup.ape2) AS nombre, g.nombre as grado, jor.nombre as jornada
FROM suplentes$semana sup
LEFT JOIN grados g ON g.id = sup.cod_grado
LEFT JOIN jornada jor ON jor.id = sup.cod_jorn_est
WHERE cod_mun_sede = '$codigo_sede'";
$respuesta_suplentes = $Link->query($consulta_suplentes) or die('Error al consultar los suplentes: '. $Link->error);

if($respuesta_suplentes->num_rows > 0){
  while($suplente = $respuesta_suplentes->fetch_assoc()) {
    $data[] = $suplente;
  }
}

$output = [
  'sEcho' => 1,
  'iTotalRecords' => count($data),
  'iTotalDisplayRecords' => count($data),
  'aaData' => $data
];

echo json_encode($output);