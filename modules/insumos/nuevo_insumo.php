<?php 

$titulo = 'Nuevo insumo';
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
      <button class="btn btn-primary guardar" onclick="submitForm('formInsumo', 1);" id="segundoBtnSubmit" style=""><span class="fa fa-check"></span> Guardar</button>
    </div><!-- /.title-action -->
  </div><!-- /.col -->
</div><!-- /.row -->


<div class="wrapper wrapper-content animated fadeInRight">
  <div class="row">
    <div class="col-lg-12">
      <div class="ibox float-e-margins">
        <div class="ibox-content contentBackground">
          <div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true"><!-- COLLAPSE -->
            <div class="panel panel-default">
              <div class="panel-heading clearfix" role="tab" id="headingOne"> 
                <h4 class="panel-title"><span class="fa fa-file-text-o"></span>   Datos del insumo
                </h4>
              </div>
              <div id="producto" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="headingOne">
                <div class="panel-body">
                  <form class="form" id="formInsumo" method="post">
                    <div class="form-group col-sm-3">
                      <label>Tipo de conteo</label>
                      <select class="form-control" name="tipo_conteo" id="tipo_conteo" required>
                        <option value="">Seleccione...</option>
                        <option value="01">Contado por cupos</option>
                        <option value="02">Contado por manipuladores</option>
                        <option value="03">Contado individualmente</option>
                      </select>
                    </div>
                    <div class="form-group col-sm-3">
                      <label>Descripción</label>
                      <input type="text" name="descripcion" id="descripcion" class="form-control" placeholder="Describa el producto" required>
                      <em style="color: #cc5965; font-size: 13px; display: none;" id="existeDesc">Un producto con esta descripción ya existe.</em>
                    </div>
                    <div class="form-group col-sm-3" id="divCantPresentacion">
                      <label>Cantidad por mes </label>
                      <input type="number" name="cantidadMes" class="form-control" required>
                    </div>
                    <hr class="col-sm-11">
                    <div class="form-group col-sm-12" id="gestionMedidas" style="display: none;">
                      <label>Añadir/borrar medida presentación</label><br>
                      <label class="btn btn-primary" onclick="anadirMedida();"><span class="fa fa-plus"></span></label>
                      <label class="btn btn-danger" onclick="quitarMedida();"><span class="fa fa-minus"></span></label>
                    </div>
                    <div id="medidasPresentacion">
                      <div class="form-group col-sm-3">
                        <label>Unidad de medida</label>
                        <select class="form-control" name="unidadMedida" id="unidadMedida" required>
                          <option value="">Seleccione...</option>
                          <option value="u">Unidad</option>
                          <option value="g">Gramos</option>
                          <option value="cc">Centímetros cúbicos</option>
                        </select>
                      </div>
                      <div class="form-group col-sm-3" id="divUnidadMedidaPresentacion" style="display: none;">
                        <label>Unidad de medida de presentación 1</label>
                        <select name="unidadMedidaPresentacion[1]" id="unidadMedidaPresentacion" class="form-control unidadMedidaPresentacion">
                          <option value="">Seleccione unidad de medida.</option>
                        </select>
                      </div>
                      <div class="form-group col-sm-3" id="divCantPresentacion">
                        <label>Cantidad presentación 1</label>
                        <input type="number" min="0" name="cantPresentacion[1]" id="cantPresentacion" class="form-control" onkeyup="validaCantPresentacion(1);" required>
                        <em id="msgcp1" style="display: none;">Ordenar de mayor a menor.</em>
                      </div>
                    </div>
                  </form>
                  <div class="form-group col-sm-12">
                      <button class="btn btn-primary guardar" onclick="submitForm('formInsumo', 1);" id="segundoBtnSubmit" style=""><span class="fa fa-check"></span> Guardar</button>
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
    $('#tipoProducto').change();
</script>

<?php mysqli_close($Link); ?>

</body>
</html>