<?php 
$titulo = 'Aportes calóricos y nutricionales';
require_once '../../header.php'; 
$periodoActual = $_SESSION['periodoActual'];

if ($permisos['menus'] == "0") {
  ?><script type="text/javascript">
      window.open('<?= $baseUrl ?>', '_self');
  </script>
<?php exit(); }

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
    <?php if ($_SESSION['perfil'] == "0" || $permisos['menus'] == "2"): ?>
      <div class="title-action">
        <button class="btn btn-primary" onclick="window.location.href = 'nuevo_menuvalref.php';"><span class="fa fa-plus"></span>  Nuevo</button>
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
          
        	<table class="table table-striped table-hover selectableRows" id="tablaValRef">
        		<thead>
        			<tr>
                <th>Grupo Etario</th>
        				<th>Tipo Complemento</th>
                <th>Acciones</th>
        			</tr>
        		</thead>
        		<tbody>
        		<?php 
            $consValRef = "SELECT G.DESCRIPCION as etaNombre, MV.* FROM menu_valref_nutrientes AS MV
                            INNER JOIN grupo_etario AS G ON G.ID = MV.Cod_Grupo_Etario ORDER BY MV.Cod_tipo_complemento ASC ";
            $resValRef = $Link->query($consValRef);
            if ($resValRef->num_rows > 0) {
              while ($valRef = $resValRef->fetch_assoc()) { ?>
                <tr idvalref="<?php echo $valRef['id']; ?>">
                  <td><?php echo $valRef['Cod_tipo_complemento']; ?></td>
                  <td><?php echo $valRef['etaNombre']; ?></td>
                  <td>
                    <div class="btn-group">
                      <div class="dropdown">
                        <button class="btn btn-primary btn-sm" type="button" id="accionesProducto" data-toggle="dropdown" aria-haspopup="true">
                          Acciones
                          <span class="caret"></span>
                        </button>
                        <ul class="dropdown-menu pull-right" aria-labelledby="accionesProducto">
                          <?php if ($_SESSION['perfil'] == "0" || $permisos['menus'] == "2") { ?>
                            <li><a onclick="editarValRef(<?php echo $valRef['id']; ?>)"><span class="fas fa-pencil-alt"></span>  Editar</a></li>
                            <li><a data-toggle="modal" data-target="#modalEliminarAportesCalyNut"  data-idvalref="<?php echo $valRef['id']; ?>"><span class="fa fa-trash"></span>  Eliminar</a></li>
                          <?php } ?>
                           <li><a><span class="fa fa-file-excel-o"></span> Exportar</a></li>
                        </ul>
                      </div>
                    </div>
                  </td>
                </tr>
              <?php }
            }
             ?>
        		</tbody>
        	</table>
        </div><!-- /.ibox-content -->
      </div><!-- /.ibox float-e-margins -->
    </div><!-- /.col-lg-12 -->
  </div><!-- /.row -->
</div><!-- /.wrapper wrapper-content animated fadeInRight -->
<form method="Post" id="ver_menuvalref" action="ver_menuvalref.php" style="display: none;">
  <input type="hidden" name="idvalref" id="idvalrefver">
</form>
<form method="Post" id="editar_menuvalref" action="editar_menuvalref.php" style="display: none;">
  <input type="hidden" name="idvalref" id="idvalrefeditar">
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

<!-- Section Scripts -->
<script src="<?php echo $baseUrl; ?>/modules/menu_valref/js/menu_valref.js"></script>

<script type="text/javascript">
  console.log('Aplicando Data Table');
  dataset1 = $('#tablaValRef').DataTable({
    /*order: [ 0, 'asc' ],*/
    pageLength: 25,
    responsive: true,
    dom : '<"html5buttons" B>lr<"containerBtn"><"inputFiltro"f>tip',
    buttons : [{extend:'excel', title:'Menus', className:'btnExportarExcel', exportOptions: {columns : [0,1]}}],
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