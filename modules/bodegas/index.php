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
          const list2 = document.querySelector(".li_bodegas");
          list2.className += " active ";
        </script>
        <?php
        }

  $titulo = 'Bodegas';

  $nameLabel = get_titles('configuracion', 'bodegas', $labels);
  $titulo = $nameLabel;
?>

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
    <div class="title-action">
      <a href="#" id="crearBodega" class="btn btn-primary"><i class="fa fa-plus"></i> Nuevo </a>
    </div><!-- /.title-action -->
  </div><!-- /.col -->
</div><!-- /.row -->

<!-- Tabla bodegas -->
<div class="wrapper wrapper-content animated fadeInRight">
  <div class="row">
    <div class="col-lg-12">
      <div class="ibox float-e-margins">
        <div class="ibox-content contentBackground">
          <table id="box-table" class="table table-striped table-hover selectableRows">
            <thead>
              <tr>
                <th>Código</th>
                <th>Nombres</th>
                <th>Ciudad</th>
                <th>Responsable</th>
                <th class="text-center">Acciones</th>
              </tr>
            </thead>
            <tbody>

            </tbody>
            <tfoot>
              <tr>
                <th>Código</th>
                <th>Nombres</th>
                <th>Ciudad</th>
                <th>Responsable</th>
                <th class="text-center">Acciones</th>
              </tr>
            </tfoot>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

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
        <button type="button" class="btn btn-primary btn-sm" data-dismiss="modal" id="eliminarBodega">Si</button>
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
<script src="<?php echo $baseUrl; ?>/theme/js/plugins/dataTables/datatables.min.js"></script>
<script src="<?php echo $baseUrl; ?>/theme/js/plugins/toastr/toastr.min.js"></script>
<script src="<?php echo $baseUrl; ?>/modules/bodegas/js/bodegas.js"></script>

<!-- Section Scripts -->
<script>
  $('#box-table').DataTable({
    ajax: {
      method: 'POST',
      url: 'functions/fn_bodegas_listar.php',
    },
    columns:[
      { data: 'codigoBodega'},
      { data: 'nombreBodega'},
      { data: 'ciudadBodega'},
      { data: 'responsableBodega'},
      { data: 'input', className: 'text-center'}
    ],
    buttons: [ {extend: 'excel', title: 'Bodegas', className: 'btnExportarExcel', exportOptions: { columns: [ 0, 1, 2, 3, 4 ] } } ],
    dom: 'lr<"containerBtn"><"inputFiltro"f>tip<"clear"><"html5buttons"B>',
    order: [ 1, 'asc' ],
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
    "preDrawCallback": function( settings ) {
      $('#loader').fadeIn();
    }
  }).on('draw', function() { $('#loader').fadeOut(); });

  var botonAcciones = '<div class="dropdown pull-right">'+
                      '<button class="btn btn-primary btn-sm btn-outline" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true">'+
                        'Acciones <span class="caret"></span>'+
                      '</button>'+
                      '<ul class="dropdown-menu pull-right" aria-labelledby="dropdownMenu1">'+
                        '<li><a aria-controls="box-table" href="#" onclick="$(\'.btnExportarExcel\').click();"><i class="fa fa-file-pdf-o"></i> Exportar </a></li>'+
                        '<li><a aria-controls="box-table" href="#" id="generarBodegas"><i class="fa fa-industry"></i> Generar Bodegas <strong>(Sedes)</strong></a></li>'+
                        '<li><a aria-controls="box-table" href="#" id="asignarUsuariosBodegas"><i class="fa fa-male fa-lg"></i> Asignar Bodegas a usuarios</a></li>'+
                        '<ul>'+
                      '</ul>'+
                    '</div>';
  $('.containerBtn').html(botonAcciones);
</script>


<form action="usuarios_ver.php" method="post" name="formVerUsuario" id="formVerUsuario">
  <input type="hidden" name="codigoUsuario" id="codigoUsuario">
</form>

<form action="bodega_editar.php" method="post" name="formEditarBodega" id="formEditarBodega">
  <input type="hidden" name="codigoBodega" id="codigoBodega">
</form>

<?php mysqli_close($Link); ?>

</body>
</html>