var rotador = '<i class="fa fa-refresh fa-spin fa-3x fa-fw margin-bottom"></i>';
rotador = rotador + '<span class="sr-only">Loading...</span>';
var dataset1;
var items = 0;
var itemsAgregados = 0;
var filaVacia = '<tr> <td></td> <td></td> <td></td> <td></td> <td></td> <td></td> <td></td> <td></td> <td></td> <td></td> <td></td> <td> </td> </tr> ';















function buscar_cant_unidad(item,codigo){
  console.log('Se va a buscar el nombre y la cantidad que representa la unidad item: '+item+' codigo: '+codigo);

  //unidades
  //cantunidades
  //unidadesnm

  var unidad = $('#unidades'+item).val();

  if(unidad != ''){
    var datos = { "unidad":unidad,"codigo":codigo };
    $.ajax({
      type: "POST",
      url: "functions/fn_movimiento_buscar_cantidades_unidad.php",
      data: datos,
      beforeSend: function(){},
      success: function(i){


        try {
          var obj = JSON.parse(i);
          var aux = obj.cantidad;
          aux = parseFloat(aux).toFixed(2);
          //alert(aux);
  
          $('#factor'+item).removeAttr( "value" );
          $('#factor'+item).attr( "value", aux);
  
  
          $('#unidadesnm'+item).removeAttr( "value" );
          $('#unidadesnm'+item).attr( "value", obj.nombre);
        }
        catch(err) {
          $('#debug').html(err.message);
          $('#debug').append('<br/><br/>');
          $('#debug').append(i);
        }
        
        
        
        
     






      }//Termina el success
    });
  }else{
    var aux = '';
    $('#factor'+item).removeAttr( "value" );
    $('#factor'+item).attr( "value", aux);


    $('#unidadesnm'+item).removeAttr( "value" );
    $('#unidadesnm'+item).attr( "value", aux);
  }
}





function guardar_cambio_movimiento(aprobado){
  var aprobado = aprobado;
  console.log('Se van a guardar los cambios.');
  var formulario = $('#nuevoDocumento');
  var bandera = 0;

  //Iniciando la validación de los campos

  var documento = $('#documento').val();
  if(documento == '' && bandera == 0){
    alert('El campo documento es obligatorio');
    $('#documento').focus();
    bandera++;
  }

  var tipo = $('#tipo').val();
  if(tipo == '' && bandera == 0){
    alert('El campo tipo es obligatorio');
    $('#tipo').focus();
    bandera++;
  }

  var fecha = $('#fecha').val();
  if(fecha == '' && bandera == 0){
    alert('El campo fecha es obligatorio');
    $('#fecha').focus();
    bandera++;
  }

  if ($('#bodegaOrigen').prop("disabled") == false){
    var bodegaOrigen = $('#bodegaOrigen').val();
    if(bodegaOrigen == '' && bandera == 0){
      alert('El campo bodega origen es obligatorio');
      $('#bodegaOrigen').focus();
      bandera++;
    }
  }

  if ($('#bodegaDestino').prop("disabled") == false){
    var bodegaDestino = $('#bodegaDestino').val();
    if(bodegaDestino == '' && bandera == 0){
      alert('El campo bodega destino es obligatorio');
      $('#bodegaDestino').focus();
      bandera++;
    }
  }

  var nitcc = $('#nitcc').val();
  if(nitcc == '' && bandera == 0){
    alert('El campo nitcc es obligatorio');
    $('#nitcc').focus();
    bandera++;
  }

  var nombre = $('#nombre').val();
  if(nombre == '' && bandera == 0){
    alert('El campo nombre es obligatorio');
    $('#nombre').focus();
    bandera++;
  }

  var tipoTransporte = $('#tipoTransporte').val();
  if(tipoTransporte == '' && bandera == 0){
    alert('El campo tipo de transporte es obligatorio');
    $('#tipoTransporte').focus();
    bandera++;
  }

  var conductor = $('#conductor').val();
  if(conductor == '' && bandera == 0){
    alert('El campo conductor es obligatorio');
    $('#conductor').focus();
    bandera++;
  }

  var placa = $('#placa').val();
  if(placa == '' && bandera == 0){
    alert('El campo placa es obligatorio');
    $('#placa').focus();
    bandera++;
  }

  if(placa != '' && bandera == 0){
    expr = /^([a-zA-Z0-9])+([a-zA-Z0-9])+$/;
    if ( !expr.test(placa) ) {
      alert('El campo placa debe ser llenado solo con números y letras y debe tener una longitud máxima de 6 caracteres');
      bandera++;
    }
  }













  var concepto = $('#concepto').val();
  if(concepto == '' && bandera == 0){
    alert('El campo concepto es obligatorio');
    $('#concepto').focus();
    bandera++;
  }

  // Validando los campos de la grilla
  $('.bodegaOrigen').each(function(){
    if ($(this).prop("disabled") == false){
      var bodegaOrigen = $(this).val();
      if(bodegaOrigen == '' && bandera == 0){
        alert('El campo bodega origen es obligatorio');
        $(this).focus();
        bandera++;
      }
    }
  });

  $('.bodegaDestino').each(function(){
    if ($(this).prop("disabled") == false){
      var bodegaDestino = $(this).val();
      if(bodegaDestino == '' && bandera == 0){
        alert('El campo bodega destino es obligatorio');
        $(this).focus();
        bandera++;
      }
    }
  });

  $('.unidadesM').each(function(){
    if ($(this).prop("disabled") == false){
      var unidadesM = $(this).val();
      if(unidadesM == '' && bandera == 0){
        alert('El campo U. Medida es obligatorio');
        $(this).focus();
        bandera++;
      }
    }
  });

  $('.cantunidades').each(function(){
    if ($(this).prop("disabled") == false){
      var cantunidades = $(this).val();
      if(cantunidades == '' && bandera == 0){
        alert('El campo Cant. U. Medida es obligatorio');
        $(this).focus();
        bandera++;
      }
    }
  });

  $('.unidades').each(function(){
    if ($(this).prop("disabled") == false){
      var unidades = $(this).val();
      if(unidades == 0 && bandera == 0){
        alert('El campo unidades es obligatorio');
        $(this).focus();
        bandera++;
      }
    }
  });

  $('.loteItem').each(function(){
    if ($(this).prop("disabled") == false){
      var loteItem = $(this).val();
      if(loteItem == 0 && bandera == 0){
        alert('El campo lote es obligatorio');
        $(this).focus();
        bandera++;
      }
    }
  });

  $('.fechaItem').each(function(){
    if ($(this).prop("disabled") == false){
      var fechaItem = $(this).val();
      if(fechaItem == '' && bandera == 0){
        alert('El campo fecha de vencimiento es obligatorio');
        $(this).focus();
        bandera++;
      }
    }
  });

  $('.costounitario').each(function(){
    if ($(this).prop("disabled") == false){
      var costounitario = $(this).val();
      if(costounitario == 0 && bandera == 0){
        alert('El campo costo unitario es obligatorio');
        $(this).focus();
        bandera++;
      }
    }
  });


  if(bandera == 0){
    if (aprobado == 1) {
      var r = confirm("El movimiento de producto que está intentando aprobar y guardar afectará los inventarios y no se podrá modificar posterior mente.");
      if (r == true) {}
      else { bandera++; }
    }else{
      alert('El movimiento de producto que está intentando guardar no quedara aprobado por lo tanto no afecta inventarios. Para aprobarlo debe seleccionarlo, editarlo y escoger la opción aprobar y guardar nuevamente');
    }
  }
  if(items == 0){
    items = $('#box-table-a tbody tr').length;
  }
  console.log('Items a insertar: '+items);



  if(bandera == 0){
    $.ajax({
      type: "POST",
      url: "functions/fn_movimiento_guardar_cambio_documento.php",
      data: formulario.serialize()+ "&items="+items+ "&aprobado="+aprobado,
      beforeSend: function(){},
      success: function(i){
        //$('#debug').html(i);
        if(i == 1){
          $(window).unbind('beforeunload');
          alert('El registro se ha actualizado con éxito.');
          window.location.href = "movimientos.php";
        }else{
          $('#debug').html(i);
        }
      }//Termina el success
    });
  }
}//Termina la función de guardar cambio en movimiento




function guardar_movimiento(aprobado){
  console.log('Se va a guardar el documento, aprobado: '+aprobado);
  var aprobado = aprobado;
  var formulario = $('#nuevoDocumento');
  var bandera = 0;
  //Iniciando la validación de los campos

  var documento = $('#documento').val();
  if(documento == '' && bandera == 0){
    alert('El campo documento es obligatorio');
    $('#documento').focus();
    bandera++;
  }

  var tipo = $('#tipo').val();
  if(tipo == '' && bandera == 0){
    alert('El campo tipo es obligatorio');
    $('#tipo').focus();
    bandera++;
  }

  var fecha = $('#fecha').val();
  if(fecha == '' && bandera == 0){
    alert('El campo fecha es obligatorio');
    $('#fecha').focus();
    bandera++;
  }

  if ($('#bodegaOrigen').prop("disabled") == false){
    var bodegaOrigen = $('#bodegaOrigen').val();
    if(bodegaOrigen == '' && bandera == 0){
      alert('El campo bodega origen es obligatorio');
      $('#bodegaOrigen').focus();
      bandera++;
    }
  }

  if ($('#bodegaDestino').prop("disabled") == false){
    var bodegaDestino = $('#bodegaDestino').val();
    if(bodegaDestino == '' && bandera == 0){
      alert('El campo bodega destino es obligatorio');
      $('#bodegaDestino').focus();
      bandera++;
    }
  }

  var nitcc = $('#nitcc').val();
  if(nitcc == '' && bandera == 0){
    alert('El campo nitcc es obligatorio');
    $('#nitcc').focus();
    bandera++;
  }

  var nombre = $('#nombre').val();
  if(nombre == '' && bandera == 0){
    alert('El campo nombre es obligatorio');
    $('#nombre').focus();
    bandera++;
  }

  var tipoTransporte = $('#tipoTransporte').val();
  if(tipoTransporte == '' && bandera == 0){
    alert('El campo tipo de transporte es obligatorio');
    $('#tipoTransporte').focus();
    bandera++;
  }

  var conductor = $('#conductor').val();
  if(conductor == '' && bandera == 0){
    alert('El campo conductor es obligatorio');
    $('#conductor').focus();
    bandera++;
  }

  var placa = $('#placa').val();
  if(placa == '' && bandera == 0){
    alert('El campo placa es obligatorio');
    $('#placa').focus();
    bandera++;
  }

  if(placa != '' && bandera == 0){
    expr = /^([a-zA-Z0-9])+([a-zA-Z0-9])+$/;
    if ( !expr.test(placa) ) {
      alert('El campo placa debe ser llenado solo con números y letras y debe tener una longitud máxima de 6 caracteres');
      bandera++;
    }
  }













  var concepto = $('#concepto').val();
  if(concepto == '' && bandera == 0){
    alert('El campo concepto es obligatorio');
    $('#concepto').focus();
    bandera++;
  }

  // Validando los campos de la grilla
  $('.bodegaOrigen').each(function(){
    if ($(this).prop("disabled") == false){
      var bodegaOrigen = $(this).val();
      if(bodegaOrigen == '' && bandera == 0){
        alert('El campo bodega origen es obligatorio');
        $(this).focus();
        bandera++;
      }
    }
  });

  $('.bodegaDestino').each(function(){
    if ($(this).prop("disabled") == false){
      var bodegaDestino = $(this).val();
      if(bodegaDestino == '' && bandera == 0){
        alert('El campo bodega destino es obligatorio');
        $(this).focus();
        bandera++;
      }
    }
  });

  $('.unidadesM').each(function(){
    if ($(this).prop("disabled") == false){
      var unidadesM = $(this).val();
      if(unidadesM == '' && bandera == 0){
        alert('El campo U. Medida es obligatorio');
        $(this).focus();
        bandera++;
      }
    }
  });

  $('.cantunidades').each(function(){
    if ($(this).prop("disabled") == false){
      var cantunidades = $(this).val();
      if(cantunidades == '' && bandera == 0){
        alert('El campo Cant. U. Medida es obligatorio');
        $(this).focus();
        bandera++;
      }
    }
  });

  $('.unidades').each(function(){
    if ($(this).prop("disabled") == false){
      var unidades = $(this).val();
      if(unidades == 0 && bandera == 0){
        alert('El campo unidades es obligatorio');
        $(this).focus();
        bandera++;
      }
    }
  });

  $('.loteItem').each(function(){
    if ($(this).prop("disabled") == false){
      var loteItem = $(this).val();
      if(loteItem == 0 && bandera == 0){
        alert('El campo lote es obligatorio');
        $(this).focus();
        bandera++;
      }
    }
  });

  $('.fechaItem').each(function(){
    if ($(this).prop("disabled") == false){
      var fechaItem = $(this).val();
      if(fechaItem == '' && bandera == 0){
        alert('El campo fecha de vencimiento es obligatorio');
        $(this).focus();
        bandera++;
      }
    }
  });

  $('.costounitario').each(function(){
    if ($(this).prop("disabled") == false){
      var costounitario = $(this).val();
      if(costounitario == 0 && bandera == 0){
        alert('El campo costo unitario es obligatorio');
        $(this).focus();
        bandera++;
      }
    }
  });

  if(bandera == 0){

    if (aprobado == 1) {
      var r = confirm("El movimiento de producto que está intentando aprobar y guardar afectará los inventarios y no se podrá modificar posterior mente.");
      if (r == true) {}
      else { bandera++; }
    }else{
      alert('El movimiento de producto que está intentando guardar no quedara aprobado por lo tanto no afecta inventarios. Para aprobarlo debe seleccionarlo, editarlo y escoger la opción aprobar y guardar nuevamente');
    }

  }


  if(bandera == 0){
    console.log('Iniciando ajax proceso de guardado del movimiento.');
    $.ajax({
      type: "POST",
      url: "functions/fn_movimiento_guardar_documento.php",
      data: formulario.serialize()+ "&items="+items+ "&aprobado="+aprobado,
      beforeSend: function(){},
      success: function(i){
        //$('#debug').html(i);
        //console.log(i);
        if(i == 1){
          $(window).unbind('beforeunload');
          alert('El registro se ha hecho con éxito.');
          window.location.href = "movimientos.php";
        }
        else{
          console.log(i);
          $('#debug').html(i);  
        }
      }//Termina el success
    });
  }
}// Termina la función de guardar movimiento.

  














function agregar_item_grilla(){
  //Validando encabezado
  var bandera = 0;

  //Iniciando la validación de los campos

  var documento = $('#documento').val();
  if(documento == '' && bandera == 0){
    alert('El campo documento es obligatorio');
    $('#documento').focus();
    bandera++;
  }

  var tipo = $('#tipo').val();
  if(tipo == '' && bandera == 0){
    alert('El campo tipo es obligatorio');
    $('#tipo').focus();
    bandera++;
  }

  var fecha = $('#fecha').val();
  if(fecha == '' && bandera == 0){
    alert('El campo fecha es obligatorio');
    $('#fecha').focus();
    bandera++;
  }

  if ($('#bodegaOrigen').prop("disabled") == false){
    var bodegaOrigen = $('#bodegaOrigen').val();
    if(bodegaOrigen == '' && bandera == 0){
      alert('El campo bodega origen es obligatorio');
      $('#bodegaOrigen').focus();
      bandera++;
    }
  }

  if ($('#bodegaDestino').prop("disabled") == false){
    var bodegaDestino = $('#bodegaDestino').val();
    if(bodegaDestino == '' && bandera == 0){
      alert('El campo bodega destino es obligatorio');
      $('#bodegaDestino').focus();
      bandera++;
    }
  }

  var nitcc = $('#nitcc').val();
  if(nitcc == '' && bandera == 0){
    alert('El campo nitcc es obligatorio');
    $('#nitcc').focus();
    bandera++;
  }

  var nombre = $('#nombre').val();
  if(nombre == '' && bandera == 0){
    alert('El campo nombre es obligatorio');
    $('#nombre').focus();
    bandera++;
  }

  var tipoTransporte = $('#tipoTransporte').val();
  if(tipoTransporte == '' && bandera == 0){
    alert('El campo tipo de transporte es obligatorio');
    $('#tipoTransporte').focus();
    bandera++;
  }

  var conductor = $('#conductor').val();
  if(conductor == '' && bandera == 0){
    alert('El campo conductor es obligatorio');
    $('#conductor').focus();
    bandera++;
  }

  var placa = $('#placa').val();
  if(placa == '' && bandera == 0){
    alert('El campo placa es obligatorio');
    $('#placa').focus();
    bandera++;
  }

  if(placa != '' && bandera == 0){
    expr = /^([a-zA-Z0-9])+([a-zA-Z0-9])+$/;
    if ( !expr.test(placa) ) {
      alert('El campo placa debe ser llenado solo con números y letras y debe tener una longitud máxima de 6 caracteres');
      bandera++;
    }
  }













  var concepto = $('#concepto').val();
  if(concepto == '' && bandera == 0){
    alert('El campo concepto es obligatorio');
    $('#concepto').focus();
    bandera++;
  }

  //Termina de validar el encabezado

  if(bandera == 0){
    console.log('Agregando item a la grilla');
    var documento = '';
    var bodegaOrigen = '';
    var bodegaDestino = '';
    var codigoProducto = '';
    var descripcionProducto = '';

    documento = $('#documento').val();
    bodegaOrigen = $('#bodegaOrigen').val();
    bodegaDestino = $('#bodegaDestino').val();
    codigoProducto = $('#codigoProducto').val();
    descripcionProducto = $('#descripcionProducto').val();


    if(items == 0){
      items = $('#box-table-a tbody tr .codigoItem ').length;
      //alert(items);
    }
    if(items > 0){
        itemsAgregados = items;
    }

    if(codigoProducto != ''){
      items++;

      var datos = {
        "items":items,
        "documento":documento,
        "bodegaOrigen":bodegaOrigen,
        "bodegaDestino":bodegaDestino,
        "codigoProducto":codigoProducto,
        "descripcionProducto":descripcionProducto
      };

      $.ajax({
        type: "POST",
        url: "functions/fn_movimiento_agregar_item_grilla.php",
        data: datos,
        beforeSend: function(){},
        success: function(i) {
          tabla = $('#box-table-a tbody').html();

          console.log('Items:'+items);
          console.log('Items Agregados:'+itemsAgregados);

          console.log(dataset1);
          dataset1.clear();
          dataset1.destroy();

          $('#box-table-a tbody').html(i);
          if(itemsAgregados > 0){
            $('#box-table-a tbody').append(tabla);
          }
          itemsAgregados++;

          var indice = $('.consecutivo').length;
          console.log('Elementos: '+indice);
          $('.consecutivo').each(function(){
            $(this).text(indice);
            indice--;
          });
          reiniciarTabla();

          $('#codigoProducto').val('');
          $('#descripcionProducto').val('');
        }//Termina el success
      });
    }else{
      alert('Debe buscar el producto que desea agregar.');
    }
  }//Termina validación de la bandera
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
    });











  var anchoTabla = $('#box-table-a').width();
  var anchoTabla = anchoTabla-8;
  $('.fg-toolbar').css({ 'width': anchoTabla });
  $( window ).resize(function(){
    var anchoTabla = $('#box-table-a').width();
    var anchoTabla = anchoTabla-8;
    $('.fg-toolbar').css({ 'width': anchoTabla }); }
  );


calcular();






// Esta función asigna el value a los input cuando se escribe sobre
// ellos para que no se pierdan al agregar mas filas a la grilla.
$('#box-table-a input').keyup(function(){
var aux = $(this).val();
$(this).removeAttr( "value" );
$(this).attr( "value", aux);
});

$('#box-table-a input').change(function(){
var aux = $(this).val();
$(this).removeAttr( "value" );
$(this).attr( "value", aux);
});

$('#box-table-a select').change(function(){
var aux = $(this).find('option:selected');
aux.attr("selected","selected");
});



$('.datepick').each(function(){
$(this).removeClass("hasDatepicker");

var currentYear = new Date().getFullYear();
var periodoActualCompleto = $('#periodoActualCompleto').val();




if( currentYear == periodoActualCompleto){
  $(this).datepicker({
  changeMonth: true,
  changeYear: false

  });
}else{
  $(this).datepicker({
  changeMonth: true,
  changeYear: false,
  defaultDate: "01/01/"+periodoActualCompleto
  });
}









});


$('.unidades').change(function(){
calcular();
});

$('.costounitario').change(function(){
calcular();
});



function calcular(){
  var total=0;
  var subtotal = 0;
  console.log('Vamos a calcular');
  $('#box-table-a tr').each(function(){

  if ($(this).find('.unidades').length) {
    subtotal = $(".unidades",this).val() * $(".costounitario",this).val();
    $(".costototal",this).val(subtotal);
    total = total + subtotal;
    console.log('total: '+total);
  }


  });
  $("#valorTotal").val(total);
}

$('.quitarItem').click(function(){

    console.log('Se va a quitar una fila');
    fila = $(this).closest("tr");
    fila.remove();
    tabla = $('#box-table-a tbody').html();


    dataset1.clear();
    dataset1.destroy();
    $('#box-table-a tbody').html(tabla);
    itemsAgregados--;
    //items--;

    var indice = $('.consecutivo').length;
    var auxIndice = indice;
    console.log('Elementos: '+indice);
    $('.consecutivo').each(function(){
      $(this).text(indice);
      indice--;
    });

    if(auxIndice == 0){
      itemsAgregados=0;
      items=0;
      //alert('Tabla sin elementos.');
      tabla = $('#box-table-a tbody').html(filaVacia);
    }





    reiniciarTabla();

});

}




















$(document).ready(function() {













    //Al escribr dentro del input con id="service"
    $('#nombre').keyup(function(){
        //Obtenemos el value del input
        var nombre = $(this).val();
        var tipo = $('#tipo').val();
        if(tipo != ''){
          var datos = { "nombre":nombre,"tipo":tipo };



          $.ajax({
            type: "POST",
            url: "functions/fn_movimiento_buscar_tercero.php",
            data: datos,
            beforeSend: function(){
              $('#suggestionsContenedor').html(rotador);
              $('#suggestions').fadeIn(500);

            },
            success: function(data) {
              //Escribimos las sugerencias que nos manda la consulta
              $('#suggestionsContenedor').html(data);







              $('#suggestions tbody tr').click(function(){

                var idTercero = $(".idTercero",this).val();
                var nombreTercero = $(".nombreTercero",this).val();
                console.log(idTercero+' '+nombreTercero);
                $('#nombre').val(nombreTercero);
                $('#nitcc').val(idTercero);



                $('#suggestions').fadeOut(500);


              });

              $('#suggestions .cerrarSugerencia i').click(function(){
                $('#nitcc').val('');
              $('#suggestions').fadeOut(500);
              });




              /*
              //Al hacer click en algua de las sugerencias
              $('.suggest-element').live('click', function(){
              //Obtenemos la id unica de la sugerencia pulsada
              var id = $(this).attr('id');
              //Editamos el valor del input con data de la sugerencia pulsada
              $('#service').val($('#'+id).attr('data'));
              //Hacemos desaparecer el resto de sugerencias
              $('#suggestions').fadeOut(1000);
            });
            */


          }
        });


      }// Termina el if que valida que el tipo no este vacio
      else{
        $('#nombre').val('');
        alert('Debe seleccionar primero un tipo de movimiento.');
      }




    });






    $('#nitcc').keyup(function(){
        var nitcc = $(this).val();
        var tipo = $('#tipo').val();
        if(tipo != ''){
          var datos = { "nitcc":nitcc,"tipo":tipo };
          $.ajax({
            type: "POST",
            url: "functions/fn_movimiento_buscar_tercero.php",
            data: datos,
            beforeSend: function(){
              $('#suggestionsContenedorn').html(rotador);
              $('#suggestionsn').fadeIn(500);
            },
            success: function(data) {
              //Escribimos las sugerencias que nos manda la consulta
              $('#suggestionsContenedorn').html(data);
              $('#suggestionsn tbody tr').click(function(){

                var idTercero = $(".idTercero",this).val();
                var nombreTercero = $(".nombreTercero",this).val();
                console.log(idTercero+' '+nombreTercero);
                $('#nombre').val(nombreTercero);
                $('#nitcc').val(idTercero);



                $('#suggestionsn').fadeOut(500);


              });

              $('#suggestionsn .cerrarSugerencia i').click(function(){

                  $('#nombre').val('');
              $('#suggestionsn').fadeOut(500);
              });


          }//Termina el succesa
        });


      }// Termina el if que valida que el tipo no este vacio
      else{
        $('#nitcc').val('');
        alert('Debe seleccionar primero un tipo de movimiento.');
      }
    });




    $('#codigoProducto').keyup(function(){
        var codigoProducto = $(this).val();
        var bodegaOrigen = $('#bodegaOrigen').val();
        var bodegaDestino = $('#bodegaDestino').val();
        var datos = {
          "codigoProducto":codigoProducto,
          "bodegaOrigen":bodegaOrigen,
          "bodegaDestino":bodegaDestino
        };
          $.ajax({
            type: "POST",
            url: "functions/fn_movimiento_buscar_producto.php",
            data: datos,
            beforeSend: function(){
              $('#suggestionsContenedorCP').html(rotador);
              $('#suggestionsCP').fadeIn(500);
            },
            success: function(data) {
              //Escribimos las sugerencias que nos manda la consulta
              $('#suggestionsContenedorCP').html(data);
              $('#suggestionsCP tbody tr').click(function(){
                var codigoS = $(".codigoS",this).val();
                var codigoBarrasS = $(".codigoBarrasS",this).val();
                var descripcionS = $(".descripcionS",this).val();

                console.log(codigoS+' '+codigoBarrasS);
                $('#codigoProducto').val(codigoS);
                $('#codigoBarras').val(codigoBarrasS);
                $('#descripcionProducto').val(descripcionS);
                $('#suggestionsCP').fadeOut(500);
              });

              $('#suggestionsCP .cerrarSugerencia i').click(function(){

                  $('#codigoProducto').val('');
              $('#suggestionsCP').fadeOut(500);
              });


          }//Termina el succes
        });



    });




    $('#descripcionProducto').keyup(function(){
        var descripcionProducto = $(this).val();
        var bodegaOrigen = $('#bodegaOrigen').val();
        var bodegaDestino = $('#bodegaDestino').val();
        var datos = {
          "descripcionProducto":descripcionProducto,
          "bodegaOrigen":bodegaOrigen,
          "bodegaDestino":bodegaDestino
        };
          $.ajax({
            type: "POST",
            url: "functions/fn_movimiento_buscar_producto.php",
            data: datos,
            beforeSend: function(){
              $('#suggestionsContenedorCB').html(rotador);
              $('#suggestionsCB').fadeIn(500);
            },
            success: function(data) {
              //Escribimos las sugerencias que nos manda la consulta
              $('#suggestionsContenedorCB').html(data);

              $('#suggestionsCB tbody tr').click(function(){
                var codigoS = $(".codigoS",this).val();
                var codigoBarrasS = $(".codigoBarrasS",this).val();
                var descripcionS = $(".descripcionS",this).val();

                console.log(codigoS+' '+codigoBarrasS);

                $('#codigoProducto').val(codigoS);
                $('#codigoBarras').val(codigoBarrasS);
                $('#descripcionProducto').val(descripcionS);
                $('#suggestionsCB').fadeOut(500);
              });

              $('#suggestionsCB .cerrarSugerencia i').click(function(){

                  $('#codigoProducto').val('');
              $('#suggestionsCB').fadeOut(500);
              });


          }//Termina el succes
        });



    });































});//Terminan las funciones que se ejecutan con el document ready















function buscar_tipo_movimiento(){
  console.log('Buscar tipo de movimiento');
  var documento = $('#documento').val();
  var datos = { "documento":documento };
  $.ajax({
    url:'functions/fn_movimiento_buscar_tipo_movimiento.php',
    type:'post',
    dataType:'html',
    data:datos,
    beforeSend: function(){ },
    success:function(response){
      $('#tipo').html(response);
    }
  })
  .done(function(data){ })
  .fail(function(){ })
  .always(function(){ });


  // Codigo para el tratamiento de las bodegas cuando se cambia el tipo de documento.
  console.log("Se cambió el tipo de documento");
  console.log($('#documento').val());

  var documento = $('#documento').val();

  //Procedimiento si es una entrada
  if(documento == 3){

    $('.bodegaDestino').each(function(){
      $(this).attr('disabled',false);
    });

    $('.bodegaOrigen').each(function(){
      $(this).val('');
      $(this).attr('disabled',true);
    });
  }
  //Procedimiento si es una salida
  if(documento == 9){

    $('.bodegaOrigen').each(function(){
      $(this).attr('disabled',false);
    });

    $('.bodegaDestino').each(function(){
      $(this).val('');
      $(this).attr('disabled',true);
    });
  }
  //Procedimiento si es diferente de entrada y salida
  if(documento != 3 && documento != 9){
    $('.bodegaOrigen').each(function(){
      $(this).attr('disabled',false);
    });

    $('.bodegaDestino').each(function(){
      $(this).attr('disabled',false);
    });
  }



/*
  $('.consecutivo').each(function(){
    $(this).text(indice);
    indice--;
  });

  */

}




function listado(a){
  // a = 1 => Terceros
  // a = 2 => Productos
  bandera = 0;
  busqueda = a;
  var tipo = $('#tipo').val();

  var contenido = '<span class="listadoLoading"><i class="fa fa-cog fa-spin fa-3x fa-fw"></i> <span class="sr-only">Loading...</span></span>';
  contenido = contenido +'<span class="listadoCerrar"><i class="fa fa-times" aria-hidden="true"></i></span>';





if(busqueda == 1 && tipo == '' && bandera == 0){
  bandera++;
  alert('Debe seleccionar primero un tipo de movimiento.');
}




  var datos = { "busqueda":busqueda, "tipo":tipo };



if(bandera == 0){
  $('.listadoCuerpo').html(contenido);
  $('.listadoFondo').fadeIn();
  $.ajax({
    url:'functions/fn_movimiento_buscar_listado.php',
    type:'post',
    dataType:'html',
    data:datos,
    beforeSend: function(){ },
    success:function(response){
      $('.listadoCuerpo').html(response);



      datasetListado = $('#box-table-listado').dataTable({
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
      var anchoTabla = $('#box-table-listado').width();
      var anchoTabla = anchoTabla-8;
      $('.listadoCuerpo .fg-toolbar').css({ 'width': anchoTabla });
      $( window ).resize(function(){
        var anchoTabla = $('#box-table-listado').width();
        var anchoTabla = anchoTabla-8;
        $('.listadoCuerpo .fg-toolbar').css({ 'width': anchoTabla }); }
      );

      if(busqueda == 1){
        $('.listadoCuerpo tbody tr').click(function(){
          var idTercero = $(".idTercero",this).val();
          var nombreTercero = $(".nombreTercero",this).val();
          console.log(idTercero+' '+nombreTercero);
          $('#nombre').val(nombreTercero);
          $('#nitcc').val(idTercero);
          if(typeof datasetListado != 'undefined'){
            setTimeout( function(){ datasetListado.fnClearTable();
              datasetListado.fnDestroy();
            }, 1000);
          }
          $('.listadoFondo').fadeOut();
        });
      } else if(busqueda == 2){
        $('.listadoCuerpo tbody tr').click(function(){
          var codigoS = $(".codigoS",this).val();
          var codigoBarrasS = $(".codigoBarrasS",this).val();
          var descripcionS = $(".descripcionS",this).val();

          console.log(codigoS+' '+codigoBarrasS);
          $('#codigoProducto').val(codigoS);
          $('#codigoBarras').val(codigoBarrasS);
          $('#descripcionProducto').val(descripcionS);
          if(typeof datasetListado != 'undefined'){
            setTimeout( function(){ datasetListado.fnClearTable();
              datasetListado.fnDestroy();
            }, 1000);
          }
          $('.listadoFondo').fadeOut();
        });
      }












      $('.listadoCerrar').click(function(){

        $('.listadoFondo').fadeOut();
        if(typeof datasetListado != 'undefined'){
        setTimeout( function(){ datasetListado.fnClearTable();
          datasetListado.fnDestroy();
        }, 1000);

        }



      });



    }// Termina el success
  })
  .done(function(data){ })
  .fail(function(){ })
  .always(function(){ });
}else{
  /*
  // Else de si la bandera es igual a cero
  $('.listadoCuerpo').html(contenido);
  $('.listadoFondo').fadeIn();
  $('.listadoCerrar').click(function(){ $('.listadoFondo').fadeOut(); });
  */
}












}
