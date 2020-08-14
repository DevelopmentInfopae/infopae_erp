<?php 
$titulo = 'Infraestructuras';
require_once '../../header.php'; 
$periodoActual = $_SESSION['periodoActual'];
?>

<style type="text/css">

</style>
<div class="row wrapper wrapper-content border-bottom white-bg page-heading">
  <div class="col-lg-8">
    <h2><?php echo $titulo; ?></h2>
    <ol class="breadcrumb">
      <li>
        <a href="<?php echo $baseUrl; ?>">Inicio</a>
      </li>
      <li class="active">
        <strong><?php echo $titulo; ?></strong>
      </li>
    </ol>
  </div><!-- /.col -->
  <div class="col-lg-4">
    <?php if ($_SESSION['perfil'] == 1 || $_SESSION['perfil'] == 0): ?>
      <div class="title-action">
        <button class="btn btn-primary" onclick="window.location.href = 'nueva_infraestructura.php';"><span class="fa fa-plus"></span>  Nuevo</button>
      </div>
    <?php endif ?>
  </div><!-- /.col -->
</div><!-- /.row -->

<div class="wrapper wrapper-content animated fadeInRight">

<div class="row">
    <div class="col-lg-12">
        <div class="ibox float-e-margins border-bottom">
            <div class="ibox-content">
                <div class="row">
                    <div class="col-lg-12">
                        <form method="POST">
                          <div class="row">
                            <div class="form-group col-md-3">
                              <label>Municipio</label>
                              <select name="municipio" id="municipio_buscar" class="form-control">
                                <option value="">Seleccione uno</option>
                                <?php
                                  $codigoCiudad = $_SESSION['p_CodDepartamento'];
                                  $consulta1= "SELECT DISTINCT CodigoDANE, Ciudad FROM ubicacion where CodigoDANE LIKE '$codigoCiudad%' order by ciudad asc; ";
                                  $result1 = $Link->query($consulta1) or die ('Unable to execute query. '. mysqli_error($Link));
                                  if($result1){
                                    while($row1 = $result1->fetch_assoc()){
                                      $selected = '';
                                      if (!isset($_POST['municipio'])) {
                                        if (isset($row['cod_mun']) && $row['cod_mun'] == $row1['CodigoDANE'] || $municipio_defecto["CodMunicipio"] == $row1['CodigoDANE']) {
                                          $selected = 'selected="selected"';
                                        }
                                      } else {
                                        if ($_POST['municipio'] == $row1['CodigoDANE']) {
                                          $selected = 'selected="selected"';
                                        }
                                      }

                                  ?>
                                      <option value="<?php echo $row1['CodigoDANE']; ?>" <?= $selected ?>>
                                        <?php echo $row1['Ciudad']; ?>
                                      </option>
                                <?php
                                    }
                                  }
                                ?>
                              </select>
                            </div>
                            <div class="form-group col-md-3">
                              <label>Institución</label>
                              <select name="institucion" id="institucion_buscar" class="form-control" <?= isset($_POST['institucion']) ? "data-institucion='".$_POST['institucion']."'" : "" ?> >
                                <option value="">Seleccione</option>
                              </select>
                            </div>
                            <div class="form-group col-md-3">
                              <label>Sede</label>
                              <select name="sede" id="sede_buscar" class="form-control" <?= isset($_POST['sede']) ? "data-sede='".$_POST['sede']."'" : "" ?> >
                                <option value="">Seleccione Institución</option>
                              </select>
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
                              <select class="form-control" name="id_Complem_JMJT" id="id_Complem_JMJT">
                                <option value="">Seleccione...</option>
                                <?php echo $opciones; ?>
                              </select>
                            </div>
                          </div>
                          <div class="row">
                            <div class="form-group col-sm-3">
                              <label>Almuerzo</label>
                              <select class="form-control" name="id_Almuerzo" id="id_Almuerzo">
                                <option value="">Seleccione...</option>
                                <?php echo $opciones; ?>
                              </select>
                            </div>
                            <div class="form-group col-sm-3">
                              <label>¿Cuenta con comedor escolar?</label><br>
                              <select name="Comedor_Escolar" id="Comedor_Escolar" class="form-control">
                                <option value="">Seleccione...</option>
                                <option value="1" <?= isset($_POST['Comedor_Escolar']) && $_POST['Comedor_Escolar'] == '1' ? 'selected="selected"' : '' ?>>Si</option>
                                <option value="0" <?= isset($_POST['Comedor_Escolar']) && $_POST['Comedor_Escolar'] == '0' ? 'selected="selected"' : '' ?>>No</option>
                              </select>
                            </div>
                            <div class="form-group col-sm-3">
                              <label>Concepto sanitario</label>
                              <select class="form-control" name="Concepto_Sanitario" id="Concepto_Sanitario">
                                <option value="">Seleccione...</option>
                                <option value="1" <?= isset($_POST['Concepto_Sanitario']) && $_POST['Concepto_Sanitario'] == '1' ? 'selected="selected"' : '' ?>>Favorable</option>
                                <option value="2" <?= isset($_POST['Concepto_Sanitario']) && $_POST['Concepto_Sanitario'] == '2' ? 'selected="selected"' : '' ?>>Favorable con requerimiento</option>
                                <option value="0" <?= isset($_POST['Concepto_Sanitario']) && $_POST['Concepto_Sanitario'] == '0' ? 'selected="selected"' : '' ?>>Desfavorable</option>
                              </select>
                            </div>
                            <!-- <div class="form-group col-sm-3">
                              <label>Fecha de expedición</label>
                              <input type="date" class="form-control" name="fecha_expedicion" id="fecha_expedicion" <?= isset($_POST['fecha_expedicion']) ? 'value="'.$_POST['fecha_expedicion'].'"' : '' ?>>
                            </div> -->
                            <div class="form-group col-sm-3">
                              <label>Rural / Urbana</label>
                              <select name="sector" id="sector" class="form-control">
                                <option value="">Seleccione...</option>
                                <option value="1" <?= isset($_POST['sector']) && $_POST['sector'] == '1' ? 'selected="selected"' : '' ?>>Rural</option>
                                <option value="2" <?= isset($_POST['sector']) && $_POST['sector'] == '2' ? 'selected="selected"' : '' ?>>Urbano</option>
                              </select>
                            </div>
                          </div>
                          <div class="row">
                            <div class="col-md-12">
                              <button class="btn btn-primary"> <span class="fa fa-search"></span> Buscar</button>
                            </div>
                          </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

  <div class="row">
    <div class="col-lg-12">
      <div class="ibox float-e-margins">
        <div class="ibox-content contentBackground">

             <!--  <div class="dropdown pull-right">
                <button class="btn btn-primary btn-sm btn-outline" type="button" id="accionesTabla" data-toggle="dropdown" aria-haspopup="true">
                  Acciones 
                  <span class="caret"></span>
                </button>
                <ul class="dropdown-menu pull-right" aria-labelledby="accionesTabla">
                  <li><a onclick="$('.btnExportarExcel').click()"><span class="fa fa-file-excel-o"></span> Exportar </a></li>
                </ul>
              </div> -->
          
        	<table class="table table-striped table-hover selectableRows" id="tablaInfraestructuras">
        		<thead>
        			<tr>
                <th>Municipio</th>
        				<th>Institución</th>
        				<th>Sede</th>
                <th>Rural / Urbana</th>
                <th>Complemento JM/JT</th>
                <th>Almuerzo</th>
                <th>¿Cuenta con comedor?</th>
                <th>Concepto sanitario</th>
                <th>Fecha de expedición</th>
                <th>Acciones</th>
        			</tr>
        		</thead>
        		<tbody>
        		<?php 
              $instituciones = array();
              $consultaInstituciones = "SELECT DISTINCT codigo_inst, nom_inst, cod_mun FROM instituciones WHERE EXISTS (SELECT cod_inst FROM sedes".$_SESSION['periodoActual']." WHERE cod_inst = codigo_inst) ORDER BY nom_inst ASC;";
              $resultadoInstituciones = $Link->query($consultaInstituciones);
              if ($resultadoInstituciones->num_rows > 0) {
                while ($row = $resultadoInstituciones->fetch_assoc()) {
                  $instituciones[$row['codigo_inst']] = $row['nom_inst'];
                }
              }

              $sedes = array();
              $consultarSedes = "SELECT DISTINCT cod_sede, nom_sede FROM sedes".$_SESSION['periodoActual']." ORDER BY nom_sede ASC;";
              $resultadoSedes = $Link->query($consultarSedes);
              if ($resultadoSedes->num_rows > 0) {
                while ($row = $resultadoSedes->fetch_assoc()) {
                  $sedes[$row['cod_sede']] = $row['nom_sede'];
                }
              }

              $modalidades = array();
              $consultarModalidad = "SELECT * FROM modalidad_suministro;";
              $resultadoModalidad = $Link->query($consultarModalidad);
              if ($resultadoModalidad->num_rows > 0) {
                while ($row = $resultadoModalidad->fetch_assoc()) {
                  $modalidades[$row['id']] = $row['Descripcion'];
                }
              }

              $sectores = array('1' => 'Rural', '2' => 'Urbano', '0' => 'No especificado.');
              $conceptos_sanitario = array('1' => 'Favorable', '2' => 'Favorable con requerimiento','0' => 'Desfavorable');
              $estados = array('1' => 'Si', '0' => 'No', '2' => 'No aplica');

              $municipio = isset($_POST['municipio']) ? $_POST['municipio'] : NULL;
              $cod_inst = isset($_POST['cod_inst']) ? $_POST['cod_inst'] : NULL;
              $institucion = isset($_POST['institucion']) ? $_POST['institucion'] : NULL;
              $sede = isset($_POST['sede']) ? $_POST['sede'] : NULL;
              $id_Complem_JMJT = isset($_POST['id_Complem_JMJT']) ? $_POST['id_Complem_JMJT'] : NULL;
              $id_Almuerzo = isset($_POST['id_Almuerzo']) ? $_POST['id_Almuerzo'] : NULL;
              $Comedor_Escolar = isset($_POST['Comedor_Escolar']) ? $_POST['Comedor_Escolar'] : NULL;
              $Concepto_Sanitario = isset($_POST['Concepto_Sanitario']) ? $_POST['Concepto_Sanitario'] : NULL;
              $fecha_expedicion = isset($_POST['fecha_expedicion']) ? $_POST['fecha_expedicion'] : NULL; 
              $sector = isset($_POST['sector']) ? $_POST['sector'] : NULL;

              $condicion = '';

              if ($cod_inst != NULL || $institucion != NULL) {
                $condicion = "infraestructura.cod_inst = ".($cod_inst != NULL ? $cod_inst : $institucion);
              }

              if ($sede != NULL) {
                if (!empty($condicion)) {
                  $condicion.=" AND ";
                }
                $condicion.=" infraestructura.cod_sede = ".$sede;
              }

              if ($id_Complem_JMJT != NULL) {
                if (!empty($condicion)) {
                  $condicion.=" AND ";
                }
                $condicion.=" infraestructura.id_Complem_JMJT = ".$id_Complem_JMJT;
              }

              if ($id_Almuerzo != NULL) {
                if (!empty($condicion)) {
                  $condicion.=" AND ";
                }
                $condicion.=" infraestructura.id_Almuerzo = ".$id_Almuerzo;
              }

              if ($Comedor_Escolar != NULL) {
                if (!empty($condicion)) {
                  $condicion.=" AND ";
                }
                $condicion.=" infraestructura.Comedor_Escolar = ".$Comedor_Escolar;
              }

              if ($Concepto_Sanitario != NULL) {
                if (!empty($condicion)) {
                  $condicion.=" AND ";
                }
                $condicion.=" infraestructura.Concepto_Sanitario = ".$Concepto_Sanitario;
              }

              if ($fecha_expedicion != NULL) {
                if (!empty($condicion)) {
                  $condicion.=" AND ";
                }
                $condicion.=" infraestructura.Fecha_Expe = '".$fecha_expedicion."'";
              }

              if ($municipio != NULL || $sector != NULL) {

                if ($municipio != NULL) {
                  if (!empty($condicion)) {
                    $condicion.=" AND ";
                  }
                  $condicion.=" S.cod_mun_sede = ".$municipio;
                }

                if ($sector != NULL) {
                  if (!empty($condicion)) {
                    $condicion.=" AND ";
                  }
                  $condicion.=" S.sector = ".$sector;
                }

                if (!empty($condicion)) {
                  $condicion = "WHERE ".$condicion;
                }

                $consulta = "SELECT infraestructura.* FROM infraestructura
                              INNER JOIN sedes".$_SESSION['periodoActual']." AS S ON S.cod_sede = infraestructura.cod_sede ".$condicion;
              } else {
                if (!empty($condicion)) {
                  $condicion = "WHERE ".$condicion;
                }
                $consulta = "SELECT * FROM infraestructura ".$condicion;
              }

              // echo $consulta;

                $result1 = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));
                if($result1->num_rows > 0){

                  while($row1 = $result1->fetch_assoc()){ 

                    $consultarMunicipioInst = "SELECT ubicacion.Ciudad FROM ubicacion, instituciones WHERE instituciones.cod_mun = ubicacion.CodigoDANE AND instituciones.codigo_inst = ".$row1['cod_inst'];
                    $resultadoMunicipioInst = $Link->query($consultarMunicipioInst);
                    if ($resultadoMunicipioInst->num_rows > 0) {
                      if ($municipio = $resultadoMunicipioInst->fetch_assoc()) {
                        $nomMunicipio = $municipio['Ciudad'];
                      }
                    }

                    $consultarSectorSede = "SELECT sector FROM sedes".$_SESSION['periodoActual']." WHERE cod_sede = ".$row1['cod_sede'];
                    $resultadoSectorSede = $Link->query($consultarSectorSede);
                    if ($resultadoSectorSede->num_rows > 0) {
                      if ($sectorSede = $resultadoSectorSede->fetch_assoc()) {
                        $nomSector = $sectores[$sectorSede['sector']];
                      }
                    }
                  	?>

                  	<tr idinfraestructura="<?php echo $row1['id']; ?>"> 
                      <td><?php echo $nomMunicipio; ?></td>
                  		<td><?php echo $instituciones[$row1['cod_inst']]; ?></td>
                  		<td><?php echo $sedes[$row1['cod_sede']]; ?></td>
                      <td><?php echo $nomSector; ?></td>
                      <td><?php echo $modalidades[$row1['id_Complem_JMJT']]; ?></td>
                      <td><?php echo $modalidades[$row1['id_Almuerzo']]; ?></td>
                      <td><?php echo $estados[$row1['Comedor_Escolar']]; ?></td>
                      <td><?php echo $conceptos_sanitario[$row1['Concepto_Sanitario']]; ?></td>
                      <td><?php echo $row1['Fecha_Expe']; ?></td>
                      <td>
                        <div class="btn-group">
                          <div class="dropdown">
                            <button class="btn btn-primary btn-sm" type="button" id="accionesProducto" data-toggle="dropdown" aria-haspopup="true">
                              Acciones
                              <span class="caret"></span>
                            </button>
                            <ul class="dropdown-menu pull-right" aria-labelledby="accionesProducto">
                              <?php if ($_SESSION['perfil'] == 1 || $_SESSION['perfil'] == 0): ?>
                              <li><a onclick="editarInfraestructura(<?php echo $row1['id']; ?>)"><span class="fa fa-pencil"></span>  Editar</a></li>
                                <li>
                                  <a data-toggle="modal" data-target="#modalEliminarInfraestructura"  data-idinfraestructura="<?php echo $row1['id']; ?>"><span class="fa fa-trash"></span>  Eliminar</a>
                                </li>
                              <?php endif ?>
                               <li>
                                <a href="exportar_infraestructuras.php?id=<?= $row1['id'] ?>"><span class="fa fa-file-excel-o"></span> Exportar</a>
                               </li>
                            </ul>
                          </div>
                        </div>
                      </td>
                  	</tr>
                 <?php 
             		 }
                }
	            ?>
        		</tbody>
        	</table>
        </div><!-- /.ibox-content -->
      </div><!-- /.ibox float-e-margins -->
    </div><!-- /.col-lg-12 -->
  </div><!-- /.row -->
</div><!-- /.wrapper wrapper-content animated fadeInRight -->
<form method="Post" id="ver_infraestructura" action="ver_infraestructura.php" style="display: none;">
  <input type="hidden" name="idinfraestructura" id="idinfraestructuraver">
</form>
<form method="Post" id="editar_infraestructura" action="editar_infraestructura.php" style="display: none;">
  <input type="hidden" name="idinfraestructura" id="idinfraestructuraeditar">
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
<script src="<?php echo $baseUrl; ?>/theme/js/plugins/iCheck/icheck.min.js"></script>
<script src="<?php echo $baseUrl; ?>/theme/js/plugins/steps/jquery.steps.min.js"></script>
<script src="<?php echo $baseUrl; ?>/theme/js/plugins/select2/select2.full.min.js"></script>

<!-- Section Scripts -->
<script src="<?php echo $baseUrl; ?>/modules/infraestructuras/js/infraestructuras.js"></script>

<script type="text/javascript">
  $(document).ready(function(){

    $('.form-group').find('select.form-control').select2({width: "100%"});

    $('#municipio_buscar').trigger('change');

    <?php if (isset($_POST['id_Complem_JMJT'])): ?>
      $('#id_Complem_JMJT').val(<?= $_POST['id_Complem_JMJT'] ?>);
    <?php endif ?>

    <?php if (isset($_POST['id_Almuerzo'])): ?>
      $('#id_Almuerzo').val(<?= $_POST['id_Almuerzo'] ?>);
    <?php endif ?>

  });
</script>

<script type="text/javascript">
  console.log('Aplicando Data Table');
  dataset1 = $('#tablaInfraestructuras').DataTable({
    /*order: [ 0, 'asc' ],*/
    pageLength: 25,
    responsive: true,
    dom : '<"html5buttons" B>lr<"containerBtn"><"inputFiltro"f>tip',
    buttons : [{extend:'excel', title:'Menus', className:'btnExportarExcel', exportOptions: {columns : [0,1,2,3,4,5,6]}}],
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
    });
  var btnAcciones = '<div class="dropdown pull-right" id=""><button class="btn btn-primary btn-sm btn-outline" type="button" id="accionesTabla" data-toggle="dropdown" aria-haspopup="true">Acciones<span class="caret"></span></button><ul class="dropdown-menu pull-right" aria-labelledby="accionesTabla"><li><a onclick="window.open(\'exportar_infraestructuras.php\', \'_blank\');"><span class="fa fa-file-excel-o"></span> Exportar </a></li></ul></div>';

  $('.containerBtn').html(btnAcciones);
</script>

<?php mysqli_close($Link); ?>

</body>
</html>