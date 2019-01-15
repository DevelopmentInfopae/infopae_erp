<?php
  include '../../header.php';
  set_time_limit (0);
  ini_set('memory_limit','6000M');

  $periodoActual = $_SESSION["periodoActual"];
  $titulo = "Sedes Educativas";
  $institucionNombre = "";
?>

<div class="row wrapper wrapper-content border-bottom white-bg page-heading">
  <div class="col-lg-8">
      <h2>Sedes Educativas</h2>
      <ol class="breadcrumb">
          <li>
              <a href="<?php echo $baseUrl; ?>">Home</a>
          </li>
          <li class="active">
              <strong><?php echo $titulo; ?></strong>
          </li>
      </ol>
  </div>
  <div class="col-lg-4">
      <div class="title-action">         
          <a href="#" class="btn btn-primary" onclick="crearSede();"><i class="fa fa-plus"></i> Nueva</a>
      </div>
  </div>
</div>
<!-- /.row wrapper de la cabecera de la seccion -->

<div class="wrapper wrapper-content  animated fadeInRight">
  <div class="row">
    <div class="col-sm-12">
      <div class="ibox">
        <div class="ibox-content">
        <?php
          $consulta = " select distinct semana from planilla_semanas ";
          $resultado = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));
          if($resultado->num_rows >= 1){
            while($row = $resultado->fetch_assoc()){
              $aux = $row['semana'];
              $consulta2 = " show tables LIKE 'focalizacion$aux' ";
              $resultado2 = $Link->query($consulta2) or die ('Unable to execute query. '. mysqli_error($Link));
              if($resultado2->num_rows >= 1){
               $semanas[] = $aux;
              }
            }
          }
        ?>
          <div class="row">
            <div class="col-sm-12">
              <form action="" id="formSedes" name="formSedes" method="post">
                <div class="row">
                  <div class="col-sm-2 form-group">
                    <label for="municipio">Municipio</label>  
                    <select class="form-control" name="municipio" id="municipio" required>
                      <option value="">Seleccione uno</option>
                      <?php
                        $consulta = " SELECT DISTINCT codigoDANE, ciudad FROM ubicacion WHERE 1=1 ";

                        $DepartamentoOperador = $_SESSION['codCiudad'];
                        if($DepartamentoOperador != ''){
                          $consulta = $consulta." AND CodigoDANE LIKE '$DepartamentoOperador%' ";
                        }
                        $consulta = $consulta." ORDER BY ciudad ASC ";
                        $resultado = $Link->query($consulta);
                        if($resultado->num_rows > 0){
                          while($row = $resultado->fetch_assoc()) {
                            $selected = (isset($_POST["municipio"]) && $_POST["municipio"] == $row["codigoDANE"] ) ? " selected " : "";
                            echo '<option value="' . $row["codigoDANE"] . '" ' . $selected . '> 
                                    ' . $row["ciudad"] .
                                  '</option>';
                          }// Termina el while
                        }//Termina el if que valida que si existan resultados
                      ?>
                    </select>
                  </div><!-- /.col -->

                  <div class="col-sm-3 form-group">
                    <label for="institucion">Institución</label>
                    <select class="form-control" name="institucion" id="institucion">
                      <option value="">Todas</option>
                      <?php
                        if(isset($_POST["municipio"]) && $_POST["municipio"] != "" ){
                          $municipio = mysqli_real_escape_string($Link, $_POST["municipio"]);
                          $consulta = " SELECT DISTINCT s.cod_inst, s.nom_inst FROM sedes$periodoActual s LEFT JOIN sedes_cobertura sc ON s.cod_sede = sc.cod_sede WHERE 1=1 ";
                          $consulta = $consulta." and s.cod_mun_sede = '$municipio' ";
                          $consulta = $consulta." order by s.nom_inst asc ";

                          $resultado = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));
                          if($resultado->num_rows >= 1){
                            while($row = $resultado->fetch_assoc()) { ?>
                              <option value="<?php echo $row['cod_inst']; ?>" <?php if(isset($_POST["institucion"]) && $_POST["institucion"] == $row['cod_inst'] ){ echo " selected "; }  ?> > <?php echo $row['nom_inst']; ?></option>
                      <?php }// Termina el while
                          }//Termina el if que valida que si existan resultados
                        }
                       ?>
                    </select>
                  </div><!-- /.col -->

                  <div class="col-sm-3">
                  </div>
                </div>
                <div class="row">
                  <div class="col-sm-3 form-group">
                    <button class="btn btn-primary" type="button" id="btnBuscar"> <i class="fa fa-search"></i> Buscar</button>
                  </div>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<?php if(isset($_POST["municipio"]) && $_POST['municipio'] != ''){ ?>
  <div class="wrapper wrapper-content  animated fadeInRight">
        <div class="row">
            <div class="col-sm-12">
                <div class="ibox">
                    <div class="ibox-content">
                      <h2>Sedes</h2>
                      <table class="table table-striped table-bordered table-hover selectableRows dataTablesSedes" >
                        <thead>
                          <tr>
                              <th>Nombre sede</th>
                              <th>Coordinador</th>
                              <th>Jornada</th>
                              <th>Tipo validación</th>
                              <th>Acciones</th>
                          </tr>
                        </thead>
                        <tbody>
                          <?php
                            $consulta = "SELECT 
                                          sed.id, 
                                          sed.nom_sede AS nombreSede, 
                                          sed.cod_sede AS codigoSede, 
                                          sed.estado AS estadoSede, 
                                          sed.nom_inst AS nombreInstitucion, 
                                          sed.tipo_validacion AS tipoValidacion,
                                          sed.estado AS estadoSede,
                                          usu.nombre AS nombreCoordinador, 
                                          jor.nombre AS nombreJornada
                                        FROM sedes$periodoActual sed
                                        LEFT JOIN usuarios usu ON usu.id = sed.id_coordinador 
                                        LEFT JOIN jornada jor ON jor.id = sed.jornada 
                                        WHERE 1=1 ";
                            if(isset($_POST['municipio']) && $_POST['municipio'] != ''){ $consulta .= " AND cod_mun_sede = '" . $_POST['municipio'] . "' "; }
                            if(isset($_POST['institucion']) && $_POST['institucion'] != ''){ $consulta .= " AND cod_inst = '" . $_POST['institucion'] . "' "; }
                            $consulta .= "ORDER BY nom_sede ASC ";

                            $resultado = $Link->query($consulta);
                            if($resultado->num_rows > 0){
                              while($row = $resultado->fetch_assoc()) { ?>
                                <tr codsede="<?php echo $row["codigoSede"]; ?>" nomsede="<?php echo $row["nombreSede"]; ?>" nominst="<?php echo $row["nombreInstitucion"];?>">
                                  <td><?php echo $row["nombreSede"]; ?></td>
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
                                            <a href="#" onclick="editarSede('<?php echo $row["codigoSede"]; ?>', '<?php echo $row["nombreSede"]; ?>');"><i class="fa fa-pencil fa-lg"></i> Editar</a>
                                          </li>
                                          <li>
                                            <a href="#">
                                              Estado: &nbsp;
                                              <input type="checkbox" id="inputEstadoSede<?php echo $row["id"]; ?>" data-toggle="toggle" data-on="Activo" data-off="Inactivo" data-size="mini" data-width="70" data-height="24" onchange="confirmarCambioEstado(<?php echo $row["id"]; ?>, this.checked);">
                                            </a>
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
                        <tfoot>
                          <tr>
                              <th>Nombre sede</th>
                              <th>Coordinador</th>
                              <th>Jornada</th>
                              <th>Tipo validación</th>
                              <th>Acciones</th>
                          </tr>
                        </tfoot>
                      </table>
                    </div>
                </div>
            </div>

        </div><!-- /.row -->
  </div>
<?php } ?>

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

<?php mysqli_close($Link); ?>

<!-- Page-Level Scripts -->
<script>
  $(document).ready(function(){
    // Configuración para la tabla de sedes.
    $('.dataTablesSedes').DataTable({
      buttons: [ {extend: 'excel', title: 'Sedes', className: 'btnExportarExcel', exportOptions: { columns: [ 0, 1, 2, 4 ] } } ],
      dom: 'lr<"containerBtn"><"inputFiltro"f>tip<"html5buttons"B>',
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
      pageLength: 10,
      responsive: true
    });

    // Configuración para la validación del formulario de búsqueda de sedes.
    jQuery.extend(jQuery.validator.messages, { required: "Este campo es obligatorio.", remote: "Por favor, rellena este campo.", email: "Por favor, escribe una dirección de correo válida", url: "Por favor, escribe una URL válida.", date: "Por favor, escribe una fecha válida.", dateISO: "Por favor, escribe una fecha (ISO) válida.", number: "Por favor, escribe un número entero válido.", digits: "Por favor, escribe sólo dígitos.", creditcard: "Por favor, escribe un número de tarjeta válido.", equalTo: "Por favor, escribe el mismo valor de nuevo.", accept: "Por favor, escribe un valor con una extensión aceptada.", maxlength: jQuery.validator.format("Por favor, no escribas más de {0} caracteres."), minlength: jQuery.validator.format("Por favor, no escribas menos de {0} caracteres."), rangelength: jQuery.validator.format("Por favor, escribe un valor entre {0} y {1} caracteres."), range: jQuery.validator.format("Por favor, escribe un valor entre {0} y {1}."), max: jQuery.validator.format("Por favor, escribe un valor menor o igual a {0}."), min: jQuery.validator.format("Por favor, escribe un valor mayor o igual a {0}.") });


    // Botón de acciones para la tabla.
    var botonAcciones = '<div class="dropdown pull-right">'+
                      '<button class="btn btn-primary btn-sm btn-outline" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true">'+
                        'Acciones <span class="caret"></span>'+
                      '</button>'+
                      '<ul class="dropdown-menu pull-right" aria-labelledby="dropdownMenu1">'+
                        '<li><a tabindex="0" aria-controls="box-table" href="#" onclick="$(\'.btnExportarExcel\').click();"><i class="fa fa-file-pdf-o"></i> Exportar </a></li>'+
                        '<li>'+
                          '<a class="fileinput fileinput-new" data-provides="fileinput">'+
                            '<span class="btn-file">'+
                              '<i class="fa fa-upload"></i> '+
                              '<span class="fileinput-new">Importar</span>'+
                              '<span class="fileinput-exists">Cambiar</span>'+
                              '<input type="file" name="archivo" id="archivo" onchange="if(!this.value.length) return false; cargarArchivo();" accept=".csv, .xlsx">'+
                            '</span> '+
                            '<span class="fileinput-filename center-block"></span>'+
                            '<span href="#" class="close fileinput-exists" data-dismiss="fileinput" style="float: none">&times;</span>'+
                          '</a>'+
                        '</li>'+
                        '<li class="divider"></li>'+
                        '<li><a href="'+ $('#inputBaseUrl').val() +'/download/usuarios/Plantilla_Sedes.csv" dowload> <i class="fa fa-download"></i> Descarga plantilla .CSV</a></li>'+
                        '<li><a href="'+ $('#inputBaseUrl').val() +'/download/usuarios/Plantilla_Sedes.xlsx" dowload> <i class="fa fa-download"></i> Descarga plantilla .XLSX </a></li>'+
                        '<ul>'+
                      '</ul>'+
                    '</div>';
  $('.containerBtn').html(botonAcciones);

  // Evitar el burbujeo del DOM en el control dropbox
  $(document).on('click', '.dropdown li:nth-child(2)', function(e) { e.stopPropagation(); });
  });
</script>

<form action="sede.php" method="post" name="formVerSede" id="formVerSede">
  <input type="hidden" name="codSede" id="codSede">
  <input type="hidden" name="nomSede" id="nomSede">
  <input type="hidden" name="nomInst" id="nomInst" value="<?php echo $institucionNombre; ?>">
</form>

<form action="sede_editar.php" method="post" name="formEditarSede" id="formEditarSede">
  <input type="hidden" name="codigoSede" id="codigoSede">
  <input type="hidden" name="nombreSede" id="nombreSede">
  <!-- <input type="hidden" name="nomInst" id="nomInst" value="<?php //echo $institucionNombre; ?>"> -->
</form>

</body>
</html>
