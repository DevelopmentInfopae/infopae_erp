<?php 
$titulo = 'Infraestucturas';
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
    <div class="title-action">
      <button class="btn btn-primary" onclick="window.location.href = 'nueva_infraestructura.php';"><span class="fa fa-plus"></span>  Nuevo</button>
    </div>
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
          
        	<table class="table table-striped table-hover selectableRows" id="tablaInfraestructuras">
        		<thead>
        			<tr>
                <th>Municipio</th>
        				<th>Institución</th>
        				<th>Sede</th>
                <th>Rural / Urbana</th>
                <th>Complemento JM/JT</th>
                <th>Almuerzo</th>
                <th>¿Cuenta con comedor?</th>
                <th>Concepto sanitario</th>
                <th>Fecha de expedición</th>
                <th>Acciones</th>
        			</tr>
        		</thead>
        		<tbody>
        		<?php 
              $instituciones = array();
              $consultaInstituciones = "SELECT DISTINCT codigo_inst, nom_inst, cod_mun FROM instituciones WHERE EXISTS (SELECT cod_inst FROM sedes".$_SESSION['periodoActual']." WHERE cod_inst = codigo_inst) ORDER BY nom_inst ASC;";
              $resultadoInstituciones = $Link->query($consultaInstituciones);
              if ($resultadoInstituciones->num_rows > 0) {
                while ($row = $resultadoInstituciones->fetch_assoc()) {
                  $instituciones[$row['codigo_inst']] = $row['nom_inst'];
                }
              }

              $sedes = array();
              $consultarSedes = "SELECT DISTINCT cod_sede, nom_sede FROM sedes".$_SESSION['periodoActual']." ORDER BY nom_sede ASC;";
              $resultadoSedes = $Link->query($consultarSedes);
              if ($resultadoSedes->num_rows > 0) {
                while ($row = $resultadoSedes->fetch_assoc()) {
                  $sedes[$row['cod_sede']] = $row['nom_sede'];
                }
              }

              $modalidades = array();
              $consultarModalidad = "SELECT * FROM modalidad_suministro;";
              $resultadoModalidad = $Link->query($consultarModalidad);
              if ($resultadoModalidad->num_rows > 0) {
                while ($row = $resultadoModalidad->fetch_assoc()) {
                  $modalidades[$row['id']] = $row['Descripcion'];
                }
              }

              $sectores = array('1' => 'Rural', '2' => 'Urbano', '0' => 'No especificado.');
              $conceptos_sanitario = array('1' => 'Favorable', '0' => 'Desfavorable');
              $estados = array('1' => 'Si', '0' => 'No', '2' => 'No aplica');

              $consulta = "SELECT * FROM Infraestructura ";
                $result1 = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));
                if($result1->num_rows > 0){

                  while($row1 = $result1->fetch_assoc()){ 

                    $consultarMunicipioInst = "SELECT ubicacion.Ciudad FROM ubicacion, instituciones WHERE instituciones.cod_mun = ubicacion.CodigoDANE AND instituciones.codigo_inst = ".$row1['cod_inst'];
                    $resultadoMunicipioInst = $Link->query($consultarMunicipioInst);
                    if ($resultadoMunicipioInst->num_rows > 0) {
                      if ($municipio = $resultadoMunicipioInst->fetch_assoc()) {
                        $nomMunicipio = $municipio['Ciudad'];
                      }
                    }

                    $consultarSectorSede = "SELECT sector FROM sedes".$_SESSION['periodoActual']." WHERE cod_sede = ".$row1['cod_sede'];
                    $resultadoSectorSede = $Link->query($consultarSectorSede);
                    if ($resultadoSectorSede->num_rows > 0) {
                      if ($sectorSede = $resultadoSectorSede->fetch_assoc()) {
                        $nomSector = $sectores[$sectorSede['sector']];
                      }
                    }
                  	?>

                  	<tr idinfraestructura="<?php echo $row1['id']; ?>"> 
                      <td><?php echo $nomMunicipio; ?></td>
                  		<td><?php echo $instituciones[$row1['cod_inst']]; ?></td>
                  		<td><?php echo $sedes[$row1['cod_sede']]; ?></td>
                      <td><?php echo $nomSector; ?></td>
                      <td><?php echo $modalidades[$row1['id_Complem_JMJT']]; ?></td>
                      <td><?php echo $modalidades[$row1['id_Almuerzo']]; ?></td>
                      <td><?php echo $estados[$row1['Comedor_Escolar']]; ?></td>
                      <td><?php echo $conceptos_sanitario[$row1['Concepto_Sanitario']]; ?></td>
                      <td><?php echo $row1['Fecha_Expe']; ?></td>
                      <td>
                        <div class="btn-group">
                          <div class="dropdown">
                            <button class="btn btn-primary btn-sm" type="button" id="accionesProducto" data-toggle="dropdown" aria-haspopup="true">
                              Acciones
                              <span class="caret"></span>
                            </button>
                            <ul class="dropdown-menu pull-right" aria-labelledby="accionesProducto">
                              <!-- <li><a onclick="editarProducto(<?php echo $row1['id']; ?>)"><span class="fa fa-pencil"></span>  Editar</a></li>
                               <li>
                                <a><span class="fa fa-file-excel-o"></span> Exportar</a>
                               </li>
                                <li>
                                  <a data-toggle="modal" data-target="#modalEliminar"  data-idinfraestructura="<?php echo $row1['id']; ?>"><span class="fa fa-trash"></span>  Eliminar</a>
                                </li> -->
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
<form method="Post" id="menus_analisis" action="menus_analisis.php" style="display: none;">
  <input type="hidden" name="descripcion" id="descripcion">
  <input type="hidden" name="codigo" id="codigo">
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
<script src="<?php echo $baseUrl; ?>/modules/infraestructuras/js/infraestructuras.js"></script>

<script type="text/javascript">
  console.log('Aplicando Data Table');
  dataset1 = $('#tablaInfraestructuras').DataTable({
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
  var btnAcciones = '<div class="dropdown pull-right" id=""><button class="btn btn-primary btn-sm btn-outline" type="button" id="accionesTabla" data-toggle="dropdown" aria-haspopup="true">Acciones<span class="caret"></span></button><ul class="dropdown-menu pull-right" aria-labelledby="accionesTabla"><li><a onclick="window.open(\'exportar_infraestructuras.php\', \'_blank\');"><span class="fa fa-file-excel-o"></span> Exportar </a></li></ul></div>';

  $('.containerBtn').html(btnAcciones);
</script>

<?php mysqli_close($Link); ?>

</body>
</html>