<?php 
$titulo = 'Estadísticas Avanzadas';
require_once '../../header.php'; 
$periodoActual = $_SESSION['periodoActual'];
$mesesNom = array('01' => "Enero", "02" => "Febrero", "03" => "Marzo", "04" => "Abril", "05" => "Mayo", "06" => "Junio", "07" => "Julio", "08" => "Agosto", "09" => "Septiembre", "10" => "Octubre", "11" => "Noviembre", "12" => "Diciembre");
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
    <div class="title-action">
      <button class="btn btn-primary exportarEstadisticas" style="display: none;"><span class="fa fa-file-excel-o"></span>  Exportar</button>
    </div>
  </div><!-- /.col -->
</div><!-- /.row -->

<div class="wrapper wrapper-content animated fadeInRight">
  <div class="row">
    <div class="col-lg-12">
      <div class="ibox float-e-margins">
        <div class="ibox-content contentBackground row">
          <div class="col-sm-12" style="text-align: center;">
            <h2>Totales por semana</h2>
            <em>Puede ver estadísticas por semana, haciendo clic en cada una de ellas de la siguiente tabla.</em>
            <br>
          </div>
          <table class="table selectableRows table-bordered col-sm-12">
            <thead id="tHeadSemana">
              
            </thead>
            <tbody id="tBodySemana">
              
            </tbody>
            <tfoot id="tFootSemana">
              
            </tfoot>
          </table>
          <div class="col-sm-12">
            <div id="graficaTotalesSemanas"></div>
          </div>
          <hr class="col-sm-12">
          <div class="col-sm-12" style="text-align: center;">
            <h2>Totales por tipo complemento alimentario</h2>
            <br>
          </div>
          <div class="col-sm-6 nopadding">
            <table class="table table-bordered">
              <thead id="tHeadComp">
                
              </thead>
              <tbody id="tBodyComp">
                
              </tbody>
              <tfoot id="tFootComp">
                
              </tfoot>
            </table>
          </div>
          <div class="col-sm-6">
            <div id="graficaTotalesComplemento"></div>
          </div>
          <hr class="col-sm-12">

          <div class="col-sm-12" style="text-align: center;">
            <h2>Totales por género</h2>
            <br>
          </div>
          <div class="col-sm-6 nopadding">
            <table class="table table-bordered">
              <thead id="tHeadGenero">
                
              </thead>
              <tbody id="tBodyGenero">
                
              </tbody>
              <tfoot id="tFootGenero">
                
              </tfoot>
            </table>
          </div>
          <div class="col-sm-6">
            <div id="graficaTotalesGenero"></div>
          </div>
          <hr class="col-sm-12">
        </div><!-- /.ibox-content -->
      </div><!-- /.ibox float-e-margins -->
    </div><!-- /.col-lg-12 -->
  </div><!-- /.row -->
</div><!-- /.wrapper wrapper-content animated fadeInRight -->
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
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script src="<?php echo $baseUrl; ?>/theme/js/plugins/jvectormap/jquery-jvectormap-2.0.2.min.js"></script>
<script src="<?php echo $baseUrl; ?>/theme/js/plugins/jvectormap/jquery-jvectormap-world-mill-en.js"></script>
<script src="<?php echo $baseUrl; ?>/theme/js/plugins/jvectormap/jquery-jvectormap-co-merc.js"></script>

<!-- Section Scripts -->
<script src="<?php echo $baseUrl; ?>/modules/estadisticas_avanzadas/js/estadisticas_avanzadas.js"></script>


<script type="text/javascript">

</script>

<?php mysqli_close($Link); ?>

</body>
</html>