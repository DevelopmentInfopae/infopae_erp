<?php
	require_once '../../header.php';
	set_time_limit (0);
	ini_set('memory_limit','6000M');
	$periodoActual = $_SESSION['periodoActual'];
	$titulo = "Suplentes";
?>

	<div class="row wrapper wrapper-content border-bottom white-bg page-heading">
	  	<div class="col-lg-8">
	      <h2><?= $titulo; ?></h2>
			<ol class="breadcrumb">
				<li>
				 	<a href="<?php echo $baseUrl; ?>">Inicio</a>
				</li>
				<li class="active">
				  	<strong><?= $titulo; ?></strong>
				</li>
			</ol>
	  	</div>
	  	<div class="col-lg-4">
		    <div class="title-action">
			    <?php if ($_SESSION['perfil'] == 1 || $_SESSION['perfil'] == 0) { ?>
			      	<button class="btn btn-primary" onclick="window.location.href = 'nuevo_suplente.php';"><span class="fa fa-plus"></span>  Nuevo</button>
			    <?php } ?>
		    </div>
	  	</div>
	</div>

	<div class="wrapper wrapper-content  animated fadeInRight">
	    <div class="row">
	        <div class="col-sm-12">
	              <div class="ibox">
	                  <div class="ibox-content">
	                      <div class="row">
	                    	<div class="col-sm-12">
	                        	<div class="table-responsive">
				                    <table class="table table-striped table-hover" id="tablaSuplentes">
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
												<th>Acciones</th>
											</tr>
										</thead>
										<tbody>
											<?php
												$consulta = "SELECT *, CONCAT(s.nom1, ' ', s.nom2, ' ', s.ape1, ' ', s.ape2) AS nombre, g.nombre as grado, jor.nombre as jornada
															FROM suplentes s
															LEFT JOIN grados g ON g.id = s.cod_grado
															LEFT JOIN jornada jor ON jor.id = s.cod_jorn_est";
												$resultado = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));
												if($resultado->num_rows >= 1) {
													while($row = $resultado->fetch_assoc()) {
											?>
											<tr numDoc="<?= $row['num_doc']; ?>" tipoDoc="<?= $row['tipo_doc']; ?>">
												<td><?= $row['num_doc']; ?></td>
												<td><?= $row['tipo_doc_nom']; ?></td>
												<td><?= $row['nombre']; ?></td>
												<td style="text-align: center;"><?= $row['genero']; ?></td>
												<td><?= $row['grado']; ?></td>
												<td style="text-align: center;"><?= $row['nom_grupo']; ?></td>
												<td><?= $row['jornada']; ?></td>
												<td style="text-align: center;"><?= $row['edad']; ?></td>
												<td>
													<div class="btn-group">
							                          	<div class="dropdown">
							                            	<button class="btn btn-primary btn-sm" type="button" id="accionesProducto" data-toggle="dropdown" aria-haspopup="true">Acciones <span class="caret"></span></button>
							                            	<ul class="dropdown-menu pull-right" aria-labelledby="accionesProducto">
							                           		<?php if ($_SESSION['perfil'] == 1 || $_SESSION['perfil'] == 0) { ?>
							                           			<li><a onclick="editarTitular(<?= $row['num_doc']; ?>)"><span class="fa fa-pencil"></span>  Editar</a></li>
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
												<th>Acciones</th>
											</tr>
										</tfoot>
				                    </table>
	                        	</div>
	  	                    </div>
	                    </div>
	                </div>
	            </div>
	        </div>
		</div>
	</div>

	<?php include '../../footer.php'; ?>

	<!-- Mainly scripts -->
	<script src="<?= $baseUrl; ?>/theme/js/jquery-3.1.1.min.js"></script>
	<script src="<?= $baseUrl; ?>/theme/js/bootstrap.min.js"></script>
	<script src="<?= $baseUrl; ?>/theme/js/plugins/metisMenu/jquery.metisMenu.js"></script>
	<script src="<?= $baseUrl; ?>/theme/js/plugins/slimscroll/jquery.slimscroll.min.js"></script>
	<script src="<?= $baseUrl; ?>/theme/js/plugins/dataTables/datatables.min.js"></script>

	<!-- Custom and plugin javascript -->
	<script src="<?= $baseUrl; ?>/theme/js/inspinia.js"></script>
	<script src="<?= $baseUrl; ?>/theme/js/plugins/pace/pace.min.js"></script>
	<script src="<?= $baseUrl; ?>/theme/js/plugins/toastr/toastr.min.js"></script>
	<script src="<?= $baseUrl; ?>/theme/js/plugins/validate/jquery.validate.min.js"></script>
	<script src="<?= $baseUrl; ?>/theme/js/plugins/iCheck/icheck.min.js"></script>
	<script src="<?= $baseUrl; ?>/theme/js/plugins/steps/jquery.steps.min.js"></script>
	<script src="<?= $baseUrl; ?>/theme/js/plugins/toggle/toggle.min.js"></script>
	<script src="<?= $baseUrl; ?>/theme/js/plugins/select2/select2.full.min.js"></script>
	<!-- Section Scripts -->

	<script src="<?= $baseUrl; ?>/modules/suplentes/js/suplentes.js"></script>


	<!-- Page-Level Scripts -->
	<script>
	  	dataset1 = $('#tablaSuplentes').DataTable({
	    	pageLength: 25,
	    	responsive: true,
	    	dom : '<"html5buttons" B>lr<"containerBtn"><"inputFiltro"f>tip',
	    	buttons : [{extend:'excel', title:'Suplentes', className:'btnExportarExcel', exportOptions: {columns : [0,1,2,3,4,5,6,7]}}],
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
	</script>

	<form id="editar_suplente" action="editar_suplente.php" method="post">
		<input type="hidden" name="numDoc" id="numDoc">
	</form>

	<form action="ver_suplente.php" method="post" name="verSuplente" id="verSuplente">
	  	<input type="hidden" name="numDoc" id="numDoc">
	  	<input type="hidden" name="tipoDoc" id="tipoDoc">
	</form>

	<?php mysqli_close($Link); ?>
</body>
</html>
