<?php
$titulo = 'Editar Suplente';
require_once '../../header.php';
$periodoActual = $_SESSION['periodoActual'];
if ($_SESSION['perfil'] == 1 || $_SESSION['perfil'] == 0) {
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
      <li>
        <a href="<?php echo $baseUrl; ?>">Inicio</a>
      </li>
      <li>
        <a href="index.php">Suplentes</a>
      </li>
      <li class="active">
        <strong><?php echo $titulo; ?></strong>
      </li>
    </ol>
  </div><!-- /.col -->
  <div class="col-lg-4">
    <div class="title-action">
      <button class="btn btn-primary" onclick="validForm(0, 0, 0);" id="segundoBtnSubmit" style="display: none;"><span class="fa fa-check"></span> Guardar</button>
    </div><!-- /.title-action -->
  </div><!-- /.col -->
</div><!-- /.row -->


<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row">
        <div class="col-lg-12">
            <div class="ibox float-e-margins">
                <div class="ibox-content contentBackground">
                <?php
                    if (isset($_POST['numDoc'])) {
                        $resultadoBuscarSuplente = $Link->query("SELECT * FROM suplentes WHERE num_doc = '".$_POST['numDoc']."'");
                        if ($resultadoBuscarSuplente->num_rows > 0) {
                            while($registroBuscarSuplente = $resultadoBuscarSuplente->fetch_assoc()) {
                                $suplente = $registroBuscarSuplente;
                            }
                        }

                        var_dump($suplente);
                    ?>
                    <form class="form row" id="formTitularEditar">
                        <div>
                            <h3>Datos del estudiante</h3>
                            <section>
                                <div class="form-group col-sm-3">
                                    <label>Tipo de documento</label>
                                    <select name="tipo_doc" class="form-control" required>
                                        <option value="">Seleccione...</option>
                                        <?php
                                            $tiposdocumento = [];
                                            $consultarTipoDocumento = "SELECT * FROM tipodocumento";
                                            $resultadoTipoDocumento = $Link->query($consultarTipoDocumento);
                                            if ($resultadoTipoDocumento->num_rows > 0) {
                                                while ($tdoc = $resultadoTipoDocumento->fetch_assoc()) {
                                                    $tiposdocumento[$tdoc['id']] = $tdoc['nombre'];
                                        ?>
                                        <option value="<?= $tdoc["id"]; ?>" data-abreviatura="<?= $tdoc["Abreviatura"]; ?>" <?= (isset($suplente) && $suplente["tipo_doc"] == $tdoc["id"]) ? "selected" : ""; ?>><?= $tdoc["nombre"]; ?></option>
                                        <?php
                                                }
                                            }/*onchange="validaNumDoc(this)"*/
                                        ?>
                                    </select>
                                    <label for="tipo_doc" class="error"></label>
                                </div>
                                <div class="form-group col-sm-3">
                                    <label>N° de documento</label>
                                    <input type="number" name="num_doc" id="num_doc" class="form-control" min="0" value="<?php echo $suplente['num_doc']; ?>" readonly>
                                    <label for="num_doc" class="error"></label>
                                </div>
                                <div class="form-group col-sm-3">
                                    <label>Primer nombre</label>
                                    <input type="text" name="nom1" class="form-control" value="<?php echo $suplente['nom1'] ?>" required>
                                    <label for="nom1" class="error"></label>
                                </div>
                                <div class="form-group col-sm-3">
                                    <label>Segundo nombre</label>
                                    <input type="text" name="nom2" value="<?php echo $suplente['nom2'] ?>" class="form-control">
                                </div>
                                <div class="form-group col-sm-3">
                                    <label>Primer apellido</label>
                                    <input type="text" name="ape1" value="<?php echo $suplente['ape1'] ?>" class="form-control" required>
                                    <label for="ape1" class="error"></label>
                                </div>
                                <div class="form-group col-sm-3">
                                    <label>Segundo apellido</label>
                                    <input type="text" name="ape2" value="<?php echo $suplente['ape2'] ?>" class="form-control">
                                </div>
                                <div class="form-group col-sm-3">
                                    <label>Género</label>
                                    <select name="genero" class="form-control" required>
                                        <option value="">Seleccione...</option>
                                        <option value="F" <?= (isset($suplente) && $suplente["genero"] == "F") ? "selected" : ""; ?>>Femenino</option>
                                        <option value="M" <?= (isset($suplente) && $suplente["genero"] == "M") ? "selected" : ""; ?>>Masculino</option>
                                    </select>
                                    <label for="genero" class="error"></label>
                                </div>
                                <div class="form-group col-sm-3">
                                    <label>Teléfono</label>
                                    <input type="number" name="telefono" value="<?php echo $suplente['telefono'] ?>" class="form-control" min="0" required>
                                    <label for="telefono" class="error"></label>
                                </div>
                                <div class="form-group col-sm-3">
                                    <label>Fecha de nacimiento</label>
                                    <input type="date" name="fecha_nac" class="form-control" max="<?php echo date('Y-m-d') ?>" value="<?php echo $suplente['fecha_nac'] ?>" required>
                                    <label for="fecha_nac" class="error"></label>
                                </div>
                                <div class="form-group col-sm-3">
                                    <label>Ciudad de nacimiento</label>
                                    <select name="cod_mun_nac" class="form-control" required>
                                        <option value="">Seleccione...</option>
                                    <?php
                                        $resultadoMunicipios = $Link->query("SELECT DISTINCT CodigoDANE, Ciudad FROM ubicacion ORDER BY Ciudad ASC");
                                        if ($resultadoMunicipios->num_rows > 0) {
                                            while ($mun = $resultadoMunicipios->fetch_assoc()) {
                                    ?>
                                        <option value="<?= $mun["CodigoDANE"] ?>" <?= (isset($suplente) && $suplente["cod_mun_nac"] == $mun["CodigoDANE"]) ? "selected" : ""; ?>><?= $mun["Ciudad"]; ?></option>
                                    <?php
                                            }
                                        }
                                    ?>
                                    </select>
                                    <label for="cod_mun_nac" class="error"></label>
                                </div>
                                <div class="form-group col-sm-3">
                                    <label>Dirección de residencia</label>
                                    <input type="text" name="dir_res" class="form-control" value="<?php echo $suplente['dir_res'] ?>" required>
                                    <label for="dir_res" class="error"></label>
                                </div>
                                <div class="form-group col-sm-3">
                                    <label>Ciudad de residencia</label>
                                    <select name="cod_mun_res" class="form-control" required>
                                        <option value="">Seleccione...</option>
                                    <?php
                                        $consultaMunicipios = "SELECT DISTINCT ubicacion.CodigoDANE, ubicacion.Ciudad
                                                FROM ubicacion, parametros
                                                WHERE
                                                    ubicacion.ETC = 0
                                                    AND ubicacion.CodigoDane LIKE CONCAT(parametros.CodDepartamento, '%')
                                                    AND EXISTS(SELECT DISTINCT
                                                                cod_mun
                                                            FROM
                                                                instituciones
                                                            WHERE
                                                                cod_mun = ubicacion.CodigoDANE)
                                                ORDER BY ubicacion.Ciudad ASC";
                                        $resultadoMunicipios = $Link->query($consultaMunicipios);
                                        if ($resultadoMunicipios->num_rows > 0) {
                                            while ($mun = $resultadoMunicipios->fetch_assoc()) {
                                    ?>
                                        <option value="<?= $mun["CodigoDANE"] ?>" <?= (isset($suplente) && $suplente["cod_mun_res"] == $mun["CodigoDANE"]) ? "selected" : ""; ?>><?= $mun["Ciudad"] ?></option>
                                    <?php
                                            }
                                        }
                                    ?>
                                    </select>
                                    <label for="cod_mun_res" class="error"></label>
                                </div>
                                <div class="form-group col-sm-3">
                                    <label>Estrato</label>
                                    <select name="cod_estrato" class="form-control" required>
                                    <?php
                                        $consultarEstrato = "SELECT * FROM estrato";
                                        $resultadoEstrato = $Link->query($consultarEstrato);
                                        if ($resultadoEstrato->num_rows > 0) {
                                            while ($est = $resultadoEstrato->fetch_assoc()) {
                                    ?>
                                        <option value="<?= $est['id'] ?>" <?= (isset($suplente) && $suplente["cod_estrato"] == $est["id"]) ? "selected" : ""; ?>><?= $est['nombre'] ?></option>
                                    <?php   }
                                        }
                                    ?>
                                    </select>
                                  <label for="cod_estrato" class="error"></label>
                                </div>
                                <div class="form-group col-sm-3">
                                    <label for="sector">Sector</label>
                                    <div class="radio" style="margin-top: 5px; margin-bottom: 0px;">
                                        <label>
                                            <input type="radio" name="sector" id="urbano" value="1" <?= (isset($suplente) && $suplente["zona_res_est"] == "1") ? "checked": ""; ?> required> Urbano
                                        </label>
                                        <label>
                                            <input type="radio" name="sector" id="rural" value="2" <?= (isset($suplente) && $suplente["zona_res_est"] == "2") ? "checked": ""; ?> required> Rural
                                        </label>
                                    </div>
                                    <label for="sector" class="error"></label>
                                </div>
                                <div class="col-sm-12">
                                    <em id="errorEst" style="display: none; font-size: 120%;"> <b>Nota : </b>Ya ha sido registrado un estudiante con el número de documento especificado en <b><span id="semanasErr"></span></b>.</em>
                                </div>
                            </section>
                            <h3>Información especial</h3>
                            <section>
                                <div class="form-group col-sm-3">
                                    <label>Puntaje SISBÉN</label>
                                    <input type="number" name="sisben" class="form-control" value="<?php echo $suplente['sisben'] ?>"  step="0.00001" min="0" required>
                                    <label for="sisben" class="error"></label>
                                </div>
                                <div class="form-group col-sm-3">
                                    <label>Discapacidad</label>
                                    <select name="cod_discap" class="form-control" required>
                                    <?php
                                        $consultarDiscapacidad = "SELECT * FROM discapacidades";
                                        $resultadoDiscapacidad = $Link->query($consultarDiscapacidad);
                                        if ($resultadoDiscapacidad->num_rows > 0) {
                                            while ($dis = $resultadoDiscapacidad->fetch_assoc()) {
                                       ?>
                                        <option value="<?= $dis['id'] ?>" <?= (isset($suplente) && $suplente["id_disp_est"] == $dis["id"]) ? "selected" : "";?>><?= $dis['nombre'] ?></option>
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
                                    <?php
                                        $consultarEtnia = "SELECT * FROM etnia";
                                        $resultadoEtnia = $Link->query($consultarEtnia);
                                        if ($resultadoEtnia->num_rows > 0) {
                                            while ($etnia = $resultadoEtnia->fetch_assoc()) {
                                    ?>
                                        <option value="<?= $etnia['ID'] ?>" <?= (isset($suplente) && $suplente['etnia'] == $etnia['ID']) ? "selected" : "";?>><?= $etnia['DESCRIPCION'] ?></option>
                                    <?php
                                            }
                                        }
                                    ?>
                                    </select>
                                    <label for="etnia" class="error"></label>
                                </div>
                                <div class="form-group col-sm-3">
                                    <label>Tipo de población víctima</label>
                                    <select name="cod_pob_victima" class="form-control" required>
                                    <?php
                                        $consultarPobVictima = "SELECT * FROM pobvictima";
                                        $resultadoPobVictima = $Link->query($consultarPobVictima);
                                        if ($resultadoPobVictima->num_rows > 0) {
                                            while ($pobVictima = $resultadoPobVictima->fetch_assoc()) {
                                        ?>
                                        <option value="<?= $pobVictima['id'] ?>" <?= (isset($suplente) && $suplente['cod_pob_victima'] == $pobVictima['id']) ? "selected" : ""; ?>><?= $pobVictima['nombre'] ?></option>
                                    <?php
                                            }
                                        }
                                    ?>
                                    </select>
                                    <label for="cod_pob_victima" class="error"></label>
                                </div>
                            </section>
                            <h3>Información académica</h3>
                            <section>
                                <div class="form-group col-sm-3">
                                    <label>Institución</label>
                                    <select name="cod_inst" id="cod_inst" class="form-control select2" onchange="obtenerSedes(this)" style="width: 100%;" required>
                                    <?php
                                        $consultarInstitucion = "SELECT instituciones.* FROM instituciones, parametros WHERE cod_mun like CONCAT(parametros.CodDepartamento, '%') AND EXISTS(SELECT cod_inst FROM sedes".$_SESSION['periodoActual']." as sedes WHERE sedes.cod_inst = instituciones.codigo_inst) ORDER BY nom_inst ASC";
                                        $resultadoInstitucion = $Link->query($consultarInstitucion);
                                        if ($resultadoInstitucion->num_rows > 0) {
                                            while ($institucion = $resultadoInstitucion->fetch_assoc()) {
                                    ?>
                                        <option value="<?= $institucion['codigo_inst'] ?>" <?= (isset($suplente) && $suplente['cod_inst'] == $institucion['codigo_inst']) ? "selected" : ""; ?>><?= $institucion['nom_inst'] ?></option>
                                    <?php
                                            }
                                        }
                                    ?>
                                    </select>
                                    <label for="cod_inst" class="error"></label>
                                </div>
                        <!-- <div class="form-group col-sm-3">
                          <label>Sede</label>
                          <select name="cod_sede" id="cod_sede" class="form-control select2" style="width: 100%;" required>
                          <?php $consultaInstParametros = "SELECT DISTINCT cod_sede, nom_sede FROM sedes".$_SESSION['periodoActual']." WHERE cod_inst = '".$suplente['cod_inst']."' ORDER BY nom_sede ASC";
                            $resultado = $Link->query($consultaInstParametros);
                            if ($resultado->num_rows > 0) {
                              while ($institucion = $resultado->fetch_assoc()) {

                                if ($suplente['cod_sede'] == $institucion['cod_sede']) {
                                  $selected = "selected";
                                } else {
                                  $selected = "";
                                }

                                ?>
                                <option value="<?php echo $institucion['cod_sede'] ?>" <?php echo $selected; ?>><?php echo $institucion['nom_sede'] ?></option>
                              <?php }
                            } ?>
                          </select>
                          <label for="cod_sede" class="error"></label>
                        </div>
                        <div class="form-group col-sm-3">
                          <label>Grado</label>
                          <select name="cod_grado" class="form-control" required>
                            <?php
                            $consultarGrados = "SELECT * FROM grados ORDER BY id ASC";
                            $resultadoGrados = $Link->query($consultarGrados);
                            if ($resultadoGrados->num_rows > 0) {
                              while ($grado = $resultadoGrados->fetch_assoc()) {

                                if ($suplente['cod_grado'] == $grado['id']) {
                                  $selected = "selected";
                                } else {
                                  $selected = "";
                                }

                                ?>
                                <option value="<?php echo $grado['id'] ?>" <?php echo $selected; ?>><?php echo $grado['nombre'] ?></option>
                              <?php }
                            }
                             ?>
                          </select>
                          <label for="cod_grado" class="error"></label>
                        </div>
                        <div class="form-group col-sm-3">
                          <label>Grupo</label>
                          <input type="text" name="nom_grupo" value="<?php echo $suplente['nom_grupo'] ?>" class="form-control" required>
                          <label for="nom_grupo" class="error"></label>
                        </div>
                        <div class="form-group col-sm-3">
                          <label>Jornada</label>
                          <select name="cod_jorn_est" class="form-control" required>
                            <?php
                            $consultarGrados = "SELECT * FROM jornada ORDER BY id ASC";
                            $resultadoGrados = $Link->query($consultarGrados);
                            if ($resultadoGrados->num_rows > 0) {
                              while ($grado = $resultadoGrados->fetch_assoc()) {

                                if ($suplente['nom_grupo'] == $grado['id']) {
                                  $selected = "selected";
                                } else {
                                  $selected = "";
                                }

                                ?>
                                <option value="<?php echo $grado['id'] ?>" <?php echo $selected; ?>><?php echo $grado['nombre'] ?></option>
                              <?php }
                            }
                             ?>
                          </select>
                          <label for="cod_jorn_est" class="error"></label>
                        </div>
                        <div class="form-group col-sm-3">
                          <label>¿Repitente?</label>
                          <select name="repitente" class="form-control" required>
                            <?php if ($suplente['repitente'] == "N"): ?>
                            <option value="N">No</option>
                            <option value="S">Si</option>
                            <?php elseif ($suplente['repitente'] == "S"): ?>
                            <option value="S">Si</option>
                            <option value="N">No</option>
                            <?php endif ?>
                          </select>
                          <label for="repitente" class="error"></label>
                        </div>
                        <div class="col-sm-12">
                          <span class="btn btn-primary" onclick="agregarSemana()"><i class="fa fa-plus"></i></span>
                          <span class="btn btn-primary" onclick="eliminarSemana()"><i class="fa fa-minus"></i></span>
                        </div>

                        <div class="form-group col-sm-12">
                          <table class="table">
                            <thead>
                              <tr>
                                <th style="width: 40%;">Semana</th>
                                <th>Tipo complemento</th>
                              </tr>
                            </thead>
                            <tbody id="semanasComplemento">
                            <?php

                            $cnt = 0;
                              for ($i=0; $i < sizeof($complemento_semana) ; $i++) {
                                foreach ($complemento_semana[$i] as $semana => $complemento) {
                                  $cnt++;
                                  ?>

                              <tr id="semana_<?php echo $cnt; ?>">
                                <input type="hidden" name="id_comp_semana[<?php echo $cnt; ?>]" value="<?php echo $id_comp_semana[$i][$semana]?>">
                                <td>
                                  <select name="semana[<?php echo $cnt; ?>]" id="semana<?php echo $cnt; ?>" onchange="validaCompSemana(this, 1)" class="form-control semana" required>
                                    <?php $consultarFocalizacion = "SELECT table_name AS tabla FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name like 'focalizacion%' ";
                                $resultadoFocalizacion = $Link->query($consultarFocalizacion);
                                if ($resultadoFocalizacion->num_rows > 0) {
                                    while ($focalizacion = $resultadoFocalizacion->fetch_assoc()) {

                                      if ($focalizacion['tabla'] == $semana) { ?>
                                        <option value="<?php echo $focalizacion['tabla']; ?>" <?php echo $selected; ?>>Semana <?php echo substr($focalizacion['tabla'], 12, 2); ?></option>
                                      <?php }
                                    }
                                  } ?>
                                  </select>
                                  <label for="#semana<?php echo $cnt; ?>" class="error"></label>
                                </td>
                                <td>
                                  <select name="tipo_complemento[<?php echo $cnt; ?>]" id="tipo_complemento<?php echo $cnt; ?>" onchange="validaCompSemana(this, 2)" class="form-control tipo_complemento" required>
                                    <option value="">Seleccione...</option>
                                    <?php
                                    $consultarGrados = "SELECT * FROM tipo_complemento ORDER BY ID ASC";
                                    $resultadoGrados = $Link->query($consultarGrados);
                                    if ($resultadoGrados->num_rows > 0) {
                                      while ($grado = $resultadoGrados->fetch_assoc()) {

                                        if ($grado['CODIGO'] == $complemento) {
                                          $selected = "selected";
                                        } else {
                                          $selected = "";
                                        }

                                        ?>
                                        <option value="<?php echo $grado['CODIGO'] ?>" <?php echo $selected; ?>><?php echo $grado['CODIGO']." (".$grado['DESCRIPCION'].")" ?></option>
                                      <?php }
                                    }
                                     ?>
                                  </select>
                                  <br>
                                  <label for="#tipo_complemento<?php echo $cnt; ?>" class="error"></label>
                                </td>
                              </tr>
                                <?php }
                              }
                            ?>
                            </tbody>
                            <tfoot>
                              <tr>
                                <th>Semana</th>
                                <th>Tipo complemento</th>
                              </tr>
                            </tfoot>
                          </table>
                        </div>
                      </section> -->
                        </div>
                    </form>
                <?php
                    } else {
                        echo "Titular no definido.";
                    }
                ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php } else { //validación usuario tipo admin ?>
    <script type="text/javascript">
      location.href="<?php echo $baseUrl ?>";
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

    var form = $("#formTitularEditar");
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
              $('#formTitularEditar').submit();
            }
        }
    });

    $('.select2').select2({ width: "resolve" });
    $('input').iCheck({ radioClass: "iradio_square-green" });
</script>

<?php mysqli_close($Link); ?>

</body>
</html>
