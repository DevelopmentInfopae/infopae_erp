$(document).ready(function() {
	// Configuración para la validación del formulario de búsqueda de sedes.
    jQuery.extend(jQuery.validator.messages, { required: "Campo obligatorio.", remote: "Por favor, rellena este campo.", email: "Por favor, escribe una dirección de correo válida", url: "Por favor, escribe una URL válida.", date: "Por favor, escribe una fecha válida.", dateISO: "Por favor, escribe una fecha (ISO) válida.", number: "Por favor, escribe un número entero válido.", digits: "Por favor, escribe sólo dígitos.", creditcard: "Por favor, escribe un número de tarjeta válido.", equalTo: "Por favor, escribe el mismo valor de nuevo.", accept: "Por favor, escribe un valor con una extensión aceptada.", maxlength: jQuery.validator.format("Por favor, no escribas más de {0} caracteres."), minlength: jQuery.validator.format("Por favor, no escribas menos de {0} caracteres."), rangelength: jQuery.validator.format("Por favor, escribe un valor entre {0} y {1} caracteres."), range: jQuery.validator.format("Por favor, escribe un valor entre {0} y {1}."), max: jQuery.validator.format("Por favor, escribe un valor menor o igual a {0}."), min: jQuery.validator.format("Por favor, escribe un valor mayor o igual a {0}.") });

	cargar_semanas($('#mes').val());

	$(document).on('change', '#mes', function (){ cargar_semanas($(this).val()); });
	$(document).on('change', '#municipio', function(){ buscar_institucion($(this).val()); });
	$(document).on('change', '#institucion', function(){ buscar_sede($(this).val()); });
	$(document).on('click', '#imprimir_formato', function(){ validar_formulario_alimentos(); });

	$('#municipio').trigger('change');
});

function cargar_semanas($mes, $semana = '') {
	$.ajax({
		url: 'functions/fn_buscar_semanas_mes.php',
		type: 'POST',
		dataType: 'HTML',
		data: {
			'mes': $mes,
			'semana' : $semana
		},
	})
	.done(function(data) {
		$('#semana_inicial').html(data);
	})
	.fail(function(data) {
		console.log(data.responseText);
	});
}

function buscar_institucion(municipio){
  $.ajax({
    type: 'POST',
    url: 'functions/fn_buscar_institucion_municipio.php',
    data: { 'municipio': municipio }
  })
  .done(function(data){ $('#institucion').html(data); $('#sede').html('<option value="">Seleccione</option>'); })
  .fail(function(data){ console.log(data.responseText); });
}

function buscar_sede(institucion){
    $.ajax({
      type: 'POST',
      url: 'functions/fn_buscar_sedes_institucion.php',
      dataType: 'HTML',
      data: { 'institucion': institucion }
    })
    .done(function(data){ $('#sede').html(data); })
    .fail(function(data){ data.responseText; });
}


function validar_formulario_alimentos() {
	if($('#form_imprimir_formato').valid()) {
		$('#form_imprimir_formato').submit();
	}
}