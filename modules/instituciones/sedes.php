<?php 
  include '../../header.php';
  set_time_limit (0); 
  ini_set('memory_limit','6000M');

  if ($permisos['instituciones'] == "0") {
    ?><script type="text/javascript">
      window.open('<?= $baseUrl ?>', '_self');
    </script>
  <?php exit(); }

  $periodoActual = $_SESSION["periodoActual"];
  $titulo = "Sedes Educativas";
  $institucionNombre = "";

  // Declaración de variables.
  $dataSedes = [];
  $periodoActual = $_SESSION['periodoActual'];
  $institucion = (isset($_POST["institucion"]) && $_POST["institucion"] != "") ? mysqli_real_escape_string($Link, $_POST["institucion"]) : "";
  $municipio   = (isset($_POST['municipio']) && $_POST['municipio'] != '') ? ($_POST["municipio"] == "0") ? "" : mysqli_real_escape_string($Link, $_POST["municipio"]) : "";
  $region = isset($_GET["region"]) ? $_GET["region"] : false;

  $zonaPae = "";
  if ($_SESSION['p_Municipio'] == "0") {
    $zonaPae .= " sed.Zona_Pae AS zonaPae, ";
  }
  $consultaSedes = "SELECT
                      sed.id AS id,
                      sed.cod_sede AS codigoSede,
                      sed.nom_sede AS nombreSede,
                      sed.sector AS sectorSede,
                      ".($region ? "ubicacion.region," : "")."
                      sed.cod_inst AS codigoInstitucion,
                      sed.nom_inst AS nombreInstitucion,
                      usu.nombre AS nombreCoordinador,
                      jor.nombre AS nombreJornada,
                      sed.tipo_validacion AS tipoValidacion,
                      $zonaPae
                      ubicacion.Ciudad as municipio,
                      sed.estado AS estadoSede
                    FROM sedes$periodoActual sed
                    LEFT JOIN usuarios usu ON usu.num_doc = sed.id_coordinador
                    LEFT JOIN jornada jor ON jor.id = sed.jornada
                    LEFT JOIN ubicacion ON ubicacion.CodigoDANE = sed.cod_mun_sede
                    WHERE 1=1 ";
  if($municipio  != ""){ $consultaSedes .= " AND cod_mun_sede = '" . $_POST['municipio'] . "' "; }
  if($institucion != ""){ $consultaSedes .= " AND cod_inst = '" . $_POST['institucion'] . "' "; }
  $consultaSedes .= "ORDER BY nom_sede ASC";
  $resultadoSedes = $Link->query($consultaSedes);
  if($resultadoSedes->num_rows > 0){
    while($registrosSedes = $resultadoSedes->fetch_assoc()) {
      $dataSedes[] = $registrosSedes;
    }
  } 
?>
<div class="row wrapper wrapper-content border-bottom white-bg page-heading">
  <div class="col-lg-8">
      <h2>Sedes Educativas</h2>
      <ol class="breadcrumb">
          <li>
              <a href="<?php echo $baseUrl; ?>">Inicio</a>
          </li>
          <li class="active">
              <strong><?php echo $titulo; ?></strong>
          </li>
      </ol>
  </div>
  <div class="col-lg-4">
      <div class="title-action">
        <?php if ($_SESSION['perfil'] == "0" || $permisos['instituciones'] == "1" || $permisos['instituciones'] == "2"): ?>
          <a href="#" class="btn btn-primary" name="boton_abri_ventana_exportar_priorizacion" id="boton_abri_ventana_exportar_priorizacion"><i class="fa fa-file-excel-o"></i> Exportar</a>
        <?php endif ?>
        <?php if($_SESSION["perfil"] == "0" || $permisos['instituciones'] == "2") { ?>
          <a href="#" class="btn btn-primary" onclick="crearSede();"><i class="fa fa-plus"></i> Nueva</a>
        <?php } ?>
      </div>
  </div>
</div>

<?php if ($_SESSION['perfil'] != 6): ?>
<div class="wrapper wrapper-content  animated fadeInRight">
  <div class="row">
    <div class="col-sm-12">
      <div class="ibox">
        <div class="ibox-content">
          <form action="sedes.php<?= isset($_GET['region']) ? "?region=1" : "" ?>" id="formSedes" name="formSedes" method="post">
            <div class="row">
              <div class="col-lg-2 col-md-4 col-sm-8 form-group">
                <label for="municipio">Municipio</label>
                <select class="form-control" name="municipio" id="municipio" required>
                  <option value="">Seleccione uno</option>
                  <?php
                  $consulta = "SELECT DISTINCT codigoDANE, ciudad FROM ubicacion WHERE ETC <> '1' ";

                  $DepartamentoOperador = $_SESSION['p_CodDepartamento'];
                  if($DepartamentoOperador != ''){
                    $consulta = $consulta." AND CodigoDANE LIKE '$DepartamentoOperador%' ";
                  }
                  $consulta = $consulta." ORDER BY ciudad ASC ";
                  $resultado = $Link->query($consulta);
                  if($resultado->num_rows > 0){
                    while($row = $resultado->fetch_assoc()) { ?>
                      <option value="<?php echo $row["codigoDANE"]; ?>" <?php if(isset($_POST["municipio"]) && $_POST["municipio"] == $row["codigoDANE"] || $municipio_defecto["CodMunicipio"] == $row["codigoDANE"]) { echo " selected "; } ?>>
                                  <?php echo $row["ciudad"]; ?>
                      </option>
                  <?php
                    }
                  }
                  ?>
                </select>
              </div>

              <div class="col-lg-3 col-md-6 col-sm-9 form-group">
                <label for="institucion">Institución</label>
                <select class="form-control" name="institucion" id="institucion">
                  <option value="">Todas</option>
                    <?php
                      $municipio = (isset($_POST["municipio"]) && empty($_POST["municipio"])) ? mysqli_real_escape_string($Link, $_POST["municipio"]) : $municipio_defecto["CodMunicipio"];

                      $consultaIns = "SELECT codigo_inst AS cod_inst, nom_inst FROM instituciones WHERE cod_mun = '$municipio' ORDER BY nom_inst ASC";
                      $resultado = $Link->query($consultaIns) or die ('Unable to execute query. '. mysqli_error($Link));
                      if($resultado->num_rows >= 1){
                        while($row = $resultado->fetch_assoc()) { ?>
                          <option value="<?= $row['cod_inst']; ?>" <?php if(isset($_POST["institucion"]) && $_POST["institucion"] == $row['cod_inst'] ){ echo " selected "; }  ?> > <?= $row['nom_inst']; ?>
                          </option>
                    <?php
                        }
                      }
                    ?>
                  </select>
                  </div><!-- /.col -->
                </div>
                <div class="row">
                  <div class="col-sm-3 form-group">
                    <button class="btn btn-primary" type="button" id="btnBuscar"> <i class="fa fa-search"></i> Buscar</button>
                  </div>
                </div>
              </form>
        </div> <!-- ibox-content -->
      </div> <!-- ibox -->
    </div> <!-- col-sm-12 -->
  </div> <!-- row -->
</div> <!-- fadeInRight -->
<?php endif ?>

<div class="wrapper wrapper-content  animated fadeInRight">
  <div class="row">
    <div class="col-sm-12">
      <div class="ibox">
        <div class="ibox-content">
          <h2>Sedes</h2>
            <table class="table table-striped table-hover selectableRows dataTablesSedes" >
              <thead>
                <tr>
                  <?php if ($_SESSION['p_Municipio'] == "0"): ?>
                    <th>Zona Pae</th>
                  <?php endif ?>
                  <?php if (isset($_GET['region'])): ?>
                    <th>Región</th>
                  <?php endif ?>
                  <th>Municipio</th>
                  <th>Código institución</th>
                  <th>Nombre institución</th>
                  <th>Código Sede</th>
                  <th>Nombre sede</th>
                  <th>Zona sede</th> 
                  <th>Jornada</th> 
                  <th>Tipo validación</th>                             
                  <th>Coordinador</th>
                  <?php if($_SESSION["perfil"] == "0" || $permisos['instituciones'] == "1" || $permisos['instituciones'] == "2") { ?>
                    <th>Acciones</th>
                  <?php } ?>
                </tr>
              </thead>
              <tbody>
                <?php if (isset($dataSedes)) { ?>
                  <?php foreach ($dataSedes as $key => $data): ?>
                    <tr data-codigosede = "<?= $data['codigoSede']; ?>" data-nombresede = "<?= $data['nombreSede']; ?>" data-nombreinstitucion = "<?= $data['nombreInstitucion']; ?>">
                      <?php if ($_SESSION['p_Municipio'] == "0"): ?>
                        <td><?= $data['zonaPae']; ?></td>
                      <?php endif ?>
                      <?php if (isset($_GET['region'])): ?>
                        <td><?= $data['region'];?></td>
                      <?php endif ?>
                      <td><?= $data['municipio']; ?></td>
                      <td><?= $data['codigoInstitucion']; ?></td>
                      <td><?= $data['nombreInstitucion']; ?></td>
                      <td><?= $data['codigoSede']?></td>
                      <td><?= $data['nombreSede']?></td>
                      <td><?php $stringSector = '';  
                            if ($data['sectorSede'] == 1) {$stringSector = 'Rural';} 
                            else if ($data['sectorSede'] == 2) {$stringSector = 'Urbano';}  
                            else {$stringSector = 'Indefinido';}
                            echo $stringSector; ?>              
                      </td>
                      <td><?= $data['nombreJornada']?></td>
                      <td><?= $data['tipoValidacion']?></td>
                      <td><?= $data['nombreCoordinador']?></td>
                      <?php if ($_SESSION['perfil'] == "0" || $permisos['instituciones'] == "1" || $permisos['instituciones'] == "2"): ?>
                      <td>
                        <div class="btn-group">
                          <div class="dropdown pull-right">
                            <button class="btn btn-primary btn-sm" type="button" id="dropDownMenu1" data-toggle="dropdown"  aria-haspopup="true">Acciones <span class="caret"></span>
                            </button>
                            <ul class="dropdown-menu pull-right" aria-labelledby="dropDownMenu1">
                              <?php if ($_SESSION['perfil'] == "0" || $permisos['instituciones'] == "2"): ?>
                                <li>
                                  <a href="#" data-codigo = "<?= $data['codigoSede'];?>" data-nombre = "<?= $data['nombreSede'];?>" class="editarSede"><i class="fas fa-pencil-alt fa-lg"></i> Editar</a>
                                </li>
                              <?php endif ?>
                              <li>
                                <a href="#" data-codigo = "<?= $data['codigoSede']; ?>" class="verDispositivos"><i class="fa fa-eye fa-lg"></i> Ver dispositivos</a>
                              </li>
                              <li>
                                <a href="#" data-codigo = "<?= $data['codigoSede']; ?>" class="verInfraestructura"><i class="fa fa-bank fa-lg"></i> Ver Infraestructura</a>
                              </li>
                              <li>
                                <a href="#" data-codigo = "<?= $data['codigoSede']; ?>" class="verTitulares"><i class="fa fa-child fa-lg"></i> Ver Titulares</a>
                              </li>
                              <?php if ($_SESSION['perfil'] == "0" || $permisos['instituciones'] == "2"): ?>
                                <li class="divider"></li>
                                <li>
                                  <a href="#">
                                    Estado: &nbsp;
                                    <input type="checkbox" id="inputEstadoSede<?php echo $data["id"]; ?>" data-codigo = "<?= $data['id'];?>" data-estado = "<?= $data['estadoSede'];?>" id="InputEstadoSede" class="estadoSede" data-toggle="toggle" data-on="Activo" data-off="Inactivo" data-size="mini" data-width="70" data-height="24"<?php if($data['estadoSede'] == 1) {echo "checked";} ?>>
                                  </a>
                                </li>
                              <?php endif ?>
                            </ul>
                          </div> <!-- pull-right -->
                        </div> <!-- btn-group -->
                      </td>
                      <?php endif ?>
                    </tr>
                  <?php endforeach ?>
                <?php } ?>  
              </tbody>
              <tfoot>
                <tr>
                  <?php if ($_SESSION['p_Municipio'] == "0"): ?>
                    <th>Zona Pae</th>
                  <?php endif ?>
                  <?php if (isset($_GET['region'])): ?>
                    <th>Región</th>
                  <?php endif ?>
                  <th>Municipio</th>
                  <th>Código institución</th>
                  <th>Nombre institución</th>
                  <th>Código Sede</th>
                  <th>Nombre sede</th>
                  <th>Zona sede</th> 
                  <th>Jornada</th> 
                  <th>Tipo validación</th>                             
                  <th>Coordinador</th>
                  <?php if($_SESSION["perfil"] == "0" || $permisos['instituciones'] == "1" || $permisos['instituciones'] == "2") { ?>
                    <th>Acciones</th>
                  <?php } ?>
                </tr>
              </tfoot>
            </table>                 
        </div> <!-- ibox-content -->
      </div><!-- ibox -->
    </div> <!-- col-sm-12 -->
  </div> <!-- row -->
</div> <!-- fadeInRight -->

<!-- ventana confirmar cambio de estado -->
<div class="modal inmodal fade" id="ventanaConfirmar" tabindex="-1" role="dialog" style="display: none;" aria-hidden="true">
  <div class="modal-dialog modal-sm">
    <div class="modal-content">
      <div class="modal-header text-info" style="padding: 15px;">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only">Close</span></button>
        <h3><i class="fa fa-question-circle fa-lg" aria-hidden="true"></i> Información InfoPAE </h3>
      </div>
      <div class="modal-body">
          <p class="text-center"></p>
      </div>
      <div class="modal-footer">
        <input type="hidden" id="codigoACambiar">
        <input type="hidden" id="estadoACambiar">
        <button type="button" class="btn btn-danger btn-outline btn-sm" data-dismiss="modal" onclick="revertirEstado();">Cancelar</button>
        <button type="button" class="btn btn-primary btn-sm" data-dismiss="modal" onclick="cambiarEstado();">Aceptar</button>
      </div>
    </div>
  </div>
</div>

<!-- Ventana formulari para la priorización -->
<div class="modal inmodal fade" id="ventanaFormularioPri" tabindex="-1" role="dialog" style="display: none;" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header text-info" style="padding: 15px;">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only">Close</span></button>
        <h3><i class="fa fa-upload fa-lg" aria-hidden="true"></i> Importar Priorización  </h3>
      </div>
      <div class="modal-body">
        <form action="" name="frmSubirArchivoPriorizacion" id="frmSubirArchivoPriorizacion">
          <div class="row">
            <div class="col-md-4">
              <div class="form-group">
                <label for="mes">Mes</label>
                <select class="form-control" name="mes" id="mes" required>
                  <option value="">Selección</option>
                  <?php
                    $consultaMes = "SELECT distinct MES AS mes FROM planilla_semanas;";
                    $resultadoMes = $Link->query($consultaMes);
                    if($resultadoMes->num_rows > 0){
                      while($registros = $resultadoMes->fetch_assoc()) {
                  ?>
                      <option value="<?php echo $registros["mes"]; ?>"><?php echo $registros["mes"]; ?></option>
                  <?php
                      }
                    }
                  ?>
                </select>
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group">
                <label for="mes">Semana</label>
                <select class="form-control" name="semana" id="semana" required>
                  <option value="">Selección</option>
                </select>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-10">
              <div class="form-group">
                <label for="archivoPriorizacion">Archivo</label>
                <div class="fileinput fileinput-new input-group" data-provides="fileinput">
                  <div class="form-control" data-trigger="fileinput">
                    <i class="glyphicon glyphicon-file fileinput-exists"></i> <span class="fileinput-filename"></span>
                  </div>
                  <span class="input-group-addon btn btn-default btn-file"><span class="fileinput-new">Seleccionar archivo</span><span class="fileinput-exists">Cambiar</span>
                    <input type="file" name="archivoPriorizacion" id="archivoPriorizacion" accept=".csv, .xlsx" required>
                  </span>
                  <a href="#" class="input-group-addon btn btn-default fileinput-exists" data-dismiss="fileinput">Borrar</a>
                </div>
                <label for="archivoPriorizacion" class="error" style="display: none;"></label>
              </div>
              <label class="text-warning">Para mayor eficacia es mejor subir el archivo con extensión .CSV </label>
            </div>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-danger btn-outline btn-sm" data-dismiss="modal">Cancelar</button>
        <button type="button" class="btn btn-primary btn-sm" id="subirArchivoPriorizacion">Aceptar</button>
      </div>
    </div>
  </div>
</div>

<!-- Ventana de formulario de exportación para la priorización -->
<div class="modal inmodal fade" id="ventana_formulario_exportar_priorizacion" tabindex="-1" role="dialog" style="display: none;" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header text-info" style="padding: 15px;">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only">Close</span></button>
        <h3><i class="fa fa-upload fa-lg" aria-hidden="true"></i> Exportar Priorización  </h3>
      </div>
      <div class="modal-body">
        <form action="" name="formulario_exportar_priorizacion" id="formulario_exportar_priorizacion">
          <input type="hidden" id="region_exportar" value="<?= isset($_GET['region']) ? true : false ?>">
          <div class="row">
            <div class="col-md-4">
              <div class="form-group">
                <label for="mes_exportar">Mes</label>
                <select class="form-control" name="mes_exportar" id="mes_exportar" required>
                  <option value="">Selección</option>
                  <?php
                    $consultaMes = "SELECT distinct MES AS mes FROM planilla_semanas;";
                    $resultadoMes = $Link->query($consultaMes);
                    if($resultadoMes->num_rows > 0){
                      while($registros = $resultadoMes->fetch_assoc()) {
                  ?>
                      <option value="<?php echo $registros["mes"]; ?>"><?php echo $registros["mes"]; ?></option>
                  <?php
                      }
                    }
                  ?>
                </select>
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group">
                <label for="semana_exportar">Semana</label>
                <select class="form-control" name="semana_exportar" id="semana_exportar" required>
                  <option value="">Selección</option>
                </select>
              </div>
            </div>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-danger btn-outline btn-sm" data-dismiss="modal">Cancelar</button>
        <button type="button" class="btn btn-primary btn-sm" id="exportar_priorizacion">Aceptar</button>
      </div>
    </div>
  </div>
</div>

<!-- Ventana formulari para la focalización -->
<div class="modal inmodal fade" id="ventanaFormularioFoc" tabindex="-1" role="dialog" style="display: none;" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header text-info" style="padding: 15px;">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only">Close</span></button>
        <h3><i class="fa fa-upload fa-lg" aria-hidden="true"></i> Importar Focalización  </h3>
      </div>
      <div class="modal-body">
        <form action="" name="frmSubirArchivoFocalizacion" id="frmSubirArchivoFocalizacion">
          <div class="row">
            <div class="col-md-4">
              <div class="form-group">
                <label for="mesFocalizacion">Mes</label>
                <select class="form-control" name="mesFocalizacion" id="mesFocalizacion" required>
                  <option value="">Selección</option>
                  <?php
                    $consultaMes = "SELECT distinct MES AS mes FROM planilla_semanas;";
                    $resultadoMes = $Link->query($consultaMes);
                    if($resultadoMes->num_rows > 0){
                      while($registros = $resultadoMes->fetch_assoc()) {
                  ?>
                      <option value="<?php echo $registros["mes"]; ?>"><?php echo $registros["mes"]; ?></option>
                  <?php
                      }
                    }
                  ?>
                </select>
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group">
                <label for="semanaFocalizacion">Semana</label>
                <select class="form-control" name="semanaFocalizacion" id="semanaFocalizacion" required>
                  <option value="">Selección</option>
                </select>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-10">
              <div class="form-group">
                <label for="archivoFocalizacion">Archivo</label>
                <div class="fileinput fileinput-new input-group" data-provides="fileinput">
                  <div class="form-control" data-trigger="fileinput">
                    <i class="glyphicon glyphicon-file fileinput-exists"></i> <span class="fileinput-filename"></span>
                  </div>
                  <span class="input-group-addon btn btn-default btn-file"><span class="fileinput-new">Seleccionar archivo</span><span class="fileinput-exists">Cambiar</span>
                    <input type="file" name="archivoFocalizacion" id="archivoFocalizacion" accept=".csv" required>
                  </span>
                  <a href="#" class="input-group-addon btn btn-default fileinput-exists" data-dismiss="fileinput">Borrar</a>
                </div>
                <label for="archivoFocalizacion" class="error" style="display: none;"></label>
              </div>
              <label class="text-warning">Para mayor eficacia es mejor subir el archivo con extensión .CSV </label>
            </div>
            <div class="col-md-2">
              <label for="archivoFocalizacion">Validar</label>
              <input type="checkbox" name="validar" id="validar" data-toggle="toggle" data-on="si" data-off="no" checked>
            </div>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-danger btn-outline btn-sm" data-dismiss="modal" onclick="$('#ventanaFormularioFoc').on('hidden.bs.modal', function (e) { $('#frmSubirArchivoFocalizacion')[0].reset(); })">Cancelar</button>
        <button type="button" class="btn btn-primary btn-sm" id="subirArchivoFocalizacion">Aceptar</button>
      </div>
    </div>
  </div>
</div>

 <form action="sede.php" method="post" name="formVerSede" id="formVerSede">
    <input type="hidden" name="codSede" id="codSede">
    <input type="hidden" name="nomSede" id="nomSede">
    <input type="hidden" name="nomInst" id="nomInst">
  </form>

  <form action="exportar_manipuladoras_requeridas.php" method="post" name="exportarManipuladoras" id="exportarManipuladoras">
  </form>

  <form action="sede_editar.php" method="post" name="formEditarSede" id="formEditarSede">
    <input type="hidden" name="codigoSede" id="codigoSede">
    <input type="hidden" name="nombreSede" id="nombreSede">
  </form>

  <form action="../dispositivos_biometricos/index.php" method="post" name="formDispositivosSede" id="formDispositivosSede">
    <input type="hidden" name="cod_sede" id="cod_sede" value="">
  </form>

  <form action="../infraestructuras/ver_infraestructura.php" method="post" name="formInfraestructuraSede" id="formInfraestructuraSede">
    <input type="hidden" name="cod_sede" id="cod_sede" value="">
  </form>

  <form action="../titulares_derecho/index.php" method="post" name="formTitularesSede" id="formTitularesSede">
    <input type="hidden" name="cod_sede" id="cod_sede" value="">
  </form>

</body>
</html>

<?php include '../../footer.php'; ?>

<!-- Mainly scripts -->
<script src="<?php echo $baseUrl; ?>/theme/js/jquery-3.1.1.min.js"></script>
<script src="<?php echo $baseUrl; ?>/theme/js/bootstrap.min.js"></script>
<script src="<?php echo $baseUrl; ?>/theme/js/plugins/metisMenu/jquery.metisMenu.js"></script>
<script src="<?php echo $baseUrl; ?>/theme/js/plugins/slimscroll/jquery.slimscroll.min.js"></script>

<!-- Custom and plugin javascript -->
<script src="<?php echo $baseUrl; ?>/theme/js/inspinia.js"></script>
<script src="<?php echo $baseUrl; ?>/theme/js/plugins/pace/pace.min.js"></script>
<script src="<?php echo $baseUrl; ?>/theme/js/plugins/dataTables/datatables.min.js"></script>
<script src="<?php echo $baseUrl; ?>/theme/js/plugins/jasny/jasny-bootstrap.min.js"></script>
<script src="<?php echo $baseUrl; ?>/theme/js/plugins/validate/jquery.validate.min.js"></script>
<script src="<?php echo $baseUrl; ?>/theme/js/plugins/toggle/toggle.min.js"></script>
<script src="<?php echo $baseUrl; ?>/theme/js/plugins/toastr/toastr.min.js"></script>
<script src="<?php echo $baseUrl; ?>/modules/instituciones/js/sedes.js"></script>

<!-- Page-Level Scripts -->

<script>
  $(document).ready(function(){
  // Evitar el burbujeo del DOM en el control dropbox
  $(document).on('click', '.dropdown li:nth-child(2)', function(e) { e.stopPropagation(); });
  // Configuración para la tabla de sedes.
    $('.dataTablesSedes').DataTable({
          pageLength: 10,
          responsive: true,
          dom: 'lr<"containerBtn"><"inputFiltro"f>tip<"clear"><"html5buttons"B>',
          order: [ [1, 'asc'], [2, 'asc']],
            oLanguage: {
            sLengthMenu: 'Mostrando _MENU_ registros',
            sZeroRecords: 'No se encontraron registros',
            sInfo: 'Mostrando _START_ a _END_ de _TOTAL_ registros ',
            sInfoEmpty: 'Mostrando 0 a 0 de 0 registros',
            sInfoFiltered: '(Filtrado desde _MAX_ registros)',
            sSearch:         'Buscar: ',
            oPaginate:{
              sFirst:    'Primero',
              sLast:     'Último',
              sNext:     'Siguiente',
              sPrevious: 'Anterior'
            }
          },
          buttons: [ {extend: 'excel', title: 'Sedes', className: 'btnExportarExcel', exportOptions: { columns: [ 0, 1, 2, 3, 4, 5, 6, 7] } } ]
        });

    <?php if($_SESSION["perfil"] == "0" || $permisos['instituciones'] == "1" || $permisos['instituciones'] == "2") { ?>
    // Botón de acciones para la tabla.
    var botonAcciones = '<div class="dropdown pull-right">'+
                      '<button class="btn btn-primary btn-sm btn-outline" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true">'+
                        'Acciones <span class="caret"></span>'+
                      '</button>'+
                      '<ul class="dropdown-menu pull-right" aria-labelledby="dropdownMenu1">'+
                        '<li><a tabindex="0" aria-controls="box-table" href="#" onclick="$(\'.btnExportarExcel\').click();"><i class="fa fa-file-pdf-o"></i> Exportar </a></li>'+
                        <?php if ($_SESSION['perfil'] == "0" || $permisos['instituciones'] == "2"): ?>
                          '<li><a tabindex="0" aria-controls="box-table" href="#" onclick="calcular_manipuladoras();"><i class="fa fa-refresh"></i> Actualizar número de manipuladoras requeridas </a></li>'+
                        <?php endif ?>
                        '<li><a href="'+ $('#inputBaseUrl').val() +'/modules/instituciones/sedes.php<?= isset($_GET['region']) ? "" : "?region=1" ?>"><i class="fa fa-eye"></i> Ver zona </a></li>'+
                        <?php if ($_SESSION['perfil'] == "0" || $permisos['instituciones'] == "2"): ?>
                        '<li class="divider"></li>'+
                        '<li>'+
                          '<a class="fileinput fileinput-new" data-provides="fileinput">'+
                            '<span class="btn-file">'+
                              '<i class="fa fa-upload"></i> '+
                              '<span class="fileinput-new">Importar sedes</span>'+
                              '<span class="fileinput-exists">Cambiar</span>'+
                              '<input type="file" name="archivoSede" id="archivoSede" onchange="if(!this.value.length) return false; cargarArchivo();" accept=".csv">'+
                            '</span> '+
                            '<span class="fileinput-filename center-block"></span>'+
                            '<span href="#" class="close fileinput-exists" data-dismiss="fileinput" style="float: none">&times;</span>'+
                          '</a>'+
                        '</li>'+
                        '<li><a href="#" id="importarPriorizacion"><i class="fa fa-upload"></i> Importar priorización</a></li>'+
                        '<li><a href="#" id="importarFocalizacion"><i class="fa fa-upload"></i> Importar focalización</a></li>'+
                        '<li>'+
                          '<a class="fileinput fileinput-new" data-provides="fileinput">'+
                            '<span class="btn-file">'+
                              '<i class="fa fa-upload"></i> '+
                              '<span class="fileinput-new">Importar manipuladoras</span>'+
                              '<span class="fileinput-exists">Cambiar</span>'+
                              '<input type="file" name="archivoManipuladoras" id="archivoManipuladoras" onchange="if(!this.value.length) return false; subir_archivo_manipuladoras();" accept=".csv">'+
                            '</span> '+
                            '<span class="fileinput-filename center-block"></span>'+
                            '<span href="#" class="close fileinput-exists" data-dismiss="fileinput" style="float: none">&times;</span>'+
                          '</a>'+
                        '</li>'+
                        '<li class="divider"></li>'+
                        '<li><a href="'+ $('#inputBaseUrl').val() +'/download/sedes/Plantilla_Sedes.csv" dowload> <i class="fa fa-download"></i> Descarga plantilla sedes.CSV</a></li>'+
                        '<li><a href="'+ $('#inputBaseUrl').val() +'/download/sedes/Plantilla_Sedes.xlsx" dowload> <i class="fa fa-download"></i> Descarga plantilla sedes.XLSX </a></li>'+
                        '<li><a href="'+ $('#inputBaseUrl').val() +'/download/priorizacion/Plantilla_Priorizacion.csv" dowload> <i class="fa fa-download"></i> Descarga plantilla priorización .CSV</a></li>'+
                        '<li><a href="'+ $('#inputBaseUrl').val() +'/download/focalizacion/Plantilla_Focalizacion.csv" dowload> <i class="fa fa-download"></i> Descarga plantilla focalización .CSV</a></li>'+
                        '<li><a href="#" onclick="$(\'#exportarManipuladoras\').submit();"> <i class="fa fa-download"></i> Generar archivo manipuladoras </a></li>'+
                        '<li><a id="descargarPlantillaManipuladoras"> <i class="fa fa-download"></i> Descarga plantilla manipuladoras.CSV</a></li>'+                    
                        <?php endif ?>
                        '<ul>'+
                      '</ul>'+
                    '</div>';
  $('.containerBtn').html(botonAcciones);
  <?php } ?>

    // evento editar sedes
    $(document).on('click', '.editarSede', function(){
      var codigoSede = $(this).data('codigo');
      var nombreSede = $(this).data('nombre');
      editarSede(codigoSede, nombreSede);
    });

    // Evento para cambiar de estado a la sede
    $(document).on('change', '.estadoSede', function(){
      var codigoSede = $(this).data('codigo');
      var estadoSede = $(this).data('estado');
      confirmarCambioEstado(codigoSede, estadoSede);
    });

    // Evento para ver la sede
    $(document).on('click', 'tbody td:nth-child(-n+7)', function(){
      var codigoSede = $(this).parent().data('codigosede');
      var nombreSede = $(this).parent().data('nombresede');
      var nombreInstitucion = $(this).parent().data('nombreinstitucion');

      $('#formVerSede #codSede').val(codigoSede);
      $('#formVerSede #nomSede').val(nombreSede);
      $('#formVerSede #nomInst').val(nombreInstitucion);
      $('#formVerSede').submit();
    });

    // Evento para ver dispositivos de la sede
    $(document).on('click', '.verDispositivos', function(){
      var codigoSede = $(this).data('codigo');
      verDispositivosSede(codigoSede);
    });

    // Evento para ver infraestructura de la sede
    $(document).on('click', '.verInfraestructura', function(){
      var codigoSede = $(this).data('codigo');
      verInfraestructurasSede(codigoSede);
    });

    // Evento para ver titulares de la sede
    $(document).on('click', '.verTitulares', function(){
      var codigoSede = $(this).data('codigo');
      verTitularesSede(codigoSede);
    });

    // Evitar el burbujeo del DOM en el control dropbox
    $(document).on('click', '.dropdown li:nth-child(6)', function(e) { e.stopPropagation(); });

    // Configuración para la validación del formulario de búsqueda de sedes.
    jQuery.extend(jQuery.validator.messages, { required: "Campo obligatorio.", remote: "Por favor, rellena este campo.", email: "Por favor, escribe una dirección de correo válida", url: "Por favor, escribe una URL válida.", date: "Por favor, escribe una fecha válida.", dateISO: "Por favor, escribe una fecha (ISO) válida.", number: "Por favor, escribe un número entero válido.", digits: "Por favor, escribe sólo dígitos.", creditcard: "Por favor, escribe un número de tarjeta válido.", equalTo: "Por favor, escribe el mismo valor de nuevo.", accept: "Por favor, escribe un valor con una extensión aceptada.", maxlength: jQuery.validator.format("Por favor, no escribas más de {0} caracteres."), minlength: jQuery.validator.format("Por favor, no escribas menos de {0} caracteres."), rangelength: jQuery.validator.format("Por favor, escribe un valor entre {0} y {1} caracteres."), range: jQuery.validator.format("Por favor, escribe un valor entre {0} y {1}."), max: jQuery.validator.format("Por favor, escribe un valor menor o igual a {0}."), min: jQuery.validator.format("Por favor, escribe un valor mayor o igual a {0}.") });

});
</script>

<?php mysqli_close($Link); ?>