$(document).ready(function(){
	dataset1 = $('#box-table-movimientos').DataTable({    
      	ajax: {
         	method: 'POST',
         	url: 'functions/fn_obtener_datos_tabla.php',
         	data:{
            	consulta: $('#consulta').val()
         	}
      	},
      	columns:[
         	{ className: "text-center", data: 'input'},
         	{ className: "text-center", data: 'Num_OCO'},
         	{ className: "text-center", data: 'FechaHora_Elab'},
         	{ className: "text-center", data: 'Semana'},
         	{ className: "text-center", data: 'Dias'},
         	{ className: "text-center", data: 'Menus'},
         	{ className: "text-center", data: 'rutaMunicipio'},
         	{ className: "text-center", data: 'Tipo_Complem'},
         	{ className: "text-center", data: 'descVariacion'},
         	{ className: "text-center", data: 'tipodespacho_nm'},
         	// { className: "text-center", data: 'bodegaId'},
         	{ className: "text-center", data: 'bodega'},
         	{ className: "text-center", data: 'estado'},
         	{ className: "text-center", data: 'Nitcc'},
         	{ className: "text-center", data: 'Nombrecomercial'},
      	],
      	pageLength: 25,
      	responsive: true,
      	dom : '<"html5buttons"> lr   <"containerBtn"> <"inputFiltro" f > tip',
      	buttons : [{extend:'excel', title:'Ordenes de compra', className:'btnExportarExcel', exportOptions: {columns : [0,1,2,3,4,5,6,7,8,9]}}],
      	order: [ 1, 'asc'],
      	oLanguage: {
         	sLengthMenu: 'Mostrando _MENU_ registros por página',
         	sZeroRecords: 'No se encontraron registros',
         	sInfo: 'Mostrando _START_ a _END_ de _TOTAL_ registros',
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
      	initComplete: function() {
      		$('tbody tr').click(function () {
      			if ($(this).find('input').val() != 'on' && $(this).find('input').val() != null && $(this).attr('estado') != 0 ) {
      				// despachoPorSede($(this).find('input').val())
      			}
      		})
      	},
      	preDrawCallback: function( settings ) {
         	$('#loader').fadeIn();
      	}
   	}).on("draw", function(){ 
   		$('.i-checks').iCheck({ checkboxClass: 'icheckbox_square-green' });
     	$('#loader').fadeOut();
   	})

	// Configuración del pligin toast
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

	$('.annoInicial').select2();
	$('.mesInicial').select2();
	$('.diaInicial').select2();
	$('.annoFinal').select2();
	$('.mesfText').select2();
	$('.diaFinal').select2();
	$('.semana').select2();
	$('.tipoComplemento').select2();
	$('.municipio').select2();
	$('.institucion').select2();
	$('.sede').select2();
	$('.alimento').select2();
	$('.ruta').select2();
	$('.imprimirMes').iCheck({checkboxClass: 'icheckbox_square-green ' });

	var mes = $('#mesi').val();
	$('#mesf').val(mes);
	$('#mesfText').select2('val', mes); 
	$('#mesfText').select2("enable", false);

		
		// var mesText = $("#mesi option[value='"+mes+"']").text()
		// $('#mesfText').val(mesText);
		
	$('.i-checks').iCheck({ checkboxClass: 'icheckbox_square-green' });

	$(document).on('ifChecked', '#seleccionarVarios', function () {
		$('tbody input[type=checkbox]').each(function(){
			if (!$(this).prop('disabled')) {
				$(this).iCheck('check');
			}
		});
	});
	$(document).on('ifUnchecked', '#seleccionarVarios', function () { $('tbody input[type=checkbox]').iCheck('uncheck'); });


	$('#diai').change(function(){
		diaInicial = parseInt($('#diai').val());
		diaFinal = parseInt($('#diaf').val());
		if (diaFinal < diaInicial ) { $('#diaf').select2('val',diaInicial); }
		$('#semana').select2('val', '');
		$('#tipo').select2('val','');
	})

	$('#diaf').change(function(){
		diaInicial = parseInt($('#diai').val());
		diaFinal = parseInt($('#diaf').val());
		if (diaFinal < diaInicial ) { $('#diaf').select2('val',diaInicial); }
	})

	$('#tipo').change(function(){
		$('#municipio').select2('val', '');
	})

	$('#municipio').change(function(){
		$('#institucion').select2('val', '');
		var tipo = $('#tipo').val();
		var municipio = $(this).val();
		buscar_institucion(municipio,tipo);
	});

	$('#institucion').change(function(){
		$('#sede').select2('val', '');
		var institucion = $(this).val();
		var municipio = $('#municipio').val();
		buscar_sede(municipio, institucion);
	});

	$( "#btnBuscar" ).click(function(){
		$('#loader').fadeIn();
		$("#pb_annoi").val($("#annoi").val());
		$("#pb_mesi").val($("#mesi").val());
		$("#pb_diai").val($("#diai").val());
		$("#pb_annof").val($("#annof").val());
		$("#pb_mesf").val($("#mesf").val());
		$("#pb_diaf").val($("#diaf").val());
		$('#pb_semana').val($("#semana").val());
		$("#pb_tipo").val($("#tipo").val());
		$("#pb_municipio").val($("#municipio").val());
		$("#pb_institucion").val($("#institucion").val());
		$("#pb_sede").val($("#sede").val());
		$("#pb_tipoDespacho").val($("#tipoDespacho").val());
		$("#pb_ruta").val($("#ruta").val());
		$("#pb_btnBuscar").val(1);
		$("#parametrosBusqueda").submit();
	});
});

function buscar_institucion(municipio,tipo){
	var datos = {"municipio":municipio,"tipo":tipo};
	$.ajax({
		type: "POST",
		url: "functions/fn_buscar_institucion.php",
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

function buscar_sede( municipio, institucion){
	var datos = { "municipio":municipio, "institucion":institucion};
	$.ajax({
		type: "POST",
		url: "functions/fn_buscar_sede.php",
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

function mesFinal(){
	var mes = $('#mesi').val();
	var mesText = $("#mesi option[value='"+mes+"']").text()
	$('#mesfText').val(mesText);
	$('#mesf').val(mes);
	cargar_semanas_mes(mes);
}

function cargar_semanas_mes(mes) {
	$.ajax({
		url: 'functions/fn_buscar_semanas.php',
		type: 'POST',
		dataType: 'HTML',
		data: {"mes": mes},
	})
	.done(function(data) { 
		if (data != '') {
			$("#semana").html(data);
		}
	})
	.fail(function(data) {
		console.log(data);
	});
}

function despachoPorSede(){
	var cant = 0;
	var bandera = 0;
	var ord = '';
	$("tbody input:checked").each(function(){
		estado_actual = $(this).attr('estado');
		Num_Oco = $(this).val();
		annoI = $('#annoi').val();
		mesI = $('#mesi').val();
		ord += $(this).val() + ',';
		cant ++;
	}); // Termina

	if(cant == 0){
		Command: toastr.warning('Debe seleccionar al menos una orden para formato individual', 'Advertencia');
		bandera++;
	}
	if(bandera == 0){
		$('#formDespachoPorSede #ordenCompra').val( ord );
		$('#formDespachoPorSede #MesI').val( mesI );
		$('#formDespachoPorSede #AnnoI').val( annoI );
		$('#formDespachoPorSede #imprimirMesI').val(0);
		$('#formDespachoPorSede').submit();
	}
}

function ordenesConsolidado(){
	var ordenes = [];
	$( "#box-table-movimientos tbody input:checked" ).each(function() {
		ordenes.push($( this ).val());
	});
	ordenes = ordenes.toString();
	var mesI = $('#mesi').val();
	var annoI = $('#annoi').val();
	var imprimirMes = 0;
	if (ordenes.length > 0) {
		if( $('#imprimirMes').prop('checked') ) {
			imprimirMes = 1;
		}else{
			imprimirMes = 0;
		}
		$('#imprimirMesIC').val(imprimirMes);	
		$('#AnnoIC').val(annoI);
		$('#MesIC').val(mesI);
		$('#ordenesCompra').val(ordenes)
		$('#formOrdenesConsolidado').submit();
	}else{
		Command: toastr.warning('Debe seleccionar al menos una orden para formato consolidado', 'Advertencia');
		bandera++;
	}
}

function editar_orden(){
	var cant = 0;
	var bandera = 0;
	$("tbody input:checked").each(function(){
		estado_actual = $(this).attr('estado');
		if(bandera == 0){
			cant++;
			if(2 != estado_actual){
				bandera++;
				Command: toastr.warning('Solo se pueden editar Ordenes en estado <strong>Pendiente.</strong>', 'Advertencia');
			}
			if(cant > 1){
				Command: toastr.warning('Debe seleccionar solo una Orden de compra para editar', 'Advertencia');
				bandera++;
			}
		}
		mesi = $('#mesi').val();
		Num_Oco = $(this).val();
	}); // Termina de revisar cada uno de los elementos que se encuentren checkeados.
	if(cant == 0){
		Command: toastr.warning('Debe seleccionar al menos una orden para editar', 'Advertencia');
		bandera++;
	}
	if(bandera == 0){
		$('#ordenCompraEditar #Num_oco').val( Num_Oco );
		$('#ordenCompraEditar #mesi').val( mesi );
		$('#ordenCompraEditar').submit();
	}
}

function eliminar_orden(){
	var cant = 0;
	var bandera = 0;
	$("tbody input:checked").each(function(){
		estado_actual = $(this).attr('estado');
		if(bandera == 0){
			cant++;
			despacho = $(this).val();
			if(cant > 1){
				Command: toastr.warning('Debe seleccionar solo una Orden de compra para eliminar', 'Advertencia');
				bandera++;
				// return false;
			}
		}
		estadoEliminar = estado_actual;
		annoEliminar = $('#annoi').val();
		mesEliminar = $('#mesi').val();
		Num_OCO = $(this).val();
	}); // Termina de revisar cada uno de los elementos que se encuentren checkeados.

	if(cant == 0){
		Command: toastr.warning('Debe seleccionar solo una Orden de compra para eliminar', 'Advertencia');
		bandera++;
	}

	if(bandera == 0){
		$('#mes_eliminar').val(mesEliminar);
		$('#num_oco_eliminar').val(Num_OCO);
		$('#anno_eliminar').val(annoEliminar);
		$('#estado_eliminar').val(estadoEliminar);
		$('#ventanaConfirmar').modal();

	}// Termina el if si la bandera esta en cero
}// Termina la función para eliminar despachos.

function deleteOrden(){
	var anno_eliminar = $('#anno_eliminar').val();
	var mes_eliminar = $('#mes_eliminar').val();
	var num_oco_eliminar = $('#num_oco_eliminar').val();
	var estado_eliminar = $('#estado_eliminar').val();
	// Se va agregar el año y el mes para hacer la eliminación en la tabla correspondiente
	var mesi = mes_eliminar
	// Se va a envíar la variable despacho para iniciar el procesos de eliminación.
	var datos = {	"despacho":num_oco_eliminar,
					"annoi":anno_eliminar,
					"estado":estado_eliminar,
					"mesi":mesi};
	$.ajax({
		type: "POST",
		url: "functions/fn_eliminar.php",
		data: datos,
		beforeSend: function(){
			$('#loader').fadeIn();
		},
		success: function(data){
			$('#debug').html(data);
			if(data == 1){
				Command : toastr.success( "Se ha eliminado con éxito la Orden.", "Éxito", { 
										onHidden : function(){ 
											$('#loader').fadeOut();
											location.reload(); 
										}
							});
			}
		}
	})
	.done(function(){ })
	.fail(function(){ })
	.always(function(){
		$('#loader').fadeOut();
	});
}

/*******************************recibir orden**********************************/
function recibir_orden(){
	var cant = 0;
	var bandera = 0;
	$("tbody input:checked").each(function(){
		estado_actual = $(this).attr('estado');
		if (estado_actual == 1) {
			Command: toastr.warning('esta orden ya se encuentra,<strong> recibida </strong>', 'Advertencia');
			bandera++;
			cant++;
		}
		if(bandera == 0){
			cant++;
			despacho = $(this).val();
			if(cant > 1){
				Command: toastr.warning(' Debe seleccionar solo una Orden de compra para recibir', 'Advertencia');
				bandera++;
			}
		}
		Num_OCO = $(this).val();
		bodega = $(this).attr('bodega');
		complemento = $(this).attr('complemento');
		nameWarehouse = $(this).attr('nameWarehouse');
		mes = $('#mesi').val();
	}); // Termina de revisar cada uno de los elementos que se encuentren checkeados.

	if(cant == 0){
		Command: toastr.warning('Debe seleccionar solo una Orden de compra para recibir', 'Advertencia');
		bandera++;
	}
	datos = { 	'Num_OCO' : Num_OCO, 
				'bodega' : bodega, 
				'complemento' : complemento,
				'nameWarehouse' : nameWarehouse,
				'mes' : mes
			}
	if(bandera == 0){
		$.ajax({
			type: "POST",
			url: "functions/fn_insert_inventory.php",
			data: datos,
			beforeSend: function(){
				$('#loader').fadeIn();
			},
			success: function(data){
				$('#debug').html(data);
				if(data == 1){
					Command : toastr.success( "Se ha recibido con éxito la Orden.", "Éxito", { 
											onHidden : function(){ 
												$('#loader').fadeOut();
												location.reload(); 
											}
								});
				}
				else{
					Command : toastr.error( data, "Error", { 
						onHidden : function(){ 
							$('#loader').fadeOut();
							// location.reload(); 
						}
			});
				}
			}
		})
		.done(function(){ })
		.fail(function(){ })
		.always(function(){
			$('#loader').fadeOut();
		});
		

	}// Termina el if si la bandera esta en cero
}// Termina la función para eliminar despachos.




