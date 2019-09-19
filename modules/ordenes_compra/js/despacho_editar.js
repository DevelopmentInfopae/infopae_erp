
$(document).ready(function(){
	console.log('Inicio de la seccion de edición de despacho.');
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
	var tipoRacion = $('#tipoRacion').val();
	var municipio = $('#municipioInicial').val();
	buscar_municipio(tipoRacion,municipio);
	var institucion = $('#institucionInicial').val();
	buscar_institucion(municipio,tipoRacion,institucion);
	var sede = $('#sedeInicial').val();
	buscar_sede(semana,municipio,tipoRacion,institucion,sede),$('#loader');
});
//Termina document ready
// Funcion para crear y guardar el despacho.














function actualizarDespacho(){
	console.log('Se va a generar el despacho.');
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
	console.log('Buscando items agregados.');
	$( "#box-table-a tbody input[type=checkbox]" ).each(function(){ itemsDespacho.push($(this).val()); console.log($(this).val());    });
	bandera = 0;
	// Validaciones para actualizar el despacho
	if(subtipo == ''){
		alert('El campo tipo de despacho es obligatorio.');
		bandera++;
	}  else if(proveedorEmpleado == ''){
		alert('El campo Proveedor / Empleado es obligatorio.');
		bandera++;
	}  else if(semana == ''){
		alert('El campo semana es obligatorio.');
		bandera++;
	}  else if(dias.length == 0){
		alert('Debe seleccionar al menos un día para el despacho');
		bandera++;
	}  else if(itemsDespacho.length == 0){
		bandera++;
		alert('Debe seleccionar la sede a la que se le va a despachar.');
	}
	else if(itemsDespacho.length > 1){
		bandera++;
		alert('Debe seleccionar una sola sede para la edición del despacho individual.');
	}  else if(bodegaOrigen == ''){
		alert('El campo bodega origen es obligatorio');
		bandera++;
	}  else if(tipoTransporte == ''){
		alert('El campo tipo de transporte es obligatorio');
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
				console.log('Inicia la inserción en tablas');
				$('#loader').fadeIn();
			},
			success: function(data){
				//window.open("despacho_consolidado_fecha_lote.php");
				if(data == 1){
					alert('El despacho se ha actualizado con éxito.');
					$(window).unbind('beforeunload');
					window.location.href = 'despachos.php';
					//window.location.href = 'despacho_consolidado_fecha_lote.php';
					//window.location.href = 'despachos.php';
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
			//$('#debug').html(data);
			$('#dias').html(data);
		}
	});
}


function buscar_institucion(municipio, tipo, institucion){
	var datos = {
		"municipio":municipio,
		"tipo":tipo,"institucion":institucion
	};
	$.ajax({
		type: "POST",
		url: "functions/fn_despacho_buscar_institucion_edicion.php",
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

function buscar_municipio(tipo,municipio){
	var datos = {
		"tipo" : tipo,
		"municipio" : municipio
	};
	$.ajax({
		type: "POST",
		url: "functions/fn_despacho_buscar_municipio_edicion.php",
		data: datos,
		beforeSend: function(){
			$('#loader').fadeIn();
		},
		success: function(data){
			//$('#debug').html(data);
			//console.log(data);
			$('#municipio').html(data);
		}
	})
	.done(function(){})
	.fail(function(){})
	.always(function(){
		$('#loader').fadeOut();
	});}

	function buscar_sede( semana, municipio, tipo, institucion, sede){
		var datos = {
			"semana" : semana,
			"municipio" : municipio,
			"tipo" : tipo,
			"institucion" : institucion,
			"sede" : sede
		};
		$.ajax({
			type: "POST",
			url: "functions/fn_despacho_buscar_sede_edicion.php",
			data: datos,
			beforeSend: function(){
				$('#loader').fadeIn();
			},
			success: function(data){
				//$('#debug').html(data);
				$('#sede').html(data);
			}
		})
		.done(function(){})
		.fail(function(){})
		.always(function(){
			$('#loader').fadeOut();
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
			beforeSend: function(){$('#loader').fadeIn();},
			success: function(data){
				$('#proveedorEmpleado').html(data);
			}
		})
		.done(function(){
			var usuarioNm = $("#proveedorEmpleado option:selected").text();
			$('#proveedorEmpleadoNm').val(usuarioNm);
		})
		.fail(function(){})
		.always(function(){
			$('#loader').fadeOut();
		});
	}


	function buscar_bodegas( usuario , bodegaOrigen){
		console.log(usuario);
		var datos = {
			"usuario" : usuario,
			"bodegaOrigen" : bodegaOrigen
		};
		$.ajax({
			type: "POST",
			url: "functions/fn_despacho_buscar_bodegas_edicion.php",
			data: datos,
			beforeSend: function(){
				$('#loader').fadeIn();
			},
			success: function(data){
				//$('#debug').html(data);
				$('#bodegaOrigen').html(data);
			}
		})
		.done(function(){})
		.fail(function(){})
		.always(function(){
			$('#loader').fadeOut();
		});
	}
