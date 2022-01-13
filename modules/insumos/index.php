<?php
$titulo = 'Insumos';
require_once '../../header.php';

if ($permisos['configuracion'] == "0" || $permisos['configuracion'] == "1") {
  ?><script type="text/javascript">
    window.open('<?= $baseUrl ?>', '_self');
  </script>
<?php exit(); }

$periodoActual = $_SESSION['periodoActual'];

$tiposInsumos = [];

$tipoInsumo = "SELECT Codigo, Descripcion FROM productos".$_SESSION['periodoActual']." WHERE Codigo LIKE '05%' AND Nivel = '2'";
$resultadoTipoInsumo = $Link->query($tipoInsumo);
if ($resultadoTipoInsumo->num_rows > 0) {
  while ($tipoInsumo = $resultadoTipoInsumo->fetch_assoc()) {
    $tiposInsumos[$tipoInsumo['Codigo']] = $tipoInsumo['Descripcion'];
  }
}
// var_dump($tipoInsumo);
?>

<style type="text/css">

</style>
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
    <?php if ($_SESSION['perfil'] == "0" || $permisos['configuracion'] == "2"): ?>
      <div class="title-action">
        <button class="btn btn-primary" onclick="window.location.href = 'nuevo_insumo.php';"><span class="fa fa-plus"></span>  Nuevo</button>
      </div>
    <?php endif ?>
  </div><!-- /.col -->
</div><!-- /.row -->

<div class="wrapper wrapper-content animated fadeInRight">
  <div class="row">
    <div class="col-lg-12">
      <div class="ibox float-e-margins">
        <div class="ibox-content contentBackground">

          <?php
          $insumos = [];
          $consulta = "SELECT * FROM productos".$_SESSION['periodoActual']." WHERE Codigo LIKE '05%' AND Nivel = 3";
          $resultado = $Link->query($consulta);
          if ($resultado->num_rows > 0) {
            while ($insumo = $resultado->fetch_assoc()) {
              $insumos[] = $insumo;
            }
          }
          // var_dump($insumos);

           ?>

          <table class="table table-hover selectableRows" id="tablaInsumos">
            <thead>
              <tr>
                <th>Código</th>
                <th>Tipo de conteo</th>
                <th>Descripción</th>
                <th>Unidad de medida</th>
                <th>Cantidad</th>
                <th>Acciones</th>
              </tr>
            </thead>
            <tbody id="tbodyInsumos">

              <?php foreach ($insumos as $ins): ?>
                <tr codigoinsumo="<?php echo $ins['Codigo']; ?>">
                  <td><?php echo $ins['Codigo']; ?></td>
                  <td><?php echo strtoupper($tiposInsumos[substr($ins['Codigo'], 0, 4)]); ?></td>
                  <td><?php echo $ins['Descripcion']; ?></td>
                  <td><?php echo $ins['NombreUnidad1']; ?></td>
                  <td><?php echo $ins['CantidadUnd1']; ?></td>
                  <td>
                    <div class="btn-group">
                      <div class="dropdown">
                        <button class="btn btn-primary btn-sm" type="button" id="accionesProducto" data-toggle="dropdown" aria-haspopup="true">
                          Acciones
                          <span class="caret"></span>
                        </button>
                        <ul class="dropdown-menu pull-right" aria-labelledby="accionesProducto">
                          <?php if ($_SESSION['perfil'] == 1 || $_SESSION['perfil'] == 0): ?>
                          <li><a onclick="editarInsumo('<?php echo $ins['Codigo']; ?>')"><span class="fas fa-pencil-alt"></span>  Editar</a></li>
                            <li>
                              <a data-toggle="modal" data-target="#modalEliminar"  data-codigo="<?php echo $ins['Codigo']; ?>"><span class="fa fa-trash"></span>  Eliminar</a>
                            </li>
                          <?php endif ?>
                           <li>
                            <a><span class="fa fa-file-excel-o"></span> Exportar</a>
                           </li>
                        </ul>
                      </div>
                    </div>
                  </td>
                </tr>
              <?php endforeach ?>

            </tbody>
            <tfoot>
              <tr>
                <th>Código</th>
                <th>Tipo de conteo</th>
                <th>Descripción</th>
                <th>Unidad de medida</th>
                <th>Cantidad</th>
                <th>Acciones</th>
              </tr>
            </tfoot>
          </table>


        </div><!-- /.ibox-content -->
      </div><!-- /.ibox float-e-margins -->
    </div><!-- /.col-lg-12 -->
  </div><!-- /.row -->
</div><!-- /.wrapper wrapper-content animated fadeInRight -->
<form method="POST" id="ver_insumo" action="ver_insumo.php" style="display: none;">
  <input type="hidden" name="codigoinsumover" id="codigoinsumover">
</form>

<form method="POST" id="editar_insumo" action="editar_insumo.php" style="display: none;">
  <input type="hidden" name="codigoinsumoeditar" id="codigoinsumoeditar">
</form>
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

<!-- Section Scripts -->
<script src="<?php echo $baseUrl; ?>/modules/insumos/js/insumos.js"></script>

<script type="text/javascript">
  console.log('Aplicando Data Table');
  dataset1 = $('#tablaInsumos').DataTable({
    /*order: [ 0, 'asc' ],*/
    pageLength: 25,
    responsive: true,
    dom : '<"html5buttons" B>lr<"containerBtn"><"inputFiltro"f>tip',
    buttons : [{extend:'excel', title:'Menus', className:'btnExportarExcel', exportOptions: {columns : [0,1,2,3,4,5,6]}}],
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
    }
    });
  var btnAcciones = '<div class="dropdown pull-right" id=""><button class="btn btn-primary btn-sm btn-outline" type="button" id="accionesTabla" data-toggle="dropdown" aria-haspopup="true">Acciones<span class="caret"></span></button><ul class="dropdown-menu pull-right" aria-labelledby="accionesTabla"><li><a onclick="$(\'.btnExportarExcel\').click()"><span class="fa fa-file-excel-o"></span> Exportar </a></li></ul></div>';

  $('.containerBtn').html(btnAcciones);
</script>

<?php mysqli_close($Link); ?>

</body>
</html>