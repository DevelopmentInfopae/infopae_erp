$(document).ready(function(){
	var municipio = $('#municipio').val();
	buscarInstituciones(municipio);

	$(document).on('change', '#municipio', function(){
		$("#instituciones").select2('val', '');
		var municipio = $('#municipio').val();
		buscarInstituciones(municipio);
	});

	$(document).on('change', '#instituciones', function(){
		$("#sedes").select2('val', '');
		var institucion = $('#instituciones').val();
		buscarSedes(institucion);
	});

	$('.i-checks').iCheck({
		checkboxClass: 'icheckbox_square-green',
		radioClass: 'iradio_square-green',
	});

	$('.select2').select2();
	
	$('#imprimirMes').prop('checked',true);
});

function buscarInstituciones(municipio){
	$.ajax({
		url: 'functions/fn_buscar_instituciones.php',
		type: 'POST',
		data: {municipio: municipio},
		beforeSend: function(){
			$('#loader').fadeIn();
		},
	})
	.done(function(data) {
		$('#instituciones').html(data);
		$('#loader').fadeOut();
	})
	.fail(function() {
		console.log("error");
	})
	.always(function() {
		console.log("complete");
	});	
}

function buscarSedes(institucion){
	$.ajax({
		url: 'functions/fn_buscar_sedes.php',
		type: 'POST',
		data: {institucion: institucion},
		beforeSend: function(){
			$('#loader').fadeIn();
		}
	})
	.done(function(data) {
		$('#loader').fadeOut();
		$('#sedes').html(data)
	})
	.fail(function() {
		console.log("error");
	})
	.always(function() {
		console.log("complete");
	});
	
}