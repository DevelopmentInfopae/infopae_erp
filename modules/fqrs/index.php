<?php
  include '../../header.php';

  if ($permisos['fqrs'] == "0") {
    ?><script type="text/javascript">
      window.open('<?= $baseUrl ?>', '_self');
    </script>
  <?php exit(); }

  $titulo = 'Fqrs';
?>

<div class="row wrapper wrapper-content border-bottom white-bg page-heading">
  <div class="col-lg-8">
    <h2><?= $titulo; ?></h2>
    <ol class="breadcrumb">
      <li>
        <a href="<?= $baseUrl; ?>">Home</a>
      </li>
      <li class="active">
        <strong><?= $titulo; ?></strong>
      </li>
    </ol>
  </div>
  <div class="col-lg-4">
  </div>
</div>

<!-- Table de fqrs -->
<div class="wrapper wrapper-content animated fadeInRight">
  <div class="row">
    <div class="col-lg-12">
      <div class="ibox float-e-margins">
        <div class="ibox-content contentBackground">
          <table id="tabla_fqrs" class="table table-striped table-hover selectableRows">
            <thead>
              <tr>
                <th>Municipio</th>
                <th>Sede</th>
                <th>Tipo de caso</th>
                <th>Tipo de persona</th>
                <th>Nombre</th>
                <th>Número documento</th>
                <th>Estado</th>
                <th>Fecha creación</th>
                <?php if ($_SESSION['perfil'] == "0" || $permisos['fqrs'] == "2"): ?>
                  <th class="text-center">Acciones</th>
                <?php endif ?>   
              </tr>
            </thead>
            <tbody>

            </tbody>
            <tfoot>
              <tr>
                <th>Municipio</th>
                <th>Sede</th>
                <th>Tipo de caso</th>
                <th>Tipo de persona</th>
                <th>Nombre</th>
                <th>Número documento</th>
                <th>Estado</th>
                <th>Fecha creación</th>
                <?php if ($_SESSION['perfil'] == "0" || $permisos['fqrs'] == "2"): ?>
                  <th class="text-center">Acciones</th>
                <?php endif ?> 
              </tr>
            </tfoot>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>


<?php include '../../footer.php'; ?>

<!-- Mainly scripts -->
<script src="<?= $baseUrl; ?>/theme/js/jquery-3.1.1.min.js"></script>
<script src="<?= $baseUrl; ?>/theme/js/bootstrap.min.js"></script>
<script src="<?= $baseUrl; ?>/theme/js/plugins/metisMenu/jquery.metisMenu.js"></script>
<script src="<?= $baseUrl; ?>/theme/js/plugins/slimscroll/jquery.slimscroll.min.js"></script>

<script src="<?= $baseUrl; ?>/theme/js/plugins/dataTables/datatables.min.js"></script>

<!-- Custom and plugin javascript -->
<script src="<?= $baseUrl; ?>/theme/js/inspinia.js"></script>
<script src="<?= $baseUrl; ?>/theme/js/plugins/pace/pace.min.js"></script>
<script src="<?= $baseUrl; ?>/theme/js/plugins/iCheck/icheck.min.js"></script>
<script src="<?= $baseUrl; ?>/theme/js/plugins/toastr/toastr.min.js"></script>

<!-- Section Scripts -->
<script src="<?= $baseUrl; ?>/modules/fqrs/js/fqrs.js"></script>
<script>
  $('#tabla_fqrs').DataTable({
    ajax: {
      method: 'post',
      url: 'functions/fn_fqrs_listar.php'
    },
    columns: [
      {data: 'municipio'},
      {data: 'nombre_sede'},
      {data: 'tipo_caso'},
      {data: 'tipo_persona'},
      {data: 'nombre_persona'},
      {data: 'numero_documento'},
      {data: 'estado'},
      {data: 'fecha_creacion'},
      <?php if ($_SESSION['perfil'] == "0" || $permisos['fqrs'] == "2"): ?>
        {data: 'input', className: 'text-center'},
      <?php endif ?>
    ],
    buttons: [ {extend: 'excel', title: '<?= $titulo; ?>', className: 'btnExportarExcel', exportOptions: { columns: [ 0, 1, 2, 3, 4, 5, 6, 7 ] } } ],
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
      row.id = data.id_fqrs;
    },
    preDrawCallback: function() {
      $('#loader').fadeIn();
    },
    drawCallback: function() {
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
    }
  }).on('draw', function () { $('#loader').fadeOut(); });
</script>


<form action="fqrs_ver.php" method="post" name="formulario_ver_fqrs" id="formulario_ver_fqrs">
  <input type="hidden" name="id_fqrs" id="id_fqrs">
</form>

<?php mysqli_close($Link); ?>

</body>
</html>