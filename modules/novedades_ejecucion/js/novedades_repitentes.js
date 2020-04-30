$(document).ready(function()
{
	// Configuración de select2.
	$('.select2').select2();

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

	// Configuración de Jquery validator.
  jQuery.extend(jQuery.validator.messages, { required: "Campo obligatorio.", remote: "Por favor, rellena este campo.", email: "Por favor, escribe una dirección de correo válida", url: "Por favor, escribe una URL válida.", date: "Por favor, escribe una fecha válida.", dateISO: "Por favor, escribe una fecha (ISO) válida.", number: "Por favor, escribe un número entero válido.", digits: "Por favor, escribe sólo dígitos.", creditcard: "Por favor, escribe un número de tarjeta válido.", equalTo: "Por favor, escribe el mismo valor de nuevo.", accept: "Por favor, escribe un valor con una extensión aceptada.", maxlength: jQuery.validator.format("Por favor, no escribas más de {0} caracteres."), minlength: jQuery.validator.format("Por favor, no escribas menos de {0} caracteres."), rangelength: jQuery.validator.format("Por favor, escribe un valor entre {0} y {1} caracteres."), range: jQuery.validator.format("Por favor, escribe un valor entre {0} y {1}."), max: jQuery.validator.format("Por favor, escribe un valor menor o igual a {0}."), min: jQuery.validator.format("Por favor, escribe un valor mayor o igual a {0}.") });

	_total_focalizados_D1 = 0;
	_total_focalizados_D2 = 0;
	_total_focalizados_D3 = 0;
	_total_focalizados_D4 = 0;
	_total_focalizados_D5 = 0;
	_total_priorizado_dia = 0;
  _cantidadDiasFocalizados = 0;
	_cantidadDiasFocalizadosActual = 0;

	$('#municipio_hidden').val($('#municipio').val());

	$(document).on('change', '#mes', function() { buscar_semana_mes($(this).val()); });
	$(document).on('change', '#sede', function() { buscar_meses_sede($(this).val()); });
	$(document).on('change', '#institucion', function() { buscar_sedes_institucion(); });
	$(document).on('change', '#semana', function() { buscar_complementos($(this).val()); });
	$(document).on('change', '#municipio', function(){ buscar_instituciones($(this).val()); });
	$(document).on('click', '#boton_buscar_novedades_repitentes', function() { validar_campos_filtros(); });
	$(document).on('click', '.boton_guardar_novedades_repitentes', function() { guardar_novedades_repitentes(); });
	$(document).on('change', '#tipo_complemento', function() { $('#tipo_complemento_hidden').val($(this).val()); });

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

    validar_columnas();
	});

	$(document).on('change', '.checkbox1, .checkbox2, .checkbox3, .checkbox4, .checkbox5', function()
	{
		nombre_clase_checkbox =  $(this).prop('class');
		posicion_columna_checbox = nombre_clase_checkbox.replace("checkbox", "");

		if($(this).is(':checked'))
		{
			sumarCantidadDias($(this));
		}
		else
		{
			restarCantidadDias($(this));
		}

		validar_columnas();
	});
});

function buscar_instituciones(municipio)
{
	$.ajax({
		type: "POST",
		url: "functions/fn_buscar_instituciones.php",
		dataType: 'JSON',
		data: { 'municipio': municipio },
		beforeSend: function(){ $('#loader').fadeIn(); },
		success: function(data){
			if(data.estado == 1){
				$('#institucion').html(data.opciones);
				$('#loader').fadeOut();
			} else {
				Command: toastr.error( data.mensaje, "Error al cargar los municipios.", { onHidden : function(){ $('#institucion').html(data.opciones); $('#loader').fadeOut(); } } );
			}
		},
		error: function (data){
			console.log(data.responseText);
		}
	});

	$('#municipio_hidden').val(municipio);
}

function buscar_sedes_institucion()
{
	institucion = $('#institucion').val();
	$.ajax({
		url: 'functions/fn_buscar_sedes.php',
		type: 'POST',
		dataType: 'HTML',
		data: {'institucion': institucion},
		beforeSend: function(){ $('#loader').fadeIn(); }
	})
	.done(function(data)
	{
		$('#sede').html(data);
	})
	.fail(function(data)
	{
		console.log(data.responseText);
	});

	$('#sede').select2('val', '');
	$('#institucion_hidden').val(institucion);
}

function buscar_meses_sede(sede)
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

function buscar_semana_mes(mes)
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

function buscar_complementos(semana)
{
	$.ajax({
		type: "POST",
		url: "functions/fn_buscar_complementos.php",
		dataType: 'HTML',
    data: {
    	'semana': semana,
    	'mes': $('#mes').val(),
    	'sede': $('#sede').val(),
    	'institucion': $('#institucion').val(),
    },
		beforeSend: function(){ $('#loader').fadeIn(); },
		success: function(data)
		{
			$('#tipo_complemento').html(data);
			$('#loader').fadeOut();
		},
		error: function(data)
		{
			console.log(data.responseText);
			$('#loader').fadeOut();
		}
	});

	$('#tipo_complemento').select2('val', '');
	$('#semana_hidden').val(semana);
}

function validar_campos_filtros()
{
	if ($('#formulario_buscar_novedades_repitentes').valid())
	{
		calcular_priorizacion_dias();
	}
}

function calcular_priorizacion_dias()
{
	$.ajax({
		url: 'functions/fn_novedades_repitentes_calcular_priorizacion.php',
		type: 'POST',
		dataType: 'JSON',
		data: {
			mes: $('#mes').val(),
			sede: $('#sede').val(),
			semana: $('#semana').val(),
			tipo_complemento: $('#tipo_complemento').val(),
		},
	})
	.done(function(data)
	{
		if (data.estado == 1)
		{
			sessionStorage.setItem("datos_focalizacion", data.datos);
			buscar_dias_semanas();
		}
		else if (data.estado == 2)
		{
			Command: toastr.warning(data.mensaje, 'Advertencia');
		}
		else
		{
			Command: toastr.error(data.mensaje, 'Error de proceso');
		}
	})
	.fail(function(data)
	{
		Command: toastr.error('Al parecer existe un problema. Por favor comuníquese con el administrador del sistema.', 'Error de proceso', { onHidden: function(){ console.log(data.responseText); } });
	});
}

function buscar_dias_semanas()
{
	$.ajax({
		url: 'functions/fn_novedades_suplentes_buscar_dias_semana.php',
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
		$('#contenedor_tabla_novedades_suplentes').fadeIn();
		$('.tabla_novedades_suplentes thead tr, .tabla_novedades_suplentes tfoot tr').html(data);

		buscar_focalizados();
	})
	.fail(function(data)
	{
		$('#loader').fadeOut();
		Command: toastr.error('Al parecer existe un problema. Por favor comuníquese con el administrador del sistema.', 'Error de proceso', { onHidden: function(){ console.log(data.responseText); } });
	});
}

function buscar_focalizados()
{
	total_suma_dias = 0;
	mes = $('#mes').val();
	sede = $('#sede').val();
	semana = $('#semana').val();
	institucion = $('#institucion').val();
	tipo_complemento = $('#tipo_complemento').val();

	$('.tabla_novedades_suplentes').DataTable({
    ajax: {
      method: 'POST',
      url: 'functions/fn_novedades_repitentes_buscar.php',
      data: {
      	'mes': mes,
      	'sede': sede,
      	'semana': semana,
      	'institucion': institucion,
      	'tipo_complemento': tipo_complemento
      }
      // ,success:function(data){console.log(data);}
      // ,error:function(data){console.log(data.responseText);}
    },
    columns:[
      { data: 'abreviatura_documento'},
      { data: 'numero_documento', className: 'columna_numero_documento'},
      { data: 'nombre'},
      { data: 'grado'},
      { data: 'grupo'},
      { data: 'maximo_D1', className: 'text-center'},
      { data: 'maximo_D2', className: 'text-center'},
      { data: 'maximo_D3', className: 'text-center'},
      { data: 'maximo_D4', className: 'text-center'},
      { data: 'maximo_D5', className: 'text-center'}
    ],
    order: [[3, 'asc'], [4, 'asc'], [2, 'asc']],
    dom: 'r<"containerBtn">tip',
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
    destroy: true,
    pageLength: -1,
    responsive: true,
    rowCallback: function(row, data)
		{
  		total_suma_dias += parseInt(data.suma_total_dias);
    },
    initComplete: function(settings, json)
    {
    	// Método para buscar repitentes y verificar su validación.
    	buscar_repitentes();

			$('.checkbox-header').each(function()
			{
				var contador = 0;

				$('.checkbox'+ $(this).data('columna')).each(function() {
					if ($(this).is(':checked')) { contador++; }
				});

				if (contador > 0) { $(this).prop('checked', true); }
				else { $(this).prop('checked', false); }
			});

			var datos_focalizacion = JSON.parse(sessionStorage.getItem("datos_focalizacion"));
			console.log(datos_focalizacion);

			_total_focalizados_D1 = (typeof datos_focalizacion.total_D1 != "undefined") ? parseInt(datos_focalizacion.total_D1) : 0;
			_total_focalizados_D2 = (typeof datos_focalizacion.total_D2 != "undefined") ? parseInt(datos_focalizacion.total_D2) : 0;
			_total_focalizados_D3 = (typeof datos_focalizacion.total_D3 != "undefined") ? parseInt(datos_focalizacion.total_D3) : 0;
			_total_focalizados_D4 = (typeof datos_focalizacion.total_D4 != "undefined") ? parseInt(datos_focalizacion.total_D4) : 0;
			_total_focalizados_D5 = (typeof datos_focalizacion.total_D5 != "undefined") ? parseInt(datos_focalizacion.total_D5) : 0;
			_total_priorizado_dia = parseInt(datos_focalizacion.total_priorizado_dia);
			_cantidadDiasFocalizados = parseInt(datos_focalizacion.total_priorizado_semana);

			validar_columnas();

			$('.boton_guardar_novedades_repitentes').removeClass('disabled');
      $('#loader').fadeOut();
    }
  });
}

function buscar_repitentes()
{
	mes = $('#mes').val();
	sede = $('#sede').val();
	semana = $('#semana').val();
	institucion = $('#institucion').val();
	tipo_complemento = $('#tipo_complemento').val();

	$.ajax({
		url: 'functions/fn_novedades_repitentes_buscar_repitentes.php',
		type: 'POST',
		dataType: 'JSON',
		data: {
			'mes': mes,
    	'sede': sede,
    	'semana': semana,
    	'institucion': institucion,
    	'tipo_complemento': tipo_complemento
		},
	})
	.done(function(data)
	{
		if (typeof data !== 'undefined' && data.length > 0)
		{
			for (var i = 0; i < data.length; i++)
			{
				if (typeof data !== 'undefined' && data[i].D1 == 1) { $('#'+ data[i].num_doc +'_D1').prop('checked', true); }
				if (typeof data !== 'undefined' && data[i].D2 == 1) { $('#'+ data[i].num_doc +'_D2').prop('checked', true); }
				if (typeof data !== 'undefined' && data[i].D3 == 1) { $('#'+ data[i].num_doc +'_D3').prop('checked', true); }
				if (typeof data !== 'undefined' && data[i].D4 == 1) { $('#'+ data[i].num_doc +'_D4').prop('checked', true); }
				if (typeof data !== 'undefined' && data[i].D5 == 1) { $('#'+ data[i].num_doc +'_D5').prop('checked', true); }
			}
		}
	})
	.fail(function(data)
	{
		Command: toastr.error('Al parecer existe un problema en el sistema. Por favor comuníquese con el administrador.', 'Error', { onHidden: function() { console.log(data.responseText); } });
	});
}

function validar_columnas()
{
	_cantidadDiasFocalizadosActual = _total_focalizados_D1 + _total_focalizados_D2 + _total_focalizados_D3 + _total_focalizados_D4 + _total_focalizados_D5;

	$('#total_complementos').html(_cantidadDiasFocalizados);
	$('#complementos_faltantes').html(_cantidadDiasFocalizadosActual);

	if(_total_focalizados_D1 >= _total_priorizado_dia)
	{
		$('input[name="checkbox-header_1"]').prop('disabled', true);
		$('.checkbox1').each(function()
		{
			if (! $(this).is(':checked'))
			{
				$(this).prop('disabled', true);
			}
		});
	}
	else
	{
		$('input[name="checkbox-header_1"]').prop('disabled', false);
		$('.checkbox1').each(function()
		{
			$(this).prop('disabled', false);
		});
	}

	if(_total_focalizados_D2 >= _total_priorizado_dia)
	{
		$('input[name="checkbox-header_2"]').prop('disabled', true);
		$('.checkbox2').each(function()
		{
			if (! $(this).is(':checked'))
			{
				$(this).prop('disabled', true);
			}
		});
	}
	else
	{
		$('input[name="checkbox-header_2"]').prop('disabled', false);
		$('.checkbox2').each(function()
		{
			$(this).prop('disabled', false);
		});
	}


	if(_total_focalizados_D3 >= _total_priorizado_dia)
	{
		$('input[name="checkbox-header_3"]').prop('disabled', true);
		$('.checkbox3').each(function()
		{
			if (! $(this).is(':checked'))
			{
				$(this).prop('disabled', true);
			}
		});
	}
	else
	{
		$('input[name="checkbox-header_3"]').prop('disabled', false);
		$('.checkbox3').each(function()
		{
			$(this).prop('disabled', false);
		});
	}

	if(_total_focalizados_D4 >= _total_priorizado_dia)
	{
		$('input[name="checkbox-header_4"]').prop('disabled', true);
		$('.checkbox4').each(function()
		{
			if (! $(this).is(':checked'))
			{
				$(this).prop('disabled', true);
			}
		});
	}
	else
	{
		$('input[name="checkbox-header_4"]').prop('disabled', false);
		$('.checkbox4').each(function()
		{
			$(this).prop('disabled', false);
		});
	}

	if(_total_focalizados_D5 >= _total_priorizado_dia)
	{
		$('input[name="checkbox-header_5"]').prop('disabled', true);
		$('.checkbox5').each(function()
		{
			if (! $(this).is(':checked'))
			{
				$(this).prop('disabled', true);
			}
		});
	}
	else
	{
		$('input[name="checkbox-header_5"]').prop('disabled', false);
		$('.checkbox5').each(function()
		{
			$(this).prop('disabled', false);
		});
	}
}

function sumarCantidadDias(checkbox)
{
	if (_cantidadDiasFocalizadosActual < _cantidadDiasFocalizados)
	{
		_cantidadDiasFocalizadosActual += 1;
		checkbox.prop('checked', true);

		nombre_clase_checkbox =  checkbox.prop('class');
		posicion_columna_checbox = nombre_clase_checkbox.replace("checkbox", "");

 		if (posicion_columna_checbox == 1)
 		{
 			if (_total_focalizados_D1 < _total_priorizado_dia) { _total_focalizados_D1 += 1; } else { checkbox.prop('checked', false); }
 		}
		if (posicion_columna_checbox == 2)
 		{
 			if (_total_focalizados_D2 < _total_priorizado_dia) { _total_focalizados_D2 += 1; } else { checkbox.prop('checked', false); }
 		}
 		if (posicion_columna_checbox == 3)
 		{
 			if (_total_focalizados_D3 < _total_priorizado_dia) { _total_focalizados_D3 += 1; } else { checkbox.prop('checked', false); }
 		}
 		if (posicion_columna_checbox == 4)
 		{
 			if (_total_focalizados_D4 < _total_priorizado_dia) { _total_focalizados_D4 += 1; } else { checkbox.prop('checked', false); }
 		}
 		if (posicion_columna_checbox == 5)
 		{
 			if (_total_focalizados_D5 < _total_priorizado_dia) { _total_focalizados_D5 += 1; } else { checkbox.prop('checked', false); }
 		}
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

		nombre_clase_checkbox =  checkbox.prop('class');
		posicion_columna_checbox = nombre_clase_checkbox.replace("checkbox", "");

 		if (posicion_columna_checbox == 1)
 		{
 			if (_total_focalizados_D1 > 0) { _total_focalizados_D1 -= 1; }
 		}
		if (posicion_columna_checbox == 2)
 		{
 			if (_total_focalizados_D2 > 0) { _total_focalizados_D2 -= 1; }
 		}
 		if (posicion_columna_checbox == 3)
 		{
 			if (_total_focalizados_D3 > 0) { _total_focalizados_D3 -= 1; }
 		}
 		if (posicion_columna_checbox == 4)
 		{
 			if (_total_focalizados_D4 > 0) { _total_focalizados_D4 -= 1; }
 		}
 		if (posicion_columna_checbox == 5)
 		{
 			if (_total_focalizados_D5 > 0) { _total_focalizados_D5 -= 1; }
 		}
	}
	else
	{
		checkbox.prop('checked', true);
	}

	$('#complementos_faltantes').html(_cantidadDiasFocalizadosActual);
}

function guardar_novedades_repitentes()
{
	var formData = new FormData($("#formulario_guardar_novedades_repitentes")[0]);

	$.ajax({
		url: 'functions/fn_novedades_repitentes_guardar.php',
		type: 'POST',
		dataType: 'JSON',
		data: formData,
		contentType: false,
		processData: false,
		beforeSend: function() { $('#loader').fadeIn(); }
	})
	.done(function(data)
	{console.log(data);
		if (data.estado == 1)
		{
			Command: toastr.success(data.mensaje, 'Proceso Exitoso.', { onHidden: function() { $('#loader').fadeOut(); location.reload(); }});
		}
		else
		{
			Command: toastr.error(data.mensaje, 'Error de proceso.', { onHidden: function() { console.log(data); $('#loader').fadeOut(); }});
		}
	})
	.fail(function(data)
	{
		Command: toastr.error('Al parecer existe un error en el sistema. Por favor comuníquese con el adminstrodor del sistema.', 'Error de proceso.', { onHidden: function() { console.log(data.responseText); $('#loader').fadeOut(); }});
	});
}