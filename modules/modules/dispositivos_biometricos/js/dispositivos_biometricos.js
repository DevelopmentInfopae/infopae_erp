$(document).ready(function(){

  jQuery.extend(jQuery.validator.messages, {//Configuración jquery valid
    step: "Por favor ingresa un número entero",
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

$('#referencia').on('keyup', function(){
  this.value = this.value.replace(/[^A-Z a-z 0-9 -]/g, '');
});

$('#id_bioest').on('keyup', function(){
  this.value = this.value.replace(/[^0-9,]/g, '');
});

$('#num_serial').on('keyup', function(){
  var num_serial = $(this).val();
    $.ajax({
        type: "POST",
        url: "functions/fn_dispositivos_biometricos_validar_numserial.php",
        data : {"num_serial" : num_serial},
        beforeSend: function(){},
        success: function(data){
          if (data == "1") {
            $('#existeNumSerial').css('display', '');
            $('#botonSiguiente').attr('disabled', true);
          } else if (data == "0") {
            $('#existeNumSerial').css('display', 'none');
            $('#botonSiguiente').removeAttr('disabled');
          }
        }
      });
});

$('#cod_municipio').on('change', function(){
  $.ajax({
    type: "POST",
    url: "functions/fn_dispositivos_biometricos_obtener_instituciones.php",
    data: {"cod_municipio" : $(this).val()},
    beforeSend: function(){},
    success: function(data){
      $('#cod_inst').html(data);
    }
  });
});

$('#cod_inst').on('change', function(){
  $('#cod_sede').select2("val", "");
  $.ajax({
    type: "POST",
    url: "functions/fn_dispositivos_biometricos_obtener_sedes.php",
    data: {"cod_inst" : $(this).val()},
    beforeSend: function(){},
    success: function(data){
      $('#cod_sede').html(data);
    }
  });
});

$('#cod_sede').on('change', function(){
  $('#semana_focalizacion').select2("val", "");
  var nom_sede = $('#cod_sede option:selected').text();
  $('#nom_sede').val(nom_sede);
});

$('#semana_focalizacion').on('change', function(){
  $('#nivel').val("");
});

// recogemos los datos hasta aca para traer los grados
$('#nivel').on('change', function(){
  $('#grados').select2("val", "");
  var codSede = $('#cod_sede option:selected').val();
  var nivel = $('#nivel option:selected').val();
  var semana = $('#semana_focalizacion option:selected').val();
  $.ajax({
    type: "POST",
    url: "functions/fn_dispositivos_biometricos_obtener_grados.php",
    data: {"codSede" : codSede, "nivel" : nivel, "semana" : semana},
    beforeSend: function(){},
    success: function(data){
      $('#grados').html(data);
      $('#grados').on('change', function(){
        $('#grupo').val("");
        var grado = $('#grados option:selected').val();
        $.ajax({
          type : "POST",
          url : "functions/fn_dispositivos_biometricos_obtener_grupos.php",
          data : {"codSede" : codSede, "semana" : semana, "grado" : grado},
          beforeSend: function(){},
          success: function(data){
            $('#grupo').html(data);
          }
        });
      });
    }
  });
});


function validaForm(idForm, panelOcultar, panelMostrar){
  if ($('#'+idForm).valid()) {
    $('#btnEditar_1').css('display', '');
    // $('#segundoBtnSubmit').css('display', '');
    $('#'+panelOcultar).collapse('hide');
    $('#'+panelMostrar).collapse('show');
  }
}

function validaBioEst(input, num, tipo, value){
  
  var entero = (value - Math.floor(value));
  if (entero != 0) {
    Command: toastr.warning( 'Id biometría no puede ser número decimal');
    $(input).val("").focus();
  }

  if (value <= 0) {
    Command: toastr.warning( 'Id biometría no puede ser menor que 1');
    $(input).val("").focus();
  }

  // validacion atributos que ya estan creados
  validaNum = 0;
  $('#tablaEstudiantes #id_bioest').each(function(){
    if ($(this).val() == $(input).val() && $(this).val() != "" &&  $(this).attr('name') != $(input).attr('name')) {
      validaNum++;
    }
  });

  // validacion a la tabla para comparar los id que ya estan creados despues de cambiar de campo o avanzar
  if ($('#iddispositivo')) {
    var iddispositivo = $('#iddispositivo').val();
        idbioest = $(input).val();
     $.ajax({
        type: "POST",
        url: "functions/fn_dispositivos_biometricos_validar_idbioest.php",
        data : {"iddispositivo" : iddispositivo, "idbioest" : idbioest},
        beforeSend: function(){},
        success: function(data){
          if (data == "1") {
            validaNum++;
          }
        }
      });
  }

  if (tipo == 1) {
    setTimeout(function() {
      if (validaNum > 0) {
        $(input).val("").focus();
        $('#existeBioEst'+num).css('display', '');
      } else {
        $('#existeBioEst'+num).css('display', 'none');
      }
    }, 5);
  } else if (tipo == 2) {
    setTimeout(function() {
      if (validaNum > 0) {
        $(input).val("").focus();
        $('#existeBioEstEdit'+num).css('display', '');
      } else {
        $('#existeBioEstEdit'+num).css('display', 'none');
      }
    }, 5);
  }
}

$('#tablaDispositivos tbody td:nth-child(-n+5)').on('click', function(){
    $('#idDispositivoVer').val($(this).parent().attr("iddispositivo"));
    $('#ver_dispositivo').submit();
  });

function editarDispositivo(dispositivo){
  $('#idDispositivoEditar').val(dispositivo);
  $('#editar_dispositivo').submit();
}

function buscarEstudiantes(){
  if ($('#formFocalizacion').valid()) {

      $('#loader').fadeIn();
      $('#segundoBtnSubmit').css('display', '');
      $('#segundoBtnSubmit2').css('display', '');
      var focalizacion = new Array();

      cod_sede = $('#cod_sede').val();
      semana_turno = $('#semana_focalizacion option:selected').val();
      grado = $('#grados option:selected').val();
      grupo = $('#grupo option:selected').val();

       $.ajax({
        type: "POST",
        url: "functions/fn_dispositivos_biometricos_estudianes_focalizacion.php",
        data: {"cod_sede" : cod_sede, "semana_turno" : semana_turno, "grado" : grado, "grupo" : grupo},
        beforeSend: function(){$('#tbodyEstudiantes tr').remove();},
        success: function(data){
          $('#tbodyEstudiantes').append(data);
          $('#loader').fadeOut();
        }
      });
   }
}

// creacion de nuevo dispositivo
function submitForm(){
if($('#formBiometria').valid()){ 
  $('#loader').fadeIn();
  var datos;
  $('form').each(function(){
    datos = datos +"&"+$(this).serialize();
  });

  $.ajax({
    type: "POST",
    url: "functions/fn_dispositivos_biometricos_ingresar_dispositivo.php",
    data: datos,
    beforeSend: function(){},
    success: function(data){

      console.log(data);

      data = JSON.parse(data);

      if (data.respuesta[0].exitoso == "1") {
        Command: toastr.success("Se creó con éxito.", "Creado", {onHidden : function(){location.reload();}})
      } else if (data.respuesta[0].exitoso == "1") {
        Command: toastr.error(data.respuesta[0].respuesta, "Error", {onHidden : function(){ }})
      } else {
        Command: toastr.error("Hubo un error.", "Error", {onHidden : function(){ }})
        console.log(data);
      }
    }
  });
}
}

// edicion de datos biometricos
function submitFormEditar(){
  var vari = $('#formBiometria input#id_bioest.form-control').data();
  if (typeof vari.type !== 'undefined' &&  vari.type == 1) {
    if($('#formBiometria').valid()){
 
    $('#loader').fadeIn();
    var datos;
    $('form').each(function(){
      datos = datos +"&"+$(this).serialize();
    });

    $.ajax({
      type: "POST",
      url: "functions/fn_dispositivos_biometricos_editar_dispositivo.php",
      data: datos,
      beforeSend: function(){},
      success: function(data){

        data = JSON.parse(data);

        if (data.respuesta[0].exitoso == "1") {
          Command: toastr.success("Se actualizó con éxito.", "Actualizado", {onHidden : function(){
            iddisp = $('#iddispositivo').val();
            $('#idDispositivoVer').val(iddisp);
            $('#ver_dispositivo').submit();
          }})
        } else if (data.respuesta[0].exitoso == "0") {
          Command: toastr.error(data.respuesta[0].respuesta, "Error", {onHidden : function(){ }})
        }else if (data.respuesta[0].exitoso == "2") {
          Command: toastr.warning(data.respuesta[0].respuesta, "Error", {onHidden : function(){ $('#loader').fadeOut();}})
        } 
        else {
          Command: toastr.error("Hubo un error.", "Error", {onHidden : function(){ }})
          console.log(data);
        }
      }
    });
    }
  }else if (typeof vari.type !== 'undefined' &&  vari.type == 2) {
    if($('#formBiometria').valid()){
 
    $('#loader').fadeIn();
    var datos;
    $('form').each(function(){
      datos = datos +"&"+$(this).serialize();
    // console.log(datos);
    });

    $.ajax({
      type: "POST",
      url: "functions/fn_dispositivos_biometricos_actualizar_dispositivo.php",
      data: datos,
      // dataType: json,
      beforeSend: function(){},
      success: function(data){
        data = JSON.parse(data);
        if (data.respuesta[0].exitoso == "1") {
          Command: toastr.success("Se actualizó con éxito.", "Actualizado", {onHidden : function(){
            iddisp = $('#iddispositivo').val();
            $('#idDispositivoVer').val(iddisp);
            $('#ver_dispositivo').submit();
          }})
        } else if (data.respuesta[0].exitoso == "0") {
          Command: toastr.error(data.respuesta[0].respuesta, "Error", {onHidden : function(){ }})
        }else if (data.respuesta[0].exitoso == "2") {
          Command: toastr.warning(data.respuesta[0].respuesta, "Error", {onHidden : function(){ $('#loader').fadeOut();}})
        } 
        else {
          Command: toastr.error("Hubo un error.", "Error", {onHidden : function(){ }})
          console.log(data);
        }
      }
    });
  }
  }
}

$('#modalEliminarBiometria').on('show.bs.modal', function (event) {
  var button = $(event.relatedTarget);
      idbiometria = button.data('idbiometria');
      numbiometria = button.data('numbiometria');
      console.log(idbiometria);
      $('#idbiometriaeliminar').val(idbiometria);
      $('#numbiometria').val(numbiometria);
});

$('#modalEliminarDispositivo').on('show.bs.modal', function (event) {
  var button = $(event.relatedTarget);
      iddispositivo = button.data('iddispositivo');
      // console.log(iddispositivo);
      $('#iddispositivoEli').val(iddispositivo);
});



function eliminarBiometria(){
  $('#loader').fadeIn();
  $('#modalEliminarBiometria').modal('hide');
  var idbiometria = $('#idbiometria').val();
      numbiometria = $('#numbiometria').val();
      $.ajax({
      type: "POST",
      url: "functions/fn_dispositivos_biometricos_eliminar_biometria.php",
      data: {"idbiometria" : idbiometria},
      beforeSend: function(){},
      success: function(data){

        if (data == "1") {
          $('#biometria_'+numbiometria).remove();
          Command: toastr.success("Se eliminó con éxito.", "Eliminado", {onHidden : function(){$('#loader').fadeOut();}})
        } else if (data == "0") {
          Command: toastr.error("Error al eliminar", "Error", {onHidden : function(){ }})
        } else {
          Command: toastr.error("Hubo un error.", "Error", {onHidden : function(){ }})
          console.log(data);
        }
      }
    });
}
function eliminarDispositivo(){
  $('#loader').fadeIn();
  $('#modalEliminarDispositivo').modal('hide');
  var iddispositivo = $('#iddispositivoEli').val();
      $.ajax({
      type: "POST",
      url: "functions/fn_dispositivos_biometricos_eliminar_dispositivo.php",
      data: {"iddispositivo" : iddispositivo},
      dataType: 'json',
      beforeSend: function(){},
      success: function(data){

        if (data == "1") {
          Command: toastr.success("Se eliminó con éxito.", "Eliminado", {onHidden : function(){location.href='index.php';}})
        } else if (data == "0") {
          Command: toastr.error("No se puede elimar un Dispositivo con registros", "Error", {onHidden : function(){ }})
        } else {
          Command: toastr.error("Hubo un error.", "Error", {onHidden : function(){ }})
          console.log(data);
        }
        $('#loader').fadeOut();
      }
    });
}

function existenBiometrias(){
  cntBiometria = $('#cntBiometrias').val();
  if (cntBiometria > 0) {
    var iddispositivo = $('#iddispositivo').val();
    $('#tbodyEstudiantes').empty();
    $('#consecutivoActual').html();
    $('#titularesAsignados').css('display', '');
    $.ajax({
      type: "POST",
      url: "functions/fn_dispositivos_biometricos_obtener_consecutivo.php",
      data: {"iddispositivo" : iddispositivo},
      beforeSend: function(){},
      success: function(data){
        $('#consecutivoActual').html(data);
      }
    });
  }
}

function exportarDispositivo(idDispositivo){
  $('#idDispositivoexportar').val(idDispositivo);
  $('#exportar_dispositivo').submit();
}