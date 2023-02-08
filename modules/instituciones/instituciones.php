<?php
  include '../../header.php';

  set_time_limit (0);
  ini_set('memory_limit','6000M');

  $titulo = "Instituciones";
  $periodoActual = $_SESSION['periodoActual'];

  if ($permisos['instituciones'] == "0") {
    ?><script type="text/javascript">
      window.open('<?= $baseUrl ?>', '_self');
    </script>
  <?php exit(); 
  }
  else {
    ?><script type="text/javascript">
      const list = document.querySelector(".li_instituciones");
      list.className += " active ";
    </script>
  <?php
  }
  $nameLabel = get_titles('instituciones', 'instituciones', $labels);
?>

<div class="row wrapper wrapper-content border-bottom white-bg page-heading">
  <div class="col-lg-8">
      <h2><?php echo $nameLabel; ?></h2>
      <ol class="breadcrumb">
          <li>
              <a href="<?php echo $baseUrl; ?>">Inicio</a>
          </li>
          <li class="active">
              <strong><?php echo $nameLabel; ?></strong>
          </li>
      </ol>
  </div>
  <?php if($_SESSION['perfil'] == "0" || $permisos['instituciones'] == "2") { ?>
  <div class="col-lg-4">
    <div class="title-action">
      <a href="#" class="btn btn-primary" id="crearInstitucion"><i class="fa fa-plus"></i> Nuevo</a>
    </div>
  </div>
  <?php } ?>
</div>

<div class="wrapper wrapper-content  animated fadeInRight">
    <div class="row">
        <div class="col-sm-12">
              <div class="ibox">
                  <div class="ibox-content">
                    <table class="table table-striped table-hover selectableRows dataTablesInstituciones" >
                      <thead>
                        <tr>
    						            <th>Municipio</th>
                            <th>Código</th>
                            <th>Institución</th>
                            <?php if (isset($_GET['region'])): ?>
                              <th>Zona</th>
                              <th>Región</th>
                            <?php endif ?>
                            <?php if($_SESSION['perfil'] == "0" || $permisos['instituciones'] == "2") { ?>
                            <th class="text-center">Acciones</th>
                            <?php } ?>
                        </tr>
                      </thead>
                      <tbody>
                          <?php
                          $condicionInstitucion = '';
                          if ($_SESSION['perfil'] == 6 ) {
                            $condicionInstitucion = " AND i.cc_rector = " .$_SESSION['num_doc']. " ";
                          }elseif($_SESSION['perfil'] == 7){
                            $condicionInstitucion = " AND s.id_coordinador = " .$_SESSION['num_doc']. " ";
                          }
                          $periodoActual = $_SESSION['periodoActual'];
                          $consulta = " SELECT
                                              i.id,
                                              i.nom_inst,
                                              i.codigo_inst,
                                              u.Ciudad AS ciudad,
                                              u.Region AS region,
                                              s.sector AS sector,
                                              i.estado AS estadoInstitucion
                                        FROM instituciones i
                                            LEFT JOIN ubicacion u ON i.cod_mun = u.CodigoDANE
                                            LEFT JOIN sedes$periodoActual s ON s.cod_inst = i.codigo_inst
                                        WHERE 1=1 $condicionInstitucion    
                                        GROUP BY i.id
                                        ORDER BY i.nom_inst ASC ";
                          $resultado = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));
                          if($resultado->num_rows >= 1){
                              while($row = $resultado->fetch_assoc()) { ?>
                                <tr codInst="<?php echo $row["codigo_inst"]; ?>" nomInst="<?php echo $row["nom_inst"]; ?>">
                									<td><?php echo $row["ciudad"]; ?></td>
                                  <td><?php echo $row["codigo_inst"]; ?></td>
                									<td><?php echo $row["nom_inst"]; ?></td>
                                  <?php if (isset($_GET['region'])): ?>
                                    <td><?php echo $row["sector"] == 1 ? "Rural" : "Urbano"; ?></td>
                                    <td><?php echo $row["region"]; ?></td>
                                  <?php endif ?>
                                  <?php if($_SESSION['perfil'] == "0" || $permisos['instituciones'] == "2") { ?>
                                  <td class="text-center">
                                    <div class="btn-group">
                                      <div class="dropdown pull-right">
                                        <button class="btn btn-primary btn-sm" type="button" id="dropDownMenu1" data-toggle="dropdown"  aria-haspopup="true">
                                          Acciones <span class="caret"></span>
                                        </button>
                                        <ul class="dropdown-menu pull-right" aria-labelledby="dropDownMenu1">
                                          <li>
                                            <a href="#" class="editarInstitucion" data-codigoinstitucion = <?php echo $row['codigo_inst']; ?>><i class="fas fa-pencil-alt fa-lg"></i>  Editar</a>
                                          </li>
                                        </ul>
                                      </div>
                                    </div>
                                  </td>
                                  <?php } ?>
                								</tr>
                              <?php
                              }// Termina el while
                          }//Termina el if que valida que si existan resultados
                          ?>
                      </tbody>
                      <tfoot>
                        <tr>
							            <th>Municipio</th>
							            <th>Código</th>
              				    <th>Institución</th>
                          <?php if($_SESSION['perfil'] == "0" || $permisos['instituciones'] == "2") { ?>
                          <th class="text-center">Acciones</th>
                          <?php } ?>
                        </tr>
                      </tfoot>
                    </table>
                  </div>
              </div>
          </div>
     </div>
</div>

<!-- contenedor de modal para crear una nueva institucion -->
<div id="contenedor_crear_institucion"></div>

<!-- contenedor de modal para crear una nueva institucion -->
<div id="contenedor_editar_institucion"></div>

<!-- Ventana modal confirmar -->
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
        <button type="button" class="btn btn-primary btn-outline btn-sm" data-dismiss="modal" onclick="revertirEstado();">Cancelar</button>
        <button type="button" class="btn btn-primary btn-sm" data-dismiss="modal" onclick="cambiarEstado();">Aceptar</button>
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
<script src="<?php echo $baseUrl; ?>/theme/js/plugins/jasny/jasny-bootstrap.min.js"></script>
<script src="<?php echo $baseUrl; ?>/theme/js/plugins/toggle/toggle.min.js"></script>
<script src="<?php echo $baseUrl; ?>/theme/js/plugins/toastr/toastr.min.js"></script>
<script src="<?php echo $baseUrl; ?>/theme/js/plugins/validate/jquery.validate.min.js"></script>
<script src="<?php echo $baseUrl; ?>/modules/instituciones/js/instituciones.js"></script>

<?php mysqli_close($Link); ?>


  <!-- Page-Level Scripts -->
  <script>
    $(document).ready(function(){
      $(document).on('click', '.dropdown li:nth-child(2)', function(e) { e.stopPropagation(); });

      $('.dataTablesInstituciones').DataTable({
        pageLength: 25,
        responsive: true,
        dom: 'lr<"containerBtn"><"inputFiltro"f>tip<"clear"><"html5buttons"B>',
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
        buttons: [ {extend: 'excel', title: 'Instituciones', className: "btnExportarExcel", exportOptions: { columns: [ 0, 1, 2] } } ]
      });

    <?php if($_SESSION['perfil'] == "0" || ($permisos['instituciones'] == "1" || $permisos['instituciones'] == "2")) { ?>
      var botonAcciones = '<div class="dropdown pull-right">'+
                            '<button class="btn btn-primary btn-sm btn-outline" type="button" id="dropdownMenu2" data-toggle="dropdown" aria-haspopup="true">'+
                              'Acciones <span class="caret"></span>'+
                            '</button>'+
                            '<ul class="dropdown-menu pull-right" aria-labelledby="dropdownMenu2">'+
                              '<li><a tabindex="0" aria-controls="box-table" href="#" onclick="$(\'.btnExportarExcel\').click();"><span class="fa fa-file-pdf-o"></span> Exportar </a></li>'+
                              <?php if ($_SESSION['perfil'] == "0" || $permisos['instituciones'] == "2"): ?>
                              '<li>'+
                                '<a class="fileinput fileinput-new" data-provides="fileinput">'+
                                  '<span class="btn-file">'+
                                    '<i class="fa fa-upload"></i>'+
                                    '<span class="fileinput-new">Importar</span>'+
                                    '<span class="fileinput-exists">Cambiar</span>'+
                                    '<input type="file" name="archivo" id="archivo" onchange="if(!this.value.length) return false; cargarArchivo();" accept=".csv, .xlsx">'+
                                  '</span>'+
                                  '<span class="fileinput-filename center-block"></span>'+
                                  '<span href="#" class="close fileinput-exists" data-dismiss="fileinput" style="float: none">&times;</span>'+
                                '</a>'+
                              '</li>'+
                              '<li class="divider"></li>'+
                              '<li><a href="'+ $('#inputBaseUrl').val() +'/download/instituciones/Plantilla_Instituciones.csv"><i class="fa fa-download"></i> Descarga Plantilla .CSV </a></li>'+
                              '<li><a href="'+ $('#inputBaseUrl').val() +'/download/instituciones/Plantilla_Instituciones.xlsx"><i class="fa fa-download"></i> Descarga Plantilla .XLSX </a></li>'+
                              <?php endif ?>
                              '<li><a href="'+ $('#inputBaseUrl').val() +'/modules/instituciones/instituciones.php<?= isset($_GET['region']) ? "" : "?region=1" ?>"><i class="fa fa-eye"></i> Ver zona </a></li>'+
                            '</ul>'+
                          '</div>';
      $('.containerBtn').html(botonAcciones);
    <?php } ?>
    });
  </script>

  <form action="institucion.php" method="post" name="verInst" id="verInst">
    <input type="hidden" name="codInst" id="codInst">
    <input type="hidden" name="nomInst" id="nomInst">
  </form>

  <form action="instituciones_editar.php" method="post" name="formEditarInstitucion" id="formEditarInstitucion">
    <input type="hidden" name="codigoInstitucion" id="codigoInstitucion">
    <input type="hidden" name="nombreInstitucion" id="nombreInstitucion">
  </form>

  <form action="../dispositivos_biometricos/index.php" method="post" name="formDispositivosSede" id="formDispositivosSede">
    <input type="hidden" name="cod_inst" id="cod_inst" value="">
  </form>

  <form action="../infraestructuras/index.php" method="post" name="formInfraestructura" id="formInfraestructura">
    <input type="hidden" name="cod_inst" id="cod_inst" value="">
  </form>

  <form action="../titulares_derecho/index.php" method="post" name="formTitulares" id="formTitulares">
    <input type="hidden" name="cod_inst" id="cod_inst" value="">
  </form>

</body>
</html>
