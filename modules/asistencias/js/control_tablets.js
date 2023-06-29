jQuery.extend(
	jQuery.validator.messages, { 
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
	}
);

$(document).ready(function(){
	$('#btnBuscarControl').click(function(){
		if($('#form_control_asistencia').valid()){
			$('#form_control_asistencia').submit()	
		}
	});	
	$('select').select2();

	/************************** MES FORMULARIO ***************************************/ 
	$( "#mes" ).change(function() {
		localStorage.setItem("wappsi_mes", $("#mes").val()); // cuando camibia el mes seteamos el cache
		$('#semana').select2('val', '');
		cargarSemanas($('#mes').val(), ''); // cargamos las semanas con el nuevo mes
	});
	if(localStorage.getItem("wappsi_mes") != null){ // validamos el cache del mes
		$('#mes').select2('destroy');
		$("#mes").val(localStorage.getItem("wappsi_mes"));
		$('#mes').select2()
		cargarSemanas(localStorage.getItem("wappsi_mes"), localStorage.getItem("wappsi_semana"));	
	}
	/************************** MES FORMULARIO ***************************************/ 

	/*************************** SEMANA FORMULARIO *****************************************/
	$( "#semana" ).change(function() {
		localStorage.setItem("wappsi_semana", $("#semana").val()); // Cuando cambia la semana seteamos el cache de la semana
		cargarDias($('#mes').val(), $('#semana').val(), $('#dia').val()); // buscamos los días de la nueva semana
	});
	if(localStorage.getItem("wappsi_semana") != null){
		cargarDias(localStorage.getItem("wappsi_mes"), localStorage.getItem("wappsi_semana"), localStorage.getItem("wappsi_dia"));	
	}
	/*************************** SEMANA FORMULARIO *****************************************/

	/***************************** DIA FORMULARIO ****************************************************/
	$('#dia').change(function(){ 
		localStorage.setItem("wappsi_dia", $("#dia").val()); // cuando cambia el día seteamos el cache del día
	})
	/***************************** DIA FORMULARIO ****************************************************/

	/******************************************** MUNICIPIO FORMULARIO ********************************************/
	$( "#municipio" ).change(function() {
		localStorage.setItem("wappsi_municipio", $("#municipio").val()); 
		cargarInstituciones($('#municipio').val(), '');
		$('#institucion').select2('val','')
	});	

	if (localStorage.getItem("wappsi_municipio") != null) {
		cargarInstituciones(localStorage.getItem("wappsi_municipio"), localStorage.getItem("wappsi_institucion"));
	}
	/******************************************** MUNICIPIO FORMULARIO ********************************************/

	/****************************** INSTITUCION FORMULARIO *********************************************/
	$( "#institucion" ).change(function() {
		localStorage.setItem("wappsi_institucion", $("#institucion").val());
		$('#sede').select2('val','');
		cargarSedes($('#institucion').val(), '');
	});	
	if(localStorage.getItem("wappsi_institucion") != null){
		cargarSedes(localStorage.getItem("wappsi_institucion"), localStorage.getItem("wappsi_sede"));	
	}
	/****************************** INSTITUCION FORMULARIO *********************************************/

	/******************************* SEDE EDUCATIVA FORMULARIO ****************************************************/
	$( "#sede" ).change(function() {
		if ($('#sede').val() != '') {
			if ($('#semana').val() != '') {
				localStorage.setItem("wappsi_sede", $("#sede").val(), $('#semana').val());
			}
		}
	});
	/******************************* SEDE EDUCATIVA FORMULARIO ****************************************************/

	cargarMunicipios(localStorage.getItem("wappsi_municipio")); // llamamos los municipios apenas carga la pagina
});


function cargarSemanas(mes, semana){
	var formData = new FormData();
	formData.append('mes', mes); 
	formData.append('semana', semana);
	$.ajax({
		type: "post",
		url: "functions/fn_buscar_semanas.php",
		dataType: "html",
		contentType: false,
		processData: false,
		data: formData,
		beforeSend: function(){ $('#loader').fadeIn(); },
		success: function(data){
			$('#semana').select2('destroy');
			$('#semana').html(data);
			$('#semana').select2();
			$('#loader').fadeOut();
		},
		error: function(data){
		}
	});
}

function cargarDias(mes, semana, dia){
	var formData = new FormData();
	formData.append('mes', mes);
	formData.append('semana', semana);
	formData.append('dia', dia);
	$.ajax({
		type: "post",
		url: "functions/fn_buscar_dias.php",
		dataType: "html",
		contentType: false,
		processData: false,
		data: formData,
		beforeSend: function(){ $('#loader').fadeIn(); },
		success: function(data){
			$('#dia').select2('destroy');
			$('#dia').html(data);
			$('#dia').select2();
			$('#loader').fadeOut();
		},
		error: function(data){

		}
	});
}

function cargarMunicipios(municipio){
	var formData = new FormData();
	formData.append('municipioCache', municipio);
	$.ajax({
		type: "post",
		url: "functions/fn_buscar_municipios.php",
		dataType: "json",
		contentType: false,
		processData: false,
		data: formData,
		beforeSend: function(){ $('#loader').fadeIn(); },
		success: function(data){
			if(data.estado == 1){
				$('#municipio').select2('destroy');
				$('#municipio').html(data.opciones);
				$('#municipio').select2();
				$('#loader').fadeOut();
				var instituto = ($('#institucion').val() == '' ) ? localStorage.getItem("wappsi_institucion") : $('#institucion').val();
				cargarInstituciones($('#municipio').val(), instituto);
			}
			else{
				Command:toastr.error(data.mensaje,"Error",{onHidden:function(){$('#loader').fadeOut();}});
			}
		},
		error: function(data){
		}
	});
}

function cargarInstituciones(municipio, institucion){
	var formData = new FormData();
	formData.append('municipio', municipio);
	formData.append('institucion', institucion);
	if($('#validacion').val() != null){
		formData.append('validacion', $('#validacion').val());
	}
	$.ajax({
		type: "post",
		url: "functions/fn_buscar_instituciones.php",
		dataType: "html",
		contentType: false,
		processData: false,
		data: formData,
		beforeSend: function(){ $('#loader').fadeIn(); },
		success: function(data){
			$('#institucion').select2('destroy');
			$('#institucion').html(data);
			$('#institucion').select2();
			$('#loader').fadeOut();
		},
		error: function(data){
		}
	});
}

function cargarSedes(institucion, sede){
	var formData = new FormData();
	formData.append('institucion', institucion);
	formData.append('sede', sede);
	if($('#validacion').val() != null){
		formData.append('validacion', $('#validacion').val());
	}
	$.ajax({
		type: "post",
		url: "functions/fn_buscar_sede.php",
		dataType: "html",
		contentType: false,
		processData: false,
		data: formData,
		beforeSend: function(){ $('#loader').fadeIn(); },
		success: function(data){
			$('#sede').select2('destroy');
			$('#sede').html(data);
			$('#sede').select2();
			$('#loader').fadeOut();	
		},
		error: function(data){
		}
	});
}
