$(document).ready(function() {
  $('.select2').select2();

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
  };

  jQuery.extend(jQuery.validator.messages,
  {
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

  // $('#tablaSuplentes tbody td:nth-child(-n+8)').on('click', function() { ver_suplente($(this).parent().attr('numDoc'), $(this).parent().attr('tipoDoc')); });
  // $(document).on('change', '#tipo_doc', function () { $('#abreviatura').val($("#tipo_doc option:selected").data('abreviatura')); });

  $(document).on('click', '#boton_buscar_suplentes', buscar_suplentes);
  $(document).on('change', '#mes', function(){ buscarSemanasMes($(this)); });
  $(document).on('click', '.boton_subir_suplentes', function(){ subir_suplentes(); });
  $(document).on('change', '#institucion', function() { buscar_sedes($(this).val()); });
  $(document).on('click', '.subir_suplentes', function() { $('#ventana_subir_suplentes').modal(); });

  // $('#ventana_subir_suplentes').on('shown.bs.modal', function () { $('#mes').select2({dropdownParent: $('#ventana_subir_suplentes')}); });
});

function buscar_suplentes()
{
  if ($('#formulario_buscar_suplentes').valid())
  {
    $('#contenedor_listado').show();
    $('#tabla_suplentes').DataTable({
      ajax: {
        method: 'post',
        url: 'functions/fn_suplentes_buscar_suplentes.php',
        data: {
          sede: $('#sede').val(),
          semana: $('#semana').val()
        }
      },
      columns:[
        { data: 'num_doc'},
        { data: 'tipo_doc_nom'},
        { data: 'nombre'},
        { data: 'genero'},
        { data: 'grado'},
        { data: 'nom_grupo'},
        { data: 'jornada'},
        { data: 'edad'},
        { data: 'edad'}
      ],
      destroy: true,
      pageLength: 25,
      responsive: true,
      dom : '<"html5buttons" B>lr<"containerBtn"><"inputFiltro"f>tip',
      buttons : [{extend:'excel', title:'Suplentes', className:'btnExportarExcel', exportOptions: {columns : [0,1,2,3,4,5,6,7]}}],
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
      },
      initComplete: function(settings, json)
      {
        var btnAcciones = '<div class="dropdown pull-right" id=""><button class="btn btn-primary btn-sm btn-outline" type="button" id="accionesTabla" data-toggle="dropdown" aria-haspopup="true">Acciones<span class="caret"></span></button><ul class="dropdown-menu pull-right" aria-labelledby="accionesTabla"><li><a onclick="$(\'.btnExportarExcel\').click()"><span class="fa fa-file-excel-o"></span> Exportar </a></li><li><a class="subir_suplentes"><span class="fa fa-upload"></span> Importar</a></li></ul></div>';
        $('.containerBtn').html(btnAcciones);
      }
    });
  }
  else
  {
    $('#contenedor_listado').hide();
  }
}

// function ver_suplente(numDoc, tipoDoc) {
//     $('#verSuplente #numDoc').val(numDoc);
//     $('#verSuplente #tipoDoc').val(tipoDoc);
//     $('#verSuplente').submit();
// }

// function editarTitular(num_doc) {
//   $('#numDoc').val(num_doc);
//   $('#editar_suplente').submit();
// }

// function obtenerInstituciones(cod_mun) {
//     $.ajax({
//     type: "POST",
//     url: "functions/fn_suplentes_obtener_instituciones.php",
//     data: {"cod_mun" : cod_mun},
//     beforeSend: function(){},
//     success: function(data){
//         $('#cod_inst').html(data);
//         $('#cod_sede').html('<option valule="">Seleccione...</option>');

//         $('#nom_inst').val('');
//         $('#nom_sede').val('');
//     }
//   });
// }

function buscar_sedes(institucion)
{
  $.ajax({
    type: "post",
    url: "functions/fn_suplentes_buscar_sedes.php",
    data: { "municipio" : institucion }
  })
  .done(function(data)
  {
    $('#sede').html(data);
    $('#sede').select2('val', '');
  })
  .fail(function(data)
  {
    console.log(data.responseText);
  });
}

// function obtenerNombreSede() {
//     $('#nom_sede').val($('#cod_sede option:selected').text());
// }

// $('#formNuevoSuplente').on('submit', function(event){
//     $('#loader').fadeIn();

//     $.ajax({
//         type: 'POST',
//         url: 'functions/fn_suplentes_ingresar.php',
//         data: $('#formNuevoSuplente').serialize(),
//         dataType: 'json',
//         success: function(data) { console.log(data);
//             if (data.success == 1) {
//                 Command: toastr.success(data.message, 'Guardado con éxito.', {onHidden : function() { location.reload(); }});
//             } else {
//                 Command: toastr.error(data.message, 'Proceso cancelado', { onHidden : function() { location.reload(); } });
//             }
//         },
//         error: function (data) {
//             console.log(data);
//         }
//     });
//     event.preventDefault();
// });

// $('#formSuplentesEditar').on('submit', function(event) {
//     $('#loader').fadeIn();

//     $.ajax({
//         type: "POST",
//         url: "functions/fn_suplentes_editar.php",
//         data: $('#formSuplentesEditar').serialize(),
//         dataType : "JSON",
//         success: function(data){ console.log(data);
//             if (data.success == 1) {
//                 Command: toastr.success(data.message, "Actualizado con éxito.", {onHidden : function(){location.href="index.php";}})
//             } else {
//                 console.log(data);
//             }
//         }
//     });
//     event.preventDefault();
// });

// function validaNumDoc(input){
//     num_doc = $(input).val();

//     $.ajax({
//         type: "POST",
//         url: "functions/fn_suplentes_valida_documento.php",
//         data: {"num_doc" : num_doc},
//         success: function(data) { console.log(data);
//             data = JSON.parse(data);
//             if (data.respuesta[0].respuesta == "1") {
//                 $('#errorEst').css('display', '');
//                 $('#semanasErr').html(data.respuesta[0].semanas);
//             } else if (data.respuesta[0].respuesta == "0") {
//                 $('#errorEst').css('display', 'none');
//             }
//         }
//     });
// }

function buscarSemanasMes(control){
  $.ajax({
    type: "post",
    url: "functions/fn_suplentes_buscar_semana_mes.php",
    data: {"mes": control.val()},
    dataType: 'html'
  })
  .done(function(data){
    $('#semana_modal').html(data);
  })
  .fail(function(data){
    console.log(data.responseText);
  });
}

// function subir_suplentes()
// {
//     if ($('#form_subir_suplentes').valid())
//     {
//         alert('todo bien!');
//     }
// }