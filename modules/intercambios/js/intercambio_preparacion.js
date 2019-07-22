var indiceProductosAjuste = 0;

$(document).ready(function(){
	$('#btnBuscar').click(function(){
		buscarPreparacionesIntercambio();
	});	
});

function  buscarPreparacionesIntercambio(){
	if($('#formParametros').valid()){

		console.log("Buscar preparaciónes del menú");

		var formData = new FormData();

		formData.append('menu', $('#codigoMenu').val());

		$.ajax({
			type: "post",
			url: "functions/fn_buscar_preparaciones_intercambio.php",
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

// Equivalente a document ready despues de traer los producto de la preparación
function inicializarFunciones(){
	cargarOpcionesDeProducto();
	indiceProductosAjuste = $('.tablaAjuste tbody tr').length;
	console.log("indiceProductosAjuste = "+indiceProductosAjuste);
	$('.quitarProducto').click(function(){
		quitarProducto(this);
	});	

	$('.btnGuardar').click(function(){
		guardarIntercambio();
	});

}

function cargarOpcionesDeProducto(){
	var valor = "";
	console.log("Cargar opciones de prodcuto");

	var formData = new FormData();
	formData.append('grupoEtario', $('#grupoEtario').val());

	$.ajax({
		type: "post",
		url: "functions/fn_buscar_opciones_preparaciones.php",
		dataType: "json",
		contentType: false,
		processData: false,
		data: formData,
		beforeSend: function(){ $('#loader').fadeIn(); },
		success: function(data){
			if(data.estado == 1){
				$('.productoFichaTecnicaDet select').append(data.opciones);
				$('.productoFichaTecnicaDet select').select2({width : "100%"});
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

function obtenerUnidadMedidaProducto(elemento, indice){
 	console.log("No se necesita buscar unidad de medida en este caso. "+indice);
}

function anadirProducto(){
	indiceProductosAjuste++;
	var nuevoPorducto = ""; 
		nuevoPorducto += "<tr class\"productoAjuste"+indiceProductosAjuste+" productoFichaTecnicaDetA\" indice=\""+indiceProductosAjuste+"\">";
		nuevoPorducto += "<td><select class=\"form-control producto\" name=\"productoFichaTecnicaDet["+indiceProductosAjuste+"]\" id=\"productoFichaTecnicaDet"+indiceProductosAjuste+"\" onchange=\"obtenerUnidadMedidaProducto(this, "+indiceProductosAjuste+");\" required=\"\"> </select></td>"; 
		//nuevoPorducto += "<td><input type=\"text\" class=\"form-control text-center\" name=\"unidadMedidaProducto["+indiceProductosAjuste+"]\" id=\"unidadMedidaProducto"+indiceProductosAjuste+"\" value=\"\" readonly=\"\"></td>";
		//nuevoPorducto += "<td><input type=\"number\" min=\"0\" class=\"form-control text-center\" name=\"cantidadProducto["+indiceProductosAjuste+"]\" id=\"cantidadProducto5\" value=\"\" onchange=\"cambiarPesos(this, "+indiceProductosAjuste+");\" step=\".0001\"></td>";
		//nuevoPorducto += "<td><input type=\"number\" min=\"0\" class=\"form-control text-center\" name=\"pesoBrutoProducto["+indiceProductosAjuste+"]\" id=\"pesoBrutoProducto"+indiceProductosAjuste+"\" value=\"\" step=\".0001\"></td>";
		//nuevoPorducto += "<td><input type=\"number\" min=\"0\" class=\"form-control text-center\" name=\"pesoNetoProducto["+indiceProductosAjuste+"]\" id=\"pesoNetoProducto"+indiceProductosAjuste+"\" value=\"\" step=\".0001\"></td>";
		nuevoPorducto += "<td><span class=\"btn btn-danger btn-sm btn-outline quitarProducto\" data-numftd=\""+indiceProductosAjuste+"\" title=\"Eliminar de la composición\"><span class=\"fa fa-trash\"></span></span></td>";
		nuevoPorducto += "</tr>"; 
	$('.tablaAjuste tbody').append(nuevoPorducto);
	cargarOpcionesDeNuevoProducto(indiceProductosAjuste);

	$('.quitarProducto').click(function(){
		quitarProducto(this);
	});

}

function cargarOpcionesDeNuevoProducto(indiceProductosAjuste){
	var indice = indiceProductosAjuste;
	var valor = "";
	console.log("Cargar opciones de producto nuevo");

	var formData = new FormData();
	formData.append('grupoEtario', $('#grupoEtario').val());

	$.ajax({
		type: "post",
		url: "functions/fn_buscar_opciones_preparaciones.php",
		dataType: "json",
		contentType: false,
		processData: false,
		data: formData,
		beforeSend: function(){ $('#loader').fadeIn(); },
		success: function(data){
			if(data.estado == 1){
				//console.log(data);
				console.log(indice);
				$('#productoFichaTecnicaDet'+indice).append(data.opciones);
				$('#productoFichaTecnicaDet'+indice).select2({width : "100%"});
				// $('.productoFichaTecnicaDet select').append(data.opciones);
				// 
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

function quitarProducto(elemento){
	console.log("Quitar elemento.");
	console.log(elemento);
	$(elemento).closest("tr").remove();
}

function guardarIntercambio(){
	console.log("Guadar intercambio de preparación");
	if($('#formParametros').valid()){
		console.log("Formulario validado.");

		var formData = new FormData();
		
		formData.append('mes', $('#mes').val());
		formData.append('semana', $('#semana').val());
		formData.append('dia', $('#dia').val());
		formData.append('tipoComplemento', $('#tipoComplemento').val());
		formData.append('grupoEtario', $('#grupoEtario').val());
		formData.append('codigoMenu', $('#codigoMenu').val());
		formData.append('menu', $('#menu').val());

		$( ".tablaAjuste tbody tr" ).each(function() {
			var producto = $(this).find('.producto').val();
			var productoNombre = $(this).find('.producto option:selected').text();

			console.log(producto);
			console.log(productoNombre);

			formData.append('productos['+producto+'][producto]', producto);
			formData.append('productos['+producto+'][productoNombre]', productoNombre);

		});

		$.ajax({
			type: "post",
			url: "functions/fn_guardar_intercambio_preparacion.php",
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