$(document).ready(function(){
	// $('select').select2();
	$('.select2').select2({ width: "resolve" });
	$('.i_checks').iCheck({     checkboxClass: 'icheckbox_square',
    radioClass: "iradio_square-green" });
	var municipioRector = $('#municipio').val();
	if (municipioRector != "" && municipioRector != "0") {
		var tipo = $('#tipoRacion').val();
		var municipio = municipioRector;
		// buscar_institucion(municipio,tipo);
		var institucionRector = $('#institucion').val();
		if (institucionRector != "" && institucionRector != "0") {
			var tipo = $('#tipoRacion').val();
			var municipio = $('#municipio').val();
			var semana = $('#semana').val();
			// buscar_sede(semana,municipio,tipo,institucionRector);
		}
	}

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

	var cantidadDetallados = 0;
	var mes = $('#mesi').val(); 
	var mesText = $("#mesi option[value='"+mes+"']").text()

	$('#mesf').val(mes);
	$('#mesfText').select2('val', mes); 
	$('#mesfText').select2("enable", false);
	// console.log($('#mesf').val())
	$('.i-checks').iCheck({ checkboxClass: 'icheckbox_square-green' });
	$(document).on('ifChecked', '#seleccionarVarios', function () {
		$('tbody input[type=checkbox]').each(function(){
			if (!$(this).prop('disabled')) {
				$(this).iCheck('check');
			}
		});
	});
	$(document).on('ifUnchecked', '#seleccionarVarios', function () { $('tbody input[type=checkbox]').iCheck('uncheck'); });

	$('#municipio').change(function(){
		$('#institucion').select2('val','');
		var tipo = $('#tipoRacion').val();
		var municipio = $(this).val();
		buscar_institucion(municipio,tipo);
	});

	$('#institucion').change(function(){
		$('#sede').select2('val', '');
		var institucion = $(this).val();
		var tipo = $('#tipoRacion').val();
		var municipio = $('#municipio').val();
		var semana = $('#semana').val();
		buscar_sede(semana,municipio,tipo,institucion);
	});

	$( "#btnBuscar" ).click(function(){
		$('#pb_entrega').val($('#numeroEntrega').val());
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

	dataset1 = $('#box-table-movimientos').DataTable({
		order: [[ 8, 'asc' ], [ 10, 'asc' ], [ 11, 'asc' ]],
		dom: 'lr<"containerBtn"><"inputFiltro"f>tip',
		pageLength: 25,
		lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "TODO"]],
		responsive: true,
		// aoColumnDefs: [{ "bVisible": false, "aTargets": [9] }],
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
		}
	});

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
});

function buscar_institucion(municipio,tipo){
	var datos = {"municipio":municipio,"tipo":tipo};
	$.ajax({
		type: "POST",
		url: "functions/fn_despacho_buscar_institucion.php",
		data: datos,
		beforeSend: function(){
			$('#loader').fadeIn();
		},
		success: function(data){
			$('#institucion').html(data);
			var respuestaInstitucion = data;
		}
	})
	.done(function(){ })
	.fail(function(){ })
	.always(function(){ $('#loader').fadeOut(); });
}

function buscar_sede(semana, municipio, tipo, institucion){
	var datos = {"semana":semana, "municipio":municipio, "tipo":tipo, "institucion":institucion};
	$.ajax({
		type: "POST",
		url: "functions/fn_despacho_buscar_sede.php",
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
	.always(function(){ $('#loader').fadeOut(); });
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
		url: 'functions/fn_despacho_buscar_semanas.php',
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

// carga la vista para ingresar lotes y fechas de vencimiento 
function despachos_por_sede_fecha_lote(){
	var cant = 0;
	var despacho = 0;
	var semana = 0;
	var tipo = '';
	var bandera = 0;
	$("tbody input:checked").each(function(){
		if(bandera == 0){
			cant++;
			if(2 != $(this).attr('estado')){
				bandera++;
				Command: toastr.warning('Solo se pueden agregar lotes y fechas de vencimiento a despachos en estado <strong>Pendiente.</strong>', 'Advertencia');
			}
			if(cant > 1){
				bandera++;
				Command: toastr.warning('Debe seleccionar solo un despacho para agregar lotes y fechas de vencimiento', 'Advertencia');
			}
		}
	}); 
	$("tbody input:checked").each(function(){
		if(bandera == 0){
			cant++;
			if(bandera == 0){
				if(semana == 0){
					semana = $(this).attr('semana');
				}
				else{
					if(semana != $(this).attr('semana')){
						bandera++;
						Command: toastr.warning('Los despachos seleccionados deben ser de la misma <strong>semana</strong>', 'Advertencia');
					}
				}
			}
			if(bandera == 0){
				if(tipo == ''){
					tipo = $(this).attr('complemento');
				}
				else{
					if(tipo != $(this).attr('complemento')){
						bandera++;
						Command: toastr.warning('Los despachos seleccionados deben ser del mismo tipo de ración', 'Advertencia');
					}
				}
			}
		}
	});
	if(cant == 0){
		Command: toastr.warning('Debe seleccionar al menos un despacho para continuar', 'Advertencia');
		bandera++;
	}
	if(bandera == 0){
		$('#formDespachos').attr('action', 'despacho_por_sede_fecha_lote.php');
		$('#formDespachos').attr('method', 'post');
		$('#formDespachos').submit();
		$('#formDespachos').attr('method', 'get');
	}
}

/************************************************ FORMATOS ********************************************************/
// formato kardex normal
function despachos_kardex(){
	var cant = 0;
	var despacho = 0;
	var semana = 0;
	var tipo = '';
	var bandera = 0;
	$("tbody input:checked").each(function(){
		if(bandera == 0){
			cant++;
			if(bandera == 0){
				if(semana == 0){
					semana = $(this).attr('semana');
				}
				else{
					if(semana != $(this).attr('semana')){
						bandera++;
						Command: toastr.warning('Los despachos seleccionados deben ser de la misma <strong>semana</strong>', 'Advertencia');
					}
				}
			}
			if(bandera == 0){
				if(tipo == ''){
					tipo =$(this).attr('complemento');
				}
				else{
					if(tipo != $(this).attr('complemento')){
						bandera++;
						Command: toastr.warning('Los despachos seleccionados deben ser del mismo <strong>tipo de ración</strong>', 'Advertencia');
					}
				}
			}
		}
	});
	if(cant == 0){
		Command: toastr.warning('Debe seleccionar al menos un despacho para continuar', 'Advertencia');
		bandera++;
	}
	if(bandera == 0){
		$('#formDespachos').attr('action', 'despacho_kardex3.php');
		$('#formDespachos').attr('method', 'post');
		$('#formDespachos').submit();
		$('#formDespachos').attr('method', 'get');
	}
}

// formato kardex multiple
function despachos_kardex_multiple(){
	var cant = 0;
	var despacho = 0;
	var semana = 0;
	var tipo = '';
	var bandera = 0;
	$("tbody input:checked").each(function(){
		if(bandera == 0){
			cant++;
			if(bandera == 0){
				if(semana == 0){
					semana = $(this).attr('semana');
				}
				else{
					if(semana != $(this).attr('semana')){
						bandera++;
						Command: toastr.warning('Los despachos seleccionados deben ser de la misma <strong>semana</strong>', 'Advertencia');
					}
				}
			}
			if(bandera == 0){
				if(tipo == ''){
					tipo = $(this).attr('complemento');
				}
				else{
					if(tipo != $(this).attr('complemento')){
						bandera++;
						Command: toastr.warning('Los despachos seleccionados deben ser del mismo <strong>tipo de ración</strong>', 'Advertencia');
					}
				}
			}
		}
	});
	if(cant == 0){
		Command: toastr.warning('Debe seleccionar al menos un despacho para continuar', 'Advertencia');
		bandera++;
	}
	if(bandera == 0){
		$('#formDespachos').attr('action', 'despacho_kardex4_multiple.php');
		$('#formDespachos').attr('method', 'post');
		$('#formDespachos').submit();
		$('#formDespachos').attr('method', 'get');
	}
}

// formato individual cuando se envia por menu desplegable
function despachos_por_sede(){
	var cant = 0;
	var despacho = 0;
	var semana = 0;
	var tipo = '';
	var bandera = 0;
	$("tbody input:checked").each(function(){
		if(bandera == 0){
			cant++;
			if(bandera == 0){
				if(semana == 0){
					semana = $(this).attr('semana');
				}
				else{
					// if(semana != $(this).attr('semana')){
					// 	bandera++;
					// 	Command: toastr.warning('Los despachos seleccionados deben ser de la misma <strong>semana</strong>', 'Advertencia');
					// }
				}
			}
		}
	});
	if(cant == 0){
		Command: toastr.warning('Debe seleccionar al menos un despacho para continuar', 'Advertencia');
		bandera++;
	}
	if(bandera == 0){
		$('#formDespachos').attr('action', 'despacho_por_sede.php');
		$('#formDespachos').attr('method', 'post');
		$('#formDespachos').submit();
		$('#formDespachos').attr('method', 'get');
	}
}

// formato individual cuando se da click en la tabla
function despachoPorSede(despacho){
	estado = $('#'+despacho).attr('estado');
	if(estado != 0){
		$( ".soloJs" ).remove();
		var mesI = $('#mesi').val();
		var annoI = $('#annoi').val();
		var paginasObservacionesI = $('#paginasObservaciones').val();
		$('#despachoAnnoI').val(annoI);
		$('#despachoMesI').val(mesI);
		$('#paginasObservacionesI').val(paginasObservacionesI);
		$('#despacho').val(despacho);
		$('#formDespachoPorSede').submit();
	}
}

// consolidado normal
function despachos_consolidado(){
	var cant = 0;
	var complementoActual = '';
	var semana = 0;
	var bandera = 0;
	var despacho = 0;
	$('tbody input:checked').each(function(){
		if(bandera == 0){
			cant++;
			var complemento =  $(this).attr("complemento");
			if(bandera == 0){
				if(complementoActual == ''){
					complementoActual = complemento;
				}
				else{
					if(complementoActual != complemento){
						bandera++;
						Command: toastr.warning('Los despachos seleccionados deben ser del mismo <strong>tipo de ración</strong>', 'Advertencia');
					}
				}
			}
		}
	});
	if(cant == 0){
		Command: toastr.warning('Debe seleccionar al menos un despacho para continuar', 'Advertencia');
		bandera++;
	}
	if(bandera == 0){
		var rutaSeleccionada = $('#ruta option:selected').text();
		$('#rutaNm').val(rutaSeleccionada);
		$('#formDespachos').attr('action', 'despacho_consolidado.php');
		$('#formDespachos').attr('method', 'post');
		$('#formDespachos').submit();
		$('#formDespachos').attr('method', 'get');
	}
}


// consolidado 2
function despachos_consolidado2(){
	var cant = 0;
	var complementoActual = '';
	var semana = 0;
	var bandera = 0;
	var despacho = 0;
	$('tbody input:checked').each(function(){
		if(bandera == 0){
			cant++;
			var complemento =  $(this).attr("complemento");
			if(bandera == 0){
				if(complementoActual == ''){
					complementoActual = complemento;
				}
				else{
					if(complementoActual != complemento){
						bandera++;
						Command: toastr.warning('Los despachos seleccionados deben ser del mismo <strong>tipo de ración</strong>', 'Advertencia');
					}
				}
			}
		}
	});
	if(cant == 0){
		Command: toastr.warning('Debe seleccionar al menos un despacho para continuar', 'Advertencia');
		bandera++;
	}
	if(bandera == 0){
		var rutaSeleccionada = $('#ruta option:selected').text();
		$('#rutaNm').val(rutaSeleccionada);
		$('#formDespachos').attr('action', 'despacho_consolidado2.php');
		$('#formDespachos').attr('method', 'post');
		$('#formDespachos').submit();
		$('#formDespachos').attr('method', 'get');
	}
}

// formatos covid rpc
function covid19_despachos_consolidado(){
	var cant = 0;
	var complementoActual = '';
	var semana = 0;
	var bandera = 0;
	var despacho = 0;
	$('tbody input:checked').each(function(){
		if(bandera == 0){
			cant++;
			var complemento =  $(this).attr("complemento");
			if(bandera == 0){
				if(complementoActual == ''){
					complementoActual = complemento;
				}
				else{
					if(complementoActual != complemento){
						bandera++;
						Command: toastr.warning('Los despachos seleccionados deben ser del mismo <strong>tipo de ración</strong>', 'Advertencia');
					}
				}
			}
		}
	});
	if(cant == 0){
		Command: toastr.warning('Debe seleccionar al menos un despacho para continuar', 'Advertencia');
		bandera++;
	}
	if(bandera == 0){
		var rutaSeleccionada = $('#ruta option:selected').text();
		$('#rutaNm').val(rutaSeleccionada);
		$('#formDespachos').attr('action', 'canasta_despacho_consolidado.php');
		$('#formDespachos').attr('method', 'post');
		$('#formDespachos').submit();
		$('#formDespachos').attr('method', 'get');
	}
}

// formatos covid industrializados
function covid19_despachos_consolidado_ri(){
	var cant = 0;
	var complementoActual = '';
	var semana = 0;
	var bandera = 0;
	var despacho = 0;
	$('tbody input:checked').each(function(){
		if(bandera == 0){
			cant++;
			var complemento =  $(this).attr("complemento");	
			if(bandera == 0){
				if(complementoActual == ''){
					complementoActual = complemento;
				}
				else{
					if(complementoActual != complemento){
						bandera++;
						Command: toastr.warning('Los despachos seleccionados deben ser del mismo <strong>tipo de ración</strong>', 'Advertencia');
					}
				}
			}
		}
	});
	if(cant == 0){
		Command: toastr.warning('Debe seleccionar al menos un despacho para continuar', 'Advertencia');
		bandera++;
	}
	if(bandera == 0){
		var rutaSeleccionada = $('#ruta option:selected').text();
		$('#rutaNm').val(rutaSeleccionada);
		$('#formDespachos').attr('action', 'covid19_despacho_consolidado_ri.php');
		$('#formDespachos').attr('method', 'post');
		$('#formDespachos').submit();
		$('#formDespachos').attr('method', 'get');
	}
}

// formato despachos agrupados 
function despachos_agrupados(){
	var cant = 0;
	var despacho = 0;
	var sede = 0;
	var tipo = '';
	var tipodespacho = 0;
	var bandera = 0;
	$("tbody input:checked").each(function(){
		if(bandera == 0){
			cant++;
			if(bandera == 0){
				if(sede == 0){
					sede = $(this).attr('sede');
				}
				else{
					if(sede != $(this).attr('sede')){
						bandera++;
						Command: toastr.warning('Los despachos seleccionados deben ser de la misma sede');
					}
				}
			}
			if(bandera == 0){
				if(tipo == ''){
					tipo = $(this).attr('complemento');
				}
				else{
					if(tipo != $(this).attr('complemento')){
						bandera++;
						Command: toastr.warning('Los despachos seleccionados deben ser del mismo <strong>tipo de ración</strong>', 'Advertencia');
					}
				}
			}
			if(bandera == 0){
				if(tipodespacho == 0){
					tipodespacho = $(this).attr('tipo');
				}
				else{
					if(tipodespacho != $(this).attr('tipo')){
						bandera++;
						Command: toastr.warning('Los despachos seleccionados deben ser del mismo <strong>tipo de despacho</strong>', 'Advertencia');
					}
				}
			}
		}
	});

	if(cant == 0){
		Command: toastr.warning('Debe seleccionar al menos un despacho para continuar', 'Advertencia');
		bandera++;
	}

	if(bandera == 0){
		$('#formDespachos').attr('action', 'despacho_agrupado.php');
		$('#formDespachos').attr('method', 'post');
		$('#formDespachos').submit();
	}
}

// despachos por sede vertical
function despachos_por_sede_vertical(){
	var cant = 0;
	var despacho = 0;
	var semana = 0;
	var tipo = '';
	var bandera = 0;
	$("tbody input:checked").each(function(){
		if(bandera == 0){
			cant++;
			if(bandera == 0){
				if(semana == 0){
					semana = $(this).attr('semana');
				}
				else{
					if(semana != $(this).attr('semana')){
						bandera++;
						Command: toastr.warning('Los despachos seleccionados deben ser de la misma <strong>semana</strong>', 'Advertencia');
					}
				}
			}
			if(bandera == 0){
				if(tipo == ''){
					tipo = $(this).attr('complemento');
				}
			}
		}
	});
	if(cant == 0){
		Command: toastr.warning('Debe seleccionar al menos un despacho para continuar', 'Advertencia');
		bandera++;
	}
	if(bandera == 0){
		$('#formDespachos').attr('action', 'despacho_por_sede_vertical.php');
		$('#formDespachos').attr('method', 'post');
		$('#formDespachos').submit();
		$('#formDespachos').attr('method', 'get');
	}
}

// despachos por sede kardex multiple vertical
function despachos_kardex_multiple_vertical(){
	var cant = 0;
	var despacho = 0;
	var semana = 0;
	var tipo = '';
	var bandera = 0;
	$("tbody input:checked").each(function(){
		if(bandera == 0){
			cant++;
			if(bandera == 0){
				if(semana == 0){
					semana = $(this).attr('semana');
				}
				else{
					if(semana != $(this).attr('semana')){
						bandera++;
						Command: toastr.warning('Los despachos seleccionados deben ser de la misma <strong>semana</strong>', 'Advertencia');
					}
				}
			}
			if(bandera == 0){
				if(tipo == ''){
					tipo = $(this).attr('complemento');
				}
				else{
					if(tipo != $(this).attr('complemento')){
						bandera++;
						Command: toastr.warning('Los despachos seleccionados deben ser del mismo <strong>tipo de ración</strong>', 'Advertencia');
					}
				}
			}
		}
	});
	if(cant == 0){
		Command: toastr.warning('Debe seleccionar al menos un despacho para continuar', 'Advertencia');
		bandera++;
	}
	if(bandera == 0){
		$('#formDespachos').attr('action', 'despacho_kardex_multiple_vertical.php');
		$('#formDespachos').attr('method', 'post');
		$('#formDespachos').submit();
		$('#formDespachos').attr('method', 'get');
	}
}

// consolidado vertical
function despachos_consolidado_vertical(){
	var cant = 0;
	var tipo = '';
	var semana = 0;
	var bandera = 0;
	var despacho = 0;
	$('tbody input:checked').each(function(){
		if(bandera == 0){
			cant++;
			if(bandera == 0){
				if(semana == 0){
					semana = $(this).attr('semana');
				} else{
					if(semana != $(this).attr('semana')){
						bandera++;
						Command: toastr.warning('Los despachos seleccionados deben ser de la misma <strong>semana</strong>', "Advertencia");
					}
				}
			}
			if(bandera == 0){
				if(tipo == ''){
					tipo = $(this).attr('complemento');
				}
				else{
					if(tipo != $(this).attr('complemento')){
						bandera++;
						Command: toastr.warning('Los despachos seleccionados deben ser del mismo <strong>tipo de ración</strong>', 'Advertencia');
					}
				}
			}
		}
	});
	if(cant == 0){
		Command: toastr.warning('Debe seleccionar al menos un despacho para continuar', 'Advertencia');
		bandera++;
	}
	if(bandera == 0){
		var rutaSeleccionada = $('#ruta option:selected').text();
		$('#rutaNm').val(rutaSeleccionada);
		$('#formDespachos').attr('action', 'despacho_consolidado_vertical.php');
		$('#formDespachos').attr('method', 'post');
		$('#formDespachos').submit();
		$('#formDespachos').attr('method', 'get');
	}
}

function despachos_consolidado_x_sede(){
	var cant = 0;
	var complementoActual = '';
	var semana = 0;
	var bandera = 0;
	var despacho = 0;

	$('tbody input:checked').each(function(){
		if(bandera == 0){
			cant++;
			var complemento =  $(this).attr("complemento");		
			if(bandera == 0){
				if(complementoActual == ''){
					complementoActual = complemento;
				}
				else{
					if(complementoActual != complemento){
						bandera++;
						Command: toastr.warning('Los despachos seleccionados deben ser del mismo <strong>tipo de ración</strong>', 'Advertencia');
					}
				}
			}
		}
	});
	if(cant == 0){
		Command: toastr.warning('Debe seleccionar al menos un despacho para continuar', 'Advertencia');
		bandera++;
	}
	if(bandera == 0){
		var rutaSeleccionada = $('#ruta option:selected').text();
		$('#rutaNm').val(rutaSeleccionada);
		$('#formDespachos').attr('action', 'despacho_consolidado_sedes.php');
		$('#formDespachos').attr('method', 'post');
		$('#formDespachos').submit();
		$('#formDespachos').attr('method', 'get');
	}
}

/************************************** FIN DE FORMATOS ***********************************************/

/************************************** FORMATOS EN EXCEL *********************************************/
function despachos_por_sede_xlsx(){
	var cant = 0;
	var despacho = 0;
	var semana = 0;
	var tipo = '';
	var bandera = 0;
	$("tbody input:checked").each(function(){
		if(bandera == 0){
			cant++;
			if(bandera == 0){
				if(semana == 0){
					semana = $(this).attr('semana');
				}
				else{
					if(semana != $(this).attr('semana')){
						bandera++;
						Command: toastr.warning('Los despachos seleccionados deben ser de la misma <strong>semana</strong>', 'Advertencia');
					}
				}
			}
		}
	});
	if(cant == 0){
		Command: toastr.warning('Debe seleccionar al menos un despacho para continuar', 'Advertencia');
		bandera++;
	}
	if(bandera == 0){
		$('#formDespachos').attr('target','_self');
		$('#formDespachos').attr('action', 'despacho_por_sede_xlsx.php');
		$('#formDespachos').attr('method', 'post');
		$('#formDespachos').submit();
		$('#formDespachos').attr('target','_blank');
		$('#formDespachos').attr('method', 'get');
		// location.reload();
	}
}

/************************************** FORMATOS EN EXCEL *********************************************/


function editar_despacho(){
	var cant = 0;
	var despacho = 0;
	var tipo = '';
	var estado = '';
	var bandera = 0;
	$("tbody input:checked").each(function(){
		estado_actual = $(this).attr('estado');
		if(bandera == 0){
			cant++;
			if(2 != estado_actual){
				bandera++;
				Command: toastr.warning('Solo se pueden editar despachos en estado <strong>Pendiente.</strong>', 'Advertencia');
			}
			if(cant > 1){
				Command: toastr.warning('Debe seleccionar solo un despacho para editar', 'Advertencia');
				bandera++;
			}
		}
	}); // Termina de revisar cada uno de los elementos que se encuentren checkeados.
	if(cant == 0){
		Command: toastr.warning('Debe seleccionar al menos un despacho para editar', 'Advertencia');
		bandera++;
	}
	if(bandera == 0){
		$('#formDespachos').attr('action', 'despacho_editar.php');
		$('#formDespachos').attr('method', 'post');
		$('#formDespachos').attr('target', '_self');
		$('#formDespachos').submit();
		$('#formDespachos').attr('method', 'get');
	}
}

function eliminar_despacho(){
	var cant = 0;
	var despacho = 0;
	var tipo = '';
	var estado = '';
	var bandera = 0;
	var despachosSeleccionados = '';
	$("tbody input:checked").each(function(){
		despacho = $(this).val();
		// console.log(despacho)
		if(bandera == 0){
			cant++;
			despachosSeleccionados += "'" + despacho + "',";
		}
		annoEliminar = $('#annoi').val();
		mesEliminar = $('#mesi').val();
		Num_Doc = despachosSeleccionados;
	}); // Termina de revisar cada uno de los elementos que se encuentren checkeados.
	if(cant == 0){
		Command: toastr.warning('Debe seleccionar al menos un despacho para eliminar', 'Advertencia');
		bandera++;
	}
	$('#mes_eliminar').val(mesEliminar);
	$('#num_Doc_eliminar').val(Num_Doc);
	$('#anno_eliminar').val(annoEliminar);
	$('#ventanaConfirmar').modal();
}// Termina la función para eliminar despachos.

function deleteRemision() {
	// Se va agregar el año y el mes para hacer la eliminación en la tabla correspondiente
	var annoi = $('#anno_eliminar').val();
	var mesi = $('#mes_eliminar').val();
	var numDoc = $('#num_Doc_eliminar').val()
	var datos = { "despachos" : numDoc, "annoi" : annoi, "mesi" : mesi };
	$.ajax({
		type: "POST",
		url: "functions/fn_despacho_eliminar.php",
		data: datos,
		beforeSend: function(){
			$('#loader').fadeIn();
		},
		success: function(data){
			$('#debug').html(data);
			if(data == 1){
				Command: toastr.success('Se ha eliminado con éxito el despacho.',
										'Exito',{onHidden : function(){ location.reload(); }});
			}
		}
	})
	.done(function(){ })
	.fail(function(){ })
	.always(function(){
		$('#loader').fadeOut();
	});
}


/********** MANEJO DE INVENTARIOS ************/
function enviar_despacho(){
	var cant = 0;
	var bandera = 0;
	$("tbody input:checked").each(function(){
		estado_actual = $(this).attr('estado');
		if(bandera == 0){
			cant++;
			if(estado_actual == 1){
				bandera++;
				Command: toastr.warning('El despacho seleccionado esta <strong> Enviado.</strong>', 'Advertencia');
			}
			if(cant > 1){
				Command: toastr.warning('Debe seleccionar solo un despacho para <strong> Enviar. </strong>', 'Advertencia');
				bandera++;
			}
			despacho = $(this).val();
			complemento = $(this).attr('complemento');
			nom_sede = $(this).attr('nom_sede');
		}
	}); // Termina de revisar cada uno de los elementos que se encuentren checkeados.
	if(cant == 0){
		Command: toastr.warning('Debe seleccionar al menos un despacho para <strong> Enviar.</strong>', 'Advertencia');
		bandera++;
	}
	if(bandera == 0){
		var annoi = $('#annoi').val();
		var mesi = $('#mesi').val();
		var datos = { "despacho" : despacho, "annoi" : annoi, "mesi" : mesi, "complemento" : complemento, "nom_sede" : nom_sede };
		$.ajax({
			type: "POST",
			url: "functions/fn_despacho_enviar.php",
			data: datos,
			beforeSend: function(){
				$('#loader').fadeIn();
			},
			success: function(data){
				$('#debug').html(data);
				if(data == 1){
					Command: toastr.success(
						'Se ha enviado con éxito.',
						"Éxito", { onHidden : function(){ location.reload(); } }
					);		
				}else{
					Command: toastr.error(
						'Existe un error, comuniquese con el administrador del sistema',
						"Error", { onHidden : function(){ location.reload(); } }
					);	
				}
			}
		})
		.done(function(){ })
		.fail(function(){ })
		.always(function(){
			$('#loader').fadeOut();
		});
	}
}
