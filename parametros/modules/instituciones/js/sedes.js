$(document).ready(function(){
	$('#municipio').change(function(){ buscarInstitucion($(this).val()); });
	$('#btnBuscar').click(function() { buscarSedes();	});

	// Enviando al formulario Ver Sede.
	$('.dataTablesSedes tbody td:nth-child(-n+4)').click(function(){
		$('#formVerSede #codSede').val($(this).closest('tr').attr('codsede'));
		$('#formVerSede #nomSede').val($(this).closest('tr').attr('nomsede'));
		$('#formVerSede #nomInst').val($(this).closest('tr').attr('nominst'));
		$('#formVerSede').submit();	
	});

	// Configuración del pligin toast
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

function crearSede(){
  window.open('sede_crear.php', '_self');
}

function buscarInstitucion(municipio){
	$.ajax({
	  type: "post",
	  url: "functions/fn_buscar_instituciones.php",
	  data: {"municipio":municipio},
	  beforeSend: function(){ $('#loader').fadeIn(); },
	  success: function(data){
	  	$('#loader').fadeOut();
	    $('#institucion').html(data);
	  },
	  error: function(data){
	  	$('#loader').fadeOut();
	  	console.log(data);
	  }
	});
}

function buscarSedes(){
	if($('#formSedes').valid()){ $('#formSedes').submit(); }
}

function cargarArchivo(){
	$('#loader').fadeIn();

	var formData = new FormData();
  formData.append('archivoSede', $('#archivoSede')[0].files[0]);

  $.ajax({
    type: "POST",
    url: "functions/fn_sedes_cargar_archivo.php",
    contentType: false,
    processData: false,
    data: formData,
    dataType: 'html',
    beforeSend: function(){},
    success: function(data){ console.log(data);
    	$('#loader').fadeOut();
      // $('#ventanaInformar .modal-body p').html(data.mensaje);
      // $('#ventanaInformar').modal();
      // $('#ventanaInformar').on('hidden.bs.modal', function (e) {
    		// $('.fileinput').fileinput('reset');
      // });
    },
    error: function(data){ console.log(data);
    	$('#loader').fadeOut();
    	// $('#ventanaInformar .modal-body p').html("Existe un error con el archivo. Por favor verifique la información suministrada");
     //  $('#ventanaInformar').modal();
     //  $('#ventanaInformar').on('hidden.bs.modal', function (e) {
    	// 	$('.fileinput').fileinput('reset');
     //  });
    }
  });
}

function confirmarCambioEstado(codigoSede, estado){
  $('#codigoACambiar').val(codigoSede);
  $('#estadoACambiar').val(estado);

  if(estado){ textoEstado = 'Activar' } else { textoEstado = 'Inactivar'; }

  $('#ventanaConfirmar .modal-body p').html('¿Esta seguro de <strong>' + textoEstado + '</strong> la Sede?');
  $('#ventanaConfirmar').modal();
}

function revertirEstado(){
  $codigoSede = $('#codigoACambiar').val();
  var estado = $('#inputEstadoSede' + $codigoSede).prop('checked');
  if (estado) {
    $('#inputEstadoSede' + $codigoSede).bootstrapToggle('off');
  } else {
    $('#inputEstadoSede' + $codigoSede).bootstrapToggle('on');
  }
}

function cambiarEstado(){
  $.ajax({
    type: "POST",
    url: "functions/fn_sedes_cambiar_estado.php",
    dataType: 'json',
    data: {
      codigo: $('#codigoACambiar').val(),
      estado: $('#estadoACambiar').val()
    },
    beforeSend: function(){ $('#loader').fadeIn(); },
    success: function(data){
      if(data.estado == 1){
        Command: toastr.success(
          data.mensaje, 
          "Cambio de estado", { onHidden : function(){ $('#loader').fadeOut(); } }
        );
      } else {
        Command: toastr.error(
          data.mensaje, 
          "Error al cambiar estado", { onHidden : function(){ $('#loader').fadeOut(); } }
        );
      }
    },
    error: function(data){console.log(data);
      Command: toastr.error(
        "Al parecer existe un error con el servidor. Por favor comuníquese con el adminstrador del sitio InfoPAE.", 
        "Error al cambiar estado", 
        { onHidden : function(){ $('#loader').fadeOut(); } }
      );
    }
  });
}

function editarSede(codigoSede, nombreSede){
  $('#formEditarSede #codigoSede').val(codigoSede);
  $('#formEditarSede #nombreSede').val(nombreSede);
  $('#formEditarSede').submit();
}