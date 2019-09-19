<?php
  require_once '../../header.php';

  $titulo = 'Nuevo suplente';
  $periodoActual = $_SESSION['periodoActual'];

  if ($_SESSION['perfil'] == 1 || $_SESSION['perfil'] == 0)
  {
    $codigo_departamento = $_SESSION['p_CodDepartamento'];
    $codigo_municipio = $_SESSION['p_Municipio'];
?>

<style type="text/css">
  .wizard .content{
    min-height: 40em;
    overflow-y: auto;
  }
  #loader{
    display: block;
  }
</style>

<div class="row wrapper wrapper-content border-bottom white-bg page-heading">
  <div class="col-lg-8">
  <h2><?php echo $titulo; ?></h2>
    <ol class="breadcrumb">
      <li><a href="<?php echo $baseUrl; ?>">Inicio</a></li>
      <li><a href="index.php">Suplentes</a></li>
      <li class="active"><strong><?php echo $titulo; ?></strong></li>
    </ol>
  </div>
  <div class="col-lg-4">
    <div class="title-action">
      <button class="btn btn-primary" onclick="validForm(0, 0, 0);" id="segundoBtnSubmit" style="display: none;"><span class="fa fa-check"></span> Guardar</button>
    </div>
  </div>
</div>


<div class="wrapper wrapper-content animated fadeInRight">
  <div class="row">
    <div class="col-lg-12">
      <div class="ibox float-e-margins">
        <div class="ibox-content contentBackground">
          <form class="form row" id="formulario_crear_suplente">
            <div>
              <h3>Datos del estudiante</h3>
              <section style="width: 100%;">
                <div class="row">
                  <div class="form-group col-sm-3">
                    <label>Tipo de documento</label>
                    <select name="tipo_doc" id="tipo_doc" class="form-control" required>
                      <option value="">seleccione</option>
                      <?php
                        $consultarTipoDocumento = "SELECT * FROM tipodocumento";
                        $resultadoTipoDocumento = $Link->query($consultarTipoDocumento);
                        if ($resultadoTipoDocumento->num_rows > 0)
                        {
                          while ($tdoc = $resultadoTipoDocumento->fetch_assoc())
                          {
                      ?>
                            <option value="<?= $tdoc['id'] ?>" data-abreviatura='<?= $tdoc["Abreviatura"]; ?>'><?= $tdoc['nombre'] ?></option>
                      <?php
                          }
                        }
                      ?>
                    </select>
                    <input type="hidden" name="abreviatura" id="abreviatura">
                    <label for="tipo_doc" class="error"></label>
                  </div>
                  <div class="form-group col-sm-3">
                    <label>N° de documento</label>
                    <input type="number" name="num_doc" id="num_doc" class="form-control" min="0" required>
                    <label for="num_doc" class="error"></label>
                  </div>
                  <div class="form-group col-sm-3">
                    <label>Fecha de nacimiento</label>
                    <input type="date" name="fecha_nac" class="form-control" max="<?php echo date('Y-m-d') ?>" required>
                    <label for="fecha_nac" class="error"></label>
                  </div>
                  <div class="form-group col-sm-3">
                    <label>Ciudad de nacimiento</label>
                    <select name="cod_mun_nac" class="form-control" required>
                      <option value="">seleccione</option>
                      <?php
                        $consulta_municipios = "SELECT DISTINCT CodigoDANE, Ciudad FROM ubicacion ORDER BY Ciudad ASC";
                        $resultadoMunicipios = $Link->query($consulta_municipios);
                        if ($resultadoMunicipios->num_rows > 0)
                        {
                          while ($municipio = $resultadoMunicipios->fetch_assoc())
                          {
                             echo '<option value="'.$municipio["CodigoDANE"].'">'.ucfirst(mb_strtolower($municipio["Ciudad"])).'</option>';
                          }
                        }
                      ?>
                    </select>
                    <label for="cod_mun_nac" class="error"></label>
                  </div>
                </div>
                <div class="row">
                  <div class="form-group col-sm-3">
                    <label>Primer nombre</label>
                    <input type="text" name="nom1" class="form-control" required>
                    <label for="nom1" class="error"></label>
                  </div>
                  <div class="form-group col-sm-3">
                    <label>Segundo nombre</label>
                    <input type="text" name="nom2" class="form-control">
                  </div>
                  <div class="form-group col-sm-3">
                    <label>Primer apellido</label>
                    <input type="text" name="ape1" class="form-control" required>
                    <label for="ape1" class="error"></label>
                  </div>
                  <div class="form-group col-sm-3">
                    <label>Segundo apellido</label>
                    <input type="text" name="ape2" class="form-control">
                  </div>
                </div>
                <div class="row">
                  <div class="form-group col-sm-3">
                    <label>Género</label>
                    <select name="genero" class="form-control" required>
                      <option value="">seleccione</option>
                      <option value="M">Masculino</option>
                      <option value="F">Femenino</option>
                    </select>
                    <label for="genero" class="error"></label>
                  </div>
                  <div class="form-group col-sm-3">
                    <label>Teléfono</label>
                    <input type="number" name="telefono" class="form-control" min="0" required>
                    <label for="telefono" class="error"></label>
                  </div>
                  <div class="form-group col-sm-3">
                    <label>Dirección de residencia</label>
                    <input type="text" name="dir_res" class="form-control" required>
                    <label for="dir_res" class="error"></label>
                  </div>
                  <div class="form-group col-sm-3">
                    <label>Ciudad de residencia</label>
                    <select name="cod_mun_res" class="form-control" required>
                      <option value="">seleccione</option>
                      <?php
                        $consultaMunicipios = "SELECT DISTINCT
                                                  ubicacion.CodigoDANE, ubicacion.Ciudad
                                              FROM
                                                  ubicacion,
                                                  parametros
                                              WHERE
                                                  ubicacion.ETC = 0
                                                  AND ubicacion.CodigoDane LIKE CONCAT(parametros.CodDepartamento, '%')
                                                  AND EXISTS( SELECT DISTINCT
                                                      cod_mun
                                                  FROM
                                                      instituciones
                                                  WHERE
                                                      cod_mun = ubicacion.CodigoDANE)
                                              ORDER BY ubicacion.Ciudad ASC";
                        $ciudades="";
                        $resultadoMunicipios = $Link->query($consultaMunicipios);
                        if ($resultadoMunicipios->num_rows > 0) {
                          while ($municipio = $resultadoMunicipios->fetch_assoc()) {
                            echo '<option value="'.$municipio["CodigoDANE"].'">'.ucfirst(mb_strtolower($municipio["Ciudad"])).'</option>';
                          }
                        }
                      ?>
                    </select>
                    <label for="cod_mun_res" class="error"></label>
                  </div>
                </div>
                <div class="row">
                  <div class="form-group col-sm-3">
                    <label>Estrato</label>
                    <select name="cod_estrato" class="form-control" required>
                      <option value="">seleccione</option>
                      <?php
                        $consultarEstrato = "SELECT * FROM estrato";
                        $resultadoEstrato = $Link->query($consultarEstrato);
                        if ($resultadoEstrato->num_rows > 0)
                        {
                          while ($estrato = $resultadoEstrato->fetch_assoc())
                          {
                        ?>
                            <option value="<?php echo $estrato['id'] ?>"><?php echo $estrato['nombre'] ?></option>
                      <?php
                          }
                        }
                      ?>
                    </select>
                    <label for="cod_estrato" class="error"></label>
                  </div>
                  <div class="form-group col-sm-3">
                    <label for="sector">Sector</label>
                    <div class="radio" style="margin-top: 5px; margin-bottom: 0px;">
                      <label>
                        <input type="radio" name="sector" id="urbano" value="1" checked required> Urbano
                      </label>
                      <label>
                        <input type="radio" name="sector" id="rural" value="2" required> Rural
                      </label>
                    </div>
                    <label for="sector" class="error"></label>
                  </div>
                </div>
                <div class="col-sm-12">
                    <em id="errorEst" style="display: none; font-size: 120%;" class="text-danger"> <b>Nota : </b>Ya ha sido registrado un estudiante con el número de documento especificado en <b><span id="semanasErr"></span></b>.</em>
                </div>
              </section>


              <h3>Información especial</h3>
              <section style="width: 100%;">
                <div class="row">
                  <div class="form-group col-sm-3">
                    <label>Puntaje SISBÉN</label>
                    <input type="number" name="sisben" class="form-control" step="0.00001" min="0" required>
                    <label for="sisben" class="error"></label>
                  </div>
                  <div class="form-group col-sm-3">
                    <label>Discapacidad</label>
                    <select name="cod_discap" class="form-control" required>
                      <option value="">seleccione</option>
                      <?php
                      $consultarDiscapacidad = "SELECT * FROM discapacidades";
                      $resultadoDiscapacidad = $Link->query($consultarDiscapacidad);
                      if ($resultadoDiscapacidad->num_rows > 0)
                      {
                        while ($discapacidad = $resultadoDiscapacidad->fetch_assoc())
                        { ?>
                          <option value="<?php echo $discapacidad['id'] ?>"><?php echo $discapacidad['nombre'] ?></option>
                      <?php
                        }
                      }
                      ?>
                    </select>
                    <label for="cod_discap" class="error"></label>
                  </div>
                  <div class="form-group col-sm-3">
                    <label>Etnia</label>
                    <select name="etnia" class="form-control" required>
                      <option value="">seleccione</option>
                      <?php
                      $consultarDiscapacidad = "SELECT * FROM etnia";
                      $resultadoDiscapacidad = $Link->query($consultarDiscapacidad);
                      if ($resultadoDiscapacidad->num_rows > 0) {
                        while ($discapacidad = $resultadoDiscapacidad->fetch_assoc()) { ?>
                          <option value="<?php echo $discapacidad['ID'] ?>"><?php echo $discapacidad['DESCRIPCION'] ?></option>
                       <?php }
                      }
                      ?>
                    </select>
                    <label for="etnia" class="error"></label>
                  </div>
                  <div class="form-group col-sm-3">
                    <label>Tipo de población víctima</label>
                    <select name="cod_pob_victima" class="form-control" required>
                      <option value="">seleccione</option>
                      <?php
                      $consultarDiscapacidad = "SELECT * FROM pobvictima";
                      $resultadoDiscapacidad = $Link->query($consultarDiscapacidad) or die("Error al consultar poblacion victima: ". $Link->error);
                      if ($resultadoDiscapacidad->num_rows > 0)
                      {
                        while ($discapacidad = $resultadoDiscapacidad->fetch_assoc())
                        { ?>
                          <option value="<?php echo $discapacidad['id'] ?>"><?php echo $discapacidad['nombre'] ?></option>
                       <?php
                        }
                      }
                      ?>
                    </select>
                    <label for="cod_pob_victima" class="error"></label>
                  </div>
                </div>
              </section>


              <h3>Información académica</h3>
              <section>
                <div class="row">
                  <div class="form-group col-sm-3">
                      <label>Municipio</label>
                      <select name="cod_mun" id="cod_mun" class="form-control select2" onchange="buscar_instituciones(this.value)" style="width: 100%;" required>
                          <option value="">seleccione</option>
                          <?php
                            $consulta_municipios = "SELECT ubicacion.* FROM ubicacion WHERE CodigoDANE like '$codigo_departamento%'";
                            if ($codigo_municipio != 0) { $consulta_municipios .= " AND CodigoDANE = '$codigo_municipio'"; }
                            $consulta_municipios .= " ORDER BY Ciudad";
                            $resultadoMunicipio = $Link->query($consulta_municipios);
                            if ($resultadoMunicipio->num_rows > 0)
                            {
                              while ($municipio = $resultadoMunicipio->fetch_assoc())
                              { ?>
                                <option value="<?= $municipio['CodigoDANE'] ?>" <?= ($municipio['CodigoDANE'] == $codigo_municipio) ? 'selected' : '' ?>><?php echo $municipio['Ciudad'] ?></option>
                          <?php
                              }
                            }
                          ?>
                      </select>
                      <label for="cod_inst" class="error"></label>
                  </div>
                  <div class="form-group col-sm-3">
                    <label>Institución</label>
                    <select name="cod_inst" id="cod_inst" class="form-control select2" onchange="buscar_sedes(this.value)" style="width: 100%;" required>
                      <option value="">seleccione</option>
                      <?php
                        if ($codigo_municipio != 0)
                        {
                          $consulta_instituciones = "SELECT codigo_inst AS codigo, nom_inst AS nombre FROM instituciones WHERE cod_mun = '$codigo_municipio' ORDER BY nom_inst ASC";
                          $respuesta_instituciones = $Link->query($consulta_instituciones);
                          if ($respuesta_instituciones->num_rows > 0)
                          {
                            while($institucion = $respuesta_instituciones->fetch_assoc())
                            { ?>
                                <option value="<?= $institucion['codigo'] ?>"><?= $institucion['nombre'] ?></option>
                      <?php
                            }

                          }
                        }
                      ?>
                    </select>
                    <input type="hidden" name="nombre_institucion" id="nombre_institucion">
                    <label for="cod_inst" class="error"></label>
                  </div>
                  <div class="form-group col-sm-3">
                      <label>Sede</label>
                      <select name="sede" id="sede" class="form-control select2" onchange="obtener_nombre_sede();" style="width: 100%;" required>
                          <option>seleccione</option>
                      </select>
                      <input type="hidden" name="nombre_sede" id="nombre_sede">
                      <label for="sede" class="error"></label>
                  </div>
                  <div class="form-group col-sm-3">
                    <label>Grado</label>
                    <select name="cod_grado" class="form-control" required>
                      <option>seleccione</option>
                      <?php
                      $consultarGrados = "SELECT * FROM grados ORDER BY id ASC";
                      $resultadoGrados = $Link->query($consultarGrados);
                      if ($resultadoGrados->num_rows > 0) {
                        while ($grado = $resultadoGrados->fetch_assoc()) { ?>
                          <option value="<?php echo $grado['id'] ?>"><?php echo $grado['nombre'] ?></option>
                        <?php }
                      }
                       ?>
                    </select>
                    <label for="cod_grado" class="error"></label>
                  </div>
                </div>
                <div class="row">
                  <div class="form-group col-sm-3">
                    <label>Grupo</label>
                    <input type="text" name="nom_grupo" class="form-control" required>
                    <label for="nom_grupo" class="error"></label>
                  </div>
                  <div class="form-group col-sm-3">
                    <label>Jornada</label>
                    <select name="cod_jorn_est" class="form-control" required>
                      <option>seleccione</option>
                      <?php
                      $consultarGrados = "SELECT * FROM jornada ORDER BY id ASC";
                      $resultadoGrados = $Link->query($consultarGrados);
                      if ($resultadoGrados->num_rows > 0) {
                        while ($grado = $resultadoGrados->fetch_assoc()) { ?>
                          <option value="<?php echo $grado['id'] ?>"><?php echo $grado['nombre'] ?></option>
                        <?php }
                      }
                       ?>
                    </select>
                    <label for="cod_jorn_est" class="error"></label>
                  </div>
                  <div class="form-group col-sm-3">
                    <label>Semana</label>
                    <select class="form-control" id="semana" name="semana">
                      <option value="">seleccione</option>
                      <?php
                        $consulta_semanas = "SELECT TABLE_NAME AS tabla
                                            FROM INFORMATION_SCHEMA.TABLES
                                            WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME LIKE 'suplentes%'";
                        $resultado_semanas = $Link->query($consulta_semanas) or die ('Error al consultar planilla_semanas: '. $Link->error);
                        if($resultado_semanas->num_rows > 0)
                        {
                          while($semana = $resultado_semanas->fetch_assoc())
                          {
                            $nombre_semana = str_replace('suplentes', '', $semana["tabla"]);
                      ?>
                            <option value="<?= $nombre_semana; ?>" <?php if(isset($_POST['semana']) && $_POST['semana'] == $nombre_semana){ echo " selected "; } ?>><?= $nombre_semana; ?></option>
                      <?php
                          }
                        }
                      ?>
                    </select>
                    <label for="sisben" class="error"></label>
                  </div>
                  <div class="form-group col-sm-3">
                    <label>¿Repitente?</label>
                    <select name="repitente" class="form-control" required>
                      <option>seleccione</option>
                      <option value="N">No</option>
                      <option value="S">Si</option>
                    </select>
                    <label for="repitente" class="error"></label>
                  </div>
                </div>
              </section>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>

<?php } else { ?>
    <script type="text/javascript">
      location.href="<?= $baseUrl ?>";
    </script>
<?php } ?>

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
<script src="<?php echo $baseUrl; ?>/modules/suplentes/js/suplentes.js"></script>

<script type="text/javascript">
  var form = $("#formulario_crear_suplente");
  form.validate({
    errorPlacement: function errorPlacement(error, element) { element.before(error); },
    rules: {
      confirm: {
        equalTo: "#password"
      }
    }
  });

  form.children("div").steps({
    headerTag: "h3",
    bodyTag: "section",
    transitionEffect: "slideLeft",
    labels: {
      cancel: "Cancelar",
      current: "Paso actual:",
      pagination: "Paginación",
      finish: "Guardar",
      next: "Siguiente",
      previous: "Anterior",
      loading: "Loading ..."
    },
    onStepChanging: function (event, currentIndex, newIndex)
    {
      form.validate().settings.ignore = ":disabled,:hidden";
      return form.valid();
    },
    onFinishing: function (event, currentIndex)
    {
      form.validate().settings.ignore = ":disabled";
      return form.valid();
    },
    onFinished: function (event, currentIndex)
    {
      form.validate().settings.ignore = ":disabled";
      if (form.valid()) {
        $('#formulario_crear_suplente').submit();
      }
    }
  });

  $('.select2').select2({ width: "resolve" });
  $('input').iCheck({ radioClass: "iradio_square-green" });
</script>

<?php mysqli_close($Link); ?>

</body>
</html>
