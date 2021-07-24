<?php
  include '../../header.php';
  set_time_limit (0);
  ini_set('memory_limit','6000M');

  if($_SESSION['perfil'] != "6" && $_SESSION['perfil'] != "7"){
    if(!isset($_REQUEST['codInst'])){ header('Location: instituciones.php'); }
  }

  $periodoActual = $_SESSION['periodoActual'];

  // Quien consulta es el rector de la Institución
  if($_SESSION['perfil'] == 6){
    $rectorDocumento = $_SESSION['num_doc'];
    $consulta = " SELECT instituciones.*, ubicacion.Ciudad as municipio from instituciones left join ubicacion on instituciones.cod_mun = ubicacion.CodigoDANE where cc_rector = $rectorDocumento limit 1  ";
  }
  // Quien consulta es el coordinador
  else if($_SESSION['perfil'] == 7){
    $documentoCoordinador = $_SESSION['num_doc'];
    $consulta = "SELECT i.*, u.Ciudad as municipio FROM instituciones i LEFT JOIN ubicacion u ON i.cod_mun = u.codigoDANE LEFT JOIN sedes$periodoActual s ON s.cod_inst = i.codigo_inst WHERE s.id_coordinador = $documentoCoordinador LIMIT 1 ";
  }
  // Quien consulta no es ni rector ni coordinador y viene el codigo de institucion de instituciones
  else{
  	$codInst = $_REQUEST['codInst'];
  	$consulta = " SELECT instituciones.*, ubicacion.Ciudad as municipio from instituciones left join ubicacion on instituciones.cod_mun = ubicacion.CodigoDANE where codigo_inst = $codInst ";
  }
  // exit(var_dump($consulta));
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
            <a href="<?php echo $baseUrl; ?>">Inicio</a>
          </li>
					<?php   if($_SESSION['perfil'] != "6" && $_SESSION['perfil'] != "7"){ ?>
          <li>
            <a href="<?php echo $baseUrl . '/modules/instituciones/instituciones.php'; ?>">Instituciones</a>
          </li>
				<?php } ?>
          <li class="active">
            <strong>Ver institución </strong>
          </li>
      </ol>
  </div>
  <?php if(($_SESSION['perfil'] == "0" || $permisos['instituciones'] == "1" || $permisos['instituciones'] == "2") && ($_SESSION['perfil'] != "6" && $_SESSION['perfil'] != "7")) { ?>
  <div class="col-lg-4">
    <div class="title-action">
      <div class="btn-group">
        <div class="dropdown pull-right">
          <button class="btn btn-primary" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true">
            Acciones <span class="caret"></span>
          </button>
          <ul class="dropdown-menu pull-right keep-open-on-click" aria-labelledby="dropdownMenu1">
            <?php if ($_SESSION['perfil'] == "0" || $permisos['instituciones'] == "2"): ?>
                <li>
                  <a href="#" class="editarInstitucion" data-codigoinstitucion = <?php echo $_POST["codInst"]; ?>><i class="fas fa-pencil-alt"></i> Editar </a>
                </li>
            <?php endif ?>
            <li>
              <a href="#" class="verDispositivos" data-codigoinstitucion="<?php echo $_POST["codInst"]; ?>"><i class="fa fa-eye fa-lg"></i> Ver dispositivos</a>
            </li>
            <li>
              <a href="#" class="verInfraestructura" data-codigoinstitucion="<?php echo $_POST["codInst"]; ?>"><i class="fa fa-bank fa-lg"></i> Ver Infraestructura</a>
            </li>
            <li>
              <a href="#" class="verTitulares" data-codigoinstitucion="<?php echo $_POST["codInst"] ?>"><i class="fa fa-child fa-lg"></i> Ver Titulares</a>
            </li>
            <?php if ($_SESSION['perfil'] == "0" || $permisos['instituciones'] == "2"): ?>
              <li class="divider"></li>
              <li >
                <a href="#">
                  Estado:
                  <input type="checkbox" id="inputEstadoIntitucion<?php echo $row["id"]; ?>" data-toggle="toggle" data-size="mini" data-on="Activo" data-off="Inactivo" data-width="70" data-height="24" <?php if($row["estado"] == 1){ echo "checked"; } ?> onchange="confirmarCambioEstado(<?php echo $row["id"]; ?>, this.checked);">
                </a>
              </li>
            <?php endif ?>
          </ul>
         </div>
      </div>
    </div>
  </div>
  <?php } ?>
</div>
<!-- contenedor de modal para crear una nueva institucion -->
<div id="contenedor_editar_institucion"></div>

<!-- /.row wrapper de la cabecera de la seccion -->

<div class="wrapper wrapper-content  animated fadeInRight">
      <div class="row">
          <div class="col-sm-8">
              <div class="ibox">
                  <div class="ibox-content">
                      <h2>Sedes</h2>
                      <table class="table table-striped table-hover selectableRows dataTablesSedes" >
                        <thead>
                          <tr>
                            <th>Código sede</th>
                              <th>Nombre sede</th>
                              <th>Nombre coordinador</th>
                              <th>Jornada</th>
                              <th>Tipo validación</th>
                              <?php if(($_SESSION['perfil'] == "0" || $permisos['instituciones'] == "1" || $permisos['instituciones'] == "2") && ($_SESSION['perfil'] != "6" && $_SESSION['perfil'] != "7")) { ?>
                              <th>Acciones</th>
                              <?php } ?>
                          </tr>
                        </thead>
                        <tbody>
                          <?php
                            if ($_SESSION['perfil'] == "7") {
                              $consulta = " SELECT sed.id, sed.nom_sede, sed.cod_sede, sed.estado AS estadoSede, usu.nombre AS nombreCoordinador, jor.nombre AS nombreJornada, tipo_validacion AS tipoValidacion FROM sedes$periodoActual sed LEFT JOIN usuarios usu ON usu.num_doc = sed.id_coordinador LEFT JOIN jornada jor ON jor.id = sed.jornada WHERE cod_inst = $institucionCodigo AND sed.id_coordinador = $documentoCoordinador ORDER BY nom_sede ASC";
                            }else {
                              $consulta = " SELECT sed.id, sed.nom_sede, sed.cod_sede, sed.estado AS estadoSede, usu.nombre AS nombreCoordinador, jor.nombre AS nombreJornada, tipo_validacion AS tipoValidacion FROM sedes$periodoActual sed LEFT JOIN usuarios usu ON usu.num_doc = sed.id_coordinador LEFT JOIN jornada jor ON jor.id = sed.jornada WHERE cod_inst = $institucionCodigo ORDER BY nom_sede ASC";
                            }
                            // exit(var_dump($consulta));
                            $resultado = $Link->query($consulta) or die ('Error al consultar las sedes educativas. '. mysqli_error($Link));
                            if($resultado->num_rows >= 1){
                              while($row = $resultado->fetch_assoc()) { ?>
                                <tr codsede="<?php echo $row["cod_sede"]; ?>" nomsede="<?php echo $row["nom_sede"]; ?>">
                                  <td><?php echo $row["cod_sede"]; ?></td>
                                  <td><?php echo $row["nom_sede"]; ?></td>
                                  <td><?php echo $row["nombreCoordinador"]; ?></td>
                                  <td><?php echo $row["nombreJornada"]; ?></td>
                                  <td><?php echo $row["tipoValidacion"]; ?></td>
                                  <?php if(($_SESSION['perfil'] == "0" || $permisos['instituciones'] == "1" || $permisos['instituciones'] == "2") && ($_SESSION['perfil'] != "6" && $_SESSION['perfil'] != "7")) { ?>
                                  <td class="text-center">
                                    <div class="btn-group">
                                      <div class="dropdown pull-right">
                                        <button class="btn btn-primary btn-sm" type="button" id="dropDownMenu1" data-toggle="dropdown"  aria-haspopup="true">
                                          Acciones <span class="caret"></span>
                                        </button>
                                        <ul class="dropdown-menu pull-right" aria-labelledby="dropDownMenu1">
                                          <?php if ($_SESSION['perfil'] == "0" || $permisos['instituciones'] == "2"): ?>
                                            <li>
                                              <a href="#" class="editarSede" data-codigosede="<?php echo $row["cod_sede"]; ?>"><i class="fas fa-pencil-alt fa-lg"></i> Editar</a>
                                            </li>
                                          <?php endif ?>
                                          <li>
                                            <a href="#" class="verDispositivosSede" data-codigosede="<?php echo $row["cod_sede"]; ?>"><i class="fa fa-eye fa-lg"></i> Ver Dispositivos</a>
                                          </li>
                                          <li>
                                            <a href="#" class="verInfraestructuraSede" data-codigosede="<?php echo $row["cod_sede"]; ?>"><i class="fa fa-bank fa-lg"></i> Ver Infraestructura</a>
                                          </li>
                                          <li>
                                            <a href="#" class="verTitularesSede" data-codigosede="<?php echo $row["cod_sede"]; ?>"><i class="fa fa-child fa-lg"></i> Ver Titulares</a>
                                          </li>
                                          <?php if ($_SESSION['perfil'] == "0" || $permisos['instituciones'] == "2"): ?>
                                            <li class="divider"></li>
                                            <li>
                                              <a href="#">
                                                Estado: &nbsp;
                                                <input type="checkbox" id="inputEstadoSede<?php echo $row["id"]; ?>" data-toggle="toggle" data-on="Activo" data-off="Inactivo" data-size="mini" data-width="70" data-height="24" <?php if($row["estadoSede"] == 1){ echo "checked"; } ?> onchange="confirmarCambioEstadoSede(<?php echo $row["id"]; ?>, <?php echo $row["estadoSede"]; ?> );">
                                              </a>
                                            </li>
                                          <?php endif ?>
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
                          <th>Código sede</th>
                          <th>Nombre de la sede</th>
                          <th>Nombre coordinador</th>
                          <th>Jornada</th>
                          <th>Tipo validación</th>
                          <?php if(($_SESSION['perfil'] == "0" || $permisos['instituciones'] == "1" || $permisos['instituciones'] == "2") && ($_SESSION['perfil'] != "6" && $_SESSION['perfil'] != "7")) { ?>
                          <th>Acciones</th>
                          <?php } ?>
                        </tr>
                      </tfoot>
                      </table>
                  </div>
              </div>
          </div>
          <div class="col-sm-4">
            <div class="ibox ">
              <!-- validamos que no sea coordinador para mostrar la informacion del usuario -->
              <?php if ($_SESSION['perfil'] != "7"): ?>
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
													<?php
													$aux = $rectorFoto;
													$aux = substr( $aux, 5);
													$foto = $baseUrl.$aux;
													if(!is_url_exist($foto)){
														$foto = $baseUrl."/img/no_image48.jpg";
													}
													?>
	                        <img alt="image" class="img-circle" src="<?php echo $foto; ?>" style="width: 62px">
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
      										$resultado = $Link->query($consulta);
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
            <?php endif ?>

            <?php if ($_SESSION['perfil'] == "7"): ?>
              <div class="ibox-content">
                <?php
                  $consulta = " SELECT * FROM usuarios WHERE num_doc = $documentoCoordinador LIMIT 1 ";
                  $resultado = $Link->query($consulta) or die ('Error al consultar los datos de el coordinador. '. mysqli_error($Link));
                    if($resultado->num_rows >= 1){
                      $row = $resultado->fetch_assoc();
                      $coordinadorNombre = $row['nombre'];
                      $coordinadorFoto = $row['foto'];
                      $coordinadorCorreo = $row['email'];
                      $coordinadorUsuario = $row['id'];
                    }
                  
                ?>
                <div class="tab-content">
                  <div id="contact-1" class="tab-pane active">
                    <div class="row m-b-lg">
                      <div class="col-lg-12 text-center">
                        <h2><strong>Coordinador:</strong> <?= $coordinadorNombre; ?></h2>
                        <div class="m-b-sm">
                          <?php
                          $aux = $coordinadorFoto;
                          $aux = substr( $aux, 5);
                          $foto = $baseUrl.$aux;
                          if(!is_url_exist($foto)){
                            $foto = $baseUrl."/img/no_image48.jpg";
                          }
                          ?>
                          <img alt="image" class="img-circle" src="<?= $foto; ?>" style="width: 62px">
                        </div>
                        <a href="mailto<?= $coordinadorCorreo; ?>"><?= $coordinadorCorreo; ?></a>
                      </div>
                    </div>
                    <div class="client-detail">
                      <div class="full-height-scroll">
                        <strong>Últimos Accesos</strong>
                        <ul class="list-group clear-list">
                        <?php
                          // Consulta para buscar los ultimos accesos del Rector
                          $consulta = " SELECT * FROM bitacora WHERE usuario = $coordinadorUsuario AND tipo_accion = 1 order by fecha desc limit 5  ";
                          $resultado = $Link->query($consulta);
                          if($resultado->num_rows >= 1){
                            $auxPrimer = 0;
                            while ($row = $resultado->fetch_assoc()) {
                              $aux = $row['fecha'];
                              $aux = date("h:i:s A d/m/Y", strtotime($aux));
                        ?>
                              <li class="list-group-item <?php if($auxPrimer == 0){ ?>fist-item<?php } ?>">
                                <span class="pull-right"> <?= $aux; ?> </span>
                                Inició sesión
                              </li>
                        <?php
                              $auxPrimer++;
                            }
                          }
                        ?>
                        </ul>

                      <?php if( $institucionTel != '' || $coordinadorCorreo != '' || $institucionCodigoDane != '' || $institucionMunicipio != ''){ ?>
                        <p><strong>Datos:</strong></p>
                      <?php } ?>

                      <?php if( $institucionCodigoDane != ''){ ?>
                         <p> <strong>Codigo DANE:</strong> <?= $institucionCodigoDane; ?> </p>
                      <?php } ?>

                      <?php if( $institucionMunicipio != ''){ ?>
                        <p> <strong>Municipio:</strong> <?= $institucionMunicipio; ?> </p>
                      <?php } ?>
                                    
                      <?php if( $institucionTel != ''){ ?>
                        <p> <strong>Tel:</strong> <?= $institucionTel; ?> </p>
                      <?php } ?>

                      <?php if( $coordinadorCorreo != ''){ ?>
                        <p> <strong>Correo:</strong> <a href="mailto:<?= $coordinadorCorreo; ?>"><?php echo $coordinadorCorreo; ?></a> </p>
                      <?php } ?>

                      </div>
                    </div>
                  </div>
                </div>
              </div><!-- /.ibox-content -->
            <?php endif ?>

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
          <button type="button" class="btn btn-danger btn-outline btn-sm" data-dismiss="modal" onclick="revertirEstado();">Cancelar</button>
          <button type="button" class="btn btn-primary btn-sm" data-dismiss="modal" onclick="cambiarEstado();">Aceptar</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Ventana modal confirmar SEDE -->
  <div class="modal inmodal fade" id="ventanaConfirmarSede" tabindex="-1" role="dialog" style="display: none;" aria-hidden="true">
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
          <input type="hidden" id="codigoACambiarSede">
          <input type="hidden" id="estadoACambiarSede">
          <button type="button" class="btn btn-danger btn-outline btn-sm" data-dismiss="modal" onclick="revertirEstadoSede();">Cancelar</button>
          <button type="button" class="btn btn-primary btn-sm" data-dismiss="modal" onclick="cambiarEstadoSede();">Aceptar</button>
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
    <script src="<?php echo $baseUrl; ?>/theme/js/plugins/validate/jquery.validate.min.js"></script>
    
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
          buttons: [ {extend: 'excel', title: 'Sedes', className: 'btnExportarExcel', exportOptions: { columns: [ 0, 1, 2, 3, 4] } } ]
        });
        <?php if ($_SESSION['perfil'] == "0" || $permisos['instituciones'] == "1" || $permisos['instituciones'] == "2"): ?>
          var botonAcciones = '<div class="dropdown pull-right">'+
                              '<button class="btn btn-primary btn-sm btn-outline" type="button" id="dropdownMenu2" data-toggle="dropdown" aria-haspopup="true">'+
                                'Acciones <span class="caret"></span>'+
                              '</button>'+
                              '<ul class="dropdown-menu pull-right" aria-labelledby="dropdownMenu2">'+
                              <?php if($_SESSION['perfil'] == "0" || $permisos['instituciones'] == "2") { ?>
                                '<li><a id="crearSedeInstitucion" data-codigointitucion="<?php echo $institucionCodigo; ?>"> <i class="fa fa-plus"></i> Nueva </a></li>'+
                              <?php } ?>
                                '<li><a tabindex="0" aria-controls="box-table" href="#" onclick="$(\'.btnExportarExcel\').click();"><span class="fa fa-file-pdf-o"></span> Exportar </a></li>'+
                              <?php if ($_SESSION['perfil'] == "0" || $permisos['instituciones'] == "2"): ?>
                                '<li class="divider"></li>'+
                                '<li><a href="'+ $('#inputBaseUrl').val() +'/download/sedes/Plantilla_Sedes.csv"><i class="fa fa-download"></i> Descarga Plantilla .CSV </a></li>'+
                                '<li><a href="'+ $('#inputBaseUrl').val() +'/download/sedes/Plantilla_Sedes.xlsx"><i class="fa fa-download"></i> Descarga Plantilla .XLSX </a></li>'+
                              <?php endif ?>  
                              '</ul>'+
                            '</div>';
          $('.containerBtn').html(botonAcciones);  
        <?php endif ?> 
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

<form action="sede_crear.php" method="post" name="formCrearSede" id="formCrearSede">
  <input type="hidden" name="codigoInstitucion" id="codigoInstitucion">
</form>

<form action="sede_editar.php" method="post" name="formEditarSede" id="formEditarSede">
  <input type="hidden" name="codigoSede" id="codigoSede">
  <input type="hidden" name="nombreSede" id="nombreSede">
</form>

<form action="../dispositivos_biometricos/index.php" method="post" name="formDispositivosSede" id="formDispositivosSede">
  <input type="hidden" name="cod_inst" id="cod_inst" value="">
  <input type="hidden" name="cod_sede" id="cod_sede" value="">
</form>

<form action="../infraestructuras/index.php" method="post" name="formInfraestructura" id="formInfraestructura">
  <input type="hidden" name="cod_inst" id="cod_inst" value="">
</form>

<form action="../infraestructuras/ver_infraestructura.php" method="post" name="formInfraestructuraSede" id="formInfraestructuraSede">
  <input type="hidden" name="cod_inst" id="cod_inst" value="">
  <input type="hidden" name="cod_sede" id="cod_sede" value="">
</form>

<form action="../titulares_derecho/index.php" method="post" name="formTitulares" id="formTitulares">
  <input type="hidden" name="cod_inst" id="cod_inst" value="">
  <input type="hidden" name="cod_sede" id="cod_sede" value="">
</form>

</body>
</html>
