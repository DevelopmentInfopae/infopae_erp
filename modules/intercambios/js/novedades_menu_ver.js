function crearNovedadPriorizacion(){
  window.open('novedades_priorizacion_crear.php', '_self');
}
$(document).ready(function(){
	//$('.priorizacionAction').fadeIn();
	//buscar_municipios();


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

