<?php 
$titulo = 'Ver dispositivo biométrico';
require_once '../../header.php'; 
$periodoActual = $_SESSION['periodoActual'];

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
      <div class="dropdown pull-right">
        <button class="btn btn-primary" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true">  Acciones <span class="caret"></span>
        </button>
        <ul class="dropdown-menu pull-right" aria-labelledby="dropdownMenu1">
          <?php if ($_SESSION['perfil'] == 1 || $_SESSION['perfil'] == 0): ?>
          <li><a onclick="editarDispositivo(<?php echo $_POST['idDispositivoVer']; ?>)"><span class="fas fa-pencil-alt"></span> Editar </a></li>
          <li><a data-toggle="modal" data-target="#modalEliminarDispositivo"  data-iddispositivo="<?php echo $_POST['idDispositivoVer']; ?>"><span class="fa fa-trash"></span>  Eliminar</a></li>
          <?php endif ?>
          <li><a onclick="exportarDispositivo(<?php echo $_POST['idDispositivoVer']; ?>);"><span class="fa fa-file-excel-o"></span> Exportar </a></li>
        </ul>
      </div>
    </div><!-- /.title-action -->
  </div><!-- /.col -->
</div><!-- /.row -->

<div class="wrapper wrapper-content animated fadeInRight">
  <div class="row">
    <div class="col-lg-12">
      <div class="ibox float-e-margins">
        <div class="ibox-content contentBackground">

<?php if (isset($_POST['idDispositivoVer'])): ?>

  <?php 

    $iddispositivo = $_POST['idDispositivoVer'];
    $consultarDispositivo = "SELECT sede.nom_sede, sede.cod_mun_sede, ubicacion.Ciudad, instituciones.nom_inst, usuarios.nombre as nom_usu, dispositivos.* FROM dispositivos INNER JOIN sedes".$_SESSION['periodoActual']." as sede ON sede.cod_sede = dispositivos.cod_sede INNER JOIN usuarios ON usuarios.id = dispositivos.id_usuario INNER JOIN ubicacion ON ubicacion.CodigoDANE = sede.cod_mun_sede INNER JOIN instituciones ON instituciones.codigo_inst = sede.cod_inst WHERE dispositivos.id = ".$iddispositivo;
    $resultadoDispositivo = $Link->query($consultarDispositivo);
    if ($resultadoDispositivo->num_rows > 0) {
      $infoDispositivo = $resultadoDispositivo->fetch_assoc();
    }
   ?>

<div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
  <div class="panel panel-default">
    <div class="panel-heading" role="tab" id="headingOne">
      <h4 class="panel-title">
        <a class="pull-right" role="button" data-toggle="collapse" data-parent="#accordion" href="#datosDispositivo" aria-expanded="true" aria-controls="datosDispositivo" style="color: #337ab7; display: none;" id="btnEditar_1">
          Ver
        </a>

        Datos de dispositivo
      </h4>
    </div>
    <div id="datosDispositivo" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="headingOne">
      <div class="panel-body">
        <form class="form" id="formDispositivo">
          <div class="form-group col-sm-3">
            <label>Municipio</label>
            <input type="text" class="form-control" value="<?php echo $infoDispositivo['Ciudad'] ?>" readonly>
          </div>
          <div class="form-group col-sm-3">
            <label>Institución</label>
            <input type="text" class="form-control" value="<?php echo $infoDispositivo['nom_inst'] ?>" readonly>
          </div>
          <div class="form-group col-sm-3">
            <label>Sede</label>
            <input type="text" class="form-control" value="<?php echo $infoDispositivo['nom_sede'] ?>" readonly>
            <input type="hidden" id="cod_sede" value="<?php echo $infoDispositivo['cod_sede'] ?>">
          </div>
          <div class="form-group col-sm-3">
            <label>Referencia</label>
            <input type="text" name="referencia" id="referencia" class="form-control" value="<?php echo $infoDispositivo['referencia'] ?>" readonly required>
          </div>
          <div class="form-group col-sm-3">
            <label>Número de serial</label>
            <input type="number" name="num_serial" id="num_serial" class="form-control" value="<?php echo $infoDispositivo['num_serial'] ?>" readonly required>
            <em style="color: #cc5965; font-size: 13px; display: none;" id="existeNumSerial">Un dispositivo con este n° de serial ya existe.</em>
          </div>
          <div class="form-group col-sm-3">
            <label>Usuario</label>
            <input type="text" name="id_usuario" id="id_usuario" class="form-control" value="<?php echo $infoDispositivo['nom_usu']; ?>" readonly required>
          </div>
          <div class="form-group col-sm-3">
            <label>Tipo dispositivo</label>
            <input type="text" name="tipo" id="tipo" class="form-control" value="<?php echo $infoDispositivo['tipo']; ?>" readonly required>
          </div>
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
        <form class="form" id="formBiometria">
          <div class="col-sm-12">
            <table class="table" id="tablaEstudiantes">
              <thead>
                <tr>
                  <th>Tipo documentación</th>
                  <th>N° identificación</th>
                  <th>Nombre estudiante</th>
                  <th>Grado</th>
                  <th>Id biometría de estudiante</th>
                </tr>
              </thead>
              <tbody id="tbodyEstudiantes">
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
                   $focalizaciones[] = $focalizacion['tabla'];
                 }
               }

              if (strlen($infoDispositivo['id']) == 1) {
                 $idDisp = "00".$infoDispositivo['id'];
               } else if (strlen($infoDispositivo['id']) == 2) {
                 $idDisp = "0".$infoDispositivo['id'];
               } else if (strlen($infoDispositivo['id']) == 3) {
                 $idDisp = $infoDispositivo['id'];
               }
               $grados = [];
               $consultarGrados = "SELECT * FROM grados ";
               $resultadoGrados = $Link->query($consultarGrados);
               if ($resultadoGrados->num_rows > 0) {
                 while ($gradosInfo = $resultadoGrados->fetch_assoc()) {
                   $grados[$gradosInfo['id']] = $gradosInfo['nombre'];
                 }
               }

               $selectFoc = "";
               $sqlFoc = ""; 
               foreach ($focalizaciones as $focalizacion => $valor) {
                $selectFoc = " ".$valor.".nom1, ".$valor.".ape1, ".$valor.".cod_grado, ".$valor.".cod_sede, ";
                $sqlFoc.=" INNER JOIN ".$valor." ON ".$valor.".num_doc = biometria.num_doc ";
               }

                $consultarBiometria = "SELECT ".$selectFoc." tipodocumento.nombre as tdocnom, biometria.* FROM biometria INNER JOIN tipodocumento ON biometria.tipo_doc = tipodocumento.id ".$sqlFoc." WHERE id_dispositivo = ".$idDisp;
                // echo $consultarBiometria;
                $resultadoBiometria = $Link->query($consultarBiometria);

                if ($resultadoBiometria->num_rows > 0) {
                  while ($biometria = $resultadoBiometria->fetch_assoc()) { 
                    if ($biometria['cod_sede'] == $infoDispositivo['cod_sede']) {
                    ?>
                   <tr>
                     <td><?php echo $biometria['tdocnom']; ?></td>
                     <td><?php echo $biometria['num_doc']; ?></td>
                     <td><?php echo $biometria['nom1'] ." ".$biometria['ape1']; ?></td>
                     <td><?php echo $grados[$biometria['cod_grado']]; ?></td>
                     <td><?php echo $biometria['id_bioest']; ?></td>
                   </tr>
                  <?php 
                    }
                  }
                } ?> 
              </tbody>
              <tfoot>
                <tr>
                  <th>Tipo documentación</th>
                  <th>N° identificación</th>
                  <th>Nombre estudiante</th>
                  <th>Grado</th>
                  <th>Id biometría de estudiante</th>
                </tr>
              </tfoot>
            </table>
          </div>
        </form>
        <!-- <div class="col-sm-12">
          <button class="btn btn-primary" onclick="submitForm();"><span class="fa fa-check"></span>  Guardar</button>
        </div> -->
      </div>
    </div>
  </div>
</div>
<form method="Post" id="editar_dispositivo" action="editar_dispositivo.php" style="display: none;">
  <input type="hidden" name="idDispositivoEditar" id="idDispositivoEditar">
</form>

<form method="Post" id="exportar_dispositivo" action="exportar_dispositivo.php" style="display: none;">
  <input type="hidden" name="idDispositivoexportar" id="idDispositivoexportar">
</form>



<?php else: ?>
Dispositivo no definido.
<?php endif ?>

        </div><!-- /.ibox-content -->
      </div><!-- /.ibox float-e-margins -->
    </div><!-- /.col-lg-12 -->
  </div><!-- /.row -->
</div><!-- /.wrapper wrapper-content animated fadeInRight -->

 <!-- modal eliminar biometria -->
<div class="modal inmodal fade" id="modalEliminarDispositivo" tabindex="-1" role="dialog" style="display: none;" aria-hidden="true">
 <div class="modal-dialog modal-sm">
   <div class="modal-content">
     <div class="modal-header text-info" style="padding: 15px;">
       <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only">Cerrar</span></button>
       <h3><i class="fa fa-question-circle fa-lg" aria-hidden="true"></i> Confirmación InfoPAE </h3>
     </div>
     <div class="modal-body" style="text-align: center;">
         <span>¿Está seguro de borrar Dispositivo Biométrico?</span>
         <input type="hidden" name="iddispositivoEli" id="iddispositivoEli">
         <!-- <input type="hidden" name="numbiometria" id="numbiometria"> -->
     </div>
     <div class="modal-footer">
       <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal"><i class="fas fa-times"></i> No</button>
       <button type="button" class="btn btn-primary btn-sm" onclick="eliminarDispositivo()"><i class="fa fa-check"></i> Si</button>
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
<!-- Section Scripts -->
<script src="<?php echo $baseUrl; ?>/modules/dispositivos_biometricos/js/dispositivos_biometricos.js"></script>

<script type="text/javascript">
  console.log('Aplicando Data Table');
  dataset1 = $('#tablaEstudiantes').DataTable({
    order: [ 4, 'asc' ],
    pageLength: 25,
    responsive: true,
    dom : '<"html5buttons" B>lr<"containerBtn"><"inputFiltro"f>tip',
    buttons : [{extend:'excel', title:'Biometrias', className:'btnExportarExcel', exportOptions: {columns : [0,1,2,3,4]}}],
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

  var btnAcciones = '<div class="dropdown pull-right" id=""><button class="btn btn-primary btn-sm btn-outline" type="button" id="accionesTabla" data-toggle="dropdown" aria-haspopup="true">Acciones<span class="caret"></span></button><ul class="dropdown-menu pull-right" aria-labelledby="accionesTabla"><li><a onclick="exportarDispositivo(<?php echo $iddispositivo; ?>);"><span class="fa fa-file-excel-o"></span> Exportar </a></li></ul></div>';

  $('.containerBtn').html(btnAcciones);
</script>

<?php mysqli_close($Link); ?>

</body>
</html>