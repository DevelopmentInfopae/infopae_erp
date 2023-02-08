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

  $titulo = 'Tipo Vehículos';

  $nameLabel = get_titles('configuracion', 'tipoVehiculos', $labels);
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
      <a href="#" class="btn btn-primary" id="guardarTipoVehiculo"><i class="fa fa-check"></i> Guardar </a>
    </div>
  </div>
</div>

<!-- Tabla de usuarios -->
<div class="wrapper wrapper-content animated fadeInRight">
  <div class="row">
    <div class="col-lg-12">
      <div class="ibox float-e-margins">
        <div class="ibox-content contentBackground">
          <form id="formCrearTipoVehiculos">
            <div class="row">
              <div class="col-sm-12">

                <div class="form-group col-sm-6 col-md-4">
                  <label for="nombre">Nombre</label>
                  <input type="text" class="form-control" name="nombre" id="nombre" maxlength="20" required>
                </div>

              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Tabla de usuarios -->
<div class="wrapper wrapper-content animated fadeInRight">
  <div class="row">
    <div class="col-lg-12">
      <div class="ibox float-e-margins">
        <div class="ibox-content contentBackground">
          <table id="box-table" class="table table-striped table-hover selectableRows">
            <thead>
              <tr>
                <th>Identificador</th>
                <th>Descripción</th>
                <th class="text-center">Acciones</th>
              </tr>
            </thead>
            <tbody>
              <?php
                $consulta = "SELECT * FROM tipovehiculo";
                $resultado = $Link->query($consulta) or die ('Error al consultar tipo de vehículos: '. mysqli_error($Link));
                if($resultado){
                  while($registros = $resultado->fetch_assoc()){
              ?>
              <tr>
                <td align="left"><?php echo $registros['Id']; ?></td>
                <td align="left"><?php echo $registros['Nombre']; ?></td>
                <td align="center">
                  <div class="btn-group">
                    <div class="dropdown">
                      <button class="btn btn-primary btn-sm" id="dLabel" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        Acciones <span class="caret"></span>
                      </button>
                      <ul class="dropdown-menu pull-right" aria-labelledby="dLabel">
                        <li><a href="#" class="editarTipoVehiculo" data-codigotipovehiculo="<?php echo $registros['Id']; ?>" data-nombretipovehiculo="<?php echo $registros['Nombre']; ?>"><i class="fa fa-pencil fa-lg"></i> Editar</a></li>
                        <li><a href="#" class="confirmarEliminarTipoVehiculo" data-codigotipovehiculo="<?php echo $registros['Id']; ?>"><i class="fa fa-trash fa-lg"></i> Eliminar</a></li>
                      </ul>
                    </div>
                  </div>
                </td>
              </tr>
              <?php
                  }
                }
              ?>
            </tbody>
            <tfoot>
              <tr>
                <th>Idetificador</th>
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
        <button type="button" class="btn btn-primary btn-sm" data-dismiss="modal" id="eliminarTipoVehiculo">Si</button>
      </div>
    </div>
  </div>
</div>

<!-- Ventana de formulario -->
<div class="modal inmodal fade" id="ventanaFormulario" tabindex="-1" role="dialog" style="display: none;" aria-hidden="true">
  <div class="modal-dialog modal-sm">
    <div class="modal-content">
      <div class="modal-header text-info" style="padding: 15px;">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only">Close</span></button>
        <h3><i class="fa fa-question-circle fa-lg" aria-hidden="true"></i> Formulario InfoPAE </h3>
      </div>
      <div class="modal-body">
          <p></p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-white btn-sm" data-dismiss="modal">No</button>
        <button type="button" class="btn btn-primary btn-sm" data-dismiss="modal" id="actualizarTipoVehiculo">Si</button>
      </div>
    </div>
  </div>
</div>

<form action="tipo_vehiculos_editar.php" method="post" name="formEditarTipoVehiculos" id="formEditarTipoVehiculos">
  <input type="hidden" name="codigoTipoVehiculo" id="codigoTipoVehiculo">
</form>

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
<script src="<?php echo $baseUrl; ?>/theme/js/plugins/validate/jquery.validate.min.js"></script>

<!-- Section Scripts -->
<script src="<?php echo $baseUrl; ?>/modules/tipo_vehiculos/js/tipo_vehiculos.js"></script>
<script>
  $('#box-table').DataTable({
    buttons: [ {extend: 'excel', title: 'Tipo_vehículos', className: 'btnExportarExcel', exportOptions: { columns: [ 0, 1] } } ],
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
    search:{
      "search": "<?php if (isset($_GET['filtro'])) echo $_GET['filtro']; ?>"
    }
  });

  var botonAcciones = '<div class="dropdown pull-right">'+
                      '<button class="btn btn-primary btn-sm btn-outline" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true">'+
                        'Acciones <span class="caret"></span>'+
                      '</button>'+
                      '<ul class="dropdown-menu pull-right" aria-labelledby="dropdownMenu1">'+
                        '<li><a tabindex="0" aria-controls="box-table" href="#" onclick="$(\'.btnExportarExcel\').click();"><i class="fa fa-file-excel-o"></i> Exportar </a></li>'+
                      '</ul>'+
                    '</div>';
  $('.containerBtn').html(botonAcciones);
</script>

<?php mysqli_close($Link); ?>

</body>
</html>