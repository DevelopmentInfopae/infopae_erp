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

var longMaxObservaciones  = 500;
$('#observaciones').on('keyup', function(event){
  var longObservaciones = $(this).val().length;
    diff = longMaxObservaciones - $(this).val().length;
  $('#maxLongObservaciones').html('('+diff+')');
});

function validForm(idForm, PanelOcultar, PanelMostrar){
  if ($('#'+idForm).valid()) {
    if (PanelOcultar != 0) {
      $('#btnEditar_'+PanelOcultar).css('display', '');
      $('#'+PanelOcultar).collapse('hide');
      $('#'+PanelMostrar).collapse('show');
    } else {
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
            Command: toastr.error(data.respuesta[0].respuesta, "Error", {onHidden : function(){$('#loader').fadeOut();;}})
          }
        }
      });
    }
  }
}


function exportarInfraestructuras(){
  window.location.
}
