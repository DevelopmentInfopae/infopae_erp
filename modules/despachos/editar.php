<?php
  include '../../header.php';
  require_once '../../db/conexion.php';

  $titulo = "Editar despachos";
  $fecha_despacho = "";
  $periodo_actual = $_SESSION['periodoActual'];
  $codigo_municipio = $_SESSION["p_Municipio"];
  $codigo_departamento = $_SESSION['p_CodDepartamento'];
  $nombre_meses = ["01"=>"Enero","02"=>"Febrero","03"=>"Marzo","04"=>"Abril","05"=>"Mayo","06"=>"Junio","07"=>"Julio","08"=>"Agosto","09"=>"Septiembre","10"=>"Octubre","11"=>"Noviembre","12"=>"Diciembre"];

  $consulta_mes = "SELECT DISTINCT MES as codigo FROM planilla_semanas;";
  $respuesta_consulta_mes = $Link->query($consulta_mes) or die("Error al consultar los meses: ". $Link->error);

  if (isset($_POST["mes"]))
  {
    $mes = $_POST["mes"];
    $semana = $_POST["semana"];

    $consulta_semana = "SELECT DISTINCT SEMANA AS semana FROM planilla_semanas WHERE MES = '$mes'";
    $respuesta_consulta_semana = $Link->query($consulta_semana) or die("Error al consultar las semanas del mes: ". $Link->error);

    $consulta_complementos = "SELECT DISTINCT Tipo_Complem AS tipo_complemento FROM despachos_enc$mes$periodo_actual WHERE Semana='$semana' ORDER BY tipo_complemento;";
    $respuesta_complementos = $Link->query($consulta_complementos) or die ('Error al consultar los complementos: '. $Link->error);
  }

  $consulta_municipios = "SELECT DISTINCT codigoDANE as codigo, ciudad FROM ubicacion WHERE ETC = 0";

  if (! empty($codigo_departamento)) { $consulta_municipios .= " AND CodigoDANE LIKE '$codigo_departamento%'"; }
  if (! empty($codigo_municipio)) { $consulta_municipios .= " AND CodigoDANE = '$codigo_municipio'"; }

  $consulta_municipios .= " ORDER BY ciudad ASC";
  $respuesta_municipios = $Link->query($consulta_municipios) or die("Error al consultar los municipios: ". $Link->error);

  if ((isset($_POST["municipio"]) && ! empty($_POST["municipio"])) || ! empty($codigo_municipio))
  {
    $consulta_instituciones = "SELECT DISTINCT s.cod_inst AS codigo, s.nom_inst AS nombre FROM sedes$periodo_actual s LEFT JOIN sedes_cobertura sc ON s.cod_sede = sc.cod_sede WHERE 1=1";
    $municipio = (isset($_POST["municipio"])) ? $_POST["municipio"] : $codigo_municipio;
    $consulta_instituciones .= " AND s.cod_mun_sede = '$municipio'";
    $consulta_instituciones .= " ORDER BY s.nom_inst ASC";
    $respuesta_instituciones = $Link->query($consulta_instituciones) or die("Error al consultar las instituciones: ". $Link->error);
  }

  if(isset($_POST['institucion']) && ! empty($_POST['institucion']))
  {
    $consulta_sedes = "SELECT DISTINCT s.cod_sede AS codigo, s.nom_sede AS nombre FROM sedes$periodo_actual s LEFT JOIN sedes_cobertura sc ON s.cod_sede = sc.cod_sede WHERE 1=1 ";
    $consulta_sedes .= " AND s.cod_inst = '". $_POST["institucion"] ."'";
    $respuesta_sedes = $Link->query($consulta_sedes) or die ("Error al consultar las sedes: ". $Link->error);
  }

  if (isset($_POST) && ! empty($_POST))
  {
    $mes = $_POST["mes"];
    $semana = $_POST["semana"];
    $tipo_complemento = $_POST["complemento"];

    // Consulta que retorna los productos de los depachos segun los filtros seleccionados.
    $condicion_sede = (isset($_POST["sede"]) && ! empty($_POST['sede'])) ? " AND d.cod_Sede = '".$_POST["sede"]."'" : "";
    $condicion_municipio = (isset($_POST["municipio"]) && ! empty($_POST['municipio'])) ? " AND s.cod_mun_sede = '".$_POST["municipio"]."'" : "";
    $condicion_intitucion = (isset($_POST["institucion"]) && ! empty($_POST['institucion'])) ? " AND s.cod_inst = '".$_POST["institucion"]."'" : "";

    $consulta_productos_despachos = "SELECT
                                      DISTINCT
                                      p.CodigoProducto AS codigo,
                                      p.Descripcion As descripcion
                                    FROM
                                        productosmovdet$mes$periodo_actual p
                                            INNER JOIN
                                        despachos_enc$mes$periodo_actual d ON (p.numero = d.Num_Doc)
                                            INNER JOIN
                                        sedes$periodo_actual s ON (p.BodegaDestino = s.cod_sede)
                                    WHERE
                                        d.Semana = '$semana'
                                        AND d.Tipo_complem = '$tipo_complemento'
                                        $condicion_municipio
                                        $condicion_intitucion
                                        $condicion_sede
                                    ORDER BY descripcion;";
    $resultado_productos_despachos = $Link->query($consulta_productos_despachos) or die("Error al consultar los productos de los despachos. ". $Link->error);

    $productos_array = [];
    if ($resultado_productos_despachos->num_rows > 0)
    {
      $ids_despachos = [];
      while ($registro_productos_despachos = $resultado_productos_despachos->fetch_object())
      {
        $productos_array[$registro_productos_despachos->codigo] = $registro_productos_despachos->descripcion;
      }
    }

    // Consulta que retorna la fecha de despacho de los productos. Se utiliza para hallar la fecha minima permitida.
    $consulta_fecha_despacho = "SELECT
                                  ANO AS ano,
                                  MES AS mes,
                                  MAX(CONVERT(DIA, UNSIGNED INTEGER)) AS dia
                                FROM
                                  planilla_semanas
                                WHERE
                                  semana = '$semana';";
    $respuesta_fecha_despacho = $Link->query($consulta_fecha_despacho) or die("Erro al consultar la fecha de despacho: ". $Link->error);
    if ($respuesta_fecha_despacho->num_rows > 0)
    {
      $registro_fecha_despacho = $respuesta_fecha_despacho->fetch_object();
      $fecha_despacho = $registro_fecha_despacho->ano."-".$registro_fecha_despacho->mes."-".$registro_fecha_despacho->dia;
    }

    // Consulta que retorna los tipos de vehículos.
    $consulta_tipo_vehiculos = "SELECT Id as id, Nombre AS nombre FROM tipovehiculo;";
    $respuesta_tipo_vehiculos = $Link->query($consulta_tipo_vehiculos) or die("Error al consultar los tipo de vehiculos: ". $Link->error);
  }
?>

<div class="row wrapper wrapper-content border-bottom white-bg page-heading">
  <div class="col-lg-8">
    <h2><?= $titulo; ?></h2>
    <ol class="breadcrumb">
      <li>
        <a href="<?= $baseUrl; ?>">Home</a>
      </li>
      <li>
        <a href="<?= $baseUrl . '/modules/despachos/despachos.php'; ?>">Despachos</a>
      </li>
      <li class="active">
        <strong><?= $titulo; ?></strong>
      </li>
    </ol>
  </div>
  <div class="col-lg-4">
    <div class="title-action">
    </div>
  </div>
</div>

<div class="wrapper wrapper-content animated fadeInRight">
  <div class="row">
    <div class="col-lg-12">
      <div class="ibox float-e-margins">
        <div class="ibox-content contentBackground">
          <h2>Filtros de búsqueda</h2>
          <form class="col-lg-12" action="editar.php" name="formulario_buscar_despachos" id="formulario_buscar_despachos" method="post">
            <div class="row">
              <div class="form-group col-md-4">
                <label for="mes">Mes *</label>
                <select class="form-control" name="mes" id="mes" required="required">
                  <option value="">Seleccione</option>
                  <?php if ($respuesta_consulta_mes->num_rows > 0) : ?>
                    <?php while ($mes = $respuesta_consulta_mes->fetch_object()) : ?>
                      <option value="<?= $mes->codigo; ?>"
                        <?= (isset($_POST["mes"]) && $_POST["mes"] == $mes->codigo) ? "selected" : ""; ?>>
                        <?= $nombre_meses[$mes->codigo]; ?></option>
                    <?php endwhile ?>
                  <?php endif ?>
                </select>
              </div>

              <div class="form-group col-md-4">
                <label for="semana">Semana *</label>
                <select class="form-control" name="semana" id="semana" required="required">
                  <option value="">Seleccione</option>
                  <?php if (isset($_POST["mes"])) : ?>
                    <?php if ($respuesta_consulta_semana->num_rows > 0) : ?>
                      <?php while ($semana = $respuesta_consulta_semana->fetch_object()) : ?>
                        <option value="<?= $semana->semana; ?>" <?= (isset($_POST) && $_POST["semana"] == $semana->semana) ? "selected" : ""; ?>><?= "Semana ".$semana->semana; ?></option>
                      <?php endwhile ?>
                    <?php endif ?>
                  <?php endif ?>
                </select>
              </div>

              <div class="form-group col-md-4">
                <label for="complemento">Tipo Complemento *</label>
                <select class="form-control" name="complemento" id="complemento" required="required">
                  <option value="">Seleccione</option>
                  <?php if ($respuesta_complementos->num_rows > 0) : ?>
                    <?php while($complementos = $respuesta_complementos->fetch_object()) : ?>
                      <option value="<?= $complementos->tipo_complemento; ?>" <?= (isset($_POST['complemento']) && ($_POST['complemento'] == $complementos->tipo_complemento)) ? 'selected' : ''; ?>><?= $complementos->tipo_complemento; ?></option>
                    <?php endwhile ?>
                  <?php endif ?>
                </select>
              </div>
            </div>

            <div class="row">
              <div class="form-group col-md-4">
                <label for="municipio">Municipio</label>
                <select class="form-control" name="municipio" id="municipio">
                  <option value="">Seleccione</option>
                  <?php if($respuesta_municipios->num_rows > 0) : ?>
                    <?php while ($municipio = $respuesta_municipios->fetch_object()) : ?>
                      <option value="<?= $municipio->codigo; ?>" <?= ((isset($_POST["municipio"]) && $_POST["municipio"] == $municipio->codigo) || ($codigo_municipio == $municipio->codigo)) ? "selected" : "";?>><?= $municipio->ciudad; ?></option>
                    <?php endwhile ?>
                  <?php endif ?>
                </select>
              </div>

              <div class="form-group col-md-4">
                <label for="institucion">Institución</label>
                <select class="form-control" name="institucion" id="institucion">
                  <option value="">Seleccione</option>
                  <?php if($respuesta_instituciones->num_rows > 0) : ?>
                    <?php while ($institucion = $respuesta_instituciones->fetch_object()) : ?>
                      <option value="<?= $institucion->codigo; ?>" <?= (isset($_POST["institucion"]) && $_POST["institucion"] == $institucion->codigo) ? "selected" : ""; ?>> <?= $institucion->nombre; ?></option>
                    <?php endwhile ?>
                  <?php endif ?>
                </select>
              </div>

              <div class="form-group col-md-4">
                <label for="sede">sede</label>
                <select class="form-control" name="sede" id="sede">
                  <option value="">Seleccione</option>
                  <?php if($respuesta_sedes->num_rows > 0) : ?>
                    <?php while ($sede = $respuesta_sedes->fetch_object()) : ?>
                      <option value="<?= $sede->codigo; ?>" <?= (isset($_POST["sede"]) && $_POST["sede"] == $sede->codigo) ? "selected" : ""; ?>><?= $sede->nombre; ?></option>
                    <?php endwhile ?>
                  <?php endif ?>
                </select>
              </div>
            </div>

            <div class="row">
              <div class="form-group col-xs-12">
                <button type="submit" class="btn btn-primary pull-right" name="boton_buscar" id="boton_buscar"><i class="fa fa-search"></i> Buscar</button>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>

    <?php if (isset($_POST) && ! empty($_POST)) : ?>
      <div class="col-lg-12 contenedor_campos_edicion">
        <div class="ibox float-e-margins">
          <div class="ibox-content contentBackground">
            <?php if (! empty($productos_array)) : ?>
              <h2>Campos de edición <button type="button" class="btn btn-primary pull-right" id="boton_agregar_producto"><i class="fa fa-plus"></i></button></h2>
              <form class="col-lg-12" name="formulario_editar_despacho" id="formulario_editar_despacho">
                <input type="hidden" name="mes_edicion" value="<?= $_POST["mes"]; ?>">
                <input type="hidden" name="sede_edicion" value="<?= $_POST["sede"]; ?>">
                <input type="hidden" name="semana_edicion" value="<?= $_POST["semana"]; ?>">
                <input type="hidden" name="municipio_edicion" value="<?= $_POST["municipio"]; ?>">
                <input type="hidden" name="complemento_edicion" value="<?= $_POST["complemento"]; ?>">
                <input type="hidden" name="institucion_edicion" value="<?= $_POST["institucion"]; ?>">
                <div class="row">
                  <div class="col-sm-4">
                    <label for="tipo_vehiculo">Tipo de vehículos</label>
                    <select class="form-control tipo_vehiculo" name="tipo_vehiculo" id="tipo_vehiculo">
                      <option value="">Seleccione</option>
                      <?php if ($respuesta_tipo_vehiculos->num_rows > 0) : ?>
                        <?php while ($tipo_vehiculo = $respuesta_tipo_vehiculos->fetch_object()) : ?>
                          <option value="<?= $tipo_vehiculo->id; ?>"><?= $tipo_vehiculo->nombre; ?></option>
                        <?php endwhile ?>
                      <?php endif ?>
                    </select>
                    <label class="error" for="tipo_vehiculo" style="display: none"></label>
                  </div>

                  <div class="col-sm-4">
                    <label for="placa">Placa</label>
                    <input type="text" class="form-control placa" name="placa" id="placa" minlength="7" maxlength="7" pattern="^([A-Z]{3}-[A-Z0-9]{3})$" placeholder="Eje: MDX-89E">
                  </div>

                  <div class="col-sm-4">
                    <label for="conductor">Conductor</label>
                    <input type="text" class="form-control conductor" name="conductor" id="conductor">
                  </div>
                </div>

                <hr>

                <table class="table table-striped table-condenced" id="tabla_editar_productos_despacho">
                  <thead>
                    <tr>
                      <th class="col-md-4">Productos</th>
                      <th class="col-md-2">Lote</th>
                      <th class="col-md-2">Fecha vencimiento</th>
                      <th class="col-md-4">Marca</th>
                      <th>Eliminar</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr id="1">
                      <td>
                        <select class="form-control producto" name="producto[0]" id="producto_0" required="required" style="width: 100%">
                          <option value="">Seleccione</option>
                          <?php foreach ($productos_array as $clave => $producto) : ?>
                            <option value="<?= $clave; ?>"><?= $producto; ?></option>
                          <?php endforeach ?>
                        </select>
                        <label class="error" for="producto_0" style="display: none"></label>
                      </td>
                      <td><input type="text" class="form-control lote" name="lote[0]" id="lote_0" required="required"/></td>
                      <td><input type="date" class="form-control fecha_vencimiento" name="fecha_vencimiento[0]" id="fecha_vencimiento_0" required="required" min="<?= $fecha_despacho; ?>"/></td>
                      <td><input type="text" class="form-control marca" name="marca[0]" id="marca_0"/></td>
                      <td class="text-center" style="vertical-align: middle;"><i class="fa fa-trash fa-1x remover_fila" data-indice_fila="0" style="cursor: pointer; font-size: 24px;"></i></td>
                    </tr>
                  </tbody>
                </table>

                <div class="row">
                  <div class="col-sm-12 text-right">
                    <button type="button" class="btn btn-primary" name="boton_guardar_datos_edicion" id="boton_guardar_datos_edicion"><i class="fa fa-check"></i> Guardar</button>
                  </div>
                </div>
              </form>
            <?php else : ?>
              <div class="well text-center text-muted" style="margin-bottom: 0px;"><strong>No se encontraron despachos con los registros seleccionados</strong></div>
            <?php endif ?>
          </div>
        </div>
      </div>
    <?php endif ?>
  </div>
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
<script src="<?php echo $baseUrl; ?>/theme/js/plugins/iCheck/icheck.min.js"></script>
<script src="<?php echo $baseUrl; ?>/theme/js/plugins/toastr/toastr.min.js"></script>
<script src="<?php echo $baseUrl; ?>/theme/js/plugins/select2/select2.full.min.js"></script>
<script src="<?php echo $baseUrl; ?>/theme/js/plugins/validate/jquery.validate.min.js"></script>

<script>
  $(document).ready(function()
  {
    _consecutivo_fila = 1;

    $('select').select2();

    toastr.options = {
      "closeButton": true,
      "debug": false,
      "progressBar": true,
      "preventDuplicates": false,
      "positionClass": "toast-bottom-right",
      "onclick": null,
      "showDuration": "400",
      "hideDuration": "1000",
      "timeOut": "4000",
      "extendedTimeOut": "1000",
      "showEasing": "swing",
      "hideEasing": "linear",
      "showMethod": "fadeIn",
      "hideMethod": "fadeOut"
    }

    // Configuración para la validación del formulario de búsqueda de sedes.
    jQuery.extend(jQuery.validator.messages, { required: "Campo obligatorio.", remote: "Por favor, rellena este campo.", email: "Por favor, escribe una dirección de correo válida", url: "Por favor, escribe una URL válida.", date: "Por favor, escribe una fecha válida.", dateISO: "Por favor, escribe una fecha (ISO) válida.", number: "Por favor, escribe un número entero válido.", digits: "Por favor, escribe sólo dígitos.", creditcard: "Por favor, escribe un número de tarjeta válido.", equalTo: "Por favor, escribe el mismo valor de nuevo.", accept: "Por favor, escribe un valor con una extensión aceptada.", maxlength: jQuery.validator.format("Por favor, no escribas más de {0} caracteres."), minlength: jQuery.validator.format("Por favor, no escribas menos de {0} caracteres."), rangelength: jQuery.validator.format("Por favor, escribe un valor entre {0} y {1} caracteres."), range: jQuery.validator.format("Por favor, escribe un valor entre {0} y {1}."), max: jQuery.validator.format("Por favor, escribe un valor menor o igual a {0}."), min: jQuery.validator.format("Por favor, escribe un valor mayor o igual a {0}.") });

    $(document).on('change', '#mes', cargar_semanas);
    $(document).on('change', '#institucion', cargar_sede);
    $(document).on('change', '#semana', cargar_complementos);
    $(document).on('change', '#municipio', cargar_instituciones);
    $(document).on('click', '#boton_agregar_producto', agregar_producto);
    $(document).on('click', '.remover_fila', function () { remover_fila($(this)); });
    $(document).on('click', '#boton_guardar_datos_edicion', validar_campos_edicion);
    $(document).on('change', '.producto', function () { validar_producto_repetido($(this)); });
    $(document).on('change', '#sede', function () { $('.contenedor_campos_edicion').fadeOut(); });
    $(document).on('change', '#complemento', function () { $('.contenedor_campos_edicion').fadeOut(); });
  });

  function cargar_semanas()
  {
    $.ajax({
      url: 'functions/fn_despacho_buscar_semanas.php',
      type: 'POST',
      dataType: 'HTML',
      data: {mes: parseInt($('#mes').val())},
      beforeSend: function(){ $('#loader').fadeIn(); }
    })
    .done(function(datos) {
      $('#semana').html(datos);
      $('#semana').select2('val', '');
      $('#complemento').select2('val', '');

      $('#loader').fadeOut();
      $('.contenedor_campos_edicion').fadeOut();
    })
    .fail(function(datos) {
      $('#loader').fadeOut();
      console.log(datos.responseText);
    });
  }

  function cargar_complementos()
  {
    $.ajax({
      url: 'functions/fn_despachos_buscar_complemento_semana.php',
      type: 'POST',
      dataType: 'HTML',
      data: {
        mes: $('#mes').val(),
        semana: $('#semana').val()
      },
      beforeSend: function() { $('#loader').fadeIn(); }
    })
    .done(function(datos) {
      $('#complemento').html(datos);
      $('#complemento').select2('val', '');

      $('#loader').fadeOut();
      $('.contenedor_campos_edicion').fadeOut();
    })
    .fail(function(datos) {
      console.log(datos.responseText);
      $('#loader').fadeOut();
    });
  }

  function cargar_instituciones()
  {
    $.ajax({
      url: "functions/fn_despacho_buscar_institucion.php",
      type: "POST",
      dateType: "HTML",
      data: {
        municipio: $('#municipio').val()
      },
      beforeSend: function() { $('#loader').fadeIn(); }
    })
    .done(function(datos)
    {
      $('#institucion').html(datos);
      $('#sede').select2('val', '');
      $('#institucion').select2('val', '');

      $('.contenedor_campos_edicion').fadeOut();
      $('#loader').fadeOut();
    })
    .fail(function(datos)
    {
      console.log(datos.responseText);
      $('#loader').fadeOut();
    });
  }

  function cargar_sede()
  {
    $.ajax({
      url: "functions/fn_despacho_buscar_sede.php",
      type: "POST",
      dataType: "HTML",
      data: { institucion: $('#institucion').val() },
      beforeSend: function(){ $('#loader').fadeIn(); }
    })
    .done(function(datos)
    {
      $('#sede').html(datos);
      $('#sede').select2('val', '');

      $('.contenedor_campos_edicion').fadeOut();
      $('#loader').fadeOut();
    })
    .fail(function(datos)
    {
      console.log(datos.responseText);
      $('#loader').fadeOut();
    });
  }

  function agregar_producto()
  {
    $.ajax({
      url: 'functions/fn_despachos_buscar_productos_despachos.php',
      type: 'POST',
      dataType: 'HTML',
      data: {
        mes: $('#mes').val(),
        sede: $('#sede').val(),
        semana: $('#semana').val(),
        municipio: $('#municipio').val(),
        institucion: $('#institucion').val(),
        tipo_complemento: $('#complemento').val()
      },
      beforeSend: function() { $('#loader').fadeIn(); }
    })
    .done(function(datos)
    {
      var campos_edicion_producto = '<tr id="'+_consecutivo_fila+'">'+
                                      '<td>'+
                                        '<select class="form-control producto" name="producto['+_consecutivo_fila+']" id="producto_'+_consecutivo_fila+'" required="required" style="width: 100%">'+
                                          datos +
                                        '</select>'+
                                        '<label class="error" for="producto_'+_consecutivo_fila+'" style="display: none"></label>'+
                                      '</td>'+
                                      '<td><input type="text" class="form-control lote" name="lote['+_consecutivo_fila+']" id="lote_'+_consecutivo_fila+'" required="required"/></td>'+
                                      '<td><input type="date" class="form-control fecha_vencimiento" name="fecha_vencimiento['+_consecutivo_fila+']" id="fecha_vencimiento_'+_consecutivo_fila+'" min="<?= $fecha_despacho; ?>" required="required"/></td>'+
                                      '<td><input type="text" class="form-control marca" name="marca['+_consecutivo_fila+']" id="marca_'+_consecutivo_fila+'"/></td>'+
                                      '<td class="text-center" style="vertical-align: middle;"><i class="fa fa-trash fa-1x remover_fila" data-indice_fila="'+_consecutivo_fila+'" style="cursor: pointer; font-size: 24px;"></i></td>'+
                                    '</tr>';
      $('#tabla_editar_productos_despacho').append(campos_edicion_producto);

      _consecutivo_fila ++;

      $('select').select2();
      $('#loader').fadeOut();
    })
    .fail(function(datos)
    {
      console.log(datos.responseText);
      $('#loader').fadeOut();
    });
  }

  function remover_fila(boton)
  {
    var indice_fila = boton.data('indice_fila');

    $('tbody tr#'+indice_fila).remove();
  }

  function validar_producto_repetido(control_select)
  {
    var id_producto = control_select.val();
    var indice_fila = control_select.index('.producto');

    $('.producto').each(function(indice) {
      if (indice != indice_fila)
      {
        if ($(this).find('option:selected').val() == id_producto)
        {
          Command: toastr.warning('No puede seleccionar un mismo producto.', 'Advertencia', { onHidden: function() { control_select.select2('val', ''); } });
        }
      }
    });
  }

  function validar_campos_edicion()
  {
    if ($('#formulario_editar_despacho').valid())
    {
      $.ajax({
        url: 'functions/fn_despachos_actualizar_productos_despachos.php',
        type: 'POST',
        dataType: 'JSON',
        data: $("#formulario_editar_despacho").serialize(),
        beforeSend: function() { $('#loader').fadeIn(); }
      })
      .done(function(datos) {
        Command: toastr.success('Los despachos se actualizaron correctamente.', 'Proceso exitoso', { onHidden: function(){ location.reload(); } });
        $('#loader').fadeOut();
      })
      .fail(function(datos) {
        $('#loader').fadeOut();
        Command: toastr.error(datos.responseText, 'Error en el proceso', { onHidden: function(){ location.reload(); } });
      });
    }
  }
</script>

<?php mysqli_close($Link); ?>

</body>
</html>
