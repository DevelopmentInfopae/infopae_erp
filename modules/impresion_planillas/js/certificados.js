$( document ).ready(function() {
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
		if($('#formPlanillas').valid()){
			$('#formPlanillas').submit();
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
