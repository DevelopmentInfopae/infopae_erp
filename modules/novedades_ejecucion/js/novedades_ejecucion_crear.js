$(document).ready(function()
{
	// Configuración inicial del plugin select2.
	$('select').select2();

	// Configuración inicial del plugin toastr.
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

	// Configuración inicial del plugin jquery validator.
	jQuery.extend(jQuery.validator.messages, { required: "Este campo es obligatorio.", remote: "Por favor, rellena este campo.", email: "Por favor, escribe una dirección de correo válida", url: "Por favor, escribe una URL válida.", date: "Por favor, escribe una fecha válida.", dateISO: "Por favor, escribe una fecha (ISO) válida.", number: "Por favor, escribe un número entero válido.", digits: "Por favor, escribe sólo dígitos.", creditcard: "Por favor, escribe un número de tarjeta válido.", equalTo: "Por favor, escribe el mismo valor de nuevo.", accept: "Por favor, escribe un valor con una extensión aceptada.", maxlength: jQuery.validator.format("Por favor, no escribas más de {0} caracteres."), minlength: jQuery.validator.format("Por favor, no escribas menos de {0} caracteres."), rangelength: jQuery.validator.format("Por favor, escribe un valor entre {0} y {1} caracteres."), range: jQuery.validator.format("Por favor, escribe un valor entre {0} y {1}."), max: jQuery.validator.format("Por favor, escribe un valor menor o igual a {0}."), min: jQuery.validator.format("Por favor, escribe un valor mayor o igual a {0}.") });


	$('#municipio_hidden').val($('#municipio').val());

	_tablaFocalizados = '';
	_tablaNoFocalizados = '';
	_cantidad_columnas_tabla = 0;
	_cantidadDiasFocalizados = 0;
	_cantidadDiasFocalizadosActual = 0;

	_cambio_check_encabezado = false;

	$('#btnBuscar').click(function(){ validar_campos_filtros(); });
	$('.guaradarNovedad').click(function(){ guardarNovedad(); });
	$('#sede').change(function(){ buscarMeses($(this).val()); });
	$('#mes').change(function(){ buscarSemanas($(this).val()); });
	$('#institucion').change(function(){ buscarSede($(this).val()); });
	$('#semana').change(function(){ buscarComplementos($(this).val()); });
	$('#municipio').change(function(){ buscarInstituciones($(this).val()); });
	$('#tipoComplemento').change(function() { $('#tipoComplemento_hidden').val($(this).val()); });

	$(document).on('click', '.checkbox-header', function()
	{
		_parent = $(this);
		if(_parent.is(':checked'))
		{
			$('.checkbox'+ $(this).data('columna')).each(function()
			{
				if (! $(this).is(':checked'))
				{
					sumarCantidadDias($(this));
				}
			});
    }
    else
    {
    	$('.checkbox'+ _parent.data('columna')).each(function()
    	{
    		if ($(this).is(':checked'))
    		{
    			restarCantidadDias($(this));
    		}
			});
    }

		var contador = 0;
    $('.checkbox'+ _parent.data('columna')).each(function()
  	{
  		if ($(this).is(':checked'))
  		{
  			contador++;
  		}
  	});

    if (contador > 0)
    {
    	$('input[name="checkbox-header_'+$(this).data('columna')+'"]').prop('checked', true);
    }
    else
    {
    	$('input[name="checkbox-header_'+$(this).data('columna')+'"]').prop('checked', false);
    }
	});

	$(document).on('change', '.checkbox1, .checkbox2, .checkbox3, .checkbox4, .checkbox5', function()
	{
		if($(this).is(':checked'))
		{
			sumarCantidadDias($(this));
		}
		else
		{
			restarCantidadDias($(this));
		}
	});
});

function buscarMunicipios()
{
	$.ajax({
		type: "POST",
		url: "functions/fn_buscar_municipios.php",
		contentType: false,
		processData: false,
		dataType: 'json',
		beforeSend: function(){ $('#loader').fadeIn(); },
		success: function(data){
			if(data.estado == 1){
				$('#municipio').html(data.opciones);
				$('#loader').fadeOut();
			} else {
				Command: toastr.error( data.mensaje, "Error al cargar los municipios.", { onHidden : function(){ $('#loader').fadeOut(); } } );
			}
		},
		error: function (data){
			console.log(data.responseText);
		}
	});
}

function buscarInstituciones(municipio)
{
	$.ajax({
		type: "POST",
		url: "functions/fn_buscar_instituciones.php",
		dataType: 'JSON',
    data: {
    	'municipio': municipio
    },
		beforeSend: function(){ $('#loader').fadeIn(); },
		success: function(data) {
			if(data.estado == 1){
				$('#institucion').html(data.opciones);
				$('#loader').fadeOut();
			} else {
				$('#institucion').html(data.opciones);
				Command: toastr.error( data.mensaje, "Error al cargar las instituciones.", { onHidden : function(){ $('#loader').fadeOut(); } } );
			}
		},
		error: function(data) {
			$('#loader').fadeOut();
		}
	});

	$('#institucion').select2('val', '');
	$('#municipio_hidden').val(municipio);
}

function buscarSede(institucion)
{
	$.ajax({
		type: 'POST',
		url: 'functions/fn_buscar_sedes.php',
		dataType: 'HTML',
    data: {
    	'institucion': institucion
    },
		beforeSend: function(){ $('#loader').fadeIn(); },
		success: function(data)
		{
			$('#sede').html(data);
			$('#loader').fadeOut();
		},
		error: function(data)
		{
			$('#loader').fadeOut();
			console.log(data.responseText);
		}
	});

	$('#sede').select2('val', '');
	$('#institucion_hidden').val(institucion);
}

function buscarMeses(sede)
{
	$.ajax({
		type: "POST",
		url: "functions/fn_buscar_meses.php",
		dataType: 'JSON',
    data: {
    	'sede': sede
    },
		beforeSend: function(){ $('#loader').fadeIn(); },
		success: function(data){
			if(data.estado == 1){
				$('#mes').html(data.opciones);
				$('#loader').fadeOut();
			} else {
				Command: toastr.error( data.mensaje, "Error al cargar las instituciones.", { onHidden : function(){ $('#loader').fadeOut(); } } );
			}
		},
		error: function (data) {
			$('#loader').fadeOut();
		}
	});

	$('#mes').select2('val', '');
	$('#sede_hidden').val(sede);
}

function buscarSemanas(mes)
{
	$.ajax({
		type: "POST",
		url: "functions/fn_buscar_semanas.php",
		dataType: 'HTML',
    data: {
    	'mes': mes
    },
		beforeSend: function(){ $('#loader').fadeIn(); },
		success: function(data)
		{
			$('#semana').html(data);
			$('#loader').fadeOut();
		},
		error: function(data)
		{
			console.log(data.responseText);
			$('#loader').fadeOut();
		}
	});

	$('#semana').select2('val', '');
	$('#mes_hidden').val(mes);
}

function buscarComplementos(semana)
{
	$.ajax({
		type: "POST",
		url: "functions/fn_buscar_complementos.php",
		dataType: 'HTML',
    data: {
    	'semana': semana,
    	'mes': $('#mes').val(),
    	'institucion': $('#institucion').val()
    },
		beforeSend: function(){ $('#loader').fadeIn(); },
		success: function(data)
		{
			$('#tipoComplemento').html(data);
			$('#loader').fadeOut();
		},
		error: function(data)
		{
			console.log(data.responseText);
			$('#loader').fadeOut();
		}
	});

	$('#tipoComplemento').select2('val', '');
	$('#semana_hidden').val(semana);
}

function validar_campos_filtros()
{
	if ($('#formulario_buscar_focalizacion').valid())
	{
		buscar_dias_semanas();
	}
}

function buscar_dias_semanas()
{
	$.ajax({
		url: 'functions/fn_novedades_ejecucion_buscar_dias_semana.php',
		type: 'POST',
		dataType: 'HTML',
		data: {
			mes: $('#mes').val(),
			semana: $('#semana').val()
		},
		beforeSend: function()
		{
			$('#loader').fadeIn();
		}
	})
	.done(function(data)
	{
		$('#contenedor_tabla_focalizados').fadeIn();
		$('.tabla_focalizacion thead tr, .tabla_focalizacion tfoot tr').html(data);

		buscar_priorizacion();
	})
	.fail(function(data)
	{
		$('#loader').fadeOut()
		console.log(data);
	});
}

function buscar_priorizacion()
{
	$.ajax({
		url: 'functions/fn_novedades_ejecucion_buscar_priorizacion.php',
		type: 'POST',
		dataType: 'JSON',
		data: {
			mes: $('#mes').val(),
			sede: $('#sede').val(),
			semana: $('#semana').val(),
			tipo_complemento: $('#tipoComplemento').val()
		}
	})
	.done(function(data) {
		if (data == 0)
		{
			Command: toastr.error('No existe priorización según los filtros seleccionados.', 'Error de proceso', { onHidden: function() { location.reload(); } });
		}
		else
		{
			_cantidadDiasFocalizados = parseInt(data);
			$('#total_priorizacion').html(_cantidadDiasFocalizados);

			buscar_focalizacion();
		}
	})
	.fail(function(data) {
		Command: toastr.error('Al parecer existe un problema. Por favor comuníquese con el administrador del sistema.', 'Error de proceso', { onHidden: function() { console.log(data.responseText); $('#loader').fadeOut(); } });
	});
}

function buscar_focalizacion()
{
	total_suma_dias = 0;

	$('.tabla_focalizacion').DataTable({
		ajax: {
			method: 'POST',
			url: 'functions/fn_novedades_ejecucion_buscar_focalizacion.php',
			data:{
				municipio: $('#municipio').val(),
				institucion: $('#institucion').val(),
				sede: $('#sede').val(),
				mes: $('#mes').val(),
				semana: $('#semana').val(),
				tipoComplemento: $('#tipoComplemento').val()
			}
		},
		columns:[
			{ data: 'abreviatura_documento'},
			{ data: 'numero_documento'},
			{ data: 'nombre'},
			{ data: 'grado'},
			{ data: 'grupo'},
			{ data: 'complemento'},
			{ data: 'D1', className: 'text-center', orderable: false},
			{ data: 'D2', className: 'text-center', orderable: false},
			{ data: 'D3', className: 'text-center', orderable: false},
			{ data: 'D4', className: 'text-center', orderable: false},
			{ data: 'D5', className: 'text-center', orderable: false}
		],
		order: [[3, 'asc'], [4, 'asc'], [2, 'asc']],
		oLanguage: {
			sLengthMenu: 'Mostrando _MENU_ registros',
			sZeroRecords: 'No se encontraron registros',
			sInfo: 'Mostrando _START_ a _END_ de _TOTAL_ registros ',
			sInfoEmpty: 'Mostrando 0 a 0 de 0 registros',
			sInfoFiltered: '(Filtrado desde _MAX_ registros)',
			sSearch:         'Buscar: ',
			oPaginate:{
				sFirst:    'Primero',
				sLast:     'Último',
				sNext:     'Siguiente',
				sPrevious: 'Anterior'
			}
		},
		dom: 'r<"containerBtn"><"inputFiltro"f>tip',
		MenuLength: [[-1], ['Todo']],
		destroy: true,
		responsive: true,
		pageLength: -1,
		rowCallback: function(row, data)
		{
  		total_suma_dias += parseInt(data.suma_dias);
    },
		initComplete: function(settings, json)
		{
			$('#loader').fadeOut();
			$('#boton_guardar_novedades').removeClass('disabled');

			// Iteración para habilitar el check principal del encabezado de tabla
			$('.checkbox-header').each(function()
			{
				var contador = 0;

				$('.checkbox'+ $(this).data('columna')).each(function() {
					if ($(this).is(':checked'))
					{
						contador++;
					}
				});

				if (contador > 0)
				{
					$(this).prop('checked', true);
				}
				else
				{
					$(this).prop('checked', false);
				}
			});

			_cantidadDiasFocalizadosActual = parseInt(total_suma_dias);
			$('#complementos_faltantes').html(_cantidadDiasFocalizadosActual);
		}
	});
}

function sumarCantidadDias(checkbox)
{
	if (_cantidadDiasFocalizadosActual < _cantidadDiasFocalizados)
	{
		_cantidadDiasFocalizadosActual += 1;
		checkbox.prop('checked', true);

	}
	else
	{
		checkbox.prop('checked', false);
	}

	$('#complementos_faltantes').html(_cantidadDiasFocalizadosActual);
}

function restarCantidadDias(checkbox)
{
	if (_cantidadDiasFocalizadosActual > 0)
	{
		_cantidadDiasFocalizadosActual -= 1;
		checkbox.prop('checked', false);
	}
	else
	{
		checkbox.prop('checked', true);
	}

	$('#complementos_faltantes').html(_cantidadDiasFocalizadosActual);
}

function guardarNovedad()
{
  	if($('#formulario_guardar_novedades_focalizacion').valid())
  	{
		var formData = new FormData($("#formulario_guardar_novedades_focalizacion")[0]);
		$.ajax({
			type: "POST",
			url: "functions/fn_guardar_novedad_ejecucion.php",
			dataType: 'JSON',
		    data: formData,
				contentType: false,
				processData: false,
				beforeSend: function() { $('#loader').fadeIn(); },
				success: function(data)
				{
					console.log(data);
					if(data.estado == 1)
					{
						Command: toastr.success( data.mensaje, "Proceso exitoso.", {
							onHidden : function(){ $('#loader').fadeOut(); location.href="index.php"; }
						});
					} else {
						Command: toastr.error( data.mensaje, "Error de proceso.", {
							onHidden : function(){ $('#loader').fadeOut(); }
						});
					}
				},
				error: function(data)
				{
		      		Command: toastr.error("Al parecer existe un error. Por favor comuníquese con el administrador del sistema.", "Error de proceso.", {
		        	onHidden : function(){ $('#loader').fadeOut(); console.log(data.responseText); }
		      	}
		      );
		    }
		});
  	}
}