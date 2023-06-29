<?php
$titulo = 'Estadísticas';
require_once '../../header.php';

if ($permisos['informes'] == "0") {
  ?><script type="text/javascript">
    window.open('<?= $baseUrl ?>', '_self');
  </script>
<?php exit(); }
	  else {
      ?><script type="text/javascript">
        const list = document.querySelector(".li_informes");
        list.className += " active ";
        const list2 = document.querySelector(".li_estadisticas");
        list2.className += " active ";
      </script>
      <?php
      }
$periodoActual = $_SESSION['periodoActual'];
$mesesNom = array('01' => "Enero", "02" => "Febrero", "03" => "Marzo", "04" => "Abril", "05" => "Mayo", "06" => "Junio", "07" => "Julio", "08" => "Agosto", "09" => "Septiembre", "10" => "Octubre", "11" => "Noviembre", "12" => "Diciembre");

$nameLabel = get_titles('informes', 'estadisticas', $labels);
$titulo = $nameLabel;
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
  </div>
  <div class="col-lg-4">
    <div class="title-action">
      <button class="btn btn-primary exportarEstadisticas" style="display: none;"><span class="fa fa-file-excel-o"></span>  Exportar</button>
    </div>
  </div>
</div>

<div class="wrapper wrapper-content animated fadeInRight">
  <div class="row">

    <div class="col-lg-12">
      <div class="ibox float-e-margins">
        <div class="ibox-content contentBackground row">
          <div class="col-sm-12 text-center">
            <h2><strong>Totales por semana</strong></h2>
            <em>Puede ver estadísticas por semana, haciendo clic en cada una de ellas de la siguiente tabla.</em>
            <br>
          </div>
          <div class="col-sm-12">  
            <div class="table-responsive">
              <table class="table selectableRows table-bordered col-sm-12">
                <thead id="tHeadSemana">

                </thead>
                <tbody id="tBodySemana">

                </tbody>
                <tfoot id="tFootSemana">

                </tfoot>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="col-lg-12">
      <div class="ibox float-e-margins">
        <div class="ibox-content contentBackground row">
          <div class="col-sm-12 text-center">
            <h2><strong>Totales por tipo complemento alimentario</strong></h2>
            <br>
          </div>
          <div class="col-sm-6 col-sm-offset-3">
            <table class="table table-bordered table-striped table-hover selectableRows">
              <thead id="tHeadComp">

              </thead>
              <tbody id="tBodyComp">

              </tbody>
              <tfoot id="tFootComp">

              </tfoot>
            </table>
          </div>

          <div class="col-sm-12 nopadding">
            <div id="graficaTotalesComplemento"></div>
          </div>
        </div>
      </div>
    </div>

    <div class="col-lg-12">
      <div class="ibox float-e-margins">
        <div class="ibox-content contentBackground row">
          <div class="col-sm-12 text-center">
            <h2><strong>Totales por municipio y semana</strong></h2>
            <br>
          </div>

          <div class="col-sm-8">
            <div class="table-responsive">
              <table class="table table-striped table-hover selectableRows" id="tablaMunicipios">
                <thead id="tHeadSemanaMun">

                </thead>
                <tbody id="tBodySemanaMun">

                </tbody>
                <tfoot id="tFootSemanaMun">

                </tfoot>
              </table>
            </div>
          </div>
          <div class="col-sm-4">
            <div class="well">
              <div id="map" style="height: 500px;">
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="col-lg-12">
      <div class="ibox float-e-margins">
        <div class="ibox-content contentBackground row">
          <div class="col-sm-12" style="text-align: center;">
            <h2><strong>Valor de recursos ejecutados</strong></h2>
            <br>
          </div>
          <div class="col-sm-4">
            <?php
              $consValores = "SELECT 'ValorContrato' AS Concepto, ValorContrato FROM parametros
                            UNION
                            SELECT CODIGO AS Concepto, ValorRacion AS ValorContrato FROM tipo_complemento ORDER BY Concepto;";
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
            <table class="table table-striped table-condensed">
              <tr>
                <th>Valor del contrato</th>
                <td class="text-right">$ <?= number_format($valorContrato, 2, ",", "."); ?></td>
              </tr>
              <?php foreach ($valorRaciones as $complemento => $valor): ?>
                <tr>
                  <th>Valor Ofertado por <?= $complemento ?></th>
                  <td class="text-right">$ <?= number_format($valor, 2, ",", "."); ?></td>
                </tr>
              <?php endforeach ?>
            </table>
          </div>

          <div class="col-sm-4">
            <table class="table table-bordered table-striped table-hover selectableRows" id="tablaValoresEjecutadosPorcentajes">

            </table>
          </div>
          <div class="col-sm-4">
            <div id="graficaValoresEjecutadosPorcentajes"></div>
          </div>

          <div class="col-sm-12 nopadding">
            <div id="graficaValoresEjecutados"></div>
          </div>

          <div class="col-sm-12">
            <table class="table table-bordered table-hover selectableRows" id="tablaValoresEjecutados">

            </table>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="row" id="filtroSemana" style="display: none;">
    <div class="col-lg-12">
      <div class="ibox float-e-margins">
        <div class="ibox-content contentBackground row">
          <div class="col-sm-12 row" >
            <div class="col-sm-12 text-center">
              <h1>Semana <span id="numSemana"></span></h1>
            </div>
            <input type="hidden" name="semana" id="semana">
            <hr class="col-sm-12">
            <div class="row">
              <h3 class="text-center"><strong>Totales Tipo complemento por semana</strong></h3>
              <br>
              <div class="col-sm-4">
                <div class="table-responsive">
                  <table class="table table-bordered" id="complementoSemanas">

                  </table>
                </div>
              </div>
              <div class="col-sm-8">
                <div id="graficaComplementoSemanas"></div>
              </div>
            </div>
            <hr>
            <div class="row">
              <h3 class="text-center"><strong>Totales semana por tipo complemento alimentario y grupo etario</strong></h3>
              <br>
              <div class="col-sm-4">
                <div class="table-responsive">
                  <table class="table table-bordered" id="complementoEtarios">

                  </table>
                </div>
              </div>
              <div class="col-sm-8">
                <div id="graficaComplementoEtarios"></div>
              </div>
            </div>
            <hr>
            <div class="row">
              <h3 class="text-center"><strong>Totales por dias tipo complemento alimentario</strong></h3>
              <br>
              <div class="col-sm-4">
                <div class="table-responsive">
                  <table class="table table-bordered" id="complementoDias">

                  </table>
                </div>
              </div>
              <div class="col-sm-8">
                <div id="graficaComplementoDias"></div>
              </div>
            </div>
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

<?php mysqli_close($Link); ?>

</body>
</html>