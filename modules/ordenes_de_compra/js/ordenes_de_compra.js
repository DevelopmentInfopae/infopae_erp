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
      		if ($(this).find('input').val() != 'on' && $(this).find('input').val() != null ) {
      			despachoPorSede($(this).find('input').val())
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

function despachoPorSede(despacho){
	var mesI = $('#mesi').val();
	var annoI = $('#annoi').val();	
	var imprimirMes = 0;
	if( $('#imprimirMes').prop('checked') ) {
		imprimirMes = 1;
	}else{
		imprimirMes = 0;
	}
	$('#imprimirMesI').val(imprimirMes);
	$('#AnnoI').val(annoI);
	$('#MesI').val(mesI);
	$('#ordenCompra').val(despacho);
	$('#formDespachoPorSede').submit();
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
}

function eliminar_orden(){
	var cant = 0;
	var despacho = 0;
	var tipo = '';
	var estado = '';
	var bandera = 0;
	$("tbody input:checked").each(function(){
		if(bandera == 0){
			cant++;
			despacho = $(this).val();
			if(cant > 1){
				alert('Debe seleccionar solo una orden de compra para eliminar');
				bandera++;
				return false;
			}
		}
	}); // Termina de revisar cada uno de los elementos que se encuentren checkeados.

	if(cant == 0){
		alert('Debe seleccionar al menos una orden de compra para eliminar');
		bandera++;
	}

	if(bandera == 0){
		var r = confirm("Confirma que desea eliminar este registro.");
		if (r == true) {
			// Se va agregar el año y el mes para hacer la eliminación en la tabla correspondiente
			var annoi = $('#annoi').val();
			var mesi = $('#mesi').val();
			// Se va a envíar la variable despacho para iniciar el procesos de eliminación.
			var datos = {"despacho":despacho,"annoi":annoi,"mesi":mesi};
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
	}// Termina el if si la bandera esta en cero
}// Termina la función para eliminar despachos.








