var tablaFocalizados;
var tablaNoFocalizados;

$(document).ready(function(){
	buscarMunicipios();


	$('#btnBuscar').click(function(){
		buscarFocalizacion();
	});


	$('.guaradarNovedad').click(function(){
		guardarNovedad();
	});

	$('#municipio').change(function(){
		var municipio = $(this).val();
		buscarInstituciones();
		$('#sede').html('<option value = "">Seleccione una</option>');
	});
	$('#institucion').change(function(){
		var institucion = $(this).val();
		buscarSede();
	});
	$('#sede').change(function(){
		var sede = $(this).val();
		buscarMeses();
	});
	$('#mes').change(function(){
		buscarSemanas();
	});
	$('#semana').change(function(){
		buscarComplementos();
	});


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
		}
	});
}
function buscarInstituciones(){
	var formData = new FormData();
	formData.append('municipio', $('#municipio').val());
	$.ajax({
		type: "POST",
		url: "functions/fn_buscar_instituciones.php",
		contentType: false,
		processData: false,
    data: formData,
		dataType: 'json',
		beforeSend: function(){ $('#loader').fadeIn(); },
		success: function(data){
			if(data.estado == 1){
				$('#institucion').html(data.opciones);
				$('#loader').fadeOut();
			} else {
				Command: toastr.error( data.mensaje, "Error al cargar las instituciones.", { onHidden : function(){ $('#loader').fadeOut(); } } );
			}
		}
	});
}
function buscarSede(){
	var formData = new FormData();
	formData.append('institucion', $('#institucion').val());
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
function buscarMeses(){
	var formData = new FormData();
	formData.append('sede', $('#sede').val());
	$.ajax({
		type: "POST",
		url: "functions/fn_buscar_meses.php",
		contentType: false,
		processData: false,
    data: formData,
		dataType: 'json',
		beforeSend: function(){ $('#loader').fadeIn(); },
		success: function(data){
			if(data.estado == 1){
				$('#mes').html(data.opciones);
				$('#loader').fadeOut();
			} else {
				Command: toastr.error( data.mensaje, "Error al cargar las instituciones.", { onHidden : function(){ $('#loader').fadeOut(); } } );
			}
		}
	});
}
function buscarSemanas(){
	var formData = new FormData();
	formData.append('mes', $('#mes').val());
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
function buscarComplementos(){
	var formData = new FormData();
	formData.append('semana', $('#semana').val());
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
	console.log('Bucar focalización');
	$("#observaciones").prop('required',false);
	$("#foto").prop('required',false);
	if($('#formNovedadesEjecucion').valid()){
		if(tablaFocalizados !== undefined && $.fn.DataTable.isDataTable('.dataTablesNovedadesEjecucionFocalizados') ){
			tablaFocalizados.destroy();
		}
		if(tablaNoFocalizados !== undefined && $.fn.DataTable.isDataTable('.dataTablesNovedadesEjecucionReserva') ){
			tablaNoFocalizados.destroy();
		}
		tablaFocalizados = $('.dataTablesNovedadesEjecucionFocalizados').DataTable({
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
				}
			},
			columns:[
				{ data: 'Abreviatura'},
				{ data: 'num_doc'},
				{ data: 'nombre'},
				{ data: 'complemento'},
				{
					sortable: false,
					"render": function ( data, type, full, meta ) {
						var numDoc = full.num_doc
						var D1 = full.D1;
						var accion = ' <div class="i-checks"><label> <input type="checkbox" name="'+numDoc+'_D1" id="'+numDoc+'_D1" value="1" ';
						if(D1 == 1){
							accion = accion + ' checked ';
						}
						accion = accion + ' > <i></i></label></div> ';
						return accion;
					}
				},
				{
					sortable: false,
					"render": function ( data, type, full, meta ) {
						var numDoc = full.num_doc
						var D2 = full.D2;
						var accion = ' <div class="i-checks"><label> <input type="checkbox" name="'+numDoc+'_D2" id="'+numDoc+'_D2" value="1" ';
						if(D2 == 1){
							accion = accion + ' checked ';
						}
						accion = accion + ' > <i></i></label></div> ';
						return accion;
					}
				},
				{
					sortable: false,
					"render": function ( data, type, full, meta ) {
						var numDoc = full.num_doc
						var D3 = full.D3;
						var accion = ' <div class="i-checks"><label> <input type="checkbox" name="'+numDoc+'_D3" id="'+numDoc+'_D3" value="1" ';
						if(D3 == 1){
							accion = accion + ' checked ';
						}
						accion = accion + ' > <i></i></label></div> ';
						return accion;
					}
				},
				{
					sortable: false,
					"render": function ( data, type, full, meta ) {
						var numDoc = full.num_doc
						var D4 = full.D4;
						var accion = ' <div class="i-checks"><label> <input type="checkbox" name="'+numDoc+'_D4" id="'+numDoc+'_D4" value="1" ';
						if(D4 == 1){
							accion = accion + ' checked ';
						}
						accion = accion + ' > <i></i></label></div> ';
						return accion;
					}
				},
				{
					sortable: false,
					"render": function ( data, type, full, meta ) {
						var numDoc = full.num_doc
						var D5 = full.D5;
						var accion = ' <div class="i-checks"><label> <input type="checkbox" name="'+numDoc+'_D5" id="'+numDoc+'_D5" value="1" ';
						if(D5 == 1){
							accion = accion + ' checked ';
						}
						accion = accion + ' > <i></i></label></div> ';
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
			"preDrawCallback": function( settings ) {
				$('#loader').fadeIn();
			}
		}).on("draw", function(){ $('#loader').fadeOut(); $('.i-checks').iCheck({ checkboxClass: 'icheckbox_square-green', radioClass: 'iradio_square-green', }); });
		tablaNoFocalizados = $('.dataTablesNovedadesEjecucionReserva').DataTable({
			ajax: {
				method: 'POST',
				url: 'functions/fn_novedades_ejecucion_no_focalizados_buscar_datatables.php',
				data:{
					municipio: $('#municipio').val(),
					institucion: $('#institucion').val(),
					sede: $('#sede').val(),
					mes: $('#mes').val(),
					semana: $('#semana').val(),
					tipoComplemento: $('#tipoComplemento').val()
				}
			},
			columns:[
				{ data: 'Abreviatura'},
				{ data: 'num_doc'},
				{ data: 'nombre'},
				{ data: 'complemento'},
				{
					sortable: false,
					"render": function ( data, type, full, meta ) {
						var numDoc = full.num_doc
						var D1 = full.D1;
						var accion = ' <div class="i-checks"><label> <input type="checkbox" name="'+numDoc+'_D1" id="'+numDoc+'_D1" value="1" ';
						if(D1 == 1){
							accion = accion + ' checked ';
						}
						accion = accion + ' > <i></i></label></div> ';
						return accion;
					}
				},
				{
					sortable: false,
					"render": function ( data, type, full, meta ) {
						var numDoc = full.num_doc
						var D2 = full.D2;
						var accion = ' <div class="i-checks"><label> <input type="checkbox" name="'+numDoc+'_D2" id="'+numDoc+'_D2" value="1" ';
						if(D2 == 1){
							accion = accion + ' checked ';
						}
						accion = accion + ' > <i></i></label></div> ';
						return accion;
					}
				},
				{
					sortable: false,
					"render": function ( data, type, full, meta ) {
						var numDoc = full.num_doc
						var D3 = full.D3;
						var accion = ' <div class="i-checks"><label> <input type="checkbox" name="'+numDoc+'_D3" id="'+numDoc+'_D3" value="1" ';
						if(D3 == 1){
							accion = accion + ' checked ';
						}
						accion = accion + ' > <i></i></label></div> ';
						return accion;
					}
				},
				{
					sortable: false,
					"render": function ( data, type, full, meta ) {
						var numDoc = full.num_doc
						var D4 = full.D4;
						var accion = ' <div class="i-checks"><label> <input type="checkbox" name="'+numDoc+'_D4" id="'+numDoc+'_D4" value="1" ';
						if(D4 == 1){
							accion = accion + ' checked ';
						}
						accion = accion + ' > <i></i></label></div> ';
						return accion;
					}
				},
				{
					sortable: false,
					"render": function ( data, type, full, meta ) {
						var numDoc = full.num_doc
						var D5 = full.D5;
						var accion = ' <div class="i-checks"><label> <input type="checkbox" name="'+numDoc+'_D5" id="'+numDoc+'_D5" value="1" ';
						if(D5 == 1){
							accion = accion + ' checked ';
						}
						accion = accion + ' > <i></i></label></div> ';
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
			"preDrawCallback": function( settings ) {
				$('#loader').fadeIn();
			}
		}).on("draw", function(){ $('#loader').fadeOut(); $('.i-checks').iCheck({ checkboxClass: 'icheckbox_square-green', radioClass: 'iradio_square-green', }); });
	}
}

function guardarNovedad(){
	console.log('Guardar Novedad');
	// $("#observaciones").prop('required',true);
	// $("#foto").prop('required',true);
  if($('#formNovedadesEjecucion').valid()){
		var formData = new FormData($("#formNovedadesEjecucion")[0]);
		$.ajax({
			type: "POST",
			url: "functions/fn_guardar_novedad_ejecucion.php",
			contentType: false,
			processData: false,
	    data: formData,
			dataType: 'json',
			beforeSend: function(){ $('#loader').fadeIn(); },
			success: function(data){
				if(data.estado == 1){
					//$('#semana').html(data.opciones);
					//$('#loader').fadeOut();
					Command: toastr.success( data.mensaje, "Se ha realizado el registro.", { onHidden : function(){ $('#loader').fadeOut(); location.href="index.php"; } } );
				} else {
					Command: toastr.error( data.mensaje, "Error al cargar las instituciones.", { onHidden : function(){ $('#loader').fadeOut(); } } );
				}
			},
			error: function(data){console.log(data);
	      Command: toastr.error(
	        "Al parecer existe un error con el servidor. Por favor comuníquese con el adminstrador del sitio InfoPAE.",
	        "Error al hacer el registro.",
	        { onHidden : function(){ $('#loader').fadeOut(); } }
	      );
	    }
		});
  }
}
