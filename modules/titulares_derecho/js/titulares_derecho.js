$(document).ready(function(){

  $(document).on('change', '#mes_exportar', function(){ buscarSemanasMesExportar($(this)); });
  $(document).on('click', '#boton_abri_ventana_exportar_focalizacion', function(){ abrir_ventana_exportar_focalizacion(); });
  $(document).on('click', '#exportar_focalizacion', function(){ exportar_focalizacion(); });

  $('#loader').fadeOut();
   var heights = $(".col-sm-3").map(function() {
        return $(this).height();
    }).get(),
    maxHeight = Math.max.apply(null, heights);
    $(".col-sm-3").height(maxHeight);

  jQuery.extend(jQuery.validator.messages, {//Configuración jquery valid
    step : "Por favor, escribe un número entero",
    required: "Este campo es obligatorio.",
    remote: "Por favor, rellena este campo.",
    email: "Por favor, escribe una dirección de correo válida",
    url: "Por favor, escribe una URL válida.",
    date: "Por favor, escribe una fecha válida.",
    dateISO: "Por favor, escribe una fecha (ISO) válida.",
    number: "Por favor, escribe un número entero válido.",
    digits: "Por favor, escribe sólo dígitos.",
    creditcard: "Por favor, escribe un número de tarjeta válido.",
    equalTo: "Por favor, escribe el mismo valor de nuevo.",
    accept: "Por favor, escribe un valor con una extensión aceptada.",
    maxlength: jQuery.validator.format("Por favor, no escribas más de {0} caracteres."),
    minlength: jQuery.validator.format("Por favor, no escribas menos de {0} caracteres."),
    rangelength: jQuery.validator.format("Por favor, escribe un valor entre {0} y {1} caracteres."),
    range: jQuery.validator.format("Por favor, escribe un valor entre {0} y {1}."),
    max: jQuery.validator.format("Por favor, escribe un valor menor o igual a {0}."),
    min: jQuery.validator.format("Por favor, escribe un valor mayor o igual a {0}.")
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

    // console.log($('#estado'));
});


var numSemana = $('.semana').length;
var numSemanaInicial = $('.semana').length;

$('#tablaTitulares tbody td:nth-child(-n+9)').on('click', function(){
  var aux = $(this).parent().attr('numDoc');
  $('#verTitular #numDoc').val(aux);
  aux = $(this).parent().attr('tipoDoc');
  $('#verTitular #tipoDoc').val(aux);
  $('#verTitular').submit();
});

function confirmarCambioEstado(num_doc, estado, semana){
  console.log(num_doc, estado, semana);
  if(estado){ textoEstado = 'Inactivar'; } else { textoEstado = 'Activar'; }
  $('#ventanaConfirmar .modal-body p').html('¿Esta seguro de <strong>' + textoEstado + '</strong> del titular?');
  $('#ventanaConfirmar').modal();
  $('#documentoACambiar').val(num_doc);
  $('#estadoACambiar').val(estado);
  $('#semanaDeCambio').val(semana);
}

function cambiarEstado(){
  $.ajax({
    url: 'functions/fn_titulares_derecho_cambio_estado.php',
    type: 'POST',
    data: {documento : $('#documentoACambiar').val(), 
          estado : $('#estadoACambiar').val(), 
          semana : $('#semanaDeCambio').val()},
    dataType: 'json',
    beforeSend: function(){ $('#loader').fadeIn(); }
  })
  .done(function(data) {
    if (data.estado == 1) {
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
  })
  .fail(function() {
    console.log("error");
  })
  .always(function() {
    // console.log("complete");
  });
  
}

function revertirEstado(){
  $documento = $('#documentoACambiar').val();
  var estado = $('#inputEstado' + $documento).prop('checked');
  if (estado) {
    $('#inputEstado' + $documento).bootstrapToggle('off');
  } else {
    $('#inputEstado' + $documento).bootstrapToggle('on');
  }
}

function obtenerSedes(institucion){
  $.ajax({
    type: "POST",
    url: "functions/fn_titulares_derecho_obtener_sedes.php",
    data: {"cod_inst" : $(institucion).val()},
    beforeSend: function(){},
    success: function(data){
      $('#cod_sede').html(data);
    }
  });
}

function agregarSemana(){
  var mes = ($('#mes').val());
  if (mes == 0) {
    alert('Por favor selecciona un mes');
    $('#mes').focus();
  }else {
    $('#mes').prop('disabled', 'disabled');
      numSemana++;
    $.ajax({
      type: "POST",
      url: "functions/fn_titulares_derecho_armar_complemento_semana.php",
      data: {"numSemana" : numSemana, "mes" : mes},
      beforeSend: function(){  $('#loader').fadeIn(); },
      success: function(data){
        $('#semanasComplemento').append(data);
        $('#loader').fadeOut();
      }
    });
  }

}

function eliminarSemana(){
  if (numSemana > numSemanaInicial) {
    $('#semana_'+numSemana).remove();
    numSemana--;
  }
}

function validaCompSemana(select, num){
  valida = 0;
  if (num == 2) {
    indexselect = $('.tipo_complemento').index($(select));
    complemento = $(select).val();
    semana = $('.semana').eq(indexselect).val();
    $('.tipo_complemento').each(function(index, val){
      if (complemento == $(val).val() && semana == $('.semana').eq(index).val() && index != indexselect) {
        $(select).val('Seleccione...').focus();
         Command: toastr.warning("No puede elegir el mismo complemento para la misma semana.", "¡Atención!", {onHidden : function(){}})
      } else {

        num_doc = $('#num_doc').val();
        cod_sede = $('#cod_sede').val();

        $.ajax({
        type: "POST",
        url: "functions/fn_titulares_derecho_valida_complemento_semana.php",
        data: {"tipo_complemento" : complemento, "semana" : semana, "num_doc" : num_doc, "cod_sede" : cod_sede},
        beforeSend: function(){},
        success: function(data){
          if (data == "1") {
            $(select).val('Seleccione...').focus();
             Command: toastr.warning("Ya se registró el estudiante en la misma semana y con el mismo complemento.", "¡Atención!", {onHidden : function(){}})
          }
        }
        });
      }
    });
  } else if (num == 1){
    indexselect = $('.semana').index($(select));
    semana = $(select).val();
    complemento = $('.tipo_complemento').eq(indexselect).val();
    $('.semana').each(function(index, val){
      if (semana == $(val).val() && complemento == $('.tipo_complemento').eq(index).val() && index != indexselect) {
        $(select).val('Seleccione...').focus();
         Command: toastr.warning("No puede elegir el mismo complemento para la misma semana.", "¡Atención!", {onHidden : function(){}})
      } else {

        num_doc = $('#num_doc').val();
        cod_sede = $('#cod_sede').val();

        $.ajax({
        type: "POST",
        url: "functions/fn_titulares_derecho_valida_complemento_semana.php",
        data: {"tipo_complemento" : complemento, "semana" : semana, "num_doc" : num_doc, "cod_sede" : cod_sede},
        beforeSend: function(){},
        success: function(data){
          if (data == "1") {
            $(select).val('Seleccione...').focus();
             Command: toastr.warning("Ya se registró el estudiante en la misma semana y con el mismo complemento.", "¡Atención!", {onHidden : function(){}})
          }
        }
        });
      }
    });
  }
}

$('#formTitular').on('submit', function(event){
  $('#loader').fadeIn();

  var datos = $('#formTitular').serialize();

  $.ajax({
      type: "POST",
      url: "functions/fn_titulares_derecho_ingresar_titular.php",
      data: datos,
      beforeSend: function(){},
      success: function(data){
        data = JSON.parse(data);
        console.log(data);
        if (data.estado == "1") {
         Command: toastr.success("Se guardó el estudiante con éxito.", "Guardado con éxito.", {onHidden : function(){location.reload();}})
        }else if (data.estado == "0") {
          Command: toastr.error("El numero de documento ya se encuentra registrado la semana " + data.semana + " ", "Error.", {onHidden : function(){location.reload();}})
        }
      }
    });
event.preventDefault();
});

$('#formTitularEditar').on('submit', function(event){
  $('#loader').fadeIn();

  var datos = $('#formTitularEditar').serialize();

  $.ajax({
      type: "POST",
      url: "functions/fn_titulares_derecho_editar_titular.php",
      data: datos,
      beforeSend: function(){},
      success: function(data){
        if (data == "1") {
         Command: toastr.success("No Se actualizó el estudiante con éxito.", "Actualizado con éxito.", {onHidden : function(){location.href="index.php";}})
        } else {
          console.log(data);
        }
      }
    });
event.preventDefault();
});

function validaNumDoc(input){
  num_doc = $(input).val();
  $.ajax({
      type: "POST",
      url: "functions/fn_titulares_derecho_valida_num_doc.php",
      data: {"num_doc" : num_doc},
      beforeSend: function(){},
      success: function(data){
        data = JSON.parse(data);
        if (data.respuesta[0].respuesta == "1") {
          $('#errorEst').css('display', '');
          $('#semanasErr').html(data.respuesta[0].semanas);
        } else if (data.respuesta[0].respuesta == "0") {
          $('#errorEst').css('display', 'none');
        } else {
        }
      }
    });
}

function editarTitular(num_doc){
  $('#num_doc_editar').val(num_doc);
  $('#editar_titular').submit();
}



function eliminarTitular(){
  $('#modalEliminarTitular').modal('hide');
  $('#loader').fadeIn();
  var idtitular = $('#idtitular').val();
  $.ajax({
    type: "POST",
    url: "functions/fn_titulares_derecho_eliminar_titular.php",
    data: {"num_doc" : idtitular},
    beforeSend: function(){},
    success: function(data){
      if (data == "1") {
        Command: toastr.success("Desactivado con éxito.", "Desactivado", {onHidden : function(){$('#loader').fadeOut();}})
      } else if (data == "0") {
        console.log(data);
        Command: toastr.error("Error al desactivar.", "Error", {onHidden : function(){}})
      } else {
        console.log(data);
      }
    }
  });

}


$('#semana').on('change', function(){
  $('#loader').fadeIn();
  $.ajax({
    type: "POST",
    url: "functions/fn_titulares_derecho_obtener_municipios.php",
    data: {"semana" : $(this).val()},
    beforeSend: function(){},
    success: function(data){
      $('#municipio_titular').html(data);
      $('#loader').fadeOut();
    }
  })
});

$('#municipio_titular').on('change', function(){
  $('#loader').fadeIn();
  $.ajax({
    type: "POST",
    url: "functions/fn_titulares_derecho_obtener_instituciones.php",
    data: {"municipio" : $(this).val()},
    beforeSend: function(){},
    success: function(data){
      $('#institucion_titular').html(data);
      $('#loader').fadeOut();
    }
  })
});

$('#institucion_titular').on('change', function(){
  $('#loader').fadeIn();
  $.ajax({
    type: "POST",
    url: "functions/fn_titulares_derecho_obtener_sedes.php",
    data: {"cod_inst" : $(this).val()},
    beforeSend: function(){},
    success: function(data){
      $('#sede_titular').html(data);
      $('#loader').fadeOut();
    }
  })
});

function abrir_ventana_exportar_focalizacion(){
  $('#ventana_formulario_exportar_focalizacion').modal();
}

function exportar_focalizacion(){
  if ($('#formulario_exportar_focalizacion').valid()) {
    var mes = $('#mes_exportar').val();
    var semana = $('#semana_exportar').val();
    var zona = $('#zona_exportar').val();
    window.open('functions/fn_titulares_derecho_exportar_focalizacion.php?mes='+mes+'&semana='+semana+'&zona='+zona, '_blank');

  }
}

function buscarSemanasMesExportar(control){
  $.ajax({
    type: "post",
    url: "functions/fn_titulares_derecho_buscar_semana_mes.php",
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

function exportarTitular(numDoc, semana){
  // console.log(numDoc, semana);
  $('#exportar_titular #num_doc_exportar').val(numDoc);
  $('#exportar_titular #semana').val(semana);
  $('#exportar_titular').submit();
}

function validaDocAcudiente(){
  documentoTitular = $('#num_doc').val();
  documentoAcudiente = $('#doc_acudiente').val();
  if (documentoTitular == documentoAcudiente) {
    alert('El documento del acudiente no puede ser igual a documento de titular');
    $('#doc_acudiente').val('');
    $('#doc_acudiente').focus();
  }
}

function validaCaracteres(){  
    var docAcudiente = $('#doc_acudiente').val();
    docAcudiente2 = docAcudiente.replace(/[^A-Z a-z 0-9]/gi, '');
    $('#doc_acudiente').val(docAcudiente2);
}