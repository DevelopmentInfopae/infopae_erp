$( document ).ready(function() {
    $(document).on('change', '#mes', function () { cargarSemanas($(this).val()); });
    $(document).on('change', '#semana_inicial', function () { cargarSemanas($('#mes').val(), $("#semana_inicial option:selected").data("diainicial"));});
    $(document).on('change', '#semana_final', function () { actualizarDiasCampo(); });

	$('#municipio').change(function() {
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
		var sede = $('#sede').val();
		var mes = $('#mes').val();
		buscar_sede(municipio, institucion);
		buscar_complemento(institucion, sede, mes);
	});

	$('#sede').change(function(){
		var institucion = $('#institucion').val();
		var sede = $('#sede').val();
		var mes = $('#mes').val();
		buscar_complemento(institucion, sede, mes);
	});

	$('#btnBuscar').click(function(){
		//Validaciones
		var bandera = 0;
		if( $('#municipio').val() == '' ){
			bandera++;
			alert('Debe seleccionar un municipio');
			$('#municipio').focus();
		} else if($('#mes').val() == '') {
			bandera++;
			alert('Debe seleccionar un mes');
			$('#mes').focus();
		} else if ($('#semana_inicial').val() == '') {
			bandera++;
			alert('Debe seleccionar la semana Inicial');
			$('#semana_inicial').focus();
		} else if ($('#semana_final').val() == '') {
			bandera++;
			alert('Debe seleccionar la semana final');
			$('#semana_final').focus();
		} else if( $('#institucion').val() == '' ) {
			bandera++;
			alert('Debe seleccionar una institucion');
			$('#institucion').focus();
		} else if( $('#sede').val() == '' ) {
			bandera++;
			alert('Debe seleccionar una sede');
			$('#sede').focus();
		} else if($('#tipo').val() == '') {
			bandera++;
			alert('Debe seleccionar un tipo de complemento');
			$('#tipo').focus();
		} else {
			var tipoPlanilla = $('input[name="tipoPlanilla"]:checked').val();
			if (tipoPlanilla == null){
				bandera++;
				alert('Debe seleccionar un tipo de planilla');
				$('input[name="tipoPlanilla"]').focus();
			}
		}



		if(bandera == 0){
			$('#formPlanillas').submit();
		}
	});
});

// Método AJAX para cargar combo con las semanas del mes seleccionado.
function cargarSemanas($mes, $diainicialSemanaAnterior = '') {
	if ($diainicialSemanaAnterior == '') {
		$('#semana_final').html('<option value="">Selecciones uno</option>');
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
				$('#semana_final').html('<option value="">Selecciones uno</option>');
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

function buscar_complemento(institucion, sede, mes) {
    $.ajax({
      type: "POST",
      url: "functions/fn_buscar_complemento.php",
      data: {"sede":sede,"institucion":institucion, "mes":mes},
      beforeSend: function() { $('#loader').fadeIn(); },
      success: function(data) { $('#tipo').html(data); }
    })
    .always(function() { $('#loader').fadeOut(); });

    //se añade el valor del mes escogido, para buscar en tabla entregas_res correspondiente al mes
}