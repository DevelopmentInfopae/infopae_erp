<?php
  error_reporting(E_ALL);
  require_once 'autenticacion.php';
?>
<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title></title>
    <link rel="stylesheet" href="css/estilos.css" media="screen" title="no title" charset="utf-8">
    <link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.6.2/css/font-awesome.min.css">

  <script type="text/javascript" src="js/jquery.js"></script>
  <script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>

  <script>
  $.datepicker.regional['es'] = {
  closeText: 'Cerrar',
  prevText: '<Ant',
  nextText: 'Sig>',
  currentText: 'Hoy',
  monthNames: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
  monthNamesShort: ['Ene','Feb','Mar','Abr', 'May','Jun','Jul','Ago','Sep', 'Oct','Nov','Dic'],
  dayNames: ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'],
  dayNamesShort: ['Dom','Lun','Mar','Mié','Juv','Vie','Sáb'],
  dayNamesMin: ['Do','Lu','Ma','Mi','Ju','Vi','Sá'],
  weekHeader: 'Sm',
  dateFormat: 'dd/mm/yy',
  firstDay: 1,
  isRTL: false,
  showMonthAfterYear: false,
  yearSuffix: ''
  };
  $.datepicker.setDefaults($.datepicker.regional['es']);
  </script>




  <link rel="stylesheet" href="js/timepicker/jquery-ui-timepicker-addon.css" media="screen" title="no title" charset="utf-8">
  <script src="js/timepicker/jquery-ui-timepicker-addon.js" type="text/javascript"> </script>
  <script type="text/javascript">





    $(function (){




      $.timepicker.regional['es'] = {
      	                timeOnlyTitle: 'Elegir una hora',
      	                timeText: 'Hora',
      	                hourText: 'Horas',
      	                minuteText: 'Minutos',
      	                secondText: 'Segundos',
      	                millisecText: 'Milisegundos',
                      timezoneText: 'Huso horario',
      	                currentText: 'Ahora',
      	                closeText: 'Cerrar',
      	                timeFormat: 'HH:mm',
      	                amNames: ['a.m.', 'AM', 'A'],
      	                pmNames: ['p.m.', 'PM', 'P'],
      	                isRTL: false
      	        };
      $.timepicker.setDefaults($.timepicker.regional['es']);













      $("#fecha").datetimepicker({
        changeMonth: true,
        changeYear: true,
        yearRange:'-70:+0',
        controlType: 'select',
  	    oneLine: true,
  	    timeFormat: 'hh:mm tt',

      });





    });



  </script>






<script type="text/javascript">

$(document).ready(function() {

  $('.datepick').each(function(){
    $(this).removeClass("hasDatepicker");
    $(this).datepicker({
      changeMonth: true,
      changeYear: true,
      yearRange:'-70:+0'
    });
  });

});




</script>


















  <script type="text/javascript">
    //Función que vijila el no salirse de la pagina sin guardar
     $(window).bind('beforeunload', function(){
     return 'Está a punto de salir sin guardar el movimiento actual, todos los campos diligenciados se perderán';
     });
     $('#contactform').submit(function(){
     $(window).unbind('beforeunload');
     return false;
     });
   </script>






<script type="text/javascript" src="js/despacho_consolidado_fecha_lote.js"> </script>





  </head>







  <body>


<?php

//var_dump($_SESSION);


  $totalesSedeCobertura = $_SESSION['totalesSedeCobertura'];
  $complementosCantidades = $_SESSION['complementosCantidades'];

  // Se buscaran los totales de los diferentes grupos etarios y el total de niños
  // beneficiados con este despacho

  $totalGrupo1 = 0;
  $totalGrupo2 = 0;
  $totalGrupo3 = 0;
  $totalTotal = 0;

  for ($i=0; $i < count($complementosCantidades) ; $i++) {
    $complemento = $complementosCantidades[$i];
    if($totalGrupo1 < $complemento['grupo1']){
      $totalGrupo1 = $totalesSedeCobertura['grupo1'];
      $totalTotal = $totalGrupo1 + $totalGrupo2 + $totalGrupo3;
    }
    if($totalGrupo2 < $complemento['grupo2']){
      $totalGrupo2 = $totalesSedeCobertura['grupo2'];
      $totalTotal = $totalGrupo1 + $totalGrupo2 + $totalGrupo3;
    }
    if($totalGrupo3 < $complemento['grupo3']){
      $totalGrupo3 = $totalesSedeCobertura['grupo3'];
      $totalTotal = $totalGrupo1 + $totalGrupo2 + $totalGrupo3;
    }
    if($totalGrupo1 != 0 && $totalGrupo2 != 0 && $totalGrupo3 != 0){
      break;
    }
  }

  require_once 'db/conexion.php';

  $Link = new mysqli($Hostname, $Username, $Password, $Database);
  if ($Link->connect_errno) {
    echo "Fallo al contenctar a MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
  }
  $Link->set_charset("utf8");









/*
  // Se van a buscar los atributos grupo alimenticio y presentación para cada uno de los componentes
  for ($i=0; $i < count($complementosCantidades) ; $i++) {
    $complemento = $complementosCantidades[$i];
    $complemento['grupo_alim'] = '';
    $complemento['presentacion'] = '';
    $codigo = $complemento['codigo'];
    $consulta = " select  ft.Codigo AS codigo_preparado, ftd.codigo, ftd.Componente,p.nombreunidad1 presentacion, m.grupo_alim, ftd.Cantidad, ftd.UnidadMedida from fichatecnica ft inner join fichatecnicadet ftd on ft.id=ftd.idft inner join productos16 p on ftd.codigo=p.codigo inner join menu_aportes_calynut m on ftd.codigo=m.cod_prod where ft.codigo = $codigo and ftd.tipo = 'Alimento' ";

    //echo "<br>".$consulta."<br>";

    $resultado = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));
    if($resultado->num_rows >= 1){
      $row = $resultado->fetch_assoc();
      $grupo_alim = $row['grupo_alim'];
      $presentacion = $row['presentacion'];
      $complemento['grupo_alim'] = $grupo_alim;
      $complemento['presentacion'] = $presentacion;
    }
    $complementosCantidades[$i]=$complemento;
  }

  //var_dump($complementosCantidades);

*/
// Ordenando Array x grupo_alim
foreach ($complementosCantidades as $clave => $fila) {
  $grupo[$clave] = $fila['grupo_alim'];
}
array_multisort($grupo,SORT_DESC,$complementosCantidades);
// Termna de ordenar Array x grupo



//var_dump($complementosCantidades);

















 ?>



















<?php
  $sedes = $_SESSION['sedes'];
  $semana = $_SESSION['semana'];
  $dias = $_SESSION['dias'];
?>

<form class="" action="" method="post" name="formulario1" id="formulario1">
    <table class="despachoConsolidado">
      <thead>
        <tr>
          <th colspan="7" class="sinBordes">
              <img src="imagenes/santander-nos-une.jpg" alt="Santander nos une" />
          </th>
          <th colspan="2" class="sinBordes" align="right">
              <!-- Provincia MARES -->
          </th>
        </tr>
        <tr> <th colspan="2" class="alineacionIzquierda"> MUNICIPIO </th> <th colspan="7" class="alineacionIzquierda sencilla">

        <?php

        $consulta=" select distinct u.Ciudad from sedes16  s 
        left join ubicacion u on u.CodigoDANE = s.cod_mun_sede and u.ETC = 0 
        where 1=1 and ( ";
        for ($i=0; $i < count($sedes) ; $i++) {
          $sede = $sedes[$i];
          if($i == 0){
              $consulta=$consulta." s.cod_sede = $sede ";
          } else{
            $consulta=$consulta." or s.cod_sede = $sede ";
          }
        }
        $consulta=$consulta." ) ";
        //echo "<br>Consulta de los municipios <br>";
        //echo $consulta;
        //echo "<br>";

        $resultado = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));
        if($resultado->num_rows >= 1){
          $aux = 0;
          while($row = $resultado->fetch_assoc()) {
            echo $row['Ciudad'].' ';
          }// Termina el while
        }//Termina el if que valida que si existan resultados
        $resultado->close();

        ?>


        </th> </tr>
        <tr>
          <th colspan="2" class="alineacionIzquierda"> N° BENEFICIARIOS </th>
          <th colspan="7" class="alineacionIzquierda sencilla"> <?php echo $totalesSedeCobertura['total']; ?> </th>


        </tr>
        <tr> <th colspan="2" class="alineacionIzquierda"> MODALIDAD </th> <th colspan="7" class="alineacionIzquierda sencilla"> <?php echo $_SESSION['tipo']; ?></th> </tr>
        <tr> <th colspan="2" class="alineacionIzquierda"> SEMANA N° </th> <th colspan="7" class="alineacionIzquierda sencilla"><?php echo $semana; ?></th> </tr>
        <tr> <th colspan="2" class="alineacionIzquierda"> N° DIAS DESPACHO </th> <th colspan="7" class="alineacionIzquierda sencilla"><?php echo $dias; ?></th> </tr>
        <tr> <th colspan="9" class="sinBordes"> </th> </tr>





        <tr>
          <th colspan="9">
              PLANILLA DE ENTREGA DE ALIMENTOS - PROGRAMA DE ALIMENTACIÓN ESCOLAR
          </th>
        </tr>
        <tr>
          <th colspan="3"> GRUPO DE EDAD BENEFICIARIOS </th>
          <th> 4 A 6 AÑOS Y 11 MESES </th>
          <th> 7 A 12 AÑOS Y 11 MESES </th>
          <th> 13 A 17 AÑOS Y 11 MESES </th>
          <th> TOTAL COBERTURA </th>
          <th> LOTE </th>
          <th> FECHA DE VENCIMIENTO </th>
        </tr>
        <tr>
          <th colspan="3"> COBERTURA POR GRUPO DE EDAD </th>
          <th class="sencilla"> <?php echo $totalGrupo1; ?> </th>
          <th class="sencilla"> <?php echo $totalGrupo2; ?> </th>
          <th class="sencilla"> <?php echo $totalGrupo3; ?> </th>
          <th class="sencilla"> <?php echo $totalTotal; ?> </th>
          <th class="sencilla"></th>
          <th class="sencilla"></th>
        </tr>
        <tr>
          <th>
            GRUPOS DE ALIMENTOS
          </th>
          <th>
            ALIMENTOS
          </th>
          <th>
            PRESENTACIÓN
          </th>
          <th>
            Cantidad
          </th>
          <th>
            Cantidad
          </th>
          <th>
            Cantidad
          </th>
          <th>
            TOTAL A RECIBIR
          </th>
          <th>

          </th>
          <th>

          </th>
        </tr>
      </thead>
      <tbody>

        <?php
          $grupoAlimActual = '';
          for ($i=0; $i < count($complementosCantidades ) ; $i++) {
            $item = $complementosCantidades[$i];
            //var_dump($item);





          ?>
          <tr>




          <?php
            // El array $grupo tiene los grupos alimenticios de cada componente
            // Vamos a contar cuantas veces se repiten para controlar el rowspan.
            if($item['grupo_alim'] != $grupoAlimActual){
              $grupoAlimActual = $item['grupo_alim'];
              $filas = array_count_values($grupo)[$grupoAlimActual];
              ?>
              <td rowspan="<?php echo $filas; ?>" class="mayusculas"><?php echo $grupoAlimActual; ?> </td>
              <?php
            }
          ?>
















            <td> <?php echo $item['Componente']; ?> </td>
            <td> <?php echo $item['presentacion']; ?> </td>














            <td> <?php echo round($item['grupo1'] * $totalGrupo1); ?> </td>
            <td> <?php echo round($item['grupo2'] * $totalGrupo2); ?> </td>
            <td> <?php echo round($item['grupo3'] * $totalGrupo3); ?> </td>



            <td> <?php echo round($item['grupo1'] * $totalGrupo1 + $item['grupo2'] * $totalGrupo2 + $item['grupo3'] * $totalGrupo3); ?> </td>






            <td><input class="inputLote" type="text" name="lote<?php echo $item['codigo']; ?>" id="lote<?php echo $item['codigo']; ?>" value=""></td>
            <td><input class="inputFechaVencimiento datepick" type="text" name="fechaVencimiento<?php echo $item['codigo']; ?>" id="fechaVencimiento<?php echo $item['codigo']; ?>" value=""></td>
          </tr>
        <?php   } ?>



      </tbody>
    </table>
    <div class="btnGuardarFechasLotes">

      <button type="button" name="button" onclick="actualizarFechasLotes()">Guardar</button>
    </div>
</form>

    <?php     $Link->close(); ?>



<div class="" id="debug"> </div>
<div class="" id="loader">
  <div class="" id="loaderFondo">
    <div class="" id="loaderContenedor">
      <i class="fa fa-cog fa-spin fa-3x fa-fw"></i>
      <span class="sr-only">Loading...</span>
    </div>
  </div>
</div>

  </body>
</html>
