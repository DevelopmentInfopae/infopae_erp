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

  $('#btnBuscar').click(function(){ validar_semanas_cantidades(); });
	$('.tablaNuevasCantidades input').change(function(){
		console.log($(this).val());
		if ($(this).val() == "") {
			$(this).val('0');
		}

		totalizar(); });
	$('.guaradarNovedad').click(function(){ guardar_priorizacion(); });
});

function crearNovedadPriorizacion(){
  window.open('novedades_priorizacion_crear.php', '_self');
}

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
    $.ajax({
			type: "POST",
			url: "functions/fn_buscar_institucion.php",
			data: {"municipio":municipio},
			dataType: "HTML",
			beforeSend: function(){
				$('#loader').fadeIn();
			},
    	success: function(data){
				$('#institucion').html(data);
    	}
	  })
	  .fail(function(data){
	  	console.log(data);
	  })
	  .always(function(){
	  	$('#loader').fadeOut();
	  });
}

function buscar_sede(institucion){
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
    $.ajax({
			type: "POST",
			url: "functions/fn_buscar_semanas.php",
			data: {"mes":mes, "sede":sede},
			beforeSend: function(){
				$('#loader').fadeIn();
			},
    	success: function(data){
			try {
		  		var obj = JSON.parse(data);
				$('#semana').html(obj.respuesta);
				$('input').iCheck({ radioClass: "iradio_square-green" });
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
	var bandera = 0;

	$('.priorizacionAction').hide('fast');

	if($('#municipio').val() === undefined || $('#municipio').val() == ''){
		alert('Debe seleccionar un municipio.');
		$('#municipio').focus();
		bandera++;
	} else if($('#institucion').val() === undefined || $('#institucion').val() == ''){
		alert('Debe seleccionar una institución.');
		$('#institucion').focus();
		bandera++;
	} else if($('#sede').val() === undefined || $('#sede').val() == ''){
		alert('Debe seleccionar una sede.');
		$('#sede').focus();
		bandera++;
	} else if($('#mes').val() === undefined || $('#mes').val() == ''){
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
						buscar_priorizacion(semanas);
					}
					else{
						alert(obj.log);
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
}

function buscar_priorizacion(semanas){
	// Bucaremos si hay registros de priorización para la sede seleccionada en el mes seleccionado.
	var municipio = $('#municipio').val();
	var institucion = $('#institucion').val();
	var sede = $('#sede').val();
	var mes = $('#mes').val();

	$.ajax({
		type: "POST",
		url: "functions/fn_buscar_priorizacion.php",
		data: {
			"mes":mes,
			"sede":sede,
			"semanas":semanas,
			"municipio":municipio,
			"institucion":institucion
		},
		beforeSend: function(){
			$('#loader').fadeIn();
		},
		success: function(data){
			try {
				var obj = JSON.parse(data);

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
					if(aps == 0) {
						$('.APSactual').hide('fast');
					} else {
						$('#APSactualTotal').val(aps);
						$('#APSactual1').val(aps1); $('#APS1').val(aps1);
						$('#APSactual2').val(aps2); $('#APS2').val(aps2);
						$('#APSactual3').val(aps3); $('#APS3').val(aps3);
						$('#APSTotal').val(aps);

						$('.APSactual').show('fast');
					}
					if(cajmps == 0){
						$('.CAJMPSactual').hide('fast');
					}else{
						$('#CAJMPSactualTotal').val(cajmps);
						$('#CAJMPSactual1').val(cajmps1); $('#CAJMPS1').val(cajmps1);
						$('#CAJMPSactual2').val(cajmps2); $('#CAJMPS2').val(cajmps2)
						$('#CAJMPSactual3').val(cajmps3); $('#CAJMPS3').val(cajmps3)

						$('.CAJMPSactual').show('fast');
					}
					if(cajmri == 0){
						$('.CAJMRIactual').hide('fast');
					}else{
						$('#CAJMRIactualTotal').val(cajmri);
						$('#CAJMRIactual1').val(cajmri1); $('#CAJMRI1').val(cajmri1);
						$('#CAJMRIactual2').val(cajmri2); $('#CAJMRI2').val(cajmri2);
						$('#CAJMRIactual3').val(cajmri3); $('#CAJMRI3').val(cajmri3);

						$('.CAJMRIactual').show('fast');
					}

					totalizar();
					$('.priorizacionAction').fadeIn();
				}
		  } catch(err) {
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
	var bandera = 0;
	var semanas = new Array();

  $('#semana .semana:checked').each(function() {
		var aux = $(this).val();
		semanas.push(aux);
	});

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

		$.ajax({
			url: "functions/fn_guardar_priorizacion.php",
			type: "POST",
			data: formData,
			contentType: false,
			processData: false,
			beforeSend: function(){
				$('#loader').fadeIn();
			},
			success: function(datos){
				console.log(datos);
				try{
					var obj = JSON.parse(datos);
					if(obj.respuesta == 1){
						alert('Se ha registrado con éxito la novedad de priorización');
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
		.always(function(){
			$('#loader').fadeOut();
		});
	}
}
