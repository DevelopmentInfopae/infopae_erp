<?php
  include '../../header.php';
  $titulo = 'Tipo complementos';
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
  </div><!-- /.col -->
  <div class="col-lg-4">
    <div class="title-action">
      <a href="#" class="btn btn-primary" id="crearTipoComplemento"><i class="fa fa-plus"></i> Nuevo </a>
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
                <th>Descripción</th>
                <th>Jornada</th>
                <th class="text-center">Valor ración</th>
                <th class="text-center">Acciones</th>
              </tr>
            </thead>
            <tbody>
            <?php
            $consulta = "SELECT tipc.CODIGO AS codigoTipoComplemento, tipc.ID AS idTipopComplento, tipc.DESCRIPCION AS descripcionTipoComplemento, jor.nombre AS jornadaTipoComplemento, tipc.ValorRacion AS valorRacionTipoComplemento FROM tipo_complemento tipc INNER JOIN jornada jor ON jor.id = tipc.jornada;";
            $resultado = $Link->query($consulta) or die('Error al consultar los tipos de complementos: '. mysqli_error($Link));
            if($resultado->num_rows)
            {
              while ($registros = $resultado->fetch_assoc())
              {
            ?>
              <tr>
                <td><?php echo $registros['codigoTipoComplemento']; ?></td>
                <td><?php echo $registros['descripcionTipoComplemento']; ?></td>
                <td><?php echo $registros['jornadaTipoComplemento']; ?></td>
                <td class="text-right">$ <?php echo $registros['valorRacionTipoComplemento']; ?></td>
                <td>
                  <div class="btn-group">
                    <div class="dropdown">
                      <button class="btn btn-primary btn-sm" id="dLabel" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        Acciones <span class="caret"></span>
                      </button>
                      <ul class="dropdown-menu pull-right" aria-labelledby="dLabel">
                        <li><a href="#" class="editarTipoComplemento" data-idtipocomplemento="<?php echo $registros['idTipopComplento']; ?>"><i class="fa fa-pencil fa-lg"></i> Editar</a></li>
                        <li><a href="#" class="confirmarEliminarTipoComplemento" data-idtipocomplemento="<?php echo $registros['idTipopComplento']; ?>"><i class="fa fa-trash fa-lg"></i> Eliminar</a></li>
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
                <th>Código</th>
                <th>Descripción</th>
                <th>Jornada</th>
                <th class="text-center col-sm-2">Valor ración</th>
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
        <button type="button" class="btn btn-primary btn-sm" data-dismiss="modal" id="eliminarTipoComplemento">Si</button>
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
<script src="<?php echo $baseUrl; ?>/modules/tipo_complementos/js/tipo_complementos.js"></script>

<!-- Section Scripts -->
<script>
  $('#box-table').DataTable({
    buttons: [ {extend: 'excel', title: 'Tipo_complemento', className: 'btnExportarExcel', exportOptions: { columns: [0, 1, 2, 3] } } ],
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
    responsive: true
  });

  var botonAcciones = '<div class="dropdown pull-right">'+
                      '<button class="btn btn-primary btn-sm btn-outline" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true">'+
                        'Acciones <span class="caret"></span>'+
                      '</button>'+
                      '<ul class="dropdown-menu pull-right" aria-labelledby="dropdownMenu1">'+
                        '<li><a aria-controls="box-table" href="#" onclick="$(\'.btnExportarExcel\').click();"><i class="fa fa-file-excel-o"></i> Exportar </a></li>'+
                        '<ul>'+
                      '</ul>'+
                    '</div>';
  $('.containerBtn').html(botonAcciones);
</script>

<form action="tipo_complementos_editar.php" method="post" name="formEditarTipoComplemento" id="formEditarTipoComplemento">
  <input type="hidden" name="idTipoComplemento" id="idTipoComplemento">
</form>

<?php mysqli_close($Link); ?>

</body>
</html>