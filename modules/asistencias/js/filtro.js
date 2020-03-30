jQuery.extend(jQuery.validator.messages, { required: "Este campo es obligatorio.", remote: "Por favor, rellena este campo.", email: "Por favor, escribe una dirección de correo válida", url: "Por favor, escribe una URL válida.", date: "Por favor, escribe una fecha válida.", dateISO: "Por favor, escribe una fecha (ISO) válida.", number: "Por favor, escribe un número entero válido.", digits: "Por favor, escribe sólo dígitos.", creditcard: "Por favor, escribe un número de tarjeta válido.", equalTo: "Por favor, escribe el mismo valor de nuevo.", accept: "Por favor, escribe un valor con una extensión aceptada.", maxlength: jQuery.validator.format("Por favor, no escribas más de {0} caracteres."), minlength: jQuery.validator.format("Por favor, no escribas menos de {0} caracteres."), rangelength: jQuery.validator.format("Por favor, escribe un valor entre {0} y {1} caracteres."), range: jQuery.validator.format("Por favor, escribe un valor entre {0} y {1}."), max: jQuery.validator.format("Por favor, escribe un valor menor o igual a {0}."), min: jQuery.validator.format("Por favor, escribe un valor mayor o igual a {0}.") });

$(document).ready(function(){

	cargarMunicipios();

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
	});

	$( "#semana" ).change(function() {
		localStorage.setItem("wappsi_semana", $("#semana").val());
		cargarDias();
	});

	$( "#dia" ).change(function() {
		localStorage.setItem("wappsi_dia", $("#dia").val());
	});

	$( "#municipio" ).change(function() {
		cargarInstituciones();
	});	

	$( "#institucion" ).change(function() {
		localStorage.setItem("wappsi_institucion", $("#institucion").val());
		cargarSedes();
	});	

	$( "#sede" ).change(function() {
		localStorage.setItem("wappsi_sede", $("#sede").val());
		cargarNiveles();
		cargarComplementos();
	});

	$( "#nivel" ).change(function() {
		cargarGrados();
	});

	$( "#grado" ).change(function() {
		if($('#grupo').length){
			cargarGrupos();
		}
	});
});

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

function cargarMunicipios(){
	//formData.append('municipio', $('#municipio').val());
	$.ajax({
		type: "post",
		url: "functions/fn_buscar_municipios.php",
		dataType: "json",
		contentType: false,
		processData: false,
		//data: formData,
		beforeSend: function(){ $('#loader').fadeIn(); },
		success: function(data){
			if(data.estado == 1){
				$('#municipio').html(data.opciones);
				$('#loader').fadeOut();
				cargarInstituciones();
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

function cargarInstituciones(){
	console.log("Cargar Instituciones.");
	var formData = new FormData();
	formData.append('municipio', $('#municipio').val());
	if($('#validacion').val() != null){
		console.log($('#validacion').val());
		formData.append('validacion', $('#validacion').val());
	}

	$.ajax({
		type: "post",
		url: "functions/fn_buscar_instituciones.php",
		dataType: "json",
		contentType: false,
		processData: false,
		data: formData,
		beforeSend: function(){ $('#loader').fadeIn(); },
		success: function(data){
			if(data.estado == 1){
				$('#institucion').html(data.opciones);
				
				$('#institucion').val(localStorage.getItem("wappsi_institucion"));
				localStorage.setItem("wappsi_institucion", $("#institucion").val());
				if($('#institucion').val() != ""){
					cargarSedes()
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

function cargarSedes(){
	console.log("Cargar Sedes.");
	var formData = new FormData();
	formData.append('institucion', $('#institucion').val());
	if($('#validacion').val() != null){
		console.log($('#validacion').val());
		formData.append('validacion', $('#validacion').val());
	}
	$.ajax({
		type: "post",
		url: "functions/fn_buscar_sede.php",
		dataType: "json",
		contentType: false,
		processData: false,
		data: formData,
		beforeSend: function(){ $('#loader').fadeIn(); },
		success: function(data){
			if(data.estado == 1){
				$('#sede').html(data.opciones);
				$('#sede').val(localStorage.getItem("wappsi_sede"));
				localStorage.setItem("wappsi_institucion", $("#institucion").val());
				if($('#sede').val() != ""){
					cargarNiveles()
					cargarComplementos();
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

function cargarNiveles(){
	var formData = new FormData();
	
	if($('#semana').val() != "" && $('#semana').val() != null){
		formData.append('semanaActual', $('#semana').val());
		console.log("Se usó semana.");
		console.log($('#semana').val());
	}else{
		formData.append('semanaActual', $('#semanaActual').val());
		console.log("Se usó semana actual.");
		console.log($('#semanaActual').val());
	}

	formData.append('sede', $('#sede').val());
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
				$('#nivel').html(data.opciones);
				if($('#nivel').val() != ""){
					cargarGrados();
				}
				$('#loader').fadeOut();

			}
			else{
				Command:toastr.error(data.mensaje,"Error",{onHidden:function(){$('#loader').fadeOut();}});
			}
		},
		error: function(data){
			console.log("Error");
			console.log("Puede que no este la tabla de focalización para la semana actual o elegida en el filtro.");
			console.log(data);
			$("#sede").val("");
			//Command:toastr.error("Al parecer existe un problema con el servidor.","Error en el Servidor",{onHidden:function(){$('#loader').fadeOut();}});
		}
	});
}

function cargarGrados(){
	console.log("Función Cargar Grados");
	var formData = new FormData();

	if($('#semana').val() != "" && $('#semana').val() != null){
		formData.append('semanaActual', $('#semana').val());
	}else{
		formData.append('semanaActual', $('#semanaActual').val());
	}

	formData.append('sede', $('#sede').val());
	formData.append('nivel', $('#nivel').val());

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
				$('#grado').html(data.opciones);
				if($('#grado').val() != ""){
					if($('#grupo').length){
						cargarGrupos();
					}
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

function cargarGrupos(){
	console.log("Función Cargar Grupos");
	var formData = new FormData();


	if($('#semana').val() != "" && $('#semana').val() != null){
		formData.append('semanaActual', $('#semana').val());
	}else{
		formData.append('semanaActual', $('#semanaActual').val());
	}

	formData.append('grado', $('#grado').val());
	formData.append('sede', $('#sede').val());
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
				$('#grupo').html(data.opciones);
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
			console.log(data);
			Command:toastr.error("Al parecer existe un problema con el servidor.","Error en el Servidor",{onHidden:function(){$('#loader').fadeOut();}});
		}
	});
}

function cargarComplementos(){
	var formData = new FormData();
	
	if($('#semana').val() != "" && $('#semana').val() != null){
		formData.append('semanaActual', $('#semana').val());
		console.log("Se usó semana.");
		console.log($('#semana').val());
	}else{
		formData.append('semanaActual', $('#semanaActual').val());
		console.log("Se usó semana actual.");
		console.log($('#semanaActual').val());
	}

	formData.append('sede', $('#sede').val());

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
				$('#complemento').html(data.opciones);
				$('#loader').fadeOut();

			}
			else{
				Command:toastr.error(data.mensaje,"Error",{onHidden:function(){$('#loader').fadeOut();}});
			}
		},
		error: function(data){
			console.log("Error");
			console.log("Puede que no este la tabla de focalización para la semana actual o elegida en el filtro.");
			console.log(data);
			$("#sede").val("");
			//Command:toastr.error("Al parecer existe un problema con el servidor.","Error en el Servidor",{onHidden:function(){$('#loader').fadeOut();}});
		}
	});
}

function actualizarMarcadores(flagConsumo){
	console.log('Función para actualizar los marcadores con los datos de asistencia almacenados en la base de datos.');
	
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
				//console.log(data.asistencia);
				var aux = [];
				console.log("Actualización de marcadores !!!");
				for (var i = 0; i < data.asistencia.length; i+=1) {
  					//console.log(data.asistencia[i]);
  					aux = data.asistencia[i];
  					// console.log(aux);
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

				console.log("Total");
				console.log(total);
				console.log("Reg Faltan");
				console.log(reg_faltan);				
				console.log("Reg Ausentes"); 
				console.log(reg_ausentes);				
				console.log("Reg Repitentes");
				console.log(reg_repitentes);
				console.log("Reg Consumieron");
				console.log(reg_consumieron);				
				console.log("Reg Repitieron");
				console.log(reg_repitieron);

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
					console.log("Inviertiendo faltan: "+faltan);
					$(".asistenciaFaltantes").html(faltan);
				}

			}		
		},
		error: function(data){
			console.log(data);
			Command:toastr.error("Al parecer existe un problema con el servidor.","Error en el Servidor",{onHidden:function(){$('#loader').fadeOut();}});
		}
	});
}

