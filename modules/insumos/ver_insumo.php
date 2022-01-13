<?php
if (!isset($_POST['codigoinsumover'])) { echo "<script>location.href='index.php';</script>"; }


$titulo = 'Ver insumo';
require_once '../../header.php';

if ($permisos['configuracion'] == "0" || $permisos['configuracion'] == "1") {
  ?><script type="text/javascript">
    window.open('<?= $baseUrl ?>', '_self');
  </script>
<?php exit(); }

$periodoActual = $_SESSION['periodoActual'];

$codigoinsumo = $_POST['codigoinsumover'];

$tiposInsumos = [];

$tipoInsumo = "SELECT Codigo, Descripcion FROM productos".$_SESSION['periodoActual']." WHERE Codigo LIKE '05%' AND Nivel = '2'";
$resultadoTipoInsumo = $Link->query($tipoInsumo);
if ($resultadoTipoInsumo->num_rows > 0) {
  while ($tipoInsumo = $resultadoTipoInsumo->fetch_assoc()) {
    $tiposInsumos[$tipoInsumo['Codigo']] = $tipoInsumo['Descripcion'];
  }
}

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
      <div class="btn-group">
        <div class="dropdown">
          <button class="btn btn-primary btn-sm" type="button" id="accionesProducto" data-toggle="dropdown" aria-haspopup="true">
            Acciones
            <span class="caret"></span>
          </button>
          <ul class="dropdown-menu pull-right" aria-labelledby="accionesProducto">
            <?php if ($_SESSION['perfil'] == 1 || $_SESSION['perfil'] == 0): ?>
            <li><a onclick="editarInsumo('<?php echo $codigoinsumo; ?>')"><span class="fa fa-pencil"></span>  Editar</a></li>
              <li>
                <a data-toggle="modal" data-target="#modalEliminar"  data-codigo="<?php echo $codigoinsumo; ?>"><span class="fa fa-trash"></span>  Eliminar</a>
              </li>
            <?php endif ?>
             <li>
              <a><span class="fa fa-file-excel-o"></span> Exportar</a>
             </li>
          </ul>
        </div>
      </div>
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
                    <div class="form-group col-sm-3">
                      <label>Descripción</label>
                      <input type="text" name="descripcion" id="descripcion_editar" class="form-control" placeholder="Describa el producto" value="<?php echo $insumo['Descripcion'] ?>" required>
                    </div>
                    <div class="form-group col-sm-3" id="divCantPresentacion">
                      <label>Cantidad por mes </label>
                      <input type="number" name="cantidadMes" class="form-control" value="<?php echo $insumo['CantidadUnd1']; ?>" required>
                    </div>
                    <hr class="col-sm-11">
                    <div id="medidasPresentacion">
                      <div class="form-group col-sm-3">
                        <label>Unidad de medida</label>
                        <select class="form-control" name="unidadMedida" id="unidadMedida" value="cc" required>
                          <?php if ($insumo['NombreUnidad1'] == "u"): ?>
                            <option value="u">Unidad</option>
                          <?php elseif ($insumo['NombreUnidad1'] == "g"): ?>
                            <option value="g">Gramos</option>
                          <?php elseif ($insumo['NombreUnidad1'] == "cc"): ?>
                            <option value="cc">Centímetros cúbicos</option>
                          <?php endif ?>
                        </select>
                      </div>
                      <?php if($insumo['NombreUnidad1'] == "u"){$style="style='display:none;'";} else { $style="";} ?>
                      <div class="form-group col-sm-3" id="divUnidadMedidaPresentacion" <?php echo $style; ?>>
                        <label>Unidad medida presentación 1</label>
                        <select name="unidadMedidaPresentacion[1]" id="unidadMedidaPresentacion" class="form-control unidadMedidaPresentacion">
                          <?php if ($insumo['NombreUnidad2'] == "u" && $insumo['NombreUnidad1'] == "g"): ?>
                            <option value="u">Unidad</option>
                          <?php elseif ($insumo['NombreUnidad2'] == "u" && $insumo['NombreUnidad1'] == "cc"): ?>
                            <option value="u">Unidad</option>
                          <?php elseif ($insumo['NombreUnidad2'] == "g" || ($insumo['NombreUnidad2'] != "g" && $insumo['NombreUnidad2'] != "cc" && strpos($insumo['NombreUnidad2'], " g"))): ?>
                            <option value="g">Gramos</option>
                          <?php elseif ($insumo['NombreUnidad2'] == "cc" || ($insumo['NombreUnidad2'] != "g" && $insumo['NombreUnidad2'] != "cc" && strpos($insumo['NombreUnidad2'], "cc"))): ?>
                            <option value="cc">Centímetros cúbicos</option>
                          <?php elseif (strpos($insumo['NombreUnidad2'], "lb")): ?>
                            <option value="lb">Libra</option>
                          <?php elseif (strpos($insumo['NombreUnidad2'], "kg")): ?>
                            <option value="kg">Kilogramo</option>
                          <?php elseif (strpos($insumo['NombreUnidad2'], "lt")): ?>
                            <option value="lt">Litro</option>
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
                </div>
              </div>
            </div>
            </div>
            </div>
          </div><!-- COLLAPSE -->
        </div><!-- /.ibox-content -->
      </div><!-- /.ibox float-e-margins -->
    </div><!-- /.col-lg-12 -->
  </div><!-- /.row -->
</div><!-- /.wrapper wrapper-content animated fadeInRight -->

<form method="POST" id="editar_insumo" action="editar_insumo.php" style="display: none;">
  <input type="hidden" name="codigoinsumoeditar" id="codigoinsumoeditar">
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

<script type="text/javascript">

$('#formInsumoEditar').find('input, textarea, button, select').prop('disabled',true);
/*
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

    $('select[name=unidadMedida]').val('<?php echo $insumo['NombreUnidad1']; ?>');
</script>

<?php mysqli_close($Link); ?>

</body>
</html>