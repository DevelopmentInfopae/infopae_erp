<?php
error_reporting(E_ALL);
require_once '../../../db/conexion.php';
//var_dump($_POST);

$mes = $_POST['mesinicial'];
$annoinicial = $_POST['annoinicial'];
$annoinicial = substr($annoinicial, 2, 2);
$municipio = $_POST['municipio'];
$institucion = $_POST['institucion'];
$sede = $_POST['sede'];

//Se va a capturar el año actual para sacar la subcadena del periodo
//que permitira saber que tablas consultar de acuerdo al año.

if (isset($_SESSION['annoactual']) && $_SESSION['annoactual']!= '') {
  $annoactual = $_SESSION['annoactual'];
}
else{
  $annoactual = date('Y');
}

$_SESSION['annoactual'] = $annoactual;
$periodoactual = substr($annoactual, 2, 2);

if (isset($_SESSION['mesinicialConsulta']) && $_SESSION['mesinicialConsulta']!= '') {
  $mesinicialConsulta = $_SESSION['mesinicialConsulta'];
}

/*
Agrupamiento de datos:
1.  Edad
2.  Estrato
3.  Sisben
4.  Discapacidad
5.  Etnia
6.  Resguardo
7.  Poblacion Victima de Desplazamiento
8.  Grado
9.  Jornada Escolar
10. Zona Residencia Estudiante
*/

    $segmento = 2;
    $serieSegmento = "Estratos";
    $consultaSegmento = "es.nombre";

if (isset($_POST['segmento'])) {
    $segmento = $_POST['segmento'];
}

// Por Edad
if($segmento == 1){
    $serieSegmento = "Edad";
    $consultaSegmento = "edad";
}

// Por Estrato
if($segmento == 2){
    $serieSegmento = "Estratos";
    $consultaSegmento = "es.nombre";
}

// Por Sisben
if($segmento == 3){
    $serieSegmento = "Sisben";
    $consultaSegmento = "sisben";
}

// Por Discapacidad
if($segmento == 4){
    $serieSegmento = "Discapacidad";
    $consultaSegmento = "dis.nombre";
}

// Por Discapacidad
if($segmento == 5){
    $serieSegmento = "Etnia";
    $consultaSegmento = "etnia";
}

// Por Resguardo
if($segmento == 6){
    $serieSegmento = "Resguardo";
    $consultaSegmento = "resguardo";
}

// Por Poblacion Victima de Desplazamiento
if($segmento == 7){
    $serieSegmento = "Poblacion Victima de Desplazamiento";
    $consultaSegmento = "cod_pob_victima";
}

// Por Grado
if($segmento == 8){
    $serieSegmento = "Grado";
    $consultaSegmento = "g.nombre";
}

// Jornada Escolar
if($segmento == 9){
    $serieSegmento = "Jornada escolar";
    $consultaSegmento = "j.nombre";
}

// Zona Residencia Estudiante
if($segmento == 10){
    $serieSegmento = "Zona Residencia Estudiante";
    $consultaSegmento = "zona_res_est";
}

$vsql = " SELECT
  SUM(
    er.D1 + er.D2 + er.D3 + er.D4 + er.D5 + er.D6 + er.D7 + er.D8 + er.D9 + er.D10 + er.D11 + er.D12 + er.D13 + er.D14 + er.D15 + er.D16 + er.D17 + er.D18 + er.D19 + er.D20 + er.D21 + er.D22
  ) AS total, ".$consultaSegmento." as consulta
FROM
  entregas_res_".$mes.$annoinicial." er ";


if( $segmento == 2){ $vsql  = $vsql." LEFT JOIN estrato es ON er.cod_estrato = es.id "; }
if( $segmento == 4){ $vsql  = $vsql." LEFT JOIN discapacidades dis ON er.cod_discap = dis.id "; }
if( $segmento == 8){ $vsql  = $vsql." LEFT JOIN grados g ON er.cod_grado = g.id "; }
if( $segmento == 9){ $vsql  = $vsql." LEFT JOIN jornada j ON er.cod_jorn_est = j.id "; }



$vsql = $vsql." WHERE
  1 = 1 ";



if($municipio != ''){
  $vsql = $vsql." and er.cod_mun_sede = '$municipio' ";
}
if($institucion != ''){
  $vsql = $vsql." AND er.cod_inst = '$institucion' ";
}
if($sede != ''){
  $vsql = $vsql." AND er.cod_sede = '$sede' ";
}


$vsql = $vsql." GROUP BY (".$consultaSegmento.")  ";




//echo "<br><br>".$vsql."<br><br>";






    $Link = new mysqli($Hostname, $Username, $Password, $Database);
    $result = $Link->query($vsql);
    $Link->close();


    $valores=array();
    $titulos=array();
    $resultados=0;

    while($row = $result->fetch_assoc()) {
        $valores[] = $row['total'];



        if($segmento == 10){

         // 1=Urbana y 2=Rural

          $auxZona = $row['consulta'];
          if ($auxZona == 1) {
            $auxZona = 'URBANA';
          }
          else if ($auxZona == 2) {
            $auxZona = 'RURAL';
          }



          $titulos[] =  $auxZona;

        }
        else{

        $titulos[] = $row['consulta'];


        }






        $resultados++;
    }

//var_dump($serieSegmento);
//var_dump($titulos);
//var_dump($valores);
echo json_encode(array("serieSegmento"=>$serieSegmento,"titulos"=>$titulos,"valores"=>$valores));
