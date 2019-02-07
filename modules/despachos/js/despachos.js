$(document).ready(function(){
	var mes = $('#mesi').val();
	var mesText = $("#mesi option[value='"+mes+"']").text()
	$('#mesfText').val(mesText);
	$('#mesf').val(mes);

	$('#seleccionarVarios').change(function(){
		console.log('Cambio el de varios');
		if ($('#seleccionarVarios').is(':checked')) {
			$('tbody input[type=checkbox]').prop( "checked", true );
		}
		else{
			$('tbody input[type=checkbox]').prop( "checked", false );
		}
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


$( "#btnBuscar" ).click(function(){
  console.log('Se va  a hacer una busqueda.');
  $("#pb_annoi").val($("#annoi").val());
  $("#pb_mesi").val($("#mesi").val());
  $("#pb_diai").val($("#diai").val());
  $("#pb_annof").val($("#annof").val());
  $("#pb_mesf").val($("#mesf").val());
  $("#pb_diaf").val($("#diaf").val());
  $("#pb_tipo").val($("#tipo").val());
  $("#pb_municipio").val($("#municipio").val());
  $("#pb_institucion").val($("#institucion").val());
  $("#pb_sede").val($("#sede").val());
  $("#pb_tipoDespacho").val($("#tipoDespacho").val());
  $("#pb_ruta").val($("#ruta").val());
  $("#pb_btnBuscar").val(1);
  $("#parametrosBusqueda").submit();
});






});







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


function mesFinal(){
  var mes = $('#mesi').val();
  var mesText = $("#mesi option[value='"+mes+"']").text()
  $('#mesfText').val(mesText);
  $('#mesf').val(mes);
}


function despachos_kardex(){
  //Contando los elementos checked
  var cant = 0;
  var despacho = 0;
  var semana = 0;
  var tipo = '';
  var bandera = 0;
  $("tbody input:checked").each(function(){
    if(bandera == 0){
      cant++;
      despacho = $(this).val();

      if(bandera == 0){
        if(semana == 0){
          semana = $("#semana_"+despacho).val();
        }
        else{
          if(semana != $("#semana_"+despacho).val()){
            bandera++;
            alert('Los despachos seleccionados deben ser de la misma semana');

          }
        }
      }

      if(bandera == 0){
        if(tipo == ''){
          tipo = $("#tipo_"+despacho).val();
        }
        else{
          if(tipo != $("#tipo_"+despacho).val()){
            bandera++;
            alert('Los despachos seleccionados deben ser del mismo tipo de ración');

          }
        }
      }
    }
  });

  if(cant == 0){
    alert('Debe seleccionar al menos un despacho para continuar');
    bandera++;
  }

  if(bandera == 0){
    $( ".soloJs" ).remove();
    console.log('Se van a mostrar los despachos por sede');

    $('#formDespachos').attr('action', 'despacho_kardex3.php');
    $('#formDespachos').attr('method', 'post');
    $('#formDespachos').submit();
    $('#formDespachos').attr('method', 'get');
  }
}

function despachos_kardex_multiple(){
  //Contando los elementos checked
  var cant = 0;
  var despacho = 0;
  var semana = 0;
  var tipo = '';
  var bandera = 0;
  $("tbody input:checked").each(function(){
    if(bandera == 0){
      cant++;
      despacho = $(this).val();

      if(bandera == 0){
        if(semana == 0){
          semana = $("#semana_"+despacho).val();
        }
        else{
          if(semana != $("#semana_"+despacho).val()){
            bandera++;
            alert('Los despachos seleccionados deben ser de la misma semana');

          }
        }
      }

      if(bandera == 0){
        if(tipo == ''){
          tipo = $("#tipo_"+despacho).val();
        }
        else{
          if(tipo != $("#tipo_"+despacho).val()){
            bandera++;
            alert('Los despachos seleccionados deben ser del mismo tipo de ración');
          }
        }
      }
    }
  });

  if(cant == 0){
    alert('Debe seleccionar al menos un despacho para continuar');
    bandera++;
  }

  if(bandera == 0){
    $( ".soloJs" ).remove();
    console.log('Se van a mostrar los despachos por sede');

    $('#formDespachos').attr('action', 'despacho_kardex4_multiple.php');
    $('#formDespachos').attr('method', 'post');
    $('#formDespachos').submit();
    $('#formDespachos').attr('method', 'get');
  }
}

function despachos_kardex2(){
  //Contando los elementos checked
  var cant = 0;
  var despacho = 0;
  var semana = 0;
  var tipo = '';
  var bandera = 0;
  $("tbody input:checked").each(function(){
    if(bandera == 0){
      cant++;
      despacho = $(this).val();

      if(bandera == 0){
        if(semana == 0){
          semana = $("#semana_"+despacho).val();
        }
        else{
          if(semana != $("#semana_"+despacho).val()){
            bandera++;
            alert('Los despachos seleccionados deben ser de la misma semana');

          }
        }
      }

      if(bandera == 0){
        if(tipo == ''){
          tipo = $("#tipo_"+despacho).val();
        }
        else{
          if(tipo != $("#tipo_"+despacho).val()){
            bandera++;
            alert('Los despachos seleccionados deben ser del mismo tipo de ración');

          }
        }
      }
    }
  });

  if(cant == 0){
    alert('Debe seleccionar al menos un despacho para continuar');
    bandera++;
  }

  if(bandera == 0){
    $( ".soloJs" ).remove();
    console.log('Se van a mostrar los despachos por sede');

    $('#formDespachos').attr('action', 'despacho_kardex2.php');








    $('#formDespachos').attr('method', 'post');
    $('#formDespachos').submit();
    $('#formDespachos').attr('method', 'get');
  }
}

function despachos_mixta(){
  //Contando los elementos checked
  var cant = 0;
  var despacho = 0;
  var semana = 0;
  var tipo = '';
  var bandera = 0;
  $("tbody input:checked").each(function(){
    if(bandera == 0){
      cant++;
      despacho = $(this).val();

      if(bandera == 0){
        if(semana == 0){
          semana = $("#semana_"+despacho).val();
        }
        else{
          if(semana != $("#semana_"+despacho).val()){
            bandera++;
            alert('Los despachos seleccionados deben ser de la misma semana');

          }
        }
      }

      if(bandera == 0){
        if(tipo == ''){
          tipo = $("#tipo_"+despacho).val();
        }
        else{
          if(tipo != $("#tipo_"+despacho).val()){
            bandera++;
            alert('Los despachos seleccionados deben ser del mismo tipo de ración');

          }
        }
      }
    }
  });

  if(cant == 0){
    alert('Debe seleccionar al menos un despacho para continuar');
    bandera++;
  }

  if(bandera == 0){
    $( ".soloJs" ).remove();
    console.log('Se van a mostrar los despachos por sede');

    $('#formDespachos').attr('action', 'despacho_mixta.php');








    $('#formDespachos').attr('method', 'post');
    $('#formDespachos').submit();
    $('#formDespachos').attr('method', 'get');
  }
}



function despachos_por_sede_fecha_lote(){

  //Contando los elementos checked
  var cant = 0;
  var despacho = 0;
  var semana = 0;
  var tipo = '';
  var bandera = 0;


  $("tbody input:checked").each(function(){
    if(bandera == 0){
      cant++;
      despacho = $(this).val();
      if(2 != $("#estado_"+despacho).val()){
        bandera++;
        alert('Solo se pueden agregar lotes y fechas de vencimiento a despachos en estado Pendiente.');
        return false;
      }
      if(cant > 1){
        alert('Debe seleccionar solo un despacho para agregar lotes y fechas de vencimiento');
        bandera++;
        return false;
      }
    }
  }); // Termina de revisar cada uno de los elementos que se encuentren checkeados.








  $("tbody input:checked").each(function(){
    if(bandera == 0){
      cant++;
      despacho = $(this).val();

      if(bandera == 0){
        if(semana == 0){
          semana = $("#semana_"+despacho).val();
        }
        else{
          if(semana != $("#semana_"+despacho).val()){
            bandera++;
            alert('Los despachos seleccionados deben ser de la misma semana');

          }
        }
      }

      if(bandera == 0){
        if(tipo == ''){
          tipo = $("#tipo_"+despacho).val();
        }
        else{
          if(tipo != $("#tipo_"+despacho).val()){
            bandera++;
            alert('Los despachos seleccionados deben ser del mismo tipo de ración');

          }
        }
      }
    }
  });

  if(cant == 0){
    alert('Debe seleccionar al menos un despacho para continuar');
    bandera++;
  }

  if(bandera == 0){
    $( ".soloJs" ).remove();
    console.log('Se van a mostrar los despachos por sede');
    $('#formDespachos').attr('action', 'despacho_por_sede_fecha_lote.php');

    $('#formDespachos').attr('method', 'post');
    $('#formDespachos').submit();
    $('#formDespachos').attr('method', 'get');







  }
}




function despachos_por_sede(){
  //Contando los elementos checked
  var cant = 0;
  var despacho = 0;
  var semana = 0;
  var tipo = '';
  var bandera = 0;
  $("tbody input:checked").each(function(){
    if(bandera == 0){
      cant++;
      despacho = $(this).val();

      if(bandera == 0){
        if(semana == 0){
          semana = $("#semana_"+despacho).val();
        }
        else{
          if(semana != $("#semana_"+despacho).val()){
            bandera++;
            alert('Los despachos seleccionados deben ser de la misma semana');

          }
        }
      }

      if(bandera == 0){
        if(tipo == ''){
          tipo = $("#tipo_"+despacho).val();
        }
        else{
          if(tipo != $("#tipo_"+despacho).val()){
            bandera++;
            alert('Los despachos seleccionados deben ser del mismo tipo de ración');

          }
        }
      }
    }
  });

  if(cant == 0){
    alert('Debe seleccionar al menos un despacho para continuar');
    bandera++;
  }

  if(bandera == 0){
    $( ".soloJs" ).remove();
    console.log('Se van a mostrar los despachos por sede');

    $('#formDespachos').attr('action', 'despacho_por_sede.php');








    $('#formDespachos').attr('method', 'post');
    $('#formDespachos').submit();
    $('#formDespachos').attr('method', 'get');
  }
}


function despachoPorSede(despacho){
  console.log('Click en una fila.');
  despacho = despacho;
  estado = $("#estado_"+despacho).val();
  console.log("Estado del despacho: "+estado);
  if(estado != 0){
    $( ".soloJs" ).remove();
    var mesI = $('#mesi').val();
    var annoI = $('#annoi').val();
    $('#despachoAnnoI').val(annoI);
    $('#despachoMesI').val(mesI);
    $('#despacho').val(despacho);
    $('#formDespachoPorSede').submit();
  }
}





function despachos_consolidado(){
  //Contando los elementos checked
  var cant = 0;
  var despacho = 0;
  var semana = 0;
  var tipo = '';
  var bandera = 0;
  $("tbody input:checked").each(function(){
    if(bandera == 0){
      cant++;
      despacho = $(this).val();

      if(bandera == 0){
        if(semana == 0){
          semana = $("#semana_"+despacho).val();
        }
        else{
          if(semana != $("#semana_"+despacho).val()){
            bandera++;
            alert('Los despachos seleccionados deben ser de la misma semana');

          }
        }
      }

      if(bandera == 0){
        if(tipo == ''){
          tipo = $("#tipo_"+despacho).val();
        }
        else{
          if(tipo != $("#tipo_"+despacho).val()){
            bandera++;
            alert('Los despachos seleccionados deben ser del mismo tipo de ración');

          }
        }
      }
    }
  });

  if(cant == 0){
    alert('Debe seleccionar al menos un despacho para continuar');
    bandera++;
  }

  if(bandera == 0){
    $( ".soloJs" ).remove();
    console.log('Se van a mostrar los despachos consolidados');


    var rutaSeleccionada = $('#ruta option:selected').text();
    $('#rutaNm').val(rutaSeleccionada);


    $('#formDespachos').attr('action', 'despacho_consolidado.php');
    $('#formDespachos').attr('method', 'post');
    $('#formDespachos').submit();
    $('#formDespachos').attr('method', 'get');
  }



}




function editar_despacho(){
  console.log('Editar despacho.');




  //Contando los elementos checked
  var cant = 0;
  var despacho = 0;
  var tipo = '';
  var estado = '';
  var bandera = 0;
  $("tbody input:checked").each(function(){
    if(bandera == 0){
      cant++;
      despacho = $(this).val();
      if(2 != $("#estado_"+despacho).val()){
        bandera++;
        alert('Solo se pueden editar despachos en estado Pendiente.');
        return false;
      }
      if(cant > 1){
        alert('Debe seleccionar solo un despacho para editar');
        bandera++;
        return false;
      }
    }
  }); // Termina de revisar cada uno de los elementos que se encuentren checkeados.

  if(cant == 0){
    alert('Debe seleccionar al menos un despacho para editar');
    bandera++;
  }

  if(bandera == 0){
    $( ".soloJs" ).remove();
    console.log('Se va a editar el despacho.');
    $('#formDespachos').attr('action', 'despacho_editar.php');
    $('#formDespachos').attr('method', 'post');
    $('#formDespachos').attr('target', '_self');
    $('#formDespachos').submit();
    $('#formDespachos').attr('method', 'get');
  }// Termina el if si la bandera esta en cero
}

function eliminar_despacho(){
  console.log('Se va a eliminar un despacho');


  //Contando los elementos checked
  var cant = 0;
  var despacho = 0;
  var tipo = '';
  var estado = '';
  var bandera = 0;
  $("tbody input:checked").each(function(){
    if(bandera == 0){
      cant++;
      despacho = $(this).val();
      if(2 != $("#estado_"+despacho).val()){
        bandera++;
        alert('Solo se pueden eliminar despachos en estado Pendiente.');
        return false;
      }
      if(cant > 1){
        alert('Debe seleccionar solo un despacho para eliminar');
        bandera++;
        return false;
      }
    }
  }); // Termina de revisar cada uno de los elementos que se encuentren checkeados.

  if(cant == 0){
    alert('Debe seleccionar al menos un despacho para eliminar');
    bandera++;
  }

  if(bandera == 0){
    var r = confirm("Confirma que desea eliminar este registro.");
    if (r == true) {
      // Se va agregar el año y el mes para hacer la eliminación en la tabla correspondiente
      var annoi = $('#annoi').val();
      var mesi = $('#mesi').val();


      // Se va a envíar la variable despacho para iniciar el procesos de eliminación.
      var datos = {"despacho":despacho,"annoi":annoi,"mesi":mesi};
      $.ajax({
        type: "POST",
        url: "functions/fn_despacho_eliminar.php",
        data: datos,
        beforeSend: function(){
          $('#loader').fadeIn();
        },
        success: function(data){
          $('#debug').html(data);
          console.log('Resultado de la eliminación');
          console.log(data);
          if(data == 1){
            alert('Se ha eliminado con éxito el despacho.');
            location.reload();
          }
          //$('#municipio').html(data);
        }
      })
      .done(function(){ })
      .fail(function(){ })
      .always(function(){
        $('#loader').fadeOut();
      });
    }
  }// Termina el if si la bandera esta en cero
}// Termina la función para eliminar despachos.











var cantidadDetallados = 0;

$(document).ready( function () {
  dataset1 = $('#box-table-movimientos').DataTable({
    order: [ 1, 'desc' ],
    pageLength: 25,
    lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "TODO"]],
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
});













function despachos_agrupados(){
  var cant = 0;
  var despacho = 0;
  var sede = 0;
  var tipo = '';
  var tipodespacho = 0;
  var bandera = 0;
  $("tbody input:checked").each(function(){








    if(bandera == 0){
      cant++;
      despacho = $(this).val();
      console.log($("#tipodespacho_"+despacho).val());

      if(bandera == 0){
        if(sede == 0){
          sede = $("#cod_sede_"+despacho).val();
        }
        else{
          if(sede != $("#cod_sede_"+despacho).val()){
            bandera++;
            alert('Los despachos seleccionados deben ser de la misma sede');

          }
        }
      }

      if(bandera == 0){
        if(tipo == ''){
          tipo = $("#tipo_"+despacho).val();
        }
        else{
          if(tipo != $("#tipo_"+despacho).val()){
            bandera++;
            alert('Los despachos seleccionados deben ser del mismo tipo de ración');

          }
        }
      }


      if(bandera == 0){
        if(tipodespacho == 0){
          tipodespacho = $("#tipodespacho_"+despacho).val();
        }
        else{
          if(tipodespacho != $("#tipodespacho_"+despacho).val()){
            bandera++;
            alert('Los despachos seleccionados deben ser del mismo tipo de despacho');

          }
        }
      }




    }
  });

  if(cant == 0){
    alert('Debe seleccionar al menos un despacho para continuar');
    bandera++;
  }

  if(bandera == 0){
    $( ".soloJs" ).remove();
    console.log('Se van a mostrar los despachos agrupados');
    $('#formDespachos').attr('action', 'despacho_agrupado.php');
    $('#formDespachos').submit();

  }
}
