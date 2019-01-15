$(document).ready(function(){
	$('#editarInstitucion').click(function (){ editarInstitucion($(this)) });

  $('.dataTablesSedes tbody td:nth-child(-n+2)').click(function(){
  	$('#formVerSede #codSede').val($(this).closest('tr').attr('codsede'));
  	$('#formVerSede #nomSede').val($(this).closest('tr').attr('nomsede'));
  	$('#formVerSede').submit();
  });

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


function editarInstitucion(control){
	codigoInstitucion = control.data('codigoinstitucion');
	$('#formEditarInstitucion #codigoInstitucion').val(codigoInstitucion);
	$('#formEditarInstitucion').submit();
}

function confirmarCambioEstado(codigoInstitucion, estado){
  $('#codigoACambiar').val(codigoInstitucion);
  $('#estadoACambiar').val(estado);

  if(estado){ textoEstado = 'Activar' } else { textoEstado = 'Inactivar'; }

  $('#ventanaConfirmar .modal-body p').html('¿Esta seguro de <strong>' + textoEstado + '</strong> la Institución?');
  $('#ventanaConfirmar').modal();
}

function cambiarEstado(){
  $.ajax({
    type: "POST",
    url: "functions/fn_instituciones_cambiar_estado.php",
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
        "Error al cambiar estado", { onHidden : function(){ $('#loader').fadeOut(); } }
      );
    }
  });
}

function revertirEstado(){
  $codigoInstitucion = $('#codigoACambiar').val();
  var estado = $('#inputEstadoIntitucion' + $codigoInstitucion).prop('checked');
  if (estado) {
    $('#inputEstadoIntitucion' + $codigoInstitucion).bootstrapToggle('off');
  } else {
    $('#inputEstadoIntitucion' + $codigoInstitucion).bootstrapToggle('on');
  }
}