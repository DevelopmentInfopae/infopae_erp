$( document ).ready(function() {
	toastr.options = {
	    "closeButton": true,
	    "debug": false,
	    "progressBar": true,
	    "preventDuplicates": false,
	    "positionClass": "toast-top-right",
	    "onclick": null,
	    "showDuration": "400",
	    "hideDuration": "1000",
	    "timeOut": "2000",
	    "extendedTimeOut": "1000",
	    "showEasing": "swing",
	   	"hideEasing": "linear",
	    "showMethod": "fadeIn",
	    "hideMethod": "fadeOut"
    }
	buscar_municipios();
	buscar_municipios_lateral();
	$('html,body').animate({scrollTop: 0}, 1000);

	$('.nueva-categoria').click(function(event){
		nuevaCategoria($('#nuevaCategoria').val());
	});

	$('.categoria-editar').click(function(event){
		var id = $(this).val();
		var text = $('#categoria-editar-'+id).val();
		editarCategoria(id,text);
	});

	$('.categoria-eliminar').click(function(event){
		var id = $(this).val();
		eliminarCategoria(id);
	});

	$('.file-control').click(function(event){
		event.preventDefault();
		var aux = $(this).attr('value');
		$('#tipo').val(aux);
		$('#mostrarArchivos').submit();
	});

	$('.category-control').click(function(event){
		event.preventDefault();
		var aux = $(this).attr('value');
		$('#categoriaFnd').val(aux);
		$('#mostrarArchivos').submit();
	});

	$('.btnBorrar').click(function(event){
		// console.log($(this));
		event.preventDefault();
		var aux = $(this).attr('value');
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
			if(datos == 1){
				if(datos == 1){
					Command: toastr.success('Creado con éxito.','El registro se ha creado con éxito.',{onHidden : function(){ location.href='index.php';}});
				}
			}
		}
	})
	.done(function(){ })
	.fail(function(){ })
	.always(function(){
		$('#loader').fadeOut();
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
			if(datos == 1){
				if(datos == 1){
					Command: toastr.success('Actualizado con éxito.','El registro se ha actualizado con éxito.',{onHidden : function(){ location.href='index.php';}});
				}
			}
		}
	})
	.done(function(){ })
	.fail(function(){ })
	.always(function(){
		$('#loader').fadeOut();
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
				if(datos == 1){
					Command: toastr.success('Eliminado con éxito.','El registro se ha eliminado con éxito.',{onHidden : function(){ location.href='index.php';}});
				}
			}
		})
		.done(function(){ })
		.fail(function(){ })
		.always(function(){
			$('#loader').fadeOut();
		});
	}
}

function buscar_municipios(){
	var datos = {};
	$.ajax({
		type: "POST",
		url: "functions/fn_archivos_buscar_municipios.php",
		data: datos,
		beforeSend: function(){
		},
		success: function(data){
			$('#municipio').html(data);
		}
	})
	.done(function(){ })
	.fail(function(){ })
	.always(function(){
	});
}
function buscar_municipios_lateral(){
	var datos = {};
	$.ajax({
		type: "POST",
		url: "functions/fn_archivos_buscar_municipios.php",
		data: datos,
		beforeSend: function(){
		},
		success: function(data){
			$('#municipioLateral').html(data);
			$('#municipioLateral').select2('val', $('#municipioFnd').val());
			buscar_instituciones_lateral($('#municipioFnd').val());
		}
	})
	.done(function(){ })
	.fail(function(){ })
	.always(function(){
	});
}

// INSTITUCIONES
function buscar_instituciones(municipio){
	var datos = {"municipio":municipio};
	$.ajax({
		type: "POST",
		url: "functions/fn_archivos_buscar_instituciones.php",
		data: datos,
		beforeSend: function(){
			$('#loader').fadeIn();
		},
		success: function(data){
			$('#institucion').html(data);
		}
	})
	.done(function(){ })
	.fail(function(){ })
	.always(function(){
		$('#loader').fadeOut();
	});
}

function buscar_instituciones_lateral(municipio){
	var datos = {"municipio":municipio};
	$.ajax({
		type: "POST",
		url: "functions/fn_archivos_buscar_instituciones.php",
		data: datos,
		beforeSend: function(){
			$('#loader').fadeOut();
		},
		success: function(data){
			$('#institucionLateral').html(data);
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

// SEDES 
function buscar_sedes(institucion){
	var datos = {"institucion":institucion};
	$.ajax({
		type: "POST",
		url: "functions/fn_archivos_buscar_sedes.php",
		data: datos,
		beforeSend: function(){
			$('#loader').fadeIn();
		},
		success: function(data){
			$('#sede').html(data);
		}
	})
	.done(function(){ })
	.fail(function(){ })
	.always(function(){
		$('#loader').fadeOut();
	});
}

function buscar_sedes_lateral(institucion){
	var datos = {"institucion":institucion};
	$.ajax({
		type: "POST",
		url: "functions/fn_archivos_buscar_sedes.php",
		data: datos,
		beforeSend: function(){
			$('#loader').fadeOut();
		},
		success: function(data){
			$('#sedeLateral').html(data);
			$('#sedeLateral').select2('val', $('#sedeFnd').val());
		}
	})
	.done(function(){ })
	.fail(function(){ })
	.always(function(){
		$('#loader').fadeOut();
	});
}

// RUTINA PARA SUBIR EL ARCHIVO
var fotos = "0";
$(function(){
	$('#btnSubirArchivo').click(function(event){
		var bandera = 0;
		if($('#nombre').val() == ''){
			Command: toastr.warning('Campo Obligatorio','El nombre del archivo es un campo obligatorio.',{onHidden : function(){}});
			$('#nombre').focus();
			bandera++;
		} else if($('#categoria').val() == ''){
			Command: toastr.warning('Campo Obligatorio','Debe seleccionar una categoria.',{onHidden : function(){ }});
			$('#categoria').select2('open').select2('close');
			bandera++;
		} else if($('#foto').val() == ''){
			Command: toastr.warning('Campo Obligatorio','Debe seleccionar un archivo.',{onHidden : function(){ }});
			$('#foto').focus();
			bandera++;
		}
		if(bandera == 0){
			var formData = new FormData($("#formArchivos")[0]);
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
					if(datos == 1){
						Command: toastr.success('Exito','El archivo se ha cargado con exito.',{onHidden : function(){ location.href='index.php'; }});
					}
					if(datos == 2){
						Command: toastr.warning('Alerta','Solo se permiten fotografías en formato JPG.',{onHidden : function(){ }});
					}
					else if(datos == 3){
						Command: toastr.warning('Alerta','El archivo pesa más de 1MB.',{onHidden : function(){ }});
					}
					else if(datos == 4){
						Command: toastr.warning('Alerta','La Fotografía debe ser horizontal.',{onHidden : function(){ }});
					}else{
						$('.debugCarga').html(datos);
					}
				}
			})
			.done(function(){ })
			.fail(function(){ })
			.always(function(){
				$('#loader').fadeOut();
			});
		}
	});
});
// TERMINA LA RUTINA PARA SUBIR EL ARCHIVO

function borrarArchivo(id){
	var idBorrar = id;
	var r = confirm("¿Está seguro de que desea eliminar este archivo?");
	if (r == true) {
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
				if(datos == 1){
					Command: toastr.success('Exito','El archivo se ha eliminado con exito.',{onHidden : function(){ location.href='index.php'; }});
				}
				else{
					Command: toastr.success('Error','No se ha conseguido eliminar el archivo.',{onHidden : function(){ location.href='index.php'; }});
					$('.debug').html(datos);
				}
			}
		})
		.done(function(){ })
		.fail(function(){ })
		.always(function(){
			$('#loader').fadeOut();
		});
	}
}

//  Codigo para suvizar anclas
$(function(){
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
