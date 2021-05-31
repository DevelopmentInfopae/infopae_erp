<?php
error_reporting(E_ALL);
require_once '../../../db/conexion.php';

$mes = $_POST['mesinicial'];
$annoinicial = $_POST['annoinicial'];
$annoinicial = substr($annoinicial, 2, 2);
$municipio = $_POST['municipio'];
$institucion = $_POST['institucion'];
$sede = $_POST['sede'];


////////////////////////////////////////////////////////////////////////////////////
//Consulta para obtener los datos de planillas dias del mes seleccionado.
$planillasDias = [];
$posicionColumna = 1;
$columnasDiasEntregasRes = "";
$consultaPlanillaDias = "SELECT * FROM planilla_dias WHERE mes = '$mes'";
$respuestaPlanillaDias = $Link->query($consultaPlanillaDias) or die("Error al consultar planilla_dias: ". $Link->error);
if ($respuestaPlanillaDias->num_rows > 0) {
  $registroPlanillaDias = $respuestaPlanillaDias->fetch_assoc();
}

foreach ($registroPlanillaDias as $clave => $planillaDias) {
  if ($posicionColumna > 5) {
    if ($mes == date("m")) {
      if ($planillaDias <= date("d") && $planillaDias != "") {
        $columnasDiasEntregasRes .= "er." . $clave ." + ";
      }
    } else {
      $columnasDiasEntregasRes .= "er." . $clave ." + ";
    }
  }

  $posicionColumna++;
}

// var_dump($columnasDiasEntregasRes);
////////////////////////////////////////////////////////////////////////////////////

//Se va a capturar el año actual para sacar la subcadena del periodo que permitira saber que tablas consultar de acuerdo al año.

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
    $consultaSegmento = "et.DESCRIPCION";
}

// Por Resguardo
if($segmento == 6){
    $serieSegmento = "Resguardo";
    $consultaSegmento = "resguardo";
}

// Por Poblacion Victima de Desplazamiento
if($segmento == 7){
    $serieSegmento = "Población Victima";
    $consultaSegmento = "po.nombre";
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
  SUM(". trim($columnasDiasEntregasRes, " + ") .") AS total, ".$consultaSegmento." as consulta
FROM
  entregas_res_".$mes.$annoinicial." er ";

if( $segmento == 5){ $vsql  = $vsql." LEFT JOIN etnia et ON er.etnia = et.id "; }
if( $segmento == 7){ $vsql  = $vsql." LEFT JOIN pobvictima po ON er.cod_pob_victima = po.id "; }
if( $segmento == 2){ $vsql  = $vsql." LEFT JOIN estrato es ON er.cod_estrato = es.id "; }
if( $segmento == 4){ $vsql  = $vsql." LEFT JOIN discapacidades dis ON er.cod_discap = dis.id "; }
if( $segmento == 8){ $vsql  = $vsql." LEFT JOIN grados g ON er.cod_grado = g.id "; }
if( $segmento == 9){ $vsql  = $vsql." LEFT JOIN jornada j ON er.cod_jorn_est = j.id "; }

$vsql = $vsql." WHERE 1 = 1 ";

if($municipio != ''){ $vsql = $vsql." and er.cod_mun_sede = '$municipio' "; }
if($institucion != ''){ $vsql = $vsql." AND er.cod_inst = '$institucion' "; }
if($sede != ''){ $vsql = $vsql." AND er.cod_sede = '$sede' "; }
$vsql = $vsql." GROUP BY (".$consultaSegmento.") ";

if ($segmento == 8) {
  $vsql .= 'ORDER BY er.cod_grado DESC';
}

$Link = new mysqli($Hostname, $Username, $Password, $Database);
$result = $Link->query($vsql);
$Link->close();

$valores=array();
$titulos=array();
$resultados=0;
// exit(var_dump($vsql));
while($row = $result->fetch_assoc()) {
    $tituloString = '';
    $valores[] = $row['total'];

    if($segmento == 10) {
      $auxZona = $row['consulta'];
      if ($auxZona == 1) {
        $auxZona = 'URBANA';
      } else if ($auxZona == 2) { $auxZona = 'RURAL'; }

      $titulos[] =  $auxZona;
    } else {
      $string = utf8_encode($row['consulta']);
      if (tieneAcentos($string)) {
        $tituloString = eliminar_acentos($string);
      }else{
        $tituloString = $row['consulta'];
      }
      $titulos[] = strtoupper($tituloString); 
    }
  }

    $resultados++;


echo json_encode(array("serieSegmento"=>$serieSegmento,"titulos"=>$titulos,"valores"=>$valores));


function tieneAcentos($string)
{
  if(preg_match('/á|é|í|ó|ú|Á|É|Í|Ó|Ú|à|è|ì|ò|ù|À|È|Ì|Ò|Ù|ñ|Ñ|ä|ë|ï|ö|ü|Ä|Ë|Ï|Ö|Ü|â|ê|î|ô|û|Â|Ê|Î|Ô|Û|ý|Ý|ÿ/', $string)===1)
    return true;
  return false;
}


function eliminar_acentos($cadena){
    
    //Reemplazamos la A y a
    $cadena = str_replace(
    array('Á', 'À', 'Â', 'Ä', 'á', 'à', 'ä', 'â', 'ª'),
    array('A', 'A', 'A', 'A', 'a', 'a', 'a', 'a', 'a'),
    $cadena
    );
 
    //Reemplazamos la E y e
    $cadena = str_replace(
    array('É', 'È', 'Ê', 'Ë', 'é', 'è', 'ë', 'ê'),
    array('E', 'E', 'E', 'E', 'e', 'e', 'e', 'e'),
    $cadena );
 
    //Reemplazamos la I y i
    $cadena = str_replace(
    array('Í', 'Ì', 'Ï', 'Î', 'í', 'ì', 'ï', 'î'),
    array('I', 'I', 'I', 'I', 'i', 'i', 'i', 'i'),
    $cadena );
 
    //Reemplazamos la O y o
    $cadena = str_replace(
    array('Ó', 'Ò', 'Ö', 'Ô', 'ó', 'ò', 'ö', 'ô'),
    array('O', 'O', 'O', 'O', 'o', 'o', 'o', 'o'),
    $cadena );
 
    //Reemplazamos la U y u
    $cadena = str_replace(
    array('Ú', 'Ù', 'Û', 'Ü', 'ú', 'ù', 'ü', 'û'),
    array('U', 'U', 'U', 'U', 'u', 'u', 'u', 'u'),
    $cadena );
 
    //Reemplazamos la N, n, C y c
    $cadena = str_replace(
    array('Ñ', 'ñ', 'Ç', 'ç'),
    array('N', 'n', 'C', 'c'),
    $cadena
    );
    
    return $cadena;
  }