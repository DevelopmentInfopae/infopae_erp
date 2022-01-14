$(document).ready(function(){
  $(document).on('click', '.verDispositivos', function(){ verDispositivos($(this)); });
  $(document).on('click', '.verInfraestructura', function(){ verInfraestructura($(this)); });
  $(document).on('click', '.verTitulares', function(){ verTitulares($(this)); });
  $(document).on('click', '#crearInstitucion', function() { abrir_modal_crear_institucion(); });
  $(document).on('click', '#guardar_institucion', function() { guardarInstitucion(true); });
  $(document).on('click', '.editarInstitucion', function(){ abrir_modal_editar_institucion($(this).data('codigoinstitucion')); });
  $(document).on('click', '#editar_institucion', function(){ actualizarInstitucion(); })


  $('.dataTablesInstituciones').on('click', 'tbody tr td:nth-child(-n+3)', function(){
    var aux = $(this).parent().attr('codinst');
    $('#verInst #codInst').val(aux);
    var aux2 = $(this).parent().attr('nominst');
    $('#verInst #nomInst').val(aux2);
    console.log(aux);
    $('#verInst').submit();
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
 
});

function abrir_modal_crear_institucion(){
  $('#contenedor_crear_institucion').load($('#inputBaseUrl').val() +'/modules/instituciones/instituciones_crear.php');
}

function abrir_modal_editar_institucion(codigoinstitucion){
   $('#contenedor_editar_institucion').load($('#inputBaseUrl').val() +'/modules/instituciones/instituciones_editar.php?codigo='+codigoinstitucion);
}

function guardarInstitucion(continuar){
  if($('#formCrearInstitucion').valid()){
    $.ajax({
      type: "POST",
      url: "functions/fn_instituciones_crear.php",
      data: {
        codigo: $('#codigo').val(),
        nombre: $('#nombre').val(),
        telefono: $('#telefono').val(),
        email: $('#email').val(),
        municipio: $('#municipio').val(),
        rector: $('#rector').val()
      },
      dataType: 'json',
      beforeSend: function(){$('#loader').fadeIn();},
      success: function(data){
        if(data.estado == 1){
          Command: toastr.success(
          data.mensaje,
          "Creado",
                  {
                    onHidden : function(){
                    $('#loader').fadeOut();
                    window.open('instituciones.php', '_self');                                    
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
      error: function(data){            
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

function actualizarInstitucion(){
  if($('#formEditarInstitucion').valid()){
    datos = $('#formEditarInstitucion').serialize();
    $.ajax({
      type: "post",
      url: "functions/fn_instituciones_actualizar.php",
      dataType: 'json',
      data : datos,
      beforeSend: function(){ $('#loader').fadeIn(); },
      success: function(data){
        // console.log(data);
        if(data.estado == 1){
          Command: toastr.success(
            data.mensaje,
            'Actualizado',
            { onHidden : function(){
                $('#loader').fadeOut();
                window.open('instituciones.php', '_self'); } }
          );
        } else {
          Command: toastr.warning(
            data.mensaje,
            "Error al editar",
            { onHidden: function(){ $('#loader').fadeOut(); } }
          );
        }
      },
      error: function(data){
        console.log(data.responseText);
        Command: toastr.error(
          "Al parecer existe un problema con el servidor. Por favor contactese con el administrador del sitio InfoPae.",
          "Error al guardar",
          { onHidden: function(){ $('#loader').fadeOut(); } }
        );
      }
    });
  }
}

function cargarArchivo(){
	var formData = new FormData();
  formData.append('archivo', $('#archivo')[0].files[0]);

  $.ajax({
    type: "POST",
    url: "functions/fn_instituciones_cargar_archivo.php",
    contentType: false,
    processData: false,
    data: formData,
    dataType: 'json',
    beforeSend: function(){ $('#loader').fadeIn(); },
    success: function(data){ 
      console.log(data);
      // data = JSON.parse(data);
      if(data.estado == 1){
        Command: toastr.success(
          data.mensaje,
          "Procesado",
          {
            onHidden : function(){ $('#loader').fadeOut(); window.open($("#inputBaseUrl").val()+"/modules/instituciones/instituciones.php", "_self") }
          }
        );
      } else if(data.estado == 0){
        Command: toastr.error(
          data.mensaje,
          "Error al procesar",
          {
            onHidden : function(){ $('#loader').fadeOut(); $('.fileinput').fileinput('reset'); }
          }
        );
      }
    },
    error: function(data){  
      console.log(data);
      Command: toastr.error(
        "Existe un error con el archivo. Por favor verifique los datos suministrados. Posiblemente los códigos de instituciones se encuentran duplicados.",
        "Error al procesar",
        {
          onHidden : function(){ $('#loader').fadeOut(); $('.fileinput').fileinput('reset'); }
        }
      );
    }
  }).done(function(data){
    // console.log(data);
  });
}

function confirmarCambioEstado(codigoInstitucion, estado){
  $('#codigoACambiar').val(codigoInstitucion);
  $('#estadoACambiar').val(estado);

  if(estado){ textoEstado = 'activar' } else { textoEstado = 'desactivar'; }

  $('#ventanaConfirmar .modal-body p').html('¿Esta seguro de ' + textoEstado + ' la Institución?');
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
    success: function(data){ console.log(data);
      if(data.estado == 1){
        Command: toastr.success(
          data.mensaje,
          "Cambio de estado",
          {
            onHidden : function(){ $('#loader').fadeOut(); }
          }
        );
      } else {
        Command: toastr.error(
          data.mensaje,
          "Error al cambiar estado",
          {
            onHidden : function(){ $('#loader').fadeOut(); }
          }
        );
      }
    },
    error: function(data){console.log(data);
      Command: toastr.error(
        "Al parecer existe un error con el servidor. Por favor comuníquese con el adminstrador del sitio InfoPAE.",
        "Error al cambiar estado",
        {
          onHidden : function(){ $('#loader').fadeOut(); }
        }
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

function verDispositivos(control){
  codigoInstitucion = control.data('codigoinstitucion');
  $('#formDispositivosSede #cod_inst').val(codigoInstitucion);
  $('#formDispositivosSede').submit();
}

function verInfraestructura(control){
  codigoInstitucion = control.data('codigoinstitucion');
  $('#formInfraestructura #cod_inst').val(codigoInstitucion);
  $('#formInfraestructura').submit();
}

function verTitulares(control){
  codigoInstitucion = control.data('codigoinstitucion');
  $('#formTitulares #cod_inst').val(codigoInstitucion);
  $('#formTitulares').submit();
}