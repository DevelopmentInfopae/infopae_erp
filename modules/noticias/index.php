<?php
  include '../../header.php';

  if ($permisos['configuracion'] == "0" || $permisos['configuracion'] == "1") {
    ?><script type="text/javascript">
      window.open('<?= $baseUrl ?>', '_self');
    </script>
  <?php exit(); }
  	  else {
        ?><script type="text/javascript">
          const list = document.querySelector(".li_configuracion");
          list.className += " active ";
        </script>
        <?php
        }
  
  $titulo = 'Noticias';

  $c_noticias = "SELECT * FROM noticias";
  $r_noticias = $Link->query($c_noticias) or die ('Error al consultar listado de noticias: '. mysqli_error($Link));
  if($r_noticias->num_rows > 0){
    while($noticia = $r_noticias->fetch_object()) {
      $noticias[] = $noticia;
    }
  }
  $nameLabel = get_titles('configuracion', 'noticias', $labels);
  $titulo = $nameLabel;
?>

<div class="row wrapper wrapper-content border-bottom white-bg page-heading">
  <div class="col-lg-8">
    <h2><?php echo $titulo; ?></h2>
    <ol class="breadcrumb">
      <li>
        <a href="<?php echo $baseUrl; ?>">Home</a>
      </li>
      <li class="active">
        <strong><?php echo $titulo; ?></strong>
      </li>
    </ol>
  </div>
  <div class="col-lg-4">
    <div class="title-action">
      <a href="#" class="btn btn-primary" id="guardar_noticia"><i class="fa fa-check"></i> Guardar </a>
    </div>
  </div>
</div>

<div class="wrapper wrapper-content animated fadeInRight">
  <div class="row">
    <div class="col-lg-12">
      <div class="ibox float-e-margins">
        <div class="ibox-content contentBackground">
          <form id="formulario_crear_noticia" action="functions/fn_noticias_crear.php" method="post">
            <div class="row">
              <div class="col-sm-4">
                <div class="form-group">
                  <label for="nombre">Logo</label>
                  <div class="fileinput fileinput-new " data-provides="fileinput" style="width: 100%;">
                    <div class="fileinput-preview thumbnail img-banner" data-trigger="fileinput" style="width: inherit;">
                      <img class="img-responsive" <?php if (isset($datos['imagen']) && $datos['imagen'] != "") { ?> src="<?= $datos['imagen']; ?>" <?php } ?> alt="">
                    </div>
                    <div class="text-center">
                      <span class="btn btn-default btn-file">
                        <span class="fileinput-new">seleccionar</span>
                        <span class="fileinput-exists">Cambiar</span>
                        <input type="file" name="imagen" id="imagen" accept="image/jpg, image/jpeg, image/png">
                      </span>
                      <a href="#" class="btn btn-default fileinput-exists" data-dismiss="fileinput">Quitar</a>
                    </div>
                  </div>
                </div>
              </div>

              <div class="col-sm-8">
                <div class="row">
                  <div class="col-sm-6">
                    <div class="form-group">
                      <label for="fecha">Fecha</label>
                      <input type="date" class="form-control" name="fecha" id="fecha" value="<?= date("Y-m-d") ?>" required>
                    </div>
                  </div>

                  <div class="col-sm-6">
                    <div class="form-group">
                      <label for="titulo">Título</label>
                      <input type="text" class="form-control" name="titulo" id="titulo" required>
                    </div>
                  </div>
                </div>

                <div class="row">
                  <div class="col-sm-12">
                    <label for="titulo">Descripción</label>
                    <textarea class="form-control" id="descripcion" name="descripcion" rows="6" required></textarea>
                  </div>
                </div>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="wrapper wrapper-content animated fadeInRight">
  <div class="row">
    <div class="col-lg-12">
      <div class="ibox float-e-margins">
        <div class="ibox-content contentBackground">
          <table id="tabla_noticias" class="table table-striped table-hover selectableRows">
            <thead>
              <tr>
                <th>Fecha</th>
                <th>Título</th>
                <th>Descripción</th>
                <th class="text-center">Acciones</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($noticias as $key => $noticia) { ?>
              <tr>
                <td align="left"><?= $noticia->fecha; ?></td>
                <td align="left"><?= $noticia->titulo; ?></td>
                <td align="left"><?= $noticia->descripcion; ?></td>
                <td align="center">
                  <div class="btn-group">
                    <div class="dropdown">
                      <button class="btn btn-primary btn-sm" id="dLabel" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        Acciones <span class="caret"></span>
                      </button>
                      <ul class="dropdown-menu pull-right" aria-labelledby="dLabel">
                        <li>
                          <a class="editar_noticia" data-id_noticia="<?= $noticia->id; ?>"><i class="fa fa-pencil-square-o"></i> Editar</a>
                        </li>
                        <li>
                          <a class="eliminar_noticia" data-id_noticia="<?= $noticia->id; ?>"><i class="fa fa-trash"></i> Eliminar</a>
                        </li>
                      </ul>
                    </div>
                  </div>
                </td>
              </tr>
              <?php } ?>
            </tbody>
            <tfoot>
              <tr>
                <th>Fecha</th>
                <th>Título</th>
                <th>Descripción</th>
                <th class="text-center">Acciones</th>
              </tr>
            </tfoot>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Ventana de confirmación -->
<div class="modal inmodal fade" id="ventanaConfirmar" tabindex="-1" role="dialog" style="display: none;" aria-hidden="true">
  <div class="modal-dialog modal-sm">
    <div class="modal-content">
      <div class="modal-header text-info" style="padding: 15px;">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only">Close</span></button>
        <h3><i class="fa fa-question-circle fa-lg" aria-hidden="true"></i> Confirmación InfoPAE </h3>
      </div>
      <div class="modal-body">
          <p class="text-center"></p>
      </div>
      <div class="modal-footer">
        <input type="hidden" id="idAEliminar">
        <button type="button" class="btn btn-white btn-sm" data-dismiss="modal">No</button>
        <button type="button" class="btn btn-primary btn-sm" data-dismiss="modal" id="eliminarTipoDespacho">Si</button>
      </div>
    </div>
  </div>
</div>

<!-- Ventana de formulario -->
<div class="modal inmodal fade" id="ventana_editar_noticia" tabindex="-1" role="dialog" style="display: none;" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header text-info" style="padding: 15px;">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only">Close</span></button>
        <h3><i class="fa fa-pencil-square-o fa-lg" aria-hidden="true"></i> Editar Noticia</h3>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-lg-12">
            <form id="formulario_actualizar_noticia" action="functions/fn_noticias_editar.php" method="post">
              <input type="hidden" name="id" id="id">
              <div class="row">
                <div class="col-sm-4">
                  <div class="form-group">
                    <label for="nombre">Logo</label>
                    <div class="fileinput fileinput-new " data-provides="fileinput" style="width: 100%;">
                      <div class="fileinput-preview thumbnail img-banner" data-trigger="fileinput" style="width: inherit;">
                        <img class="img-responsive" id="imagen_previa" <?php if (isset($datos['imagen']) && $datos['imagen'] != "") { ?> src="<?= $datos['imagen']; ?>" <?php } ?> alt="">
                      </div>
                      <div class="text-center">
                        <span class="btn btn-default btn-file">
                          <span class="fileinput-new">seleccionar</span>
                          <span class="fileinput-exists">Cambiar</span>
                          <input type="file" name="imagen" id="imagen" accept="image/jpg, image/jpeg, image/png">
                        </span>
                        <a href="#" class="btn btn-default fileinput-exists" data-dismiss="fileinput">Quitar</a>
                      </div>
                    </div>
                  </div>
                </div>

                <div class="col-sm-8">
                  <div class="row">
                    <div class="col-sm-6">
                      <div class="form-group">
                        <label for="fecha">Fecha</label>
                        <input type="date" class="form-control" name="fecha" id="fecha" value="<?= date("Y-m-d") ?>" required>
                      </div>
                    </div>

                    <div class="col-sm-6">
                      <div class="form-group">
                        <label for="titulo">Título</label>
                        <input type="text" class="form-control" name="titulo" id="titulo" required>
                      </div>
                    </div>
                  </div>

                  <div class="row">
                    <div class="col-sm-12">
                      <label for="titulo">Descripción</label>
                      <textarea class="form-control" id="descripcion" name="descripcion" rows="6" required></textarea>
                    </div>
                  </div>
                </div>
              </div>
            </form>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-white btn-sm" data-dismiss="modal">Cerrar</button>
        <button type="button" class="btn btn-primary btn-sm" id="actualizar_noticia">Actualizar</button>
      </div>
    </div>
  </div>
</div>

<?php include '../../footer.php'; ?>

<!-- Mainly scripts -->
<script src="<?php echo $baseUrl; ?>/theme/js/jquery-3.1.1.min.js"></script>
<script src="<?php echo $baseUrl; ?>/theme/js/bootstrap.min.js"></script>
<script src="<?php echo $baseUrl; ?>/theme/js/plugins/metisMenu/jquery.metisMenu.js"></script>
<script src="<?php echo $baseUrl; ?>/theme/js/plugins/slimscroll/jquery.slimscroll.min.js"></script>

<!-- Custom and plugin javascript -->
<script src="<?php echo $baseUrl; ?>/theme/js/inspinia.js"></script>
<script src="<?php echo $baseUrl; ?>/theme/js/plugins/pace/pace.min.js"></script>
<script src="<?php echo $baseUrl; ?>/theme/js/plugins/jasny/jasny-bootstrap.min.js"></script>
<script src="<?php echo $baseUrl; ?>/theme/js/plugins/dataTables/datatables.min.js"></script>
<script src="<?php echo $baseUrl; ?>/theme/js/plugins/toastr/toastr.min.js"></script>
<script src="<?php echo $baseUrl; ?>/theme/js/plugins/validate/jquery.validate.min.js"></script>

<script>
  $(document).ready(function() {
    $('#tabla_noticias').DataTable({
      buttons: [ {extend: 'excel', title: 'Tipo_despachos', className: 'btnExportarExcel', exportOptions: { columns: [ 0, 1] } } ],
      dom: 'lr<"containerBtn"><"inputFiltro"f>tip<"clear"><"html5buttons"B>',
      order: [ 0, 'asc' ],
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
      pageLength: 25,
      responsive: true,
      search:{
        "search": "<?php if (isset($_GET['filtro'])) echo $_GET['filtro']; ?>"
      }
    });

    var botonAcciones = '<div class="dropdown pull-right">'+
                        '<button class="btn btn-primary btn-sm btn-outline" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true">'+
                          'Acciones <span class="caret"></span>'+
                        '</button>'+
                        '<ul class="dropdown-menu pull-right" aria-labelledby="dropdownMenu1">'+
                          '<li><a tabindex="0" aria-controls="tabla_noticias" href="#" onclick="$(\'.btnExportarExcel\').click();"><i class="fa fa-file-excel-o"></i> Exportar </a></li>'+
                        '</ul>'+
                      '</div>';
    $('.containerBtn').html(botonAcciones);

    // Configuración plugin validate.
    jQuery.extend(jQuery.validator.messages, { required: "Campo obligatorio.", remote: "Por favor, rellena este campo.", email: "Por favor, escribe una dirección de correo válida", url: "Por favor, escribe una URL válida.", date: "Por favor, escribe una fecha válida.", dateISO: "Por favor, escribe una fecha (ISO) válida.", number: "Por favor, escribe un número entero válido.", digits: "Por favor, escribe sólo dígitos.", creditcard: "Por favor, escribe un número de tarjeta válido.", equalTo: "Por favor, escribe el mismo valor de nuevo.", accept: "Por favor, escribe un valor con una extensión aceptada.", maxlength: jQuery.validator.format("Por favor, no escribas más de {0} caracteres."), minlength: jQuery.validator.format("Por favor, no escribas menos de {0} caracteres."), rangelength: jQuery.validator.format("Por favor, escribe un valor entre {0} y {1} caracteres."), range: jQuery.validator.format("Por favor, escribe un valor entre {0} y {1}."), max: jQuery.validator.format("Por favor, escribe un valor menor o igual a {0}."), min: jQuery.validator.format("Por favor, escribe un valor mayor o igual a {0}.") });


    $(document).on('click', '#guardar_noticia', function() { guardar_noticia(); });
    $(document).on('click', '.editar_noticia', function() { editar_noticia($(this).data('id_noticia')); });
    $(document).on('click', '#actualizar_noticia', function() { actualizar_noticia(); });
  });

  function guardar_noticia()
  {
    if($("#formulario_crear_noticia").valid()) {
      var formData = new FormData();
      formData.append('imagen', $('#imagen')[0].files[0]);
      formData.append('fecha', $("#fecha").val());
      formData.append('titulo', $("#titulo").val());
      formData.append('descripcion', $('#descripcion').val());

      $.ajax({
        type: "POST",
        url: "functions/fn_noticias_crear.php",
        contentType: false,
        processData: false,
        data: formData,
        dataType: 'json',
        beforeSend: function() { $('#loader').fadeIn(); },
        success: function(data) {
          if(data.estado == 1){
            Command: toastr.success(data.mensaje, "¡Correcto!", { onHidden : function(){ $('#loader').fadeOut(); location.reload(); } });
          } else {
            Command: toastr.error( data.mensaje, "¡Error!", { onHidden : function(){ $('#loader').fadeOut(); } });
          }
        },
        error: function(data){ console.log(data);
          Command: toastr.error("Al parecer existe un problema. Por favor comuníquese con el adminstrador del sitio InfoPAE.", "¡Error!", { onHidden : function(){ $('#loader').fadeOut(); } });
        }
      });
    }
  }

  function editar_noticia(id_noticia)
  {
    $.ajax({
      url: 'functions/fn_noticia_obtener.php',
      type: 'POST',
      dataType: 'JSON',
      data: {
        'id': id_noticia
      },
    })
    .done(function(data) {
      if (data.estado == 1) {
        $('#formulario_actualizar_noticia #id').val(data.datos.id);
        $('#formulario_actualizar_noticia #fecha').val(data.datos.fecha);
        $('#formulario_actualizar_noticia #titulo').val(data.datos.titulo);
        $('#formulario_actualizar_noticia #descripcion').val(data.datos.descripcion);
        $('#formulario_actualizar_noticia #imagen_previa').prop('src', data.datos.imagen);

        $('#ventana_editar_noticia').modal('show');
      } else {
        Command: toastr.error(data.mensaje, "¡Error!", { onHidden : function(){ $('#loader').fadeOut(); }});
      }
    })
    .fail(function(data) {
      Command: toastr.error("Al parecer existe un problema. Por favor comuníquese con el adminstrador del sitio InfoPAE.", "¡Error!", { onHidden : function(){ $('#loader').fadeOut(); }});
    });
  }

  function actualizar_noticia()
  {
    if ($('#formulario_actualizar_noticia').valid()) {
      var formData = new FormData();
      formData.append('id', $("#formulario_actualizar_noticia #id").val());
      formData.append('fecha', $("#formulario_actualizar_noticia #fecha").val());
      formData.append('titulo', $("#formulario_actualizar_noticia #titulo").val());
      formData.append('imagen', $('#formulario_actualizar_noticia #imagen')[0].files[0]);
      formData.append('descripcion', $('#formulario_actualizar_noticia #descripcion').val());

      $.ajax({
        type: "POST",
        url: "functions/fn_noticias_actualizar.php",
        contentType: false,
        processData: false,
        data: formData,
        dataType: 'json',
        beforeSend: function() { $('#loader').fadeIn(); },
        success: function(data) {
          if(data.estado == 1) {
            $('#ventana_editar_noticia').modal('hide');

            Command: toastr.success(data.mensaje, "¡Correcto!", {onHidden : function(){ $('#loader').fadeOut(); location.reload(); }});
          } else {
            Command: toastr.error( data.mensaje, "¡Error!", { onHidden : function(){ $('#loader').fadeOut(); } });
          }
        },
        error: function(data){
          Command: toastr.error("Al parecer existe un problema. Por favor comuníquese con el adminstrador del sitio InfoPAE.", "¡Error!", {onHidden : function(){ $('loader').fadeOut(); }});
        }
      });
    }
  }

</script>

<?php mysqli_close($Link); ?>

</body>
</html>