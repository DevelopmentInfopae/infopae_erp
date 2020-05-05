$(document).ready(function(){
  	$(document).on('ifChecked', '#selectVarios', function(){ $('#box-table-a tbody input[type=checkbox]').iCheck('check'); });
  	$(document).on('ifUnchecked', '#selectVarios', function(){ $('#box-table-a tbody input[type=checkbox]').iCheck('uncheck'); });
	$(document).on('change', '#tipo', function(){ cambia_tipo($(this)); });
	$(document).on('change', '#mes', function(){ cambia_mes($(this)); });
	$(document).on('change', '#municipio', function(){ cambia_municipio($(this)); });
	$(document).on('change', '#institucion', function(){ cambia_institucion($(this)); });
	$(document).on('click', '#aplicar_filtro', function(){ buscar_empleados(); });
	$(document).on('change', '#semana_inicial, #semana_final', function(){ validar_semanas(); });
	$(document).on('keyup', '.dias_laborados', function(){ validar_dias_laborados($(this)); });
	$(document).on('click', '#crear_nomina', function(){ crear_nomina(); });
	$(document).on('change', '.dias_incapacidad', function(){ cambia_dias_incapacidad($(this)); });
	$(document).on('change', '.valor_base', function(){ cambia_valor_base($(this)); });

	$('input').iCheck({
	     radioClass: 'iradio_square-green',
 		 checkboxClass: 'icheckbox_square-green',
	});

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
	};
});

function cambia_tipo(select){
	tipo_select = select.val();
	$.ajax({
		url : "functions/fn_nomina_obtener_meses.php",
		type : "POST",
		data : { tipo : tipo_select},
	}).done(function(data){
		$('#mes').html(data);
	}).fail(function(data){
		console.log(data);
	});

	if (tipo_select == 2) {
		$('.manipuladora_mostrar').fadeIn();
		$('.manipuladora_ocultar').fadeOut();
	} else if(tipo_select == 4){
		$('.manipuladora_mostrar').fadeOut();
		$('.manipuladora_ocultar').fadeOut();
		$('.transportador_mostrar').fadeIn();
	} else {
		$('.manipuladora_ocultar').fadeIn();
		$('.manipuladora_mostrar').fadeOut();
	}

}

function cambia_mes(select){
	mes_select = select.val();
	tipo_select = $('#tipo').val();
	$.ajax({
		url : "functions/fn_nomina_obtener_semanas.php",
		type : "POST",
		data : { tipo : tipo_select, mes : mes_select },
	}).done(function(data){
		$('#semana_inicial').html(data);
		$('#semana_final').html(data);
	}).fail(function(data){
		console.log(data);
	});
}

function cambia_municipio(select){
	municipio_select = select.val();
    $.ajax({
      type: "POST",
      url: "../despachos/functions/fn_despacho_buscar_institucion.php",
      data: {municipio : municipio_select},
 	}).done(function(data){
 		$('#institucion').html(data);
 	}).fail(function(data){
 		console.log(data);
 	});
}

function cambia_institucion(select){
	municipio_select = $('#municipio').val();
	institucion_select = select.val();
    $.ajax({
      type: "POST",
      url: "../despachos/functions/fn_despacho_buscar_sede.php",
      data: {
        municipio : municipio_select,
        institucion : institucion_select
      },
    }).done(function(data){ 
    	$('#sede').html(data); 
    }).fail(function(data){ 
    	console.log(data); 
    });
}

function buscar_empleados(){
	if ($('#form_filtrar_empleados').valid()) {
		datos = $('#form_filtrar_empleados').serialize();
		$.ajax({
			url : 'functions/fn_nomina_buscar_empleados.php',
			type : 'POST',
			data : datos,
			dataType : 'JSON'
		}).done(function(data){

			if (data.status == 0) {
				Command: toastr.warning(
					'No se encontró registros para el filtro aplicado',
					'Sin registros',
					{ onHidden: function()
						{
							$('#loader').fadeOut();
						}
					}
				);
			}

			$('.div_table').html(data.html);
			$('input').iCheck({
			     radioClass: 'iradio_square-green',
		 		 checkboxClass: 'icheckbox_square-green',
			});
		}).fail(function(data){
			console.log(data);
		});
	}
}

function validar_semanas(){
	semana_inicial = $('#semana_inicial option:selected').data('num');
	semana_final = $('#semana_final option:selected').data('num');
	console.log('inicial '+semana_inicial+' final '+semana_final);
	if (semana_inicial > semana_final) {
		$('#semana_inicial').val('');
		$('#semana_final').val('');
		Command: toastr.error(
			'La semana inicial no puede ser mayor a la semana final',
			'Error',
			{ onHidden: function()
				{
					$('#loader').fadeOut();
				}
			}
		);
	}

}

function validar_dias_laborados(input){
	max = input.data('max');
	original = input.data('original');
	if (input.val() > max) {
		input.val(original);
	}
}

function crear_nomina(){
	data1 = $('#form_filtrar_empleados').serialize();
	data2 = $('#form_crear_nomina').serialize();
	if ($('#form_crear_nomina').valid()) {
		data = data1+'&'+data2;
		$('#loader').fadeIn();
		$.ajax({
			url : 'functions/fn_nomina_crear_nomina.php',
			type : 'POST',
			data : data,
			dataType : 'JSON'
		}).done(function(data){
			if (data.estado == 1) {
				Command: toastr.success(
					data.mensaje,
					'Guardado correctamente',
					{ onHidden: function()
						{
							$('#loader').fadeOut();
							location.reload();
						}
					}
				);
			} else if (data.estado == 0) {
				Command: toastr.warning(
					data.mensaje,
					'Advertencia',
					{ onHidden: function()
						{
							$('#loader').fadeOut();
						}
					}
				);
			}
		}).fail(function(data){
			Command: toastr.error(
				'Ocurrió un error al guardar, contacte con el administrador',
				'Error',
				{ onHidden: function()
					{
						$('#loader').fadeOut();
					}
				}
			);
		});
	}
}

function cambia_dias_incapacidad(input){
	var index = $('.dias_incapacidad').index(input);
	var aux_transporte = $('.aux_transporte').eq(index);
	var aux_transporte_x_dia = aux_transporte.data('transportexdia');
	var aux_transporte_origin = aux_transporte.data('transporteorigin');
	total_discount_aux_transporte = aux_transporte_x_dia * input.val();
	aux_transporte_vlr = aux_transporte_origin - total_discount_aux_transporte;
	aux_transporte.val(aux_transporte_vlr);
	$('.transporte_txt').eq(index).text(aux_transporte_vlr);
	$('.desc_auxtrans_incap').eq(index).val(total_discount_aux_transporte);
	deducidos = $('.deducidos').eq(index);
	deducidosorigin = deducidos.data('deducidosorigin');
	total_deducidos = (deducidosorigin + total_discount_aux_transporte);
	deducidos.val(total_deducidos);
	valor_base = $('.valor_base').eq(index).val();
	total = parseFloat(valor_base) + parseFloat(aux_transporte_origin) - parseFloat(total_deducidos); 
	$('.total_pagado').eq(index).val(total);
}

function cambia_valor_base(input){
	var index = $('.valor_base').index(input);
	var retefuente = $('.retefuente').eq(index);
	var retefuente_perc = parseFloat(retefuente.data('percent'));
	var reteica = $('.reteica').eq(index);
	var reteica_perc = parseFloat(reteica.data('percent'));
	var total_pagado = $('.total_pagado').eq(index);
	var valor_base = parseFloat(input.val());
	rete_fuente = valor_base * retefuente_perc;
	rete_ica = valor_base * reteica_perc;
	retefuente.val(rete_fuente);
	reteica.val(rete_ica);
	total = valor_base - rete_fuente - rete_ica;
	total_pagado.val(total);
}