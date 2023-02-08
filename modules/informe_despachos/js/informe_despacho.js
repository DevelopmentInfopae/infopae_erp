$(document).ready(function(){
	jQuery.extend(jQuery.validator.messages, {step:"Por favor ingresa un número entero", required: "Este campo es obligatorio.", remote: "Por favor, rellena este campo.", 
      email: "Por favor, escribe una dirección de correo válida", url: "Por favor, escribe una URL válida.", 
      date: "Por favor, escribe una fecha válida.", dateISO: "Por favor, escribe una fecha (ISO) válida.", 
      number: "Por favor, escribe un número entero válido.", digits: "Por favor, escribe sólo dígitos.", 
      creditcard: "Por favor, escribe un número de tarjeta válido.", equalTo: "Por favor, escribe el mismo valor de nuevo.", 
      accept: "Por favor, escribe un valor con una extensión aceptada.", 
      maxlength: jQuery.validator.format("Por favor, no escribas más de {0} caracteres."), 
      minlength: jQuery.validator.format("Por favor, no escribas menos de {0} caracteres."), 
      rangelength: jQuery.validator.format("Por favor, escribe un valor entre {0} y {1} caracteres."), 
      range: jQuery.validator.format("Por favor, escribe un valor entre {0} y {1}."), 
      max: jQuery.validator.format("Por favor, escribe un valor menor o igual a {0}."), 
      min: jQuery.validator.format("Por favor, escribe un valor mayor o igual a {0}.") 	
   	});

	buscarSemanas($('#mes').val());
	rutaSelected = $('#ruta').val();
	if (rutaSelected != '') {
		$('#municipio').prop('disabled', true);
		$('#institucion').prop('disabled', true);
		$('#sede').prop('disabled', true);
	}

	semanaSelected = $('#semana').val();
	if (semanaSelected != '') {
		buscarDias(semanaSelected);
	}
	
	$(document).on('click', '#btnBuscar', function(e){ 
	var municipioPost = $('#municipio').val();
	var instPost = $('#ruta').val();

	var instPost = $('#ruta').val();
		if (municipioPost == '' && instPost == '') { alert('Debe seleccionar un municipio o una ruta');}
		console.log('imhere');
	});

	$('.selectMunicipio').select2();
	$('.selectInstitucion').select2();
	$('.selectSede').select2();
	$('.selectComplem').select2();
	$('.selectRuta').select2();
	$('.selectMes').select2();
	$('.selectSemana').select2();
	$('.selectDia').select2();

	$( "#municipio" ).change(function() {
		var municipioPost = $('#municipio').val();
		if ($(this).val() != "") {
            $('#ruta').prop("disabled", true);
        } else {
            $('#ruta').prop("disabled", false);
        }
		$.ajax({
			type: "POST",
			url: "functions/fn_despachodia_buscar_institucion.php",
			data: { 'muni' : municipioPost },
			beforeSend: function(){ $('#loader').fadeIn() },
			success: function(data){
		   		$('#institucion').html(data);0
			}
		}).always($('#loader').fadeOut());
	});

	$( "#institucion" ).change(function() {
		var instPost = $('#institucion').val();
		// console.log(instPost)
		if ($(this).val() != "") {
            $('#ruta').prop("disabled", true);
        } else {
            $('#ruta').prop("disabled", false);
        }
		// $("#ruta").prop('disabled', true);
		 // console.log(instPost)
		$.ajax({
			type: "POST",
			url: "functions/fn_despachodia_buscar_sede.php",
			data: { 'inst' : instPost },
			beforeSend: function(){ $('#loader').fadeIn() },
			success: function(data){
		   		 // console.log(data)
		   	$('#sede').html(data);
		   	
			}
		}).always($('#loader').fadeOut());
	});

	$( "#sede" ).change(function() {
		var instPost = $('#sede').val();
		 // console.log(instPost)
		 if ($(this).val() != "") {
            $('#ruta').prop("disabled", true);
        } else {
            $('#ruta').prop("disabled", false);
        }
		$.ajax({
			type: "POST",
			url: "functions/fn_despachodia_buscar_complemento.php",
			data: { 'sedes' : instPost },
			beforeSend: function(){ $('#loader').fadeIn() },
			success: function(data){
		   		 // console.log(data)
		   	$('#ruta').html(data);
			}
		}).always($('#loader').fadeOut());
	});
	
	$( "#mes" ).change(function() {
		var instPost = $('#mes').val();
		// console.log(instPost)
		$.ajax({
				type: "POST",
				url: "functions/fn_despachodia_buscar_semana.php",
				data: { 'mes' : instPost },
				beforeSend: function(){ $('#loader').fadeIn() },
				success: function(data){
				// console.log(data)
				$('#semana').html(data);
				}
		}).always($('#loader').fadeOut());
	});

	$( "#semana" ).change(function() {
		var semanaPost = $('#semana').val();
		// console.log(diaPost)
		$.ajax({
			type: "POST",
			url: "functions/fn_despachodia_buscar_dia.php",
			data: { 'semana' : semanaPost },
			beforeSend: function(){ $('#loader').fadeIn() },
			success: function(data){
			// console.log(data)
			$('#dia').html(data);
			}
		}).always($('#loader').fadeOut());
	});


$( "#ruta" ).change(function() {
		var instPost = $('#ruta').val();
		// console.log(instPost)
		if ($(this).val() != "") {
            $('#municipio').prop("disabled", true)
            $('#institucion').prop("disabled", true)
            $('#sede').prop("disabled", true);
        } else {
            $('#municipio').prop("disabled", false)
            $('#institucion').prop("disabled", false)
            $('#sede').prop("disabled", false);
        }

		$.ajax({
			type: "POST",
			url: "functions/fn_despachodia_buscar_semana.php",
			data: { 'ruta' : instPost },
			beforeSend: function(){ $('#loader').fadeIn() },
			success: function(data){
			// console.log(data)
			$('#semana').html(data);
			}
		}).always($('#loader').fadeOut());
	});

});

function buscarSemanas(mes){
	$.ajax({
		type: "POST",
		url: "functions/fn_despachodia_buscar_semana.php",
		data: { 'mes' : mes },
		beforeSend: function(){ $('#loader').fadeIn() },
		success: function(data){
			$('#semana').html(data);
			}
	}).always($('#loader').fadeOut());
}


function buscarDias(semana){
	$.ajax({
		type: "POST",
		url: "functions/fn_despachodia_buscar_dia.php",
		data: { 'semana' : semana },
		beforeSend: function(){ $('#loader').fadeIn() },
		success: function(data){
			$('#dia').html(data);
		}
	}).always($('#loader').fadeOut());
}
