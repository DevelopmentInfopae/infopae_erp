var itemsActuales = [];
var dataset1;

$(document).ready(function(){
  $('.select2').select2({ width: "resolve" });

  $('.i-checks').iCheck({ checkboxClass: 'icheckbox_square-green' });
  $(document).on('ifChecked', '#selectVarios', function(){ $('#box-table-a tbody input[type=checkbox]').iCheck('check'); });
  $(document).on('ifUnchecked', '#selectVarios', function(){ $('#box-table-a tbody input[type=checkbox]').iCheck('uncheck'); });

  dataset1 =  $('#box-table-a').DataTable({
    bPaginate: false,
    order: [ 1, 'desc' ],
    pageLength: 25,
    responsive: true,
    buttons: [ {extend: 'excel', title: 'Sedes', className: 'btnExportarExcel'/*, exportOptions: { columns: [0, 1, 2, 3, 4, 5, 6] } */} ],
    dom: 'lr<"containerBtn"><"inputFiltro"f>tip<"html5buttons"B>',
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


  $('#proveedorEmpleado').change(function(){
    var usuario = $(this).val();
    var usuarioNm = $("#proveedorEmpleado option:selected").text();
    $('#proveedorEmpleadoNm').val(usuarioNm);
    buscar_bodegas(usuario);
  });

  $('#semana').change(function(){
    buscar_dias($(this).val());

    $('#tipoRacion').val('');
    $('#municipio').html('<option value="">Seleccione uno</option>');
    $('#institucion').html('<option value="">Todos</option>');
    $('#sede').html('<option value="">Todos</option>');

    dataset1.clear();
    dataset1.destroy();
    $('#box-table-a tbody').html('<tr class="odd"> <td class=" sorting_1"></td> <td></td> <td></td> <td></td> </tr>');

    reiniciarTabla();
  });

  $('#subtipo').change(function(){
    var subtipo = $(this).val();
    buscar_proveedor_empleado(subtipo);
    var subtipoNm = $("#subtipo option:selected").text();
    $('#subtipoNm').val(subtipoNm);
  });

  $('#tipoRacion').change(function(){
    var tipo = $(this).val();
    buscar_municipio(tipo);
    $('#institucion').html('<option value="">Todos</option>');
    $('#sede').html('<option value="">Todos</option>');
    dataset1.clear();
    dataset1.destroy();
    $('#box-table-a tbody').html('<tr class="odd"> <td class=" sorting_1"></td> <td></td> <td></td> <td></td> </tr>');
    reiniciarTabla();
  });

  $('#municipio').change(function(){
    var tipo = $('#tipoRacion').val();
    var municipio = $(this).val();
    buscar_institucion(municipio,tipo);
  });

  $('#institucion').change(function(){
    var institucion = $(this).val();
    var tipo = $('#tipoRacion').val();
    var municipio = $('#municipio').val();
    var semana = $('#semana').val();
    buscar_sede(semana,municipio,tipo,institucion);
  });

  $('#btnAgregar').click(function(){
    itemsActuales = [];
    $('#selectVarios').prop( "checked", false );
    var semana = $('#semana').val();
    var tipo = $('#tipoRacion').val();
    var municipio = $('#municipio').val();
    var ruta = $('#ruta').val();
    var institucion = $('#institucion').val();
    var sede = $('#sede').val();
    var consecutivo = $('#box-table-a tbody input[type=checkbox]').length;


    var bandera=0;
    if(tipo == ''){
      bandera++;
      alert('Debe seleccionar el tipo de ración');
      $('#tipoRacion').focus();
    }

    if(municipio == '' && ruta == ''){
      bandera++;
      alert('Debe seleccionar al menos un municipio ó una ruta');
      $('#municipio').focus();
    }

    if(bandera == 0){
      $( "#box-table-a tbody input[type=checkbox]" ).each(function(){
        itemsActuales.push($(this).val());
        console.log($(this).val());
      });

      var datos = {"semana":semana, "municipio":municipio, "ruta":ruta, "tipo":tipo,"institucion":institucion,"sede":sede,"consecutivo":consecutivo,"itemsActuales":itemsActuales};
      $.ajax({
        type: "POST",
        url: "functions/fn_despacho_agregar_items.php",
        data: datos,
        beforeSend: function(){
          $('#loader').fadeIn();
        },
        success: function(data){
          if(consecutivo == 0){
            dataset1.clear();
            dataset1.destroy();
            $('#box-table-a tbody').html(data);
            reiniciarTabla();
          }else{
            tabla = $('#box-table-a tbody').html();
            dataset1.clear();
            dataset1.destroy();
            $('#box-table-a tbody').html(data);
            $('#box-table-a tbody').append(tabla);
            reiniciarTabla();
          }

          $('.i-checks').iCheck({
            checkboxClass: 'icheckbox_square-green'
          });
        }
      })
      .done(function(){ })
      .fail(function(){ })
      .always(function(){
        $('#loader').fadeOut();
      });
    }
  });

  $('#btnQuitar').click(function(){
    if ($('#box-table-a tbody input[type=checkbox]').length){
      $('#selectVarios').prop( "checked", false );
      $( "#box-table-a tbody input:checked" ).each(function(){
        console.log($(this).val());
        $(this).closest('tr').remove();
      });
      tabla = $('#box-table-a tbody').html();
      dataset1.clear();
      dataset1.destroy();
      $('#box-table-a tbody').html(tabla);
      reiniciarTabla();
    }
  });

  // Configuración del pligin toast
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

// Funcion para crear y guardar el despacho.
function generarDespacho(){
  var bandera = 0;
  var itemsDespacho = [];
  var dias = new Array();
  var placa = $('#placa').val();
  var semana = $('#semana').val();
  var subtipo = $('#subtipo').val();
  var tipo = $('#tipoRacion').val();
  var subtipoNm = $('#subtipoNm').val();
  var conductor = $('#conductor').val();
  var tipoDespacho = $('#tipoDespacho').val();
  var bodegaOrigen = $('#bodegaOrigen').val();
  var tipoTransporte = $('#tipoTransporte').val();
  var proveedorEmpleado = $('#proveedorEmpleado').val();
  var proveedorEmpleadoNm = $('#proveedorEmpleadoNm').val();

  $('#dias .dia:checked').each(function() {
    var aux = $(this).val();
    dias.push(aux);
  });

  var string_variaciones;

  $( "#box-table-a tbody input[type=checkbox]" ).each(function() {
    itemsDespacho.push($(this).val());
    string_variaciones += $(this).val()+"-"+($(this).data('variaciones') == 0 ? 3 : $(this).data('variaciones'))+", ";
  });

  // Validaciones para generar el despacho
  if(subtipo == ''){
    Command: toastr.warning('El campo <strong>tipo de despacho</strong> es obligatorio.', 'Advertencia');
    bandera++;
  } else if (proveedorEmpleado == '') {
    Command: toastr.warning('El campo <strong>Proveedor / Empleado</strong> es obligatorio.', 'Advertencia');
    bandera++;
  } else if (semana == '') {
    Command: toastr.warning('El campo <strong>semana</strong> es obligatorio.', 'Advertencia');
    bandera++;
  } else if (dias.length == 0) {
    Command: toastr.warning('Debe seleccionar al menos un <strong>día</strong> para el despacho.', 'Advertencia');
    bandera++;
  } else if (itemsDespacho.length == 0) {
    Command: toastr.warning('Debe agregar al menos una <strong>sede</strong> para el despacho.', 'Advertencia');
    bandera++;
  } else if (bodegaOrigen == '') {
    Command: toastr.warning('El campo <strong>bodega origen</strong> es obligatorio.', 'Advertencia');
    bandera++;
  } else if (tipoTransporte == '') {
    Command: toastr.warning('El campo <strong>tipo de transporte</strong> es obligatorio.', 'Advertencia');
    bandera++;
  }

  if(bandera == 0) {
    $.ajax({
      type: "POST",
      url: "functions/fn_despacho_generar.php",
      dataType: "HTML",
      data: {
        "subtipo":subtipo,
        "subtipoNm":subtipoNm,
        "proveedorEmpleado":proveedorEmpleado,
        "proveedorEmpleadoNm":proveedorEmpleadoNm,
        "semana":semana,
        "dias":dias,
        "tipo":tipo,
        "tipoDespacho":tipoDespacho,
        "itemsDespacho":itemsDespacho,
        "itemsDespachoVariaciones":string_variaciones,
        "bodegaOrigen":bodegaOrigen,
        "tipoTransporte":tipoTransporte,
        "placa":placa,
        "conductor":conductor
      },
      beforeSend: function(){
        $('#loader').fadeIn();
      }
    })
    .done(function(data) {
      console.log(data);
      $('#debug').html(data);

      if (data == 1) {
        alert('El despacho se ha registrado con éxito.');
        $(window).unbind('beforeunload');
        window.location.href = 'despachos.php';
      } else {
        alert(data);
      }
    })
    .fail(function(data){ console.log(data); })
    .always(function(){
      $('#loader').fadeOut();
    });
  }
}

function reiniciarTabla(){
  dataset1 = $('#box-table-a').DataTable({
    bPaginate: false,
    order: [ 1, 'desc' ],
    pageLength: 25,
    responsive: true,
    buttons: [ {extend: 'excel', title: 'Sedes', className: 'btnExportarExcel'/*, exportOptions: { columns: [0, 1, 2, 3, 4, 5, 6] } */} ],
    dom: 'lr<"containerBtn"><"inputFiltro"f>tip<"html5buttons"B>',
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
  });// Fin Funcionamiento del report

  var anchoTabla = $('#box-table-a').width();
  var anchoTabla = anchoTabla-8;
  $('.fg-toolbar').css({ 'width': anchoTabla });
  $( window ).resize(function(){
    var anchoTabla = $('#box-table-a').width();
    var anchoTabla = anchoTabla-8;
    $('.fg-toolbar').css({ 'width': anchoTabla }); }
  );
}

function buscar_bodegas(usuario){
  var datos = {"usuario":usuario};
  $.ajax({
    type: "POST",
    url: "functions/fn_despacho_buscar_bodegas.php",
    data: datos,
    beforeSend: function(){},
    success: function(data){
      //$('#debug').html(data);
      $('#bodegaOrigen').html(data);
    }
  });
}

function buscar_dias(semana){
  var datos = {"semana":semana};
  $.ajax({
    type: "POST",
    url: "functions/fn_despacho_buscar_dias_semana_check.php",
    data: datos,
    beforeSend: function(){},
    success: function(data){
      $('#dias').html(data);
      $('.i-checks').iCheck({ checkboxClass: 'icheckbox_square-green' });
    }
  });
}

function buscar_proveedor_empleado(subtipo){
  var datos = {"subtipo":subtipo};
    $.ajax({
      type: "POST",
      url: "functions/fn_despacho_buscar_proveedor_empleado.php",
      data: datos,
      beforeSend: function(){},
      success: function(data){
        //alert(data);
        $('#proveedorEmpleado').html(data);
      }
    });
}

function buscar_institucion(municipio,tipo){
  var datos = {"municipio":municipio,"tipo":tipo};
    $.ajax({
      type: "POST",
      url: "functions/fn_despacho_buscar_institucion.php",
      data: datos,
      beforeSend: function(){
        $('#loader').fadeIn();
      },
      success: function(data){
        //$('#debug').html(data);
        $('#institucion').html(data);
      }
    })
    .done(function(){ })
    .fail(function(){ })
    .always(function(){
      $('#loader').fadeOut();
    });
}

function buscar_municipio(tipo){
    var datos = {"tipo":tipo};
    $.ajax({
      type: "POST",
      url: "functions/fn_despacho_buscar_municipio.php",
      data: datos,
      beforeSend: function(){
        $('#loader').fadeIn();
      },
      success: function(data){
        $('#municipio').html(data);
      }
    })
    .done(function(){ })
    .fail(function(){ })
    .always(function(){
      $('#loader').fadeOut();
    });
}

function buscar_sede(semana, municipio, tipo, institucion){
    $.ajax({
      type: "POST",
      url: "functions/fn_despacho_buscar_sede.php",
      data: {
        "semana":semana,
        "municipio":municipio,
        "tipo":tipo,
        "institucion":institucion
      },
      beforeSend: function(){
        $('#loader').fadeIn();
      }
    })
    .done(function(data){ $('#sede').html(data); })
    .fail(function(data){ console.log(data); })
    .always(function(){
      $('#loader').fadeOut();
    });
}
