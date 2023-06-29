$(document).ready(function(){

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
      min: jQuery.validator.format("Por favor, escribe un valor mayor o igual a {0}.") 
   });

	$.fn.datepicker.dates['en'] = {
	   days: ["Domingo", "Lunes", "Martes", "Miércoles", "Jueves", "Viernes", "Sábado", "Domingo"],
	   daysShort: ["Dom", "Lun", "Mar", "Mié", "Jue", "Vie", "Sab", "Dom"],
	   daysMin: ["Do", "Lu", "Ma", "Mi", "Ju", "Vi", "Sa", "Do"],
	   months: ["Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"],
	   monthsShort: ["Ene", "Feb", "Mar", "Abr", "May", "Jun", "Jul", "Ago", "Sep", "Oct", "Nov", "Dic"]
	};
	$('#loaderAjax').fadeOut();

  	$('[data-toggle="tooltip"]').tooltip();

	var maximumDate = $('#fecha_inicio_elaboracion').attr('max');

	$(".datepicker_inicio").datepicker({
    	endDate: new Date(maximumDate)
	});

	$(".datepicker_fin").datepicker();

	$('#totales').iCheck({ 
		checkboxClass: 'icheckbox_square-green ' 
	});
	

	$('.selectMesExportar').select2();
	$('.selectSemanaExportar').select2();
	$('.selectSemanaExportarFinal').select2();
	$('.selectFechaDe').select2();
	$('.selectMesInicio').select2();
	$('.selectDiaInicio').select2();
	$('.mesFin').select2();
	$('.selectDiaFin').select2();
	$('.selectMunicipio').select2();
	$('.selectTipoDocumento').select2();
	$('.selectProveedor').select2();
	$('.selectTipoFiltro').select2();
	$('.selectTipoBodega').select2();
	$('.selectBodegas').select2();
	$('.selectCoductores').select2();
	$('.selectProductos').select2();
	$('.selectGrupoEtario').select2();
	$('.selectTipoComplemento').select2();


	$(document).on('click', '#descargarPlantillaTrazabilidadRutas', function(){ 
		$('#tipoFormato').val(1); 
		$('#titulo').text('Exportar Plantillas'); 
		$('#ventana_formulario_exportar_plantilla_trazabilidad').modal(); 
	});
  	$(document).on('click', '#descargarPlantillaTrazabilidadDetalle', function(){ 
  		$('#tipoFormato').val(2); 
  		$('#titulo').text('Exportar Plantillas'); 
  		$('#ventana_formulario_exportar_plantilla_trazabilidad').modal(); 
  	});
  	$(document).on('click', '#informeTrazabilidadRutas', function(){ 
  		$('#tipoFormato').val(3); 
  		$('#titulo').text('Exportar Informes'); 
  		$('#ventana_formulario_exportar_plantilla_trazabilidad').modal(); 
  	});
  	$(document).on('click', '#informeTrazabilidadDetalle', function(){ 
  		$('#tipoFormato').val(4); 
  		$('#titulo').text('Exportar Informes'); 
  		$('#ventana_formulario_exportar_plantilla_trazabilidad').modal(); 
  	});
  	$(document).on('click', '#exportar_plantillaTrazabilidad', function(){ plantilla_trazabilidad(); });

  	// $('#fecha_fin_elaboracion').val($('#fecha_inicio_elaboracion').val());
  	var fecha_desde = $('#fecha_inicio_elaboracion').val();
	var fecha = fecha_desde.split('-');
	var ultimoDia = new Date(fecha[0], fecha[1], 0);
	$('#fecha_fin_elaboracion').datepicker('setDate', fecha[0]+'-'+fecha[1]+'-'+ultimoDia.getDate());

	// manejo de cache 
	if(sessionStorage.getItem("mes_exportar_trazabilidad") != null){ 
		var semanaCache = sessionStorage.getItem('semana_exportar_trazabilidad');
		var semanaCacheFinal = sessionStorage.getItem('semana_exportar_final_trazabilidad');
		var mesPost = sessionStorage.getItem('mes_exportar_trazabilidad'); 
		(mesPost !== '' ) ? $('#'+mesPost).attr('selected', true) : 'f';
		$( "#mes_exportar" ).select2('destroy');
		$( "#mes_exportar" ).val(sessionStorage.getItem('mes_exportar_trazabilidad'));
		$( "#mes_exportar" ).select2();
		$( "#mes_exportar" ).select2('val', sessionStorage.getItem("mes_exportar_trazabilidad"));
		$.ajax({
			type: "POST",
			url: "functions/fn_trazabilidad_obtener_semanas.php",
			data: { 'mes' : mesPost , 'semanaCache' : semanaCache },
			beforeSend: function(){ $('#loaderAjax').fadeIn() },
			success: function(data){
		   	$('#semana_exportar').html(data);
		   	$.ajax({
		   		type : 'POST',
		   		url: "functions/fn_trazabilidad_obtener_semanas_final.php",
		   		data: { 'mes' : mesPost , 'semanaCache' : semanaCache, 'semanaCacheFinal' : semanaCacheFinal },
		   		beforeSend: function(){ $('#loaderAjax').fadeIn() },
					success: function(data){
						$('#semana_exportar_final').html(data);
						$('.selectSemanaExportarFinal').select2();
						$('#loaderAjax').fadeOut();
					}
		   	})
		   	$('.selectSemanaExportar').select2();
			}
		}).always($('#loaderAjax').fadeOut());
	}

	$( "#mes_exportar" ).change(function() {
		if ($('#mes_exportar').val() == '') {
			$('#semana_exportar').select2('val','');
			$('#semana_exportar_final').select2('val','');
		}else{
			mesCache = sessionStorage.getItem("mes_exportar_trazabilidad");
			$('#'+mesCache).attr('selected', false);
			sessionStorage.setItem("mes_exportar_trazabilidad", $("#mes_exportar").val());
			var semanaCache = sessionStorage.getItem('semana_exportar_trazabilidad');
			var semanaCacheFinal = sessionStorage.getItem('semana_exportar_final_trazabilidad');
			$('#semana_exportar').select2('val','');
			$('#semana_exportar_final').select2('val','');
			var mesPost = $('#mes_exportar').val();
			$('#'+mesPost).attr('selected', true);
			$.ajax({
				type: "POST",
				url: "functions/fn_trazabilidad_obtener_semanas.php",
				data: { 'mes' : mesPost },
				beforeSend: function(){ $('#loaderAjax').fadeIn() },
				success: function(data){
		   		$('#semana_exportar').html(data);
		   		$.ajax({
		   			type : 'POST',
		   			url: "functions/fn_trazabilidad_obtener_semanas_final.php",
		   			data: { 'mes' : mesPost , 'semanaCache' : semanaCache, 'semanaCacheFinal' : semanaCacheFinal },
		   			beforeSend: function(){},
						success: function(data){
							$('#semana_exportar_final').html(data);
						}
		   		})
				}
			}).always($('#loaderAjax').fadeOut());
		}
	});


	if(sessionStorage.getItem("semana_exportar_trazabilidad") != null){
		$( "#semana_exportar" ).select2('val', sessionStorage.getItem("semana_exportar_trazabilidad"));
	}
	$( "#semana_exportar" ).change(function() { 
		sessionStorage.setItem("semana_exportar_trazabilidad", $(this).val());
		var semanaCache = $('#semana_exportar').val();
		$('#semana_exportar_final').select2('val','');
		var mesPost = $('#mes_exportar').val();
		$.ajax({
			type: "POST",
			url: "functions/fn_trazabilidad_obtener_semanas.php",
			data: { 'mes' : mesPost , 'semanaCache' : semanaCache},
			beforeSend: function(){ $('#loaderAjax').fadeIn() },
			success: function(data){
		   	$('#semana_exportar').html(data);
		   	$.ajax({
		   		type : 'POST',
		   		url: "functions/fn_trazabilidad_obtener_semanas_final.php",
		   		data: { 'mes' : mesPost , 'semanaCache' : semanaCache, 'semanaCacheFinal' : semanaCacheFinal },
		   		beforeSend: function(){},
					success: function(data){
						$('#semana_exportar_final').html(data);
					}
		   	})
			}
		}).always($('#loaderAjax').fadeOut());
	});

	if(sessionStorage.getItem("semana_exportar_final_trazabilidad") != null){
		$( "#semana_exportar_final" ).select2('val', sessionStorage.getItem("semana_exportar_final_trazabilidad"));
	}
	$( "#semana_exportar_final" ).change(function() {
		$('#sf'+sessionStorage.getItem('semana_exportar_final_trazabilidad')).attr('selected', false);
		sessionStorage.setItem("semana_exportar_final_trazabilidad", $(this).val());
		var semanaExportarFinal = $('#semana_exportar_final').val();
		$('#sf'+semanaExportarFinal).attr('selected', true);
	});


	if(sessionStorage.getItem("fecha_de_trazabilidad") != null){
		$( "#fecha_de" ).select2('val', sessionStorage.getItem("fecha_de_trazabilidad"));
		fechaDe(sessionStorage.getItem('fecha_de_trazabilidad'));
	}
	$( "#fecha_de" ).change(function() { 
		sessionStorage.setItem("fecha_de_trazabilidad", $("#fecha_de").val());
		fechaDe(sessionStorage.getItem('fecha_de_trazabilidad'));
	});


	if(sessionStorage.getItem("mes_inicio_trazabilidad") != null){
		$("#mes_inicio").select2('val', sessionStorage.getItem("mes_inicio_trazabilidad"));
		$('#mes_fin').select2('val', $('#mes_inicio').val());
		$('#mes_fin').select2("enable", false);
	}
	$( "#mes_inicio" ).change(function() {
		sessionStorage.setItem("mes_inicio_trazabilidad", $("#mes_inicio").val());
		var mes = $('#mes_inicio').val(); 
		$('#mes_fin').select2('val', mes); 
		$('#mes_fin').select2("enable", false);
		$('#mes_fin').val(sessionStorage.getItem("mes_inicio_trazabilidad"));
	});


	if(sessionStorage.getItem("dia_inicio_trazabilidad") != null){
		$("#dia_inicio").select2('val', sessionStorage.getItem("dia_inicio_trazabilidad"));
	}
	$( "#dia_inicio" ).change(function() {
		sessionStorage.setItem("dia_inicio_trazabilidad", $("#dia_inicio").val());
		if (sessionStorage.getItem('dia_inicio_trazabilidad') <= 30) {
			dayValue = parseInt(sessionStorage.getItem('dia_inicio_trazabilidad'))+1;
			$('#dia_fin').select2('val',dayValue);
		} else if (sessionStorage.getItem('dia_inicio_trazabilidad') == 31) {
			$('#dia_fin').select2('val','31');
		}
		sessionStorage.setItem("dia_fin_trazabilidad", $("#dia_fin").val());
	});

	$('#dia_fin').change(function(){
		if ( parseInt($('#dia_fin').val()) < parseInt($('#dia_inicio').val()) ) {
			dayValue = parseInt($('#dia_inicio').val())+1;
			$('#dia_fin').select2('val',dayValue);
			sessionStorage.setItem("dia_fin_trazabilidad", $("#dia_fin").val());
		}else{
			sessionStorage.setItem("dia_fin_trazabilidad", $("#dia_fin").val());
		}
	})


	if (sessionStorage.getItem('dia_fin_trazabilidad') != null) {
		$("#dia_fin").select2('val', sessionStorage.getItem("dia_fin_trazabilidad"));
	}
	$('#dia_fin').change(function(){
		sessionStorage.getItem('dia_fin_trazabilidad', $('#dia_fin').val());
	})


	if(sessionStorage.getItem("fecha_inicio_elaboracion_trazabilidad") != null){
		$("#fecha_inicio_elaboracion").val(sessionStorage.getItem("fecha_inicio_elaboracion_trazabilidad"));
		$('#fecha_inicio_elaboracion').trigger('change');
		$('#fecha_fin_elaboracion').attr('min',$("#fecha_inicio_elaboracion").val())
	}
	$( "#fecha_inicio_elaboracion" ).change(function() {
		sessionStorage.setItem("fecha_inicio_elaboracion_trazabilidad", $("#fecha_inicio_elaboracion").val());
		var fecha_desde = $('#fecha_inicio_elaboracion').val();
		var fecha = fecha_desde.split('-');
		var ultimoDia = new Date(fecha[0], fecha[1], 0);
		$('#fecha_fin_elaboracion').datepicker('setDate', fecha[0]+'-'+fecha[1]+'-'+ultimoDia.getDate());
	});


	if(sessionStorage.getItem("fecha_fin_elaboracion_trazabilidad") != null){
		$("#fecha_fin_elaboracion").val(sessionStorage.getItem("fecha_fin_elaboracion_trazabilidad"));
	}
	$( "#fecha_fin_elaboracion" ).change(function() {
		sessionStorage.setItem("fecha_fin_elaboracion_trazabilidad", $("#fecha_fin_elaboracion").val());
	});


	if(sessionStorage.getItem("municipio_trazabilidad") != null){
		$("#municipio").select2('val', sessionStorage.getItem("municipio_trazabilidad"));
	}
	$( "#municipio" ).change(function() {
		sessionStorage.setItem("municipio_trazabilidad", $("#municipio").val());
		$('#divBodegas').css('display', 'none');
	});


	if(sessionStorage.getItem("tipo_documento_trazabilidad") != null){
		$("#tipo_documento").select2('val', sessionStorage.getItem("tipo_documento_trazabilidad"));
		var tdoc = $("#tipo_documento option:selected").text();
		var prov = '';
		if (sessionStorage.getItem('proveedor_trazabilidad') != null) { prov = sessionStorage.getItem('proveedor_trazabilidad'); }
		$.ajax({
	   	type: "POST",
	   	url: "functions/fn_trazabilidad_obtener_responsables.php",
	   	data: { "tipo_documento" : tdoc , "proveedorActual" : prov },
	   	beforeSend: function(){ $('#loaderAjax').fadeIn() },
	   	success: function(data){
	     		$('#proveedor').html(data);
	     		$('.selectProveedor').select2();
	   	}
	 	}).always($('#loaderAjax').fadeOut());
	}
	$( "#tipo_documento" ).change(function() {
		sessionStorage.setItem("tipo_documento_trazabilidad", $("#tipo_documento").val());
		$('#proveedor').select2('val','');
		var tdoc = $("#tipo_documento option:selected").text();
		$.ajax({
	   	type: "POST",
	   	url: "functions/fn_trazabilidad_obtener_responsables.php",
	   	data: { "tipo_documento" : tdoc },
	   	beforeSend: function(){ $('#loaderAjax').fadeIn() },
	   	success: function(data){
	     		$('#proveedor').html(data);
	   	}
	 	}).always($('#loaderAjax').fadeOut());
	});


	if(sessionStorage.getItem("proveedor_trazabilidad") != null){
		$("#proveedor").select2('val', sessionStorage.getItem("proveedor_trazabilidad"));
	}
	$( "#proveedor" ).change(function() {
		sessionStorage.setItem("proveedor_trazabilidad", $("#proveedor").val());
	});


	if(sessionStorage.getItem("tipo_filtro_trazabilidad") != null){
		$("#tipo_filtro").select2('val', sessionStorage.getItem("tipo_filtro_trazabilidad"));
		tipo_filtro();
	}
	$( "#tipo_filtro" ).change(function() {
		sessionStorage.setItem("tipo_filtro_trazabilidad", $("#tipo_filtro").val());
		tipo_filtro();
	});


	if(sessionStorage.getItem("tipo_bodega_trazabilidad") != null){
		$("#tipo_bodega").select2('val', sessionStorage.getItem("tipo_bodega_trazabilidad"));
	}
	$( "#tipo_bodega" ).change(function() {
		sessionStorage.setItem("tipo_bodega_trazabilidad", $("#tipo_bodega").val());
	});


	if(sessionStorage.getItem("bodegas_trazabilidad") != null){
		$("#bodegas").select2('val', sessionStorage.getItem("bodegas_trazabilidad"));
	}
	$( "#bodegas" ).change(function() {
		sessionStorage.setItem("bodegas_trazabilidad", $("#bodegas").val());
	});


	if(sessionStorage.getItem("conductor_trazabilidad") != null){
		$("#conductor").select2('val', sessionStorage.getItem("conductor_trazabilidad"));
	}
	$( "#conductor" ).change(function() {
		sessionStorage.setItem("conductor_trazabilidad", $("#conductor").val());
	});


	if(sessionStorage.getItem("producto_trazabilidad") != null){
		$("#producto").select2('val', sessionStorage.getItem("producto_trazabilidad"));
	}
	$( "#producto" ).change(function() {
		sessionStorage.setItem("producto_trazabilidad", $("#producto").val());
	});

	// sessionStorage.setItem('totales_trazabilidad', $('#totales').prop('checked'));
	if(sessionStorage.getItem("totales_trazabilidad") != null){
		if (sessionStorage.getItem('totales_trazabilidad') == 'true') {
			$('#totales').iCheck( "check");
		}else{
			$('#totales').iCheck( "uncheck");
		}
	}	
	$('#totales').on('ifChecked', function () { 
		sessionStorage.setItem("totales_trazabilidad", true);
	})
	$('#totales').on('ifUnchecked', function () { 
		sessionStorage.setItem("totales_trazabilidad", false);
	})


	if(sessionStorage.getItem("grupoEtario_trazabilidad") != null){
		$("#grupo_etario").select2('val', sessionStorage.getItem("grupoEtario_trazabilidad"));
	}
	$( "#grupo_etario" ).change(function() {
		sessionStorage.setItem("grupoEtario_trazabilidad", $("#grupo_etario").val());
	});


	if(sessionStorage.getItem("tipoComplemento_trazabilidad") != null){
		$("#tipo_complemento").select2('val', sessionStorage.getItem("tipoComplemento_trazabilidad"));
	}
	$( "#tipo_complemento" ).change(function() {
		sessionStorage.setItem("tipoComplemento_trazabilidad", $("#tipo_complemento").val());
	});

});


function fechaDe(num){
	if (num==1) {
		$('#fechaElaboracion').css('display', '');
		$('#fechaDiasDespachos').css('display', 'none');
		$('#tipo_filtro').select2('val', '');
		$('#divsubFiltro').css('display', 'none');
	} else if (num==2) {
		$('#fechaElaboracion').css('display', 'none');
		$('#fechaDiasDespachos').css('display', '');
		$('#divsubFiltro').css('display', '');
	} else if(num==3){
		$('#fechaElaboracion').css('display', 'none');
		$('#fechaDiasDespachos').css('display', '');
		$('#divsubFiltro').css('display', '');
	}
}


function plantilla_trazabilidad(){
	if ($('#formulario_exportar_plantilla_trazabilidad').valid()) {
		mesExportar = $('#mes_exportar').val();
		semanaExportar = $('#semana_exportar').val();
		semanaExportaFinal = $('#semana_exportar_final').val();
		tipo = $('#tipoFormato').val();
		window.open('functions/fn_trazabilidad_exportar_plantilla.php?mes='+mesExportar+'&semana='+semanaExportar+'&semanaFinal='+semanaExportaFinal+'&tipo='+tipo, '_blank');	
		$('#ventana_formulario_exportar_plantilla_trazabilidad').modal('hide');
		$('#formulario_exportar_plantilla_trazabilidad').trigger("reset");
	}
}


function tipo_filtro() {
	mesinicio = $('#mes_inicio').val();
	if (mesinicio == undefined) {
		mesinicio = $('#numeroEntrega').val();
	}
	var filtro = $('#tipo_filtro').val();
	$('#divBodegas').css('display', 'none');
	$('#divConductores').css('display', 'none');
	$('#divProductos').css('display', 'none');
	$('#divGrupoEtario').css('display', 'none');
	$('#divTipoComplemento').css('display', 'none');
	$('#divFechaVencimiento').css('display', 'none');
		
	if (filtro == "1") {
		fechaDe(sessionStorage.getItem('fecha_de_trazabilidad'));
		$('#fecha_de').select2('val', sessionStorage.getItem('fecha_de_trazabilidad'));
		var bodegaCache = sessionStorage.getItem('bodegas_trazabilidad');
		$.ajax({
		   type: "POST",
		   url: "functions/fn_trazabilidad_obtener_bodegas.php",
		   data: {"municipio" : $('#municipio').val(), 'bodegaCache' : bodegaCache},
		   beforeSend: function(){ $('#loaderAjax').fadeIn() },
		   success: function(data){
		   	$('#bodegas').select2('destroy');
		     	$('#bodegas').html(data);
		     	$('#bodegas').select2();
		   }
		}).always($('#loaderAjax').fadeOut());
		$('#divBodegas').css('display', '');
	} 
	else if (filtro == "2") {
		fechaDe(sessionStorage.getItem('fecha_de_trazabilidad'));
		$('#fecha_de').select2('val', sessionStorage.getItem('fecha_de_trazabilidad'));
		var coductorCache = sessionStorage.getItem('conductor_trazabilidad');
		$.ajax({
		   type: "POST",
		   url: "functions/fn_trazabilidad_obtener_conductores.php",
		   data : {"mestabla" : mesinicio, 'coductorCache' : coductorCache},
		   beforeSend: function(){ $('#loaderAjax').fadeIn() },
		   success: function(data){
		   	$('#conductor').select2('destroy');
		      $('#conductor').html(data);
		      $('#conductor').select2();
		   }
		}).always( $('#loaderAjax').fadeOut());
		$('#divConductores').css('display', '');
	} 
	else if (filtro == "3") {
		fechaDe(sessionStorage.getItem('fecha_de_trazabilidad'));
		$('#fecha_de').select2('val', sessionStorage.getItem('fecha_de_trazabilidad'));
		productoCache = sessionStorage.getItem('producto_trazabilidad');
		$.ajax({
		   type: "POST",
		   url: "functions/fn_trazabilidad_obtener_productos.php",
		   data : {"mestabla" : mesinicio, 'productoCache' : productoCache },
		   beforeSend: function(){ $('#loaderAjax').fadeIn() },
		   success: function(data){
		   	$('#producto').select2('destroy');
		     	$('#producto').html(data);
		     	$('#producto').select2();
		   }
		}).always($('#loaderAjax').fadeOut());
		$('#divProductos').css('display', '');
	} 
	else if (filtro == "4") {
		fechaDe(sessionStorage.getItem('fecha_de_trazabilidad'));
		$('#fecha_de').select2('val', sessionStorage.getItem('fecha_de_trazabilidad'));
		grupoEtarioCache = sessionStorage.getItem('grupoEtario_trazabilidad');
		$.ajax({
		   type: "POST",
		   url: "functions/fn_trazabilidad_obtener_grupo_etarios.php",
		   data : {"grupoEtarioCache" : grupoEtarioCache },
		   beforeSend: function(){ $('#loaderAjax').fadeIn() },
		   success: function(data){
		   	$('#grupo_etario').select2('destroy');
		      $('#grupo_etario').html(data);
		      $('#grupo_etario').select2();
		   }
		}).always($('#loaderAjax').fadeOut());
		$('#divGrupoEtario').css('display', '');
	} 
	else if (filtro == "5") {
		fechaDe(sessionStorage.getItem('fecha_de_trazabilidad'));
		$('#fecha_de').select2('val', sessionStorage.getItem('fecha_de_trazabilidad'));
		$('#divTipoComplemento').css('display', '');
	} 
	setTimeout(function() {			
	}, 1000);
}

function limpiarFormulario(){
	$( "#mes_exportar" ).select2('val', '');
	$( "#fecha_de" ).select2('val', '1');
	$("#dia_inicio").select2('val', '');
	$("#dia_fin").select2('val', '');
	$("#municipio").select2('val', '');
	$("#tipo_documento").select2('val', '');
	$("#proveedor").select2('val', '');
	$("#tipo_filtro").select2('val', '');
	$("#tipo_bodega").select2('val', '');
	$("#bodegas").select2('val', '');
	$("#conductor").select2('val', '');
	$('#totales').iCheck( "uncheck");
	$("#grupo_etario").select2('val', '');
	$("#producto").select2('val', '');
}
