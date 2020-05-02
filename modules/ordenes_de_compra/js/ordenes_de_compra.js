$(document).ready(function(){
	var cantidadDetallados = 0;
	var mes = $('#mesi').val();
	var mesText = $("#mesi option[value='"+mes+"']").text()
	$('#mesfText').val(mesText);
	$('#mesf').val(mes);

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
		var tipo = $('#tipoRacion').val();
		var municipio = $(this).val();
		buscar_institucion(municipio,tipo);
	});

	$('#institucion').change(function(){
		var institucion = $(this).val();
		var tipo = $('#tipoRacion').val();
		var municipio = $('#municipio').val();
		var semana = $('#semana').val();
		buscar_sede(semana,municipio,tipo,institucion);
	});

	$( "#btnBuscar" ).click(function(){
		console.log('Se va  a hacer una busqueda.');
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
		order: [ 1, 'desc' ],
		dom: 'lr<"containerBtn"><"inputFiltro"f>tip',
		pageLength: 25,
		lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "TODO"]],
		responsive: true,
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
	console.log('Actualizando lista de instituciones.');
	console.log(municipio);
	console.log(tipo);
	var datos = {"municipio":municipio,"tipo":tipo};
		$.ajax({
			type: "POST",
			url: "functions/fn_despacho_buscar_institucion.php",
			data: datos,
			beforeSend: function(){
				$('#loader').fadeIn();
			},
			success: function(data){
				//$('#debug').html(data);
				$('#institucion').html(data);
			}
		})
		.done(function(){ })
		.fail(function(){ })
		.always(function(){
			$('#loader').fadeOut();
		});
}

function buscar_sede(semana, municipio, tipo, institucion){
	var datos = {"semana":semana,"municipio":municipio,"tipo":tipo,"institucion":institucion};
		$.ajax({
			type: "POST",
			url: "functions/fn_despacho_buscar_sede.php",
			data: datos,
			beforeSend: function(){
				$('#loader').fadeIn();
			},
			success: function(data){
				//$('#debug').html(data);
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
	.done(function(data) { console.log(data);
		if (data != '') {
			$("#semana").html(data);
		}
	})
	.fail(function(data) {
		console.log(data);
	});
}

function despachos_kardex(){
	//Contando los elementos checked
	var cant = 0;
	var despacho = 0;
	var semana = 0;
	var tipo = '';
	var bandera = 0;
	$("tbody input:checked").each(function(){
		if(bandera == 0){
			cant++;
			despacho = $(this).val();

			if(bandera == 0){
				if(semana == 0){
					semana = $("#semana_"+despacho).val();
				}
				else{
					if(semana != $("#semana_"+despacho).val()){
						bandera++;
						alert('Los despachos seleccionados deben ser de la misma semana');

					}
				}
			}

			if(bandera == 0){
				if(tipo == ''){
					tipo = $("#tipo_"+despacho).val();
				}
				else{
					if(tipo != $("#tipo_"+despacho).val()){
						bandera++;
						alert('Los despachos seleccionados deben ser del mismo tipo de ración');

					}
				}
			}
		}
	});

	if(cant == 0){
		alert('Debe seleccionar al menos un despacho para continuar');
		bandera++;
	}

	if(bandera == 0){
		$( ".soloJs" ).remove();
		console.log('Se van a mostrar los despachos por sede');

		$('#formDespachos').attr('action', 'despacho_kardex3.php');
		$('#formDespachos').attr('method', 'post');
		$('#formDespachos').submit();
		$('#formDespachos').attr('method', 'get');
	}
}

function despachos_kardex_multiple(){
	var cant = 0;
	var despacho = 0;
	var semana = 0;
	var tipo = '';
	var bandera = 0;

	// Ciclo para contar los despachos seleccionados.
	$("tbody input:checked").each(function(){
		if(bandera == 0){
			cant++;
			despacho = $(this).val();

			if(bandera == 0){
				if(semana == 0){
					semana = $("#semana_"+despacho).val();
				}
				else{
					if(semana != $("#semana_"+despacho).val()){
						bandera++;
						Command: toastr.warning('Los despachos seleccionados deben ser de la misma <strong>semana</strong>', 'Advertencia');
					}
				}
			}

			if(bandera == 0){
				if(tipo == ''){
					tipo = $("#tipo_"+despacho).val();
				}
				else{
					if(tipo != $("#tipo_"+despacho).val()){
						bandera++;
						Command: toastr.warning('Los despachos seleccionados deben ser del mismo <strong>tipo de ración</strong>', 'Advertencia');
						alert('');
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
		$( ".soloJs" ).remove();

		$('#formDespachos').attr('action', 'despacho_kardex4_multiple.php');
		$('#formDespachos').attr('method', 'post');
		$('#formDespachos').submit();
		$('#formDespachos').attr('method', 'get');
	}
}

function despachos_kardex2(){
	//Contando los elementos checked
	var cant = 0;
	var despacho = 0;
	var semana = 0;
	var tipo = '';
	var bandera = 0;
	$("tbody input:checked").each(function(){
		if(bandera == 0){
			cant++;
			despacho = $(this).val();

			if(bandera == 0){
				if(semana == 0){
					semana = $("#semana_"+despacho).val();
				}
				else{
					if(semana != $("#semana_"+despacho).val()){
						bandera++;
						alert('Los despachos seleccionados deben ser de la misma semana');

					}
				}
			}

			if(bandera == 0){
				if(tipo == ''){
					tipo = $("#tipo_"+despacho).val();
				}
				else{
					if(tipo != $("#tipo_"+despacho).val()){
						bandera++;
						alert('Los despachos seleccionados deben ser del mismo tipo de ración');

					}
				}
			}
		}
	});

	if(cant == 0){
		alert('Debe seleccionar al menos un despacho para continuar');
		bandera++;
	}

	if(bandera == 0){
		$( ".soloJs" ).remove();
		console.log('Se van a mostrar los despachos por sede');

		$('#formDespachos').attr('action', 'despacho_kardex2.php');








		$('#formDespachos').attr('method', 'post');
		$('#formDespachos').submit();
		$('#formDespachos').attr('method', 'get');
	}
}

function despachos_mixta(){
	//Contando los elementos checked
	var cant = 0;
	var despacho = 0;
	var semana = 0;
	var tipo = '';
	var bandera = 0;
	$("tbody input:checked").each(function(){
		if(bandera == 0){
			cant++;
			despacho = $(this).val();

			if(bandera == 0){
				if(semana == 0){
					semana = $("#semana_"+despacho).val();
				}
				else{
					if(semana != $("#semana_"+despacho).val()){
						bandera++;
						alert('Los despachos seleccionados deben ser de la misma semana');

					}
				}
			}

			if(bandera == 0){
				if(tipo == ''){
					tipo = $("#tipo_"+despacho).val();
				}
				else{
					if(tipo != $("#tipo_"+despacho).val()){
						bandera++;
						alert('Los despachos seleccionados deben ser del mismo tipo de ración');

					}
				}
			}
		}
	});

	if(cant == 0){
		alert('Debe seleccionar al menos un despacho para continuar');
		bandera++;
	}

	if(bandera == 0){
		$( ".soloJs" ).remove();
		console.log('Se van a mostrar los despachos por sede');

		$('#formDespachos').attr('action', 'despacho_mixta.php');








		$('#formDespachos').attr('method', 'post');
		$('#formDespachos').submit();
		$('#formDespachos').attr('method', 'get');
	}
}

function despachos_por_sede_fecha_lote(){

	//Contando los elementos checked
	var cant = 0;
	var despacho = 0;
	var semana = 0;
	var tipo = '';
	var bandera = 0;


	$("tbody input:checked").each(function(){
		if(bandera == 0){
			cant++;
			despacho = $(this).val();
			if(2 != $("#estado_"+despacho).val()){
				bandera++;
				alert('Solo se pueden agregar lotes y fechas de vencimiento a despachos en estado Pendiente.');
				return false;
			}
			if(cant > 1){
				alert('Debe seleccionar solo un despacho para agregar lotes y fechas de vencimiento');
				bandera++;
				return false;
			}
		}
	}); // Termina de revisar cada uno de los elementos que se encuentren checkeados.

	$("tbody input:checked").each(function(){
		if(bandera == 0){
			cant++;
			despacho = $(this).val();

			if(bandera == 0){
				if(semana == 0){
					semana = $("#semana_"+despacho).val();
				}
				else{
					if(semana != $("#semana_"+despacho).val()){
						bandera++;
						alert('Los despachos seleccionados deben ser de la misma semana');

					}
				}
			}

			if(bandera == 0){
				if(tipo == ''){
					tipo = $("#tipo_"+despacho).val();
				}
				else{
					if(tipo != $("#tipo_"+despacho).val()){
						bandera++;
						alert('Los despachos seleccionados deben ser del mismo tipo de ración');

					}
				}
			}
		}
	});

	if(cant == 0){
		alert('Debe seleccionar al menos un despacho para continuar');
		bandera++;
	}

	if(bandera == 0){
		$( ".soloJs" ).remove();
		console.log('Se van a mostrar los despachos por sede');
		$('#formDespachos').attr('action', 'despacho_por_sede_fecha_lote.php');

		$('#formDespachos').attr('method', 'post');
		$('#formDespachos').submit();
		$('#formDespachos').attr('method', 'get');
	}
}

function despachos_por_sede(){
	//Contando los elementos checked
	var cant = 0;
	var despacho = 0;
	var semana = 0;
	var tipo = '';
	var bandera = 0;
	$("tbody input:checked").each(function(){
		if(bandera == 0){
			cant++;
			despacho = $(this).val();

			if(bandera == 0){
				if(semana == 0){
					semana = $("#semana_"+despacho).val();
				}
				else{
					if(semana != $("#semana_"+despacho).val()){
						bandera++;
						Command: toastr.warning('Los despachos seleccionados deben ser de la misma <strong>semana</strong>', 'Advertencia');
					}
				}
			}

			if(bandera == 0){
				if(tipo == ''){
					tipo = $("#tipo_"+despacho).val();
				}
				else{
					if(tipo != $("#tipo_"+despacho).val()){
						bandera++;
						alert('');
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
		$( ".soloJs" ).remove();

		$('#formDespachos').attr('action', 'orden_de_compra.php');
		$('#formDespachos').attr('method', 'post');
		$('#formDespachos').submit();
		$('#formDespachos').attr('method', 'get');
	}
}

function despachoPorSede(despacho){
	console.log('Click en una fila.');
	// despacho = despacho;
	estado = $("#estado_"+despacho).val();
	console.log("Estado del despacho: "+estado);
	if(estado != 0){
		$( ".soloJs" ).remove();
		var mesI = $('#mesi').val();
		var annoI = $('#annoi').val();
		$('#AnnoI').val(annoI);
		$('#MesI').val(mesI);
		$('#ordenCompra').val(despacho);
		$('#formDespachoPorSede').submit();
	}
}










