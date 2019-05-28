var tablaFocalizados;
var tablaNoFocalizados;
var cantidadDiasFocalizados = 0;

$(document).ready(function(){
	buscarMunicipios();

	$('#municipio').change(function(){ buscarInstituciones($(this).val()); });
	$('#institucion').change(function(){ buscarSede($(this).val()); });
	$('#sede').change(function(){ buscarMeses($(this).val()); });
	$('#mes').change(function(){ buscarSemanas($(this).val()); });
	$('#semana').change(function(){ buscarComplementos($(this).val()); });

	$('#btnBuscar').click(function(){ buscarFocalizacion(); });
	$('.guaradarNovedad').click(function(){ guardarNovedad(); });

	$(document).on('ifChecked', '.checkbox-header', function () { $('.checkbox'+ $(this).data('columna')).iCheck('check'); });
	$(document).on('ifUnchecked', '.checkbox-header', function () { $('.checkbox'+ $(this).data('columna')).iCheck('uncheck'); });

	$(document).on('ifChecked', '.checkbox-header-2', function () { $('.checkbox-2'+ $(this).data('columna')).iCheck('check'); });
	$(document).on('ifUnchecked', '.checkbox-header-2', function () { $('.checkbox-2'+ $(this).data('columna')).iCheck('uncheck'); });

	$(document).on('ifChecked', '.checkbox1, .checkbox2, .checkbox3, .checkbox4, .checkbox5', function () { sumarCantidadDias($(this)); });
	$(document).on('ifUnchecked', '.checkbox1, .checkbox2, .checkbox3, .checkbox4, .checkbox5', function () { restarCantidadDias($(this)); });
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
});

function buscarMunicipios(){
	$.ajax({
		type: "POST",
		url: "functions/fn_buscar_municipios.php",
		contentType: false,
		processData: false,
		dataType: 'json',
		beforeSend: function(){ $('#loader').fadeIn(); },
		success: function(data){
			if(data.estado == 1){
				$('#municipio').html(data.opciones);
				$('#loader').fadeOut();
			} else {
				Command: toastr.error( data.mensaje, "Error al cargar los municipios.", { onHidden : function(){ $('#loader').fadeOut(); } } );
			}
		},
		error: function (data){
			console.log(data.responseText);
		}
	});
}

function buscarInstituciones(municipio){
	$.ajax({
		type: "POST",
		url: "functions/fn_buscar_instituciones.php",
		dataType: 'JSON',
    data: {
    	'municipio': municipio
    },
		beforeSend: function(){ $('#loader').fadeIn(); },
		success: function(data) {
			if(data.estado == 1){
				$('#institucion').html(data.opciones);
				$('#loader').fadeOut();
			} else {
				$('#institucion').html(data.opciones);
				Command: toastr.error( data.mensaje, "Error al cargar las instituciones.", { onHidden : function(){ $('#loader').fadeOut(); } } );
			}
		},
		error: function(data) {
			$('#loader').fadeOut();
		}
	});
}

function buscarSede(institucion){
	var formData = new FormData();
	formData.append('institucion', institucion);
	$.ajax({
		type: "POST",
		url: "functions/fn_buscar_sedes.php",
		contentType: false,
		processData: false,
    data: formData,
		dataType: 'json',
		beforeSend: function(){ $('#loader').fadeIn(); },
		success: function(data){
			if(data.estado == 1){
				$('#sede').html(data.opciones);
				$('#loader').fadeOut();
			} else {
				Command: toastr.error( data.mensaje, "Error al cargar las instituciones.", { onHidden : function(){ $('#loader').fadeOut(); } } );
			}
		}
	});
}

function buscarMeses(sede) {
	$.ajax({
		type: "POST",
		url: "functions/fn_buscar_meses.php",
		dataType: 'JSON',
    data: {
    	'sede': sede
    },
		beforeSend: function(){ $('#loader').fadeIn(); },
		success: function(data){
			if(data.estado == 1){
				$('#mes').html(data.opciones);
				$('#loader').fadeOut();
			} else {
				Command: toastr.error( data.mensaje, "Error al cargar las instituciones.", { onHidden : function(){ $('#loader').fadeOut(); } } );
			}
		},
		error: function (data) {
			$('#loader').fadeOut();
		}
	});
}

function buscarSemanas(mes){
	var formData = new FormData();
	formData.append('mes', mes);
	$.ajax({
		type: "POST",
		url: "functions/fn_buscar_semanas.php",
		contentType: false,
		processData: false,
    data: formData,
		dataType: 'json',
		beforeSend: function(){ $('#loader').fadeIn(); },
		success: function(data){
			if(data.estado == 1){
				$('#semana').html(data.opciones);
				$('#loader').fadeOut();
			} else {
				Command: toastr.error( data.mensaje, "Error al cargar las instituciones.", { onHidden : function(){ $('#loader').fadeOut(); } } );
			}
		}
	});
}

function buscarComplementos(semana){
	var formData = new FormData();
	formData.append('semana', semana);
	$.ajax({
		type: "POST",
		url: "functions/fn_buscar_complementos.php",
		contentType: false,
		processData: false,
    data: formData,
		dataType: 'json',
		beforeSend: function(){ $('#loader').fadeIn(); },
		success: function(data){
			if(data.estado == 1){
				$('#tipoComplemento').html(data.opciones);
				$('#loader').fadeOut();
			} else if(data.estado == 0){
				Command: toastr.warning( data.mensaje, "Error al cargar los complementos.", { onHidden : function(){ $('#loader').fadeOut(); } } );
			}else {
				Command: toastr.error( data.mensaje, "Error al cargar los complementos.", { onHidden : function(){ $('#loader').fadeOut(); } } );
			}
		}
	});
}

function buscarFocalizacion(){
	sumaDias = 0;
	$("#observaciones").prop('required',false);
	$("#foto").prop('required',false);
	if ($('#formNovedadesEjecucion').valid()) {
		$('#loader').fadeIn();

		if(tablaFocalizados !== undefined && $.fn.DataTable.isDataTable('.dataTablesNovedadesEjecucionFocalizados') ){
			tablaFocalizados.destroy();
		}
		if(tablaNoFocalizados !== undefined && $.fn.DataTable.isDataTable('.dataTablesNovedadesEjecucionReserva') ){
			tablaNoFocalizados.destroy();
		}

		tablaFocalizados = $('.dataTablesNovedadesEjecucionFocalizados').DataTable({
			destroy: true,
			ajax: {
				method: 'POST',
				url: 'functions/fn_novedades_ejecucion_buscar_datatables.php',
				data:{
					municipio: $('#municipio').val(),
					institucion: $('#institucion').val(),
					sede: $('#sede').val(),
					mes: $('#mes').val(),
					semana: $('#semana').val(),
					tipoComplemento: $('#tipoComplemento').val()
				},
				error: function(data) {
					console.log(data.responseText);
				}
			},
			columns:[
				{ data: 'Abreviatura'},
				{ data: 'num_doc'},
				{ data: 'nombre'},
				{ data: 'complemento'},
				{
					sortable: false,
					render: function ( data, type, full, meta ) {
						chequeado = (full.D1 == 1) ? 'checked' : '';
						var accion = '<div class="i-checks text-center"><label><input type="checkbox" class="checkbox1" name="'+ full.num_doc +'_D1" id="'+ full.num_doc +'_D1" value="1" '+ chequeado +' ></label></div>';
						return accion;
					}
				},
				{
					sortable: false,
					"render": function ( data, type, full, meta ) {
						chequeado = (full.D2 == 1) ? 'checked' : '';
						var accion = '<div class="i-checks text-center"><label><input type="checkbox" class="checkbox2" name="'+ full.num_doc +'_D2" id="'+ full.num_doc +'_D2" value="1" '+ chequeado +' ></label></div>';
						return accion;
					}
				},
				{
					sortable: false,
					"render": function ( data, type, full, meta ) {
						chequeado = (full.D3 == 1) ? 'checked' : '';
						var accion = '<div class="i-checks text-center"><label><input type="checkbox" class="checkbox3" name="'+ full.num_doc +'_D3" id="'+ full.num_doc +'_D3" value="1" '+ chequeado +' ></label></div>';
						return accion;
					}
				},
				{
					sortable: false,
					"render": function ( data, type, full, meta ) {
						chequeado = (full.D4 == 1) ? 'checked' : '';
						var accion = '<div class="i-checks text-center"><label><input type="checkbox" class="checkbox4" name="'+ full.num_doc +'_D4" id="'+ full.num_doc +'_D4" value="1" '+ chequeado +' ></label></div>';
						return accion;
					}
				},
				{
					sortable: false,
					"render": function ( data, type, full, meta ) {
						chequeado = (full.D5 == 1) ? 'checked' : '';
						var accion = '<div class="i-checks text-center"><label><input type="checkbox" class="checkbox5" name="'+ full.num_doc +'_D5" id="'+ full.num_doc +'_D5" value="1" '+ chequeado +' ></label></div> ';
						return accion;
					}
				}
			],
			oLanguage: {
				sLengthMenu: 'Mostrando _MENU_ registros',
				sZeroRecords: 'No se encontraron registros',
				sInfo: 'Mostrando _START_ a _END_ de _TOTAL_ registros ',
				sInfoEmpty: 'Mostrando 0 a 0 de 0 registros',
				sInfoFiltered: '(Filtrado desde _MAX_ registros)',
				sSearch:         'Buscar: ',
				oPaginate:{
					sFirst:    'Primero',
					sLast:     'Último',
					sNext:     'Siguiente',
					sPrevious: 'Anterior'
				}
			},
			pageLength: 10000,
			responsive: true,
			rowCallback: function( row, data ) {
    		sumaDias += parseInt(data.sumaDias);
	    }
		}).on("draw", function(){
			buscarSuplentes();

			cantidadDiasFocalizados = tablaFocalizados.data().count() * $('#semana option:selected').data("cantidaddias");
			cantidadDiasFocalizadosActual = sumaDias;
		});
	}
}

function buscarSuplentes(){
	tablaNoFocalizados = $('.dataTablesNovedadesEjecucionReserva').DataTable({
		destroy: true,
		ajax: {
			method: 'POST',
			url: 'functions/fn_novedades_ejecucion_no_focalizados_buscar_datatables.php',
			data:{
				mes: $('#mes').val(),
				sede: $('#sede').val(),
				semana: $('#semana').val(),
				municipio: $('#municipio').val(),
				institucion: $('#institucion').val(),
				tipoComplemento: $('#tipoComplemento').val()
			},
			error: function(data){
				console.log(data.responseText);
			}
		},
		columns:[
			{ data: 'abreviatura'},
			{ data: 'numero_documento'},
			{ data: 'nombre_suplente'},
			{ data: 'tipo_complemento'},
			{
				sortable: false,
				"render": function ( data, type, full, meta ) {
					chequeado = (full.D1 == 1) ? 'checked' : '';
					var accion = '<div class="i-checks text-center"><label><input type="checkbox" class="checkbox-21" name="'+ full.numero_documento +'_D1" id="'+ full.numero_documento +'_D1" value="1" '+ chequeado +' ></label></div>';
					return accion;
				}
			},
			{
				sortable: false,
				"render": function ( data, type, full, meta ) {
					chequeado = (full.D2 == 1) ? 'checked' : '';
					var accion = '<div class="i-checks text-center"><label><input type="checkbox" class="checkbox-22" name="'+ full.numero_documento +'_D2" id="'+ full.numero_documento +'_D2" value="1" '+ chequeado +' ></label></div>';
					return accion;
				}
			},
			{
				sortable: false,
				"render": function ( data, type, full, meta ) {
					chequeado = (full.D3 == 1) ? 'checked' : '';
					var accion = '<div class="i-checks text-center"><label><input type="checkbox" class="checkbox-23" name="'+ full.numero_documento +'_D3" id="'+ full.numero_documento +'_D3" value="1" '+ chequeado +' ></label></div>';
					return accion;
				}
			},
			{
				sortable: false,
				"render": function ( data, type, full, meta ) {
					chequeado = (full.D4 == 1) ? 'checked' : '';
					var accion = '<div class="i-checks text-center"><label><input type="checkbox" class="checkbox-24" name="'+ full.numero_documento +'_D4" id="'+ full.numero_documento +'_D4" value="1" '+ chequeado +' ></label></div>';
					return accion;
				}
			},
			{
				sortable: false,
				"render": function ( data, type, full, meta ) {
					chequeado = (full.D5 == 1) ? 'checked' : '';
					var accion = '<div class="i-checks text-center"><label><input type="checkbox" class="checkbox-25" name="'+ full.numero_documento +'_D5" id="'+ full.numero_documento +'_D5" value="1" '+ chequeado +' ></label></div> ';
					return accion;
				}
			}
		],
		oLanguage: {
			sLengthMenu: 'Mostrando _MENU_ registros',
			sZeroRecords: 'No se encontraron registros',
			sInfo: 'Mostrando _START_ a _END_ de _TOTAL_ registros ',
			sInfoEmpty: 'Mostrando 0 a 0 de 0 registros',
			sInfoFiltered: '(Filtrado desde _MAX_ registros)',
			sSearch:         'Buscar: ',
			oPaginate:{
				sFirst:    'Primero',
				sLast:     'Último',
				sNext:     'Siguiente',
				sPrevious: 'Anterior'
			}
		},
		pageLength: 10000,
		responsive: true,
	}).on("draw", function() {
		$('#loader').fadeOut();
		$('.i-checks').iCheck({ checkboxClass: 'icheckbox_square-green', radioClass: 'iradio_square-green', });
	});
}

function guardarNovedad(){
  if($('#formNovedadesEjecucion').valid()){
		var formData = new FormData($("#formNovedadesEjecucion")[0]);
		$.ajax({
			type: "POST",
			url: "functions/fn_guardar_novedad_ejecucion.php",
			contentType: false,
			processData: false,
	    data: formData,
			dataType: 'JSON',
			beforeSend: function() { $('#loader').fadeIn(); },
			success: function(data) {
				if(data.estado == 1) {
					Command: toastr.success( data.mensaje, "Se ha realizado el registro.", { onHidden : function(){ $('#loader').fadeOut(); location.href="index.php"; } } );
				} else {
					Command: toastr.error( data.mensaje, "No se ha realizado el registro.", { onHidden : function(){ $('#loader').fadeOut(); } } );
				}
			},
			error: function(data) {
				console.log(data.responseText);
	      Command: toastr.error(
	        "Al parecer existe un error con el servidor. Por favor comuníquese con el adminstrador del sitio InfoPAE.",
	        "Error al hacer el registro.",
	        { onHidden : function(){ $('#loader').fadeOut(); } }
	      );
	    }
		});
  }
}

function restarCantidadDias(checkbox){
	if (cantidadDiasFocalizadosActual > 0) {
		cantidadDiasFocalizadosActual -= 1;
	} else {
		checkbox.iCheck('destroy').prop('checked', true).iCheck({
			checkboxClass: 'icheckbox_square-green'
		});
	}
}

function sumarCantidadDias(checkbox){
	if (cantidadDiasFocalizadosActual < cantidadDiasFocalizados) {
		cantidadDiasFocalizadosActual += 1;
	} else {
		checkbox.iCheck('destroy').prop('checked', false).iCheck({
			checkboxClass: 'icheckbox_square-green'
		});
	}
}