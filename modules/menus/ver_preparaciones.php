<?php 
$titulo = 'Preparaciones';
require_once '../../header.php'; 
$periodoActual = $_SESSION['periodoActual'];
?>

<div class="row wrapper wrapper-content border-bottom white-bg page-heading">
  <div class="col-lg-8">
    <h2><?php echo $titulo; ?></h2>
    <ol class="breadcrumb">
      <li>
        <a href="<?php echo $baseUrl; ?>">Home</a>
      </li>
      <li class="active">
        <strong><?php echo $titulo; ?></strong>
      </li>
    </ol>
  </div><!-- /.col -->
  <div class="col-lg-4">
    <?php if ($_SESSION['perfil'] == 1 || $_SESSION['perfil'] == 0) { ?>
      <div class="title-action">
        <button class="btn btn-primary" onclick="window.location.href = 'nueva_preparacion.php';"><span class="fa fa-plus"></span>  Nuevo</button>
      </div>
    <?php } ?>
  </div><!-- /.col -->
</div><!-- /.row -->

<div class="wrapper wrapper-content animated fadeInRight">
  <div class="row">
    <div class="col-lg-12">
      <div class="ibox float-e-margins">
        <div class="ibox-content contentBackground">

              <!-- <div class="dropdown pull-right" id=""><button class="btn btn-primary btn-sm btn-outline" type="button" id="accionesTabla" data-toggle="dropdown" aria-haspopup="true">Acciones<span class="caret"></span></button><ul class="dropdown-menu pull-right" aria-labelledby="accionesTabla"><li><a onclick="$('.btnExportarExcel').click()"><span class="fa fa-file-excel-o"></span> Exportar </a></li></ul></div> -->
          
        	<table class="table table-striped selectableRows" id="box-table">
        		<thead>
        			<tr>
        				<th>Código</th>
        				<th>Descripción</th>
        				<th>Tipo Producto</th>
        				<th>Unidad de Medida</th>
        				<th>Factor conversión</th>
                <th>Estado</th>
                <th>Acciones</th>
        			</tr>
        		</thead>
        		<tbody>
        		<?php 
              $consulta = "SELECT * FROM productos".date('y')." WHERE Codigo like '02%' AND nivel = '3'";
                $result1 = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));
                if($result1->num_rows > 0){

                  while($row1 = $result1->fetch_assoc()){ 
                  	?>

                  	<tr idProducto="<?php echo $row1['Id']; ?>">
                  		<td><?php echo $row1['Codigo']; ?></td>
                  		<td><?php echo $row1['Descripcion']; ?></td>
                  		<td><?php echo $row1['TipodeProducto']; ?></td>
                  		<td><?php echo $row1['NombreUnidad1']; ?></td>
                  		<td><?php echo $row1['CantidadUnd1']; ?></td>
                      <?php if ($row1['Inactivo'] == 0): ?>
                        <td>Activo</td>
                      <?php else: ?>
                        <td>Inactivo</td>
                      <?php endif ?>
                      <td>
                        <div class="btn-group">
                          <div class="dropdown">
                            <button class="btn btn-primary btn-sm" type="button" id="accionesProducto" data-toggle="dropdown" aria-haspopup="true">
                              Acciones
                              <span class="caret"></span>
                            </button>
                            <ul class="dropdown-menu pull-right" aria-labelledby="accionesProducto">
                              <?php if ($_SESSION['perfil'] == 1 || $_SESSION['perfil'] == 0) { ?>
                                <li><a onclick="editarProducto(<?php echo $row1['Id']; ?>)"><span class="fa fa-pencil"></span>  Editar</a></li>
                                <?php if ($row1['Inactivo'] == 0): ?>
                                <li><a data-toggle="modal" data-target="#modalEliminar"  data-codigo="<?php echo $row1['Codigo']; ?>" data-tipocomplemento="<?php echo $row1['Cod_Tipo_complemento']; ?>" data-ordenciclo="<?php echo $row1['Orden_Ciclo']; ?>"><span class="fa fa-trash"></span>  Eliminar</a></li>
                                <?php endif ?>
                              <?php } ?>                               
                              <li><a><span class="fa fa-file-excel-o"></span> Exportar</a></li>
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
<form method="Post" id="ver_producto" action="ver_producto.php" style="display: none;">
  <input type="hidden" name="idProducto" id="idProducto">
</form>
<form method="Post" id="editar_producto" action="editar_producto.php" style="display: none;">
  <input type="hidden" name="idProducto" id="idProductoEditar">
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
<script src="<?php echo $baseUrl; ?>/modules/menus/js/menus.js"></script>

<script type="text/javascript">
  console.log('Aplicando Data Table');
  dataset1 = $('#box-table').DataTable({
    /*order: [ 0, 'asc' ],*/
    pageLength: 25,
    responsive: true,
    dom : '<"html5buttons" B>lr<"containerBtn"><"inputFiltro"f>tip',
    buttons : [{extend:'excel', title:'Preparaciones', className:'btnExportarExcel', exportOptions: {columns : [0,1,2,3,4,5]}}],
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
  /*
  dataset1.buttons().container().appendTo($('#btnExcelDataTable'));
  $('.btnExportar').removeAttr('class').css('color', '#333');*/
</script>

<?php mysqli_close($Link); ?>

</body>
</html>