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

  $titulo = 'Empleados';

  $nameLabel = get_titles('configuracion', 'empleados', $labels);
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
      <a href="#" class="btn btn-primary" id="crearEmpleado"><i class="fa fa-plus"></i> Nuevo </a>
    </div>
  </div>
</div>

<!-- Table de empleados -->
<div class="wrapper wrapper-content animated fadeInRight">
  <div class="row">
    <div class="col-lg-12">
      <div class="ibox float-e-margins">
        <div class="ibox-content contentBackground">
          <table id="tablaEmpleados" class="table table-striped table-hover selectableRows">
            <thead>
              <tr>
                <th>N° Documento</th>
                <th>Nombres y apellidos</th>
                <th>Correo electrónico</th>
                <th>Municipio</th>
                <th>Tipo Empleado</th>
                <th>Cargo</th>
                <th>Estado</th>
                <th class="text-center">Acciones</th>
              </tr>
            </thead>
            <tbody>

            </tbody>
            <tfoot>
              <tr>
                <th>N° Documento</th>
                <th>Nombres y apellidos</th>
                <th>Correo electrónico</th>
                <th>Municipio</th>
                <th>Tipo Empleado</th>
                <th>Cargo</th>
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
        <button type="button" class="btn btn-white btn-sm" data-dismiss="modal">No</button>
        <button type="button" class="btn btn-primary btn-sm" id="eliminarEmpleado" data-dismiss="modal">Si</button>
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
<script src="<?php echo $baseUrl; ?>/modules/empleados/js/empleados.js"></script>
<script>


function estado_empleado(x){
  return x+"AA";
}

function estado_empleado(x){
  return x+"AA";
}

  $('#tablaEmpleados').DataTable({
    ajax: {
      method: 'post',
      url: 'functions/fn_empleados_listar.php'
    },
    columns: [
      {data: 'cedulaEmpleado'},
      {data: 'nombreEmpleado'},
      {data: 'emailEmpleado'},
      {data: 'ciudadEmpleado'},
      {data: 'tipoEmpleado', "mRender" : function ( data, type, full ) 
        {
          if (data == 1) {
            return 'Empleado(a)';
          } else if (data == 2) {
            return 'Manipulador(a)';
          } else if (data == 3) {
            return 'Contratista';
          } else if (data == 4) {
            return 'Transportador';
          } else  {
            return data;
          }
        }
    },
      {data: 'cargoEmpleado'},
      {data: 'estadoEmpleado', "mRender" : function ( data, type, full ) 
        {
          if (data == 1) {
            return 'Activo';
          } else  {
            return 'Inactivo';
          }
        }
      },
      {data: 'input', className: 'text-center'},
    ],
    fnRowCallback: function (nRow, aData, iDisplayIndex)
    {
        nRow.setAttribute('data-idempleado', aData['idEmpleado']);
        return nRow;
    },
    buttons: [ {extend: 'excel', title: 'Empleados', className: 'btnExportarExcel', exportOptions: { columns: [ 0, 1, 2, 3, 4, 5 ] } } ],
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


<form action="empleados_ver.php" method="post" name="formVerEmpleado" id="formVerEmpleado" target="_blank">
  <input type="hidden" name="codigoEmpleado" id="codigoEmpleado">
</form>

<form action="empleados_editar.php" method="post" name="formEditarEmpleado" id="formEditarEmpleado">
  <input type="hidden" name="idEmpleado" id="idEmpleado">
</form>

<?php mysqli_close($Link); ?>

</body>
</html>