
$(document).ready(function(){

	toastr.options = {
	    "closeButton": true,
	    "debug": false,
	    "progressBar": true,
	    "preventDuplicates": false,
	    "positionClass": "toast-top-right",
	    "onclick": null,
	    "showDuration": "0",
	    "hideDuration": "0",
	    "timeOut": "2000",
	    "extendedTimeOut": "0",
	    "showEasing": "swing",
	   	"hideEasing": "linear",
	    "showMethod": "fadeIn",
	    "hideMethod": "fadeOut"
    }

	$('.i-checks').iCheck({ checkboxClass: 'icheckbox_square-green' });
	$('.select2').select2();
	var subtipo = $('#subtipo').val();
	var subtipoNm = $("#subtipo option:selected").text();
	$('#subtipoNm').val(subtipoNm);
	var proveedorEmpleado = $('#proveedorEmpleadoInicial').val();
	buscar_proveedor_empleado(subtipo,proveedorEmpleado);
	var bodegaOrigen = $('#bodegaOrigen').val();
	buscar_bodegas(proveedorEmpleado,bodegaOrigen);
	var semana = $('#semana').val();
	var diasDespacho = $('#diasDespacho').val();
	buscar_dias(semana,diasDespacho);
});

// Funcion para crear y guardar el despacho.
function actualizarDespacho(){
	$('#btnGuardar').attr('disabled', true);
	var subtipo = $('#subtipo').val();
	var subtipoNm = $('#subtipoNm').val();
	var proveedorEmpleado = $('#proveedorEmpleado').val();
	var proveedorEmpleadoNm = $('#proveedorEmpleadoNm').val();
	var despacho = $('#despacho').val();
	var semana = $('#semana').val();
	var tipo = $('#tipoRacion').val();
	var tipoDespacho = $('#tipoDespacho').val();
	var bodegaOrigen = $('#bodegaOrigen').val();
	var tipoTransporte = $('#tipoTransporte').val();
	var placa = $('#placa').val();
	var conductor = $('#conductor').val();
	var dias = new Array();
	$('#dias .dia:checked').each( function() { var aux = $(this).val(); dias.push(aux); }  );
	var itemsDespacho = [];
	var itemsDespachoVariacion = [];
	$( "#box-table-a tbody input[type=checkbox]" ).each(function(){
		variacion = ($(this).data('variacion') == 0 ? 3 : $(this).data('variacion'));
		itemsDespacho.push($(this).val());
		itemsDespachoVariacion.push(variacion);
	});
	bandera = 0;
	// Validaciones para actualizar el despacho
	if(subtipo == ''){
    	Command: toastr.warning('El campo <strong>tipo de despacho</strong> es obligatorio.', 'Advertencia');
		bandera++;
	}  else if(proveedorEmpleado == ''){
    	Command: toastr.warning('El campo <strong>Proveedor / Empleado</strong> es obligatorio.', 'Advertencia');
		bandera++;
	}  else if(dias.length == 0){
		Command: toastr.warning('Debe seleccionar al menos un día para el despacho.', 'Advertencia');
		bandera++;
	}  	else if(bodegaOrigen == ''){
		Command: toastr.warning('El campo bodega origen es obligatorio.', 'Advertencia');
		bandera++;
	}  else if(tipoTransporte == ''){
		Command: toastr.warning('El campo tipo de transporte es obligatorio', 'Advertencia');
		bandera++;
	}
	if(bandera == 0){
		var datos = {
			"subtipo":subtipo,
			"subtipoNm":subtipoNm,
			"proveedorEmpleado":proveedorEmpleado,
			"proveedorEmpleadoNm":proveedorEmpleadoNm,
			"despacho":despacho,
			"semana":semana,
			"dias":dias,
			"tipo":tipo,
			"tipoDespacho":tipoDespacho ,
			"itemsDespacho":itemsDespacho,
			"itemsDespachoVariacion":itemsDespachoVariacion,
			"bodegaOrigen":bodegaOrigen,
			"tipoTransporte":tipoTransporte,
			"placa":placa,
			"conductor":conductor
		};
		$.ajax({
			type: "POST",
			url: "functions/fn_despacho_editar.php",
			data: datos,
			beforeSend: function(){
				$('#loader').fadeIn();
			},
			success: function(data){
				if(data == 1){
					Command: toastr.success('El despacho se ha actualizado con éxito.','El despacho se actualizó con éxito.',{onHidden : function(){ location.href='despachos.php'; }});
					// Command: toastr.success('Despachado con éxito.','El despacho se registró con éxito.',{onHidden : function(){}});
				}else{
					alert(data);
					$('#debug').html(data);
					if (data.indexOf('Ya existe despacho para la sede')==-1){
						$('#debug').html(data);
						console.log(data);
					}
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

function buscar_dias(semana,diasDespacho){
	var datos = { "semana" : semana,
	"diasDespacho" : diasDespacho };
	$.ajax({
		type: "POST",
		url: "functions/fn_despacho_buscar_dias_semana_check_dias_despacho.php",
		data: datos,
		beforeSend: function(){},
		success: function(data){
			$('#dias').html(data);
			$('.i-checks').iCheck({ checkboxClass: 'icheckbox_square-green' });
		}
	});
}

function buscar_proveedor_empleado( subtipo, proveedorEmpleado ){
	var datos = {
		"subtipo" : subtipo,
		"proveedorEmpleado" : proveedorEmpleado
	};
	$.ajax({
		type: "POST",
		url: "functions/fn_despacho_buscar_proveedor_empleado_edicion.php",
		data: datos,
		beforeSend: function(){},
		success: function(data){
			$('#proveedorEmpleado').html(data);
		}
	})
	.done(function(){
		var usuarioNm = $("#proveedorEmpleado option:selected").text();
		$('#proveedorEmpleadoNm').val(usuarioNm);
	})
	.fail(function(){})
	.always(function(){});
}

function buscar_bodegas( usuario , bodegaOrigen){
	var datos = {
		"usuario" : usuario,
		"bodegaOrigen" : bodegaOrigen
	};
	$.ajax({
		type: "POST",
		url: "functions/fn_despacho_buscar_bodegas_edicion.php",
		data: datos,
		beforeSend: function(){},
		success: function(data){
			$('#bodegaOrigen').html(data);
		}
	})
	.done(function(){})
	.fail(function(){})
	.always(function(){});
}
