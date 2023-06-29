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
			  const list2 = document.querySelector(".li_parametrosInfraestructura");
			  list2.className += " active ";
			</script>
			<?php
			}

  $titulo = 'Parámetros Infraestructura'; 
  $nameLabel = get_titles('configuracion', 'parametrosInfraestructura', $labels);
  $titulo = $nameLabel;
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
	  		<a href="#" class="btn btn-primary" id="crearParametrosInfraestructura"><i class="fa fa-plus"></i> Nuevo </a>
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
								<th>Identificador</th>
								<th>Descripción</th>
								<th>Acciones</th>
							</tr>
						</thead>
						<tbody>
							<?php
							$consulta = "SELECT id, descripcion FROM parametros_infraestructura;";
							$respuesta = $Link->query($consulta); 
							if ($respuesta->num_rows > 0) {
								while ($dataRespuesta = $respuesta->fetch_assoc()) {
							?>
							<tr>
								<td><?php echo $dataRespuesta['id']; ?></td>
								<td><?php echo $dataRespuesta['descripcion']; ?></td>
								<td align="left">
                      				<div class="btn-group">
                        				<div class="dropdown">
                         	 				<button class="btn btn-primary btn-sm" id="dLabel" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> Acciones <span class="caret"></span>
                          					</button>
                          					<ul class="dropdown-menu pull-right" aria-labelledby="dLabel">
                            					<li><a href="#" class="editarParametrosInfraestructura" data-idparametroinfraestructura = <?php echo $dataRespuesta['id']; ?> ><i class="fas fa-pencil-alt"></i> Editar</a></li>
                            					<li><a data-toggle="modal" data-target="#modalEliminarParametrosInfraestructura"  data-idparametroinfraestructura = <?php echo $dataRespuesta['id']; ?> ><span class="fa fa-trash"></span>  Eliminar</a></li>
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
								<th>Identificador</th>
								<th>Descripción</th>
								<th>Acciones</th>
							</tr>
						</tfoot>
					</table> <!-- table -->
				</div> <!-- ibox-content -->
			</div> <!-- float-e-margins -->
		</div> <!-- col-lg-12 -->
	</div> <!-- row -->
</div> <!-- fadeInRight -->

<div id="contenedor_crear_parametros_infraestructura"></div>
<div id="contenedor_editar_parametros_infraestructura"></div>

<!-- Button trigger modal -->
<input type="hidden" name="inputBaseUrl" id="inputBaseUrl" value="<?php echo $baseUrl; ?>">

<div class="modal inmodal fade" id="modalEliminarParametrosInfraestructura" tabindex="-1" role="dialog" style="display: none;" aria-hidden="true">
 <div class="modal-dialog modal-sm">
   <div class="modal-content">
     <div class="modal-header text-info" style="padding: 15px;">
       <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only">Cerrar</span></button>
       <h3><i class="fa fa-question-circle fa-lg" aria-hidden="true"></i> Confirmación InfoPAE </h3>
     </div>
     <div class="modal-body" style="text-align: center;">
         <span>¿Está seguro de borrar el parámetro infraestructura?</span>
         <input type="hidden" name="idParametroInfraestructuraEliminar" id="idParametroInfraestructuraEliminar">
     </div>
     <div class="modal-footer">
       <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal"><i class="fas fa-times"></i> No</button>
       <button type="button" class="btn btn-primary btn-sm" onclick="eliminarParametroInfraestructura()"><i class="fa fa-check"></i> Si </button>
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
<!-- <script src="<?php echo $baseUrl; ?>/theme/js/plugins/select2/select2.full.min.js"></script> -->

<script type="text/javascript">
$(document).ready(function(){
	$(document).on('click', '#crearParametrosInfraestructura', function() { abrir_modal_crear_parametros_infraestructura(); });
	$(document).on('click', '#guardar_parametros_infraestructura', function() { guardar_parametros_infraestructura(); });
	$(document).on('click', '.editarParametrosInfraestructura', function() { abrir_modal_editar_parametros_infraestructura($(this).data('idparametroinfraestructura')); });
	$(document).on('click', '#actualizar_parametros_infraestructura', function() { actualizarParametrosInfraestructura(); });

	$('#box-table').DataTable({
		buttons: [ {extend: 'excel', title: 'Parámetros Infraestructura', className: 'btnExportarExcel', exportOptions: { columns: [ 0, 1] } } ],
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

});	

function abrir_modal_crear_parametros_infraestructura(){
  	$('#contenedor_crear_parametros_infraestructura').load($('#inputBaseUrl').val() +'/modules/parametros_infraestructura/add.php');
}

function guardar_parametros_infraestructura(){
	if($('#formCrearParametrosInfraestructura').valid()){
    $('#loader').fadeIn();
  		$.ajax({
    		type: "post",
    		url: "functions/fn_parametros_infraestructura_crear.php",
    		data: $('#formCrearParametrosInfraestructura').serialize(),
    		dataType: 'json',
    		beforeSend: function(){ $('#loader').fadeIn(); },
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

function abrir_modal_editar_parametros_infraestructura(valor_id)
{
    $('#contenedor_editar_parametros_infraestructura').load($('#inputBaseUrl').val() +'/modules/parametros_infraestructura/edit.php?idparametroinfraestructura='+valor_id);
}

function actualizarParametrosInfraestructura()
{
  if($('#formActualizarParametrosInfraestructura').valid()){
    $.ajax({
      type: "post",
      url: "functions/fn_parametros_infraestructura_editar.php",
      data: $('#formActualizarParametrosInfraestructura').serialize(),
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

$('#modalEliminarParametrosInfraestructura').on('show.bs.modal', function (event) {
  var button = $(event.relatedTarget);
      idparametroinfraestructura = button.data('idparametroinfraestructura');
      // console.log(idparametromanipuladora);
      $('#idParametroInfraestructuraEliminar').val(idparametroinfraestructura);
});

function eliminarParametroInfraestructura(){
  $('#modalEliminarParametrosInfraestructura').modal('hide');
  $('#loader').fadeIn();
  var id = $('#idParametroInfraestructuraEliminar').val();
  $.ajax({
      type: "POST",
      url: "functions/fn_parametros_infraestructura_eliminar.php",
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