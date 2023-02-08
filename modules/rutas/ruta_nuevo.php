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

$periodoActual = $_SESSION['periodoActual'];
require_once '../../db/conexion.php';
$Link = new mysqli($Hostname, $Username, $Password, $Database);
if ($Link->connect_errno) {
    echo "Fallo al contenctar a MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
}
$Link->set_charset("utf8");

$nameLabel = get_titles('configuracion', 'rutas', $labels);
$titulo = $nameLabel. ' - ver'
?>





    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="col-lg-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <h2><?= $titulo ?></h2>
                    </div>
                    <div class="ibox-content">
                        <ol class="breadcrumb">
                            <li>
                                <a href="<?php echo $baseUrl; ?>">Home</a>
                            </li>
                            <li class="active">
                                <strong><?= $titulo ?></strong>
                            </li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>




    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="col-lg-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-content">

                        <div class="row">
                            <form class="col-lg-12" role="form">
                                <div class="row">
                                    <div class="col-sm-3 form-group"><label>Nombre de la Ruta</label>
                                        <input type="text" placeholder="Nombre de la Ruta" class="form-control" id="nombreRuta" name="nombreRuta">
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
                                        <button class="btn btn-w-s btn-primary" type="button" onclick="guardarRuta()"><strong>Guardar Ruta</strong></button>
                                    </div>
                                </div>
                                <div class="row">
                                    <hr>
                                    <div class="col-sm-3 form-group">
                                        <input type="checkbox" name="selectVarios" id="selectVarios" value="">
                                        <label for="selectVarios">Seleccionar Todos</label>
                                    </div>
                                </div>
                            </form>
                        </div>









                        <div class="table-responsive">
                            <table class="table table-striped table-bordered table-hover dataTables-example" id="box-table-a" >
                                <thead>
                                    <tr>
                                        <th></th>
                                        <th class="center">Municipio</th>
                                        <th class="center">Institución</th>
                                        <th class="center">Sede</th>
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
                                    <th class="center">Municipio</th>
                                    <th class="center">Institución</th>
                                    <th class="center">Sede</th>
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


    <script src="<?php echo $baseUrl; ?>/modules/rutas/js/ruta_nuevo.js"></script>



    <!-- Page-Level Scripts -->
    <script>
        $(document).ready(function() {
            dataset1 =  $('#box-table-a').DataTable({
            "bPaginate": false,
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
    });
        });
    </script>

</body>
</html>
