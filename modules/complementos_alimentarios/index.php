<?php
include '../../header.php';

if ($permisos['configuracion'] == "0" || $permisos['configuracion'] == "1") {
?><script type="text/javascript">
    window.open('<?= $baseUrl ?>', '_self');
</script>
<?php exit(); }
	  else {
		?><script type="text/javascript">
		  const list = document.querySelector(".li_configuracion");
		  list.className += " active ";
		</script>
	  <?php
	  }

$nameLabel = get_titles('configuracion', 'complementosAlimentarios', $labels);
$titulo = $nameLabel;
?>

<style>
	.fa-check {
 	 	color : green;
	}
	.fa-circle {
		color : gray;
	}
</style>

<div class="row wrapper wrapper-content border-bottom white-bg page-heading">
  	<div class="col-lg-8">
    	<h2><?= $titulo; ?></h2>
    	<ol class="breadcrumb">
      		<li>
        		<a href="<?= $baseUrl; ?>">Home</a>
      		</li>
      		<li class="active">
        		<strong><?= $titulo; ?></strong>
      		</li>
    	</ol>
  	</div>
	<div class="col-lg-4">
    	<?php if ($_SESSION['perfil'] == "0" || $permisos['configuracion'] == "2"): ?>
      		<div class="title-action">
        		<a href="#" class="btn btn-primary" id="crearComplementosAlimentarios"><i class="fa fa-plus"></i> Nuevo </a>
      		</div>
    	<?php endif ?>
  	</div>
</div>

<div class="wrapper wrapper-content animated fadeInRight">
  	<div class="row">
    	<div class="col-lg-12">
      		<div class="ibox float-e-margins">
        		<div class="ibox-content contentBackground">
          			<table id="box-table" class="table table-striped table-hover selectableRows">
            			<thead>
              				<tr>
                				<th>Código</th>
                				<th>Descripción</th>
                				<th>Jornada</th>
                				<th>Valor ración</th>
                				<th>Raciones Contratadas</th>
                				<th class="text-center">Jornada Única</th>
                				<th class="text-center">Acciones</th>
              				</tr>
            			</thead>
            			<tbody>
              			<?php
                			$consulta = "SELECT 
											tco.CODIGO AS codigoTipoComplemento, 
											tco.ID AS idTipoComplemento, 
											tco.DESCRIPCION AS descripcionTipoComplemento, 
											tco.ValorRacion AS valorRacionTipoComplemento, 
											tco.numero_raciones_contratadas AS numeroRacionesContratadas, 
											tco.jornadaUnica AS jornadaUnica, 
											jor.id As idJornada, 
											jor.nombre AS nombreJornada
                            			FROM tipo_complemento tco
                            			INNER JOIN jornada jor ON jor.id = tco.jornada";
                			$resultado = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));
                			if($resultado){
                  				while($registros = $resultado->fetch_assoc()){
              			?>
              				<tr>
                				<td align="left"><?= $registros['codigoTipoComplemento']; ?></td>
                				<td align="left"><?= $registros['descripcionTipoComplemento']; ?></td>
                				<td align="left"><?= $registros['nombreJornada']; ?></td>
                				<td align="left"><?= $registros['valorRacionTipoComplemento']; ?></td>
                				<td align="left"><?= $registros['numeroRacionesContratadas']; ?></td>
                				<td align="center"><?php 	if($registros['jornadaUnica']==1){ echo "<i class='fas fa-check'></i>"; } 
														else {echo "<i class='fas fa-circle'></i>";} ?>
								</td>
                				<td align="center">
                  					<div class="btn-group">
                    					<div class="dropdown">
                      						<button class="btn btn-primary btn-sm" id="dLabel" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        						Acciones <span class="caret"></span>
                      						</button>
                      						<ul class="dropdown-menu pull-right" aria-labelledby="dLabel">
                        						<li><a href="#" class="editarComplementoAlimentario" data-idTipoComplemento="<?= $registros['idTipoComplemento']; ?>"><i class="fas fa-pencil-alt fa-lg"></i> Editar</a></li>
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
                				<th>Código</th>
                				<th>Descripción</th>
                				<th>Jornada</th>
                				<th>Valor ración</th>
                				<th>Raciones Contratadas</th>
                				<th class="text-center">Jornada Única</th>
                				<th class="text-center">Acciones</th>
              				</tr>
            			</tfoot>
          			</table>
        		</div>
      		</div>
    	</div>
  	</div>
</div>

<form action="complementos_alimentarios_editar.php" method="post" name="formEditarComplementosAlimentarios" id="formEditarComplementosAlimentarios">
  	<input type="hidden" name="idTipoComplemento" id="idTipoComplemento">
</form>

<?php include '../../footer.php'; ?>

<!-- Mainly scripts -->
<script src="<?= $baseUrl; ?>/theme/js/jquery-3.1.1.min.js"></script>
<script src="<?= $baseUrl; ?>/theme/js/bootstrap.min.js"></script>
<script src="<?= $baseUrl; ?>/theme/js/plugins/metisMenu/jquery.metisMenu.js"></script>
<script src="<?= $baseUrl; ?>/theme/js/plugins/slimscroll/jquery.slimscroll.min.js"></script>

<!-- Custom and plugin javascript -->
<script src="<?= $baseUrl; ?>/theme/js/inspinia.js"></script>
<script src="<?= $baseUrl; ?>/theme/js/plugins/pace/pace.min.js"></script>
<script src="<?= $baseUrl; ?>/theme/js/plugins/dataTables/datatables.min.js"></script>
<script src="<?= $baseUrl; ?>/theme/js/plugins/toastr/toastr.min.js"></script>
<script src="<?= $baseUrl; ?>/theme/js/plugins/validate/jquery.validate.min.js"></script>
<script src="<?= $baseUrl; ?>/theme/js/plugins/iCheck/icheck.min.js"></script>

<!-- Section Scripts -->
<script src="<?= $baseUrl; ?>/modules/complementos_alimentarios/js/complementos_alimentarios.js"></script>
<script>
  	$('#box-table').DataTable({
    	buttons: [ {extend: 'excel', title: 'Grupo_etarios', className: 'btnExportarExcel', exportOptions: { columns: [ 0, 1, 2] } } ],
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
                        		'<li><a tabindex="0" aria-controls="box-table" href="#" onclick="$(\'.btnExportarExcel\').click();"><i class="fa fa-file-excel-o"></i> Exportar </a></li>'+
                      		'</ul>'+
                    	'</div>';
  	$('.containerBtn').html(botonAcciones);
</script>

<?php mysqli_close($Link); ?>

</body>
</html>