<?php 
$titulo = 'Movimientos';
include '../../header.php'; 
set_time_limit (0);
ini_set('memory_limit','6000M');
$periodoActual = $_SESSION['periodoActual'];
require_once '../../db/conexion.php';
$Link = new mysqli($Hostname, $Username, $Password, $Database);
if ($Link->connect_errno) {
    echo "Fallo al contenctar a MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
}
$Link->set_charset("utf8");
?>

<div class="row wrapper wrapper-content border-bottom white-bg page-heading">
  <div class="col-lg-8">
    <h2>Menús</h2>
    <ol class="breadcrumb">
      <li>
        <a href="<?php echo $baseUrl; ?>">Home</a>
      </li>
      <li class="active">
        <strong>Menús</strong>
      </li>
    </ol>
  </div><!-- /.col -->
  <div class="col-lg-4">
    <div class="title-action">
      <!--
      <a href="#" class="btn btn-white"><i class="fa fa-pencil"></i> Edit </a>
      <a href="#" class="btn btn-white"><i class="fa fa-check "></i> Save </a>
      <a href="<?php echo $baseUrl; ?>/modules/movimientos/movimiento_nuevo.php" target="_self" class="btn btn-primary"><i class="fa fa-arrow-circle-right" aria-hidden="true"></i> Nuevo Movimiento </a>
      -->
    </div><!-- /.title-action -->
  </div><!-- /.col -->
</div><!-- /.row -->


<div class="wrapper wrapper-content animated fadeInRight">
  <div class="row">
    <div class="col-lg-12">
      <div class="ibox float-e-margins">
        <div class="ibox-content contentBackground">

          <div class="row">
            <div class="col-xs-12">
              <div class="table-responsive">


                <?php 
                $vsql="select id, codigo, descripcion from productos$periodoActual where TipodeProducto = 'Menú'";
                $result = $Link->query($vsql) or die ('Unable to execute query. '. mysqli_error($Link)); 
                ?>

                <table class="table table-striped table-bordered table-hover selectableRows" id="box-table" >
                  <thead>
                    <tr>
                      <th>Código</th>
                      <th>Descripción</th>
                    </tr>
                  </thead>
                  <tbody>

                    <?php while($row = $result->fetch_assoc()){ ?>
                       <tr onclick="menuanalisis('<?php echo $row['id']; ?>','<?php echo $row['codigo']; ?>','<?php echo $row['descripcion']; ?>');">
                          <td align="center"><?php echo $row['codigo']; ?></td>
                          <td><?php echo $row['descripcion']; ?></td>
                       </tr>
                    <?php } ?>

                  </tbody>
                  <tfoot>
                    <tr>
                      <th>Código</th>
                      <th>Descripción</th>
                    </tr>
                  </tfoot> 
                </table>
              </div><!-- /.table-responsive -->
            </div><!-- /.col -->
          </div><!--- /.row -->

        </div><!-- /.ibox-content -->
      </div><!-- /.ibox float-e-margins -->
    </div><!-- /.col-lg-12 -->
  </div><!-- /.row -->
</div><!-- /.wrapper wrapper-content animated fadeInRight -->





<form action="menus_analisis.php" name="analisis" id="analisis" method="post">
  <input type="hidden" name="id" id="id">
  <input type="hidden" name="codigo" id="codigo">
  <input type="hidden" name="descripcion" id="descripcion">
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

<!-- Scripts sección del modulo -->

<!-- Page-Level Scripts -->

<script>
  function menuanalisis(id,codigo,descripcion){
    console.log('Analisis del menú'+id+' '+codigo+' '+descripcion);
    $('#id').val(id);
    $('#codigo').val(codigo);
    $('#descripcion').val(descripcion);
    $('#analisis').submit();
  }
</script>

<script>
  console.log('Aplicando Data Table');
  dataset1 = $('#box-table').DataTable({
    order: [ 0, 'asc' ],
    pageLength: 25,
    responsive: true,
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
</script>


<?php mysqli_close($Link); ?>

</body>
</html>