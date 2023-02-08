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

  $titulo = 'Prioridad Caracterización'; 
  $nameLabel = get_titles('configuracion', 'prioridadCaracterizacion', $labels);
  $titulo = $nameLabel;
?>

<div class="row wrapper wrapper-content border-bottom white-bg page-heading">
  	<div class="col-lg-12">
    	<h2><?php echo $titulo; ?></h2>
    	<ol class="breadcrumb">
      		<li>
        		<a href="<?php echo $baseUrl; ?>">Inicio</a>
      		</li>
      		<li class="active">
        		<strong><?php echo $titulo; ?></strong>
      		</li>
    	</ol>
  </div> 
</div>

<div class="wrapper wrapper-content animated fadeInRight">
	<div class="row">
		<div class="col-lg-12">
			<div class="ibox float-e-margins">
				<div class="ibox-content contentBackground">
					<table id="box-table" class="table table-striped table-hover selectableRows table-responsive">
						<thead>
							<tr>
								<th>Descripción</th>
								<th>Orden</th>
								<th>Entregas</th>
								<th>Valor por Defecto</th>
								<th>Acciones</th>
							</tr>
						</thead>
						<tbody>
							<?php 
							$consulta = "SELECT id, descripcion, orden, campo_entregas_res, valor_NA FROM prioridad_caracterizacion;";
							$respuesta = $Link->query($consulta);
							if ($respuesta->num_rows >0) {
								while ($dataRespuesta = $respuesta->fetch_assoc()) {
							?>
							<tr>
								<td><?php echo $dataRespuesta['descripcion'] ?></td>
								<td><?php echo $dataRespuesta['orden'] ?></td>
								<td><?php echo $dataRespuesta['campo_entregas_res']; ?></td>
								<td><?php echo $dataRespuesta['valor_NA']; ?></td>
								<td align="left">
                      				<div class="btn-group">
                        				<div class="dropdown">
                         	 				<button class="btn btn-primary btn-sm" id="dLabel" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> Acciones <span class="caret"></span>
                          					</button>
                          					<ul class="dropdown-menu pull-right" aria-labelledby="dLabel">
                            					<li><a href="#" class="subirPrioriodadCaracterizacion" data-idprioridadcaracterizacion = <?php echo $dataRespuesta['id']; ?> ><i class="fas fa-arrow-up"></i> Subir Orden</a></li>
                            					<li><a href="#" class="bajarPrioriodadCaracterizacion" data-idprioridadcaracterizacion = <?php echo $dataRespuesta['id']; ?> ><i class="fas fa-arrow-down"></i> Bajar Orden</a></li>
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
								<th>Descripción</th>
								<th>Orden</th>
								<th>Entregas</th>
								<th>Valor por Defecto</th>
								<th>Acciones</th>
							</tr>	
						</tfoot>
					</table>
				</div><!--  ibox-content -->
			</div><!-- float-e-margins -->
		</div> <!-- col-lg-12 -->
	</div> <!-- row -->
</div> <!-- fadeInRight -->


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
<script src="<?php echo $baseUrl; ?>/theme/js/plugins/toastr/toastr.min.js"></script>
<!-- <script src="<?php echo $baseUrl; ?>/theme/js/plugins/validate/jquery.validate.min.js"></script> -->

<script type="text/javascript">
	$(document).ready(function(){
		$('.subirPrioriodadCaracterizacion').on('click', function(){subirPrioriodadCaracterizacion($(this).data('idprioridadcaracterizacion'))});
		$('.bajarPrioriodadCaracterizacion').on('click', function(){bajarPrioriodadCaracterizacion($(this).data('idprioridadcaracterizacion'))});

		$('#box-table').DataTable({
		buttons: [ {extend: 'excel', title: 'Prioridad Caracterización', className: 'btnExportarExcel', exportOptions: { columns: [ 0, 1, 2 ,3] } } ],
    	dom: 'lr<"containerBtn"><"inputFiltro"f>tip<"clear"><"html5buttons"B>',
    	order: [ 1, 'desc'],
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
    	pageLength: 10,
    	responsive: true,
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

  		  toastr.options = {
		    "closeButton": true,
		    "debug": false,
		    "progressBar": true,
		    "preventDuplicates": false,
		    "positionClass": "toast-top-right",
		    "onclick": null,
		    "showDuration": "400",
		    "hideDuration": "1000",
		    "timeOut": "2000",
		    "extendedTimeOut": "1000",
		    "showEasing": "swing",
		    "hideEasing": "linear",
		    "showMethod": "fadeIn",
		    "hideMethod": "fadeOut"
		  }  
	});

	function subirPrioriodadCaracterizacion(id){
		var idP = id;
		$.ajax({
			type: "post",
			url: "functions/fn_subir_prioridad_caracterizacion.php",
			data: {"idP" : id },
			dataType: "json",
			beforeSend: function(){ $('#loader').fadeIn(); },
			success: function(data){
				console.log(data);
				if (data.estado == 1) {
					comman : toastr.success(
						data.mensaje,
						"Orden Aumentado",
						{
							onHidden: function(){
								$('#loader').fadeOut();
								window.open('index.php', '_self');   
							}
						}
					);
				}
				else
      			{
        			Command: toastr.warning(
        			data.mensaje,
        			"Error al aumentar",
          				{
            			onHidden : function(){ $('#loader').fadeOut(); }
          				}
        			);
      			}
			},
			error: function(data) {
      			console.log(data.responseText);
      			Command: toastr.error(
      			'Al parecer existe un error en el proceso',
      			"Error al aumentar",
        			{
          			onHidden : function(){ $('#loader').fadeOut(); }
        			}
     			);
    		}
		});
	}

function bajarPrioriodadCaracterizacion(id){
		var idP = id;
		$.ajax({
			type: "post",
			url: "functions/fn_bajar_prioridad_caracterizacion.php",
			data: {"idP" : id },
			dataType: "json",
			beforeSend: function(){ $('#loader').fadeIn(); },
			success: function(data){
				console.log(data);
				if (data.estado == 1) {
					comman : toastr.success(
						data.mensaje,
						"Orden Disminuido",
						{
							onHidden: function(){
								$('#loader').fadeOut();
								window.open('index.php', '_self');   
							}
						}
					);
				}
				else
      			{
        			Command: toastr.warning(
        			data.mensaje,
        			"Error al disminuir",
          				{
            			onHidden : function(){ $('#loader').fadeOut(); }
          				}
        			);
      			}
			},
			error: function(data) {
      			console.log(data.responseText);
      			Command: toastr.error(
      			'Al parecer existe un error en el proceso',
      			"Error al disminuir",
        			{
          			onHidden : function(){ $('#loader').fadeOut(); }
        			}
     			);
    		}
		});
	}
</script>
