<?php
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
   $consultaSegmento = "estr.nombre";







   if (isset($_POST['segmento'])) {
       $segmento = $_POST['segmento'];
   }

   // Por Edad
   if($segmento == 1){
       $serieSegmento = "Edad";
       $consultaSegmento = "est.edad";
   }

   // Por Estrato
   if($segmento == 2){
       $serieSegmento = "Estratos";
       $consultaSegmento = "estr.nombre";
   }

   // Por Sisben
   if($segmento == 3){
       $serieSegmento = "Sisben";
       $consultaSegmento = "est.sisben";
   }

   // Por Discapacidad
   if($segmento == 4){
       $serieSegmento = "Discapacidad";
       $consultaSegmento = "disc.nombre";
   }

   // Por Discapacidad
   if($segmento == 5){
       $serieSegmento = "Etnia";
       $consultaSegmento = "est.etnia";
   }

   // Por Resguardo
   if($segmento == 6){
       $serieSegmento = "Resguardo";
       $consultaSegmento = "est.resguardo";
   }

   // Por Poblacion Victima de Desplazamiento
   if($segmento == 7){
       $serieSegmento = "Poblacion Victima de Desplazamiento";
       $consultaSegmento = "pob.nombre";
   }

   // Por Grado
   if($segmento == 8){
       $serieSegmento = "Grado";
       $consultaSegmento = "gra.nombre";
   }

   // Jornada Escolar
   if($segmento == 9){
       $serieSegmento = "Jornada escolar";
       $consultaSegmento = "jor.nombre";
   }

   // Zona Residencia Estudiante
   if($segmento == 10){
       $serieSegmento = "Zona Residencia Estudiante";
       $consultaSegmento = "est.zona_res_est";
   }




$vsql="select COUNT($consultaSegmento) as total, $consultaSegmento as consulta

from entregas".$mesinicialConsulta.$periodoactual." ent
join estudiantes".$periodoactual." est on est.num_doc = ent.id_estudiante



left join sedes".$periodoactual." sed on sed.cod_sede = est.cod_sede



left join ubicacion ubi on ubi.CodigoDANE = sed.cod_mun_sede and ubi.ETC = 0

left join discapacidades disc on est.cod_discap = disc.id 
left join estrato estr on est.cod_estrato = estr.id 
left join pobvictima pob on pob.id = est.cod_pob_victima 
left join grados gra on gra.id = est.cod_grado 
left join jornada jor on jor.id = est.cod_jorn_est



where 1=1 ";



    if(isset($_POST["municipio"]) && $_POST["municipio"] != "" ){
        $municipio = $_POST["municipio"];
        $vsql = $vsql." and ubi.CodigoDANE = '$municipio' ";
    }

    if(isset($_POST["institucion"]) && $_POST["institucion"] != ""){
        $institucion = $_POST["institucion"];
        $vsql = $vsql." and sed.cod_inst = '$institucion' ";
    }

    if(isset($_POST["sede"]) && $_POST["sede"]!=""){
        $sede = $_POST["sede"];
        $vsql = $vsql." and sed.cod_sede = '$sede' ";
    }

    if(isset($_POST["estudiante"]) && $_POST["estudiante"] != ""){
        $estudiante = $_POST["estudiante"];
        $vsql = $vsql." and est.num_doc = '$estudiante' ";
    }



   if(isset($_POST["diainicial"]) && $_POST["diainicial"] != "" ){
      $diainicial = $_POST["diainicial"];
      $vsql = $vsql." and DAYOFMONTH(STR_TO_DATE(ent.fecha_hora, '%d/%m/%Y %H:%i:%s')) >= ".$diainicial." ";
   }

   if(isset($_POST["mesinicial"]) && $_POST["mesinicial"] != "" ){
      $mesinicial = $_POST["mesinicial"];
      $vsql = $vsql." and MONTH(STR_TO_DATE(ent.fecha_hora, '%d/%m/%Y %H:%i:%s')) >= ".$mesinicial." ";
   }

   if(isset($_POST["annoinicial"]) && $_POST["annoinicial"] != "" ){
      $annoinicial = $_POST["annoinicial"];
      $vsql = $vsql." and YEAR(STR_TO_DATE(ent.fecha_hora, '%d/%m/%Y %H:%i:%s')) >= ".$annoinicial." ";
   }


    if(isset($_POST["diafinal"]) && $_POST["diafinal"] != "" ){
      $diafinal = $_POST["diafinal"];
      $vsql = $vsql." and DAYOFMONTH(STR_TO_DATE(ent.fecha_hora, '%d/%m/%Y %H:%i:%s')) <= ".$diafinal." ";
   }

   if(isset($_POST["mesfinal"]) && $_POST["mesfinal"] != "" ){
      $mesfinal = $_POST["mesfinal"];
      $vsql = $vsql." and MONTH(STR_TO_DATE(ent.fecha_hora, '%d/%m/%Y %H:%i:%s')) <= ".$mesfinal." ";
   }

   if(isset($_POST["annoinicial"]) && $_POST["annoinicial"] != "" ){
      $annofinal = $_POST["annofinal"];
      $vsql = $vsql." and YEAR(STR_TO_DATE(ent.fecha_hora, '%d/%m/%Y %H:%i:%s')) <= ".$annoinicial." ";
   }




    $vsql = $vsql." GROUP BY ($consultaSegmento) ";


 //Imprimiendo Consulta
//echo "<br><br>Consulta de la grafica<br><br>".$vsql."<br><br>";
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
