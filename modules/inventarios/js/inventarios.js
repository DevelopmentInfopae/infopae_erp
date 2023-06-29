$(document).ready(function(){

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
    $(document).on('click', '#descargarPlantillaProductos', function(){ descarga_plantilla_productos(); });
	$(document).on('click', '#importarCantidades', function(){ $('#ventanaFormularioCargarCantidades').modal(); });
	$(document).on('click', '#subirArchivoInventario', function(){ subirArchivoInventarios();});
	$(document).on('click', '#sincronizar_bodegas', function(){ sincronizarBodegas();});
	$(document).on('click', '#cronjob', function(){ $('#ventanaFormularioSincronizarCantidades').modal(); });
	$(document).on('click', '#sincronizarCantidades', function(){ sincronizarInventarios();});
	// $(document).on('click', '#cronjob', function(){ executeCronJob();});
	$(document).on('click', '.iniciarSinc', function(){ sincronizacionInicial();});

    $('#municipio').change(function(){
        get_warehouses($(this).val());
    })

	$('#municipioImport').change(function(){
		get_warehouses_import($(this).val());
	})

	$('#municipioSincronizacion').change(function(){
		get_warehouses_sincronizacion($(this).val());
	})

	$('#municipioExport').change(function(){
		get_warehouses_export($(this).val());
	})

    $('#bodega').change(function(){
        if ($(this).val() == '') {
            $('#municipio').select2('val','')
        }
    })

    $('#btnBuscar').click(function(){
		if($('#formInventario').valid()){
			$('#formInventario').submit();
		}
    })

	$('#mesSincronizacion').change(function(){
		get_semana_sincronizacion($(this).val());
	})

	$('#semanaSincronizacion').change(function(){
		get_dia_sincronizacion($(this).val());
	})

});

function get_warehouses(municipio){
    $.ajax({
		type: "POST",
		url: "functions/fn_get_warehouses.php",
		data: { "municipio" : municipio },
		beforeSend: function(){
			$('#loaderAjax').fadeIn();
		},
	})
	.done(function(data){ 
		$('#bodega').select2('destroy');
        $('#bodega').html(data);
		$('#bodega').select2();
    })
	.fail(function(){ })
	.always(function(){
		$('#loaderAjax').fadeOut();
	});
}

function get_warehouses_import(municipio){
    $.ajax({
		type: "POST",
		url: "functions/fn_get_warehouses.php",
		data: { "municipio" : municipio },
		beforeSend: function(){
			$('#loaderAjax').fadeIn();
		},
	})
	.done(function(data){ 
        $('#bodegaImport').html(data);
    })
	.fail(function(){ })
	.always(function(){
		$('#loaderAjax').fadeOut();
	});
}

function get_warehouses_sincronizacion(municipio){
    $.ajax({
		type: "POST",
		url: "functions/fn_get_warehouses.php",
		data: { "municipio" : municipio, "sinc" : 1 },
		beforeSend: function(){
			$('#loaderAjax').fadeIn();
		},
	})
	.done(function(data){ 
        $('#bodegaSincronizacion').html(data);
    })
	.fail(function(){ })
	.always(function(){
		$('#loaderAjax').fadeOut();
	});
}

function get_semana_sincronizacion(mes){
	$.ajax({
		type: "POST",
		url: "functions/fn_get_week.php",
		data: { "mes" : mes },
		beforeSend: function(){
			$('#loaderAjax').fadeIn();
		},
	})
	.done(function(data){ 
        $('#semanaSincronizacion').html(data);
    })
	.fail(function(){ })
	.always(function(){
		$('#loaderAjax').fadeOut();
	});
}

function get_dia_sincronizacion(semana){
	$.ajax({
		type: "POST",
		url: "functions/fn_get_day.php",
		data: { "semana" : semana },
		beforeSend: function(){
			$('#loaderAjax').fadeIn();
		},
	})
	.done(function(data){ 
        $('#diaSincronizacion').html(data);
    })
	.fail(function(){ })
	.always(function(){
		$('#loaderAjax').fadeOut();
	});
}

function get_warehouses_export(municipio){
    $.ajax({
		type: "POST",
		url: "functions/fn_get_warehouses.php",
		data: { "municipio" : municipio },
		beforeSend: function(){
			$('#loaderAjax').fadeIn();
		},
	})
	.done(function(data){ 
        $('#bodegaExport').html(data);
    })
	.fail(function(){ })
	.always(function(){
		$('#loaderAjax').fadeOut();
	});
}

function sincronizarBodegas(){
    $.ajax({
		type: "POST",
		url: "functions/fn_sync_warehouses.php",
		dataType : "JSON",
		beforeSend: function(){
			$('#loaderAjax').fadeIn();
		},
	})
	.done(function(data){ 
		console.log(data);
        if (data.estado == 1) {
			Command: toastr.success(
				data.mensaje,
				"Exito", { onHidden : function(){ $('#loaderAjax').fadeOut(); location.reload() } }
			);
		}else{
			Command: toastr.error(
				data.mensaje,
				"Error", { onHidden : function(){ $('#loaderAjax').fadeOut(); } }
			);
		}
    })
	.fail(function(){ })
	.always(function(){
		$('#loaderAjax').fadeOut();
	});
}

function sincronizacionInicial(){
	$.ajax({
		type: "POST",
		url: "functions/fn_sync_warehouses.php",
		dataType : "JSON",
		beforeSend: function(){},
	})
	.done(function(data){ 
        if (data.estado == 1) {
			var progreso = 0;
			var idIterval = setInterval(function(){
	  			// Aumento en 10 el progeso
	  			progreso +=2;
	  			$('#bar').css('width', progreso + '%');
		 
	  			//Si llegó a 100 elimino el interval
	  			if(progreso == 100){
					clearInterval(idIterval);
					Command: toastr.success(
						data.mensaje,
						"Exito", { onHidden : function(){ $('#loaderAjax').fadeOut(); location.reload() } }
					);
	  			}
			},100); 	
		}else{
			Command: toastr.error(
				data.mensaje,
				"Error", { onHidden : function(){ $('#loaderAjax').fadeOut(); } }
			);
		}
    })
	.fail(function(){ })
	.always(function(){ });
}

function descarga_plantilla_productos() {
	window.open('functions/fn_get_inventory_template.php', + '_self');		
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
		  	beforeSend: function(){ $('#loaderAjax').fadeIn(); },
		  	success: function(data){ 
				if(data.estado == 1){
					Command: toastr.success(
						data.mensaje,
						"Exito", { onHidden : function(){ $('#loaderAjax').fadeOut(); } }
					);
				} else {
					Command: toastr.error(
						data.mensaje,
						"Error al subir datos", { onHidden : function(){ $('#loaderAjax').fadeOut(); } }
					);
				}
				$('#ventanaFormularioCargarCantidades').modal('hide');
		  	},
		  	error: function(data){
				$('#loaderAjax').fadeOut();
				Command: toastr.error(
			  		"Al parecer existe un problema en el servidor. Por favor comuníquese con el administrador del sitio InfoPAE.",
			  		"Error al subir datos", { onHidden : function(){ $('#loaderAjax').fadeOut(); console.log(data.responseText); } }
			  	);
		  	}
		});
	}
}

function sincronizarInventarios(){
	if($('#formSincronizarCantidades').valid()){
		var datos = {
				'municipio' : $('#municipioSincronizacion').val(),
				'bodega'    : $('#bodegaSincronizacion').val(),
				'complemento' : $('#complementoSincronizacion').val(),
				'mes'       : $('#mesSincronizacion').val(),
				'semana' : $('#semanaSincronizacion').val(),
				'dia' : $('#diaSincronizacion').val()
		}
		$.ajax({
			type: "POST",
			url: "functions/fn_execute_cronjob.php",
			data : datos,
			dataType: 'json',
			beforeSend: function(){ $('#loaderAjax').fadeIn(); },
			success: function(data){ 
			  if(data.estado == 1){
				  Command: toastr.success(
					  data.mensaje,
					  "Sincronización exitosa", { onHidden : function(){ $('#loaderAjax').fadeOut(); location.reload() } }
				  );
			  } else {
				  Command: toastr.error(
					  data.mensaje,
					  "Error al sincronizar", { onHidden : function(){ $('#loaderAjax').fadeOut(); } }
				  );
			  }
			},
			error: function(data){
			  $('#loaderAjax').fadeOut();
			  Command: toastr.error(
					"Al parecer existe un problema en el servidor. Por favor comuníquese con el administrador del sitio InfoPAE.",
					"Error al subir datos", { onHidden : function(){ $('#loaderAjax').fadeOut(); console.log(data.responseText); } }
				);
			}
	  	});
	}
}

function movimientos(codigo, bodega, sincronizacion){
	$('#loader').fadeIn();
    $('#contenedor_movimientos').load($('#inputBaseUrl').val() +'/modules/inventarios/movements.php?codigo='+codigo+'&bodega='+bodega+'&sinc='+sincronizacion, function() {
		$('#loader').fadeOut()
	  }); 
}

function sincProduct(codigo, bodega, sincronizacion){
	datos = {
		'codigo' : codigo,
		'bodega' : bodega,
		'fechaSic' : sincronizacion
	}
	$.ajax({
		type: "POST",
		url: "functions/fn_sync_products.php",
		data : datos,
		dataType: 'json',
		beforeSend: function(){ $('#loader').fadeIn(); },
		success: function(data){ 
		  if(data.estado == 1){
			  Command: toastr.success(
				  data.mensaje,
				  "Sincronización exitosa", { onHidden : function(){ $('#loader').fadeOut(); location.reload() } }
			  );
		  } else {
			  Command: toastr.error(
				  data.mensaje,
				  "Error al sincronizar", { onHidden : function(){ $('#loader').fadeOut(); } }
			  );
		  }
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
