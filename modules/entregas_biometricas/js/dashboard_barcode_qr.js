$(document).ready(function(){
	mueveReloj();
	fechaActual();

	$('#lector').keypress(function(event){
		var keycode = (event.keyCode ? event.keyCode : event.which);
		if(keycode == '13'){
			event.preventDefault()
			nuevoRegistro();
			//alert('You pressed a "enter" key in textbox');  
			
		}
	});
	
	// $( "#lector" ).change(function() {
	// 	nuevoRegistro();
	// });

	$( "#btn-lector" ).click(function() {
		nuevoRegistro();
	});

	$("#lector").focus();

	// cargarMunicipios();
	// if(localStorage.getItem("wappsi_mes") != null){
	// 	$( "#mes" ).val(localStorage.getItem("wappsi_mes"));
	// 	cargarSemanas();	
	// }

});

function fechaActual(){
	let date = new Date();
	let day = date.getDate();
	let month = date.getMonth() + 1;
	let year = date.getFullYear();
	
	if(month < 10){
	  console.log(`${day}/0${month}/${year}`);
	  fechaActual = `${day}/0${month}/${year}`;
	}else{
	  console.log(`${day}/${month}/${year}`);
	  fechaActual = `${day}/${month}/${year}`;
	}
	$('.fecha-actual').html(fechaActual);
}

function mueveReloj(){ 
	momentoActual = new Date();
	hora = momentoActual.getHours();
	minuto = momentoActual.getMinutes();
	segundo = momentoActual.getSeconds();
	
	var ampm = hora >= 12 ? 'p.m.' : 'a.m.';
	hora = hora % 12;
	hora = hora ? hora : 12; // the hour '0' should be '12'
	hora = hora < 10 ? '0'+hora : hora;
	minuto = minuto < 10 ? '0'+minuto : minuto;
	segundo = segundo < 10 ? '0'+segundo : segundo;

	horaImprimible = hora + " : " + minuto + " : " + segundo + ' ' + ampm;

	//document.form_reloj.reloj.value = horaImprimible 
	$('.hora-actual').html(horaImprimible);

	//La función se tendrá que llamar así misma para que sea dinámica, 
	//de esta forma:

	setTimeout(mueveReloj,1000);
}

function nuevoRegistro(){
	console.log('Función para nuevo registro.');
	var lector = $('#lector').val();
	var dispositivo = $('#dispositivo').val();
	if(lector != ''){
		console.log(lector);
		var formData = new FormData();
		formData.append('lector', lector);
		formData.append('dispositivo', dispositivo);
		$.ajax({
			type: "post",
			url: "functions/fn_buscar_datos_registro_qr.php",
			dataType: "json",
			contentType: false,
			processData: false,
			data: formData,
			beforeSend: function(){ $('#loader').fadeIn(); },
			success: function(data){
				console.log(data);
				if(data.estado == 1){
					$('.entregas-qr').prepend(data.fila);
					$('#loader').fadeOut();
					$("#lector").html('');
					
					
					
					// console.log('Terminada la verificación de nuevos registros.');	
					// if(totalEntregas > 0){
					// 	var codSede = data.codSede;
					// 	$('.entregas').prepend(data.cuerpo);
					// 	console.log("Sede que recibió registro: "+codSede);
					// 	var aux = $('.entregado-'+codSede).html();
					// 	aux = parseInt(aux);
					// 	aux++;
					// 	$('.entregado-'+codSede).html(aux);
		
					// 	//Aumentando el contador de lo entregado
					// 	totalEntregado = totalEntregado + 1;
						
					// 	// Actualizando el Id del ultimo registro procesado
					// 	ultimoRegistro = data.ultimoRegistro;
	
					// 	if ( $(".entrega").length > 9){
					// 		$(".entrega").last().remove();
					// 	}
					// }
	
	
					//entregas
	
	
					// $('.sedes').html(data.cuerpo);
					// ultimoRegistro = data.ultimo_registro;
					// console.log("Ultimo Registro: "+ultimoRegistro);
	
					// // $('#dia').html(data.opciones);
					// // $('#dia').val(localStorage.getItem("wappsi_dia"));
					// // localStorage.setItem("wappsi_dia", $("#dia").val());
					// // if($('#semana').val() != ""){
					// // 	cargarDias()
					// // }
					$("#lector").val('');
					$("#lector").focus();
	
				}
				else{
					//Command:toastr.error(data.mensaje,"Error",{onHidden:function(){$('#loader').fadeOut();}});
				}
			},
			error: function(data){
				console.log(data);
				Command:toastr.error("Al parecer existe un problema con el servidor.","Error en el Servidor",{onHidden:function(){$('#loader').fadeOut();}});
			}
		});
	}
	else{
		console.log("El campo del documento está vacío.");
	}
}