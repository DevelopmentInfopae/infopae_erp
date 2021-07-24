$(document).ready(function(){
	$('#municipio').change(function(){ buscarInstitucion($(this).val()); });
	$('#jornada').change(function(){ buscarTipoComplemento($(this).val()); });
  $(document).on('click', '#editarSede', function(){ editarSede($(this)); });
  $(document).on('click', '.verDispositivosSede', function(){ verDispositivosSede($(this)); });
  $(document).on('click', '.verInfraestructuraSede', function(){ verInfraestructurasSede($(this)); });
  $(document).on('click', '.verTitularesSede', function(){ verTitularesSede($(this)); });

    //Configuración de los radio button
  $('input').iCheck({
    checkboxClass: 'icheckbox_square',
    radioClass: "iradio_square-green"
  });

	$('.titular').click(function(){
		var aux = $(this).attr('numDoc');
		$('#verTitular #numDoc').val(aux);
		aux = $(this).attr('tipoDoc');
		$('#verTitular #tipoDoc').val(aux);
		aux = $(this).attr('semana');
		$('#verTitular #semana').val(aux);
		$('#verTitular').submit();
	});

	// Configuración del plugin toastr
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

    jQuery.extend(jQuery.validator.messages, {step:"Por favor ingresa un número entero", required: "Este campo es obligatorio.", remote: "Por favor, rellena este campo.", 
      email: "Por favor, escribe una dirección de correo válida", url: "Por favor, escribe una URL válida.", 
      date: "Por favor, escribe una fecha válida.", dateISO: "Por favor, escribe una fecha (ISO) válida.", 
      number: "Por favor, escribe un número entero válido.", digits: "Por favor, escribe sólo dígitos.", 
      creditcard: "Por favor, escribe un número de tarjeta válido.", equalTo: "Por favor, escribe el mismo valor de nuevo.", 
      accept: "Por favor, escribe un valor con una extensión aceptada.", 
      maxlength: jQuery.validator.format("Por favor, no escribas más de {0} caracteres."), 
      minlength: jQuery.validator.format("Por favor, no escribas menos de {0} caracteres."), 
      rangelength: jQuery.validator.format("Por favor, escribe un valor entre {0} y {1} caracteres."), 
      range: jQuery.validator.format("Por favor, escribe un valor entre {0} y {1}."), 
      max: jQuery.validator.format("Por favor, escribe un valor menor o igual a {0}."), 
      min: jQuery.validator.format("Por favor, escribe un valor mayor o igual a {0}.") });

  var municipio = $('#municipio').val();
  buscarInstitucion(municipio);
});

function editarSede(control){
  var codigoSede = control.data('codigosede');
  $('#formEditarSede #codigoSede').val(codigoSede);
  $('#formEditarSede').submit();
}

function buscarInstitucion(municipio){
	$.ajax({
	  type: "post",
	  url: "functions/fn_buscar_instituciones.php",
	  data: {"municipio":municipio, "modulo": "sede"},
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

function buscarTipoComplemento(jornada){
	$.ajax({
	  type: "post",
	  url: "functions/fn_sede_buscar_tipo_complemento.php",
	  data: {"jornada":jornada},
	  beforeSend: function(){ $('#loader').fadeIn(); },
	  success: function(data){
	  	$('#loader').fadeOut();
	    $('#complemento').html(data);
	  },
	  error: function(data){
	  	$('#loader').fadeOut();
	  	console.log(data);
	  }
	});
}

function guardarSede(continuar){
  if($('#formCrearSede').valid()){
  	var formData = new FormData();
    formData.append('zonaPae', $('#zonaPae').val());
    formData.append('email', $('#email').val());
    formData.append('codigo', $('#codigo').val());
    formData.append('nombre', $('#nombre').val());
    formData.append('jornada', $('#jornada').val());
    formData.append('telefono', $('#telefono').val());
    formData.append('municipio', $('#municipio').val());
    formData.append('direccion', $('#direccion').val());
    formData.append('variacion', $('#variacion').val());
    formData.append('imagen', $('#imagen')[0].files[0]);
    formData.append('coordinador', $('#coordinador').val());
    formData.append('institucion', $('#institucion').val());
    formData.append('complemento', $('#complemento').val());
    formData.append('manipuladora', $('#manipuladora').val());
    formData.append('sector', $('input[name=sector]:checked').val());
    formData.append('validacion', $('input[name=validacion]:checked').val());
  	formData.append('nombreInstitucion', $('#institucion option:selected').text());

    $.ajax({
      type: "POST",
      url: "functions/fn_sede_crear.php",
      contentType: false,
      processData: false,
      data: formData,
      dataType: 'json',
      beforeSend: function(){ $('#loader').fadeIn(); },
      success: function(data){
        if(data.estado == 1){
          Command: toastr.success(
            data.mensaje,
            "Creado", {
              onHidden : function(){
                if(continuar){
                  $('#loader').fadeOut();
                  $("#formCrearSede")[0].reset();
                }else{
                  window.open('sedes.php', '_self');
                }
              }
            }
          );
        } else {
          Command: toastr.error(
            data.mensaje,
            "Error al crear", { onHidden : function(){ $('#loader').fadeOut(); } }
          );
        }
      },
      error: function(data){ console.log(data);
         Command: toastr.error(
            "Al parecer existe un error al ejecutar el proceso. Por favor contacte con el administrador del InfoPAE.",
            "Error al crear", { onHidden : function(){ $('#loader').fadeOut(); } }
          );
      }
    });
  }
}

function actualizarSede(continuar){
  if($('#formActualizarSede').valid()){
    var formData = new FormData();
    formData.append('id', $('#id').val());
    formData.append('zonaPae', $('#zonaPae').val());
    formData.append('email', $('#email').val());
    formData.append('codigo', $('#codigo').val());
    formData.append('nombre', $('#nombre').val());
    formData.append('jornada', $('#jornada').val());
    formData.append('telefono', $('#telefono').val());
    formData.append('municipio', $('#municipio').val());
    formData.append('direccion', $('#direccion').val());
    formData.append('variacion', $('#variacion').val());
    formData.append('imagen', $('#imagen')[0].files[0]);
    formData.append('institucion', $('.institucion').val());
    formData.append('coordinador', $('#coordinador').val());
    formData.append('complemento', $('#complemento').val());
    formData.append('manipuladora', $('#manipuladora').val());
    formData.append('sector', $('input[name=sector]:checked').val());
    formData.append('estado', $('input[name=estado]:checked').val());
    formData.append('validacion', $('input[name=validacion]:checked').val());
    formData.append('nombreInstitucion', $('#institucion option:selected').text());

    $.ajax({
      type: "POST",
      url: "functions/fn_sede_actualizar.php",
      contentType: false,
      processData: false,
      data: formData,
      dataType: 'json',
      beforeSend: function(){ $('#loader').fadeIn(); },
      success: function(data){
        if(data.estado == 1){
          Command: toastr.success(
            data.mensaje,
            "Actualizado", { onHidden : function(){ $('#loader').fadeOut(); } }
          );
        } else {
          Command: toastr.error(
            data.mensaje,
            "Error al actualizar", { onHidden : function(){ $('#loader').fadeOut(); } }
          );
        }
      },
      error: function(data){ console.log(data);
         Command: toastr.error(
            "Al parecer existe un error al ejecutar el proceso. Por favor contacte con el administrador del InfoPAE.",
            "Error al actualizar", { onHidden : function(){ $('#loader').fadeOut(); } }
          );
      }
    });
  }
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

function verDispositivosSede(control){
  codigoSede = control.data('codigosede');
  $('#formDispositivosSede #cod_sede').val(codigoSede);
  $('#formDispositivosSede').submit();
}

function verInfraestructurasSede(control){
  codigoSede = control.data('codigosede');
  $('#formInfraestructuraSede #cod_sede').val(codigoSede);
  $('#formInfraestructuraSede').submit();
}

function verTitularesSede(control){
  codigoSede = control.data('codigosede');
  $('#formTitularesSede #cod_sede').val(codigoSede);
  $('#formTitularesSede').submit();
}