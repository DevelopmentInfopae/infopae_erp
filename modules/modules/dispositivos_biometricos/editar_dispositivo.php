<?php 
$titulo = 'Editar dispositivo biométrico';
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
      <button class="btn btn-primary" onclick="submitFormEditar();" id="segundoBtnSubmit" style="display: none;"><span class="fa fa-check"></span> Guardar</button>
    </div><!-- /.title-action -->
  </div><!-- /.col -->
</div><!-- /.row -->

<div class="wrapper wrapper-content animated fadeInRight">
  <div class="row">
    <div class="col-lg-12">
      <div class="ibox float-e-margins">
        <div class="ibox-content contentBackground">
        <?php if (isset($_POST['idDispositivoEditar'])): ?>
          <?php 
            $iddispositivo = $_POST['idDispositivoEditar'];
            $consultarDispositivo = "SELECT sede.nom_sede, sede.cod_mun_sede, ubicacion.Ciudad, instituciones.nom_inst, instituciones.codigo_inst, usuarios.nombre as nom_usu, dispositivos.* FROM dispositivos INNER JOIN sedes".$_SESSION['periodoActual']." as sede ON sede.cod_sede = dispositivos.cod_sede INNER JOIN usuarios ON usuarios.id = dispositivos.id_usuario INNER JOIN ubicacion ON ubicacion.CodigoDANE = sede.cod_mun_sede INNER JOIN instituciones ON instituciones.codigo_inst = sede.cod_inst WHERE dispositivos.id = ".$iddispositivo;
            $resultadoDispositivo = $Link->query($consultarDispositivo);
            if ($resultadoDispositivo->num_rows > 0) {
              $infoDispositivo = $resultadoDispositivo->fetch_assoc();
            }
           ?>

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
                    <input type="hidden" name="iddispositivo" id="iddispositivo" value="<?php echo $iddispositivo; ?>">
                      <div class="row">
                        <div class="form-group col-lg-3 col-md-6 col-sm-12">
                          <label>Referencia</label>
                          <input type="text" name="referencia" id="referencia" class="form-control" value="<?php echo $infoDispositivo['referencia'] ?>" required>
                        </div>  
                      <div class="form-group col-lg-3 col-md-6 col-sm-12">
                        <label>Número de serial</label>
                        <input type="number" min="0" step="1" name="num_serial" id="num_serial" class="form-control" value="<?php echo $infoDispositivo['num_serial'] ?>" required>
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
                              while ($Usuarios = $resultadoUsuarios->fetch_assoc()) { 
                                if ($infoDispositivo['id_usuario'] == $Usuarios['id']) {
                                  $selected = "selected";
                                } else {
                                  $selected = "";
                                }
                                ?>
                                <option value="<?php echo $Usuarios['id'] ?>" <?php echo $selected; ?>><?php echo $Usuarios['nombre']; ?></option>
                              <?php }
                            }
                           ?>
                        </select>
                        </div>
                        <div class="form-group col-lg-3 col-md-6 col-sm-12">
                          <label>Tipo dispositivo</label>
                          <input type="text" name="tipo" id="tipo" class="form-control" value="<?php echo $infoDispositivo['tipo']; ?>" required>
                        </div>
                      </div> <!-- row -->
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
              <label>Municipio</label>
              <select class="form-control" name="cod_municipio" id="cod_municipio" onchange="existenBiometrias()" required readonly>
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
                  while ($municipio = $resultadoMunicipios->fetch_assoc()) { 

                    if ($municipio['CodigoDANE'] == $infoDispositivo['cod_mun_sede']) {
                      $selected = "selected";
                    } else {
                      $selected = "";
                    }

                    ?>
                <option value="<?php echo $municipio['CodigoDANE']; ?>" <?php echo $selected; ?>><?php echo ucfirst(mb_strtolower($municipio['Ciudad'])); ?></option>
                <?php }
                }
               ?>
              </select>
            </div>
            <div class="form-group col-lg-3 col-md-6 col-sm-12">
              <label>Institución</label>
              <select class="form-control select2" name="cod_inst" id="cod_inst" onchange="existenBiometrias()" required style="width: 100%;" >
              <?php 
              $consultaInstParametros = "SELECT DISTINCT codigo_inst, nom_inst, cod_mun FROM instituciones WHERE cod_mun = '".$infoDispositivo['cod_mun_sede']."' AND EXISTS (SELECT cod_inst FROM sedes".$_SESSION['periodoActual']." WHERE cod_inst = codigo_inst) ORDER BY nom_inst ASC ";
              $resultado = $Link->query($consultaInstParametros);
              if ($resultado->num_rows > 0) {
                while ($institucion = $resultado->fetch_assoc()) { 
                  if ($institucion['codigo_inst'] == $infoDispositivo['codigo_inst']) {
                    $selected = "selected";
                  } else {
                    $selected = "";
                  }
                  ?>
                  <option value="<?php echo $institucion['codigo_inst'] ?>" <?php echo $selected; ?>><?php echo $institucion['nom_inst'] ?></option>
                <?php }
                } else { ?>
                <option value="">Sin instituciones</option>
              <?php } ?>
              </select>
            </div>   
            <div class="form-group col-lg-3 col-md-6 col-sm-12">
              <label>Sede</label>
              <select class="form-control select2" name="cod_sede" id="cod_sede" onchange="existenBiometrias()" required style="width: 100%;" >
              <?php $consultaInstParametros = "SELECT DISTINCT cod_sede, nom_sede FROM sedes".$_SESSION['periodoActual']." WHERE cod_inst = '".$infoDispositivo['codigo_inst']."' ORDER BY nom_sede ASC";
                $resultado = $Link->query($consultaInstParametros);
                if ($resultado->num_rows > 0) {
                  while ($institucion = $resultado->fetch_assoc()) { 
                    if ($infoDispositivo['cod_sede'] == $institucion['cod_sede']) {
                      $selected = "selected";
                    } else {
                      $selected = "";
                    }
                    ?>
                    <option value="<?php echo $institucion['cod_sede'] ?>" <?php echo $selected; ?>><?php echo $institucion['nom_sede'] ?></option>
                  <?php }
                } else { ?>
                <option value="">Sin sedes</option>
                <?php } ?>
              </select>
              <input type="hidden" name="nom_sede" id="nom_sede" value="<?php echo $infoDispositivo['nom_sede'] ?>">
            </div>
            <div class="form-group col-lg-3 col-md-6 col-sm-12">
              <label>Semana focalización</label>
              <select name="semana_focalizacion" id="semana_focalizacion" class="form-control select2" required style="width: 100%;"> 

              <?php 
              $focalizaciones = [];
              $consultarFocalizacion = "SELECT 
                                         table_name AS tabla
                                        FROM 
                                         information_schema.tables
                                        WHERE 
                                         table_schema = DATABASE() AND table_name like 'focalizacion%'";
              $resultadoFocalizacion = $Link->query($consultarFocalizacion);
              if ($resultadoFocalizacion->num_rows > 0) {
                while ($focalizacion = $resultadoFocalizacion->fetch_assoc()) {
                  $focalizaciones[] = $focalizacion['tabla']; ?>
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
          </div> <!-- row -->
 
        </form>
        <div class="col-sm-12">
          <button class="btn btn-success" onclick="buscarEstudiantes();"><span class="fa fa-search"></span>  Buscar estudiantes</button>
        </div>
        <div class="col-sm-10"><hr></div>
        <form class="form" id="formBiometria">

          <div class="col-sm-12" id="titularesAsignados" style="display: none;">
            <em>Ya existen titulares asignados de la sede anterior, el consecutivo actual de biometría es :  <span id="consecutivoActual"></span></em><br>
          </div>
          <input type="hidden" name="borrarBiometrias" id="borrarBiometrias" value="0">
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
              <tbody id="tbodyEstudiantes">
               <?php 

               if (strlen($infoDispositivo['id']) == 1) {
                 $idDisp = "00".$infoDispositivo['id'];
               } else if (strlen($infoDispositivo['id']) == 2) {
                 $idDisp = "0".$infoDispositivo['id'];
               } else if (strlen($infoDispositivo['id']) == 3) {
                 $idDisp = $infoDispositivo['id'];
               }
               $grados = [];
               $consultarGrados = "SELECT convert(id, signed) as id, nombre FROM grados ";
               $resultadoGrados = $Link->query($consultarGrados);
               if ($resultadoGrados->num_rows > 0) {
                 while ($gradosInfo = $resultadoGrados->fetch_assoc()) {
                   $grados[$gradosInfo['id']] = $gradosInfo['nombre'];
                   $gradosId[$gradosInfo['id']] = $gradosInfo['id'];
                 }
               }

               $selectFoc = "";
               $sqlFoc = ""; 
               foreach ($focalizaciones as $focalizacion => $valor) {
                $selectFoc = " ".$valor.".nom1, ".$valor.".ape1, ".$valor.".cod_grado, ".$valor.".nom_grupo, ";
                $sqlFoc.=" INNER JOIN ".$valor." ON ".$valor.".num_doc = biometria.num_doc ";
               }

                $consultarBiometria = "SELECT ".$selectFoc." tipodocumento.nombre as tdocnom, biometria.* FROM biometria INNER JOIN tipodocumento ON biometria.tipo_doc = tipodocumento.id ".$sqlFoc." WHERE id_dispositivo = ".$idDisp." ORDER BY cod_grado, nom_grupo;";
                // echo $consultarBiometria;
                $resultadoBiometria = $Link->query($consultarBiometria);
                if ($resultadoBiometria->num_rows > 0) {
                  $cnt = 0;
                  while ($biometria = $resultadoBiometria->fetch_assoc()) {
                    // echo $biometria['id']; 
                    if ($biometria['cod_sede'] == $infoDispositivo['cod_sede']) {
                    $cnt++; ?>
                   <tr id="biometria_<?php echo $cnt ?>">
                     <td><?php echo $biometria['tdocnom']; ?><input type='hidden' name='tipo_doc[<?php echo $biometria['num_doc']; ?>]' value='<?php echo $biometria['tipo_doc']; ?>'>
                    <input type="hidden" name="idBiometria[<?php echo $biometria['num_doc']; ?>]" value="<?php echo $biometria['id']; ?>"></td>
                     <td><?php echo $biometria['num_doc']; ?><input type='hidden' name='num_doc[]' value='<?php echo $biometria['num_doc']; ?>'></td>
                     <td><?php echo $biometria['nom1']." ".$biometria['ape1']; ?></td>
                     <td><?php echo $grados[$biometria['cod_grado']]; ?></td>
                     <td><?php echo $biometria['nom_grupo']; ?></td>
                     <td><input type='number' name='id_bioest[<?php echo $biometria['num_doc']; ?>]' data-type = 1 id='id_bioest' onchange='validaBioEst(this, "<?php echo $cnt; ?>", 2, this.value)' class='form-control' value="<?php echo $biometria['id_bioest']; ?>" min="1" step="1" required><em style='color: #cc5965; font-size: 13px; display: none;' id='existeBioEstEdit<?php echo $cnt; ?>'> Ya digitó un id similar a este.</em></td>
                   </tr>
                  <?php
                    } 
                  }
                } else {
                    $cnt = 0;
                  } ?> 
                
                <input type="hidden" id="cntBiometrias" value="<?php echo $cnt; ?>">
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
          <button class="btn btn-primary" onclick="submitFormEditar();"><span class="fa fa-check"></span>  Guardar</button>
        </div>
      </div>
    </div>
  </div>
</div>
<div class="modal inmodal fade" id="modalEliminarBiometria" tabindex="-1" role="dialog" style="display: none;" aria-hidden="true">
 <div class="modal-dialog modal-sm">
   <div class="modal-content">
     <div class="modal-header text-info" style="padding: 15px;">
       <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only">Cerrar</span></button>
       <h3><i class="fa fa-question-circle fa-lg" aria-hidden="true"></i> Confirmación InfoPAE </h3>
     </div>
     <div class="modal-body" style="text-align: center;">
         <span>¿Está seguro de borrar el registro?</span>
         <input type="hidden" name="idbiometriaeliminar" id="idbiometriaeliminar">
         <input type="hidden" name="numbiometria" id="numbiometria">
     </div>
     <div class="modal-footer">
       <button type="button" class="btn btn-white btn-sm" data-dismiss="modal">No</button>
       <button type="button" class="btn btn-primary btn-sm" onclick="eliminarBiometria()">Si</button>
     </div>
   </div>
 </div>
</div>
<form method="Post" id="ver_dispositivo" action="ver_dispositivo.php" style="display: none;">
  <input type="hidden" name="idDispositivoVer" id="idDispositivoVer">
</form>

<?php else: ?>
Dispositivo no definido.
<?php endif ?>

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
    $('.select2').select2({
      width: "resolve"
    });
    $('#tipoProducto').change();
</script>

<?php mysqli_close($Link); ?>

</body>
</html>