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
		$.ajax({
			type : "POST",
			url : "functions/fn_buscar_complementos.php"
		}).done(function(data){
			var obj = JSON.parse(data);
			totalizar(obj.complementos, obj.cantGruposEtarios);
			$('.priorizacionAction').fadeIn();		
		})
		.fail(function(){ })
		.always(function(){
			$('#loader').fadeOut();
		});
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
	$('#semana .semana:checked').each(function () { 
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
				for (let i = 0; i < obj.complementos.length; i++) {
    				if(obj.complementos[i] == 0) {
						$('.'+obj.complementos[i]+'actual').hide('fast');
					} else {
						aux = obj.complementos[i];
						$('#'+obj.complementos[i]+'actualTotal').val(obj[aux]);
						for (let x = 1; x <= obj.cantGruposEtarios; x++) {
							aux2 = obj.complementos[i]+x;
							$('#'+obj.complementos[i]+'actual'+x).val(obj[aux2]); 
							$('#'+obj.complementos[i]+x).val(obj[aux2]);
						}
						$('#'+obj.complementos[i]+'Total').val(obj[aux]);
						$('.'+obj.complementos[i]+'actual').show('fast');
					}
				}

				totalizar(obj.complementos, obj.cantGruposEtarios);
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

function totalizar(complementos, cantGruposEtarios){

	// Se va a calcular las cantidades totales para cada complemento.
	arraycomplementos = complementos;
	// console.log(arraycomplementos)
	for (var i = 0; i < complementos.length; i++) {
		var aux = 0;
		for (var x =  1; x <= cantGruposEtarios; x++) {
			aux += parseInt($('#'+complementos[i]+x).val());
		}
		$('#'+complementos[i]+'Total').val(aux);
	}

	aux = 0;
	for (var i =  0; i < complementos.length; i++) {
		aux += parseInt($('#'+complementos[i]+'Total').val());
	}
	$('#totalTotal').val(aux);

	for (var x =  1; x <= cantGruposEtarios; x++) {
		aux = 0;
		for (var i = 0; i < complementos.length; i++) {
			aux += parseInt($('#'+complementos[i]+x).val());
		}
		$('#total'+x).val(aux);	
	}
}

function guardar_priorizacion(){
	var bandera = 0;
	var semanas = new Array();

  	$('#semana .semana:checked').each(function() {
		var aux = $(this).val();
		semanas.push(aux);
	});

	if($('#observaciones').val() == '') {
		bandera++;
		Command: toastr.error(
			'El campo observaciones es obligatorio.',
			'Validación de formulario',
			{
				onHidden: function() {
					$('#observaciones').focus();
					
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
