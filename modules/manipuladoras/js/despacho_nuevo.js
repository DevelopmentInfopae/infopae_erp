var itemsActuales = [];
var dataset1;
$(document).ready(function(){


    


dataset1 =  $('#box-table-a').DataTable({
    bPaginate: false,
    order: [ 1, 'desc' ],
    pageLength: 25,
    responsive: true,
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









  $('#selectVarios').change(function(){
    console.log('Cambio el de varios');
    if ($('#selectVarios').is(':checked')) {
      $('#box-table-a tbody input[type=checkbox]').prop( "checked", true );
    }
    else{
      $('#box-table-a tbody input[type=checkbox]').prop( "checked", false );
    }
  });



  $('#proveedorEmpleado').change(function(){
    var usuario = $(this).val();
    var usuarioNm = $("#proveedorEmpleado option:selected").text();
    $('#proveedorEmpleadoNm').val(usuarioNm);
    buscar_bodegas(usuario);
  });

  $('#semana').change(function(){
    var semana = $(this).val();
    buscar_dias(semana);
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
      alert('Debe seleccionar almenos un municipio ó ruta');
      $('#municipio').focus();
    }


    if(bandera == 0){


      console.log('Buscando items agregados.');
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
          console.log(data);
          //$('#debug').html(data);

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





          //$('#sede').html(data);
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
  });



});

// Funcion para crear y guardar el despacho.
function generarDespacho(){
  console.log('Se va a generar el despacho.');

  var subtipo = $('#subtipo').val();
  var subtipoNm = $('#subtipoNm').val();

  var proveedorEmpleado = $('#proveedorEmpleado').val();
  var proveedorEmpleadoNm = $('#proveedorEmpleadoNm').val();

  var semana = $('#semana').val();
  var tipo = $('#tipoRacion').val();
  var tipoDespacho = $('#tipoDespacho').val();





  var bodegaOrigen = $('#bodegaOrigen').val();
  var tipoTransporte = $('#tipoTransporte').val();
  var placa = $('#placa').val();
  var conductor = $('#conductor').val();
  var dias = new Array();
  $('#dias .dia:checked').each(
    function() {
      //alert("El checkbox con valor " + $(this).val() + " está seleccionado");
      var aux = $(this).val();
      dias.push(aux);
    }
  );

  var itemsDespacho = [];

  console.log('Buscando items agregados.');
  $( "#box-table-a tbody input[type=checkbox]" ).each(function(){
    itemsDespacho.push($(this).val());
    console.log($(this).val());
  });

  bandera = 0;
  // Validaciones para generar el despacho
  if(subtipo == ''){
    alert('El campo tipo de despacho es obligatorio.');
    bandera++;
  }
  else if(proveedorEmpleado == ''){
    alert('El campo Proveedor / Empleado es obligatorio.');
    bandera++;
  }
  else if(semana == ''){
    alert('El campo semana es obligatorio.');
    bandera++;
  }
  else if(dias.length == 0){
    alert('Debe seleccionar al menos un día para el despacho');
    bandera++;
  }
  else if(itemsDespacho.length == 0){
    alert('Debe agregar al menos una sede para el despacho');
    bandera++;
  }
  else if(bodegaOrigen == ''){
    alert('El campo bodega origen es obligatorio');
    bandera++;
  }
  else if(tipoTransporte == ''){
    alert('El campo tipo de transporte es obligatorio');
    bandera++;
  }







  if(bandera == 0){




    var datos = {
      "subtipo":subtipo,
      "subtipoNm":subtipoNm,
      "proveedorEmpleado":proveedorEmpleado,
      "proveedorEmpleadoNm":proveedorEmpleadoNm,
      "semana":semana,
      "dias":dias,
      "tipo":tipo,
      "tipoDespacho":tipoDespacho,
      "itemsDespacho":itemsDespacho,
      "bodegaOrigen":bodegaOrigen,
      "tipoTransporte":tipoTransporte,
      "placa":placa,
      "conductor":conductor
    };

    $.ajax({
      type: "POST",
      url: "functions/fn_despacho_generar.php",
      data: datos,
      beforeSend: function(){
        console.log('Inicia la inserción en tablas');
        $('#loader').fadeIn();
      },
      success: function(data){
        $('#debug').html(data);

        if(data == 1){
          alert('El despacho se ha registrado con éxito.');
          $(window).unbind('beforeunload');
          window.location.href = 'despachos.php';
          //window.open("despacho_consolidado_fecha_lote.php");
          //window.location.href = 'despacho_consolidado_fecha_lote.php';
        }else{
          alert(data);
          console.log(data);
          //if (data.indexOf('Se ha encontrado un despacho para la sede')==-1) {
          //$('#debug').html(data);
          //}
        }














      }
    })
    .done(function(){ })
    .fail(function(){ })
    .always(function(){
      $('#loader').fadeOut();
    });
  }//Termina el if de bandera == 0

}








function reiniciarTabla(){
  dataset1 = $('#box-table-a').DataTable({
    bPaginate: false,
    order: [ 1, 'desc' ],
    pageLength: 25,
    responsive: true,
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
  console.log(usuario);
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
      //$('#debug').html(data);
      $('#dias').html(data);
    }
  });
}


function buscar_proveedor_empleado(subtipo){
  console.log('Buscar proveedor empleado')
  var datos = {"subtipo":subtipo};
    $.ajax({
      type: "POST",
      url: "functions/fn_despacho_buscar_proveedor_empleado.php",
      data: datos,
      beforeSend: function(){},
      success: function(data){
        alert(data);
        $('#proveedorEmpleado').html(data);
      }
    });
}


function buscar_institucion(municipio,tipo){
  console.log('Actualizando lista de instituciones.');
  console.log(municipio);
  console.log(tipo);
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
        //$('#debug').html(data);
        //console.log(data);
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
  var datos = {"semana":semana,"municipio":municipio,"tipo":tipo,"institucion":institucion};
    $.ajax({
      type: "POST",
      url: "functions/fn_despacho_buscar_sede.php",
      data: datos,
      beforeSend: function(){
        $('#loader').fadeIn();
      },
      success: function(data){
        //$('#debug').html(data);
        $('#sede').html(data);
      }
    })
    .done(function(){ })
    .fail(function(){ })
    .always(function(){
      $('#loader').fadeOut();
    });
}
