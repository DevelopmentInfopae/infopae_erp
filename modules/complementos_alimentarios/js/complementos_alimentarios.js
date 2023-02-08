$(document).ready(function () {
	jQuery.extend(jQuery.validator.messages, { 	required: "Campo obligatorio.", 
		remote: "Por favor, rellena este campo.", 
		step: "Por favor, escribe un número entero", 
		email: "Por favor, escribe una dirección de correo válida", 
		url: "Por favor, escribe una URL válida.", 
		date: "Por favor, escribe una fecha válida.", 
		dateISO: "Por favor, escribe una fecha (ISO) válida.", 
		number: "Por favor, escribe un número entero válido.", 
		digits: "Por favor, escribe sólo dígitos.", 
		creditcard: "Por favor, escribe un número de tarjeta válido.", 
		equalTo: "Por favor, escribe el mismo valor de nuevo.", 
		accept: "Por favor, escribe un valor con una extensión aceptada.", 
		maxlength: jQuery.validator.format("Por favor, no escribas más de {0} caracteres."), 
		minlength: jQuery.validator.format("Por favor, no escribas menos de {0} caracteres."), 
		rangelength: jQuery.validator.format("Por favor, escribe un valor entre {0} y {1} caracteres."), 
		range: jQuery.validator.format("Por favor, escribe un valor entre {0} y {1}."), 
		max: jQuery.validator.format("Por favor, escribe un valor menor o igual a {0}."), 
		min: jQuery.validator.format("Por favor, escribe un valor mayor o igual a {0}.") 
	});

	$(document).on('click', '#crearComplementosAlimentarios', function () { crearComplementosAlimentarios(); });
	$(document).on('click', '#guardarComplementoAlimentario', function () { guardarComplementoAlimentario(false); });
	$(document).on('click', '#guardarComplementoAlimentarioContinuar', function () { guardarComplementoAlimentario(true); });
	$(document).on('click', '#actualizarComplementoAlimentario', function () { actualizarComplementoAlimentario(false); });
	$(document).on('click', '#actualizarComplementoAlimentarioContinuar', function () { actualizarComplementoAlimentario(true); });
	$(document).on('click', '.editarComplementoAlimentario', function () { editarComplementoAlimentario($(this).data('idtipocomplemento') ); });

	// Configuración inicial del plugin toastr.
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

	$('input').iCheck({
        radioClass: 'iradio_square-green',
        checkboxClass: 'icheckbox_square-green'
    });
});

function crearComplementosAlimentarios(){
	window.open('complementos_alimentarios_crear.php', '_self');
}

function editarComplementoAlimentario(idTipoComplemento, codigoTipoComplemento){
  	$('#formEditarComplementosAlimentarios #idTipoComplemento').val(idTipoComplemento);
	$('#formEditarComplementosAlimentarios #codigoTipoComplemento').val(codigoTipoComplemento);
	$('#formEditarComplementosAlimentarios').submit();
}

function guardarComplementoAlimentario(continuar){
	if($('#formCrearComplementoAlimentario').valid()){
    	$.ajax({
			type: "post",
			url: "functions/fn_complementos_alimentarios_crear.php",
			data: $('#formCrearComplementoAlimentario').serialize(),
			dataType: 'json',
			beforeSend: function(){ $('#loader').fadeIn(); },
			success: function(data) { 
				if(data.estado == 1){
					Command: toastr.success(
						data.mensaje,
						"Creado", {
							onHidden : function(){
								if(continuar){
									$("#formCrearComplementoAlimentario")[0].reset();
									$('#loader').fadeOut();
								}else{
									window.open('index.php', '_self');
								}
							}
						}
					);
				}
				else {
					Command: toastr.warning(
						data.mensaje,
						"Error al crear", {
							onHidden : function(){ $('#loader').fadeOut(); }
						}
					);
				}
			},
			error: function(data) {
				console.log(data.responseText);
				Command: toastr.error(
					'Al parecer existe un error en el proceso',
					"Error al crear", {
						onHidden : function(){ $('#loader').fadeOut(); }
					}
				);
			}
    	});
  	}
}

function actualizarComplementoAlimentario(continuar) {
	if($('#formActualizarComplementoAlimentario').valid()){
    	$.ajax({
      		type: "post",
      		url: "functions/fn_complementos_alimentarios_actualizar.php",
      		data: $('#formActualizarComplementoAlimentario').serialize(),
      		dataType: 'json',
      		beforeSend: function(){ $('#loader').fadeIn(); },
      		success: function(data) { 
        		if(data.estado == 1){
          			Command: toastr.success(
            			data.mensaje,
            			"Creado", {
              				onHidden : function(){
                				if(continuar){
                  					$('#loader').fadeOut();
                				}else{
                  					window.open('index.php', '_self');
                				}
              				}
            			}
          			);
        		}
        		else {
          			Command: toastr.warning(
            			data.mensaje,
            			"Error al crear", {	
              				onHidden : function(){ $('#loader').fadeOut(); }
            			}
          			);
        		}
      		},
      		error: function(data) {
      			console.log(data.responseText);
        		Command: toastr.error(
          			'Al parecer existe un error en el proceso',
          			"Error al crear", {
            			onHidden : function(){ $('#loader').fadeOut(); }
          			}
        		);
      		}
    	});
  	}
}