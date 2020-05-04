$(document).ready(function(){
	$(document).on('change', '#tipo', function(){ cambia_tipo($(this)); });
	$(document).on('change', '#mes', function(){ cambia_mes($(this)); });
	$(document).on('change', '#municipio', function(){ cambia_municipio($(this)); });
	$(document).on('change', '#institucion', function(){ cambia_institucion($(this)); });
	$(document).on('click', '#aplicar_filtro', function(){ buscar_empleados(); });
	$(document).on('change', '#semana_inicial, #semana_final', function(){ validar_semanas(); });
	$(document).on('keyup', '.dias_laborados', function(){ validar_dias_laborados($(this)); });

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
			data : datos
		}).done(function(data){
			$('#tbody_empleados').html(data);
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