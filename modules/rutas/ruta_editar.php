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
                        <h2>Editar Ruta</h2>
                    </div>
                    <div class="ibox-content">
                        <ol class="breadcrumb">
                            <li>
                                <a href="<?php echo $baseUrl; ?>">Home</a>
                            </li>
                            <li>
                                <a href="<?php echo $baseUrl; ?>/rutas.php">Rutas</a>
                            </li>
                            <li class="active">
                                <strong>Editar Ruta</strong>
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
                                        <button class="btn btn-w-s btn-success" type="button" id="btnAgregar"><strong>+</strong></button>
                                        <button class="btn btn-w-s btn-success" type="button" id="btnQuitar"><strong>-</strong></button>
                                        <button class="btn btn-w-s btn-success" type="button" onclick="actualizarRuta()"><strong>Actualizar Ruta</strong></button>
                                    </div>
                                </div>    
                            </form>

                    
                    </div><!-- /.row -->











<div class="row">
  <div class="col-lg-12">
    
        <input type="checkbox" name="selectVarios" id="selectVarios" value="">
          <label for="selectVarios">Seleccionar Todos</label>
  </div>
</div>








                        <div class="table-responsive">
                            <?php
                                $consulta = " select * from rutas ";
                                //echo "<br>$consulta<br><br><br>";
                                $resultado = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));
                            ?>
                       
                            <table class="table table-striped table-bordered table-hover dataTables-example" id="box-table-a">
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





    <!-- Page-Level Scripts -->
    <script>
        $(document).ready(function() {  


  dataset1 = $('.dataTables-example').DataTable({
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


       //Función que vijila el no salirse de la pagina sin guardar
       /*
       $(window).bind('beforeunload', function(){
        return 'Está a punto de salir sin guardar el movimiento actual, todos los campos diligenciados se perderán';
       });
       $('#contactform').submit(function(){
        $(window).unbind('beforeunload');
        return false;
       });
       */

    </script>
    
    <script src="<?php echo $baseUrl; ?>/modules/rutas/js/ruta_editar.js"></script>

</body>
</html>
