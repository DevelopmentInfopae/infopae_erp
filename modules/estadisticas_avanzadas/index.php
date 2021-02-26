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
            <h2><strong>Totales por semana</strong></h2>
<!--             <em>Puede ver estadísticas por semana, haciendo clic en cada una de ellas de la siguiente tabla.</em> -->
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
         </div> 
       </div>
     </div>

    <div class="col-lg-12">
      <div class="ibox float-e-margins">
        <div class="ibox-content contentBackground row">
          <div class="col-sm-12" style="text-align: center;">
            <h2><strong>Totales por tipo complemento alimentario</strong></h2>
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
        </div>
      </div>
    </div>  

    <div class="col-lg-12">
      <div class="ibox float-e-margins">
        <div class="ibox-content contentBackground row">

          <div class="col-sm-12" style="text-align: center;">
            <h2><strong>Totales por género</strong></h2>
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
        </div>
      </div>
    </div>

    <div class="col-lg-12">
      <div class="ibox float-e-margins">
        <div class="ibox-content contentBackground row">

          <div class="col-sm-12" style="text-align: center;">
            <h2><strong>Totales por edad</strong></h2>
            <br>
          </div>
          <div class="col-sm-12 nopadding">
            <table class="table table-bordered">
              <thead id="tHeadEdad">
                
              </thead>
              <tbody id="tBodyEdad">
                
              </tbody>
              <tfoot id="tFooTEdad">
                
              </tfoot>
            </table>
          </div>
          <div class="col-sm-12"> 
            <div id="graficaTotalesEdad"></div>
          </div>
        </div>
      </div>
    </div>

    <div class="col-lg-12">
      <div class="ibox float-e-margins">
        <div class="ibox-content contentBackground row">

          <div class="col-sm-12" style="text-align: center;">
            <h2><strong>Totales por estrato</strong></h2>
            <br>
          </div>
          <div class="col-sm-12 nopadding">
            <table class="table table-bordered">
              
              <thead id="tHeadEstrato">
                
              </thead>
              <tbody id="tBodyEstrato">
                
              </tbody>
              <tfoot id="tFootEstrato">
                
              </tfoot>
            </table>
            </div>
            <div class="col-sm-12"> 
            <div id="graficaTotalesEstrato"></div>
            </div>
          </div>
        </div>
      </div>
          
     <div class="col-lg-12">
      <div class="ibox float-e-margins">
        <div class="ibox-content contentBackground row">      

          <div class="col-sm-12" style="text-align: center;">
            <h2><strong>Totales por zona de residencia</strong></h2>
            <br>
          </div>
          <div class="col-sm-12 nopadding">
            <table class="table table-bordered">
              
              <thead id="tHeadResidencia">
                
              </thead>
              <tbody id="tBodyResidencia">
                
              </tbody>
              <tfoot id="tFootResidencia">
                
              </tfoot>
            </table>
            </div>
            <div class="col-sm-12"> 
            <div id="graficaTotalesResidencia"></div>
            </div>
          </div>
        </div>
      </div>

    <div class="col-lg-12">
      <div class="ibox float-e-margins">
        <div class="ibox-content contentBackground row">

              <div class="col-sm-12" style="text-align: center;">
              <h2><strong>Totales por grado de escolaridad</strong></h2>
              <br>
              </div>
              <div class="col-sm-12 nopadding">
                <table class="table table-bordered">
              
                  <thead id="tHeadEscolaridad">
                  
                  </thead>
                  <tbody id="tBodyEscolaridad">
                
                  </tbody>
                  <tfoot id="tFootEscolaridad">
                
                  </tfoot>
                </table>
              </div>
            <div class="col-sm-12 nopadding"> 
            <div id="graficaTotalesEscolaridad"></div>
            </div>
          </div>
        </div>
      </div>

    <div class="col-lg-12">
      <div class="ibox float-e-margins">
        <div class="ibox-content contentBackground row">

              <div class="col-sm-12" style="text-align: center;">
              <h2><strong>Totales por jornada</strong></h2>
              <br>
              </div>
              <div class="col-sm-12 nopadding">
                <table class="table table-bordered">
              
                  <thead id="tHeadJornada">
                  
                  </thead>
                  <tbody id="tBodyJornada">
                
                  </tbody>
                  <tfoot id="tFootJornada">
                
                  </tfoot>
                </table>
              </div>
            <div class="col-sm-12 nopadding"> 
            <div id="graficaTotalesJornada"></div>
            </div>
          </div>
        </div>
      </div>

     <div class="col-lg-12">
      <div class="ibox float-e-margins">
        <div class="ibox-content contentBackground row">

              <div class="col-sm-12" style="text-align: center;">
              <h2><strong>Totales por municipio</strong></h2>
              <br>
              </div>
              <div class="col-sm-12 nopadding">
                <table class="table table-bordered" id="tablaMunicipios">
              
                  <thead id="tHeadMunicipio">
                  
                  </thead>
                  <tbody id="tBodyMunicipio">
                
                  </tbody>
                  <tfoot id="tFootMunicipio">
                
                  </tfoot>
                </table>
                </div>
             <div class="col-sm-12">
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
              <h2><strong>Totales por población discapacidad</strong></h2>
              <br>
              </div>
              <div class="col-sm-12 nopadding">
                <table class="table table-bordered">
              
                  <thead id="tHeadDiscapacidad">
                  
                  </thead>
                  <tbody id="tBodyDiscapacidad">
                
                  </tbody>
                  <tfoot id="tFootDiscapacidad">
                
                  </tfoot>
                </table>
              </div>
            <div class="col-sm-12 nopadding"> 
            <div id="graficaTotalesDiscapacidad"></div>
            </div>
          </div>
        </div>
      </div>

    <div class="col-lg-12">
      <div class="ibox float-e-margins">
        <div class="ibox-content contentBackground row">

              <div class="col-sm-12" style="text-align: center;">
              <h2><strong>Totales por población victima</strong></h2>
              <br>
              </div>
              <div class="col-sm-12 nopadding">
                <table class="table table-bordered">
              
                  <thead id="tHeadVictima">
                  
                  </thead>
                  <tbody id="tBodyVictima">
                
                  </tbody>
                  <tfoot id="tFootVictima">
                
                  </tfoot>
                </table>
              </div>
            <div class="col-sm-12 nopadding"> 
            <div id="graficaTotalesVictima"></div>
            </div>
          </div>
        </div>
    </div>

    <div class="col-lg-12">
      <div class="ibox float-e-margins">
        <div class="ibox-content contentBackground row">

              <div class="col-sm-12" style="text-align: center;">
              <h2><strong>Totales por Etnia</strong></h2>
              <br>
              </div>
              <div class="col-sm-12 nopadding">
                <table class="table table-bordered">
              
                  <thead id="tHeadEtnia">
                  
                  </thead>
                  <tbody id="tBodyEtnia">
                
                  </tbody>
                  <tfoot id="tFootEtnia">
                
                  </tfoot>
                </table>
              </div>
            <div class="col-sm-12 nopadding"> 
            <div id="graficaTotalesEtnia"></div>
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
          <!-- </div>  -->   

            <div class="col-sm-12">
              <table class="table table-bordered table-hover selectableRows" id="tablaValoresEjecutados">

              </table>
            </div>

            <div class="col-sm-12 nopadding">
                <div id="graficaValoresEjecutados"></div>
            </div>


        </div><!-- /.ibox-content -->
      </div><!-- /.ibox float-e-margins -->
    </div>/<!-- .col-lg-12 -->
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