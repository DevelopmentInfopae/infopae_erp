<?php
  include '../../header.php';

  set_time_limit (0);
  ini_set('memory_limit','6000M');

  $titulo = "Instituciones";
  $periodoActual = $_SESSION['periodoActual'];
?>

<div class="row wrapper wrapper-content border-bottom white-bg page-heading">
  <div class="col-lg-8">
      <h2><?php echo $titulo; ?></h2>
      <ol class="breadcrumb">
          <li>
              <a href="<?php echo $baseUrl.$_SESSION['rutaDashboard']; ?>">Home</a>
          </li>
          <li class="active">
              <strong><?php echo $titulo; ?></strong>
          </li>
      </ol>
  </div>
  <div class="col-lg-4">
    <div class="title-action">
      <a href="#" class="btn btn-primary" onclick="crearInstitucion();"><i class="fa fa-plus"></i> Nuevo </a>
    </div>
  </div>
</div>
<!-- /.row wrapper de la cabecera de la seccion -->

<div class="wrapper wrapper-content  animated fadeInRight">
    <div class="row">
        <div class="col-sm-12">
              <div class="ibox">
                  <div class="ibox-content">
                    <table class="table table-striped table-hover selectableRows dataTablesInstituciones" >
                      <thead>
                        <tr>
    						            <th>Municipio</th>
                            <th>Institución</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                      </thead>
                      <tbody>
                          <?php
                          $periodoActual = $_SESSION['periodoActual'];
                          $consulta = " SELECT i.id, i.nom_inst, i.codigo_inst, u.Ciudad AS ciudad, i.estado AS estadoInstitucion FROM instituciones i LEFT JOIN ubicacion u ON i.cod_mun = u.CodigoDANE ORDER BY i.nom_inst ASC ";
                          $resultado = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));
                          if($resultado->num_rows >= 1){
                              while($row = $resultado->fetch_assoc()) { ?>
                                <tr codInst="<?php echo $row["codigo_inst"]; ?>" nomInst="<?php echo $row["nom_inst"]; ?>">
                									<td><?php echo $row["ciudad"]; ?></td>
                									<td><?php echo $row["nom_inst"]; ?></td>
                                  <td class="text-center">
                                    <div class="btn-group">
                                      <div class="dropdown pull-right">
                                        <button class="btn btn-primary btn-sm" type="button" id="dropDownMenu1" data-toggle="dropdown"  aria-haspopup="true">
                                          Acciones <span class="caret"></span>
                                        </button>
                                        <ul class="dropdown-menu pull-right" aria-labelledby="dropDownMenu1">
                                          <li>
                                            <a href="#" onclick="editarInstitucion('<?php echo $row["codigo_inst"]; ?>', '<?php echo $row["nom_inst"]; ?>');"><i class="fa fa-pencil fa-lg"></i> Editar</a>
                                          </li>
                                          <li>
                                            <a href="#">
                                              Estado: &nbsp;
                                              <input type="checkbox" id="inputEstadoIntitucion<?php echo $row["id"]; ?>" data-toggle="toggle" data-on="Activo" data-off="Inactivo" data-size="mini" data-width="70" data-height="24" <?php if($row["estadoInstitucion"] == 1){ echo "checked"; } ?> onchange="confirmarCambioEstado(<?php echo $row["id"]; ?>, this.checked);">
                                            </a>
                                          </li>
                                        </ul>
                                      </div>
                                    </div>
                                  </td>
                								</tr>
                              <?php
                              }// Termina el while
                          }//Termina el if que valida que si existan resultados
                          ?>
                      </tbody>
                      <tfoot>
                        <tr>
              						<th>Municipio</th>
              						<th>Institución</th>
                          <th class="text-center">Acciones</th>
                        </tr>
                      </tfoot>
                    </table>
                  </div>
              </div>
          </div>
     </div>
</div>

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

<!-- Section Scripts -->
<script src="<?php echo $baseUrl; ?>/modules/instituciones/js/instituciones.js"></script>

<!-- Page-Level Scripts -->


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
        buttons: [ {extend: 'excel', title: 'Instituciones', className: "btnExportarExcel"} ]
      });

      var botonAcciones = '<div class="dropdown pull-right">'+
                            '<button class="btn btn-primary btn-sm btn-outline" type="button" id="dropdownMenu2" data-toggle="dropdown" aria-haspopup="true">'+
                              'Acciones <span class="caret"></span>'+
                            '</button>'+
                            '<ul class="dropdown-menu pull-right" aria-labelledby="dropdownMenu2">'+
                              '<li><a tabindex="0" aria-controls="box-table" href="#" onclick="$(\'.btnExportarExcel\').click();"><span class="fa fa-file-pdf-o"></span> Exportar </a></li>'+
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
                            '</ul>'+
                          '</div>';
      $('.containerBtn').html(botonAcciones);
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
<!-- 
<form action="despacho_por_sede.php" method="post" name="formDespachoPorSede" id="formDespachoPorSede">
  <input type="hidden" name="despachoAnnoI" id="despachoAnnoI" value="">
  <input type="hidden" name="despachoMesI" id="despachoMesI" value="">
  <input type="hidden" name="despacho" id="despacho" value="">
</form>

<form action="despachos.php" id="parametrosBusqueda" method="get">
  <input type="hidden" id="pb_annoi" name="pb_annoi" value="">
  <input type="hidden" id="pb_mes" name="pb_mes" value="">
  <input type="hidden" id="pb_diai" name="pb_diai" value="">
  <input type="hidden" id="pb_annof" name="pb_annof" value="">
  <input type="hidden" id="pb_mesf" name="pb_mesf" value="">
  <input type="hidden" id="pb_diaf" name="pb_diaf" value="">
  <input type="hidden" id="pb_tipo" name="pb_tipo" value="">
  <input type="hidden" id="pb_municipio" name="pb_municipio" value="">
  <input type="hidden" id="pb_institucion" name="pb_institucion" value="">
  <input type="hidden" id="pb_sede" name="pb_sede" value="">
  <input type="hidden" id="pb_tipoDespacho" name="pb_tipoDespacho" value="">
  <input type="hidden" id="pb_ruta" name="pb_ruta" value="">
  <input type="hidden" id="pb_btnBuscar" name="pb_btnBuscar" value="">
</form> -->

</body>
</html>
