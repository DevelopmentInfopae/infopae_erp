<?php
  $titulo = 'Informe ordenes de compra';
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
          const list2 = document.querySelector(".li_informeOrdenCompra");
          list2.className += " active ";
        </script>
        <?php
        }

  $periodo_actual = $_SESSION['periodoActual'];

  $nameLabel = get_titles('informes', 'informeOrdenCompra', $labels);
  $titulo = $nameLabel;
?>

<div class="row wrapper wrapper-content border-bottom white-bg page-heading">
  <div class="col-lg-8">
    <h2><?= $titulo; ?></h2>
    <ol class="breadcrumb">
      <li>
        <a href="<?= $baseUrl; ?>">Inicio</a>
      </li>
      <li class="active">
        <strong><?= $titulo; ?></strong>
      </li>
    </ol>
  </div>
</div>

<div class="wrapper wrapper-content animated fadeInRight">
  <div class="row">
    <div class="col-lg-12">
      <div class="ibox float-e-margins">
        <div class="ibox-content contentBackground">
          <form class="form" id="formulario_buscar_alimentos" action="#" method="post">
            <div class="row">

              <div class="col-sm-3 form-group">
                <label class="control-label" for="mes">Mes</label>
                <select class="form-control" name="mes" id="mes" required="required">
                  <option value="">Seleccione</option>
                  <?php
                  $consulta_meses = "SELECT DISTINCT MES AS mes FROM planilla_semanas;";
                  $respuesta_meses = $Link->query($consulta_meses) or die("Error al consultar planilla_semanas: ". $Link->error);
                  if ($respuesta_meses->num_rows > 0) { ?>
                    <?php while ($mes = $respuesta_meses->fetch_assoc()) { ?>
                      <option value="<?= $mes["mes"]; ?>" <?= ($mes["mes"] == date("m")) ? "selected" : ""; ?>><?= $meses[$mes["mes"]]; ?></option>
                    <?php } ?>
                  <?php } ?>
                </select>
              </div>

              <div class="col-sm-3 form-group">
                <label for="semana_inicial">Semana</label>
                <select class="form-control" name="semana_inicial" id="semana_inicial">
                  <option value="">Seleccione</option>
                </select>
              </div>

              <div class="form-group col-sm-3">
                <label class="control-label" for="municipio">Municipio</label>
                <select class="form-control" name="municipio" id="municipio" required="required">
                  <option value="">Seleccione</option>
                  <?php
                  $condicion_municipio = "";
                  if (!empty($_SESSION["p_Municipio"])) {
                    $condicion_municipio = " AND CodigoDANE = '". $_SESSION["p_Municipio"] ."' ";
                  }
                  $consulta_municipios = "SELECT CodigoDANE AS codigo, Ciudad AS municipio FROM ubicacion WHERE CodigoDANE LIKE '". $_SESSION["p_CodDepartamento"] ."%' $condicion_municipio ORDER BY Ciudad";
                  $respuesta_municipios = $Link->query($consulta_municipios) or die("Error al consultar ubicacion: ". $Link->error);
                  if ($respuesta_municipios->num_rows > 0) { ?>
                    <?php while ($municipio = $respuesta_municipios->fetch_assoc()) { ?>
                      <option value="<?= $municipio['codigo'] ?>" <?php if ($_SESSION["p_Municipio"] == $municipio["codigo"]) { echo "selected"; } ?>><?= $municipio['municipio'] ?></option>
                    <?php } ?>
                  <?php } ?>
                </select>
              </div>

              <div class="form-group col-sm-3">
                <label class="control-label" for="institucion">Institución</label>
                <select class="form-control" name="institucion" id="institucion">
                  <option value="">Seleccione</option>
                </select>
              </div>
            </div>

            <div class="row">
              <div class="form-group col-sm-3">
                <label class="control-label" for="sede">Sede</label>
                <select class="form-control" name="sede" id="sede">
                  <option value="">Seleccione</option>
                </select>
              </div>

              <div class="form-group col-sm-3">
                <label class="control-label" for="tipo_complemento">Tipo complemento</label>
                <select class="form-control" name="tipo_complemento" id="tipo_complemento" required="required">
                  <option value="">Seleccione</option>
                </select>
              </div>

              <div class="form-group col-sm-3">
                <label class="control-label" for="tipo_alimento">Tipo alimento</label>
                <select class="form-control" name="tipo_alimento" id="tipo_alimento">
                  <?php $tipo_alimento_consulta = "SELECT * FROM tipo_despacho;"; ?>
                  <?php $tipo_alimento_respuesta = $Link->query($tipo_alimento_consulta) or die("Error al consultar tipo de alimentos: ". $Link->error); ?>
                  <option value="">Seleccione</option>
                  <?php if ($tipo_alimento_respuesta->num_rows > 0) { ?>
                    <?php while ($tipo_alimento = $tipo_alimento_respuesta->fetch_object()) { ?>
                      <option value="<?= $tipo_alimento->Id ?>"><?= $tipo_alimento->Descripcion; ?></option>
                    <?php } ?>
                  <?php } ?>
                </select>
              </div>

              <div class="form-group col-sm-3">
                <label class="control-label" for="proveedor">Proveedor</label>
                <select class="form-control" name="proveedor" id="proveedor">
                  <option value="">Seleccione</option>
                </select>
              </div>
            </div>
            <div class="row">
              <div class="form-group col-sm-3">
                <label class="control-label" for="alimento">Producto</label>
                <select class="form-control" name="alimento" id="alimento">
                  <option value="">Seleccione</option>
                </select>
              </div>

              <div class="form-group col-sm-3">
                <label class="control-label" for="ruta">Ruta</label>
                <select class="form-control" name="ruta" id="ruta">
                  <?php $ruta_consulta = "SELECT * FROM rutas;"; ?>
                  <?php $ruta_respuesta = $Link->query($ruta_consulta) or die("Error al consultar rutas: ". $Link->error); ?>
                  <option value="">Seleccione</option>
                  <?php if ($ruta_respuesta->num_rows > 0) { ?>
                    <?php while ($ruta = $ruta_respuesta->fetch_object()) { ?>
                      <option value="<?= $ruta->ID; ?>"><?= $ruta->Nombre; ?></option>
                    <?php } ?>
                  <?php } ?>
                </select>
              </div>

              <div class="form-group col-sm-3">
                <label class="control-label" for="por_totales">Ver por</label>
                <div class="checkbox">
                    <input type="checkbox"> Totales
                    <input type="hidden" name="por_totales" id="por_totales">
                </div>
              </div>

            </div>

            <div class="row">
              <div class="col-sm-12">
                <button class="btn btn-primary pull-right" type="button" name="boton_buscar" id="boton_buscar"><span class="fa fa-search"></span> Buscar</button>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>

  <?php //if (isset($_POST["boton_buscar"])) { ?>
    <div class="row" id="contenedor_orden_compras" style="display: none;">
      <div class="col-lg-12">
        <div class="ibox float-e-margins">
          <div class="ibox-content contentBackground">
            <div class="table-responsive">
               <table class="table" id="tabla_ordenes_compra">
                  <thead>
                    <tr>
                      <th>Tipo Doc</th>
                      <th>Número</th>
                      <th>Fecha / Hora</th>
                      <th>Proveedor</th>
                      <th>Producto / Alimento</th>
                      <th>Unidad Medida</th>
                      <th>Cantidad</th>
                      <th class="columna_ubicacion">Municipio</th>
                      <th class="columna_ubicacion">Sede</th>
                    </tr>
                  </thead>
                  <tbody id="tBodyTrazabilidad">

                  </tbody>
                  <tfoot>
                    <tr>
                      <th>Tipo Doc</th>
                      <th>Número</th>
                      <th>Fecha / Hora</th>
                      <th>Proveedor</th>
                      <th>Producto / Alimento</th>
                      <th>Unidad Medida</th>
                      <th>Cantidad</th>
                      <th class="columna_ubicacion">Municipio</th>
                      <th class="columna_ubicacion">Sede</th>
                    </tr>
                  </tfoot>
                </table>
            </div>
          </div>
        </div>
      </div>
    </div>


    <?php
      // $join_rutas = $condicion_ruta = $condicion_municipio = $condicion_institucion = $condicion_sede = $condicion_semana = $condicion_tipo_complemento = $condicion_tipo_alimento = $condicion_proveedor = $condicion_producto = $agrupar_totales = $seleccion_ubicacion = "";

      // if (!empty($_POST["ruta"])) {
      //   $join_rutas = "INNER JOIN rutasedes rs ON rs.cod_Sede = oce.cod_Sede";
      //   $condicion_ruta = "AND rs.IDRUTA = '". $_POST["ruta"] ."'";
      // } else {
      //   if (!empty($_POST["municipio"])) {
      //     $condicion_municipio = "AND s.cod_mun_sede = '". $_POST["municipio"] ."'";
      //   }

      //   if (!empty($_POST["institucion"])) {
      //     $condicion_institucion = "AND s.cod_inst = '". $_POST["institucion"] ."'";
      //   }

      //   if (!empty($_POST["sede"])) {
      //     $condicion_sede = "AND s.cod_sede = '". $_POST["sede"] ."'";
      //   }
      // }

      // if (!empty($_POST["semana"])) {
      //   $condicion_semana = "AND Semana = '". $_POST["semana"] ."'";
      // }

      // if (!empty($_POST["tipo_alimento"])) {
      //   $condicion_tipo_alimento = "AND p.TipoDespacho = '". $_POST["tipo_alimento"] ."'";
      // }

      // if (!empty($_POST["proveedor"])) {
      //   $condicion_proveedor = "AND oce.proveedor = '". $_POST["proveedor"] ."'";
      // }

      // if (!empty($_POST["producto"])) {
      //   $condicion_producto = "AND p.Id = '". $_POST["producto"] ."'";
      // }

      // if (!empty($_POST["por_totales"])) {
      //   $agrupar_totales = ", ocd.cod_Alimento";
      // } else {
      //   $seleccion_ubicacion = ", u.Ciudad AS municipio, s.nom_sede AS sede";
      // }

      // $consulta = "SELECT
      //             oce.Tipo_Doc AS tipo_documento,
      //             oce.Num_Doc AS numero_documento,
      //             oce.FechaHora_Elab AS fecha,
      //             oce.proveedor AS proveedor,
      //             pv.Nombrecomercial AS nombre_proveedor,
      //             p.Descripcion AS nombre_producto,
      //             p.NombreUnidad2 AS unidad_medida_producto,
      //             SUM(ocd.Cantidad) AS cantidad_producto,
      //             u.Ciudad AS municipio,
      //             s.nom_sede AS sede FROM
      //             orden_compra_enc".$_POST["mes"]."$periodo_actual oce
      //               INNER JOIN
      //             orden_compra_det".$_POST["mes"]."$periodo_actual ocd ON ocd.Num_Doc = oce.Num_Doc
      //               INNER JOIN
      //             sedes20 s ON s.cod_sede = oce.cod_Sede
      //               INNER JOIN
      //             productos20 p ON p.Codigo = ocd.cod_Alimento
      //               INNER JOIN
      //             proveedores pv ON pv.Nitcc = oce.proveedor
      //             $join_rutas
      //               INNER JOIN
      //             ubicacion u ON u.CodigoDANE = s.cod_mun_sede
      //         WHERE
      //             oce.Tipo_Complem = '". $_POST["tipo_complemento"] ."'
      //             $condicion_semana
      //             $condicion_municipio
      //             $condicion_institucion
      //             $condicion_sede
      //             $condicion_tipo_complemento
      //             $condicion_tipo_alimento
      //             $condicion_proveedor
      //             $condicion_producto
      //             $condicion_ruta
      //         GROUP BY
      //             s.cod_mun_sede, s.cod_inst, s.nom_sede $agrupar_totales;";
      //   echo $consulta;
    ?>
  <?php //} ?>
</div>

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
<script src="<?php echo $baseUrl; ?>/theme/js/plugins/select2/select2.full.min.js"></script>

<script type="text/javascript">
  $(document).ready(function() {
    $('select').select2({width: "100%"});
    $('input').iCheck({
      checkboxClass: 'icheckbox_square-green',
      radioClass: "iradio_square-green"
    });

    $(document).on('change', '#mes', function (){ cargar_semanas($(this).val()); });
    $(document).on('change', '#municipio', function(){ buscar_institucion($(this).val()); });
    $(document).on('change', '#institucion', function(){ buscar_sede($(this).val()); });
    $(document).on('change', '#semana_inicial, #municipio, #institucion, #sede', function(){ buscar_complemento(); });
    $(document).on('change', '#tipo_alimento', function() { buscar_alimentos(); buscar_proveedores(); });
    $(document).on('change', '#ruta', function() { bloquear_campos_ubicacion(); });
    $(document).on('click', '#boton_buscar', function() { buscar_ordenes_compra(); });


    $('#mes').trigger('change');
    <?php if ($_SESSION["p_Municipio"] != "") { ?> $('#municipio').trigger('change'); <?php } ?>

    $('input').on('ifChecked', function(event){
      $('#por_totales').val('1');
    });

    $('input').on('ifUnchecked', function(event){
      $('#por_totales').val('0');
    });
  });

  function cargar_semanas($mes, $semana = '')
  {
    $.ajax({
      url: 'functions/fn_buscar_semanas_mes.php',
      type: 'POST',
      dataType: 'HTML',
      data: {
        'mes': $mes,
        'semana' : $semana
      },
    })
    .done(function(data) {
      if ($semana != '') {
        $('#semana_inicial').select2('val', '');
        $('#tipo_complemento').html('<option value="">Seleccione</option>');

        buscar_complemento();
      } else {
        $('#semana_inicial').select2('val', '');
        $('#semana_inicial').html(data);

        if ($("#municipio").val() == "" && $("#institucion").val() == "" && $("#sede").val() == "") {
          $('#tipo_complemento').html('<option value="">Seleccione</option>');
        } else {
          buscar_complemento();
        }
      }
    })
    .fail(function(data) {
      console.log(data.responseText);
    });
  }

  function buscar_institucion(municipio)
  {
    $.ajax({
      type: 'POST',
      url: 'functions/fn_buscar_institucion_municipio.php',
      data: { 'municipio': municipio }
    })
    .done(function(data){
      $('#institucion').select('val', '');
      $('#institucion').html(data);
    });
  }

  function buscar_sede(institucion)
  {
    $.ajax({
      type: 'POST',
      url: 'functions/fn_buscar_sedes_institucion.php',
      dataType: 'HTML',
      data: { 'institucion': institucion }
    })
    .done(function(data){
      $('#sede').select2('val', '');
      $('#sede').html(data);
    })
    .fail(function(data){ data.responseText; });
  }

  function buscar_complemento()
  {
    $.ajax({
      url: 'functions/fn_buscar_complemento.php',
      type: 'POST',
      dataType: 'HTML',
      data: {
        mes: $('#mes').val(),
        sede: $('#sede').val(),
        municipio: $('#municipio').val(),
        institucion: $('#institucion').val(),
        semana_final: $('#semana_final').val(),
        semana_inicial: $('#semana_inicial').val(),
        ruta: $('#ruta').val()
      },
    })
    .done(function(data) {
      $('#tipo_complemento').select2('val', '');
      $('#tipo_complemento').html(data);
    })
    .fail(function(data) {
      console.log(data.responseText);
    });
  }

  function buscar_proveedores()
  {
    $.ajax({
      url: 'functions/fn_buscar_proveedor.php',
      type: 'POST',
      dataType: 'HTML',
      data: {tipo_alimento: $('#tipo_alimento').val()},
    })
    .done(function(data) {
      $('#proveedor').select2('val', '');
      $('#proveedor').html(data);
    })
    .fail(function(data) {
      console.log(data.responseText);
    });
  }

  function buscar_alimentos()
  {
    $.ajax({
      url: 'functions/fn_buscar_alimentos_por_tipo.php',
      type: 'POST',
      dataType: 'HTML',
      data: {tipo_alimento: $('#tipo_alimento').val()},
    })
    .done(function(data) {
      $('#alimento').select2('val', '');
      $('#alimento').html(data);
    })
    .fail(function(data) {
      console.log(data.responseText);
    });
  }

  function bloquear_campos_ubicacion()
  {
    var ruta = $('#ruta').val();

    if (ruta != '') {
      $('#municipio').select2('val', '');
      $('#municipio').attr('disabled', true);
      $('#institucion').select2('val', '');
      $('#institucion').attr('disabled', true);
      $('#sede').select2('val', '');
      $('#sede').attr('disabled', true);
    } else {
      $('#municipio').select2('val', <?= $_SESSION["p_Municipio"]; ?>);
      $('#municipio').attr('disabled', false);
      $('#institucion').select2('val', '');
      $('#institucion').attr('disabled', false);
      $('#sede').select2('val', '');
      $('#sede').attr('disabled', false);
    }
  }

  function buscar_ordenes_compra()
  {
    if (validar_campos()) {
      $('#contenedor_orden_compras').css('display', 'block');

      dataset1 = $('#tabla_ordenes_compra').DataTable({
        ajax: {
          method: 'POST',
          url: 'functions/fn_buscar_ordenes_compra_tabla.php',
          data:{
            mes : $('#mes').val(),
            semana: $('#semana_inicial').val(),
            municipio: $('#municipio').val(),
            institucion: $('#institucion').val(),
            sede: $('#sede').val(),
            tipo_complemento: $('#tipo_complemento').val(),
            tipo_alimento: $('#tipo_alimento').val(),
            proveedor: $('#proveedor').val(),
            producto: $('#alimento').val(),
            ruta: $('#ruta').val(),
            por_totales: $('#por_totales').val(),
          }
        },
        columns:[
          { data: 'tipo_documento'},
          { data: 'numero_documento'},
          { data: 'fecha'},
          { data: 'nombre_proveedor'},
          { data: 'nombre_producto'},
          { data: 'unidad_medida_producto'},
          { data: 'cantidad_producto'},
          { data: 'municipio'},
          { data: 'sede'},
        ],
        pageLength: 25,
        responsive: true,
        destroy: true,
        dom : '<"html5buttons" B>lr<"containerBtn"><"inputFiltro"f>tip',
        buttons : [{extend:'excel', title:'Ordenes de compra', className:'btnExportarExcel', exportOptions: {columns : [0,1,2,3,4,5,6,7,8]}}],
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
        drawCallback: function(settings) {
          if ($('#por_totales').val() == '1') {
            $('.columna_ubicacion').hide();

            settings.aoColumns[7].bVisible = false
            settings.aoColumns[8].bVisible = false
          } else {
            $('.columna_ubicacion').show();

            settings.aoColumns[7].bVisible = true
            settings.aoColumns[8].bVisible = true
          }
        },
        preDrawCallback: function(settings) {
          $('#loader').fadeIn();
        }
      });
    }
  }

  function validar_campos()
  {
    if ($('#mes').val() == '') {
      Command: toastr.error('El campo sede es obligatorio', 'Validación de formulario', { onHidden: function() { $('#mes').focus(); }});
      return false;
    }

    if ($('#municipio').val() == '') {
      Command: toastr.error('El campo municipio es obligatorio', 'Validación de formulario', { onHidden: function() { $('#municipio').focus(); }});
      return false;
    }

    if ($('#tipo_complemento').val() == '') {
      Command: toastr.error('El campo tipo de complemento es obligatorio', 'Validación de formulario', { onHidden: function() { $('#tipo_complemento').focus(); }});
      return false;
    } else if ($('#tipo_complemento').val() != 'RPC' && $('#semana').val() == '') {
      Command: toastr.error('Para el tipo de complemento '+ $('#tipo_complemento').val() +' el campo semana es obligatorio', 'Validación de formulario', { onHidden: function() { $('#semana').focus(); }});
      return false;
    }

    return true;
  }
</script>

<?php mysqli_close($Link); ?>

</body>
</html>