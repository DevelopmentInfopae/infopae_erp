<?php 
$titulo = 'Estadísticas Avanzadas'; 
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
  </script>
  <?php
  }

$periodoActual = $_SESSION['periodoActual'];
$mesesNom = array('01' => "Enero", "02" => "Febrero", "03" => "Marzo", "04" => "Abril", "05" => "Mayo", "06" => "Junio", "07" => "Julio", "08" => "Agosto", "09" => "Septiembre", "10" => "Octubre", "11" => "Noviembre", "12" => "Diciembre");

$nameLabel = get_titles('informes', 'estadisticasAvanzadas', $labels);
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
  </div><!-- /.col -->
  <div class="col-lg-4">
    <div class="title-action">
      <button class="btn btn-primary exportarEstadisticas"><span class="fa fa-file-excel-o"></span>  Exportar</button>
    </div>
  </div><!-- /.col -->
</div><!-- /.row -->

<div class="wrapper wrapper-content animated fadeInRight">
  <div class="row">
    <!-- seccion totales semana -->
    <div class="col-lg-12">
      <div class="ibox float-e-margins">
        <div class="ibox-content contentBackground row">
          <div class="row">
            <div class="col-sm-12" style="text-align: center;">
              <h2><strong>Totales por semana</strong></h2>
              <br>
            </div>    
            <div class="col-sm-12">  
              <div class="table-responsive">
                <table class="table selectableRows table-hover table-bordered col-sm-12 table-responsive" >
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
          <div class="row">
            <div class="col-lg-12"> 
              <div id="graficaTotalesSemanas"></div>
            </div>
          </div>
         </div> 
       </div>
     </div>

      <!-- seccion totales por tipo de complemento -->
    <div class="col-lg-12">
      <div class="ibox float-e-margins">
        <div class="ibox-content contentBackground row">
          <div class="row">
            <div class="col-sm-12" style="text-align: center;">
              <h2><strong>Totales por tipo complemento alimentario</strong></h2>
              <br>
            </div>
            <div class="col-md-5 col-sm-12">
                <br>
                <br>
              <div class="table-responsive">
                <table class="table table-bordered selectableRows table-hover" id="tableResponsiveStacktable">
                  <thead id="tHeadComp">
                    
                  </thead>
                  <tbody id="tBodyComp">
                    
                  </tbody>
                  <tfoot id="tFootComp">
                    
                  </tfoot>
                </table>
              </div>
            </div>
            <div class="col-md-7 col-sm-12 nopadding">
              <div id="graficaTotalesComplemento"></div>
            </div>
          </div>
        </div>
      </div>
    </div>
      
      <!-- seccion totales por genero -->
    <div class="col-lg-12">
      <div class="ibox float-e-margins">
        <div class="ibox-content contentBackground row">
          <div class="row">
            <div class="col-sm-12" style="text-align: center;">
              <h2><strong>Totales por género</strong></h2>
              <br>
            </div>
            <div class="col-sm-12">
              <div class="table-responsive">
                <table class="table table-bordered selectableRows table-hover">
                  <thead id="tHeadGeneros">
                      
                  </thead>
                  <tbody id="tBodyGeneros">
                      
                  </tbody>
                  <tfoot id="tFooTGeneros">
                      
                  </tfoot>
                </table>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-6 col-sm-8 col-xs-10">
              <br>
              <br>
              <table class="table table-bordered selectableRows table-hover">
                <thead id="tHeadGenero">
                    
                </thead>
                <tbody id="tBodyGenero">
                    
                </tbody>
                <tfoot id="tFootGenero">
                    
                </tfoot>
              </table>
            </div>
            <!-- </div> -->
            <div class="col-md-6 col-sm-8 col-xs-10 nopadding">
              <div id="graficaTotalesGenero"></div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- seccion totales por edad -->
    <div class="col-lg-12">
      <div class="ibox float-e-margins">
        <div class="ibox-content contentBackground row">
          <div class="row">
            <div class="col-sm-12" style="text-align: center;">
              <h2><strong>Totales por edad</strong></h2>
              <br>
            </div>
            <div class="col-sm-12">
              <div class="table-responsive">
                <table class="table table-bordered selectableRows table-hover">
                  <thead id="tHeadEdad">
                    
                  </thead>
                  <tbody id="tBodyEdad">
                    
                  </tbody>
                  <tfoot id="tFooTEdad">
                    
                  </tfoot>
                </table>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-sm-12"> 
              <div id="graficaTotalesEdad"></div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- seccion totales por estrato  -->
    <div class="col-lg-12">
      <div class="ibox float-e-margins">
        <div class="ibox-content contentBackground row">
          <div class="row">
            <div class="col-sm-12" style="text-align: center;">
              <h2><strong>Totales por estrato</strong></h2>
              <br>
            </div>
          <!-- <div class="row"> -->
            <div class="col-sm-12">
              <div class="table-responsive">
                <table class="table table-bordered selectableRows table-hover"> 
                  <thead id="tHeadEstrato">
                    
                  </thead>
                  <tbody id="tBodyEstrato">
                    
                  </tbody>
                  <tfoot id="tFootEstrato">
                    
                  </tfoot>
                </table>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-sm-12"> 
              <div id="graficaTotalesEstrato"></div>
            </div>
          </div>
        </div>
      </div>
    </div>

      <!-- seccion totales residencia       -->
    <div class="col-lg-12">
      <div class="ibox float-e-margins">
        <div class="ibox-content contentBackground row">
          <div class="row">      
            <div class="col-sm-12" style="text-align: center;">
              <h2><strong>Totales por zona de residencia</strong></h2>
              <br>
            </div>
            <div class="col-sm-12">
              <div class="table-responsive">
                <table class="table table-bordered selectableRows table-hover">
                  <thead id="tHeadResidencia">
                    
                  </thead>
                  <tbody id="tBodyResidencia">
                    
                  </tbody>
                  <tfoot id="tFootResidencia">
                    
                  </tfoot>
                </table>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-sm-12"> 
              <div id="graficaTotalesResidencia"></div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- seccion totales escolaridad -->
    <div class="col-lg-12">
      <div class="ibox float-e-margins">
        <div class="ibox-content contentBackground row">
          <div class="row">
            <div class="col-sm-12" style="text-align: center;">
              <h2><strong>Totales por grado de escolaridad</strong></h2>
              <br>
            </div>
            <div class="col-sm-12">
              <div class="table-responsive">
                <table class="table table-bordered selectableRows table-hover">
                  <thead id="tHeadEscolaridad">
                    
                  </thead>
                  <tbody id="tBodyEscolaridad">
              
                  </tbody>
                  <tfoot id="tFootEscolaridad">
                  
                  </tfoot>
                </table>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-sm-12 nopadding"> 
              <div id="graficaTotalesEscolaridad"></div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- seccion totales por jornada   -->
    <div class="col-lg-12">
      <div class="ibox float-e-margins">
        <div class="ibox-content contentBackground row">
          <div class="row">
            <div class="col-sm-12" style="text-align: center;">
              <h2><strong>Totales por jornada</strong></h2>
              <br>
            </div>
            <div class="col-sm-12">
              <div class="table-responsive">
                <table class="table table-bordered selectableRows table-hover">         
                  <thead id="tHeadJornada">
                    
                  </thead>
                  <tbody id="tBodyJornada">
                  
                  </tbody>
                  <tfoot id="tFootJornada">
                  
                  </tfoot>
                </table>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-sm-12 nopadding"> 
              <div id="graficaTotalesJornada"></div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- seccion de municipios o sedes educativas -->
    <?php
      $consCodMun = "SELECT codMunicipio FROM parametros";            
      $resCodMun = $Link->query($consCodMun);
      $codMun = [];
        if ($resCodMun->num_rows > 0) {
          while($Valores = $resCodMun->fetch_assoc()) {
            $codMun = $Valores;
          }
          $codMun2 = $codMun['codMunicipio'];
          if ($codMun2 == "0") {
                echo "<div class='col-lg-12'>";
                  echo "<div class='ibox float-e-margins'>";
                    echo "<div class='ibox-content contentBackground row'>";
                      echo "<div class='row'>";
                        echo "<div class='col-sm-12' style='text-align: center;''>";
                          echo "<h2><strong>Totales por municipio</strong></h2>";
                          echo "<br>";
                        echo "</div>";
                        echo "<div class='col-sm-12'>";
                          echo "<div class='table-responsive'>";
                            echo "<table class='table table-bordered selectableRows table-hover' id='tablaMunicipios'>";
                              echo "<thead id='tHeadMunicipio'></thead>";
                              echo "<tbody id='tBodyMunicipio'></tbody>";
                              echo "<tfoot id='tFootMunicipio'></tfoot>";
                            echo "</table>";
                          echo "</div>";
                        echo "</div>";    
                      echo "</div>";
                      echo "<div class='row'>";
                        echo "<div class='col-sm-12'>"; 
                          echo "<div class='well'>"; 
                            echo "<div id='map' style='height: 500px;''></div>";
                          echo "</div>";
                        echo "</div>";
                      echo "</div>";  
                    echo "</div>";
                  echo "</div>";
                echo "</div>";            
            } else {
                echo "<div class='col-lg-12'>";
                  echo "<div class='ibox float-e-margins'>";
                    echo "<div class='ibox-content contentBackground row'>";
                      echo "<div class='row'>";
                        echo "<div class='col-sm-12' style='text-align: center;''>";
                          echo "<h2><strong>Totales por Sede educativa</strong></h2>";
                          echo "<br>";
                        echo "</div>";
                        echo "<div class='col-sm-12'>";
                          echo "<div class='table-responsive'>";
                            echo "<table class='table table-bordered selectableRows table-hover' id='tablaSedes'>";
                              echo "<thead id='tHeadSedes'></thead>";
                              echo "<tbody id='tBodySedes'></tbody>";
                              echo "<tfoot id='tFootSedes'></tfoot>";
                            echo "</table>";
                          echo "</div>";
                        echo "</div>";   
                      echo "</div>";
                        
                          echo "<div class='col-sm-12 '>";  
                            echo "<div id='graficaTotalesSedes'></div>";
                          echo "</div>";
                        
                    echo "</div>";
                  echo "</div>";
                echo "</div>";
            }
        }
    ?>

    <!-- seccion de discapacidad -->
    <div class="col-lg-12">
      <div class="ibox float-e-margins">
        <div class="ibox-content contentBackground row">
          <div class="row">
            <div class="col-sm-12" style="text-align: center;">
              <h2><strong>Totales por población discapacidad</strong></h2>
              <br>
            </div>
            <div class="col-sm-12">
              <div class="table-responsive">
                <table class="table table-bordered selectableRows table-hover">
                  <thead id="tHeadDiscapacidad">
                    
                  </thead>
                  <tbody id="tBodyDiscapacidad">
                  
                  </tbody>
                  <tfoot id="tFootDiscapacidad">
                  
                  </tfoot>
                </table>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-sm-12 nopadding"> 
              <div id="graficaTotalesDiscapacidad"></div>
            </div>
          </div>
        </div>
      </div>
    </div> 

        <!-- seccion poblacion victima -->
    <div class="col-lg-12">
      <div class="ibox float-e-margins">
        <div class="ibox-content contentBackground row">
          <div class="row">
            <div class="col-sm-12" style="text-align: center;">
              <h2><strong>Totales por población victima</strong></h2>
              <br>
            </div>
            <div class="col-sm-12">
              <div class="table-responsive">
                <table class="table table-bordered selectableRows table-hover">
                  <thead id="tHeadVictima">
                    
                  </thead>
                  <tbody id="tBodyVictima">
                  
                  </tbody>
                  <tfoot id="tFootVictima">
                  
                  </tfoot>
                </table>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-sm-12 nopadding"> 
              <div id="graficaTotalesVictima"></div>
            </div>
          </div>
        </div>
      </div>
    </div>

        <!-- seccion totales por etnia -->
    <div class="col-lg-12">
      <div class="ibox float-e-margins">
        <div class="ibox-content contentBackground row">
          <div class="row">
            <div class="col-sm-12" style="text-align: center;">
              <h2><strong>Totales por Etnia</strong></h2>
              <br>
            </div>
            <div class="col-sm-12">             
              <div class="table-responsive">
                <table class="table table-bordered selectableRows table-hover">
                
                  <thead id="tHeadEtnia">
                    
                  </thead>
                  <tbody id="tBodyEtnia">
                  
                  </tbody>
                  <tfoot id="tFootEtnia">
                  
                  </tfoot>
                </table>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-sm-12 nopadding"> 
              <div id="graficaTotalesEtnia"></div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- seccion totales ejecutados -->
    <div class="col-lg-12">
      <div class="ibox float-e-margins">
        <div class="ibox-content contentBackground row">
          <div class="col-sm-12" >
              <h2 style="text-align: center;"><strong>Valor de recursos ejecutados</strong></h2>
              <br>
          <div class="row">
          <div class="col-md-4 col-sm-12">
                  <?php
                    $consValores = "SELECT 'ValorContrato' AS Concepto, ValorContrato FROM parametros
                                  UNION
                                  SELECT CODIGO AS Concepto, ValorRacion AS ValorContrato FROM tipo_complemento WHERE valorRacion > 0 ORDER BY Concepto;";
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
            <div class="col-md-4 col-sm-12">
              <table class="table table-bordered table-striped table-hover selectableRows" id="tablaValoresEjecutadosPorcentajes">

              </table>
            </div>

            <div class="col-md-4 col-sm-12">
                <div id="graficaValoresEjecutadosPorcentajes"></div>
            </div>
            </div>
          <!-- </div>  -->   
            <div class="row">
              <div class="table-responsive">
                <table class="table table-bordered table-hover selectableRows" id="tablaValoresEjecutados">

                </table>
              </div>
            </div>

            <div class="col-sm-12 ">
                <div id="graficaValoresEjecutados"></div>
            </div>

          </div>    
        </div><!-- /.ibox-content -->
      </div><!-- /.ibox float-e-margins -->
    </div>/<!-- .col-lg-12 -->

<!--  -->
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