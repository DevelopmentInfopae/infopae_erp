<?php
  include '../../header.php';
  $titulo = 'Grupo etarios';
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
      <a href="#" class="btn btn-primary" id="crearGrupoEtario"><i class="fa fa-plus"></i> Nuevo </a>
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
                <th>Descripción</th>
                <th>Edad Inicial</th>
                <th>Edad Final</th>
                <th class="text-center">Acciones</th>
              </tr>
            </thead>
            <tbody>
              <?php
                $consulta = "SELECT * FROM grupo_etario";
                $resultado = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));
                if($resultado){
                  while($registros = $resultado->fetch_assoc()){
              ?>
              <tr>
                <td align="left"><?php echo $registros['DESCRIPCION']; ?></td>
                <td align="left"><?php echo $registros['EDADINICIAL']; ?></td>
                <td align="left"><?php echo $registros['EDADFINAL']; ?></td>
                <td align="center">
                  <div class="btn-group">
                    <div class="dropdown">
                      <button class="btn btn-primary btn-sm" id="dLabel" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        Acciones <span class="caret"></span>
                      </button>
                      <ul class="dropdown-menu pull-right" aria-labelledby="dLabel">
                        <li><a href="#" class="editarGrupoEtario" data-codigogrupoetario="<?php echo $registros['ID']; ?>"><i class="fa fa-pencil fa-lg"></i> Editar</a></li>
                        <li><a href="#" class="confirmarGrupoEtario" data-codigogrupoetario="<?php echo $registros['ID']; ?>"><i class="fa fa-trash fa-lg"></i> Eliminar</a></li>
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
                <th>Descripción</th>
                <th>Edad Inicial</th>
                <th>Edad Final</th>
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
        <button type="button" class="btn btn-primary btn-sm" data-dismiss="modal" id="eliminarGrupoEtario">Si</button>
      </div>
    </div>
  </div>
</div>

<form action="grupos_etarios_editar.php" method="post" name="formEditarGrupoEtario" id="formEditarGrupoEtario">
  <input type="hidden" name="codigoGrupoEtario" id="codigoGrupoEtario">
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

<!-- Section Scripts -->
<script src="<?php echo $baseUrl; ?>/modules/grupos_etarios/js/grupos_etarios.js"></script>
<script>
  console.log('Aplicando Data Table');
  $('#box-table').DataTable({
    buttons: [ {extend: 'excel', title: 'Grupo_etarios', className: 'btnExportarExcel', exportOptions: { columns: [ 0, 1, 2] } } ],
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