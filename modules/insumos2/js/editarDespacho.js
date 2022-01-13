var numProducto = $('.productodesp').length;
var numProductoInicial = $('.productodesp').length;

$(document).ready(function(){

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

    jQuery.extend(jQuery.validator.messages, {//Configuración jquery valid
      step : "Por favor, escribe un número entero",
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

	$('#tipoDespacho').on('change', function(){
		var tipoDespacho = $('#tipoDespacho').val();
 		$.ajax({
         	url: 'functions/fn_insumos_obtener_responsables.php',
         	type: 'POST',
         	data: {tipoDocumento : tipoDespacho},
         	beforeSend: function(){ $('#loader').fadeIn(); },
         })
         .done(function(data) {
         	$('#proveedor').html(data);
         })
         .fail(function() {
         	console.log("error");
         })
         .always(function() {
         	$('#loader').fadeOut(); 
         });	
	});

  $('#proveedor').on('change', function() {
    var proveedor = $('#proveedor').val();
    var tipoDespacho = $('#tipoDespacho').val();
    $.ajax({
      url: 'functions/fn_insumos_obtener_bodega_origen.php',
      type: 'POST',
      data: {tipoDespacho : tipoDespacho, proveedor : proveedor},
    })
    .done(function(data) {
      $('#bodega_origen').html(data);
    })
    .fail(function() {
      console.log("error");
    })
    .always(function() {
    });
  });;
});

function anadirProducto(){
  	numProducto++;
  	$.ajax({
   		type: "POST",
   		url: "functions/fn_insumos_obtener_productos_para_despacho.php",
   		data : {"num_producto" : numProducto},
   			beforeSend: function(){},
   			success: function(data){
    			$('#productosDesp').append(data);
    			$('.productodesp:last').select2();
   			}
  	});
}

function borrarProducto(){
  if (numProducto > numProductoInicial) {
    $('#producto_'+numProducto).remove();
    numProducto--;
  }
}


$('#modalEliminarProductoDespacho').on('show.bs.modal', function (event) {
  var button = $(event.relatedTarget);
      iddet = button.data('iddet');
      numdoc = button.data('numdoc');
      mestabla = button.data('mestabla');
      numdet = button.data('numdet');
      $('#id_det_despacho').val(iddet);
      $('#numdoc_eliminar_det').val(numdoc);
      $('#mes_tabla_eliminar_det').val(mestabla);
      $('#num_det').val(numdet);
      if ($('.productodesp').length == 1) {
        $('#mensajeConfirm').css('display','');
        $('#tipoCabecera').removeClass('text-info').addClass('text-warning');
        $('#tipoBoton').removeClass('btn-primary').addClass('btn-warning');
      }
});

function validaProductos(select, num){
  var produc = $(select).val();
  $('#descIns_'+num).val($("#"+$(select).prop('id')+" option:selected").text());

      cnt=0;
  $('.productodesp').each(function(){
    if ($(this).val() == produc) {
      cnt++;
    }
  });
  setTimeout(function() {
    if (cnt>1) {
      $(select).val('').focus();
      Command: toastr.warning("No puede escoger el mismo Insumo dos veces para el mismo despacho.", "Insumo escogido dos veces.", {onHidden : function(){ }})
    }
  }, 800);
}

function eliminarProductoDespacho(){
    $('#modalEliminarProductoDespacho').modal('hide');
    $('#loader').fadeIn();
    var iddet = $('#id_det_despacho').val();
       numdoc = $('#numdoc_eliminar_det').val();
       mestabla = $('#mes_tabla_eliminar_det').val();
       num_det = $('#num_det').val();
     $.ajax({
      type : "POST",
      url : "functions/fn_insumos_eliminar_producto_despacho.php",
      data : {"iddet" : iddet, "num_doc" : numdoc, "mestabla" : mestabla},
      success: function(data){
        if (data == "1") {
          if ($('.productodesp').length == 1) {
            location.href='despachos.php';
          } else {
            $('#producto_'+num_det).remove();
            $('#loader').fadeOut();
          }
        } else {
           Command: toastr.error("Hubo un error al tratar de eliminar el producto.", "Ocurrió un error.", {onHidden : function(){ console.log(data) }})
           $('#loader').fadeOut();
        }
      }
     });
}

function submitDespacho(){

    mesErr = "";
    productos = [];
    valida = 0;

    var sede = $('input[name="sede"]').val();
    $('select[name="productoDespacho[]"]').each(function(){
        productos.push($(this).val());
    });

    if (valida == 0) {
    	if ($('#formDespachoInsumo').valid()) {
    		$.ajax({
    			url: 'functions/fn_insumos_validar_despacho_editar.php',
    			type: 'POST',
    			data: {sedes: JSON.stringify(sede), productos: JSON.stringify(productos), mes: $('#mes').val(), complemento: $('#tipo_complemento').val()},
    			beforeSend: function(){ $('#loader').fadeIn(); },
    		})
    		.done(function(data) {
    			data = JSON.parse(data);
    			if (data.respuesta[0].respuesta == "0") {
    				btn = 	'<button type="button" class="close" onclick="$(\'#errDespachos\').fadeOut();" aria-label="Close">'+
                      			'<span aria-hidden="true">&times;</span>'+
                    		'</button>';
	               	Command: toastr.error("Se encontró una inconsistencia durante la validación del despacho.", "Inconsistencia encontrada.", {onHidden : function(){ $('#loader').fadeOut();}})
	               	$('#errDespachos').html(btn+"<b>Detalles de inconsistencia : </b> </br>"+data.respuesta[0].coincide);
	               	$('#errDespachos').show();
    			}else if (data.respuesta[0].respuesta == "1") {
    				Command: toastr.info("El proceso de validación y registro de despacho es un poco extenso, por favor espere.", "Aguarde un momento.", {onHidden : function(){}})
    				datos = $('#formDespachoInsumo').serialize();
    				$.ajax({
    					url: 'functions/fn_insumos_actualizar_despacho.php',
    					type: 'POST',
    					data: datos,
    				})
    				.done(function(data) {
    					if (data == "1") {
    						Command: toastr.success('Despachado con éxito.','El despacho se registró con éxito.',{onHidden : function(){ location.href='despachos.php';}});
    					}
    				})
    				.fail(function() {
    					console.log("error");
    				})
    				.always(function() {
    					// console.log("complete");
    				});
    				
    			}
    		})
    		.fail(function() {
    			console.log("error");
    		})
    		.always(function() {
    			$('#loader').fadeOut();
    		});
    		
    	}
    }
}
