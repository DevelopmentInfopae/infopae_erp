<?php
    include 'header.php';
    if ($_SESSION['dashboard'] != "5") {
        ?><script type="text/javascript">
              window.open('<?= $baseUrl.$_SESSION['rutaDashboard'] ?>', '_self');
        </script>
        <?php exit(); }

    else {
    ?><script type="text/javascript">
        const list = document.querySelector(".li_inicio");
        list.className += " active ";
    </script>
    <?php
    }
    $consultaModulosPrincipales = " SELECT  m1.icon,
                                            m1.permisos,
                                            m1.nombre_submodulo, 
                                            m1.label_submodulo, 
                                            m1.ruta
                                        FROM menu_sidebar m1
                                        WHERE m1.dashboard_funcional = 1 AND m1.ruta != '' 
                                        ORDER BY m1.orden
                                        ";
    $respuestaModulosPrincipales = $Link->query($consultaModulosPrincipales) or die (mysqli_error($Link));
    if ($respuestaModulosPrincipales->num_rows > 0) {
        while ($dataModulosPrincipales = $respuestaModulosPrincipales->fetch_assoc()) {
            $modulosPrincipales[] = $dataModulosPrincipales;
        }
    }
    
    $urlWebSite = trim($baseUrl, '/app');
?>
<style>
    .button_index{
        height : 6.5em;
        white-space: normal;
    }
    i{

    }
</style>

<div class="wrapper wrapper-content">
    <div class="row">
        <div class="col-lg-12"> 
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h1><b>Eje Operativo<b></h1>
                </div>
                <div class="ibox-content">
                    <div class="row">
                        <div class="col-lg-12">
                            <?php
                                foreach ($modulosPrincipales as $key => $value) {
                                    $icon = $value['icon'];
                                    $permiso = $value['permisos'];
                                    if ($permisos[$permiso] == 1 || $permisos[$permiso] == 2) {
                                        $nombre = $value['nombre_submodulo'];
                                        $ruta = $value['ruta'];
                                        echo "<div class='col-md-3 col-sm-6'>";
                                            $label = $value['label_submodulo'];
                                            if ($ruta != '') {
                                                $href = $baseUrl.$ruta;
                                            }else{
                                                $href = '#';
                                            }
                                            echo "  <button onclick=location.href='$href' value='$href' data-baseurl='$baseUrl'  type='button' class='btn btn-primary btn-outline btn-block button_index'>
                                                        <i class = '$icon fa-xl'></i> 
                                                        <span>&nbsp $label</span>    
                                                    </button>";
                                        echo "</div>";
                                    }    
                                }
                            ?>
                        </div>
                    </div> <!--  row -->
                </div><!--  ibox-content -->
            </div><!--  ibox -->
        </div> <!--  col-lg-12 -->
    </div> <!--  row -->
</div> <!-- wrapper -->

<?php include 'footer.php'; ?>

<!-- Mainly scripts -->
<script src="theme/js/jquery-3.1.1.min.js"></script>
<script src="theme/js/bootstrap.min.js"></script>
<script src="theme/js/plugins/metisMenu/jquery.metisMenu.js"></script>
<script src="theme/js/plugins/slimscroll/jquery.slimscroll.min.js"></script>

<!-- Custom and plugin javascript -->
<script src="theme/js/inspinia.js"></script>
<script src="theme/js/plugins/pace/pace.min.js"></script>

<!-- jQuery UI -->
<script src="theme/js/plugins/jquery-ui/jquery-ui.min.js"></script>

</body>
</html>
