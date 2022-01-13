$(document).ready(function(){
	$(document).on('click', '#crearTipoPersonaFqrs', function() { abrir_modal_crear_persona_fqrs(); });
	$(document).on('click', '#guardar_tipo_persona_fqrs', function() { guardar_tipo_persona_fqrs(); });
	$(document).on('click', '.editarTipoPersonaFqrs', function() { abrir_modal_editar_persona_fqrs($(this).data('idtipopersonafqrs')); });
	$(document).on('click', '#actualizar_tipo_persona_fqrs', function() { actualizar_tipo_persona_fqrs(); })

	$('#box-table').DataTable({
		buttons: [ {extend: 'excel', title: 'Tipo Persona FQRS', className: 'btnExportarExcel', exportOptions: { columns: [ 0, 1] } } ],
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

function abrir_modal_crear_persona_fqrs(){
	$('#contenedor_crear_tipo_persona_fqrs').load($('#inputBaseUrl').val() +'/modules/tipo_personafqrs/add.php');
}

function guardar_tipo_persona_fqrs(){
	if($('#formCrearTipoPersonaFqrs').valid()){
    $('#loader').fadeIn();
  		$.ajax({
    		type: "post",
    		url: "functions/fn_tipo_personaFqrs_crear.php",
    		data: $('#formCrearTipoPersonaFqrs').serialize(),
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

function abrir_modal_editar_persona_fqrs(id){
	$('#contenedor_editar_tipo_persona_fqrs').load($('#inputBaseUrl').val() +'/modules/tipo_personafqrs/edit.php?idTipoPersonaFqrs='+id);
}

function actualizar_tipo_persona_fqrs()
{
  if($('#formActualizarTipoPersonaFqrs').valid()){
    $.ajax({
      type: "post",
      url: "functions/fn_tipo_personaFqrs_editar.php",
      data: $('#formActualizarTipoPersonaFqrs').serialize(),
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

$('#modalEliminarTipoPersonaFqrs').on('show.bs.modal', function (event) {
  var button = $(event.relatedTarget);
      idTipoPersonaFqrs = button.data('idtipopersonafqrs');
      // console.log(idparametromanipuladora);
      $('#idTipoPersonaFqrs').val(idTipoPersonaFqrs);
});

function eliminarTipoPersonaFqrs(){
  $('#modalEliminarTipoPersonaFqrs').modal('hide');
  $('#loader').fadeIn();
  var id = $('#idTipoPersonaFqrs').val();
  $.ajax({
      type: "POST",
      url: "functions/fn_tipo_personaFqrs_eliminar.php",
      data: {"id" : id },
      dataType: 'json',
      success: function(data){
      console.log(data);
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