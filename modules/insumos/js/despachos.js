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


	// $('#nomMesFin').val($('#mes_inicio option:selected').text());
	// $('#mes_fin').val($('#mes_inicio').val());

	$('input').iCheck({
     checkboxClass: 'icheckbox_square-green '
  	});

	$.ajax({
	   type: "POST",
	   url: "functions/fn_insumos_obtener_municipios.php",
	   data: { "mes_tabla" : $('#mes_inicio').val() },
	   beforeSend: function(){},
	   success: function(data){
	     $('#municipio').html(data);
	   }
	 });

	$.fn.datepicker.dates['en'] = {
    days: ["Domingo", "Lunes", "Martes", "Miércoles", "Jueves", "Viernes", "Sábado", "Domingo"],
    daysShort: ["Dom", "Lun", "Mar", "Mié", "Jue", "Vie", "Sab", "Dom"],
    daysMin: ["Do", "Lu", "Ma", "Mi", "Ju", "Vi", "Sa", "Do"],
    months: ["Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"],
    monthsShort: ["Ene", "Feb", "Mar", "Abr", "May", "Jun", "Jul", "Ago", "Sep", "Oct", "Nov", "Dic"]
	};

	$( ".datepicker" ).datepicker();
	$('#fecha_fin_elaboracion').datepicker('setStartDate', $('#fecha_inicio_elaboracion').val());
	$('#fecha_inicio_elaboracion').datepicker('setEndDate', $('#fecha_fin_elaboracion').val());
});

$('#fecha_inicio_elaboracion').on('change', function(){
	$('#fecha_fin_elaboracion').datepicker('setStartDate', $(this).val());
});

$('#fecha_fin_elaboracion').on('change', function(){
	$('#fecha_inicio_elaboracion').datepicker('setEndDate', $(this).val());
});

$('#tipo_documento').on('change', function(){

	$('#loader').fadeIn();

	var tdoc = $("#tipo_documento option:selected").text();

	$.ajax({
	   type: "POST",
	   url: "functions/fn_insumos_obtener_responsables.php",
	   data: { "tipo_documento" : tdoc },
	   beforeSend: function(){},
	   success: function(data){
	     $('#proveedor').html(data);
	     $('#loader').fadeOut();
	   }
	 });

});

$('#tipo_filtro').on('change', function(){

	$('#loader').fadeIn();

	mesinicio = $('#mes_inicio').val();

	var filtro = $(this).val();
		$('#divBodegas').css('display', 'none');
		$('#divConductores').css('display', 'none');
		$('#divProductos').css('display', 'none');
		$('#divGrupoEtario').css('display', 'none');
		$('#divTipoComplemento').css('display', 'none');

	if (filtro == "1") {
		$.ajax({
		    type: "POST",
		    url: "functions/fn_trazabilidad_obtener_bodegas.php",
		    beforeSend: function(){},
		    success: function(data){
		      $('#bodegas').html(data);
		    }
		  });
		$('#divBodegas').css('display', '');
	} else if (filtro == "2") {
		fechaDe(2);
		$('#fecha_de').val(2);
		$.ajax({
		    type: "POST",
		    url: "functions/fn_trazabilidad_obtener_conductores.php",
		    data : {"mestabla" : mesinicio},
		    beforeSend: function(){},
		    success: function(data){
		      $('#conductor').html(data);
		    }
		  });
		$('#divConductores').css('display', '');
	} else if (filtro == "3") {
		fechaDe(2);
		$('#fecha_de').val(2);
		$.ajax({
		    type: "POST",
		    url: "functions/fn_trazabilidad_obtener_productos.php",
		    data : {"mestabla" : mesinicio},
		    beforeSend: function(){},
		    success: function(data){
		      $('#producto').html(data);
		    }
		  });
		$('#divProductos').css('display', '');
	} else if (filtro == "4") {
		fechaDe(2);
		$('#fecha_de').val(2);
		$.ajax({
		    type: "POST",
		    url: "functions/fn_trazabilidad_obtener_grupo_etarios.php",
		    beforeSend: function(){},
		    success: function(data){
		      $('#grupo_etario').html(data);
		    }
		  });
		$('#divGrupoEtario').css('display', '');
	} else if (filtro == "5") {
		fechaDe(2);
		$('#fecha_de').val(2);
		$('#divTipoComplemento').css('display', '');
	}

	setTimeout(function() {
		$('#loader').fadeOut();
				}, 1000);
});

$('#mes_inicio').on('change', function(){
	// $('#mes_fin').val($(this).val());
	// $('#nomMesFin').val($('#mes_inicio option:selected').text());
	$('#loader').fadeIn();
	$('#tablaMesInicio').val($(this).val());

	$.ajax({
	   type: "POST",
	   url: "functions/fn_insumos_obtener_municipios.php",
	   data: { "mes_tabla" : $('#mes_inicio').val() },
	   beforeSend: function(){},
	   success: function(data){
	     $('#municipio').html(data);
	     $('#loader').fadeOut();
	   }
	 });
});

$('#mes_fin').on('change', function(){
	$('#mes_fin').val($(this).val());
	// $('#nomMesFin').val($('#mes_inicio option:selected').text());
	// $('#loader').fadeIn();
	$('#tablaMesFin').val($(this).val());

	// $.ajax({
	//    type: "POST",
	//    url: "functions/fn_insumos_obtener_municipios.php",
	//    data: { "mes_tabla" : $('#mes_inicio').val() },
	//    beforeSend: function(){},
	//    success: function(data){
	//      $('#municipio').html(data);
	//      $('#loader').fadeOut();
	//    }
	//  });
});

$('#fecha_de').on('change', function(){
	fechaDe($(this).val());
});

function fechaDe(num){
	if (num==1) {
		$('#fechaElaboracion').css('display', '');
		$('#fechaDiasDespachos').css('display', 'none');
	} else if (num==2) {
		$('#fechaElaboracion').css('display', 'none');
		$('#fechaDiasDespachos').css('display', '');
	}
}

$('#dia_inicio').on('change', function(){

	if ($(this).val() <= 30) {
		$('#dia_fin').val(parseInt($(this).val())+1);
	} else if ($(this).val() == 31) {
		$('#dia_fin').val($(this).val());
	}
});


//JS PARA CONSULTA DE DESPACHOS (despachos.php)

function informeDespachos(num){
	if (num == 1) {
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
			$('#id_despacho').val($(input).val());
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

$('#municipio_desp').on('change', function(){
  $('#loader').fadeIn();
  var cod_municipio = $(this).val();
  $.ajax({
     type: "POST",
     url: "functions/fn_insumos_obtener_instituciones.php",
     data: { "cod_municipio" : cod_municipio },
     beforeSend: function(){},
     success: function(data){
       $('#institucion_desp').html(data);
       $('#loader').fadeOut();
     }
   });
});

$('#institucion_desp').on('change', function(){
  $('#loader').fadeIn();
  var cod_inst = $(this).val();
  $.ajax({
     type: "POST",
     url: "functions/fn_insumos_obtener_sedes.php",
     data: { "cod_inst" : cod_inst },
     beforeSend: function(){},
     success: function(data){
       $('#sede_desp').html(data);
       $('#loader').fadeOut();
     }
   });
});


$(document).on('ifChecked', '.checkDespacho', function(){

	var despachos = "";

	$('.checkDespacho').each(function(index, val){
		if ($(this).iCheck('data')[0].checked) {
			despachos += $(this).data('iddespacho')+"_"+$(this).data('mesdespacho')+", ";
		}
	});

	$('#despachos_seleccionados').val(despachos);

});

