$(document).ready(function(){
  $('#editarInstitucion').click(function (){ editarInstitucion($(this)) });
  $(document).on('click', '#crearSedeInstitucion', function(){ crearSede($(this)); });
  $(document).on('click', '.editarSede', function(){ editarSede($(this)); });
  $(document).on('click', '.verDispositivos', function(){ verDispositivos($(this)); });
  $(document).on('click', '.verDispositivosSede', function(){ verDispositivosSede($(this)); });
  $(document).on('click', '.verInfraestructura', function(){ verInfraestructura($(this)); });
  $(document).on('click', '.verInfraestructuraSede', function(){ verInfraestructurasSede($(this)); });
  $(document).on('click', '.verTitulares', function(){ verTitulares($(this)); });
  $(document).on('click', '.verTitularesSede', function(){ verTitularesSede($(this)); });

  $('.dataTablesSedes tbody td:nth-child(-n+5)').click(function(){
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

function crearSede(control){
  codigoInstitucion = control.data('codigointitucion');
  $('#formCrearSede #codigoInstitucion').val(codigoInstitucion);
  $('#formCrearSede').submit();
}

function editarSede(control){
  codigoSede = control.data('codigosede');
  $('#formEditarSede #codigoSede').val(codigoSede);
  $('#formEditarSede').submit();
}

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

// SEDES
function confirmarCambioEstadoSede(codigoSede, estado){
  $('#codigoACambiar').val(codigoSede);
  $('#estadoACambiar').val(estado);

  if(estado){ textoEstado = 'Activar' } else { textoEstado = 'Inactivar'; }

  $('#ventanaConfirmarSede .modal-body p').html('¿Esta seguro de <strong>' + textoEstado + '</strong> la Sede?');
  $('#ventanaConfirmarSede').modal();
}

function revertirEstadoSede(){
  $codigoSede = $('#codigoACambiar').val();
  var estado = $('#inputEstadoSede' + $codigoSede).prop('checked');
  if (estado) {
    $('#inputEstadoSede' + $codigoSede).bootstrapToggle('off');
  } else {
    $('#inputEstadoSede' + $codigoSede).bootstrapToggle('on');
  }
}

function cambiarEstadoSede(){
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

function verDispositivos(control){
  codigoInstitucion = control.data('codigoinstitucion');
  $('#formDispositivosSede #cod_inst').val(codigoInstitucion);
  $('#formDispositivosSede').submit();
}

function verDispositivosSede(control){
  codigoSede = control.data('codigosede');
  $('#formDispositivosSede #cod_sede').val(codigoSede);
  $('#formDispositivosSede').submit();
}

function verInfraestructura(control){
  codigoSede = control.data('codigoinstitucion');
  $('#formInfraestructura #cod_inst').val(codigoSede);
  $('#formInfraestructura').submit();
}

function verInfraestructurasSede(control){
  codigoSede = control.data('codigosede');
  $('#formInfraestructuraSede #cod_sede').val(codigoSede);
  $('#formInfraestructuraSede').submit();
}

function verTitulares(control){
  codigoInstitucion = control.data('codigoinstitucion');
  $('#formTitulares #cod_inst').val(codigoInstitucion);
  $('#formTitulares').submit();
}

function verTitularesSede(control){
  codigoSede = control.data('codigosede');
  $('#formTitulares #cod_sede').val(codigoSede);
  $('#formTitulares').submit();
}