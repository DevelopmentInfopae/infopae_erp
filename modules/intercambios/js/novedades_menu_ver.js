function crearNovedadPriorizacion(){
  window.open('novedades_priorizacion_crear.php', '_self');
}
$(document).ready(function(){
	//$('.priorizacionAction').fadeIn();
	//buscar_municipios();
	
	$('#btnReversarIntercambio').click(function(){
		reversar();
	});

	$('#ventanaReversar .btnSiReversar').click(function(){
		$("#loader").fadeIn(); 
		reversar(1);
	});













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

function reversar(flag){
	if(flag != 1){
		console.log('Despliegue de modal para confirmar la reversión del intercambio');
		$('#ventanaReversar .modal-body p').html('¿Esta seguro de <strong>reversar el intercambio,</strong>? ya no se permitirá reactivar.');
  		$('#ventanaReversar').modal();
	}else{
		console.log('Inicia reversar intercambio');
		var formData = new FormData();
		formData.append('idIntercambio', $('#idIntercambio').val());
		formData.append('tipoIntercambio', $('#tipoIntercambio').val());
		formData.append('codigoIntercambio', $('#codigoIntercambio').val());

		$.ajax({
			type: "post",
			url: "functions/fn_reversar_intercambio.php",
			dataType: "json",
			contentType: false,
			processData: false,
			data: formData,
			beforeSend: function(){ $("#loader").fadeIn(); },
			success: function(data){
				if(data.state == 1){
					Command : toastr.success( data.message, "Actualización del registro exitosa", { onHidden : function(){ 
					$('#loader').fadeOut(); 
					location.reload();
				/* location.href="URL para redireccionar"; */ }});
				}else{
					Command:toastr.error(data.message,"Error al actualizar el registro.",{onHidden:function(){ $('#loader').fadeOut(); }});
				}
			},
			error: function(data){
				console.log(data);
				Command:toastr.error("Al parecer existe un problema con el servidor.","Error en el Servidor",{onHidden:function(){ $('#loader').fadeOut(); }});
			}
		});
	}
}