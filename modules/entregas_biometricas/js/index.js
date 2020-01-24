/**
 * @author Ricardo Farfán <ricardo@xlogam.com>
 */


$(document).ready(function(){
	$( "#btnBuscar" ).click(function() {
		$('#loader').fadeIn();
		if($('#form_asistencia').valid()){
			//cargarEstudiantes();
			console.log('Se va a cargar la interfaz de registro.');
			$('#form_asistencia').attr('action', 'dashboard.php');
			$('#form_asistencia').submit();
			$('#loader').fadeOut();
		}else{
			$('#loader').fadeOut();
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
					cargarNiveles();
					cargarDispositivos();
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
	}else{
		formData.append('semanaActual', $('#semanaActual').val());
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



function cargarDispositivos(){
	console.log('Buscando los dispositivos asociados con la sede.');
	var formData = new FormData();
	
	if($('#semana').val() != "" && $('#semana').val() != null){
		formData.append('semanaActual', $('#semana').val());
	}else{
		formData.append('semanaActual', $('#semanaActual').val());
	}

	formData.append('sede', $('#sede').val());
	$.ajax({
		type: "post",
		url: "functions/fn_buscar_dispositivos.php",
		dataType: "json",
		contentType: false,
		processData: false,
		data: formData,
		beforeSend: function(){ $('#loader').fadeIn(); },
		success: function(data){
			if(data.estado == 1){
				$('#dispositivo').html(data.opciones);
				if($('#dispositivo').val() != ""){
					//cargarGrados();
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
