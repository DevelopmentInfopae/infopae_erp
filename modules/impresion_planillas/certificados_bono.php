<?php
  include '../../header.php';
  
  if ($permisos['entrega_complementos'] == "0") {
    ?><script type="text/javascript">
      window.open('<?= $baseUrl ?>', '_self');
    </script>
  <?php exit(); }

  require_once '../../db/conexion.php';

  set_time_limit (0);
  ini_set('memory_limit','6000M');
  $periodoActual = $_SESSION['periodoActual'];


  $con_cod_muni = "SELECT CodMunicipio FROM parametros;";
  $res_minicipio = $Link->query($con_cod_muni) or die(mysqli_error($Link));
  if ($res_minicipio->num_rows > 0) {
  $codigoDANE = $res_minicipio->fetch_array();
  }
?>

<div class="row wrapper wrapper-content border-bottom white-bg page-heading">
  <div class="col-lg-8">
  <h2>Certificados Bono Alimentario</h2>
    <ol class="breadcrumb">
      <li>
        <a href="<?php echo $baseUrl; ?>">Inicio</a>
      </li>
      <li class="active">
        <strong>Certificados Bono Alimentario</strong>
      </li>
    </ol>
  </div>
</div>

<div class="wrapper wrapper-content animated fadeInRight">
  <div class="row">
    <div class="col-lg-12">
      <div class="ibox float-e-margins">
        <div class="ibox-content contentBackground">
          <form class="col-lg-12" action="certificado_bono.php" name="formPlanillas" id="formPlanillas" method="post" target="_blank">
            <div class="row">
              <div class="col-sm-6 col-lg-4 form-group">
                <label for="municipio">Municipio *</label>
                <select class="form-control select2" name="municipio" id="municipio" required>
                  <option value="">Seleccione uno</option>
                  <?php
                    $consulta = "SELECT DISTINCT codigoDANE, ciudad FROM ubicacion WHERE ETC = 0 ";
                    $codMunicipio = $_SESSION['p_Municipio'];
                    $DepartamentoOperador = $_SESSION['p_CodDepartamento'];
                    if($codMunicipio == '0'){
                      $consulta = $consulta." and CodigoDANE like '$DepartamentoOperador%' ";
                    }else if ($codMunicipio != '0') {
                      $consulta = $consulta. " and codigoDANE = '" .$codMunicipio. "'"; 
                    }
                    $consulta = $consulta." order by ciudad asc ";
                    $resultado = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));
                    if($resultado->num_rows >= 1){
                      while($row = $resultado->fetch_assoc()) { 
                  ?>
                        <option value="<?php echo $row["codigoDANE"]; ?>"  <?php  if((isset($_GET["pb_municipio"]) && $_GET["pb_municipio"] == $row["codigoDANE"]) || ($codigoDANE["CodMunicipio"] == $row["codigoDANE"])){ echo " selected "; } ?> ><?php echo $row["ciudad"]; ?></option>
                  <?php
                      }// Termina el while
                    }//Termina el if que valida que si existan resultados
                  ?>
                </select>
              </div> <!-- form-group -->

              <div class="col-sm-6 col-lg-4 form-group">
                <label for="instituciones">Instituciones *</label>
                <select class="form-control select2" name="instituciones" id="instituciones" required >
                  <option value="">Seleccione...</option>
                </select>
              </div> <!-- form-group -->

              <div class="col-sm-6 col-lg-4 form-group">
                <label for="sedes">Sedes</label>
                <select class="form-control select2" name="sedes" id="sedes">
                  <option value="">Seleccione...</option>
                </select>
              </div> <!-- form-group -->
            </div> <!-- row -->

            <div class="row">
              <div class="col-sm-6 col-lg-4 form-group">
                <label for="meses">Mes *</label>
                <select class="form-control" name="meses" id="meses" required>
                  <option value="">Seleccione...</option>
                  <?php
                    $nombreMes = ''; 
                    $consultaMeses = "SELECT DISTINCT(mes) as mes FROM planilla_semanas;";
                    $respuestaMeses = $Link->query($consultaMeses) or die('Error al consultar los meses ' . mysqli_error($Link));
                    if ($respuestaMeses->num_rows > 0) {
                      while ($dataMeses = $respuestaMeses->fetch_assoc()) {
                        if ($dataMeses['mes'] == '01' || $dataMeses['mes'] == '1' || $dataMeses['mes'] == '1b') {
                          $nombreMes = 'Enero';
                        }
                        elseif ($dataMeses['mes'] == '02' || $dataMeses['mes'] == '2' || $dataMeses['mes'] == '2b') {
                          $nombreMes = 'Febrero';
                        }
                        elseif ($dataMeses['mes'] == '03' || $dataMeses['mes'] == '3' || $dataMeses['mes'] == '3b') {
                          $nombreMes = 'Marzo';
                        }
                        elseif ($dataMeses['mes'] == '04' || $dataMeses['mes'] == '4' || $dataMeses['mes'] == '4b') {
                          $nombreMes = 'Abril';
                        }
                        elseif ($dataMeses['mes'] == '05' || $dataMeses['mes'] == '5' || $dataMeses['mes'] == '5b') {
                          $nombreMes = 'Mayo';
                        }
                        elseif ($dataMeses['mes'] == '06' || $dataMeses['mes'] == '6' || $dataMeses['mes'] == '6b') {
                          $nombreMes = 'Junio';
                        }
                        elseif ($dataMeses['mes'] == '07' || $dataMeses['mes'] == '7' || $dataMeses['mes'] == '7b') {
                          $nombreMes = 'Julio';
                        }
                        elseif ($dataMeses['mes'] == '08' || $dataMeses['mes'] == '8' || $dataMeses['mes'] == '8b') {
                          $nombreMes = 'Agosto';
                        }
                        elseif ($dataMeses['mes'] == '09' || $dataMeses['mes'] == '9' || $dataMeses['mes'] == '9b') {
                          $nombreMes = 'Septiembre';
                        }
                        elseif ($dataMeses['mes'] == '10' || $dataMeses['mes'] == '10' || $dataMeses['mes'] == '10b') {
                          $nombreMes = 'Octubre';
                        }
                        elseif ($dataMeses['mes'] == '11' || $dataMeses['mes'] == '11' || $dataMeses['mes'] == '11b') {
                          $nombreMes = 'Noviembre';
                        }
                        elseif ($dataMeses['mes'] == '12' || $dataMeses['mes'] == '12' || $dataMeses['mes'] == '12b') {
                          $nombreMes = 'Diciembre';
                        }
                  ?>
                  <option value="<?php echo $dataMeses['mes'];?>"> <?php echo $nombreMes; ?></option>
                  <?php     
                      }   
                    }
                  ?>
                </select>
              </div> <!-- form-group -->

              <div class="col-sm-6 col-lg-4 form-group">
                <label for="hojaNovedades">Páginas de observaciones</label>
                <input class="form-control" type="number" name="hojaNovedades" id="hojaNovedades" value="1" min="0">
              </div>
              
              <div class="col-sm-6 col-lg-4 form-group">
                <label for="imprimirMes">Imprimir nombre del mes</label>
                  <div>
                    <input type="checkbox" name="imprimirMes" id="imprimirMes">
                  </div>
              </div>
            </div> <!-- row -->

            <div class="row">
              <div class="col-sm-12 form-group">
                <label for="observaciones">Observaciones</label>
                <textarea class="form-control" name="observaciones" id="observaciones" cols="30" rows="5"></textarea>
              </div>

              <div class="row">
                <div class="col-sm-3 form-group">
                  <button class="btn btn-primary" type="submit" id="btnBuscar" name="btnBuscar" value="1" ><strong> <i class="fas fa-search"></i> Buscar </strong></button>
                </div> <!-- col-sm-3 -->
              </div> <!-- row -->
            </div>
          </form> 
        </div> <!-- contentBackground -->
      </div> <!-- float-e-margins -->
    </div> <!-- col-lg-12 -->
  </div> <!-- row -->
</div> <!-- fadeInRight -->

<!-- Mainly scripts -->
<script src="<?php echo $baseUrl; ?>/theme/js/jquery-3.1.1.min.js"></script>
<script src="<?php echo $baseUrl; ?>/theme/js/bootstrap.min.js"></script>
<script src="<?php echo $baseUrl; ?>/theme/js/plugins/metisMenu/jquery.metisMenu.js"></script>
<script src="<?php echo $baseUrl; ?>/theme/js/plugins/slimscroll/jquery.slimscroll.min.js"></script>

<!-- Custom and plugin javascript -->
<script src="<?php echo $baseUrl; ?>/theme/js/inspinia.js"></script>
<script src="<?php echo $baseUrl; ?>/theme/js/plugins/validate/jquery.validate.min.js"></script>
<script src="<?php echo $baseUrl; ?>/theme/js/plugins/iCheck/icheck.min.js"></script>
<script src="<?= $baseUrl; ?>/theme/js/plugins/select2/select2.full.min.js"></script>

<script src="<?php echo $baseUrl; ?>/modules/impresion_planillas/js/bonos.js"></script>
