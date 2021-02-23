$( document ).ready(function() {
	buscar_municipios();
	buscar_municipios_lateral();
	

	$('html,body').animate({scrollTop: 0}, 1000);

	$('.nueva-categoria').click(function(event){
		//alert($('#nuevaCategoria').val());
		nuevaCategoria($('#nuevaCategoria').val());
	});

	$('.categoria-editar').click(function(event){
		var id = $(this).val();
		var text = $('#categoria-editar-'+id).val();
		//alert(text);
		editarCategoria(id,text);
	});

	$('.categoria-eliminar').click(function(event){
		var id = $(this).val();
		var text = $('#categoria-editar-'+id).val();
		//alert(text);
		eliminarCategoria(id);
	});

	
	







	$('.file-control').click(function(event){
		event.preventDefault();
		var aux = $(this).attr('value');
		console.log(aux);
		$('#tipo').val(aux);
		$('#mostrarArchivos').submit();
	});

	$('.category-control').click(function(event){
		event.preventDefault();
		var aux = $(this).attr('value');
		console.log(aux);
		$('#categoriaFnd').val(aux);
		$('#mostrarArchivos').submit();
	});

	$('.btnBorrar').click(function(event){
		event.preventDefault();
		var aux = $(this).attr('value');
		console.log(aux);
		borrarArchivo(aux);
	});

	$('#municipio').change(function(){
		var municipio = $(this).val();




		
		
		
		
		
		$('#institucion').html("<option value=\"\">Todas</option>");
		$('#institucion').select2('val', '');
		$('#sede').html("<option value=\"\">Todas</option>");
		$('#sede').select2('val', '');
		buscar_instituciones(municipio);
	});

	$('#institucion').change(function(){
		var institucion = $(this).val();
		$('#sede').html("<option value=\"\">Todas</option>");
		$('#sede').select2('val', '');
		buscar_sedes(institucion);
	});

	$('#municipioLateral').change(function(){
		var municipio = $(this).val();
		if(municipio != $('#municipioFnd').val()){
			$('#municipioFnd').val(municipio);
			$('#mostrarArchivos').submit();
		}
	});

	$('#institucionLateral').change(function(){
		var institucion = $(this).val();
		if(institucion != $('#institucionFnd').val()){
			$('#institucionFnd').val(institucion);
			$('#mostrarArchivos').submit();
		}
	});

	$('#sedeLateral').change(function(){
		var sede = $(this).val();
		if(sede != $('#sedeFnd').val()){
			$('#sedeFnd').val(sede);
			$('#mostrarArchivos').submit();
		}
	});

	


});

function buscar_municipios(){
	console.log('Actualizando lista de municipios.');
	//var datos = {"municipio":municipio,"tipo":tipo};
	var datos = {};
	$.ajax({
		type: "POST",
		url: "functions/fn_archivos_buscar_municipios.php",
		data: datos,
		beforeSend: function(){
			$('#loader').fadeIn();
		},
		success: function(data){
			//$('#debug').html(data);
			$('#municipio').html(data);
		}
	})
	.done(function(){ })
	.fail(function(){ })
	.always(function(){
		$('#loader').fadeOut();
	});
}

function buscar_municipios_lateral(){
	console.log('Actualizando lista de municipios.');
	//var datos = {"municipio":municipio,"tipo":tipo};
	var datos = {};
	$.ajax({
		type: "POST",
		url: "functions/fn_archivos_buscar_municipios.php",
		data: datos,
		beforeSend: function(){
			//$('#loader').fadeIn();
		},
		success: function(data){
			//$('#debug').html(data);
			$('#municipioLateral').html(data);
			//$('#municipioLateral').val($('#municipioFnd').val());
			$('#municipioLateral').select2('val', $('#municipioFnd').val());
			buscar_instituciones_lateral($('#municipioFnd').val());
		}
	})
	.done(function(){ })
	.fail(function(){ })
	.always(function(){
		//$('#loader').fadeOut();
	});
}

function buscar_instituciones_lateral(municipio){
	console.log('Actualizando lista de instituciones.');
	console.log(municipio);
	var datos = {"municipio":municipio};
	$.ajax({
		type: "POST",
		url: "functions/fn_archivos_buscar_instituciones.php",
		data: datos,
		beforeSend: function(){
			$('#loader').fadeIn();
		},
		success: function(data){
			//$('#debug').html(data);
			$('#institucionLateral').html(data);
			//$('#institucionLateral').val($('#institucionFnd').val());
			$('#institucionLateral').select2('val', $('#institucionFnd').val());
			buscar_sedes_lateral($('#institucionFnd').val());
		}
	})
	.done(function(){ })
	.fail(function(){ })
	.always(function(){
		$('#loader').fadeOut();
	});
}

function buscar_sedes_lateral(institucion){
	console.log('Actualizando lista de sedes.');
	console.log(institucion);
	var datos = {"institucion":institucion};
	$.ajax({
		type: "POST",
		url: "functions/fn_archivos_buscar_sedes.php",
		data: datos,
		beforeSend: function(){
			$('#loader').fadeIn();
		},
		success: function(data){
			//$('#debug').html(data);
			$('#sedeLateral').html(data);
			//$('#sedeLateral').val($('#sedeFnd').val());
			$('#sedeLateral').select2('val', $('#sedeFnd').val());
			
		}
	})
	.done(function(){ })
	.fail(function(){ })
	.always(function(){
		$('#loader').fadeOut();
	});
}





function buscar_instituciones(municipio){
	console.log('Actualizando lista de instituciones.');
	console.log(municipio);
	var datos = {"municipio":municipio};
	$.ajax({
		type: "POST",
		url: "functions/fn_archivos_buscar_instituciones.php",
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

function buscar_sedes(institucion){
	console.log('Actualizando lista de sedes.');
	console.log(institucion);
	var datos = {"institucion":institucion};
	$.ajax({
		type: "POST",
		url: "functions/fn_archivos_buscar_sedes.php",
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

function borrarArchivo(id){
	var idBorrar = id;
	var r = confirm("¿Está seguro de que desea eliminar este archivo?");
	if (r == true) {
	    txt = "You pressed OK!";
		var ruta = "functions/fn_archivo_eliminar.php";
		var datos = {
			"id":idBorrar
		};
		$.ajax({
			url: ruta,
			type:'post',
			dataType:'html',
			data:datos,
			beforeSend: function(){
				$('#loader').fadeIn();
			},
			success: function(datos){
				console.log('Terminado Ajax');
				//auxContenido = $('.fotografias').html();
				//$('.fotografias').html(auxContenido);
				//$('.fotografias').append(datos);
				//$('.fotosReporte').appendTo('#reporteCargas');
				if(datos == 1){
					alert('El archivo se ha eliminado con exito.');
					location.reload();
				}
				else{
					alert('No se ha conseguido eliminar el archivo.');
					console.log(datos);
					$('.debug').html(datos);
				}
			}
		})
		.done(function(){ })
		.fail(function(){ })
		.always(function(){
			$('#loader').fadeOut();
			// $('.cargador').fadeOut();
			// $('#foto').val(function() { return this.defaultValue; });
		});
	}
}

// RUTINA PARA SUBIR EL ARCHIVO
var fotos = "0";
$(function(){
	$('#btnSubirArchivo').click(function(event){
		var bandera = 0;
		if($('#nombre').val() == ''){
			alert('El nombre del archivo es un campo obligatorio.');
			$('#nombre').focus();
			bandera++;
		} else if($('#categoria').val() == ''){
			alert('Debe seleccionar una categoria.');
			$('#categoria').focus();
			bandera++;
		} else if($('#foto').val() == ''){
			alert('Debe seleccionar un archivo.');
			$('#foto').focus();
			bandera++;
		}


		if(bandera == 0){
			console.log('Se ha escogido un archivo.');
			var formData = new FormData($("#formArchivos")[0]);
			console.log(formData);
			var ruta = "functions/fn_archivo_guardar.php";
			var auxContenido = '';
			$.ajax({
				url: ruta,
				type: "POST",
				data: formData,
				contentType: false,
				processData: false,
				beforeSend: function(){
					$('#loader').fadeIn();
					$("input[name='foto']").val('');
				},
				success: function(datos){
					console.log('Terminado Ajax');
					//auxContenido = $('.fotografias').html();
					//$('.fotografias').html(auxContenido);
					//$('.fotografias').append(datos);
					//$('.fotosReporte').appendTo('#reporteCargas');
					if(datos == 1){
						alert('El archivo se ha cargado con exito.');
						location.reload();
					}
					if(datos == 2){
						alert('Solo se permiten fotografías en formato JPG.');
					}
					else if(datos == 3){
						alert('El archivo pesa más de 1MB.');
					}
					else if(datos == 4){
						alert('La Fotografía debe ser horizontal.');
					}else{
						$('.debugCarga').html(datos);
					}
				}
			})
			.done(function(){ })
			.fail(function(){ })
			.always(function(){
				$('#loader').fadeOut();
				// $('.cargador').fadeOut();
				// $('#foto').val(function() { return this.defaultValue; });
			});
			// Termina el ajax
		}

	});
});

// TERMINA LA RUTINA PARA SUBIR EL ARCHIVO



function borrarFoto(nombre){
	console.log('Se va a borrar la foto '+nombre);
	var datos = {
		"nombre":nombre
	};


  var ruta = "fn_borrar_foto.php";
	var auxContenido = '';

	$.ajax({
		url: ruta,
		type:'post',
		dataType:'html',
		data:datos,
		beforeSend: function(){
			$('#loader').fadeIn();
		},
		success:function(response){
			console.log(response);
			$('#foto_'+nombre).remove();
		}
	})
	.done(function(){})
	.fail(function(){})
	.always(function(){
		$('#loader').fadeOut();
	});
	// Termina el ajax
}

function borrarFotoGuardada(nombre, url){
	var r = confirm("Confirma que desea eliminar esta foto, una vez eliminada no se podrá recuperar.");
	if (r == true) {
		console.log('se va a borrar la foto '+nombre+' que se encuentra en9: '+url);
		var datos = {
			"nombre":nombre,
			"url":url
		};


		var ruta = "fn_borrar_foto_guardada.php";
		var auxContenido = '';

		$.ajax({
			url: ruta,
			type:'post',
			dataType:'html',
			data:datos,
			beforeSend: function(){
				$('#loader').fadeIn();
			},
			success:function(response){
				//$('#debug').html(response);
				console.log(response);
				if(response == 1){
					//alert('Se ha eliminado la foto con éxito.');
					//alert(nombre);
					$('#'+nombre).remove();
				}
			}
		})
		.done(function(){})
		.fail(function(){})
		.always(function(){
			$('#loader').fadeOut();
		});
		// Termina el ajax
	}
}


//  Codigo para suvizar anclas
$(function(){
  //$('a[href*=#]').click(function() {
  $('a[href*=\\#]').click(function() {
    if (location.pathname.replace(/^\//,'') == this.pathname.replace(/^\//,'') && location.hostname == this.hostname) {
      var $target = $(this.hash);
      try {
        $target = $target.length && $target || $('[name=' + this.hash.slice(1) +']');
      }
      catch(err) {
        console.log(err.message);
      }

      if ($target.length) {
        var targetOffset = $target.offset().top;
        $('html,body').animate({scrollTop: targetOffset-500}, 1000);
        return false;
      }
    }
  });
});
// Final Codigo para suavizar anclas

function nuevaCategoria(categoria){
	var ruta = "functions/fn_nueva_categoria.php";
	var datos = {
		"categoria":categoria
	};
	$.ajax({
		url: ruta,
		type:'post',
		dataType:'html',
		data:datos,
		beforeSend: function(){
			$('#loader').fadeIn();
		},
		success: function(datos){
			console.log('Terminado Ajax');
			//auxContenido = $('.fotografias').html();
			//$('.fotografias').html(auxContenido);
			//$('.fotografias').append(datos);
			//$('.fotosReporte').appendTo('#reporteCargas');
			if(datos == 1){
				alert('El registro se ha realizado con éxito.');
				location.reload();
			}
			else{
				// alert('No se ha conseguido eliminar el archivo.');
				// console.log(datos);
				// $('.debug').html(datos);
				//location.reload();
			}
		}
	})
	.done(function(){ })
	.fail(function(){ })
	.always(function(){
		$('#loader').fadeOut();
		// $('.cargador').fadeOut();
		// $('#foto').val(function() { return this.defaultValue; });
	});
}

function editarCategoria(id,text){
	var ruta = "functions/fn_editar_categoria.php";
	var datos = {
		"id":id,
		"categoria":text
	};
	$.ajax({
		url: ruta,
		type:'post',
		dataType:'html',
		data:datos,
		beforeSend: function(){
			$('#loader').fadeIn();
		},
		success: function(datos){
			console.log('Terminado Ajax');
			//auxContenido = $('.fotografias').html();
			//$('.fotografias').html(auxContenido);
			//$('.fotografias').append(datos);
			//$('.fotosReporte').appendTo('#reporteCargas');
			if(datos == 1){
				alert('El registro se ha actualizado con éxito.');
				location.reload();
			}
			else{
				// alert('No se ha conseguido eliminar el archivo.');
				// console.log(datos);
				// $('.debug').html(datos);
				//location.reload();
			}
		}
	})
	.done(function(){ })
	.fail(function(){ })
	.always(function(){
		$('#loader').fadeOut();
		// $('.cargador').fadeOut();
		// $('#foto').val(function() { return this.defaultValue; });
	});
}

function eliminarCategoria(id){
	var r = confirm("¿Está seguro de que desea eliminar esta categoría?");
	if (r == true) {
		var ruta = "functions/fn_eliminar_categoria.php";
		var datos = {
			"id":id
		};
		$.ajax({
			url: ruta,
			type:'post',
			dataType:'html',
			data:datos,
			beforeSend: function(){
				$('#loader').fadeIn();
			},
			success: function(datos){
				console.log('Terminado Ajax');
				//auxContenido = $('.fotografias').html();
				//$('.fotografias').html(auxContenido);
				//$('.fotografias').append(datos);
				//$('.fotosReporte').appendTo('#reporteCargas');
				if(datos == 1){
					alert('El registro se ha eliminado con éxito.');
					location.reload();
				}
				else{
					// alert('No se ha conseguido eliminar el archivo.');
					// console.log(datos);
					// $('.debug').html(datos);
					//location.reload();
				}
			}
		})
		.done(function(){ })
		.fail(function(){ })
		.always(function(){
			$('#loader').fadeOut();
			// $('.cargador').fadeOut();
			// $('#foto').val(function() { return this.defaultValue; });
		});
	}
}