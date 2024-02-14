<?php
require_once '../../../db/conexion.php';
require_once '../../../config.php';

$mes = $_POST["data"]["mes"];
$semana = $_POST["data"]["semana"];
$complementos = $_POST["data"]["complementos"];
$municipio = $_POST["data"]["municipio"];
$ruta = $_POST["data"]["ruta"];
$sector = $_POST["data"]["sector"];
$institucion = $_POST["data"]["institucion"];
$sede = $_POST["data"]["sede"];
$periodoaño = $_SESSION['periodoActual'];

echo "El mes que elejiste es: $mes" . "\n";
echo "La semana que elejiste es: $semana" . "\n";
echo "Los complementos que elejiste son: " . "\n";
print_r($complementos);
echo "El municipio actual es: " . $municipio . "\n";
echo "La ruta actual es: " . $ruta . "\n";
echo "El sector actual es: " . $sector . "\n";
echo "La institucion actual es: " . $institucion . "\n";
echo "La sede actual es: " . $sede . "\n";
echo "El año/periodo en el que estamos es: " . $periodoaño . "\n";



echo "---------------------apuntes arriba-----------------------" . "\n";


// semanas
// $consulta_semanas = "SELECT DISTINCT (semana) AS semana FROM planilla_semanas WHERE mes = '$mes'";
// if ($semana && $semana != "") {
//     $consulta_semanas .= "AND semana = '$semana'";
// }

// $respuestas_semanas = $Link->query($consulta_semanas);
// if ($respuestas_semanas->num_rows > 0) {
//     while ($data_semanas = $respuestas_semanas->fetch_object()) {
//         var_dump($data_semanas);
//     }
// }

// //sede
// $consulta_sede = "SELECT nom_sede FROM sedes24 WHERE cod_sede = " . $sede;

// $respuesta_sede = $Link->query($consulta_sede);
// if ($respuesta_sede->num_rows > 0) {
//     while ($data_sede = $respuesta_sede->fetch_object()) {
//         var_dump($data_sede);
//     }
// }

// echo "-------------------final abajo-------------------" . "\n";
//final


$complementos = isset($_POST["data"]["complementos"]) ? $_POST["data"]["complementos"] : array();

if (empty($complementos)) {
    $consulta_complementos = "SELECT codigo FROM tipo_complemento";
    $respuestas_complemento = $Link->query($consulta_complementos);

    if ($respuestas_complemento->num_rows > 0) {
        while ($data_complemento = $respuestas_complemento->fetch_object()) {
            $complementos[] = $data_complemento->codigo;
        }
    }
}

$complementos_str = implode(', ', $complementos);

$consulta_oficial = "SELECT nom_sede, mes, semana, $complementos_str
                    FROM sedes$periodoaño
                    INNER JOIN sedes_cobertura
                    ON sedes24.cod_sede = sedes_cobertura.cod_sede
                    WHERE sedes24.cod_sede = $sede AND sedes_cobertura.mes = '$mes';";

$respuesta_oficial = $Link->query($consulta_oficial);

if ($respuesta_oficial->num_rows > 0) {
    while ($data_oficial = $respuesta_oficial->fetch_object()) {
        var_dump($data_oficial);
    }
}




// if (isset($_GET['mes']) && $_GET['mes'] != '') {

//     if (strlen($_GET['semana']) == 2) {
//         echo "elejiste solo una semana";
//         echo "y es " . $_GET['semana'];
//         $respuestaSemanas = $Link->query("SELECT * from planilla_semanas");
//         if ($respuestaSemanas->num_rows > 0) {
//             while ($dataSemanas = $respuestaSemanas->fetch_object()) {
//                 var_dump($dataSemanas);
//             }                                                                                                               
//         }

//     }else{
//         echo "se elijieron todas";
//                $respuestaSemanas = $Link->query(" SELECT DISTINCT (SEMANA) AS semana FROM planilla_semanas WHERE MES ='" .$_GET['mes']. "'");
//     if ($respuestaSemanas->num_rows > 0) {
//         while ($dataSemanas = $respuestaSemanas->fetch_object()) {
//             var_dump($dataSemanas);
//         }                                                                                                               
//     }
//     }
// }





// if (isset($_GET['mes']) && $_GET['mes'] != '') {

//     if (strlen($_GET['semana']) == 2) {
//         echo "elejiste solo una semana bro";
//         echo "y es " . $_GET['semana'];
//         $respuestaSemanas = $Link->query("SELECT * from planilla_semanas");
//         if ($respuestaSemanas->num_rows > 0) {
//             while ($dataSemanas = $respuestaSemanas->fetch_object()) {
//                 var_dump($dataSemanas);
//             }                                                                                                               
//         }

//     }else{
//         echo "se elijieron todas";
//                $respuestaSemanas = $Link->query(" SELECT DISTINCT (SEMANA) AS semana FROM planilla_semanas WHERE MES ='" .$_GET['mes']. "'");
//     if ($respuestaSemanas->num_rows > 0) {
//         while ($dataSemanas = $respuestaSemanas->fetch_object()) {
//             var_dump($dataSemanas);
//         }                                                                                                               
//     }
//     }
// }

// $definitiva = "SELECT";
// $respuestas_definitiva = $Link->query($definitiva);


echo "---------------------arriba consulta oficial-----------------------" . "\n";

// $guardo_datos = [];
//  $guardo_datos[] = $_POST;
//  print_r($guardo_datos);

//  $sede = $guardo_datos[0]['data']['sede'];
//  echo $sede;


// $tem = [];
// $consulta_complemento = "SELECT codigo FROM tipo_complemento;";
// $respuestas_complemento = $Link->query($consulta_complemento);
// if ($respuestas_complemento->num_rows > 0 ) {
//     while ($data_complemento = $respuestas_complemento->fetch_object()) {
//      $aux = $data_complemento->codigo;
//      $consulta_s = "SELECT " . $aux . " FROM priorizacion22 WHERE cod_sede = $sede";
//      $respuesta = $Link->query($consulta_s);
//      if ($respuesta->num_rows > 0 ) {
//         while ($data2_complemento = $respuesta->fetch_object()){
//  $tem['16830700025101']['22'][$aux] = $data2_complemento->$aux;
//         }
//      }
//     }
// }

// print_r($tem);

// $guardo_datos = [];
// $guardo_datos[] = $_POST;
// $sede = $guardo_datos[0]['data']['sede'];
// $semana = $guardo_datos[0]['data']['semana'] . "\n";


// $tem = [];
// $consulta_complemento = "SELECT codigo FROM tipo_complemento;";
// $respuestas_complemento = $Link->query($consulta_complemento);

// if ($respuestas_complemento->num_rows > 0) {
//     while ($data_complemento = $respuestas_complemento->fetch_object()) {
//         $aux = $data_complemento->codigo;
    
//         $pruebados = [];
//         $pruebaconsulta = "SHOW TABLES LIKE 'priorizacion%';";
//         $resultado = $Link->query($pruebaconsulta);

//         if ($resultado) {
//             while ($fila = $resultado->fetch_assoc()) {
//                 $pruebados[] = $fila;
//             }
    
//         } else {
//             echo "Error en la consulta: " . $Link->error;
//         }

//         $largor = count($pruebados);

//         for ($i = 0; $i < $largor; $i++) {
//             $tabla_actual = reset($pruebados[$i]);
//             $consulta_s = "SELECT " . $aux . " FROM " . $tabla_actual . " WHERE cod_sede = $sede";
//             $respuesta = $Link->query($consulta_s);

//             if ($respuesta->num_rows > 0) {
//                 while ($data2_complemento = $respuesta->fetch_object()) {
//                     $tem[$sede][$semana][$tabla_actual][$aux] = $data2_complemento->$aux;
//                 }
//             }
//         }
//     }
// }

// var_dump($tem);


// echo "---------------------arriba consulta de prueba apoyo-----------------------" . "\n";


// echo ".............";

// $output = [
//     'sEcho' => 1,
//     'iTotalRecords' => count($datosE),
//     'iTotalDisplayRecords' => count($datosE),
//     'aaData' => $datosE
// ];

//  exit(var_dump($tem));

// foreach ($guardo_datos as $clave => $valor) {
//     echo $clave . ": " . $valor . "<br>";
// }


// echo "Datos final<br>";
// print_r($guardo_datos);


// echo "datos de la sede, acceso por [ ].";


// echo ".............";



// $pruebados = [];
// $pruebaconsulta = "SELECT
// nom_sede,
// SUM(APS) AS total_APS,
// SUM(CAJMPS) AS total_CAJMPS,
// SUM(CAJMRI) AS total_CAJMRI,
// SUM(CAJTPS) AS total_CAJTPS,
// SUM(CAJTRI) AS total_CAJTRI,
// (SUM(APS) + SUM(CAJMPS) + SUM(CAJMRI) + SUM(CAJTPS) + SUM(CAJTRI)) AS total_general
// FROM
// sedes24
// INNER JOIN
// priorizacion22 ON sedes24.cod_sede = priorizacion22.cod_sede
// GROUP BY
// nom_sede;";
// $resultado = $Link->query($pruebaconsulta);

// if ($resultado) {
//     while ($fila = $resultado->fetch_assoc()) {
//         $pruebados[] = $fila;
//     }
//     print_r($pruebados);
// } else {
//     echo "Error en la consulta: " . $Link->error;
// }

// echo ".............";
