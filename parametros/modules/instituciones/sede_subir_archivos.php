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

$nomSede = '';
if(isset($_POST['nomSede'])){
    $nomSede = $_POST['nomSede'];
}
$codSede = '';
if(isset($_POST['codSede'])){
    $codSede = $_POST['codSede'];
}
$nomInst = '';
if(isset($_POST['nomInst'])){
    $nomInst = $_POST['nomInst'];
}
?>






<div class="row wrapper wrapper-content border-bottom white-bg page-heading">
    <div class="col-lg-8">
        <div>

            <?php var_dump($_POST); ?>
            



        </div>
        <h1><?php echo $nomInst; ?></h1>
        <h2>Sede: <?php echo $nomSede; ?></h2>
        <h4><?php echo $codSede; ?></h4>
        <ol class="breadcrumb">
          <li>
              <a href="<?php echo $baseUrl.$_SESSION['rutaDashboard']; ?>">Home</a>
          </li>
          <li class="active">
              <strong>Institucion</strong>
          </li>
        </ol>
    </div>
  <div class="col-lg-4">
      <div class="title-action">
         <!--
          <a href="#" class="btn btn-white"><i class="fa fa-pencil"></i> Edit </a>
          <a href="#" class="btn btn-white"><i class="fa fa-check "></i> Save </a>
        -->
          <!-- <a href="<?php echo $baseUrl; ?>/modules/despachos/despacho_nuevo.php" target="_self" class="btn btn-primary"><i class="fa fa-truck"></i> Nuevo despacho </a> -->
      </div>
  </div>
</div>
<!-- /.row wrapper de la cabecera de la seccion -->












<div class="wrapper wrapper-content">
            <div class="row">
                <div class="col-lg-3">
                    <div class="ibox float-e-margins">
                        <div class="ibox-content">
                            <div class="file-manager">
                                <h5>Show:</h5>
                                <a href="#" class="file-control active">Ale</a>
                                <a href="#" class="file-control">Documents</a>
                                <a href="#" class="file-control">Audio</a>
                                <a href="#" class="file-control">Images</a>
                                <div class="hr-line-dashed"></div>
                                <button class="btn btn-primary btn-block">Upload Files</button>
                                <div class="hr-line-dashed"></div>
                                <h5>Folders</h5>
                                <ul class="folder-list" style="padding: 0">
                                    <li><a href=""><i class="fa fa-folder"></i> Files</a></li>
                                    <li><a href=""><i class="fa fa-folder"></i> Pictures</a></li>
                                    <li><a href=""><i class="fa fa-folder"></i> Web pages</a></li>
                                    <li><a href=""><i class="fa fa-folder"></i> Illustrations</a></li>
                                    <li><a href=""><i class="fa fa-folder"></i> Films</a></li>
                                    <li><a href=""><i class="fa fa-folder"></i> Books</a></li>
                                </ul>
                                <h5 class="tag-title">Tags</h5>
                                <ul class="tag-list" style="padding: 0">
                                    <li><a href="">Family</a></li>
                                    <li><a href="">Work</a></li>
                                    <li><a href="">Home</a></li>
                                    <li><a href="">Children</a></li>
                                    <li><a href="">Holidays</a></li>
                                    <li><a href="">Music</a></li>
                                    <li><a href="">Photography</a></li>
                                    <li><a href="">Film</a></li>
                                </ul>
                                <div class="clearfix"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-9 animated fadeInRight">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="file-box">
                                <div class="file">
                                    <a href="#">
                                        <span class="corner"></span>

                                        <div class="icon">
                                            <i class="fa fa-file"></i>
                                        </div>
                                        <div class="file-name">
                                            Document_2014.doc
                                            <br/>
                                            <small>Added: Jan 11, 2014</small>
                                        </div>
                                    </a>
                                </div>

                            </div>
                            <div class="file-box">
                                <div class="file">
                                    <a href="#">
                                        <span class="corner"></span>

                                        <div class="image">
                                            <img alt="image" class="img-responsive" src="img/p1.jpg">
                                        </div>
                                        <div class="file-name">
                                            Italy street.jpg
                                            <br/>
                                            <small>Added: Jan 6, 2014</small>
                                        </div>
                                    </a>

                                </div>
                            </div>
                            <div class="file-box">
                                <div class="file">
                                    <a href="#">
                                        <span class="corner"></span>

                                        <div class="image">
                                            <img alt="image" class="img-responsive" src="img/p2.jpg">
                                        </div>
                                        <div class="file-name">
                                            My feel.png
                                            <br/>
                                            <small>Added: Jan 7, 2014</small>
                                        </div>
                                    </a>
                                </div>
                            </div>
                            <div class="file-box">
                                <div class="file">
                                    <a href="#">
                                        <span class="corner"></span>

                                        <div class="icon">
                                            <i class="fa fa-music"></i>
                                        </div>
                                        <div class="file-name">
                                            Michal Jackson.mp3
                                            <br/>
                                            <small>Added: Jan 22, 2014</small>
                                        </div>
                                    </a>
                                </div>
                            </div>
                            <div class="file-box">
                                <div class="file">
                                    <a href="#">
                                        <span class="corner"></span>

                                        <div class="image">
                                            <img alt="image" class="img-responsive" src="img/p3.jpg">
                                        </div>
                                        <div class="file-name">
                                            Document_2014.doc
                                            <br/>
                                            <small>Added: Fab 11, 2014</small>
                                        </div>
                                    </a>
                                </div>
                            </div>
                            <div class="file-box">
                                <div class="file">
                                    <a href="#">
                                        <span class="corner"></span>

                                        <div class="icon">
                                            <i class="img-responsive fa fa-film"></i>
                                        </div>
                                        <div class="file-name">
                                            Monica's birthday.mpg4
                                            <br/>
                                            <small>Added: Fab 18, 2014</small>
                                        </div>
                                    </a>
                                </div>
                            </div>
                            <div class="file-box">
                                <a href="#">
                                    <div class="file">
                                        <span class="corner"></span>

                                        <div class="icon">
                                            <i class="fa fa-bar-chart-o"></i>
                                        </div>
                                        <div class="file-name">
                                            Annual report 2014.xls
                                            <br/>
                                            <small>Added: Fab 22, 2014</small>
                                        </div>
                                    </div>
                                </a>
                            </div>
                            <div class="file-box">
                                <div class="file">
                                    <a href="#">
                                        <span class="corner"></span>

                                        <div class="icon">
                                            <i class="fa fa-file"></i>
                                        </div>
                                        <div class="file-name">
                                            Document_2014.doc
                                            <br/>
                                            <small>Added: Jan 11, 2014</small>
                                        </div>
                                    </a>
                                </div>

                            </div>
                            <div class="file-box">
                                <div class="file">
                                    <a href="#">
                                        <span class="corner"></span>

                                        <div class="image">
                                            <img alt="image" class="img-responsive" src="img/p1.jpg">
                                        </div>
                                        <div class="file-name">
                                            Italy street.jpg
                                            <br/>
                                            <small>Added: Jan 6, 2014</small>
                                        </div>
                                    </a>

                                </div>
                            </div>
                            <div class="file-box">
                                <div class="file">
                                    <a href="#">
                                        <span class="corner"></span>

                                        <div class="image">
                                            <img alt="image" class="img-responsive" src="img/p2.jpg">
                                        </div>
                                        <div class="file-name">
                                            My feel.png
                                            <br/>
                                            <small>Added: Jan 7, 2014</small>
                                        </div>
                                    </a>
                                </div>
                            </div>
                            <div class="file-box">
                                <div class="file">
                                    <a href="#">
                                        <span class="corner"></span>

                                        <div class="icon">
                                            <i class="fa fa-music"></i>
                                        </div>
                                        <div class="file-name">
                                            Michal Jackson.mp3
                                            <br/>
                                            <small>Added: Jan 22, 2014</small>
                                        </div>
                                    </a>
                                </div>
                            </div>
                            <div class="file-box">
                                <div class="file">
                                    <a href="#">
                                        <span class="corner"></span>

                                        <div class="image">
                                            <img alt="image" class="img-responsive" src="img/p3.jpg">
                                        </div>
                                        <div class="file-name">
                                            Document_2014.doc
                                            <br/>
                                            <small>Added: Fab 11, 2014</small>
                                        </div>
                                    </a>
                                </div>
                            </div>
                            <div class="file-box">
                                <div class="file">
                                    <a href="#">
                                        <span class="corner"></span>

                                        <div class="icon">
                                            <i class="img-responsive fa fa-film"></i>
                                        </div>
                                        <div class="file-name">
                                            Monica's birthday.mpg4
                                            <br/>
                                            <small>Added: Fab 18, 2014</small>
                                        </div>
                                    </a>
                                </div>
                            </div>
                            <div class="file-box">
                                <a href="#">
                                    <div class="file">
                                        <span class="corner"></span>

                                        <div class="icon">
                                            <i class="fa fa-bar-chart-o"></i>
                                        </div>
                                        <div class="file-name">
                                            Annual report 2014.xls
                                            <br/>
                                            <small>Added: Fab 22, 2014</small>
                                        </div>
                                    </div>
                                </a>
                            </div>
                            <div class="file-box">
                                <div class="file">
                                    <a href="#">
                                        <span class="corner"></span>

                                        <div class="icon">
                                            <i class="fa fa-file"></i>
                                        </div>
                                        <div class="file-name">
                                            Document_2014.doc
                                            <br/>
                                            <small>Added: Jan 11, 2014</small>
                                        </div>
                                    </a>
                                </div>

                            </div>
                            <div class="file-box">
                                <div class="file">
                                    <a href="#">
                                        <span class="corner"></span>

                                        <div class="image">
                                            <img alt="image" class="img-responsive" src="img/p1.jpg">
                                        </div>
                                        <div class="file-name">
                                            Italy street.jpg
                                            <br/>
                                            <small>Added: Jan 6, 2014</small>
                                        </div>
                                    </a>

                                </div>
                            </div>
                            <div class="file-box">
                                <div class="file">
                                    <a href="#">
                                        <span class="corner"></span>

                                        <div class="image">
                                            <img alt="image" class="img-responsive" src="img/p2.jpg">
                                        </div>
                                        <div class="file-name">
                                            My feel.png
                                            <br/>
                                            <small>Added: Jan 7, 2014</small>
                                        </div>
                                    </a>
                                </div>
                            </div>
                            <div class="file-box">
                                <div class="file">
                                    <a href="#">
                                        <span class="corner"></span>

                                        <div class="icon">
                                            <i class="fa fa-music"></i>
                                        </div>
                                        <div class="file-name">
                                            Michal Jackson.mp3
                                            <br/>
                                            <small>Added: Jan 22, 2014</small>
                                        </div>
                                    </a>
                                </div>
                            </div>
                            <div class="file-box">
                                <div class="file">
                                    <a href="#">
                                        <span class="corner"></span>

                                        <div class="image">
                                            <img alt="image" class="img-responsive" src="img/p3.jpg">
                                        </div>
                                        <div class="file-name">
                                            Document_2014.doc
                                            <br/>
                                            <small>Added: Fab 11, 2014</small>
                                        </div>
                                    </a>
                                </div>
                            </div>
                            <div class="file-box">
                                <div class="file">
                                    <a href="#">
                                        <span class="corner"></span>

                                        <div class="icon">
                                            <i class="img-responsive fa fa-film"></i>
                                        </div>
                                        <div class="file-name">
                                            Monica's birthday.mpg4
                                            <br/>
                                            <small>Added: Fab 18, 2014</small>
                                        </div>
                                    </a>
                                </div>
                            </div>
                            <div class="file-box">
                                <a href="#">
                                    <div class="file">
                                        <span class="corner"></span>

                                        <div class="icon">
                                            <i class="fa fa-bar-chart-o"></i>
                                        </div>
                                        <div class="file-name">
                                            Annual report 2014.xls
                                            <br/>
                                            <small>Added: Fab 22, 2014</small>
                                        </div>
                                    </div>
                                </a>
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


    <script src="<?php echo $baseUrl; ?>/modules/impresion_planillas/js/certificados.js"></script>



    <!-- Page-Level Scripts -->


<?php mysqli_close($Link); ?>

<form action="despacho_por_sede.php" method="post" name="formDespachoPorSede" id="formDespachoPorSede">
  <input type="hidden" name="despachoAnnoI" id="despachoAnnoI" value="">
  <input type="hidden" name="despachoMesI" id="despachoMesI" value="">
  <input type="hidden" name="despacho" id="despacho" value="">
</form>

<form action="despachos.php" id="parametrosBusqueda" method="get">
  <input type="hidden" id="pb_annoi" name="pb_annoi" value="">
  <input type="hidden" id="pb_mes" name="pb_mes" value="">
  <input type="hidden" id="pb_diai" name="pb_diai" value="">
  <input type="hidden" id="pb_annof" name="pb_annof" value="">
  <input type="hidden" id="pb_mesf" name="pb_mesf" value="">
  <input type="hidden" id="pb_diaf" name="pb_diaf" value="">
  <input type="hidden" id="pb_tipo" name="pb_tipo" value="">
  <input type="hidden" id="pb_municipio" name="pb_municipio" value="">
  <input type="hidden" id="pb_institucion" name="pb_institucion" value="">
  <input type="hidden" id="pb_sede" name="pb_sede" value="">
  <input type="hidden" id="pb_tipoDespacho" name="pb_tipoDespacho" value="">
  <input type="hidden" id="pb_ruta" name="pb_ruta" value="">
  <input type="hidden" id="pb_btnBuscar" name="pb_btnBuscar" value="">
</form>


    <!-- Page-Level Scripts -->
    <script>
        $(document).ready(function(){
            $('.dataTables-sedes').DataTable({
                pageLength: 25,
                responsive: true,
                dom: '<"html5buttons"B>lTfgitp',
                buttons: [
                    { extend: 'copy'},
                    {extend: 'csv'},
                    {extend: 'excel', title: 'ExampleFile'},
                    {extend: 'pdf', title: 'ExampleFile'},

                    {extend: 'print',
                     customize: function (win){
                            $(win.document.body).addClass('white-bg');
                            $(win.document.body).css('font-size', '10px');

                            $(win.document.body).find('table')
                                    .addClass('compact')
                                    .css('font-size', 'inherit');
                    }
                    }
                ]

            });

        });

    </script>





</body>
</html>