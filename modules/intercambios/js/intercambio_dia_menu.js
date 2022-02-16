var indiceProductosAjuste = 0;

$(document).ready(function(){
	$('#btnBuscar').click(function(){
		$(".boxPreparacion").html('');
		buscarMenusSemana();
	});	
});

// Equivalente a document ready despues de traer los producto de la preparación
function inicializarFunciones(){
	$('.datepick').each(function(){
		$(this).removeClass("hasDatepicker");
		$(this).datepicker({
			format: 'dd/mm/yyyy',
			todayHighlight: 'true',
			autoclose: 'true'
		});
	});

	cargarOpcionesDeMenu();
	$('.menuDia').change(function(){
		$('.menuDia').find('option').not(':selected').remove();
		cargarOpcionesDeMenu();
	});

	$('.btnGuardar').click(function(){
		guardarIntercambio();
	});	
}

function  buscarMenusSemana(){
	if($('#formParametros').valid()){
		var formData = new FormData();
		formData.append('mes', $('#mes').val());
		formData.append('semana', $('#semana').val());
		formData.append('tipoComplemento', $('#tipoComplemento').val());
		formData.append('grupoEtario', $('#grupoEtario').val());
		formData.append('variacion', $('#variacion').val());
		$.ajax({
			type: "post",
			url: "functions/fn_buscar_menus_semana.php",
			contentType: false,
			processData: false,
			data: formData,
			beforeSend: function(){ $('#loader').fadeIn(); },
			success: function(data){
				$(".boxPreparacion").html(data);
				$('#loader').fadeOut();
				inicializarFunciones();
			},
			error: function(data){
				console.log(data);
				Command:toastr.error("Al parecer existe un problema con el servidor.","Error en el Servidor",{onHidden:function(){$('#loader').fadeOut();}});
			}
		});
	}
}

function cargarOpcionesDeMenu(){
	var valor = "";
	var menusSeleccionados = [];
	$( ".menuDia" ).each(function() {
		if($(this).val()){
			menusSeleccionados.push($(this).val());
		}
	});

	var formData = new FormData();
	formData.append('mes', $('#mes').val());
	formData.append('semana', $('#semana').val());
	formData.append('tipoComplemento', $('#tipoComplemento').val());
	formData.append('grupoEtario', $('#grupoEtario').val());
	formData.append('menusSeleccionados', menusSeleccionados);
	formData.append('variacion', $('#variacion').val());
	$.ajax({
		type: "post",
		url: "functions/fn_buscar_opciones_de_menu.php",
		dataType: "json",
		contentType: false,
		processData: false,
		data: formData,
		beforeSend: function(){ $('#loader').fadeIn(); },
		success: function(data){
			if(data.estado == 1){
				$('.menuDia').append(data.opciones);
				$('.menuDia').select2({width : "100%"});
				$('#loader').fadeOut();
			}
			else{
				Command:toastr.error(data.mensaje,"Error",{onHidden:function(){$('#loader').fadeOut();}});
			}
		},
		error: function(data){
			console.log(data);
			Command:toastr.error("Al parecer existe un problema con el servidor.","Error en el Servidor",{onHidden:function(){$('#loader').fadeOut();}});
		}
	});
}


function guardarIntercambio(){
	if($('#formParametros').valid()){
		var formData = new FormData();
		formData.append('mes', $('#mes').val());
		formData.append('semana', $('#semana').val());
		formData.append('tipoComplemento', $('#tipoComplemento').val());
		formData.append('grupoEtario', $('#grupoEtario').val());
		formData.append('variacion', $('#variacion').val());
		$( ".menuDia" ).each(function() {
			var dia = $(this).attr("dia");
			var menu = $(this).attr("menu");
			var codigoMenu = $(this).val();
			formData.append('menu['+codigoMenu+'][ordenCiclo]', menu);
			formData.append('menu['+codigoMenu+'][codigo]', codigoMenu);
		});

		formData.append('fechaVencimiento', $('#fechaVencimiento').val());
		formData.append('foto', $('#foto')[0].files[0]);
		formData.append('observaciones', $('#observaciones').val());
		$.ajax({
			type: "post",
			url: "functions/fn_guardar_intercambio_dia_menu.php",
			dataType: "json",
			contentType: false,
			processData: false,
			data: formData,
			beforeSend: function(){ $('#loader').fadeIn(); },
			success: function(data){
				if(data.estado == 1){
					Command : toastr.success( data.message, "Registro Exitoso", { onHidden : function(){ 
						$('#loader').fadeOut();
						location.reload();
					}});
				}
				else{
					Command:toastr.error(data.mensaje,"Error",{onHidden:function(){$('#loader').fadeOut();}});
				}
			},
			error: function(data){
				console.log(data);
				Command:toastr.error("Al parecer existe un problema con el servidor.","Error en el Servidor",{onHidden:function(){$('#loader').fadeOut();}});
			}
		});

	}
}
