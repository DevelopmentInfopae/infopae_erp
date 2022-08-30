$(document).ready(function(){

  	$(document).on('click', '#descargarPlantillaTrazabilidadRutas', function(){ $('#tipoFormato').val(1); $('#titulo').text('Exportar Plantillas'); $('#ventana_formulario_exportar_plantilla_trazabilidad').modal(); });
  	$(document).on('click', '#descargarPlantillaTrazabilidadDetalle', function(){ $('#tipoFormato').val(2); $('#titulo').text('Exportar Plantillas'); $('#ventana_formulario_exportar_plantilla_trazabilidad').modal(); });
  	$(document).on('click', '#informeTrazabilidadRutas', function(){ $('#tipoFormato').val(3); $('#titulo').text('Exportar Informes'); $('#semana_exportar').removeAttr('required',); $('#ventana_formulario_exportar_plantilla_trazabilidad').modal(); });
  	$(document).on('click', '#informeTrazabilidadDetalle', function(){ $('#tipoFormato').val(4); $('#titulo').text('Exportar Informes'); $('#semana_exportar').removeAttr('required',); $('#ventana_formulario_exportar_plantilla_trazabilidad').modal(); });
  	$(document).on('click', '#exportar_plantillaTrazabilidad', function(){ plantilla_trazabilidad(); });
  	$('[data-toggle="tooltip"]').tooltip();

  	jQuery.extend(jQuery.validator.messages, {step:"Por favor ingresa un número entero", required: "Este campo es obligatorio.", remote: "Por favor, rellena este campo.", 
      email: "Por favor, escribe una dirección de correo válida", url: "Por favor, escribe una URL válida.", 
      date: "Por favor, escribe una fecha válida.", dateISO: "Por favor, escribe una fecha (ISO) válida.", 
      number: "Por favor, escribe un número entero válido.", digits: "Por favor, escribe sólo dígitos.", 
      creditcard: "Por favor, escribe un número de tarjeta válido.", equalTo: "Por favor, escribe el mismo valor de nuevo.", 
      accept: "Por favor, escribe un valor con una extensión aceptada.", 
      maxlength: jQuery.validator.format("Por favor, no escribas más de {0} caracteres."), 
      minlength: jQuery.validator.format("Por favor, no escribas menos de {0} caracteres."), 
      rangelength: jQuery.validator.format("Por favor, escribe un valor entre {0} y {1} caracteres."), 
      range: jQuery.validator.format("Por favor, escribe un valor entre {0} y {1}."), 
      max: jQuery.validator.format("Por favor, escribe un valor menor o igual a {0}."), 
      min: jQuery.validator.format("Por favor, escribe un valor mayor o igual a {0}.") });

  	$('#mes_exportar').on('change', function(){buscarSemanas()});
	$.fn.datepicker.dates['en'] = {
	   days: ["Domingo", "Lunes", "Martes", "Miércoles", "Jueves", "Viernes", "Sábado", "Domingo"],
	   daysShort: ["Dom", "Lun", "Mar", "Mié", "Jue", "Vie", "Sab", "Dom"],
	   daysMin: ["Do", "Lu", "Ma", "Mi", "Ju", "Vi", "Sa", "Do"],
	   months: ["Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"],
	   monthsShort: ["Ene", "Feb", "Mar", "Abr", "May", "Jun", "Jul", "Ago", "Sep", "Oct", "Nov", "Dic"]
	};
	$(".datepicker").datepicker();
	$('input').iCheck({ checkboxClass: 'icheckbox_square-green ' });
	$('#mes_fin').val($('#mes_inicio').val());
	$('#nomMesFin').val($('#mes_inicio option:selected').text());
	$('#fecha_inicio_elaboracion').trigger('change');
	$('#municipio').on('change', function(){
		$('#tipo_filtro').prop('selectedIndex',0);
		$('#divBodegas').css('display', 'none');
	})
});

$('#fecha_inicio_elaboracion').on('change', function(){
	var fecha_desde = $(this).val();
	var fecha = fecha_desde.split('-');
	var ultimoDia = new Date(fecha[0], fecha[1], 0);
	$('#fecha_fin_elaboracion').datepicker('setStartDate', $(this).val());
	$('#fecha_fin_elaboracion').datepicker('setEndDate', fecha[0]+'-'+fecha[1]+'-'+ultimoDia.getDate());
	$('#fecha_fin_elaboracion').datepicker('setDate', fecha[0]+'-'+fecha[1]+'-'+ultimoDia.getDate());
});

$('#tipo_documento').on('change', function(){
	$('#loader').fadeIn();
	var tdoc = $("#tipo_documento option:selected").text();
	$.ajax({
	   type: "POST",
	   url: "functions/fn_trazabilidad_obtener_responsables.php",
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
	if (mesinicio == undefined) {
		mesinicio = $('#numeroEntrega').val();
	}
	var filtro = $(this).val();
		$('#divBodegas').css('display', 'none');
		$('#divConductores').css('display', 'none');
		$('#divProductos').css('display', 'none');
		$('#divGrupoEtario').css('display', 'none');
		$('#divTipoComplemento').css('display', 'none');
		$('#divFechaVencimiento').css('display', 'none');

	if (filtro == "1") {
		$.ajax({
		   type: "POST",
		   url: "functions/fn_trazabilidad_obtener_bodegas.php",
		   data: {"municipio" : $('#municipio').val()},
		   beforeSend: function(){},
		   success: function(data){
		      $('#bodegas').html(data);
		   }
		});
		$('#divBodegas').css('display', '');
	} 
	else if (filtro == "2") {
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
	} 
	else if (filtro == "3") {
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
	} 
	else if (filtro == "4") {
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
	} 
	else if (filtro == "5") {
		fechaDe(2);
		$('#fecha_de').val(2);
		$('#divTipoComplemento').css('display', '');
	} 
	else if (filtro == "6") {
		fechaDe(2);
		$('#fecha_de').val(2);
		$('#divFechaVencimiento').css('display', '');
	}
	setTimeout(function() {
		$('#loader').fadeOut();			
	}, 1000);
});

$('#mes_inicio').on('change', function(){
	$('#mes_fin').val($(this).val());
	$('#nomMesFin').val($('#mes_inicio option:selected').text());
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

function plantilla_trazabilidad(){
	if ($('#formulario_exportar_plantilla_trazabilidad').valid()) {
		mesExportar = $('#mes_exportar').val();
		semanaExportar = $('#semana_exportar').val();
		tipo = $('#tipoFormato').val();
		window.open('functions/fn_trazabilidad_exportar_plantilla.php?mes='+mesExportar+'&semana='+semanaExportar+'&tipo='+tipo, '_blank');	
		$('#ventana_formulario_exportar_plantilla_trazabilidad').modal('hide');
		$('#formulario_exportar_plantilla_trazabilidad').trigger("reset");
		$('#semana_exportar').prop('required',true);
	}
}

function buscarSemanas(){
	var mesPost = $('#mes_exportar').val();
	$.ajax({
		type: "POST",
		url: "functions/fn_trazabilidad_obtener_semanas.php",
		data: { 'mes' : mesPost },
		beforeSend: function(){ $('#loader').fadeIn() },
		success: function(data){
		   $('#semana_exportar').html(data);
		}
	}).always($('#loader').fadeOut());
}