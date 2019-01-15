<?php 
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


<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row">
        <div class="col-lg-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h2>Rutas</h2>
                </div>
                <div class="ibox-content">
                    <ol class="breadcrumb">
                        <li>
                            <a href="<?php echo $baseUrl; ?>">Home</a>
                        </li>
                        <li class="active">
                            <strong>Rutas</strong>
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
                        <div class="table-responsive">
                            <?php
                                $consulta = " select * from rutas ";
                                //echo "<br>$consulta<br><br><br>";
                                $resultado = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));
                            ?>
                            <table class="table table-striped table-bordered table-hover dataTables-example selectableRows" >
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
            $('.dataTables-example').DataTable({
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
