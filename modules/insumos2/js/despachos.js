$(document).ready(function(){

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

     jQuery.extend(jQuery.validator.messages, {//Configuración jquery valid
      step : "Por favor, escribe un número entero",
      required: "Este campo es obligatorio.",
      remote: "Por favor, rellena este campo.",
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

	$('.select2').select2();
	$('.i-checks').iCheck({ checkboxClass: 'icheckbox_square-green' });

	$('#mes_fin').val($('#mes_inicio').val());
	$('#mes_inicio').on('change', function(){
		var mesInicial = $('#mes_inicio').val();
		// console.log(mesInicial);
		// $('#mes_fin').prop('selectedIndex',mesInicial);	
		$('#mes_fin').val(mesInicial);	

	});

	buscarProveedores();
	$('#tipoDocumento').on('change', function() {
         var tipoDocumento = $('#tipoDocumento').val();
         $.ajax({
         	url: 'functions/fn_insumos_obtener_responsables.php',
         	type: 'POST',
         	data: {tipoDocumento : tipoDocumento},
         	beforeSend: function(){ $('#loader').fadeIn(); },
         })
         .done(function(data) {
         	$('#responsable').html(data);
         })
         .fail(function() {
         	console.log("error");
         })
         .always(function() {
         	$('#loader').fadeOut(); 
         });  
	});

	buscarPorRuta();
	$('#rutas').on('change', function() { buscarPorRuta(); });

	
	buscarInstituciones();
	$('#municipio').on('change', function(){ buscarInstituciones($('#municipio').val()); });

	var institucion = $('#institucion').val();
	if (institucion != "") {buscarSedes(institucion); }
	$('#institucion').on('change', function(){ buscarSedes($('#institucion').val()); });
});

function buscarProveedores () {
	var tipoDocumento = $('#tipoDocumento').val();
	var responsable = $('#responsable').val();
    $.ajax({
        url: 'functions/fn_insumos_obtener_responsables.php',
        type: 'POST',
        data: {tipoDocumento : tipoDocumento, responsable : responsable},
        beforeSend: function(){ $('#loader').fadeIn(); },
    })
   	.done(function(data) {
        $('#responsable').html(data);
    })
    .fail(function() {
        console.log("error");
    })
    .always(function() {
       $('#loader').fadeOut(); 
    });  
}

function buscarInstituciones () {
	var municipio = $('#municipio').val();
	var institucion = $('#institucion').val();
	$.ajax({
		url: 'functions/fn_insumos_obtener_instituciones.php',
		type: 'POST',
		data: {municipio : municipio, institucion : institucion},
		beforeSend: function(){ $('#loader').fadeIn(); },
	})
	.done(function(data) {
		$('#institucion').html(data);
	})
	.fail(function() {
		console.log("error");
	})
	.always(function() {
		$('#loader').fadeOut(); 
	});
}

function buscarSedes (institucion) {
	$.ajax({
		url: 'functions/fn_insumos_obtener_sedes.php',
		type: 'POST',
		data: {institucion : institucion},
		beforeSend: function(){ $('#loader').fadeIn(); },
	})
	.done(function(data) {
		$('#sede').html(data);
	})
	.fail(function() {
		console.log("error");
	})
	.always(function() {
		$('#loader').fadeOut(); 
	});	
}

function buscarPorRuta(){
	var ruta = $('#rutas').val();
	if (ruta != "") {
		$('#municipio').prop('disabled', true);
		$('#institucion').prop('disabled', true);
		$('#sede').prop('disabled', true);
	}else{
		$('#municipio').prop('disabled', false);
		$('#institucion').prop('disabled', false);
		$('#sede').prop('disabled', false);
	}
}


function informeDespachos(num){

	if (num == 1) {	
		var aux = $('#observaciones').val()
		$('#paginasObservaciones').val(aux);

		$('#formDespachos').prop('action', 'functions/fn_insumos_informe_despachos.php').prop('method');
		var checks = 0;
		$('input[name="sedes[]"]').each(function(){
			if ($(this).prop('checked')) {
				checks++;
			}
		});

		if (checks > 0) {
			$('#formDespachos').submit();
		} else {
			Command: toastr.warning("Debe seleccionar al menos un despacho para exportar.", "No hay despacho seleccionados.", {onHidden : function(){
			      				}})
		}
	}
}

function informeDespachos2(num){
	if (num == 1) {
		$('#formDespachos').prop('action', 'functions/fn_insumos_informe_despachos2.php').prop('method');
		var checks = 0;
		$('input[name="sedes[]"]').each(function(){
			if ($(this).prop('checked')) {
				checks++;
			}
		});

		if (checks > 0) {
			$('#formDespachos').submit();
		} else {
			Command: toastr.warning("Debe seleccionar al menos un despacho para exportar.", "No hay despacho seleccionados.", {onHidden : function(){
			      				}})
		}
	}
}

function informeDespachos2Vertical(num){
	if (num == 1) {
		$('#formDespachos').prop('action', 'functions/fn_insumos_informe_despachos2_vertical.php').prop('method');
		var checks = 0;
		$('input[name="sedes[]"]').each(function(){
			if ($(this).prop('checked')) {
				checks++;
			}
		});

		if (checks > 0) {
			var estado = $('#imprimirMes').prop("checked"); 
			$('#formDespachos #mesImprimir').val(estado);
			$('#formDespachos').submit(); 
		} else {
			Command: toastr.warning("Debe seleccionar al menos un despacho para exportar.", "No hay despacho seleccionados.", {onHidden : function(){
			      				}})
		}
	}
}

function informeDespachosVertical2(num){
	if (num == 1) {
		$('#formDespachos').prop('action', 'functions/fn_insumos_informe_despachos_vertical2.php').prop('method');
		var checks = 0;
		$('input[name="sedes[]"]').each(function(){
			if ($(this).prop('checked')) {
				checks++;
			}
		});

		if (checks > 0) {
			var estado = $('#imprimirMes').prop("checked"); 
			$('#formDespachos #mesImprimir').val(estado);
			$('#formDespachos').submit(); 
		} else {
			Command: toastr.warning("Debe seleccionar al menos un despacho para exportar.", "No hay despacho seleccionados.", {onHidden : function(){
			      				}})
		}
	}
}

function informeDespachosInstitucion(num){
	if (num == 1) {
		$('#formDespachos').prop('action', 'functions/fn_insumos_informe_despachos_institucion.php');
		var checks = 0;
		var inst = 0;
		var dif_inst = 0;
		$('input[name="sedes[]"]:checked').each(function(){
			checks++;
			if (inst == 0) {
				inst = $(this).data('inst');
			}

			if (inst != $(this).data('inst')) {
				dif_inst++;
			}
		});

		if (checks > 0) {
			$('#formDespachos').submit();
		} else {
			if (checks == 0) {
				Command: toastr.warning("Debe seleccionar al menos un despacho para exportar.", "No hay despacho seleccionados.", {onHidden : function(){
			      				}})
			}
		}
	}
}

function informeDespachosConsolidado(num){
	if (num == 1) {
		$('#formDespachos').prop('action', 'functions/fn_insumos_informe_despachos_consolidado.php');
		var checks = 0;
		var inst = 0;
		var dif_inst = 0;
		$('input[name="sedes[]"]:checked').each(function(){
			checks++;
			if (inst == 0) {
				inst = $(this).data('inst');
			}
			if (inst != $(this).data('inst')) {
				dif_inst++;
			}
		});
		if (checks > 0) {
			$('#formDespachos').submit();
		} else {
			if (checks == 0) {
				Command: toastr.warning("Debe seleccionar al menos un despacho para exportar.", "No hay despacho seleccionados.", {onHidden : function(){
			      				}})
			}
		}
	}
}

function editarDespacho(){
	var checks = 0;
	$('input[name="sedes[]"]').each(function(){
		if ($(this).prop('checked')) {
			checks++;
			input = this;
		}
	});

	if (checks > 0) {
		if (checks == 1) {
			$('#id_despacho').val($(input).data('iddespacho'));
			$('#mesTabla').val($('#mes_inicio').val());
			$('#editar_despacho').submit();
		} else {
			Command: toastr.warning("Debe seleccionar sólo un despacho para editar.", "Seleccione sólo un despacho.", {onHidden : function(){
			      				}})
		}
	} else {
		Command: toastr.warning("Debe seleccionar un despacho para editar.", "No hay despacho seleccionado.", {onHidden : function(){
		      				}})
	}
}

function eliminarDespachos(){
	$('#modalEliminarDespachos').modal('hide');
	$('#loader').fadeIn();
	var checks = 0;
	$('input[name="sedes[]"]').each(function(){
		if ($(this).prop('checked')) {
			checks++;
		}
	});
	
	if (checks > 0) {
		datos = $('#formDespachos').serialize();
		$.ajax({
		   type: "POST",
		   url: "functions/fn_insumos_eliminar_despachos.php",
		   data: datos,
		   beforeSend: function(){},
		   success: function(data){
		     if (data == "1") {
		     	Command: toastr.success("Los despachos se eliminaron exitosamente.", "Eliminado con éxito.", {onHidden : function(){
		     			location.reload();
		      				}})
		     } else {
		     	Command: toastr.error("Hubo un error al eliminar los despachos.", "Error al eliminar.", {onHidden : function(){
					$('#loader').fadeOut();
		     		console.log(data);
		     	}})
		     }
		   }
	 });
	} else {
		Command: toastr.warning("Debe seleccionar al menos un despacho para eliminar.", "No hay despacho seleccionados.", {onHidden : function(){
		      			$('#loader').fadeOut();
		      				}})
	}
}


function informeConsolidadoVertical(num){
	if (num == 1) {
		$('#formDespachos').prop('action', 'functions/fn_insumos_informe_consolidado_vertical.php').prop('method');
		var checks = 0;
		$('input[name="sedes[]"]').each(function(){
			if ($(this).prop('checked')) {
				checks++;
			}
		});

		if (checks > 0) {
			var estado = $('#imprimirMes').prop("checked");
			var ruta = $('#rutas').val();
			$('#formDespachos #ruta').val(ruta); 
			$('#formDespachos #mesImprimir').val(estado);
			$('#formDespachos').submit(); 
		} else {
			Command: toastr.warning("Debe seleccionar al menos un despacho para exportar.", "No hay despacho seleccionados.", {onHidden : function(){
			      				}})
		}
	}
}