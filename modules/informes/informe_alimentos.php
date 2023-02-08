<?php
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

$titulo = 'Informes de alimentos.';

$periodoActual = $_SESSION['periodoActual'];
$meses = array('01' => "Enero", "02" => "Febrero", "03" => "Marzo", "04" => "Abril", "05" => "Mayo", "06" => "Junio", "07" => "Julio", "08" => "Agosto", "09" => "Septiembre", "10" => "Octubre", "11" => "Noviembre", "12" => "Diciembre");

$nameLabel = get_titles('informes', 'informeAlimentos', $labels);
$titulo = $nameLabel;
?>

<div class="row wrapper wrapper-content border-bottom white-bg page-heading">
  <div class="col-lg-8">
    <h2><?= $titulo; ?></h2>
    <ol class="breadcrumb">
      <li>
        <a href="<?= $baseUrl; ?>">Inicio</a>
      </li>
      <li class="active">
        <strong><?= $titulo; ?></strong>
      </li>
    </ol>
  </div>
</div>

<div class="wrapper wrapper-content animated fadeInRight">
  <div class="row">
    <div class="col-lg-12">
      <div class="ibox float-e-margins">
        <div class="ibox-content contentBackground">
          <form class="form" id="formulario_buscar_alimentos">
            <div class="row">
              <div class="col-sm-3 form-group">
                <label class="control-label" for="mes">Mes</label>
                <select class="form-control" name="mes" id="mes" required="required">
                  <option value="">Seleccione</option>
                  <?php
                  $consulta_meses = "SELECT DISTINCT MES AS mes FROM planilla_semanas;";
                  $respuesta_meses = $Link->query($consulta_meses) or die("Error al consultar planilla_semanas: ". $Link->error);
                  if ($respuesta_meses->num_rows > 0) {
                    while ($mes = $respuesta_meses->fetch_assoc()) {
                  ?>
                    <option value="<?= $mes["mes"]; ?>" <?= ($mes["mes"] == date("m")) ? "selected" : ""; ?>><?= $meses[$mes["mes"]]; ?></option>
                  <?php
                    }
                  }
                  ?>
                </select>
              </div>

              <div class="col-sm-3 form-group">
                <label for="semana_inicial">Semana Inicial</label>
                <select class="form-control" name="semana_inicial" id="semana_inicial" required="required">
                  <option value="">Seleccione</option>
                </select>
              </div>

              <div class="col-sm-3 form-group">
                <label for="semana_final">Semana Final</label>
                <select class="form-control" name="semana_final" id="semana_final" required="required">
                  <option value="">Seleccione</option>
                </select>
              </div>

              <div class="col-sm-3 form-group">
                <label class="control-label" for="ruta">Rutas</label>
                <select class="form-control" name="ruta" id="ruta">
                  <option value="">Seleccione</option>
                  <?php
                  $consulta_rutas = "SELECT ID AS id, Nombre as nombre FROM rutas ORDER BY Nombre";
                  $respuesta_rutas = $Link->query($consulta_rutas) or die("Error al consultar rutas: ". $Link->error);
                  if ($respuesta_rutas->num_rows > 0) {
                    while ($ruta = $respuesta_rutas->fetch_assoc()) {
                  ?>
                  <option value="<?= $ruta["id"]; ?>"><?= $ruta['nombre']; ?></option>
                  <?php
                    }
                  }
                  ?>
                </select>
              </div>
            </div>

            <div class="row">
              <div class="form-group col-sm-3">
                <label class="control-label" for="municipio">Municipio</label>
                <select class="form-control" name="municipio" id="municipio">
                  <option value="">Seleccione</option>
                  <?php
                  $consulta_municipios = "SELECT CodigoDANE AS codigo, Ciudad AS municipio FROM ubicacion WHERE CodigoDANE LIKE '". $_SESSION["p_CodDepartamento"] ."%' ORDER BY Ciudad";
                  $respuesta_municipios = $Link->query($consulta_municipios) or die("Error al consultar ubicacion: ". $Link->error);
                  if ($respuesta_municipios->num_rows > 0) {
                    while ($municipio = $respuesta_municipios->fetch_assoc()) {
                  ?>
                  <option value="<?= $municipio['codigo'] ?>" <?php if ($_SESSION["p_Municipio"] == $municipio["codigo"]) { echo "selected"; } ?>><?= $municipio['municipio'] ?></option>
                  <?php
                    }
                  }
                  ?>
                </select>
              </div>

              <div class="form-group col-sm-3">
                <label class="control-label" for="institucion">Institución</label>
                <select class="form-control" name="institucion" id="institucion">
                  <option value="">Seleccione</option>
                </select>
              </div>

              <div class="form-group col-sm-3">
                <label class="control-label" for="sede">Sede</label>
                <select class="form-control" name="sede" id="sede">
                  <option value="">Seleccione</option>
                </select>
              </div>

              <div class="form-group col-sm-3">
                <label class="control-label" for="tipo_complemento">Tipo complemento</label>
                <select class="form-control" name="tipo_complemento" id="tipo_complemento">
                  <option value="">Seleccione</option>
                </select>
              </div>
            </div>

            <div class="row">
              <div class="col-sm-12">
                <button class="btn btn-primary" type="button" name="boton_buscar" id="boton_buscar"><span class="fa fa-search"></span> Buscar</button>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-lg-12">
      <div class="ibox float-e-margins">
        <div class="ibox-content contentBackground">
          <div class="table-responsive">
            <table class="table table-striped table-hover selectableRows" id="tabla_productos">
              <thead>
                <tr>
                  <th style="width: 8.33%;">Código</th>
                  <th style="width: 17.65%;">Alimento</th>
                  <th style="width: 8.33%;">Cantidad Requerida</th>
                  <th style="width: 8.33%;">Cantidad Presentación</th>
                  <th style="width: 8.33%;">Cantidad medida 1</th>
                  <th style="width: 6%;">Un. medida 1</th>
                  <th style="width: 8.33%;">Cantidad medida 2</th>
                  <th style="width: 6%;">Un. medida 2</th>
                  <th style="width: 8.33%;">Cantidad medida 3</th>
                  <th style="width: 6%;">Un. medida 3</th>
                  <th style="width: 8.33%;">Cantidad medida 4</th>
                  <th style="width: 6%;">Un. medida 4</th>
                </tr>
              </thead>
              <tbody>

              </tbody>
              <tfoot>
                <tr>
                  <th style="width: 8.33%;">Código</th>
                  <th style="width: 17.65%;">Alimento</th>
                  <th style="width: 8.33%;">Cantidad Requerida</th>
                  <th style="width: 8.33%;">Cantidad Presentación</th>
                  <th style="width: 8.33%;">Cantidad medida 1</th>
                  <th style="width: 6%;">Un. medida 1</th>
                  <th style="width: 8.33%;">Cantidad medida 2</th>
                  <th style="width: 6%;">Un. medida 2</th>
                  <th style="width: 8.33%;">Cantidad medida 3</th>
                  <th style="width: 6%;">Un. medida 3</th>
                  <th style="width: 8.33%;">Cantidad medida 4</th>
                  <th style="width: 6%;">Un. medida 4</th>
                </tr>
              </tfoot>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<?php include '../../footer.php'; ?>

<!-- Mainly scripts -->
<script src="<?= $baseUrl; ?>/theme/js/jquery-3.1.1.min.js"></script>
<script src="<?= $baseUrl; ?>/theme/js/bootstrap.min.js"></script>
<script src="<?= $baseUrl; ?>/theme/js/plugins/metisMenu/jquery.metisMenu.js"></script>
<script src="<?= $baseUrl; ?>/theme/js/plugins/slimscroll/jquery.slimscroll.min.js"></script>
<script src="<?= $baseUrl; ?>/theme/js/plugins/dataTables/datatables.min.js"></script>

<!-- Custom and plugin javascript -->
<script src="<?= $baseUrl; ?>/theme/js/inspinia.js"></script>
<script src="<?= $baseUrl; ?>/theme/js/plugins/pace/pace.min.js"></script>
<script src="<?= $baseUrl; ?>/theme/js/plugins/toastr/toastr.min.js"></script>
<script src="<?= $baseUrl; ?>/theme/js/plugins/validate/jquery.validate.min.js"></script>
<script src="<?= $baseUrl; ?>/theme/js/plugins/iCheck/icheck.min.js"></script>
<script src="<?= $baseUrl; ?>/theme/js/plugins/datapicker/bootstrap-datepicker.js"></script>

<!-- Section Scripts -->
<script src="<?= $baseUrl; ?>/modules/informes/js/informes.js"></script>

<?php mysqli_close($Link); ?>

</body>
</html>