$(document).ready(function(){

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

$('#complemento').on('change', function(){
  if ($(this).val() != "") {
    validaValRef(this);
  }
});

$('#grupoEtario').on('change', function(){
  if ($(this).val() != "") {
    validaValRef(this);
  }
});

function validaValRef(select){
  var complemento = $('#complemento').val();
      grupoEtario = $('#grupoEtario').val();

      $.ajax({
        type: "POST",
        url : "functions/fn_menu_valref_validar_existente.php",
        data : {"complemento" : complemento, "grupoEtario" : grupoEtario },
        success: function(data){
          if (data == "1") {
            Command: toastr.warning("Ya existen valores registrados para el tipo de complemento y grupo etario seleccionado.", "Valores existentes.", {onHidden : function(){}})
            $(select).val("");
          } else if (data == "0") {
            $('#nomGETA').val($('#grupoEtario option:selected').text());
          }
        }
      });
}

var textRespuesta = "";
var titleRespuesta = "";

$('.submitValref').on('click', function(){
  if ($('#formValRef').valid()) {
    titleRespuesta = "Creado con éxito";
    textRespuesta = "Se han registrado con éxito los datos.";
    $('#formValRef').prop('action', 'functions/fn_menu_valref_insertar.php');
    $('#formValRef').submit();
  }
});

$('.submitValrefEditar').on('click', function(){
  if ($('#formValRef').valid()) {
    titleRespuesta = "Actualizado con éxito";
    textRespuesta = "Se han actualizado con éxito los datos.";
    $('#formValRef').prop('action', 'functions/fn_menu_valref_editar.php');
    $('#formValRef').submit();
  }
});

$('#formValRef').on('submit', function(event){
  $('#loader').fadeIn();
  var datos = $(this).serialize();
  $.ajax({
    type : "POST",
    url : $(this).prop('action'),
    data : datos,
    success: function(data){
      if (data == "1") {
        Command: toastr.success(textRespuesta, titleRespuesta, {onHidden : function(){location.reload();}})
      } else {
        Command: toastr.error("Ha ocurrido un error al registrar los datos.", "Error al crear.", {onHidden : function(){
          console.log(data);
          $('#loader').fadeOut();
        }})
      }
    }
  });
event.preventDefault();
});

$('#tablaValRef tbody td:nth-child(-n+2)').on('click', function(){
    $('#idvalrefver').val($(this).parent().attr("idvalref"));
    $('#ver_menuvalref').submit();
  });

function editarValRef(idvalref){
  $('#idvalrefeditar').val(idvalref);
  $('#editar_menuvalref').submit();
}

$('#modalEliminarAportesCalyNut').on('show.bs.modal', function (event) {
  var button = $(event.relatedTarget);
      idvalref = button.data('idvalref');
      $('#idvalref').val(idvalref);
});

function eliminarMenuValRef(){
  $('#modalEliminarAportesCalyNut').modal('hide');
  $('#loader').fadeIn();
  var idvalref = $('#idvalref').val();
  $.ajax({
    type: "POST",
    url : "functions/fn_menu_valref_eliminar.php",
    data: {"idvalref" : idvalref},
    success : function(data){
      if (data == "1") {
        Command: toastr.success("Se eliminó con éxito el registro.", "Eliminado con éxito.", {onHidden : function(){location.href='index.php'}})
      } else {
        Command: toastr.error("Ocurrió un error durante la eliminación del registro.", "Error al eliminar.", {onHidden : function(){location.href='index.php'}})
      }
    }
  });
}