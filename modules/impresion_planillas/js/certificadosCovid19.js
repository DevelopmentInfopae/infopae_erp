$( document ).ready(function() {
	$(document).on('change', '#mes', function () {
		var mes = $(this).val();
		if (mes < 10) {
			mesCompleto = '0' + mes;
		} else {
			mesCompleto = mes;
		}
		cargarSemanas(mesCompleto);
	});
  $(document).on('change', '#semana_inicial', function () {
  	var mes = $('#mes').val();
		if (mes < 10) {
			mesCompleto = '0' + mes;
		} else {
			mesCompleto = mes;
		}
  	cargarSemanas(mesCompleto, $("#semana_inicial option:selected").data("diainicial"));
  });
  $(document).on('change', '#semana_final', function () { actualizarDiasCampo(); });


	jQuery.extend(jQuery.validator.messages, { required: "Este campo es obligatorio.", remote: "Por favor, rellena este campo.", email: "Por favor, escribe una dirección de correo válida", url: "Por favor, escribe una URL válida.", date: "Por favor, escribe una fecha válida.", dateISO: "Por favor, escribe una fecha (ISO) válida.", number: "Por favor, escribe un número entero válido.", digits: "Por favor, escribe sólo dígitos.", creditcard: "Por favor, escribe un número de tarjeta válido.", equalTo: "Por favor, escribe el mismo valor de nuevo.", accept: "Por favor, escribe un valor con una extensión aceptada.", maxlength: jQuery.validator.format("Por favor, no escribas más de {0} caracteres."), minlength: jQuery.validator.format("Por favor, no escribas menos de {0} caracteres."), rangelength: jQuery.validator.format("Por favor, escribe un valor entre {0} y {1} caracteres."), range: jQuery.validator.format("Por favor, escribe un valor entre {0} y {1}."), max: jQuery.validator.format("Por favor, escribe un valor menor o igual a {0}."), min: jQuery.validator.format("Por favor, escribe un valor mayor o igual a {0}.") });





    console.log( "ready!" );
	$('#municipio').change(function(){
		console.log('Cambio de municipio');
		var tipo = $('#tipoRacion').val();
		var municipio = $(this).val();
		$('#municipioNm').val($("#municipio option:selected").text());
		buscar_institucion(municipio,tipo);
	});

	$('#municipio').trigger('change');

	$('#institucion').change(function(){
		var institucion = $(this).val();
		var municipio = $('#municipio').val();
		buscar_sede(municipio, institucion);
	});

	$('#btnBuscar').click(function(){
		var version = $('input:radio[name=tipoPlanilla]:checked').val();
		if (version == 1) {
			if($('#formPlanillas').valid()){
				$('#formPlanillas').submit();
			}
		}
		if (version == 2) {
			if ($('#formPlanillas').valid()) {
				municipio = $('#formPlanillas #municipio').val();
				institucion = $('#formPlanillas #institucion').val();
				mes = $('#formPlanillas #mes').val();
				semanaInicial = $('#formPlanillas #semana_inicial').val();
				semanaFinal = $('#formPlanillas #semana_final').val();
				imprimirMes = $('#formPlanillas #imprimirMes').val();
				fechaDesde = $('#formPlanillas #fecha_desde').val();
				fechaHasta = $('#formPlanillas #fecha_hasta').val();
				observaciones = $('#formPlanillas #observaciones').val();
			}	
				// console.log(municipio,institucion,mes, semanaInicial, semanaFinal, imprimirMes, fechaDesde, fechaHasta, observaciones);
				$('#formPlanillas2 #municipio').val(municipio);
				$('#formPlanillas2 #institucion').val(institucion);
				$('#formPlanillas2 #mes').val(mes);
				$('#formPlanillas2 #semana_inicial').val(semanaInicial);
				$('#formPlanillas2 #semana_final').val(semanaFinal);
				$('#formPlanillas2 #imprimirMes').val(imprimirMes);
				$('#formPlanillas2 #fecha_desde').val(fechaDesde);
				$('#formPlanillas2 #fecha_hasta').val(fechaHasta);
				$('#formPlanillas2 #observaciones').val(observaciones);
				$('#formPlanillas2').submit();
		}






		//
		// console.log('Enviar Formulario');
		// //Validaciones
		// var bandera = 0;
		// if( $('#mes').val() == '' ){
		// 	bandera++;
		// 	alert('Debe seleccionar un mes');
		// 	$('#mes').focus();
		// }
		// else if( $('#municipio').val() == '' ){
		// 	bandera++;
		// 	alert('Debe seleccionar un municipio');
		// 	$('#municipio').focus();
		// }
		// // else if( $('#institucion').val() == '' ){
		// // 	bandera++;
		// // 	alert('Debe seleccionar una institucion');
		// // 	$('#institucion').focus();
		// // }
		// // else if( $('#sede').val() == '' ){
		// // 	bandera++;
		// // 	alert('Debe seleccionar una sede');
		// // 	$('#sede').focus();
		// // }
		// else if( $('#tipo').val() == '' ){
		// 	bandera++;
		// 	alert('Debe seleccionar un tipo de complemento');
		// 	$('#tipo').focus();
		// }
		//
		// else{
		// 	var tipoPlanilla = $('input[name="tipoPlanilla"]:checked').val();
		// 	if (tipoPlanilla == null){
		// 		bandera++;
		// 		alert('Debe seleccionar un tipo de planilla');
		// 		$('input[name="tipoPlanilla"]').focus();
		// 	}
		// }
		//
		//
		//
		// if(bandera == 0){
		// 	$('#formPlanillas').submit();
		// }






	});
});


// Método AJAX para cargar combo con las semanas del mes seleccionado.
function cargarSemanas($mes, $diainicialSemanaAnterior = '') {
	if ($diainicialSemanaAnterior == '') {
		$('#semana_final').html('<option value="">Seleccione uno</option>');
	}

	$.ajax({
		url: 'functions/fn_buscar_semanas.php',
		type: 'POST',
		dataType: 'HTML',
		data: {
			'mes': $mes,
			'diainicialSemanaAnterior' : $diainicialSemanaAnterior
		},
	})
	.done(function(data) {
		if (data != '') {
			if ($diainicialSemanaAnterior != '') {
				$('#semana_final').html(data);

				var diainicialSemanaInicial = $('#semana_inicial option:selected').data("diainicial");
				var diafinalSemanaInicial = $('#semana_inicial option:selected').data("diafinal");
				$('#diaInicialSemanaInicial').val(diainicialSemanaInicial);
				$('#diaFinalSemanaInicial').val(diafinalSemanaInicial);
				$('#diaInicialSemanaFinal').val('');
				$('#diaFinalSemanaFinal').val('');
			} else {
				$('#semana_inicial').html(data);
				$('#semana_final').html('<option value="">Seleccione uno</option>');
				$('#diaInicialSemanaInicial').val('');
				$('#diaFinalSemanaInicial').val('');
				$('#diaInicialSemanaFinal').val('');
				$('#diaFinalSemanaFinal').val('');
			}
		}
	})
	.fail(function(data) {
		console.log(data);
	});
}

function actualizarDiasCampo() {
	var diainicialSemanaFinal = $('#semana_final option:selected').data("diainicial");
	var diafinalSemanaFinal = $('#semana_final option:selected').data("diafinal");
	$('#diaInicialSemanaFinal').val(diainicialSemanaFinal);
	$('#diaFinalSemanaFinal').val(diafinalSemanaFinal);
}


function buscar_institucion(municipio,tipo){
  console.log('Actualizando lista de instituciones.');
  console.log(municipio);
  console.log(tipo);
  var datos = {"municipio":municipio,"tipo":tipo};
    $.ajax({
      type: "POST",
      url: "functions/fn_buscar_institucion.php",
      data: datos,
      beforeSend: function(){
        $('#loader').fadeIn();
      },
      success: function(data){
        //$('#debug').html(data);
        $('#institucion').html(data);
      }
    })
    .done(function(){ })
    .fail(function(){ })
    .always(function(){
      $('#loader').fadeOut();
    });
}

function buscar_sede(municipio, institucion){
  var datos = {"municipio":municipio,"institucion":institucion};
    $.ajax({
      type: "POST",
      url: "functions/fn_buscar_sede.php",
      data: datos,
      beforeSend: function(){
        $('#loader').fadeIn();
      },
      success: function(data){
        //$('#debug').html(data);
        $('#sede').html(data);
      }
    })
    .done(function(){ })
    .fail(function(){ })
    .always(function(){
      $('#loader').fadeOut();
    });
}
