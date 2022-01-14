<?php
  include '../../header.php';

  if ($permisos['configuracion'] == "0" || $permisos['configuracion'] == "1") {
    ?><script type="text/javascript">
        window.open('<?= $baseUrl ?>', '_self');
    </script>
  <?php exit(); }

  $titulo = 'Parámetros Nómina'; 
?>

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
  </div> 
	<div class="col-lg-4">
	    <div class="title-action">
	  		<a href="#" class="btn btn-primary" id="crearParametrosNomina"><i class="fa fa-plus"></i> Nuevo </a>
    	</div>
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
								<th>Horas Mes</th>
								<th>Salario Mínimo</th>
								<th>Auxilio Transporte</th>
								<th>Descuento EPS</th>
								<th>Descuento AFP</th>
								<th>Entidad ARL</th>
								<th>Entidad CAJA</th>
								<th>Porcentaje CAJA</th>
								<th>Porcentaje ICBF</th>
								<th>Porcentaje SENA</th>
								<th>Retefuente Servicios</th>
								<th>Retefuente Honorarios</th>
								<th>Reteica</th>
								<th align="center">Acciones</th>
							</tr>
						</thead>
						<tbody>
							<?php 
								$consultaTabla = "SELECT * FROM parametros_nomina";
								$resConsultaTabla =  $Link->query($consultaTabla);
								if ($resConsultaTabla->num_rows > 0) {
									while ($dataRespuesta = $resConsultaTabla->fetch_assoc()) {
							?>
								<tr>
									<td><?php echo $dataRespuesta['hora_mes']; ?></td>
									<td><?php echo "$".number_format($dataRespuesta['salario_minimo'],2,',','.'); ?></td>
									<td><?php echo "$".number_format($dataRespuesta['auxilio_trans'],2,',','.'); ?></td>
									<td><?php echo $dataRespuesta['desc_eps']; ?></td>
									<td><?php echo $dataRespuesta['desc_afp']; ?></td>
									<td><?php 
										$consultaArlEntidad = "SELECT Entidad FROM nomina_entidad WHERE ID = ".$dataRespuesta['arl_entidad'].";";
										$respuestaConsultaArlEntidad = $Link->query($consultaArlEntidad);
										if ($respuestaConsultaArlEntidad->num_rows > 0) {
											$dataRespuestaConsultaArlEntidad = $respuestaConsultaArlEntidad->fetch_assoc();
										}
										echo $dataRespuestaConsultaArlEntidad['Entidad']; ?>		
									</td>
									<td><?php 
										$consultaCajaEntidad = "SELECT Entidad FROM nomina_entidad WHERE ID = ".$dataRespuesta['caja_entidad'].";";
										$respuestaConsultaCajaEntidad = $Link->query($consultaCajaEntidad);
										if ($respuestaConsultaCajaEntidad->num_rows > 0) {
											$dataRespuestaConsultaCajaEntidad = $respuestaConsultaCajaEntidad->fetch_assoc();
										}
										echo $dataRespuestaConsultaCajaEntidad['Entidad']; ?>		
									</td>
									<td><?php echo $dataRespuesta['caja_porc']; ?></td>
									<td><?php echo $dataRespuesta['icbf_porc']; ?></td>
									<td><?php echo $dataRespuesta['sena_porc']; ?></td>
									<td><?php echo $dataRespuesta['retefuente_servicios']; ?></td>
									<td><?php echo $dataRespuesta['retefuente_honorarios']; ?></td>
									<td><?php echo $dataRespuesta['reteica']; ?></td>
									<td align="left">
                      				<div class="btn-group">
                        				<div class="dropdown">
                         	 				<button class="btn btn-primary btn-sm" id="dLabel" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> Acciones <span class="caret"></span>
                          					</button>
                          					<ul class="dropdown-menu pull-right" aria-labelledby="dLabel">
                            					<li><a href="#" class="editarParametrosNomina" data-horaparametronomina = <?php echo $dataRespuesta['hora_mes']; ?> ><i class="fas fa-pencil-alt"></i> Editar</a></li>
                            					<li><a data-toggle="modal" data-target="#modalEliminarParametrosNomina"  data-horaparametronomina = <?php echo $dataRespuesta['hora_mes']; ?> ><span class="fa fa-trash"></span>  Eliminar</a></li>
                          					</ul>
                        				</div>
                      				</div>
                    			</td> 
								</tr>
							<?php 			
									}
								}
							?>
							<tfoot>
								<tr>
									<th>Horas Mes</th>
									<th>Salario Mínimo</th>
									<th>Auxilio Transporte</th>
									<th>Descuento EPS</th>
									<th>Descuento AFP</th>
									<th>Entidad ARL</th>
									<th>Entidad CAJA</th>
									<th>Porcentaje CAJA</th>
									<th>Porcentaje ICBF</th>
									<th>Porcentaje SENA</th>
									<th>Retefuente Servicios</th>
									<th>Retefuente Honorarios</th>
									<th>Reteica</th>
									<th align="center">Acciones</th>
								</tr>
							</tfoot>
						</tbody>
					</table> <!-- table -->
				</div> <!-- contentBackground -->
			</div> <!-- float-e-margins -->
		</div> <!-- col-lg-12 -->
	</div> <!-- row -->
</div> <!-- fadeInRight -->
<div id="contenedor_crear_parametros_nomina"></div>
<div id="contenedor_editar_parametros_nomina"></div>

<!-- Button trigger modal eliminar -->
<input type="hidden" name="inputBaseUrl" id="inputBaseUrl" value="<?php echo $baseUrl; ?>">

<div class="modal inmodal fade" id="modalEliminarParametrosNomina" tabindex="-1" role="dialog" style="display: none;" aria-hidden="true">
 <div class="modal-dialog modal-sm">
   <div class="modal-content">
     <div class="modal-header text-info" style="padding: 15px;">
       <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only">Cerrar</span></button>
       <h3><i class="fa fa-question-circle fa-lg" aria-hidden="true"></i> Confirmación InfoPAE </h3>
     </div>
     <div class="modal-body" style="text-align: center;">
         <span>¿Está seguro de borrar el parámetro nómina?</span>
         <input type="hidden" name="idParametroNominaEliminar" id="idParametroNominaEliminar">
     </div>
     <div class="modal-footer">
       <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal"><i class="fas fa-times"></i> No</button>
       <button type="button" class="btn btn-primary btn-sm" onclick="eliminarParametroNomina()"><i class="fa fa-check"></i> Si </button>
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

<!-- Custom and plugin javascript -->
<script src="<?php echo $baseUrl; ?>/theme/js/inspinia.js"></script>
<script src="<?php echo $baseUrl; ?>/theme/js/plugins/pace/pace.min.js"></script>
<script src="<?php echo $baseUrl; ?>/theme/js/plugins/dataTables/datatables.min.js"></script>
<script src="<?php echo $baseUrl; ?>/theme/js/plugins/toastr/toastr.min.js"></script>
<script src="<?php echo $baseUrl; ?>/theme/js/plugins/validate/jquery.validate.min.js"></script>
<script src="<?php echo $baseUrl; ?>/theme/js/plugins/select2/select2.full.min.js"></script>

<script type="text/javascript">
	$(document).ready(function(){
		$(document).on('click', '#crearParametrosNomina', function() { abrir_modal_crear_parametros_nomina(); });
		$(document).on('click', '#guardar_parametros_nomina', function() { guardar_parametros_nomina(); });
		$(document).on('click', '.editarParametrosNomina', function() { abrir_modal_editar_parametros_nomina($(this).data('horaparametronomina')); });
		$(document).on('click', '#editar_parametros_nomina', function() { actualizarParametrosNomina(); });

// });

	$('#box-table').DataTable({
		buttons: [ {extend: 'excel', title: 'Parámetros Nómina', className: 'btnExportarExcel', exportOptions: { columns: [ 0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12] } } ],
    	dom: 'lr<"containerBtn"><"inputFiltro"f>tip<"clear"><"html5buttons"B>',
    	order: [ 0, 'asc'],
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

})


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

function abrir_modal_crear_parametros_nomina(){
  	$('#contenedor_crear_parametros_nomina').load($('#inputBaseUrl').val() +'/modules/parametros_nomina/add.php');
}

function guardar_parametros_nomina(){
	if($('#formCrearParametrosNomina').valid()){
    $('#loader').fadeIn();
  		$.ajax({
    		type: "post",
    		url: "functions/fn_parametros_nomina_crear.php",
    		data: $('#formCrearParametrosNomina').serialize(),
    		dataType: 'json',
    		// beforeSend: function(){ $('#loader').fadeIn(); },
    		success: function(data) { 
            // console.log(data);
      			if(data.estado == 1){
        			Command: toastr.success(
        			data.mensaje,
        			"Creado",
          				{
                      onHidden : function(){
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
        			"Error al crear",
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
      			"Error al crear",
        			{
          			onHidden : function(){ $('#loader').fadeOut(); }
        			}
     			);
    		}
    	});
  	}
}

function abrir_modal_editar_parametros_nomina(valor_hora)
{
    $('#contenedor_editar_parametros_nomina').load($('#inputBaseUrl').val() +'/modules/parametros_nomina/edit.php?horParametroNomina='+valor_hora);
}

function actualizarParametrosNomina()
{
  if($('#formActualizarParametrosNomina').valid()){
    $.ajax({
      type: "post",
      url: "functions/fn_parametros_nomina_editar.php",
      data: $('#formActualizarParametrosNomina').serialize(),
      dataType: 'json',
      beforeSend: function(){ $('#loader').fadeIn(); },
      success: function(data){
        console.log(data);
        if(data.estado == 1){
          Command: toastr.success(
            data.mensaje,
            "Actualizado",
            {
              onHidden : function(){window.open('index.php', '_self');}
            }
          );
        }
        else
        {
          Command: toastr.warning(
            data.mensaje,
            "Error al actualizar",
            {
              onHidden : function(){ $('#loader').fadeOut(); }
            }
          );
        }
      },
      error: function(data)
      {
        console.log(data.responseText);
        Command: toastr.error(
          'Al parecer existe un error en el proceso',
          "Error al actualizar",
          {
            onHidden : function(){ $('#loader').fadeOut(); }
          }
        );
      }
    });
  }
}

$('#modalEliminarParametrosNomina').on('show.bs.modal', function (event) {
  var button = $(event.relatedTarget);
      horaparametronomina = button.data('horaparametronomina');
      // console.log(idparametromanipuladora);
      $('#idParametroNominaEliminar').val(horaparametronomina);
});

function eliminarParametroNomina(){
  $('#modalEliminarParametrosNomina').modal('hide');
  $('#loader').fadeIn();
  var id = $('#idParametroNominaEliminar').val();
  $.ajax({
      type: "POST",
      url: "functions/fn_parametros_nomina_eliminar.php",
      data: {"id" : id },
      dataType: 'json',
      success: function(data){
      // console.log(data);
        if(data.estado == 1){
            Command: toastr.success(
            data.mensaje,
            "Eliminado",
            {
              onHidden : function(){
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
            "Error al eliminar",
            {
              onHidden : function(){ $('#loader').fadeOut(); }
            }
          );
        }
    },
    error: function(data)
      {
          console.log(data.responseText);
          Command: toastr.error(
            'Al parecer existe un error en el proceso',
            "Error al eliminar",
          {
             onHidden : function(){ $('#loader').fadeOut(); }
          }
          );
      }
  });
}
</script>