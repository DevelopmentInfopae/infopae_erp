<?php
$titulo = 'Impresión formato tratamiento de datos personales';
require_once '../../header.php';
$periodoActual = $_SESSION['periodoActual'];
$meses = array('01' => "Enero", "02" => "Febrero", "03" => "Marzo", "04" => "Abril", "05" => "Mayo", "06" => "Junio", "07" => "Julio", "08" => "Agosto", "09" => "Septiembre", "10" => "Octubre", "11" => "Noviembre", "12" => "Diciembre");
?>

<div class="row wrapper wrapper-content border-bottom white-bg page-heading">
  <div class="col-lg-8">
    <h2><?php echo $titulo; ?></h2>
    <ol class="breadcrumb">
      <li>
        <a href="<?php echo $baseUrl; ?>">Inicio</a>
      </li>
      <li>
        <a href="index.php">Ver dispositivos biométricos</a>
      </li>
      <li class="active">
        <strong><?php echo $titulo; ?></strong>
      </li>
    </ol>
  </div>
  <div class="col-lg-4">
    <div class="title-action">
      <button class="btn btn-primary" onclick="submitForm();" id="segundoBtnSubmit" style="display: none;"><span class="fa fa-check"></span> Guardar</button>
    </div>
  </div>
</div>

<div class="wrapper wrapper-content animated fadeInRight">
  <div class="row">
    <div class="col-lg-12">
      <div class="ibox float-e-margins">
        <div class="ibox-content contentBackground">
          <form id="form_imprimir_formato" action="imprimir_formato.php" method="post" target="_blank">
            <div class="row">
              <div class="col-xs-12">
                <div class="row">

                  <div class="col-sm-3 form-group">
                    <label class="control-label" for="mes">Mes</label>
                    <select class="form-control" name="mes" id="mes" required="required">
                      <option value="">Seleccione</option>
                      <?php
                      $consulta_meses = "SELECT DISTINCT MES AS mes FROM planilla_semanas;";
                      $respuesta_meses = $Link->query($consulta_meses) or die("Error al consultar planilla_semanas: ". $Link->error);
                      if ($respuesta_meses->num_rows > 0)
                      {
                        while ($mes = $respuesta_meses->fetch_assoc())
                        {
                      ?>
                        <option value="<?= $mes["mes"]; ?>" <?= ($mes["mes"] == date("m")) ? "selected" : ""; ?>><?= $meses[$mes["mes"]]; ?></option>
                      <?php
                        }
                      }
                      ?>
                    </select>
                  </div>

                  <div class="col-sm-3 form-group">
                    <label for="semana_inicial">Semana</label>
                    <select class="form-control" name="semana_inicial" id="semana_inicial" required="required">
                      <option value="">Seleccione</option>
                    </select>
                  </div>

                  <div class="form-group col-sm-3">
                    <label for="municipio">Municipio </label>
                    <select class="form-control" name="municipio" id="municipio" required="required">
                      <option value="">Seleccione uno</option>
                      <?php
                      $codigo_ciudad = $_SESSION['p_CodDepartamento'];
                      $consulta_municipio = "SELECT DISTINCT CodigoDANE, Ciudad FROM ubicacion where CodigoDANE LIKE '$codigo_ciudad%' ORDER BY ciudad asc;";
                      $respuesta_municipio = $Link->query($consulta_municipio) or die ('Error al consultar ubicacion'. $Link->error);
                      if($respuesta_municipio->num_rows > 0)
                      {
                        while($municipios = $respuesta_municipio->fetch_assoc())
                        {
                      ?>
                      <option value="<?php echo $municipios['CodigoDANE']; ?>" <?php if(isset($row['cod_mun']) && $row['cod_mun'] == $municipios['CodigoDANE'] || $municipio_defecto["CodMunicipio"] == $municipios['CodigoDANE']){ echo ' selected '; } ?>> <?php echo $municipios['Ciudad']; ?></option>
                      <?php
                        }
                      }
                      ?>
                    </select>
                  </div>

                  <div class="form-group col-sm-3">
                    <label class="control-label" for="institucion">Institución</label>
                    <select class="form-control" name="institucion" id="institucion" required="required">
                      <option value="">Seleccione</option>
                    </select>
                  </div>
                </div>
                <div class="row">

                  <div class="form-group col-sm-3">
                    <label class="control-label" for="sede">Sede</label>
                    <select class="form-control" name="sede" id="sede">
                      <option value="">Seleccione</option>
                    </select>
                  </div>

                </div>
              </div>
            </div>

            <div class="row">
              <div class="col-sm-3 col-lg-2">
                <button class="btn btn-primary" type="button" id="imprimir_formato"><i class="fa fa-print"></i> Imprimir</a></button>
              </div>
            </div>
          </form>
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
<script src="<?php echo $baseUrl; ?>/theme/js/plugins/select2/select2.full.min.js"></script>
<!-- Section Scripts -->
<script src="<?php echo $baseUrl; ?>/modules/dispositivos_biometricos/js/formato_datos_personales.js"></script>

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

    $('.select2').select2({
      width: "resolve"
    });
</script>

<?php mysqli_close($Link); ?>

</body>
</html>