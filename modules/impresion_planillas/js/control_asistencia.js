$( document ).ready(function() {
    console.log( "ready!" );
	$('#municipio').change(function(){
		console.log('Cambio de municipio');
		var tipo = $('#tipoRacion').val();
		var municipio = $(this).val();
		$('#municipioNm').val($("#municipio option:selected").text());
		buscar_institucion(municipio,tipo);
		//$('#formDespachoPorSede').submit();
	});

	$('#institucion').change(function(){
		var institucion = $(this).val();
		var municipio = $('#municipio').val();
		var sede = $('#sede').val();
		buscar_sede(municipio, institucion);
		buscar_complemento(institucion, sede);
	});

	$('#sede').change(function(){
		var institucion = $('#institucion').val();
		var sede = $('#sede').val();
		buscar_complemento(institucion, sede);
	});

	$('#btnBuscar').click(function(){
		console.log('Enviar Formulario');
		//Validaciones
		var bandera = 0;
		if( $('#mes').val() == '' ){
			bandera++;
			alert('Debe seleccionar un mes');
			$('#mes').focus();
		}
		else if( $('#municipio').val() == '' ){
			bandera++;
			alert('Debe seleccionar un municipio');
			$('#municipio').focus();
		}
		else if( $('#institucion').val() == '' ){
			bandera++;
			alert('Debe seleccionar una institucion');
			$('#institucion').focus();
		}
		// else if( $('#sede').val() == '' ){
		// 	bandera++;
		// 	alert('Debe seleccionar una sede');
		// 	$('#sede').focus();
		// }
		else if( $('#tipo').val() == '' ){
			bandera++;
			alert('Debe seleccionar un tipo de complemento');
			$('#tipo').focus();
		}

		else{
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

function buscar_complemento(institucion, sede) {
    $.ajax({
      type: "POST",
      url: "functions/fn_buscar_complemento.php",
      data: {"sede":sede,"institucion":institucion},
      beforeSend: function() { $('#loader').fadeIn(); },
      success: function(data) { $('#tipo').html(data); }
    })
    .always(function() { $('#loader').fadeOut(); });
}