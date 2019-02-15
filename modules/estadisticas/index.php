<?php 
$titulo = 'Estadísticas';
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
            <h2>Totales por municipio  y semana</h2>
            <br>
          </div>

          <div class="col-sm-7">
            <table class="table table-striped table-hover selectableRows" id="tablaMunicipios">
              <thead id="tHeadSemanaMun">
                
              </thead>
              <tbody id="tBodySemanaMun">
                
              </tbody>
              <tfoot id="tFootSemanaMun">
                
              </tfoot>
            </table>
          </div>
          <div class="col-sm-5">
            <div id="map" style="height: 500px;">
            </div>
          </div>
          <hr class="col-sm-12">
          <div class="col-sm-12" style="text-align: center;">
            <h2>Valor de recursos ejecutados</h2>
            <br>
          </div>
          <div class="col-sm-6">

            <?php 
            $consValores = "SELECT 'ValorContrato' AS Concepto, ValorContrato FROM parametros
                            UNION
                            SELECT CODIGO AS Concepto, ValorRacion AS ValorContrato FROM tipo_complemento;";
            $resValores = $Link->query($consValores);

            $valorRaciones = [];
            if ($resValores->num_rows > 0) {
              while($Valores = $resValores->fetch_assoc()) {
                if ($Valores['Concepto'] == "ValorContrato") {
                  $valorContrato = $Valores['ValorContrato'];
                } else {
                  $valorRaciones[$Valores['Concepto']] = $Valores['ValorContrato'];
                }
              }
            }

             ?>

            <table class="table">
              <tr>
                <th>Valor del contrato</th>
                <td style="text-align: right;">$ <?php echo number_format($valorContrato, 2, ",", "."); ?></td>
              </tr>
              <?php foreach ($valorRaciones as $complemento => $valor): ?>
                <tr>
                  <th>Valor Ofertado por <?= $complemento ?></th>
                  <td style="text-align: right;">$ <?php echo number_format($valor, 2, ",", "."); ?></td>
                </tr>
              <?php endforeach ?>
            </table>
          </div>
          <div class="col-sm-12">
            
          </div>
          <div class="col-sm-5">
            <table class="table table-striped table-hover selectableRows" id="tablaValoresEjecutados">
              
            </table>
          </div>
          <div class="col-sm-7">
            <div id="graficaValoresEjecutados"></div>
          </div>
          <div class="col-sm-5">
            <table class="table table-striped table-hover selectableRows" id="tablaValoresEjecutadosPorcentajes">
              
            </table>
          </div>
          <div class="col-sm-7">
            <div id="graficaValoresEjecutadosPorcentajes"></div>
          </div>

        </div><!-- /.ibox-content -->
      </div><!-- /.ibox float-e-margins -->
    </div><!-- /.col-lg-12 -->
  </div><!-- /.row -->

  <div class="row" id="filtroSemana" style="display: none;">
    <div class="col-lg-12">
      <div class="ibox float-e-margins">
        <div class="ibox-content contentBackground row">
          <div class="col-sm-12 row nopadding" >
            <div class="col-sm-12" style="text-align: center;">
              <h1>Semana <span id="numSemana"></span></h1>
            </div>
            <input type="hidden" name="semana" id="semana">
            <hr class="col-sm-12">
            <div class="col-sm-4">
              <h3>Totales Tipo complemento por semana</h3>
              <table class="table table-bordered" id="complementoSemanas">
              
              </table>
            </div>
            <div class="col-sm-8">
              <div id="graficaComplementoSemanas"></div>
            </div>
            <div class="col-sm-4">
              <h3>Totales semana por tipo complemento alimentario y grupo etario</h3>
              <table class="table table-bordered" id="complementoEtarios">
              
              </table>
            </div>
            <div class="col-sm-8">
              <div id="graficaComplementoEtarios"></div>
            </div>
            <div class="col-sm-4">
              <h3>Totales por dias tipo complemento alimentario</h3>
              <table class="table table-bordered" id="complementoDias">
              
              </table>
            </div>
            <div class="col-sm-8">
              <div id="graficaComplementoDias"></div>
            </div>
          </div>
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
<script src="<?php echo $baseUrl; ?>/modules/estadisticas/js/estadisticas.js"></script>


<script type="text/javascript">

</script>

<?php mysqli_close($Link); ?>

</body>
</html>