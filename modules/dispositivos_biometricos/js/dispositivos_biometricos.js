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
  var nom_sede = $('#cod_sede option:selected').text();
  $('#nom_sede').val(nom_sede);
});

function validaForm(idForm, panelOcultar, panelMostrar){
  if ($('#'+idForm).valid()) {
    $('#btnEditar_1').css('display', '');
    $('#segundoBtnSubmit').css('display', '');
    $('#'+panelOcultar).collapse('hide');
    $('#'+panelMostrar).collapse('show');
  }
}

function validaBioEst(input, num, tipo){
  var validaNum = 0;
  $('#tablaEstudiantes #id_bioest').each(function(){
    if ($(this).val() == $(input).val() && $(this).val() != "" &&  $(this).attr('name') != $(input).attr('name')) {
      validaNum++;
    }
  });

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
    }, 800);
  } else if (tipo == 2) {
    setTimeout(function() {
      if (validaNum > 0) {
        $(input).val("").focus();
        $('#existeBioEstEdit'+num).css('display', '');
      } else {
        $('#existeBioEstEdit'+num).css('display', 'none');
      }
    }, 800);
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
      var focalizacion = new Array();

      $('#semana_focalizacion option:selected').each(function(){
        focalizacion.push($(this).val());
      });
      if ( $.fn.DataTable.isDataTable( '#tablaEstudiantes' ) ) {
        $('#tablaEstudiantes').DataTable().destroy();
      }
      cod_sede = $('#cod_sede').val();
       $.ajax({
        type: "POST",
        url: "functions/fn_dispositivos_biometricos_estudianes_focalizacion.php",
        data: {"focalizacion" : focalizacion, "cod_sede" : cod_sede},
        beforeSend: function(){},
        success: function(data){
          $('#tbodyEstudiantes').append(data);
          setTimeout(function() {
          $('#tablaEstudiantes').DataTable({
          order: [ 3, 'asc' ],
          pageLength: 25,
          responsive: true,
          fnDestroy: true,
          dom : '<"html5buttons" B>lr<"containerBtn"><"inputFiltro"f>tip',
          buttons : [{extend:'excel', title:'Biometrias', className:'btnExportarExcel', exportOptions: {columns : [0,1,2,3,4]}}],
          oLanguage: {
            sLengthMenu: 'Mostrando _MENU_ registros por página',
            sZeroRecords: 'No se encontraron registros',
            sInfo: 'Mostrando _START_ a _END_ de _TOTAL_ registros',
            sInfoEmpty: 'Mostrando 0 a 0 de 0 registros',
            sInfoFiltered: '(Filtrado desde _MAX_ registros)',
            sSearch:         'Buscar: ',
            oPaginate:{
              sFirst:    'Primero',
              sLast:     'Último',
              sNext:     'Siguiente',
              sPrevious: 'Anterior'
            }
          }
          });
          $('#loader').fadeOut();}, 1000);
        }
      });
   }
}

function submitForm(){
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

function submitFormEditar(){
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

      console.log(data);

      data = JSON.parse(data);

      if (data.respuesta[0].exitoso == "1") {
        Command: toastr.success("Se actualizó con éxito.", "Actualizado", {onHidden : function(){
          iddisp = $('#iddispositivo').val();
          $('#idDispositivoVer').val(iddisp);
          $('#ver_dispositivo').submit();
        }})
      } else if (data.respuesta[0].exitoso == "0") {
        Command: toastr.error(data.respuesta[0].respuesta, "Error", {onHidden : function(){ }})
      } else {
        Command: toastr.error("Hubo un error.", "Error", {onHidden : function(){ }})
        console.log(data);
      }
    }
  });
}

$('#modalEliminarBiometria').on('show.bs.modal', function (event) {
  var button = $(event.relatedTarget);
      idbiometria = button.data('idbiometria');
      numbiometria = button.data('numbiometria');
      $('#idbiometriaeliminar').val(idbiometria);
      $('#numbiometria').val(numbiometria);
});

$('#modalEliminarDispositivo').on('show.bs.modal', function (event) {
  var button = $(event.relatedTarget);
      iddispositivo = button.data('iddispositivo');
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
      beforeSend: function(){},
      success: function(data){

        if (data == "1") {
          Command: toastr.success("Se eliminó con éxito.", "Eliminado", {onHidden : function(){location.href='index.php';}})
        } else if (data == "0") {
          Command: toastr.error("Error al eliminar", "Error", {onHidden : function(){ }})
        } else {
          Command: toastr.error("Hubo un error.", "Error", {onHidden : function(){ }})
          console.log(data);
        }
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