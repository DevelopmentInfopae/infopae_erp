$( document ).ready(function() {

	$('html,body').animate({scrollTop: 0}, 1000);

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
});




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
			beforeSend: function(){},
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
			// $('.cargador').fadeOut();
			// $('#foto').val(function() { return this.defaultValue; });
		});
	}
}







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
				// $('.cargador').fadeOut();
				// $('#foto').val(function() { return this.defaultValue; });
			});
			// Termina el ajax
		}

	});
});


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
		beforeSend: function(){},
		success:function(response){
			console.log(response);
			$('#foto_'+nombre).remove();
		}
	})
	.done(function(){})
	.fail(function(){})
	.always(function(){});
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
			beforeSend: function(){},
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
		.always(function(){});
		// Termina el ajax
	}
}







/*

function borrar_foto(){
	 	//Borrado del archivo anterior
        	var fotoCargada = $('#fotoCargada').val();

        	if(fotoCargada != ""){
        		console.log('Borrando inagen previa.');


	        	var datos = {
					"fotoCargada":fotoCargada
				}

	        	var ruta = 'fn_borrar_foto_cargada.php';
				$.ajax({

			    	url: ruta,
			        type:'post',
			        dataType:'html',
			        data:datos,
			        beforeSend: function(){
			          $('.pensando').css('display','inline');
			        },

			        success:function(response){

			        	//alert(response);
			          	$('#debug').html(response);

			        	if (response == 1) {
			        		alert('Se ha enviado el formulario con exito. Gracias!');
			           		//location.reload();
			           	};
			        }


			    })
				.done(function(data){console.log("success"); })
				.fail(function(){console.log("error"); })
				.always(function(){console.log("complete"); });

        	}

        	//Termina el borrado del archivo anterior
}


function borrarFoto(foto,ruta){
	$('.fotografia'+foto+' .fotoCargada').remove();
	$('.fotografia'+foto+' .borrarFoto').remove();
	$('#fotoCargada'+foto).val('');
	var bandera = 0;
	if(ruta == '' || ruta == 'original'){
		bandera++;
	}
	if(bandera == 0){
	console.log('Se va a borrar fisicamente la foto.');

	var datos = {
		"ruta":ruta
	}

	var ruta = 'fn_borrar_foto.php';


	$.ajax({

		url: ruta,
		type:'post',
		dataType:'html',
		data:datos,
		beforeSend: function(){},

		success:function(response){





			$('#fotocargada'+foto).val('');
			$('.fotografia'+foto).html('<input type="hidden" name="fotoCargada'+foto+'" id="fotoCargada'+foto+'" value="">');


		}


	})
	.done(function(data){console.log("success"); })
	.fail(function(){console.log("error"); })
	.always(function(){console.log("complete"); });

}



}










function borrarFotoAdmin(foto,ruta){
	$('.fotografia'+foto+' .fotoCargada').remove();
	$('.fotografia'+foto+' .borrarFoto').remove();
	$('#fotoCargada'+foto).val('');
	var bandera = 0;
	if(ruta == '' || ruta == 'original'){
		bandera++;
	}
	if(bandera == 0){
	console.log('Se va a borrar fisicamente la foto.');

	var datos = {
		"ruta":ruta
	}

	var ruta = 'fn_borrar_foto_admin.php';


	$.ajax({

		url: ruta,
		type:'post',
		dataType:'html',
		data:datos,
		beforeSend: function(){},

		success:function(response){

			console.log(response);





			$('#fotocargada'+foto).val('');
			$('.fotografia'+foto).html('<input type="hidden" name="fotoCargada'+foto+'" id="fotoCargada'+foto+'" value="">');


		}


	})
	.done(function(data){console.log("success"); })
	.fail(function(){console.log("error"); })
	.always(function(){console.log("complete"); });

}



}

*/




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
