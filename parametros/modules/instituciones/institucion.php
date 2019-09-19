<?php
  include '../../header.php';
  set_time_limit (0);
  ini_set('memory_limit','6000M');
  $periodoActual = $_SESSION['periodoActual'];

  // Quien consulta es el rector de la Institución
  if($_SESSION['perfil'] == 6){
    $rectorDocumento = $_SESSION['num_doc'];
    $consulta = " SELECT instituciones.*, ubicacion.Ciudad as municipio from instituciones left join ubicacion on instituciones.cod_mun = ubicacion.CodigoDANE where cc_rector = $rectorDocumento limit 1  ";
  }else{
  	$codInst = $_REQUEST['codInst'];
  	$consulta = " SELECT instituciones.*, ubicacion.Ciudad as municipio from instituciones left join ubicacion on instituciones.cod_mun = ubicacion.CodigoDANE where codigo_inst = $codInst ";
  }
  $resultado = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));

  if($resultado->num_rows >= 1){
  	$row = $resultado->fetch_assoc();
  	$institucionCodigo = $row['codigo_inst'];
  	$institucionNombre = $row['nom_inst'];
  	$institucionRector = $row['cc_rector'];
  	$institucionTel = $row['tel_int'];
  	$institucionCorreo = $row['email_inst'];
  	$institucionCodigoDane = $row['cod_mun'];
  	$institucionMunicipio = $row['municipio'];
  }
?>

<div class="row wrapper wrapper-content border-bottom white-bg page-heading">
  <div class="col-lg-8">
      <h2><?php echo $institucionNombre; ?></h2>
      <ol class="breadcrumb">
          <li>
            <a href="<?php echo $baseUrl; ?>">Home</a>
          </li>
          <li>
            <a href="<?php echo $baseUrl . '/modules/instituciones/instituciones.php'; ?>">Instituciones</a>
          </li>
          <li class="active">
            <strong>Ver institución </strong>
          </li>
      </ol>
  </div>
  <div class="col-lg-4">
    <div class="title-action">
      <div class="btn-group">
        <div class="dropdown pull-right">
          <button class="btn btn-primary" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true">
            Acciones <span class="caret"></span>
          </button>
          <ul class="dropdown-menu pull-right keep-open-on-click" aria-labelledby="dropdownMenu1">
            <li>
              <a href="#" data-codigoinstitucion="<?php echo $_POST["codInst"]; ?>" name="editarInstitucion" id="editarInstitucion"><i class="fa fa-pencil"></i> Editar </a>
            </li>
            <li >
              <a href="#">
                Estado:
                <input type="checkbox" id="inputEstadoIntitucion<?php echo $row["id"]; ?>" data-toggle="toggle" data-size="mini" data-on="Activo" data-off="Inactivo" data-width="70" data-height="24" <?php if($row["estado"] == 1){ echo "checked"; } ?> onchange="confirmarCambioEstado(<?php echo $row["id"]; ?>, this.checked);">
              </a>
            </li>
          </ul>
         </div>
      </div>
    </div>
  </div>
</div>
<!-- /.row wrapper de la cabecera de la seccion -->

<div class="wrapper wrapper-content  animated fadeInRight">
      <div class="row">
          <div class="col-sm-8">
              <div class="ibox">
                  <div class="ibox-content">
                      <h2>Sedes</h2>
                      <table class="table table-striped table-bordered table-hover selectableRows dataTablesSedes" >
                        <thead>
                          <tr>
                              <th>Nombre de la sede</th>
                              <th>Nombre coordinador</th>
                              <th>Jornada</th>
                              <th>Tipo validación</th>
                              <th>Acciones</th>
                          </tr>
                        </thead>
                        <tbody>
                          <?php
                            $periodoActual = $_SESSION['periodoActual'];
                            $consulta = " SELECT sed.id, sed.nom_sede, sed.cod_sede, sed.estado AS estadoSede, usu.nombre AS nombreCoordinador, jor.nombre AS nombreJornada, tipo_validacion AS tipoValidacion FROM sedes$periodoActual sed LEFT JOIN usuarios usu ON usu.id = sed.id_coordinador LEFT JOIN jornada jor ON jor.id = sed.jornada WHERE cod_inst = $institucionCodigo ORDER BY nom_sede ASC";
                            $resultado = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));
                            if($resultado->num_rows >= 1){
                              while($row = $resultado->fetch_assoc()) { ?>
                                <tr codsede="<?php echo $row["cod_sede"]; ?>" nomsede="<?php echo $row["nom_sede"]; ?>">
                                  <td><?php echo $row["nom_sede"]; ?></td>
                                  <td><?php echo $row["nombreCoordinador"]; ?></td>
                                  <td><?php echo $row["nombreJornada"]; ?></td>
                                  <td><?php echo $row["tipoValidacion"]; ?></td>
                                  <td class="text-center">
                                    <div class="btn-group">
                                      <div class="dropdown pull-right">
                                        <button class="btn btn-primary btn-sm" type="button" id="dropDownMenu1" data-toggle="dropdown"  aria-haspopup="true">
                                          Acciones <span class="caret"></span>
                                        </button>
                                        <ul class="dropdown-menu pull-right" aria-labelledby="dropDownMenu1">
                                          <li>
                                            <a href="#" onclick=""><i class="fa fa-pencil fa-lg"></i> Editar</a>
                                          </li>
                                          <li>
                                            <a href="#">
                                              Estado: &nbsp;
                                              <input type="checkbox" id="inputEstadoSede" data-toggle="toggle" data-on="Activo" data-off="Inactivo" data-size="mini" data-width="70" data-height="24" <?php if($row["estadoSede"] == 1){ echo "checked"; } ?> onchange="confirmarCambioEstado(<?php echo $row["id"]; ?>, this.checked);">
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
                            <th>Nombre de la sede</th>
                            <th>Nombre coordinador</th>
                            <th>Jornada</th>
                            <th>Tipo validación</th>
                            <th>Acciones</th>
                        </tr>
                      </tfoot>
                      </table>
                  </div>
              </div>
          </div>
          <div class="col-sm-4">
            <div class="ibox ">
              <div class="ibox-content">
                <?php
                // Consulta para buscar los datos del Rector
      						if($institucionRector != ''){
                    $consulta = " SELECT * FROM usuarios WHERE num_doc = $institucionRector LIMIT 1 ";
                    $resultado = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));
                    if($resultado->num_rows >= 1){
                      $row = $resultado->fetch_assoc();
                      $rectorNombre = $row['nombre'];
                      $rectorFoto = $row['foto'];
                      $rectorCorreo = $row['email'];
                      $rectorUsuario = $row['id'];
                    }
						      }
                ?>
                <div class="tab-content">
                  <div id="contact-1" class="tab-pane active">
							    <?php if($institucionRector != ''){ ?>
                    <div class="row m-b-lg">
                      <div class="col-lg-12 text-center">
                        <h2><strong>Rector:</strong> <?php echo $rectorNombre; ?></h2>
                        <div class="m-b-sm">
                            <img alt="image" class="img-circle" src="<?php echo $rectorFoto; ?>" style="width: 62px">
                        </div>
                        <a href="mailto<?php echo $rectorCorreo; ?>"><?php echo $rectorCorreo; ?></a>
                      </div>             
                    </div>
						  	  <?php } ?>
                    <div class="client-detail">
                      <div class="full-height-scroll">
								      <?php if($institucionRector != ''){ ?>
                        <strong>Últimos Accesos</strong>
                        <ul class="list-group clear-list">
                        <?php
      										// Consulta para buscar los ultimos accesos del Rector
      										$consulta = " SELECT * FROM bitacora WHERE usuario = $rectorUsuario AND tipo_accion = 1 order by fecha desc limit 5  ";
      										$resultado = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));
      										if($resultado->num_rows >= 1){
      											$auxPrimer = 0;
      											while ($row = $resultado->fetch_assoc()) {
      												$aux = $row['fecha'];
      												$aux = date("h:i:s A d/m/Y", strtotime($aux));
												?>
      												<li class="list-group-item <?php if($auxPrimer == 0){ ?>fist-item<?php } ?>">
      													<span class="pull-right"> <?php echo $aux; ?> </span>
      													Inició sesión
      												</li>
												<?php
      												$auxPrimer++;
                            }
                          }
                        ?>
                        </ul>
      							  <?php } ?>

                                    <?php if( $institucionTel != '' || $institucionCorreo != '' || $institucionCodigoDane != '' || $institucionMunicipio != ''){ ?>
                                        <p><strong>Datos de la institución:</strong></p>
                                    <?php } ?>





                                    <?php if( $institucionCodigoDane != ''){ ?>
                                        <p> <strong>Codigo DANE:</strong> <?php echo $institucionCodigoDane; ?> </p>
                                    <?php } ?>
                                    <?php if( $institucionMunicipio != ''){ ?>
                                        <p> <strong>Municipio:</strong> <?php echo $institucionMunicipio; ?> </p>
                                    <?php } ?>
                                    <?php if( $institucionTel != ''){ ?>
                                        <p> <strong>Tel:</strong> <?php echo $institucionTel; ?> </p>
                                    <?php } ?>
                                    <?php if( $institucionCorreo != ''){ ?>
                                        <p> <strong>Correo:</strong> <a href="mailto:<?php echo $institucionCorreo; ?>"><?php echo $institucionCorreo; ?></a> </p>
                                    <?php } ?>



                              </div>
                              </div>
                          </div>
                      </div>
                  </div><!-- /.ibox-content -->
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
    <script src="<?php echo $baseUrl; ?>/theme/js/plugins/toggle/toggle.min.js"></script>
    <script src="<?php echo $baseUrl; ?>/theme/js/plugins/toastr/toastr.min.js"></script>

    <!-- Section Scripts -->
    <script src="<?php echo $baseUrl; ?>/modules/instituciones/js/institucion.js"></script>

    <!-- Page-Level Scripts -->
    <script>
      $(document).ready(function(){
        // Evitar el burbujeo del DOM en el control dropbox
        $(document).on('click', '.dropdown li:nth-child(2)', function(e) { e.stopPropagation(); }); 

        // Configuración para la tabla de sedes.
        $('.dataTablesSedes').DataTable({
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
          buttons: [ {extend: 'excel', title: 'Sedes', className: 'btnExportarExcel'} ]
        });

        var botonAcciones = '<div class="dropdown pull-right">'+
                              '<button class="btn btn-primary btn-sm btn-outline" type="button" id="dropdownMenu2" data-toggle="dropdown" aria-haspopup="true">'+
                                'Acciones <span class="caret"></span>'+
                              '</button>'+
                              '<ul class="dropdown-menu pull-right" aria-labelledby="dropdownMenu2">'+
                                '<li><a href="#"> <i class="fa fa-plus"></i> Nueva </a></li>'+
                                '<li><a tabindex="0" aria-controls="box-table" href="#" onclick="$(\'.btnExportarExcel\').click();"><span class="fa fa-file-pdf-o"></span> Exportar </a></li>'+
                                '<li class="divider"></li>'+
                                '<li><a href="'+ $('#inputBaseUrl').val() +'/download/sedes/Plantilla_Sedes.csv"><i class="fa fa-download"></i> Descarga Plantilla .CSV </a></li>'+
                                '<li><a href="'+ $('#inputBaseUrl').val() +'/download/sedes/Plantilla_Sedes.xlsx"><i class="fa fa-download"></i> Descarga Plantilla .XLSX </a></li>'+
                              '</ul>'+
                            '</div>';
        $('.containerBtn').html(botonAcciones);
      });
    </script>


<?php mysqli_close($Link); ?>

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
</form>

<form action="sede.php" method="post" name="formVerSede" id="formVerSede">
  <input type="hidden" name="codSede" id="codSede">
  <input type="hidden" name="nomSede" id="nomSede">
  <input type="hidden" name="nomInst" id="nomInst" value="<?php echo $institucionNombre; ?>">
</form>

<form action="instituciones_editar.php" method="post" name="formEditarInstitucion" id="formEditarInstitucion">
  <input type="hidden" name="codigoInstitucion" id="codigoInstitucion">
</form>
</body>
</html>
