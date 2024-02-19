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
$complementos_str = implode(', ', $complementos);


// ---------------------------Peticion oficial---------------------------
    $consulta_oficial ="SELECT sector,nom_inst,nom_sede, mes, semana, $complementos_str
                        FROM sedes$periodoaño
                        INNER JOIN sedes_cobertura
                        ON sedes$periodoaño.cod_sede = sedes_cobertura.cod_sede WHERE sedes_cobertura.mes = '$mes'";


// si se proporciona la semana, solo me trae la que le indico
if (!empty($semana)) {
    $consulta_oficial .= " AND sedes_cobertura.semana = '$semana' ";
}

// traer sector
if (!empty($sector)) {
    $consulta_oficial .= " AND sedes$periodoaño.sector = '$sector' ";
}

//trae institucion
if (!empty($institucion)) {
    $consulta_oficial .= " AND sedes$periodoaño.cod_inst = '$institucion' ";
}

if (!empty($sede)) {
    $consulta_oficial .= " AND sedes$periodoaño.cod_sede = $sede";
}


$respuesta_oficial = $Link->query($consulta_oficial);
if ($respuesta_oficial->num_rows > 0) {
    while ($data_oficial = $respuesta_oficial->fetch_object()) {       
        /* integracion de la consulta oficial para generar los datos de la respuesta */   
        $datosE[] = $data_oficial;  
    }
}

// echo "---------------------arriba consulta oficial-----------------------" . "\n";
$output = [
    'sEcho' => 1,
    'iTotalRecords' => count($datosE),
    'iTotalDisplayRecords' => count($datosE),
    'aaData' => $datosE
];

echo json_encode($output);

