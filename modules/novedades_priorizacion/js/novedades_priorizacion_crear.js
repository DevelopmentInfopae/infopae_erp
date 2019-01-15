function crearNovedadPriorizacion(){
  window.open('novedades_priorizacion_crear.php', '_self');
}
$(document).ready(function(){
	buscar_municipios();


    $('#municipio').change(function(){
        var municipio = $(this).val();
		buscar_institucion(municipio);
		$('#sede').html('<option value = "">Seleccione una</option>');
	});

	$('#institucion').change(function(){
        var institucion = $(this).val();
        buscar_sede(institucion);
	});

	$('#institucion').change(function(){
        var institucion = $(this).val();
        buscar_sede(institucion);
	});

	$('#sede').change(function(){
        var sede = $(this).val();
        buscar_meses(sede);
    });

	$('#mes').change(function(){
        var mes = $(this).val();
		var sede = $('#sede').val();
        buscar_semanas(mes,sede);
    });

    $('#btnBuscar').click(function(){
		//$("#myModal").modal();
		validar_semanas_cantidades();
	});

	$('.tablaNuevasCantidades input').change(function(){
		totalizar();
	});

	$('.guaradarNovedad').click(function(){
		guardar_priorizacion();
	});
});

function buscar_municipios(){
    $.ajax({
		type: "POST",
		url: "functions/fn_buscar_municipios.php",
		beforeSend: function(){
			$('#loader').fadeIn();
		},
    	success: function(data){
			try {
		  		var obj = JSON.parse(data);
				// console.log('Log');
				// console.log(obj.log);
				// console.log('Respuesta');
				// console.log(obj.respuesta);
				$('#municipio').html(obj.respuesta);
  			}
  			catch(err) {
				$('.debug').html(err.message);
				$('.debug').append('<br/><br/>');
				$('.debug').append(data);
  			}
      	}
    })
    .done(function(){ })
    .fail(function(){ })
    .always(function(){
    	$('#loader').fadeOut();
    });
}

function buscar_institucion(municipio){
    console.log('Actualizando lista de instituciones.');
    console.log(municipio);
    var datos = {"municipio":municipio};
    $.ajax({
		type: "POST",
		url: "functions/fn_buscar_institucion.php",
		data: datos,
		beforeSend: function(){
			$('#loader').fadeIn();
		},
    	success: function(data){
			try {
		  		var obj = JSON.parse(data);
				// console.log('Log');
				// console.log(obj.log);
				// console.log('Respuesta');
				// console.log(obj.respuesta);
				$('#institucion').html(obj.respuesta);
  			}
  			catch(err) {
				$('.debug').html(err.message);
				$('.debug').append('<br/><br/>');
				$('.debug').append(data);
  			}
      	}
    })
    .done(function(){ })
    .fail(function(){ })
    .always(function(){
    	$('#loader').fadeOut();
    });
}

function buscar_sede(institucion){
    console.log('Actualizando lista de sedes.');
    console.log(institucion);
    var datos = {"institucion":institucion};
    $.ajax({
		type: "POST",
		url: "functions/fn_buscar_sede.php",
		data: datos,
		beforeSend: function(){
			$('#loader').fadeIn();
		},
    	success: function(data){
			try {
		  		var obj = JSON.parse(data);
				// console.log('Log');
				// console.log(obj.log);
				// console.log('Respuesta');
				// console.log(obj.respuesta);
				$('#sede').html(obj.respuesta);
  			}
  			catch(err) {
				$('.debug').html(err.message);
				$('.debug').append('<br/><br/>');
				$('.debug').append(data);
  			}
      	}
    })
    .done(function(){ })
    .fail(function(){ })
    .always(function(){
    	$('#loader').fadeOut();
    });
}

function buscar_meses(sede){
	var datos = {"sede":sede};
    $.ajax({
		type: "POST",
		url: "functions/fn_buscar_meses.php",
		data: datos,
		beforeSend: function(){
			$('#loader').fadeIn();
		},
    	success: function(data){
			try {
		  		var obj = JSON.parse(data);
				console.log(obj);
				// console.log('Log');
				// console.log(obj.log);
				// console.log('Respuesta');
				// console.log(obj.respuesta);
				$('#mes').html(obj.respuesta);
  			}
  			catch(err) {
				$('.debug').html(err.message);
				$('.debug').append('<br/><br/>');
				$('.debug').append(data);
  			}
      	}
    })
    .done(function(){ })
    .fail(function(){ })
    .always(function(){
    	$('#loader').fadeOut();
    });
}

function buscar_semanas(mes,sede){
    console.log('Actualizando lista de semanas.');
    console.log('mes: '+mes);
    console.log('sede: '+sede);
    var datos = {"mes":mes, "sede":sede};
    $.ajax({
		type: "POST",
		url: "functions/fn_buscar_semanas.php",
		data: datos,
		beforeSend: function(){
			$('#loader').fadeIn();
		},
    	success: function(data){
			try {
		  		var obj = JSON.parse(data);
				// console.log('Log');
				// console.log(obj.log);
				// console.log('Respuesta');
				// console.log(obj.respuesta);
				$('#semana').html(obj.respuesta);
  			}
  			catch(err) {
				$('.debug').html(err.message);
				$('.debug').append('<br/><br/>');
				$('.debug').append(data);
  			}
      	}
    })
    .done(function(){ })
    .fail(function(){ })
    .always(function(){
    	$('#loader').fadeOut();
    });
}

function validar_semanas_cantidades(){
	$('.priorizacionAction').hide('fast');
	var bandera = 0;
	console.log('Buscar priorización.');
	console.log($('#municipio').val());
	console.log($('#institucion').val());
	console.log($('#sede').val());
	console.log($('#mes').val());
	console.log($('#semana').val());
	if($('#municipio').val() === undefined || $('#municipio').val() == ''){
		alert('Debe seleccionar un municipio.');
		$('#municipio').focus();
		bandera++;
	}
	else if($('#institucion').val() === undefined || $('#institucion').val() == ''){
		alert('Debe seleccionar una institución.');
		$('#institucion').focus();
		bandera++;
	}
	else if($('#sede').val() === undefined || $('#sede').val() == ''){
		alert('Debe seleccionar una sede.');
		$('#sede').focus();
		bandera++;
	}
	else if($('#mes').val() === undefined || $('#mes').val() == ''){
		alert('Debe seleccionar un mes.');
		$('#mes').focus();
		bandera++;
	}
	// Recogemos todas las semanas chequeadas
	var semanas = new Array();
    $('#semana .semana:checked').each(
		function() {
			var aux = $(this).val();
			semanas.push(aux);
		}
    );
	if(semanas.length <= 0 && bandera == 0){
		alert('Debe seleccionar por lo menos una semana.');
		$('#semana0').focus();
	}else if(bandera == 0){
		console.log(semanas);
		var sede = $('#sede').val();
		var datos = {"semanas":semanas, "sede":sede};
		$.ajax({
			type: "POST",
			url: "functions/fn_validar_semanas_cantidades.php",
			data: datos,
			beforeSend: function(){
				$('#loader').fadeIn();
			},
			success: function(data){
				try {
					var obj = JSON.parse(data);
					if(obj.respuesta == 1){
						console.log('Las semanas seleccionadas tienen las mismas cantidades.');
						buscar_priorizacion(semanas);
					}
					else{
						alert(obj.log);
					}
					console.log(obj);
				}
				catch(err) {
					$('.debug').html(err.message);
					$('.debug').append('<br/><br/>');
					$('.debug').append(data);
				}
			}
		})
		.done(function(){ })
		.fail(function(){ })
		.always(function(){
			$('#loader').fadeOut();
		});
	}
}

function buscar_priorizacion(semanas){
	// Bucaremos si hay registros de priorización para la sede seleccionada en el mes seleccionado.
	console.log('Buscando registros de priorización');

	var municipio = $('#municipio').val();
	var institucion = $('#institucion').val();
	var sede = $('#sede').val();
	var mes = $('#mes').val();

	var datos = {"municipio":municipio, "institucion":institucion, "sede":sede, "mes":mes, "semanas":semanas};

	$.ajax({
		type: "POST",
		url: "functions/fn_buscar_priorizacion.php",
		data: datos,
		beforeSend: function(){
			$('#loader').fadeIn();
		},
		success: function(data){
			try {
				var obj = JSON.parse(data);
				console.log(obj);
				// console.log('Log');
				// console.log(obj.log);
				// console.log('Respuesta');
				// console.log(obj.respuesta);
				//$('#semana').html(obj.respuesta);
				if(obj.registros > 0){
					var aps = obj.aps;
					var aps1 = obj.aps1;
					var aps2 = obj.aps2;
					var aps3 = obj.aps3;
					var cajmps = obj.cajmps;
					var cajmps1 = obj.cajmps1;
					var cajmps2 = obj.cajmps2;
					var cajmps3 = obj.cajmps3;
					var cajmri = obj.cajmri;
					var cajmri1 = obj.cajmri1;
					var cajmri2 = obj.cajmri2;
					var cajmri3 = obj.cajmri3;
					var cantEstudiantes = obj.cantEstudiantes;
					var numEstFocalizados = obj.numEstFocalizados;
					if(aps == 0){
						$('.APSactual').hide('fast');
					}else{
						$('#APSactualTotal').val(aps);
						$('#APSactual1').val(aps1);
						$('#APSactual2').val(aps2);
						$('#APSactual3').val(aps3);
						$('#APSTotal').val(aps);
						$('#APS1').val(aps1);
						$('#APS2').val(aps2);
						$('#APS3').val(aps3);
						$('.APSactual').show('fast');
					}
					if(cajmps == 0){
						$('.CAJMPSactual').hide('fast');
					}else{
						$('#CAJMPSactualTotal').val(cajmps);
						$('#CAJMPSactual1').val(cajmps1);
						$('#CAJMPSactual2').val(cajmps2);
						$('#CAJMPSactual3').val(cajmps3);
						$('.CAJMPSactual').show('fast');
					}
					if(cajmri == 0){
						$('.CAJMRIactual').hide('fast');
					}else{
						$('#CAJMRIactualTotal').val(cajmri);
						$('#CAJMRIactual1').val(cajmri1);
						$('#CAJMRIactual2').val(cajmri2);
						$('#CAJMRIactual3').val(cajmri3);
						$('.CAJMRIactual').show('fast');
					}
					totalizar();
					$('.priorizacionAction').fadeIn();
				}
			  }
			  catch(err) {
				$('.debug').html(err.message);
				$('.debug').append('<br/><br/>');
				$('.debug').append(data);
			}
		}
	})
	.done(function(){ })
	.fail(function(){ })
	.always(function(){
		$('#loader').fadeOut();
	});
}

function totalizar(){
	console.log('Cambio en las cantidades.');
	// Se va a calcular las cantidades totales para cada complemento.
	var aux = 0;
	aux = parseInt($('#APS1').val()) + parseInt($('#APS2').val()) + parseInt($('#APS3').val());
	$('#APSTotal').val(aux);
	aux = parseInt($('#CAJMPS1').val()) + parseInt($('#CAJMPS2').val()) + parseInt($('#CAJMPS3').val());
	$('#CAJMPSTotal').val(aux);
	aux = parseInt($('#CAJMRI1').val()) + parseInt($('#CAJMRI2').val()) + parseInt($('#CAJMRI3').val());
	$('#CAJMRITotal').val(aux);
	aux = parseInt($('#APSTotal').val()) + parseInt($('#CAJMPSTotal').val()) + parseInt($('#CAJMRITotal').val());
	$('#totalTotal').val(aux);
	aux = parseInt($('#APS1').val()) + parseInt($('#CAJMPS1').val()) + parseInt($('#CAJMRI1').val());
	$('#total1').val(aux);
	aux = parseInt($('#APS2').val()) + parseInt($('#CAJMPS2').val()) + parseInt($('#CAJMRI2').val());
	$('#total2').val(aux);
	aux = parseInt($('#APS3').val()) + parseInt($('#CAJMPS3').val()) + parseInt($('#CAJMRI3').val());
	$('#total3').val(aux);
}

function guardar_priorizacion(){
	console.log('Guardar priorización');
	var bandera = 0;

	var semanas = new Array();
    $('#semana .semana:checked').each(
		function() {
			var aux = $(this).val();
			semanas.push(aux);
		}
    );

	if($('#observaciones').val() == ''){
		alert('El campo observaciones es obligatorio.');
		$('#observaciones').focus();
		bandera++;
	} else if($('#foto').val() == ''){
		alert('Debe seleccionar un archivo.');
		$('#foto').focus();
		bandera++;
	}

	if(bandera == 0){
		var formData = new FormData($("#formArchivos")[0]);
		formData.append('semanas',semanas);
		console.log(formData);
		var ruta = "functions/fn_guardar_priorizacion.php";
		var auxContenido = '';
		$.ajax({
			url: ruta,
			type: "POST",
			data: formData,
			contentType: false,
			processData: false,
			beforeSend: function(){
				$('#loader').fadeIn();
			},
			success: function(datos){
				try{
					var obj = JSON.parse(datos);
					console.log(obj);
					if(obj.respuesta == 1){
						//$("#myModal").modal();
						alert('Se ha registrado con éxito la novedad de priorización');
						//location.reload();
						location.href="index.php";
					}else{
						alert(obj.reporte);
					}
				}
				catch(err) {
					$('.debug').html(err.message);
					$('.debug').append('<br/><br/>');
					$('.debug').append(datos);
				}
			}
		})
		.done(function(){ })
		.fail(function(){ })
		.always(function(){
			$('#loader').fadeOut();
		});
	}
}
