<?php
include '../../header.php';
set_time_limit (0);
ini_set('memory_limit','6000M');
$periodoActual = $_SESSION['periodoActual'];
require_once '../../db/conexion.php';
$Link = new mysqli($Hostname, $Username, $Password, $Database);
if ($Link->connect_errno) {
    echo "Fallo al contenctar a MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
}
$Link->set_charset("utf8");
?>

<div class="row wrapper wrapper-content border-bottom white-bg page-heading">
  <div class="col-lg-8">
    <h2>Agregar Fechas y Lotes a Despacho Individual</h2>
    <ol class="breadcrumb">
      <li>
        <a href="<?php echo $baseUrl; ?>">Home</a>
      </li>
      <li class="active">
        <strong>Agregar Fechas y Lotes a Despacho Individual</strong>
      </li>
    </ol>
  </div><!-- /.col -->
  <div class="col-lg-4">
    <div class="title-action">
      <!--
      <a href="#" class="btn btn-white"><i class="fa fa-pencil"></i> Edit </a>
      <a href="#" class="btn btn-white"><i class="fa fa-check "></i> Save </a>
      -->
      <a href="#" onclick="actualizarFechasLotes()" target="_self" class="btn btn-primary"><i class="fa fa-floppy-o" aria-hidden="true"></i> Guardar Cambios </a>
    </div><!-- /.title-action -->
  </div><!-- /.col -->
</div><!-- /.row -->


<div class="wrapper wrapper-content animated fadeInRight">
  <div class="row">
    <div class="col-lg-12">
      <div class="ibox float-e-margins">
        <div class="ibox-content contentBackground">

<?php
  //include '../../php/funciones.php';
  //var_dump($_POST);
?>


<?php
  //var_dump($_POST);
  $mesAnno = '';
  if( isset($_POST['despachoAnnoI']) && isset($_POST['despachoMesI']) && isset($_POST['despacho']) ){
    // Se va a recuperar el mes y el año para las tablaMesAnno
    $mes = $_POST['despachoMesI'];
    if($mes < 10){
      $mes = '0'.$mes;
    }
    $mes = trim($mes);
    $anno = $_POST['despachoAnnoI'];
    $anno = substr($anno, -2);
    $anno = trim($anno);
    $mesAnno = $mes.$anno;
    $_POST = array_slice($_POST, 2);
    $_POST = array_values($_POST);
  }else{
    // Se va a recuperar el mes y el año para las tablaMesAnno
    $mes = $_POST['mesiConsulta'];
    if($mes < 10){
      $mes = '0'.$mes;
    }
    $mes = trim($mes);
    $anno = $_POST['annoi'];
    $anno = substr($anno, -2);
    $anno = trim($anno);
    $mesAnno = $mes.$anno;
    // Quitando las variable que no sean numeros de despacho
    $_POST = array_slice($_POST, 18);
    $_POST = array_values($_POST);
    // var_dump($_POST);
  }
?>


          <?php

            for ($k=0; $k < count($_POST) ; $k++) {
              // Borrando variables array para usarlas en cada uno de los despachos
              unset($sedes);
              unset($items);
              unset($menus);
              unset($sedesCobertura);
              unset($complementosCantidades);

              $claves = array_keys($_POST);
              $aux = $claves[$k];
              $despacho = $_POST[$aux];

              $consulta = " SELECT de.*, s.nom_sede, s.nom_inst, u.Ciudad FROM despachos_enc$mesAnno de left join sedes$anno s on de.cod_sede = s.cod_sede
              left join ubicacion u on s.cod_mun_sede = u.CodigoDANE and u.ETC = 0
              WHERE de.Num_Doc = $despacho ";

              // echo '<br><br>'.$consulta.'<br><br>';

              $resultado = $Link->query($consulta) or die ('Unable to execute query datos del despacho '. mysqli_error($Link));
              if($resultado->num_rows >= 1){
                $row = $resultado->fetch_assoc();
              }
              $municipio = $row['Ciudad'];
              $institucion = $row['nom_inst'];
              $sede = $row['nom_sede'];
              $codSede = $row['cod_Sede'];
              $semana = $row['Semana'];
              $cobertura = $row['Cobertura'];
              $modalidad = $row['Tipo_Complem'];
              $tipo = $modalidad;
              $sedes[] = $codSede;

              // Iniciando la busqueda de los días que corresponden a esta semana de contrato.
              $dias = '';

              $consulta = " select * from planilla_semanas where SEMANA = '$semana' ";
              $resultado = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));
              $cantDias = $resultado->num_rows;
              if($resultado->num_rows >= 1){
                $aux = 0;
                while($row = $resultado->fetch_assoc()) {
                  if($aux == 0){
                    $mes = $row['MES'];
                  }

                  if($row['MES'] != $mes){
                    $dias = $dias.' ';
                    $dias = $dias.mesEnLetras($mes);
                    $dias = $dias.', ';
                    $mes = $row['MES'];
                  }
                  else{
                    if($aux != 0){
                      $dias = $dias.", ";
                    }
                  }

                  $dias = $dias.$row['DIA'];


                  $aux++;
                }// Termina el while
              }//Termina el if que valida que si existan resultados
              $dias = $dias.' ';
              $dias = $dias.mesEnLetras($mes);
              // Termina la busqueda de los días que corresponden a esta semana de contrato.

              // Bucando la cobertura para la sede en esa semana para el tipo de complementosCantidades
              $cantSedeGrupo1 = 0;
              $cantSedeGrupo2 = 0;
              $cantSedeGrupo3 = 0;

              $consulta = " select Etario1_$modalidad as grupo1, Etario2_$modalidad as grupo2, Etario3_$modalidad as grupo3 from sedes_cobertura where semana = '$semana' and cod_sede  = $codSede ";

              // echo $consulta."</br>";

              $resultado = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));
              if($resultado->num_rows >= 1){
                $row = $resultado->fetch_assoc();
                $cantSedeGrupo1 = $row['grupo1'];
                $cantSedeGrupo2 = $row['grupo2'];
                $cantSedeGrupo3 = $row['grupo3'];
              }

              //echo "<br>".$cantSedeGrupo1;
              //echo "<br>".$cantSedeGrupo2;
              //echo "<br>".$cantSedeGrupo3;

              // A medida que se recoja la información de los aliemntos se
              // determianra si todos los grupos etarios fueron beneficiados
              // y usaremos las cantidades de las siguientes variables.

              $sedeGrupo1 = 0;
              $sedeGrupo2 = 0;
              $sedeGrupo3 = 0;


            // Se van a buscar los alimentos de este despacho.
            $alimentos = array();
            $consulta = " select distinct cod_alimento from despachos_det$mesAnno
            where Tipo_Doc = 'DES'
            and Num_Doc = $despacho
            order by cod_alimento asc ";
            $resultado = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));
            if($resultado->num_rows >= 1){
              while($row = $resultado->fetch_assoc()){
                $alimento = array();
                $alimento['codigo'] = $row['cod_alimento'];
                $alimentos[] = $alimento;
              }
            }

            //var_dump($alimentos);

              // Buscando propiedades y cantidades de cada alimento.
              for ($i=0; $i < count($alimentos) ; $i++) {
                $alimento = $alimentos[$i];
                $auxCodigo = $alimento['codigo'];

                $consulta = " select distinct ftd.codigo, ftd.Componente, p.nombreunidad2 presentacion, p.cantidadund1 cantidadPresentacion, m.grupo_alim, ftd.UnidadMedida, ( select Cantidad from despachos_det$mesAnno where Tipo_Doc = 'DES' and Num_Doc = $despacho and cod_Alimento = $auxCodigo and Id_GrupoEtario = 1 ) as cant_grupo1, ( select Cantidad from despachos_det$mesAnno where Tipo_Doc = 'DES' and Num_Doc = $despacho and cod_Alimento = $auxCodigo and Id_GrupoEtario = 2 ) as cant_grupo2, ( select Cantidad from despachos_det$mesAnno where Tipo_Doc = 'DES' and Num_Doc = $despacho and cod_Alimento = $auxCodigo and Id_GrupoEtario = 3 ) as cant_grupo3 from fichatecnicadet ftd inner join productos$anno p on ftd.codigo=p.codigo inner join menu_aportes_calynut m on ftd.codigo=m.cod_prod where ftd.codigo = $auxCodigo and ftd.tipo = 'Alimento'  ";

              //  echo "<br><br>".$consulta."<br><br>";

                $resultado = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));

                $alimento['componente'] = '';
                $alimento['presentacion'] = '';
                $alimento['grupo_alim'] = '';
                $alimento['cant_grupo1'] = 0;
                $alimento['cant_grupo2'] = 0;
                $alimento['cant_grupo3'] = 0;
                $alimento['cant_total'] = 0;




                if($resultado->num_rows >= 1){
                  while($row = $resultado->fetch_assoc()){
                    $alimento['componente'] = $row['Componente'];
                    $alimento['presentacion'] = $row['presentacion'];
                    $alimento['grupo_alim'] = $row['grupo_alim'];
                    $alimento['cant_grupo1'] = $row['cant_grupo1'];
                    $alimento['cant_grupo2'] = $row['cant_grupo2'];
                    $alimento['cant_grupo3'] = $row['cant_grupo3'];
                    $alimento['cant_total'] = $row['cant_grupo1'] + $row['cant_grupo2'] + $row['cant_grupo3'];

                    if($row['cant_grupo1'] > 0){
                      $sedeGrupo1 = $cantSedeGrupo1;
                    }
                    if($row['cant_grupo2'] > 0){
                      $sedeGrupo2 = $cantSedeGrupo2;
                    }
                    if($row['cant_grupo3'] > 0){
                      $sedeGrupo3 = $cantSedeGrupo3;
                    }
                  }
                }
                $alimentos[$i] = $alimento;
              }

              // Ordenando Array x grupo_alim
              $grupo = array();
              foreach ($alimentos as $clave => $fila) {
                $grupo[$clave] = $fila['grupo_alim'];
              }
              array_multisort($grupo,SORT_DESC,$alimentos);
              // Termna de ordenar Array x grupo
          ?>




























          <form class="" action="" method="post" name="formulario1" id="formulario1">
            <input type="hidden" name="despacho" id="despacho" value="<?php echo $despacho; ?>" />
            <input type="hidden" name="semana" id="semana" value="<?php echo $semana; ?>" />

            <div class="table-responsive">
              <table class="table table-bordered">
                <thead>

        <tr> <th colspan="2" class="alineacionIzquierda"> MUNICIPIO </th>
          <th colspan="7" class="alineacionIzquierda sencilla"><?php echo $municipio; ?></th>
        </tr>
        <tr> <th colspan="2" class="alineacionIzquierda"> INSTITUCION </th>
          <th colspan="7" class="alineacionIzquierda sencilla"><?php echo $institucion; ?></th>
        </tr>
        <tr> <th colspan="2" class="alineacionIzquierda"> SEDE EDUCATIVA </th>
          <th colspan="7" class="alineacionIzquierda sencilla"><?php echo $sede; ?></th>
        </tr>





        <tr> <th colspan="2" class="alineacionIzquierda"> N° BENEFICIARIOS </th> <th colspan="7" class="alineacionIzquierda sencilla"> <?php echo $cobertura; ?> </th> </tr>
        <tr> <th colspan="2" class="alineacionIzquierda"> MODALIDAD </th> <th colspan="7" class="alineacionIzquierda sencilla"> <?php echo $modalidad; ?> </th> </tr>
        <tr> <th colspan="2" class="alineacionIzquierda"> SEMANA N° </th> <th colspan="7" class="alineacionIzquierda sencilla"> <?php echo $semana; ?> </th> </tr>
        <tr> <th colspan="2" class="alineacionIzquierda"> N° DIAS DESPACHO </th> <th colspan="7" class="alineacionIzquierda sencilla"> X <?php echo $cantDias; ?> DIAS - <?php echo strtoupper($dias); ?></th> </tr>

        <tr> <td colspan="9" class="sinBordes"> </td> </tr>





        <tr>
          <th colspan="9" class="text-center">
              PLANILLA DE ENTREGA DE ALIMENTOS - PROGRAMA DE ALIMENTACIÓN ESCOLAR
          </th>
        </tr>
        <tr>
          <th colspan="5"> GRUPO DE EDAD BENEFICIARIOS </th>
          <th class="text-center"> 4 A 6 AÑOS Y 11 MESES </th>
          <th class="text-center"> 7 A 12 AÑOS Y 11 MESES </th>
          <th class="text-center"> 13 A 17 AÑOS Y 11 MESES </th>
          <th class="text-center"> TOTAL COBERTURA </th>

        </tr>
        <tr>
          <th colspan="5"> COBERTURA POR GRUPO DE EDAD </th>





          <th class="text-center"><?php echo $sedeGrupo1; ?></th>
          <th class="text-center"><?php echo $sedeGrupo2; ?></th>
          <th class="text-center"><?php echo $sedeGrupo3;?></th>
          <th class="text-center"><?php echo ($sedeGrupo1 + $sedeGrupo2 + $sedeGrupo3); ?></th>

        </tr>
        <tr>
          <th class="text-center">
            GRUPOS DE ALIMENTOS
          </th>
          <th class="text-center">
            ALIMENTOS
          </th>
          <th class="text-center">
            PRESENTACIÓN
          </th>
          <th class="text-center">
            Cantidad
          </th>
          <th class="text-center">
            Cantidad
          </th>
          <th class="text-center">
            Cantidad
          </th>
          <th class="text-center">
            TOTAL A RECIBIR
          </th>
          <th class="text-center">LOTE</th>
          <th class="text-center">FECHA DE VENCIMIENTO</th>

        </tr>
      </thead>
      <tbody>

<!--  Aqui van los alimentos -->
          <?php
          $grupoAlimActual = '';
          for ($i=0; $i < count($alimentos ) ; $i++) {
            $alimento = $alimentos[$i];
            $codigo = $alimento['codigo'];
            //var_dump($item);
          ?>
          <tr>




          <?php
            // El array $grupo tiene los grupos alimenticios de cada componente
            // Vamos a contar cuantas veces se repiten para controlar el rowspan.
            if($alimento['grupo_alim'] != $grupoAlimActual){
              $grupoAlimActual = $alimento['grupo_alim'];
              $filas = array_count_values($grupo)[$grupoAlimActual];
              ?>
              <td rowspan="<?php echo $filas; ?>" class="text-center mayusculas verticalMiddle"><?php echo $grupoAlimActual; ?></td>
              <?php
            }
          ?>







          <td>
            <?php echo $alimento['componente']; ?>
          </td>
          <td>
            <?php echo $alimento['presentacion']; ?>
          </td>
          <td>
            <?php echo 0+$alimento['cant_grupo1']; ?>
          </td>
          <td>
            <?php echo 0+$alimento['cant_grupo2']; ?>
          </td>
          <td>
            <?php echo 0+$alimento['cant_grupo3']; ?>
          </td>
          <td>
            <?php echo 0+$alimento['cant_total']; ?>
          </td>
          <td>

          <input type="hidden" name="alimento<?php echo $codigo; ?>" id="alimento<?php echo $codigo; ?>" value="<?php echo $codigo; ?>" />

          <input class="form-control inputLote" type="text" name="lote<?php echo $codigo; ?>" id="lote<?php echo $codigo; ?>" value="">





          </td>
          <td>


<div class="input-group date">
                                    <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                    <input name="fechaVencimiento<?php echo $codigo; ?>" id="fechaVencimiento<?php echo $codigo; ?>" type="text" class="form-control inputFechaVencimiento datepick" value="">
                                </div>







          </td>
        </tr>

<?php } ?>



      </tbody>
              </table>
            </div><!-- /.table-responsive -->
    </form>



  <?php
    } // Termina el for cuando recibe variable POS
  ?>
          <div class="listadoFondo">
            <div class="listadoContenedor">
              <div class="listadoCuerpo">
              </div><!-- /.listadoCuerpo -->
            </div><!-- /.listadoContenedor -->
          </div><!-- /.listadoFondo -->
        </div><!-- /.ibox-content -->
      </div><!-- /.ibox float-e-margins -->
    </div><!-- /.col-lg-12 -->
  </div><!-- /.row -->
</div><!-- /.wrapper wrapper-content animated fadeInRight -->

<?php include '../../footer.php'; ?>

<!-- Mainly scripts -->
<script src="<?php echo $baseUrl; ?>/theme/js/jquery-3.1.1.min.js"></script>
<script src="<?php echo $baseUrl; ?>/theme/js/bootstrap.min.js"></script>
<script src="<?php echo $baseUrl; ?>/theme/js/plugins/metisMenu/jquery.metisMenu.js"></script>
<script src="<?php echo $baseUrl; ?>/theme/js/plugins/slimscroll/jquery.slimscroll.min.js"></script>

<script src="<?php echo $baseUrl; ?>/theme/js/plugins/dataTables/datatables.min.js"></script>

<!-- Custom and plugin javascript -->
<script src="<?php echo $baseUrl; ?>/theme/js/inspinia.js"></script>
<script src="<?php echo $baseUrl; ?>/theme/js/plugins/pace/pace.min.js"></script>

<!-- Data picker -->
<script src="<?php echo $baseUrl; ?>/theme/js/plugins/datapicker/bootstrap-datepicker.js"></script>

<!-- Date picker en español -->
<script src="<?php echo $baseUrl; ?>/js/bootstrap-datepicker.es.js"></script>


<script src="<?php echo $baseUrl; ?>/modules/despachos/js/despacho_por_sede_fecha_lote.js"></script>





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






<script type="text/javascript">
$(document).ready(function() {

  $('.datepick').each(function(){
    $(this).removeClass("hasDatepicker");
    $(this).datepicker({
    format: 'dd/mm/yyyy',
    todayHighlight: 'true',
    autoclose: 'true'
  });
  });

});
</script>







<script>
$(document).ready(function(){
  $.fn.datepicker.defaults.language = 'es';

  $('#fechaFin').datepicker({
    format: 'dd/mm/yyyy',
    todayHighlight: 'true',
    autoclose: 'true'
  });

});

/*

$(document).ready(function(){
  $('#fechaInicio').datepicker({
    format: 'dd/mm/yyyy',
    todayHighlight: 'true',
    autoclose: 'true',
    startDate: '<?php echo $fechaInicio; ?>',
    endDate: '<?php echo $fechaFin; ?>'
  });
  $('#fechaFin').datepicker({
    format: 'dd/mm/yyyy',
    todayHighlight: 'true',
    autoclose: 'true',
    startDate: '<?php echo $fechaInicio; ?>',
    endDate: '<?php echo $fechaFin; ?>'
  });
});

*/

</script>





<script>
/*

$('#data_1 .input-group.date').datepicker({
                todayBtn: "linked",
                keyboardNavigation: false,
                forceParse: false,
                calendarWeeks: true,
                autoclose: true
            });

*/



/*

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

  */

</script>

<!-- Page-Level Scripts -->

<?php mysqli_close($Link); ?>

</body>
</html>
