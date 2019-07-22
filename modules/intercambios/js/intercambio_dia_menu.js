var indiceProductosAjuste = 0;

$(document).ready(function(){
	$('#btnBuscar').click(function(){
		$(".boxPreparacion").html('');
		buscarMenusSemana();
	});	
});

// Equivalente a document ready despues de traer los producto de la preparación
function inicializarFunciones(){
	cargarOpcionesDeMenu();
	$('.menuDia').change(function(){
		$('.menuDia').find('option').not(':selected').remove();
		cargarOpcionesDeMenu();
	});

	$('.btnGuardar').click(function(){
		guardarIntercambio();
	});



	// indiceProductosAjuste = $('.tablaAjuste tbody tr').length;
	// console.log("indiceProductosAjuste = "+indiceProductosAjuste);
	
	// $('.quitarProducto').click(function(){
	// 	quitarProducto(this);
	// });		
}

function  buscarMenusSemana(){
	if($('#formParametros').valid()){

		console.log("Buscar preparación");

		var formData = new FormData();

		formData.append('mes', $('#mes').val());
		formData.append('semana', $('#semana').val());
		formData.append('tipoComplemento', $('#tipoComplemento').val());
		formData.append('grupoEtario', $('#grupoEtario').val());

		$.ajax({
			type: "post",
			url: "functions/fn_buscar_menus_semana.php",
			//dataType: "json",
			contentType: false,
			processData: false,
			data: formData,
			beforeSend: function(){ $('#loader').fadeIn(); },
			success: function(data){
				//console.log(data);
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
	console.log("Cargar opciones de prodcuto");
	var menusSeleccionados = [];
	$( ".menuDia" ).each(function() {
		//console.log($(this).val());
		if($(this).val()){
			menusSeleccionados.push($(this).val());
		}
	});
	//console.log(menusSeleccionados);	

	var formData = new FormData();
	formData.append('mes', $('#mes').val());
	formData.append('semana', $('#semana').val());
	formData.append('tipoComplemento', $('#tipoComplemento').val());
	formData.append('grupoEtario', $('#grupoEtario').val());
	formData.append('menusSeleccionados', menusSeleccionados);

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
	console.log("Guadar intercambio de menús días");
	if($('#formParametros').valid()){
		console.log("Formulario validado.");

		var formData = new FormData();
		
		formData.append('mes', $('#mes').val());
		formData.append('semana', $('#semana').val());
		formData.append('tipoComplemento', $('#tipoComplemento').val());
		formData.append('grupoEtario', $('#grupoEtario').val());

		$( ".menuDia" ).each(function() {
			var dia = $(this).attr("dia");
			var menu = $(this).attr("menu");
			var codigoMenu = $(this).val();

			console.log("día "+dia);
			console.log("Menú "+menu);
			console.log("Codigo Menú "+codigoMenu);


			formData.append('menu['+codigoMenu+'][ordenCiclo]', menu);
			formData.append('menu['+codigoMenu+'][codigo]', codigoMenu);

		});

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



