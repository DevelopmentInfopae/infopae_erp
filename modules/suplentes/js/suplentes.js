$(document).ready(function(){
    $('#loader').fadeOut();
    var heights = $(".col-sm-3").map(function() {
        return $(this).height();
    }).get(),
    maxHeight = Math.max.apply(null, heights);
    $(".col-sm-3").height(maxHeight);

    jQuery.extend(jQuery.validator.messages, {
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

    $('#tablaSuplentes tbody td:nth-child(-n+8)').on('click', function() { ver_suplente($(this).parent().attr('numDoc'), $(this).parent().attr('tipoDoc')); });
    $(document).on('change', '#tipo_doc', function () { $('#abreviatura').val($("#tipo_doc option:selected").data('abreviatura')); });
});

function ver_suplente(numDoc, tipoDoc) {
    $('#verSuplente #numDoc').val(numDoc);
    $('#verSuplente #tipoDoc').val(tipoDoc);
    $('#verSuplente').submit();
}

function editarTitular(num_doc) {
  $('#numDoc').val(num_doc);
  $('#editar_suplente').submit();
}

function obtenerInstituciones(cod_mun) {
    $.ajax({
    type: "POST",
    url: "functions/fn_suplentes_obtener_instituciones.php",
    data: {"cod_mun" : cod_mun},
    beforeSend: function(){},
    success: function(data){
        $('#cod_inst').html(data);
        $('#cod_sede').html('<option valule="">Seleccione...</option>');

        $('#nom_inst').val('');
        $('#nom_sede').val('');
    }
  });
}

function obtenerSedes(institucion){

    $.ajax({
        type: "POST",
        url: "functions/fn_suplentes_obtener_sedes.php",
        data: {"cod_inst" : $(institucion).val()},
        beforeSend: function(){},
        success: function(data){
            $('#cod_sede').html(data);

            $('#nom_inst').val($('#cod_inst option:selected').text());
            $('#nom_sede').val('');
        }
    });
}

function obtenerNombreSede() {
    $('#nom_sede').val($('#cod_sede option:selected').text());
}

// function agregarSemana(){
//   $('#loader').fadeIn();
//   numSemana++;
//   $.ajax({
//       type: "POST",
//       url: "functions/fn_titulares_derecho_armar_complemento_semana.php",
//       data: {"numSemana":numSemana},
//       beforeSend: function(){},
//       success: function(data){
//         $('#semanasComplemento').append(data);
//         $('#loader').fadeOut();
//       }
//     });
// }

// function eliminarSemana(){
//   if (numSemana > numSemanaInicial) {
//     $('#semana_'+numSemana).remove();
//     numSemana--;
//   }
// }

// function validaCompSemana(select, num){
//   valida = 0;
//   if (num == 2) {
//     indexselect = $('.tipo_complemento').index($(select));
//     complemento = $(select).val();
//     semana = $('.semana').eq(indexselect).val();
//     $('.tipo_complemento').each(function(index, val){
//       if (complemento == $(val).val() && semana == $('.semana').eq(index).val() && index != indexselect) {
//         $(select).val('Seleccione...').focus();
//          Command: toastr.warning("No puede elegir el mismo complemento para la misma semana.", "¡Atención!", {onHidden : function(){}})
//       } else {

//         num_doc = $('#num_doc').val();
//         cod_sede = $('#cod_sede').val();

//         $.ajax({
//         type: "POST",
//         url: "functions/fn_titulares_derecho_valida_complemento_semana.php",
//         data: {"tipo_complemento" : complemento, "semana" : semana, "num_doc" : num_doc, "cod_sede" : cod_sede},
//         beforeSend: function(){},
//         success: function(data){
//           if (data == "1") {
//             $(select).val('Seleccione...').focus();
//              Command: toastr.warning("Ya se registró el estudiante en la misma semana y con el mismo complemento.", "¡Atención!", {onHidden : function(){}})
//           }
//         }
//         });
//       }
//     });
//   } else if (num == 1){
//     indexselect = $('.semana').index($(select));
//     semana = $(select).val();
//     complemento = $('.tipo_complemento').eq(indexselect).val();
//     $('.semana').each(function(index, val){
//       if (semana == $(val).val() && complemento == $('.tipo_complemento').eq(index).val() && index != indexselect) {
//         $(select).val('Seleccione...').focus();
//          Command: toastr.warning("No puede elegir el mismo complemento para la misma semana.", "¡Atención!", {onHidden : function(){}})
//       } else {

//         num_doc = $('#num_doc').val();
//         cod_sede = $('#cod_sede').val();

//         $.ajax({
//         type: "POST",
//         url: "functions/fn_titulares_derecho_valida_complemento_semana.php",
//         data: {"tipo_complemento" : complemento, "semana" : semana, "num_doc" : num_doc, "cod_sede" : cod_sede},
//         beforeSend: function(){},
//         success: function(data){
//           if (data == "1") {
//             $(select).val('Seleccione...').focus();
//              Command: toastr.warning("Ya se registró el estudiante en la misma semana y con el mismo complemento.", "¡Atención!", {onHidden : function(){}})
//           }
//         }
//         });
//       }
//     });
//   }
// }

$('#formNuevoSuplente').on('submit', function(event){
    $('#loader').fadeIn();

    $.ajax({
        type: 'POST',
        url: 'functions/fn_suplentes_ingresar.php',
        data: $('#formNuevoSuplente').serialize(),
        dataType: 'json',
        success: function(data) { console.log(data);
            if (data.success == 1) {
                Command: toastr.success('Se guardó el suplente con éxito.', 'Guardado con éxito.', {onHidden : function() { location.reload(); }});
            } else {
                Command: toastr.error(data.message, 'Proceso cancelado', { onHidden : function() { /*location.reload();*/ } });
            }
        },
        error: function (data) {
            console.log(data);
        }
    });
    event.preventDefault();
});

// $('#formTitularEditar').on('submit', function(event){
//   $('#loader').fadeIn();

//   var datos = $('#formTitularEditar').serialize();

//   $.ajax({
//       type: "POST",
//       url: "functions/fn_titulares_derecho_editar_titular.php",
//       data: datos,
//       beforeSend: function(){},
//       success: function(data){
//         if (data == "1") {
//          Command: toastr.success("Se actualizó el estudiante con éxito.", "Actualizado con éxito.", {onHidden : function(){location.href="index.php";}})
//         } else {
//           console.log(data);
//         }
//       }
//     });
// event.preventDefault();
// });

function validaNumDoc(input){
    num_doc = $(input).val();

    $.ajax({
        type: "POST",
        url: "functions/fn_suplentes_valida_documento.php",
        data: {"num_doc" : num_doc},
        success: function(data) { console.log(data);
            data = JSON.parse(data);
            if (data.respuesta[0].respuesta == "1") {
                $('#errorEst').css('display', '');
                $('#semanasErr').html(data.respuesta[0].semanas);
            } else if (data.respuesta[0].respuesta == "0") {
                $('#errorEst').css('display', 'none');
            }
        }
    });
}


// $(document).on('change', '.dropdown li:nth-child(2)', function(){
//       var idtitular = $(this).data('idtitular');
//       accion = $(this).data('accion');
//       if ($(this).find('input').prop('checked') == false) {
//         $('#idtitular').val(idtitular);
//         $('#modalEliminarTitular').modal();
//       } else if ($(this).find('input').prop('checked')) {
//           $('#loader').fadeIn();
//           $.ajax({
//             type: "POST",
//             url: "functions/fn_titulares_derecho_activar_titular.php",
//             data: {"num_doc" : idtitular},
//             beforeSend: function(){},
//             success: function(data){
//               if (data == "1") {
//                 Command: toastr.success("Se activó con éxito el estudiante.", "Activado con éxito", {onHidden : function(){$('#loader').fadeOut();}})
//               } else if (data == "0") {
//                 console.log(data);
//                 Command: toastr.error("Error al activar.", "Error", {onHidden : function(){}})
//               } else {
//                 console.log(data);
//               }
//             }
//           });
//       }
// });

// function eliminarTitular(){
//   $('#modalEliminarTitular').modal('hide');
//   $('#loader').fadeIn();
//   var idtitular = $('#idtitular').val();
//   $.ajax({
//     type: "POST",
//     url: "functions/fn_titulares_derecho_eliminar_titular.php",
//     data: {"num_doc" : idtitular},
//     beforeSend: function(){},
//     success: function(data){
//       if (data == "1") {
//         Command: toastr.success("Desactivado con éxito.", "Desactivado", {onHidden : function(){$('#loader').fadeOut();}})
//       } else if (data == "0") {
//         console.log(data);
//         Command: toastr.error("Error al desactivar.", "Error", {onHidden : function(){}})
//       } else {
//         console.log(data);
//       }
//     }
//   });

// }


// $('#semana').on('change', function(){
//   $('#loader').fadeIn();
//   $.ajax({
//     type: "POST",
//     url: "functions/fn_titulares_derecho_obtener_municipios.php",
//     data: {"semana" : $(this).val()},
//     beforeSend: function(){},
//     success: function(data){
//       $('#municipio_titular').html(data);
//       $('#loader').fadeOut();
//     }
//   })
// });

// $('#municipio_titular').on('change', function(){
//   $('#loader').fadeIn();
//   $.ajax({
//     type: "POST",
//     url: "functions/fn_titulares_derecho_obtener_instituciones.php",
//     data: {"municipio" : $(this).val()},
//     beforeSend: function(){},
//     success: function(data){
//       $('#institucion_titular').html(data);
//       $('#loader').fadeOut();
//     }
//   })
// });

// $('#institucion_titular').on('change', function(){
//   $('#loader').fadeIn();
//   $.ajax({
//     type: "POST",
//     url: "functions/fn_titulares_derecho_obtener_sedes.php",
//     data: {"cod_inst" : $(this).val()},
//     beforeSend: function(){},
//     success: function(data){
//       $('#sede_titular').html(data);
//       $('#loader').fadeOut();
//     }
//   })
// });