<?php
$titulo = 'Trazabilidad de insumos';
$meses = array('01' => "Enero", "02" => "Febrero", "03" => "Marzo", "04" => "Abril", "05" => "Mayo", "06" => "Junio", "07" => "Julio", "08" => "Agosto", "09" => "Septiembre", "10" => "Octubre", "11" => "Noviembre", "12" => "Diciembre");
require_once '../../header.php';

if ($permisos['informes'] == "0") {
  ?><script type="text/javascript">
    window.open('<?= $baseUrl ?>', '_self');
  </script>
<?php exit(); }
	  else {
      ?><script type="text/javascript">
        const list = document.querySelector(".li_informes");
        list.className += " active ";
      </script>
      <?php
      }

$periodoActual = $_SESSION['periodoActual'];

$nameLabel = get_titles('informes', 'trazabilidadInsumos', $labels);
$titulo = $nameLabel;
?>

<style type="text/css">

</style>
<div class="row wrapper wrapper-content border-bottom white-bg page-heading">
  <div class="col-lg-8">
    <h2><?php echo $titulo; ?></h2>
    <ol class="breadcrumb">
      <li>
        <a href="<?php echo $baseUrl; ?>">Inicio</a>
      </li>
      <li class="active">
        <strong><?php echo $titulo; ?></strong>
      </li>
    </ol>
  </div><!-- /.col -->
  <div class="col-lg-4">

  </div><!-- /.col -->
</div><!-- /.row -->

<div class="wrapper wrapper-content animated fadeInRight">
  <div class="row">
    <div class="col-lg-12">
      <div class="ibox float-e-margins">
        <div class="ibox-content contentBackground">
          <?php
          $opciones ="";
          $consultaTablas = "SELECT
                                   table_name AS tabla
                                  FROM
                                   information_schema.tables
                                  WHERE
                                   table_schema = DATABASE() AND table_name like 'insumosmovdet%'";
          $resultadoTablas = $Link->query($consultaTablas);
          if ($resultadoTablas->num_rows > 0) {
            $cnt=0;
            while ($tabla = $resultadoTablas->fetch_assoc()) {
              $mes = str_replace("insumosmovdet", "", $tabla['tabla']);
              $mes = str_replace($_SESSION['periodoActual'], "", $mes);

              $nomMes = $meses[$mes];
              $opciones.= '<option value="'.$mes.'">'.$nomMes.'</option>';

              if ($cnt == 0) {
                  $cnt++;
                  $mesTablaInicio = $mes;
              }
             }
          }
           ?>
          <form class="form row" id="formBuscar" method="POST">
            <div id="fechaDiasDespachos" style="display: none;">
              <div class="form-group col-sm-2">
                <label>Desde</label>
                <div class="row compositeDate">
                  <div class="col-sm-8 nopadding">
                    <select name="mes_inicio" id="mes_inicio" class="form-control ">
                    <?php echo $opciones; ?>
                    </select>
                  </div>
                  <div class="col-sm-4 nopadding">
                    <select name="dia_inicio" id="dia_inicio" class="form-control">
                      <option value="">dd</option>
                    </select>
                  </div>
                </div>
              </div>
              <div class="form-group col-sm-2">
                <label>Hasta</label>
                <div class="row compositeDate">
                  <div class="col-sm-8 nopadding">
                    <input type="text" id="nomMesFin" value="Espere..." class="form-control" readonly>
                    <input type="hidden" name="mes_fin" id="mes_fin" value="01">
                  </div>
                  <div class="col-sm-4 nopadding">
                    <select name="dia_fin" id="dia_fin" class="form-control">
                      <option value="">dd</option>
                    </select>
                  </div>
                </div>
              </div>
            </div>
            <div id="fechaElaboracion">
              <div class="form-group col-sm-2">
                <label>Desde</label>
                <input type="text" name="fecha_inicio_elaboracion" id="fecha_inicio_elaboracion" value="<?php echo date('Y-m')."-01" ?>"data-date-format="yyyy-mm-dd"  class="form-control datepicker">
              </div>
              <div class="form-group col-sm-2">
                <label>Hasta</label>
                <?php $mesSiguiente = date('m')+1; ?>
                <input type="text" name="fecha_fin_elaboracion" id="fecha_fin_elaboracion" data-date-format="yyyy-mm-dd" value="<?php echo date('Y')."-".$mesSiguiente."-01"; ?>" class="form-control datepicker">
              </div>
            </div>
            <div class="form-group col-sm-2">
              <label>Fecha de </label>
              <div class="row compositeDate">
                <select name="fecha_de" id="fecha_de" class="form-control ">
                  <option value="1">Elaboración documento</option>
                  <option value="2">Mes despachado</option>
                </select>
              </div>
            </div>
            <div class="form-group col-sm-3">
              <label>Municipio</label>
              <select class="form-control" name="municipio" id="municipio">
                <option>Cargando...</option>
              </select>
            </div>
            <div class="form-group col-sm-3">
              <label>Tipo documento</label>
              <select name="tipo_documento" id="tipo_documento" class="form-control">
                <option value="">Seleccione...</option>
              <?php
              $consultarTipoDocumento = "SELECT * FROM tipomovimiento";
              $resultadoTipoDocumento = $Link->query($consultarTipoDocumento);
              if ($resultadoTipoDocumento->num_rows > 0) {
                while ($tdoc = $resultadoTipoDocumento->fetch_assoc()) { ?>
                  <option value="<?php echo $tdoc['Movimiento'] ?>"><?php echo $tdoc['Movimiento'] ?></option>
                <?php }
              }
               ?>
              </select>
            </div>
            <div class="form-group col-sm-3">
              <label>Proveedor/Responsable</label>
              <select name="proveedor" id="proveedor" class="form-control">
                <option value="">Seleccione tipo documento</option>
              </select>
            </div>
            <div class="form-group col-sm-2">
              <label>Filtrar por  </label>
              <select name="tipo_filtro" id="tipo_filtro" class="form-control">
                <option value="">Seleccione...</option>
                <option value="1">Bodega</option>
                <option value="2">Conductor</option>
                <option value="3">Producto</option>
              </select>
            </div>
            <div id="divBodegas" style="display: none;">
              <div class="form-group col-sm-2">
                <label>Tipo bodega  </label>
                <select name="tipo_bodega" id="tipo_bodega" class="form-control">
                  <option value="">Todas</option>
                  <option value="1">Bodega origen</option>
                  <option value="2">Bodega destino</option>
                </select>
              </div>
              <div class="form-group col-sm-2">
                <label>Bodegas  </label>
                <select name="bodegas" id="bodegas" class="form-control">
                </select>
              </div>
            </div>
            <div class="form-group col-sm-3" id="divConductores" style="display: none;">
              <label>Conductor</label>
              <select name="conductor" id="conductor" class="form-control">

              </select>
            </div>
            <div id="divProductos" style="display: none;">
              <div class="form-group col-sm-3">
                <label>Producto</label>
                <select name="producto" id="producto" class="form-control">
                  <option value="">Cargando...</option>
                </select>
              </div>
              <div class="form-group col-sm-3">
                <label>Ver por  </label>
                <div class="radio">
                <label><input type="checkbox" name="totales" id="totales" value="1" <?php if(isset($_POST['totales']) && $_POST['totales'] = "1") { ?> checked <?php } ?> required> Totales</label>
                </div>
              </div>
            </div>
            <input type="hidden" name="buscar" value="1">
          </form>
          <div class="col-sm-12">
            <button class="btn btn-primary" onclick="$('#formBuscar').submit();" id="btnBuscar"> <span class="fa fa-search"></span>  Buscar</button>
            <?php if (isset($_POST['buscar'])): ?>
              <button class="btn btn-primary" onclick="location.href='index.php';" id="btnBuscar"> <span class="fa fa-times"></span>  Limpiar búsqueda</button>
            <?php endif ?>
          </div>
        </div><!-- /.ibox-content -->
      </div><!-- /.ibox float-e-margins -->
    </div><!-- /.col-lg-12 -->
  </div><!-- /.row -->



  <div class="row">
    <div class="col-lg-12">
      <div class="ibox float-e-margins">
        <div class="ibox-content contentBackground">
          <div class="table-responsive">
             <table class="table" id="tablaTrazabilidad">
                <thead>
                  <tr>
                    <th style="width: 7.7%;">Tipo Doc</th>
                    <th style="width: 7.7%; word-wrap: break-word;">Número</th>
                    <th style="width: 7.7%;">Fecha / Hora</th>
                    <th style="width: 7.7%;">Responsable / Proveedor</th>
                    <th style="width: 7.7%;">Nombre Producto / Alimento</th>
                    <th style="width: 7.7%;">Unidad Medida</th>
                    <th style="width: 7.7%;">Factor</th>
                    <th style="width: 7.7%;">Cantidad</th>
                    <th style="width: 7.7%;">Nombre Bodega Origen</th>
                    <th style="width: 7.7%;">Nombre Bodega Destino</th>
                    <th style="width: 7.7%;">Tipo Transp</th>
                    <th style="width: 7.7%;">Placa</th>
                    <th style="width: 7.6%;">Conductor</th>
                  </tr>
                </thead>
                <tbody id="tBodyTrazabilidad">

                </tbody>
                <tfoot>
                  <tr>
                    <th>Tipo Doc</th>
                    <th>Número</th>
                    <th>Fecha / Hora</th>
                    <th>Responsable / Proveedor</th>
                    <th>Nombre Producto / Alimento</th>
                    <th>Unidad Medida</th>
                    <th>Factor</th>
                    <th>Cantidad</th>
                    <th>Nombre Bodega Origen</th>
                    <th>Nombre Bodega Destino</th>
                    <th>Tipo Transp</th>
                    <th>Placa</th>
                    <th>Conductor</th>
                  </tr>
                </tfoot>
              </table>
          </div>
<?php

  if (!isset($_POST['buscar'])) { //Si no hay filtrado

    $numtabla = $mesTablaInicio.$_SESSION['periodoActual'];

    $consulta = "SELECT
        pmov.Tipo, pmov.Numero, pmov.FechaMYSQL, pmov.Nombre as Proveedor, pmovdet.Descripcion, pmovdet.Umedida, FORMAT(pmovdet.Factor, 4) as Factor, FORMAT(pmovdet.Cantidad, 4) as Cantidad, bodegas.NOMBRE as nomBodegaOrigen, b2.NOMBRE as nomBodegaDestino, tipovehiculo.Nombre as TipoTransporte, pmov.Placa, pmov.ResponsableRecibe
        FROM insumosmov$numtabla AS pmov
          INNER JOIN insumosmovdet$numtabla AS pmovdet ON pmov.Numero = pmovdet.Numero
          INNER JOIN bodegas ON bodegas.ID = pmovdet.BodegaOrigen
          INNER JOIN bodegas as b2 ON b2.ID = pmovdet.BodegaDestino
          INNER JOIN tipovehiculo ON tipovehiculo.Id = pmov.TipoTransporte
        LIMIT 200;";
  } else if (isset($_POST['buscar'])) { //Si hay filtrado

    $numtabla = $_POST['mes_inicio'].$_SESSION['periodoActual']; //Número MesAño según mes escogido
    $condiciones = ""; //Donde se almacenan las condiciones según parámetros
    $inners="";//Donde se almacenan los INNERS necesarios para traer datos externos.
    $datos=" pmov.Tipo, pmov.Numero, pmov.FechaMYSQL, pmov.Nombre as Proveedor, pmovdet.Descripcion, pmovdet.Umedida, FORMAT(pmovdet.Factor, 4) as Factor, FORMAT(pmovdet.Cantidad, 4) as Cantidad, bodegas.NOMBRE as nomBodegaOrigen, b2.NOMBRE as nomBodegaDestino, tipovehiculo.Nombre as TipoTransporte, pmov.Placa, pmov.ResponsableRecibe  ";

    if (isset($_POST['fecha_de']) && $_POST['fecha_de'] != "") { //Si está seteado el tipo de búsqueda por fecha
      $fecha_de = $_POST['fecha_de'];
      if ($fecha_de == 1) { //Si el tipo de búsqueda es por elaboración de documento
        $condiciones.=" AND pmov.FechaMYSQL > '".$_POST['fecha_inicio_elaboracion']." 00:00:00' AND pmov.FechaMYSQL < '".$_POST['fecha_fin_elaboracion']." 00:00:00' ";
      }
    }


    if (isset($_POST['tipo_documento']) && $_POST['tipo_documento'] != "") { //Si el tipo de documento se especificó
      if ($_POST['proveedor'] != "") { //Si el proveedor se especificó, busca según las bodegas relacionadas
        $condiciones.=" AND pmov.Tipo = '".$_POST['tipo_documento']."' AND pmov.Nitcc = '".$_POST['proveedor']."' ";
      } else { //Si no especificó, trae todos los registros con el tipo de documento escogido
        $condiciones.=" AND pmov.Tipo = '".$_POST['tipo_documento']."' ";
      }
    }

    if (isset($_POST['municipio']) && $_POST['municipio'] != "") { //Si el usuario especifica municipio, busca las sedes relacionadas que sean del municipio escogido
      if ($_POST['fecha_de'] != "2") {

      }
      $inners.=" INNER JOIN sedes".$_SESSION['periodoActual']." as sede ON sede.cod_sede = pmov.BodegaDestino ";
      $condiciones.=" AND sede.cod_mun_sede = '".$_POST['municipio']."' ";
    }

    if (isset($_POST['tipo_filtro']) && $_POST['tipo_filtro'] != "") {
    $filtro = $_POST['tipo_filtro'];

      if ($filtro == 1) { //Valor escogido en tipo de filtro

          if (isset($_POST['bodegas']) && $_POST['bodegas'] != "") { //Si el filtro de búsqueda está por bodegas

            if ($_POST['tipo_bodega'] == 1) { //Si eligió buscar por la bodega de Origen
              $condiciones.=" AND pmovdet.BodegaOrigen = '".$_POST['bodegas']."' ";
            } else if ($_POST['tipo_bodega'] == 2) { //Si eligió buscar por la bodega de Destino
              $condiciones.=" AND pmovdet.BodegaDestino = '".$_POST['bodegas']."' ";
            } else if ($_POST['tipo_bodega'] == "") { //Si eligió buscar por las dos bodegas (Origen y Destino)
              $condiciones.=" AND (pmovdet.BodegaOrigen = '".$_POST['bodegas']."' OR pmovdet.BodegaDestino = '".$_POST['bodegas']."')";
            }
          }

      } else if ($filtro == 2) {

          if (isset($_POST['conductor']) && $_POST['conductor'] != "") { //Si el filtro de búsqueda está por conductor
            $condiciones.=" AND pmov.ResponsableRecibe = '".$_POST['conductor']."' ";
          }

      } else if ($filtro == 3) {

          if (isset($_POST['producto']) && $_POST['producto'] != "") { //Si especificó filtro por producto
              if (isset($_POST['totales'])) { //Si especificó ver por totales, suma las cantidades despachadas

                if ($condiciones == "") { //Si no hay otros criterios especificados, muestra sólo valores Nombre de producto, Factor, Unidad medida y Cantidad
                  $txtTotales = "--";
                  $datos =" '".$txtTotales."' as Tipo, '".$txtTotales."' as Numero, '".$txtTotales."' as FechaMYSQL, '".$txtTotales."' as Proveedor, pmovdet.Descripcion, pmovdet.Umedida, FORMAT(pmovdet.Factor, 4) as Factor, FORMAT(SUM(pmovdet.Cantidad), 4) as Cantidad,  '".$txtTotales."' as nomBodegaOrigen, '".$txtTotales."' as nomBodegaDestino,  '".$txtTotales."' as TipoTransporte, '".$txtTotales."' as Placa, '".$txtTotales."' as ResponsableRecibe ";
                } else { //Si hay criterios, muestra los resultados agrupados
                  $datos=" pmov.Tipo, pmov.Numero, pmov.FechaMYSQL, pmov.Nombre as Proveedor, pmovdet.Descripcion, pmovdet.Umedida, FORMAT(pmovdet.Factor, 4) as Factor, FORMAT(SUM(pmovdet.Cantidad), 4) as Cantidad, bodegas.NOMBRE as nomBodegaOrigen, b2.NOMBRE as nomBodegaDestino, tipovehiculo.Nombre as TipoTransporte, pmov.Placa, pmov.ResponsableRecibe  ";
                }

                $condiciones.=" AND pmovdet.CodigoProducto = '".$_POST['producto']."' GROUP BY pmovdet.CodigoProducto ";

              } else { // Si no se especificó ver por totales, muestra cada uno de los despachos del producto
                $condiciones.=" AND pmovdet.CodigoProducto = '".$_POST['producto']."' ";
              }
          }

      } else if ($filtro == 4) {


      } else if ($filtro == 5) {

      }
    }

    $consulta = "SELECT
                      $datos
                  FROM
                    insumosmov$numtabla AS pmov
                      INNER JOIN insumosmovdet$numtabla AS pmovdet ON pmov.Numero = pmovdet.Numero
                      INNER JOIN bodegas ON bodegas.ID = pmovdet.BodegaOrigen
                      INNER JOIN bodegas as b2 ON b2.ID = pmovdet.BodegaDestino
                      INNER JOIN tipovehiculo ON tipovehiculo.Id = pmov.TipoTransporte
                      $inners $condiciones
                  LIMIT 2000;";
}

?>
              <input type="hidden" name="consulta" id="consulta" value="<?php echo $consulta; ?>">
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
<script src="<?php echo $baseUrl; ?>/theme/js/plugins/toastr/toastr.min.js"></script>
<script src="<?php echo $baseUrl; ?>/theme/js/plugins/validate/jquery.validate.min.js"></script>
<script src="<?php echo $baseUrl; ?>/theme/js/plugins/iCheck/icheck.min.js"></script>
<script src="<?php echo $baseUrl; ?>/theme/js/plugins/steps/jquery.steps.min.js"></script>
<script src="<?php echo $baseUrl; ?>/theme/js/plugins/datapicker/bootstrap-datepicker.js"></script>

<!-- Section Scripts -->
<script src="<?php echo $baseUrl; ?>/modules/trazabilidad_insumos/js/trazabilidad.js"></script>

  <script type="text/javascript">
  dataset1 = $('#tablaTrazabilidad').DataTable({
    ajax: {
        method: 'POST',
        url: 'functions/fn_trazabilidad_obtener_datos_tabla.php',
        data:{
          consulta: $('#consulta').val()
        }
      },
    columns:[
        { data: 'Tipo'},
        { data: 'Numero'},
        { data: 'FechaMYSQL'},
        { data: 'Proveedor'},
        { data: 'Descripcion'},
        { data: 'Umedida'},
        { data: 'Factor'},
        { data: 'Cantidad'},
        { data: 'nomBodegaOrigen'},
        { data: 'nomBodegaDestino'},
        { data: 'TipoTransporte'},
        { data: 'Placa'},
        { data: 'ResponsableRecibe'},
      ],
          /*order: [ 0, 'asc' ],*/
    pageLength: 25,
    responsive: true,
    dom : '<"html5buttons" B>lr<"containerBtn"><"inputFiltro"f>tip',
    buttons : [{extend:'excel', title:'Trazabilidad_insumos', className:'btnExportarExcel', exportOptions: {columns : [0,1,2,3,4,5,6]}}],
    oLanguage: {
      sLengthMenu: 'Mostrando _MENU_ registros por página',
      sZeroRecords: 'No se encontraron registros',
      sInfo: 'Mostrando _START_ a _END_ de _TOTAL_ registros',
      sInfoEmpty: 'Mostrando 0 a 0 de 0 registros',
      sInfoFiltered: '(Filtrado desde _MAX_ registros)',
      sSearch:         'Buscar: ',
      oPaginate:{
        sFirst:    'Primero',
        sLast:     'Último',
        sNext:     'Siguiente',
        sPrevious: 'Anterior'
      }
    },
    initComplete: function() {
      var btnAcciones = '<div class="dropdown pull-right" id=""><button class="btn btn-primary btn-sm btn-outline" type="button" id="accionesTabla" data-toggle="dropdown" aria-haspopup="true">Acciones<span class="caret"></span></button><ul class="dropdown-menu pull-right" aria-labelledby="accionesTabla"><li><a onclick="$(\'.btnExportarExcel\').click()"><span class="fa fa-file-excel-o"></span> Exportar </a></li></ul></div>';
      $('.containerBtn').html(btnAcciones);
      $('#loader').fadeOut();
    },
    preDrawCallback: function( settings ) {
        $('#loader').fadeIn();
      }
    }).on("draw", function(){ $('#loader').fadeOut();});;
  // var btnAcciones = '<div class="dropdown pull-right" id=""><button class="btn btn-primary btn-sm btn-outline" type="button" id="accionesTabla" data-toggle="dropdown" aria-haspopup="true">Acciones<span class="caret"></span></button><ul class="dropdown-menu pull-right" aria-labelledby="accionesTabla"><li><a onclick="$(\'.btnExportarExcel\').click()"><span class="fa fa-file-excel-o"></span> Exportar </a></li></ul></div>';

  // $('.containerBtn').html(btnAcciones);

  <?php if (isset($_POST['buscar'])): ?>

    // Código para setear los campos del formulario de búsqueda con los parámetros especificados.

    $('#btnBuscar').prop('disabled', true);
    $('#formBuscar').find('input, textarea, button, select').prop('disabled',true);
    $('#fecha_de').val(<?php echo $_POST['fecha_de']; ?>).change();

    $('#mes_inicio').val('<?php echo $_POST['mes_inicio']; ?>').change();
    $('#mes_fin').val('<?php echo $_POST['mes_fin']; ?>').change();

    <?php if ($_POST['tipo_documento'] != ""): ?>
      $('#tipo_documento').val('<?php echo $_POST['tipo_documento']; ?>').change();
    <?php endif ?>
    <?php if ($_POST['tipo_filtro'] != ""): ?>
      $('#tipo_filtro').val('<?php echo $_POST['tipo_filtro']; ?>').change();
    <?php endif ?>
    <?php if ($_POST['dia_inicio'] != ""): ?>
      $('#dia_inicio').val('<?php echo $_POST['dia_inicio']; ?>').change();
    <?php endif ?>
    <?php if ($_POST['dia_fin'] != ""): ?>
      $('#dia_fin').val('<?php echo $_POST['dia_fin']; ?>').change();
    <?php endif ?>
    setTimeout(function() {
      <?php if ($_POST['municipio'] != ""): ?>
        $('#municipio').val('<?php echo $_POST['municipio']; ?>').change();
      <?php endif ?>
      <?php if ($_POST['proveedor'] != ""): ?>
        $('#proveedor').val('<?php echo $_POST['proveedor']; ?>').change();;
      <?php endif ?>

      <?php if (isset($_POST['conductor']) && $_POST['conductor'] != ""): ?>
        $('#conductor').val('<?php echo $_POST['conductor']; ?>').change();
      <?php endif ?>

      <?php if ($_POST['tipo_bodega'] != ""): ?>
        $('#tipo_bodega').val('<?php echo $_POST['tipo_bodega']; ?>').change();
      <?php endif ?>
      <?php if (isset($_POST['bodegas']) && $_POST['bodegas'] != ""): ?>
        $('#bodegas').val('<?php echo $_POST['bodegas']; ?>').change();
      <?php endif ?>

      <?php if ($_POST['producto'] != ""): ?>
        $('#producto').val('<?php echo $_POST['producto']; ?>').change();
      <?php endif ?>

      $('#btnBuscar').prop('disabled', false);
      $('#formBuscar').find('input, textarea, button, select').prop('disabled',false);
    }, 3500);

  <?php endif ?>

</script>

<?php mysqli_close($Link); ?>

</body>
</html>