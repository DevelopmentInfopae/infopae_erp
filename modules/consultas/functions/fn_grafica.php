<?php
    error_reporting(E_ALL);
    require_once 'db/conexion.php';



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



?>

<?php
    // Consulta a la base de datos
    /*$vsql = "
    select COUNT( $consultaSegmento) as total, $consultaSegmento as consulta

    from estudiantes est


    left join sedes sed on sed.cod_sede = est.cod_sede
    left join ubicacion ubi on ubi.CodigoDANE = sed.cod_mun_sede
    left join discapacidades disc on est.cod_discap = disc.id
    left join estrato estr on est.cod_estrato = estr.id
    left join pobvictima pob on pob.id = est.cod_pob_victima
    left join grados gra on gra.id = est.cod_grado
    left join jornada jor on jor.id = est.cod_jorn_est
    where 1=1


";*/


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



    echo "<br><br>".$vsql."<br><br>";






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










// Fin Consulta a la base de datos


?>





<?php
    /*
        Tipos de grafico
        1.  Pastel
        2.  Barras
    */

    $tipoGrafico = $_POST['tipoGrafico'];
    if ($tipoGrafico==1) {
        ?>


                    <!-- Comienza el Script que hace funcionar al gráfico -->
  <script type="text/javascript">
$(function () {

    /*var titulo = 'aaa';
    console.log(titulo);*/
var titulo = "<?php echo 'Segmentación por: '.$serieSegmento; ?>";
console.log(titulo);
    $('#container2').highcharts({
        chart: {
            plotBackgroundColor: null,
            plotBorderWidth: null,
            plotShadow: false
        },
        title: {
            text: titulo



        },
        tooltip: {
            pointFormat: '<b>{point.y:.1f}</b>'
        },
        plotOptions: {
            pie: {
                allowPointSelect: true,
                cursor: 'pointer',
                dataLabels: {
                    enabled: true,
                    format: '<b>{point.name}</b>: {point.y:.1f} ',
                    style: {
                        color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black'
                    }
                }
            }
        },
        series: [{
            type: 'pie',
            name: '',
            data: [

            <?php
                //echo "  ['Firefox',   15],
                //['IE',       4],
                //['Others',   1]";

                for($i=0; $i<$resultados ; $i ++){
                    if($i > 0){echo",";}
                    echo "  ['$titulos[$i]',  $valores[$i]]";






                }






            ?>

            ]
        }]
    });
});


    </script>
<!-- Termina el Script que hace funcionar al gráfico -->
<div id="container2" style="min-width: 310px; height: 400px; max-width: 600px; margin: 0 auto"></div>


        <?php

    }

    else{


?>



<!-- Comienza el Script que hace funcionar al gráfico -->
 <script type="text/javascript">
$(function () {

    var titulo = "<?php echo 'Segmentación por: '.$serieSegmento; ?>";
    $('#container').highcharts({
        chart: {
            type: 'column'
        },
        title: {
            text: titulo
        },
        subtitle: {
            text: ''
        },
        xAxis: {
            categories: [

                ''
            ],
            crosshair: true
        },
        yAxis: {
            min: 0,
            title: {
                text: 'Estudiantes'
            }
        },
        tooltip: {
            headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
            pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
                '<td style="padding:0"><b>{point.y:.0f}</b></td></tr>',
            footerFormat: '</table>',
            shared: true,
            useHTML: true
        },
        plotOptions: {
            column: {
                pointPadding: 0.2,
                borderWidth: 0
            }
        },
        series: [

         <?php
                //echo "  ['Firefox',   15],
                //['IE',       4],
                //['Others',   1]";


                    //if($i > 0){echo",";}
                   // echo "  ['$titulos[$i]',  $valores[$i]]";
?>



                    <?php






?>



<?php $t=$titulos[0]; $v=$valores[0]; ?>


{name: '<?php echo $t; ?>', data: [<?php echo $v;  ?>] }

                    <?php for($i=1 ; $i<$resultados ; $i++){
                        $t=$titulos[$i];
                        $v=$valores[$i];
                        ?>


                   ,{name: '<?php echo $t; ?>', data: [<?php echo $v; ?>] }











                    <?php } ?>








        ]
    });
});
    </script>
<!-- Termina el Script que hace funcionar al gráfico -->

<div id="container" style="min-width: 310px; height: 400px; margin: 0 auto"></div>

<?php


    }
?>
