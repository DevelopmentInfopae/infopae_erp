$(document).ready(function(){});

function crearNovedadPriorizacion(){
  window.open('novedades_priorizacion_crear.php', '_self');
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
