$(document).ready(function(){
	jQuery.extend(jQuery.validator.messages, { 	required: "Campo obligatorio.", 
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
																min: jQuery.validator.format("Por favor, escribe un valor mayor o igual a {0}.") });

	// $('#box-table-movimientos').DataTable();
	$('select').select2();
	var mes = $('#mes').val();
	buscar_semanas(mes);

	$('#mes').change(function(){
		var mes = $(this).val();
		$('#semana').select2('val', '');
		buscar_semanas(mes);
	});

	$('#municipio').change(function(){
		var municipio = $(this).val();
		$('#institucion').select2('val', '');
		$('#ruta').select2('val','');
		if ($('#municipio').val() != '') {
			$('#ruta').attr('disabled', true);
		}else{
			$('#ruta').attr('disabled', false);
		}
		buscar_institucion(municipio);
	})

	$('#ruta').change(function(){
		if ($('#ruta').val() != '') {
			$('#municipio').attr('disabled', true);
			$('#institucion').attr('disabled', true);
			$('#sede').attr('disabled', true);
		}else{
			$('#municipio').attr('disabled', false);
			$('#institucion').attr('disabled', false);
			$('#sede').attr('disabled', false);
		}
	})

	$('#institucion').change(function(){
		var institucion = $(this).val();
		$('#sede').select2('val', '');
		if ($('#institucion').val() != '') {
			buscar_sede(institucion);
		}
	})

	$('#btnBuscar').click(function(event){ 
		event.preventDefault();
		consultarInforme() 
	})
})

function buscar_semanas(mes){
	var datos = { "mes":mes };
	$.ajax({
		type : "post",
		url : "functions/fn_buscar_semanas.php",
		data : datos,
		beforeSend : function(){ $('#loader').fadeIn() },
	}).done(function(data){
		$('#semana').html(data);
	}).fail(function(data){
		console.log(data);
	}).always(function(){
		$('#loader').fadeOut();
	})
}

function buscar_institucion(municipio){
	var datos = { "municipio":municipio };
	$.ajax({
		type : "post",
		url : "functions/fn_buscar_instituciones.php",
		data : datos,
		beforeSend : function(){ $('#loader').fadeIn() },
	}).done(function(data){
		$('#institucion').html(data);
	}).fail(function(data){
		console.log(data);
	}).always(function(){
		$('#loader').fadeOut();
	})
}

function buscar_sede(institucion){
	var datos = { "institucion":institucion };
	$.ajax({
		type : "post",
		url : "functions/fn_buscar_sedes.php",
		data : datos,
		beforeSend : function(){ $('#loader').fadeIn() },
	}).done(function(data){
		$('#sede').html(data);
	}).fail(function(data){
		console.log(data);
	}).always(function(){
		$('#loader').fadeOut();
	})
}

function consultarInforme(){
	if($('#formInejecuciones').valid()){
		datos = $('#formInejecuciones').serialize(),
		$.ajax({
			type : "post",
			url : "functions/fn_buscar_datatables.php",
			data : datos,
			beforeSend : function(){ $('#loader').fadeIn() },
		}).done(function(data){
			if( $.fn.DataTable.isDataTable( '#box-table-movimientos' )){
				dataset1.destroy();
			}
			$('.rowTable').css('display', 'block');
			data = JSON.parse(data);
			$('#tHead').html(data['thead']);
			$('#tBody').html(data['tbody']);
			$('#tFoot').html(data['tfoot']);

			dataset1 = $('#box-table-movimientos').DataTable({
				order : [[ 0, 'asc' ], [ 3, 'asc' ], [ 5, 'asc' ]],
				pageLength: 25,
				responsive: true,
				dom : 'lr<"containerBtn"><"inputFiltro"f>tip<"clear"><"html5buttons"B>',
				buttons : [ {extend: 'excel', title: 'Inejecuciones', className: 'btnExportarExcel'}],
				oLanguage: {
					sLengthMenu: 'Mostrando _MENU_ registros por página',
					sZeroRecords: 'No se encontraron registros',
					sInfoEmpty: 'Mostrando 0 a 0 de 0 registros',
					sInfoFiltered: '(Filtrado desde _MAX_ registros)',
					sSearch:         'Buscar: ',
					sInfo: "Mostrando _START_ a _END_ de _TOTAL_ registros",
					oPaginate:{
					   sFirst:    'Primero',
					   sLast:     'Último',
					   sNext:     'Siguiente',
					   sPrevious: 'Anterior'
					}
				}
			});
			
			var botonAcciones = '<div class="dropdown pull-right">'+
                      				'<button class="btn btn-primary btn-sm btn-outline" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true">'+
                        				'Acciones <span class="caret"></span>'+
                      				'</button>'+
                      				'<ul class="dropdown-menu pull-right" aria-labelledby="dropdownMenu1">'+
                        				'<li><a tabindex="0" aria-controls="box-table" href="#" onclick="$(\'.btnExportarExcel\').click();"><i class="fa fa-file-excel-o"></i> Exportar </a></li>'+
                      				'</ul>'+
                    				'</div>';
  			$('.containerBtn').html(botonAcciones);	 


		}).fail(function(data){
			console.log(data);
		}).always(function(){
			$('#loader').fadeOut();
		})
	}
}
