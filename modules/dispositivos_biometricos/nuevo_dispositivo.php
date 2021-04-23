<?php 
$titulo = 'Nuevo dispositivo biométrico';
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
        <a href="index.php">Ver dispositivos biométricos</a>
      </li>
      <li class="active">
        <strong><?php echo $titulo; ?></strong>
      </li>
    </ol>
  </div><!-- /.col -->
  <div class="col-lg-4">
    <div class="title-action">
      <button class="btn btn-primary" onclick="submitForm();" id="segundoBtnSubmit" style="display: none;"><span class="fa fa-check"></span> Guardar</button>
    </div><!-- /.title-action -->
  </div><!-- /.col -->
</div><!-- /.row -->

<div class="wrapper wrapper-content animated fadeInRight">
  <div class="row">
    <div class="col-lg-12">
      <div class="ibox float-e-margins">
        <div class="ibox-content contentBackground">
          <div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
            <div class="panel panel-default">
              <div class="panel-heading" role="tab" id="headingOne">
                <h4 class="panel-title">
                  <a class="pull-right" role="button" data-toggle="collapse" data-parent="#accordion" href="#datosDispositivo" aria-expanded="true" aria-controls="datosDispositivo" style="color: #337ab7; display: none;" id="btnEditar_1" onclick="$('#datosBiometria').collapse('hide');">
                  Editar
                  </a>

                  Datos de dispositivo
                </h4>
              </div>
              <div id="datosDispositivo" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="headingOne">
                <div class="panel-body">
                  <form class="form" id="formDispositivo">
                    <div class="row">
                      <div class="form-group col-lg-3 col-md-6 col-sm-12">
                        <label>Referencia</label>
                        <input type="text" name="referencia" id="referencia" class="form-control" required>
                      </div>
                      <div class="form-group col-lg-3 col-md-6 col-sm-12">
                        <label>Número de serial</label>
                        <input type="number" min="0" step="1" name="num_serial" id="num_serial" class="form-control" required>
                        <em style="color: #cc5965; font-size: 13px; display: none;" id="existeNumSerial">Un dispositivo con este n° de serial ya existe.</em>
                      </div>
                      <div class="form-group col-lg-3 col-md-6 col-sm-12">
                        <label>Usuario</label>
                        <select name="id_usuario" id="id_usuario" class="form-control" required>
                          <option value="">Seleccione...</option>
                          <?php 
                          $obtenerUsuarios = "SELECT * FROM usuarios WHERE Tipo_Usuario = 'Manipuladora' or Tipo_Usuario = 'Coordinador' AND Estado = 1";
                          $resultadoUsuarios = $Link->query($obtenerUsuarios);
                          if ($resultadoUsuarios->num_rows > 0) {
                            while ($Usuarios = $resultadoUsuarios->fetch_assoc()) { ?>
                              <option value="<?php echo $Usuarios['id'] ?>"><?php echo $Usuarios['nombre']; ?></option>
                            <?php }
                          }
                          ?>
                        </select>
                      </div>
                      <div class="form-group col-lg-3 col-md-6 col-sm-12">
                        <label>Tipo dispositivo</label>
                        <select name="tipo" id="tipo" class="form-control" required>
                          <option value='Lector Huella Dactilar'>Lector Huella Dactilar</option>
                        </select>
                      </div>
                    </div> <!-- row -->
                 <!--  -->
                </form>

                <div class="col-sm-12">
                  <button class="btn btn-primary" onclick="validaForm('formDispositivo', 'datosDispositivo', 'datosBiometria')" id="botonSiguiente"><span class="fa fa-arrow-right"></span>  Siguiente</button>
                </div>
              </div>
            </div>
          </div>
          <div class="panel panel-default">
            <div class="panel-heading" role="tab" id="headingTwo">
              <h4 class="panel-title">
                <a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion" href="#datosBiometria" aria-expanded="false" aria-controls="datosBiometria">
                </a>
                Datos de biometría
              </h4>
            </div>
            <div id="datosBiometria" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingTwo">
              <div class="panel-body">
                <form class="form" id="formFocalizacion">
                   <div class="row">
                      <div class="form-group col-lg-3 col-md-6 col-sm-12">
                        <label>Municipio *</label>
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
                      <div class="form-group col-lg-3 col-md-6 col-sm-12">
                        <label>Institución *</label>
                          <select class="form-control select2" name="cod_inst" id="cod_inst" required style="width: 100%;">
                            <option value="">Seleccione Institución</option>
                          </select>
                      </div>
                      <div class="form-group col-lg-3 col-md-6 col-sm-12">
                        <label>Sede *</label>
                        <select class="form-control select2" name="cod_sede" id="cod_sede" required style="width: 100%;">
                          <option value="">Seleccione Sede</option>
                        </select>
                        <input type="hidden" name="nom_sede" id="nom_sede">
                      </div>
                      <div class="form-group col-lg-3 col-md-6 col-sm-12">
                        <label>Semana focalización * </label>
                        <select name="semana_focalizacion" id="semana_focalizacion" class="form-control select2" required style="width: 100%;">
                          <option value="">Seleccione semana</option>
                        <?php 
                        $consultarFocalizacion = "SELECT 
                                                 table_name AS tabla
                                                FROM 
                                                 information_schema.tables
                                                WHERE 
                                                 table_schema = DATABASE() AND table_name like 'focalizacion%'";
                        $resultadoFocalizacion = $Link->query($consultarFocalizacion);
                        if ($resultadoFocalizacion->num_rows > 0) {
                          while ($focalizacion = $resultadoFocalizacion->fetch_assoc()) { ?>
                            <option value="<?php echo $focalizacion['tabla']; ?>">Semana <?php echo substr($focalizacion['tabla'], 12, 3); ?></option>
                          <?php  }
                        }
                        ?>
                        </select>
                      </div>
                    </div> <!-- row -->

                    <div class="row">
                      <div class="form-group col-lg-3 col-md-6 col-sm-12">
                        <label for="nivel">Nivel *</label>
                        <select class="form-control" name="nivel" id="nivel" required>
                          <option value="">Seleccione una</option>
                          <option value="p">Primaria</option>
                          <option value="s">Secundaria</option>
                        </select>
                      </div>
                      <div class="form-group col-lg-3 col-md-6 col-sm-12">
                        <label for="grado">Grado *</label>
                        <select class="form-control select2" name="grados" id="grados" required style="width: 100%;">
                          <option value="">Seleccione Grado</option>
                        </select>
                      </div>
                      <div class="form-group col-lg-3 col-md-6 col-sm-12">
                        <label for="grupo">Grupo</label>
                        <select class="form-control" name="grupo" id="grupo" >
                          <option value="">Seleccion Grupo</option>
                        </select>
                      </div>
                    </div> 
                   </form> 
                <div class="col-sm-12">
                  <button class="btn btn-success" onclick="buscarEstudiantes();"><span class="fa fa-search"></span>  Buscar estudiantes</button>
                </div>
                <div class="col-sm-10"><hr></div>
                  <form class="form" id="formBiometria">
                    <div class="col-sm-12">
                      <table class="table" id="tablaEstudiantes">
                        <thead>
                          <tr>
                            <th>Tipo documentación</th>
                            <th>N° identificación</th>
                            <th>Nombre estudiante</th>
                            <th>Grado</th>
                            <th>Grupo</th>
                            <th>Id biometría de estudiante</th>
                          </tr>
                        </thead>
                        <!-- cuerpo que vamos a traer desde la funcion con un AJAX -->
                        <tbody id="tbodyEstudiantes">
                          
                        </tbody>
                        <tfoot>
                          <tr>
                            <th>Tipo documentación</th>
                            <th>N° identificación</th>
                            <th>Nombre estudiante</th>
                            <th>Grado</th>
                            <th>Grupo</th>
                            <th>Id biometría de estudiante</th>
                          </tr>
                        </tfoot>
                      </table>
                    </div>
                  </form>
                  <div class="col-sm-12">
                    <button class="btn btn-primary" id="segundoBtnSubmit2" onclick="submitForm();" style="display: none;"><span class="fa fa-check"></span>  Guardar</button>
                  </div>
                </div>
              </div>
            </div>
          </div>
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
<script src="<?php echo $baseUrl; ?>/modules/dispositivos_biometricos/js/dispositivos_biometricos.js"></script>

<script type="text/javascript">

    $('#tipoProducto').change();

    $('.select2').select2({
      width: "resolve"
    });
</script>

<?php mysqli_close($Link); ?>

</body>
</html>