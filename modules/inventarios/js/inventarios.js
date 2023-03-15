$(document).ready(function(){

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

	$('select').select2();	
    $(document).on('click', '#descargarPlantillaProductos', function(){ $('#ventanaFormularioDescargarCantidades').modal(); });
	$(document).on('click', '#descargarArchivoInventario', function(){ descarga_plantilla_productos(); })
	$(document).on('click', '#importarCantidades', function(){ $('#ventanaFormularioCargarCantidades').modal(); });
	$(document).on('click', '#subirArchivoInventario', function(){ subirArchivoInventarios();});
	$(document).on('click', '#sincronizar_bodegas', function(){ sincronizarBodegas();});

    $('#municipio').change(function(){
        get_warehouses($(this).val());
    })

	$('#municipioImport').change(function(){
		get_warehouses_import($(this).val());
	})

	$('#municipioExport').change(function(){
		get_warehouses_export($(this).val());
	})

    $('#bodega').change(function(){
        if ($(this).val() == 0) {
            $('#municipio').select2('val','0')
        }
    })

    $('#btnBuscar').click(function(){
		if($('#formInventario').valid()){
			$('#formInventario').submit();
		}
    })

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

function get_warehouses(municipio){
    $.ajax({
		type: "POST",
		url: "functions/fn_get_warehouses.php",
		data: { "municipio" : municipio },
		beforeSend: function(){
			$('#loader').fadeIn();
		},
	})
	.done(function(data){ 
        $('#bodega').html(data);
    })
	.fail(function(){ })
	.always(function(){
		$('#loader').fadeOut();
	});
}

function get_warehouses_import(municipio){
    $.ajax({
		type: "POST",
		url: "functions/fn_get_warehouses.php",
		data: { "municipio" : municipio },
		beforeSend: function(){
			$('#loader').fadeIn();
		},
	})
	.done(function(data){ 
        $('#bodegaImport').html(data);
    })
	.fail(function(){ })
	.always(function(){
		$('#loader').fadeOut();
	});
}

function get_warehouses_export(municipio){
    $.ajax({
		type: "POST",
		url: "functions/fn_get_warehouses.php",
		data: { "municipio" : municipio },
		beforeSend: function(){
			$('#loader').fadeIn();
		},
	})
	.done(function(data){ 
        $('#bodegaExport').html(data);
    })
	.fail(function(){ })
	.always(function(){
		$('#loader').fadeOut();
	});
}

function sincronizarBodegas(){
    $.ajax({
		type: "POST",
		url: "functions/fn_sync_warehouses.php",
		dataType : "JSON",
		beforeSend: function(){
			$('#loader').fadeIn();
		},
	})
	.done(function(data){ 
		console.log(data);
        if (data.estado == 1) {
			Command: toastr.success(
				data.mensaje,
				"Exito", { onHidden : function(){ $('#loader').fadeOut(); location.reload() } }
			);
		}else{
			Command: toastr.error(
				data.mensaje,
				"Error", { onHidden : function(){ $('#loader').fadeOut(); } }
			);
		}
    })
	.fail(function(){ })
	.always(function(){
		$('#loader').fadeOut();
	});
}

function descarga_plantilla_productos() {
	if ($('#formDescargarArchivoInventario').valid()) {
		var municipio = $('#municipioExport').val();
		var bodega = $('#bodegaExport').val();
		var complemento = $('#complementoExport').val();
		window.open('functions/fn_get_inventory_template.php?municipioExport='+municipio+'&bodegaExport='+bodega+'&complementoExport='+complemento, '_self');	
		$('#ventanaFormularioDescargarCantidades').modal('hide');	
	}
}

function subirArchivoInventarios(){
	if($('#formSubirArchivoInventario').valid()){
		var formData = new FormData();
		formData.append('municipio', $('#municipioImport').val());
		formData.append('bodega', $('#bodegaImport').val());
		formData.append('complemento', $('#complementoImport').val());
		formData.append('archivoInventario', $('#archivoInventario')[0].files[0]);
		$.ajax({
		  	type: "POST",
		  	url: "functions/fn_set_inventory_quantity.php",
		  	contentType: false,
		  	processData: false,
		  	data: formData,
		  	dataType: 'json',
		  	beforeSend: function(){ $('#loader').fadeIn(); },
		  	success: function(data){ 
				if(data.estado == 1){
					Command: toastr.success(
						data.mensaje,
						"Exito", { onHidden : function(){ $('#loader').fadeOut(); } }
					);
				} else {
					Command: toastr.error(
						data.mensaje,
						"Error al subir datos", { onHidden : function(){ $('#loader').fadeOut(); } }
					);
				}
				$('#ventanaFormularioCargarCantidades').modal('hide');
		  	},
		  	error: function(data){
				$('#loader').fadeOut();
				Command: toastr.error(
			  		"Al parecer existe un problema en el servidor. Por favor comuníquese con el administrador del sitio InfoPAE.",
			  		"Error al subir datos", { onHidden : function(){ $('#loader').fadeOut(); console.log(data.responseText); } }
			  	);
		  	}
		});
	}
}
