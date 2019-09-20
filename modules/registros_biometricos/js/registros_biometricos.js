$(document).ready(function(){
		jQuery.extend(jQuery.validator.messages, { required: "Este campo es obligatorio.", remote: "Por favor, rellena este campo.", email: "Por favor, escribe una dirección de correo válida", url: "Por favor, escribe una URL válida.", date: "Por favor, escribe una fecha válida.", dateISO: "Por favor, escribe una fecha (ISO) válida.", number: "Por favor, escribe un número entero válido.", digits: "Por favor, escribe sólo dígitos.", creditcard: "Por favor, escribe un número de tarjeta válido.", equalTo: "Por favor, escribe el mismo valor de nuevo.", accept: "Por favor, escribe un valor con una extensión aceptada.", maxlength: jQuery.validator.format("Por favor, no escribas más de {0} caracteres."), minlength: jQuery.validator.format("Por favor, no escribas menos de {0} caracteres."), rangelength: jQuery.validator.format("Por favor, escribe un valor entre {0} y {1} caracteres."), range: jQuery.validator.format("Por favor, escribe un valor entre {0} y {1}."), max: jQuery.validator.format("Por favor, escribe un valor menor o igual a {0}."), min: jQuery.validator.format("Por favor, escribe un valor mayor o igual a {0}.") });

	$('#municipio').change(function(){
		console.log('Cambio de municipio');
		var municipio = $(this).val();
		buscar_institucion(municipio);
	});

	$('#institucion').change(function(){
		console.log('Cambio de institucion');
		var institucion = $(this).val();
		buscar_sede(institucion);
	});





	$('#btnBuscar').click(function() {
		if($('#formSedes').valid()){
			if($('#municipio').val() == ''){
				alert('Debe seleccionar al menos un municipio.');
				$('#municipio').focus();
			}else{
				$('#formSedes').submit();
			}
		}
	});














// buscar_sede(


	$('.dataTables-sedes tr').click(function(){
		var aux = $(this).attr('codsede');
		$('#verSede #codSede').val(aux);
		aux = $(this).attr('nomsede');
		$('#verSede #nomSede').val(aux);
		aux = $(this).attr('nominst');
		$('#verSede #nomInst').val(aux);
		$('#verSede').submit();
	});
});


function buscar_institucion(municipio){
	console.log('Actualizando lista de instituciones.');
	console.log(municipio);
	var datos = {"municipio":municipio};
	$.ajax({
	  type: "POST",
	  url: "functions/fn_buscar_instituciones.php",
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

function buscar_sede(institucion){
	console.log('función para buscar sedes.');
  var datos = {"institucion":institucion};
    $.ajax({
      type: "POST",
      url: "functions/fn_buscar_sedes.php",
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
