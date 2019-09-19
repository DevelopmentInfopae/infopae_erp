<?php 
$titulo = 'Nueva infraestructura';
require_once '../../header.php'; 
$periodoActual = $_SESSION['periodoActual'];

if ($_SESSION['perfil'] == 1 || $_SESSION['perfil'] == 0) {
?>

<div class="row wrapper wrapper-content border-bottom white-bg page-heading">
  <div class="col-lg-8">
    <h2><?php echo $titulo; ?></h2>
    <ol class="breadcrumb">
      <li>
        <a href="<?php echo $baseUrl; ?>">Inicio</a>
      </li>
      <li>
        <a href="index.php">Ver infraestructuras</a>
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
          <div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true"><!-- COLLAPSE -->
            <div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
  <div class="panel panel-default">
    <div class="panel-heading" role="tab" id="headin_1">
      <h4 class="panel-title">
        <a class="pull-right" role="button" data-toggle="collapse" data-parent="#accordion" href="#1" aria-expanded="true" aria-controls="1" style="color: #337ab7; display: none;" id="btnEditar_1"> Editar
        </a>
        Datos de la infraestructura
      </h4>
    </div>
    <div id="1" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="1">
      <div class="panel-body">
        <form class="form row" id="formInfraestructura">
          <div class="form-group col-sm-3">
            <label>Municipio</label>
            <select class="form-control" name="cod_municipio" id="cod_municipio" required>
              <option value="">Seleccione...</option>
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
              $resultadoMunicipios = $Link->query($consultaMunicipios);
              if ($resultadoMunicipios->num_rows > 0) {
                while ($municipio = $resultadoMunicipios->fetch_assoc()) { ?>
                  <option value="<?php echo $municipio['CodigoDANE']; ?>"><?php echo ucfirst(mb_strtolower($municipio['Ciudad'])); ?></option>
                <?php }
              }
               ?>
            </select>
          </div>
          <div class="form-group col-sm-3">
            <label>Institución</label>
            <select class="form-control select2" name="cod_inst" id="cod_inst" required>
              <option value="">Seleccione municipio</option>
            </select>
          </div>
          <div class="form-group col-sm-3">
            <label>Sede</label>
            <select class="form-control select2" name="cod_sede" id="cod_sede" required>
              <option value="">Seleccione institución</option>
            </select>
            <input type="hidden" name="nom_sede" id="nom_sede">
          </div>
          <?php 
              $opciones = "";
              $consultaModalidadSuministro = "SELECT * FROM modalidad_suministro";
              $resultadoModalidadSuministro = $Link->query($consultaModalidadSuministro);
              if ($resultadoModalidadSuministro->num_rows > 0) {
                while ($modalidadSuministro = $resultadoModalidadSuministro->fetch_assoc()) { 
                  $opciones.='<option value="'.$modalidadSuministro["id"].'">'.ucfirst(mb_strtolower($modalidadSuministro["Descripcion"])).'</option>';
                 }
              }
               ?>
          <div class="form-group col-sm-3">
            <label>Complemento JM/JT</label>
            <select class="form-control" name="id_Complem_JMJT" id="id_Complem_JMJT" required>
              <option value="">Seleccione...</option>
              <?php echo $opciones; ?>
            </select>
          </div>
          <div class="form-group col-sm-3">
            <label>Almuerzo</label>
            <select class="form-control" name="id_Almuerzo" id="id_Almuerzo" required>
              <option value="">Seleccione...</option>
              <?php echo $opciones; ?>
            </select>
          </div>
          <div class="form-group col-sm-3">
            <label>Concepto sanitario</label>
            <select class="form-control" name="Concepto_Sanitario" id="Concepto_Sanitario" required>
              <option value="">Seleccione...</option>
              <option value="1">Favorable</option>
              <option value="2">Favorable con requerimiento</option>
              <option value="0">Desfavorable</option>
            </select>
          </div>
          <div class="form-group col-sm-3">
            <label>Fecha de expedición</label>
            <input type="date" class="form-control" name="fecha_expedicion" id="fecha_expedicion" required>
          </div>
          <div class="form-group col-sm-3">
            <label>¿Atención mayoritariamente a indigenas?</label><br>
            <div class="radio">
            <label><input type="radio" name="Atencion_MayoritariaI" id="Atencion_MayoritariaI" value="1" required> Si</label>
            <label><input type="radio" name="Atencion_MayoritariaI" id="Atencion_MayoritariaI" value="0" required> No </label>
            <label><input type="radio" name="Atencion_MayoritariaI" id="Atencion_MayoritariaI" value="2" required> No aplica</label>
            </div>
            <label for="Atencion_MayoritariaI" class="error"></label>
          </div>
          <div class="form-group col-sm-3">
            <label>¿Cuenta con comedor escolar?</label><br>
            <div class="radio">
            <label><input type="radio" name="Comedor_Escolar" id="Comedor_Escolar" value="1" required> Si</label>
            <label><input type="radio" name="Comedor_Escolar" id="Comedor_Escolar" value="0" required> No</label>
            </div>
            <label for="Comedor_Escolar" class="error"></label>
          </div>
          <br>
          <div class="form-group col-sm-6">
            <label>Observaciones</label> <em id="maxLongObservaciones">(500)</em>
            <textarea class="form-control" name="observaciones" id="observaciones" style="resize: none;" maxlength="500" required></textarea>
          </div>
        </form>
        <div class="col-sm-3">
          <button class="btn btn-primary" onclick="validForm('formInfraestructura', 1, 2);"><span class="fa fa-arrow-right"></span>  Siguiente</button>
        </div>
      </div>
    </div>
  </div>
    <?php 

    $dotaciones = array();

    $consultarDotacion = "SELECT * FROM dotacion";
    $resultadoDotacion = $Link->query($consultarDotacion);
    if ($resultadoDotacion->num_rows > 0) {
      while ($dotacion = $resultadoDotacion->fetch_assoc()) { 
        $dotaciones[$dotacion['id_parametroInf']][$dotacion['id']] = $dotacion['descripcion'];
      }
    }
    ?>

<?php 
$consultarParametrosInfraestructura = "SELECT * FROM parametros_infraestructura"; 
$resultadoParametrosInfraestructura = $Link->query($consultarParametrosInfraestructura);
if ($resultadoParametrosInfraestructura->num_rows > 0) {
  $countParametros = $resultadoParametrosInfraestructura->num_rows;
  $cntParametro = 2;
  while ($parametro = $resultadoParametrosInfraestructura->fetch_assoc()) { 
    
    ?>
  <div class="panel panel-default">
    <div class="panel-heading" role="tab" id="heading_<?php echo $cntParametro ?>">
      <h4 class="panel-title">
        <a class="collapsed pull-right" role="button" data-toggle="collapse" data-parent="#accordion" href="#<?php echo $cntParametro ?>" aria-expanded="false" aria-controls="<?php echo $cntParametro ?>" id="btnEditar_<?php echo $cntParametro ?>" style="color: #337ab7; display: none;"> Editar
        </a>
        Datos de <?php echo mb_strtolower($parametro['descripcion']); ?>
      </h4>

    </div>

    <div id="<?php echo $cntParametro ?>" class="panel-collapse collapse" role="tabpanel" aria-labelledby="heading_<?php echo $cntParametro ?>">
      <div class="panel-body">
        <form class="form" id="form_<?php echo $parametro['id']; ?>">
          <input type="hidden" name="id_parametro[]" value="<?php echo $parametro['id'] ?>">
          <?php if ($parametro['id'] >= 1 && $parametro['id'] <= 3): //Sólo parámetros donde se preguntan material de infraestructura ?>
              <div class="col-sm-3">
                <label>Material piso</label>
                <input type="text" name="material_piso[<?php echo $parametro['id'] ?>]" id="material_piso" class="form-control" required>
              </div>
              <div class="col-sm-3">
                <label>Material paredes</label>
                <input type="text" name="material_paredes[<?php echo $parametro['id'] ?>]" id="material_paredes" class="form-control" required>
              </div>
              <div class="col-sm-3">
                <label>Material techo</label>
                <input type="text" name="material_techo[<?php echo $parametro['id'] ?>]" id="material_techo" class="form-control" required>
              </div>
              <?php if ($parametro['id'] != 3): ?>
                <div class="col-sm-3">
                  <label>Material mesones</label>
                  <input type="text" name="material_mesones[<?php echo $parametro['id'] ?>]" id="material_mesones" class="form-control" required>
                </div>
              <?php endif ?>
          <?php endif ?>
              <?php if ($parametro['id'] == 2): ?>
                <div class="col-sm-3">
                  <label>¿Utensilios suficientes para preparacion de alimentos?</label>
                  <div class="radio">
                  <label><input type="radio" name="utensilios_suficientes[<?php echo $parametro['id'] ?>]" id="utensilios_suficientes" value="1" required> Si</label>
                  <label><input type="radio" name="utensilios_suficientes[<?php echo $parametro['id'] ?>]" id="utensilios_suficientes" value="0" required> No</label>
                  </div>
                  <label for="utensilios_suficientes[<?php echo $parametro['id'] ?>]" class="error"></label>
                </div>
              <?php endif ?>
              <?php if ($parametro['id'] == 3): ?>
                <div class="col-sm-3">
                  <label>¿Mesas y sillas suficientes para el programa?</label>
                  <div class="radio">
                  <label><input type="radio" name="mesas_sillas_suficientes[<?php echo $parametro['id'] ?>]" id="mesas_sillas_suficientes" value="1" required> Si</label>
                  <label><input type="radio" name="mesas_sillas_suficientes[<?php echo $parametro['id'] ?>]" id="mesas_sillas_suficientes" value="0" required> No</label>
                  </div>
                  <label for="mesas_sillas_suficientes[<?php echo $parametro['id'] ?>]" class="error"></label>
                </div>
                <div class="col-sm-3">
                  <label>¿Utensilios suficientes para el consumo de alimentos?</label>
                  <div class="radio">
                  <label><input type="radio" name="utensilios_suficientes[<?php echo $parametro['id'] ?>]" id="utensilios_suficientes" value="1" required> Si</label>
                  <label><input type="radio" name="utensilios_suficientes[<?php echo $parametro['id'] ?>]" id="utensilios_suficientes" value="0" required> No</label>
                  </div>
                  <label for="utensilios_suficientes[<?php echo $parametro['id'] ?>]" class="error"></label>
                </div>
              <?php endif ?>
              <?php if ($parametro['id'] == 4): ?>
                <div class="col-sm-3">
                  <label>Energia</label>
                  <input type="text" name="energia[<?php echo $parametro['id'] ?>]" id="energia" class="form-control" required>
                </div>
                <div class="col-sm-3">
                  <label>Agua potable</label>
                  <input type="text" name="agua[<?php echo $parametro['id'] ?>]" id="agua" class="form-control" required>
                </div>
                <div class="col-sm-3">
                  <label>Acueducto</label>
                  <div class="radio">
                  <label><input type="radio" name="acueducto[<?php echo $parametro['id'] ?>]" id="acueducto" value="1" required> Si</label>
                  <label><input type="radio" name="acueducto[<?php echo $parametro['id'] ?>]" id="acueducto" value="0" required> No</label>
                  <label><input type="radio" name="acueducto[<?php echo $parametro['id'] ?>]" id="acueducto" value="2" required> No aplica</label>
                  </div>
                  <label for="acueducto[<?php echo $parametro['id'] ?>]" class="error"></label>
                </div>
                <div class="col-sm-3">
                  <label>Alcantarillado</label>
                  <div class="radio">
                  <label><input type="radio" name="alcantarillado[<?php echo $parametro['id'] ?>]" id="alcantarillado" value="1" required> Si</label>
                  <label><input type="radio" name="alcantarillado[<?php echo $parametro['id'] ?>]" id="alcantarillado" value="0" required> No</label>
                  <label><input type="radio" name="alcantarillado[<?php echo $parametro['id'] ?>]" id="alcantarillado" value="2" required> No aplica</label>
                  </div>
                  <label for="alcantarillado[<?php echo $parametro['id'] ?>]" class="error"></label>
                </div>
                <div class="col-sm-3">
                  <label>Gas</label>
                  <div class="radio">
                  <label><input type="radio" name="gas[<?php echo $parametro['id'] ?>]" id="gas" value="1" required> Si</label>
                  <label><input type="radio" name="gas[<?php echo $parametro['id'] ?>]" id="gas" value="0" required> No</label>
                  <label><input type="radio" name="gas[<?php echo $parametro['id'] ?>]" id="gas" value="2" required> No aplica</label>
                  </div>
                  <label for="gas[<?php echo $parametro['id'] ?>]" class="error"></label>
                </div>
                <div class="col-sm-3">
                  <label>Almacenamiento de agua</label>
                  <input type="text" name="almacenamiento_agua[<?php echo $parametro['id'] ?>]" id="almacenamiento_agua" class="form-control" required>
                </div>
              <?php endif ?>
              <?php if ($parametro['id'] == 5): ?>
                <div class="col-sm-3">
                  <label>¿Área de almacenamiento temporal?</label>
                  <div class="radio">
                  <label><input type="radio" name="area_alm[<?php echo $parametro['id'] ?>]" id="area_alm" value="1" required> Si</label>
                  <label><input type="radio" name="area_alm[<?php echo $parametro['id'] ?>]" id="area_alm" value="0" required> No</label>
                  <label><input type="radio" name="area_alm[<?php echo $parametro['id'] ?>]" id="area_alm" value="2" required> No aplica</label>
                  </div>
                  <label for="alcantarillado[<?php echo $parametro['id'] ?>]" class="error"></label>
                </div>
                <div class="col-sm-3">
                  <label>Disposición final de residuos</label>
                  <input type="text" name="final_residuos[<?php echo $parametro['id'] ?>]" id="final_residuos" class="form-control" required>
                </div>
              <?php endif ?>
              <?php if ($parametro['id'] == 6): ?>
                <div class="col-sm-3">
                  <label>¿Área de lavado de manos?</label>
                  <div class="radio">
                  <label><input type="radio" name="lavado_manos[<?php echo $parametro['id'] ?>]" id="lavado_manos" value="1" required> Si</label>
                  <label><input type="radio" name="lavado_manos[<?php echo $parametro['id'] ?>]" id="lavado_manos" value="0" required> No</label>
                  <label><input type="radio" name="lavado_manos[<?php echo $parametro['id'] ?>]" id="lavado_manos" value="2" required> No aplica</label>
                  </div>
                  <label for="lavado_manos[<?php echo $parametro['id'] ?>]" class="error"></label>
                </div>
                <div class="col-sm-3">
                  <label>Estado</label>
                  <input type="text" name="estado_lavadomanos[<?php echo $parametro['id'] ?>]" id="estado_lavadomanos" class="form-control" required>
                </div>
                <div class="col-sm-3">
                  <label>¿Implementos de aseo necesarios?</label>
                  <div class="radio">
                  <label><input type="radio" name="manos_implemento_aseo[<?php echo $parametro['id'] ?>]" id="manos_implemento_aseo" value="1" required> Si</label>
                  <label><input type="radio" name="manos_implemento_aseo[<?php echo $parametro['id'] ?>]" id="manos_implemento_aseo" value="0" required> No</label>
                  </div>
                  <label for="manos_implemento_aseo[<?php echo $parametro['id'] ?>]" class="error"></label>
                </div>
                <div class="col-sm-3">
                  <label>¿Baño exclusivo para manipuladores?</label>
                  <div class="radio">
                  <label><input type="radio" name="bano_manipuladoras[<?php echo $parametro['id'] ?>]" id="bano_manipuladoras" value="1" required> Si</label>
                  <label><input type="radio" name="bano_manipuladoras[<?php echo $parametro['id'] ?>]" id="bano_manipuladoras" value="0" required> No</label>
                  </div>
                  <label for="bano_manipuladoras[<?php echo $parametro['id'] ?>]" class="error"></label>
                </div>
                <div class="col-sm-3">
                  <label>Estado</label>
                  <input type="text" name="estado_bano[<?php echo $parametro['id'] ?>]" id="estado_bano" class="form-control" required>
                </div>
                <div class="col-sm-3">
                  <label>¿Implementos de aseo necesarios?</label>
                  <div class="radio">
                  <label><input type="radio" name="bano_implemento_aseo[<?php echo $parametro['id'] ?>]" id="bano_implemento_aseo" value="1" required> Si</label>
                  <label><input type="radio" name="bano_implemento_aseo[<?php echo $parametro['id'] ?>]" id="bano_implemento_aseo" value="0" required> No</label>
                  </div>
                  <label for="bano_implemento_aseo[<?php echo $parametro['id'] ?>]" class="error"></label>
                </div>
              <?php endif ?>
              <?php if ($parametro['id'] != 4 && $parametro['id'] != 6): //Sólo parámetros que tienen dotacion ?>
                <?php foreach ($dotaciones[$parametro['id']] as $dotacion => $descripcion) { ?>
                  <input type="hidden" name="id_dotacion[]" value="<?php echo $dotacion; ?>">
                  <hr class="col-sm-11">
                  <div class="col-sm-12 row">
                    <div class="col-sm-12">
                      <h3><?php echo ucfirst(mb_strtolower($descripcion)); ?></h3>
                    </div>
                      <?php if (($parametro['id'] == 1 || $parametro['id'] == 2) && ($dotacion < 3 || $dotacion > 5)): ?>
                      <div class="col-sm-2">
                        <label>Tiene</label>
                        <div class="radio">
                        <label><input type="radio" name="tiene[<?php echo $parametro['id']; ?>][<?php echo $dotacion; ?>]" id="tiene" value="1" required> Si</label>
                        <label><input type="radio" name="tiene[<?php echo $parametro['id']; ?>][<?php echo $dotacion; ?>]" id="tiene" value="0" required> No</label>
                        </div>
                        <label for="tiene[<?php echo $parametro['id']; ?>][<?php echo $dotacion; ?>]" class="error"></label>
                      </div>
                      <div class="col-sm-2">
                        <label>En uso</label>
                        <div class="radio">
                        <label><input type="radio" name="en_uso[<?php echo $parametro['id']; ?>][<?php echo $dotacion; ?>]" id="en_uso" value="1" required> Si</label>
                        <label><input type="radio" name="en_uso[<?php echo $parametro['id']; ?>][<?php echo $dotacion; ?>]" id="en_uso" value="0" required> No</label>
                        </div>
                        <label for="en_uso[<?php echo $parametro['id']; ?>][<?php echo $dotacion; ?>]" class="error"></label>
                      </div>
                      <div class="col-sm-2">
                        <label>Funciona</label>
                        <div class="radio">
                        <label><input type="radio" name="funciona[<?php echo $parametro['id']; ?>][<?php echo $dotacion; ?>]" id="funciona" value="1" required> Si</label>
                        <label><input type="radio" name="funciona[<?php echo $parametro['id']; ?>][<?php echo $dotacion; ?>]" id="funciona" value="0" required> No</label>
                        </div>
                        <label for="funciona[<?php echo $parametro['id']; ?>][<?php echo $dotacion; ?>]" class="error"></label>
                      </div>
                      <?php if ($parametro['id'] == 2): ?>
                        <div class="col-sm-3">
                          <label>Tipo</label>
                          <input type="text" name="tipo[<?php echo $parametro['id']; ?>][<?php echo $dotacion; ?>]" id="tipo" class="form-control" required>
                        </div>
                      <?php endif ?>
                      <div class="col-sm-3">
                        <label>Capacidad</label>
                        <input type="text" name="capacidad[<?php echo $parametro['id']; ?>][<?php echo $dotacion; ?>]" id="capacidad" class="form-control" required>
                      </div>
                      <?php else: ?>
                      <div class="col-sm-3">
                        <label>Tiene</label>
                        <div class="radio">
                        <label><input type="radio" name="tiene[<?php echo $parametro['id']; ?>][<?php echo $dotacion; ?>]" id="tiene" value="1" required> Si</label>
                        <label><input type="radio" name="tiene[<?php echo $parametro['id']; ?>][<?php echo $dotacion; ?>]" id="tiene" value="0" required> No</label>
                        <label><input type="radio" name="tiene[<?php echo $parametro['id']; ?>][<?php echo $dotacion; ?>]" id="tiene" value="2" required> No aplica</label>
                        </div>
                        <label for="tiene[<?php echo $parametro['id']; ?>][<?php echo $dotacion; ?>]" class="error"></label>
                      </div>
                      <?php endif ?>
                  </div>
                <?php } ?>
              <?php endif ?>
        </form>
        <div class="col-sm-12">
          <?php if ($countParametros == $cntParametro-1): ?>
          <input type="hidden" name="ultimoFormulario" id="ultimoFormulario" value="form_<?php echo $parametro['id']; ?>">
          <button class="btn btn-primary" onclick="validForm(0, 0, 0);"><span class="fa fa-check"></span>  Guardar</button>
          <?php else: ?>
          <button class="btn btn-primary" onclick="validForm('form_<?php echo $parametro['id']; ?>', <?php echo $cntParametro; ?>, <?php echo $cntParametro+1; ?>);"><span class="fa fa-arrow-right"></span>  Siguiente</button>
          <?php endif ?>
        </div>
      </div>
    </div>
  </div>
  <?php $cntParametro++; 
  }
}
?>
            </div>
          </div><!-- COLLAPSE -->
        </div><!-- /.ibox-content -->
      </div><!-- /.ibox float-e-margins -->
    </div><!-- /.col-lg-12 -->
  </div><!-- /.row -->
</div><!-- /.wrapper wrapper-content animated fadeInRight -->

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
<script src="<?php echo $baseUrl; ?>/modules/infraestructuras/js/infraestructuras.js"></script>

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