$(document).ready(function(){

	$('#municipio').change(function(){ buscarInstitucion($(this).val()); });
	$('#btnBuscar').click(function() { buscarSedes();	});
  $(document).on('click', '#importarPriorizacion', function(){ $('#ventanaFormularioPri').modal(); });
  $(document).on('click', '#importarFocalizacion', function(){ $('#ventanaFormularioFoc').modal(); });
  $(document).on('change', '#mes', function(){ buscarSemanasMes($(this)); });
  $(document).on('change', '#mes_exportar', function(){ buscarSemanasMesExportar($(this)); });
  $(document).on('change', '#mesFocalizacion', function(){ buscarSemanasMesFoc($(this)); });
  $(document).on('click', '#subirArchivoPriorizacion', function(){ subirArchivoPriorizacion(); });
  $(document).on('click', '#subirArchivoFocalizacion', function(){ subirArchivoFocalizacion(); });
  $(document).on('click', '#boton_abri_ventana_exportar_priorizacion', function(){ abrir_ventana_exportar_priorizacion(); });
  $(document).on('click', '#exportar_priorizacion', function(){ exportar_priorizacion(); });
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

function editarSede(codigoSede, nombreSede){
  $('#formEditarSede #codigoSede').val(codigoSede);
  $('#formEditarSede #nombreSede').val(nombreSede);
  $('#formEditarSede').submit();
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
	var formData = new FormData();
  formData.append('archivoSede', $('#archivoSede')[0].files[0]);

  $.ajax({
    type: "POST",
    url: "functions/fn_sedes_cargar_archivo.php",
    contentType: false,
    processData: false,
    data: formData,
    dataType: 'json',
    beforeSend: function(){ $('#loader').fadeIn(); },
    success: function(data){ console.log(data);
     if(data.estado == 1){
      Command: toastr.success(
        data.mensaje,
        "Proceso realizado", { onHidden : function(){ $('#loader').fadeOut(); window.open($("#inputBaseUrl").val()+"/modules/instituciones/sedes.php", "_self"); } }
        );
    } else {
      Command: toastr.error(
        data.mensaje,
        "Error en el proceso", { onHidden : function(){ $('#loader').fadeOut(); } }
        );
    }
  },
  error: function(data){ console.log(data);
   Command: toastr.error(
    data.mensaje,
    "Error en el proceso", { onHidden : function(){ $('#loader').fadeOut(); } }
    );
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

function buscarSemanasMes(control){
  $.ajax({
    type: "post",
    url: "functions/fn_sede_buscar_semana_mes.php",
    data: {"mes": control.val()},
    dataType: 'html',
    success: function(data){
      $('#semana').html(data);
    },
    error: function(data){
      console.log(data.responseText);
    }
  });
}

function buscarSemanasMesExportar(control){
  $.ajax({
    type: "post",
    url: "functions/fn_sede_buscar_semana_mes.php",
    data: {"mes": control.val()},
    dataType: 'html',
    success: function(data){
      $('#semana_exportar').html(data);
    },
    error: function(data){
      console.log(data.responseText);
    }
  });
}

function buscarSemanasMesFoc(control){
  $.ajax({
    type: "post",
    url: "functions/fn_sede_buscar_semana_mes.php",
    data: { "mes": $('#mesFocalizacion').val() },
    dataType: 'html',
    beforeSend: function(){ $('#loader').fadeIn(); },
    success: function(data){
      $('#loader').fadeOut();
      $('#semanaFocalizacion').html(data);
    },
    error: function(data){
      $('#loader').fadeOut();
      console.log(data);
    }
  });
}

function subirArchivoPriorizacion(){
  if($('#frmSubirArchivoPriorizacion').valid()){
    var formData = new FormData();
    formData.append('mes', $('#mes').val());
    formData.append('semana', $('#semana').val());
    formData.append('archivoPriorizacion', $('#archivoPriorizacion')[0].files[0]);

    $.ajax({
      type: "post",
      url: "functions/fn_sedes_cargar_archivo_priorizacion.php",
      contentType: false,
      processData: false,
      data: formData,
      dataType: 'json',
      beforeSend: function(){ $('#loader').fadeIn(); },
      success: function(data){ console.log(data);
        if(data.estado == 1){
          Command: toastr.success(
            data.mensaje,
            "Cambio de estado", { onHidden : function(){ $('#loader').fadeOut(); } }
            );
        } else {
          Command: toastr.error(
            data.mensaje,
            "Error al subir datos", { onHidden : function(){ $('#loader').fadeOut(); } }
            );
        }
      },
      error: function(data){
        $('#loader').fadeOut();
        Command: toastr.error(
          "Al parecer existe un problema en el servidor. Por favor comuníquese con el administrador del sitio InfoPAE.",
          "Error al subir datos", { onHidden : function(){ $('#loader').fadeOut(); console.log(data.responseText); } }
          );
      }
    });
  }
}

function subirArchivoFocalizacion()
{
  if($('#frmSubirArchivoFocalizacion').valid()){
    var formData = new FormData();
    formData.append('mes', $('#mesFocalizacion').val());
    formData.append('semana', $('#semanaFocalizacion').val());
    formData.append('validar', $('#validar').prop('checked'));
    formData.append('archivoFocalizacion', $('#archivoFocalizacion')[0].files[0]);

    $.ajax({
      type: "post",
      url: "functions/fn_sedes_cargar_archivo_focalizacion.php",
      contentType: false,
      processData: false,
      data: formData,
      dataType: 'JSON',
      beforeSend: function(){ $('#loader').fadeIn(); },
      success: function(data){ console.log(data);
        if(data.estado == 1){
          Command: toastr.success(
            data.mensaje,
            "Cambio de estado", {
              onHidden : function() {
                $('#loader').fadeOut();
                if($('#validar').prop('checked'))
                {
                  generarInformeFocalizacion();
                }
              }
            }
            );
        } else {
          Command: toastr.error(
            data.mensaje,
            "Error al subir datos", { onHidden : function(){ $('#loader').fadeOut(); } }
            );
        }
      },
      error: function(data){
        console.log(data.responseText);
        $('#loader').fadeOut();
        Command: toastr.error(
          "Al parecer existe un problema en el servidor. Por favor comuníquese con el administrador del sitio InfoPAE.",
          "Error al subir datos", { onHidden : function(){ $('#loader').fadeOut(); } }
          );
      }
    });
  }
}

function generarInformeFocalizacion()
{
  window.open('functions/fn_sede_generar_informe.php?semana='+$('#semanaFocalizacion').val(), '_blank');
}

function verDispositivosSede(codigoSede){
  $('#formDispositivosSede #cod_sede').val(codigoSede);
  $('#formDispositivosSede').submit();
}

function verInfraestructurasSede(codigoSede){
  $('#formInfraestructuraSede #cod_sede').val(codigoSede);
  $('#formInfraestructuraSede').submit();
}

function verTitularesSede(codigoSede){
  $('#formTitularesSede #cod_sede').val(codigoSede);
  $('#formTitularesSede').submit();
}

function abrir_ventana_exportar_priorizacion(){
  $('#ventana_formulario_exportar_priorizacion').modal();
}

function exportar_priorizacion(){
  if ($('#formulario_exportar_priorizacion').valid()) {
    var mes = $('#mes_exportar').val();
    var semana = $('#semana_exportar').val();

    window.open('functions/fn_sedes_exportar_priorizacion.php?mes='+mes+'&semana='+semana, '_blank');
  }
}
