<?php 
$titulo = 'Dispositivos biométricos';
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
        <button class="btn btn-primary" onclick="window.location.href = 'nuevo_dispositivo.php';"><span class="fa fa-plus"></span>  Nuevo</button>
      </div>
    <?php endif ?>
  </div><!-- /.col -->
</div><!-- /.row -->

<div class="wrapper wrapper-content animated fadeInRight">
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
          
        	<table class="table table-striped table-hover selectableRows" id="tablaDispositivos">
        		<thead>
        			<tr>
        				<th>Referencia</th>
        				<th>Número de serial</th>
        				<th>Sede</th>
                <th>Usuario</th>
        				<th>Tipo</th>
                <th>Acciones</th>
        			</tr>
        		</thead>
        		<tbody>
        		<?php 

            if (isset($_POST['cod_inst']) && $_POST['cod_inst'] != "") {
              $inst = $_POST['cod_inst'];
              $consulta = "SELECT 
                                sede.nom_sede, usuarios.nombre AS nom_usu, dispositivos.*
                            FROM
                                dispositivos
                                    INNER JOIN
                                sedes".$_SESSION['periodoActual']." AS sede ON sede.cod_sede = dispositivos.cod_sede
                                    INNER JOIN
                                usuarios ON usuarios.id = dispositivos.id_usuario
                            WHERE
                                EXISTS( SELECT 
                                        *
                                    FROM
                                        instituciones
                                    WHERE
                                        instituciones.codigo_inst = '".$inst."'
                                            AND sede.cod_inst = instituciones.codigo_inst)";
            } else if (isset($_POST['cod_sede']) && $_POST['cod_sede'] != "") {
              $sede = $_POST['cod_sede'];
              $consulta = "SELECT sede.nom_sede, usuarios.nombre as nom_usu, dispositivos.* FROM dispositivos INNER JOIN sedes".$_SESSION['periodoActual']." as sede ON sede.cod_sede = dispositivos.cod_sede INNER JOIN usuarios ON usuarios.id = dispositivos.id_usuario WHERE sede.cod_sede = ".$sede;
            } else {
              $consulta = "SELECT sede.nom_sede, usuarios.nombre as nom_usu, dispositivos.* FROM dispositivos INNER JOIN sedes".$_SESSION['periodoActual']." as sede ON sede.cod_sede = dispositivos.cod_sede INNER JOIN usuarios ON usuarios.id = dispositivos.id_usuario";
            }

              
                $result1 = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));
                if($result1->num_rows > 0){

                  while($row1 = $result1->fetch_assoc()){ 
                  	?>

                  	<tr iddispositivo="<?php echo $row1['id']; ?>"> 
                  		<td><?php echo $row1['referencia']; ?></td>
                  		<td><?php echo $row1['num_serial']; ?></td>
                  		<td><?php echo $row1['nom_sede']; ?></td>
                      <td><?php echo $row1['nom_usu']; ?></td>
                      <td><?php echo $row1['tipo']; ?></td>
                      <td>
                        <div class="btn-group">
                          <div class="dropdown">
                            <button class="btn btn-primary btn-sm" type="button" id="accionesProducto" data-toggle="dropdown" aria-haspopup="true">
                              Acciones
                              <span class="caret"></span>
                            </button>
                            <ul class="dropdown-menu pull-right" aria-labelledby="accionesProducto">
                              <?php if ($_SESSION['perfil'] == 1 || $_SESSION['perfil'] == 0): ?>
                                <li><a onclick="editarDispositivo(<?php echo $row1['id']; ?>)"><span class="fa fa-pencil"></span>  Editar</a></li>
                                <li><a data-toggle="modal" data-target="#modalEliminarDispositivo"  data-iddispositivo="<?php echo $row1['id']; ?>"><span class="fa fa-trash"></span>  Eliminar</a></li>
                              <?php endif ?>
                               <li><a onclick="exportarDispositivo(<?php echo $row1['id']; ?>);"><span class="fa fa-file-excel-o"></span> Exportar</a></li>
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
<form method="Post" id="ver_dispositivo" action="ver_dispositivo.php" style="display: none;">
  <input type="hidden" name="idDispositivoVer" id="idDispositivoVer">
</form>
<form method="Post" id="editar_dispositivo" action="editar_dispositivo.php" style="display: none;">
  <input type="hidden" name="idDispositivoEditar" id="idDispositivoEditar">
</form>

<form method="Post" id="exportar_dispositivo" action="exportar_dispositivo.php" style="display: none;">
  <input type="hidden" name="idDispositivoexportar" id="idDispositivoexportar">
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

<!-- Section Scripts -->
<script src="<?php echo $baseUrl; ?>/modules/dispositivos_biometricos/js/dispositivos_biometricos.js"></script>

<script type="text/javascript">
  console.log('Aplicando Data Table');
  dataset1 = $('#tablaDispositivos').DataTable({
    order: [ 0, 'asc' ],
    pageLength: 25,
    responsive: true,
    dom : '<"html5buttons" B>lr<"containerBtn"><"inputFiltro"f>tip',
    buttons : [{extend:'excel', title:'Dispositivos', className:'btnExportarExcel', exportOptions: {columns : [0,1,2,3,4]}}],
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
  var btnAcciones = '<div class="dropdown pull-right" id=""><button class="btn btn-primary btn-sm btn-outline" type="button" id="accionesTabla" data-toggle="dropdown" aria-haspopup="true">Acciones<span class="caret"></span></button><ul class="dropdown-menu pull-right" aria-labelledby="accionesTabla"><li><a onclick="$(\'.btnExportarExcel\').click()"><span class="fa fa-file-excel-o"></span> Exportar </a></li></ul></div>';

  $('.containerBtn').html(btnAcciones);
</script>

<?php mysqli_close($Link); ?>

</body>
</html>