var itemsActuales = [];
$(document).ready(function(){

  buscar_municipio();

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
    cambioDeInstitucion();
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
    var municipio = $('#municipio').val();
    var institucion = $('#institucion').val();
    var sede = $('#sede').val();
    var consecutivo = $('#box-table-a tbody input[type=checkbox]').length;

    var bandera=0;
    if(municipio == ''){
      bandera++;
      alert('Debe seleccionar un municipio');
      $('#municipio').focus();
    }

    if(bandera == 0){
      console.log('Buscando items agregados.');
      $( "#box-table-a tbody input[type=checkbox]" ).each(function(){
        itemsActuales.push($(this).val());
        console.log($(this).val());
      });

      var datos = {"municipio":municipio,"institucion":institucion,"sede":sede,"consecutivo":consecutivo,"itemsActuales":itemsActuales};
      $.ajax({
        type: "POST",
        url: "functions/fn_rutas_agregar_items.php",
        data: datos,
        beforeSend: function(){
          $('body').addClass('stop-scrolling');
          $('body').bind('touchmove', function(e){e.preventDefault()});
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
        }
      })
      .done(function(){ })
      .fail(function(){ })
      .always(function(){
        $('#loader').fadeOut();
        $('body').removeClass('stop-scrolling');
        $('body').unbind('touchmove');
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




function cambioDeInstitucion(){
  console.log('Cambio la institución');
  var institucion = $('#institucion').val();
  var tipo = $('#tipoRacion').val();
  var municipio = $('#municipio').val();
  var semana = $('#semana').val();
  buscar_sede(semana,municipio,tipo,institucion);  
}






function guardarRuta(){
  console.log('Se va a guardar la ruta');

  var nombreRuta = '';
  var itemsDespacho = [];

  nombreRuta = $('#nombreRuta').val();
  console.log('Buscando items agregados.');
  $( "#box-table-a tbody input[type=checkbox]" ).each(function(){
    itemsDespacho.push($(this).val());
    console.log($(this).val());
  });

  bandera = 0;

  if(nombreRuta == ''){
    alert('El campo nombre ruta es obligatorio.');
    bandera++;
  }
  else if(itemsDespacho.length == 0){
    alert('Debe agregar al menos una sede a la ruta');
    bandera++;
  }

  if(bandera == 0){
    var datos = {
      "nombreRuta":nombreRuta,
      "itemsDespacho":itemsDespacho
    };

    console.log(nombreRuta);

    $.ajax({
      type: "POST",
      url: "functions/fn_ruta_guardar.php",
      data: datos,
      beforeSend: function(){
        console.log('Inicia la inserción en tablas');
        $('#loader').fadeIn();
      },
      success: function(data){
        if(data == 1){
          alert('La ruta se ha registrado con éxito.');
          $(window).unbind('beforeunload');
          window.location.href = 'rutas.php';
        }else{
          alert(data);
          console.log(data);
          $('#debug').html(data);
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
          "bPaginate": false,
          "order": [[ 1, "asc" ]],
          "oLanguage": {
            "sLengthMenu": "Mostrando _MENU_ registros por página",
            "sZeroRecords": "No se encontraron registros",
            "sInfo": "Mostrando _START_ a _END_ de _TOTAL_ registros",
            "sInfoEmpty": "Mostrando 0 a 0 de 0 registros",
            "sInfoFiltered": "(Filtrado desde _MAX_ registros)",
            "sSearch":         "Buscar: ",
        "oPaginate": {
          "sFirst":    "Primero",
          "sLast":     "Último",
          "sNext":     "Siguiente",
          "sPrevious": "Anterior"
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
    url: "fn_despacho_buscar_bodegas.php",
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
    url: "fn_despacho_buscar_dias_semana_check.php",
    data: datos,
    beforeSend: function(){},
    success: function(data){
      //$('#debug').html(data);
      $('#dias').html(data);
    }
  });
}


function buscar_proveedor_empleado(subtipo){
var datos = {"subtipo":subtipo};
  $.ajax({
    type: "POST",
    url: "fn_despacho_buscar_proveedor_empleado.php",
    data: datos,
    beforeSend: function(){},
    success: function(data){
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
      url: "functions/fn_rutas_buscar_institucion.php",
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

function buscar_municipio(){
    $.ajax({
      type: "POST",
      url: "functions/fn_rutas_buscar_municipio.php",
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
      url: "functions/fn_rutas_buscar_sede.php",
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
