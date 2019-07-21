jQuery.extend(jQuery.validator.messages, { required: "Este campo es obligatorio.", remote: "Por favor, rellena este campo.", email: "Por favor, escribe una dirección de correo válida", url: "Por favor, escribe una URL válida.", date: "Por favor, escribe una fecha válida.", dateISO: "Por favor, escribe una fecha (ISO) válida.", number: "Por favor, escribe un número entero válido.", digits: "Por favor, escribe sólo dígitos.", creditcard: "Por favor, escribe un número de tarjeta válido.", equalTo: "Por favor, escribe el mismo valor de nuevo.", accept: "Por favor, escribe un valor con una extensión aceptada.", maxlength: jQuery.validator.format("Por favor, no escribas más de {0} caracteres."), minlength: jQuery.validator.format("Por favor, no escribas menos de {0} caracteres."), rangelength: jQuery.validator.format("Por favor, escribe un valor entre {0} y {1} caracteres."), range: jQuery.validator.format("Por favor, escribe un valor entre {0} y {1}."), max: jQuery.validator.format("Por favor, escribe un valor menor o igual a {0}."), min: jQuery.validator.format("Por favor, escribe un valor mayor o igual a {0}.") });


$(document).ready(function(){

	cargarTiposComplementos();
	cargarGruposEtarios();

	if(localStorage.getItem("wappsi_mes") != null){
		$( "#mes" ).val(localStorage.getItem("wappsi_mes"));
		cargarSemanas();	
	}

	if(localStorage.getItem("wappsi_dia") != null){
		$( "#dia" ).val(localStorage.getItem("wappsi_dia"));
	}

	$( "#mes" ).change(function() {
		localStorage.setItem("wappsi_mes", $("#mes").val());
		cargarSemanas();
		if($("#codigoMenu").length > 0){
			buscarMenu();
		}
	});

	$( "#semana" ).change(function() {
		localStorage.setItem("wappsi_semana", $("#semana").val());
		cargarDias();
		if($("#codigoMenu").length > 0){
			buscarMenu();
		}
	});

	$( "#dia" ).change(function() {
		localStorage.setItem("wappsi_dia", $("#dia").val());
		if($("#codigoMenu").length > 0){
			buscarMenu();
		}
	});

	$( "#tipoComplemento" ).change(function() {
		if($("#codigoMenu").length > 0){
			buscarMenu();
		}
	});

	$( "#grupoEtario" ).change(function() {
		if($("#codigoMenu").length > 0){
			buscarMenu();
		}
	});




	




});

function cargarSemanas(){
	var formData = new FormData();
	formData.append('mes', $('#mes').val());

	$.ajax({
		type: "post",
		url: "functions/fn_buscar_semanas.php",
		dataType: "json",
		contentType: false,
		processData: false,
		data: formData,
		beforeSend: function(){ $('#loader').fadeIn(); },
		success: function(data){
			if(data.estado == 1){
				$('#semana').html(data.opciones);
				$('#semana').val(localStorage.getItem("wappsi_semana"));
				localStorage.setItem("wappsi_semana", $("#semana").val());
				if($('#semana').val() != ""){
					cargarDias()
				}
				$('#loader').fadeOut();
			}
			else{
				Command:toastr.error(data.mensaje,"Error",{onHidden:function(){$('#loader').fadeOut();}});
			}
		},
		error: function(data){
			console.log(data);
			Command:toastr.error("Al parecer existe un problema con el servidor.","Error en el Servidor",{onHidden:function(){$('#loader').fadeOut();}});
		}
	});
}

function cargarDias(){
	var formData = new FormData();
	formData.append('mes', $('#mes').val());
	formData.append('semana', $('#semana').val());
	$.ajax({
		type: "post",
		url: "functions/fn_buscar_dias.php",
		dataType: "json",
		contentType: false,
		processData: false,
		data: formData,
		beforeSend: function(){ $('#loader').fadeIn(); },
		success: function(data){
			if(data.estado == 1){
				$('#dia').html(data.opciones);
				$('#dia').val(localStorage.getItem("wappsi_dia"));
				localStorage.setItem("wappsi_dia", $("#dia").val());
				// if($('#semana').val() != ""){
				// 	cargarDias()
				// }
				$('#loader').fadeOut();
			}
			else{
				Command:toastr.error(data.mensaje,"Error",{onHidden:function(){$('#loader').fadeOut();}});
			}
		},
		error: function(data){
			console.log(data);
			Command:toastr.error("Al parecer existe un problema con el servidor.","Error en el Servidor",{onHidden:function(){$('#loader').fadeOut();}});
		}
	});
}

function cargarTiposComplementos(){
	console.log("Cargar tipos de complementos.");
	$.ajax({
		type: "post",
		url: "functions/fn_buscar_tipos_complementos.php",
		dataType: "json",
		contentType: false,
		processData: false,
		beforeSend: function(){ $('#loader').fadeIn(); },
		success: function(data){
			if(data.estado == 1){
				$('#tipoComplemento').html(data.opciones);				
				$('#loader').fadeOut();
			}
			else{
				Command:toastr.error(data.mensaje,"Error",{onHidden:function(){$('#loader').fadeOut();}});
			}
		},
		error: function(data){
			console.log(data);
			Command:toastr.error("Al parecer existe un problema con el servidor.","Error en el Servidor",{onHidden:function(){$('#loader').fadeOut();}});
		}
	});
}

function cargarGruposEtarios(){
	console.log("Cargar grupos etarios.");
	$.ajax({
		type: "post",
		url: "functions/fn_buscar_grupos_etarios.php",
		dataType: "json",
		contentType: false,
		processData: false,
		beforeSend: function(){ $('#loader').fadeIn(); },
		success: function(data){
			if(data.estado == 1){
				$('#grupoEtario').html(data.opciones);				
				$('#loader').fadeOut();
			}
			else{
				Command:toastr.error(data.mensaje,"Error",{onHidden:function(){$('#loader').fadeOut();}});
			}
		},
		error: function(data){
			console.log(data);
			Command:toastr.error("Al parecer existe un problema con el servidor.","Error en el Servidor",{onHidden:function(){$('#loader').fadeOut();}});
		}
	});
}

function buscarMenu(){
	if($('#mes').val() != "" && $('#semana').val() != "" && $('#dia').val() != "" && $('#tipoComplemento').val() != "" && $('#grupoEtario').val() != ""){
		
		console.log('Se procede a la busqueda del menú.');

		var formData = new FormData();
		formData.append('mes', $('#mes').val());
		formData.append('semana', $('#semana').val());
		formData.append('dia', $('#dia').val());
		formData.append('tipoComplemento', $('#tipoComplemento').val());
		formData.append('grupoEtario', $('#grupoEtario').val());

		$.ajax({
			type: "post",
			url: "functions/fn_buscar_menu.php",
			dataType: "json",
			contentType: false,
			processData: false,
			data: formData,
			beforeSend: function(){ $('#loader').fadeIn(); },
			success: function(data){
				if(data.estado == 1){
					//console.log(data);
					$('#codigoMenu').val(data.codigoMenu);
					$('#menu').val(data.codigoMenu+" - "+data.descripcionMenu);
					if($('#preparaciones').length != 0){
						buscarPreparaciones();
					}
					$('#loader').fadeOut();
				}
				else{
					Command:toastr.error(data.mensaje,"Error",{onHidden:function(){$('#loader').fadeOut();}});
				}
			},
			error: function(data){
				console.log(data);
				Command:toastr.error("Al parecer existe un problema con el servidor.","Error en el Servidor",{onHidden:function(){$('#loader').fadeOut();}});
			}
		});
	}
}

function buscarPreparaciones(){
	console.log('Buscar preparaciones.');
	
	var formData = new FormData();
	formData.append('codigoMenu', $('#codigoMenu').val());

	$.ajax({
		type: "post",
		url: "functions/fn_buscar_preparaciones.php",
		dataType: "json",
		contentType: false,
		processData: false,
		data: formData,
		beforeSend: function(){ $('#loader').fadeIn(); },
		success: function(data){
			if(data.estado == 1){
				$('#preparaciones').html(data.opciones);			
				$('#loader').fadeOut();
			}
			else{
				Command:toastr.error(data.mensaje,"Error",{onHidden:function(){$('#loader').fadeOut();}});
			}
		},
		error: function(data){
			console.log(data);
			Command:toastr.error("Al parecer existe un problema con el servidor.","Error en el Servidor",{onHidden:function(){$('#loader').fadeOut();}});
		}
	});
}