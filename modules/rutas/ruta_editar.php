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
              const list2 = document.querySelector(".li_rutas");
              list2.className += " active ";
              const list3 = document.querySelector(".li_rutas_submenu");
              list3.className += " active ";
            </script>
          <?php
          }

set_time_limit (0);
ini_set('memory_limit','6000M');
$periodoActual = $_SESSION['periodoActual'];
// require_once '../../db/conexion.php';
// $Link = new mysqli($Hostname, $Username, $Password, $Database);
// if ($Link->connect_errno) {
//     echo "Fallo al contenctar a MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
// }
// $Link->set_charset("utf8");

$titulo = "Editar rutas";

$nameLabel = get_titles('configuracion', 'rutas', $labels);
$titulo = $nameLabel . ' - Editar';
?>
<div class="row wrapper wrapper-content border-bottom white-bg page-heading">
    <div class="col-lg-8">
        <h2><?= $titulo; ?></h2>
        <ol class="breadcrumb">
            <li>
                <a href="<?= $baseUrl; ?>">Home</a>
            </li>
            <li>
                <a href="<?= $baseUrl; ?>/modules/rutas/rutas.php">Rutas</a>
            </li>
            <li class="active">
                <strong><?php echo $titulo; ?></strong>
            </li>
        </ol>
    </div>
  <?php if($_SESSION["perfil"] == 1 || $_SESSION["perfil"] == 0) { ?>
  <!-- <div class="col-lg-4">
      <div class="title-action">
          <a href="#" class="btn btn-primary" onclick="crearSede();"><i class="fa fa-plus"></i> Nueva</a>
      </div>
  </div> -->
  <?php } ?>
</div>

<div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="col-lg-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-content">
                        <div class="row">
                        <?php
                            $idRuta = 0;
                            $nombreRuta = '';
                            if (isset($_GET['id']) && $_GET['id'] != '') {
                                $idRuta = $_GET['id'];
                            }
                            if($idRuta > 0){
                                $consulta = " select * from rutas where id = $idRuta ";
                                $resultado = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));
                                if($resultado->num_rows >= 1){
                                    $row = $resultado->fetch_assoc();
                                    $nombreRuta = $row['Nombre'];
                                }
                            }
                        ?>
                            <form class="col-lg-12" role="form">
                                <div class="row">
                                    <div class="col-sm-3 form-group"><label>Nombre de la Ruta</label>
                                        <input type="hidden" name="idRuta" id="idRuta" value="<?php echo $idRuta; ?>">
                                        <input type="text" placeholder="Nombre de la Ruta" class="form-control" id="nombreRuta" name="nombreRuta" value="<?php echo $nombreRuta; ?>">
                                    </div>

                                    <div class="col-sm-3 form-group">
                                        <label>Municipio</label>
                                        <select class="form-control municipio" name="municipio" id="municipio">
                                            <option value="">Seleccione uno</option>
                                        </select>
                                    </div>

                                    <div class="col-sm-3 form-group"><label>Institución</label>
                                        <select class="form-control institucion" name="institucion" id="institucion">
                                            <option value="">Todos</option>
                                        </select>
                                    </div>

                                    <div class="col-sm-3 form-group"><label>Sede</label>
                                        <select class="form-control institucion" name="sede" id="sede">
                                            <option value="">Todos</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-3 form-group">
                                        <button class="btn btn-w-s btn-primary" type="button" id="btnAgregar"><strong>+</strong></button>
                                        <button class="btn btn-w-s btn-primary" type="button" id="btnQuitar"><strong>-</strong></button>
                                        <button class="btn btn-w-s btn-primary" type="button" onclick="actualizarRuta()"><strong>Actualizar Ruta</strong></button>
                                    </div>
                                </div>
                            </form>
                    </div>

                    <div class="row">
                        <div class="col-lg-12">
                            <input type="checkbox" name="selectVarios" id="selectVarios" value="">
                            <label for="selectVarios">Seleccionar Todos</label>
                        </div>
                    </div>

                    <div class="table-responsive">
                    <?php
                        $consulta = " select * from rutas ";
                        $resultado = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));
                    ?>

                    <table class="table table-striped table-hover dataTables-example" id="box-table-a">
                        <thead>
                            <tr>
                                <th></th>
                                <th>Municipio</th>
                                <th>Institución</th>
                                <th>Sede</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>
                        </tbody>
                        <tfoot>
                            <tr>
                                <th></th>
                                <th>Municipio</th>
                                <th>Institución</th>
                                <th>Sede</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

</div><!-- /.wrapper -->
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
<script src="<?php echo $baseUrl; ?>/modules/rutas/js/ruta_editar.js"></script>

<script>
    $(document).ready(function() {
        dataset1 = $('.dataTables-example').DataTable({
            pageLength: 25,
            bPaginate: true,
            dom: 'lr<"containerBtn"><"inputFiltro"f>tip<"clear">',
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
            }
            // buttons: [ {extend: 'excel', title: 'Rutas', className: "btnExportarExcel"} ]
        });
    });
</script>

</body>
</html>
