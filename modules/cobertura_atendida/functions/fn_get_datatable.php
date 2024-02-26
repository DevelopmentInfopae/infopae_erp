<?php
require_once '../../../db/conexion.php';
require_once '../../../config.php';
// exit(var_dump($_POST));
$mes = $_POST["datos"]["mes"];
$semana = $_POST["datos"]["semana"];
$complementos = isset($_POST["datos"]["complementos"]) ? $_POST["datos"]["complementos"] : [] ;
$municipio = $_POST["datos"]["municipio"];
$ruta = $_POST["datos"]["ruta"];
$sector = $_POST["datos"]["sector"];
$institucion = $_POST["datos"]["institucion"];
$sede = $_POST["datos"]["sede"];
$periodoaño = $_SESSION['periodoActual'];
$datosE = [];
// // echo "El mes que elejiste es: $mes" . "\n";
// echo "La semana que elejiste es: $semana" . "\n";
// // echo "Los complementos que elejiste son: " . "\n";
// // print_r($complementos);
// // echo "El municipio actual es: " . $municipio . "\n";
// // echo "La ruta actual es: " . $ruta . "\n";
// echo "El sector actual es: " . $sector . "\n";
// echo "La institucion actual es: " . $institucion . "\n";
// echo "La sede actual es: " . $sede . "\n";
// // echo "El año/periodo en el que estamos es: " . $periodoaño . "\n";

// echo "---------------------apuntes arriba-----------------------" . "\n";


$complementos = isset($_POST["data"]["complementos"]) ? $_POST["data"]["complementos"] : array();

if (empty($complementos)) {
    $consulta_complementos = "SELECT codigo FROM tipo_complemento";
    $respuestas_complemento = $Link->query($consulta_complementos);

    $complementos = array();

    if ($respuestas_complemento->num_rows > 0) {
        while ($data_complemento = $respuestas_complemento->fetch_object()) {
            $complementos[] = $data_complemento->codigo;
        }
    }
}



$consulta_semanas = "SELECT DISTINCT (semana) AS semana FROM planilla_semanas WHERE mes = '$mes'";
if (isset($semana) && $semana != '') {
    $consulta_semanas.= "AND semana = '$semana' ";
}

$guardofi = [];

$respuesta_sem = $Link->query($consulta_semanas);
if ($respuesta_sem->num_rows > 0) {
    while ($data_semanas = $respuesta_sem->fetch_object()) {

         foreach ($complementos as $keyc => $valuec) {

            $consulta_oficial ="SELECT s.cod_sede,s.sector,s.nom_inst,s.nom_sede,$valuec
            FROM sedes$periodoaño s
            INNER JOIN sedes_cobertura sc
            ON s.cod_sede = sc.cod_sede WHERE sc.mes = '$mes' AND sc.semana = '". $data_semanas->semana ."' ";

            if (!empty($sector)) {
                $consulta_oficial .= " AND sedes$periodoaño.sector = '$sector' ";
            }

            if (!empty($institucion)) {
                $consulta_oficial .= " AND sedes$periodoaño.cod_inst = '$institucion' ";
            }

            if (!empty($sede)) {
                $consulta_oficial .= " AND sedes$periodoaño.cod_sede = $sede";
            }

            $respuesta_total = $Link->query($consulta_oficial);
            if ($respuesta_total->num_rows > 0) {
                while ($data_total = $respuesta_total->fetch_object()) {      

                    $guardofi[$data_total->cod_sede][$valuec][$data_semanas->semana] = $data_total;
            
               }
            } 
         }
      }

    //   foreach ($guardofi as $keyz => $valuez) {
    //     exit(var_dump($valuez['APS']['21']));
    //   }
    


}

// ---------------------------Peticion oficial---------------------------

$output = [
    'sEcho' => 1,
    'iTotalRecords' => count($datosE),
    'iTotalDisplayRecords' => count($datosE),
    'aaData' => $datosE,
];

echo json_encode($output);
