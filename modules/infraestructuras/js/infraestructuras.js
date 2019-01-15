$(document).ready(function(){

  $('input').iCheck({
     radioClass: 'iradio_square-green'
  });

 // var heights = $(".col-sm-3").map(function() {
 //        return $(this).height();
 //    }).get(),

 //    maxHeight = Math.max.apply(null, heights);

 //    $(".col-sm-3").height(maxHeight);

  jQuery.extend(jQuery.validator.messages, {//Configuración jquery valid
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

});

$('#cod_municipio').on('change', function(){
  $.ajax({
    type: "POST",
    url: "functions/fn_infraestructuras_obtener_instituciones.php",
    data: {"cod_municipio" : $(this).val()},
    beforeSend: function(){},
    success: function(data){
      $('#cod_inst').html(data);
    }
  });
});

$('#cod_inst').on('change', function(){
  $.ajax({
    type: "POST",
    url: "functions/fn_infraestructuras_obtener_sedes.php",
    data: {"cod_inst" : $(this).val()},
    beforeSend: function(){},
    success: function(data){
      $('#cod_sede').html(data);
    }
  });
});

$('#cod_sede').on('change', function(){
  var nom_sede = $('#cod_sede option:selected').text();
  $('#nom_sede').val(nom_sede);
});

var longMaxObservaciones  = 500;
$('#observaciones').on('keyup', function(event){
  var longObservaciones = $(this).val().length;
    diff = longMaxObservaciones - $(this).val().length;
  $('#maxLongObservaciones').html('('+diff+')');
});

$('#modalEliminarInfraestructura').on('show.bs.modal', function (event) {
  var button = $(event.relatedTarget);
      idinfraestructura = button.data('idinfraestructura');
      $('#idinfraestructura').val(idinfraestructura);
});

function eliminarInfraestructura(){
  $('#modalEliminarInfraestructura').modal('hide');
  $('#loader').fadeIn();
  var infraestructura = $('#idinfraestructura').val();
  $.ajax({
    type: "POST",
    url: "functions/fn_infraestructuras_eliminar_infraestructura.php",
    data: {"idinfraestructura" : infraestructura},
    beforeSend: function(){},
    success: function(data){
      if (data == "1") {
        Command: toastr.success("Eliminado con éxito.", "Eliminado", {onHidden : function(){location.href = 'index.php';}})
      } else if (data == "0") {
        Command: toastr.error("Error al eliminar.", "Error", {onHidden : function(){}})
      }
    }
  });
}

function editarInfraestructura(idinfraestructura){
  $('#idinfraestructuraeditar').val(idinfraestructura);
  $('#editar_infraestructura').submit();
}

$('#tablaInfraestructuras tbody td:nth-child(-n+9)').on('click', function(){
    $('#idinfraestructuraver').val($(this).parent().attr("idinfraestructura"));
    $('#ver_infraestructura').submit();
  });

function validForm(idForm, PanelOcultar, PanelMostrar){

    if (PanelOcultar != 0) {
        if ($('#'+idForm).valid()) {
          $('#btnEditar_'+PanelOcultar).css('display', '');
          $('#'+PanelOcultar).collapse('hide');
          $('#'+PanelMostrar).collapse('show');
        }
        var paneles = $('.panel-collapse').length;
        if (PanelOcultar == paneles-1) {
          $('#segundoBtnSubmit').css('display', '')
        }
    } else {
      idForm = $('#ultimoFormulario').val();
      console.log(idForm);
      if ($('#'+idForm).valid()) {
        $('#loader').fadeIn();
        var datos = "accion=guardar";
        $('form').each(function(){
          datos = datos +"&"+$(this).serialize();
        });
        $.ajax({
          type: "POST",
          url: "functions/fn_infraestructuras_ingresar_infraestructura.php",
          data: datos,
          beforeSend: function(){},
          success: function(data){
            data = JSON.parse(data);
            if (data.respuesta[0].exitoso == "1") {
              Command: toastr.success("Se creó con éxito.", "Creado", {onHidden : function(){location.reload();}})
            } else if (data.respuesta[0].exitoso == "0") {
              Command: toastr.error(data.respuesta[0].respuesta, "Error", {onHidden : function(){$('#loader').fadeOut();}})
            }
          }
        });
      }
    }
}

function validFormEdit(idForm, PanelOcultar, PanelMostrar){

    if (PanelOcultar != 0) {
        if ($('#'+idForm).valid()) {
          $('#btnEditar_'+PanelOcultar).css('display', '');
          $('#'+PanelOcultar).collapse('hide');
          $('#'+PanelMostrar).collapse('show');
        }
        var paneles = $('.panel-collapse').length;
        if (PanelOcultar == paneles-1) {
          $('#segundoBtnSubmit').css('display', '')
        }
    } else {
      idForm = $('#ultimoFormulario').val();
      if ($('#'+idForm).valid()) {
        $('#loader').fadeIn();
        var datos = "accion=guardar";
        $('form').each(function(){
          datos = datos +"&"+$(this).serialize();
        });
        $.ajax({
          type: "POST",
          url: "functions/fn_infraestructuras_editar_infraestructura.php",
          data: datos,
          beforeSend: function(){},
          success: function(data){
            console.log(data);
            data = JSON.parse(data);
            if (data.respuesta[0].exitoso == "1") {
              Command: toastr.success("Se actualizó con éxito.", "Actualizado", {onHidden : function(){
                $('#idinfraestructuraver').val($('#idinfraestructura1').val());
                $('#ver_infraestructura').submit();
              }})
            } else if (data.respuesta[0].exitoso == "0") {
              Command: toastr.error(data.respuesta[0].respuesta, "Error", {onHidden : function(){$('#loader').fadeOut();}})
            }
          }
        });
      }
    }
}

