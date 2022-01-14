<?php 
  include '../../header.php';

  if ($permisos['configuracion'] == "0" || $permisos['configuracion'] == "1") {
    ?><script type="text/javascript">
      window.open('<?= $baseUrl ?>', '_self');
    </script>
  <?php exit(); }

  $titulo = 'Usuarios';
?>

<div class="row wrapper wrapper-content border-bottom white-bg page-heading">
  <div class="col-lg-8">
    <h2>Usuarios</h2>
    <ol class="breadcrumb">
      <li>
        <a href="<?php echo $baseUrl; ?>">Home</a>
      </li>
      <li class="active">
        <strong>Usuarios</strong>
      </li>
    </ol>
  </div><!-- /.col -->
  <div class="col-lg-4">
    <div class="title-action">
      <a href="#" class="btn btn-primary" onclick="crearUsuario();"><i class="fa fa-plus"></i> Nuevo </a>
    </div><!-- /.title-action -->
  </div><!-- /.col -->
</div><!-- /.row -->

<!-- Seccion de filtros -->
<div class="wrapper wrapper-content animated fadeInRight">
  <div class="row">
    <div class="col-lg-12">
      <div class="ibox float-e-margins">
        <div class="ibox-content contentBackground">       
          <table id="box-table" class="table table-striped table-hover selectableRows">
            <thead>
              <tr>
                <th>N° Documento</th>
                <th>Nombres y apellidos</th>
                <th>Correo electrónico</th>
                <th>Perfil</th>
                <th>Tipo de usuario</th>
                <th>Municipio</th>
                <th class="text-center">Acciones</th>
              </tr>
            </thead>
            <tbody>
              <?php 
                $consulta = "SELECT DISTINCT 
                              usu.id AS idUsuario, 
                              usu.nombre, num_doc, 
                              cod_mun, 
                              Tipo_Usuario, 
                              id_perfil, 
                              per.nombre AS nombrePerfil, 
                              email, 
                              ciudad,
                              usu.estado,
                              IFNULL(
                                (SELECT DISTINCT pro.Nitcc FROM proveedores pro WHERE usu.num_doc = pro.Nitcc ), 
                                IFNULL(
                                  (SELECT DISTINCT ubod.USUARIO FROM usuarios_bodegas ubod WHERE usu.id = ubod.USUARIO ),
                                  IFNULL(
                                    (SELECT DISTINCT emp.Nitcc FROM empleados emp WHERE usu.num_doc = emp.Nitcc ),
                                          (SELECT DISTINCT dis.id_usuario FROM dispositivos dis WHERE usu.num_doc = dis.id_usuario )
                                  )
                                )
                              ) AS 'usuarioAsociado' 
                            FROM usuarios usu 
                            LEFT JOIN ubicacion ubi ON usu.cod_mun = ubi.CodigoDANE 
                            LEFT JOIN perfiles per ON usu.id_perfil = per.id";
                $resultado = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));
                if($resultado){
                  while($row = $resultado->fetch_assoc()){
                    $estado = ($row["estado"] == 1) ? "Activo" : "Inactivo";
                    $eliminar = (is_null($row["usuarioAsociado"])) ? '<li><a onclick="confirmarEliminarUsuario(' . $row["idUsuario"] . ')"><i class="fa fa-trash fa-lg"></i> Eliminar</a></li>' : '';
                    echo '<tr data-idusuario="' . $row["idUsuario"] . '">
                            <td align="left">' . $row["num_doc"] . '</td>
                            <td align="left">' . $row["nombre"] . '</td>
                            <td align="left">' . $row["email"] . '</td>
                            <td align="left">' . $row["nombrePerfil"] . '</td>
                            <td align="left">' . $row["Tipo_Usuario"] . '</td>
                            <td align="left">' . $row["ciudad"] . '</td>
                            <td align="center">
                              <div class="btn-group">
                                <div class="dropdown">
                                  <button class="btn btn-primary btn-sm" id="dLabel" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    Acciones <span class="caret"></span>
                                  </button>
                                  <ul class="dropdown-menu pull-right" aria-labelledby="dLabel">
                                    <li><a href="#" class="btnEditarUsuario" data-idusuario="' . $row["idUsuario"] . '"><i class="fas fa-pencil-alt fa-lg"></i> Editar</a></li>'.
                                    $eliminar .
                                    '<li><a href="#" onclick="restaurarContrasenaLista(' . $row["idUsuario"] . ');"><i class="fa fa-retweet fa-lg"></i> Restaurar password</a></li>
                                    <li><a style="cursor: default"><i class="fa fa-check fa-lg"></i> Estado: <strong>'.$estado.'</strong></a></li>
                                  </ul>
                                </div>
                              </div>
                            </td>
                          </tr>';
                  }
                }
              ?>
            </tbody>
            <tfoot>
              <tr>
                <th>N° Documento</th>
                <th>Nombre</th>
                <th>Correo electrónico</th>
                <th>Perfil</th>
                <th>Tipo de usuario</th>
                <th>Municipio</th>
                <th class="text-center">Acciones</th>
              </tr>
            </tfoot>
          </table>
        </div>
      </div>
    </div>
  </div>

</div>

<div class="modal inmodal fade" id="ventanaConfirmar" tabindex="-1" role="dialog" style="display: none;" aria-hidden="true">
  <div class="modal-dialog modal-sm">
    <div class="modal-content">
      <div class="modal-header text-info" style="padding: 15px;">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only">Close</span></button>
        <h3><i class="fa fa-question-circle fa-lg" aria-hidden="true"></i> Confirmación InfoPAE </h3>
      </div>
      <div class="modal-body">
          <p class="text-center"></p>
      </div>
      <div class="modal-footer">
        <input type="hidden" id="idAEliminar">
        <button type="button" class="btn btn-white btn-sm" data-dismiss="modal">No</button>
        <button type="button" class="btn btn-primary btn-sm" data-dismiss="modal" onclick="eliminarUsuario()">Si</button>
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
<script src="<?php echo $baseUrl; ?>/theme/js/plugins/iCheck/icheck.min.js"></script>
<script src="<?php echo $baseUrl; ?>/theme/js/plugins/toastr/toastr.min.js"></script>

<!-- Section Scripts -->
<script src="<?php echo $baseUrl; ?>/modules/usuarios/js/usuarios.js"></script>
<script>
  console.log('Aplicando Data Table');
  $('#box-table').DataTable({
    buttons: [ {extend: 'excel', title: 'Usuarios', className: 'btnExportarExcel', exportOptions: { columns: [ 0, 1, 2, 3, 4, 5 ] } } ],
    dom: 'lr<"containerBtn"><"inputFiltro"f>tip<"clear"><"html5buttons"B>',
    order: [ 1, 'asc' ],
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
    },
    pageLength: 25,
    responsive: true,
    search:{
      "search": "<?php if (isset($_GET['filtro'])) echo $_GET['filtro']; ?>"
    }
  });

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
                        '<li><a href="'+ $('#inputBaseUrl').val() +'/download/usuarios/Plantilla_Usuarios.csv" dowload> <i class="fa fa-download"></i> Descarga plantilla .CSV</a></li>'+
                        '<li><a href="'+ $('#inputBaseUrl').val() +'/download/usuarios/Plantilla_Usuarios.xlsx" dowload> <i class="fa fa-download"></i> Descarga plantilla .XLSX</a></li>'+
                        '<ul>'+
                      '</ul>'+
                    '</div>';
  $('.containerBtn').html(botonAcciones);
</script>


<form action="usuarios_ver.php" method="post" name="formVerUsuario" id="formVerUsuario">
  <input type="hidden" name="codigoUsuario" id="codigoUsuario">
</form>

<form action="usuarios_editar.php" method="post" name="formEditarUsuario" id="formEditarUsuario">
  <input type="hidden" name="codigoUsuario" id="codigoUsuario">
</form>

<?php mysqli_close($Link); ?>

</body>
</html>