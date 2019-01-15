<?php
require_once '../../header.php';
set_time_limit (0);
ini_set('memory_limit','6000M');
$periodoActual = $_SESSION['periodoActual'];
?>

<div class="row wrapper wrapper-content border-bottom white-bg page-heading">
  	<div class="col-lg-8">
      <h2>Titulares de derecho</h2>
		<ol class="breadcrumb">
			<li>
			 	<a href="<?php echo $baseUrl; ?>">Home</a>
			</li>
			<li class="active">
			  	<strong>Titulares de derecho</strong>
			</li>
		</ol>
  	</div>
  	<div class="col-lg-4">
	    <div class="title-action">

	    <?php if ($_SESSION['perfil'] == 1 || $_SESSION['perfil'] == 0): ?>
	      <button class="btn btn-primary" onclick="window.location.href = 'nuevo_titular.php';"><span class="fa fa-plus"></span>  Nuevo</button>
	    <?php endif ?>
	    </div>
  	</div><!-- /.col -->
</div>


<div class="wrapper wrapper-content  animated fadeInRight">
    <div class="row">
        <div class="col-sm-12">
            <div class="ibox">
                <div class="ibox-content">
					<h3>Seleccione la semana de focalización</h3>
					<?php
					$consulta = " select distinct semana from planilla_semanas ";
					$resultado = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));
					if($resultado->num_rows >= 1){
						while($row = $resultado->fetch_assoc()){
							$aux = $row['semana'];
							$consulta2 = " show tables like 'focalizacion$aux' ";
							$resultado2 = $Link->query($consulta2) or die ('Unable to execute query. '. mysqli_error($Link));
							if($resultado2->num_rows >= 1){
							 $semanas[] = $aux;
							}
						}
					}
					//var_dump($semanas);
					?>
					<div class="row">
						<div class="col-sm-12">
							<form class="" action="" method="POST">
								<div class="row">
									<div class="col-sm-3 form-group">
										<label for="semana">Semana</label>
										<select class="form-control" name="semana" id="semana" required>
											<option value="">Seleccione una</option>
											<?php foreach ($semanas as $semana){ ?>
												<option value="<?php echo $semana; ?>" <?php if(isset($_POST['semana']) && $_POST['semana'] == $semana){echo " selected "; }  ?>><?php echo $semana; ?></option>
											<?php } ?>
										</select>
									</div>
									<?php if (isset($_POST['cod_sede']) && $_POST['cod_sede'] != ""): ?>
										<input type="hidden" name="cod_sede" value="<?php echo $_POST['cod_sede']; ?>">
									<?php endif ?>
									<?php if (isset($_POST['cod_inst']) && $_POST['cod_inst'] != ""): ?>
										<input type="hidden" name="cod_inst" value="<?php echo $_POST['cod_inst']; ?>">
									<?php endif ?>
									<div class="col-sm-3">
										<label>Municipio</label>
										<select name="municipio_titular" id="municipio_titular" class="form-control" required>
											<option value="">Seleccione...</option>
										</select>
									</div>
									<div class="col-sm-3">
										<label>Institución</label>
										<select name="institucion_titular" id="institucion_titular" class="form-control" required>
											<option value="">Seleccione...</option>
										</select>
									</div>
									<div class="col-sm-3">
										<label>Sede</label>
										<select name="sede_titular" id="sede_titular" class="form-control" required>
											<option value="">Seleccione...</option>
										</select>
									</div>
								</div>
								<div class="row">
									<div class="col-sm-3 form-group">
										<button class="btn btn-primary" type="submit"> <span class="fa fa-search"></span> Buscar</button>
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






<?php
if( isset($_POST['semana']) && $_POST['semana'] !='' ){
	$semana = $_POST['semana'];

	if (isset($_POST['institucion_titular'])) {
		$cod_inst = " WHERE f.cod_inst = ".$_POST['institucion_titular']." ";
	} else {
 		$cod_inst = "";
	}

	if (isset($_POST['sede_titular'])) {
		if ($cod_inst == "") {
			$cod_sede = " WHERE f.cod_sede = ".$_POST['sede_titular']." ";
		} else {
			$cod_sede = " AND f.cod_sede = ".$_POST['sede_titular']." ";
		}
	} else {
 		$cod_sede = "";
	}

?>

<div class="wrapper wrapper-content  animated fadeInRight">
    <div class="row">
        <div class="col-sm-12">
              <div class="ibox">
                  <div class="ibox-content">
                      <div class="row">
                    	<div class="col-sm-12">

                        <div class="table-responsive">

                    <table class="table table-striped table-hover" id="tablaTitulares">
						<thead>
							<tr>
								<th>Num doc</th>
								<th>Tipo doc</th>
								<th>Nombre</th>
								<th>Género</th>
								<th>Grado</th>
								<th>Grupo</th>
								<th>Jornada</th>
								<th>Edad</th>
								<th>Tipo complemento</th>
								<th>Acciones</th>
							</tr>
						</thead>
						<tbody>
							<?php

							$consulta = " SELECT f.num_doc, f.activo, t.Abreviatura AS tipo_doc, CONCAT(f.nom1, ' ', f.nom2, ' ', f.ape1, ' ', f.ape2) AS nombre, f.genero, g.nombre as grado, f.nom_grupo, jor.nombre as jornada, f.edad, f.Tipo_complemento, GROUP_CONCAT(f.Tipo_complemento) as complementos FROM focalizacion$semana f LEFT JOIN tipodocumento t ON t.id = f.tipo_doc LEFT JOIN grados g ON g.id = f.cod_grado LEFT JOIN jornada jor ON jor.id = f.cod_jorn_est ".$cod_inst.$cod_sede." GROUP BY f.num_doc order by f.nom1 asc";

							$resultado = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));
							if($resultado->num_rows >= 1){
								while($row = $resultado->fetch_assoc()){ ?>
								<tr numDoc="<?php echo $row['num_doc']; ?>" tipoDoc="<?php echo $row['tipo_doc']; ?>" style="cursor:pointer">
									<td><?php echo $row['num_doc']; ?></td>
									<td><?php echo $row['tipo_doc']; ?></td>
									<td><?php echo $row['nombre']; ?></td>
									<td style="text-align_center;"><?php echo $row['genero']; ?></td>
									<td><?php echo $row['grado']; ?></td>
									<td style="text-align:center;"><?php echo $row['nom_grupo']; ?></td>
									<td><?php echo $row['jornada']; ?></td>
									<td style="text-align:center;"><?php echo $row['edad']; ?></td>
									<td style="text-align:center;"><?php echo $row['complementos']; ?></td>
									<td><div class="btn-group">
			                          <div class="dropdown">
			                            <button class="btn btn-primary btn-sm" type="button" id="accionesProducto" data-toggle="dropdown" aria-haspopup="true">
			                              Acciones
			                              <span class="caret"></span>
			                            </button>
			                            <ul class="dropdown-menu pull-right" aria-labelledby="accionesProducto">
			                           	<?php if ($_SESSION['perfil'] == 1 || $_SESSION['perfil'] == 0): ?>
			                           		<li><a onclick="editarTitular(<?php echo $row['num_doc']; ?>)"><span class="fa fa-pencil"></span>  Editar</a></li>
			                                <?php if ($row['activo'] == 1): ?>
			                                <li data-idtitular="<?php echo $row['num_doc']; ?>" data-accion="1"><a> Estado : <input class="form-control estadoEst" type="checkbox" data-toggle="toggle" data-size="mini" data-on="Activo" data-off="Inactivo" data-width="74px" checked></a></li>
			                               	<?php elseif($row['activo'] == 0): ?>
			                                <li data-idtitular="<?php echo $row['num_doc']; ?>" data-accion="0"><a> Estado : <input class="form-control estadoEst" type="checkbox" data-toggle="toggle" data-size="mini" data-on="Activo" data-off="Inactivo" data-width="74px" ></a></li>
			                                <?php endif ?>
			                            <?php else: ?>
			                                <?php if ($row['activo'] == 1): ?>
			                                <li>
			                                  <a><span class="fa fa-check"></span> Estado : <b>Activo</b></a>
			                                </li>
			                               	<?php elseif($row['activo'] == 0): ?>
			                               	<li>
			                                  <a></span> Estado : <b>Inactivo</b></a>
			                                </li>
			                                <?php endif ?>
			                           	<?php endif ?>
			                               <li>
			                                <a><span class="fa fa-file-excel-o"></span> Exportar</a>
			                               </li>
			                            </ul>
			                          </div>
			                        </div>
			                    	</td>
								</tr>
								<?php }
							}
							?>
						</tbody>
						<tfoot>
							<tr>
								<th>Num doc</th>
								<th>Tipo doc</th>
								<th>Nombre</th>
								<th>Genero</th>
								<th>Grado</th>
								<th>Grupo</th>
								<th>Jornada</th>
								<th>Edad</th>
								<th>Tipo COMP</th>
								<th>Acciones</th>
							</tr>
						</tfoot>
                    </table>
                        </div>
                        <!-- Termina table responsive -->
                        </div>
                      </div>
                  </div>
              </div>
          </div>
     </div>
 </div>

<?php } ?>

<form id="editar_titular" action="editar_titular.php" method="post">
	<input type="hidden" name="num_doc_editar" id="num_doc_editar">
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
<script src="<?php echo $baseUrl; ?>/theme/js/plugins/toggle/toggle.min.js"></script>
<!-- Section Scripts -->

<script src="<?php echo $baseUrl; ?>/modules/titulares_derecho/js/titulares_derecho.js"></script>



<!-- Page-Level Scripts -->


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


    <!-- Page-Level Scripts -->
    <script>

  dataset1 = $('#tablaTitulares').DataTable({
    /*order: [ 0, 'asc' ],*/
    pageLength: 25,
    responsive: true,
    dom : '<"html5buttons" B>lr<"containerBtn"><"inputFiltro"f>tip',
    buttons : [{extend:'excel', title:'Titulares_semana', className:'btnExportarExcel', exportOptions: {columns : [0,1,2,3,4,5,6,7,8]}}],
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
    }).on("draw", function(){jQuery('.estadoEst').bootstrapToggle();});
   var btnAcciones = '<div class="dropdown pull-right" id=""><button class="btn btn-primary btn-sm btn-outline" type="button" id="accionesTabla" data-toggle="dropdown" aria-haspopup="true">Acciones<span class="caret"></span></button><ul class="dropdown-menu pull-right" aria-labelledby="accionesTabla"><li><a onclick="$(\'.btnExportarExcel\').click()"><span class="fa fa-file-excel-o"></span> Exportar </a></li></ul></div>';

  $('.containerBtn').html(btnAcciones);


$(document).on('click', '.dropdown-menu li:nth-child(2)', function(event){
	event.stopPropagation();
});

<?php if(isset($_POST['semana'])): ?>
	$('#semana').change();
<?php endif ?>

<?php if(isset($_POST['municipio_titular'])): ?>
	setTimeout(function() {$('#municipio_titular').val('<?php echo $_POST['municipio_titular']; ?>').change();}, 800);
<?php endif ?>

<?php if(isset($_POST['institucion_titular'])): ?>
	setTimeout(function() {$('#institucion_titular').val('<?php echo $_POST['institucion_titular']; ?>').change();}, 1500);
<?php endif ?>

<?php if(isset($_POST['sede_titular'])): ?>
	setTimeout(function() {$('#sede_titular').val('<?php echo $_POST['sede_titular']; ?>').change();}, 2200);
<?php endif ?>

    </script>

<form action="titular.php" method="post" name="verTitular" id="verTitular">
  <input type="hidden" name="numDoc" id="numDoc">
  <input type="hidden" name="tipoDoc" id="tipoDoc">
  <input type="hidden" name="semana" id="semana" value="<?php if(isset($_POST['semana']) && $_POST['semana'] != ''){echo $_POST['semana']; }?>">
</form>




</body>
</html>
