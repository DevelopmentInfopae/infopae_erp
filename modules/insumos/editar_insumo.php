<?php
if (!isset($_POST['codigoinsumoeditar'])) { echo "<script>location.href='index.php';</script>"; }

$codigoinsumo = $_POST['codigoinsumoeditar'];

$titulo = 'Editar insumo';
require_once '../../header.php';
$periodoActual = $_SESSION['periodoActual'];

if ($_SESSION['perfil'] == 1 || $_SESSION['perfil'] == 0) {} else { echo "<script>location.href='index.php';</script>"; }
?>

<div class="row wrapper wrapper-content border-bottom white-bg page-heading">
  <div class="col-lg-8">
    <h2><?php echo $titulo; ?></h2>
    <ol class="breadcrumb">
      <li>
        <a href="<?php echo $baseUrl; ?>">Inicio</a>
      </li>
      <li>
        <a href="index.php">Ver insumos</a>
      </li>
      <li class="active">
        <strong><?php echo $titulo; ?></strong>
      </li>
    </ol>
  </div><!-- /.col -->
  <div class="col-lg-4">
    <div class="title-action">
      <button class="btn btn-primary" onclick="submitForm('formInsumoEditar', 2);" id="segundoBtnSubmit" style=""><span class="fa fa-check"></span> Guardar</button>
    </div><!-- /.title-action -->
  </div><!-- /.col -->
</div><!-- /.row -->


<div class="wrapper wrapper-content animated fadeInRight">
  <div class="row">
    <div class="col-lg-12">
      <div class="ibox float-e-margins">
        <div class="ibox-content contentBackground">

          <?php

          $consultaInsumo = "SELECT * FROM productos".$_SESSION['periodoActual']." WHERE Codigo = ".$codigoinsumo;
          $resultadoInsumo = $Link->query($consultaInsumo);
          if ($resultadoInsumo->num_rows > 0) {
            $insumo = $resultadoInsumo->fetch_assoc();
          }
           ?>

          <div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true"><!-- COLLAPSE -->
            <div class="panel panel-default">
              <div class="panel-heading clearfix" role="tab" id="headingOne">
                <h4 class="panel-title"><span class="fa fa-file-text-o"></span>   Datos del insumo
                </h4>
              </div>
              <div id="producto" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="headingOne">
                <div class="panel-body">
                  <form class="form" id="formInsumoEditar" method="post">
                    <input type="hidden" name="idinsumo" value="<?php echo $insumo['Id']; ?>">
                    <input type="hidden" name="codigoinsumo" value="<?php echo $codigoinsumo; ?>">
                    <div class="form-group col-sm-3">
                      <label>Tipo de conteo</label>
                      <select class="form-control" name="tipo_conteo" id="tipo_conteo" required>
                        <option value="01" <?= substr($insumo['Codigo'], 2, 2) == "01" ? "selected='selected'" : "" ?>>Contado por cupos</option>
                        <option value="02" <?= substr($insumo['Codigo'], 2, 2) == "02" ? "selected='selected'" : "" ?>>Contado por manipuladores</option>
                        <option value="03" <?= substr($insumo['Codigo'], 2, 2) == "03" ? "selected='selected'" : "" ?>>Contado individualmente</option>
                        <option value="04" <?= substr($insumo['Codigo'], 2, 2) == "04" ? "selected='selected'" : "" ?>>Contado por despacho</option>
                      </select>
                    </div>
                    <?php
                    if (substr($insumo['Codigo'], 2, 2) == "04") {
                      $style_conteo = "display:none;";
                      $required_conteo = "";
                    } else {
                      $style_conteo = "";
                      $required_conteo = "required";
                    }
                     ?>
                    <div class="form-group col-sm-3">
                      <label>Descripción</label>
                      <input type="text" name="descripcion" id="descripcion_editar" class="form-control" placeholder="Describa el producto" value="<?php echo $insumo['Descripcion'] ?>" required>
                    </div>
                    <div class="form-group col-sm-3" id="divCantPresentacion" style="<?= $style_conteo ?>">
                      <label>Cantidad por mes </label>
                      <input type="number" name="cantidadMes" class="form-control" value="<?php echo $insumo['CantidadUnd1']; ?>" <?= $required_conteo ?>>
                    </div>
                    <hr class="col-sm-11">
                    <?php if($insumo['NombreUnidad1'] == "u"){$style="style='display:none;'";} else { $style="";} ?>
                    <div class="form-group col-sm-12" id="gestionMedidas" <?php echo $style; ?>>
                      <label>Añadir/borrar medida presentación</label><br>
                      <label class="btn btn-primary" onclick="anadirMedida();"><span class="fa fa-plus"></span></label>
                      <label class="btn btn-danger" onclick="quitarMedida();"><span class="fa fa-minus"></span></label>
                    </div>
                    <div id="medidasPresentacion">
                      <div class="form-group col-sm-3">
                        <label>Unidad de medida</label>
                        <select class="form-control" name="unidadMedida" id="unidadMedida" value="cc" required>
                          <?php if ($insumo['NombreUnidad1'] == "u"): ?>
                            <option value="u">Unidad</option>
                            <option value="g">Gramos</option>
                            <option value="cc">Centímetros cúbicos</option>
                          <?php elseif ($insumo['NombreUnidad1'] == "g"): ?>
                            <option value="g">Gramos</option>
                            <option value="u">Unidad</option>
                            <option value="cc">Centímetros cúbicos</option>
                          <?php elseif ($insumo['NombreUnidad1'] == "cc"): ?>
                            <option value="cc">Centímetros cúbicos</option>
                            <option value="g">Gramos</option>
                            <option value="u">Unidad</option>
                          <?php endif ?>
                        </select>
                      </div>
                      <?php if($insumo['NombreUnidad1'] == "u"){$style="style='display:none;'";} else { $style="";} ?>
                      <div class="form-group col-sm-3" id="divUnidadMedidaPresentacion" <?php echo $style; ?>>
                        <label>Unidad de medida de presentación 1</label>
                        <select name="unidadMedidaPresentacion[1]" id="unidadMedidaPresentacion" class="form-control unidadMedidaPresentacion">
                          <?php if ($insumo['NombreUnidad2'] == "u" && $insumo['NombreUnidad1'] == "g"): ?>
                            <option value="u">Unidad</option>
                            <option value="kg">Kilogramo</option>
                            <option value="lb">Libra</option>
                            <option value="g">Gramos</option>
                          <?php elseif ($insumo['NombreUnidad2'] == "u" && $insumo['NombreUnidad1'] == "cc"): ?>
                            <option value="u">Unidad</option>
                            <option value="lt">Litro</option>
                            <option value="cc">Centímetros cúbicos</option>
                          <?php elseif ($insumo['NombreUnidad2'] == "g" || ($insumo['NombreUnidad2'] != "g" && $insumo['NombreUnidad2'] != "cc" && strpos($insumo['NombreUnidad2'], " g"))): ?>
                            <option value="g">Gramos</option>
                            <option value="lb">Libra</option>
                            <option value="kg">Kilogramo</option>
                          <?php elseif ($insumo['NombreUnidad2'] == "cc" || ($insumo['NombreUnidad2'] != "g" && $insumo['NombreUnidad2'] != "cc" && strpos($insumo['NombreUnidad2'], "cc"))): ?>
                            <option value="cc">Centímetros cúbicos</option>
                            <option value="lt">Litro</option>
                          <?php elseif (strpos($insumo['NombreUnidad2'], "lb")): ?>
                            <option value="lb">Libra</option>
                            <option value="g">Gramos</option>
                            <option value="kg">Kilogramo</option>
                          <?php elseif (strpos($insumo['NombreUnidad2'], "kg")): ?>
                            <option value="kg">Kilogramo</option>
                            <option value="lb">Libra</option>
                            <option value="g">Gramos</option>
                          <?php elseif (strpos($insumo['NombreUnidad2'], "lt")): ?>
                            <option value="lt">Litro</option>
                            <option value="cc">Centímetros cúbicos</option>
                          <?php endif ?>
                        </select>
                      </div>
                      <div class="form-group col-sm-3" id="divCantPresentacion">
                        <label>Cantidad presentación 1</label>
                        <input type="number" min="0" name="cantPresentacion[1]" id="cantPresentacion" class="form-control" onkeyup="validaCantPresentacion(1);" value="<?php echo $insumo['CantidadUnd2']; ?>" required>
                        <em id="msgcp1" style="display: none;">Ordenar de mayor a menor.</em>
                      </div>

                      <?php if ($insumo['NombreUnidad3'] != ""): ?>
                        <div id="medida_2">
                          <div class="form-group col-sm-3" id="divUnidadMedidaPresentacion" <?php echo $style; ?>>
                            <label>Unidad medida presentación 2</label>
                            <select name="unidadMedidaPresentacion[2]" id="unidadMedidaPresentacion" class="form-control unidadMedidaPresentacion">
                              <?php if ($insumo['NombreUnidad3'] == "g" || ($insumo['NombreUnidad3'] != "g" && $insumo['NombreUnidad3'] != "cc" && $insumo['NombreUnidad1'] == "g")): ?>
                                <option value="g">Gramos</option>
                              <?php elseif ($insumo['NombreUnidad3'] == "cc" || ($insumo['NombreUnidad3'] != "g" && $insumo['NombreUnidad3'] != "cc" && $insumo['NombreUnidad1'] == "cc")): ?>
                                <option value="cc">Centímetros cúbicos</option>
                              <?php endif ?>
                            </select>
                          </div>
                          <div class="form-group col-sm-3" id="divCantPresentacion">
                            <label>Cantidad presentación 2</label>
                            <input type="number" min="0" name="cantPresentacion[2]" id="cantPresentacion" class="form-control" onkeyup="validaCantPresentacion(1);" value="<?php echo $insumo['CantidadUnd3']; ?>" required>
                            <em id="msgcp1" style="display: none;">Ordenar de mayor a menor.</em>
                          </div>
                        </div>
                      <?php endif ?>
                      <?php if ($insumo['NombreUnidad4'] != ""): ?>
                        <div id="medida_3">
                          <div class="form-group col-sm-3" id="divUnidadMedidaPresentacion" <?php echo $style; ?>>
                            <label>Unidad medida presentación 3</label>
                            <select name="unidadMedidaPresentacion[3]" id="unidadMedidaPresentacion" class="form-control unidadMedidaPresentacion">
                              <?php if ($insumo['NombreUnidad4'] == "g" || ($insumo['NombreUnidad4'] != "g" && $insumo['NombreUnidad4'] != "cc" && $insumo['NombreUnidad1'] == "g")): ?>
                                <option value="g">Gramos</option>
                              <?php elseif ($insumo['NombreUnidad4'] == "cc" || ($insumo['NombreUnidad4'] != "g" && $insumo['NombreUnidad4'] != "cc" && $insumo['NombreUnidad1'] == "cc")): ?>
                                <option value="cc">Centímetros cúbicos</option>
                              <?php endif ?>
                            </select>
                          </div>
                          <div class="form-group col-sm-3" id="divCantPresentacion">
                            <label>Cantidad presentación 3</label>
                            <input type="number" min="0" name="cantPresentacion[3]" id="cantPresentacion" class="form-control" onkeyup="validaCantPresentacion(1);" value="<?php echo $insumo['CantidadUnd4']; ?>" required>
                            <em id="msgcp1" style="display: none;">Ordenar de mayor a menor.</em>
                          </div>
                        </div>
                      <?php endif ?>
                      <?php if ($insumo['NombreUnidad5'] != ""): ?>
                        <div id="medida_4">
                          <div class="form-group col-sm-3" id="divUnidadMedidaPresentacion" <?php echo $style; ?>>
                            <label>Unidad medida presentación 4</label>
                            <select name="unidadMedidaPresentacion[4]" id="unidadMedidaPresentacion" class="form-control unidadMedidaPresentacion">
                              <?php if ($insumo['NombreUnidad5'] == "g" || ($insumo['NombreUnidad5'] != "g" && $insumo['NombreUnidad5'] != "cc" && $insumo['NombreUnidad1'] == "g")): ?>
                                <option value="g">Gramos</option>
                              <?php elseif ($insumo['NombreUnidad5'] == "cc" || ($insumo['NombreUnidad5'] != "g" && $insumo['NombreUnidad5'] != "cc" && $insumo['NombreUnidad1'] == "cc")): ?>
                                <option value="cc">Centímetros cúbicos</option>
                              <?php endif ?>
                            </select>
                          </div>
                          <div class="form-group col-sm-3" id="divCantPresentacion">
                            <label>Cantidad presentación 4</label>
                            <input type="number" min="0" name="cantPresentacion[4]" id="cantPresentacion" class="form-control" onkeyup="validaCantPresentacion(1);" value="<?php echo $insumo['CantidadUnd5']; ?>" required>
                            <em id="msgcp1" style="display: none;">Ordenar de mayor a menor.</em>
                          </div>
                        </div>
                      <?php endif ?>
                    </div>
                  </form>
                  <div class="form-group col-sm-12">
                      <button class="btn btn-primary" onclick="submitForm('formInsumoEditar', 2);" id="segundoBtnSubmit" style=""><span class="fa fa-check"></span> Guardar</button>
                  </div>
                </div>
              </div>
            </div>
            </div>
            </div>
          </div><!-- COLLAPSE -->
        </div><!-- /.ibox-content -->
      </div><!-- /.ibox float-e-margins -->
    </div><!-- /.col-lg-12 -->

<form method="POST" id="ver_insumo" action="ver_insumo.php" style="display: none;">
  <input type="hidden" name="codigoinsumover" id="codigoinsumover">
</form>

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

<!-- Section Scripts -->
<script src="<?php echo $baseUrl; ?>/modules/insumos/js/insumos.js"></script>

<script type="text/javascript">/*
  console.log('Aplicando Data Table');
  dataset1 = $('#box-table').DataTable({
    order: [ 0, 'asc' ],
    pageLength: 25,
    responsive: true,
    oLanguage: {
      sLengthMenu: 'Mostrando _MENU_ registros por página',
      sZeroRecords: 'No se encontraron registros',
      sInfo: 'Mostrando _START_ a _END_ de _TOTAL_ registros',
      sInfoEmpty: 'Mostrando 0 a 0 de 0 registros',
      sInfoFiltered: '(Filtrado desde _MAX_ registros)',
      sSearch:         'Buscar: ',
      oPaginate:{
        sFirst:    'Primero',
        sLast:     'Último',
        sNext:     'Siguiente',
        sPrevious: 'Anterior'
      }
    }
    });*/

    $(document).ready(function(){
      setTimeout(function() {
        $('#tipo_conteo').trigger('change');
        $('#unidadMedida').trigger('change');
      }, 2000);
    });

</script>

<?php mysqli_close($Link); ?>

</body>
</html>