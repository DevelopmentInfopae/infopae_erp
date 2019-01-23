<?php
    include '../../header.php';
    set_time_limit (0);
    ini_set('memory_limit','6000M');
    $periodoActual = $_SESSION['periodoActual'];
    $titulo = "Rutas";
?>

<div class="row wrapper wrapper-content border-bottom white-bg page-heading">
  <div class="col-lg-8">
      <h2><?= $titulo; ?></h2>
      <ol class="breadcrumb">
          <li>
              <a href="<?php echo $baseUrl; ?>">Home</a>
          </li>
          <li class="active">
              <strong><?php echo $titulo; ?></strong>
          </li>
      </ol>
  </div>
  <?php if($_SESSION["perfil"] == 1 || $_SESSION["perfil"] == 0) { ?>
  <div class="col-lg-4">
      <div class="title-action">
          <a href="#" class="btn btn-primary" onclick="crearRuta();"><i class="fa fa-plus"></i> Nueva</a>
      </div>
  </div>
  <?php } ?>
</div>

<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row">
        <div class="col-lg-12">
            <div class="ibox">
                <div class="ibox-content">
                    <div class="table-responsive">
                        <?php
                            $consulta = " select * from rutas ";
                            $resultado = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));
                        ?>
                        <table class="table table-striped table-hover selectableRows dataTablesRutas" >
                            <thead>
                                <tr>
                                    <th class="center">Id</th>
                                    <th class="center">Ruta</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if($resultado->num_rows >= 1){ while($row = $resultado->fetch_assoc()) {
                                  $id = '';
                                  $nombre = '';
                                  $id = $row['ID'];
                                  $nombre = $row['Nombre'];
                                ?>
                                  <tr>
                                    <td class="center" onclick="editarRuta('<?php echo $id; ?>');" ><?php echo $id; ?></td>
                                    <td onclick="editarRuta('<?php echo $id; ?>');" ><?php echo $nombre; ?></td>
                                  </tr>
                                <?php } } ?><!-- Cierres del if y el while de resultados para las filas de la tabla. -->
                            </tbody>
                            <tfoot>
                            <tr>
                                <th class="center">Id</th>
                                <th class="center">Ruta</th>
                            </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
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
<script src="<?php echo $baseUrl; ?>/modules/rutas/js/rutas.js"></script>

<!-- Page-Level Scripts -->
<script>
$(document).ready(function() {
    $('.dataTablesRutas').DataTable({
        pageLength: 25,
        responsive: true,
        dom: 'lr<"containerBtn"><"inputFiltro"f>tip<"clear"><"html5buttons"B>',
        "order": [[ 1, "asc" ]],
        "oLanguage": {
            "sLengthMenu": "Mostrando _MENU_ registros por página",
            "sZeroRecords": "No se encontraron registros",
            "sInfo": "Mostrando _START_ a _END_ de _TOTAL_ registros",
            "sInfoEmpty": "Mostrando 0 a 0 de 0 registros",
            "sInfoFiltered": "(Filtrado desde _MAX_ registros)",
            "sSearch":         "Buscar: ",
            "oPaginate": {
                "sFirst":    "Primero",
                "sLast":     "Último",
                "sNext":     "Siguiente",
                "sPrevious": "Anterior"
            }
        },
        buttons: [ {extend: 'excel', title: 'Rutas', className: "btnExportarExcel"} ]
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
});
</script>

</body>
</html>
