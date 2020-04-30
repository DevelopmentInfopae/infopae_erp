$(document).ready(function(){
	// Configuración del plugin toastr
	toastr.options = {
	    "closeButton": true,
	    "debug": false,
	    "progressBar": true,
	    "preventDuplicates": false,
	    "positionClass": "toast-top-right",
	    "onclick": null,
	    "showDuration": "400",
	    "hideDuration": "1000",
	    "timeOut": "2000",
	    "extendedTimeOut": "1000",
	    "showEasing": "swing",
	    "hideEasing": "linear",
	    "showMethod": "fadeIn",
	    "hideMethod": "fadeOut"
	}

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

	$('.tablaNuevasCantidades input').change(function() {
		if ($(this).val() == "") { $(this).val('0'); }

		totalizar();
	});

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
	var semanas = new Array();

	// Recogemos todas las semanas chequeadas
	$('#semana .semana:checked').each(function () { console.log($(this).val());
		semanas.push($(this).val());
	});

	$('.priorizacionAction').hide('fast');

	if($('#municipio').val() === undefined || $('#municipio').val() == '') {
		Command: toastr.error(
            'Debe seleccionar un municipio.',
            "Validación de formulario",
            {
              onHidden : function(){
				$('#municipio').focus();
				bandera++;
              }
            }
        );
	} else if($('#institucion').val() === undefined || $('#institucion').val() == '') {
		Command: toastr.error(
            'Debe seleccionar una institución.',
            "Validación de formulario",
            {
              	onHidden : function(){
					$('#institucion').focus();
					bandera++;
              	}
            }
        );
	} else if($('#sede').val() === undefined || $('#sede').val() == '') {
		Command: toastr.error(
            'Debe seleccionar una sede.',
            "Validación de formulario",
            {
              	onHidden : function(){
					$('#sede').focus();
					bandera++;
              	}
            }
        );
	} else if($('#mes').val() === undefined || $('#mes').val() == '') {
		Command: toastr.error(
			'Debe seleccionar un mes.',
			'Validación de formulario',
			{
				onHidden: function() {
					$('#mes').focus();
					bandera++;
				}
			}
		);
	} else if(semanas.length <= 0 && bandera == 0) {
		Command: toastr.error(
			'Debe seleccionar por lo menos una semana.',
			'Validación de formulario',
			{
				onHidden: function() {
					$('#semana0').focus();
				}
			}
		);
	} else if(bandera == 0) {
		var sede = $('#sede').val();
		var datos = {"semanas":semanas, "sede":sede};
		$.ajax({
			type: "POST",
			url: "functions/fn_validar_semanas_cantidades.php",
			data: datos,
			beforeSend: function() { $('#loader').fadeIn(); }
		})
		.done(function(data){
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
		})
		.fail(function(data){ console.log(data.responseText); })
		.always(function(){ $('#loader').fadeOut(); });
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
		beforeSend: function() { $('#loader').fadeIn(); }
	})
	.done(function(data){
		try {
			var obj = JSON.parse(data);

			if(obj.registros > 0){
				var aps = obj.aps;
				var aps1 = obj.aps1;
				var aps2 = obj.aps2;
				var aps3 = obj.aps3;
				var cajmri = obj.cajmri;
				var cajmri1 = obj.cajmri1;
				var cajmri2 = obj.cajmri2;
				var cajmri3 = obj.cajmri3;
				var cajtri = obj.cajtri;
				var cajtri1 = obj.cajtri1;
				var cajtri2 = obj.cajtri2;
				var cajtri3 = obj.cajtri3;
				var cajmps = obj.cajmps;
				var cajmps1 = obj.cajmps1;
				var cajmps2 = obj.cajmps2;
				var cajmps3 = obj.cajmps3;
				var cajtps = obj.cajtps;
				var cajtps1 = obj.cajtps1;
				var cajtps2 = obj.cajtps2;
				var cajtps3 = obj.cajtps3;
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

				if(cajmri == 0){
					$('.CAJMRIactual').hide('fast');
				}else{
					$('#CAJMRIactualTotal').val(cajmri);
					$('#CAJMRIactual1').val(cajmri1); $('#CAJMRI1').val(cajmri1);
					$('#CAJMRIactual2').val(cajmri2); $('#CAJMRI2').val(cajmri2);
					$('#CAJMRIactual3').val(cajmri3); $('#CAJMRI3').val(cajmri3);

					$('.CAJMRIactual').show('fast');
				}

				if(cajtri == 0){
					$('.CAJTRIactual').hide('fast');
				}else{
					$('#CAJTRIactualTotal').val(cajtri);
					$('#CAJTRIactual1').val(cajtri1); $('#CAJTRI1').val(cajtri1);
					$('#CAJTRIactual2').val(cajtri2); $('#CAJTRI2').val(cajtri2);
					$('#CAJTRIactual3').val(cajtri3); $('#CAJTRI3').val(cajtri3);

					$('.CAJTRIactual').show('fast');
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

				if(cajtps == 0){
					$('.CAJTPSactual').hide('fast');
				}else{
					$('#CAJTPSactualTotal').val(cajtps);
					$('#CAJTPSactual1').val(cajtps1); $('#CAJTPS1').val(cajtps1);
					$('#CAJTPSactual2').val(cajtps2); $('#CAJTPS2').val(cajtps2)
					$('#CAJTPSactual3').val(cajtps3); $('#CAJTPS3').val(cajtps3)

					$('.CAJTPSactual').show('fast');
				}

				totalizar();
				$('.priorizacionAction').fadeIn();
			}
	    } catch(err) {
			$('.debug').html(err.message);
			$('.debug').append('<br/><br/>');
			$('.debug').append(data);
		}
	})
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

	aux = parseInt($('#CAJMRI1').val()) + parseInt($('#CAJMRI2').val()) + parseInt($('#CAJMRI3').val());
	$('#CAJMRITotal').val(aux);

	aux = parseInt($('#CAJTRI1').val()) + parseInt($('#CAJTRI2').val()) + parseInt($('#CAJTRI3').val());
	$('#CAJTRITotal').val(aux);

	aux = parseInt($('#CAJMPS1').val()) + parseInt($('#CAJMPS2').val()) + parseInt($('#CAJMPS3').val());
	$('#CAJMPSTotal').val(aux);

	aux = parseInt($('#CAJTPS1').val()) + parseInt($('#CAJTPS2').val()) + parseInt($('#CAJTPS3').val());
	$('#CAJTPSTotal').val(aux);

	aux = parseInt($('#APSTotal').val()) + parseInt($('#CAJMRITotal').val()) + parseInt($('#CAJTRITotal').val()) + parseInt($('#CAJMPSTotal').val()) + parseInt($('#CAJTPSTotal').val());
	$('#totalTotal').val(aux);

	aux = parseInt($('#APS1').val()) + parseInt($('#CAJMRI1').val()) + parseInt($('#CAJTRI1').val()) + parseInt($('#CAJMPS1').val()) + parseInt($('#CAJTPS1').val());
	$('#total1').val(aux);

	aux = parseInt($('#APS2').val()) + parseInt($('#CAJMRI2').val()) + parseInt($('#CAJTRI2').val()) + parseInt($('#CAJMPS2').val()) + parseInt($('#CAJTPS2').val());
	$('#total2').val(aux);

	aux = parseInt($('#APS3').val()) + parseInt($('#CAJMRI3').val()) + parseInt($('#CAJTRI3').val()) + parseInt($('#CAJMPS3').val()) + parseInt($('#CAJTPS3').val());
	$('#total3').val(aux);
}

function guardar_priorizacion(){
	var bandera = 0;
	var semanas = new Array();

  	$('#semana .semana:checked').each(function() {
		var aux = $(this).val();
		semanas.push(aux);
	});

	if($('#observaciones').val() == '') {
		Command: toastr.error(
			'El campo observaciones es obligatorio.',
			'Validación de formulario',
			{
				onHidden: function() {
					$('#observaciones').focus();
					bandera++;
				}
			}
		);
	} else if($('#foto').val() == '') {
		Command: toastr.error(
			'Debe seleccionar un archivo.',
			'Validación de formulario',
			{
				onHidden: function() {
					$('#foto').focus();
					bandera++;
				}
			}
		);
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
