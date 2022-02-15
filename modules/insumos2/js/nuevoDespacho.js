
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
	    "timeOut": "6000",
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

	$('.select2').select2();
	buscarProveedores();
	$('#tipo_despacho').on('change', function(){
		var tipoDespacho = $('#tipo_despacho').val();
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

	$('#proveedor').on('change', function(){
		var tipoDespacho = $('#tipo_despacho').val();
		var proveedor = $('#proveedor').val();
		$.ajax({
			url: 'functions/fn_insumos_obtener_bodega_origen.php',
			type: 'POST',
			data: {tipoDespacho : tipoDespacho, proveedor : proveedor},
			beforeSend: function(){ $('#loader').fadeIn(); },
		})
		.done(function(data) {
			$('#bodegaOrigen').html(data);
		})
		.fail(function() {
			console.log("error");
		})
		.always(function() {
			$('#loader').fadeOut(); 
		});
		
	});

	buscarInstituciones();
	$('#municipio').on('change', function(){ buscarInstituciones($('#municipio').val()); });

	var institucion = $('#institucion_desp').val();
	if (institucion != "") {buscarSedes(institucion); }
	$('#institucion_desp').on('change', function(){ buscarSedes($('#institucion_desp').val()); });

	buscarPorRuta();
	$('#rutas').on('change', function() { buscarPorRuta(); });


	if (numProductoInicial == 0 || numProductoInicial == "" || numProductoInicial == null) {
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

    $('#mes').on('change', function(){
      $('#table tbody tr').remove(); 
      $('#table tbody p').remove();
    });

    $('#complemento').on('change', function(){
      $('#table tbody tr').remove(); 
      $('#table tbody p').remove();
    });

    $('#manipuladoras').on('change', function(){
      $('#table tbody tr').remove(); 
      $('#table tbody p').remove();
    });

});

function buscarProveedores () {
	var tipoDocumento = $('#tipoDocumento').val();
	var responsable = $('#responsable').val();
    $.ajax({
        url: 'functions/fn_insumos_obtener_responsables.php',
        type: 'POST',
        data: {tipoDocumento : tipoDocumento, responsable : responsable},
        beforeSend: function(){ $('#loader').fadeIn(); },
    })
   	.done(function(data) {
        $('#responsable').html(data);
    })
    .fail(function() {
        console.log("error");
    })
    .always(function() {
       $('#loader').fadeOut(); 
    });  
}


function buscarInstituciones () {
	var municipio = $('#municipio').val();
	var institucion = $('#institucion_desp').val();
	$.ajax({
		url: 'functions/fn_insumos_obtener_instituciones.php',
		type: 'POST',
		data: {municipio : municipio, institucion : institucion},
		beforeSend: function(){ $('#loader').fadeIn(); },
	})
	.done(function(data) {
		$('#institucion_desp').html(data);
	})
	.fail(function() {
		console.log("error");
	})
	.always(function() {
		$('#loader').fadeOut(); 
	});
}


function buscarSedes (institucion) {
	$.ajax({
		url: 'functions/fn_insumos_obtener_sedes.php',
		type: 'POST',
		data: {institucion : institucion},
		beforeSend: function(){ $('#loader').fadeIn(); },
	})
	.done(function(data) {
		$('#sede').html(data);
	})
	.fail(function() {
		console.log("error");
	})
	.always(function() {
		$('#loader').fadeOut(); 
	});	
}

function añadirSedes(){
  	mes = $('#mes option:selected').val();
  	complemento = $('#complemento option:selected').val();
    manipuladoras  = $('#manipuladoras option:selected').val();
    nom_municipio = $('#municipio option:selected').val();
    institucion = $('#institucion_desp option:selected').val();
    sede = $('#sede option:selected').val();
    ruta = $('#rutas option:selected').val();
    if (mes == "") {
    	Command: toastr.warning("Debe seleccionar un mes para despachar.", "No hay mes seleccionado.", {onHidden : function(){}});
    	$('#mes').focus();
    }
    if (complemento == "") {
    	Command: toastr.warning("Debe seleccionar un complemento para despachar.", "No hay complemento seleccionado.", {onHidden : function(){}});
    	$('#complemento').focus();
    }
    if (nom_municipio == "" && ruta == "") {
    	Command: toastr.warning("Debe seleccionar un municipio para despachar.", "No hay municipio seleccionado.", {onHidden : function(){}});
    	$('#municipio').focus();
    } 
    if ((mes != "" && complemento != "" && nom_municipio != "") || ruta != "") {
    	$.ajax({
       		type: "POST",
       		url: "functions/fn_insumos_armar_tabla.php",
       		data: { "mes" : mes,
       				"complemento" : complemento,
              "manipuladoras" : manipuladoras,
       				"municipio" : nom_municipio,
       				"institucion" : institucion,
       				"sede" : sede,
       				"ruta" : ruta
       		},
       		beforeSend: function(){ $('#loader').fadeIn();},
      		success: function(data){
        		$('#tbodySedesDespachos').append(data);
        		$('#loader').fadeOut();
       		}
    	});
    }
}

function buscarPorRuta(){
	var ruta = $('#rutas').val();
	if (ruta != "") {
		$('#municipio').prop('selectedIndex', '0');
		$('#municipio').prop('disabled', true);
		$('#institucion_desp').prop('selectedIndex', '0');
		$('#institucion_desp').prop('disabled', true);
		$('#sede').prop('selectedIndex', '0');
		$('#sede').prop('disabled', true);
	}else{
		$('#municipio').prop('disabled', false);
		$('#institucion_desp').prop('disabled', false);
		$('#sede').prop('disabled', false);
	}
}

function seleccionarTodos(check){
  if ($(check).prop('checked')==true) {
    $('.checkInst').each(function(){
      $(this).prop('checked', true);
    });
  } else if ($(check).prop('checked')==false){
     $('.checkInst').each(function(){
      $(this).prop('checked', false);
    });
  }
}

function eliminarSedes(){
  $('.checkInst:checked').each(function(){
      $(this).parents('tr').remove();
    });

  $('#seleccionar_todos').prop('checked', false);
}

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

function submitDespacho(){

    mesErr = "";
    sedes = [];
    productos = [];
    valida = 0;

    $('#formDespachoInsumo').valid();

    $('input[name="sede[]"]').each(function(){
        if ($(this).prop('checked')) {
          sedes.push($(this).val());
        }
    });

    $('select[name="productoDespacho[]"]').each(function(){
        productos.push($(this).val());
    });

    $('input[name="sede[]"]').each(function(){
        if ($(this).prop('checked')) {
          valida++;
        }
    });

    if (valida > 0) {
    	if ($('#formDespachoInsumo').valid()) {
    		$.ajax({
    			url: 'functions/fn_insumos_validar_despacho.php',
    			type: 'POST',
    			data: {sedes: JSON.stringify(sedes), productos: JSON.stringify(productos), mes: $('#mes').val(), complemento: $('#complemento').val()},
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
    					url: 'functions/fn_insumos_generar_despacho.php',
    					type: 'POST',
    					data: datos,
              beforeSend: function(){ $('#loader').fadeIn(); },
    				})
    				.done(function(data) {
              console.log(data);
    					if (data == "1") {
    						Command: toastr.success('Despachado con éxito.','El despacho se registró con éxito.',{onHidden : function(){ location.href='despachos.php';}});
    					} else if (data != "1") {
                data = JSON.parse(data);
                console.log(data);
                Command: toastr.warning('La sede ' + data.sede + ' tiene un despacho registrado con el complemento ' + data.complemento,'El despacho No se registró con éxito.',{onHidden : function(){}});
              }
    				})
    				.fail(function() {
    					console.log("error");
    				})
    				.always(function() {
            $('#loader').fadeOut();
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
    }else {
      Command: toastr.warning('Advertencia','Debe seleccionar una sede.',{onHidden : function(){}});
    }
}

