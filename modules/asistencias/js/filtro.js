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
	$('select').select2();
	$('.containerStudents').hide();
	// $('.btnGuardar').hide();
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
		$('#nivel').select2('val', '');
		$('#grado').select2('val', '');
		$('#grupo').select2('val', '');
		$('#complemento').select2('val','');
		if ($('#sede').val() != '') {
			if ($('#semana').val() != '') {
				localStorage.setItem("wappsi_sede", $("#sede").val(), $('#semana').val());
				cargarNiveles($('#sede').val(), $('#nivel').val(), $('#semana').val());
				cargarComplementos($('#sede').val(), $('#complemento').val(), $('#semana').val());
				
			}
		}
	});
	if(localStorage.getItem("wappsi_sede") != null){
		cargarNiveles(localStorage.getItem("wappsi_sede"), localStorage.getItem("wappsi_nivel"), localStorage.getItem("wappsi_semana"));
		cargarComplementos(localStorage.getItem("wappsi_sede"), localStorage.getItem("wappsi_complemento"), localStorage.getItem("wappsi_semana"));
	}
	/******************************* SEDE EDUCATIVA FORMULARIO ****************************************************/

	/**************************** NIVEL FORMULARIO *****************************************/
	$( "#nivel" ).change(function() {
		localStorage.setItem("wappsi_nivel", $('#nivel').val());
		cargarGrados($('#sede').val(), $('#nivel').val(), $('#grado').val(), $('#semana').val());
		$('#grado').select2('val','');
	});
	if (localStorage.getItem('wappsi_nivel') != null) {
		cargarGrados(localStorage.getItem("wappsi_sede"), localStorage.getItem("wappsi_nivel"), localStorage.getItem("wappsi_grado"), localStorage.getItem("wappsi_semana") );
	}
	/**************************** NIVEL FORMULARIO *****************************************/

	/**************************** GRADO FORMULARIO ************************************/
	$( "#grado" ).change(function() {
		localStorage.setItem("wappsi_grado", $('#grado').val());
		if($('#grupo').length){
			cargarGrupos($('#sede').val(), $('#grado').val(), $('#grupo').val(), $('#semana').val());
			$('#grupo').select2('val', '');
		}
	});
	if (localStorage.getItem("wappsi_grado") != null) {
		cargarGrupos(localStorage.getItem("wappsi_sede"), localStorage.getItem("wappsi_grado"), localStorage.getItem("wappsi_grupo"), localStorage.getItem("wappsi_semana"));
	}
	/**************************** GRADO FORMULARIO ************************************/

	/********************************* GRUPO FORMULARIO *********************************************/
	$( "#grupo" ).change(function() {
		localStorage.setItem("wappsi_grupo", $('#grupo').val());
	});
	/********************************* GRUPO FORMULARIO *********************************************/

	/********************* COMPLEMENTO FORMULARIO ************************/
	$( "#complemento" ).change(function() {
		localStorage.setItem("wappsi_complemento", $('#complemento').val());
	});
	/********************* COMPLEMENTO FORMULARIO ************************/
	
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

function cargarNiveles(sede, nivel, semana){
	var formData = new FormData();
	if (semana == '' || semana === null) {
		formData.append('semanaActual', $('#semanaActual').val());
	}else{
		formData.append('semanaActual', semana);
	}
	formData.append('sede', sede);
	formData.append('nivel', nivel);
	$.ajax({
		type: "post",
		url: "functions/fn_buscar_niveles.php",
		dataType: "json",
		contentType: false,
		processData: false,
		data: formData,
		beforeSend: function(){ $('#loader').fadeIn(); },
		success: function(data){
			if(data.estado == 1){
				if($('#nivel').val() !== undefined){
					$('#nivel').select2('destroy');
					$('#nivel').html(data.opciones);
					$('#nivel').select2();
					$('#loader').fadeOut();
				}
			}
			else{
				Command:toastr.error(data.mensaje,"Error",{onHidden:function(){$('#loader').fadeOut();}});
			}
		},
		error: function(data){
		}
	});
}

function cargarGrados(sede, nivel, grado, semana){
	var formData = new FormData();
	if (semana == '' || semana === null) {
		formData.append('semanaActual', $('#semanaActual').val());
	}else{
		formData.append('semanaActual', semana);
	}
	formData.append('sede', sede);
	formData.append('nivel', nivel);
	formData.append('grado', grado);
	$.ajax({
		type: "post",
		url: "functions/fn_buscar_grados.php",
		dataType: "json",
		contentType: false,
		processData: false,
		data: formData,
		beforeSend: function(){ $('#loader').fadeIn(); },
		success: function(data){
			if(data.estado == 1){
				if ($('#grado').val() !== undefined) {
					$('#grado').select2('destroy');
					$('#grado').html(data.opciones);
					$('#grado').select2();
					$('#loader').fadeOut();
				}
			}
			else{
				Command:toastr.error(data.mensaje,"Error",{onHidden:function(){$('#loader').fadeOut();}});
			}
		},
		error: function(data){

		}
	});
}

function cargarGrupos(sede, grado, grupo, semana){
	var formData = new FormData();
	if (semana == '' || semana === null) {
		formData.append('semanaActual', $('#semanaActual').val());
	}else{
		formData.append('semanaActual', semana);
	}
	formData.append('grado', grado);
	formData.append('sede', sede);
	formData.append('grupo', grupo);
	$.ajax({
		type: "post",
		url: "functions/fn_buscar_grupos.php",
		dataType: "json",
		contentType: false,
		processData: false,
		data: formData,
		beforeSend: function(){ $('#loader').fadeIn(); },
		success: function(data){
			if(data.estado == 1){
				if ($('#grupo').val() !== undefined) {
					$('#grupo').select2('destroy');
					$('#grupo').html(data.opciones);
					$('#grupo').select2();
					$('#loader').fadeOut();
				}
			}
			else{
				Command:toastr.error(data.mensaje,"Error",{onHidden:function(){$('#loader').fadeOut();}});
			}
		},
		error: function(data){
			Command:toastr.error("Al parecer existe un problema con el servidor.","Error en el Servidor",{onHidden:function(){$('#loader').fadeOut();}});
		}
	});
}

function cargarComplementos(sede, complemento, semana){
	var formData = new FormData();
	if (semana == '' || semana === null) {
		formData.append('semanaActual', $('#semanaActual').val());
	}else{
		formData.append('semanaActual', semana);
	}
	formData.append('sede', sede);
	formData.append('complemento', complemento)
	$.ajax({
		type: "post",
		url: "functions/fn_buscar_complementos.php",
		dataType: "json",
		contentType: false,
		processData: false,
		data: formData,
		beforeSend: function(){ $('#loader').fadeIn(); },
		success: function(data){
			if(data.estado == 1){
				if($('#complemento').val() !== undefined){
					$('#complemento').select2('destroy');
					$('#complemento').html(data.opciones);
					$('#complemento').select2();
					$('#loader').fadeOut();
				}
			}
			else{
				Command:toastr.error(data.mensaje,"Error",{onHidden:function(){$('#loader').fadeOut();}});
			}
		},
		error: function(data){
			//Command:toastr.error("Al parecer existe un problema con el servidor.","Error en el Servidor",{onHidden:function(){$('#loader').fadeOut();}});
		}
	});
}


function totalEstudiantesSede(){
	var formData = new FormData();
	if($('#semana').val() != "" && $('#semana').val() != null){
		formData.append('semanaActual', $('#semana').val());
	}else{
		formData.append('semanaActual', $('#semanaActual').val());
	}
	
	formData.append('sede', $('#sede').val());
	formData.append('complemento', $('#complemento').val());
	$.ajax({
		type: "post",
		url: "functions/fn_buscar_total_estudiantes.php",
		dataType: "json",
		contentType: false,
		processData: false,
		data: formData,
		beforeSend: function(){ 
			//$('#loader').fadeIn();
			 },
		success: function(data){
			if(data.estado == 1){
				total = data.total;
				localStorage.setItem("wappsi_total", total);
				$(".asistenciaTotal").html(total);
				//$('#loader').fadeOut();
			}		
		},
		error: function(data){
		}
	});
}


function actualizarMarcadores(flagConsumo){
	totalEstudiantesSede();
	reg_faltan = 0;
	reg_ausentes = [];
	reg_repitentes = [];
	reg_consumieron = [];
	reg_repitieron =[];
	var formData = new FormData();

	if($('#dia').val() != "" && $('#dia').val() != null){
		formData.append('dia', $('#dia').val());
	}
	if($('#mes').val() != "" && $('#mes').val() != null){
		formData.append('mes', $('#mes').val());
	}
	if($('#semana').val() != "" && $('#semana').val() != null){
		formData.append('semanaActual', $('#semana').val());
	}else{
		formData.append('semanaActual', $('#semanaActual').val());
	}

	formData.append('sede', $('#sede').val());
	formData.append('complemento', $('#complemento').val());
	$.ajax({
		type: "post",
		url: "functions/fn_cargar_asistencia_marcadores.php",
		dataType: "json",
		contentType: false,
		processData: false,
		data: formData,
		beforeSend: function(){ 
			//$('#loader').fadeIn();
			 },
		success: function(data){
			if(data.estado == 1){
				var aux = [];
				for (var i = 0; i < data.asistencia.length; i+=1) {
  					aux = data.asistencia[i];
  					if(aux['asistencia'] == 0){
						reg_faltan++;
						reg_ausentes.push(aux['num_doc']);
  					}
  					else{ 
  						if(aux['repite'] == 1){
  							reg_faltan--;
							reg_repitentes.push(aux['num_doc']);
						}  						
						if(aux['consumio'] == 1){
							reg_consumieron.push(aux['num_doc']);
						}
						if(aux['repitio'] == 1){
							reg_repitieron.push(aux['num_doc']);
						}
  					}
				}
				faltan = reg_faltan;
				$(".asistenciaFaltantes").html(faltan);
				localStorage.setItem("wappsi_faltan", reg_faltan);
				localStorage.setItem("wappsi_ausentes", JSON.stringify(reg_ausentes));
				localStorage.setItem("wappsi_repitentes", JSON.stringify(reg_repitentes));
				localStorage.setItem("wappsi_consumieron", JSON.stringify(reg_consumieron));
				localStorage.setItem("wappsi_repitieron", JSON.stringify(reg_repitieron));

				// Habilitar los checkbox de los que podrian repetir
				if(faltan > 0 && flagConsumo != 1){
					$( ".checkbox-header-repite:not(:checked)").iCheck('enable'); 
				}

				if(flagConsumo == 1){
					faltan = total;
					faltan = faltan - reg_consumieron.length;
					faltan = faltan - reg_repitieron.length;
					localStorage.setItem("wappsi_faltan", faltan);
					$(".asistenciaFaltantes").html(faltan);
				}
			}else if (data.estado == 0) {
				faltan = 0;
				$(".asistenciaFaltantes").html(faltan);
			}	
		},
		error: function(data){
		}
	});
}

