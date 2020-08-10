
<?php
  include '../../header.php';
  $titulo = 'Proveedores';
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
      <a href="#" class="btn btn-primary" id="crearProveedor"><i class="fa fa-plus"></i> Nuevo </a>
    </div>
  </div>
</div>

<!-- Table de empleados -->
<div class="wrapper wrapper-content animated fadeInRight">
  <div class="row">
    <div class="col-lg-12">
      <div class="ibox float-e-margins">
        <div class="ibox-content contentBackground">
          <table id="tablaProveedores" class="table table-striped table-hover selectableRows">
            <thead>
              <tr>
                <th>Nit</th>
                <th>Nombre comercial</th>
                <th>Razón Social</th>
                <th>Correo electrónico</th>
                <th>Municipio</th>
                <th>Compras locales</th>
                <th>Estado</th>
                <th class="text-center">Acciones</th>
              </tr>
            </thead>
            <tbody>

            </tbody>
            <tfoot>
              <tr>
                <th>Nit</th>
                <th>Nombre comercial</th>
                <th>Razón Social</th>
                <th>Correo electrónico</th>
                <th>Municipio</th>
                <th>Compras locales</th>
                <th>Estado</th>
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
        <input type="hidden" id="nombreAEliminar">
        <button type="button" class="btn btn-white btn-sm" data-dismiss="modal">No</button>
        <button type="button" class="btn btn-primary btn-sm" id="eliminarProveedor" data-dismiss="modal">Si</button>
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

<script src="<?php echo $baseUrl; ?>/theme/js/plugins/dataTables/datatables.min.js"></script>

<!-- Custom and plugin javascript -->
<script src="<?php echo $baseUrl; ?>/theme/js/inspinia.js"></script>
<script src="<?php echo $baseUrl; ?>/theme/js/plugins/pace/pace.min.js"></script>
<script src="<?php echo $baseUrl; ?>/theme/js/plugins/iCheck/icheck.min.js"></script>
<script src="<?php echo $baseUrl; ?>/theme/js/plugins/toastr/toastr.min.js"></script>
<script src="<?php echo $baseUrl; ?>/theme/js/plugins/select2/select2.full.min.js"></script>

<!-- Section Scripts -->
<script src="<?php echo $baseUrl; ?>/modules/proveedores/js/proveedores.js"></script>
<script>
  $('#tablaProveedores').DataTable({
    ajax: {
      method: 'post',
      url: 'functions/fn_proveedores_listar.php'
    },
    columns: [
      {data: 'nitProveedor'},
      {data: 'nombreComercialProveedor'},
      {data: 'razonsocialProveedor'},
      {data: 'emailProveedor'},
      {data: 'municipio'},
      {data: 'comprasLocales', className: 'text-center'},
      {data: 'estado'},
      {data: 'input', className: 'text-center'},
    ],
    buttons: [ {extend: 'excel', title: 'Proveedores', className: 'btnExportarExcel', exportOptions: { columns: [ 0, 1, 2, 3 ] } } ],
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
    rowCallback: function(row, data) {
      row.id = data.idProveedor;
      row.className = "editar_proveedor";
    },
    preDrawCallback: function() {
      $('#loader').fadeIn();
    }
  }).on('draw', function () { $('#loader').fadeOut(); });

  var botonAcciones = '<div class="dropdown pull-right">'+
                      '<button class="btn btn-primary btn-sm btn-outline" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true">'+
                        'Acciones <span class="caret"></span>'+
                      '</button>'+
                      '<ul class="dropdown-menu pull-right" aria-labelledby="dropdownMenu1">'+
                        '<li><a tabindex="0" aria-controls="box-table" href="#" onclick="$(\'.btnExportarExcel\').click();"><i class="fa fa-file-excel-o"></i> Exportar </a></li>'+
                        '<ul>'+
                      '</ul>'+
                    '</div>';
  $('.containerBtn').html(botonAcciones);
</script>


<form action="proveedores_ver.php" method="post" name="formVerProveedor" id="formVerProveedor">
  <input type="hidden" name="codigoProveedor" id="codigoProveedor">
</form>

<form action="proveedores_editar.php" method="post" name="formEditarProveedor" id="formEditarProveedor">
  <input type="hidden" name="idProveedor" id="idProveedor">
  <input type="hidden" name="razonSocialProveedor" id="razonSocialProveedor">
</form>

<?php mysqli_close($Link); ?>

</body>
</html>