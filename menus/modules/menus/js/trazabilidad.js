$(document).ready(function(){
  var mes = $('#mesi').val();
  var mesText = $("#mesi option[value='"+mes+"']").text()
  $('#mesfText').val(mesText);
  $('#mesf').val(mes);
});



function mesFinal(){
  var mes = $('#mesi').val();
  var mesText = $("#mesi option[value='"+mes+"']").text()
  $('#mesfText').val(mesText);
  $('#mesf').val(mes);
}

function trazabilidad(){
 $('#formTrazabilidad').submit();
}

function consultarTrazabilidad(){
  console.log("Consultar Trazabilidad");
	$('#consultar').val('1');
	$('#formTrazabilidad').submit();
}

function buscarProveedorResponsable(){
  var tipoDocumento = $('#tipoDocumento').val();
  var datos = {"tipoDocumento":tipoDocumento};
  $.ajax({
    type: "POST",
    url: "functions/fn_trazabilidad_buscar_proveedor_responsable.php",
    data: datos,
    beforeSend: function(){},
    success: function(data){
      //$('#debug').html(data);
      $('#proveedor').html(data);
    }
  });
}

function buscarBodegas(){
  var proveedor = $('#proveedor').val();
  var datos = {"proveedor":proveedor};

  $.ajax({
    type: "POST",
    url: "functions/fn_trazabilidad_buscar_bodega_origen.php",
    data: datos,
    beforeSend: function(){},
    success: function(data){
      //$('#debug').html(data);
      $('#bodegad').html(data);
    }
  });
  

  $.ajax({
    type: "POST",
    url: "functions/fn_trazabilidad_buscar_bodega_destino.php",
    data: datos,
    beforeSend: function(){},
    success: function(data){
      //$('#debug').html(data);
      $('#bodegao').html(data);
    }
  });
}
