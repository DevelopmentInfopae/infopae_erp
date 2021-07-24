<?php
$titulo = 'Menús';
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
        <a href="<?php echo $baseUrl; ?>">Home</a>
      </li>
      <li class="active">
        <strong><?php echo $titulo; ?></strong>
      </li>
    </ol>
  </div><!-- /.col -->
  <div class="col-lg-4">
    <div class="title-action">
			<?php if ($_SESSION['perfil'] == "0" || $permisos['menus'] == "2") { ?>
				<button class="btn btn-primary" onclick="window.location.href = 'nuevo_menu.php';"><span class="fa fa-plus"></span>  Nuevo</button>
			<?php } ?>
    </div>
  </div><!-- /.col -->
</div><!-- /.row -->

<div class="wrapper wrapper-content animated fadeInRight">
  <div class="row">
    <div class="col-lg-12">
      <div class="ibox float-e-margins">
        <div class="ibox-content contentBackground">
          <form class="form row" method="POST">
            <div class="form-group col-sm-3">
              <label>N° de Ciclo</label>
              <select class="form-control" name="ciclo_menus">
                <option value="">Seleccione...</option>
                <?php 
                // $consMenus = "SELECT * FROM productos".$_SESSION['periodoActual']." WHERE Codigo LIKE '01%' AND Nivel = '3' ORDER BY Codigo ASC";
                $consMenus = "SELECT * FROM productos".$_SESSION['periodoActual']." WHERE Codigo LIKE '01%' AND Nivel = 3 ORDER BY Orden_Ciclo ASC;";
                $resMenus = $Link->query($consMenus);
                if ($resMenus->num_rows > 0) {
                  $productos_ciclo = [];
                  $ciclo_guardado = [];
                  $cntCiclo = 1;
                  while ($menus = $resMenus->fetch_assoc()) {
                    if ($menus['Orden_Ciclo'] > ($cntCiclo * 5)) {
                      $cntCiclo++;
                    }
                    $productos_ciclo[$cntCiclo][] = $menus['Id'];
                  }
                }

                for ($i=1; $i <= $cntCiclo ; $i++) {  ?>
                  <option value="<?= $i ?>" <?= (isset($_POST['ciclo_menus']) && $_POST['ciclo_menus'] == $i) ? "selected='selected'" : ""; ?> >Ciclo <?= $i ?></option>
                <?php }
                 ?>
              </select>
            </div>
            <div class="form-group col-sm-3">
              <label>Tipo de complemento</label>
              <select class="form-control" name="complemento_menus">
                <option value="">Seleccione...</option>
                <?php 
                $consComplementos = "SELECT * FROM tipo_complemento";
                $resComplementos = $Link->query($consComplementos);
                if ($resComplementos->num_rows > 0) {
                  while ($complementos = $resComplementos->fetch_assoc()) { ?>
                    <option value="<?= $complementos['CODIGO'] ?>" <?= (isset($_POST['complemento_menus']) && $_POST['complemento_menus'] == $complementos['CODIGO']) ? "selected='selected'" : ""; ?>><?= $complementos['CODIGO'] ?></option>
                  <?php }
                }

                 ?>
              </select>
            </div>
            <div class="form-group col-sm-3">
              <label>Grupo Etario</label>
              <select class="form-control" name="etario_menus">
                <option value="">Seleccione...</option>
                <?php 
                $consEtarios = "SELECT * FROM grupo_etario";
                $resEtarios = $Link->query($consEtarios);
                if ($resEtarios->num_rows > 0) {
                  while ($etarios = $resEtarios->fetch_assoc()) { ?>
                    <option value="<?= $etarios['ID'] ?>"  <?= (isset($_POST['etario_menus']) && $_POST['etario_menus'] == $etarios['ID']) ? "selected='selected'" : ""; ?> ><?= $etarios['DESCRIPCION'] ?></option>
                  <?php }
                }
                 ?>
              </select>
            </div>
            <div class="form-group col-sm-12" style="padding-top: 2%;">
              <!-- <input type="submit" name="filtro_menu" value="Buscar" class="btn btn-success"> -->
              <input type="hidden" name="filtro_menus">
              <button type="submit" class="btn btn-primary"><span class="fa fa-search"></span> Buscar</button>
              <?php if (isset($_POST['filtro_menus'])): ?>
                <a href="index.php" class="btn btn-default"><span class="fa fa-times"></span> Limpiar búsqueda</a>
              <?php endif ?>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>


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

        	<table class="table table-striped table-hover selectableRows" id="tablaMenus">
        		<thead>
        			<tr>
        				<th>Código</th>
        				<th>Descripción</th>
        				<th>Tipo Producto</th>
                <th>Tipo Complemento</th>
        				<th>Grupo Etario</th>
        				<th>Variación</th>
                <th>Estado</th>
                <th>Acciones</th>
        			</tr>
        		</thead>
        		<tbody>
        		<?php
              $gruposEtarios = array();
              $consultaGruposEtarios = "SELECT * FROM grupo_etario;";
              $resultadoGruposEtarios = $Link->query($consultaGruposEtarios);
              if ($resultadoGruposEtarios->num_rows > 0) {
                while ($row = $resultadoGruposEtarios->fetch_assoc()) {
                  $gruposEtarios[$row['ID']] = $row['DESCRIPCION'];
                }
              }

              $variacionesMenu = array();
              $consultaVariacionesMenu = "SELECT * FROM variacion_menu;";
              $resultadoVariacionesMenu = $Link->query($consultaVariacionesMenu);
              if ($resultadoVariacionesMenu->num_rows > 0) {
                while ($row = $resultadoVariacionesMenu->fetch_assoc()) {
                  $variacionesMenu[$row['id']] = $row['descripcion'];
                }
              }

              if (isset($_POST['filtro_menus'])) {

                $condicion = "";

                if (isset($_POST['ciclo_menus']) && $_POST['ciclo_menus'] != "") {
                  $condicion = " AND ID IN (";
                  foreach ($productos_ciclo[$_POST['ciclo_menus']] as $row => $idProducto) {
                      $condicion.= $idProducto.", ";
                  }
                  $condicion = trim($condicion, ", ");
                  $condicion.=") ";
                }

                if (isset($_POST['complemento_menus']) && $_POST['complemento_menus'] != "") {
                  $condicion .= "AND Cod_Tipo_complemento = '".$_POST['complemento_menus']."' ";
                }

                if (isset($_POST['etario_menus']) && $_POST['etario_menus'] != "") {
                  $condicion .= "AND Cod_Grupo_Etario = '".$_POST['etario_menus']."' ";
                }

                $consulta = "SELECT * FROM productos".date('y')." WHERE Codigo like '01%' AND nivel = '3' ".$condicion;
                // echo $consulta;
              } else {
                $consulta = "SELECT * FROM productos".date('y')." WHERE Codigo like '01%' AND nivel = '3'";
              }
                $result1 = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));
                if($result1->num_rows > 0){

                  while($row1 = $result1->fetch_assoc()){
                  	?>

                  	<tr idproducto="<?php echo $row1['Id']; ?>"  codigo="<?php echo $row1['Codigo']; ?>" descripcion="<?php echo $row1['Descripcion']; ?>">
                  		<td><?php echo $row1['Codigo']; ?></td>
                  		<td><?php echo $row1['Descripcion']; ?></td>
                  		<td><?php echo $row1['TipodeProducto']; ?></td>
                      <td><?php echo $row1['Cod_Tipo_complemento']; ?></td>
                  		<td><?php echo $gruposEtarios[$row1['Cod_Grupo_Etario']]; ?></td>
                  		<td><?php echo $variacionesMenu[$row1['cod_variacion_menu']]; ?></td>
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
															<li><a><span class="fa fa-file-excel-o"></span> Exportar</a></li>
															<?php if ($_SESSION['perfil'] == "0" || $permisos['menus'] == "2") { ?>
																<li><a onclick="editarProducto(<?php echo $row1['Id']; ?>)"><span class="fas fa-pencil-alt"></span>  Editar</a></li>
																<?php if ($row1['Inactivo'] == 0): ?>
																	<li><a data-toggle="modal" data-target="#modalEliminar"  data-codigo="<?php echo $row1['Codigo']; ?>" data-tipocomplemento="<?php echo $row1['Cod_Tipo_complemento']; ?>" data-ordenciclo="<?php echo $row1['Orden_Ciclo']; ?>"><span class="fa fa-trash"></span>  Eliminar</a></li>
																<?php endif ?>
															<?php } ?>
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
<form method="Post" id="menus_analisis" action="menus_analisis.php" target="_blank" style="display: none;">
  <input type="hidden" name="descripcion" id="descripcion">
  <input type="hidden" name="codigo" id="codigo">
  <input type="hidden" name="idProducto" id="idProducto">
</form>
<form method="Post" id="editar_producto" action="editar_producto.php" target="_blank" style="display: none;">
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
<script src="<?php echo $baseUrl; ?>/modules/menus2/js/menus.js"></script>

<script type="text/javascript">
  console.log('Aplicando Data Table');
  dataset1 = $('#tablaMenus').DataTable({
    /*order: [ 0, 'asc' ],*/
    pageLength: 25,
    responsive: true,
    dom : '<"html5buttons" B>lr<"containerBtn"><"inputFiltro"f>tip',
    buttons : [{extend:'excel', title:'Menus', className:'btnExportarExcel', exportOptions: {columns : [0,1,2,3,4,5,6]}}],
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
